<?php
/* 
V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  Requires ODBC. Works on Windows and Unix.
*/
  define("_ADODB_ODBC_LAYER", 1 );
	 
/*--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------*/

  
class ADODB_odbc extends ADOConnection {
	var $databaseType = "odbc";	
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d, h:i:sA'";
	var $replaceQuote = "''"; /*  string to use to replace quotes */
	var $dataProvider = "odbc";
	var $hasAffectedRows = true;
	var $binmode = ODBC_BINMODE_RETURN;
	/* var $longreadlen = 8000; // default number of chars to return for a Blob/Long field */
	var $_bindInputArray = false;    
	var $curmode = SQL_CUR_USE_DRIVER; /*  See sqlext.h, SQL_CUR_DEFAULT == SQL_CUR_USE_DRIVER == 2L */
	var $_genSeqSQL = "create table %s (id integer)";
	var $_autocommit = true;
	var $_haserrorfunctions = true;
	var $_has_stupid_odbc_fetch_api_change = true;
	
	function ADODB_odbc() 
	{ 	
		$this->_haserrorfunctions = (strnatcmp(PHP_VERSION,'4.0.5')>=0);
		$this->_has_stupid_odbc_fetch_api_change = (strnatcmp(PHP_VERSION,'4.2.0')>=0);
	}

	function ErrorMsg()
	{
		if ($this->_haserrorfunctions) {
			if (empty($this->_connectionID)) return @odbc_errormsg();
			return @odbc_errormsg($this->_connectionID);
		} else return ADOConnection::ErrorMsg();
	}
	
	/*
		This algorithm is not very efficient, but works even if table locking
		is not available.
		
		Will return false if unable to generate an ID after $MAXLOOPS attempts.
	*/
	function GenID($seq='adodbseq',$start=1)
	{	
		/*  if you have to modify the parameter below, your database is overloaded, */
		/*  or you need to implement generation of id's yourself! */
		$MAXLOOPS = 100;
		
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
			
			if ($this->affected_rows() == 1) {
				$num += 1;
				$this->genID = $num;
				return $num;
			}
		}
		if ($fn = $this->raiseErrorFn) {
			$fn($this->databaseType,'GENID',-32000,"Unable to generate unique id after $MAXLOOP attempts",$seq,$num);
		}
		return false;
	}
	
	function ErrorNo()
	{
		if ($this->_haserrorfunctions) {
			if (empty($this->_connectionID)) $e = @odbc_error(); 
			else $e = @odbc_error($this->_connectionID);
			
			 /*  bug in 4.0.6, error number can be corrupted string (should be 6 digits) */
			 /*  so we check and patch */
			if (strlen($e)<=2) return 0;
			return $e;
		} else return ADOConnection::ErrorNo();
	}
	
	
	/*  returns true or false */
	function _connect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
	global $php_errormsg;
	
		$php_errormsg = '';
		$this->_connectionID = odbc_connect($argDSN,$argUsername,$argPassword,$this->curmode);
		$this->_errorMsg = $php_errormsg;

		/* if ($this->_connectionID) odbc_autocommit($this->_connectionID,true); */
		return $this->_connectionID != false;
	}
	
	/*  returns true or false */
	function _pconnect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
	global $php_errormsg;
		$php_errormsg = '';
		$this->_connectionID = odbc_pconnect($argDSN,$argUsername,$argPassword,$this->curmode);
		$this->_errorMsg = $php_errormsg;
		
		/* if ($this->_connectionID) odbc_autocommit($this->_connectionID,true); */
		return $this->_connectionID != false;
	}

	function BeginTrans()
	{      
		$this->_autocommit = false;
        return odbc_autocommit($this->_connectionID,false);
	}
	
	function CommitTrans($ok=true) 
	{ 
		if (!$ok) return $this->RollbackTrans();
		$this->_autocommit = true;
        $ret = odbc_commit($this->_connectionID);
		odbc_autocommit($this->_connectionID,true);
		return $ret;
	}
	
	function RollbackTrans()
	{
		$this->_autocommit = true;
        $ret = odbc_rollback($this->_connectionID);
		odbc_autocommit($this->_connectionID,true);
		return $ret;
	}
	
	function MetaTables()
	{
	global $ADODB_FETCH_MODE;
	
		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$qid = odbc_tables($this->_connectionID);
		$ADODB_FETCH_MODE = $savem;
		
		$rs = new ADORecordSet_odbc($qid);
		if (!$rs) return false;
		
		$rs->_has_stupid_odbc_fetch_api_change = $this->_has_stupid_odbc_fetch_api_change;
		
		/* print_r($rs); */
		$arr = $rs->GetArray();
		$rs->Close();
		$arr2 = array();
		for ($i=0; $i < sizeof($arr); $i++) {
			if ($arr[$i][2]) $arr2[] = $arr[$i][2];
		}
		return $arr2;
	}
	
