<!DOCTYPE html>
<html lang="en">

<?php
// set which mwf we should be using, can work on autodetecting later...
$mwf = "http://localhost/usryl/mwf/root/assets";
$mwf = "http://localhost/usyvl/mwf-local/root/assets";
$mwf = "http://mwf.usyvl.org/assets";
$mwf = "//mwf.eri.ucsb.edu";
$mwf = "//mwf8.usyvl.org";
?>

<head>
<meta charset="utf-8">
<!-- MWF 1.3 setup -->
<link rel="stylesheet" type="text/css" href="<?php echo $mwf?>/assets/css.php?lean" media="screen">
<script type="text/javascript" src="<?php echo $mwf?>/assets/js.php?standard_libs=geolocation"></script>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">

<!-- <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script> -->
<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/digitalClock.js"></script>
<script type="text/javascript" src="js/qlib.js"></script>
<?php  print $content['scripts']; ?>
<link rel="stylesheet" href="css/digitalClock.css" type="text/css">
<link rel="stylesheet" href="css/social.css" type="text/css">
<?php  print $content['css']; ?>


<title><?php  print $content['title']; ?></title>

</head>

<body>
<!-- facebook script for follow and like -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- old twitter script -->
<!-- <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script> -->

<!-- twitter script -->
<script>window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "//platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);

  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };

  return t;
}(document, "script", "twitter-wjs"));</script>


<div id="errs">
<?php  print $content['errs']; ?>
</div>

<h1 id="header">
    <a href=".">
        <img src="./img/header-usyvl.png" alt="USYVL" width="50" height="35">
    </a>
    <span><a href=".">USYVL Mobile</a></span>
</h1>

<?php  print $content['body']; ?>

<a href="./" class="button button-padded">Main Menu</a>

<div class="social-block">
    <div class="social-container">
        <!-- twitter follow fun -->
        <div class="social">
            <a href="//twitter.com/usyvl" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @twitter</a>
        </div>

        <!-- facebook like -->
        <div class="social">
            <!-- <div class="fb-like" data-href="//developers.facebook.com/docs/plugins/" data-kid-directed-site="true" data-width="The pixel width of the plugin" data-height="The pixel height of the plugin" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div> -->
            <!-- <div class="fb-like" data-href="https://developers.facebook.com/docs/plugins/" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div> -->
        </div>

        <!-- facebook follow -->
        <div class="social">
            <!-- <div class="fb-follow" data-href="//www.facebook.com/usyvl" data-width="The pixel width of the plugin" data-height="The pixel height of the plugin" data-colorscheme="light" data-layout="button_count" data-kid-directed-site="true" data-show-faces="false"></div> -->
            <div class="fb-follow" data-href="//www.facebook.com/usyvl" data-layout="standard" data-size="small" data-show-faces="true"></div>
        </div>
    </div>
</div>


<div id="footer">
    <p>United States Youth Volleyball League &copy; 2013 USYVL<br />
    <a href="//www.usyvl.org/about-united-states-youth-volleyball-league/contact-us">Contact USYVL</a> |
    <a href="//www.usyvl.org">View USYVL Main Site</a><br />
    <a href="./?mode=indev">In Development</a>
    </p>
</div>

</body>
</html>
