<?php
require_once("inc/dbManagement.inc");
require_once("inc/usyvlDB.inc");

define('DEBUGLEVEL',0);

$content['errs'] = "";
$content['title'] = "";
$content['body'] = "";

$b .= '<div id="skwrapper">';
$htb = '

  <div id="c1_wrapper" class="lfloat">
    <div class="section_style">
      <p id="tmAname" class="team_label">Team A</p>
      <button id="tmA" class="score">0</button>
        <button id="tmA_minus" class="decrement">-</button>  
        <span id="tmA_service" class="service">Serving</span>  
    </div>
  </div>
  ';
  
  
$atb .= '<div id="c2_wrapper" class="rfloat">
    <div class="section_style">
      <p id="tmBname" class="team_label">Team B</p>
      <button id="tmB" class="score">0</button>  
        <button id="tmB_minus" class="decrement">-</button>  
        <span id="tmB_service" class="service hide">Serving</span>  
    </div>
  </div>
';

$b .= "$htb";
$b .= "$atb";

$b .= '<div class="clear"></div><!-- clear floats -->';

$b .= '<div id="winner"></div>';
$b .= '<button id="switch_sides">Switch Sides</button>';
$b .= '<button id="toggle_serve">Toggle Initial Service</button>';
$b .= '<button id="scoreType">DoubleMax</button>';
$b .= '<div id="notes"></div>';

$b .= '</div><!-- close skwrapper -->
';

$b .= '<div id="notes" class="content-padded"></div>';

$content['body'] .= "$b";
$content['title'] = "ScoreKeeper";  // title is not set till after display is run...
$content['errs'] .= "";

include("tpl/scorekeeper.tpl");


?>

