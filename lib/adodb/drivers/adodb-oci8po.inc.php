<?php
/*
V4.01 23 Oct 2003  (c) 2000-2003 John Lim. All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.

  Latest version is available at http://php.weblogs.com/
  
  Portable version of oci8 driver, to make it more similar to other database drivers.
  The main differences are

   1. that the OCI_ASSOC names are in lowercase instead of uppercase.
   2. bind variables are mapped using ? instead of :<bindvar>

   Should some emulation of RecordCount() be implemented?
  
*/

include_once(ADODB_DIR.'/drivers/adodb-oci8.inc.php');

class ADODB_oci8po extends ADODB_oci8 {
	var $databaseType = 'oci8po';
	var $dataProvider = 'oci8';
	var $metaColumnsSQL = "select lower(cname),coltype,width, SCALE, PRECISION, NULLS, DEFAULTVAL from col where tname='%s' order by colno"; //changed by smondino@users.sourceforge. net
	var $metaTablesSQL = "select lower(table_name),table_type from cat where table_type in ('TABLE','VIEW')";
	
	function ADODB_oci8po()
	{
		$this->ADODB_oci8();
	}
	
	function Param($name)
	{
		return '?';
	}
	
	function Prepare($sql)
	{
		$sqlarr = explode('?',$sql);
		$sql = $sqlarr[0];
		for ($i = 1, $max = sizeof($sqlarr); $i < $max; $i++) {
			$sql .=  ':'.($i-1) . $sqlarr[$i];
		} 
		return ADODB_oci8::Prepare($sql);
	}
	
	// emulate handling of parameters ? ?, replacing with :bind0 :bind1
	function _query($sql,$inputarr)
	{
		if (is_array($inputarr)) {
			$i = 0;
			if (is_array($sql)) {
				foreach($inputarr as $v) {
					$arr['bind'.$i++] = $v;
				} 
			} else {
				$sqlarr = explode('?',$sql);
				$sql = $sqlarr[0];
				foreach($inputarr as $k => $v) {
					$sql .=  ":$k" . $sqlarr[++$i];
				}
			}
		}
		return ADODB_oci8::_query($sql,$inputarr);
	}
}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oci8po extends ADORecordset_oci8 {

	var $databaseType = 'oci8po';
	
		function ADORecordset_oci8po($queryID,$mode=false)
		{
			$this->ADORecordset_oci8($queryID,$mode);
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
 		function &_FetchField($fieldOffset = -1)
		{
				 $fld = new ADOFieldObject;
		 		 $fieldOffset += 1;
				 $fld->name = strtolower(OCIcolumnname($this->_queryID, $fieldOffset));
				 $fld->type = OCIcolumntype($this->_queryID, $fieldOffset);
				 $fld->max_length = OCIcolumnsize($this->_queryID, $fieldOffset);
				 if ($fld->type == 'NUMBER') {
				 	//$p = OCIColumnPrecision($this->_queryID, $fieldOffset);
					$sc = OCIColumnScale($this->_queryID, $fieldOffset);
					if ($sc == 0) $fld->type = 'INT';
				 }
				 return $fld;
		}

	// 10% speedup to move MoveNext to child class
	function MoveNext() 
	{
		if (!$this->EOF) {		
			$this->_currentRow++;
			if(@OCIfetchinto($this->_queryID,$this->fields,$this->fetchMode)) {
				if ($this->fetchMode & OCI_ASSOC) $this->_updatefields();
				return true;
			}
			$this->EOF = true;
		}
		return false;
	}	
	
	/* Optimize SelectLimit() by using OCIFetch() instead of OCIFetchInto() */
	function &GetArrayLimit($nrows,$offset=-1) 
	{
		if ($offset <= 0) return $this->GetArray($nrows);
		for ($i=1; $i < $offset; $i++) 
			if (!@OCIFetch($this->_queryID)) return array();
			
		if (!@OCIfetchinto($this->_queryID,$this->fields,$this->fetchMode)) return array();
		if ($this->fetchMode & OCI_ASSOC) $this->_updatefields();
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nrows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}
		
		return $results;
	}

	// Create associative array
	function _updatefields()
	{
		if (ADODB_ASSOC_CASE == 2) return; // native
	
		$arr = array();
		$lowercase = (ADODB_ASSOC_CASE == 0);
		
		foreach ($this->fields as $k => $v) {
			if (is_integer($k)) $arr[$k] = $v;
			else {
				if ($lowercase)
					$arr[strtolower($k)] = $v;
				else
					$arr[strtoupper($k)] = $v;
			}
		}
		$this->fields = $arr;
	}
	
	function _fetch() 
	{
		$ret = @OCIfetchinto($this->_queryID,$this->fields,$this->fetchMode);
		if ($ret) {
			if ($this->fetchMode & OCI_ASSOC) $this->_updatefields();
		}
		return $ret;
	}
	
}
?>