/*
/ SQL data type codes /
#define	SQL_UNKNOWN_TYPE	0
#define SQL_CHAR            1
#define SQL_NUMERIC         2
#define SQL_DECIMAL         3
#define SQL_INTEGER         4
#define SQL_SMALLINT        5
#define SQL_FLOAT           6
#define SQL_REAL            7
#define SQL_DOUBLE          8
#if (ODBCVER >= 0x0300)
#define SQL_DATETIME        9
#endif
#define SQL_VARCHAR        12

/ One-parameter shortcuts for date/time data types /
#if (ODBCVER >= 0x0300)
#define SQL_TYPE_DATE      91
#define SQL_TYPE_TIME      92
#define SQL_TYPE_TIMESTAMP 93
*/
	function ODBCTypes($t)
	{
		switch ((integer)$t) {
		case 1:	
		case 12:
		case 0:
			return 'C';
		case -1: /* text */
			return 'X';
		case -4: /* image */
			return 'B';
				
		case 91:
		case 11:
			return 'D';
			
		case 92:
		case 93:
		case 9:	return 'T';
		case 4:
		case 5:
		case -6:
			return 'I';
			
		case -11: /*  uniqidentifier */
			return 'R';
		case -7: /* bit */
			return 'L';
		
		default:
			return 'N';
		}
	}
	
	function MetaColumns($table)
	{
	global $ADODB_FETCH_MODE;
	
		$table = strtoupper($table);
		
	/* // for some reason, cannot view only 1 table with odbc_columns -- bug?
		$qid = odbc_tables($this->_connectionID);
		$rs = new ADORecordSet_odbc($qid);
		if (!$rs) return false;
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
	*/
		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$qid = odbc_columns($this->_connectionID);
		$rs = new ADORecordSet_odbc($qid);
		$ADODB_FETCH_MODE = $savem;
		
		if (!$rs) return false;
		
		$rs->_has_stupid_odbc_fetch_api_change = $this->_has_stupid_odbc_fetch_api_change;
		
		$retarr = array();
		while (!$rs->EOF) {
			if (strtoupper($rs->fields[2]) == $table) {
				$fld = new ADOFieldObject();
				$fld->name = $rs->fields[3];
				$fld->type = $this->ODBCTypes($rs->fields[4]);
				$fld->max_length = $rs->fields[7];
				$retarr[strtoupper($fld->name)] = $fld;	
			} else if (sizeof($retarr)>0)
				break;
			$rs->MoveNext();
		}
		$rs->Close(); /* -- crashes 4.03pl1 -- why? */
		
		return $retarr;
	}
	
	function Prepare($sql)
	{
		if (! $this->_bindInputArray) return $sql; /*  no binding */
		$stmt = odbc_prepare($this->_connectionID,$sql);
		if (!$stmt) {
		/* 	print "Prepare Error for ($sql) ".$this->ErrorMsg()."<br>"; */
			return $sql;
		}
		return array($sql,$stmt,false);
	}

	/* returns queryID or false */
	function _query($sql,$inputarr=false) 
	{
	GLOBAL $php_errormsg;
		$php_errormsg = '';
		$this->_error = '';
		
		if ($inputarr) {
			if (is_array($sql)) $stmtid = $sql[1];
			else $stmtid = odbc_prepare($this->_connectionID,$sql);
		
			if ($stmtid == false) {
				$this->_errorMsg = $php_errormsg;
				return false;
			}
			if (! odbc_execute($stmtid,$inputarr)) {
				@odbc_free_result($stmtid);
				return false;
			}
			
		} else if (is_array($sql)) {
			$stmtid = $sql[1];
			if (!odbc_execute($stmtid)) {
				@odbc_free_result($stmtid);
				return false;
			}
		} else
			$stmtid = odbc_exec($this->_connectionID,$sql);
		
		if ($stmtid) {
			odbc_binmode($stmtid,$this->binmode);
			odbc_longreadlen($stmtid,$this->maxblobsize);
		}
		$this->_errorMsg = $php_errormsg;
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
	
	/*  returns true or false */
	function _close()
	{
		$ret = @odbc_close($this->_connectionID);
		$this->_connectionID = false;
		return $ret;
	}

    function _affectedrows()
    {
            return  odbc_num_rows($this->_queryID);
    }
	
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_odbc extends ADORecordSet {	
	
	var $bind = false;
	var $databaseType = "odbc";		
	var $dataProvider = "odbc";
	var $_has_stupid_odbc_fetch_api_change;
	
	function ADORecordSet_odbc($id)
	{
	global $ADODB_FETCH_MODE;

		$this->fetchMode = $ADODB_FETCH_MODE;
		return $this->ADORecordSet($id);
	}


	/*  returns the field object */
	function &FetchField($fieldOffset = -1) {
		
		$off=$fieldOffset+1; /*  offsets begin at 1 */
		
		$o= new ADOFieldObject();
		$o->name = @odbc_field_name($this->_queryID,$off);
		$o->type = @odbc_field_type($this->_queryID,$off);
		$o->max_length = @odbc_field_len($this->_queryID,$off);
		
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
		$this->_numOfRows = ($ADODB_COUNT_RECS) ? @odbc_num_rows($this->_queryID) : -1;
		$this->_numOfFields = @odbc_num_fields($this->_queryID);
		
		$this->_has_stupid_odbc_fetch_api_change = $this->connection->_has_stupid_odbc_fetch_api_change;
	}	
	
	function _seek($row)
	{
		return false;
	}
	
	/*  speed up SelectLimit() by switching to ADODB_FETCH_NUM as ADODB_FETCH_ASSOC is emulated */
	function GetArrayLimit($nrows,$offset=-1) 
	{
		if ($offset <= 0) return $this->GetArray($nrows);
		$savem = $this->fetchMode;
		$this->fetchMode = ADODB_FETCH_NUM;
		$this->Move($offset);
		$this->fetchMode = $savem;
		
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			$this->fields = $this->GetRowAssoc(false);
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
			$row = 0;
			if ($this->_has_stupid_odbc_fetch_api_change)
				$rez = odbc_fetch_into($this->_queryID,$this->fields,$row);
			else 
				$rez = odbc_fetch_into($this->_queryID,$row,$this->fields);
			if ($rez) {
				if ($this->fetchMode & ADODB_FETCH_ASSOC) {
					$this->fields = $this->GetRowAssoc(false);
				}
				return true;
			}
		}
		$this->EOF = true;
		return false;
	}	
	
	function _fetch()
	{
		$row = 0;
		/*
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			$this->fields = odbc_fetch_array($this->_queryID,$row);
			return is_array($this->fields);
		} else {
			return odbc_fetch_into($this->_queryID,$row,$this->fields);
		}*/
		
		if ($this->_has_stupid_odbc_fetch_api_change)
			$rez = odbc_fetch_into($this->_queryID,$this->fields,$row);
		else 
			$rez = odbc_fetch_into($this->_queryID,$row,$this->fields);
		
		if ($rez) {
			if ($this->fetchMode & ADODB_FETCH_ASSOC) {
				$this->fields = $this->GetRowAssoc(false);
			}
			return true;
		}
		return false;
	}
	
	function _close() {
		
		return @odbc_free_result($this->_queryID);		
	}
	


}

?>