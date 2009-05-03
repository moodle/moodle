<?php
/* 
V5.08 6 Apr 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
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

$db = NewADOConnection('odbc_db2');
$db->curMode = SQL_CUR_USE_ODBC;
$db->Connect($dsn, $userid, $pwd);



USING CLI INTERFACE
===================

I have had reports that the $host and $database params have to be reversed in 
Connect() when using the CLI interface. From Halmai Csongor csongor.halmai#nexum.hu:

> The symptom is that if I change the database engine from postgres or any other to DB2 then the following
> connection command becomes wrong despite being described this version to be correct in the docs. 
>
> $connection_object->Connect( $DATABASE_HOST, $DATABASE_AUTH_USER_NAME, $DATABASE_AUTH_PASSWORD, $DATABASE_NAME )
>
> In case of DB2 I had to swap the first and last arguments in order to connect properly. 


System Error 5
==============
IF you get a System Error 5 when trying to Connect/Load, it could be a permission problem. Give the user connecting
to DB2 full rights to the DB2 SQLLIB directory, and place the user in the DBUSERS group.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}
if (!defined('ADODB_ODBC_DB2')){
define('ADODB_ODBC_DB2',1);

class ADODB_ODBC_DB2 extends ADODB_odbc {
	var $databaseType = "db2";	
	var $concat_operator = '||';
	var $sysTime = 'CURRENT TIME';
	var $sysDate = 'CURRENT DATE';
	var $sysTimeStamp = 'CURRENT TIMESTAMP';
	// The complete string representation of a timestamp has the form 
	// yyyy-mm-dd-hh.mm.ss.nnnnnn.
	var $fmtTimeStamp = "'Y-m-d-H.i.s'";
	var $ansiOuter = true;
	var $identitySQL = 'values IDENTITY_VAL_LOCAL()';
	var $_bindInputArray = true;
	 var $hasInsertID = true;
	var $rsPrefix = 'ADORecordset_odbc_';
	
	function ADODB_DB2()
	{
		if (strncmp(PHP_OS,'WIN',3) === 0) $this->curmode = SQL_CUR_USE_ODBC;
		$this->ADODB_odbc();
	}
	
	function IfNull( $field, $ifNull ) 
	{
		return " COALESCE($field, $ifNull) "; // if DB2 UDB
	}
	
	function ServerInfo()
	{
		//odbc_setoption($this->_connectionID,1,101 /*SQL_ATTR_ACCESS_MODE*/, 1 /*SQL_MODE_READ_ONLY*/);
		$vers = $this->GetOne('select versionnumber from sysibm.sysversions');
		//odbc_setoption($this->_connectionID,1,101, 0 /*SQL_MODE_READ_WRITE*/);
		return array('description'=>'DB2 ODBC driver', 'version'=>$vers);
	}
	
	function _insertid()
	{
		return $this->GetOne($this->identitySQL);
	}
	
	function RowLock($tables,$where,$flds='1 as ignore')
	{
		if ($this->_autocommit) $this->BeginTrans();
		return $this->GetOne("select $flds from $tables where $where for update");
	}
	
	function MetaTables($ttype=false,$showSchema=false, $qtable="%", $qschema="%")
	{
	global $ADODB_FETCH_MODE;
	
		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$qid = odbc_tables($this->_connectionID, "", $qschema, $qtable, "");
		
		$rs = new ADORecordSet_odbc($qid);
		
		$ADODB_FETCH_MODE = $savem;
		if (!$rs) {
			$false = false;
			return $false;
		}
		$rs->_has_stupid_odbc_fetch_api_change = $this->_has_stupid_odbc_fetch_api_change;
		
		$arr = $rs->GetArray();
		//print_r($arr);
		
		$rs->Close();
		$arr2 = array();
		
		if ($ttype) {
			$isview = strncmp($ttype,'V',1) === 0;
		}
		for ($i=0; $i < sizeof($arr); $i++) {
		
			if (!$arr[$i][2]) continue;
			if (strncmp($arr[$i][1],'SYS',3) === 0) continue;
			
			$type = $arr[$i][3];
			
			if ($showSchema) $arr[$i][2] = $arr[$i][1].'.'.$arr[$i][2];
			
			if ($ttype) { 
				if ($isview) {
					if (strncmp($type,'V',1) === 0) $arr2[] = $arr[$i][2];
				} else if (strncmp($type,'T',1) === 0) $arr2[] = $arr[$i][2];
			} else if (strncmp($type,'S',1) !== 0) $arr2[] = $arr[$i][2];
		}
		return $arr2;
	}

	function MetaIndexes ($table, $primary = FALSE, $owner=false)
	{
        // save old fetch mode
        global $ADODB_FETCH_MODE;
        $save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
               $savem = $this->SetFetchMode(FALSE);
        }
		$false = false;
		// get index details
		$table = strtoupper($table);
		$SQL="SELECT NAME, UNIQUERULE, COLNAMES FROM SYSIBM.SYSINDEXES WHERE TBNAME='$table'";
        if ($primary) 
			$SQL.= " AND UNIQUERULE='P'";
		$rs = $this->Execute($SQL);
        if (!is_object($rs)) {
			if (isset($savem)) 
				$this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
            return $false;
        }
		$indexes = array ();
        // parse index data into array
        while ($row = $rs->FetchRow()) {
			$indexes[$row[0]] = array(
			   'unique' => ($row[1] == 'U' || $row[1] == 'P'),
			   'columns' => array()
			);
			$cols = ltrim($row[2],'+');
			$indexes[$row[0]]['columns'] = explode('+', $cols);
        }
		if (isset($savem)) { 
            $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
		}
        return $indexes;
	}
	
	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{	
	// use right() and replace() ?
		if (!$col) $col = $this->sysDate;
		$s = '';
		
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			if ($s) $s .= '||';
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= "char(year($col))";
				break;
			case 'M':
				$s .= "substr(monthname($col),1,3)";
				break;
			case 'm':
				$s .= "right(digits(month($col)),2)";
				break;
			case 'D':
			case 'd':
				$s .= "right(digits(day($col)),2)";
				break;
			case 'H':
			case 'h':
				if ($col != $this->sysDate) $s .= "right(digits(hour($col)),2)";	
				else $s .= "''";
				break;
			case 'i':
			case 'I':
				if ($col != $this->sysDate)
					$s .= "right(digits(minute($col)),2)";
					else $s .= "''";
				break;
			case 'S':
			case 's':
				if ($col != $this->sysDate)
					$s .= "right(digits(second($col)),2)";
				else $s .= "''";
				break;
			default:
				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				$s .= $this->qstr($ch);
			}
		}
		return $s;
	} 
 
	
	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputArr=false)
	{
		$nrows = (integer) $nrows;
		if ($offset <= 0) {
		// could also use " OPTIMIZE FOR $nrows ROWS "
			if ($nrows >= 0) $sql .=  " FETCH FIRST $nrows ROWS ONLY ";
			$rs = $this->Execute($sql,$inputArr);
		} else {
			if ($offset > 0 && $nrows < 0);
			else {
				$nrows += $offset;
				$sql .=  " FETCH FIRST $nrows ROWS ONLY ";
			}
			$rs = ADOConnection::SelectLimit($sql,-1,$offset,$inputArr);
		}
		
		return $rs;
	}
	
};
 

class  ADORecordSet_odbc_db2 extends ADORecordSet_odbc {	
	
	var $databaseType = "db2";		
	
	function ADORecordSet_db2($id,$mode=false)
	{
		$this->ADORecordSet_odbc($id,$mode);
	}

	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		
		switch (strtoupper($t)) {
		case 'VARCHAR':
		case 'CHAR':
		case 'CHARACTER':
		case 'C':
			if ($len <= $this->blobSize) return 'C';
		
		case 'LONGCHAR':
		case 'TEXT':
		case 'CLOB':
		case 'DBCLOB': // double-byte
		case 'X':
			return 'X';
		
		case 'BLOB':
		case 'GRAPHIC':
		case 'VARGRAPHIC':
			return 'B';
			
		case 'DATE':
		case 'D':
			return 'D';
		
		case 'TIME':
		case 'TIMESTAMP':
		case 'T':
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
		case 'I':
			return 'I';
			
		default: return 'N';
		}
	}
}

} //define
?>