<?php
/**
* @version V4.93 10 Oct 2006 (c) 2000-2006 John Lim (jlim#natsoft.com.my). All rights reserved.
* Released under both BSD license and Lesser GPL library license.
* Whenever there is any discrepancy between the two licenses,
* the BSD license will take precedence.
*
* Set tabs to 4 for best viewing.
*
* Latest version is available at http://php.weblogs.com
*
* Informix 9 driver that supports SELECT FIRST
*
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR.'/drivers/adodb-informix72.inc.php');

class ADODB_informix extends ADODB_informix72 {
	var $databaseType = "informix";
	var $hasTop = 'FIRST';
	var $ansiOuter = true;
	
	function IfNull( $field, $ifNull )
	{
		return " NVL($field, $ifNull) "; // if Informix 9.X or 10.X
	}
}

class ADORecordset_informix extends ADORecordset_informix72 {
	var $databaseType = "informix";
	
	function ADORecordset_informix($id,$mode=false)
	{
		$this->ADORecordset_informix72($id,$mode);
	}
}
?>