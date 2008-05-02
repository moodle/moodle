<?php
/*
V5.04a 25 Mar 2008   (c) 2000-2008 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.

  Latest version is available at http://adodb.sourceforge.net
  
  Oracle data driver. Requires Oracle client. Works on Windows and Unix and Oracle 7.
  
  If you are using Oracle 8 or later, use the oci8 driver which is much better and more reliable.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB_oracle extends ADOConnection {
	var $databaseType = "oracle";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $concat_operator='||';
	var $_curs;
	var $_initdate = true; // init date to YYYY-MM-DD
	var $metaTablesSQL = 'select table_name from cat';	
	var $metaColumnsSQL = "select cname,coltype,width from col where tname='%s' order by colno";
	var $sysDate = "TO_DATE(TO_CHAR(SYSDATE,'YYYY-MM-DD'),'YYYY-MM-DD')";
	var $sysTimeStamp = 'SYSDATE';
	var $connectSID = true;
	
	function ADODB_oracle() 
	{
	}

	// format and return date string in database date format
	function DBDate($d)
	{
		if (is_string($d)) $d = ADORecordSet::UnixDate($d);
		return 'TO_DATE('.adodb_date($this->fmtDate,$d).",'YYYY-MM-DD')";
	}
	
	// format and return date string in database timestamp format
	function DBTimeStamp($ts)
	{

		if (is_string($ts)) $d = ADORecordSet::UnixTimeStamp($ts);
		return 'TO_DATE('.adodb_date($this->fmtTimeStamp,$ts).",'RRRR-MM-DD, HH:MI:SS AM')";
	}
	
	
	function BindDate($d)
	{
		$d = ADOConnection::DBDate($d);
		if (strncmp($d,"'",1)) return $d;
		
		return substr($d,1,strlen($d)-2);
	}
	
	function BindTimeStamp($d)
	{
		$d = ADOConnection::DBTimeStamp($d);
		if (strncmp($d,"'",1)) return $d;
		
		return substr($d,1,strlen($d)-2);
	}
	

	
	function BeginTrans()
	{	  
		 $this->autoCommit = false;
		 ora_commitoff($this->_connectionID);
		 return true;
	}

	
	function CommitTrans($ok=true) 
	{ 
		   if (!$ok) return $this->RollbackTrans();
		   $ret = ora_commit($this->_connectionID);
		   ora_commiton($this->_connectionID);
		   return $ret;
	}

	
	function RollbackTrans()
	{
		$ret = ora_rollback($this->_connectionID);
		ora_commiton($this->_connectionID);
		return $ret;
	}


	/* there seems to be a bug in the oracle extension -- always returns ORA-00000 - no error */
	function ErrorMsg() 
 	{   
        if ($this->_errorMsg !== false) return $this->_errorMsg;

        if (is_resource($this->_curs)) $this->_errorMsg = @ora_error($this->_curs);
 		if (empty($this->_errorMsg)) $this->_errorMsg = @ora_error($this->_connectionID);
		return $this->_errorMsg;
	}

 
	function ErrorNo() 
	{
		if ($this->_errorCode !== false) return $this->_errorCode;

		if (is_resource($this->_curs)) $this->_errorCode = @ora_errorcode($this->_curs);
		if (empty($this->_errorCode)) $this->_errorCode = @ora_errorcode($this->_connectionID);
        return $this->_errorCode;
	}

	

		// returns true or false
		function _connect($argHostname, $argUsername, $argPassword, $argDatabasename, $mode=0)
		{
			if (!function_exists('ora_plogon')) return null;
				
            // <G. Giunta 2003/03/03/> Reset error messages before connecting
            $this->_errorMsg = false;
		    $this->_errorCode = false;
        
            // G. Giunta 2003/08/13 - This looks danegrously suspicious: why should we want to set
            // the oracle home to the host name of remote DB?
//			if ($argHostname) putenv("ORACLE_HOME=$argHostname");

			if($argHostname) { // code copied from version submitted for oci8 by Jorma Tuomainen <jorma.tuomainen@ppoy.fi>
				if (empty($argDatabasename)) $argDatabasename = $argHostname;
				else {
					if(strpos($argHostname,":")) {
						$argHostinfo=explode(":",$argHostname);
						$argHostname=$argHostinfo[0];
						$argHostport=$argHostinfo[1];
					} else {
						$argHostport="1521";
					}


					if ($this->connectSID) {
						$argDatabasename="(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$argHostname
						.")(PORT=$argHostport))(CONNECT_DATA=(SID=$argDatabasename)))";
					} else
						$argDatabasename="(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$argHostname
						.")(PORT=$argHostport))(CONNECT_DATA=(SERVICE_NAME=$argDatabasename)))";
				}

			}

			if ($argDatabasename) $argUsername .= "@$argDatabasename";

		//if ($argHostname) print "<p>Connect: 1st argument should be left blank for $this->databaseType</p>";
			if ($mode == 1)
				$this->_connectionID = ora_plogon($argUsername,$argPassword);
			else
				$this->_connectionID = ora_logon($argUsername,$argPassword);
			if ($this->_connectionID === false) return false;
			if ($this->autoCommit) ora_commiton($this->_connectionID);
			if ($this->_initdate) {
				$rs = $this->_query("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");
				if ($rs) ora_close($rs);
			}

			return true;
		}


		// returns true or false
		function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
		{
			return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename, 1);
		}


		// returns query ID if successful, otherwise false
		function _query($sql,$inputarr=false)
		{
            // <G. Giunta 2003/03/03/> Reset error messages before executing
            $this->_errorMsg = false;
		    $this->_errorCode = false;

			$curs = ora_open($this->_connectionID);
		 
		 	if ($curs === false) return false;
			$this->_curs = $curs;
			if (!ora_parse($curs,$sql)) return false;
			if (ora_exec($curs)) return $curs;
            // <G. Giunta 2004/03/03> before we close the cursor, we have to store the error message
            // that we can obtain ONLY from the cursor (and not from the connection)
            $this->_errorCode = @ora_errorcode($curs);
            $this->_errorMsg = @ora_error($curs);
            // </G. Giunta 2004/03/03>            
		 	@ora_close($curs);
			return false;
		}


		// returns true or false
		function _close()
		{
			return @ora_logoff($this->_connectionID);
		}



}


