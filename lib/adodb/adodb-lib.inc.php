<?php
/**
 * Helper functions.
 *
 * Less commonly used functions are placed here to reduce size of adodb.inc.php.
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

global $ADODB_INCLUDED_LIB;
$ADODB_INCLUDED_LIB = 1;

/**
 * Strip the ORDER BY clause from the outer SELECT.
 *
 * @param string $sql
 *
 * @return string
 */
function adodb_strip_order_by($sql)
{
	$num = preg_match_all('/(\sORDER\s+BY\s(?:[^)](?!LIMIT))*)/is', $sql, $matches, PREG_OFFSET_CAPTURE);
	if ($num) {
		// Get the last match
		list($last_order_by, $offset) = array_pop($matches[1]);

		// If we find a ')' after the last order by, then it belongs to a
		// sub-query, not the outer SQL statement and should not be stripped
		if (strpos($sql, ')', $offset) === false) {
			$sql = str_replace($last_order_by, '', $sql);
		}
	}
	return $sql;
}

function adodb_probetypes($array,&$types,$probe=8)
{
// probe and guess the type
	$types = array();
	if ($probe > sizeof($array)) $max = sizeof($array);
	else $max = $probe;


	for ($j=0;$j < $max; $j++) {
		$row = $array[$j];
		if (!$row) break;
		$i = -1;
		foreach($row as $v) {
			$i += 1;

			if (isset($types[$i]) && $types[$i]=='C') continue;

			//print " ($i ".$types[$i]. "$v) ";
			$v = trim($v);

			if (!preg_match('/^[+-]{0,1}[0-9\.]+$/',$v)) {
				$types[$i] = 'C'; // once C, always C

				continue;
			}
			if ($j == 0) {
			// If empty string, we presume is character
			// test for integer for 1st row only
			// after that it is up to testing other rows to prove
			// that it is not an integer
				if (strlen($v) == 0) $types[$i] = 'C';
				if (strpos($v,'.') !== false) $types[$i] = 'N';
				else $types[$i] = 'I';
				continue;
			}

			if (strpos($v,'.') !== false) $types[$i] = 'N';

		}
	}

}

function adodb_transpose(&$arr, &$newarr, &$hdr, $fobjs)
{
	$oldX = sizeof(reset($arr));
	$oldY = sizeof($arr);

	if ($hdr) {
		$startx = 1;
		$hdr = array('Fields');
		for ($y = 0; $y < $oldY; $y++) {
			$hdr[] = $arr[$y][0];
		}
	} else
		$startx = 0;

	for ($x = $startx; $x < $oldX; $x++) {
		if ($fobjs) {
			$o = $fobjs[$x];
			$newarr[] = array($o->name);
		} else
			$newarr[] = array();

		for ($y = 0; $y < $oldY; $y++) {
			$newarr[$x-$startx][] = $arr[$y][$x];
		}
	}
}


function _adodb_replace($zthis, $table, $fieldArray, $keyCol, $autoQuote, $has_autoinc)
{
	// Add Quote around table name to support use of spaces / reserved keywords
	$table=sprintf('%s%s%s', $zthis->nameQuote,$table,$zthis->nameQuote);

	if (count($fieldArray) == 0) return 0;

	if (!is_array($keyCol)) {
		$keyCol = array($keyCol);
	}
	$uSet = '';
	foreach($fieldArray as $k => $v) {
		if ($v === null) {
			$v = 'NULL';
			$fieldArray[$k] = $v;
		} else if ($autoQuote && /*!is_numeric($v) /*and strncmp($v,"'",1) !== 0 -- sql injection risk*/ strcasecmp($v,$zthis->null2null)!=0) {
			$v = $zthis->qstr($v);
			$fieldArray[$k] = $v;
		}
		if (in_array($k,$keyCol)) continue; // skip UPDATE if is key

		// Add Quote around column name to support use of spaces / reserved keywords
		$uSet .= sprintf(',%s%s%s=%s',$zthis->nameQuote,$k,$zthis->nameQuote,$v);
	}
	$uSet = ltrim($uSet, ',');

	// Add Quote around column name in where clause
	$where = '';
	foreach ($keyCol as $v) {
		if (isset($fieldArray[$v])) {
			$where .= sprintf(' and %s%s%s=%s ', $zthis->nameQuote,$v,$zthis->nameQuote,$fieldArray[$v]);
		}
	}
	if ($where) {
		$where = substr($where, 5);
	}

	if ($uSet && $where) {
		$update = "UPDATE $table SET $uSet WHERE $where";
		$rs = $zthis->Execute($update);

		if ($rs) {
			if ($zthis->poorAffectedRows) {
				// The Select count(*) wipes out any errors that the update would have returned.
				// PHPLens Issue No: 5696
				if ($zthis->ErrorNo()<>0) return 0;

				// affected_rows == 0 if update field values identical to old values
				// for mysql - which is silly.
				$cnt = $zthis->GetOne("select count(*) from $table where $where");
				if ($cnt > 0) return 1; // record already exists
			} else {
				if (($zthis->Affected_Rows()>0)) return 1;
			}
		} else
			return 0;
	}

	$iCols = $iVals = '';
	foreach($fieldArray as $k => $v) {
		if ($has_autoinc && in_array($k,$keyCol)) continue; // skip autoinc col

		// Add Quote around Column Name
		$iCols .= sprintf(',%s%s%s',$zthis->nameQuote,$k,$zthis->nameQuote);
		$iVals .= ",$v";
	}
	$iCols = ltrim($iCols, ',');
	$iVals = ltrim($iVals, ',');

	$insert = "INSERT INTO $table ($iCols) VALUES ($iVals)";
	$rs = $zthis->Execute($insert);
	return ($rs) ? 2 : 0;
}

