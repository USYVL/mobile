<?php
require_once("config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");
require_once("instSummaries_inc.php");
//require_once("version.php");

define('DEBUGLEVEL',0);

$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";
$content['css'] = "";
$content['scripts'] = "";

$content['scripts'] .= '<script type="text/javascript" src="js/index.js"></script>' . "\n";
$content['css']  .= '<link rel="stylesheet" href="css/usyvl.css" type="text/css">' . "\n";

$ms = new usyvlMobileSite();

$content['body'] .= $ms->display();
$content['title'] = $ms->getTitle();  // title is not set till after display is run...
$content['errs'] .= "";

//ob_start();
include("tpl/usyvl.tpl");
//print ob_get_clean();
?>

