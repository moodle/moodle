<?php
/**
 * @version   v5.20.9  21-Dec-2016
 * @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
 * @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
 * Released under both BSD license and Lesser GPL library license.
 * Whenever there is any discrepancy between the two licenses,
 * the BSD license will take precedence.
 *
 * Set tabs to 4 for best viewing.
 *
 * The following code is adapted from the PEAR DB error handling code.
 * Portions (c)1997-2002 The PHP Group.
 */


if (!defined("DB_ERROR")) define("DB_ERROR",-1);

if (!defined("DB_ERROR_SYNTAX")) {
	define("DB_ERROR_SYNTAX",              -2);
	define("DB_ERROR_CONSTRAINT",          -3);
	define("DB_ERROR_NOT_FOUND",           -4);
	define("DB_ERROR_ALREADY_EXISTS",      -5);
	define("DB_ERROR_UNSUPPORTED",         -6);
	define("DB_ERROR_MISMATCH",            -7);
	define("DB_ERROR_INVALID",             -8);
	define("DB_ERROR_NOT_CAPABLE",         -9);
	define("DB_ERROR_TRUNCATED",          -10);
	define("DB_ERROR_INVALID_NUMBER",     -11);
	define("DB_ERROR_INVALID_DATE",       -12);
	define("DB_ERROR_DIVZERO",            -13);
	define("DB_ERROR_NODBSELECTED",       -14);
	define("DB_ERROR_CANNOT_CREATE",      -15);
	define("DB_ERROR_CANNOT_DELETE",      -16);
	define("DB_ERROR_CANNOT_DROP",        -17);
	define("DB_ERROR_NOSUCHTABLE",        -18);
	define("DB_ERROR_NOSUCHFIELD",        -19);
	define("DB_ERROR_NEED_MORE_DATA",     -20);
	define("DB_ERROR_NOT_LOCKED",         -21);
	define("DB_ERROR_VALUE_COUNT_ON_ROW", -22);
	define("DB_ERROR_INVALID_DSN",        -23);
	define("DB_ERROR_CONNECT_FAILED",     -24);
	define("DB_ERROR_EXTENSION_NOT_FOUND",-25);
	define("DB_ERROR_NOSUCHDB",           -25);
	define("DB_ERROR_ACCESS_VIOLATION",   -26);
	define("DB_ERROR_DEADLOCK",           -27);
	define("DB_ERROR_STATEMENT_TIMEOUT",  -28);
	define("DB_ERROR_SERIALIZATION_FAILURE", -29);
}

function adodb_errormsg($value)
{
global $ADODB_LANG,$ADODB_LANG_ARRAY;

	if (empty($ADODB_LANG)) $ADODB_LANG = 'en';
	if (isset($ADODB_LANG_ARRAY['LANG']) && $ADODB_LANG_ARRAY['LANG'] == $ADODB_LANG) ;
	else {
		include_once(ADODB_DIR."/lang/adodb-$ADODB_LANG.inc.php");
    }
	return isset($ADODB_LANG_ARRAY[$value]) ? $ADODB_LANG_ARRAY[$value] : $ADODB_LANG_ARRAY[DB_ERROR];
}

function adodb_error($provider,$dbType,$errno)
{
	//var_dump($errno);
	if (is_numeric($errno) && $errno == 0) return 0;
	switch($provider) {
	case 'mysql': $map = adodb_error_mysql(); break;

	case 'oracle':
	case 'oci8': $map = adodb_error_oci8(); break;

	case 'ibase': $map = adodb_error_ibase(); break;

	case 'odbc': $map = adodb_error_odbc(); break;

	case 'mssql':
	case 'sybase': $map = adodb_error_mssql(); break;

	case 'informix': $map = adodb_error_ifx(); break;

	case 'postgres': return adodb_error_pg($errno); break;

	case 'sqlite': return $map = adodb_error_sqlite(); break;
	default:
		return DB_ERROR;
	}
	//print_r($map);
	//var_dump($errno);
	if (isset($map[$errno])) return $map[$errno];
	return DB_ERROR;
}