function _adodb_getmenu($zthis, $name,$defstr='',$blank1stItem=true,$multiple=false,
			$size=0, $selectAttr='',$compareFields0=true)
{
	global $ADODB_FETCH_MODE;

	$s = _adodb_getmenu_select($name, $defstr, $blank1stItem, $multiple, $size, $selectAttr);

	$hasvalue = $zthis->FieldCount() > 1;
	if (!$hasvalue) {
		$compareFields0 = true;
	}

	$value = '';
	while(!$zthis->EOF) {
		$zval = rtrim(reset($zthis->fields));

		if ($blank1stItem && $zval == "") {
			$zthis->MoveNext();
			continue;
		}

		if ($hasvalue) {
			if ($ADODB_FETCH_MODE == ADODB_FETCH_ASSOC) {
				// Get 2nd field's value regardless of its name
				$zval2 = current(array_slice($zthis->fields, 1, 1));
			} else {
				// With NUM or BOTH fetch modes, we have a numeric index
				$zval2 = $zthis->fields[1];
			}
			$zval2 = trim($zval2);
			$value = 'value="' . htmlspecialchars($zval2) . '"';
		}

		/** @noinspection PhpUndefinedVariableInspection */
		$s .= _adodb_getmenu_option($defstr, $compareFields0 ? $zval : $zval2, $value, $zval);

		$zthis->MoveNext();
	} // while

	return $s ."\n</select>\n";
}

function _adodb_getmenu_gp($zthis, $name,$defstr='',$blank1stItem=true,$multiple=false,
			$size=0, $selectAttr='',$compareFields0=true)
{
	global $ADODB_FETCH_MODE;

	$s = _adodb_getmenu_select($name, $defstr, $blank1stItem, $multiple, $size, $selectAttr);

	$hasvalue = $zthis->FieldCount() > 1;
	$hasgroup = $zthis->FieldCount() > 2;
	if (!$hasvalue) {
		$compareFields0 = true;
	}

	$value = '';
	$optgroup = null;
	$firstgroup = true;
	while(!$zthis->EOF) {
		$zval = rtrim(reset($zthis->fields));
		$group = '';

		if ($blank1stItem && $zval=="") {
			$zthis->MoveNext();
			continue;
		}

		if ($hasvalue) {
			if ($ADODB_FETCH_MODE == ADODB_FETCH_ASSOC) {
				// Get 2nd field's value regardless of its name
				$fields = array_slice($zthis->fields, 1);
				$zval2 = current($fields);
				if ($hasgroup) {
					$group = trim(next($fields));
				}
			} else {
				// With NUM or BOTH fetch modes, we have a numeric index
				$zval2 = $zthis->fields[1];
				if ($hasgroup) {
					$group = trim($zthis->fields[2]);
				}
			}
			$zval2 = trim($zval2);
			$value = "value='".htmlspecialchars($zval2)."'";
		}

		if ($optgroup != $group) {
			$optgroup = $group;
			if ($firstgroup) {
				$firstgroup = false;
			} else {
				$s .="\n</optgroup>";
			}
			$s .="\n<optgroup label='". htmlspecialchars($group) ."'>";
		}

		/** @noinspection PhpUndefinedVariableInspection */
		$s .= _adodb_getmenu_option($defstr, $compareFields0 ? $zval : $zval2, $value, $zval);

		$zthis->MoveNext();
	} // while

	// closing last optgroup
	if($optgroup != null) {
		$s .= "\n</optgroup>";
	}
	return $s ."\n</select>\n";
}

/**
 * Generate the opening SELECT tag for getmenu functions.
 *
 * ADOdb internal function, used by _adodb_getmenu() and _adodb_getmenu_gp().
 *
 * @param string $name
 * @param string $defstr
 * @param bool   $blank1stItem
 * @param bool   $multiple
 * @param int    $size
 * @param string $selectAttr
 *
 * @return string HTML
 */
function _adodb_getmenu_select($name, $defstr = '', $blank1stItem = true,
							   $multiple = false, $size = 0, $selectAttr = '')
{
	if ($multiple || is_array($defstr)) {
		if ($size == 0 ) {
			$size = 5;
		}
		$attr = ' multiple size="' . $size . '"';
		if (!strpos($name,'[]')) {
			$name .= '[]';
		}
	} elseif ($size) {
		$attr = ' size="' . $size . '"';
	} else {
		$attr = '';
	}

	$html = '<select name="' . $name . '"' . $attr . ' ' . $selectAttr . '>';
	if ($blank1stItem) {
		if (is_string($blank1stItem)) {
			$barr = explode(':',$blank1stItem);
			if (sizeof($barr) == 1) {
				$barr[] = '';
			}
			$html .= "\n<option value=\"" . $barr[0] . "\">" . $barr[1] . "</option>";
		} else {
			$html .= "\n<option></option>";
		}
	}

	return $html;
}

/**
 * Print the OPTION tags for getmenu functions.
 *
 * ADOdb internal function, used by _adodb_getmenu() and _adodb_getmenu_gp().
 *
 * @param string $defstr  Default values
 * @param string $compare Value to compare against defaults
 * @param string $value   Ready-to-print `value="xxx"` (or empty) string
 * @param string $display Display value
 *
 * @return string HTML
 */
function _adodb_getmenu_option($defstr, $compare, $value, $display)
{
	if (   is_array($defstr) && in_array($compare, $defstr)
		|| !is_array($defstr) && strcasecmp($compare, $defstr) == 0
	) {
		$selected = ' selected="selected"';
	} else {
		$selected = '';
	}

	return "\n<option $value$selected>" . htmlspecialchars($display) . '</option>';
}

