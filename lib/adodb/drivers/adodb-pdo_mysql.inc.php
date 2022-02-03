<?php
/**
 * PDO MySQL driver
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

class ADODB_pdo_mysql extends ADODB_pdo {

	var $metaTablesSQL = "SELECT
			TABLE_NAME,
			CASE WHEN TABLE_TYPE = 'VIEW' THEN 'V' ELSE 'T' END
		FROM INFORMATION_SCHEMA.TABLES
		WHERE TABLE_SCHEMA=";
	var $metaColumnsSQL = "SHOW COLUMNS FROM `%s`";
	var $sysDate = 'CURDATE()';
	var $sysTimeStamp = 'NOW()';
	var $hasGenID = true;
	var $_genIDSQL = "UPDATE %s SET id=LAST_INSERT_ID(id+1);";
	var $_genSeqSQL = "CREATE TABLE  if NOT EXISTS %s (id int not null)";
	var $_genSeqCountSQL = "SELECT count(*) FROM %s";
	var $_genSeq2SQL = "INSERT INTO %s VALUES (%s)";
	var $_dropSeqSQL = "drop table %s";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	var $nameQuote = '`';

	function _init($parentDriver)
	{
		$parentDriver->hasTransactions = false;
		#$parentDriver->_bindInputArray = false;
		$parentDriver->hasInsertID = true;
		$parentDriver->_connectionID->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	}

	// dayFraction is a day in floating point
	function OffsetDate($dayFraction, $date=false)
	{
		if (!$date) {
			$date = $this->sysDate;
		}

		$fraction = $dayFraction * 24 * 3600;
		return $date . ' + INTERVAL ' .	$fraction . ' SECOND';
//		return "from_unixtime(unix_timestamp($date)+$fraction)";
	}
	
	/**
	 * Get a list of indexes on the specified table.
	 *
	 * @param string $table The name of the table to get indexes for.
	 * @param bool $primary (Optional) Whether or not to include the primary key.
	 * @param bool $owner (Optional) Unused.
	 *
	 * @return array|bool An array of the indexes, or false if the query to get the indexes failed.
	 */
	function metaIndexes($table, $primary = false, $owner = false)
	{
		// save old fetch mode
		global $ADODB_FETCH_MODE;

		$false = false;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== FALSE) {
			$savem = $this->setFetchMode(FALSE);
		}

		// get index details
		$rs = $this->execute(sprintf('SHOW INDEXES FROM %s',$table));

		// restore fetchmode
		if (isset($savem)) {
			$this->setFetchMode($savem);
		}
		$ADODB_FETCH_MODE = $save;

		if (!is_object($rs)) {
			return $false;
		}

		$indexes = array ();

		// parse index data into array
		while ($row = $rs->fetchRow()) {
			if ($primary == FALSE AND $row[2] == 'PRIMARY') {
				continue;
			}

			if (!isset($indexes[$row[2]])) {
				$indexes[$row[2]] = array(
					'unique' => ($row[1] == 0),
					'columns' => array()
				);
			}

			$indexes[$row[2]]['columns'][$row[3] - 1] = $row[4];
		}

		// sort columns by order in the index
		foreach ( array_keys ($indexes) as $index )
		{
			ksort ($indexes[$index]['columns']);
		}

		return $indexes;
	}

	function Concat()
	{
		$s = '';
		$arr = func_get_args();

		// suggestion by andrew005#mnogo.ru
		$s = implode(',', $arr);
		if (strlen($s) > 0) {
			return "CONCAT($s)";
		}
		return '';
	}

	function ServerInfo()
	{
		$arr['description'] = ADOConnection::GetOne('select version()');
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}

	function MetaTables($ttype=false, $showSchema=false, $mask=false)
	{
		$save = $this->metaTablesSQL;
		if ($showSchema && is_string($showSchema)) {
			$this->metaTablesSQL .= $this->qstr($showSchema);
		} else {
			$this->metaTablesSQL .= 'schema()';
		}

		if ($mask) {
			$mask = $this->qstr($mask);
			$this->metaTablesSQL .= " like $mask";
		}
		$ret = ADOConnection::MetaTables($ttype, $showSchema);

		$this->metaTablesSQL = $save;
		return $ret;
	}

    /**
     * @param bool $auto_commit
     * @return void
     */
    function SetAutoCommit($auto_commit)
    {
        $this->_connectionID->setAttribute(PDO::ATTR_AUTOCOMMIT, $auto_commit);
    }

	function SetTransactionMode($transaction_mode)
	{
		$this->_transmode  = $transaction_mode;
		if (empty($transaction_mode)) {
			$this->Execute('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
			return;
		}
		if (!stristr($transaction_mode, 'isolation')) {
			$transaction_mode = 'ISOLATION LEVEL ' . $transaction_mode;
		}
		$this->Execute('SET SESSION TRANSACTION ' . $transaction_mode);
	}

	function MetaColumns($table, $normalize=true)
	{
		$this->_findschema($table, $schema);
		if ($schema) {
			$dbName = $this->database;
			$this->SelectDB($schema);
		}
		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

		if ($this->fetchMode !== false) {
			$savem = $this->SetFetchMode(false);
		}
		$rs = $this->Execute(sprintf($this->metaColumnsSQL, $table));

		if ($schema) {
			$this->SelectDB($dbName);
		}

		if (isset($savem)) {
			$this->SetFetchMode($savem);
		}
		$ADODB_FETCH_MODE = $save;
		if (!is_object($rs)) {
			$false = false;
			return $false;
		}

		$retarr = array();
		while (!$rs->EOF){
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];
			$type = $rs->fields[1];

			// split type into type(length):
			$fld->scale = null;
			if (preg_match('/^(.+)\((\d+),(\d+)/', $type, $query_array)) {
				$fld->type = $query_array[1];
				$fld->max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
				$fld->scale = is_numeric($query_array[3]) ? $query_array[3] : -1;
			} elseif (preg_match('/^(.+)\((\d+)/', $type, $query_array)) {
				$fld->type = $query_array[1];
				$fld->max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
			} elseif (preg_match('/^(enum)\((.*)\)$/i', $type, $query_array)) {
				$fld->type = $query_array[1];
				$arr = explode(',', $query_array[2]);
				$fld->enums = $arr;
				$zlen = max(array_map('strlen', $arr)) - 2; // PHP >= 4.0.6
				$fld->max_length = ($zlen > 0) ? $zlen : 1;
			} else {
				$fld->type = $type;
				$fld->max_length = -1;
			}
			$fld->not_null = ($rs->fields[2] != 'YES');
			$fld->primary_key = ($rs->fields[3] == 'PRI');
			$fld->auto_increment = (strpos($rs->fields[5], 'auto_increment') !== false);
			$fld->binary = (strpos($type, 'blob') !== false);
			$fld->unsigned = (strpos($type, 'unsigned') !== false);

			if (!$fld->binary) {
				$d = $rs->fields[4];
				if ($d != '' && $d != 'NULL') {
					$fld->has_default = true;
					$fld->default_value = $d;
				} else {
					$fld->has_default = false;
				}
			}

			if ($save == ADODB_FETCH_NUM) {
				$retarr[] = $fld;
			} else {
				$retarr[strtoupper($fld->name)] = $fld;
			}
			$rs->MoveNext();
		}

		$rs->Close();
		return $retarr;
	}

	// returns true or false
	function SelectDB($dbName)
	{
		$this->database = $dbName;
		$this->databaseName = $dbName; # obsolete, retained for compat with older adodb versions
		$try = $this->Execute('use ' . $dbName);
		return ($try !== false);
	}

	// parameters use PostgreSQL convention, not MySQL
	function SelectLimit($sql, $nrows=-1, $offset=-1, $inputarr=false, $secs=0)
	{
		$nrows = (int) $nrows;
		$offset = (int) $offset;		
		$offsetStr =($offset>=0) ? "$offset," : '';
		// jason judge, see PHPLens Issue No: 9220
		if ($nrows < 0) {
			$nrows = '18446744073709551615';
		}

		if ($secs) {
			$rs = $this->CacheExecute($secs, $sql . " LIMIT $offsetStr$nrows", $inputarr);
		} else {
			$rs = $this->Execute($sql . " LIMIT $offsetStr$nrows", $inputarr);
		}
		return $rs;
	}

	function SQLDate($fmt, $col=false)
	{
		if (!$col) {
			$col = $this->sysTimeStamp;
		}
		$s = 'DATE_FORMAT(' . $col . ",'";
		$concat = false;
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			$ch = $fmt[$i];
			switch($ch) {

				default:
					if ($ch == '\\') {
						$i++;
						$ch = substr($fmt, $i, 1);
					}
					// FALL THROUGH
				case '-':
				case '/':
					$s .= $ch;
					break;

				case 'Y':
				case 'y':
					$s .= '%Y';
					break;

				case 'M':
					$s .= '%b';
					break;

				case 'm':
					$s .= '%m';
					break;

				case 'D':
				case 'd':
					$s .= '%d';
					break;

				case 'Q':
				case 'q':
					$s .= "'),Quarter($col)";

					if ($len > $i+1) {
						$s .= ",DATE_FORMAT($col,'";
					} else {
						$s .= ",('";
					}
					$concat = true;
					break;

				case 'H':
					$s .= '%H';
					break;

				case 'h':
					$s .= '%I';
					break;

				case 'i':
					$s .= '%i';
					break;

				case 's':
					$s .= '%s';
					break;

				case 'a':
				case 'A':
					$s .= '%p';
					break;

				case 'w':
					$s .= '%w';
					break;

				case 'W':
					$s .= '%U';
					break;

				case 'l':
					$s .= '%W';
					break;
			}
		}
		$s .= "')";
		if ($concat) {
			$s = "CONCAT($s)";
		}
		return $s;
	}

	function GenID($seqname='adodbseq',$startID=1)
	{
		$getnext = sprintf($this->_genIDSQL,$seqname);
		$holdtransOK = $this->_transOK; // save the current status
		$rs = @$this->Execute($getnext);
		if (!$rs) {
			if ($holdtransOK) $this->_transOK = true; //if the status was ok before reset
			$this->Execute(sprintf($this->_genSeqSQL,$seqname));
			$cnt = $this->GetOne(sprintf($this->_genSeqCountSQL,$seqname));
			if (!$cnt) $this->Execute(sprintf($this->_genSeq2SQL,$seqname,$startID-1));
			$rs = $this->Execute($getnext);
		}

		if ($rs) {
			$this->genID = $this->_connectionID->lastInsertId($seqname);
			$rs->Close();
		} else {
			$this->genID = 0;
		}

		return $this->genID;
	}


	function createSequence($seqname='adodbseq',$startID=1)
	{
		if (empty($this->_genSeqSQL)) {
			return false;
		}
		$ok = $this->Execute(sprintf($this->_genSeqSQL,$seqname,$startID));
		if (!$ok) {
			return false;
		}

		return $this->Execute(sprintf($this->_genSeq2SQL,$seqname,$startID-1));
	}
}
