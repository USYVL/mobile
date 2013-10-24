<?php
class digitalClock {
    function __construct(){
        // configuration is done in the digitalClock.js file
        // this class merely allows easy production of the required html
    }
    function timeChunk($baseIndent = 4){
        $b = "";
        $b .= $this->indent($baseIndent,'<ul class="digitalClock">');  
        $b .= $this->indent($baseIndent,'    <li id="digitalClock_hours"></li>');
        $b .= $this->indent($baseIndent,'    <li id="digitalClock_point">:</li>');
        $b .= $this->indent($baseIndent,'    <li id="digitalClock_min"></li>');
        $b .= $this->indent($baseIndent,'    <li id="digitalClock_point">:</li>');
        $b .= $this->indent($baseIndent,'    <li id="digitalClock_sec"></li>');
        $b .= $this->indent($baseIndent,'    <li id="digitalClock_merid"></li>');
        $b .= $this->indent($baseIndent,'</ul>');
        return $b;
    }
    function indent($count = 0,$str = ""){
        return str_repeat(" ",$count) . $str;
    }
    function timeHtml(){
        $b = "";
        $b .= "<div class=\"digitalClock\"><!-- begin digitalClock wrapper -->\n";
        $b .= $this->timeChunk(4);
        $b .= "</div><!-- end digitalClock wrapper -->\n";
        return $b;
    }
    function dateTimeHtml($sIndent = 0){
        $b = "";
        $b .= $this->indent($sIndent,'<div class="digitalClock">');
        $b .= $this->indent($sIndent+4,'<div id="digitalClock_date"></div>');
        $b .= $this->timeChunk($sIndent+8);
        $b .= $this->indent($sIndent+4,'</div>');
        $b .= $this->indent($sIndent,'</div>');
        return $b;
    }
}
?>