/*
	Count the number of records this sql statement will return by using
	query rewriting heuristics...

	Does not work with UNIONs, except with postgresql and oracle.

	Usage:

	$conn->Connect(...);
	$cnt = _adodb_getcount($conn, $sql);

*/
function _adodb_getcount($zthis, $sql,$inputarr=false,$secs2cache=0)
{
	$qryRecs = 0;

	/*
	* These databases require a "SELECT * FROM (SELECT" type
	* statement to have an alias for the result
	*/
	$requiresAlias = '';
	$requiresAliasArray = array('postgres9','postgres','mysql','mysqli','mssql','mssqlnative','sqlsrv');
	if (in_array($zthis->databaseType,$requiresAliasArray)
		|| in_array($zthis->dsnType,$requiresAliasArray)
	) {
		$requiresAlias = '_ADODB_ALIAS_';
	}

	if (!empty($zthis->_nestedSQL)
		|| preg_match("/^\s*SELECT\s+DISTINCT/is", $sql)
		|| preg_match('/\s+GROUP\s+BY\s+/is',$sql)
		|| preg_match('/\s+UNION\s+/is',$sql)
	) {
		$rewritesql = adodb_strip_order_by($sql);

		// ok, has SELECT DISTINCT or GROUP BY so see if we can use a table alias
		// but this is only supported by oracle and postgresql...
		if ($zthis->dataProvider == 'oci8') {
			// Allow Oracle hints to be used for query optimization, Chris Wrye
			if (preg_match('#/\\*+.*?\\*\\/#', $sql, $hint)) {
				$rewritesql = "SELECT ".$hint[0]." COUNT(*) FROM (".$rewritesql.")";
			} else
				$rewritesql = "SELECT COUNT(*) FROM (".$rewritesql.")";
		} else {
			$rewritesql = "SELECT COUNT(*) FROM ($rewritesql) $requiresAlias";
		}

	} else {
		// Replace 'SELECT ... FROM' with 'SELECT COUNT(*) FROM'
		// Parse the query one char at a time starting after the SELECT
		// to find the FROM clause's position, ignoring any sub-queries.
		$start = stripos($sql, 'SELECT') + 7;
		if ($start === false) {
			// Not a SELECT statement - probably should trigger an exception here
			return 0;
		}
		$len = strlen($sql);
		$numParentheses = 0;
		for ($pos = $start; $pos < $len; $pos++) {
			switch ($sql[$pos]) {
				case '(': $numParentheses++; continue 2;
				case ')': $numParentheses--; continue 2;
			}
			// Ignore whatever is between parentheses (sub-queries)
			if ($numParentheses > 0) {
				continue;
			}
			// Exit loop if 'FROM' keyword was found
			if (strtoupper(substr($sql, $pos, 4)) == 'FROM') {
				break;
			}
		}
		$rewritesql = 'SELECT COUNT(*) ' . substr($sql, $pos);

		// fix by alexander zhukov, alex#unipack.ru, because count(*) and 'order by' fails
		// with mssql, access and postgresql. Also a good speedup optimization - skips sorting!
		// also see PHPLens Issue No: 12752
		$rewritesql = adodb_strip_order_by($rewritesql);
	}

	if (isset($rewritesql) && $rewritesql != $sql) {
		if (preg_match('/\sLIMIT\s+[0-9]+/i',$sql,$limitarr)) {
			$rewritesql .= $limitarr[0];
		}

		if ($secs2cache) {
			// we only use half the time of secs2cache because the count can quickly
			// become inaccurate if new records are added
			$qryRecs = $zthis->CacheGetOne($secs2cache/2,$rewritesql,$inputarr);

		} else {
			$qryRecs = $zthis->GetOne($rewritesql,$inputarr);
		}
		if ($qryRecs !== false) return $qryRecs;
	}

	//--------------------------------------------
	// query rewrite failed - so try slower way...

	// strip off unneeded ORDER BY if no UNION
	if (preg_match('/\s*UNION\s*/is', $sql)) {
		$rewritesql = $sql;
	} else {
		$rewritesql = adodb_strip_order_by($sql);
	}

	if (preg_match('/\sLIMIT\s+[0-9]+/i',$sql,$limitarr)) {
		$rewritesql .= $limitarr[0];
	}

	if ($secs2cache) {
		$rstest = $zthis->CacheExecute($secs2cache,$rewritesql,$inputarr);
		if (!$rstest) $rstest = $zthis->CacheExecute($secs2cache,$sql,$inputarr);
	} else {
		$rstest = $zthis->Execute($rewritesql,$inputarr);
		if (!$rstest) $rstest = $zthis->Execute($sql,$inputarr);
	}
	if ($rstest) {
		$qryRecs = $rstest->RecordCount();
		if ($qryRecs == -1) {
			// some databases will return -1 on MoveLast() - change to MoveNext()
			while(!$rstest->EOF) {
				$rstest->MoveNext();
			}
			$qryRecs = $rstest->_currentRow;
		}
		$rstest->Close();
		if ($qryRecs == -1) return 0;
	}
	return $qryRecs;
}

/**
 * Execute query with pagination including record count.
 *
 * This code might not work with SQL that has UNION in it.
 * Also if you are using cachePageExecute(), there is a strong possibility that
 * data will get out of sync. cachePageExecute() should only be used with
 * tables that rarely change.
 *
 * @param ADOConnection $zthis      Connection
 * @param string        $sql        Query to execute
 * @param int           $nrows      Number of rows per page
 * @param int           $page       Page number to retrieve (1-based)
 * @param array         $inputarr   Array of bind variables
 * @param int           $secs2cache Time-to-live of the cache (in seconds), 0 to force query execution
 *
 * @return ADORecordSet|bool
 *
 * @author Cornel G <conyg@fx.ro>
 */
