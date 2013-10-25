<?php
require_once("config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");
require_once("mwfMobileSite.php");
require_once("digitalClock.php");

define('DEBUGLEVEL',0);

$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";
$content['css'] = "";
$content['scripts'] = "";

$content['scripts'] .= '<script type="text/javascript" src="js/locator.js"></script>' . "\n";
$content['css']  .= '<link rel="stylesheet" href="css/usyvl.css" type="text/css">' . "\n";

// Change Tournament Summaries to Tournament Pools

class usyvlMobileSite extends mwfMobileSite {
    function __construct(){
        parent::__construct();
    }
    function registerExtendedFunctions(){
        $this->registerFunc('launch'      , 'dispDates'     );  // use the divisions key, since thats what the core "programs" uses
        $this->registerFunc('gsumm'       , 'dispGSumm'     );
        $this->registerFunc('gpool'       , 'dispGPool'     );
    }
    function dispDates(){
        $this->initArgs('gsumm',array('mode','season','state','program','date'));
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $this->title = "USYVL Mobile - Select Tournament Date from $state Program {$this->args['program']} for {$this->args['season']}";
        
        $m = "";
        $dates = $this->sdb->fetchListNew("select distinct evds from ev where evprogram = ? and evistype=?",array($this->args['program'],'GAME'));
        //$divisions = $this->sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram=? and tmseason=? )","so_order",array({$this->args['program']},{$this->args['season']}));
        //$divisions = $this->sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div where ( tmprogram=? and tmseason=? ) order by so_order",array({$this->args['program']},{$this->args['season']}));
        foreach( $dates as $date){
                $this->setArg('date',$date);
           //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=tsumm&date=$date&season=$season&state=$state&program=$program\">$date</a></li>\n";
                $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,$date,"class=\"nonereally\"");
        }
        $b = $this->fMenu($this->args['program'] . "<br />Game Days",$m);
        
