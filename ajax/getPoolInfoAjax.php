<?php
require_once("../config.php");
require_once("dbManagement.php");
require_once("usyvlDB.php");
require_once("mwfMobileSite_tourn.php");

$ms = new mwfMobileSite_tourn();
print $ms->poolInfo();

?>
