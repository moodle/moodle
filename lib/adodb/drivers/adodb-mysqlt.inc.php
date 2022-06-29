<?php
/**
 * MySQL driver in transactional mode
 *
 * @deprecated
 *
 * This driver only supports the original MySQL driver in transactional mode. It
 * is deprecated in PHP version 5.5 and removed in PHP version 7. It is deprecated
 * as of ADOdb version 5.20.0. Use the mysqli driver instead, which supports both
 * transactional and non-transactional updates
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

include_once(ADODB_DIR."/drivers/adodb-mysql.inc.php");


class ADODB_mysqlt extends ADODB_mysql {
	var $databaseType = 'mysqlt';
	var $ansiOuter = true; // for Version 3.23.17 or later
	var $hasTransactions = true;
	var $autoRollback = true; // apparently mysql does not autorollback properly

	/* set transaction mode

	SET [GLOBAL | SESSION] TRANSACTION ISOLATION LEVEL
{ READ UNCOMMITTED | READ COMMITTED | REPEATABLE READ | SERIALIZABLE }

	*/
	function SetTransactionMode( $transaction_mode )
	{
		$this->_transmode  = $transaction_mode;
		if (empty($transaction_mode)) {
			$this->Execute('SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ');
			return;
		}
		if (!stristr($transaction_mode,'isolation')) $transaction_mode = 'ISOLATION LEVEL '.$transaction_mode;
		$this->Execute("SET SESSION TRANSACTION ".$transaction_mode);
	}

	function BeginTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->Execute('SET AUTOCOMMIT=0');
		$this->Execute('BEGIN');
		return true;
	}

	function CommitTrans($ok=true)
	{
		if ($this->transOff) return true;
		if (!$ok) return $this->RollbackTrans();

		if ($this->transCnt) $this->transCnt -= 1;
		$ok = $this->Execute('COMMIT');
		$this->Execute('SET AUTOCOMMIT=1');
		return $ok ? true : false;
	}

	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$ok = $this->Execute('ROLLBACK');
		$this->Execute('SET AUTOCOMMIT=1');
		return $ok ? true : false;
	}

	function RowLock($tables,$where='',$col='1 as adodbignore')
	{
		if ($this->transCnt==0) $this->BeginTrans();
		if ($where) $where = ' where '.$where;
		$rs = $this->Execute("select $col from $tables $where for update");
		return !empty($rs);
	}

}

class ADORecordSet_mysqlt extends ADORecordSet_mysql{
	var $databaseType = "mysqlt";

	function __construct($queryID,$mode=false)
	{
		if ($mode === false) {
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}

		switch ($mode)
		{
		case ADODB_FETCH_NUM: $this->fetchMode = MYSQL_NUM; break;
		case ADODB_FETCH_ASSOC:$this->fetchMode = MYSQL_ASSOC; break;

		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH:
		default: $this->fetchMode = MYSQL_BOTH; break;
		}

		$this->adodbFetchMode = $mode;
		parent::__construct($queryID);
	}

	function MoveNext()
	{
		if (@$this->fields = mysql_fetch_array($this->_queryID,$this->fetchMode)) {
			$this->_currentRow += 1;
			return true;
		}
		if (!$this->EOF) {
			$this->_currentRow += 1;
			$this->EOF = true;
		}
		return false;
	}
}

class ADORecordSet_ext_mysqlt extends ADORecordSet_mysqlt {

	function MoveNext()
	{
		return adodb_movenext($this);
	}
}
