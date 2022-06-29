<?php
/**
 * ADOdb PostgreSQL 7 driver
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

include_once(ADODB_DIR."/drivers/adodb-postgres64.inc.php");

class ADODB_postgres7 extends ADODB_postgres64 {
	var $databaseType = 'postgres7';
	var $hasLimit = true;	// set to true for pgsql 6.5+ only. support pgsql/mysql SELECT * FROM TABLE LIMIT 10
	var $ansiOuter = true;
	var $charSet = true; //set to true for Postgres 7 and above - PG client supports encodings

	// Richard 3/18/2012 - Modified SQL to return SERIAL type correctly AS old driver no longer return SERIAL as data type.
	var $metaColumnsSQL = "
		SELECT
			a.attname,
			CASE
				WHEN x.sequence_name != ''
				THEN 'SERIAL'
				ELSE t.typname
			END AS typname,
			a.attlen, a.atttypmod, a.attnotnull, a.atthasdef, a.attnum
		FROM
			pg_class c,
			pg_attribute a
		JOIN pg_type t ON a.atttypid = t.oid
		LEFT JOIN (
			SELECT
				c.relname as sequence_name,
				c1.relname as related_table,
				a.attname as related_column
			FROM pg_class c
			JOIN pg_depend d ON d.objid = c.oid
			LEFT JOIN pg_class c1 ON d.refobjid = c1.oid
			LEFT JOIN pg_attribute a ON (d.refobjid, d.refobjsubid) = (a.attrelid, a.attnum)
			WHERE c.relkind = 'S' AND c1.relname = '%s'
		) x ON x.related_column= a.attname
		WHERE
			c.relkind in ('r','v')
			AND (c.relname='%s' or c.relname = lower('%s'))
			AND a.attname not like '....%%'
			AND a.attnum > 0
			AND a.attrelid = c.oid
		ORDER BY
			a.attnum";

	// used when schema defined
	var $metaColumnsSQL1 = "
		SELECT
			a.attname,
			CASE
				WHEN x.sequence_name != ''
				THEN 'SERIAL'
				ELSE t.typname
			END AS typname,
			a.attlen, a.atttypmod, a.attnotnull, a.atthasdef, a.attnum
		FROM
			pg_class c,
			pg_namespace n,
			pg_attribute a
		JOIN pg_type t ON a.atttypid = t.oid
		LEFT JOIN (
			SELECT
				c.relname as sequence_name,
				c1.relname as related_table,
				a.attname as related_column
			FROM pg_class c
			JOIN pg_depend d ON d.objid = c.oid
			LEFT JOIN pg_class c1 ON d.refobjid = c1.oid
			LEFT JOIN pg_attribute a ON (d.refobjid, d.refobjsubid) = (a.attrelid, a.attnum)
			WHERE c.relkind = 'S' AND c1.relname = '%s'
		) x ON x.related_column= a.attname
		WHERE
			c.relkind in ('r','v')
			AND (c.relname='%s' or c.relname = lower('%s'))
			AND c.relnamespace=n.oid and n.nspname='%s'
			AND a.attname not like '....%%'
			AND a.attnum > 0
			AND a.atttypid = t.oid
			AND a.attrelid = c.oid
		ORDER BY a.attnum";


	function __construct()
	{
		parent::__construct();
		if (ADODB_ASSOC_CASE !== ADODB_ASSOC_CASE_NATIVE) {
			$this->rsPrefix .= 'assoc_';
		}
		$this->_bindInputArray = true;
	}


	// the following should be compat with postgresql 7.2,
	// which makes obsolete the LIMIT limit,offset syntax
	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0)
	{
		$nrows = (int) $nrows;
		$offset = (int) $offset;
		$offsetStr = ($offset >= 0) ? " OFFSET ".((integer)$offset) : '';
		$limitStr  = ($nrows >= 0)  ? " LIMIT ".((integer)$nrows) : '';
		if ($secs2cache)
			$rs = $this->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr);
		else
			$rs = $this->Execute($sql."$limitStr$offsetStr",$inputarr);

		return $rs;
	}

	/*
	function Prepare($sql)
	{
		$info = $this->ServerInfo();
		if ($info['version']>=7.3) {
			return array($sql,false);
		}
		return $sql;
	}
	*/

	/**
	 * Generate the SQL to retrieve MetaColumns data
	 * @param string $table Table name
	 * @param string $schema Schema name (can be blank)
	 * @return string SQL statement to execute
	 */
	protected function _generateMetaColumnsSQL($table, $schema)
	{
		if ($schema) {
			return sprintf($this->metaColumnsSQL1, $table, $table, $table, $schema);
		}
		else {
			return sprintf($this->metaColumnsSQL, $table, $table, $schema);
		}
	}

	/**
	 * @returns assoc array where keys are tables, and values are foreign keys
	 */
	function MetaForeignKeys($table, $owner=false, $upper=false)
	{
		# Regex isolates the 2 terms between parenthesis using subexpressions
		$regex = '^.*\((.*)\).*\((.*)\).*$';
		$sql="
			SELECT
				lookup_table,
				regexp_replace(consrc, '$regex', '\\2') AS lookup_field,
				dep_table,
				regexp_replace(consrc, '$regex', '\\1') AS dep_field
			FROM (
				SELECT
					pg_get_constraintdef(c.oid) AS consrc,
					t.relname AS dep_table,
					ft.relname AS lookup_table
				FROM pg_constraint c
				JOIN pg_class t ON (t.oid = c.conrelid)
				JOIN pg_class ft ON (ft.oid = c.confrelid)
				JOIN pg_namespace nft ON (nft.oid = ft.relnamespace)
				LEFT JOIN pg_description ds ON (ds.objoid = c.oid)
				JOIN pg_namespace n ON (n.oid = t.relnamespace)
				WHERE c.contype = 'f'::\"char\"
				ORDER BY t.relname, n.nspname, c.conname, c.oid
				) constraints
			WHERE
				dep_table='".strtolower($table)."'
			ORDER BY
				lookup_table,
				dep_table,
				dep_field";
		$rs = $this->Execute($sql);

		if (!$rs || $rs->EOF) return false;

		$a = array();
		while (!$rs->EOF) {
			if ($upper) {
				$a[strtoupper($rs->Fields('lookup_table'))][] = strtoupper(str_replace('"','',$rs->Fields('dep_field').'='.$rs->Fields('lookup_field')));
			} else {
				$a[$rs->Fields('lookup_table')][] = str_replace('"','',$rs->Fields('dep_field').'='.$rs->Fields('lookup_field'));
			}
			$rs->MoveNext();
		}

		return $a;

	}

	// from  Edward Jaramilla, improved version - works on pg 7.4
	function _old_MetaForeignKeys($table, $owner=false, $upper=false)
	{
		$sql = 'SELECT t.tgargs as args
		FROM
		pg_trigger t,pg_class c,pg_proc p
		WHERE
		t.tgenabled AND
		t.tgrelid = c.oid AND
		t.tgfoid = p.oid AND
		p.proname = \'RI_FKey_check_ins\' AND
		c.relname = \''.strtolower($table).'\'
		ORDER BY
			t.tgrelid';

		$rs = $this->Execute($sql);

		if (!$rs || $rs->EOF) return false;

		$arr = $rs->GetArray();
		$a = array();
		foreach($arr as $v) {
			$data = explode(chr(0), $v['args']);
			$size = count($data)-1; //-1 because the last node is empty
			for($i = 4; $i < $size; $i++) {
				if ($upper)
					$a[strtoupper($data[2])][] = strtoupper($data[$i].'='.$data[++$i]);
				else
					$a[$data[2]][] = $data[$i].'='.$data[++$i];
			}
		}
		return $a;
	}

	function _query($sql,$inputarr=false)
	{
		if (! $this->_bindInputArray) {
			// We don't have native support for parameterized queries, so let's emulate it at the parent
			return ADODB_postgres64::_query($sql, $inputarr);
		}

		$this->_pnum = 0;
		$this->_errorMsg = false;
		// -- added Cristiano da Cunha Duarte
		if ($inputarr) {
			$sqlarr = explode('?',trim($sql));
			$sql = '';
			$i = 1;
			$last = sizeof($sqlarr)-1;
			foreach($sqlarr as $v) {
				if ($last < $i) $sql .= $v;
				else $sql .= $v.' $'.$i;
				$i++;
			}

			$rez = pg_query_params($this->_connectionID,$sql, $inputarr);
		} else {
			$rez = pg_query($this->_connectionID,$sql);
		}
		// check if no data returned, then no need to create real recordset
		if ($rez && pg_num_fields($rez) <= 0) {
			if (is_resource($this->_resultid) && get_resource_type($this->_resultid) === 'pgsql result') {
				pg_free_result($this->_resultid);
			}
			$this->_resultid = $rez;
			return true;
		}
		return $rez;
	}

	/**
	 * Retrieve the client connection's current character set.

	 * If no charsets were compiled into the server, the function will always
	 * return 'SQL_ASCII'.
	 * @see https://www.php.net/manual/en/function.pg-client-encoding.php
	 *
	 * @return string|false The character set, or false if it can't be determined.
	 */
	function getCharSet()
	{
		if (!$this->_connectionID) {
			return false;
		}
		$this->charSet = pg_client_encoding($this->_connectionID);
		return $this->charSet ?: false;
	}

	/**
	 * Sets the client-side character set (encoding).
	 *
	 * Allows managing client encoding - very important if the database and
	 * the output target (i.e. HTML) don't match; for instance, you may have a
	 * UNICODE database and server your pages as WIN1251, etc.
	 *
	 * Supported on PostgreSQL 7.0 and above. Available charsets depend on
	 * PostgreSQL version and the distribution's compile flags.
	 *
	 * @param string $charset The character set to switch to.
	 *
	 * @return bool True if the character set was changed successfully, false otherwise.
	 */
	function setCharSet($charset)
	{
		if ($this->charSet !== $charset) {
			if (!$this->_connectionID || pg_set_client_encoding($this->_connectionID, $charset) != 0) {
				return false;
			}
			$this->getCharSet();
		}
		return true;
	}

}

