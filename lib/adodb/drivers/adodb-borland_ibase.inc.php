<?php
/* 
V5.08 6 Apr 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Support Borland Interbase 6.5 and later

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR."/drivers/adodb-ibase.inc.php");

class ADODB_borland_ibase extends ADODB_ibase {
	var $databaseType = "borland_ibase";	
	
	
	function ADODB_borland_ibase()
	{
		$this->ADODB_ibase();
	}
	
	function BeginTrans()
	{	 
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->autoCommit = false;
	 	$this->_transactionID = ibase_trans($this->ibasetrans, $this->_connectionID);
		return $this->_transactionID;
	}
	
	function ServerInfo()
	{
		$arr['dialect'] = $this->dialect;
		switch($arr['dialect']) {
		case '': 
		case '1': $s = 'Interbase 6.5, Dialect 1'; break;
		case '2': $s = 'Interbase 6.5, Dialect 2'; break;
		default:
		case '3': $s = 'Interbase 6.5, Dialect 3'; break;
		}
		$arr['version'] = '6.5';
		$arr['description'] = $s;
		return $arr;
	}
	
	// Note that Interbase 6.5 uses ROWS instead - don't you love forking wars!
	// 		SELECT col1, col2 FROM table ROWS 5 -- get 5 rows 
	//		SELECT col1, col2 FROM TABLE ORDER BY col1 ROWS 3 TO 7 -- first 5 skip 2
	// Firebird uses
	//		SELECT FIRST 5 SKIP 2 col1, col2 FROM TABLE
	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0)
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
		
		return ($secs2cache) ? 
				$this->CacheExecute($secs2cache,$sql,$inputarr)
			:
				$this->Execute($sql,$inputarr);
	}
	
};
 

class  ADORecordSet_borland_ibase extends ADORecordSet_ibase {	
	
	var $databaseType = "borland_ibase";		
	
	function ADORecordSet_borland_ibase($id,$mode=false)
	{
		$this->ADORecordSet_ibase($id,$mode);
	}
}
?>