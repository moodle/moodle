<?php
/**
 * Oracle driver via ODBC
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

if (!defined('_ADODB_ODBC_LAYER')) {
	include_once(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}


class  ADODB_odbc_oracle extends ADODB_odbc {
	var $databaseType = 'odbc_oracle';
 	var $replaceQuote = "''"; // string to use to replace quotes
	var $concat_operator='||';
	var $fmtDate = "'Y-m-d 00:00:00'";
	var $fmtTimeStamp = "'Y-m-d h:i:sA'";
	var $metaTablesSQL = 'select table_name from cat';
	var $metaColumnsSQL = "select cname,coltype,width from col where tname='%s' order by colno";
	var $sysDate = "TRUNC(SYSDATE)";
	var $sysTimeStamp = 'SYSDATE';

	//var $_bindInputArray = false;

	function MetaTables($ttype = false, $showSchema = false, $mask = false)
	{
		$false = false;
		$rs = $this->Execute($this->metaTablesSQL);
		if ($rs === false) return $false;
		$arr = $rs->GetArray();
		$arr2 = array();
		for ($i=0; $i < sizeof($arr); $i++) {
			$arr2[] = $arr[$i][0];
		}
		$rs->Close();
		return $arr2;
	}

	function MetaColumns($table, $normalize=true)
	{
	global $ADODB_FETCH_MODE;

		$rs = $this->Execute(sprintf($this->metaColumnsSQL,strtoupper($table)));
		if ($rs === false) {
			$false = false;
			return $false;
		}
		$retarr = array();
		while (!$rs->EOF) { //print_r($rs->fields);
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];
			$fld->type = $rs->fields[1];
			$fld->max_length = $rs->fields[2];


			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] = $fld;
			else $retarr[strtoupper($fld->name)] = $fld;

			$rs->MoveNext();
		}
		$rs->Close();
		return $retarr;
	}

	// returns true or false
	function _connect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
		$last_php_error = $this->resetLastError();
		$this->_connectionID = odbc_connect($argDSN,$argUsername,$argPassword,SQL_CUR_USE_ODBC );
		$this->_errorMsg = $this->getChangedErrorMsg($last_php_error);

		$this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
		//if ($this->_connectionID) odbc_autocommit($this->_connectionID,true);
		return $this->_connectionID != false;
	}
	// returns true or false
	function _pconnect($argDSN, $argUsername, $argPassword, $argDatabasename)
	{
		$last_php_error = $this->resetLastError();
		$this->_connectionID = odbc_pconnect($argDSN,$argUsername,$argPassword,SQL_CUR_USE_ODBC );
		$this->_errorMsg = $this->getChangedErrorMsg($last_php_error);

		$this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
		//if ($this->_connectionID) odbc_autocommit($this->_connectionID,true);
		return $this->_connectionID != false;
	}
}

class  ADORecordSet_odbc_oracle extends ADORecordSet_odbc {

	var $databaseType = 'odbc_oracle';

}
