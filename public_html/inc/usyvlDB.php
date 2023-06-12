<?php
require_once("dbManagement.php");

////////////////////////////////////////////////////////////////////////////////
// search for sqlite3 to find where season name addition should go....
// actually, it will be best to use it where we call usyvlDbDesc.  That way,
// statically named dbs (access can be used across seasons
////////////////////////////////////////////////////////////////////////////////
// Directory needs to be writeable for PDO to work correctly (at least for sqlite).
// must use a temp file...
// sqlite does not have a varchar() type, it has: null, integer, real, text, blob
////////////////////////////////////////////////////////////////////////////////
// the switch from using p_id instead of p_name really only involves db references.
// since php info is reloaded each time, using program as a hash key for example is
// fine as long as its loaded from the db each time as any p_name changes will be
// relected automatically.  The problem comes when p_name is stored in another db
// field (taskGroups).  Then a change in p_name causes any task status to be lost.
//
////////////////////////////////////////////////////////////////////////////////
// when I create a new season db, want to specify some defaults and clear some fields
//
// need to start documenting sequences, series, procedures and dependencies
////////////////////////////////////////////////////////////////////////////////

$s_key = ( isset($_SESSION['s_key']) ) ?  "{$_SESSION['s_key']}" : "unset" ;

// since we dont actually create any of these here, the particulars of the table definitions
// dont matter, although it does look like we have to declare them, probably so that
// things get initialized correctly.
$sdb = new dbMgmt('sdb','sqlite:' . MOBILE_SCHED_DB,'Scheduling Database',$logdb);
$evtable = new dbMgmtTable("ev");                                     // should probably add a u_id
$sdb->addTable($evtable);

$mdb = new dbMgmt('mdb','sqlite:' . REDBOOK_MANUAL_DB,'Instructional Summary Database',$logdb);
$ist = new dbMgmtTable("dd");                            // drill day table, wanted to use is for instructional summary, but "is" is a reserved word in sql
$mdb->addTable($ist);

$drt = new dbMgmtTable("dr");                            // drill day table, wanted to use is for instructional summary, but "is" is a reserved word in sql
$mdb->addTable($drt);


?>
