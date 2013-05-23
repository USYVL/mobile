<?php
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function csv2array($file){
    $fi = fopen($file,"r");
    if( $fi === false ) {
        print "Error: opening file $file<br>\n";
      return;
    }
    $headerrow = true;
    while(($data = fgetcsv($fi)) !== false ){
        //print_pre($data,"single row of data");
        $rownum++;
        foreach($data as &$d)  $d = trim($d);
        if($headerrow){
            $header = $data;
            $headerrow = false;
            continue;
        }
        
        
        if( count($header) > count($data)){
            $padcount = count($header) - count($data) ;
            //print "Error on counts..." . count($header) . " != " . count($data) . "padding data by $padcount fields<br>\n";
            for($i=0;$i<$padcount;$i++){
                array_push($data,"");
            }
        }
        $out[] = array_combine($header,$data);
    }
    fclose($fi);
    return $out;
}
////function array2dbinsert($a,$dbh,$table,$skipcols){
////    // would be nice to be able to query
////    $r = $dbh->getTableDescriptionHash($table);
////    foreach($r as $v){
////        if( ! in_array($v['name'],$skipcols))  $dbcols[] = $v['name'];
////    }
////    print_pre($r,"pragma result");
////    
////    
////    // now that we have a list of column names, we need to compare to the hash
////    $datacols = array_keys($a[0]);
////    
////    $exportcols = array_intersect($dbcols,$datacols);
////    $skippedcols = array_diff($dbcols,$datacols);
////    print_pre($skippedcols,"skipped columns");
////    print_pre($exportcols,"export columns");
////    
////}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function array2table($data,$cols = 4){
    $len = count($data);
    $rows = (int) ($len/$cols);
    $rows += (($len%$cols) == 0) ? 0 : 1 ;
    $width = (int) 100/$cols;
    
    
    $buf = "";
    $buf .= "<div style='text-align: left;'>";
    $buf .= "<table style='border: solid 0px; width: 100%; text-align: center; margin-left: auto; margin-right: auto;'>";
    $buf .= "<tr>\n";
    for( $i = 0; $i < $cols ; $i++){
        $buf .= "<td style='text-align: left; vertical-align: top; width: ${width}%'>\n";
        $coldata = array_slice($data,$i * $rows,$rows);
        foreach( $coldata as $d){
            $buf .= "$d<br />\n";
        }
        $buf .= "</td>\n";
    }
    $buf .= "</tr>\n";
    $buf .= "</table>\n";
    $buf .= "</div>\n";
    return $buf;
}
////////////////////////////////////////////////////////////////////////////////
// given a hash, this builds a text buffer with a header line containing field names
////////////////////////////////////////////////////////////////////////////////
/**
 * convert an array of hashes into a CSV export buffer
 * @author Aaron Martin
 * @name 
 * @param array $hash is an array of hashs containing the data to be converted.  The key for each hash is the column name
 * @param array $columns is an array of column names to output, if null, then an array is built from a merge of the columnNames for each hash
 * @param string $sep field separator.  Defaults to ",".
 * @param boolean $enclosespaces determines whether spaces should be enclosed with quotes.  defaults to true.
 * @return string a CSV buffer suitable to write to a file.
**/
function buildCSV($hash,$columnList = null,$sep = ",",$enclosespaces = true){
    
    
    if( ! is_array($hash)){
        print "<font color='red'>Input is not an Array</font><br>\n";
        return;
    }
    $keys = array_keys($hash);
    if( ! is_array($hash[$keys[0]])){
        print "<font color='red'>Input is not a 2D Array</font><br>\n";
        return;
    }
    
    if( $columnList == null ){
        //$columns = array_keys($hash[$keys[0]]);
        $columns = array();
        foreach( $hash as $h){
            foreach($h as $k => $v){
                $colkeys[$k] = $v;
            }
            //$columns = array_merge($columns,array_keys($h));
        }
        $columns = array_keys($colkeys);
    }
    else $columns = $columnList;
    
    //print_pre($columns,"column names");
    
    if( ! is_array($columns)){
        print "<font color='red'>ColumnList is not an Array</font><br>\n";
        return;
    }
    $header = implode("$sep",$columns);
    //print "$filename header: $header<br>\n";
    //print_pre($hash);
    $buf = $header . "\n";
    foreach($hash as $h){
        //print_pre($h);
        $accum = array();
        foreach($columns as $column){
            if( preg_match("/$sep/",$h[$column])  || ( $enclosespaces && preg_match("/ /",$h[$column]))){
                $accum[] = "\"{$h[$column]}\"";
            }
            else {
                $accum[] = "{$h[$column]}";
            }
        }
        
        // should look into using fputcsv...
        $buf .= implode("$sep",$accum) . "\n";
    }
    return $buf;
}

?>
