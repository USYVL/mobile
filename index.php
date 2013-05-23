


<?php
require_once("inc/dbManagement.inc");
require_once("inc/usyvlDB.inc");


class mwfMobileSite {
    function __construct(){
        $this->mode = "";
        $this->processGET();
        
        
        ///$this->b_foot .= $this->button("./","Return to Main Menu");
        ///$this->b_foot .= $this->button("http://www.usyvl.org","USYVL Home");
        
        ////$this->b_foot .= "<div id=\"footer\"> \n";
        ////$this->b_foot .= "    <p>United States Youth Volleyball League &copy; 2013 USYVL<br> \n";
        ////$this->b_foot .= "    <a href=\"http://www.usyvl.org/help\">Help</a> | <a href=\"http://www.usyvl.org\">View Full Site</a></p> \n";
        ////$this->b_foot .= "</div>\n";
        
       
    }
    function processGET(){
        if( isset($_GET['mode'])) $this->mode = $_GET['mode'];
    }
    function beg(){
    }
    function end(){
    }
    function dispMain(){
        $b = "";
        $b .= $this->topmenu("Main Menu");
        $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=states\">Get Your Schedule</a></li>\n";
        $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Auto Mode</a></li>\n";
        $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=auto\">Score Keeper</a></li>\n";
        $b .= "</ol>\n";


        $b .= "<div class=\"content padded\">\n";
        $b .= "<h1 class=\"light\">My Content</h1>\n";
        $b .= "  <div>\n";
        $b .= "    <p>\n";
        $b .= "    My content paragraph 1.\n";
        $b .= "    </p>\n";
        $b .= "    <p>\n";
        $b .= "    My content paragraph 2.\n";
        $b .= "    </p>\n";
        $b .= "  </div>\n";
        $b .= "</div>\n";
        return "$b";
    }
    function dispStates(){
        $sdb = $GLOBALS['dbh']['sdb'];
        
        $b = "";
        $b .= $this->topmenu("Select State");
        $b .= "<ul>\n";
        $states = $sdb->fetchList("distinct state from ev");
        foreach( $states as $state){
           $b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=programs&state=$state\">$state</a></li>\n";
        }
        
        $b .= "</ul>\n";
        return "$b";
    }
    function dispPrograms(){
        $sdb = $GLOBALS['dbh']['sdb'];
        
        $b = "";
        $b .= $this->topmenu("Select Program");
        $b .= "<ul>\n";
        $programs = $sdb->fetchList("distinct program from ev","state='" . $_GET['state'] . "'");
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
        
        $b = "";
        $b .= $this->topmenu("Select Age Division");
        $b .= "<ul>\n";
        $divisions = $sdb->fetchList("distinct div from tm","program='" . $_GET['program'] . "'");
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
        
        $b = "";
        $b .= $this->topmenu("Select Team");
        $b .= "<ul>\n";
        //$teams = $sdb->fetchList("distinct name from tm","program='" . $_GET['program'] . "' and div='" . $division . "'");
        $data = $sdb->getKeyedHash('tmid',"select * from tm where program='" . $_GET['program'] . "' and div='" . $division . "'");
        foreach( $data as $k => $d){
            $team = $d['name'];
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
        
        $b = "";
        $b .= $this->topmenu("$team Schedule");
        $b .= "<ul>\n";
        
        
        // need to get team id
        $data = $sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid where tmid1 = $tmid or tmid2 = $tmid order by ds");
        foreach( $data as $d){
            $evnm = $d['name'];
            $date = $d['ds'];
            $court = $d['court'];
            $bt = $d['time_beg'];
            $et = $d['time_end'];
            $b .= "<li>$date - $bt - $et - Ct$court - $evnm</li>\n";
            //$b .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=sched&team=$team&division=$division&state=$state&program=$program\">$team</a></li>\n";
        }
        
        $b .= "</ul>\n";
        return "$b";
    }
    function display(){
        $b = "";
        //$b .= "Calling display(mode: " . $this->mode . "):<br />";

        $content['errs'] .= "Mode: " . $this->mode . "\n";
        
        switch ($this->mode){
            case "" :
                $b .= $this->dispMain();
                break;
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
                break;
        }
                
        $b .= $this->button("http://www.usyvl.org","USYVL Home");
        $b .= $this->button("./","Main Menu");
        return "$b";
    }
    function topmenu($label = ""){
        $b = "";
        $b .=  "<div class=\"menu-full menu-detailed menu-padded\">\n";
        $b .=  "<h1 class=\"light menu-first\">$label</h1>\n"; 
        $b .=  "<ol> \n";
        return $b;
        
    }
    function button($href = "", $label = ""){
        $b = "";
        $b .= "<a href=\"" . $href . "\" class=\"button-full button-padded\">$label</a>\n";
        return $b;
    }
}

$ms = new mwfMobileSite();
$ms->processGET();

$content['title'] = "Fake Title";
$content['body'] .= $ms->display();
//$content['body'] .= "Fake Content";
$content['errs'] .= "Fake Error\n";


// Some sample data to test dynamically building the mobile pages...
$statearray = array(
    'ca' => "California",
    'wa' => "Washington",
    'nv' => "Nevada",
    'or' => "Oregon",
    );

$sitesarray = array(
    'ca' => array("Agoura Hills","Goleta", "Ojai","Ventura"),
    'wa' => array("Seattle","Tacoma"),
    'or' => array("Salem"),
    'nv' => array("Reno"),
    );

$programarray = array(
    'Agoura Hills' => array(
        "Game1 - 10:00AM Court 3 vs. Amazing Hamsters",
        "Game2 - 10:30AM Court 5 vs. Gophers It",
        "Game3 - 11:00AM Court 4 vs. Rapping Rats",
        "Game4 - 11:30AM Court 3 vs. Minnies Mice"),
    'Goleta' => array(
        "Game1 - 10:00AM Court 3 vs. Amazing Hamsters",
        "Game2 - 10:30AM Court 5 vs. Gophers It",
        "Game3 - 11:00AM Court 4 vs. Rapping Rats",
        "Game4 - 11:30AM Court 3 vs. Minnies Mice"),
    'Oxnard' => array(
        "Game1 - 10:00AM Court 3 vs. Amazing Hamsters",
        "Game2 - 10:30AM Court 5 vs. Gophers It",
        "Game3 - 11:00AM Court 4 vs. Rapping Rats",
        "Game4 - 11:30AM Court 3 vs. Minnies Mice"),
    'Ventura' => array(
        "Game1 - 10:00AM Court 3 vs. Amazing Hamsters",
        "Game2 - 10:30AM Court 5 vs. Gophers It",
        "Game3 - 11:00AM Court 4 vs. Rapping Rats",
        "Game4 - 11:30AM Court 3 vs. Minnies Mice"),
    );


ob_start();
include("tpl/usyvl.tpl");
print ob_get_clean();


// Need to figure out a clever way to determine how much information we have
/*
if (isset($_GET['program'])){
    $program = $programarray[$_GET['program']];
}


if (isset($_GET['state'])){
    $sh = $_GET['state'];
    
    if( ! isset($program)){
        
        topmenu("Locate your USYVL Program/Site");
        
        foreach( $sitesarray[$sh] as $program){
            print "      <li>\n";
            print "        <a href=\"./schedules.php?state=$sh&program=$program\">\n";
            print "        $program\n";
            print "      </a></li>\n";
        }
    }
    else {
        topmenu("Your Schedule");
        foreach( $program as $item){
            print "      <li>\n";
            print "        <a href=\"./schedules.php?state=$sh&program=$program\">\n";
            print "        $item\n";
            print "      </a></li>\n";
        }
    }
}

else {
    topmenu("Locate your USYVL Program/Site");
    foreach( $statearray as $sh => $fullstate){
        print "      <li>\n";
        print "        <a href=\"./schedules.php?state=$sh\">\n";
        print "        $fullstate\n";
        print "      </a></li>\n";
    }
}
*/
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

