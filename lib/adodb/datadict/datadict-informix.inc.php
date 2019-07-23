<?php

/**
  @version   v5.20.14  06-Jan-2019
  @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
  @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

  Set tabs to 4 for best viewing.

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB2_informix extends ADODB_DataDict {

	var $databaseType = 'informix';
	var $seqField = false;


	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';// 255
		case 'XL':
		case 'X': return 'TEXT';

		case 'C2': return 'NVARCHAR';
		case 'X2': return 'TEXT';

		case 'B': return 'BLOB';

		case 'D': return 'DATE';
		case 'TS':
		case 'T': return 'DATETIME YEAR TO SECOND';

		case 'L': return 'SMALLINT';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INTEGER';
		case 'I8': return 'DECIMAL(20)';

		case 'F': return 'FLOAT';
		case 'N': return 'DECIMAL';
		default:
			return $meta;
		}
	}

	function AlterColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		if ($this->debug) ADOConnection::outp("AlterColumnSQL not supported");
		return array();
	}


	function DropColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		if ($this->debug) ADOConnection::outp("DropColumnSQL not supported");
		return array();
	}

	// return string must begin with space
	function _CreateSuffix($fname, &$ftype, $fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{
		if ($fautoinc) {
			$ftype = 'SERIAL';
			return '';
		}
		$suffix = '';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fnotnull) $suffix .= ' NOT NULL';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}

}
