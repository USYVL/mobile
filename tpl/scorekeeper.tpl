<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<link rel="stylesheet" href="http://mwf.library.ucsb.edu/assets/css.php" type="text/css">
<link rel="stylesheet" href="css/scorekeeper.css" type="text/css">
<script type="text/javascript" src="http://mwf.library.ucsb.edu/assets/js.php?standard_libs=geolocation"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript" src="js/scorekeeper.js"></script>
<meta name="viewport" content="height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title><?php  print $content['title']; ?></title>

</head>


<body>
<div id="errs">
<?php  print $content['errs']; ?>
</div>

<h1 id="header"> 
    <a href="http://www.usyvl.org"> 
        <img src="http://mwf.library.ucsb.edu/assets/img/ucsb-header.png" alt="UCSB" width="75" height="35">
    </a> 
    <span>USYVL Mobile</span> 
</h1>



<?php  print $content['body']; ?>
<!--
<div id="skwrapper">
  <div id="hometeam_wrapper">
    <p class="team_label">Home Team</p>
    <button id="hometeam" class="score">25</button>
  </div>
  <div id="awayteam_wrapper">
    <p class="team_label">Visiting</p>
    <button id="awayteam" class="score">25</button>  
  </div>
</div>
<div class="clear">
</div>
-->


<div id="footer">
    <p>United States Youth Volleyball League &copy; 2013 USYVL<br>
    <a href="http://www.usyvl.org/help">Help</a> | <a href="http://www.usyvl.org">View Full Site</a></p>
</div>

</body>
</html>

