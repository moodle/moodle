<?php
/*
@version   v5.20.3  01-Jan-2016
@copyright (c) 2000-2013 John Lim. All rights reserved.
@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

  Latest version is available at http://adodb.sourceforge.net

  Portable version of oci8 driver, to make it more similar to other database drivers.
  The main differences are

   1. that the OCI_ASSOC names are in lowercase instead of uppercase.
   2. bind variables are mapped using ? instead of :<bindvar>

   Should some emulation of RecordCount() be implemented?

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR.'/drivers/adodb-oci8.inc.php');

class ADODB_oci8quercus extends ADODB_oci8 {
	var $databaseType = 'oci8quercus';
	var $dataProvider = 'oci8';

	function __construct()
	{
	}

}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oci8quercus extends ADORecordset_oci8 {

	var $databaseType = 'oci8quercus';

	function __construct($queryID,$mode=false)
	{
		parent::__construct($queryID,$mode);
	}

	function _FetchField($fieldOffset = -1)
	{
	global $QUERCUS;
		$fld = new ADOFieldObject;

		if (!empty($QUERCUS)) {
			$fld->name = oci_field_name($this->_queryID, $fieldOffset);
			$fld->type = oci_field_type($this->_queryID, $fieldOffset);
			$fld->max_length = oci_field_size($this->_queryID, $fieldOffset);

			//if ($fld->name == 'VAL6_NUM_12_4') $fld->type = 'NUMBER';
			switch($fld->type) {
				case 'string': $fld->type = 'VARCHAR'; break;
				case 'real': $fld->type = 'NUMBER'; break;
			}
		} else {
			$fieldOffset += 1;
			$fld->name = oci_field_name($this->_queryID, $fieldOffset);
			$fld->type = oci_field_type($this->_queryID, $fieldOffset);
			$fld->max_length = oci_field_size($this->_queryID, $fieldOffset);
		}
	 	switch($fld->type) {
		case 'NUMBER':
	 		$p = oci_field_precision($this->_queryID, $fieldOffset);
			$sc = oci_field_scale($this->_queryID, $fieldOffset);
			if ($p != 0 && $sc == 0) $fld->type = 'INT';
			$fld->scale = $p;
			break;

	 	case 'CLOB':
		case 'NCLOB':
		case 'BLOB':
			$fld->max_length = -1;
			break;
		}

		return $fld;
	}

}
