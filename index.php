<?php
require_once("inc/dbManagement.inc");
require_once("inc/usyvlDB.inc");

define('DEBUGLEVEL',0);

$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";


class mwfMobileSite {
    function __construct(){
        $this->mode = "";
        $this->title = "Main Menu";
        $this->processGET();
    }
    function processGET(){
        if( isset($_GET['mode'])) $this->mode = $_GET['mode'];
    }
    function dispMain(){
        $b = "";
        $b .= $this->topmenu("Main Menu");
        $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=states\">Get Your Schedule</a></li>\n";
        $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Auto Mode</a></li>\n";
        $b .= "  <li><a href=\"./scorekeeper.php\">Score Keeper</a></li>\n";
        $b .= "</ol>\n";
        $b .= "</div> <!-- close top menu div -->\n\n";


        //$b .= "<br />\n";
        //$b .= "<div class=\"light padded\"><!-- test div -->\n";
        $b .= "<div class=\"content-padded content-full\">\n";
        $b .= "<h2 class=\"light\">Content</h2>\n";
        //$b .= "  <div>\n";
        $b .= "    <p>\n";
        $b .= "    This is a very preliminary version of the mobile site.\n";
        $b .= "    </p>\n";
        $b .= "    <p>\n";
        $b .= "    Its designed to allow mobile access to schedules during the course of the season.\n";
        $b .= "    </p>\n";
        //$b .= "  </div>\n";
        $b .= "</div>\n";
        //$b .= "</div> <!-- close of test div -->\n";
        return "$b";
    }
    function dispStates(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $this->title = "USYVL Mobile - Select State";
        
        $b = "";
        $b .= $this->topmenu("Select State");
        $b .= "<ul>\n";
        $states = $sdb->fetchList("distinct evstate from ev");
        foreach( $states as $state){
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=programs&state=$state\">$state</a></li>\n";
        }
        
        $b .= "</ul>\n";
        return "$b";
    }
    function dispPrograms(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $this->title = "USYVL Mobile - Select Program from $state";
        
        $b = "";
        $b .= $this->topmenu("Select Program");
        $b .= "<ul>\n";
        $programs = $sdb->fetchList("distinct evprogram from ev","evstate='" . $_GET['state'] . "'");
        foreach( $programs as $program){
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=divisions&state=$state&program=$program\">$program</a></li>\n";
        }
        
        $b .= "</ul>\n";
        return "$b";
    }
    function dispDivisions(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $this->title = "USYVL Mobile - Select Division from $state Program $program";
        
        $b = "";
        $b .= $this->topmenu("Select Age Division");
        $b .= "<ul>\n";
        $divisions = $sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","tmprogram='" . $_GET['program'] . "' order by so_order");
        foreach( $divisions as $division){
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=teams&division=$division&state=$state&program=$program\">$division</a></li>\n";
        }
        
        $b .= "</ul>\n";
        return "$b";
    }
    function dispTeams(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $division = $_GET['division'];
        $this->title = "USYVL Mobile - Select Team in $division Division from $state Program $program";
        
        $b = "";
        $b .= $this->topmenu("Select Team");
        $b .= "<ul>\n";
        //$teams = $sdb->fetchList("distinct name from tm","program='" . $_GET['program'] . "' and div='" . $division . "'");
        $data = $sdb->getKeyedHash('tmid',"select * from tm where tmprogram='" . $_GET['program'] . "' and tmdiv='" . $division . "'");
        foreach( $data as $k => $d){
            $team = $d['tmname'];
            $tmid = $k;
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=sched&tmid=$tmid&team=$team&division=$division&state=$state&program=$program\">$team</a></li>\n";
        }
        
        $b .= "</ul>\n";
        return "$b";
    }
    function dispSched(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $division = $_GET['division'];
        $team = $_GET['team'];
        $tmid = $_GET['tmid'];
        $this->title = "USYVL Mobile - $team Schedule";
        
        $b = "";
        //$b .= $this->topmenu("$team Schedule");
        $b .= "<div class=\"content-padded content-full\">\n";
        $b .= "<h1>$team Schedule</h2>\n";
        //$b .= "<ul>\n";
        
        // This first part deals with practices that are NOT in the sched db (at this time)..
        // select * from ev left outer join tm on ev.program = tm.program where ev.name like '%Practice%' and tm.tmid = '231';
        //$prac = $sdb->getKeyedHash('evid',"select * from ev left outer join tm on evprogram = tmprogram where evname like '%Practice%' and tmid = $tmid");
        //foreach( $prac as $d){
        //    $date = $d['evds'];
        //    $court = $d['evcourt'];
        //    $bt = $d['evtime_beg'];
        //    $et = $d['evtime_end'];
        //    $b .= "<li>$date - $bt - $et - Ct$court - Practice</li>\n";
        //}
        
        // need to get team id
        $data = $sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid where tmid1 = $tmid or tmid2 = $tmid order by evds");
        foreach( $data as $d){
            $evloc = $d['evlocation'];
            $evnm = $d['evname'];
            $date = $d['evds'];
            $court = $d['court'];
            //$bt = $d['evtime_beg'];
            //$et = $d['evtime_end'];
            $time = $d['time'];
            // depending on type of event, court may be specified in one of two locations...
            $b .= "<p>\n";
            $b .= "$date - $time<br />";
            $b .= "$evnm<br />\n";
            $b .= "$evloc - Court $court\n";
            $b .= "</p>\n";
            //$b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=sched&team=$team&division=$division&state=$state&program=$program\">$team</a></li>\n";
        }
        $b .= "</div>\n";
        //$b .= "</ul>\n";
        return "$b";
    }
    function display(){
        global $content;
        $b = "";
        //$b .= "Calling display(mode: " . $this->mode . "):<br />";

        //$content['errs'] .= "Mode: " . $this->mode . "\n";
        
        switch ($this->mode){
            case "select" :
                $b .= $this->dispSelect();
                break;
            case "states" :
                $b .= $this->dispStates();
                break;
            case "programs" :
                $b .= $this->dispPrograms();
                break;
            case "divisions" :
                $b .= $this->dispDivisions();
                break;
            case "teams" :
                $b .= $this->dispTeams();
                break;
            case "sched" :
                $b .= $this->dispSched();
                break;
            default :
                $b .= $this->dispMain();
                break;
        }
                
        $b .= $this->button("http://www.usyvl.org","USYVL Website");
        $b .= $this->button("./","Main Menu");
        return "$b";
    }
    function topmenu($label = ""){
        $b = "";
        $b .=  "<div class=\"menu-full menu-detailed menu-padded\">\n";
        $b .=  "<h1 class=\"light menu-first\">$label</h1>\n"; 
        $b .=  "<ol> \n";
        //$b .=  "<ol> \n";
        return $b;
        
    }
    function button($href = "", $label = ""){
        $b = "";
        $b .= "<a href=\"" . $href . "\" class=\"button-full button-padded\">$label</a>\n";
        return $b;
    }
    function getTitle(){
        return $this->title;
    }
}

$ms = new mwfMobileSite();
$ms->processGET();

$content['body'] .= $ms->display();
$content['title'] = $ms->getTitle();  // title is not set till after display is run...
$content['errs'] .= "";

//ob_start();
include("tpl/usyvl.tpl");
//print ob_get_clean();


// Need to figure out a clever way to determine how much information we have
/*
How to handle DB, dont need full dbmgmt pkg as we dont need to write to or build this db

Punch through the following levels:
season  # can just take whatever db we have...
state
program
division
team     # get tmidmake
day  or display all days
*/

?>