/*--------------------------------------------------------------------------------------
	Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_postgres7 extends ADORecordSet_postgres64{

	var $databaseType = "postgres7";

	// 10% speedup to move MoveNext to child class
	function MoveNext()
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_numOfRows < 0 || $this->_numOfRows > $this->_currentRow) {
				$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);

				if (is_array($this->fields)) {
					if ($this->fields && isset($this->_blobArr)) $this->_fixblobs();
					return true;
				}
			}
			$this->fields = false;
			$this->EOF = true;
		}
		return false;
	}

}

class ADORecordSet_assoc_postgres7 extends ADORecordSet_postgres64{

	var $databaseType = "postgres7";


	function _fetch()
	{
		if ($this->_currentRow >= $this->_numOfRows && $this->_numOfRows >= 0) {
			return false;
		}

		$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);

		if ($this->fields) {
			if (isset($this->_blobArr)) $this->_fixblobs();
			$this->_updatefields();
		}

		return (is_array($this->fields));
	}

	function MoveNext()
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_numOfRows < 0 || $this->_numOfRows > $this->_currentRow) {
				$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);

				if (is_array($this->fields)) {
					if ($this->fields) {
						if (isset($this->_blobArr)) $this->_fixblobs();

						$this->_updatefields();
					}
					return true;
				}
			}


			$this->fields = false;
			$this->EOF = true;
		}
		return false;
	}
}
