<?php
/**
 * Base ODBC driver
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

  define("_ADODB_ODBC_LAYER", 2 );

/*
 * These constants are used to set define MetaColumns() method's behavior.
 * - METACOLUMNS_RETURNS_ACTUAL makes the driver return the actual type, 
 *   like all other drivers do (default)
 * - METACOLUMNS_RETURNS_META is provided for legacy compatibility (makes
 *   driver behave as it did prior to v5.21)
 *
 * @see $metaColumnsReturnType
 */
DEFINE('METACOLUMNS_RETURNS_ACTUAL', 0);
DEFINE('METACOLUMNS_RETURNS_META', 1);
	
/*--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------*/


class ADODB_odbc extends ADOConnection {
	var $databaseType = "odbc";
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d, h:i:sA'";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $dataProvider = "odbc";
	var $hasAffectedRows = true;
	var $binmode = ODBC_BINMODE_RETURN;
	var $useFetchArray = false; // setting this to true will make array elements in FETCH_ASSOC mode case-sensitive
								// breaking backward-compat
	//var $longreadlen = 8000; // default number of chars to return for a Blob/Long field
	var $_bindInputArray = false;
	var $curmode = SQL_CUR_USE_DRIVER; // See sqlext.h, SQL_CUR_DEFAULT == SQL_CUR_USE_DRIVER == 2L
	var $_genSeqSQL = "create table %s (id integer)";
	var $_autocommit = true;
	var $_lastAffectedRows = 0;
	var $uCaseTables = true; // for meta* functions, uppercase table names
	
	/*
	 * Tells the metaColumns feature whether to return actual or meta type
	 */
	public $metaColumnsReturnType = METACOLUMNS_RETURNS_ACTUAL;

	function __construct() {}

