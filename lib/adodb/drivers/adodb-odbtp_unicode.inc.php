<?php
/**
 * ODBTP Unicode driver.
 *
 * @deprecated will be removed in ADOdb version 6
 *
 * Because the ODBTP server sends and reads UNICODE text data using UTF-8
 * encoding, the following HTML meta tag must be included within the HTML
 * head section of every HTML form and script page:
 * <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 * Also, all SQL query strings must be submitted as UTF-8 encoded text.
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
 * @author Robert Twitty <rtwitty@neutron.ushmm.org>
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ODBTP_LAYER')) {
	include_once(ADODB_DIR."/drivers/adodb-odbtp.inc.php");
}

class ADODB_odbtp_unicode extends ADODB_odbtp {
	var $databaseType = 'odbtp';
	var $_useUnicodeSQL = true;
}
