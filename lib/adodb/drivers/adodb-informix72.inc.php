<?php
/*
V3.40 7 April 2003  (c) 2000-2003 John Lim. All rights reserved.
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 4 for best viewing.

  Latest version is available at http://php.weblogs.com/

  Informix port by Mitchell T. Young (mitch@youngfamily.org)

  Further mods by "Samuel CARRIERE" <samuel_carriere@hotmail.com>

*/

class ADODB_informix72 extends ADOConnection {
	var $databaseType = "informix72";
	var $dataProvider = "informix";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	var $hasInsertID = true;
	var $hasAffectedRows = true;
	var $metaTablesSQL="select tabname from systables";
	var $metaColumnsSQL = "select colname, coltype, collength from syscolumns c, systables t where c.tabid=t.tabid and tabname='%s'";
	var $concat_operator = '||';

	var $lastQuery = false;
	var $has_insertid = true;

	var $_autocommit = true;
	var $_bindInputArray = true;  // set to true if ADOConnection.Execute() permits binding of array parameters.
	var $sysDate = 'TODAY';
	var $sysTimeStamp = 'CURRENT';
   
	function ADODB_informix72()
	{

		// alternatively, use older method:
		//putenv("DBDATE=Y4MD-");

		// force ISO date format
		putenv('GL_DATE=%Y-%m-%d');
	}

	function _insertid()
	{
		$sqlca =ifx_getsqlca($this->lastQuery);
		return @$sqlca["sqlerrd1"];
	}

	function _affectedrows()
	{
		if ($this->lastQuery) {
		   return ifx_affected_rows ($this->lastQuery);
	   } else
		return 0;
	}

	function BeginTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->Execute('BEGIN');
		$this->_autocommit = false;
		return true;
	}

	function CommitTrans($ok=true) 
	{ 
		if (!$ok) return $this->RollbackTrans();
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('COMMIT');
		$this->_autocommit = true;
		return true;
	}

	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('ROLLBACK');
		$this->_autocommit = true;
		return true;
	}

	function RowLock($tables,$where)
	{
		if ($this->_autocommit) $this->BeginTrans();
		return $this->GetOne("select 1 as ignore from $tables where $where for update");
	}

	/*	Returns: the last error message from previous database operation
		Note: This function is NOT available for Microsoft SQL Server.	*/

	function ErrorMsg() {
		$this->_errorMsg = ifx_errormsg();
		return $this->_errorMsg;
	}

   function ErrorNo() 
   {
	  return ifx_error();
   }

   function MetaColumns($table)
   {
		return ADOConnection::MetaColumns($table,false);
   }

   function UpdateBlob($table, $column, $val, $where, $blobtype = 'BLOB')
   {
   		$type = ($blobtype == 'TEXT') ? 1 : 0;
		$blobid = ifx_create_blob($type,0,$val);
		return $this->Execute("UPDATE $table SET $column=(?) WHERE $where",array($blobid));
   }

   function BlobDecode($blobid)
   {
   		return @ifx_get_blob($blobid);
   }
	// returns true or false
   function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$dbs = $argDatabasename . "@" . $argHostname;
		$this->_connectionID = ifx_connect($dbs,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		#if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;
	}

	// returns true or false
   function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$dbs = $argDatabasename . "@" . $argHostname;
		$this->_connectionID = ifx_pconnect($dbs,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		#if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;
	}
