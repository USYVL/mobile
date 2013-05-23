<?php
require_once("inc/dbManagement.inc");

////////////////////////////////////////////////////////////////////////////////
// search for sqlite3 to find where season name addition should go....
// actually, it will be best to use it where we call usyvlDbDesc.  That way,
// statically named dbs (access can be used across seasons
////////////////////////////////////////////////////////////////////////////////
// Directory needs to be writeable for PDO to work correctly (at least for sqlite).
// must use a temp file...
// sqlite does not have a varchar() type, it has: null, integer, real, text, blob
////////////////////////////////////////////////////////////////////////////////
// the switch from using p_id instead of p_name really only involves db references.
// since php info is reloaded each time, using program as a hash key for example is
// fine as long as its loaded from the db each time as any p_name changes will be
// relected automatically.  The problem comes when p_name is stored in another db
// field (taskGroups).  Then a change in p_name causes any task status to be lost.
//
////////////////////////////////////////////////////////////////////////////////
// when I create a new season db, want to specify some defaults and clear some fields
// 
// need to start documenting sequences, series, procedures and dependencies
////////////////////////////////////////////////////////////////////////////////


$s_key = ( isset($_SESSION['s_key']) ) ?  "{$_SESSION['s_key']}" : "unset" ;

//$logdb = new usyvlDbDesc('log',$s_key . "-" . 'log','Logging Database');
//$ltable = new usyvlDbTable("logEntries");
//$ltable->addCol("l_timestamp","varchar(32) not null default (datetime('now','localtime'))","","","");
//$ltable->addCol("l_user","varchar(16)","","","");
//$ltable->addCol("l_db","varchar(16)","","","");
//$ltable->addCol("l_dsn","varchar(32)","","","");
//$ltable->addCol("l_ip","varchar(16)","","","");
//$ltable->addCol("l_query","varchar(1024)","","","");
//$logdb->addTable($ltable);
//

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//$access = new usyvlDbDesc('access','sqlite:../workflow/io/db/access.sqlite3','Access Control Database',$logdb);
//$utable = new usyvlDbTable("users");                                     // should probably add a u_id
//$utable->addCol("u_name","varchar(16)","primary key","","");
//$utable->addCol("u_password","varchar(32)","","","");
//$utable->addCol("u_type","varchar(16)","","","");
//$utable->addCol("u_color","varchar(32)","","","");
//$access->addTable($utable);

///$pldb = new usyvlDbDesc('pldb','sqlite:io-data/db/poollayout.sqlite3','Pool Layout Database',$logdb);
///$pltable = new usyvlDbTable("poollayout");                                     // should probably add a u_id
///$pltable->addCol("rowid","integer","primary key","","");
///$pltable->addCol("hash","text","","","");
///$pltable->addCol("courts","integer","","","");
///$pltable->addCol("games","integer","","","");
///$pltable->addCol("colls","integer","","","");
///$pltable->addCol("dups","integer","","","");
///$pltable->addCol("byes","integer","","","");
///$pltable->addCol("qual","integer","","","");
///$pltable->addCol("solution","text","","","");
///$pldb->addTable($pltable);

$sdb = new dbMgmt('sdb','sqlite:io/db/sched.sqlite3','Scheduling Database',$logdb);
$evtable = new dbMgmtTable("ev");                                     // should probably add a u_id
$evtable->addCol("evid","integer","primary key","","");
$evtable->addCol("season","text","","","");
$evtable->addCol("program","text","","","");
$evtable->addCol("name","text","","","");
$evtable->addCol("date","text","","","");
$evtable->addCol("ds","text","","","");
$evtable->addCol("time_beg","text","","","");
$evtable->addCol("time_end","text","","","");
$evtable->addCol("dow","text","","","");
$evtable->addCol("location","text","","","");
$evtable->addCol("addr","text","","","");
$evtable->addCol("city","text","","","");
$evtable->addCol("state","text","","","");
$evtable->addCol("zip","text","","","");
$sdb->addTable($evtable);

