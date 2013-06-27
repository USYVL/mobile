<?php
require_once("inc/dbManagement.inc");
require_once("inc/usyvlDB.inc");

define('DEBUGLEVEL',0);


// Should probably do some sanitizing of the names, shortening, maybe use jquery lib
// to get the length set correctly.
$team_a = ( isset($_GET['team_a'])) ? $_GET['team_a'] : "Team A";
$team_b = ( isset($_GET['team_b'])) ? $_GET['team_b'] : "Team B";


$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";

$b .= '<div id="skwrapper">';
$htb = '

  <div id="c1_wrapper" class="lfloat">
    <div class="section_style">
    ';
$htb .= "      <button id=\"tmA\" class=\"score\">0</button>\n";
$htb .= "        <button id=\"tmA_minus\" class=\"decrement\">-</button>  \n";
$htb .= "        <span id=\"tmA_service\" class=\"service\">Serving</span>  \n";
$htb .= '<p id="tmAname" class="team_label">' . "$team_a</p>\n";
$htb .= "    </div>\n";
$htb .= "  </div> \n";
  
  
$atb .= '<div id="c2_wrapper" class="rfloat">
    <div class="section_style">
    ';
$atb .= "      <button id=\"tmB\" class=\"score\">0</button>  \n";
$atb .= "        <button id=\"tmB_minus\" class=\"decrement\">-</button>  \n";
$atb .= "        <span id=\"tmB_service\" class=\"service hide\">Serving</span>  \n";
$atb .= '<p id="tmBname" class="team_label">' . "$team_b</p>\n";
$atb .= "    </div>\n";
$atb .= "  </div>\n";



$b .= "$htb";
$b .= "$atb";

$b .= "<div class=\"clear\"></div><!-- clear floats -->\n";
$b .= "</div><!-- close skwrapper -->\n";


$b .= '<div id="winner"></div>';
$b .= '<a class="button padded short" id="switch_sides">Switch Sides</a>' . "\n";
$b .= '<a class="button short" id="toggle_serve">Toggle Initial Service</a>' . "\n";
$b .= '<a class="button short" id="scoreType">DoubleMax</a>' . "\n";
//$b .= '<audio controls id="whistle"><source src="media/whistle-single.mp3" type="audio/mpeg"></audio>';
//$b .= '<div class="play">Play</div>';


$b .= '<div class="button padded">' . "\n";
$b .= '<a id="play-single" href="#">Whistle</a>' . "\n";
$b .= '<a id="play-double" href="#">WhistleX2</a>' . "\n";
$b .= '</div>' . "\n";

//$b .= '<div id="notes"></div>' . "\n";

$b .= '<div id="notes" class="content"></div>';

$content['body'] .= "$b";
$content['title'] = "ScoreKeeper";  // title is not set till after display is run...
$content['errs'] .= "";

include("tpl/scorekeeper.tpl");


?>

