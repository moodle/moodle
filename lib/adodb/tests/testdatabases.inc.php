<?php
  
/*
V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
*/ 
 
 /* this file is used by the ADODB test program: test.php */
 ?>

<table><tr valign=top><td>
<form method=get>
<input type=checkbox name="testaccess" value=1 <?php echo !empty($testaccess) ? 'checked' : '' ?>> <b>Access</b><br>
<input type=checkbox name="testibase" value=1 <?php echo !empty($testibase) ? 'checked' : '' ?>> <b>Interbase</b><br>
<input type=checkbox name="testmssql" value=1 <?php echo !empty($testmssql) ? 'checked' : '' ?>> <b>MSSQL</b><br>
 <input type=checkbox name="testmysql" value=1 <?php echo !empty($testmysql) ? 'checked' : '' ?>> <b>MySQL</b><br>
<input type=checkbox name="testmysqlodbc" value=1 <?php echo !empty($testmysqlodbc) ? 'checked' : '' ?>> <b>MySQL ODBC</b><br>
<td><input type=checkbox name="testsqlite" value=1 <?php echo !empty($testsqlite) ? 'checked' : '' ?>> <b>SQLite</b><br>
<input type=checkbox name="testproxy" value=1 <?php echo !empty($testproxy) ? 'checked' : '' ?>> <b>MySQL Proxy</b><br>
<input type=checkbox name="testoracle" value=1 <?php echo !empty($testoracle) ? 'checked' : '' ?>> <b>Oracle (oci8)</b> <br>
<input type=checkbox name="testpostgres" value=1 <?php echo !empty($testpostgres) ? 'checked' : '' ?>> <b>PostgreSQL</b><br>
<input type=checkbox name="testpgodbc" value=1 <?php echo !empty($testpgodbc) ? 'checked' : '' ?>> <b>PostgreSQL ODBC</b><br>
<td><input type=checkbox name="testdb2" value=1 <?php echo !empty($testdb2) ? 'checked' : '' ?>> DB2<br>
<input type=checkbox name="testvfp" value=1 <?php echo !empty($testvfp) ? 'checked' : '' ?>> VFP<br>
<input type=checkbox name="testado" value=1 <?php echo !empty($testado) ? 'checked' : '' ?>> ADO (for mssql and access)<br>
<input type=checkbox name="nocountrecs" value=1 <?php echo !empty($nocountrecs) ? 'checked' : '' ?>> $ADODB_COUNTRECS=false<br>
<input type=checkbox name="nolog" value=1 <?php echo !empty($nolog) ? 'checked' : '' ?>> No SQL Logging<br>
<td><input type=submit>
</form>
</table>
<?php

if ($ADODB_FETCH_MODE != ADODB_FETCH_DEFAULT) print "<h3>FETCH MODE IS NOT ADODB_FETCH_DEFAULT</h3>";

if (isset($nocountrecs)) $ADODB_COUNTRECS = false;

// cannot test databases below, but we include them anyway to check
// if they parse ok...

if (!strpos(PHP_VERSION,'5') === 0) {
	ADOLoadCode("sybase");
	ADOLoadCode("postgres");
	ADOLoadCode("postgres7");
	ADOLoadCode("firebird");
	ADOLoadCode("borland_ibase");
	ADOLoadCode("informix");
	ADOLoadCode("sqlanywhere");
}


flush();
if (!empty($testpostgres)) {
	//ADOLoadCode("postgres");

	$db = &ADONewConnection('postgres');
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->Connect("localhost","tester","test","test")) {
		testdb($db,"create table ADOXYZ (id integer, firstname char(24), lastname varchar,created date)");
	}else
		print "ERROR: PostgreSQL requires a database called test on server, user tester, password test.<BR>".$db->ErrorMsg();
}

if (!empty($testpgodbc)) { 
	
	$db = &ADONewConnection('odbc');
	$db->hasTransactions = false;
	print "<h1>Connecting $db->databaseType...</h1>";
	
	if ($db->PConnect('Postgresql')) {
		$db->hasTransactions = true;
		testdb($db,
		"create table ADOXYZ (id int, firstname char(24), lastname char(24), created date) type=innodb");
	} else print "ERROR: PostgreSQL requires a database called test on server, user tester, password test.<BR>".$db->ErrorMsg();
}

