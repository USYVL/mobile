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

$sdb = new dbMgmt('sdb','sqlite:' . __DIR__ . '/../io/db/sched.sqlite3','Scheduling Database',$logdb);
$evtable = new dbMgmtTable("ev");                                     // should probably add a u_id
$evtable->addCol("evid","integer","primary key","","");
$evtable->addCol("season","text","","","");
$evtable->addCol("program","text","","","");
$evtable->addCol("name","text","","","");
$evtable->addCol("date","text","","","");
$evtable->addCol("ds","text","","","");
$evtable->addCol("time_beg","text","","","");
$evtable->addCol("time_end","text","","","");
$evtable->addCol("dow","text","","","");
$evtable->addCol("location","text","","","");
$evtable->addCol("addr","text","","","");
$evtable->addCol("city","text","","","");
$evtable->addCol("state","text","","","");
$evtable->addCol("zip","text","","","");
$sdb->addTable($evtable);


?>