		// returns true or false
	function _connect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('odbc_connect')) return null;

		if (!empty($argDatabasename) && stristr($argDSN, 'Database=') === false) {
			$argDSN = trim($argDSN);
			$endDSN = substr($argDSN, strlen($argDSN) - 1);
			if ($endDSN != ';') $argDSN .= ';';
			$argDSN .= 'Database='.$argDatabasename;
		}

		$last_php_error = $this->resetLastError();
		if ($this->curmode === false) $this->_connectionID = odbc_connect($argDSN,$argUsername,$argPassword);
		else $this->_connectionID = odbc_connect($argDSN,$argUsername,$argPassword,$this->curmode);
		$this->_errorMsg = $this->getChangedErrorMsg($last_php_error);
		if (isset($this->connectStmt)) $this->Execute($this->connectStmt);

		return $this->_connectionID != false;
	}

	// returns true or false
	function _pconnect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('odbc_connect')) return null;

		$last_php_error = $this->resetLastError();
		$this->_errorMsg = '';
		if ($this->debug && $argDatabasename) {
			ADOConnection::outp("For odbc PConnect(), $argDatabasename is not used. Place dsn in 1st parameter.");
		}
	//	print "dsn=$argDSN u=$argUsername p=$argPassword<br>"; flush();
		if ($this->curmode === false) $this->_connectionID = odbc_connect($argDSN,$argUsername,$argPassword);
		else $this->_connectionID = odbc_pconnect($argDSN,$argUsername,$argPassword,$this->curmode);

		$this->_errorMsg = $this->getChangedErrorMsg($last_php_error);
		if ($this->_connectionID && $this->autoRollback) @odbc_rollback($this->_connectionID);
		if (isset($this->connectStmt)) $this->Execute($this->connectStmt);

		return $this->_connectionID != false;
	}


	function ServerInfo()
	{

		if (!empty($this->host)) {
			$dsn = strtoupper($this->host);
			$first = true;
			$found = false;

			if (!function_exists('odbc_data_source')) return false;

			while(true) {

				$rez = @odbc_data_source($this->_connectionID,
					$first ? SQL_FETCH_FIRST : SQL_FETCH_NEXT);
				$first = false;
				if (!is_array($rez)) break;
				if (strtoupper($rez['server']) == $dsn) {
					$found = true;
					break;
				}
			}
			if (!$found) return ADOConnection::ServerInfo();
			if (!isset($rez['version'])) $rez['version'] = '';
			return $rez;
		} else {
			return ADOConnection::ServerInfo();
		}
	}


	function CreateSequence($seqname='adodbseq',$start=1)
	{
		if (empty($this->_genSeqSQL)) return false;
		$ok = $this->Execute(sprintf($this->_genSeqSQL,$seqname));
		if (!$ok) return false;
		$start -= 1;
		return $this->Execute("insert into $seqname values($start)");
	}

	var $_dropSeqSQL = 'drop table %s';
	function DropSequence($seqname = 'adodbseq')
	{
		if (empty($this->_dropSeqSQL)) return false;
		return $this->Execute(sprintf($this->_dropSeqSQL,$seqname));
	}

	/*
		This algorithm is not very efficient, but works even if table locking
		is not available.

		Will return false if unable to generate an ID after $MAXLOOPS attempts.
	*/
	function GenID($seq='adodbseq',$start=1)
	{
		// if you have to modify the parameter below, your database is overloaded,
		// or you need to implement generation of id's yourself!
		$MAXLOOPS = 100;
		//$this->debug=1;
		while (--$MAXLOOPS>=0) {
			$num = $this->GetOne("select id from $seq");
			if ($num === false) {
				$this->Execute(sprintf($this->_genSeqSQL ,$seq));
				$start -= 1;
				$num = '0';
				$ok = $this->Execute("insert into $seq values($start)");
				if (!$ok) return false;
			}
			$this->Execute("update $seq set id=id+1 where id=$num");

			if ($this->affected_rows() > 0) {
				$num += 1;
				$this->genID = $num;
				return $num;
			} elseif ($this->affected_rows() == 0) {
				// some drivers do not return a valid value => try with another method
				$value = $this->GetOne("select id from $seq");
				if ($value == $num + 1) {
					return $value;
				}
			}
		}
		if ($fn = $this->raiseErrorFn) {
			$fn($this->databaseType,'GENID',-32000,"Unable to generate unique id after $MAXLOOPS attempts",$seq,$num);
		}
		return false;
	}


	function ErrorMsg()
	{
		if ($this->_errorMsg !== false) return $this->_errorMsg;
		if (empty($this->_connectionID)) return @odbc_errormsg();
		return @odbc_errormsg($this->_connectionID);
	}

	function ErrorNo()
	{
		if ($this->_errorCode !== false) {
			// bug in 4.0.6, error number can be corrupted string (should be 6 digits)
			return (strlen($this->_errorCode)<=2) ? 0 : $this->_errorCode;
		}

		if (empty($this->_connectionID)) $e = @odbc_error();
		else $e = @odbc_error($this->_connectionID);

		 // bug in 4.0.6, error number can be corrupted string (should be 6 digits)
		 // so we check and patch
		if (strlen($e)<=2) return 0;
		return $e;
	}



	function BeginTrans()
	{
		if (!$this->hasTransactions) return false;
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->_autocommit = false;
		return odbc_autocommit($this->_connectionID,false);
	}

	function CommitTrans($ok=true)
	{
		if ($this->transOff) return true;
		if (!$ok) return $this->RollbackTrans();
		if ($this->transCnt) $this->transCnt -= 1;
		$this->_autocommit = true;
		$ret = odbc_commit($this->_connectionID);
		odbc_autocommit($this->_connectionID,true);
		return $ret;
	}

	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->_autocommit = true;
		$ret = odbc_rollback($this->_connectionID);
		odbc_autocommit($this->_connectionID,true);
		return $ret;
	}

	function MetaPrimaryKeys($table,$owner=false)
	{
	global $ADODB_FETCH_MODE;

		if ($this->uCaseTables) $table = strtoupper($table);
		$schema = '';
		$this->_findschema($table,$schema);

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$qid = @odbc_primarykeys($this->_connectionID,'',$schema,$table);

		if (!$qid) {
			$ADODB_FETCH_MODE = $savem;
			return false;
		}
		$rs = new ADORecordSet_odbc($qid);
		$ADODB_FETCH_MODE = $savem;

		if (!$rs) return false;

		$arr = $rs->GetArray();
		$rs->Close();
		//print_r($arr);
		$arr2 = array();
		for ($i=0; $i < sizeof($arr); $i++) {
			if ($arr[$i][3]) $arr2[] = $arr[$i][3];
		}
		return $arr2;
	}



	function MetaTables($ttype=false,$showSchema=false,$mask=false)
	{
	global $ADODB_FETCH_MODE;

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$qid = odbc_tables($this->_connectionID);

		$rs = new ADORecordSet_odbc($qid);

		$ADODB_FETCH_MODE = $savem;
		if (!$rs) {
			$false = false;
			return $false;
		}

		$arr = $rs->GetArray();
		//print_r($arr);

		$rs->Close();
		$arr2 = array();

		if ($ttype) {
			$isview = strncmp($ttype,'V',1) === 0;
		}
		for ($i=0; $i < sizeof($arr); $i++) {
			if (!$arr[$i][2]) continue;
			$type = $arr[$i][3];
			if ($ttype) {
				if ($isview) {
					if (strncmp($type,'V',1) === 0) $arr2[] = $arr[$i][2];
				} else if (strncmp($type,'SYS',3) !== 0) $arr2[] = $arr[$i][2];
			} else if (strncmp($type,'SYS',3) !== 0) $arr2[] = $arr[$i][2];
		}
		return $arr2;
	}

