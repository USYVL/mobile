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
    ////////////////////////////////////////////////////////////////////////////
    // Want to centralize the data collection/validation/storage since the chain
    // is pretty well defined:
    //   season
    //   state
    //   program
    //   division
    //   team
    // 
    // The overall chain leads to the display of a teams schedule for the entire 
    // season. Each step requires all the other pieces.  Of course, the automode 
    // will add the additional dimension of time.
    //
    // Debating on using the singular and plural forms of each chain element.
    // ie: state and states, season and seasons
    ////////////////////////////////////////////////////////////////////////////
    function collectValidateDataChain($next){
        // so, one thing to consider is whether I should try to figure out the 
        // mode from what elements are available...  
        // Interesting idea, but gets trickier if we end up having sequences that 
        // dont fit this chain idea.
        
        // Was toying with a mask type idea though  0x07
        // season 0x01
        // state 0x02
        // program 0x04
        // division 0x08
        
        // thus 0x0f would indicate the presence of all of those, 0x07 only the first three
        // but many of these values rely on the previous value (thus this idea of a chain)
        // But at some point we need to think of time (date) and what where that fits in
        // in the current scheme, we can pull the schedule for one team for that date,
        // but will I want to pull data from multiple sites on a given date?????
        
        $this->chain = array('season','state','program','division','team');
        $sdb = $GLOBALS['dbh']['sdb'];
        
        $this->chaindata['seasons'] = $sdb->fetchList("distinct evseason from ev");
        $k = 'season';
        if( isset($_GET[$k])){
            $v = $_GET[$k];
        }
        // loop over chain elements, checking $_GET for values and verifying that
        // they exist and are valid
        // At some point in all this though, I need some handlers specific to the
        // data I need to examine validate...
        // Also, would be nice to use prepare for making the queries, would help
        // secure things a bit..
        foreach($this->chain as $chainelement){
            $datakey = $chainelement . "s";
            if( $next == $chainelement ) return $this->chaindata[$datakey];
            if( isset($_GET[$chainelement])) {
                $this->chainval[$chainelement] = $_GET[$chainelement];
                
                // need to validate the data now, hmmmm, we need this to lag by one
                // ie: to validate state data, we need a season value set
                // so we need to save previous loop entry
            }
            
        }
        
    }
    function dispMain(){
        $sdb = $GLOBALS['dbh']['sdb'];
        
        $this->collectValidateDataChain('season');
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
        //$divisions = $sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram='$program' and tmseason='$season' )","so_order");
        //$divisions = $sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram=? and tmseason=? )","so_order",array($program,$season));
        $divisions = $sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div where ( tmprogram=? and tmseason=? ) order by so_order",array($program,$season));
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
        //$stm = $sdb->prepare("select * from tm where ( tmprogram=? and tmdiv=? and tmseason=? )");
        //$data = $sdb->getKeyedHash('tmid',$stm,array($program,$division,$season));
        
        //$stm = $sdb->prepare("select * from tm where ( tmprogram=? and tmdiv=? and tmseason=? )";
        
        $data = $sdb->getKeyedHash('tmid',"select * from tm where ( tmprogram=? and tmdiv=? and tmseason=? )",array($program,$division,$season));
        
        //$data = $sdb->getKeyedHash('tmid',"select * from tm where ( tmprogram='$program' and tmdiv='$division' and tmseason='$season' )");
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
                
        $coach = $sdb->fetchVal('distinct tmcoach from tm','tmid=?',array($tmid));

        $b = "";
        //$b .= $this->topmenu("$team Schedule");
        $b .= "<div class=\"content content-full\">\n";
        $b .= "<h1>$team Schedule</h2>\n";
        $b .= "<p>\n";
        $b .= "Program: $program<br />\n";
        $b .= "Coach: $coach<br />\n";
        $b .= "<select id=\"select-schedule-display\">\n";
        $b .= "<option>All</option>\n";
        $b .= "<option>Practices</option>\n";
        $b .= "<option>Games</option>\n";
        $b .= "<option>Tournaments</option>\n";
        $b .= "</select>\n";
        $b .= "</p>\n";
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
        //$data = $sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid left join lc on ev.ev_lcid = lcid where ( ( tmid1=$tmid or tmid2=$tmid ) and evseason='$season' ) order by evds");
        $data = $sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid left join lc on ev.ev_lcid = lcid where ( ( tmid1=? or tmid2=? ) and evseason=? ) order by evds",array($tmid,$tmid,$season));
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
                $othertmname = $sdb->fetchVal('tmname from tm',"tmid=?",array($othertmid));
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
            case "seasons" :  // The top level 
                $b .= $this->dispMain();
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
            // this entry is the culmination of the chain (so far)    
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

