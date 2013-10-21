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
       //$this->registerFunc('divisions', 'dispDivisions' );
       //$this->registerFunc('teams'    , 'dispTeams'     );
       //$this->registerFunc('sched'    , 'dispSched'     );
       //$this->registerFunc('credits'  , 'dispCredits'   );
       //$this->registerFunc('settings' , 'dispSettings'  );
       //$this->registerFunc('auto'     , 'dispAuto'      );
   }
   //function dispDivisions(){
   //    $this->initArgs('teams',array('mode','season','state','program'));
   //    $this->title = "USYVL Mobile - Select Division from {$this->args['state']} Program {$this->args['program']} for $season";
   //    
   //    //$divisions = $this->sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram='$program' and tmseason='$season' )","so_order");
   //    //$divisions = $this->sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram=? and tmseason=? )","so_order",array($program,$season));
   //    $m = "";
   //    $divisions = $this->sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div where ( tmprogram=? and tmseason=? ) order by so_order",array($this->args['program'],$this->args['season']));
   //    foreach( $divisions as $division){
   //        $this->args['division'] = $division;
   //        $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,"$division division","class=\"nonereally\"");
   //       //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=teams&division=$division&season=$season&state=$state&program={$this->args['program']}\">$division</a></li>\n";
   //    }
   //    
   //    $b = $this->fMenu("Select Age Division",$m);
   //    
   //    return "$b";
   //}
   //function dispTeams(){
   //    $this->initArgs('sched',array('mode','season','state','program','division'));
   //    $this->title = "USYVL Mobile - Select Team in $division Division from {$this->args['state']} Program {$this->args['program']} for $season";
   //    
   //    $data = $this->sdb->getKeyedHash('tmid',"select * from tm where ( tmprogram=? and tmdiv=? and tmseason=? )",array($this->args['program'],$this->args['division'],$this->args['season']));
   //    
   //    $m = "";
   //    foreach( $data as $k => $d){
   //        $this->args['team'] = $d['tmname'];
   //        $this->args['tmid'] = $k;
   //        $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,$this->args['team'],"class=\"nonereally\"");
   //        //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=sched&season=$season&tmid=$tmid&team=$team&division={$this->args['division']}&state=$state&program={$this->args['program']}\">$team</a></li>\n";
   //    }
   //    
   //    $b = $this->fMenu("Select Team",$m);
   //    
   //    return "$b";
   //}
   //function dispSched(){
   //    $sk = array();
   //    $this->initArgs('sched',array('mode','season','state','program','division','team','tmid'));
   //    $this->title = "USYVL Mobile - $team Schedule for $season";
   //            
   //    $coach = $this->sdb->fetchVal('distinct tmcoach from tm','tmid=?',array($this->args['tmid']));
   //
   //    $b = "";
   //    $b .= "<div class=\"content content-full\">\n";
   //    $b .= "<h1>$team Schedule</h2>\n";
   //    $b .= "<p>\n";
   //    $b .= "Program: {$this->args['program']}<br />\n";
   //    $b .= "Coach: $coach<br />\n";
   //    $b .= "</p>\n";
   //   
   //    $b .= "<p id=\"select-schedule-display-container\">\n";
   //    $b .= "Display: ";
   //    $b .= "<select id=\"select-schedule-display\">\n";
   //    $b .= "<option>All</option>\n";
   //    $b .= "<option>Practices</option>\n";
   //    $b .= "<option>Games</option>\n";
   //    $b .= "<option>Tournaments</option>\n";
   //    $b .= "</select>\n";
   //    $b .= "</p>\n";
   //    
   //    // need to get team id
   //    //$data = $this->sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid left join lc on ev.ev_lcid = lcid where ( ( tmid1=$tmid or tmid2=$tmid ) and evseason='$season' ) order by evds");
   //    //$data = $this->sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid left join lc on ev.ev_lcid = lcid where ( ( tmid1=? or tmid2=? ) and evseason=? and evprogram=?) order by evds",array($this->args['tmid'],$this->args['tmid'],$this->args['season'],$this->args['program']));
   //    $data = $this->sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid left join lc on ev.ev_lcid = lcid where ( ( tmid1=? or tmid2=? ) and evseason=?) order by evds",array($this->args['tmid'],$this->args['tmid'],$this->args['season']));
   //    $sk['tshirt_a'] = $this->sdb->fetchVal('tmtshirt from tm',"tmid=?",array($this->args['tmid']));
   //    $sk['team_a'] = $this->args['team'];
   //    $sk['tmid_a'] = $this->args['tmid'];
   //    foreach( $data as $d){
   //        $evloc = $d['lclocation'];
   //        $evnm = $d['evname'];
   //        $court = $d['court'];
   //        $time = $d['time'];
   //        $this->args['date'] = $d['evds'];
   //        // Want to determine type of entry for possible filtering later
   //        // possibilities are: Practice, Home Game, Tournament
   //        if( preg_match("/^Practice/",$evnm)){
   //            $evtype = "Practices";
   //        }
   //        elseif( preg_match("/^Intersite Game Day/",$evnm)){
   //            $evtype = "Tournaments";
   //        }
   //        elseif( preg_match("/^Games/",$evnm)){
   //            $evtype = "Games";
   //        }
   //        else {
   //            $evtype = "unknown";
   //        }
   //        
   //        // depending on type of event, court may be specified in one of two locations...
   //        $b .= "<p class=\"$evtype\">\n";
   //        $b .= "{$this->args['date']} - $time<br />";
   //        $b .= "$evnm<br />\n";
   //        $b .= "$evloc - Court $court\n";
   //        if( $d['tmid1'] != "" and $d['tmid2'] != "" ){
   //            // need to get the other teams name
   //            // this team could be either tmid1 or tmid2
   //            $othertmid = ( $d['tmid1'] == $tmid ) ? $d['tmid2'] : $d['tmid1'];
   //            $sk['team_b']   = $this->sdb->fetchVal('tmname from tm',"tmid=?",array($othertmid));
   //            $sk['tshirt_b'] = $this->sdb->fetchVal('tmtshirt from tm',"tmid=?",array($othertmid));
   //            //$b .= "<br /><a href=\"scorekeeper.php?team_a=$team&team_b=$othertmname\">Scorekeep This Game</a>\n";
   //            //$b .= "<br />" . $this->buildURL("./scorekeeper.php","team_a=$team&team_b=$othertmname","Scorekeep This Game") . "\n";
   //            $b .= "<br />" . $this->buildURL("./scorekeeper.php",$sk,"Scorekeep This Game") . "\n";
   //            //$b .= "<br /><a href=\"tournSummaries.php?mode=tsumm&season=$season&date=$date&state=$state&program={$this->args['program']}\">Tournament Info</a>\n";
   //            $b .= "<br />" . $this->buildURL("./tournSummaries.php",$this->args,"Tournament Info") . "\n";
   //        }
   //        $b .= "</p>\n";
   //    }
   //    $b .= "</div>\n";
   //    return "$b";
   //}
   //// Most of this is actually done with javascript/jquery
   //function dispAuto(){
   //    $b = "Location:";
   //    $b .= "<div id=\"device_location\">NA</div>";
   //    $b .= "<div id=\"proximal_events\">Proximal Events: NA</div>";
   //    return $b;
   //}
   //function dispSettings(){
   //    $this->title = "USYVL Mobile - Settings";
   //    
   //    $b = $this->contentDiv("Settings","<p>\nSettings Coming Soon!\n</p>\n");
   //    
   //    return "$b";
   //}
   //function dispCredits(){
   //    $this->title = "USYVL Mobile - Credits";
   //    
   //    $b = "";
   //    $b .= $this->contentDiv("Version","<p class=\"credits author\">\nVersion: " . $GLOBALS['version'] . "\n</p>\n");
   //    $bb .= "<p class=\"credits\">\n";
   //    $bb .= "HTML 5\n";
   //    $bb .= "</p>\n";
   //    $bb .= "<p class=\"credits\">\n";
   //    $bb .= "CSS 3\n";
   //    $bb .= "</p>\n";
   //    $bb .= "<p class=\"credits\">\n";
   //    $bb .= "Mobile Web Framework (MWF) 1.3\n";
   //    $bb .= "</p>\n";
   //    $bb .= "<p class=\"credits\">\n";
   //    $bb .= "jQuery 1.10.x\n";
   //    $bb .= "</p>\n";
   //    $b .= $this->contentDiv("Tech",$bb);
   //    $b .= $this->contentDiv("Author","<p class=\"credits author\">\nCreated for USYVL by Aaron Martin\n</p>\n");
   //    $b .= $this->contentDiv("Art/Graphics","<p class=\"credits\">\nProvided by USYVL</p>\n");
   //    return "$b";
   //}
}

$ms = new usyvlMobileSite();

$content['body']  .= $ms->display();
$content['title']  = $ms->getTitle();  // title is not set till after display is run...
$content['errs']  .= "";

//ob_start();
include("tpl/usyvl.tpl");
//print ob_get_clean();
?>

