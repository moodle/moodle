<?php
/*
 V5.18 3 Sep 2012  (c) 2000-2012 John Lim (jlim#natsoft.com). All rights reserved.
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
	   // Richard 3/18/2012 - Modified SQL to return SERIAL type correctly AS old driver no longer return SERIAL as data type. 
	var $metaColumnsSQL =
						 "SELECT a.attname, 
									CASE 
											   WHEN x.sequence_name != '' THEN 'SERIAL'
											   ELSE t.typname
									END AS typname,
									a.attlen,a.atttypmod,a.attnotnull,a.atthasdef,a.attnum
						 FROM pg_class c, pg_attribute a
						 JOIN pg_type t ON a.atttypid = t.oid 
						 LEFT JOIN 
									(SELECT c.relname as sequence_name,  
												  c1.relname as related_table, 
												  a.attname as related_column
									FROM pg_class c 
									   JOIN pg_depend d ON d.objid = c.oid 
									   LEFT JOIN pg_class c1 ON d.refobjid = c1.oid 
									   LEFT JOIN pg_attribute a ON (d.refobjid, d.refobjsubid) = (a.attrelid, a.attnum) 
									WHERE c.relkind = 'S' AND c1.relname = '%s') x 
									ON x.related_column= a.attname
						 WHERE c.relkind in ('r','v') AND 
									(c.relname='%s' or c.relname = lower('%s')) AND 
									a.attname not like '....%%' AND 
									a.attnum > 0 AND 
									a.attrelid = c.oid 
						 ORDER BY a.attnum";

   // used when schema defined
	var $metaColumnsSQL1 = "
						 SELECT a.attname, 
									CASE 
											   WHEN x.sequence_name != '' THEN 'SERIAL'
											   ELSE t.typname
									END AS typname,
									a.attlen, a.atttypmod, a.attnotnull, a.atthasdef, a.attnum
						 FROM pg_class c, pg_namespace n, pg_attribute a 
						 JOIN pg_type t ON a.atttypid = t.oid 
						 LEFT JOIN 
									(SELECT c.relname as sequence_name,  
												  c1.relname as related_table, 
												  a.attname as related_column
									FROM pg_class c 
									   JOIN pg_depend d ON d.objid = c.oid 
									   LEFT JOIN pg_class c1 ON d.refobjid = c1.oid 
									   LEFT JOIN pg_attribute a ON (d.refobjid, d.refobjsubid) = (a.attrelid, a.attnum) 
									WHERE c.relkind = 'S' AND c1.relname = '%s') x 
									ON x.related_column= a.attname
						 WHERE c.relkind in ('r','v') AND (c.relname='%s' or c.relname = lower('%s'))
									AND c.relnamespace=n.oid and n.nspname='%s' 
									AND a.attname not like '....%%' AND a.attnum > 0 
									AND a.atttypid = t.oid AND a.attrelid = c.oid  
						 ORDER BY a.attnum";

	
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
	 function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0) 
	 {
		 $offsetStr = ($offset >= 0) ? " OFFSET ".((integer)$offset) : '';
		 $limitStr  = ($nrows >= 0)  ? " LIMIT ".((integer)$nrows) : '';
		 if ($secs2cache)
		  	$rs = $this->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr);
		 else
		  	$rs = $this->Execute($sql."$limitStr$offsetStr",$inputarr);
		
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

	/*
		I discovered that the MetaForeignKeys method no longer worked for Postgres 8.3.
		I went ahead and modified it to work for both 8.2 and 8.3. 
		Please feel free to include this change in your next release of adodb.
		 William Kolodny [William.Kolodny#gt-t.net]
	*/
	function MetaForeignKeys($table, $owner=false, $upper=false)
	{
	  $sql="
	  SELECT fum.ftblname AS lookup_table, split_part(fum.rf, ')'::text, 1) AS lookup_field,
	     fum.ltable AS dep_table, split_part(fum.lf, ')'::text, 1) AS dep_field
	  FROM (
	  SELECT fee.ltable, fee.ftblname, fee.consrc, split_part(fee.consrc,'('::text, 2) AS lf, 
	    split_part(fee.consrc, '('::text, 3) AS rf
	  FROM (
	      SELECT foo.relname AS ltable, foo.ftblname,
	          pg_get_constraintdef(foo.oid) AS consrc
	      FROM (
	          SELECT c.oid, c.conname AS name, t.relname, ft.relname AS ftblname
	          FROM pg_constraint c 
	          JOIN pg_class t ON (t.oid = c.conrelid) 
	          JOIN pg_class ft ON (ft.oid = c.confrelid)
	          JOIN pg_namespace nft ON (nft.oid = ft.relnamespace)
	          LEFT JOIN pg_description ds ON (ds.objoid = c.oid)
	          JOIN pg_namespace n ON (n.oid = t.relnamespace)
	          WHERE c.contype = 'f'::\"char\"
	          ORDER BY t.relname, n.nspname, c.conname, c.oid
	          ) foo
	      ) fee) fum
	  WHERE fum.ltable='".strtolower($table)."'
	  ORDER BY fum.ftblname, fum.ltable, split_part(fum.lf, ')'::text, 1)
	  ";
	  $rs = $this->Execute($sql);
	
	  if (!$rs || $rs->EOF) return false;
	
	  $a = array();
	  while (!$rs->EOF) {
	    if ($upper) {
	      $a[strtoupper($rs->Fields('lookup_table'))][] = strtoupper(str_replace('"','',$rs->Fields('dep_field').'='.$rs->Fields('lookup_field')));
	    } else {
	      $a[$rs->Fields('lookup_table')][] = str_replace('"','',$rs->Fields('dep_field').'='.$rs->Fields('lookup_field'));
	    }
		$rs->MoveNext();
	  }
	
	  return $a;
	
	}
	
	// from  Edward Jaramilla, improved version - works on pg 7.4
	function _old_MetaForeignKeys($table, $owner=false, $upper=false)
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
		
		$rs = $this->Execute($sql);
		
		if (!$rs || $rs->EOF) return false;
		
		$arr = $rs->GetArray();
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

	function _query($sql,$inputarr=false)
	{
		if (! $this->_bindInputArray) {
			// We don't have native support for parameterized queries, so let's emulate it at the parent
			return ADODB_postgres64::_query($sql, $inputarr);
		}
		
		$this->_pnum = 0;
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