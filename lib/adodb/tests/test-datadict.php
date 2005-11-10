<?php
/*

  V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.

*/

error_reporting(E_ALL);
include_once('../adodb.inc.php');

foreach(array('sapdb','sybase','mysqlt','access','oci8','postgres','odbc_mssql','odbc','db2','firebird','informix') as $dbType) {
	echo "<h3>$dbType</h3><p>";
	$db = NewADOConnection($dbType);
	$dict = NewDataDictionary($db);

	if (!$dict) continue;
	$dict->debug = 1;
	
	$opts = array('REPLACE','mysql' => 'TYPE=INNODB', 'oci8' => 'TABLESPACE USERS');
	
/*	$flds = array(
		array('id',	'I',								
							'AUTO','KEY'),
							
		array('name' => 'firstname', 'type' => 'varchar','size' => 30,
							'DEFAULT'=>'Joan'),
							
		array('lastname','varchar',28,
							'DEFAULT'=>'Chen','key'),
							
		array('averylonglongfieldname','X',1024,
							'NOTNULL','default' => 'test'),
							
		array('price','N','7.2',
							'NOTNULL','default' => '0.00'),
							
		array('MYDATE', 'D', 
							'DEFDATE'),
		array('TS','T',
							'DEFTIMESTAMP')
	);*/
	
	$flds = "
ID            I           AUTO KEY,
FIRSTNAME     VARCHAR(30) DEFAULT 'Joan',
LASTNAME      VARCHAR(28) DEFAULT 'Chen' key,
averylonglongfieldname X(1024) DEFAULT 'test',
price         N(7.2)  DEFAULT '0.00',
MYDATE        D      DEFDATE,
BIGFELLOW     X      NOTNULL,
TS            T      DEFTIMESTAMP";


	$sqla = $dict->CreateDatabase('KUTU',array('postgres'=>"LOCATION='/u01/postdata'"));
	$dict->SetSchema('KUTU');
	
	$sqli = ($dict->CreateTableSQL('testtable',$flds, $opts));
	$sqla = array_merge($sqla,$sqli);
	
	$sqli = $dict->CreateIndexSQL('idx','testtable','firstname,lastname',array('BITMAP','FULLTEXT','CLUSTERED','HASH'));
	$sqla = array_merge($sqla,$sqli);
	$sqli = $dict->CreateIndexSQL('idx2','testtable','price,lastname');//,array('BITMAP','FULLTEXT','CLUSTERED'));
	$sqla = array_merge($sqla,$sqli);
	
	$addflds = array(array('height', 'F'),array('weight','F'));
	$sqli = $dict->AddColumnSQL('testtable',$addflds);
	$sqla = array_merge($sqla,$sqli);
	$addflds = array(array('height', 'F','NOTNULL'),array('weight','F','NOTNULL'));
	$sqli = $dict->AlterColumnSQL('testtable',$addflds);
	$sqla = array_merge($sqla,$sqli);
	
	
	printsqla($dbType,$sqla);
	
	if (file_exists('d:\inetpub\wwwroot\php\phplens\adodb\adodb.inc.php'))
	if ($dbType == 'mysqlt') {
		$db->Connect('localhost', "root", "", "test");
		$dict->SetSchema('');
		$sqla2 = $dict->ChangeTableSQL('adoxyz',$flds);
		if ($sqla2) printsqla($dbType,$sqla2);
	}
	if ($dbType == 'postgres') {
		if (@$db->Connect('localhost', "tester", "test", "test"));
		$dict->SetSchema('');
		$sqla2 = $dict->ChangeTableSQL('adoxyz',$flds);
		if ($sqla2) printsqla($dbType,$sqla2);
	}
	
	if ($dbType == 'odbc_mssql') {
		$dsn = $dsn = "PROVIDER=MSDASQL;Driver={SQL Server};Server=localhost;Database=northwind;";
		if (@$db->Connect($dsn, "sa", "natsoft", "test"));
		$dict->SetSchema('');
		$sqla2 = $dict->ChangeTableSQL('adoxyz',$flds);
		if ($sqla2) printsqla($dbType,$sqla2);
	}
	
	
	
	adodb_pr($dict->databaseType);
	printsqla($dbType, $dict->DropColumnSQL('table',array('my col','`col2_with_Quotes`','A_col3','col3(10)')));
	printsqla($dbType, $dict->ChangeTableSQL('adoxyz','LASTNAME varchar(32)'));
	
}

function printsqla($dbType,$sqla)
{
	print "<pre>";
	//print_r($dict->MetaTables());
	foreach($sqla as $s) {
		$s = htmlspecialchars($s);
		print "$s;\n";
		if ($dbType == 'oci8') print "/\n";
	}
	print "</pre><hr>";
}

