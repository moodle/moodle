<?php
/**
 * Firebird driver.
 *
 * Requires firebird client. Works on Windows and Unix.
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
 *
 * Driver was cloned from Interbase, so there's quite a lot of duplicated code
 * @noinspection DuplicatedCode
 * @noinspection PhpUnused
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB_firebird extends ADOConnection {
	var $databaseType = "firebird";
	var $dataProvider = "firebird";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fbird_datefmt = '%Y-%m-%d'; // For hours,mins,secs change to '%Y-%m-%d %H:%M:%S';
	var $fmtDate = "'Y-m-d'";
	var $fbird_timestampfmt = "%Y-%m-%d %H:%M:%S";
	var $fbird_timefmt = "%H:%M:%S";
	var $fmtTimeStamp = "'Y-m-d, H:i:s'";
	var $concat_operator='||';
	var $_transactionID;

	public $metaTablesSQL = "SELECT LOWER(rdb\$relation_name) FROM rdb\$relations";
	//OPN STUFF start

	var $metaColumnsSQL = "select lower(a.rdb\$field_name), a.rdb\$null_flag, a.rdb\$default_source, b.rdb\$field_length, b.rdb\$field_scale, b.rdb\$field_sub_type, b.rdb\$field_precision, b.rdb\$field_type from rdb\$relation_fields a, rdb\$fields b where a.rdb\$field_source = b.rdb\$field_name and a.rdb\$relation_name = '%s' order by a.rdb\$field_position asc";
	//OPN STUFF end

	public $_genSeqSQL = "CREATE SEQUENCE %s START WITH %s";

	public $_dropSeqSQL = "DROP SEQUENCE %s";

	var $hasGenID = true;
	var $_bindInputArray = true;
	var $sysDate = "cast('TODAY' as timestamp)";
	var $sysTimeStamp = "CURRENT_TIMESTAMP"; //"cast('NOW' as timestamp)";
	var $ansiOuter = true;
	var $hasAffectedRows = true;
	var $poorAffectedRows = false;
	var $blobEncodeType = 'C';
	/*
	* firebird custom optionally specifies the user role
	*/
	public $role = false;
	/*
	* firebird custom optionally specifies the connection buffers
	*/
	public $buffers = 0;

	/*
	* firebird custom optionally specifies database dialect
	*/
	public $dialect = 3;

	var $nameQuote = '';		/// string to use to quote identifiers and names

	function __construct()
	{
		parent::__construct();
		$this->setTransactionMode('');
	}

	/**
	 * Sets the isolation level of a transaction.
	 *
	 * The default behavior is a more practical IBASE_WAIT | IBASE_REC_VERSION | IBASE_COMMITTED
	 * instead of IBASE_DEFAULT
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:settransactionmode
	 *
	 * @param string $transaction_mode The transaction mode to set.
	 *
	 * @return void
	 */
	public function setTransactionMode($transaction_mode)
	{
		$this->_transmode = $transaction_mode;

		if (empty($transaction_mode)) {
			$this->_transmode = IBASE_WAIT | IBASE_REC_VERSION | IBASE_COMMITTED;
		}

	}

	/**
	 * Connect to a database.
	 *
	 * @todo add: parameter int $port, parameter string $socket
	 *
	 * @param string|null $argHostname (Optional) The host to connect to.
	 * @param string|null $argUsername (Optional) The username to connect as.
	 * @param string|null $argPassword (Optional) The password to connect with.
	 * @param string|null $argDatabasename (Optional) The name of the database to start in when connected.
	 * @param bool $persist (Optional) Whether or not to use a persistent connection.
	 *
	 * @return bool|null True if connected successfully, false if connection failed, or null if the mysqli extension
	 * isn't currently loaded.
	 */
	public function _connect($argHostname, $argUsername, $argPassword, $argDatabasename,$persist=false)
	{
		if (!function_exists('fbird_pconnect'))
			return null;

		if ($argDatabasename)
			$argHostname .= ':'.$argDatabasename;

		$fn = ($persist) ? 'fbird_pconnect':'fbird_connect';

		/*
		* Now merge in the standard connection parameters setting
		*/
		foreach ($this->connectionParameters as $options)
		{
			foreach($options as $k=>$v)
			{
				switch($k){
				case 'role':
					$this->role = $v;
					break;
				case 'dialect':
					$this->dialect = $v;
					break;
				case 'buffers':
					$this->buffers = $v;
				}
			}
		}

		if ($this->role)
			$this->_connectionID = $fn($argHostname,$argUsername,$argPassword,
					$this->charSet,$this->buffers,$this->dialect,$this->role);
		else
			$this->_connectionID = $fn($argHostname,$argUsername,$argPassword,
					$this->charSet,$this->buffers,$this->dialect);

		if ($this->dialect == 1) { // http://www.ibphoenix.com/ibp_60_del_id_ds.html
			$this->replaceQuote = "";
		}
		if ($this->_connectionID === false) {
			$this->_handleError();
			return false;
		}

		ini_set("ibase.timestampformat", $this->fbird_timestampfmt);
		ini_set("ibase.dateformat", $this->fbird_datefmt);
		ini_set("ibase.timeformat", $this->fbird_timefmt);

		return true;
	}

	/**
	 * Connect to a database with a persistent connection.
	 *
	 * @param string|null $argHostname The host to connect to.
	 * @param string|null $argUsername The username to connect as.
	 * @param string|null $argPassword The password to connect with.
	 * @param string|null $argDatabasename The name of the database to start in when connected.
	 *
	 * @return bool|null True if connected successfully, false if connection failed, or null if the mysqli extension
	 * isn't currently loaded.
	 */
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename,true);
	}


	public function metaPrimaryKeys($table,$owner_notused=false,$internalKey=false)
	{
		if ($internalKey) {
			return array('RDB$DB_KEY');
		}

		$table = strtoupper($table);

		$sql = 'SELECT S.RDB$FIELD_NAME AFIELDNAME
	FROM RDB$INDICES I JOIN RDB$INDEX_SEGMENTS S ON I.RDB$INDEX_NAME=S.RDB$INDEX_NAME
	WHERE I.RDB$RELATION_NAME=\''.$table.'\' and I.RDB$INDEX_NAME like \'RDB$PRIMARY%\'
	ORDER BY I.RDB$INDEX_NAME,S.RDB$FIELD_POSITION';

		$a = $this->GetCol($sql,false,true);
		if ($a && sizeof($a)>0) return $a;
		return false;
	}

	/**
	 * Get information about the current Firebird server.
	 *
	 * @return array
	 */
	public function serverInfo()
	{
		$arr['dialect'] = $this->dialect;
		switch($arr['dialect']) {
			case '':
			case '1':
				$s = 'Firebird Dialect 1';
				break;
			case '2':
				$s = 'Firebird Dialect 2';
				break;
			default:
			case '3':
				$s = 'Firebird Dialect 3';
				break;
		}
		$arr['version'] = ADOConnection::_findvers($s);
		$arr['description'] = $s;
		return $arr;
	}

	/**
	 * Begin a Transaction. Must be followed by CommitTrans() or RollbackTrans().
	 *
	 * @return bool true if succeeded or false if database does not support transactions
	 */
	public function beginTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->autoCommit = false;
		/*
		* We manage the transaction mode via fbird_trans
		*/
		$this->_transactionID = fbird_trans( $this->_transmode, $this->_connectionID );
		return $this->_transactionID;
	}


	/**
	 * Commits a transaction.
	 *
	 * @param bool $ok  false to rollback transaction, true to commit
	 *
	 * @return bool
	 */
	public function commitTrans($ok=true)
	{
		if (!$ok) {
			return $this->RollbackTrans();
		}
		if ($this->transOff) {
			return true;
		}
		if ($this->transCnt) {
			$this->transCnt -= 1;
		}
		$ret = false;
		$this->autoCommit = true;
		if ($this->_transactionID) {
			$ret = fbird_commit($this->_transactionID);
		}
		$this->_transactionID = false;
		return $ret;
	}

	function _affectedrows()
	{
		return fbird_affected_rows($this->_transactionID ?: $this->_connectionID);
	}

	/**
	 * Rollback a smart transaction.
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:rollbacktrans
	 *
	 * @return bool
	 */
	public function rollbackTrans()
	{
		if ($this->transOff)
			return true;
		if ($this->transCnt)
			$this->transCnt -= 1;

		$ret = false;
		$this->autoCommit = true;

		if ($this->_transactionID) {
			$ret = fbird_rollback($this->_transactionID);
		}
		$this->_transactionID = false;

		return $ret;
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
	public function metaIndexes($table, $primary = false, $owner = false)
	{
		// save old fetch mode
		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

		if ($this->fetchMode !== FALSE) {
				$savem = $this->SetFetchMode(FALSE);
		}

		$table = strtoupper($table);
		$sql = "SELECT * FROM RDB\$INDICES WHERE RDB\$RELATION_NAME = '".$table."'";
		if (!$primary) {
			$sql .= " AND RDB\$INDEX_NAME NOT LIKE 'RDB\$%'";
		} else {
			$sql .= " AND RDB\$INDEX_NAME NOT LIKE 'RDB\$FOREIGN%'";
		}
		// get index details
		$rs = $this->execute($sql);
		if (!is_object($rs)) {
			// restore fetchmode
			if (isset($savem)) {
				$this->SetFetchMode($savem);
			}
			$ADODB_FETCH_MODE = $save;
			return false;
		}
		$indexes = array();
		while ($row = $rs->FetchRow()) {

			$index = trim($row[0]);
			if (!isset($indexes[$index])) {
				if (is_null($row[3])) {
					$row[3] = 0;
				}
				$indexes[$index] = array(
					'unique' => ($row[3] == 1),
					'columns' => array()
				);
			}
			$sql = sprintf("SELECT * FROM RDB\$INDEX_SEGMENTS WHERE RDB\$INDEX_NAME = '%s' ORDER BY RDB\$FIELD_POSITION ASC",$index);
			$rs1 = $this->execute($sql);
			while ($row1 = $rs1->FetchRow()) {
				$indexes[$index]['columns'][$row1[2]] = trim($row1[1]);
			}
		}

		// restore fetchmode
		if (isset($savem)) {
			$this->SetFetchMode($savem);
		}
		$ADODB_FETCH_MODE = $save;

		return $indexes;
	}

	/**
	 * Lock a table row for a duration of a transaction.
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:rowlock
	 * @link https://firebirdsql.org/refdocs/langrefupd21-notes-withlock.html
	 *
	 * @param string $table The table(s) to lock rows for.
	 * @param string $where (Optional) The WHERE clause to use to determine which rows to lock.
	 * @param string $col (Optional) The columns to select.
	 *
	 * @return bool True if the locking SQL statement executed successfully, otherwise false.
	 */
	public function rowLock($table,$where,$col=false)
	{
		if ($this->transCnt==0)
			$this->beginTrans();

		if ($where) $where = ' where '.$where;
		$rs = $this->execute("SELECT $col FROM $table $where FOR UPDATE WITH LOCK");
		return !empty($rs);
	}

	/**
	 * Creates a sequence in the database.
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:createsequence
	 *
	 * @param string $seqname The sequence name.
	 * @param int $startID The start id.
	 *
	 * @return ADORecordSet|bool A record set if executed successfully, otherwise false.
	 */
	public function createSequence($seqname='adodbseq', $startID = 1)
	{
		$sql = sprintf($this->_genSeqSQL,$seqname,$startID);
		return $this->execute($sql);
	}

	/**
	 * A portable method of creating sequence numbers.
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:genid
	 *
	 * @param string $seqname (Optional) The name of the sequence to use.
	 * @param int $startID (Optional) The point to start at in the sequence.
	 *
	 * @return int
	 */
	public function genID($seqname='adodbseq',$startID=1)
	{
		$getnext = ("SELECT Gen_ID($seqname,1) FROM RDB\$DATABASE");
		$rs = @$this->Execute($getnext);
		if (!$rs) {
			$this->Execute("CREATE SEQUENCE $seqname START WITH $startID");
			$rs = $this->Execute($getnext);
		}
		if ($rs && !$rs->EOF) {
			$this->genID = (integer) reset($rs->fields);
		}
		else {
			$this->genID = 0; // false
		}

		if ($rs) {
			$rs->Close();
		}

		return $this->genID;
	}

	function selectDB($dbName)
	{
		return false;
	}

	function _handleError()
	{
		$this->_errorCode = fbird_errcode();
		$this->_errorMsg  = fbird_errmsg();
	}


	public function errorNo()
	{
		return (integer) $this->_errorCode;
	}

	function errorMsg()
	{
			return $this->_errorMsg;
	}

	/**
	 * Prepares an SQL statement and returns a handle to use.
	 * This is not used by bound parameters anymore
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:prepare
	 * @todo update this function to handle prepared statements correctly
	 *
	 * @param string $sql The SQL to prepare.
	 *
	 * @return bool|array The SQL that was provided and the prepared parameters,
	 *                    or false if the preparation fails
	 */
	public function prepare($sql)
	{
		$stmt = fbird_prepare($this->_connectionID,$sql);
		if (!$stmt)
			return false;
		return array($sql,$stmt);
	}

	/**
	* Return the query id.
	*
	* @param string|array $sql
	* @param array $iarr
	*
	* @return bool|object
	*/
	function _query($sql, $iarr = false)
	{
		if (!$this->isConnected()) {
			return false;
		}

		if (!$this->autoCommit && $this->_transactionID) {
			$conn = $this->_transactionID;
			$docommit = false;
		} else {
			$conn = $this->_connectionID;
			$docommit = true;
		}

		if (is_array($sql)) {
			// Prepared statement
			$fn = 'fbird_execute';
			$args = [$sql[1]];
		} else {
			$fn = 'fbird_query';
			$args = [$conn, $sql];
		}
		if (is_array($iarr)) {
			$args = array_merge($args, $iarr);
		}
		$ret = call_user_func_array($fn, $args);

		if ($docommit && $ret === true) {
			fbird_commit($this->_connectionID);
		}

		$this->_handleError();
		return $ret;
	}

	// returns true or false
	function _close()
	{
		if (!$this->autoCommit) {
			@fbird_rollback($this->_connectionID);
		}
		return @fbird_close($this->_connectionID);
	}

	//OPN STUFF start
	function _ConvertFieldType(&$fld, $ftype, $flen, $fscale, $fsubtype, $fprecision, $dialect3)
	{
		$fscale = abs($fscale);
		$fld->max_length = $flen;
		$fld->scale = null;
		switch($ftype){
		case 7:
		case 8:
			if ($dialect3) {
				switch($fsubtype){
					case 0:
						$fld->type = ($ftype == 7 ? 'smallint' : 'integer');
						break;
					case 1:
						$fld->type = 'numeric';
						$fld->max_length = $fprecision;
						$fld->scale = $fscale;
						break;
					case 2:
						$fld->type = 'decimal';
						$fld->max_length = $fprecision;
						$fld->scale = $fscale;
						break;
				} // switch
			} else {
				if ($fscale !=0) {
					$fld->type = 'decimal';
					$fld->scale = $fscale;
					$fld->max_length = ($ftype == 7 ? 4 : 9);
				} else {
					$fld->type = ($ftype == 7 ? 'smallint' : 'integer');
				}
			}
			break;
		case 16:
			if ($dialect3) {
				switch($fsubtype){
				case 0:
					$fld->type = 'decimal';
					$fld->max_length = 18;
					$fld->scale = 0;
					break;
				case 1:
					$fld->type = 'numeric';
					$fld->max_length = $fprecision;
					$fld->scale = $fscale;
					break;
				case 2:
					$fld->type = 'decimal';
					$fld->max_length = $fprecision;
					$fld->scale = $fscale;
					break;
				} // switch
			}
			break;
		case 10:
			$fld->type = 'float';
			break;
		case 14:
			$fld->type = 'char';
			break;
		case 27:
			if ($fscale !=0) {
				$fld->type = 'decimal';
				$fld->max_length = 15;
				$fld->scale = 5;
			} else {
				$fld->type = 'double';
			}
			break;
		case 35:
			if ($dialect3) {
				$fld->type = 'timestamp';
			} else {
				$fld->type = 'date';
			}
			break;
		case 12:
			$fld->type = 'date';
			break;
		case 13:
			$fld->type = 'time';
			break;
		case 37:
			$fld->type = 'varchar';
			break;
		case 40:
			$fld->type = 'cstring';
			break;
		case 261:
			$fld->type = 'blob';
			$fld->max_length = -1;
			break;
		} // switch
	}
	//OPN STUFF end

	/**
	 * Return an array of information about a table's columns.
	 *
	 * @param string $table The name of the table to get the column info for.
	 * @param bool $normalize (Optional) Unused.
	 *
	 * @return ADOFieldObject[]|bool An array of info for each column,
	 * or false if it could not determine the info.
	 */
	public function metaColumns($table, $normalize = true)
	{

		global $ADODB_FETCH_MODE;

		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

		$rs = $this->execute(sprintf($this->metaColumnsSQL,strtoupper($table)));

		$ADODB_FETCH_MODE = $save;

		if ($rs === false) {
			return false;
		}

		$retarr = array();
		//OPN STUFF start
		$dialect3 = $this->dialect == 3;
		//OPN STUFF end
		while (!$rs->EOF) { //print_r($rs->fields);
			$fld = new ADOFieldObject();
			$fld->name = trim($rs->fields[0]);
			//OPN STUFF start
				//print_r($rs->fields);
			$this->_ConvertFieldType(
				$fld, $rs->fields[7], $rs->fields[3], $rs->fields[4], $rs->fields[5], $rs->fields[6], $dialect3);
			if (isset($rs->fields[1]) && $rs->fields[1]) {
				$fld->not_null = true;
			}
			if (isset($rs->fields[2])) {

				$fld->has_default = true;
				$d = substr($rs->fields[2],strlen('default '));
				switch ($fld->type) {
					case 'smallint':
					case 'integer':
						$fld->default_value = (int)$d;
						break;
					case 'char':
					case 'blob':
					case 'text':
					case 'varchar':
						$fld->default_value = (string)substr($d, 1, strlen($d) - 2);
						break;
					case 'double':
					case 'float':
						$fld->default_value = (float)$d;
						break;
					default:
						$fld->default_value = $d;
						break;
				}
		//	case 35:$tt = 'TIMESTAMP'; break;
			}
			if ((isset($rs->fields[5])) && ($fld->type == 'blob')) {
				$fld->sub_type = $rs->fields[5];
			} else {
				$fld->sub_type = null;
			}
			//OPN STUFF end
			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] = $fld;
			else $retarr[strtoupper($fld->name)] = $fld;

			$rs->MoveNext();
		}
		$rs->Close();
		if ( empty($retarr))
			return false;
		else return $retarr;
	}

	/**
	 * Retrieves a list of tables based on given criteria
	 *
	 * @param string|bool $ttype (Optional) Table type = 'TABLE', 'VIEW' or false=both (default)
	 * @param string|bool $showSchema (Optional) schema name, false = current schema (default)
	 * @param string|bool $mask (Optional) filters the table by name
	 *
	 * @return array list of tables
	 */
	public function metaTables($ttype = false, $showSchema = false, $mask = false)
	{
		$save = $this->metaTablesSQL;
		if (!$showSchema) {
			$this->metaTablesSQL .= " WHERE (rdb\$relation_name NOT LIKE 'RDB\$%' AND rdb\$relation_name NOT LIKE 'MON\$%' AND rdb\$relation_name NOT LIKE 'SEC\$%')";
		} elseif (is_string($showSchema)) {
			$this->metaTablesSQL .= $this->qstr($showSchema);
		}

		if ($mask) {
			$mask = $this->qstr($mask);
			$this->metaTablesSQL .= " AND table_name LIKE $mask";
		}
		$ret = ADOConnection::metaTables($ttype,$showSchema);

		$this->metaTablesSQL = $save;
		return $ret;
	}

	/**
	 * Encodes a blob, then assigns an id ready to be used
	 *
	 * @param string $blob The blob to be encoded
	 *
	 * @return bool success
	 */
	public function blobEncode( $blob )
	{
		$blobid = fbird_blob_create( $this->_connectionID);
		fbird_blob_add( $blobid, $blob );
		return fbird_blob_close( $blobid );
	}

	/**
	 * Manually decode a blob
	 *
	 * since we auto-decode all blob's since 2.42,
	 * BlobDecode should not do any transforms
	 *
	 * @param string $blob
	 *
	 * @return string the same blob
	 */
	public function blobDecode($blob)
	{
		return $blob;
	}

	/**
	 * Auto function called on read of blob to decode
	 *
	 * @param string $blob Value to decode
	 *
	 * @return string Decoded blob
	 */
	public function _blobDecode($blob)
	{
		if ($blob === null) {
			return '';
		}

		$blob_data = fbird_blob_info($this->_connectionID, $blob);
		$blobId = fbird_blob_open($this->_connectionID, $blob);

		if ($blob_data[0] > $this->maxblobsize) {
			$realBlob = fbird_blob_get($blobId, $this->maxblobsize);
			while ($string = fbird_blob_get($blobId, 8192)) {
				$realBlob .= $string;
			}
		} else {
			$realBlob = fbird_blob_get($blobId, $blob_data[0]);
		}

		fbird_blob_close($blobId);
		return $realBlob;
	}

	/**
	 * Insert blob data into a database column directly
	 * from file
	 *
	 * @param string $table table to insert
	 * @param string $column column to insert
	 * @param string $path  physical file name
	 * @param string $where string to find unique record
	 * @param string $blobtype BLOB or CLOB
	 *
	 * @return bool success
	 */
	public function updateBlobFile($table,$column,$path,$where,$blobtype='BLOB')
	{
		$fd = fopen($path,'rb');
		if ($fd === false)
			return false;

		$blob_id = fbird_blob_create($this->_connectionID);

		/* fill with data */

		while ($val = fread($fd,32768)){
			fbird_blob_add($blob_id, $val);
		}

		/* close and get $blob_id_str for inserting into table */
		$blob_id_str = fbird_blob_close($blob_id);

		fclose($fd);
		return $this->Execute("UPDATE $table SET $column=(?) WHERE $where",array($blob_id_str)) != false;
	}

	/**
	 * Insert blob data into a database column
	 *
	 * @param string $table table to insert
	 * @param string $column column to insert
	 * @param string $val    value to insert
	 * @param string $where string to find unique record
	 * @param string $blobtype BLOB or CLOB
	 *
	 * @return bool success
	 */
	public function updateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{
		$blob_id = fbird_blob_create($this->_connectionID);

		// fbird_blob_add($blob_id, $val);

		// replacement that solves the problem by which only the first modulus 64K /
		// of $val are stored at the blob field ////////////////////////////////////
		// Thx Abel Berenstein  aberenstein#afip.gov.ar
		$len = strlen($val);
		$chunk_size = 32768;
		$tail_size = $len % $chunk_size;
		$n_chunks = ($len - $tail_size) / $chunk_size;

		for ($n = 0; $n < $n_chunks; $n++) {
			$start = $n * $chunk_size;
			$data = substr($val, $start, $chunk_size);
			fbird_blob_add($blob_id, $data);
		}

		if ($tail_size) {
			$start = $n_chunks * $chunk_size;
			$data = substr($val, $start, $tail_size);
			fbird_blob_add($blob_id, $data);
		}
		// end replacement /////////////////////////////////////////////////////////

		$blob_id_str = fbird_blob_close($blob_id);

		return $this->execute("UPDATE $table SET $column=(?) WHERE $where",array($blob_id_str)) != false;

	}


	/**
	 * Returns a portably-formatted date string from a timestamp database column.
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:sqldate
	 *
	 * Firebird does not support an AM/PM format, so the A indicator always shows AM
	 *
	 * @param string $fmt The date format to use.
	 * @param string|bool $col (Optional) The table column to date format, or if false, use NOW().
	 *
	 * @return string The SQL DATE_FORMAT() string, or empty if the provided date format was empty.
	 */
	public function sqlDate($fmt, $col=false)
	{
		if (!$col)
			$col = 'CURRENT_TIMESTAMP';

		$s = '';

		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			if ($s) $s .= '||';
			$ch = $fmt[$i];
			$choice = strtoupper($ch);
			switch($choice) {
			case 'Y':
				$s .= "EXTRACT(YEAR FROM $col)";
				break;
			case 'M':
				$s .= "RIGHT('0' || TRIM(EXTRACT(MONTH FROM $col)),2)";
				break;
			case 'W':
				// The more accurate way of doing this is with a stored procedure
				// See http://wiki.firebirdsql.org/wiki/index.php?page=DATE+Handling+Functions for details
				$s .= "((EXTRACT(YEARDAY FROM $col) - EXTRACT(WEEKDAY FROM $col - 1) + 7) / 7)";
				break;
			case 'Q':
				$s .= "CAST(((EXTRACT(MONTH FROM $col)+2) / 3) AS INTEGER)";
				break;
			case 'D':
				$s .= "RIGHT('0' || TRIM(EXTRACT(DAY FROM $col)),2)";
				break;
			case 'H':
				$s .= "RIGHT('0' || TRIM(EXTRACT(HOUR FROM $col)),2)";
				break;
			case 'I':
				$s .= "RIGHT('0' || TRIM(EXTRACT(MINUTE FROM $col)),2)";
				break;
			case 'S':
				//$s .= "CAST((EXTRACT(SECOND FROM $col)) AS INTEGER)";
				$s .= "RIGHT('0' || TRIM(EXTRACT(SECOND FROM $col)),2)";
				break;
			case 'A':
				$s .= $this->qstr('AM');
				break;
			default:
				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				$s .= $this->qstr($ch);
				break;
			}
		}
		return $s;
	}

	/**
	 * Creates a portable date offset field, for use in SQL statements.
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
	 *
	 * @param float $dayFraction A day in floating point
	 * @param string|bool $date (Optional) The date to offset. If false, uses CURDATE()
	 *
	 * @return string
	 */
	public function offsetDate($dayFraction, $date=false)
	{
		if (!$date)
			$date = $this->sysTimeStamp;

		$fraction = $dayFraction * 24 * 3600;
		return sprintf("DATEADD (second, %s, %s)  FROM RDB\$DATABASE",$fraction,$date);
	}


	// Note that Interbase 6.5 uses this ROWS instead - don't you love forking wars!
	// 		SELECT col1, col2 FROM table ROWS 5 -- get 5 rows
	//		SELECT col1, col2 FROM TABLE ORDER BY col1 ROWS 3 TO 7 -- first 5 skip 2
	/**
	 * Executes a provided SQL statement and returns a handle to the result, with the ability to supply a starting
	 * offset and record count.
	 *
	 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:selectlimit
	 *
	 * @param string $sql The SQL to execute.
	 * @param int $nrows (Optional) The limit for the number of records you want returned. By default, all results.
	 * @param int $offset (Optional) The offset to use when selecting the results. By default, no offset.
	 * @param array|bool $inputarr (Optional) Any parameter values required by the SQL statement, or false if none.
	 * @param int $secs2cache (Optional) If greater than 0, perform a cached execute. By default, normal execution.
	 *
	 * @return ADORecordSet|false The query results, or false if the query failed to execute.
	 */
	public function selectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false, $secs2cache=0)
	{
		$nrows = (integer) $nrows;
		$offset = (integer) $offset;
		$str = 'SELECT ';
		if ($nrows >= 0) $str .= "FIRST $nrows ";
		$str .=($offset>=0) ? "SKIP $offset " : '';

		$sql = preg_replace('/^[ \t]*select/i',$str,$sql);
		if ($secs2cache)
			$rs = $this->cacheExecute($secs2cache,$sql,$inputarr);
		else
			$rs = $this->execute($sql,$inputarr);

		return $rs;
	}

}

