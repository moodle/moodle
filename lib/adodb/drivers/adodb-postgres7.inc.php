<?php
/*
 V4.96 24 Sept 2007  (c) 2000-2007 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 4.
  
  Postgres7 support.
  28 Feb 2001: Currently indicate that we support LIMIT
  01 Dec 2001: dannym added support for default values
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR."/drivers/adodb-postgres64.inc.php");

class ADODB_postgres7 extends ADODB_postgres64 {
	var $databaseType = 'postgres7';	
	var $hasLimit = true;	// set to true for pgsql 6.5+ only. support pgsql/mysql SELECT * FROM TABLE LIMIT 10
	var $ansiOuter = true;
	var $charSet = true; //set to true for Postgres 7 and above - PG client supports encodings
	
	function ADODB_postgres7() 
	{
		$this->ADODB_postgres64();
		if (ADODB_ASSOC_CASE !== 2) {
			$this->rsPrefix .= 'assoc_';
		}
		$this->_bindInputArray = PHP_VERSION >= 5.1;
	}

	
	// the following should be compat with postgresql 7.2, 
	// which makes obsolete the LIMIT limit,offset syntax
	 function &SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0) 
	 {
		 $offsetStr = ($offset >= 0) ? " OFFSET ".((integer)$offset) : '';
		 $limitStr  = ($nrows >= 0)  ? " LIMIT ".((integer)$nrows) : '';
		 if ($secs2cache)
		  	$rs =& $this->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr);
		 else
		  	$rs =& $this->Execute($sql."$limitStr$offsetStr",$inputarr);
		
		return $rs;
	 }
 	/*
 	function Prepare($sql)
	{
		$info = $this->ServerInfo();
		if ($info['version']>=7.3) {
			return array($sql,false);
		}
		return $sql;
	}
 	*/


	// from  Edward Jaramilla, improved version - works on pg 7.4
	function MetaForeignKeys($table, $owner=false, $upper=false)
	{
		$sql = 'SELECT t.tgargs as args
		FROM
		pg_trigger t,pg_class c,pg_proc p
		WHERE
		t.tgenabled AND
		t.tgrelid = c.oid AND
		t.tgfoid = p.oid AND
		p.proname = \'RI_FKey_check_ins\' AND
		c.relname = \''.strtolower($table).'\'
		ORDER BY
			t.tgrelid';
		
		$rs =& $this->Execute($sql);
		
		if (!$rs || $rs->EOF) return false;
		
		$arr =& $rs->GetArray();
		$a = array();
		foreach($arr as $v) {
			$data = explode(chr(0), $v['args']);
			$size = count($data)-1; //-1 because the last node is empty
			for($i = 4; $i < $size; $i++) {
				if ($upper) 
					$a[strtoupper($data[2])][] = strtoupper($data[$i].'='.$data[++$i]);
				else 
					$a[$data[2]][] = $data[$i].'='.$data[++$i];
			}
		}
		return $a;
	}

	function _query($sql,$inputarr)
	{
		if (! $this->_bindInputArray) {
			// We don't have native support for parameterized queries, so let's emulate it at the parent
			return ADODB_postgres64::_query($sql, $inputarr);
		}
		$this->_errorMsg = false;
		// -- added Cristiano da Cunha Duarte
		if ($inputarr) {
			$sqlarr = explode('?',trim($sql));
			$sql = '';
			$i = 1;
			$last = sizeof($sqlarr)-1;
			foreach($sqlarr as $v) {
				if ($last < $i) $sql .= $v;
				else $sql .= $v.' $'.$i;
				$i++;
			}
			
			$rez = pg_query_params($this->_connectionID,$sql, $inputarr);
		} else {
			$rez = pg_query($this->_connectionID,$sql);
		}
		// check if no data returned, then no need to create real recordset
		if ($rez && pg_numfields($rez) <= 0) {
			if (is_resource($this->_resultid) && get_resource_type($this->_resultid) === 'pgsql result') {
				pg_freeresult($this->_resultid);
			}
			$this->_resultid = $rez;
			return true;
		}		
		return $rez;
	}
	
 	 // this is a set of functions for managing client encoding - very important if the encodings
	// of your database and your output target (i.e. HTML) don't match
	//for instance, you may have UNICODE database and server it on-site as WIN1251 etc.
	// GetCharSet - get the name of the character set the client is using now
	// the functions should work with Postgres 7.0 and above, the set of charsets supported
	// depends on compile flags of postgres distribution - if no charsets were compiled into the server
	// it will return 'SQL_ANSI' always
	function GetCharSet()
	{
		//we will use ADO's builtin property charSet
		$this->charSet = @pg_client_encoding($this->_connectionID);
		if (!$this->charSet) {
			return false;
		} else {
			return $this->charSet;
		}
	}
	
	// SetCharSet - switch the client encoding
	function SetCharSet($charset_name)
	{
		$this->GetCharSet();
		if ($this->charSet !== $charset_name) {
			$if = pg_set_client_encoding($this->_connectionID, $charset_name);
			if ($if == "0" & $this->GetCharSet() == $charset_name) {
				return true;
			} else return false;
		} else return true;
	}

}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_postgres7 extends ADORecordSet_postgres64{

	var $databaseType = "postgres7";
	
	
	function ADORecordSet_postgres7($queryID,$mode=false) 
	{
		$this->ADORecordSet_postgres64($queryID,$mode);
	}
	
	 	// 10% speedup to move MoveNext to child class
	function MoveNext() 
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_numOfRows < 0 || $this->_numOfRows > $this->_currentRow) {
				$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
			
				if (is_array($this->fields)) {
					if ($this->fields && isset($this->_blobArr)) $this->_fixblobs();
					return true;
				}
			}
			$this->fields = false;
			$this->EOF = true;
		}
		return false;
	}		

}

class ADORecordSet_assoc_postgres7 extends ADORecordSet_postgres64{

	var $databaseType = "postgres7";
	
	
	function ADORecordSet_assoc_postgres7($queryID,$mode=false) 
	{
		$this->ADORecordSet_postgres64($queryID,$mode);
	}
	
	function _fetch()
	{
		if ($this->_currentRow >= $this->_numOfRows && $this->_numOfRows >= 0)
        	return false;

		$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
		
		if ($this->fields) {
			if (isset($this->_blobArr)) $this->_fixblobs();
			$this->_updatefields();
		}
			
		return (is_array($this->fields));
	}
	
		// Create associative array
	function _updatefields()
	{
		if (ADODB_ASSOC_CASE == 2) return; // native
	
		$arr = array();
		$lowercase = (ADODB_ASSOC_CASE == 0);
		
		foreach($this->fields as $k => $v) {
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
	
	function MoveNext() 
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_numOfRows < 0 || $this->_numOfRows > $this->_currentRow) {
				$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
			
				if (is_array($this->fields)) {
					if ($this->fields) {
						if (isset($this->_blobArr)) $this->_fixblobs();
					
						$this->_updatefields();
					}
					return true;
				}
			}
			
			
			$this->fields = false;
			$this->EOF = true;
		}
		return false;
	}
}
?>