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
$sdb = new dbMgmt('sdb','sqlite:' . __DIR__ . '/../io/db/sched.sqlite3','Scheduling Database',$logdb);
$evtable = new dbMgmtTable("ev");                                     // should probably add a u_id
///$evtable->addCol("evid","integer","primary key","","");
///$evtable->addCol("season","text","","","");
///$evtable->addCol("program","text","","","");
///$evtable->addCol("name","text","","","");
///$evtable->addCol("date","text","","","");
///$evtable->addCol("ds","text","","","");
///$evtable->addCol("time_beg","text","","","");
///$evtable->addCol("time_end","text","","","");
///$evtable->addCol("dow","text","","","");
///$evtable->addCol("location","text","","","");
///$evtable->addCol("addr","text","","","");
///$evtable->addCol("city","text","","","");
///$evtable->addCol("state","text","","","");
///$evtable->addCol("zip","text","","","");
$sdb->addTable($evtable);

$mdb = new dbMgmt('mdb','sqlite:' . __DIR__ . '/../io/db/redbook.sqlite3','Instructional Summary Database',$logdb);
$ist = new dbMgmtTable("dd");                            // drill day table, wanted to use is for instructional summary, but "is" is a reserved word in sql
///$ist->addCol("ddid","integer","primary key","","");     
///$ist->addCol("ddday","integer","","","");                // because we need to sort on this, we want to make it an integer OR force to have leading 0's
///$ist->addCol("ddtype","text","","","");                  // four letter, uppercase type/code for the days drills typically PRAC or GAME
///$ist->addCol("ddneth","text","","","");                  // comma separated list of netheights corresponding to age divisions
///$ist->addCol("ddpdfh","text","","","");                  // height of this chunk for pdf page break calculations
$mdb->addTable($ist);

// here are the drill descriptions
// the big question is whether we have the schedule items be in a separate table
// could use one table with a type: pre, sched, notes
// could use label field for time if we wanted...
$drt = new dbMgmtTable("dr");                            // drill day table, wanted to use is for instructional summary, but "is" is a reserved word in sql
///$drt->addCol("drid","integer","primary key","","");     
///$drt->addCol("drday","integer","","","");                // corresponds to ddday above, although we will not likely be joining or sorting on it
///$drt->addCol("drtype","text","","","");                  // type of entry pre, sched (goes in table), notes, post (pro, epi?)
///$drt->addCol("drweight","integer","","","");             // weight, to order entries, both descriptions and schedules
///$drt->addCol("drtime","text","","","");                  // number of minutes for this particular drill
///$drt->addCol("drlabel","text","","","");                 // optional label associated with a given drill desc, often larger and bold
///$drt->addCol("drcontent","text","","","");               // main content
$mdb->addTable($drt);


?>
