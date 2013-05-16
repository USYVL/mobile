<html>
<head>
<link rel="stylesheet" href="http://mwf.library.ucsb.edu/assets/css.php" type="text/css">
<script type="text/javascript" src="http://mwf.library.ucsb.edu/assets/js.php?standard_libs=geolocation"></script>
<meta name="viewport" content="height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">

</head>
<body>
<h1 id="header"> 
    <a href="http://www.usyvl.org"> 
        <img src="http://mwf.library.ucsb.edu/assets/img/ucsb-header.png" alt="UCSB" width="75" height="35">
    </a> 
    <span>USYVL Mobile</span> 
</h1>



<?php

function topmenu($label = ""){
    print "<div class=\"menu-full menu-detailed menu-padded\">\n";
    print "<h1 class=\"light menu-first\">$label</h1>\n"; 
    print "<ol> \n";
    
}

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
?>
    </ol>
</div>

<a href="./" class="button-full button-padded">Return to Main Menu</a>
<a href="http://www.usyvl.org" class="button-full button-padded">USYVL Home</a>

<div id="footer"> 
    <p>University of California &copy; 2011 UC Regents<br> 
    <a href="http://www.usyvl.org/help">Help</a> | <a href="http://www.usyvl.org">View Full Site</a></p> 
</div>

</body>
</html>
