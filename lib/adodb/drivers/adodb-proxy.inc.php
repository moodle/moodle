<?php
/**
 * ADOdb Proxy Server driver
 *
 * @deprecated
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

if (! defined("_ADODB_PROXY_LAYER")) {
	define("_ADODB_PROXY_LAYER", 1 );
	include_once(ADODB_DIR."/drivers/adodb-csv.inc.php");

class ADODB_proxy extends ADODB_csv {
	var $databaseType = 'proxy';
	var $databaseProvider = 'csv';
}

class ADORecordset_proxy extends ADORecordset_csv {
	var $databaseType = "proxy";
}

} // define
