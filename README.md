# mobile
Utilizes UCLA's Mobile Web Framework (MWF 1.3.x) framework to create a mobile website for USYVL programs.

Primarily driven by a single SQLite database (io/db/sched.sqlite3) that must be writable by the httpd process owner

The schema is as follows:
```
CREATE TABLE dm (dmid integer primary key,dm_src text ,dm_dst text ,dmgcd text ,dmcar text ,dmbike text ,dmfoot text );
CREATE TABLE evo (evid integer primary key,evseason text ,evprogram text ,evname text ,evdate text ,evds text ,evtime_beg text ,evtime_end text ,evdow text ,evlocation text ,evaddr text ,evcity text ,evstate text ,evzip text );
CREATE TABLE tm (tmid integer primary key,tmseason text ,tmprogram text ,tmnum integer ,tmname text ,tmdiv text ,tmcourt integer ,tmcoach text ,tmtshirt text );
CREATE TABLE ev (evid integer primary key,evseason text ,evprogram text ,evname text ,evdate text ,evds text ,evtime_beg text ,evtime_end text ,evdow text ,evistype text ,evisday text ,ev_lcid integer ,evtnid integer );
CREATE TABLE lc (lcid integer primary key,lclocation text ,lcaddress text ,lcstreet text ,lccity text ,lcstate text ,lczip text ,lclat text ,lclon text ,lcsrc text ,lcq text );
CREATE TABLE so (soid integer primary key,so_div integer ,so_order integer );
CREATE TABLE gm (gmid integer primary key,tmid1 integer ,tmid2 integer ,evid integer ,court integer ,game integer ,pool text ,time text );
CREATE TABLE pool (poolid integer primary key,p_evid integer ,poollayout text ,courts text ,times text ,division text ,poolnum integer ,neth text ,tmids text );
```

There is also a practice drill database (io/db/redbook.sqlite3) that must be writable by the httpd process owner.

Schema is as follows:
```
CREATE TABLE dd (ddid integer primary key,ddday integer ,ddtype text ,ddneth text ,ddpdfh text );
CREATE TABLE dr (drid integer primary key,drday integer ,drtype text ,drweight integer ,drtime text ,drlabel text ,drcontent text );
```
