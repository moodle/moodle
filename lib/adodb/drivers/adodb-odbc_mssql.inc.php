<?php
/* 
V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  MSSQL support via ODBC. Requires ODBC. Works on Windows and Unix. 
  For Unix configuration, see http://phpbuilder.com/columns/alberto20000919.php3
*/

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}

 
class  ADODB_odbc_mssql extends ADODB_odbc {	
	var $databaseType = 'odbc_mssql';
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d h:i:sA'";
	var $_bindInputArray = true;
	var $hasTop = 'top';		// support mssql/interbase SELECT TOP 10 * FROM TABLE
	var $sysDate = 'GetDate()';
	var $sysTimeStamp = 'GetDate()';
	
	function ADODB_odbc_mssql()
	{
		$this->ADODB_odbc();
	}
} 
 
class  ADORecordSet_odbc_mssql extends ADORecordSet_odbc {	
	
	var $databaseType = 'odbc_mssql';
	
	function ADORecordSet_odbc_mssql($id)
	{
		return $this->ADORecordSet_odbc($id);
	}
}
?>