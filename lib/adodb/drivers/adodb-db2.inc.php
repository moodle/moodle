<?php
/* 
V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  DB2 data driver. Requires ODBC.
 
From phpdb list:

Hi Andrew,

thanks a lot for your help. Today we discovered what
our real problem was:

After "playing" a little bit with the php-scripts that try
to connect to the IBM DB2, we set the optional parameter
Cursortype when calling odbc_pconnect(....).

And the exciting thing: When we set the cursor type
to SQL_CUR_USE_ODBC Cursor Type, then
the whole query speed up from 1 till 10 seconds
to 0.2 till 0.3 seconds for 100 records. Amazing!!!

Therfore, PHP is just almost fast as calling the DB2
from Servlets using JDBC (don't take too much care
about the speed at whole: the database was on a
completely other location, so the whole connection
was made over a slow network connection).

I hope this helps when other encounter the same
problem when trying to connect to DB2 from
PHP.

Kind regards,
Christian Szardenings

2 Oct 2001
Mark Newnham has discovered that the SQL_CUR_USE_ODBC is not supported by 
IBM's DB2 ODBC driver, so this must be a 3rd party ODBC driver.

From the IBM CLI Reference:

SQL_ATTR_ODBC_CURSORS (DB2 CLI v5) 
This connection attribute is defined by ODBC, but is not supported by DB2
CLI. Any attempt to set or get this attribute will result in an SQLSTATE of
HYC00 (Driver not capable). 

A 32-bit option specifying how the Driver Manager uses the ODBC cursor
library. 

So I guess this means the message [above] was related to using a 3rd party
odbc driver.

Setting SQL_CUR_USE_ODBC
========================
To set SQL_CUR_USE_ODBC for drivers that require it, do this:

$db = NewADOConnection('db2');
$db->curMode = SQL_CUR_USE_ODBC;
$db->Connect($dsn, $userid, $pwd);

*/

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}
if (!defined('ADODB_DB2')){
define('ADODB_DB2',1);

class ADODB_DB2 extends ADODB_odbc {
	var $databaseType = "db2";	
	var $concat_operator = 'CONCAT';
	var $sysDate = 'CURRENT DATE';
	var $sysTimeStamp = 'CURRENT TIMESTAMP';
	var $ansiOuter = true;
	//var $curmode = SQL_CUR_USE_ODBC;
	
	function ADODB_DB2()
	{
		$this->ADODB_odbc();
	}
	
	function RowLock($tables,$where)
	{
		if ($this->_autocommit) $this->BeginTrans();
		return $this->GetOne("select 1 as ignore from $tables where $where for update");
	}
	
	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{	
	// use right() and replace() ?
		if (!$col) $col = $this->sysDate;
		$s = '';
		
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			if ($s) $s .= '+';
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= "char(year($col))";
				break;
			case 'M':
			case 'm':
				$s .= "right(digits(month($col)),2)";
				break;
			case 'D':
			case 'd':
				$s .= "right(digits(day($col)),2)";
				break;
			default:
				$s .= $this->qstr($ch);
			}
		}
		return $s;
	} 
 
	
	function &SelectLimit($sql,$nrows=-1,$offset=-1,$arg3=false)
	{
		if ($offset <= 0) {
		// could also use " OPTIMIZE FOR $nrows ROWS "
			$sql .=  " FETCH FIRST $nrows ROWS ONLY ";
			return $this->Execute($sql,false,$arg3);
		} else {
			$nrows += $offset;
			$sql .=  " FETCH FIRST $nrows ROWS ONLY ";
			return ADOConnection::SelectLimit($sql,-1,$offset,$arg3);
		}
	}
	
};
 

class  ADORecordSet_db2 extends ADORecordSet_odbc {	
	
	var $databaseType = "db2";		
	
	function ADORecordSet_db2($id,$mode=false)
	{
		$this->ADORecordSet_odbc($id,$mode);
	}

	function MetaType($t,$len=-1,$fieldobj=false)
	{
		switch (strtoupper($t)) {
		case 'VARCHAR':
		case 'CHAR':
		case 'CHARACTER':
			if ($len <= $this->blobSize) return 'C';
		
		case 'LONGCHAR':
		case 'TEXT':
		case 'CLOB':
		case 'DBCLOB': // double-byte
			return 'X';
		
		case 'BLOB':
		case 'GRAPHIC':
		case 'VARGRAPHIC':
			return 'B';
			
		case 'DATE':
			return 'D';
		
		case 'TIME':
		case 'TIMESTAMP':
			return 'T';
		
		//case 'BOOLEAN': 
		//case 'BIT':
		//	return 'L';
			
		//case 'COUNTER':
		//	return 'R';
			
		case 'INT':
		case 'INTEGER':
		case 'BIGINT':
		case 'SMALLINT':
			return 'I';
			
		default: return 'N';
		}
	}
}

} //define
?>