/*
	// ifx_do does not accept bind parameters - wierd ???
	function Prepare($sql)
	{
		$stmt = ifx_prepare($sql);
		if (!$stmt) return $sql;
		else return array($sql,$stmt);
	}
*/
	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr)
	{
	global $ADODB_COUNTRECS;
	
	  // String parameters have to be converted using ifx_create_char
	  if ($inputarr) {
		 foreach($inputarr as $v) {
			if (gettype($v) == 'string') {
			   $tab[] = ifx_create_char($v);
			}
			else {
			   $tab[] = $v;
			}
		 }
	  }

	  // In case of select statement, we use a scroll cursor in order
	  // to be able to call "move", or "movefirst" statements
	  if (!$ADODB_COUNTRECS && preg_match("/^\s*select/i", $sql)) {
		 if ($inputarr) {
			$this->lastQuery = ifx_query($sql,$this->_connectionID, IFX_SCROLL, $tab);
		 }
		 else {
			$this->lastQuery = ifx_query($sql,$this->_connectionID, IFX_SCROLL);
		 }
	  }
	  else {
		 if ($inputarr) {
			$this->lastQuery = ifx_query($sql,$this->_connectionID, $tab);
		 }
		 else {
			$this->lastQuery = ifx_query($sql,$this->_connectionID);
		 }
	  }

	  // Following line have been commented because autocommit mode is
	  // not supported by informix SE 7.2

	  //if ($this->_autocommit) ifx_query('COMMIT',$this->_connectionID);

		return $this->lastQuery;
	}

	// returns true or false
	function _close()
	{
		$this->lastQuery = false;
		return ifx_close($this->_connectionID);
	}
}


/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_informix72 extends ADORecordSet {

	var $databaseType = "informix72";
	var $canSeek = true;
	var $_fieldprops = false;

	function ADORecordset_informix72($id,$mode=false)
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		$this->fetchMode = $mode;
		return $this->ADORecordSet($id);
	}



	/*	Returns: an object containing field information.
		Get column information in the Recordset object. fetchField() can be used in order to obtain information about
		fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
		fetchField() is retrieved.	*/
	function &FetchField($fieldOffset = -1)
	{
		if (empty($this->_fieldprops)) {
			$fp = ifx_fieldproperties($this->_queryID);
			foreach($fp as $k => $v) {
				$o = new ADOFieldObject;
				$o->name = $k;
				$arr = split(';',$v); //"SQLTYPE;length;precision;scale;ISNULLABLE"
				$o->type = $arr[0];
				$o->max_length = $arr[1];
				$this->_fieldprops[] = $o;
			}
		}
		return $this->_fieldprops[$fieldOffset];
	}

	function _initrs()
	{
		$this->_numOfRows = -1; // ifx_affected_rows not reliable, only returns estimate -- ($ADODB_COUNTRECS)? ifx_affected_rows($this->_queryID):-1;
		$this->_numOfFields = ifx_num_fields($this->_queryID);
	}

	function _seek($row)
	{
		return @ifx_fetch_row($this->_queryID, $row);
	}

   function MoveLast()
   {
	  $this->fields = @ifx_fetch_row($this->_queryID, "LAST");
	  if ($this->fields) $this->EOF = false;
	  $this->_currentRow = -1;

	  if ($this->fetchMode == ADODB_FETCH_NUM) {
		 foreach($this->fields as $v) {
			$arr[] = $v;
		 }
		 $this->fields = $arr;
	  }

	  return true;
   }

   function MoveFirst()
	{
	  $this->fields = @ifx_fetch_row($this->_queryID, "FIRST");
	  if ($this->fields) $this->EOF = false;
	  $this->_currentRow = 0;

	  if ($this->fetchMode == ADODB_FETCH_NUM) {
		 foreach($this->fields as $v) {
			$arr[] = $v;
		 }
		 $this->fields = $arr;
	  }

	  return true;
   }

   function _fetch($ignore_fields=false)
   {

		$this->fields = @ifx_fetch_row($this->_queryID);

		if (!is_array($this->fields)) return false;

		if ($this->fetchMode == ADODB_FETCH_NUM) {
			foreach($this->fields as $v) {
				$arr[] = $v;
			}
			$this->fields = $arr;
		}
		return true;
	}

	/*	close() only needs to be called if you are worried about using too much memory while your script
		is running. All associated result memory for the specified result identifier will automatically be freed.	*/
	function _close()
	{
		return ifx_free_result($this->_queryID);
	}

}
?>