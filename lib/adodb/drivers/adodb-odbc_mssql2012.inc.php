<?php
/**
 * Microsoft SQL Server 2012 driver via ODBC
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

if (!defined('ADODB_DIR')) 
	die();

include_once(ADODB_DIR."/drivers/adodb-odbc_mssql.inc.php");

class  ADODB_odbc_mssql2012 extends ADODB_odbc_mssql
{
	/*
	* Makes behavior similar to prior versions of SQL Server
	*/
	var $connectStmt = 'SET CONCAT_NULL_YIELDS_NULL ON';
}

class  ADORecordSet_odbc_mssql2012 extends ADORecordSet_odbc_mssql
{
}