function _adodb_pageexecute_all_rows($zthis, $sql, $nrows, $page, $inputarr=false, $secs2cache=0)
{
	$atfirstpage = false;
	$atlastpage = false;

	// If an invalid nrows is supplied, assume a default value of 10 rows per page
	if (!isset($nrows) || $nrows <= 0) $nrows = 10;

	$qryRecs = _adodb_getcount($zthis,$sql,$inputarr,$secs2cache);
	$lastpageno = (int) ceil($qryRecs / $nrows);

	// Check whether $page is the last page or if we are trying to retrieve
	// a page number greater than the last one.
	if ($page >= $lastpageno) {
		$page = $lastpageno;
		$atlastpage = true;
	}

	// If page number <= 1, then we are at the first page
	if (empty($page) || $page <= 1) {
		$page = 1;
		$atfirstpage = true;
	}

	// We get the data we want
	$offset = $nrows * ($page-1);
	if ($secs2cache > 0)
		$rsreturn = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $offset, $inputarr);
	else
		$rsreturn = $zthis->SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);


	// Before returning the RecordSet, we set the pagination properties we need
	if ($rsreturn) {
		$rsreturn->_maxRecordCount = $qryRecs;
		$rsreturn->rowsPerPage = $nrows;
		$rsreturn->AbsolutePage($page);
		$rsreturn->AtFirstPage($atfirstpage);
		$rsreturn->AtLastPage($atlastpage);
		$rsreturn->LastPageNo($lastpageno);
	}
	return $rsreturn;
}

/**
 * Execute query with pagination without last page information.
 *
 * This code might not work with SQL that has UNION in it.
 * Also if you are using cachePageExecute(), there is a strong possibility that
 * data will get out of sync. cachePageExecute() should only be used with
 * tables that rarely change.
 *
 * @param ADOConnection $zthis      Connection
 * @param string        $sql        Query to execute
 * @param int           $nrows      Number of rows per page
 * @param int           $page       Page number to retrieve (1-based)
 * @param array         $inputarr   Array of bind variables
 * @param int           $secs2cache Time-to-live of the cache (in seconds), 0 to force query execution
 *
 * @return ADORecordSet|bool
 *
 * @author Iván Oliva
 */
function _adodb_pageexecute_no_last_page($zthis, $sql, $nrows, $page, $inputarr=false, $secs2cache=0)
{
	$atfirstpage = false;
	$atlastpage = false;

	if (!isset($page) || $page <= 1) {
		// If page number <= 1, then we are at the first page
		$page = 1;
		$atfirstpage = true;
	}
	if ($nrows <= 0) {
		// If an invalid nrows is supplied, we assume a default value of 10 rows per page
		$nrows = 10;
	}

	$pagecounteroffset = ($page * $nrows) - $nrows;

	// To find out if there are more pages of rows, simply increase the limit or
	// nrows by 1 and see if that number of records was returned. If it was,
	// then we know there is at least one more page left, otherwise we are on
	// the last page. Therefore allow non-Count() paging with single queries
	// rather than three queries as was done before.
	$test_nrows = $nrows + 1;
	if ($secs2cache > 0) {
		$rsreturn = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $pagecounteroffset, $inputarr);
	} else {
		$rsreturn = $zthis->SelectLimit($sql, $test_nrows, $pagecounteroffset, $inputarr, $secs2cache);
	}

	// Now check to see if the number of rows returned was the higher value we asked for or not.
	if ( $rsreturn->_numOfRows == $test_nrows ) {
		// Still at least 1 more row, so we are not on last page yet...
		// Remove the last row from the RS.
		$rsreturn->_numOfRows = ( $rsreturn->_numOfRows - 1 );
	} elseif ( $rsreturn->_numOfRows == 0 && $page > 1 ) {
		// Likely requested a page that doesn't exist, so need to find the last
		// page and return it. Revert to original method and loop through pages
		// until we find some data...
		$pagecounter = $page + 1;

		$rstest = $rsreturn;
		if ($rstest) {
			while ($rstest && $rstest->EOF && $pagecounter > 0) {
				$atlastpage = true;
				$pagecounter--;
				$pagecounteroffset = $nrows * ($pagecounter - 1);
				$rstest->Close();
				if ($secs2cache>0) {
					$rstest = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $pagecounteroffset, $inputarr);
				}
				else {
					$rstest = $zthis->SelectLimit($sql, $nrows, $pagecounteroffset, $inputarr, $secs2cache);
				}
			}
			if ($rstest) $rstest->Close();
		}
		if ($atlastpage) {
			// If we are at the last page or beyond it, we are going to retrieve it
			$page = $pagecounter;
			if ($page == 1) {
				// We have to do this again in case the last page is the same as
				// the first page, that is, the recordset has only 1 page.
				$atfirstpage = true;
			}
		}
		// We get the data we want
		$offset = $nrows * ($page-1);
		if ($secs2cache > 0) {
			$rsreturn = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $offset, $inputarr);
		}
		else {
			$rsreturn = $zthis->SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);
		}
	} elseif ( $rsreturn->_numOfRows < $test_nrows ) {
		// Rows is less than what we asked for, so must be at the last page.
		$atlastpage = true;
	}

	// Before returning the RecordSet, we set the pagination properties we need
	if ($rsreturn) {
		$rsreturn->rowsPerPage = $nrows;
		$rsreturn->AbsolutePage($page);
		$rsreturn->AtFirstPage($atfirstpage);
		$rsreturn->AtLastPage($atlastpage);
	}
	return $rsreturn;
}

