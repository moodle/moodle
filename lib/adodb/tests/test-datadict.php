<?php
/*

  V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.

USAGE:

	// First create a normal connection
	$db->NewADOConnection('mysql');
	$db->Connect(...);
	
	// Then create a data dictionary object, using this connection
	$dict = NewDataDictionary($db);
	
	// We demonstrate creating tables and indexes
	$sqlarray = $dict->CreateTableSQL($tabname, $fldarray, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);
	
	$sqlarray = $dict->CreateIndexSQL($idxname, $tabname, $flds);
	$dict->ExecuteSQLArray($sqlarray);
	
FUNCTIONS:

	========
	function CreateDatabase($dbname, $optionsarray)
	
	
	========
	function CreateTableSQL($tabname, $fldarray, $taboptarray=false)
	
	RETURNS:		an array of strings, the sql to be executed, or false
	$tabname: 		name of table
	$fldarray:		array containing field info
	$taboptarray:	array containing table options
	
	The format of $fldarray is a 2-dimensional array, where each row in the 
	1st dimension represents one field. Each row has this format:
	
		array($fieldname, $type, [,$colsize] [,$otheroptions]*)
	
	The first 2 fields must be the field name and the field type. The field type
	can be a portable type codes or the actual type for that database. 
	Legal portable type codes include:

		C:  varchar
		X:  CLOB (character large object) or largest varchar size 
			if CLOB is not supported
		C2: Multibyte varchar
		X2: Multibyte CLOB
		
		B:  BLOB (binary large object)
		
		D:  Date (some databases do not support this, and we return a datetime type)
		T:  Datetime or Timestamp
		L:  Integer field suitable for storing booleans (0 or 1)
		I:  Integer (mapped to I4)
		I1: 1-byte integer
		I2: 2-byte integer
		I4: 4-byte integer
		I8: 8-byte integer
		F:  Floating point number
		N:  Numeric or decimal number
		
	The $colsize field represents the size of the field. If a decimal number is 
	used, then it is assumed that the number following the dot is the precision,
	so 6.2 means a number of size 6 digits and 2 decimal places. It is 
	recommended that the default for number types be represented as a string to 
	avoid any rounding errors.
	
	The $otheroptions include the following keywords (case-insensitive):

		AUTO			For autoincrement number. Emulated with triggers if not available.
						Sets NOTNULL also.
		AUTOINCREMENT	Same as auto.
		KEY				Primary key field. Sets NOTNULL also. Compound keys are supported.
		PRIMARY 		Same as KEY.
		DEFAULT			The default value. Character strings are auto-quoted unless
						the string begins and ends with spaces, eg ' SYSDATE '.
		NOTNULL			If field is not null.
		DEFDATE			Set default value to call function to get today's date.
		DEFTIMESTAMP	Set default to call function to get today's datetime.
		NOQUOTE			Prevents autoquoting of default string values.
		CONSTRAINTS		Additional constraints defined at the end of the field
						definition.
		
	Examples:
		array('COLNAME', 'DECIMAL', '8.4', 'DEFAULT' => 0, 'NotNull')
		array('ID',      'I'      , 'AUTO')
		array('MYDATE',  'D'      , 'DEFDATE')
		array('NAME',    'C'      ,'32', 
			  'CONSTRAINTS' => 'FOREIGN KEY REFERENCES reftable')
			  
	The $taboptarray is the 3rd parameter of the CreateTableSQL function. 
	This contains table specific settings. Legal keywords include
	
		REPLACE			Indicates that the previous table definition should be removed
						together with ALL data.
		CONSTRAINTS		Additional constraints defined for the whole table. You will
						probably need to prefix this with a comma.
		
	Database specific table options can be defined also using the name of the
	database type as the array key. For example:
	
	$taboptarray = array('mysql' => 'TYPE=ISAM', 'oci8' => 'tablespace users');
	
	You can also define foreignkey constraints. The following is syntax for 
	postgresql:
	
	$taboptarray = array('constraints' => 
					', FOREIGN KEY (col1) REFERENCES reftable (refcol)');

	
	========
	function CreateIndexSQL($idxname, $tabname, $flds, $idxoptarray=false)
	
	RETURNS:		an array of strings, the sql to be executed, or false
	$idxname:		name of index
	$tabname: 		name of table
	$fldarray:		list of fields as a comma delimited string or an array of strings
	$idxoptarray:	array of index creation options
	
	$idxoptarray is similar to $taboptarray in that index specific information can
	be embedded in the array. Other options include:
	
		CLUSTERED		Create clustered index (only mssql)
		BITMAP			Create bitmap index (only oci8)
		UNIQUE			Make unique index
		FULLTEXT		Make fulltext index (only mysql)
		HASH			Create hash index (only postgres)
		
	========	
	function AddColumnSQL($tabname, $flds)
	
	========
	function AlterColumnSQL($tabname, $flds)
	
	========
	function DropColumnSQL($tabname, $flds)
	
	========
	function ExecuteSQLArray($sqlarray, $contOnError = true)
	
	RETURNS:		0 if failed, 1 if executed all but with errors, 2 if executed successfully
	$sqlarray:		an array of strings with sql code (no semicolon at the end of string)
	$contOnError:	if true, then continue executing even if error occurs
*/

error_reporting(E_ALL);
include_once('../adodb.inc.php');

foreach(array('mysql','oci8','postgres','odbc_mssql') as $dbType) {
	echo "<h3>$dbType</h3><p>";
	$db = NewADOConnection($dbType);
	$dict = NewDataDictionary($db);

	if (!$dict) continue;
	$dict->debug = 1;
	
	$opts = array('REPLACE','mysql' => 'TYPE=ISAM', 'oci8' => 'TABLESPACE USERS');
	$flds = array(
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
	);
	
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
?>