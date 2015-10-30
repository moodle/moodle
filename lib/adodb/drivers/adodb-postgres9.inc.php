<?php
/*
 V5.14 8 Sept 2011  (c) 2000-2011 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 4.
  
  Postgres9 support.
  01 Dec 2011: gherteg added support for retrieving insert IDs from tables without OIDs
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR."/drivers/adodb-postgres7.inc.php");

class ADODB_postgres9 extends ADODB_postgres7 {
	var $databaseType = 'postgres9';	
	
	function ADODB_postgres9() 
	{
		$this->ADODB_postgres7();
	}

	// Don't use OIDs, as they typically won't be there, and
	// they're not what the application wants back, anyway.
	function _insertid($table,$column)
	{
		return empty($table) || empty($column)
			? $this->GetOne("SELECT lastval()")
			: $this->GetOne("SELECT currval(pg_get_serial_sequence('$table','$column'))");
	}
}

/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_postgres9 extends ADORecordSet_postgres7{
	var $databaseType = "postgres9";
	
	function ADORecordSet_postgres9($queryID,$mode=false) 
	{
		$this->ADORecordSet_postgres7($queryID,$mode);
	}
}

class ADORecordSet_assoc_postgres9 extends ADORecordSet_postgres7{
	var $databaseType = "postgres9";
	
	function ADORecordSet_assoc_postgres9($queryID,$mode=false) 
	{
		$this->ADORecordSet_postgres7($queryID,$mode);
	}
}
?>
