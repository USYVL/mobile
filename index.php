<?php
require_once("inc/dbManagement.inc");
require_once("inc/usyvlDB.inc");
require_once("version.php");

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
        $sdb = $GLOBALS['dbh']['sdb'];
        $seasons = $sdb->fetchList("distinct evseason from ev");
        
        $b = "";
        $b .= $this->topmenu("Main Menu");
        $b .= "<ul>\n";
        
        foreach($seasons as $season){
            $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=states&season=$season\">$season Schedules</a></li>\n";
        }
        $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Auto Mode</a></li>\n";
        $b .= "  <li><a href=\"./scorekeeper.php?team_a=Team C&team_b=Team D\">Score Keeper</a></li>\n";
        $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=credits\">Credits</a></li>\n";
        $b .= "</ul>\n";
        $b .= "</div> <!-- close top menu div -->\n\n";


        //$b .= "<br />\n";
        //$b .= "<div class=\"light padded\"><!-- test div -->\n";
        $b .= "<div class=\"content content-full\">\n";
        $b .= "<h2 class=\"light\">Content</h2>\n";
        //$b .= "  <div>\n";
        $b .= "    <p>\n";
        $b .= "    This is a very preliminary version of the mobile site";
        $b .= "    designed to allow mobile access to schedules during the course of the season.\n";
        $b .= "It is still very much under development.\n";
        $b .= "    </p>\n";
        //$b .= "  </div>\n";
        $b .= "</div>\n";
        //$b .= "</div> <!-- close of test div -->\n";
        return "$b";
    }
    function dispCredits(){
        $this->title = "USYVL Mobile - Credits";
        
        $b = "";
        //$b .= $this->topmenu("Credits");
        $b .= "<div class=\"content\">\n";
        $b .= "<h2 class=\"light\">Version</h2>\n";
        //$b .= "<h2 class=\"light\">Version: " .  $GLOBALS['version'] . "</h2>\n";
        $b .= "<p class=\"credits author\">\n";
        $b .= "Version: " . $GLOBALS['version'] . "\n";
        $b .= "</p>\n";
        $b .= "</div>\n";

        $b .= "<div class=\"content\">\n";
        $b .= "<h2 class=\"light\">Tech</h2>\n";
        $b .= "<p class=\"credits\">\n";
        $b .= "HTML 5\n";
        $b .= "</p>\n";
        $b .= "<p class=\"credits\">\n";
        $b .= "CSS 3\n";
        $b .= "</p>\n";
        $b .= "<p class=\"credits\">\n";
        $b .= "Mobile Web Framework (MWF) 1.3\n";
        $b .= "</p>\n";
        $b .= "<p class=\"credits\">\n";
        $b .= "jQuery 1.10.x\n";
        $b .= "</p>\n";
        $b .= "</div>\n";
        
        $b .= "<div class=\"content\">\n";
        $b .= "<h2 class=\"light\">Author</h2>\n";
        $b .= "<p class=\"credits author\">\n";
        $b .= "Created for USYVL by Aaron Martin\n";
        $b .= "</p>\n";
        $b .= "</div>\n";

        $b .= "<div class=\"content\">\n";
        $b .= "<h2 class=\"light\">Art/Graphics</h2>\n";
        $b .= "<p class=\"credits\">\n";
        $b .= "Provided by USYVL";
        $b .= "</p>\n";
        $b .= "</div>\n";
        return "$b";
    }
    function dispStates(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $season = $_GET['season'];
        $this->title = "USYVL Mobile - Select State $season";
        
        $b = "";
        $b .= $this->topmenu("Select State");
        $b .= "<ol>\n";
        $states = $sdb->fetchList("distinct lcstate from ev left join lc on ev_lcid = lcid where evseason='$season'");
        foreach( $states as $state){
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=programs&season=$season&state=$state\">$state</a></li>\n";
        }
        
        $b .= "</ol>\n";
        $b .= "</div>\n";
        return "$b";
    }
    function dispPrograms(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $season = $_GET['season'];
        $this->title = "USYVL Mobile - Select Program from $state for $season";
        
        $b = "";
        $b .= $this->topmenu("Select Program");
        $b .= "<ol>\n";
        $programs = $sdb->fetchList("distinct evprogram from ev left join lc on ev_lcid = lcid ","( lcstate='$state' and evseason='$season' )");
        foreach( $programs as $program){
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=divisions&season=$season&state=$state&program=$program\">$program</a></li>\n";
        }
        
        $b .= "</ol>\n";
        $b .= "</div>\n";
        return "$b";
    }
    function dispDivisions(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $this->title = "USYVL Mobile - Select Division from $state Program $program for $season";
        
        $b = "";
        $b .= $this->topmenu("Select Age Division");
        $b .= "<ol>\n";
        $divisions = $sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram='$program' and tmseason='$season' ) order by so_order");
        foreach( $divisions as $division){
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=teams&division=$division&season=$season&state=$state&program=$program\">$division</a></li>\n";
        }
        
        $b .= "</ol>\n";
        $b .= "</div>\n";
        return "$b";
    }
    function dispTeams(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $division = $_GET['division'];
        $this->title = "USYVL Mobile - Select Team in $division Division from $state Program $program for $season";
        
        $b = "";
        $b .= $this->topmenu("Select Team");
        $b .= "<ol>\n";
        //$teams = $sdb->fetchList("distinct name from tm","program='" . $_GET['program'] . "' and div='" . $division . "'");
        $data = $sdb->getKeyedHash('tmid',"select * from tm where ( tmprogram='$program' and tmdiv='$division' and tmseason='$season' )");
        foreach( $data as $k => $d){
            $team = $d['tmname'];
            $tmid = $k;
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=sched&season=$season&tmid=$tmid&team=$team&division=$division&state=$state&program=$program\">$team</a></li>\n";
        }
        
        $b .= "</ol>\n";
        $b .= "</div>\n";
        return "$b";
    }
    function dispSched(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $season = $_GET['season'];
        $program = $_GET['program'];
        $division = $_GET['division'];
        $team = $_GET['team'];
        $tmid = $_GET['tmid'];
        $this->title = "USYVL Mobile - $team Schedule for $season";
        
        $b = "";
        //$b .= $this->topmenu("$team Schedule");
        $b .= "<div class=\"content content-full\">\n";
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
        $data = $sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid left join lc on ev.ev_lcid = lcid where ( ( tmid1=$tmid or tmid2=$tmid ) and evseason='$season' ) order by evds");
        foreach( $data as $d){
            $evloc = $d['lclocation'];
            $evnm = $d['evname'];
            $date = $d['evds'];
            $court = $d['court'];
            //$bt = $d['evtime_beg'];
            //$et = $d['evtime_end'];
            $time = $d['time'];
            // Want to determine type of entry for possible filtering later
            // possibilities are: Practice, Home Game, Tournament
            if( preg_match("/^Practice/",$evnm)){
                $evtype = "practice";
            }
            elseif( preg_match("/^Intersite Game Day/",$evnm)){
                $evtype = "tournament";
            }
            elseif( preg_match("/^Games/",$evnm)){
                $evtype = "games";
            }
            else {
                $evtype = "unknown";
            }
            
            // depending on type of event, court may be specified in one of two locations...
            $b .= "<p class=\"$evtype\">\n";
            $b .= "$date - $time<br />";
            $b .= "$evnm<br />\n";
            $b .= "$evloc - Court $court\n";
            if( $d['tmid1'] != "" and $d['tmid2'] != "" ){
                // need to get the other teams name
                // this team could be either tmid1 or tmid2
                $othertmid = ( $d['tmid1'] == $tmid ) ? $d['tmid2'] : $d['tmid1'];
                $othertmname = $sdb->fetchVal('tmname from tm',"tmid=$othertmid");
                $b .= "<br /><a href=\"scorekeeper.php?team_a=$team&team_b=$othertmname\">Scorekeep This Game</a>\n";
            }
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
            case "credits" :
                $b .= $this->dispCredits();
                break;
            case "auto" :
                $b .= $this->dispAuto();
                break;
            default :
                $b .= $this->dispMain();
                break;
        }
                
        $b .= $this->button("http://www.usyvl.org","USYVL Website");
        $b .= $this->button("./","Main Menu");
        return "$b";
    }
    function dispAuto(){
        $b = "Location:";
        $b .= "<div id=\"device_location\">NA</div>";
        $b .= "<div id=\"site_proximity\">Nearest USYVL site: NA</div>";
        return $b;
    }
    // This is an unbalanced function, opens a div
    function topmenu($label = ""){
        $b = "";
        $b .=  "<div class=\"menu\">\n";
        $b .=  "<h1 class=\"light menu-first\">$label</h1>\n"; 
        //$b .=  "<ol> \n";
        //$b .=  "<ol> \n";
        return $b;
        
    }
    function button($href = "", $label = ""){
        $b = "";
        $b .= "<a href=\"" . $href . "\" class=\"button button-padded\">$label</a>\n";
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

