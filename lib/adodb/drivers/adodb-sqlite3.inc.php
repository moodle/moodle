<?php
/*
V5.17 17 May 2012  (c) 2000-2012 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.

  Latest version is available at http://adodb.sourceforge.net
  
  SQLite info: http://www.hwaci.com/sw/sqlite/
    
  Install Instructions:
  ====================
  1. Place this in adodb/drivers
  2. Rename the file, remove the .txt prefix.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

// class ADODB_sqlite extends ADOConnection { **change
class ADODB_sqlite3 extends ADOConnection {
	//var $databaseType = "sqlite"; **change
	var $databaseType = "sqlite3";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $concat_operator='||';
	var $_errorNo = 0;
	var $hasLimit = true;	
	var $hasInsertID = true; 		/// supports autoincrement ID?
	var $hasAffectedRows = true; 	/// supports affected rows for update/delete?
	var $metaTablesSQL = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
	var $sysDate = "adodb_date('Y-m-d')";
	var $sysTimeStamp = "adodb_date('Y-m-d H:i:s')";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	
	//function ADODB_sqlite3() **change
	function ADODB_sqlite3() 
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
		$arr['version'] = sqlite_libversion(); //**tochange
		$arr['description'] = 'SQLite '; //**tochange
		$arr['encoding'] = sqlite_libencoding();//**tochange
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
	
	// mark newnham
	function MetaColumns($table, $normalize=true) 
	{
	  global $ADODB_FETCH_MODE;
	  $false = false;
	  $save = $ADODB_FETCH_MODE;
	  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	  if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
	  $rs = $this->Execute("PRAGMA table_info('$table')");
	  if (isset($savem)) $this->SetFetchMode($savem);
	  if (!$rs) {
	    $ADODB_FETCH_MODE = $save; 
	    return $false;
	  }
	  $arr = array();
	  while ($r = $rs->FetchRow()) {
	  
	    $type = explode('(',$r['type']);
	    $size = '';
	    if (sizeof($type)==2)
	    $size = trim($type[1],')');
	    $fn = strtoupper($r['name']);
	    $fld = new ADOFieldObject;
	    $fld->name = $r['name'];
	    $fld->type = $type[0];
	    $fld->max_length = $size;
	    $fld->not_null = $r['notnull'];
	    $fld->default_value = $r['dflt_value'];
	    $fld->scale = 0;
		if (isset($r['pk']) && $r['pk']) $fld->primary_key=1;
	    if ($save == ADODB_FETCH_NUM) $arr[] = $fld;	
	    else $arr[strtoupper($fld->name)] = $fld;
	  }
	  $rs->Close();
	  $ADODB_FETCH_MODE = $save;
	  return $arr;
	}
	
	function _init($parentDriver)
	{
	
		$parentDriver->hasTransactions = false;
		$parentDriver->hasInsertID = true;
	}

	function _insertid()
	{
		//return sqlite_last_insert_rowid($this->_connectionID)->; //**change
		$temp = $this->_connectionID;
		return $temp->lastInsertRowID();
	}
	
	function _affectedrows()
	{
       return sqlite3_changes($this->_connectionID); //**tochange
    }
	
	function ErrorMsg() 
 	{
		if ($this->_logsql) return $this->_errorMsg;
		
		return ($this->_errorNo) ? sqlite_error_string($this->_errorNo) : ''; //**tochange?
	}
 
	function ErrorNo() 
	{
		return $this->_errorNo; //**tochange??
	}
	
	function SQLDate($fmt, $col=false)
	{
		$fmt = $this->qstr($fmt);
		return ($col) ? "adodb_date2($fmt,$col)" : "adodb_date($fmt)";
	}
	
	
	function _createFunctions()
	{
		//@sqlite3_create_function($this->_connectionID, 'adodb_date', 'adodb_date', 1); *change
		$this->_connectionID->createFunction('adodb_date', 'adodb_date', 1);
		
		//@sqlite3_create_function($this->_connectionID, 'adodb_date2', 'adodb_date2', 2);**change
		$this->_connectionID->createFunction('adodb_date2', 'adodb_date2', 2);
	}
	

	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename) //**tochange: all the function need to be changed, just hacks for the moment
	{
		$this->_connectionID = new SQLite3('/path/mydb'); // hack
		if (empty($argHostname) && $argDatabasename) $argHostname = $argDatabasename; // hack
		$this->_createFunctions();
		
		return true; // hack
		
		if (!function_exists('sqlite_open')) return null;
		if (empty($argHostname) && $argDatabasename) $argHostname = $argDatabasename;
		
		$this->_connectionID = sqlite_open($argHostname);
		if ($this->_connectionID === false) return false;
		$this->_createFunctions();
		return true;
	}
	
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename) //**tochange
	{
		if (!function_exists('sqlite_open')) return null;
		if (empty($argHostname) && $argDatabasename) $argHostname = $argDatabasename;
		
		$this->_connectionID = sqlite_popen($argHostname);
		if ($this->_connectionID === false) return false;
		$this->_createFunctions();
		return true;
	}

	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr=false)
	{
		//$rez = sqlite_query($sql,$this->_connectionID);//**change
		$rez = $this->_connectionID->query($sql);
		if (!$rez) {
			//$this->_errorNo = sqlite3_last_error($this->_connectionID);**change
			$this->_connectionID->lastErrorCode();
		}
		
		return $rez;
	}
	
	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0) 
	{
		$offsetStr = ($offset >= 0) ? " OFFSET $offset" : '';
		$limitStr  = ($nrows >= 0)  ? " LIMIT $nrows" : ($offset >= 0 ? ' LIMIT 999999999' : '');
	  	if ($secs2cache)
	   		$rs = $this->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr);
	  	else
	   		$rs = $this->Execute($sql."$limitStr$offsetStr",$inputarr);
			
		return $rs;
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
			@($num = $this->GetOne("select id from $seq"));
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
		//return @sqlite3_close($this->_connectionID);**change
		return $this->_connectionID->close();
	}

	function MetaIndexes($table, $primary = FALSE, $owner=false, $owner = false)
	{
		$false = false;
		// save old fetch mode
        global $ADODB_FETCH_MODE;
        $save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
               $savem = $this->SetFetchMode(FALSE);
        }
		$SQL=sprintf("SELECT name,sql FROM sqlite_master WHERE type='index' AND tbl_name='%s'", strtolower($table));
        $rs = $this->Execute($SQL);
        if (!is_object($rs)) {
			if (isset($savem)) 
				$this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
            return $false;
        }

		$indexes = array ();
		while ($row = $rs->FetchRow()) {
			if ($primary && preg_match("/primary/i",$row[1]) == 0) continue;
            if (!isset($indexes[$row[0]])) {

			$indexes[$row[0]] = array(
				   'unique' => preg_match("/unique/i",$row[1]),
				   'columns' => array());
			}
			/**
			  * There must be a more elegant way of doing this,
			  * the index elements appear in the SQL statement
			  * in cols[1] between parentheses
			  * e.g CREATE UNIQUE INDEX ware_0 ON warehouse (org,warehouse)
			  */
			$cols = explode("(",$row[1]);
			$cols = explode(")",$cols[1]);
			array_pop($cols);
			$indexes[$row[0]]['columns'] = $cols;
		}
		if (isset($savem)) { 
            $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
		}
        return $indexes;
	}

}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

