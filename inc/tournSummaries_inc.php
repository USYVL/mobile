<?php
require_once("mwfMobileSiteClass.php");

class mwfMobileSite_tourn extends mwfMobileSite {
    function __construct(){
        parent::__construct();
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    function registerExtendedFunctions(){
        $this->registerFunc('launch'      , 'dispDates'     );  // use the divisions key, since thats what the core "programs" uses
        $this->registerFunc('tsumm'       , 'dispTSumm'     );
        $this->registerFunc('tpool'       , 'dispTPool'     );
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    function dispDates(){
        $this->initArgs('tsumm',array('mode','season','state','program','date'));
        $sdb = $GLOBALS['dbh']['sdb'];
        $this->title = "USYVL Mobile - Select Tournament Date from {$this->args['state']} Program {$this->args['program']} for {$this->args['season']}";

        $m = "";
        $dates = $this->sdb->fetchListNew("select distinct evds from ev where evprogram = ? and evistype=?",array($this->args['program'],'INTE'));
        foreach( $dates as $date){
                $this->setArg('date',$date);
           //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=tsumm&date=$date&season=$season&state={$this->args['state']}&program=$program\">$date</a></li>\n";
                $m .= $this->buildURL_li($_SERVER['PHP_SELF'],$this->args,$date,"class=\"nonereally\"");
        }
        $b = $this->contentList($this->args['program'] . "<br />Tournament Dates",$m);

        return "$b";
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    // Main tournament summary page
    //////////////////////////////////////////////////////////////////////////////////////////
    function dispTSumm(){
        //print "WhatEvs<br/>\n";
        $this->initArgs('tsumm',array('mode','season','state','program','evds','evid','ev_refid'));
        $this->title = "USYVL Mobile - Tournament Summary - {$this->args['season']} {$this->args['program']} - {$this->args['evds']}";

        // We need the particular evid for this event...
        //$this->args['evid'] = $this->sdb->fetchVal("evid from ev","evprogram = ? and evistype = ? and evds = ?",array($this->args['program'],'INTE',$this->args['evds']));
        $b = "";

        // Get location info, possibly a better way to get it than this larger query
        $evd = $this->sdb->getKeyedHash('evid',"select * from ev left join lc on ev_lcid = lcid where evds=? and evprogram=?",array($this->args['evds'],$this->args['program']));
        if( count($evd) > 1 )  $b .= "ERROR on getKeyedHash";
        else                   $d = array_shift($evd);

        // get event description, this is not based on the evid
        $descs = $this->sdb->fetchListNew("select distinct evname from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ?",array($this->args['evds'],$this->args['program'],'INTE'));
        $desc = $descs[0];

        // start building the page
        $cb = "";
        $cb .= "<h3>";
        $cb .= "Date: {$this->args['date']}<br />";
        $cb .= "{$this->args['program']}<br />";
        $cb .= "$desc ({$d['evid']}-{$d['ev_refid']})<br />";
        //$cb .= "Host: Host Site<br />";
        $cb .= "</h3>";
        $cb .= "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";
        $b .= $this->contentDiv("Intersite Game Day",$cb);

        // Pretty sure this can be restructured...  If away game set program to tournament host
        // then we should be able to carry on normally.  It looks like the evid is not even used
        // till we get further into it below...
        //print "At Tournament Check<br/>\n";
        if( preg_match("/Intersite Game Day *Away Game *vs[.]* (.*)$/",$desc,$m) ){  // Away Game, we have to make some adjustments
            $sites = explode(" & ",$m[1]);
            $tournhost = trim($sites[0]);
            //$this->setArg('mode','tsumm');
            $this->setArg('program',$tournhost);

            //Hmmmm, at this point we might be able to just get the updated evid and proceed as normal
            /**
             Double Hmmmmm, we are now passing in the ev_refid, may not even need this check
             **/
            $this->args['ev_refid'] = $this->sdb->fetchVal("ev_refid from ev","evprogram = ? and evistype = ? and evds = ?",array($this->args['program'],'INTE',$this->args['evds']));
        }
        else {  // Home Game
            //$this->setArg('mode','tpool');
            $this->setArg('ev_refid',$this->args['evid']);
        }
        //print_pre($this->args,"arguments");
        $b .= $this->contentList("Tourn. Pools",$this->dispPoolSummaryFromRefID());

        // div wrapper for ajax provided html prepped by $this->dispPoolSummaryFromRefID()
        $b .= "<div id=\"poolInfo_ajax_result\">\n</div><!-- End poolInfo_ajax_result -->\n";

        // Add in any pdf links
        //$b .= $this->addPDFMaterialsLinks();
        $b .= $this->addPDFMaterialsLinks(array('INTERSITE','RULES'));

        // With a few changes up above, we can just add the pool info below here
        $dc = new digitalClock();
        $b .= $dc->dateTimeDiv("content");

        return "$b";
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    function dispPoolSummaryFromRefID(){
        $poolinfo_script = ( false ) ? $_SERVER['PHP_SELF'] : 'ajax/getPoolInfoAjax.php' ;
        $pdata = $this->sdb->getKeyedHash('poolid',"select * from pool where p_evid = ?",array($this->args['ev_refid']));
        //print "Home Game: {$this->args['evid']}<br>\n";
        //print_pre($pdata,"Away game data");

        $m = "";
        foreach($pdata as $pool){
            $this->setArg('poolid',$pool['poolid']);
            $this->setArg('poolnum',$pool['poolnum']);
            $this->setArg('ajax_result','poolInfo_ajax_result');
            $div = $pool['division'];
            $cts = $pool['courts'];
            $tmcount = count(explode(",",$pool['tmids']));
            $m .= $this->buildURL_li(array('ajax_result' => '#poolInfo_ajax_result', 'class' => 'ajax_tsumm', 'href' => $poolinfo_script),$this->args,"Pool " . $pool['poolnum'] . " ($div div) <br /> $tmcount Teams - Cts. $cts","class=\"nonereally\"");
            //print_pre($poolinfo_script,"ajax");
        }
        return $m;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////
    //function addPDFMaterialsLinks(){
    //    $b = '';
//
    //    $pdid = $this->sdb->fetchVal("pdid from pdfs","pdf_refid = ? and pdfcat = ?",array($this->args['ev_refid'],'INTERSITE'));
    //    if ($pdid != ""){
    //        $b .= "<li class=\"nonereally\"><a href=\"displayPDF.php?pdid=$pdid\">Tournament PDF</a></li>\n";
    //    }
//
    //    // add in static rules PDF
    //    $pdid = $this->sdb->fetchVal("pdid from pdfs","pdfcat = 'RULES';");
    //    if ($pdid != ""){
    //        $b .= "<li class=\"nonereally\"><a href=\"displayPDF.php?pdid=$pdid\">Rules PDF</a></li>\n";
    //    }
//
    //    return $this->contentList('PDF Materials Links',$b);
    //}
    //////////////////////////////////////////////////////////////////////////////////////////
    // this is used by ajax call (pretty sure) to get the single pool summary
    //////////////////////////////////////////////////////////////////////////////////////////
    function poolInfo(){
        //return "HeyThere Pool Info<br>\n";
        //return "Right after initArgs";
        $this->initArgs('tsumm',array('mode','season','state','program','date','poolid','evid'));
        $b = "";

        if( ! isset($this->args['poolid']) ||  $this->args['poolid'] == "" ) return "poolid not set";

        $pdata = $this->sdb->getKeyedHash('poolid',"select * from pool where poolid = ?",array($this->args['poolid']));
        $p = $pdata[$this->args['poolid']];

        // To this point, tpool is pretty much the same at poolsumm
        // Could possibly pull this out into another method

        $bb = "";
        $bb .= "<p><strong>Courts: </strong>" . $p['courts'] . "&nbsp;&nbsp;<strong>NetHeight: </strong>" . $p['neth'] . "<br />\n";
        $bb .= "<strong>Division: </strong>" . $p['division'] . "<br />\n";
        $bb .= "<strong>Game Times: </strong>" . $p['times'] . "\n";
        $bb .= "</p>\n";
        $b .= $this->contentDiv("Pool " . $p['poolnum'] . " - General Info",$bb);

        //print_pre($p,"Pool Hash");

        // So we do not have a team order in the pool
        // Also missing a mapping of intersite game days for visiting sites, only host site is keyed in....
        // Also division could be more easily accessed
        // probably want poollayout as well

        //if( preg_match("/Intersite Game Day *Away Game *vs\.* *(\w+) *$/",$desc[0],$m)){
        // so we need to get evisday - this is the number of the day in the manual
        //$t1 = $this->sdb->fetchListNew("select distinct tmid1 from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ? and pool = ?",array({$this->args['date']},{$this->args['program']},'INTE',$pool));
        //$t2 = $this->sdb->fetchListNew("select distinct tmid2 from ev left join gm on ev.evid = gm.evid where ev.evds = ? and evprogram = ? and evistype = ? and pool = ?",array({$this->args['date']},{$this->args['program']},'INTE',$pool));
        //$teams = array_unique(array_merge($t1,$t2));
        $bb = "";
        $thash = array();   // team name hash for use later in unwinding the game schedule...
        $tshh = array();   // team name hash for use later in unwinding the game schedule...
        foreach( explode(",",$p['tmids']) as $ind => $tmid){
            $r = $this->sdb->getKeyedHash('tmid',"select * from tm where tmid=?",array($tmid));
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
            $tshh[$num] = $tm['tmtshirt'];
            $bb .= "<p>\n";
            $bb .= "<strong>$num - $tmnam</strong><br />\n";
            $bb .= "{$tm['tmprogram']} <em>team number $tmnum ({$tm['tmdiv']})</em><br />\n";
            $bb .= ( isset($tm['tmcoach']) && $tm['tmcoach'] != "" ) ? "Coach: " . $tm['tmcoach'] . "<br />\n" : "" ;
            $bb .= "</p>\n";
        }
        ///foreach($teams as $team){
        ///    $bb .= "<li>Team $team</li>";
        ///}
        $b .= $this->contentDiv("Pool " . $p['poolnum'] . " - Teams",$bb);

        $bb = "";
        $gamepl = explode("|",$p['poollayout']);
        foreach(explode(",",$p['times']) as $gkey => $time){
            $sk = array();
            $courta = explode(",",$p['courts']);
            $matcha = explode("+",$gamepl[$gkey]);  // we now have match defined as: X-Y where X and Y are ints
            $num = $gkey + 1;
            $bb .= "<h2>Game #$num - $time</h2>\n";
            //$bb .= "<h3>Game #$num - $time</h3>\n";
            //$bb .= "<h4>Game #$num - $time</h4>\n";
            //$bb .= "<p>\n";
            foreach( $courta as $ckey => $court){
                $tmnums = explode("-",$matcha[$ckey]);
                $bb .= "<p>\n";
                $bb .= "<strong>Court: </strong> $court &nbsp;&nbsp;(match: " . $matcha[$ckey] . ")<br />";
                $bb .= $thash[$tmnums[0]] . " <strong>VS.</strong> " . $thash[$tmnums[1]] . "<br />";
                $sk['team_a'] = htmlentities($thash[$tmnums[0]],ENT_QUOTES);
                $sk['team_b'] = htmlentities($thash[$tmnums[1]],ENT_QUOTES);
                $sk['tshirt_a'] = $tshh[$tmnums[0]];
                $sk['tshirt_b'] = $tshh[$tmnums[1]];
                $bb .= $this->buildURL_li("./scorekeeper.php",$sk,"Scorekeep This Game") . "<br />\n";
                //$bb .= "<a href='./scorekeeper.php?team_a=" . . "&team_b=" . . "'>Score Keep This Match</a><br />";
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
        $b .= $this->contentDiv("Pool " . $p['poolnum'] . " - Game Schedules",$bb);

        //return "Fake return";
        return "$b";
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    function awayGameMessage($tournhost = ""){
        $b = "";
        $b .= "<p>Because of limitations of the data we have access to, you will need to navigate to the home sites link ";
        $b .= "to see tournament details.  The link should be provided below.</p>";
        $b .= "<p>Sorry for the inconvenience.  This problem will be addressed at some point in the future.</p>";
        return $b;
    }
}

?>