/**
 * Performs case conversion and quoting of the given field name.
 *
 * See Global variable $ADODB_QUOTE_FIELDNAMES.
 *
 * @param ADOConnection $zthis
 * @param string $fieldName
 *
 * @return string Quoted field name
 */
function _adodb_quote_fieldname($zthis, $fieldName)
{
	global $ADODB_QUOTE_FIELDNAMES;

	// Case conversion - defaults to UPPER
	$case = is_bool($ADODB_QUOTE_FIELDNAMES) ? 'UPPER' : $ADODB_QUOTE_FIELDNAMES;
	switch ($case) {
		case 'LOWER':
			$fieldName = strtolower($fieldName);
			break;
		case 'NATIVE':
			// Do nothing
			break;
		case 'UPPER':
		case 'BRACKETS':
		default:
			$fieldName = strtoupper($fieldName);
			break;
	}

	// Quote field if requested, or necessary (field contains space)
	if ($ADODB_QUOTE_FIELDNAMES || strpos($fieldName, ' ') !== false ) {
		if ($ADODB_QUOTE_FIELDNAMES === 'BRACKETS') {
			return $zthis->leftBracket . $fieldName . $zthis->rightBracket;
		} else {
			return $zthis->nameQuote . $fieldName . $zthis->nameQuote;
		}
	} else {
		return $fieldName;
	}
}

function _adodb_getupdatesql(&$zthis, $rs, $arrFields, $forceUpdate=false, $force=2)
{
	if (!$rs) {
		printf(ADODB_BAD_RS,'GetUpdateSQL');
		return false;
	}

	$fieldUpdatedCount = 0;
	if (is_array($arrFields))
		$arrFields = array_change_key_case($arrFields,CASE_UPPER);

	$hasnumeric = isset($rs->fields[0]);
	$setFields = '';

	// Loop through all of the fields in the recordset
	for ($i=0, $max=$rs->fieldCount(); $i < $max; $i++) {
		// Get the field from the recordset
		$field = $rs->fetchField($i);

		// If the recordset field is one
		// of the fields passed in then process.
		$upperfname = strtoupper($field->name);
		if (adodb_key_exists($upperfname, $arrFields, $force)) {

			// If the existing field value in the recordset
			// is different from the value passed in then
			// go ahead and append the field name and new value to
			// the update query.

			if ($hasnumeric) $val = $rs->fields[$i];
			else if (isset($rs->fields[$upperfname])) $val = $rs->fields[$upperfname];
			else if (isset($rs->fields[$field->name])) $val = $rs->fields[$field->name];
			else if (isset($rs->fields[strtolower($upperfname)])) $val = $rs->fields[strtolower($upperfname)];
			else $val = '';

			if ($forceUpdate || $val !== $arrFields[$upperfname]) {
				// Set the counter for the number of fields that will be updated.
				$fieldUpdatedCount++;

				// Based on the datatype of the field
				// Format the value properly for the database
				$type = $rs->metaType($field->type);

				if ($type == 'null') {
					$type = 'C';
				}

				$fnameq = _adodb_quote_fieldname($zthis, $field->name);

				//********************************************************//
				if (is_null($arrFields[$upperfname])
					|| (empty($arrFields[$upperfname]) && strlen($arrFields[$upperfname]) == 0)
					|| $arrFields[$upperfname] === $zthis->null2null
					) {

					switch ($force) {

						//case 0:
						//	// Ignore empty values. This is already handled in "adodb_key_exists" function.
						//	break;

						case 1:
							// set null
							$setFields .= $fnameq . " = null, ";
							break;

						case 2:
							// set empty
							$arrFields[$upperfname] = "";
							$setFields .= _adodb_column_sql($zthis, 'U', $type, $upperfname, $fnameq, $arrFields);
							break;

						default:
						case 3:
							// set the value that was given in array, so you can give both null and empty values
							if (is_null($arrFields[$upperfname]) || $arrFields[$upperfname] === $zthis->null2null) {
								$setFields .= $fnameq . " = null, ";
							} else {
								$setFields .= _adodb_column_sql($zthis, 'U', $type, $upperfname, $fnameq, $arrFields);
							}
							break;

						case ADODB_FORCE_NULL_AND_ZERO:

							switch ($type) {
								case 'N':
								case 'I':
								case 'L':
									$setFields .= $fnameq . ' = 0, ';
									break;
								default:
									$setFields .= $fnameq . ' = null, ';
									break;
							}
							break;

					}
				//********************************************************//
				} else {
					// we do this so each driver can customize the sql for
					// DB specific column types.
					// Oracle needs BLOB types to be handled with a returning clause
					// postgres has special needs as well
					$setFields .= _adodb_column_sql($zthis, 'U', $type, $upperfname, $fnameq, $arrFields);
				}
			}
		}
	}

	// If there were any modified fields then build the rest of the update query.
	if ($fieldUpdatedCount > 0 || $forceUpdate) {
		// Get the table name from the existing query.
		if (!empty($rs->tableName)) {
			$tableName = $rs->tableName;
		} else {
			preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $rs->sql, $tableName);
			$tableName = $tableName[1];
		}

		// Get the full where clause excluding the word "WHERE" from the existing query.
		preg_match('/\sWHERE\s(.*)/is', $rs->sql, $whereClause);

		$discard = false;
		// not a good hack, improvements?
		if ($whereClause) {
			if (preg_match('/\s(ORDER\s.*)/is', $whereClause[1], $discard));
			else if (preg_match('/\s(LIMIT\s.*)/is', $whereClause[1], $discard));
			else if (preg_match('/\s(FOR UPDATE.*)/is', $whereClause[1], $discard));
			else preg_match('/\s.*(\) WHERE .*)/is', $whereClause[1], $discard); # see https://sourceforge.net/p/adodb/bugs/37/
		} else {
			$whereClause = array(false, false);
		}

		if ($discard) {
			$whereClause[1] = substr($whereClause[1], 0, strlen($whereClause[1]) - strlen($discard[1]));
		}

		$sql = 'UPDATE '.$tableName.' SET '.substr($setFields, 0, -2);
		if (strlen($whereClause[1]) > 0) {
			$sql .= ' WHERE '.$whereClause[1];
		}
		return $sql;
	} else {
		return false;
	}
}