//class ADORecordset_sqlite extends ADORecordSet {**change
class ADORecordset_sqlite3 extends ADORecordSet {

	//var $databaseType = "sqlite";**change
	var $databaseType = "sqlite3";
	var $bind = false;

	//function ADORecordset_sqlite($queryID,$mode=false)**change
	function ADORecordset_sqlite3($queryID,$mode=false)
	{
		
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch($mode) {
		//case ADODB_FETCH_NUM: $this->fetchMode = SQLITE_NUM; break;**change
		case ADODB_FETCH_NUM: $this->fetchMode = SQLITE3_NUM; break;
		//case ADODB_FETCH_ASSOC: $this->fetchMode = SQLITE_ASSOC; break;**change
		case ADODB_FETCH_ASSOC: $this->fetchMode = SQLITE3_ASSOC; break;
		//default: $this->fetchMode = SQLITE_BOTH; break;**change
		default: $this->fetchMode = SQLITE3_BOTH; break;
		}
		$this->adodbFetchMode = $mode;
		
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


	function FetchField($fieldOffset = -1)
	{
		$fld = new ADOFieldObject;
		//$fld->name = sqlite3_field_name($this->_queryID, $fieldOffset);**change
		$fld->name->columnName($this->_queryID, $fieldOffset);
		$fld->type = 'VARCHAR';
		$fld->max_length = -1;
		return $fld;
	}
	
   function _initrs()
   {
		//$this->_numOfRows = @sqlite_num_rows($this->_queryID); **tochange but sqlite3 doesn't implement this!
		$this->_numOfRows = 1;
		//$this->_numOfFields = @sqlite3_num_fields($this->_queryID);**change
		$this->_numOfFields = $this->_queryID->numColumns();
		
   }

	function Fields($colname)
	{
		//if ($this->fetchMode != SQLITE_NUM) return $this->fields[$colname];**change
		if ($this->fetchMode != SQLITE3_NUM) return $this->fields[$colname];
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
   		return sqlite3_seek($this->_queryID, $row);//**tochange but sqlite3 seems not to implement seek!
   }

	function _fetch($ignore_fields=false) 
	{
		//$this->fields = @sqlite3_fetch_array($this->_queryID,$this->fetchMode);**change
		$this->fields = $this->_queryID->fetchArray($this->fetchMode);
		return !empty($this->fields);
	}
	
	function _close() 
	{
	}

}
?>