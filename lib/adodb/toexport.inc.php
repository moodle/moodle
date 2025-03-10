<?php
/**
 * Export recordsets in several formats.
 *
 * AS VARIABLE
 * $s = rs2csv($rs); # comma-separated values
 * $s = rs2tab($rs); # tab delimited
 *
 * TO A FILE
 * $f = fopen($path,'w');
 * rs2csvfile($rs,$f);
 * fclose($f);
 *
 * TO STDOUT
 * rs2csvout($rs);
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

// returns a recordset as a csv string
function rs2csv(&$rs,$addtitles=true)
{
	return _adodb_export($rs,',',',',false,$addtitles);
}

// writes recordset to csv file
function rs2csvfile(&$rs,$fp,$addtitles=true)
{
	_adodb_export($rs,',',',',$fp,$addtitles);
}

// write recordset as csv string to stdout
function rs2csvout(&$rs,$addtitles=true)
{
	$fp = fopen('php://stdout','wb');
	_adodb_export($rs,',',',',true,$addtitles);
	fclose($fp);
}

function rs2tab(&$rs,$addtitles=true)
{
	return _adodb_export($rs,"\t",',',false,$addtitles);
}

// to file pointer
function rs2tabfile(&$rs,$fp,$addtitles=true)
{
	_adodb_export($rs,"\t",',',$fp,$addtitles);
}

// to stdout
function rs2tabout(&$rs,$addtitles=true)
{
	$fp = fopen('php://stdout','wb');
	_adodb_export($rs,"\t",' ',true,$addtitles);
	if ($fp) fclose($fp);
}

function _adodb_export(&$rs,$sep,$sepreplace,$fp=false,$addtitles=true,$quote = '"',$escquote = '"',$replaceNewLine = ' ')
{
	if (!$rs) return '';
	//----------
	// CONSTANTS
	$NEWLINE = "\r\n";
	$BUFLINES = 100;
	$escquotequote = $escquote.$quote;
	$s = '';

	if ($addtitles) {
		$fieldTypes = $rs->FieldTypesArray();
		reset($fieldTypes);
		$i = 0;
		$elements = array();
		foreach ($fieldTypes as $o) {

			$v = ($o) ? $o->name : 'Field'.($i++);
			if ($escquote) $v = str_replace($quote,$escquotequote,$v);
			$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,$v))));
			$elements[] = $v;

		}
		$s .= implode($sep, $elements).$NEWLINE;
	}
	$hasNumIndex = isset($rs->fields[0]);

	$line = 0;
	$max = $rs->FieldCount();

	while (!$rs->EOF) {
		$elements = array();
		$i = 0;

		if ($hasNumIndex) {
			for ($j=0; $j < $max; $j++) {
				$v = $rs->fields[$j];
				if (!is_object($v)) $v = trim((string)$v);
				else $v = 'Object';
				if ($escquote) $v = str_replace($quote,$escquotequote,(string)$v);
				$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,$v))));

				if (strpos($v,$sep) !== false || strpos($v,$quote) !== false) $elements[] = "$quote$v$quote";
				else $elements[] = $v;
			}
		} else { // ASSOCIATIVE ARRAY
			foreach($rs->fields as $v) {
				if ($escquote) $v = str_replace($quote,$escquotequote,trim((string)$v));
				$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,(string)$v))));

				if (strpos($v,$sep) !== false || strpos($v,$quote) !== false) $elements[] = "$quote$v$quote";
				else $elements[] = $v;
			}
		}
		$s .= implode($sep, $elements).$NEWLINE;
		$rs->MoveNext();
		$line += 1;
		if ($fp && ($line % $BUFLINES) == 0) {
			if ($fp === true) echo $s;
			else fwrite($fp,$s);
			$s = '';
		}
	}

	if ($fp) {
		if ($fp === true) echo $s;
		else fwrite($fp,$s);
		$s = '';
	}

	return $s;
}
