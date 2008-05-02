<?php

/** 
 * @version V4.93 10 Oct 2006 (c) 2000-2008 John Lim (jlim#natsoft.com.my). All rights reserved.
 * Released under both BSD license and Lesser GPL library license. 
 * Whenever there is any discrepancy between the two licenses, 
 * the BSD license will take precedence. 
 *
 * Code to export recordsets in several formats:
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
		while(list(,$o) = each($fieldTypes)) {
			if (!$o) $v = '';
			else $v = $o->name;
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
				if (!is_object($v)) $v = trim($v);
				else $v = 'Object';
				if ($escquote) $v = str_replace($quote,$escquotequote,$v);
				$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,$v))));
				
				if (strpos($v,$sep) !== false || strpos($v,$quote) !== false) $elements[] = "$quote$v$quote";
				else $elements[] = $v;
			}
		} else { // ASSOCIATIVE ARRAY
			foreach($rs->fields as $v) {
				if ($escquote) $v = str_replace($quote,$escquotequote,trim($v));
				$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,$v))));
				
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
?>