<?php
/*
 V4.01 23 Oct 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 4.
  
  Postgres7 support.
  28 Feb 2001: Currently indicate that we support LIMIT
  01 Dec 2001: dannym added support for default values
*/

include_once(ADODB_DIR."/drivers/adodb-postgres64.inc.php");

class ADODB_postgres7 extends ADODB_postgres64 {
	var $databaseType = 'postgres7';	
	var $hasLimit = true;	// set to true for pgsql 6.5+ only. support pgsql/mysql SELECT * FROM TABLE LIMIT 10
	var $ansiOuter = true;
	var $charSet = true; //set to true for Postgres 7 and above - PG client supports encodings
	
	function ADODB_postgres7() 
	{
		$this->ADODB_postgres64();
	}

	// the following should be compat with postgresql 7.2, 
	// which makes obsolete the LIMIT limit,offset syntax
	 function &SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0) 
	 {
	  $offsetStr = ($offset >= 0) ? " OFFSET $offset" : '';
	  $limitStr  = ($nrows >= 0)  ? " LIMIT $nrows" : '';
	  return $secs2cache ?
	   $this->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr)
	  :
	   $this->Execute($sql."$limitStr$offsetStr",$inputarr);
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
    function MetaForeignKeys($table, $owner=false, $upper=false)
	{

        $sql = '
SELECT t.tgargs as args
   FROM pg_trigger t,
        pg_class c,
        pg_class c2,
        pg_proc f
   WHERE t.tgenabled
   AND t.tgrelid=c.oid
   AND t.tgconstrrelid=c2.oid
   AND t.tgfoid=f.oid
   AND f.proname ~ \'^RI_FKey_check_ins\'
   AND t.tgargs like \'$1\\\000'.strtolower($table).'%\'
   ORDER BY t.tgrelid';

        $rs = $this->Execute($sql);
		if ($rs && !$rs->EOF) {
			$arr =& $rs->GetArray();
			$a = array();
			foreach($arr as $v) {
                $data = explode(chr(0), $v['args']);
                if ($upper) {
                    $a[] = array(strtoupper($data[2]) => strtoupper($data[4].'='.$data[5]));
                } else {
                    $a[] = array($data[2] => $data[4].'='.$data[5]);
                }
                
			}
			return $a;
		}
		else return false;
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
					if (isset($this->_blobArr)) $this->_fixblobs();
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