<?php
/**
 * Data Dictionary for PostgreSQL.
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

class ADODB2_postgres extends ADODB_DataDict
{
	var $databaseType = 'postgres';
	var $seqField = false;
	var $seqPrefix = 'SEQ_';
	var $addCol = ' ADD COLUMN';
	var $quote = '"';
	var $renameTable = 'ALTER TABLE %s RENAME TO %s'; // at least since 7.1
	var $dropTable = 'DROP TABLE %s CASCADE';

	public $blobAllowsDefaultValue = true;
	public $blobAllowsNotNull = true;

	function metaType($t, $len=-1, $fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}

		$t = strtoupper($t);

		if (array_key_exists($t,$this->connection->customActualTypes))
			return  $this->connection->customActualTypes[$t];

		$is_serial = is_object($fieldobj) && !empty($fieldobj->primary_key) && !empty($fieldobj->unique) &&
			!empty($fieldobj->has_default) && substr($fieldobj->default_value,0,8) == 'nextval(';

		switch ($t) {

			case 'INTERVAL':
			case 'CHAR':
			case 'CHARACTER':
			case 'VARCHAR':
			case 'NAME':
			case 'BPCHAR':
				if ($len <= $this->blobSize) return 'C';

			case 'TEXT':
				return 'X';

			case 'IMAGE': // user defined type
			case 'BLOB': // user defined type
			case 'BIT':	// This is a bit string, not a single bit, so don't return 'L'
			case 'VARBIT':
			case 'BYTEA':
				return 'B';

			case 'BOOL':
			case 'BOOLEAN':
				return 'L';

			case 'DATE':
				return 'D';

			case 'TIME':
			case 'DATETIME':
			case 'TIMESTAMP':
			case 'TIMESTAMPTZ':
				return 'T';

			case 'INTEGER': return !$is_serial ? 'I' : 'R';
			case 'SMALLINT':
			case 'INT2': return !$is_serial ? 'I2' : 'R';
			case 'INT4': return !$is_serial ? 'I4' : 'R';
			case 'BIGINT':
			case 'INT8': return !$is_serial ? 'I8' : 'R';

			case 'OID':
			case 'SERIAL':
				return 'R';

			case 'FLOAT4':
			case 'FLOAT8':
			case 'DOUBLE PRECISION':
			case 'REAL':
				return 'F';

			default:
				return ADODB_DEFAULT_METATYPE;
		}
	}

	function actualType($meta)
	{
		$meta = strtoupper($meta);

		/*
		* Add support for custom meta types. We do this
		* first, that allows us to override existing types
		*/
		if (isset($this->connection->customMetaTypes[$meta]))
			return $this->connection->customMetaTypes[$meta]['actual'];

		switch ($meta) {
		case 'C': return 'VARCHAR';
		case 'XL':
		case 'X': return 'TEXT';

		case 'C2': return 'VARCHAR';
		case 'X2': return 'TEXT';

		case 'B': return 'BYTEA';

		case 'D': return 'DATE';
		case 'TS':
		case 'T': return 'TIMESTAMP';

		case 'L': return 'BOOLEAN';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'INT2';
		case 'I4': return 'INT4';
		case 'I8': return 'INT8';

		case 'F': return 'FLOAT8';
		case 'N': return 'NUMERIC';
		default:
			return $meta;
		}
	}

	/**
	 * Adding a new Column
	 *
	 * reimplementation of the default function as postgres does NOT allow to set the default in the same statement
	 *
	 * @param string $tabname table-name
	 * @param string $flds column-names and types for the changed columns
	 * @return array with SQL strings
	 */
	function addColumnSQL($tabname, $flds)
	{
		$tabname = $this->tableName($tabname);
		$sql = array();
		$not_null = false;
		list($lines,$pkey) = $this->_genFields($flds);
		$alter = 'ALTER TABLE ' . $tabname . $this->addCol;
		$alter .= (float)@$this->serverInfo['version'] < 9.6 ? ' ' : ' IF NOT EXISTS ';
		foreach($lines as $v) {
			if (($not_null = preg_match('/NOT NULL/i',$v))) {
				$v = preg_replace('/NOT NULL/i','',$v);
			}
			if (preg_match('/^([^ ]+) .*DEFAULT (\'[^\']+\'|\"[^\"]+\"|[^ ]+)/',$v,$matches)) {
				list(,$colname,$default) = $matches;
				$sql[] = $alter . str_replace('DEFAULT '.$default,'',$v);
				$sql[] = 'UPDATE '.$tabname.' SET '.$colname.'='.$default.' WHERE '.$colname.' IS NULL ';
				$sql[] = 'ALTER TABLE '.$tabname.' ALTER COLUMN '.$colname.' SET DEFAULT ' . $default;
			} else {
				$sql[] = $alter . $v;
			}
			if ($not_null) {
				list($colname) = explode(' ',$v);
				$sql[] = 'ALTER TABLE '.$tabname.' ALTER COLUMN '.$colname.' SET NOT NULL';
			}
		}
		return $sql;
	}


	function dropIndexSQL($idxname, $tabname = NULL)
	{
		return array(sprintf($this->dropIndex, $this->tableName($idxname), $this->tableName($tabname)));
	}

	/**
	 * Change the definition of one column
	 *
	 * Postgres can't do that on its own, you need to supply the complete
	 * definition of the new table, to allow recreating the table and copying
	 * the content over to the new table.
	 *
	 * @param string $tabname      table-name
	 * @param string $flds         column-name and type for the changed column
	 * @param string $tableflds    complete definition of the new table, e.g. for postgres, default ''
	 * @param array  $tableoptions options for the new table {@see CreateTableSQL()}, default ''
	 *
	 * @return array with SQL strings
	 */
	function alterColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		// Check if alter single column datatype available - works with 8.0+
		$has_alter_column = 8.0 <= (float) @$this->serverInfo['version'];

		if ($has_alter_column) {
			$tabname = $this->tableName($tabname);
			$sql = array();
			list($lines,$pkey) = $this->_genFields($flds);
			$set_null = false;
			foreach($lines as $v) {
				$alter = 'ALTER TABLE ' . $tabname . $this->alterCol . ' ';
				if ($not_null = preg_match('/NOT NULL/i',$v)) {
					$v = preg_replace('/NOT NULL/i','',$v);
				}
				// this next block doesn't work - there is no way that I can see to
				// explicitly ask a column to be null using $flds
				elseif ($set_null = preg_match('/NULL/i',$v)) {
					// if they didn't specify not null, see if they explicitly asked for null
					// Lookbehind pattern covers the case 'fieldname NULL datatype DEFAULT NULL'
					// only the first NULL should be removed, not the one specifying
					// the default value
					$v = preg_replace('/(?<!DEFAULT)\sNULL/i','',$v);
				}

				if (preg_match('/^([^ ]+) .*DEFAULT (\'[^\']+\'|\"[^\"]+\"|[^ ]+)/',$v,$matches)) {
					$existing = $this->metaColumns($tabname);
					list(,$colname,$default) = $matches;
					$alter .= $colname;
					if ($this->connection) {
						$old_coltype = $this->connection->metaType($existing[strtoupper($colname)]);
					} else {
						$old_coltype = $t;
					}
					$v = preg_replace('/^' . preg_quote($colname) . '\s/', '', $v);
					$t = trim(str_replace('DEFAULT '.$default,'',$v));

					// Type change from bool to int
					if ( $old_coltype == 'L' && $t == 'INTEGER' ) {
						$sql[] = $alter . ' DROP DEFAULT';
						$sql[] = $alter . " TYPE $t USING ($colname::BOOL)::INT";
						$sql[] = $alter . " SET DEFAULT $default";
					}
					// Type change from int to bool
					else if ( $old_coltype == 'I' && $t == 'BOOLEAN' ) {
						if( strcasecmp('NULL', trim($default)) != 0 ) {
							$default = $this->connection->qstr($default);
						}
						$sql[] = $alter . ' DROP DEFAULT';
						$sql[] = $alter . " TYPE $t USING CASE WHEN $colname = 0 THEN false ELSE true END";
						$sql[] = $alter . " SET DEFAULT $default";
					}
					// Any other column types conversion
					else {
						$sql[] = $alter . " TYPE $t";
						$sql[] = $alter . " SET DEFAULT $default";
					}

				}
				else {
					// drop default?
					preg_match ('/^\s*(\S+)\s+(.*)$/',$v,$matches);
					list (,$colname,$rest) = $matches;
					$alter .= $colname;
					$sql[] = $alter . ' TYPE ' . $rest;
				}

				#list($colname) = explode(' ',$v);
				if ($not_null) {
					// this does not error out if the column is already not null
					$sql[] = $alter . ' SET NOT NULL';
				}
				if ($set_null) {
					// this does not error out if the column is already null
					$sql[] = $alter . ' DROP NOT NULL';
				}
			}
			return $sql;
		}

		// does not have alter column
		if (!$tableflds) {
			if ($this->debug) ADOConnection::outp("AlterColumnSQL needs a complete table-definiton for PostgreSQL");
			return array();
		}
		return $this->_recreate_copy_table($tabname, false, $tableflds,$tableoptions);
	}

	/**
	 * Drop one column
	 *
	 * Postgres < 7.3 can't do that on it's own, you need to supply the complete definition of the new table,
	 * to allow, recreating the table and copying the content over to the new table
	 * @param string $tabname table-name
	 * @param string $flds column-name and type for the changed column
	 * @param string $tableflds complete definition of the new table, eg. for postgres, default ''
	 * @param array  $tableoptions options for the new table {@see CreateTableSQL}, default []
	 * @return array with SQL strings
	 */
	function dropColumnSQL($tabname, $flds, $tableflds='', $tableoptions='')
	{
		$has_drop_column = 7.3 <= (float) @$this->serverInfo['version'];
		if (!$has_drop_column && !$tableflds) {
			if ($this->debug) {
				ADOConnection::outp("dropColumnSQL needs complete table-definiton for PostgreSQL < 7.3");
			}
			return array();
		}
		if ($has_drop_column) {
			return ADODB_DataDict::dropColumnSQL($tabname, $flds);
		}
		return $this->_recreate_copy_table($tabname, $flds, $tableflds, $tableoptions);
	}

	/**
	 * Save the content into a temp. table, drop and recreate the original table and copy the content back in
	 *
	 * We also take care to set the values of the sequenz and recreate the indexes.
	 * All this is done in a transaction, to not loose the content of the table, if something went wrong!
	 * @internal
	 * @param string $tabname table-name
	 * @param string $dropflds column-names to drop
	 * @param string $tableflds complete definition of the new table, eg. for postgres
	 * @param array|string $tableoptions options for the new table see CreateTableSQL, default ''
	 * @return array with SQL strings
	 */
	function _recreate_copy_table($tabname, $dropflds, $tableflds, $tableoptions='')
	{
		if ($dropflds && !is_array($dropflds)) $dropflds = explode(',',$dropflds);
		$copyflds = array();
		foreach($this->metaColumns($tabname) as $fld) {
			if (preg_match('/'.$fld->name.' (\w+)/i', $tableflds, $matches)) {
				$new_type = strtoupper($matches[1]);
				// AlterColumn of a char column to a nummeric one needs an explicit conversation
				if (in_array($new_type, array('I', 'I2', 'I4', 'I8', 'N', 'F')) &&
					in_array($fld->type, array('varchar','char','text','bytea'))
				) {
					$copyflds[] = "to_number($fld->name,'S9999999999999D99')";
				} else {
					// other column-type changes needs explicit decode, encode for bytea or cast otherwise
					$new_actual_type = $this->actualType($new_type);
					if (strtoupper($fld->type) != $new_actual_type) {
						if ($new_actual_type == 'BYTEA' && $fld->type == 'text') {
							$copyflds[] = "DECODE($fld->name, 'escape')";
						} elseif ($fld->type == 'bytea' && $new_actual_type == 'TEXT') {
							$copyflds[] = "ENCODE($fld->name, 'escape')";
						} else {
							$copyflds[] = "CAST($fld->name AS $new_actual_type)";
						}
					}
				}
			} else {
				$copyflds[] = $fld->name;
			}
			// identify the sequence name and the fld its on
			if ($fld->primary_key && $fld->has_default &&
				preg_match("/nextval\('([^']+)'::(text|regclass)\)/", $fld->default_value, $matches)) {
				$seq_name = $matches[1];
				$seq_fld = $fld->name;
			}
		}
		$copyflds = implode(', ', $copyflds);

		$tempname = $tabname.'_tmp';
		$aSql[] = 'BEGIN';		// we use a transaction, to make sure not to loose the content of the table
		$aSql[] = "SELECT * INTO TEMPORARY TABLE $tempname FROM $tabname";
		$aSql = array_merge($aSql,$this->dropTableSQL($tabname));
		$aSql = array_merge($aSql,$this->createTableSQL($tabname, $tableflds, $tableoptions));
		$aSql[] = "INSERT INTO $tabname SELECT $copyflds FROM $tempname";
		if ($seq_name && $seq_fld) {	// if we have a sequence we need to set it again
			$seq_name = $tabname.'_'.$seq_fld.'_seq';	// has to be the name of the new implicit sequence
			$aSql[] = "SELECT setval('$seq_name', MAX($seq_fld)) FROM $tabname";
		}
		$aSql[] = "DROP TABLE $tempname";
		// recreate the indexes, if they not contain one of the dropped columns
		foreach($this->metaIndexes($tabname) as $idx_name => $idx_data) {
			if (substr($idx_name,-5) != '_pkey' && (!$dropflds || !count(array_intersect($dropflds,$idx_data['columns'])))) {
				$aSql = array_merge($aSql,$this->createIndexSQL($idx_name, $tabname, $idx_data['columns'],
					$idx_data['unique'] ? array('UNIQUE') : false));
			}
		}
		$aSql[] = 'COMMIT';
		return $aSql;
	}

	function dropTableSQL($tabname)
	{
		$sql = ADODB_DataDict::dropTableSQL($tabname);

		$drop_seq = $this->_dropAutoIncrement($tabname);
		if ($drop_seq) {
			$sql[] = $drop_seq;
		}
		return $sql;
	}

	// return string must begin with space
	function _createSuffix($fname, &$ftype, $fnotnull, $fdefault, $fautoinc, $fconstraint, $funsigned, $fprimary, &$pkey)
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

	// search for a sequence for the given table (asumes the seqence-name contains the table-name!)
	// if yes return sql to drop it
	// this is still necessary if postgres < 7.3 or the SERIAL was created on an earlier version!!!
	function _dropAutoIncrement($tabname)
	{
		$tabname = $this->connection->quote('%'.$tabname.'%');

		$seq = $this->connection->getOne("SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*' AND relname LIKE $tabname AND relkind='S'");

		// check if a tables depends on the sequence and it therefore can't and don't need to be dropped separately
		if (!$seq || $this->connection->getOne("SELECT relname FROM pg_class JOIN pg_depend ON pg_class.relfilenode=pg_depend.objid WHERE relname='$seq' AND relkind='S' AND deptype='i'")) {
			return false;
		}
		return "DROP SEQUENCE ".$seq;
	}

	function renameTableSQL($tabname, $newname)
	{
		if (!empty($this->schema)) {
			$rename_from = $this->tableName($tabname);
			$schema_save = $this->schema;
			$this->schema = false;
			$rename_to = $this->tableName($newname);
			$this->schema = $schema_save;
			return array (sprintf($this->renameTable, $rename_from, $rename_to));
		}

		return array (sprintf($this->renameTable, $this->tableName($tabname), $this->tableName($newname)));
	}

	/*
	CREATE [ [ LOCAL ] { TEMPORARY | TEMP } ] TABLE table_name (
	{ column_name data_type [ DEFAULT default_expr ] [ column_constraint [, ... ] ]
	| table_constraint } [, ... ]
	)
	[ INHERITS ( parent_table [, ... ] ) ]
	[ WITH OIDS | WITHOUT OIDS ]
	where column_constraint is:
	[ CONSTRAINT constraint_name ]
	{ NOT NULL | NULL | UNIQUE | PRIMARY KEY |
	CHECK (expression) |
	REFERENCES reftable [ ( refcolumn ) ] [ MATCH FULL | MATCH PARTIAL ]
	[ ON DELETE action ] [ ON UPDATE action ] }
	[ DEFERRABLE | NOT DEFERRABLE ] [ INITIALLY DEFERRED | INITIALLY IMMEDIATE ]
	and table_constraint is:
	[ CONSTRAINT constraint_name ]
	{ UNIQUE ( column_name [, ... ] ) |
	PRIMARY KEY ( column_name [, ... ] ) |
	CHECK ( expression ) |
	FOREIGN KEY ( column_name [, ... ] ) REFERENCES reftable [ ( refcolumn [, ... ] ) ]
	[ MATCH FULL | MATCH PARTIAL ] [ ON DELETE action ] [ ON UPDATE action ] }
	[ DEFERRABLE | NOT DEFERRABLE ] [ INITIALLY DEFERRED | INITIALLY IMMEDIATE ]
	*/


	/*
	CREATE [ UNIQUE ] INDEX index_name ON table
[ USING acc_method ] ( column [ ops_name ] [, ...] )
[ WHERE predicate ]
CREATE [ UNIQUE ] INDEX index_name ON table
[ USING acc_method ] ( func_name( column [, ... ]) [ ops_name ] )
[ WHERE predicate ]
	*/
	function _indexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		$sql = array();

		if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
			$sql[] = sprintf ($this->dropIndex, $idxname, $tabname);
			if ( isset($idxoptions['DROP']) ) {
				return $sql;
			}
		}

		if (empty($flds)) {
			return $sql;
		}

		$unique = isset($idxoptions['UNIQUE']) ? ' UNIQUE' : '';

		$s = 'CREATE' . $unique . ' INDEX ' . $idxname . ' ON ' . $tabname . ' ';

		if (isset($idxoptions['HASH'])) {
			$s .= 'USING HASH ';
		}

		if (isset($idxoptions[$this->upperName])) {
			$s .= $idxoptions[$this->upperName];
		}

		if (is_array($flds)) {
			$flds = implode(', ', $flds);
		}
		$s .= '(' . $flds . ')';
		$sql[] = $s;

		return $sql;
	}

	function _getSize($ftype, $ty, $fsize, $fprec, $options=false)
	{
		if (strlen($fsize) && $ty != 'X' && $ty != 'B' && $ty  != 'I' && strpos($ftype,'(') === false) {
			$ftype .= "(".$fsize;
			if (strlen($fprec)) $ftype .= ",".$fprec;
			$ftype .= ')';
		}

		/*
		* Handle additional options
		*/
		if (is_array($options)) {
			foreach($options as $type=>$value) {
				switch ($type) {
					case 'ENUM':
						$ftype .= '(' . $value . ')';
						break;
					default:
				}
			}
		}
		return $ftype;
	}

	function changeTableSQL($tablename, $flds, $tableoptions = false, $dropOldFlds=false)
	{
		global $ADODB_FETCH_MODE;
		parent::changeTableSQL($tablename, $flds);
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if ($this->connection->fetchMode !== false) {
			$savem = $this->connection->setFetchMode(false);
		}

		// check table exists
		$save_handler = $this->connection->raiseErrorFn;
		$this->connection->raiseErrorFn = '';
		$cols = $this->metaColumns($tablename);
		$this->connection->raiseErrorFn = $save_handler;

		if (isset($savem)) {
			$this->connection->setFetchMode($savem);
		}
		$ADODB_FETCH_MODE = $save;

		$sqlResult=array();
		if ( empty($cols)) {
			$sqlResult=$this->createTableSQL($tablename, $flds, $tableoptions);
		} else {
			$sqlResultAdd = $this->addColumnSQL($tablename, $flds);
			$sqlResultAlter = $this->alterColumnSQL($tablename, $flds, '', $tableoptions);
			$sqlResult = array_merge((array)$sqlResultAdd, (array)$sqlResultAlter);

			if ($dropOldFlds) {
				// already exists, alter table instead
				list($lines,$pkey,$idxs) = $this->_genFields($flds);
				// genfields can return FALSE at times
				if ($lines == null) {
					$lines = array();
				}
				$alter = 'ALTER TABLE ' . $this->tableName($tablename);
				foreach ( $cols as $id => $v ) {
					if ( !isset($lines[$id]) ) {
						$sqlResult[] = $alter . $this->dropCol . ' ' . $v->name;
					}
				}
			}

		}
		return $sqlResult;
	}
} // end class
