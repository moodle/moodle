<?php
/**
 * Data Dictionary for SAP DB.
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

class ADODB2_sapdb extends ADODB_DataDict {

	var $databaseType = 'sapdb';
	var $seqField = false;
	var $renameColumn = 'RENAME COLUMN %s.%s TO %s';

 	function ActualType($meta)
	{
		$meta = strtoupper($meta);
		
		/*
		* Add support for custom meta types. We do this
		* first, that allows us to override existing types
		*/
		if (isset($this->connection->customMetaTypes[$meta]))
			return $this->connection->customMetaTypes[$meta]['actual'];
		
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'XL':
		case 'X': return 'LONG';

		case 'C2': return 'VARCHAR UNICODE';
		case 'X2': return 'LONG UNICODE';

		case 'B': return 'LONG';

		case 'D': return 'DATE';
		case 'TS':
		case 'T': return 'TIMESTAMP';

		case 'L': return 'BOOLEAN';
		case 'I': return 'INTEGER';
		case 'I1': return 'FIXED(3)';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INTEGER';
		case 'I8': return 'FIXED(20)';

		case 'F': return 'FLOAT(38)';
		case 'N': return 'FIXED';
		default:
			return $meta;
		}
	}

	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		
		$t = strtoupper($t);
		
		if (array_key_exists($t,$this->connection->customActualTypes))
			return  $this->connection->customActualTypes[$t];

		static $maxdb_type2adodb = array(
			'VARCHAR'	=> 'C',
			'CHARACTER'	=> 'C',
			'LONG'		=> 'X',		// no way to differ between 'X' and 'B' :-(
			'DATE'		=> 'D',
			'TIMESTAMP'	=> 'T',
			'BOOLEAN'	=> 'L',
			'INTEGER'	=> 'I4',
			'SMALLINT'	=> 'I2',
			'FLOAT'		=> 'F',
			'FIXED'		=> 'N',
		);
		$type = isset($maxdb_type2adodb[$t]) ? $maxdb_type2adodb[$t] : ADODB_DEFAULT_METATYPE;

		// convert integer-types simulated with fixed back to integer
		if ($t == 'FIXED' && !$fieldobj->scale && ($len == 20 || $len == 3)) {
			$type = $len == 20 ? 'I8' : 'I1';
		}
		if ($fieldobj->auto_increment) $type = 'R';

		return $type;
	}

	// return string must begin with space
	function _createSuffix($fname, &$ftype, $fnotnull, $fdefault, $fautoinc, $fconstraint, $funsigned, $fprimary, &$pkey)
	{
		$suffix = '';
		if ($funsigned) $suffix .= ' UNSIGNED';
		if ($fnotnull) $suffix .= ' NOT NULL';
		if ($fautoinc) $suffix .= ' DEFAULT SERIAL';
		elseif (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}

	function AddColumnSQL($tabname, $flds)
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		return array( 'ALTER TABLE ' . $tabname . ' ADD (' . implode(', ',$lines) . ')' );
	}

	function AlterColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		return array( 'ALTER TABLE ' . $tabname . ' MODIFY (' . implode(', ',$lines) . ')' );
	}

	function DropColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		$tabname = $this->TableName ($tabname);
		if (!is_array($flds)) $flds = explode(',',$flds);
		foreach($flds as $k => $v) {
			$flds[$k] = $this->NameQuote($v);
		}
		return array( 'ALTER TABLE ' . $tabname . ' DROP (' . implode(', ',$flds) . ')' );
	}
}
