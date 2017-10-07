<?php

require_once("config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");

// most of our posts will use PDID
if (isset($_GET['pdid'])){
    if ( preg_match("/^[0-9]+$/",$_GET['pdid'])){
        $pdid = $_GET['pdid'];
        $pdfstr = $GLOBALS['sdb']->fetchVal('pdfstr from pdfs','pdid = ?',array($pdid));
        $d = $GLOBALS['sdb']->getKeyedHash('pdid','select * from pdfs where pdid = ?',array($pdid));
        if( isset($d[$pdid]['pdfcompression']) && $d[$pdid]['pdfcompression'] == 'ZLIB'){
            $pdfstr = gzuncompress($d[$pdid]['pdfstr']);
        }
        else {
            $pdfstr = $d[$pdid]['pdfstr'];
        }
        displayPDF($pdfstr,$d[$pdid]['pdfbase'] . ".pdf");
    }
    else {
        print "Sorry we had an error (pdid not an int)";
    }
}
else {
    // Need to pass in a 'pdf_refid' argument
    if (isset($_GET['pdf_refid'])){
        if ( preg_match("/^[0-9]+$/",$_GET['pdf_refid'])){
            $pdf_refid = $_GET['pdf_refid'];
        }
        else {
            print "Sorry we had an error (pdf_refid not an int)";
        }
    }
    else {
        print "Sorry we had an error (no pdf_refid)<br>\n";
    }
    if (isset($_GET['pdfcat'])){
        if ( preg_match("/^[A-Z]+$/",$_GET['pdfcat'])){
            $pdfcat = $_GET['pdfcat'];
        }
        else {
            print "Sorry we had an error (pdcat not all uppercase alpha)";
        }
    }
    else {
        print "Sorry we had an error (no pdf_refid)<br>\n";
    }

    $d = $GLOBALS['sdb']->getKeyedHash('pdf_refid','select * from pdfs where pdf_refid = ? and pdfcat = ?;',array($pdf_refid,$pdfcat));
    if ( isset($d[$pdf_refid]['pdfcompression']) && $d[$pdf_refid]['pdfcompression'] == 'ZLIB' ){
        $pdfstr = gzuncompress($d[$pdf_refid]['pdfstr']);
    }
    else {
        $pdfstr = $d[$pdf_refid]['pdfstr'];
    }
    displayPDF($pdfstr,$d[$pdf_refid]['pdfbase'] . ".pdf");
}
////////////////////////////////////////////////////////////////////////////////
//
// During initial deployment, the name was introduced WITHOUT the .pdf suffix
// Am taking the easy way out here
////////////////////////////////////////////////////////////////////////////////
function displayPDF($pdfStr = "" , $name = "UnknownName" ){
    if ( $pdfStr == "" ) return;

    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.
    header("Content-type:application/pdf");
    header("Content-Disposition:inline;filename=${name}");
    print $pdfStr;
}
?>
