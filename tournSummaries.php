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


class usyvlMobileSite extends mwfMobileSite {
    function __construct(){
        parent::__construct();
    }
    function registerExtendedFunctions(){
        $this->registerFunc('divisions'   , 'dispDates'     );  // use the divisions key, since thats what the core "programs" uses
        $this->registerFunc('tsumm'       , 'dispTSumm'     );
        $this->registerFunc('tpool'       , 'dispTPool'     );
    }
    function dispDates(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $this->title = "USYVL Mobile - Select Tournament Date from $state Program $program for $season";
        
        $m = "";
        $dates = $sdb->fetchListNew("select distinct evds from ev where evprogram = ? and evistype=?",array($program,'INTE'));
        //$divisions = $sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram=? and tmseason=? )","so_order",array($program,$season));
        //$divisions = $sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div where ( tmprogram=? and tmseason=? ) order by so_order",array($program,$season));
        foreach( $dates as $date){
           $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=tsumm&date=$date&season=$season&state=$state&program=$program\">$date</a></li>\n";
        }
        $b = $this->fMenu("Select Tourn Date",$m);
        
        return "$b";
    }
    function dispTSumm(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $mdb = $GLOBALS['dbh']['mdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $date = $_GET['date'];
        //$division = $_GET['division'];
        $this->title = "USYVL Mobile - Tournament Summary - $season $program - $date";
        
        // We need the particular evid for this event...
        $evid = $sdb->fetchVal("evid from ev","evprogram = ? and evistype = ? and evds = ?",array($program,'INTE',$date));
        $b = "";
        
        // This is really only used for the location stuff, possibly a better way to get it...
        $evd = $sdb->getKeyedHash('evid',"select * from ev left join lc on ev_lcid = lcid where evds=? and evprogram=?",array($date,$program));
        if( count($evd) > 1 )  $b .= "ERROR on getKeyedHash";
        else                   $d = array_shift($evd);

        //$evd = $sdb->getKeyedHash('gmid',"select * from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ?",array($date,$program,'INTE'));
        $descs = $sdb->fetchListNew("select distinct evname from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ?",array($date,$program,'INTE'));       
        $desc = $descs[0];
        $cb = "";
        $cb .= "<h3>";
        $cb .= "Date: $date<br />";
        $cb .= "$program<br />";
        $cb .= "$desc<br />";
        //$cb .= "Host: Host Site<br />";
        $cb .= "</h3>";
        $cb .= "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";
        $b .= $this->contentDiv("Intersite Game Day",$cb);
        
        if( preg_match("/Intersite Game Day *Away Game *vs.* (.*)$/",$desc,$m) ){
            $sites = explode(",",$m[1]);
            $tournhost = $sites[0];
            $b .= $this->contentDiv("Workaround Required",$this->awayGameMessage($tournhost));
            $m = "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=tsumm&date=$date&season=$season&state=$state&program=$tournhost\">$tournhost<br />$date</a></li>\n";
            $b .= $this->fMenu("Tournament Hosts Link",$m); 

        }
        else {
            $pdata = $sdb->getKeyedHash('poolid',"select * from pool where p_evid = ?",array($evid));
            //print_pre($pdata,"pool data");
            
            // so we need to get evisday - this is the number of the day in the manual
            $pools = $sdb->fetchListNew("select distinct pool from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ?",array($date,$program,'INTE'));       
            $m = "";
            foreach($pdata as $pool){
                $poolid = $pool['poolid'];
                $poolnum = $pool['poolnum'];
                $div = $pool['division'];
                $cts = $pool['courts'];
                $tmcount = count(explode(",",$pool['tmids']));
                $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=tpool&poolid=$poolid&poolnum=$poolnum&date=$date&season=$season&state=$state&program=$program\">Pool $poolnum ($div div) - $tmcount Teams - Cts. $cts</a></li>\n";
            }
            //foreach($pools as $pool){
            //    //$bb .= "<li><a href=Pool $pool</li>";
            //    $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=tpool&pool=$pool&date=$date&season=$season&state=$state&program=$program\">Pool $pool</a></li>\n";
            //}
            $b .= $this->fMenu("Tourn. Pools",$m);
        }
        
        return "$b";
    }
    function dispTPool(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $mdb = $GLOBALS['dbh']['mdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $date = $_GET['date'];
        $poolid = $_GET['poolid'];
       
        $pdata = $sdb->getKeyedHash('poolid',"select * from pool where poolid = ?",array($poolid));
        $p = $pdata[$poolid];
        $poolnum = $p['poolnum'];

        $this->title = "USYVL Mobile - Tournament Summary - $season $program - $date - Pool " . $p['poolnum'];
        
        $b = "";
        
        // This is really only used for the location stuff, possibly a better way to get it...
        $evd = $sdb->getKeyedHash('gmid',"select * from ev left join lc on ev_lcid = lcid where ev.evds = ? and evprogram = ? and evistype = ?",array($date,$program,'INTE'));
        if( count($evd) > 1 ){
            $b .= "ERROR on getKeyedHash";
            //print_pre($evd,"event data: should have been a single event");
        }
        else                   $d = array_shift($evd);
        
        $descs = $sdb->fetchListNew("select distinct evname from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ?",array($date,$program,'INTE'));       
        $desc = $descs[0];
        $cb = "";
        $cb .= "<h3>";
        $cb .= "Date: $date<br />";
        $cb .= "$program<br />";
        $cb .= "$desc<br />";
        //$cb .= "Host: Host Site<br />";
        $cb .= "</h3>";
        $cb .= "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";
        $b .= $this->contentDiv("Intersite Game Day",$cb);
        
        $bb = "";
        $bb .= "<p><strong>Courts: </strong>" . $p['courts'] . "&nbsp;&nbsp;<strong>NetHeight: </strong>" . $p['neth'] . "<br />\n";
        $bb .= "<strong>Division: </strong>" . $p['division'] . "<br />\n";
        $bb .= "<strong>Game Times: </strong>" . $p['times'] . "\n";
        $bb .= "</p>\n";
        $b .= $this->contentDiv("Pool $poolnum General Info",$bb);
        
        //print_pre($p,"Pool Hash");
        
        // So we do not have a team order in the pool
        // Also missing a mapping of intersite game days for visiting sites, only host site is keyed in....
        // Also division could be more easily accessed
        // probably want poollayout as well
        
        //if( preg_match("/Intersite Game Day *Away Game *vs\.* *(\w+) *$/",$desc[0],$m)){
        // so we need to get evisday - this is the number of the day in the manual
        //$t1 = $sdb->fetchListNew("select distinct tmid1 from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ? and pool = ?",array($date,$program,'INTE',$pool));       
        //$t2 = $sdb->fetchListNew("select distinct tmid2 from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ? and pool = ?",array($date,$program,'INTE',$pool));       
        //$teams = array_unique(array_merge($t1,$t2));
        $bb = "";
        $thash = array();   // team name hash for use later in unwinding the game schedule...
        foreach( explode(",",$p['tmids']) as $ind => $tmid){
            $r = $sdb->getKeyedHash('tmid',"select * from tm where tmid=?",array($tmid));
            $tm = array_shift($r);
            
            // sort out team names and program numbers...
            // if Team XX then its a default name, take number...
            $num = $ind + 1;
            $tmnum = "";
            $tmnam = $tm['tmname'];
            if( preg_match("/^Team (\d+)/",$tm['tmname'],$m)){
                $tmnum = $m[1];
                $tmnam = $tm['tmname'];
            }
            if( preg_match("/^(\d+) *- *(.*)$/",$tm['tmname'],$m)){
                $tmnum = $m[1];
                $tmnam = $m[2];
            }
            $thash[$num] = $tmnam;
            $bb .= "<p>\n";
            $bb .= "<strong>$num - $tmnam</strong><br />\n";
            $bb .= "{$tm['tmprogram']} <em>team number $tmnum ({$tm['tmdiv']})</em><br />\n";
            $bb .= ( isset($tm['tmcoach']) && $tm['tmcoach'] != "" ) ? "Coach: " . $tm['tmcoach'] . "<br />\n" : "" ;
            $bb .= "</p>\n";
        }
        ///foreach($teams as $team){
        ///    $bb .= "<li>Team $team</li>";
        ///}
        $b .= $this->contentDiv("Teams in Pool " . $p['poolnum'],$bb);
        
        $bb = "";
        $gamepl = explode("|",$p['poollayout']);
        foreach(explode(",",$p['times']) as $gkey => $time){
            $courta = explode(",",$p['courts']);
            $matcha = explode("+",$gamepl[$gkey]);  // we now have match defined as: X-Y where X and Y are ints
            $num = $gkey + 1;
            $bb .= "<h2>Game #$num - $time</h2>\n";
            //$bb .= "<p>\n";
            foreach( $courta as $ckey => $court){
                $tmnums = explode("-",$matcha[$ckey]);
                $bb .= "<p>\n";
                $bb .= "<strong>Court: </strong> $court &nbsp;&nbsp;(match: " . $matcha[$ckey] . ")<br />";
                $bb .= $thash[$tmnums[0]] . " <strong>VS.</strong> " . $thash[$tmnums[1]] . "<br />";
                $bb .= "</p>\n";
            }
            if( count($matcha) > count($courta)){
                $bb .= "<p>\n";
                $bb .= "<strong>BYE: </strong> (match: " . $matcha[count($matcha)-1] . ")&nbsp;&nbsp;";
                $bb .= $thash[$matcha[count($matcha)-1]]  . "<br />";
                $bb .= "</p>\n";
            }
            //$bb .= "<strong>Game Time: </strong>$time<br />\n";
            //$bb .= "</p>\n";
        }
        $b .= $this->contentDiv("Game Schedules Pool " . $p['poolnum'],$bb);
       
        
        
        return "$b";
    }
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

