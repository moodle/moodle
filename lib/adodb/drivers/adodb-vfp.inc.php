<?php
/**
 * Microsoft Visual FoxPro driver
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

if (!defined('_ADODB_ODBC_LAYER')) {
	include_once(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}
if (!defined('ADODB_VFP')){
define('ADODB_VFP',1);
class ADODB_vfp extends ADODB_odbc {
	var $databaseType = "vfp";
	var $fmtDate = "{^Y-m-d}";
	var $fmtTimeStamp = "{^Y-m-d, h:i:sA}";
	var $replaceQuote = "'+chr(39)+'" ;
	var $true = '.T.';
	var $false = '.F.';
	var $hasTop = 'top';		// support mssql SELECT TOP 10 * FROM TABLE
	var $_bindInputArray = false; // strangely enough, setting to true does not work reliably
	var $sysTimeStamp = 'datetime()';
	var $sysDate = 'date()';
	var $ansiOuter = true;
	var $hasTransactions = false;
	var $curmode = false ; // See sqlext.h, SQL_CUR_DEFAULT == SQL_CUR_USE_DRIVER == 2L

	function Time()
	{
		return time();
	}

	function BeginTrans() { return false;}

	// quote string to be sent back to database
	function qstr($s,$nofixquotes=false)
	{
		if (!$nofixquotes) return  "'".str_replace("\r\n","'+chr(13)+'",str_replace("'",$this->replaceQuote,$s))."'";
		return "'".$s."'";
	}


	// TOP requires ORDER BY for VFP
	function SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0)
	{
		$this->hasTop = preg_match('/ORDER[ \t\r\n]+BY/is',$sql) ? 'top' : false;
		$ret = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);
		return $ret;
	}



};


class  ADORecordSet_vfp extends ADORecordSet_odbc {

	var $databaseType = "vfp";


	function MetaType($t, $len = -1, $fieldobj = false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		switch (strtoupper($t)) {
		case 'C':
			if ($len <= $this->blobSize) return 'C';
		case 'M':
			return 'X';

		case 'D': return 'D';

		case 'T': return 'T';

		case 'L': return 'L';

		case 'I': return 'I';

		default: return ADODB_DEFAULT_METATYPE;
		}
	}
}

} //define
