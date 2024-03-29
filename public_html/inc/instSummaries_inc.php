<?php
require_once("mwfMobileSiteClass.php");
require_once("digitalClock.php");

class usyvlMobileSite extends mwfMobileSite {
    public   string   $title;
    //////////////////////////////////////////////////////////////////////////////////////////
    function __construct(){
        parent::__construct();
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    function registerExtendedFunctions(){
        $this->registerFunc('launch'   , 'dispDates'     );  // use the divisions key, since thats what the core "programs" uses
        $this->registerFunc('isumm'    , 'dispISumm'     );
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    // This is the list of events for the season for a given site
    //////////////////////////////////////////////////////////////////////////////////////////
    function dispDates(){
        $this->initArgs('tsumm',array('mode','season','state','program'));

        $this->title = "USYVL Mobile - Select Date from {$this->args['state']} Program {$this->args['program']} for {$this->args['season']}";

        $m = "";
        //$dates = $sdb->fetchListNew("select distinct evds from ev where evseason=? and evprogram = ?",array($season,$program));
        // using evds as key is actually not good - breaks if there are two events same day (ie: Parent Meeting, Coaching Clinic)
        $dates = $this->sdb->fetchListNew("SELECT DISTINCT evds FROM ev WHERE evseason=? AND evprogram=? ORDER BY evds",array($this->getArg('season'),$this->getArg('program')));
        //print_pre($dates,"dates");
        //$dates = array_keys($evh);

        foreach($dates as $date){
            $evh = $this->sdb->getKeyedHash('evid',"SELECT * FROM ev WHERE evseason=? AND evprogram=? AND evds=? ORDER BY evtime_beg",array($this->getArg('season'),$this->getArg('program'),$date));

            //print_pre($evh,"Event Hash");
            foreach($evh as $evid => $evd){
                $this->setArg('ev_refid',$evd['ev_refid']);
                $this->setArg('evid',$evd['evid']);
                $date = $evd['evds'];
                $this->setArg('evds',$date);
                //foreach( $dates as $date){
                //$label = "$date - " . $evistypemap[$evd['evistype']];
                $label = "$date - " . $evd['evname'];
                // if the event is a tournament/intersite game day, then switch over to the tournament summary page for this day
                // could do this as a switch possibly
                if( $evd['evistype'] == 'INTE' ){
                    $this->setArg('mode','tsumm');
                    // this works but pretty sure it is not needed here
                    //$this->setArg('ev_refid',$evd['ev_refid']);
                    //$m .= "  <li><a href=\"./tournSummaries.php?mode=tsumm&date=$date&season=$season&state=$state&program=$program\">$label</a></li>\n";
                    $m .= $this->buildURL_li('./tournSummaries.php',$this->args,$label,"class=\"nonereally\"");
                }
                elseif( $evd['evistype'] == 'GAME' ){
                    $this->setArg('mode','gsumm');
                    //$m .= "  <li><a href=\"./tournSummaries.php?mode=tsumm&date=$date&season=$season&state=$state&program=$program\">$label</a></li>\n";
                    $m .= $this->buildURL_li('./gameSummaries.php',$this->args,$label,"class=\"nonereally\"");
                }
                else {
                    $this->setArg('mode','isumm');
                    //$m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=isumm&date=$date&season=$season&state=$state&program=$program\">$label</a></li>\n";
                    $m .= $this->buildURL_li($_SERVER['PHP_SELF'],$this->args,$label,"class=\"nonereally\"");
                }
            }
        }
        $b = $this->contentList($this->args['program'] . "<br />Daily Schedule Entries",$m);

        $b .= $this->addPDFMaterialsLinks(array('INSTRUCT','GAMES','RULES'));
        //$b .= $this->addPDFMaterialsLinks($this->args['ev_refid'],'INSTRUCT','Instructional Summary PDF');

        return "$b";
    }
    //////////////////////////////////////////////////////////////////////////////////////////
    // this is the daily summary
    //////////////////////////////////////////////////////////////////////////////////////////
    function dispISumm(){
        $keyError = false;
        $this->initArgs('tsumm',array('mode','season','state','program','evds','evid'));
        $this->title = "USYVL Mobile - Instructional Summary - {$this->args['season']} {$this->args['program']}";

        $b = "";

        // so we need to get evisday - this is the number of the day in the manual
        $isdays = $this->sdb->fetchListNew("select evisday from ev where evid = ? and evprogram=? and evds=?",array($this->args['evid'],$this->args['program'],$this->args['evds']));
        //print_pre($isdays,"IS Day List");

        // could get the isdays above from this
        // since we are now passing in evid, the extra fields should be redundant
        $evd = $this->sdb->getKeyedHash('evid',"select * from ev left join lc on ev_lcid = lcid where evid = ? and evds=? and evprogram=?",array($this->args['evid'],$this->args['evds'],$this->args['program']));
        //$evd = $this->sdb->getKeyedHash('evid',"select * from ev left join lc on ev_lcid = lcid where evid = ?",array($this->args['evid']));
        //print_pre($evd,"Raw data");
        // Not sure where these checks are at now.... Pretty sure
        // the multiple events per days are now handled gracefully...
        $evcount = count($evd);
        if ($evcount < 1){
            // need to bail
            $keyError = true;
        }
        else {
            $d = array_shift($evd);
        }
        //// shouldn't really get keyErrors any more since the addition of the evid argument.
        //if( count($evd) > 1 ) {
        //    $keyError = true;
        //    $evd = $this->sdb->getKeyedHash('evid',"select * from ev where evds=? and evprogram=?",array($this->args['evds'],$this->args['program']));
        //    if( count($evd) > 1 ) {
        //        // maybe completely bail
        //    }
        //    else {
        //        $d = array_shift($evd);
        //    }
        //    //$b .= "ERROR on getKeyedHash";
        //}
        //else {
        //    $d = array_shift($evd);
        //}

        //$d = array_shift($evd);

        //$b .= "<div class=\"content content-full\">\n";
        //$b .= "<h2 class=\"light\">\n";

        $t  = "";
        $t .= "Instructional Summary<br />\n{$this->args['program']}<br />\n{$d['evdate']}<br />\n";
        $t .= ($keyError) ? "{$d['evname']}" : $d['evname'] . "<br />\n" . $d['evtime_beg'] . " to " .  $d['evtime_end'] . "\n";
        $t .= "</h2>\n";

        $c  = "";
        $c .= ($keyError) ? "EVD $evcount" : "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";

        foreach( $isdays as $isday){
            if( $isday == "" ){
                // So we have something going on, should be OFF, SKIP or INTE type
            }
            else {
                $divisions = $this->sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div where tmprogram not like '%Advanced Juniors%' order by so_order");

                $isd = $this->mdb->getKeyedHash('ddday',"select * from dd where ddday = ?",array($isday));
                $isd = $this->mdb->getKeyedHash('ddid',"select * from dd where ddday = ?",array($isday));
                //print_pre($isd,"Data from redbook.dd");
                // Build the Net Heights block
                $netheights = explode(",",$isd[$isday]['ddneth']);
                //print_pre($netheights,"netheights from redbook.dd");
                $nh  = "";
                if(count($netheights) > 0 && ( $netheights[0] != '' && $netheights[0] != 'NA' )){
                    $nh .= "<table class=\"isumm\">";
                    $nh .= "<tr class=\"isumm\">";
                    $nh .= "<td class=\"isumm isummtime\"><strong>Division</strong></td>\n";
                    $nh .= "<td class=\"isumm isummdesc\"><strong>Setting</strong></td>\n";
                    $nh .= "</tr>";
                    foreach( $divisions as $k => $di){
                        $nh .= "<tr class=\"isumm\">";
                        $nh .= "<td class=\"isumm isummtime\">" . $di . "</td>\n";
                        $nh .= "<td class=\"isumm isummdesc\">" . $netheights[$k] . "</td>\n";
                        $nh .= "</tr>";
                    }
                    $nh .= "</table>";
                }
                //$nh .= "<p>Net Heights: " . $isd[$isday]['ddneth'] . "</p>\n";

                // Build the drill/instructional Schedule
                $drills = $this->mdb->getKeyedHash('drid',"select * from dr where drday = ? and drtype = 'DRILL' order by drweight",array($isday));
                $bl  = "";
                if(count($drills) > 0){
                    $bl .= "<table class=\"isumm\">";
                    $bl .= "<tr class=\"isumm\">";
                    $bl .= "<td class=\"isumm isummtime\"><strong>Min.</strong></td>\n";
                    $bl .= "<td class=\"isumm isummdesc\"><strong>Drill Description</strong></td>\n";
                    $bl .= "</tr>";
                    foreach( $drills as $drill){
                        $bl .= "<tr class=\"isumm\">";
                        $bl .= "<td class=\"isumm isummtime\">" . $drill['drtime'] . "</td>\n";
                        $bl .= "<td class=\"isumm isummdesc\">" . $drill['drcontent'] . "</td>\n";
                        $bl .= "</tr>";
                    }
                    $bl .= "</table>";
                }

                // Build the drill/instructional details block
                //print_pre($isday,"IS Day");
                $dl = "";
                $descs = $this->mdb->getKeyedHash('drid',"select * from dr where drday = ? and drtype = 'DRILLDESC' order by drweight",array($isday));
                foreach( $descs as $desc){
                    $dl .= "<p>\n";
                    if( $desc['drlabel'] != "" )   $dl .= "<strong>" . $desc['drlabel'] . ":</strong>  ";
                    $dl .= $desc['drcontent'] . "</p>\n";
                }
            }
        }
        $b .= $this->contentDiv($t,$c);
        $b .= ($keyError || $nh == "") ? "" : $this->contentDiv("Net Heights",$nh);
        $b .= ($keyError || $bl == "") ? "" : $this->contentDiv("Drill Schedule (Day $isday)",$bl);
        $b .= ($keyError || $dl == "") ? "" : $this->contentDiv("Drill Details/Notes",$dl);

        $dc = new digitalClock();
        $b .= $dc->dateTimeDiv("content");

        return "$b";
    }
}
?>
