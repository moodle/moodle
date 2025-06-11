<?php
/**
 * ADOdb PostgreSQL 8 driver
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

include_once(ADODB_DIR."/drivers/adodb-postgres7.inc.php");

class ADODB_postgres8 extends ADODB_postgres7
{
	var $databaseType = 'postgres8';

	// From PostgreSQL 8.0 onwards, the adsrc column used in earlier versions to
	// retrieve the default value is obsolete and should not be used (see #562).
	var $metaDefaultsSQL = "SELECT d.adnum as num, pg_get_expr(d.adbin, d.adrelid) as def
		FROM pg_attrdef d, pg_class c
		WHERE d.adrelid=c.oid AND c.relname='%s'
		ORDER BY d.adnum";

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
	protected function _insertID( $table = '', $column = '' )
	{
		global $ADODB_GETONE_EOF;

		$sql = empty($table) || empty($column)
			? 'SELECT lastval()'
			: "SELECT currval(pg_get_serial_sequence('$table', '$column'))";

		// Squelch "ERROR:  lastval is not yet defined in this session" (see #978)
		$result = @$this->GetOne($sql);
		if ($result === false || $result == $ADODB_GETONE_EOF) {
			if ($this->debug) {
				ADOConnection::outp(__FUNCTION__ . "() failed : " . $this->errorMsg());
			}
			return false;
		}
		return $result;
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
