<?php
/* 
V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  Support Borland Interbase 6.5 and later

*/

include_once(ADODB_DIR."/drivers/adodb-ibase.inc.php");

class ADODB_borland_ibase extends ADODB_ibase {
	var $databaseType = "borland_ibase";	
	
	function ADODB_borland_ibase()
	{
		$this->ADODB_ibase();
	}
	
	// Note that Interbase 6.5 uses ROWS instead - don't you love forking wars!
	// 		SELECT col1, col2 FROM table ROWS 5 -- get 5 rows 
	//		SELECT col1, col2 FROM TABLE ORDER BY col1 ROWS 3 TO 7 -- first 5 skip 2
	// Firebird uses
	//		SELECT FIRST 5 SKIP 2 col1, col2 FROM TABLE
	function &SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false, $arg3=false,$secs=0)
	{
		if ($nrows > 0) {
			if ($offset <= 0) $str = " ROWS $nrows "; 
			else {
				$a = $offset+1;
				$b = $offset+$nrows;
				$str = " ROWS $a TO $b";
			}
		} else {
			// ok, skip 
			$a = $offset + 1;
			$str = " ROWS $a TO 999999999"; // 999 million
		}
		$sql .= $str;
		
		return ($secs) ? 
				$this->CacheExecute($secs,$sql,$inputarr,$arg3)
			:
				$this->Execute($sql,$inputarr,$arg3);
	}
	
};
 

class  ADORecordSet_borland_ibase extends ADORecordSet_ibase {	
	
	var $databaseType = "borland_ibase";		
	
	function ADORecordSet_borland_ibase($id)
	{
		$this->ADORecordSet_ibase($id);
	}
}
?>