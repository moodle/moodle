<?php

/*
V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
  
  MySQL code that supports transactions. For MySQL 3.23 or later.
  Code from James Poon <jpoon88@yahoo.com>
  
  Requires mysql client. Works on Windows and Unix.
*/


include_once(ADODB_DIR."/drivers/adodb-mysql.inc.php");


class ADODB_mysqlt extends ADODB_mysql {
	var $databaseType = 'mysqlt';
	var $ansiOuter = true; // for Version 3.23.17 or later
	var $hasTransactions = true;
	
	function BeginTrans()
	{	  
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->Execute('SET AUTOCOMMIT=0');
		$this->Execute('BEGIN');
		return true;
	}
	
	function CommitTrans($ok=true) 
	{
		if ($this->transOff) return true; 
		if (!$ok) return $this->RollbackTrans();
		
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('COMMIT');
		$this->Execute('SET AUTOCOMMIT=1');
		return true;
	}
	
	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('ROLLBACK');
		$this->Execute('SET AUTOCOMMIT=1');
		return true;
	}
	
}

class ADORecordSet_mysqlt extends ADORecordSet_mysql{	
	var $databaseType = "mysqlt";
	
	function ADORecordSet_mysqlt($queryID,$mode=false) {
		return $this->ADORecordSet_mysql($queryID,$mode);
	}
	
	function MoveNext() 
	{	
		if ($this->EOF) return false;

		$this->_currentRow++;
		// using & below slows things down by 20%!
		$this->fields =  @mysql_fetch_array($this->_queryID,$this->fetchMode);
		if ($this->fields) return true;
		$this->EOF = true;
		
		return false;
	}	
}
?>