<?php
require_once("config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");
require_once("tournSummaries_inc.php");
require_once("digitalClock.php");

define('DEBUGLEVEL',0);

$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";
$content['css'] = "";
$content['scripts'] = "";

$content['scripts'] .= '<script type="text/javascript" src="js/locator.js"></script>' . "\n";
$content['scripts'] .= '<script type="text/javascript" src="js/tsumm.js"></script>' . "\n";
$content['css']  .= '<link rel="stylesheet" href="css/usyvl.css" type="text/css">' . "\n";

// Change Tournament Summaries to Tournament Pools


$ms = new mwfMobileSite_tourn();

$content['body'] .= $ms->display();
$content['title'] = $ms->getTitle();  // title is not set till after display is run...
$content['errs'] .= "";

//ob_start();
include("tpl/usyvl.tpl");
//print ob_get_clean();
/*
select distinct evid from ev where evprogram = 'Goleta' and evistype = 'INTE';
select * from ev left join gm on ev.evid = gm.evid where ev.evid=262 and evprogram = 'Goleta' and evistype = 'INTE';
*/
?>

