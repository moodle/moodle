<?php
/* 
V4.51 29 July 2004  (c) 2000-2004 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Requires ODBC. Works on Windows and Unix.
*/
// security - hide paths
if (!defined('ADODB_DIR')) die();
	 
/*--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------*/


class ADODB_pdo extends ADOConnection {
	var $databaseType = "pdo";	
	var $dataProvider = "pdo";
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d, h:i:sA'";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $hasAffectedRows = true;
	var $_bindInputArray = true;	
	var $_genSeqSQL = "create table %s (id integer)";
	var $_autocommit = true;
	var $_haserrorfunctions = true;
	var $_lastAffectedRows = 0;
	
	var $stmt = false;
	
	function ADODB_pdo() 
	{
	}
	

	// returns true or false
	function _connect($argDSN, $argUsername, $argPassword, $argDatabasename, $persist=false)
	{
		$this->_connectionID = new PDO($argDSN, $argUsername, $argPassword);
		if ($this->_connectionID) {
			switch(ADODB_ASSOC_CASE){
			case 0: $m = PDO_CASE_LOWER; break;
			case 1: $m = PDO_CASE_UPPER; break;
			default:
			case 2: $m = PDO_CASE_NATURAL; break;
			}
			
			//$this->_connectionID->setAttribute(PDO_ATTR_ERRMODE,PDO_ERRMODE_SILENT );
			$this->_connectionID->setAttribute(PDO_ATTR_CASE,$m);
			
			//$this->_connectionID->setAttribute(PDO_ATTR_AUTOCOMMIT,true);
			
			return true;
		}
		return false;
	}
	
	// returns true or false
	function _pconnect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
		return $this->_connect($argDSN, $argUsername, $argPassword, $argDatabasename, true);
	}
	
	function ErrorMsg()
	{
		if ($this->_stmt) $arr = $this->_stmt->errorInfo();
		else $arr = $this->_connectionID->errorInfo();
		
		if ($arr) {
			if ($arr[0]) return $arr[2];
			else return '';
		} else return '-1';
	}
	
	function InParameter(&$stmt,&$var,$name,$maxLen=4000,$type=false)
	{
		$obj = $stmt[1];
		if ($type) $obj->bindParam($name,$var,$type,$maxLen);
		else $obj->bindParam($name, $var);
	}
	
	function ErrorNo()
	{
		
		if ($this->_stmt) return $this->_stmt->errorCode();
		else return $this->_connectionID->errorInfo();
	}

	function BeginTrans()
	{	
		if (!$this->hasTransactions) return false;
		if ($this->transOff) return true; 
		$this->transCnt += 1;
		$this->_autocommit = false;
		$this->_connectionID->setAttribute(PDO_ATTR_AUTOCOMMIT,false);
		return $this->_connectionID->beginTransaction();
	}
	
	function CommitTrans($ok=true) 
	{ 
		if ($this->transOff) return true; 
		if (!$ok) return $this->RollbackTrans();
		if ($this->transCnt) $this->transCnt -= 1;
		$this->_autocommit = true;
		
		$ret = $this->_connectionID->commit();
		$this->_connectionID->setAttribute(PDO_ATTR_AUTOCOMMIT,true);
		return $ret;
	}
	
	function RollbackTrans()
	{
		if ($this->transOff) return true; 
		if ($this->transCnt) $this->transCnt -= 1;
		$this->_autocommit = true;
		
		$ret = $this->_connectionID->rollback();
		$this->_connectionID->setAttribute(PDO_ATTR_AUTOCOMMIT,true);
		return $ret;
	}
	
	function Prepare($sql)
	{
		$this->_stmt = $this->_connectionID->prepare($sql);
		if ($this->_stmt) return array($sql,$this->_stmt);
		
		return false;
	}
	
	function PrepareStmt($sql)
	{
		$stmt = $this->_connectionID->prepare($sql);
		if (!$stmt) return false;
		$obj = new ADOPDOStatement($stmt,$this);
		return $obj;
	}

	/* returns queryID or false */
	function _query($sql,$inputarr=false) 
	{
		if (is_array($sql)) {
			$stmt = $sql[1];
		} else {
			$stmt = $this->_connectionID->prepare($sql);		
		}
		if ($stmt) {
			if ($inputarr) $stmt->execute($inputarr);
			else $stmt->execute();
		}
		$this->_stmt = $stmt;
		return $stmt;
	}

	// returns true or false
	function _close()
	{
		$this->_stmt = false;
		return true;
	}

	function _affectedrows()
	{
		return ($this->_stmt) ? $this->_stmt->rowCount() : 0;
	}
	
	function _insertid()
	{
		return ($this->_connectionID) ? $this->_connectionID->lastInsertId() : 0;
	}
}

