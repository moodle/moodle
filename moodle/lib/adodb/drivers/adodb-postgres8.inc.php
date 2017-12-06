<?php
/*
 @version   v5.20.7  20-Sep-2016
 @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
 @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 4.

  Postgres8 support.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR."/drivers/adodb-postgres7.inc.php");

class ADODB_postgres8 extends ADODB_postgres7
{
	var $databaseType = 'postgres8';


	/**
	 * Retrieve last inserted ID
	 * Don't use OIDs, since as per {@link http://php.net/function.pg-last-oid php manual }
	 * they won't be there in Postgres 8.1
	 * (and they're not what the application wants back, anyway).
	 * @param string $table
	 * @param string $column
	 * @return int last inserted ID for given table/column, or the most recently
	 *             returned one if $table or $column are empty
	 */
	function _insertid($table, $column)
	{
		return empty($table) || empty($column)
			? $this->GetOne("SELECT lastval()")
			: $this->GetOne("SELECT currval(pg_get_serial_sequence('$table', '$column'))");
	}
}

class ADORecordSet_postgres8 extends ADORecordSet_postgres7
{
	var $databaseType = "postgres8";
}

class ADORecordSet_assoc_postgres8 extends ADORecordSet_assoc_postgres7
{
	var $databaseType = "postgres8";
}
