<?php
/* 
V4.94 23 Jan 2007  (c) 2000-2007 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Oracle support via ODBC. Requires ODBC. Works on Windows. 
*/
// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}

 
class  ADODB_odbc_oracle extends ADODB_odbc {	
	var $databaseType = 'odbc_oracle';
 	var $replaceQuote = "''"; // string to use to replace quotes
	var $concat_operator='||';
	var $fmtDate = "'Y-m-d 00:00:00'"; 
	var $fmtTimeStamp = "'Y-m-d h:i:sA'";
	var $metaTablesSQL = 'select table_name from cat';
	var $metaColumnsSQL = "select cname,coltype,width from col where tname='%s' order by colno";
	var $sysDate = "TRUNC(SYSDATE)";
	var $sysTimeStamp = 'SYSDATE';
	
	//var $_bindInputArray = false;
	
	function ADODB_odbc_oracle()
	{
		$this->ADODB_odbc();
	}
		
	function &MetaTables() 
	{
		$false = false;
		$rs = $this->Execute($this->metaTablesSQL);
		if ($rs === false) return $false;
		$arr = $rs->GetArray();
		$arr2 = array();
		for ($i=0; $i < sizeof($arr); $i++) {
			$arr2[] = $arr[$i][0];
		}
		$rs->Close();
		return $arr2;
	}
	
	function &MetaColumns($table) 
	{
	global $ADODB_FETCH_MODE;
	
		$rs = $this->Execute(sprintf($this->metaColumnsSQL,strtoupper($table)));
		if ($rs === false) {	
			$false = false;
			return $false;
		}
		$retarr = array();
		while (!$rs->EOF) { //print_r($rs->fields);
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];
			$fld->type = $rs->fields[1];
			$fld->max_length = $rs->fields[2];
			
			
			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] = $fld;	
			else $retarr[strtoupper($fld->name)] = $fld;
			
			$rs->MoveNext();
		}
		$rs->Close();
		return $retarr;	
	}

	// returns true or false
	function _connect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
	global $php_errormsg;
	
		$php_errormsg = '';
		$this->_connectionID = odbc_connect($argDSN,$argUsername,$argPassword,SQL_CUR_USE_ODBC );
		$this->_errorMsg = $php_errormsg;
		
		$this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
		//if ($this->_connectionID) odbc_autocommit($this->_connectionID,true);
		return $this->_connectionID != false;
	}
	// returns true or false
	function _pconnect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
	global $php_errormsg;
		$php_errormsg = '';
		$this->_connectionID = odbc_pconnect($argDSN,$argUsername,$argPassword,SQL_CUR_USE_ODBC );
		$this->_errorMsg = $php_errormsg;
		
		$this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
		//if ($this->_connectionID) odbc_autocommit($this->_connectionID,true);
		return $this->_connectionID != false;
	}
} 
 
class  ADORecordSet_odbc_oracle extends ADORecordSet_odbc {	
	
	var $databaseType = 'odbc_oracle';
	
	function ADORecordSet_odbc_oracle($id,$mode=false)
	{
		return $this->ADORecordSet_odbc($id,$mode);
	}
}
?>