//**************************************************************************************

function adodb_error_pg($errormsg)
{
	if (is_numeric($errormsg)) return (integer) $errormsg;
	// Postgres has no lock-wait timeout.  The best we could do would be to set a statement timeout.
	static $error_regexps = array(
			'(Table does not exist\.|Relation [\"\'].*[\"\'] does not exist|sequence does not exist|class ".+" not found)$' => DB_ERROR_NOSUCHTABLE,
			'Relation [\"\'].*[\"\'] already exists|Cannot insert a duplicate key into (a )?unique index.*|duplicate key.*violates unique constraint'     => DB_ERROR_ALREADY_EXISTS,
			'database ".+" does not exist$'       => DB_ERROR_NOSUCHDB,
			'(divide|division) by zero$'          => DB_ERROR_DIVZERO,
			'pg_atoi: error in .*: can\'t parse ' => DB_ERROR_INVALID_NUMBER,
			'ttribute [\"\'].*[\"\'] not found|Relation [\"\'].*[\"\'] does not have attribute [\"\'].*[\"\']' => DB_ERROR_NOSUCHFIELD,
			'(parser: parse|syntax) error at or near \"'   => DB_ERROR_SYNTAX,
			'referential integrity violation'     => DB_ERROR_CONSTRAINT,
			'deadlock detected$'                  => DB_ERROR_DEADLOCK,
			'canceling statement due to statement timeout$' => DB_ERROR_STATEMENT_TIMEOUT,
			'could not serialize access due to'   => DB_ERROR_SERIALIZATION_FAILURE
		);
	reset($error_regexps);
	while (list($regexp,$code) = each($error_regexps)) {
		if (preg_match("/$regexp/mi", $errormsg)) {
			return $code;
		}
	}
	// Fall back to DB_ERROR if there was no mapping.
	return DB_ERROR;
}

function adodb_error_odbc()
{
static $MAP = array(
            '01004' => DB_ERROR_TRUNCATED,
            '07001' => DB_ERROR_MISMATCH,
            '21S01' => DB_ERROR_MISMATCH,
            '21S02' => DB_ERROR_MISMATCH,
            '22003' => DB_ERROR_INVALID_NUMBER,
            '22008' => DB_ERROR_INVALID_DATE,
            '22012' => DB_ERROR_DIVZERO,
            '23000' => DB_ERROR_CONSTRAINT,
            '24000' => DB_ERROR_INVALID,
            '34000' => DB_ERROR_INVALID,
            '37000' => DB_ERROR_SYNTAX,
            '42000' => DB_ERROR_SYNTAX,
            'IM001' => DB_ERROR_UNSUPPORTED,
            'S0000' => DB_ERROR_NOSUCHTABLE,
            'S0001' => DB_ERROR_NOT_FOUND,
            'S0002' => DB_ERROR_NOSUCHTABLE,
            'S0011' => DB_ERROR_ALREADY_EXISTS,
            'S0012' => DB_ERROR_NOT_FOUND,
            'S0021' => DB_ERROR_ALREADY_EXISTS,
            'S0022' => DB_ERROR_NOT_FOUND,
			'S1000' => DB_ERROR_NOSUCHTABLE,
            'S1009' => DB_ERROR_INVALID,
            'S1090' => DB_ERROR_INVALID,
            'S1C00' => DB_ERROR_NOT_CAPABLE
        );
		return $MAP;
}