if (!empty($testibase)) {
	
	$db = &ADONewConnection('firebird');
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("localhost:d:\\firebird\\10\\examples\\employee.gdb", "sysdba", "masterkey", ""))
		testdb($db,"create table ADOXYZ (id integer, firstname char(24), lastname char(24),price numeric(12,2),created date)");
	 else print "ERROR: Interbase test requires a database called employee.gdb".'<BR>'.$db->ErrorMsg();
	
}


if (!empty($testsqlite)) {
	$db = &ADONewConnection('sqlite');
	print "<h1>Connecting $db->databaseType...</h1>";
	
	if (@$db->PConnect("d:\\inetpub\\adodb\\sqlite.db", "", "", ""))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: SQLite";
	
}

// REQUIRES ODBC DSN CALLED nwind
if (!empty($testaccess)) {
	$db = &ADONewConnection('access');
	print "<h1>Connecting $db->databaseType...</h1>";
	
	$dsn = "nwind";
	$driver = "Driver={Microsoft Access Driver (*.mdb)};Dbq=d:\inetpub\adodb\northwind.mdb;Uid=Admin;Pwd=;";
	if (@$db->PConnect($dsn, "", "", ""))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: Access test requires a Windows ODBC DSN=nwind, Access driver";
	
}

if (!empty($testaccess) && !empty($testado)) { // ADO ACCESS

	$db = &ADONewConnection("ado_access");
	print "<h1>Connecting $db->databaseType...</h1>";
	
	$access = 'd:\inetpub\wwwroot\php\NWIND.MDB';
	$myDSN =  'PROVIDER=Microsoft.Jet.OLEDB.4.0;'
		. 'DATA SOURCE=' . $access . ';';
		//. 'USER ID=;PASSWORD=;';
	
	if (@$db->PConnect($myDSN, "", "", "")) {
		print "ADO version=".$db->_connectionID->version."<br>";
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	} else print "ERROR: Access test requires a Access database $access".'<BR>'.$db->ErrorMsg();
	
}

if (!empty($testvfp)) { // ODBC
	$db = &ADONewConnection('vfp');
	print "<h1>Connecting $db->databaseType...</h1>";flush();

	if ( $db->PConnect("vfp-adoxyz")) {
		testdb($db,"create table d:\\inetpub\\adodb\\ADOXYZ (id int, firstname char(24), lastname char(24),created date)");
	 } else print "ERROR: Visual FoxPro test requires a Windows ODBC DSN=vfp-adoxyz, VFP driver";
	
}


// REQUIRES MySQL server at localhost with database 'test'
if (!empty($testmysql)) { // MYSQL

	$db = &ADONewConnection('mysql');
	print "<h1>Connecting $db->databaseType...</h1>";
	if (PHP_VERSION >= 5 || $HTTP_SERVER_VARS['HTTP_HOST'] == 'localhost') $server = 'localhost';
	else $server = "mangrove";
	if ($db->PConnect($server, "root", "", "northwind")) {
		//$db->debug=1;$db->Execute('drop table ADOXYZ');
		testdb($db,
		"create table ADOXYZ (id int, firstname char(24), lastname char(24), created date)");
	} else print "ERROR: MySQL test requires a MySQL server on localhost, userid='admin', password='', database='test'".'<BR>'.$db->ErrorMsg();
}

// REQUIRES MySQL server at localhost with database 'test'
if (!empty($testmysqlodbc)) { // MYSQL
	
	$db = &ADONewConnection('odbc');
	$db->hasTransactions = false;
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($HTTP_SERVER_VARS['HTTP_HOST'] == 'localhost') $server = 'localhost';
	else $server = "mangrove";
	if ($db->PConnect('mysql', "root", ""))
		testdb($db,
		"create table ADOXYZ (id int, firstname char(24), lastname char(24), created date) type=innodb");
	else print "ERROR: MySQL test requires a MySQL server on localhost, userid='admin', password='', database='test'".'<BR>'.$db->ErrorMsg();
}

if (!empty($testproxy)){
	$db = &ADONewConnection('proxy');
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($HTTP_SERVER_VARS['HTTP_HOST'] == 'localhost') $server = 'localhost';

	if ($db->PConnect('http://localhost/php/phplens/adodb/server.php'))
		testdb($db,
		"create table ADOXYZ (id int, firstname char(24), lastname char(24), created date) type=innodb");
	else print "ERROR: MySQL test requires a MySQL server on localhost, userid='admin', password='', database='test'".'<BR>'.$db->ErrorMsg();

}