///$tmtable = new usyvlDbTable("tm");                                     // should probably add a u_id
///$tmtable->addCol("tmid","integer","primary key","","");
///$tmtable->addCol("season","text","","","");
///$tmtable->addCol("program","text","","","");
///$tmtable->addCol("num","integer","","","");
///$tmtable->addCol("name","text","","","");
///$tmtable->addCol("div","text","","","");
///$tmtable->addCol("court","integer","","","");
///$tmtable->addCol("coach","text","","","");
///$tmtable->addCol("tshirt","text","","","");
///$sdb->addTable($tmtable);
///
///// this table maps all games for an entire season (Inrasite Game Days and Intersite Tournaments)
///$gmtable = new usyvlDbTable("gm");                                     // should probably add a u_id
///$gmtable->addCol("gmid","integer","primary key","","");
///$gmtable->addCol("tmid1","integer","","","");
///$gmtable->addCol("tmid2","integer","","","");
///$gmtable->addCol("evid","integer","","","");
///$gmtable->addCol("court","integer","","","");
///$gmtable->addCol("game","integer","","","");
///$gmtable->addCol("pool","text","","","");
///$gmtable->addCol("time","text","","","");
///$sdb->addTable($gmtable);

// Want a location db, we want a per season db, but also want a cross season db
// to do initial lookups against.

// bigger picture, usyvl cross season db should contain:
// Seasons, Programs, Facilities, Facility Locations (ie: workflow stuff....)



///// updated version of the users table
///$utable = new usyvlDbTable("users");                                     // should probably add a u_id
///$utable->addCol("u_id","integer","primary key","","");
///$utable->addCol("u_name","text","unique","","");
///$utable->addCol("u_password","text","","","");
///$utable->addCol("u_type","text","","","");
///$utable->addCol("u_color","text","","","");
///$access->addTable($utable);

////////////////////////////////////////////////////////////////////////////////////
////function getProgramList(){
////    global $program;
////    $result = $program->query("select * from programs order by p_name");
////    while( $r = $result->fetch(PDO::FETCH_ASSOC)){
////        $programList[] = $r['p_name'];
////    }
////    return $programList;
////}
////////////////////////////////////////////////////////////////////////////////////
////function getFacilityList(){
////    global $program;
////    $result = $program->query("select * from facilities order by f_name");
////    while( $r = $result->fetch(PDO::FETCH_ASSOC)){
////        $programList[] = $r['f_name'];
////    }
////    return $programList;
////}
////////////////////////////////////////////////////////////////////////////////////
////function getFacilityIDs(){
////    global $program;
////    $result = $program->query("select f_id from facilities order by f_id");
////    while( $r = $result->fetch(PDO::FETCH_ASSOC)){
////        $programList[] = $r['f_id'];
////    }
////    return $programList;
////}
//////////////////////////////////////////////////////////////////////////////////////
//function getProgramsInProgramGroup($programgroup){
//    global $program;
//    $result = $program->query("select members from programgroups where p_name='$programgroup'");
//    $r = $result->fetchAll(PDO::FETCH_COLUMN);
//    $l = explode(",",$r[0]);
//    return $l;
//}

