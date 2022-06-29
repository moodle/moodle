<?php
/**
 * Microsoft Access ADO driver.
 *
 * Requires ADO and ODBC. Works only on MS Windows.
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

if (!defined('_ADODB_ADO_LAYER')) {
	include_once(ADODB_DIR . "/drivers/adodb-ado5.inc.php");
}

class  ADODB_ado_access extends ADODB_ado {
	var $databaseType = 'ado_access';
	var $hasTop = 'top';		// support mssql SELECT TOP 10 * FROM TABLE
	var $fmtDate = "#Y-m-d#";
	var $fmtTimeStamp = "#Y-m-d h:i:sA#";// note no comma
	var $sysDate = "FORMAT(NOW,'yyyy-mm-dd')";
	var $sysTimeStamp = 'NOW';
	var $upperCase = 'ucase';

	/*function BeginTrans() { return false;}

	function CommitTrans() { return false;}

	function RollbackTrans() { return false;}*/

}


class  ADORecordSet_ado_access extends ADORecordSet_ado {

	var $databaseType = "ado_access";

}