ADOLoadCode('oci805');
ADOLoadCode("oci8po");
if (!empty($testoracle)) { 
	
	$db = ADONewConnection('oci8po');
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($db->Connect('', "scott", "natsoft",''))
	//if ($db->PConnect("", "scott", "tiger", "juris.ecosystem.natsoft.com.my"))
		testdb($db,"create table ADOXYZ (id int, firstname varchar(24), lastname varchar(24),created date)");
	else print "ERROR: Oracle test requires an Oracle server setup with scott/natsoft".'<BR>'.$db->ErrorMsg();

}
ADOLoadCode("oracle"); // no longer supported
if (false && !empty($testoracle)) { 
	
	$db = ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($db->PConnect("", "scott", "tiger", "natsoft.domain"))
		testdb($db,"create table ADOXYZ (id int, firstname varchar(24), lastname varchar(24),created date)");
	else print "ERROR: Oracle test requires an Oracle server setup with scott/tiger".'<BR>'.$db->ErrorMsg();

}

ADOLoadCode("db2"); // no longer supported
if (!empty($testdb2)) { 
	
	$db = ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($db->Connect("db2_sample", "root", "natsoft", ""))
		testdb($db,"create table ADOXYZ (id int, firstname varchar(24), lastname varchar(24),created date)");
	else print "ERROR: DB2 test requires an server setup with odbc data source db2_sample".'<BR>'.$db->ErrorMsg();

}


ADOLoadCode('odbc_mssql');
if (!empty($testmssql)) { // MS SQL Server via ODBC
	$db = ADONewConnection();
	
	print "<h1>Connecting $db->databaseType...</h1>";
	
	$dsn = "mssql-northwind";
	$dsn = "Driver={SQL Server};Server=localhost;Database=northwind;";
	
	if (@$db->PConnect($dsn, "adodb", "natsoft", ""))  {
		testdb($db,"create table ADOXYZ (id int, firstname char(24) null, lastname char(24) null,created datetime null)");
	}
	else print "ERROR: MSSQL test 1 requires a MS SQL 7 server setup with DSN setup";

}

ADOLoadCode("ado_mssql");

if (!empty($testmssql) && !empty($testado) ) { // ADO ACCESS MSSQL -- thru ODBC -- DSN-less
	
	$db = &ADONewConnection("ado_mssql");
	//$db->debug=1;
	print "<h1>Connecting DSN-less $db->databaseType...</h1>";
	
	$myDSN="PROVIDER=MSDASQL;DRIVER={SQL Server};"
		. "SERVER=tigress;DATABASE=NorthWind;UID=adodb;PWD=natsoft;Trusted_Connection=No"  ;

		
	if (@$db->PConnect($myDSN, "", "", ""))
		testdb($db,"create table ADOXYZ (id int, firstname char(24) null, lastname char(24) null,created datetime null)");
	else print "ERROR: MSSQL test 2 requires MS SQL 7";
	
}


ADOLoadCode("mssqlpo");
if (!empty($testmssql)) { // MS SQL Server -- the extension is buggy -- probably better to use ODBC
	$db = ADONewConnection("mssqlpo");
	//$db->debug=1;
	print "<h1>Connecting $db->databaseType...</h1>";
	
	$ok = $db->PConnect('tigress','adodb','natsoft','northwind');
	//$rs = $db->Execute("exec sp_ddate");
	//print_r($rs->fields);
	//die();

	if ($ok or @$db->PConnect("mangrove", "sa", "natsoft", "ai")) {
		AutoDetect_MSSQL_Date_Order($db);
	//	$db->Execute('drop table adoxyz');
		testdb($db,"create table ADOXYZ (id int, firstname char(24) null, lastname char(24) null,created datetime null)");
	} else print "ERROR: MSSQL test 2 requires a MS SQL 7 on a server='192.168.0.1', userid='sa', password='natsoft', database='ai'".'<BR>'.$db->ErrorMsg();
	
}

if (!empty($testmssql) && !empty($testado)) { // ADO ACCESS MSSQL with OLEDB provider

	$db = &ADONewConnection("ado_mssql");
	print "<h1>Connecting DSN-less OLEDB Provider $db->databaseType...</h1>";
	//$db->debug=1;
	$myDSN="SERVER=tigress;DATABASE=northwind;Trusted_Connection=yes";
	//$myDSN='SERVER=(local)\NetSDK;DATABASE=northwind;';
	if ($db->PConnect($myDSN, "adodb", "natsoft", 'SQLOLEDB'))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: MSSQL test 2 requires a MS SQL 7 on a server='mangrove', userid='sa', password='', database='ai'";

}


print "<h3>Tests Completed</h3>";

?>