/**
 * Class ADORecordset_firebird
 */
class ADORecordset_firebird extends ADORecordSet
{
	var $databaseType = "firebird";
	var $bind = false;

	/**
	 * @var ADOFieldObject[] Holds a cached version of the metadata
	 */
	private $fieldObjects = false;

	/**
	 * @var bool Flags if we have retrieved the metadata
	 */
	private $fieldObjectsRetrieved = false;

	/**
	 * @var array Cross-reference the objects by name for easy access
	 */
	private $fieldObjectsIndex = array();

	/**
	 * @var bool Flag to indicate if the result has a blob
	 */
	private $fieldObjectsHaveBlob = false;

	function __construct($id, $mode = false)
	{
		global $ADODB_FETCH_MODE;

		$this->fetchMode = ($mode === false) ? $ADODB_FETCH_MODE : $mode;
		parent::__construct($id);
	}


	/**
	 * Returns: an object containing field information.
	 *
	 * Get column information in the Recordset object. fetchField()
	 * can be used in order to obtain information about fields in a
	 * certain query result. If the field offset isn't specified,
	 * the next field that wasn't yet retrieved by fetchField()
	 * is retrieved.
	 *
	 * $param int $fieldOffset (optional default=-1 for all
	 * @return mixed an ADOFieldObject, or array of objects
	 */
	private function _fetchField($fieldOffset = -1)
	{
		if ($this->fieldObjectsRetrieved) {
			if ($this->fieldObjects) {
				// Already got the information
				if ($fieldOffset == -1) {
					return $this->fieldObjects;
				} else {
					return $this->fieldObjects[$fieldOffset];
				}
			} else {
				// No metadata available
				return false;
			}
		}

		// Populate the field objects cache
		$this->fieldObjectsRetrieved = true;
		$this->fieldObjectsHaveBlob = false;
		$this->_numOfFields = fbird_num_fields($this->_queryID);
		for ($fieldIndex = 0; $fieldIndex < $this->_numOfFields; $fieldIndex++) {
			$fld = new ADOFieldObject;
			$ibf = fbird_field_info($this->_queryID, $fieldIndex);

			$name = empty($ibf['alias']) ? $ibf['name'] : $ibf['alias'];

			switch (ADODB_ASSOC_CASE) {
				case ADODB_ASSOC_CASE_UPPER:
					$fld->name = strtoupper($name);
					break;
				case ADODB_ASSOC_CASE_LOWER:
					$fld->name = strtolower($name);
					break;
				case ADODB_ASSOC_CASE_NATIVE:
				default:
					$fld->name = $name;
					break;
			}

			$fld->type = $ibf['type'];
			$fld->max_length = $ibf['length'];

			// This needs to be populated from the metadata
			$fld->not_null = false;
			$fld->has_default = false;
			$fld->default_value = 'null';

			$this->fieldObjects[$fieldIndex] = $fld;
			$this->fieldObjectsIndex[$fld->name] = $fieldIndex;

			if ($fld->type == 'BLOB') {
				$this->fieldObjectsHaveBlob = true;
			}
		}

		if ($fieldOffset == -1) {
			return $this->fieldObjects;
		}

		return $this->fieldObjects[$fieldOffset];
	}

