<?php
require_once("mwfMobileSiteClass.php");
require_once("digitalClock.php");
class usyvlMobileSite extends mwfMobileSite {
    function __construct(){
        parent::__construct();
    }
    ////////////////////////////////////////////////////////////////////////////
    function registerExtendedFunctions(){
        $this->registerFunc('launch'      , 'dispDates'     );  // use the divisions key, since thats what the core "programs" uses
        $this->registerFunc('gsumm'       , 'dispGSumm'     );
        $this->registerFunc('gpool'       , 'dispGPool'     );
    }
    ////////////////////////////////////////////////////////////////////////////
    function dispDates(){
        $this->initArgs('gsumm',array('mode','season','state','program','date'));
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $this->title = "USYVL Mobile - Select Tournament Date from $state Program {$this->args['program']} for {$this->args['season']}";

        $m = "";
        $hash = $this->sdb->getKeyedHash('evds',"SELECT * FROM ev WHERE evprogram = ? AND evistype=? ORDER BY evds",array($this->args['program'],'GAME'));
        //$divisions = $this->sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram=? and tmseason=? )","so_order",array({$this->args['program']},{$this->args['season']}));
        //$divisions = $this->sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div where ( tmprogram=? and tmseason=? ) order by so_order",array({$this->args['program']},{$this->args['season']}));
        foreach( $hash as $date => $data){
            $this->setArg('ev_refid',$data['ev_refid']);
            $this->setArg('evid',$data['evid']);
            $this->setArg('evds',$data['evds']);
            //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=tsumm&date=$date&season=$season&state=$state&program=$program\">$date</a></li>\n";
            $m .= $this->buildURL_li($_SERVER['PHP_SELF'],$this->args,$date,"class=\"nonereally\"");
        }
        $b = $this->contentList($this->args['program'] . "<br />Game Days",$m);

        return "$b";
    }
    ////////////////////////////////////////////////////////////////////////////
    // This is the game summary display
    ////////////////////////////////////////////////////////////////////////////
    function dispGSumm(){
        $this->initArgs('gsumm',array('mode','season','state','program','evds','evid', 'ev_refid'));
        $this->title = "USYVL Mobile - Game Summary - {$this->args['season']} {$this->args['program']} - {$this->args['evds']}";

        // not working
        // mode=gsumm&season=Fall%202025&state=CA&program=Goleta &date=2025-10-18
        // mode=gsumm&season=Fall%202025&state=CA&program=Goleta &ev_refid=4569&evid=789&evds=2025-10-18

        // working
        // mode=tsumm&season=Fall%202025&state=CA&program=Goleta&ev_refid=790&evid=790&evds=2025-10-11
        // mode=tsumm&season=Fall%202025&state=CA&program=Goleta&ev_refid=790&ev_id=790&evds=2025-10-11
        
        // We need the particular evid for this event...  now it's passed in
        //$this->args['evid'] = $this->sdb->fetchVal("evid from ev","evprogram = ? and evistype = ? and evds = ?",array($this->args['program'],'GAME',$this->args['evds']));
        $b = "";
        // Get location info, possibly a better way to get it than this larger query
        //$evd = $this->sdb->getKeyedHash('evid',"select * from ev left join lc on ev_lcid = lcid where evds=? and evprogram=?",array($this->args['ds'],$this->args['program']));
        $evd = $this->sdb->getKeyedHash('evid',"SELECT * FROM ev LEFT JOIN lc ON ev_lcid = lcid WHERE evid=? and evprogram=?",array($this->args['evid'],$this->args['program']));
        if( count($evd) > 1 )  $b .= "ERROR on getKeyedHash (" . count($evd) . ")";
        if( count($evd) == 0 )  $b .= "ERROR on getKeyedHash (0)";
        else                   $d = array_shift($evd);
        $this->args['ev_refid'] = $d['ev_refid'];
        //print_pre($this->args,"Incoming args");
        //print_pre($d,"evd");

        // get event description, this is not based on the evid
        $descs = $this->sdb->fetchListNew("SELECT DISTINCT evname FROM ev LEFT JOIN gm ON ev.evid = gm.evid WHERE ev.evds = ? AND evprogram = ? AND evistype = ?",array($this->args['evds'],$this->args['program'],'GAME'));
        $desc = $descs[0];
        // seems like this could be done just using $d['evname']

        $label = ( $d['evname'] == $d['evtype']) ? "{$d['evname']}" : "{$d['evtype']} - {$d['evname']}";
        $label .= " ({$d['evid']})";
        // start building the page
        $cb = "";
        $cb .= "<h3>";
        //$cb .= "Date: {$this->args['date']}<br />";
        $cb .= "Date: {$this->args['evds']}<br />";
        $cb .= "Times: {$d['evtime_beg']} to {$d['evtime_end']}<br />";
        //$cb .= "Junk";
        //$cb .= d['evtime_beg'] . " to " .  $d['evtime_end']
        $cb .= "{$this->args['program']}<br />";
        $cb .= "$label<br />";
        $cb .= "</h3>";
        $cb .= "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";
        $b .= $this->contentDiv("Game Day",$cb);

        $b .= $this->dispGamesSummaries();   // produce summary of each individual game

        $dc = new digitalClock();
        $b .= $dc->dateTimeDiv("content");

        //$b .= $this->addLinks();
        //$b .= $this->addPDFMaterialsLinks($this->args['ev_refid'],'GAMES','Games PDF');
        $b .= $this->addPDFMaterialsLinks(array('GAMES','RULES'));

        return "$b";
    }
    ////////////////////////////////////////////////////////////////////////////
    function dispGamesSummaries(){
        $b = '';
        // Each age division, is, in essence, a pool
        // but we want to get a list of all the matches on this day
        $games = $this->sdb->fetchListNew("SELECT DISTINCT game FROM gm LEFT JOIN ev ON ev.evid = gm.evid WHERE ev.evid = ?",array($this->args['evid']));

            //$this->setArg('mode','gpool');
        foreach($games as $game){
            $tmdata = $this->sdb->getKeyedHash('tmid',"SELECT * FROM tm WHERE tmprogram = ?",array($this->args['program']));
            // could possibly get distinct times and then loop over that as well...
            $mdata = $this->sdb->getKeyedHash('gmid',"SELECT * FROM gm LEFT JOIN ev ON gm.evid = ev.evid WHERE ev.evid = ? AND game = ? ORDER BY court",array($this->args['evid'],$game));
            // print_pre($mdata,"matchdata");

            $bb = "";
            foreach($mdata as $pool){
                // this setup is NOT terribly great with only a single team in certain divisions...
                // The division label gets sort F'ed up - 1 team in each division moves 9-10 up to 11-12 and 13-15 down to 11-12 for matches

                // All the sk variable work below is to setup the "scorekeep this game" link below

                // to get poolid and poolnum there would need to be a join from ev to pool entry
                // pool entries are currently not in the active db (they were at one time)
                // $this->setArg('poolid',$pool['poolid']);
                // $this->setArg('poolnum',$pool['poolnum']);
                $time = $pool['time'];

                $div = $pool['pool'];
                $ct = $pool['court'];
                // $sk['team_a'] = $sk['team_b'] = "";
                $sk['team_a'] = ( $pool['tmid1'] == 'BYE') ? "BYE" : htmlentities($tmdata[$pool['tmid1']]['tmname'],ENT_QUOTES);
                $sk['team_b'] = ( $pool['tmid2'] == 'BYE') ? "BYE" : htmlentities($tmdata[$pool['tmid2']]['tmname'],ENT_QUOTES);
                $sk['tshirt_a'] = ( $pool['tmid1'] == 'BYE') ? "BYE" : $tmdata[$pool['tmid1']]['tmtshirt'];
                $sk['tshirt_b'] = ( $pool['tmid2'] == 'BYE') ? "NONE" : $tmdata[$pool['tmid2']]['tmtshirt'];

                $bb .= "<p>Ct. $ct - ($div div)<br />";

                if ( $sk['team_a'] == "" || $sk['team_b'] == "" ){
                    $bb .= $sk['team_a'] . " " . $sk['team_b'];
                }
                else {
                    $bb .= $sk['team_a'];
                    $bb .= "  <strong>VS.</strong>  ";
                    $bb .= $sk['team_b'];
                    $bb .= "<br />\n";
                    $bb .= ( $pool['tmid1'] == 'BYE' || $pool['tmid2'] == 'BYE') ? "" : $this->buildURL_li("./scorekeeper.php",$sk,"Scorekeep This Game");
                }
                $bb .= "\n</p>\n";
            }
            $b .= $this->contentDiv("Game $game Matches<br />$time",$bb);
        }
        return $b;
    }
    ////////////////////////////////////////////////////////////////////////////
    //function addLinks(){
    //    //global $sdb;
    //    $b = '';
//
    //    $pdid = $this->sdb->fetchVal("pdid from pdfs","pdf_refid = ? and pdfcat = ?",array($this->args['ev_refid'],'GAMES'));
    //    if ($pdid != ""){
    //        $b .= "<li class=\"nonereally\"><a href=\"displayPDF.php?pdid=$pdid\">Games PDF</a></li>\n";
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
    // ////////////////////////////////////////////////////////////////////////////
    // function awayGameMessage($tournhost = ""){
    //     $b = "";
    //     $b .= "<p>Because of limitations of the data we have access to, you will need to navigate to the home sites link ";
    //     $b .= "to see tournament details.  The link should be provided below.</p>";
    //     $b .= "<p>Sorry for the inconvenience.  This problem will be addressed at some point in the future.</p>";
    //     return $b;
    // }
}

?>
