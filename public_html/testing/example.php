<!DOCTYPE html>

<?php
$mwf = "http://localhost/usyvl/mwf/root/assets";
$mwf = "http://mwf.usyvl.org/assets";
?>

<html>
<head>
<title>MWF About</title>
<link rel="stylesheet" type="text/css" href="<?php echo $mwf?>/css.php?lean" media="screen">
<script type="text/javascript" src="<?php echo $mwf?>/js.php"></script>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
</head>
<body>
<h1 id="header">
<a href="http://localhost/usyvl/mwf/root">
<img src="http://mwf.usyvl.org/assets/img/mwf-header.gif" alt="MWF"></a><span>Entities Demo</span>
</h1>

<div class="button"><a href="#">Option</a></div>
<div class="button"><a href="#">Option 1</a>
<a href="#">Option 2</a></div><div class="button"><a href="#" class="light">Option Light</a></div>
<div class="button"><a href="#" class="light">Option 1 Light</a>
<a href="#" class="light">Option 2 Light</a>
</div>
<div class="button"><a href="#" class="light">Option 1 Light</a><a href="#">Option 2</a></div>
<div class="content"><h2>Content</h2></div><div class="content"><h2 class="light">Content Light</h2></div>
<div class="content"><h2>Content with Content</h2><p>Text</p><div>Text</div></div>
<div class="content"><h2 class="light">Content Light with Content</h2><p>Text</p><div>Text</div></div>
<div class="content"><p>Text</p><div>Text</div></div><div class="menu"><h2>Menu</h2></div>
<div class="menu"><h2 class="light">Menu Light</h2></div><div class="menu detailed"><h2>Menu Detailed</h2></div>
<div class="menu detailed"><h2 class="light">Menu Light Detailed</h2></div>
<div class="menu"><h2>Menu</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div>
<div class="menu"><h2 class="light">Menu Light</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div>
<div class="menu detailed center"><h2>Menu Detailed Center</h2><ul><li><a href="#">Item 1<p>Description</p></a></li>
<li><a href="#">Item 2<p>Description</p></a></li></ul></div>
<div class="menu detailed center"><h2 class="light">Menu Light Detailed Center</h2><ul>
<li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul>
</div>
<div class="menu left"><h2>Menu Left-Aligned</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div>
<div class="menu left"><h2 class="light">Menu Light Left</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div>
<div class="menu detailed"><h2>Menu Detailed</h2>
<ul>
<li><a href="#">Item 1<p>Description</p></a></li>
<li><a href="#">Item 2<p>Description</p></a></li>
</ul></div>
<div class="menu detailed"><h2 class="light">Menu Light Detailed</h2><ul><li><a href="#">Item 1<p>Description</p></a></li>
<li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu"><ul><li><a href="#">Item 1</a></li>
<li><a href="#">Item 2</a></li></ul></div><div class="menu detailed center"><ul><li><a href="#">Item 1<p>Description</p></a></li>
<li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu left"><ul><li><a href="#">Item 1</a></li>
<li><a href="#">Item 2</a></li></ul></div><div class="menu detailed"><ul><li><a href="#">Item 1<p>Description</p></a></li>
<li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="button not-padded"><a href="#">Option</a></div>
<div class="button not-padded"><a href="#">Option 1</a><a href="#">Option 2</a></div>
<div class="button not-padded"><a href="#" class="light">Option Light</a></div>
<div class="button not-padded"><a href="#" class="light">Option 1 Light</a>
<a href="#" class="light">Option 2 Light</a></div><div class="button not-padded">
<a href="#" class="light">Option 1 Light</a><a href="#">Option 2</a></div><div class="content not-padded">
<h2>Content Padded</h2>
</div>
<div class="content not-padded"><h2 class="light">Content Padded Light</h2></div>
<div class="content not-padded"><h2>Content Padded</h2><p>Text</p><div>Text</div></div>
<div class="content not-padded"><h2 class="light">Content Padded Light</h2><p>Text</p>
<div>Text</div></div><div class="content not-padded"><p>Text</p><div>Text</div></div>
<div class="menu not-padded"><h2>Menu Padded</h2></div><div class="menu not-padded">
<h2 class="light">Menu Padded Light</h2></div><div class="menu detailed not-padded">
<h2>Menu Padded Detailed</h2></div><div class="menu detailed not-padded">
<h2 class="light">Menu Padded Light Detailed</h2></div><div class="menu not-padded">
<h2>Menu Padded</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div><div class="menu not-padded"><h2 class="light">Menu Padded Light</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div><div class="menu detailed not-padded center"><h2>Menu Padded Detailed Centered</h2><ul><li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu detailed not-padded center"><h2 class="light">Menu Padded Light Detailed Centered</h2><ul><li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu not-padded left"><h2>Menu Padded Left-Aligned</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div><div class="menu not-padded left"><h2 class="light">Menu Padded Light Left</h2><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div><div class="menu detailed not-padded"><h2>Menu Padded Detailed</h2><ul><li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu detailed not-padded"><h2 class="light">Menu Padded Light Detailed</h2><ul><li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu not-padded"><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div><div class="menu not-padded"><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div><div class="menu detailed not-padded center"><ul><li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu detailed not-padded center"><ul><li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul></div><div class="menu not-padded left"><ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul></div><div class="menu detailed not-padded"><ul><li><a href="#">Item 1<p>Description</p></a></li><li><a href="#">Item 2<p>Description</p></a></li></ul></div><a id="button-top" class="button" href="/">Top Button</a><div class="button not-padded"><a href="http://localhost/usyvl/mwf/root/mwf/demos.php">Back To Demos</a></div><div id="footer"><p>University of California &copy; 2010-12 UC Regents</p><p style="font-weight:bold;font-style:italic">Powered by the<br><span class="external"><a rel="external" class="no-ext-ind" href="http://mwf.ucla.edu" target="_blank">Mobile Web Framework</a></span>
</p></div></body></html>
