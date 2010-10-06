<?php


/*
V5.11 5 May 2010   (c) 2000-2010 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
 
*/ 

class ADODB_pdo_mssql extends ADODB_pdo {
	
	var $hasTop = 'top';
	var $sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
	var $sysTimeStamp = 'GetDate()';
	
	
	function _init($parentDriver)
	{
	
		$parentDriver->hasTransactions = false; ## <<< BUG IN PDO mssql driver
		$parentDriver->_bindInputArray = false;
		$parentDriver->hasInsertID = true;
	}
	
	function ServerInfo()
	{
		return ADOConnection::ServerInfo();
	}
	
	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0)
	{
		$ret = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);
		return $ret;
	}
	
	function SetTransactionMode( $transaction_mode ) 
	{
		$this->_transmode  = $transaction_mode;
		if (empty($transaction_mode)) {
			$this->Execute('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
			return;
		}
		if (!stristr($transaction_mode,'isolation')) $transaction_mode = 'ISOLATION LEVEL '.$transaction_mode;
		$this->Execute("SET TRANSACTION ".$transaction_mode);
	}
	
	function MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{
		return false;
	}
	
	function MetaColumns($table,$normalize=true)
	{
		return false;
	}

}
?>