/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oracle extends ADORecordSet {

	var $databaseType = "oracle";
	var $bind = false;

	function ADORecordset_oracle($queryID,$mode=false)
	{
		
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		$this->fetchMode = $mode;
		
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



	   /*		Returns: an object containing field information.
			   Get column information in the Recordset object. fetchField() can be used in order to obtain information about
			   fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
			   fetchField() is retrieved.		*/

	   function FetchField($fieldOffset = -1)
	   {
			$fld = new ADOFieldObject;
			$fld->name = ora_columnname($this->_queryID, $fieldOffset);
			$fld->type = ora_columntype($this->_queryID, $fieldOffset);
			$fld->max_length = ora_columnsize($this->_queryID, $fieldOffset);
			return $fld;
	   }

	/* Use associative array to get fields array */
	function Fields($colname)
	{
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
		   $this->_numOfRows = -1;
		   $this->_numOfFields = @ora_numcols($this->_queryID);
   }


   function _seek($row)
   {
		   return false;
   }

   function _fetch($ignore_fields=false) {
// should remove call by reference, but ora_fetch_into requires it in 4.0.3pl1
		if ($this->fetchMode & ADODB_FETCH_ASSOC)
			return @ora_fetch_into($this->_queryID,&$this->fields,ORA_FETCHINTO_NULLS|ORA_FETCHINTO_ASSOC);
   		else 
			return @ora_fetch_into($this->_queryID,&$this->fields,ORA_FETCHINTO_NULLS);
   }

   /*		close() only needs to be called if you are worried about using too much memory while your script
		   is running. All associated result memory for the specified result identifier will automatically be freed.		*/

   function _close() 
{
		   return @ora_close($this->_queryID);
   }

	function MetaType($t,$len=-1)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		
		switch (strtoupper($t)) {
		case 'VARCHAR':
		case 'VARCHAR2':
		case 'CHAR':
		case 'VARBINARY':
		case 'BINARY':
				if ($len <= $this->blobSize) return 'C';
		case 'LONG':
		case 'LONG VARCHAR':
		case 'CLOB':
		return 'X';
		case 'LONG RAW':
		case 'LONG VARBINARY':
		case 'BLOB':
				return 'B';
		
		case 'DATE': return 'D';
		
		//case 'T': return 'T';
		
		case 'BIT': return 'L';
		case 'INT': 
		case 'SMALLINT':
		case 'INTEGER': return 'I';
		default: return 'N';
		}
	}
}
?>