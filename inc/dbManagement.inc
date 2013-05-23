<?php
require_once("inc/csvUtils.inc");
class usyvlDbDesc {
    function __construct($key,$dsn,$desc,&$log = null){
        $this->db = $key;
        $this->dsn = $dsn;
        // type is not really used as I expect this to remain sqlite
        list($this->type,$this->path) = explode(":",$this->dsn);
        
        $this->log = $log;
        $this->counter = 0;
        $this->mesgs = "";
        
        
        $GLOBALS['dbh'][$key] =& $this;
    }
    /////function __construct($db,$file,$desc,&$log = null){
    /////    $this->db = $db;
    /////    
    /////    // hate doing this here, this should be done differently, but this is because
    /////    // I am sharing the access database....  Should be passing this in as a dsn  sqlite:pathname
    /////    if ( $db == "access" ) $path = "../workflow/io/db/";
    /////    else                   $path = "io-data/db/";
    /////    
    /////    $this->dbfile = $path . $file . '.sqlite3';
    /////    $this->desc = $desc;
    /////    $this->dsn = 'sqlite:' . $this->dbfile;
    /////    $this->log = $log;
    /////    $this->counter = 0;
    /////    $this->mesgs = "";
    /////    list($this->type,$this->path) = explode(":",$this->dsn);
    /////    
    /////    // what about this??????
    /////    $GLOBALS['dbh'][$db] =& $this;
    /////    
    /////}
    function isWritable(){
         return is_writable($this->path);
    }
    function displayMesgs(){
        print $this->mesgs;
        $this->mesgs = "";
    }
    function initDB(){
        // this makes the (possibly) mistaken assumption that all tables are created at once...
        if( ! file_exists($this->path)){
            //touch($this->path);
            // loop over tables, creating them
            print "file doesn't exist, need to initialize<br>\n";
            $this->tablesCreated = false;
        }
        //else {
        //  $this->tablesCreated = true;
        //}
        if( ! file_exists($this->path)){
            dprint("usyvlDbDesc",0,0,"$this->path does not exist");
        }
        
        if( ! is_writable($this->path)){
            dprint("usyvlDbDesc",0,0,"$this->path is not writable");
        }
        if( ! is_writable(dirname($this->path))){
            dprint("usyvlDbDesc",0,0,"directory: " . dirname($this->path) . " is not writable for path $this->path");
        }
        
        if( ! isset($this->dbh)){
            try {
                $this->dbh = new PDO($this->dsn);
            } catch (PDOException $exception){
                die($exception->getMessage() . " " . $this->dsn);
            }
        }
        
        //$this->createTables();
        if( ! isset($this->tables) || ! is_array($this->tables)){
            dprint("usyvlDbDesc::initDB()",0,0,"problem with table array, initiating backtrace");
            $bt = debug_backtrace();
            print_pre($bt);
        }
        
        foreach($this->tables as $table){
            if( ! $this->tableCreated[$table->name] ){
                // check to see if the table exists
                // maybe do a pragma query???
                // what about seeing if we need to alter the db (ie: usyvlDbTable has changed, but db has not been
                // recreated).  Need to see if there are any new columns to be added via alter....
                $qstr = "select * from $table->name";
                $result = $this->dbh->query($qstr);
                if( $result == null ){
                    // table probably doesnt exist
                    $qs = $table->createTable();
                    dprint("creating table $table->name",0,2,"$qs");;
                    $result = $this->dbh->query("$qs");
                    if( $result == null ){
                        dprint("problem creating table",0,0,$table->name . " with query: $qs");
                        $ei = $this->dbh->errorInfo();
                        print_pre($ei);
                        return false;
                    }
                }
                $this->tableCreated[$table->name] = true;
            }
        }
        return true;
    }
    function addTable($table){
        $this->tables[$table->name] = $table;
        $this->tableCreated[$table->name] = false;
        
        // maybe should create tables here?
    }
    function query($qstr){
        if ( ! $this->initDB() )  return false; 
        //$this->counter++;
         
        //dprint("executing query on {$this->db} db",0,0,"$qstr");
        $this->result = $this->dbh->query("$qstr");
        
        
        if ($this->result == null) {
                //$error = $result->errorCode();
                dprint("error on query",0,0,"$qstr, debug_backtrace follows:");
                $dbt = debug_backtrace();
                $ei = $this->dbh->errorInfo();
                print_pre($ei);
                print_pre($qstr);
                print_pre($dbt);
                die("fatal db query error<br>\n");

        }
        else {
            if( 1 && $this->log && ! preg_match("/^select/",$qstr)){
                // log this entry
                // would be nice to somehow pull the prog_coord for the given site....
                // options: use a SESSION var that would be set before a query (relies on programming)
                //          try to derive from the query (ie: look for prog_coord string or p_name and then map (not reliable))
                $htmlized = str_replace("'","&apos;",$qstr);
                $istr = "insert into logEntries ('l_user','l_db','l_dsn','l_ip','l_query') values('{$_SESSION['u_name']}','{$this->db}','{$this->dsn}','{$_SERVER['REMOTE_ADDR']}','$htmlized')";
                //$istr = "insert into logEntries ('l_user','l_db','l_dsn','l_ip','l_query') values('{$_SESSION['u_name']}','{$this->db}','{$this->dsn}','{$_SERVER['REMOTE_ADDR']}','fixed string {$this->counter}')";
                //dprint("Creating a log entry",0,0,"$istr");
                $this->log->query($istr);
                //usleep(1000000);
            }
        }
        //print_r($result);
        //print "just printed result<br>\n";
        return($this->result);
    }
    ////////////////////////////////////////////////////////////////////////////
    function createTables(){
        foreach($this->tables as $table){
            $qs = $table->createTable();
            print "{$table->name}: qs: $qs<br>\n";
            $result = $this->dbh->query("$qs");
        }
        $this->tablesCreated = true;
    }
    ////////////////////////////////////////////////////////////////////////////
    function resetTable($which){
        if ( isset( $this->tables[$which])){
            $this->query("drop table $which");
            $this->query($this->tables[$which]->createTable());
        }
    }
    ////////////////////////////////////////////////////////////////////////////
    function getTable($name){
        foreach($this->tables as $table){
            if( $table->name == $name ) return $table;
        }
        return null;
    }
    ////////////////////////////////////////////////////////////////////////////
    function verifyDB(){
        foreach(array_keys($this->tables) as $t){
            $this->verifyTableByName($t);
        }
    }
    ////////////////////////////////////////////////////////////////////////////
    function verifyTableByName($tablename){
        if( ! isset($this->tables[$tablename])){
            print "invalide table name $tablename<br>\n";
        }
        
        $table = $this->tables[$tablename];
        //foreach($this->tables as $table){
        print "verifying table $table->name<br>\n";
        $actual_col = array();
        $spec_col = $table->colDesc;
        
        $result = $this->query("pragma table_info('$table->name')");
        $r = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach($r as $col){
            $actual_col[$col['name']] = $col['type'];
        }
        $akeys = array_keys($actual_col);
        $skeys = array_keys($spec_col);
        $ikeys = array_intersect($akeys,$skeys);
        
        $missing_from_spec = array_diff($skeys,$akeys);
        $missing_from_actu = array_diff($akeys,$skeys);
        //print_pre($ikeys);
        if( count($missing_from_spec)){
            print "found in the table spec but not in the actual db: {$this->db} {$table->name}<br>\n";
            print_pre($missing_from_spec);
            // need to run alter to add the columns...
            foreach($missing_from_spec as $field){
                $qstr = "alter table {$table->name} add column $field {$spec_col[$field]}";
                print "$qstr<br>\n";
                if( ! count($missing_from_actu)){
                    // if there are not opposing entries, then most likely we can just automatically add
                    print "<font color='green'>automatically altering the database to match the spec with: </font>$qstr<br>\n";
                    $this->query($qstr);
                }
                else {
                    print "There are discrepancies of the opposing type in the database and there is no automatic way to resolve.<br>\n";
                    print "will eventually try to create a form here that would allow the user to make a choice to apply<br>\n";
                    print "<font color='green'>To update from the command line: </font>sqlite3 {$this->path} \"$qstr\"<br>\n";
                }
            }
        }
        if( count($missing_from_actu)){
            print "found in the actual db but not in the table spec: {$this->db} {$table->name}<br>\n";
            print "this may require removing a column from the database table and cannot be done automatically<br>\n";
            
            print_pre($missing_from_actu);
        }
        //print_pre($actual_col);
        //}
    }
    ////////////////////////////////////////////////////////////////////////////
    function exportCSV($exportdir = "."){
        $b = "";
        foreach( $this->tables as $table){
            $filename = "{$this->db}-{$table->name}.csv";
            $result = $this->query("select * from {$table->name}");
            $hash = $result->fetchAll(PDO::FETCH_ASSOC);
            
            if( ! is_array($hash[0])){
                print "<font color='red'>{$this->db}: select * from {$table->name} failed (possibly empty table?)</font><br>\n";
                return;
            }
            $buf = buildCSV($hash);
            list($header) = explode("\n",$buf);
            $b .= "CSV exported to $exportdir/$filename: headerrow=$header<br>\n";
            //writeBuf(iopath('e',$filename),$buf);
            writeBuf("$exportdir/$filename",$buf);
            // write out buffer somewhere
        }
        return $b;
    }
    ////////////////////////////////////////////////////////////////////////////
    function getTableDescriptionHash($table){
        // would be nice to be able to query
        //print "getting pragma for table: $table<br>\n";
        $result = $this->query( "pragma table_info('" . $table . "')");
        $r = $result->fetchAll(PDO::FETCH_ASSOC);
        //print_pre($r,"pragma result");
        return $r;
    }
    ////////////////////////////////////////////////////////////////////////////
    function getColumnNamesFromTable($table,$skipcols = array()){
        $r = $this->getTableDescriptionHash($table);
        foreach($r as $v){            
            if( ! in_array($v['name'],$skipcols))  $dbcols[] = $v['name'];
        }
        return $dbcols;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // The array input is expected to be a indexed array of hashed arrays with 
    // values keyed by column names.  
    // skipcols is an array of column names to avoid inserting values for (primary keys possibly)
    // Output from csv2array() is suitable
    //
    // if the integer primary key field is provided, sqlite effectively drops any 
    // rows larger than that number upon insert.  This is convenient for much of our
    // use, but needs to be kept in mind.
    // 
    // may want to add a qualifier to skip insertion or warn if not enough fields match
    //
    // Routine above converted to use a prepared statment...
    ////////////////////////////////////////////////////////////////////////////////
    // The prepared statement version
    ////////////////////////////////////////////////////////////////////////////////
    function insertArrayOfHashes($a,$table,$skipcols,$do_query = true){
        // would be nice to be able to query
        // Hmmmm, this fails on gem (cricess db)...  So then we have no columns to enter...
        $r = $this->getTableDescriptionHash($table);
        $dbcols = array();
        
        foreach($r as $v){
            if( $v['pk'] == 1 ) $primary_key = $v['name'];
            
            if( ! in_array($v['name'],$skipcols))  $dbcols[] = $v['name'];
            $type[$v['name']] = $v['type'];
        }
        //print_pre($r,"pragma result (table schema)");        
        
        // now that we have a list of column names, we need to compare to the hash
        $datacols = array_keys($a[0]);
        
        $exportcols = array_intersect($dbcols,$datacols);
        $skippedcols = array_diff($dbcols,$datacols);
        $this->mesgs .= print_pre($skippedcols,"skipped columns",true);
        $this->mesgs .= print_pre($exportcols,"export columns",true);
        
        foreach($a as $row){
            $out = array();
            $pq = "insert into $table (";
            $pq .= implode(",",$exportcols);
            $pq .= ") values (" . implode(",",array_fill(0,count($exportcols),"?")) . ")";
            
            // just need a series of question marks here
            
            $pqvals = array();
            foreach($exportcols as $col){
                $pqvals[] = $row[$col];
            }
            
            $stm = $this->dbh->prepare($pq);
            
            if( $stm === false ){
                print "prepare set error code: " . $this->dbh->errorCode() . "<br>\n";
                print "prepare set error info: " . print_r($this->dbh->errorInfo()) . "<br>\n";
                print "prepared query: $pq <br>\n";
                print "Prepare failed<br>\n";
            }
            else {
                $this->mesgs .= "Prepare succeeded<br>\n";
            }
            
            if( $do_query ){
                if( $stm->execute($pqvals) === true ){
                    $this->mesgs .= "statement execution succeeded<br>\n";
                }
                else {
                    $this->mesgs .= "statement execution failed<br>\n";
                    $this->mesgs .= print_pre($stm->errorInfo(),"error info",true);
                    
                }
            }
            else {
                print "TEST MODE: ";
            }
            $pqvstr = implode(",",$pqvals);
            $this->mesgs .= "insertArrayOfHashes(): prepared query: $pq   : valstr: $pqvstr<br>\n";
        }
        
        // return the rowid of this entry, (0 if it fails)...
        return $this->getLastInserted($primary_key,$table);
        // should return last primary key....
    }
    ////////////////////////////////////////////////////////////////////////////////
    function getLastInserted($key,$table){
        $qstr = "select $key from $table where $key=last_insert_rowid()";
        $qstr = 'SELECT last_insert_rowid() as last_insert_rowid';
        //$qstr = "select last_insert_rowid()";
        $this->mesgs .= "qstr for lastInserted: $qstr<br>\n";
        $result = $this->query("$qstr");
        //$r = $result->fetchAll(PDO::FETCH_ASSOC);
        $r = $result->fetch();
        return $r['last_insert_rowid'];
    }
    ////////////////////////////////////////////////////////////////////////////////
    // prepare done on db,
    // execute done on returned pdostatement
    // if data is not provided, post field values of anything other than "" will be
    // considered to be changes
    //
    // this should also convert any special changes done for HTML display
    // such as apostrophes in input fields...
    ////////////////////////////////////////////////////////////////////////////////
    function updateViaPreparedPostHash($table,$key,$fields,$post,$data = array()){
        //print_pre($post,"post hash");
        //print_pre($data,"data hash");
        //print "updateViaPreparedPostHash(): fields " . implode(",",$fields) . "<br>\n";
        if( ! isset($post[$key]) || $post[$key] == "" ){
            // then dont do anything
            //print "updateViaPreparedPostHash(): post key failed<br>\n";
            return "";
        }
        $where = " where $key='{$post[$key]}'";
        
        // convert any special changes for html display to original values... ie: apostrophes
        foreach($post as &$p)  $p = str_replace(array("&apos;","\'"),array("'","'"),$p);
        
        $fs = array();
        $vs  = array();
        foreach( $fields as $field){
            if( $post[$field] != $data[$field] ) {
                $fs[] = "$field=?";
                $vs[] = $post[$field];
            }
        }
        //print "updateViaPreparedPostHash(): field count: " . count($fs) . "<br>\n";
        
        if( count($fs) > 0) {
            $setstr = implode(",",$fs);
            $vstr = implode(",",$vs);
            $pq = "update $table set " . $setstr . $where;
            $stm = $this->dbh->prepare($pq);
            $status = $stm->execute($vs);
            //print "updateViaPreparedPostHash(): prepared query: $pq   : valstr: $vstr<br>\n";
            return $stm->queryString   . " with values: " . $vstr;
        }
        else return "";
        
        //$this->mesgs .= "qstr: $qstr<br>\n";
    }
}
////////////////////////////////////////////////////////////////////////////////
class usyvlDbTable {
    function __construct($tablename){
        $this->name = $tablename;
    }
    function addCol($fieldname,$type, $args = "",$default = "",$desc = ""){
        $this->fieldname[] = $fieldname;
        $this->type[] = $type;
        $this->args[] = $args;
        $this->createstr[] = "$fieldname $type $args";
        $this->default[] = $default;
        $this->desc[] = $desc;
        $this->colDesc[$fieldname] = $type;
    }
    function createTable(){
        $createstr = "create table {$this->name} (" . implode(",",$this->createstr) . ")";
        return $createstr;
        //print "createstr: $createstr<br>\n";
    }
}
?>
