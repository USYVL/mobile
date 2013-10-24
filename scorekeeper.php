<?php
require_once("config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");
require_once("digitalClock.php");

define('DEBUGLEVEL',0);

function sanitize($str){
    $s = htmlspecialchars($str);
    $s = str_replace(array("The","the"),"",$s);
    $s = str_replace(array("\\",""),"",$s);
    $s = preg_replace("/\d+ *- */","",$s);
    return $s;
}

// Should probably do some sanitizing of the names, shortening, maybe use jquery lib
// to get the length set correctly.
$team_a = ( isset($_GET['team_a'])) ? sanitize($_GET['team_a']) : sanitize("The A's");
$team_b = ( isset($_GET['team_b'])) ? sanitize($_GET['team_b']) : sanitize("01 - The BB's");
$tshirt_a = ( isset($_GET['tshirt_a'])) ? sanitize($_GET['tshirt_a']) : sanitize("blue");
$tshirt_b = ( isset($_GET['tshirt_b'])) ? sanitize($_GET['tshirt_b']) : sanitize("red");


$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";
$content['css'] = "";
$content['scripts'] = "";
$content['css']      .= '<link rel="stylesheet" href="css/scorekeeper.css" type="text/css">' . "\n";
$content['scripts']  .= '<script type="text/javascript" src="js/scorekeeper.js"></script>' . "\n";

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
$atb .= "        <span id=\"tmB_service\" class=\"service hideservice\">Serving</span>  \n";
$atb .= '<p id="tmBname" class="team_label">' . "$team_b</p>\n";
$atb .= "    </div>\n";
$atb .= "  </div>\n";



$b .= "$htb";
$b .= "$atb";

$b .= "<div class=\"clear\"></div><!-- clear floats -->\n";
$b .= "</div><!-- close skwrapper -->\n";


$b .= '<div id="winner"></div>';

$b .= "<div id=\"TheClock\" class=\"content\">\n";
$digitalClock = new digitalClock();
$b .= $digitalClock->timeHtml();
$b .= "</div>\n";

$b .= '<div class="button padded">' . "\n";
$b .= '<a id="play-single" href="#">Whistle</a>' . "\n";
$b .= '<a id="play-double" href="#">WhistleX2</a>' . "\n";
$b .= '</div>' . "\n";

$b .= '<a class="button padded short" id="switch_sides">Swap Scorepads</a>' . "\n";
$b .= '<a class="button short" id="toggle_serve">Toggle Initial Service</a>' . "\n";
$b .= '<a class="button short" id="scoreType">DoubleMax</a>' . "\n";
//$b .= '<audio controls id="whistle"><source src="media/whistle-single.mp3" type="audio/mpeg"></audio>';
//$b .= '<div class="play">Play</div>';


$b .= '<div class="button short">' . "\n";
$b .= '<a id="tmA_color" href="#">' . $tshirt_a . '</a>' . "\n";
$b .= '<a id="tmB_color" href="#">' . $tshirt_b . '</a>' . "\n";
$b .= '</div>' . "\n";

$b .= '<div id="help" class="content">' . "\n";
$b .= "<h2>Instructions</h2>\n";
$b .= "<h3>The following controls can only be changed while the score is 0-0</h3>\n";
$b .= "<p>Scoring Type: (Default: DoubleMax) Clicking the button toggles between the different scoring options.</p>\n";
$b .= "<p>Toggle Initial Service: shifts the serve indicator (\"Serving\") to the other team.  Service should be automatically tracked once scoring begins.</p>\n";
$b .= "<p>Score Panel Color: click button to toggle between available colors.  The color is initially set to the teams tshirt color (when available or defaults if not).</p>\n";
$b .= "<h3>The following controls are available at all times:</h3>\n";
$b .= "<p>Score Pads: Tap the score pad of the team that wins the rally.  The score is incremented and service indicator is updated.</p>\n";
$b .= "<p>Tap the Minus sign just below each scorepad to decrement the score (in the event of a mistake).  <span class=\"r\">NOTE: </span> that the service indicator may no longer correctly reflect the server once the score has been corrected. </p>\n";
$b .= "<p>Swap Sides: This will swap the Score Pad positions to accomodate team side changes.</p>\n";
$b .= "<h3>Tips</h3>\n";
$b .= "<p></p>\n";
$b .= "</div>\n";

$b .= '<div id="notes" class="content"></div>';

$content['body'] .= "$b";
$content['title'] = "ScoreKeeper";  // title is not set till after display is run...
$content['errs'] .= "";

include("tpl/usyvl.tpl");


?>

