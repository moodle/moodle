<?php
  
/*
V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
*/ 
 
 /* this file is used by the ADODB test program: test.php */
 

// cannot test databases below, but we include them anyway to check
// if they parse ok...
ADOLoadCode("sybase");
ADOLoadCode("postgres");
ADOLoadCode("postgres7");
ADOLoadCode("firebird");
ADOLoadCode("borland_ibase");
ADOLoadCode("informix");
ADOLoadCode("sqlanywhere");
flush();
if (!empty($testpostgres)) {
	//ADOLoadCode("postgres");
	$db = &ADONewConnection('postgres');
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("localhost","tester","test","test")) {
		testdb($db,"create table ADOXYZ (id integer, firstname char(24), lastname varchar,created date)");
	}else
		print "ERROR: PostgreSQL requires a database called test on server susetikus, user tester, password test.<BR>".$db->ErrorMsg();
}
if (!empty($testibase)) {
	
	$db = &ADONewConnection('firebird');
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("localhost:e:\\firebird\\examples\\employee.gdb", "sysdba", "masterkey", ""))
		testdb($db,"create table ADOXYZ (id integer, firstname char(24), lastname char(24),price numeric(12,2),created date)");
	 else print "ERROR: Interbase test requires a database called employee.gdb".'<BR>'.$db->ErrorMsg();
	
}

// REQUIRES ODBC DSN CALLED nwind
if (!empty($testaccess)) {
	$db = &ADONewConnection('access');
	print "<h1>Connecting $db->databaseType...</h1>";
	
	if (@$db->PConnect("nwind", "", "", ""))
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
		testdb($db,"create table d:\\inetpub\\wwwroot\\php\\vfp\\ADOXYZ (id int, firstname char(24), lastname char(24),created date)");
	 } else print "ERROR: Visual FoxPro test requires a Windows ODBC DSN=logos2, VFP driver";
	
}


// REQUIRES MySQL server at localhost with database 'test'
if (!empty($testmysql)) { // MYSQL
	
	$db = &ADONewConnection('mysql');
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($HTTP_SERVER_VARS['HTTP_HOST'] == 'localhost') $server = 'localhost';
	else $server = "mangrove";
	if ($db->PConnect('jaguar', "mobydick", "", "test"))
		testdb($db,
		"create table ADOXYZ (id int, firstname char(24), lastname char(24), created date) type=innodb");
	else print "ERROR: MySQL test requires a MySQL server on localhost, userid='admin', password='', database='test'".'<BR>'.$db->ErrorMsg();
}

// REQUIRES MySQL server at localhost with database 'test'
if (!empty($testmysqlodbc)) { // MYSQL
	
	$db = &ADONewConnection('odbc');
	$db->hasTransactions = false;
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($HTTP_SERVER_VARS['HTTP_HOST'] == 'localhost') $server = 'localhost';
	else $server = "mangrove";
	if ($db->PConnect('mysql', "mobydick", ""))
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
	if ($db->NConnect('', "scott", "tiger",'natsoft.ecosystem.natsoft.com.my'))
	//if ($db->PConnect("", "scott", "tiger", "juris.ecosystem.natsoft.com.my"))
		testdb($db,"create table ADOXYZ (id int, firstname varchar(24), lastname varchar(24),created date)");
	else print "ERROR: Oracle test requires an Oracle server setup with scott/tiger".'<BR>'.$db->ErrorMsg();

}
ADOLoadCode("oracle"); // no longer supported
if (false && !empty($testoracle)) { 
	
	$db = ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($db->PConnect("", "scott", "tiger", "natsoft.domain"))
		testdb($db,"create table ADOXYZ (id int, firstname varchar(24), lastname varchar(24),created date)");
	else print "ERROR: Oracle test requires an Oracle server setup with scott/tiger".'<BR>'.$db->ErrorMsg();

}


ADOLoadCode("odbc_mssql");
if (!empty($testmssql)) { // MS SQL Server via ODBC
	
	$db = ADONewConnection();
	
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("netsdk", "adodb", "natsoft", ""))  {
		testdb($db,"create table ADOXYZ (id int, firstname char(24) null, lastname char(24) null,created datetime null)");
	}
	else print "ERROR: MSSQL test 1 requires a MS SQL 7 server setup with DSN setup";

}

ADOLoadCode("ado_mssql");

if (!empty($testmssql) && !empty($testado) ) { // ADO ACCESS MSSQL -- thru ODBC -- DSN-less
	
	$db = &ADONewConnection("ado_mssql");
	$db->debug=1;
	print "<h1>Connecting DSN-less $db->databaseType...</h1>";
	
	$myDSN="PROVIDER=MSDASQL;DRIVER={SQL Server};"
		. "SERVER=JAGUAR\VSDOTNET;DATABASE=NorthWind;UID=adodb;PWD=natsoft;Trusted_Connection=No"  ;

		
	if (@$db->PConnect($myDSN, "", "", ""))
		testdb($db,"create table ADOXYZ (id int, firstname char(24) null, lastname char(24) null,created datetime null)");
	else print "ERROR: MSSQL test 2 requires MS SQL 7";
	
}


ADOLoadCode("mssqlpo");
if (!empty($testmssql)) { // MS SQL Server -- the extension is buggy -- probably better to use ODBC
	$db = ADONewConnection();
	$db->debug=1;
	print "<h1>Connecting $db->databaseType...</h1>";
	
	$db->PConnect('JAGUAR\vsdotnet','adodb','natsoft','northwind');
	
	if (true or @$db->PConnect("mangrove", "sa", "natsoft", "ai")) {
		AutoDetect_MSSQL_Date_Order($db);
	//	$db->Execute('drop table adoxyz');
		testdb($db,"create table ADOXYZ (id int, firstname char(24) null, lastname char(24) null,created datetime null)");
	} else print "ERROR: MSSQL test 2 requires a MS SQL 7 on a server='192.168.0.1', userid='sa', password='natsoft', database='ai'".'<BR>'.$db->ErrorMsg();
	
}

if (!empty($testmssql) && !empty($testado)) { // ADO ACCESS MSSQL with OLEDB provider

	$db = &ADONewConnection("ado_mssql");
	print "<h1>Connecting DSN-less OLEDB Provider $db->databaseType...</h1>";
	$db->debug=1;
	$myDSN="SERVER=(local)\NetSDK;DATABASE=northwind;Trusted_Connection=yes";
	//$myDSN='SERVER=(local)\NetSDK;DATABASE=northwind;';
	if ($db->PConnect($myDSN, "sa", "natsoft", 'SQLOLEDB'))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: MSSQL test 2 requires a MS SQL 7 on a server='mangrove', userid='sa', password='', database='ai'";

}


print "<h3>Tests Completed</h3>";

?>
