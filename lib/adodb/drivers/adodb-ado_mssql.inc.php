<?php
/* 
V4.01 23 Oct 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  Microsoft SQL Server ADO data driver. Requires ADO and MSSQL client. 
  Works only on MS Windows.
  
  It is normally better to use the mssql driver directly because it is much faster. 
  This file is only a technology demonstration and for test purposes.
*/

if (!defined('_ADODB_ADO_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-ado.inc.php");
}

class  ADODB_ado_mssql extends ADODB_ado {	
	var $databaseType = 'ado_mssql';
	var $hasTop = 'top';
	var $sysDate = 'GetDate()';
	var $sysTimeStamp = 'GetDate()';
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $ansiOuter = true; // for mssql7 or later
	
	//var $_inTransaction = 1; // always open recordsets, so no transaction problems.
	
	function ADODB_ado_mssql()
	{
		$this->ADODB_ado();
	}
	
	function _insertid()
	{
		return $this->GetOne('select @@identity');
	}
	
	function _affectedrows()
	{
		return $this->GetOne('select @@rowcount');
	}
	
}
 
class  ADORecordSet_ado_mssql extends ADORecordSet_ado {	
	
	var $databaseType = 'ado_mssql';
	
	function ADORecordSet_ado_mssql($id,$mode=false)
	{
		return $this->ADORecordSet_ado($id,$mode);
	}
}
?>