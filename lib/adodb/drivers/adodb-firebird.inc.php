<?php
/*
@version   v5.20.3  01-Jan-2016
@copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.sourceforge.net

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR."/drivers/adodb-ibase.inc.php");

class ADODB_firebird extends ADODB_ibase {
	var $databaseType = "firebird";
	var $dialect = 3;

	var $sysTimeStamp = "CURRENT_TIMESTAMP"; //"cast('NOW' as timestamp)";

	function ServerInfo()
	{
		$arr['dialect'] = $this->dialect;
		switch($arr['dialect']) {
		case '':
		case '1': $s = 'Firebird Dialect 1'; break;
		case '2': $s = 'Firebird Dialect 2'; break;
		default:
		case '3': $s = 'Firebird Dialect 3'; break;
		}
		$arr['version'] = ADOConnection::_findvers($s);
		$arr['description'] = $s;
		return $arr;
	}

	// Note that Interbase 6.5 uses this ROWS instead - don't you love forking wars!
	// 		SELECT col1, col2 FROM table ROWS 5 -- get 5 rows
	//		SELECT col1, col2 FROM TABLE ORDER BY col1 ROWS 3 TO 7 -- first 5 skip 2
	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false, $secs=0)
	{
		$nrows = (integer) $nrows;
		$offset = (integer) $offset;
		$str = 'SELECT ';
		if ($nrows >= 0) $str .= "FIRST $nrows ";
		$str .=($offset>=0) ? "SKIP $offset " : '';

		$sql = preg_replace('/^[ \t]*select/i',$str,$sql);
		if ($secs)
			$rs = $this->CacheExecute($secs,$sql,$inputarr);
		else
			$rs = $this->Execute($sql,$inputarr);

		return $rs;
	}


};


class  ADORecordSet_firebird extends ADORecordSet_ibase {

	var $databaseType = "firebird";

	function __construct($id,$mode=false)
	{
		parent::__construct($id,$mode);
	}
}