	/**
	 * Fetchfield copies the oracle method, it loads the field information
	 * into the _fieldobjs array once, to save multiple calls to the
	 * fbird_ function
	 *
	 * @param int $fieldOffset (optional)
	 *
	 * @return adoFieldObject|false
	 */
	public function fetchField($fieldOffset = -1)
	{
		if ($fieldOffset == -1) {
			return $this->fieldObjects;
		}

		return $this->fieldObjects[$fieldOffset];
	}

	function _initrs()
	{
		$this->_numOfRows = -1;

		/*
		* Retrieve all of the column information first. We copy
		* this method from oracle
		*/
		$this->_fetchField();

	}

	function _seek($row)
	{
		return false;
	}

	public function _fetch()
	{
		// Case conversion function for use in Closure defined below
		$localFnCaseConv = null;

		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			// Handle either associative or fetch both
			$localNumeric = false;

			$f = @fbird_fetch_assoc($this->_queryID);
			if (is_array($f)) {
				// Optimally do the case_upper or case_lower
				if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_LOWER) {
					$f = array_change_key_case($f, CASE_LOWER);
					$localFnCaseConv = 'strtolower';
				} elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
					$f = array_change_key_case($f, CASE_UPPER);
					$localFnCaseConv = 'strtoupper';
				}
			}
		} else {
			// Numeric fetch mode
			$localNumeric = true;
			$f = @fbird_fetch_row($this->_queryID);
		}

		if ($f === false) {
			$this->fields = false;
			return false;
		}

		// OPN stuff start - optimized
		// fix missing nulls and decode blobs automatically
		global $ADODB_ANSI_PADDING_OFF;
		$rtrim = !empty($ADODB_ANSI_PADDING_OFF);

		// For optimal performance, only process if there is a possibility of something to do
		if ($this->fieldObjectsHaveBlob || $rtrim) {
			$localFieldObjects = $this->fieldObjects;
			$localFieldObjectIndex = $this->fieldObjectsIndex;
			/** @var ADODB_firebird $localConnection */
			$localConnection = &$this->connection;

			/**
			 * Closure for an efficient method of iterating over the elements.
			 * @param mixed      $value
			 * @param string|int $key
			 * @return void
			 */
			$rowTransform = function ($value, $key) use (
				&$f,
				$rtrim,
				$localFieldObjects,
				$localConnection,
				$localNumeric,
				$localFnCaseConv,
				$localFieldObjectIndex
			) {
				if ($localNumeric) {
					$localKey = $key;
				} else {
					// Cross-reference the associative key back to numeric
					// with appropriate case conversion
					$index = $localFnCaseConv ? $localFnCaseConv($key) : $key;
					$localKey = $localFieldObjectIndex[$index];
				}

				// As we iterate the elements check for blobs and padding
				if ($localFieldObjects[$localKey]->type == 'BLOB') {
					$f[$key] = $localConnection->_BlobDecode($value);
				} else {
					if ($rtrim && is_string($value)) {
						$f[$key] = rtrim($value);
					}
				}

			};

			// Walk the array, applying the above closure
			array_walk($f, $rowTransform);
		}

		if (!$localNumeric && $this->fetchMode & ADODB_FETCH_NUM) {
			// Creates a fetch both
			$fNum = array_values($f);
			$f = array_merge($f, $fNum);
		}

		$this->fields = $f;

		return true;
	}

	/**
	 * Get the value of a field in the current row by column name.
	 * Will not work if ADODB_FETCH_MODE is set to ADODB_FETCH_NUM.
	 *
	 * @param string $colname is the field to access
	 *
	 * @return mixed the value of $colname column
	 */
	public function fields($colname)
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			return $this->fields[$colname];
		}

		if (!$this->bind) {
			// fieldsObjectIndex populated by the recordset load
			$this->bind = array_change_key_case($this->fieldObjectsIndex, CASE_UPPER);
		}

		return $this->fields[$this->bind[strtoupper($colname)]];
	}


	function _close()
	{
		return @fbird_free_result($this->_queryID);
	}

	public function metaType($t, $len = -1, $fieldObj = false)
	{
		if (is_object($t)) {
			$fieldObj = $t;
			$t = $fieldObj->type;
			$len = $fieldObj->max_length;
		}

		$t = strtoupper($t);

		if (array_key_exists($t, $this->connection->customActualTypes)) {
			return $this->connection->customActualTypes[$t];
		}

		switch ($t) {
			case 'CHAR':
				return 'C';

			case 'TEXT':
			case 'VARCHAR':
			case 'VARYING':
				if ($len <= $this->blobSize) {
					return 'C';
				}
				return 'X';
			case 'BLOB':
				return 'B';

			case 'TIMESTAMP':
			case 'DATE':
				return 'D';
			case 'TIME':
				return 'T';
			//case 'T': return 'T';

			//case 'L': return 'L';
			case 'INT':
			case 'SHORT':
			case 'INTEGER':
				return 'I';
			default:
				return ADODB_DEFAULT_METATYPE;
		}
	}

}

