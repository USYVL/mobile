<?php
require_once("../config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");
// require_once("mwfMobileSite_tourn.php");
require_once("tournSummarries_inc.php");

// can test this with
// ajax/getPoolInfoAjax.php?poolid=110
$ms = new mwfMobileSite_tourn();
print $ms->poolInfo();

?>
