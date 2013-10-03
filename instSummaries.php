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
        $this->registerFunc('isumm'       , 'dispISumm'     );
    }
    function dispDates(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $this->title = "USYVL Mobile - Select Date from $state Program $program for $season";
        
        $m = "";
        $dates = $sdb->fetchList("distinct evds from ev","evprogram = '$program'",'evds');
        //$divisions = $sdb->fetchList("distinct tmdiv from tm left join so on tmdiv=so_div","( tmprogram=? and tmseason=? )","so_order",array($program,$season));
        //$divisions = $sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div where ( tmprogram=? and tmseason=? ) order by so_order",array($program,$season));
        foreach( $dates as $date){
           $m .= "  <li><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=isumm&date=$date&season=$season&state=$state&program=$program\">$date</a></li>\n";
        }
        $b = $this->fMenu("Select Date",$m);
        
        return "$b";
    }
    function dispISumm(){
        $sdb = $GLOBALS['dbh']['sdb'];
        $mdb = $GLOBALS['dbh']['mdb'];
        $state = $_GET['state'];
        $program = $_GET['program'];
        $season = $_GET['season'];
        $date = $_GET['date'];
        //$division = $_GET['division'];
        $this->title = "USYVL Mobile - Instructional Summary - $season $program";
        
        $b = "";
        
        // so we need to get evisday - this is the number of the day in the manual
        $isdays = $sdb->fetchList("evisday from ev","evprogram = '$program' and evds ='$date'",'evisday');
        
        // could get the isdays above from this
        $evd = $sdb->getKeyedHash('evid',"select * from ev left join lc on ev_lcid = lcid where evds=? and evprogram=?",array($date,$program));
        if( count($evd) > 1 ) {
            $b .= "ERROR on getKeyedHash";
        }
        else {
            $d = array_shift($evd);
        }
        
        //$b .= "<div class=\"content content-full\">\n";
        //$b .= "<h2 class=\"light\">\n";
        
        $t  = "";
        $t .= "Instructional Summary<br />\n$program<br />\n$date<br />\n";
        $t .= $d['evname'] . "<br />\n" . $d['evtime_beg'] . " to " .  $d['evtime_end'] . "\n";
        $t .= "</h2>\n";
        
        $c  = "";
        $c .= "<h3>" . $d['lclocation'] . "<br />" . $d['lcaddress'] . "</h3>\n";
        
        foreach( $isdays as $isday){
            if( $isday == "" ){
                // So we have something going on, should be OFF, SKIP or INTE type
            }
            else {
                $divisions = $this->sdb->fetchListNew("select distinct tmdiv from tm left join so on tmdiv=so_div order by so_order");

                $isd = $mdb->getKeyedHash('ddid',"select * from dd where ddday = ?",array($isday));
                $netheights = explode(",",$isd[$isday]['ddneth']);
                
                $nh  = "";
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
                //$nh .= "<p>Net Heights: " . $isd[$isday]['ddneth'] . "</p>\n";

                // prep drills table
                $drills = $mdb->getKeyedHash('drid',"select * from dr where drday = ? and drtype = 'DRILL' order by drweight",array($isday));
                $bl  = "";
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
                
                
                // drill descriptions
                $dl = "";
                $descs = $mdb->getKeyedHash('drid',"select * from dr where drday = ? and drtype = 'DRILLDESC' order by drweight",array($isday));
                foreach( $descs as $desc){
                    $dl .= "<p><strong>" . $desc['drlabel'] . ":</strong>  " . $desc['drcontent'] . "</p>\n";
                }
                
            }
        }
        $b .= $this->contentDiv($t,$c);
        $b .= $this->contentDiv("Net Heights",$nh);
        $b .= $this->contentDiv("Drill Schedule",$bl);
        $b .= $this->contentDiv("Drill Details/Notes",$dl);
        
        return "$b";
    }
}

$ms = new usyvlMobileSite();

$content['body'] .= $ms->display();
$content['title'] = $ms->getTitle();  // title is not set till after display is run...
$content['errs'] .= "";

//ob_start();
include("tpl/usyvl.tpl");
//print ob_get_clean();
?>