/***

Generated SQL:

mysql

CREATE DATABASE KUTU;
DROP TABLE KUTU.testtable;
CREATE TABLE KUTU.testtable (
id               INTEGER NOT NULL AUTO_INCREMENT,
firstname        VARCHAR(30) DEFAULT 'Joan',
lastname         VARCHAR(28) NOT NULL DEFAULT 'Chen',
averylonglongfieldname LONGTEXT NOT NULL,
price            NUMERIC(7,2) NOT NULL DEFAULT 0.00,
MYDATE           DATE DEFAULT CURDATE(),
                 PRIMARY KEY (id, lastname)
)TYPE=ISAM;
CREATE FULLTEXT INDEX idx ON KUTU.testtable (firstname,lastname);
CREATE INDEX idx2 ON KUTU.testtable (price,lastname);
ALTER TABLE KUTU.testtable  ADD height           DOUBLE;
ALTER TABLE KUTU.testtable  ADD weight           DOUBLE;
ALTER TABLE KUTU.testtable  MODIFY COLUMN height           DOUBLE NOT NULL;
ALTER TABLE KUTU.testtable  MODIFY COLUMN weight           DOUBLE NOT NULL;


--------------------------------------------------------------------------------

oci8

CREATE USER KUTU IDENTIFIED BY tiger;
/
GRANT CREATE SESSION, CREATE TABLE,UNLIMITED TABLESPACE,CREATE SEQUENCE TO KUTU;
/
DROP TABLE KUTU.testtable CASCADE CONSTRAINTS;
/
CREATE TABLE KUTU.testtable (
id               NUMBER(16) NOT NULL,
firstname        VARCHAR(30) DEFAULT 'Joan',
lastname         VARCHAR(28) DEFAULT 'Chen' NOT NULL,
averylonglongfieldname CLOB NOT NULL,
price            NUMBER(7,2) DEFAULT 0.00 NOT NULL,
MYDATE           DATE DEFAULT TRUNC(SYSDATE),
                 PRIMARY KEY (id, lastname)
)TABLESPACE USERS;
/
DROP SEQUENCE KUTU.SEQ_testtable;
/
CREATE SEQUENCE KUTU.SEQ_testtable;
/
CREATE OR REPLACE TRIGGER KUTU.TRIG_SEQ_testtable BEFORE insert ON KUTU.testtable 
		FOR EACH ROW
		BEGIN
		  select KUTU.SEQ_testtable.nextval into :new.id from dual;
		END;
/
CREATE BITMAP INDEX idx ON KUTU.testtable (firstname,lastname);
/
CREATE INDEX idx2 ON KUTU.testtable (price,lastname);
/
ALTER TABLE testtable ADD (
 height           NUMBER,
 weight           NUMBER);
/
ALTER TABLE testtable MODIFY(
 height           NUMBER NOT NULL,
 weight           NUMBER NOT NULL);
/


--------------------------------------------------------------------------------

postgres
AlterColumnSQL not supported for PostgreSQL


CREATE DATABASE KUTU LOCATION='/u01/postdata';
DROP TABLE KUTU.testtable;
CREATE TABLE KUTU.testtable (
id               SERIAL,
firstname        VARCHAR(30) DEFAULT 'Joan',
lastname         VARCHAR(28) DEFAULT 'Chen' NOT NULL,
averylonglongfieldname TEXT NOT NULL,
price            NUMERIC(7,2) DEFAULT 0.00 NOT NULL,
MYDATE           DATE DEFAULT CURRENT_DATE,
                 PRIMARY KEY (id, lastname)
);
CREATE INDEX idx ON KUTU.testtable USING HASH (firstname,lastname);
CREATE INDEX idx2 ON KUTU.testtable (price,lastname);
ALTER TABLE KUTU.testtable  ADD height           FLOAT8;
ALTER TABLE KUTU.testtable  ADD weight           FLOAT8;


--------------------------------------------------------------------------------

odbc_mssql

CREATE DATABASE KUTU;
DROP TABLE KUTU.testtable;
CREATE TABLE KUTU.testtable (
id               INT IDENTITY(1,1) NOT NULL,
firstname        VARCHAR(30) DEFAULT 'Joan',
lastname         VARCHAR(28) DEFAULT 'Chen' NOT NULL,
averylonglongfieldname TEXT NOT NULL,
price            NUMERIC(7,2) DEFAULT 0.00 NOT NULL,
MYDATE           DATETIME DEFAULT GetDate(),
                 PRIMARY KEY (id, lastname)
);
CREATE CLUSTERED INDEX idx ON KUTU.testtable (firstname,lastname);
CREATE INDEX idx2 ON KUTU.testtable (price,lastname);
ALTER TABLE KUTU.testtable  ADD
 height           REAL,
 weight           REAL;
ALTER TABLE KUTU.testtable  ALTER COLUMN height           REAL NOT NULL;
ALTER TABLE KUTU.testtable  ALTER COLUMN weight           REAL NOT NULL;


--------------------------------------------------------------------------------
*/


echo "<h1>Test XML Schema</h1>";
$ff = file('xmlschema.xml');
echo "<pre>";
foreach($ff as $xml) echo htmlspecialchars($xml);
echo "</pre>";
include_once('test-xmlschema.php');
?>