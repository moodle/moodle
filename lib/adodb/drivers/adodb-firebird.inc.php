<?php
/* 
V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/

*/


include_once(ADODB_DIR."/drivers/adodb-ibase.inc.php");

class ADODB_firebird extends ADODB_ibase {
	var $databaseType = "firebird";	
	
	function ADODB_firebird()
	{	
		$this->ADODB_ibase();
	}
	
	/*  Note that Interbase 6.5 uses this ROWS instead - don't you love forking wars! */
	/*  		SELECT col1, col2 FROM table ROWS 5 -- get 5 rows  */
	/* 		SELECT col1, col2 FROM TABLE ORDER BY col1 ROWS 3 TO 7 -- first 5 skip 2 */
	function &SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false, $arg3=false,$secs=0)
	{
		$str = 'SELECT ';
		if ($nrows >= 0) $str .= "FIRST $nrows "; 
		$str .=($offset>=0) ? "SKIP $offset " : '';
		
		$sql = preg_replace('/^[ \t]*select/i',$str,$sql); 
		return ($secs) ? 
				$this->CacheExecute($secs,$sql,$inputarr,$arg3)
			: 	
				$this->Execute($sql,$inputarr,$arg3);
	}
	
};
 

class  ADORecordSet_firebird extends ADORecordSet_ibase {	
	
	var $databaseType = "firebird";		
	
	function ADORecordSet_firebird($id)
	{
		$this->ADORecordSet_ibase($id);
	}
}
?>