function adodb_error_ibase()
{
static $MAP = array(
            -104 => DB_ERROR_SYNTAX,
            -150 => DB_ERROR_ACCESS_VIOLATION,
            -151 => DB_ERROR_ACCESS_VIOLATION,
            -155 => DB_ERROR_NOSUCHTABLE,
            -157 => DB_ERROR_NOSUCHFIELD,
            -158 => DB_ERROR_VALUE_COUNT_ON_ROW,
            -170 => DB_ERROR_MISMATCH,
            -171 => DB_ERROR_MISMATCH,
            -172 => DB_ERROR_INVALID,
            -204 => DB_ERROR_INVALID,
            -205 => DB_ERROR_NOSUCHFIELD,
            -206 => DB_ERROR_NOSUCHFIELD,
            -208 => DB_ERROR_INVALID,
            -219 => DB_ERROR_NOSUCHTABLE,
            -297 => DB_ERROR_CONSTRAINT,
            -530 => DB_ERROR_CONSTRAINT,
            -803 => DB_ERROR_CONSTRAINT,
            -551 => DB_ERROR_ACCESS_VIOLATION,
            -552 => DB_ERROR_ACCESS_VIOLATION,
            -922 => DB_ERROR_NOSUCHDB,
            -923 => DB_ERROR_CONNECT_FAILED,
            -924 => DB_ERROR_CONNECT_FAILED
        );

		return $MAP;
}

function adodb_error_ifx()
{
static $MAP = array(
            '-201'    => DB_ERROR_SYNTAX,
            '-206'    => DB_ERROR_NOSUCHTABLE,
            '-217'    => DB_ERROR_NOSUCHFIELD,
            '-329'    => DB_ERROR_NODBSELECTED,
            '-1204'   => DB_ERROR_INVALID_DATE,
            '-1205'   => DB_ERROR_INVALID_DATE,
            '-1206'   => DB_ERROR_INVALID_DATE,
            '-1209'   => DB_ERROR_INVALID_DATE,
            '-1210'   => DB_ERROR_INVALID_DATE,
            '-1212'   => DB_ERROR_INVALID_DATE
       );

	   return $MAP;
}

function adodb_error_oci8()
{
static $MAP = array(
			 1 => DB_ERROR_ALREADY_EXISTS,
            900 => DB_ERROR_SYNTAX,
            904 => DB_ERROR_NOSUCHFIELD,
            923 => DB_ERROR_SYNTAX,
            942 => DB_ERROR_NOSUCHTABLE,
            955 => DB_ERROR_ALREADY_EXISTS,
            1476 => DB_ERROR_DIVZERO,
            1722 => DB_ERROR_INVALID_NUMBER,
            2289 => DB_ERROR_NOSUCHTABLE,
            2291 => DB_ERROR_CONSTRAINT,
            2449 => DB_ERROR_CONSTRAINT
        );

	return $MAP;
}

function adodb_error_mssql()
{
static $MAP = array(
		  208 => DB_ERROR_NOSUCHTABLE,
          2601 => DB_ERROR_ALREADY_EXISTS
       );

	return $MAP;
}

function adodb_error_sqlite()
{
static $MAP = array(
		  1 => DB_ERROR_SYNTAX
       );

	return $MAP;
}

function adodb_error_mysql()
{
static $MAP = array(
           1004 => DB_ERROR_CANNOT_CREATE,
           1005 => DB_ERROR_CANNOT_CREATE,
           1006 => DB_ERROR_CANNOT_CREATE,
           1007 => DB_ERROR_ALREADY_EXISTS,
           1008 => DB_ERROR_CANNOT_DROP,
		   1045 => DB_ERROR_ACCESS_VIOLATION,
           1046 => DB_ERROR_NODBSELECTED,
		   1049 => DB_ERROR_NOSUCHDB,
           1050 => DB_ERROR_ALREADY_EXISTS,
           1051 => DB_ERROR_NOSUCHTABLE,
           1054 => DB_ERROR_NOSUCHFIELD,
           1062 => DB_ERROR_ALREADY_EXISTS,
           1064 => DB_ERROR_SYNTAX,
           1100 => DB_ERROR_NOT_LOCKED,
           1136 => DB_ERROR_VALUE_COUNT_ON_ROW,
           1146 => DB_ERROR_NOSUCHTABLE,
           1048 => DB_ERROR_CONSTRAINT,
		    2002 => DB_ERROR_CONNECT_FAILED,
			2005 => DB_ERROR_CONNECT_FAILED
       );

	return $MAP;
}
