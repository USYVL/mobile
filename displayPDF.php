<?php

require_once("config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");

// Need to pass in a 'pdid' argument
$pdid = '71';
$d = $GLOBALS['sdb']->getKeyedHash('pdid','select * from pdfs where pdid = ?;',array($pdid));

if( isset($d[$pdid])){
    $name=$d[$pdid]['pdfbase'];
    //print_pre($d[$id],"Data")
    header("Content-type:application/pdf");
    header("Content-Disposition:inline;filename=$name");
    print $d[$pdid]['pdfstr'];
}
else {
    print "Sorry we had an error<br>\n";
    print_pre($d,"pdid");
}
 ?>
