<!DOCTYPE html>
<html lang="en">

<?php
// set which mwf we should be using, can work on autodetecting later...
$mwf = "http://localhost/usryl/mwf/root/assets";
$mwf = "http://localhost/usyvl/mwf-local/root/assets";
$mwf = "http://mwf.usyvl.org/assets";
?>

<head>
<meta charset="utf-8">

<!-- MWF 1.2 stuff
-->
<!--
<link rel="stylesheet" href="http://mwf.library.ucsb.edu/assets/css.php" type="text/css"> 
<script type="text/javascript" src="http://mwf.usyvl.org/assets/js.php?standard_libs=geolocation"></script>
<meta name="viewport" content="height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
-->

<!-- MWF 1.3 setup -->
<link rel="stylesheet" type="text/css" href="<?php echo $mwf?>/css.php?lean" media="screen">
<script type="text/javascript" src="<?php echo $mwf?>/js.php?standard_libs=geolocation"></script>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">

<link rel="stylesheet" href="css/usyvl.css" type="text/css">
<link rel="stylesheet" href="css/digital-clock.css" type="text/css">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript" src="js/qlib.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="js/digital-clock.js"></script>


<title><?php  print $content['title']; ?></title>

</head>

<body>
<div id="errs">
<?php  print $content['errs']; ?>
</div>

<h1 id="header"> 
    <a href="."> 
        <img src="http://mwf.usyvl.org/assets/img/header-usyvl.png" alt="USYVL" width="50" height="35">
    </a> 
    <span><a href=".">USYVL Mobile</a></span> 
</h1>

<?php  print $content['body']; ?>

<div id="footer">
    <p>United States Youth Volleyball League &copy; 2013 USYVL<br>
    <a href="http://www.usyvl.org/help">Help</a> | <a href="http://www.usyvl.org">View Full Site</a></p>
</div>

<div class="digital-clock">
<div id="Date"></div>
  <ul class="digital-clock">
      <li id="hours"></li>
      <li id="point">:</li>
      <li id="min"></li>
      <li id="point">:</li>
      <li id="sec"></li>
  </ul>
</div>

</body>
</html>

