<?php
/**
 * Oracle "quercus" driver.
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR.'/drivers/adodb-oci8.inc.php');

class ADODB_oci8quercus extends ADODB_oci8 {
	var $databaseType = 'oci8quercus';
	var $dataProvider = 'oci8';

}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oci8quercus extends ADORecordset_oci8 {

	var $databaseType = 'oci8quercus';

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