function adodb_key_exists($key, $arr,$force=2)
{
	if ($force<=0) {
		// the following is the old behaviour where null or empty fields are ignored
		return (!empty($arr[$key])) || (isset($arr[$key]) && strlen($arr[$key])>0);
	}

	if (isset($arr[$key]))
		return true;
	## null check below
	return array_key_exists($key,$arr);
}

/**
 * There is a special case of this function for the oci8 driver.
 * The proper way to handle an insert w/ a blob in oracle requires
 * a returning clause with bind variables and a descriptor blob.
 *
 *
 */
function _adodb_getinsertsql(&$zthis, $rs, $arrFields, $force=2)
{
static $cacheRS = false;
static $cacheSig = 0;
static $cacheCols;

	$tableName = '';
	$values = '';
	$fields = '';
	if (is_array($arrFields))
		$arrFields = array_change_key_case($arrFields,CASE_UPPER);
	$fieldInsertedCount = 0;

	if (is_string($rs)) {
		//ok we have a table name
		//try and get the column info ourself.
		$tableName = $rs;

		//we need an object for the recordSet
		//because we have to call MetaType.
		//php can't do a $rsclass::MetaType()
		$rsclass = $zthis->rsPrefix.$zthis->databaseType;
		$recordSet = new $rsclass(ADORecordSet::DUMMY_QUERY_ID, $zthis->fetchMode);
		$recordSet->connection = $zthis;

		if (is_string($cacheRS) && $cacheRS == $rs) {
			$columns = $cacheCols;
		} else {
			$columns = $zthis->MetaColumns( $tableName );
			$cacheRS = $tableName;
			$cacheCols = $columns;
		}
	} else if (is_subclass_of($rs, 'adorecordset')) {
		if (isset($rs->insertSig) && is_integer($cacheRS) && $cacheRS == $rs->insertSig) {
			$columns = $cacheCols;
		} else {
			$columns = [];
			for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++)
				$columns[] = $rs->FetchField($i);
			$cacheRS = $cacheSig;
			$cacheCols = $columns;
			$rs->insertSig = $cacheSig++;
		}
		$recordSet = $rs;

	} else {
		printf(ADODB_BAD_RS,'GetInsertSQL');
		return false;
	}

	// Loop through all of the fields in the recordset
	foreach( $columns as $field ) {
		$upperfname = strtoupper($field->name);
		if (adodb_key_exists($upperfname, $arrFields, $force)) {
			$bad = false;
			$fnameq = _adodb_quote_fieldname($zthis, $field->name);
			$type = $recordSet->MetaType($field->type);

			/********************************************************/
			if (is_null($arrFields[$upperfname])
				|| (empty($arrFields[$upperfname]) && strlen($arrFields[$upperfname]) == 0)
				|| $arrFields[$upperfname] === $zthis->null2null
			) {
				switch ($force) {

					case ADODB_FORCE_IGNORE: // we must always set null if missing
						$bad = true;
						break;

					case ADODB_FORCE_NULL:
						$values .= "null, ";
						break;

					case ADODB_FORCE_EMPTY:
						//Set empty
						$arrFields[$upperfname] = "";
						$values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq, $arrFields);
						break;

					default:
					case ADODB_FORCE_VALUE:
						//Set the value that was given in array, so you can give both null and empty values
						if (is_null($arrFields[$upperfname]) || $arrFields[$upperfname] === $zthis->null2null) {
							$values .= "null, ";
						} else {
							$values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq, $arrFields);
						}
						break;

					case ADODB_FORCE_NULL_AND_ZERO:
						switch ($type) {
							case 'N':
							case 'I':
							case 'L':
								$values .= '0, ';
								break;
							default:
								$values .= "null, ";
								break;
						}
						break;

				} // switch

				/*********************************************************/
			} else {
				//we do this so each driver can customize the sql for
				//DB specific column types.
				//Oracle needs BLOB types to be handled with a returning clause
				//postgres has special needs as well
				$values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq, $arrFields);
			}

			if ($bad) {
				continue;
			}
			// Set the counter for the number of fields that will be inserted.
			$fieldInsertedCount++;

			// Get the name of the fields to insert
			$fields .= $fnameq . ", ";
		}
	}


	// If there were any inserted fields then build the rest of the insert query.
	if ($fieldInsertedCount <= 0) return false;

	// Get the table name from the existing query.
	if (!$tableName) {
		if (!empty($rs->tableName)) $tableName = $rs->tableName;
		else if (preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $rs->sql, $tableName))
			$tableName = $tableName[1];
		else
			return false;
	}

	// Strip off the comma and space on the end of both the fields
	// and their values.
	$fields = substr($fields, 0, -2);
	$values = substr($values, 0, -2);

	// Append the fields and their values to the insert query.
	return 'INSERT INTO '.$tableName.' ( '.$fields.' ) VALUES ( '.$values.' )';
}