class ADOPDOStatement {

	var $databaseType = "pdo";		
	var $dataProvider = "pdo";
	var $_stmt;
	var $_connectionID;
	
	function ADOPDOStatement($stmt,$connection)
	{
		$this->_stmt = $stmt;
		$this->_connectionID = $connection;
	}
	
	function Execute($inputArr=false)
	{
		$savestmt = $this->_connectionID->_stmt;
		$rs = $this->_connectionID->Execute(array(false,$this->_stmt),$inputArr);
		$this->_connectionID->_stmt = $savestmt;
		return $rs;
	}
	
	function InParameter(&$var,$name,$maxLen=4000,$type=false)
	{

		if ($type) $this->_stmt->bindParam($name,$var,$type,$maxLen);
		else $this->_stmt->bindParam($name, $var);
	}
	
	function Affected_Rows()
	{
		return ($this->_stmt) ? $this->_stmt->rowCount() : 0;
	}
	
	function ErrorMsg()
	{
		if ($this->_stmt) $arr = $this->_stmt->errorInfo();
		else $arr = $this->_connectionID->errorInfo();
		print_r($arr);
		if ($arr) {
			if ($arr[0]) return $arr[2];
			else return '';
		} else return '-1';
	}
	
	function ErrorNo()
	{
		if ($this->_stmt) return $this->_stmt->errorCode();
		else return $this->_connectionID->errorInfo();
	}
}

/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_pdo extends ADORecordSet {	
	
	var $bind = false;
	var $databaseType = "pdo";		
	var $dataProvider = "pdo";
	
	function ADORecordSet_pdo($id,$mode=false)
	{
		if ($mode === false) {  
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch($mode) {
		default:
		case ADODB_FETCH_BOTH: $mode = PDO_FETCH_BOTH; break;
		case ADODB_FETCH_NUM: $mode = PDO_FETCH_NUM; break;
		case ADODB_FETCH_ASSOC:  $mode = PDO_FETCH_ASSOC; break;
		}
		$this->fetchMode = $mode;
		
		$this->_queryID = $id;
		$this->ADORecordSet($id);
	}


	// returns the field object
	function &FetchField($fieldOffset = -1) 
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
	
	function Init()
	{
		if ($this->_inited) return;
		$this->_inited = true;
		if ($this->_queryID) @$this->_initrs();
		else {
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
		}
		if ($this->_numOfRows != 0 && $this->_currentRow == -1) {
			$this->_currentRow = 0;
			if ($this->EOF = ($this->_fetch() === false)) {
				$this->_numOfRows = 0; // _numOfRows could be -1
			}
			$this->_numOfFields = sizeof($this->fields);
		} else {
			$this->EOF = true;
		}
	}
	
		
	function _initrs()
	{
	global $ADODB_COUNTRECS;
	
		$this->_numOfRows = ($ADODB_COUNTRECS) ? @$this->_queryID->rowCount() : -1;
		if (!$this->_numOfRows) $this->_numOfRows = -1;
		$this->_numOfFields =0;
	}	
	
	function _seek($row)
	{
		return false;
	}
	
	function _fetch()
	{
		$this->fields = $this->_queryID->fetch($this->fetchMode);
		return !empty($this->fields);
	}
	
	function _close() 
	{
		$this->_queryID = false;
	}

}

?>