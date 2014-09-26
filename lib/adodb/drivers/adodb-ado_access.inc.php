<?php
/*
V5.19  23-Apr-2014  (c) 2000-2014 John Lim (jlim#natsoft.com). All rights reserved.
Released under both BSD license and Lesser GPL library license.
Whenever there is any discrepancy between the two licenses,
the BSD license will take precedence. See License.txt.
Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.sourceforge.net

	Microsoft Access ADO data driver. Requires ADO and ODBC. Works only on MS Windows.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ADO_LAYER')) {
	if (PHP_VERSION >= 5) include(ADODB_DIR."/drivers/adodb-ado5.inc.php");
	else include(ADODB_DIR."/drivers/adodb-ado.inc.php");
}

class  ADODB_ado_access extends ADODB_ado {
	var $databaseType = 'ado_access';
	var $hasTop = 'top';		// support mssql SELECT TOP 10 * FROM TABLE
	var $fmtDate = "#Y-m-d#";
	var $fmtTimeStamp = "#Y-m-d h:i:sA#";// note no comma
	var $sysDate = "FORMAT(NOW,'yyyy-mm-dd')";
	var $sysTimeStamp = 'NOW';
	var $upperCase = 'ucase';

	function ADODB_ado_access()
	{
		$this->ADODB_ado();
	}

	/*function BeginTrans() { return false;}

	function CommitTrans() { return false;}

	function RollbackTrans() { return false;}*/

}


class  ADORecordSet_ado_access extends ADORecordSet_ado {

	var $databaseType = "ado_access";

	function ADORecordSet_ado_access($id,$mode=false)
	{
		return $this->ADORecordSet_ado($id,$mode);
	}
}
