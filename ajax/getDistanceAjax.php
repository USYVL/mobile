<?php
require_once(__DIR__ . "/../inc/printUtils.inc");
require_once(__DIR__ . "/../inc/gcd.inc");
require_once(__DIR__ . "/../inc/dbManagement.inc");
require_once(__DIR__ . "/../inc/csvUtils.inc");
require_once(__DIR__ . "/../inc/usyvlDB.inc");

//print __DIR__ . "<br />\n";

$sdb = $GLOBALS['dbh']['sdb'];
date_default_timezone_set('America/Los_Angeles');
// .1 is ~ 6.9 miles at 47.8, -122.2
// using the tolerance for the coarse check basically checks within a square 
$location_tol = 0.6;
$miles_tol = sprintf("%.2f miles",($location_tol * 69));
$ds_tol = "7 days";

$bds = date("Y-m-d");
$eds = date("Y-m-d",strtotime('+' . $ds_tol));
if( isset($_GET['lat'])){
    $lat = $_GET['lat'];
    $latmin = $_GET['lat'] - $location_tol;
    $latmax = $_GET['lat'] + $location_tol;
}
if( isset($_GET['lon'])){
    $lon = $_GET['lon'];
    $lonmin = $_GET['lon'] - $location_tol;
    $lonmax = $_GET['lon'] + $location_tol;
}

// should return nothing if we didnt get a lat lon passed in

//print "Checking for sites between $latmin,$latmax,$lonmin,$lonmax<br />\n";
$data = $sdb->getKeyedHash('lcid',"select * from lc where ( cast(lclat as real) > ? and cast(lclat as real) < ? and cast(lclon as real) > ? and cast(lclon as real) < ? )",array($latmin,$latmax,$lonmin,$lonmax));
//print_pre($data,"sites within the predescribed tolerance ($location_tol degrees)");

$distance_hash = array();  // create a hash with 
// loop over results getting distances
foreach( array_keys($data) as $lcid){
    $cdist = calcDist($lat,$lon,$data[$lcid]['lclat'],$data[$lcid]['lclon']);
    //$gcdist = gcd($lat,$lon,$data[$lcid]['lclat'],$data[$lcid]['lclon']);
    //print "distance $cdist miles<br />\n";
    $data[$lcid]['dist'] = $cdist;
    $distance_hash[$lcid] = $cdist;
}
asort($distance_hash);

$h = "<h2>Found the following USYVL events:</h2>\n";
$b = "";
foreach($distance_hash as $k => $v ){
    $events = $sdb->getKeyedHash('evid',"select * from ev left join lc on lcid = ev_lcid where ( lcid = ? and evds >= ? and evds <= ? ) order by evds",array($lcid,$bds,$eds));
    //print_pre($events,"events for lcid=$lcid between $bds and $eds");
    foreach($events as $e){
        $b .= $data[$k]['lclocation'] . "<br />";
        $b .= $data[$k]['lcaddress'] . "<br />";
        $b .= sprintf("%.2f miles",$v) . " away<br />\n";
        $b .= $e['evname'] . ": ";
        $b .= $e['evds'] . " at ";
        $b .= $e['evtime_beg'] . "-";
        $b .= $e['evtime_end'] . "<br /><br />";
    }
    
}

// ultimately we want to produce a list of nearby programs.
// Should this be program events instead??? 
// If the latter we could also check temporally to locate the precise practice/game/tournament

// should organize entries by distance proximity first and then by date

( $b != "" ) ? print "$h$b" : print "<p>Unable to find any USYVL events<br />within approx. $miles_tol and $ds_tol</p>\n";


?>