////////////////////////////////////////////////////////////////////////////////
//function getGroupList(){
//    global $pw;
//    $result = $pw->query("select name from groups order by name");
//    $gl = $result->fetchAll(PDO::FETCH_COLUMN,0);
//    return $gl;
//}
////////////////////////////////////////////////////////////////////////////////
////function getUserList(){
////    global $access;
////    $result = $access->query("select u_name from users");
////    $gl = $result->fetchAll(PDO::FETCH_COLUMN,0);
////    return $gl;
////}
////////////////////////////////////////////////////////////////////////////////////
////function getProgramGroupList($newmode = true){
////    //global $program;
////    if( $newmode ){
////      $result = $GLOBALS['dbh']['programs']->query("select g_name from groups order by g_name");
////      $gl = $result->fetchAll(PDO::FETCH_COLUMN,0 );
////    }
////    else {
////      $result = $GLOBALS['dbh']['programs']->query("select pg_name from programgroups order by pg_name");
////      $gl = $result->fetchAll(PDO::FETCH_COLUMN,0 );
////    }
////    
////    return $gl;
////}
////////////////////////////////////////////////////////////////////////////////////
////function getProgramGroupListN($includeEmpty = true){
////    
////    return $gl;
////}
////////////////////////////////////////////////////////////////////////////////////
////function getProgramList2($join = "", $whereexpr = "",$orderexpr = ""){
////    // what if programgroups is set to all?
////    global $program, $access;   
////    
////    $plist = array();
////    $where = ( $whereexpr == "" ) ? "" : "where $whereexpr";
////    $order = ( $orderexpr == "" ) ? "" : "order by $orderexpr";
//// 
////    
////    //$dbt = debug_backtrace();
////    //print_pre($dbt);
////
////    $qstr = "select programs.p_name from programs $join $where $order ";
////    //print "gpl2 qstr: $qstr<br>\n";
////    $result = $program->query($qstr);
////    //print "error $error<br>\n";
////    $r = $result->fetchAll(PDO::FETCH_COLUMN);
////    
////    return $r;
////}
////////////////////////////////////////////////////////////////////////////////////
////function addProgram(){
////    global $program;
////    // may need to get index of table in tables array to get more info.
////    // what about checking to see if the site already exists....
////    $program->query("insert into table programs");    
////}
////////////////////////////////////////////////////////////////////////////////////
////function fetchVal($dbh,$select,$where){
////    //print_pre($dbh);
////
////    $qstr = "select $select";
////    if( $where != "" ) $qstr .= " where $where";
////    
////    //print "fetchVal: qstr: $qstr<br>\n";
////    $result = $dbh->query("$qstr");
////    $r = $result->fetchAll(PDO::FETCH_COLUMN);
////    return $r[0];
////}
////////////////////////////////////////////////////////////////////////////////////
////function updateVal($dbh,$table,$field,$value,$where){
////    $qstr = "update $table set $field='$value'";
////    if( $where != "" ) $qstr .= "where $where";
////    else return false;
////    
////    $dbh->query($qstr);
////    return $result;
////}
////////////////////////////////////////////////////////////////////////////////////
////// this returns an array of single elements that match the criteria
////function fetchList($dbh,$select,$where = "",$order = ""){
////    if( ! is_object($dbh)) {
////    print_pre(debug_backtrace(),"fetchList: debug_backtrace");
////    }
////    
////    $qstr = "select $select";
////    if( $where != "" ) $qstr .= " where $where";
////    if( $order != "" ) $qstr .= " order by $order";
////    
////    //print "fetchVal: qstr: $qstr<br>\n";
////    $result = $dbh->query("$qstr");
////    $r = $result->fetchAll(PDO::FETCH_COLUMN);
////    return $r;
////}
////////////////////////////////////////////////////////////////////////////////////
////function getKeyedHashSingle($dbh,$key,$val,$qstr){
////    $result = $dbh->query("$qstr");
////    $r = $result->fetchAll(PDO::FETCH_ASSOC);
////    //print_pre($r);
////    
////    foreach($r as $row){
////        $hash[$row[$key]] = $row[$val];
////    }
////    return $hash;
////}
////////////////////////////////////////////////////////////////////////////////////
////// returns a hash of entries selected from qstr, the hash keys are the field matching
////// $key.  If $key is not a valid column name, then the key is, in essense, an empty string
////// so you will only get a single entry hash, even though multiple records may have matched
////function getKeyedHash($dbh,$key = "",$qstr = ""){
////    if( ! is_object($dbh)) {
////        print_pre(debug_backtrace(),"dbh not an object: triggerning debug backtrace in getKeyedHash");
////    }
////    $result = $dbh->query("$qstr");
////    $r = $result->fetchAll(PDO::FETCH_ASSOC);
////    //print_pre($r);
////    if( $key == "" ) return $r;
////    
////    foreach($r as $row){
////        $hash[$row[$key]] = $row;
////    }
////    return $hash;
////}
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
////function userInGroup($which){
////    if(isset($_SESSION['u_name'])){     
////        $type = getUserType($_SESSION['u_name']);
////        if( is_array($which)){
////            foreach( $which as $w){
////                if( "$w" == "$type") return true;
////            }
////            return false;
////        }
////        else {
////            if( "$which" == "$type") return true;
////            return false;
////        }
////    } 
////    else{ 
////        //header( "Location: $loginURL" ); 
////        print "Session not registered";
////        die ;
////    } 
////}
////////////////////////////////////////////////////////////////////////////////////
////function require_access($which){
////    if( userInGroup($which)){
////        return true;
////    } 
////    else{ 
////        //header( "Location: $loginURL" ); 
////        print "Access Level inadequate";
////        die ;
////    } 
////}
////////////////////////////////////////////////////////////////////////////////////
////function getUserType($user){
////    global $access;
////    $qstr = "select u_type from users where u_name='$user'";
////    //print "$qstr<br>\n";
////    $result = $access->query($qstr);
////    $g = $result->fetchAll(PDO::FETCH_COLUMN);
////    return $g[0];
////}
////////////////////////////////////////////////////////////////////////////////////
////function getProgCoordList(){
////    global $access;
////    $result = $access->query("select u_name from users where u_type='prog_coord'");
////    $r = $result->fetchAll(PDO::FETCH_COLUMN);
////    return $r;
////}
////////////////////////////////////////////////////////////////////////////////////
/////**
////* essentially unused, found a cleaner way to do this....
////**/
////function get_prog_coord_for_program($p){
////    global $program;
////    $pc = fetchVal($program,"programgroups.pg_prog_coord from programs left join programgroups on programs.p_prog_group = programgroups.pg_name","programs.p_name='$p'");
////    return $pc;
////}
////////////////////////////////////////////////////////////////////////////////////
////function syncProgramCoord(){
////    global $program;
////    // loop over program groups and do a single update for all entries!!
////    // ideally should design the db to do this with a trigger?????
////    //dprint("syncProgramCoord",0,0,"entering sync");
////    $result = $program->query("select pg_name,pg_prog_coord from programgroups");
////    while( $row = $result->fetch(PDO::FETCH_ASSOC)){
////        $qstr = "update programs set p_prog_coord='{$row['pg_prog_coord']}' where p_prog_group='{$row['pg_name']}'";
////        //dprint("syncProgramCoord",0,0,"$qstr");
////        $r2 = $program->query($qstr);
////    }
////}
////////////////////////////////////////////////////////////////////////////////////
////function updateDBFromHash($dbh,$table,$idfield,$o,$doqueries = false){
////    // single db row passed in...
////    // $idfield must be set in each row of output hash..., it is ignored, so you cannot update idfield with this routine...
////    
////    $qstr = createUpdateString($table,$o,$idfield);
////    
////    if( $qstr != "" && $doqueries ){
////        print "running query: $qstr<br>\n";
////        $dbh->query($qstr);
////    }
////    else {
////        print "TEST ONLY: not running query: $qstr<br>\n";
////    }
////}
////////////////////////////////////////////////////////////////////////////////////
////function createUpdateString($table,$i,$idfield){
////    $failed = false;
////    $o = $i;  // make a copy since we are going to muck with keys...
////    $idfields = ( is_array($idfield)) ?  $idfield : $idfields = array($idfield);
////    foreach($idfields as $id){
////        if( isset($o[$id])){
////            $wherecomp[] = "$id=" . wrapDBvalue($o[$id]);
////            unset($o[$id]);
////        }
////        else $failed = true;
////    }
////    
////    $where = implode(" and ",$wherecomp);
////    if( $where == "" ) return "";
////    
////    if($failed) return "";
////    
////    // now process the remaining hash values as setvals
////    foreach($o as $ok => $ov){
////        $setvals[] = "$ok=" . wrapDBValue($ov);
////    }
////    
////    $setstr = implode(",",$setvals);
////    if( $setstr == "" ) return "";
////    return "update $table set $setstr where $where";
////    
////}
////////////////////////////////////////////////////////////////////////////////////
////function insertDBFromHash($dbh,$table,$o,$doqueries = false){
////    // keys of hash to output ($o) must correspond to field names
////    // single db row passed in...
////    // $idfield must be set in each row of output hash..., it is ignored, so you cannot update idfield with this routine...
////    
////    $qstr = createInsertString($table,$o);
////    if( $doqueries ){
////        print "running query: $qstr<br>\n";
////        $dbh->query($qstr);
////    }
////    else {
////        print "TEST ONLY: not running query: $qstr<br>\n";
////    }
////}
////////////////////////////////////////////////////////////////////////////////////
////function createInsertString($table,$i){
////    foreach($i as $field => $value){
////        //if( preg_match("/^\d+$/",$value)) $container="";
////        //else                              $container="'";
////        //
////        //$vals[] = $container . $value . $container;
////        $vals[] = wrapDBvalue($value);
////        $flds[] = $field;
////    }
////    $valstr = implode(",",$vals);
////    $fstr = implode(",",$flds);
////    $qstr = "insert into $table ($fstr) values ($valstr)";
////    return $qstr;
////}
////////////////////////////////////////////////////////////////////////////////////
////function wrapDBvalue($value){
////        if( preg_match("/^\d+$/",$value)) $container="";
////        else                              $container="'";
////        return $container . $value . $container;
////}
/*

Sort programs by:
   needs attention
   completed
   program group
   coordinator
   
view programs:
   mine-default
   all
   program group
   
task display options:
  compact, summary, full, date, showed notes
  color-by: program_coordinator, program_group
////////////////////////////////////////////////////////////////////////////////
Possibly add program group into program schema.
Possibly add some color options to program_group
possibly move USYVL tournAssign into the workflow

talk about those options with Randy
////////////////////////////////////////////////////////////////////////////////
   
Program
  Name
  Coordinator
  Registration Count
  Location1 -> Location table
  Location2 -> Location table
  PM_Date
  PM_BegTime
  PM_EndTime
  PM_status (tentative, confirmed)
  PM_Notes
  CC_Date
  CC_BegTime
  CC_EndTime
  CC_status (tentative, confirmed)
  CC_Notes
  Season_BegDate
  Season_EndDate
  School_Year_BegDate
  School_Year_EndDate
  Spring_Break_BegDate
  Spring_Break_EndDate
  Weekday_Schedule
    Day
    BegTime
    EndTime
  Weekend Schedule
    Day
    BegTime
    EndTime
  School District 
  Site Directors obtained by matching Program.Name to SiteDirector.Program
  Clinicians obtained by matching Program.Name to Clinician.Program


Location: each site could have multiple Locations  (use program name for id?)
Facility?
  f_id
  f_name
  f_prog_name
  f_active
  f_type
  f_Lat
  f_Lon
  f_LatLon Quality
  f_LatLon Source
  f_Type   (grass, sand, indoor)
  f_Bathrooms
  f_Permitting Agency -> Permitting Agency - one per location, could possibly move the fields here
  
Personnel: each site could have multiple Clinicians
  pnl_id
  pnl_name_last
  pnl_name_first
  pnl_prog_name
  pnl_active
  pnl_type             - clinician or site director
  pnl_email
  pnl_addr
  pnl_phone
  pnl_fax
  pnl_last_training    -- date
  pnl_days_attended    - clinician only -- this is almost a task, probably shouldnt be kept here....
  pnl_notes
  pnl_form_1033_onfile - true/false, yes/no
 
  pnl_form_1033_sent   -- this is a task
  pnl_form 1033 rcvd   -- this is a task
  pnl_check_sent       - clinician only -- this is a task
  
Agency ???  Could cover both permitting agency and school districts...
SchoolDistrict: each program could have multiple school districts AND/OR each school district could be used by multiple programs
  a_id
  a_type
  a_Name
  a_abbr
  a_addr
  a_city
  a_state
  a_zip
  a_phone
  a_fax
  a_email
  a_url
  a_restrictions
  a_instructions
  a_notes
  
  Email
  Contact
  Restrictions
  Notes/Instructions
  Flyer Approval request   -task    
  Flyer Approval received  -task
  Flyers sent              -task
  Flyers sent note         -task
  Begin School Year
  End School Year
  Begin Spring Break
  End Spring Break
  Notes
  
  sd_name
  sd_abbr
  sd_addr
  sd_state
  sd_city
  sd_zip
  sd_contact
  sd_phone
  sd_fax
  sd_email
  sd_school_beg
  sd_school_end
  sd_springbreak_beg
  sd_springbreak_end
  sd_restrictions
  sd_instructions
  sd_notes
  
  
Permitting Agency: one per location
  Location.Name
  Name
  Abbreviation
  Address
  Phone
  Cell
  Fax
  Email
  Contact
  Notes
  Restrictions
  
  pa_name
  pa_abbr
  pa_addr
  pa_state
  pa_city
  pa_zip
  pa_contact
  pa_phone
  pa_cell
  pa_fax
  pa_email
  pa_restrictions
  pa_instructions
  pa_notes
  
Contact
  c_id
  c_firstname
  c_lastname
  c_addr
  c_state
  c_city
  c_zip
  c_phone
  c_fax
  c_mobile
  c_email

  
Think about importing and exporting data from the db....
ie: if I change the schema, how to migrate data to new schema....

Need to have filename based on season.  Use same naming conventions as sched gen...
By default use latest one from a listing of the program database....

Are there any other fields we should consider adding for the sched gen...
Do the geocoding here??
What about generating events schedules for import into rainier.
What about using said event schedules to feed right into sched gen tournament assign?

Tasks:
  Verbally verified site director return
  verbally verify site clinician return
  verify location availability verify permitting agency information
  set season dates
  set registration dates
  set times
  sick days
  propose parent meeting date and time
  confirm parent meeting date and time
  propose coaching clinic meeting date and time
  confirmed coaching clinic meeting date and time
  San clinician shirt and shorts
  send a site director sure
  verify receipt of clinician shirt and shorts
  verify receipt site director sure
  verify training status of clinician
  verify a trained status of site director
  scheduled training for clinician
  schedule training for site director
  Mail 1030 form to clinician
  Mail 1030 form to site director
  form 1030 received from clinician
  form 1030 received from site director
  assign username and password for site director
  assign username and password for clinician
  assemble site box
  assemble medical kit
  ship medical kit
  ship site box
  confirm receipt of medical kit and said box
  send parent meeting calling post
  send a coaching clinic calling powers
  form teams before apparent meeting
  received team changes
  receive site form 1030s after parent meeting
  received site 1030s after first day
  
  apply for a permit
  permit received
  permit paid
  permit deposit
  permit deposit returned
  
  verify school District information
  request flyer approval
  fire approval request approved
  verify fly or needs when printing
  print flyers
  Flyers since
  verify flyer return mailing address
  press release
  
  
  
  
  Operational Conditions for USYVL - are these OK?
    Program Name is a primary key - this means that
      Program name must be unique - 
      Changing program name - (deleting and adding) will cause fields to be invalidated
      
    Volunteers
      can only be assigned to one program
      can have type CL SD or CL+SD (this enumeration could cause us grief later)
      consider possibility of using an int and doing bitwise comparisons, easier to combine, but 
      would have to use check boxes for CL and SD... 
      
      Clinician     = CL = 1
      Site Director = SD = 2
      Head Coach    = HC = 4
      Asst. Coach   = AC = 8
      
      possibly have a db table for such mappings...
     
    Location Name is a primary key - this means that
      Each location name must be unique
      Changing location name - deleting and adding will cause fields to be invalidated
      
      
    A program id and a program name would be comparitive ??? keys (a one to one mapping)
    and would allow a program name to be changed without invalidating data.
    
    Should figure out where constraints and foreign key clauses may be able to help me.

Holidays - dates that usyvl will not run
  h_date   - 2009-02-11
  h_type   - holiday, rainday, snowday, weather, facility closed
  h_desc   - Labor Day
  h_p_id   - program id if particular to one site, if null or blank, applies to all
    
Events - tied to a given location which in turn is tied to a single program (if needed)
  Should have an event for every program on everyday they meet...roughly 16x<number of programs>
  Want a generation routine that takes the times shown in the program schedule and expands that out
  for each day.  Taking into account any holidays
  e_id
  e_name or
  e_desc
  e_type       - this would be a departure from cmi, but possibly a good one - Games,Practice,Tournament
  e_date
  e_beg_time
  e_end_time
  e_f_id
  e_exa
  e_exb
*/

?>
