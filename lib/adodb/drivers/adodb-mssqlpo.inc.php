<?php
/**
* @version V4.98 13 Feb 2008 (c) 2000-2008 John Lim (jlim#natsoft.com.my). All rights reserved.
* Released under both BSD license and Lesser GPL library license.
* Whenever there is any discrepancy between the two licenses,
* the BSD license will take precedence.
*
* Set tabs to 4 for best viewing.
*
* Latest version is available at http://php.weblogs.com
*
*  Portable MSSQL Driver that supports || instead of +
*
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();


/*
	The big difference between mssqlpo and it's parent mssql is that mssqlpo supports
	the more standard || string concatenation operator.
*/
	
include_once(ADODB_DIR.'/drivers/adodb-mssql.inc.php');

class ADODB_mssqlpo extends ADODB_mssql {
	var $databaseType = "mssqlpo";
	var $concat_operator = '||'; 
	
	function ADODB_mssqlpo()
	{
		ADODB_mssql::ADODB_mssql();
	}

	function PrepareSP($sql)
	{
		if (!$this->_has_mssql_init) {
			ADOConnection::outp( "PrepareSP: mssql_init only available since PHP 4.1.0");
			return $sql;
		}
		if (is_string($sql)) $sql = str_replace('||','+',$sql);
		$stmt = mssql_init($sql,$this->_connectionID);
		if (!$stmt)  return $sql;
		return array($sql,$stmt);
	}
	
	function _query($sql,$inputarr)
	{
		if (is_string($sql)) $sql = str_replace('||','+',$sql);
		return ADODB_mssql::_query($sql,$inputarr);
	}
}

class ADORecordset_mssqlpo extends ADORecordset_mssql {
	var $databaseType = "mssqlpo";
	function ADORecordset_mssqlpo($id,$mode=false)
	{
		$this->ADORecordset_mssql($id,$mode);
	}
}
?>