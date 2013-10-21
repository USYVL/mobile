<?php
require_once("config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");
require_once("mwfMobileSite.php");
require_once("version.php");

define('DEBUGLEVEL',0);

$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";

// The first three layers are dealt with in the parent class
class usyvlMobileSite extends mwfMobileSite {
    function __construct(){
        parent::__construct();
    }
   function registerExtendedFunctions(){
       $this->registerFunc('credits'  , 'dispCredits'   );
       $this->registerFunc('settings' , 'dispSettings'  );
       $this->registerFunc('auto'     , 'dispAuto'      );
   }
   function dispAuto(){
       $b = "Location:";
       $b .= "<div id=\"device_location\">NA</div>";
       $b .= "<div id=\"proximal_events\">Proximal Events: NA</div>";
       return $b;
   }
   function dispSettings(){
       $this->title = "USYVL Mobile - Settings";
       
       $b = $this->contentDiv("Settings","<p>\nSettings Coming Soon!\n</p>\n");
       
       return "$b";
   }
   function dispCredits(){
       $this->title = "USYVL Mobile - Credits";
       
       $b = "";
       $b .= $this->contentDiv("Version","<p class=\"credits author\">\nVersion: " . $GLOBALS['version'] . "\n</p>\n");
       $bb .= "<p class=\"credits\">\n";
       $bb .= "HTML 5\n";
       $bb .= "</p>\n";
       $bb .= "<p class=\"credits\">\n";
       $bb .= "CSS 3\n";
       $bb .= "</p>\n";
       $bb .= "<p class=\"credits\">\n";
       $bb .= "Mobile Web Framework (MWF) 1.3\n";
       $bb .= "</p>\n";
       $bb .= "<p class=\"credits\">\n";
       $bb .= "jQuery 1.10.x\n";
       $bb .= "</p>\n";
       $b .= $this->contentDiv("Tech",$bb);
       $b .= $this->contentDiv("Author","<p class=\"credits author\">\nCreated for USYVL by Aaron Martin\n</p>\n");
       $b .= $this->contentDiv("Art/Graphics","<p class=\"credits\">\nProvided by USYVL</p>\n");
       return "$b";
   }
}

$ms = new usyvlMobileSite();

$content['body']  .= $ms->display();
$content['title']  = $ms->getTitle();  // title is not set till after display is run...
$content['errs']  .= "";

//ob_start();
include("tpl/usyvl.tpl");
//print ob_get_clean();
?>