        return "$b";
    }
    function dispGSumm(){
        $this->initArgs('gsumm',array('mode','season','state','program','date'));
        $this->title = "USYVL Mobile - Game Summary - {$this->args['season']} {$this->args['program']} - {$this->args['date']}";
        
        // We need the particular evid for this event...
        $this->args['evid'] = $this->sdb->fetchVal("evid from ev","evprogram = ? and evistype = ? and evds = ?",array($this->args['program'],'GAME',$this->args['date']));
        $b = "";
        
        // Get location info, possibly a better way to get it than this larger query
        $evd = $this->sdb->getKeyedHash('evid',"select * from ev left join lc on ev_lcid = lcid where evds=? and evprogram=?",array($this->args['date'],$this->args['program']));
        if( count($evd) > 1 )  $b .= "ERROR on getKeyedHash";
        else                   $d = array_shift($evd);

        // get event description, this is not based on the evid
        $descs = $this->sdb->fetchListNew("select distinct evname from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ?",array($this->args['date'],$this->args['program'],'GAME'));       
        $desc = $descs[0];
        
        // start building the page
        $cb = "";
        $cb .= "<h3>";
        $cb .= "Date: {$this->args['date']}<br />";
        $cb .= "{$this->args['program']}<br />";
        $cb .= "$desc<br />";
        //$cb .= "Host: Host Site<br />";
        $cb .= "</h3>";
        $cb .= "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";
        $b .= $this->contentDiv("Game Day",$cb);
        
        
        // Each age division, is, in essence, a pool
        // but we want to get a list of all the matches on this day
        $games = $this->sdb->fetchListNew("select distinct game from gm left join ev on ev.evid = gm.evid where ev.evid = ?",array($this->args['evid']));       

            //$this->setArg('mode','gpool');
        foreach($games as $game){
            $tmdata = $this->sdb->getKeyedHash('tmid',"select * from tm where tmprogram = ?",array($this->args['program']));
            // could possibly get distinct times and then loop over that as well...
            $mdata = $this->sdb->getKeyedHash('gmid',"select * from gm left join ev on gm.evid = ev.evid where ev.evid = ? and game = ? order by court",array($this->args['evid'],$game));
            //print_pre($mdata,"matchdata");
            
            $bb = "";
            foreach($mdata as $pool){
                $this->setArg('poolid',$pool['poolid']);
                $this->setArg('poolnum',$pool['poolnum']);
                $time = $pool['time'];
                
                $div = $pool['pool'];
                $ct = $pool['court'];
                $sk['team_a'] = htmlentities($tmdata[$pool['tmid1']]['tmname'],ENT_QUOTES);
                $sk['team_b'] = htmlentities($tmdata[$pool['tmid2']]['tmname'],ENT_QUOTES);
                $sk['tshirt_a'] = $tmdata[$pool['tmid1']]['tmtshirt'];
                $sk['tshirt_b'] = $tmdata[$pool['tmid2']]['tmtshirt'];
                $bb .= "<p>Ct. $ct - ($div div)<br />";
                $bb .= $sk['team_a']; 
                $bb .= "  <strong>VS.</strong>  "; 
                $bb .= $sk['team_b']; 
                $bb .= "<br />\n";
                $bb .= $this->buildURL("./scorekeeper.php",$sk,"Scorekeep This Game");
                $bb .= "\n</p>\n";
            }
            $b .= $this->contentDiv("Game $game Matches<br />$time",$bb);
        }
        $dc = new digitalClock();
        $b .= $dc->dateTimeDiv("content");
        
        return "$b";
    }
    // because of the way I want to navigate between pools, we may be able to just
    // merge dispTSumm and dispTPool, if we have poolid and poolnum then we have the extra display
    ////function dispGPool(){
    ////    $this->initArgs('gsumm',array('mode','season','state','program','date','poolid','evid'));
    ////   
    ////    $pdata = $this->sdb->getKeyedHash('poolid',"select * from pool where poolid = ?",array($this->args['poolid']));
    ////    $p = $pdata[$this->args['poolid']];
    ////    $poolnum = $p['poolnum'];
    ////
    ////    $this->title = "USYVL Mobile - Tournament Summary - {$this->args['season']} {$this->args['program']} - {$this->args['date']} - Pool " . $p['poolnum'];
    ////    
    ////    $b = "";
    ////    
    ////    // This is really only used for the location stuff, possibly a better way to get it...
    ////    $evd = $this->sdb->getKeyedHash('gmid',"select * from ev left join lc on ev_lcid = lcid where ev.evds = ? and evprogram = ? and evistype = ?",array($this->args['date'],$this->args['program'],'INTE'));
    ////    if( count($evd) > 1 ){
    ////        $b .= "ERROR on getKeyedHash";
    ////        //print_pre($evd,"event data: should have been a single event");
    ////    }
    ////    else                   $d = array_shift($evd);
    ////    
    ////    $descs = $this->sdb->fetchListNew("select distinct evname from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ?",array($this->args['date'],$this->args['program'],'INTE'));       
    ////    $desc = $descs[0];
    ////    $cb = "";
    ////    $cb .= "<h3>";
    ////    $cb .= "Date: {$this->args['date']}<br />";
    ////    $cb .= "{$this->args['program']}<br />";
    ////    $cb .= "$desc<br />";
    ////    //$cb .= "Host: Host Site<br />";
    ////    $cb .= "</h3>";
    ////    $cb .= "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";
    ////    $b .= $this->contentDiv("Intersite Game Day",$cb);
    ////
    ////    //$bb = "";
    ////    //$b .= $this->contentDiv("Navigation",$bb);
    ////    if( true ){
    ////        $this->setArg('mode','tpool');
    ////        $pdata = $this->sdb->getKeyedHash('poolid',"select * from pool where p_evid = ?",array($this->args['evid']));
    ////        
    ////        $m = "";
    ////        foreach($pdata as $pool){
    ////            $this->setArg('poolid',$pool['poolid']);
    ////            $this->setArg('poolnum',$pool['poolnum']);
    ////            $div = $pool['division'];
    ////            $cts = $pool['courts'];
    ////            $tmcount = count(explode(",",$pool['tmids']));
    ////            $m .= $this->buildURL($_SERVER['PHP_SELF'],$this->args,"Pool " . $pool['poolnum'] . " ($div div) <br /> $tmcount Teams - Cts. $cts","class=\"nonereally\"");
    ////        }
    ////        $b .= $this->fMenu("Tourn. Pools",$m);
    ////    }
    ////    
    ////    
    ////    // To this point, tpool is pretty much the same at poolsumm
    ////    
    ////    $bb = "";
    ////    $bb .= "<p><strong>Courts: </strong>" . $p['courts'] . "&nbsp;&nbsp;<strong>NetHeight: </strong>" . $p['neth'] . "<br />\n";
    ////    $bb .= "<strong>Division: </strong>" . $p['division'] . "<br />\n";
    ////    $bb .= "<strong>Game Times: </strong>" . $p['times'] . "\n";
    ////    $bb .= "</p>\n";
    ////    $b .= $this->contentDiv("Pool $poolnum - General Info",$bb);
    ////    
    ////    //print_pre($p,"Pool Hash");
    ////    
    ////    // So we do not have a team order in the pool
    ////    // Also missing a mapping of intersite game days for visiting sites, only host site is keyed in....
    ////    // Also division could be more easily accessed
    ////    // probably want poollayout as well
    ////    
    ////    //if( preg_match("/Intersite Game Day *Away Game *vs\.* *(\w+) *$/",$desc[0],$m)){
    ////    // so we need to get evisday - this is the number of the day in the manual
    ////    //$t1 = $this->sdb->fetchListNew("select distinct tmid1 from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ? and pool = ?",array({$this->args['date']},{$this->args['program']},'INTE',$pool));       
    ////    //$t2 = $this->sdb->fetchListNew("select distinct tmid2 from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ? and pool = ?",array({$this->args['date']},{$this->args['program']},'INTE',$pool));       
    ////    //$teams = array_unique(array_merge($t1,$t2));
    ////    $bb = "";
    ////    $thash = array();   // team name hash for use later in unwinding the game schedule...
    ////    $tshh = array();   // team name hash for use later in unwinding the game schedule...
    ////    foreach( explode(",",$p['tmids']) as $ind => $tmid){
    ////        $r = $this->sdb->getKeyedHash('tmid',"select * from tm where tmid=?",array($tmid));
    ////        $tm = array_shift($r);
    ////        
    ////        // sort out team names and program numbers...
    ////        // if Team XX then its a default name, take number...
    ////        $num = $ind + 1;
    ////        $tmnum = "";
    ////        $tmnam = $tm['tmname'];
    ////        if( preg_match("/^Team (\d+)/",$tm['tmname'],$m)){
    ////            $tmnum = $m[1];
    ////            $tmnam = $tm['tmname'];
    ////        }
    ////        if( preg_match("/^(\d+) *- *(.*)$/",$tm['tmname'],$m)){
    ////            $tmnum = $m[1];
    ////            $tmnam = $m[2];
    ////        }
    ////        $thash[$num] = $tmnam;
    ////        $tshh[$num] = $tm['tmtshirt'];
    ////        $bb .= "<p>\n";
    ////        $bb .= "<strong>$num - $tmnam</strong><br />\n";
    ////        $bb .= "{$tm['tmprogram']} <em>team number $tmnum ({$tm['tmdiv']})</em><br />\n";
    ////        $bb .= ( isset($tm['tmcoach']) && $tm['tmcoach'] != "" ) ? "Coach: " . $tm['tmcoach'] . "<br />\n" : "" ;
    ////        $bb .= "</p>\n";
    ////    }
    ////    ///foreach($teams as $team){
    ////    ///    $bb .= "<li>Team $team</li>";
    ////    ///}
    ////    $b .= $this->contentDiv("Pool " . $p['poolnum'] . " - Teams",$bb);
    ////    
    ////    $bb = "";
    ////    $gamepl = explode("|",$p['poollayout']);
    ////    foreach(explode(",",$p['times']) as $gkey => $time){
    ////        $sk = array();
    ////        $courta = explode(",",$p['courts']);
    ////        $matcha = explode("+",$gamepl[$gkey]);  // we now have match defined as: X-Y where X and Y are ints
    ////        $num = $gkey + 1;
    ////        $bb .= "<h2>Game #$num - $time</h2>\n";
    ////        //$bb .= "<h3>Game #$num - $time</h3>\n";
    ////        //$bb .= "<h4>Game #$num - $time</h4>\n";
    ////        //$bb .= "<p>\n";
    ////        foreach( $courta as $ckey => $court){
    ////            $tmnums = explode("-",$matcha[$ckey]);
    ////            $bb .= "<p>\n";
    ////            $bb .= "<strong>Court: </strong> $court &nbsp;&nbsp;(match: " . $matcha[$ckey] . ")<br />";
    ////            $bb .= $thash[$tmnums[0]] . " <strong>VS.</strong> " . $thash[$tmnums[1]] . "<br />";
    ////            $sk['team_a'] = htmlentities($thash[$tmnums[0]],ENT_QUOTES);
    ////            $sk['team_b'] = htmlentities($thash[$tmnums[1]],ENT_QUOTES);
    ////            $sk['tshirt_a'] = $tshh[$tmnums[0]];
    ////            $sk['tshirt_b'] = $tshh[$tmnums[1]];
    ////            $bb .= $this->buildURL("./scorekeeper.php",$sk,"Scorekeep This Game") . "<br />\n";
    ////            //$bb .= "<a href='./scorekeeper.php?team_a=" . . "&team_b=" . . "'>Score Keep This Match</a><br />";
    ////            $bb .= "</p>\n";
    ////        }
    ////        if( count($matcha) > count($courta)){
    ////            $bb .= "<p>\n";
    ////            $bb .= "<strong>BYE: </strong> (match: " . $matcha[count($matcha)-1] . ")&nbsp;&nbsp;";
    ////            $bb .= $thash[$matcha[count($matcha)-1]]  . "<br />";
    ////            $bb .= "</p>\n";
    ////        }
    ////        //$bb .= "<strong>Game Time: </strong>$time<br />\n";
    ////        //$bb .= "</p>\n";
    ////    }
    ////    $b .= $this->contentDiv("Pool " . $p['poolnum'] . " - Game Schedules",$bb);
    ////   
    ////    
    ////    
    ////    return "$b";
    ////}
    function awayGameMessage($tournhost = ""){
        $b = "";
        $b .= "<p>Because of limitations of the data we have access to, you will need to navigate to the home sites link ";
        $b .= "to see tournament details.  The link should be provided below.</p>";
        $b .= "<p>Sorry for the inconvenience.  This problem will be addressed at some point in the future.</p>";
        return $b;
    }
}

$ms = new usyvlMobileSite();

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