/**
 * This private method is used to help construct
 * the update/sql which is generated by GetInsertSQL and GetUpdateSQL.
 * It handles the string construction of 1 column -> sql string based on
 * the column type.  We want to do 'safe' handling of BLOBs
 *
 * @param string the type of sql we are trying to create
 *                'I' or 'U'.
 * @param string column data type from the db::MetaType() method
 * @param string the column name
 * @param array the column value
 *
 * @return string
 *
 */
function _adodb_column_sql_oci8(&$zthis,$action, $type, $fname, $fnameq, $arrFields)
{
	// Based on the datatype of the field
	// Format the value properly for the database
	switch ($type) {
		case 'B':
			//in order to handle Blobs correctly, we need
			//to do some magic for Oracle

			//we need to create a new descriptor to handle
			//this properly
			if (!empty($zthis->hasReturningInto)) {
				if ($action == 'I') {
					$sql = 'empty_blob(), ';
				} else {
					$sql = $fnameq . '=empty_blob(), ';
				}
				//add the variable to the returning clause array
				//so the user can build this later in
				//case they want to add more to it
				$zthis->_returningArray[$fname] = ':xx' . $fname . 'xx';
			} else {
				if (empty($arrFields[$fname])) {
					if ($action == 'I') {
						$sql = 'empty_blob(), ';
					} else {
						$sql = $fnameq . '=empty_blob(), ';
					}
				} else {
					//this is to maintain compatibility
					//with older adodb versions.
					$sql = _adodb_column_sql($zthis, $action, $type, $fname, $fnameq, $arrFields, false);
				}
			}
			break;

		case "X":
			//we need to do some more magic here for long variables
			//to handle these correctly in oracle.

			//create a safe bind var name
			//to avoid conflicts w/ dupes.
			if (!empty($zthis->hasReturningInto)) {
				if ($action == 'I') {
					$sql = ':xx' . $fname . 'xx, ';
				} else {
					$sql = $fnameq . '=:xx' . $fname . 'xx, ';
				}
				//add the variable to the returning clause array
				//so the user can build this later in
				//case they want to add more to it
				$zthis->_returningArray[$fname] = ':xx' . $fname . 'xx';
			} else {
				//this is to maintain compatibility
				//with older adodb versions.
				$sql = _adodb_column_sql($zthis, $action, $type, $fname, $fnameq, $arrFields, false);
			}
			break;

		default:
			$sql = _adodb_column_sql($zthis, $action, $type, $fname, $fnameq, $arrFields, false);
			break;
	}

	return $sql;
}

function _adodb_column_sql(&$zthis, $action, $type, $fname, $fnameq, $arrFields, $recurse=true)
{

	if ($recurse) {
		switch($zthis->dataProvider) {
		case 'postgres':
			if ($type == 'L') $type = 'C';
			break;
		case 'oci8':
			return _adodb_column_sql_oci8($zthis, $action, $type, $fname, $fnameq, $arrFields);

		}
	}

	switch($type) {
		case "C":
		case "X":
		case 'B':
			$val = $zthis->qstr($arrFields[$fname]);
			break;

		case "D":
			$val = $zthis->DBDate($arrFields[$fname]);
			break;

		case "T":
			$val = $zthis->DBTimeStamp($arrFields[$fname]);
			break;

		case "N":
			$val = $arrFields[$fname];
			if (!is_numeric($val)) $val = str_replace(',', '.', (float)$val);
			break;

		case "I":
		case "R":
			$val = $arrFields[$fname];
			if (!is_numeric($val)) $val = (integer) $val;
			break;

		default:
			$val = str_replace(array("'"," ","("),"",$arrFields[$fname]); // basic sql injection defence
			if (empty($val)) $val = '0';
			break;
	}

	if ($action == 'I') return $val . ", ";

	return $fnameq . "=" . $val . ", ";
}


