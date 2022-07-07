<?php
/*
@version   v5.20.16  12-Jan-2020
@copyright (c) 2000-2013 John Lim. All rights reserved.
@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

  Latest version is available at http://adodb.org/

  Portable version of oci8 driver, to make it more similar to other database drivers.
  The main differences are

   1. that the OCI_ASSOC names are in lowercase instead of uppercase.
   2. bind variables are mapped using ? instead of :<bindvar>

   Should some emulation of RecordCount() be implemented?

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR.'/drivers/adodb-oci8.inc.php');

class ADODB_oci8po extends ADODB_oci8 {
	var $databaseType = 'oci8po';
	var $dataProvider = 'oci8';
	var $metaColumnsSQL = "select lower(cname),coltype,width, SCALE, PRECISION, NULLS, DEFAULTVAL from col where tname='%s' order by colno"; //changed by smondino@users.sourceforge. net
	var $metaTablesSQL = "select lower(table_name),table_type from cat where table_type in ('TABLE','VIEW')";

	function __construct()
	{
		$this->_hasOCIFetchStatement = ADODB_PHPVER >= 0x4200;
		# oci8po does not support adodb extension: adodb_movenext()
	}

	function Param($name,$type='C')
	{
		return '?';
	}

	function Prepare($sql,$cursor=false)
	{
		$sqlarr = explode('?',$sql);
		$sql = $sqlarr[0];
		for ($i = 1, $max = sizeof($sqlarr); $i < $max; $i++) {
			$sql .=  ':'.($i-1) . $sqlarr[$i];
		}
		return ADODB_oci8::Prepare($sql,$cursor);
	}

	function Execute($sql,$inputarr=false)
	{
		return ADOConnection::Execute($sql,$inputarr);
	}

	/**
	 * The optimizations performed by ADODB_oci8::SelectLimit() are not
	 * compatible with the oci8po driver, so we rely on the slower method
	 * from the base class.
	 * We can't properly handle prepared statements either due to preprocessing
	 * of query parameters, so we treat them as regular SQL statements.
	 */
	function SelectLimit($sql, $nrows=-1, $offset=-1, $inputarr=false, $secs2cache=0)
	{
		if(is_array($sql)) {
//			$sql = $sql[0];
		}
		return ADOConnection::SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);
	}

	// emulate handling of parameters ? ?, replacing with :bind0 :bind1
	function _query($sql,$inputarr=false)
	{
		if (is_array($inputarr)) {
			$i = 0;
			if (is_array($sql)) {
				foreach($inputarr as $v) {
					$arr['bind'.$i++] = $v;
				}
			} else {
				$sql = $this->extractBinds($sql,$inputarr);
			}
		}
		return ADODB_oci8::_query($sql,$inputarr);
	}
	/**
	* Replaces compatibility bind markers with oracle ones and returns a
	* valid sql statement
	*
	* This replaces a regexp based section of code that has been subject
	* to numerous tweaks, as more extreme test cases have appeared. This
	* is now done this like this to help maintainability and avoid the 
	* need to rely on regexp experienced maintainers
        *
	* @param	string		$sql		The sql statement
	* @param	string[]	$inputarr	The bind array
	*
	* @return	string	The modified statement
	*/	
	final private function extractBinds($sql,$inputarr)
	{
		$inString  = false;
		$escaped   = 0;
		$sqlLength = strlen($sql) - 1;
		$newSql    = '';
		$bindCount = 0;
		
		/*
		* inputarr is the passed in bind list, which is associative, but
		* we only want the keys here
		*/
		$inputKeys = array_keys($inputarr);
		
		
		for ($i=0;$i<=$sqlLength;$i++)
		{
			/*
			* find the next character of the string
			*/
            $c = $sql[$i];

			if ($c == "'" && !$inString && $escaped==0)
				/*
				* Found the start of a string inside the statement
				*/
				$inString = true;
			elseif ($c == "\\" && $escaped==0)
				/*
				* The next character will be escaped
				*/
				$escaped = 1;
			elseif ($c == "'" && $inString && $escaped==0)
				/*
				* We found the end of the string
				*/
				$inString = false;
			
			if ($escaped == 2)
				$escaped = 0;

			if ($escaped==0 && !$inString && $c == '?')
				/*
				* We found a bind symbol, replace it with the oracle equivalent
				*/
				$newSql .= ':' . $inputKeys[$bindCount++];
			else
				/*
				* Add the current character the pile
				*/
				$newSql .= $c;
			
			if ($escaped == 1)
				/*
				* We have just found an escape character, make sure we ignore the
				* next one that comes along, it might be a ' character
				*/
				$escaped = 2;
		}
		
		return $newSql;
			
	}
}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oci8po extends ADORecordset_oci8 {

	var $databaseType = 'oci8po';

	function __construct($queryID,$mode=false)
	{
		parent::__construct($queryID,$mode);
	}

	function Fields($colname)
	{
		if ($this->fetchMode & OCI_ASSOC) return $this->fields[$colname];

		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		 return $this->fields[$this->bind[strtoupper($colname)]];
	}

	// lowercase field names...
	function _FetchField($fieldOffset = -1)
	{
		$fld = new ADOFieldObject;
		$fieldOffset += 1;
		$fld->name = OCIcolumnname($this->_queryID, $fieldOffset);
		if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_LOWER) {
			$fld->name = strtolower($fld->name);
		}
		$fld->type = OCIcolumntype($this->_queryID, $fieldOffset);
		$fld->max_length = OCIcolumnsize($this->_queryID, $fieldOffset);
		if ($fld->type == 'NUMBER') {
			$sc = OCIColumnScale($this->_queryID, $fieldOffset);
			if ($sc == 0) {
				$fld->type = 'INT';
			}
		}
		return $fld;
	}

	// 10% speedup to move MoveNext to child class
	function MoveNext()
	{
		$ret = @oci_fetch_array($this->_queryID,$this->fetchMode);
		if($ret !== false) {
		global $ADODB_ANSI_PADDING_OFF;
			$this->fields = $ret;
			$this->_currentRow++;
			$this->_updatefields();

			if (!empty($ADODB_ANSI_PADDING_OFF)) {
				foreach($this->fields as $k => $v) {
					if (is_string($v)) $this->fields[$k] = rtrim($v);
				}
			}
			return true;
		}
		if (!$this->EOF) {
			$this->EOF = true;
			$this->_currentRow++;
		}
		return false;
	}

	/* Optimize SelectLimit() by using OCIFetch() instead of OCIFetchInto() */
	function GetArrayLimit($nrows,$offset=-1)
	{
		if ($offset <= 0) {
			$arr = $this->GetArray($nrows);
			return $arr;
		}
		for ($i=1; $i < $offset; $i++)
			if (!@OCIFetch($this->_queryID)) {
				$arr = array();
				return $arr;
			}
		$ret = @oci_fetch_array($this->_queryID,$this->fetchMode);
		if ($ret === false) {
			$arr = array();
			return $arr;
		}
		$this->fields = $ret;
		$this->_updatefields();
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nrows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}

		return $results;
	}

	function _fetch()
	{
		global $ADODB_ANSI_PADDING_OFF;

		$ret = @oci_fetch_array($this->_queryID,$this->fetchMode);
		if ($ret) {
			$this->fields = $ret;
			$this->_updatefields();

			if (!empty($ADODB_ANSI_PADDING_OFF)) {
				foreach($this->fields as $k => $v) {
					if (is_string($v)) $this->fields[$k] = rtrim($v);
				}
			}
		}
		return $ret !== false;
	}

}
