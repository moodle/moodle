<?php

/**
 * Provided by Ned Andre to support sqlsrv library
 */
class ADODB_pdo_sqlsrv extends ADODB_pdo
{

	var $hasTop = 'top';
	var $sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
	var $sysTimeStamp = 'GetDate()';

	function _init(ADODB_pdo $parentDriver)
	{
		$parentDriver->hasTransactions = true;
		$parentDriver->_bindInputArray = true;
		$parentDriver->hasInsertID = true;
		$parentDriver->fmtTimeStamp = "'Y-m-d H:i:s'";
		$parentDriver->fmtDate = "'Y-m-d'";
	}

	function BeginTrans()
	{
		$returnval = parent::BeginTrans();
		return $returnval;
	}

	function MetaColumns($table, $normalize = true)
	{
		return false;
	}

	function MetaTables($ttype = false, $showSchema = false, $mask = false)
	{
		return false;
	}

	function SelectLimit($sql, $nrows = -1, $offset = -1, $inputarr = false, $secs2cache = 0)
	{
		$ret = ADOConnection::SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);
		return $ret;
	}

	function ServerInfo()
	{
		return ADOConnection::ServerInfo();
	}

}
