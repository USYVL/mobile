<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<!-- <link rel="stylesheet" href="http://mwf.library.ucsb.edu/assets/css.php" type="text/css"> -->
<link rel="stylesheet" href="http://mwf.usyvl.org/assets/css.php" type="text/css">
<script type="text/javascript" src="http://mwf.usyvl.org/assets/js.php?standard_libs=geolocation"></script>
<meta name="viewport" content="height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
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
    <span>USYVL Mobile</span> 
</h1>



<?php  print $content['body']; ?>


<div id="footer">
    <p>United States Youth Volleyball League &copy; 2013 USYVL<br>
    <a href="http://www.usyvl.org/help">Help</a> | <a href="http://www.usyvl.org">View Full Site</a></p>
</div>

</body>
</html>