/*
See http://msdn.microsoft.com/library/default.asp?url=/library/en-us/odbc/htm/odbcdatetime_data_type_changes.asp
/ SQL data type codes /
#define	SQL_UNKNOWN_TYPE	0
#define SQL_CHAR			1
#define SQL_NUMERIC		 2
#define SQL_DECIMAL		 3
#define SQL_INTEGER		 4
#define SQL_SMALLINT		5
#define SQL_FLOAT		   6
#define SQL_REAL			7
#define SQL_DOUBLE		  8
#if (ODBCVER >= 0x0300)
#define SQL_DATETIME		9
#endif
#define SQL_VARCHAR		12


/ One-parameter shortcuts for date/time data types /
#if (ODBCVER >= 0x0300)
#define SQL_TYPE_DATE	  91
#define SQL_TYPE_TIME	  92
#define SQL_TYPE_TIMESTAMP 93

#define SQL_UNICODE                             (-95)
#define SQL_UNICODE_VARCHAR                     (-96)
#define SQL_UNICODE_LONGVARCHAR                 (-97)
*/
	function ODBCTypes($t)
	{
		switch ((integer)$t) {
		case 1:
		case 12:
		case 0:
		case -95:
		case -96:
			return 'C';
		case -97:
		case -1: //text
			return 'X';
		case -4: //image
			return 'B';

		case 9:
		case 91:
			return 'D';

		case 10:
		case 11:
		case 92:
		case 93:
			return 'T';

		case 4:
		case 5:
		case -6:
			return 'I';

		case -11: // uniqidentifier
			return 'R';
		case -7: //bit
			return 'L';

		default:
			return 'N';
		}
	}

	function MetaColumns($table, $normalize=true)
	{
	global $ADODB_FETCH_MODE;

		$false = false;
		if ($this->uCaseTables) $table = strtoupper($table);
		$schema = '';
		$this->_findschema($table,$schema);

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

		/*if (false) { // after testing, confirmed that the following does not work because of a bug
			$qid2 = odbc_tables($this->_connectionID);
			$rs = new ADORecordSet_odbc($qid2);
			$ADODB_FETCH_MODE = $savem;
			if (!$rs) return false;
			$rs->_fetch();

			while (!$rs->EOF) {
				if ($table == strtoupper($rs->fields[2])) {
					$q = $rs->fields[0];
					$o = $rs->fields[1];
					break;
				}
				$rs->MoveNext();
			}
			$rs->Close();

			$qid = odbc_columns($this->_connectionID,$q,$o,strtoupper($table),'%');
		} */

		switch ($this->databaseType) {
		case 'access':
		case 'vfp':
			$qid = odbc_columns($this->_connectionID);#,'%','',strtoupper($table),'%');
			break;


		case 'db2':
            $colname = "%";
            $qid = odbc_columns($this->_connectionID, "", $schema, $table, $colname);
            break;

		default:
			$qid = @odbc_columns($this->_connectionID,'%','%',strtoupper($table),'%');
			if (empty($qid)) $qid = odbc_columns($this->_connectionID);
			break;
		}
		if (empty($qid)) return $false;

		$rs = new ADORecordSet_odbc($qid);
		$ADODB_FETCH_MODE = $savem;

		if (!$rs) return $false;
		$rs->_fetch();

		$retarr = array();

		/*
		$rs->fields indices
		0 TABLE_QUALIFIER
		1 TABLE_SCHEM
		2 TABLE_NAME
		3 COLUMN_NAME
		4 DATA_TYPE
		5 TYPE_NAME
		6 PRECISION
		7 LENGTH
		8 SCALE
		9 RADIX
		10 NULLABLE
		11 REMARKS
		*/
		while (!$rs->EOF) {
		//	adodb_pr($rs->fields);
			if (strtoupper(trim($rs->fields[2])) == $table && (!$schema || strtoupper($rs->fields[1]) == $schema)) {
				$fld = new ADOFieldObject();
				$fld->name = $rs->fields[3];
				if ($this->metaColumnsReturnType == METACOLUMNS_RETURNS_META)
					/* 
				    * This is the broken, original value
					*/
					$fld->type = $this->ODBCTypes($rs->fields[4]);
				else
					/*
				    * This is the correct new value
					*/
				    $fld->type = $rs->fields[4];

				// ref: http://msdn.microsoft.com/library/default.asp?url=/archive/en-us/dnaraccgen/html/msdn_odk.asp
				// access uses precision to store length for char/varchar
				if ($fld->type == 'C' or $fld->type == 'X') {
					if ($this->databaseType == 'access')
						$fld->max_length = $rs->fields[6];
					else if ($rs->fields[4] <= -95) // UNICODE
						$fld->max_length = $rs->fields[7]/2;
					else
						$fld->max_length = $rs->fields[7];
				} else
					$fld->max_length = $rs->fields[7];
				$fld->not_null = !empty($rs->fields[10]);
				$fld->scale = $rs->fields[8];
				$retarr[strtoupper($fld->name)] = $fld;
			} else if (sizeof($retarr)>0)
				break;
			$rs->MoveNext();
		}
		$rs->Close(); //-- crashes 4.03pl1 -- why?

		if (empty($retarr)) $retarr = false;
		return $retarr;
	}

	function Prepare($sql)
	{
		if (! $this->_bindInputArray) return $sql; // no binding
		$stmt = odbc_prepare($this->_connectionID,$sql);
		if (!$stmt) {
			// we don't know whether odbc driver is parsing prepared stmts, so just return sql
			return $sql;
		}
		return array($sql,$stmt,false);
	}

	/* returns queryID or false */
	function _query($sql,$inputarr=false)
	{
		$last_php_error = $this->resetLastError();
		$this->_errorMsg = '';

		if ($inputarr) {
			if (is_array($sql)) {
				$stmtid = $sql[1];
			} else {
				$stmtid = odbc_prepare($this->_connectionID,$sql);

				if ($stmtid == false) {
					$this->_errorMsg = $this->getChangedErrorMsg($last_php_error);
					return false;
				}
			}

			if (! odbc_execute($stmtid,$inputarr)) {
				//@odbc_free_result($stmtid);
				$this->_errorMsg = odbc_errormsg();
				$this->_errorCode = odbc_error();
				return false;
			}

		} else if (is_array($sql)) {
			$stmtid = $sql[1];
			if (!odbc_execute($stmtid)) {
				//@odbc_free_result($stmtid);
				$this->_errorMsg = odbc_errormsg();
				$this->_errorCode = odbc_error();
				return false;
			}
		} else
			$stmtid = odbc_exec($this->_connectionID,$sql);

		$this->_lastAffectedRows = 0;
		if ($stmtid) {
			if (@odbc_num_fields($stmtid) == 0) {
				$this->_lastAffectedRows = odbc_num_rows($stmtid);
				$stmtid = true;
			} else {
				$this->_lastAffectedRows = 0;
				odbc_binmode($stmtid,$this->binmode);
				odbc_longreadlen($stmtid,$this->maxblobsize);
			}

			$this->_errorMsg = '';
			$this->_errorCode = 0;
		} else {
			$this->_errorMsg = odbc_errormsg();
			$this->_errorCode = odbc_error();
		}
		return $stmtid;
	}

	/*
		Insert a null into the blob field of the table first.
		Then use UpdateBlob to store the blob.

		Usage:

		$conn->Execute('INSERT INTO blobtable (id, blobcol) VALUES (1, null)');
		$conn->UpdateBlob('blobtable','blobcol',$blob,'id=1');
	*/
	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{
		return $this->Execute("UPDATE $table SET $column=? WHERE $where",array($val)) != false;
	}

	// returns true or false
	function _close()
	{
		$ret = @odbc_close($this->_connectionID);
		$this->_connectionID = false;
		return $ret;
	}

	function _affectedrows()
	{
		return $this->_lastAffectedRows;
	}

}

