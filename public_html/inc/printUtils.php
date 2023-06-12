<?php
/*
  These routines were created probably around 2005
  Some fine tuned as recently as 2010.
*/
////////////////////////////////////////////////////////////////////////////////
function cprint_r($comment,$val,$returnme = false){
    $b = "<!-- Beg $comment\n";
    $b .= print_r($val,true);
    $b .=  "\nEnd $comment -->\n";
    
    if( ! $returnme ) print $b;
    return $b;
}
////////////////////////////////////////////////////////////////////////////////
function print_pre($v,$label = "",$returnme = false){
    $b = "";
    if ( $label != "" ){
        $b .= "<hr><font color=blue>$label</font>\n";
    }
    $b .= "<pre style='margin: 0; padding: 0;'>";
    
    $b .= print_r($v,true);
    
    $b .= "</pre>";
    if ( $label != "" ){
        $b .= "<font color=blue>$label</font><hr>\n";
    }
    
    if( ! $returnme ) print $b;
    return $b;
}
////////////////////////////////////////////////////////////////////////////////
// debug printing routine, input variables are:
//   function name, indent level, debug level, error text
////////////////////////////////////////////////////////////////////////////////
function dprint_orig($func = "",$indent = 0, $dlevel = 0, $string = "" ,$bcolor = "blue", $color = "black"){
  if( $dlevel > DEBUGLEVEL ){   // do nothing if not in the right range
    return; 
  }
  if( $indent > 0) {
    for($i=0;$i<$indent; $i++){
      print "&nbsp;&nbsp;";
    }
  }
  print "<font style='font-family: helvetica-boldOblique; font-size: 14;  color: $bcolor;'>$func : </font>";
  if( is_array($string) || is_object($string)){
    print "<font color=$color><pre>\n";
    print_r($string);
    print "</pre></font>\n";
  }
  else {
    print "<font color=$color>$string</font><br>\n";
  }
}
////////////////////////////////////////////////////////////////////////////////
// debug printing routine, input variables are:
//   function name, indent level, debug level, error text
// Same as dprint above but returns instead of printing...
////////////////////////////////////////////////////////////////////////////////
function dprint($func = "",$indent = 0, $dlevel = 0, $string = "" ,$bcolor = "blue", $color = "black",$returnme = false){
    $buf = "";
  if( $dlevel > DEBUGLEVEL ){   // do nothing if not in the right range
    return ""; 
  }
  if( $indent > 0) {
    for($i=0;$i<$indent; $i++){
      $buf .= "&nbsp;&nbsp;";
    }
  }
  $buf .= "<font style='font-family: helvetica-boldOblique; font-size: 14;  color: $bcolor;'>$func : </font>";
  if( is_array($string) || is_object($string)){
    $buf .= "<font color=$color><pre>\n";
    $buf .= print_r($string,true);
    $buf .= "</pre></font>\n";
  }
  else {
    $buf .= "<font color=$color>$string</font><br>\n";
  }
  if( ! $returnme ) print $buf;
  
  return $buf;
}
////////////////////////////////////////////////////////////////////////////////
function outputSpacer($len = 6000){
    // value of 6000 or so seems to provide almost line by line updates
    $str = "comment designed to force more frequent page updates to the browser viewing the output of a dynamically running script.  \n";
    $str .= "The additional volume is significant, but since this is expected to be run on a local or nearby system, the speed penalty should not be too high.";
    $l = strlen($str) + 20;  // the 9 is for the comment open and close, spaces, etc...
    //print "<!-- ";
    for($i=0;$i < $len; $i+=$l){
        print "<!-- ~$l character $str -->\n";
    }
    //print " -->\n";
}

?>
