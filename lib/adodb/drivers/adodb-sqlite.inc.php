<?php
/*
V4.01 23 Oct 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.

  Latest version is available at http://php.weblogs.com/
  
  SQLite info: http://www.hwaci.com/sw/sqlite/
    
  Install Instructions:
  ====================
  1. Place this in adodb/drivers
  2. Rename the file, remove the .txt prefix.
*/

class ADODB_sqlite extends ADOConnection {
	var $databaseType = "sqlite";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $concat_operator='||';
	var $_errorNo = 0;
	var $hasLimit = true;	
	var $hasInsertID = true; 		/// supports autoincrement ID?
	var $hasAffectedRows = true; 	/// supports affected rows for update/delete?
	var $metaTablesSQL = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
	var $sysDate = "adodb_date('Y-m-d')";
	var $sysTimeStamp = "adodb_date('Y-m-d H:i:s')";
	
	function ADODB_sqlite() 
	{
	}
	
/*
  function __get($name) 
  {
  	switch($name) {
	case 'sysDate': return "'".date($this->fmtDate)."'";
	case 'sysTimeStamp' : return "'".date($this->sysTimeStamp)."'";
	}
  }*/
	
	function ServerInfo()
	{
		$arr['version'] = sqlite_libversion();
		$arr['description'] = 'SQLite ';
		$arr['encoding'] = sqlite_libencoding();
		return $arr;
	}
	
	function BeginTrans()
	{	  
		 if ($this->transOff) return true; 
		 $ret = $this->Execute("BEGIN TRANSACTION");
		 $this->transCnt += 1;
		 return true;
	}
	
	function CommitTrans($ok=true) 
	{ 
		if ($this->transOff) return true; 
		if (!$ok) return $this->RollbackTrans();
		$ret = $this->Execute("COMMIT");
		if ($this->transCnt>0)$this->transCnt -= 1;
		return !empty($ret);
	}
	
	function RollbackTrans()
	{
		if ($this->transOff) return true; 
		$ret = $this->Execute("ROLLBACK");
		if ($this->transCnt>0)$this->transCnt -= 1;
		return !empty($ret);
	}

	function _insertid()
	{
		return sqlite_last_insert_rowid($this->_connectionID);
	}
	
	function _affectedrows()
	{
        return sqlite_changes($this->_connectionID);
    }
	
	function ErrorMsg() 
 	{
		if ($this->_logsql) return $this->_errorMsg;
		return ($this->_errorNo) ? sqlite_error_string($this->_errorNo) : '';
	}
 
	function ErrorNo() 
	{
		return $this->_errorNo;
	}
	
	function SQLDate($fmt, $col=false)
	{
		$fmt = $this->qstr($fmt);
		return ($col) ? "adodb_date2($fmt,$col)" : "adodb_date($fmt)";
	}
	
	function &MetaColumns($tab)
	{
	global $ADODB_FETCH_MODE;
	
		$rs = $this->Execute("select * from $tab limit 1");
		if (!$rs) return false;
		$arr = array();
		for ($i=0,$max=$rs->_numOfFields; $i < $max; $i++) {
			$fld =& $rs->FetchField($i);
			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] =& $fld;	
			else $arr[strtoupper($fld->name)] =& $fld;
		}
		$rs->Close();
		return $arr;
	}
	
	function _createFunctions()
	{
		@sqlite_create_function($this->_connectionID, 'adodb_date', 'adodb_date', 1);
		@sqlite_create_function($this->_connectionID, 'adodb_date2', 'adodb_date2', 2);
	}
	

	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$this->_connectionID = sqlite_open($argHostname);
		if ($this->_connectionID === false) return false;
		$this->_createFunctions();
		return true;
	}
	
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$this->_connectionID = sqlite_popen($argHostname);
		if ($this->_connectionID === false) return false;
		$this->_createFunctions();
		return true;
	}

	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr=false)
	{
		$rez = sqlite_query($sql,$this->_connectionID);
		if (!$rez) {
			$this->_errorNo = sqlite_last_error($this->_connectionID);
		}
		
		return $rez;
	}
	
	function &SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0) 
	{
		$offsetStr = ($offset >= 0) ? " OFFSET $offset" : '';
		$limitStr  = ($nrows >= 0)  ? " LIMIT $nrows" : ($offset >= 0 ? ' LIMIT 999999999' : '');
	  	return $secs2cache ?
	   		$this->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr)
	  	:
	   		$this->Execute($sql."$limitStr$offsetStr",$inputarr);
	}
	
	/*
		This algorithm is not very efficient, but works even if table locking
		is not available.
		
		Will return false if unable to generate an ID after $MAXLOOPS attempts.
	*/
	var $_genSeqSQL = "create table %s (id integer)";
	
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
			}
		}
		if ($fn = $this->raiseErrorFn) {
			$fn($this->databaseType,'GENID',-32000,"Unable to generate unique id after $MAXLOOPS attempts",$seq,$num);
		}
		return false;
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
	function DropSequence($seqname)
	{
		if (empty($this->_dropSeqSQL)) return false;
		return $this->Execute(sprintf($this->_dropSeqSQL,$seqname));
	}
	
	// returns true or false
	function _close()
	{
		return @sqlite_close($this->_connectionID);
	}


}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_sqlite extends ADORecordSet {

	var $databaseType = "sqlite";
	var $bind = false;

	function ADORecordset_sqlite($queryID,$mode=false)
	{
		
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch($mode) {
		case ADODB_FETCH_NUM: $this->fetchMode = SQLITE_NUM; break;
		case ADODB_FETCH_ASSOC: $this->fetchMode = SQLITE_ASSOC; break;
		default: $this->fetchMode = SQLITE_BOTH; break;
		}
		
		$this->_queryID = $queryID;
	
		$this->_inited = true;
		$this->fields = array();
		if ($queryID) {
			$this->_currentRow = 0;
			$this->EOF = !$this->_fetch();
			@$this->_initrs();
		} else {
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
			$this->EOF = true;
		}
		
		return $this->_queryID;
	}


	function &FetchField($fieldOffset = -1)
	{
		$fld = new ADOFieldObject;
		$fld->name = sqlite_field_name($this->_queryID, $fieldOffset);
		$fld->type = 'VARCHAR';
		$fld->max_length = -1;
		return $fld;
	}
	
   function _initrs()
   {
		$this->_numOfRows = @sqlite_num_rows($this->_queryID);
		$this->_numOfFields = @sqlite_num_fields($this->_queryID);
   }

	function Fields($colname)
	{
		if ($this->fetchMode != SQLITE_NUM) return $this->fields[$colname];
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		
		 return $this->fields[$this->bind[strtoupper($colname)]];
	}
	
   function _seek($row)
   {
   		return sqlite_seek($this->_queryID, $row);
   }

	function _fetch($ignore_fields=false) 
	{
		$this->fields = @sqlite_fetch_array($this->_queryID,$this->fetchMode);
		return !empty($this->fields);
	}
	
	function _close() 
	{
	}

}
?>