/**
* Replaces standard _execute when debug mode is enabled
*
* @param ADOConnection   $zthis    An ADOConnection object
* @param string|string[] $sql      A string or array of SQL statements
* @param string[]|null   $inputarr An optional array of bind parameters
*
* @return  handle|void A handle to the executed query
*/
function _adodb_debug_execute($zthis, $sql, $inputarr)
{
	// Unpack the bind parameters
	$ss = '';
	if ($inputarr) {
		foreach ($inputarr as $kk => $vv) {
			if (is_string($vv) && strlen($vv) > 64) {
				$vv = substr($vv, 0, 64) . '...';
			}
			if (is_null($vv)) {
				$ss .= "($kk=>null) ";
			} else {
				if (is_array($vv)) {
					$vv = sprintf("Array Of Values: [%s]", implode(',', $vv));
				}
				$ss .= "($kk=>'$vv') ";
			}
		}
		$ss = "[ $ss ]";
	}

	$sqlTxt = is_array($sql) ? $sql[0] : $sql;

	// Remove newlines and tabs, compress repeating spaces
	$sqlTxt = preg_replace('/\s+/', ' ', $sqlTxt);

	// check if running from browser or command-line
	$inBrowser = isset($_SERVER['HTTP_USER_AGENT']);

	$myDatabaseType = $zthis->databaseType;
	if (!isset($zthis->dsnType)) {
		// Append the PDO driver name
		$myDatabaseType .= '-' . $zthis->dsnType;
	}

	if ($inBrowser) {
		if ($ss) {
			// Default formatting for passed parameter
			$ss = sprintf('<code class="adodb-debug">%s</code>', htmlspecialchars($ss));
		}
		if ($zthis->debug === -1) {
			$outString = "<br class='adodb-debug'>(%s):  %s &nbsp; %s<br class='adodb-debug'>";
			ADOConnection::outp(sprintf($outString, $myDatabaseType, htmlspecialchars($sqlTxt), $ss), false);
		} elseif ($zthis->debug !== -99) {
			$outString = "<hr class='adodb-debug'>(%s):  %s &nbsp; %s<hr class='adodb-debug'>";
			ADOConnection::outp(sprintf($outString, $myDatabaseType, htmlspecialchars($sqlTxt), $ss), false);
		}
	} else {
		// CLI output
		if ($zthis->debug !== -99) {
			$outString = sprintf("%s\n%s\n    %s %s \n%s\n", str_repeat('-', 78), $myDatabaseType, $sqlTxt, $ss, str_repeat('-', 78));
			ADOConnection::outp($outString, false);
		}
	}

	// Now execute the query
	$qID = $zthis->_query($sql, $inputarr);

	// Alexios Fakios notes that ErrorMsg() must be called before ErrorNo() for mssql
	// because ErrorNo() calls Execute('SELECT @ERROR'), causing recursion
	if ($zthis->databaseType == 'mssql') {
		// ErrorNo is a slow function call in mssql
		if ($emsg = $zthis->ErrorMsg()) {
			if ($err = $zthis->ErrorNo()) {
				if ($zthis->debug === -99) {
					ADOConnection::outp("<hr>\n($myDatabaseType): " . htmlspecialchars($sqlTxt) . " &nbsp; $ss\n<hr>\n", false);
				}

				ADOConnection::outp($err . ': ' . $emsg);
			}
		}
	} else {
		if (!$qID) {
			// Statement execution has failed
			if ($zthis->debug === -99) {
				if ($inBrowser) {
					$outString = "<hr class='adodb-debug'>(%s):  %s &nbsp; %s<hr class='adodb-debug'>";
					ADOConnection::outp(sprintf($outString, $myDatabaseType, htmlspecialchars($sqlTxt), $ss), false);
				} else {
					$outString = sprintf("%s\n%s\n    %s %s \n%s\n",str_repeat('-',78),$myDatabaseType,$sqlTxt,$ss,str_repeat('-',78));
					ADOConnection::outp($outString, false);
				}
			}

			// Send last error to output
			$errno = $zthis->ErrorNo();
			if ($errno) {
				ADOConnection::outp($errno . ': ' . $zthis->ErrorMsg());
			}
		}
	}

	if ($qID === false || $zthis->debug === 99) {
		_adodb_backtrace();
	}
	return $qID;
}

/**
 * Pretty print the debug_backtrace function
 *
 * @param string[]|bool $printOrArr       Whether to print the result directly or return the result
 * @param int           $maximumDepth     The maximum depth of the array to traverse
 * @param int           $elementsToIgnore The backtrace array indexes to ignore
 * @param null|bool     $ishtml           True if we are in a CGI environment, false for CLI,
 *                                        null to auto detect
 *
 * @return string Formatted backtrace
 */
function _adodb_backtrace($printOrArr=true, $maximumDepth=9999, $elementsToIgnore=0, $ishtml=null)
{
	if (!function_exists('debug_backtrace')) {
		return '';
	}

	if ($ishtml === null) {
		// Auto determine if we in a CGI enviroment
		$html = (isset($_SERVER['HTTP_USER_AGENT']));
	} else {
		$html = $ishtml;
	}

	$cgiString = "</font><font color=#808080 size=-1> %% line %4d, file: <a href=\"file:/%s\">%s</a></font>";
	$cliString = "%% line %4d, file: %s";
	$fmt = ($html) ? $cgiString : $cliString;

	$MAXSTRLEN = 128;

	$s = ($html) ? '<pre align=left>' : '';

	if (is_array($printOrArr)) {
		$traceArr = $printOrArr;
	} else {
		$traceArr = debug_backtrace();
	}

	// Remove first 2 elements that just show calls to adodb_backtrace
	array_shift($traceArr);
	array_shift($traceArr);

	// We want last element to have no indent
	$tabs = sizeof($traceArr) - 1;

	foreach ($traceArr as $arr) {
		if ($elementsToIgnore) {
			// Ignore array element at start of array
			$elementsToIgnore--;
			$tabs--;
			continue;
		}
		$maximumDepth--;
		if ($maximumDepth < 0) {
			break;
		}

		$args = array();

		if ($tabs) {
			$s .= str_repeat($html ? ' &nbsp; ' : "\t", $tabs);
			$tabs--;
		}
		if ($html) {
			$s .= '<font face="Courier New,Courier">';
		}

		if (isset($arr['class'])) {
			$s .= $arr['class'] . '.';
		}

		if (isset($arr['args'])) {
			foreach ($arr['args'] as $v) {
				if (is_null($v)) {
					$args[] = 'null';
				} elseif (is_array($v)) {
					$args[] = 'Array[' . sizeof($v) . ']';
				} elseif (is_object($v)) {
					$args[] = 'Object:' . get_class($v);
				} elseif (is_bool($v)) {
					$args[] = $v ? 'true' : 'false';
				} else {
					$v = (string)@$v;
					// Truncate
					$v = substr($v, 0, $MAXSTRLEN);
					// Remove newlines and tabs, compress repeating spaces
					$v = preg_replace('/\s+/', ' ', $v);
					// Convert htmlchars (not sure why we do this in CLI)
					$str = htmlspecialchars($v);

					if (strlen($v) > $MAXSTRLEN) {
						$str .= '...';
					}

					$args[] = $str;
				}
			}
		}
		$s .= $arr['function'] . '(' . implode(', ', $args) . ')';
		$s .= @sprintf($fmt, $arr['line'], $arr['file'], basename($arr['file']));
		$s .= "\n";
	}
	if ($html) {
		$s .= '</pre>';
	}
	if ($printOrArr) {
		print $s;
	}

	return $s;
}