/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_odbc extends ADORecordSet {

	var $bind = false;
	var $databaseType = "odbc";
	var $dataProvider = "odbc";
	var $useFetchArray;

	function __construct($id,$mode=false)
	{
		if ($mode === false) {
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		$this->fetchMode = $mode;

		$this->_queryID = $id;

		// the following is required for mysql odbc driver in 4.3.1 -- why?
		$this->EOF = false;
		$this->_currentRow = -1;
		//parent::__construct($id);
	}


	// returns the field object
	function FetchField($fieldOffset = -1)
	{

		$off=$fieldOffset+1; // offsets begin at 1

		$o= new ADOFieldObject();
		$o->name = @odbc_field_name($this->_queryID,$off);
		$o->type = @odbc_field_type($this->_queryID,$off);
		$o->max_length = @odbc_field_len($this->_queryID,$off);
		if (ADODB_ASSOC_CASE == 0) $o->name = strtolower($o->name);
		else if (ADODB_ASSOC_CASE == 1) $o->name = strtoupper($o->name);
		return $o;
	}

	/* Use associative array to get fields array */
	function Fields($colname)
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) return $this->fields[$colname];
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}

		 return $this->fields[$this->bind[strtoupper($colname)]];
	}


	function _initrs()
	{
	global $ADODB_COUNTRECS;
		$this->_numOfRows = ($ADODB_COUNTRECS) ? @odbc_num_rows($this->_queryID) : -1;
		$this->_numOfFields = @odbc_num_fields($this->_queryID);
		// some silly drivers such as db2 as/400 and intersystems cache return _numOfRows = 0
		if ($this->_numOfRows == 0) $this->_numOfRows = -1;
		//$this->useFetchArray = $this->connection->useFetchArray;
	}

	function _seek($row)
	{
		return false;
	}

	// speed up SelectLimit() by switching to ADODB_FETCH_NUM as ADODB_FETCH_ASSOC is emulated
	function GetArrayLimit($nrows,$offset=-1)
	{
		if ($offset <= 0) {
			$rs = $this->GetArray($nrows);
			return $rs;
		}
		$savem = $this->fetchMode;
		$this->fetchMode = ADODB_FETCH_NUM;
		$this->Move($offset);
		$this->fetchMode = $savem;

		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			$this->fields = $this->GetRowAssoc();
		}

		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nrows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}

		return $results;
	}


	function MoveNext()
	{
		if ($this->_numOfRows != 0 && !$this->EOF) {
			$this->_currentRow++;
			if( $this->_fetch() ) {
				return true;
			}
		}
		$this->fields = false;
		$this->EOF = true;
		return false;
	}

	function _fetch()
	{
		$this->fields = false;
		$rez = @odbc_fetch_into($this->_queryID,$this->fields);
		if ($rez) {
			if ($this->fetchMode & ADODB_FETCH_ASSOC) {
				$this->fields = $this->GetRowAssoc();
			}
			return true;
		}
		return false;
	}

	function _close()
	{
		return @odbc_free_result($this->_queryID);
	}

}
