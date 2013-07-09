<!DOCTYPE html>
<html lang="en">

<?php
// set which mwf we should be using, can work on autodetecting later...
$mwf = "http://localhost/usyvl/mwf/root/assets";
$mwf = "http://localhost/usyvl/mwf-local/root/assets";
$mwf = "http://mwf.usyvl.org/assets";
?>

<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $mwf?>/css.php?lean" media="screen">
<script type="text/javascript" src="<?php echo $mwf?>/js.php"></script>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript" src="js/qlib.js"></script>
<link rel="stylesheet" href="css/scorekeeper.css" type="text/css"> 
<script type="text/javascript" src="js/scorekeeper.js"></script>

<title><?php  print $content['title']; ?></title>

</head>


<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

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
<a href="https://twitter.com/usyvl" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @twitter</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

<div class="fb-follow" data-href="https://www.facebook.com/usyvl" data-show-faces="false" data-width="450"></div>

<a href="http://www.usyvl.org" class="button">USYVL Website</a>
<a href="./" class="button">Main Menu</a>

<div id="footer">
    <p>United States Youth Volleyball League &copy; 2013 USYVL<br>
    <a href="http://www.usyvl.org/help">Help</a> | <a href="http://www.usyvl.org">View Full Site</a></p>
</div>


</body>
</html>

