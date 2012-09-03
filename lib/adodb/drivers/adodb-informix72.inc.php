<?php
/*
V5.17 17 May 2012  (c) 2000-2012 John Lim. All rights reserved.
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.sourceforge.net

  Informix port by Mitchell T. Young (mitch@youngfamily.org)

  Further mods by "Samuel CARRIERE" <samuel_carriere@hotmail.com>

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('IFX_SCROLL')) define('IFX_SCROLL',1);

class ADODB_informix72 extends ADOConnection {
	var $databaseType = "informix72";
	var $dataProvider = "informix";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	var $hasInsertID = true;
	var $hasAffectedRows = true;
    var $substr = 'substr';
	var $metaTablesSQL="select tabname,tabtype from systables where tabtype in ('T','V') and owner!='informix'"; //Don't get informix tables and pseudo-tables


	var $metaColumnsSQL = 
		"select c.colname, c.coltype, c.collength, d.default,c.colno
		from syscolumns c, systables t,outer sysdefaults d
		where c.tabid=t.tabid and d.tabid=t.tabid and d.colno=c.colno
		and tabname='%s' order by c.colno";

	var $metaPrimaryKeySQL =
		"select part1,part2,part3,part4,part5,part6,part7,part8 from
		systables t,sysconstraints s,sysindexes i where t.tabname='%s'
		and s.tabid=t.tabid and s.constrtype='P'
		and i.idxname=s.idxname";

	var $concat_operator = '||';

	var $lastQuery = false;
	var $has_insertid = true;

	var $_autocommit = true;
	var $_bindInputArray = true;  // set to true if ADOConnection.Execute() permits binding of array parameters.
	var $sysDate = 'TODAY';
	var $sysTimeStamp = 'CURRENT';
	var $cursorType = IFX_SCROLL; // IFX_SCROLL or IFX_HOLD or 0
   
	function ADODB_informix72()
	{
		// alternatively, use older method:
		//putenv("DBDATE=Y4MD-");
		
		// force ISO date format
		putenv('GL_DATE=%Y-%m-%d');
		
		if (function_exists('ifx_byteasvarchar')) {
			ifx_byteasvarchar(1); // Mode "0" will return a blob id, and mode "1" will return a varchar with text content. 
        	ifx_textasvarchar(1); // Mode "0" will return a blob id, and mode "1" will return a varchar with text content. 
        	ifx_blobinfile_mode(0); // Mode "0" means save Byte-Blobs in memory, and mode "1" means save Byte-Blobs in a file.
		}
	}
	
	function ServerInfo()
	{
	    if (isset($this->version)) return $this->version;
	
	    $arr['description'] = $this->GetOne("select DBINFO('version','full') from systables where tabid = 1");
	    $arr['version'] = $this->GetOne("select DBINFO('version','major') || DBINFO('version','minor') from systables where tabid = 1");
	    $this->version = $arr;
	    return $arr;
	}



	function _insertid()
	{
		$sqlca =ifx_getsqlca($this->lastQuery);
		return @$sqlca["sqlerrd1"];
	}

	function _affectedrows()
	{
		if ($this->lastQuery) {
		   return @ifx_affected_rows ($this->lastQuery);
		}
		return 0;
	}

	function BeginTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->Execute('BEGIN');
		$this->_autocommit = false;
		return true;
	}

	function CommitTrans($ok=true) 
	{ 
		if (!$ok) return $this->RollbackTrans();
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('COMMIT');
		$this->_autocommit = true;
		return true;
	}

	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->Execute('ROLLBACK');
		$this->_autocommit = true;
		return true;
	}

	function RowLock($tables,$where,$col='1 as adodbignore')
	{
		if ($this->_autocommit) $this->BeginTrans();
		return $this->GetOne("select $col from $tables where $where for update");
	}

	/*	Returns: the last error message from previous database operation
		Note: This function is NOT available for Microsoft SQL Server.	*/

	function ErrorMsg() 
	{
		if (!empty($this->_logsql)) return $this->_errorMsg;
		$this->_errorMsg = ifx_errormsg();
		return $this->_errorMsg;
	}

	function ErrorNo()
	{
		preg_match("/.*SQLCODE=([^\]]*)/",ifx_error(),$parse);
		if (is_array($parse) && isset($parse[1])) return (int)$parse[1]; 
		return 0;
	}

	
	function MetaProcedures($NamePattern = false, $catalog  = null, $schemaPattern  = null)
    {
        // save old fetch mode
        global $ADODB_FETCH_MODE;

        $false = false;
        $save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
               $savem = $this->SetFetchMode(FALSE);

        }
        $procedures = array ();

        // get index details

        $likepattern = '';
        if ($NamePattern) {
           $likepattern = " WHERE procname LIKE '".$NamePattern."'";
        }

        $rs = $this->Execute('SELECT procname, isproc FROM sysprocedures'.$likepattern);

        if (is_object($rs)) {
            // parse index data into array

            while ($row = $rs->FetchRow()) {
                $procedures[$row[0]] = array(
                        'type' => ($row[1] == 'f' ? 'FUNCTION' : 'PROCEDURE'),
                        'catalog' => '',
                        'schema' => '',
                        'remarks' => ''
                    );
            }
	    }

        // restore fetchmode
        if (isset($savem)) {
                $this->SetFetchMode($savem);
        }
        $ADODB_FETCH_MODE = $save;

        return $procedures;
    }
   
    function MetaColumns($table, $normalize=true)
	{
	global $ADODB_FETCH_MODE;
	
		$false = false;
		if (!empty($this->metaColumnsSQL)) {
			$save = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
			if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
          		$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table));
			if (isset($savem)) $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
			if ($rs === false) return $false;
			$rspkey = $this->Execute(sprintf($this->metaPrimaryKeySQL,$table)); //Added to get primary key colno items

			$retarr = array();
			while (!$rs->EOF) { //print_r($rs->fields);
				$fld = new ADOFieldObject();
				$fld->name = $rs->fields[0];
/*  //!eos.
						$rs->fields[1] is not the correct adodb type
						$rs->fields[2] is not correct max_length, because can include not-null bit

				$fld->type = $rs->fields[1];
				$fld->primary_key=$rspkey->fields && array_search($rs->fields[4],$rspkey->fields); //Added to set primary key flag
				$fld->max_length = $rs->fields[2];*/
				$pr=ifx_props($rs->fields[1],$rs->fields[2]); //!eos
				$fld->type = $pr[0] ;//!eos
				$fld->primary_key=$rspkey->fields && array_search($rs->fields[4],$rspkey->fields);
				$fld->max_length = $pr[1]; //!eos
				$fld->precision = $pr[2] ;//!eos
				$fld->not_null = $pr[3]=="N"; //!eos

				if (trim($rs->fields[3]) != "AAAAAA 0") {
	                    		$fld->has_default = 1;
	                    		$fld->default_value = $rs->fields[3];
				} else {
					$fld->has_default = 0;
				}

                $retarr[strtolower($fld->name)] = $fld;	
				$rs->MoveNext();
			}

			$rs->Close();
			$rspkey->Close(); //!eos
			return $retarr;	
		}

		return $false;
	}
	
   function xMetaColumns($table)
   {
		return ADOConnection::MetaColumns($table,false);
   }

	 function MetaForeignKeys($table, $owner=false, $upper=false) //!Eos
	{
		$sql = "
			select tr.tabname,updrule,delrule,
			i.part1 o1,i2.part1 d1,i.part2 o2,i2.part2 d2,i.part3 o3,i2.part3 d3,i.part4 o4,i2.part4 d4,
			i.part5 o5,i2.part5 d5,i.part6 o6,i2.part6 d6,i.part7 o7,i2.part7 d7,i.part8 o8,i2.part8 d8
			from systables t,sysconstraints s,sysindexes i,
			sysreferences r,systables tr,sysconstraints s2,sysindexes i2
			where t.tabname='$table'
			and s.tabid=t.tabid and s.constrtype='R' and r.constrid=s.constrid
			and i.idxname=s.idxname and tr.tabid=r.ptabid
			and s2.constrid=r.primary and i2.idxname=s2.idxname";

		$rs = $this->Execute($sql);
		if (!$rs || $rs->EOF)  return false;
		$arr = $rs->GetArray();
		$a = array();
		foreach($arr as $v) {
			$coldest=$this->metaColumnNames($v["tabname"]);
			$colorig=$this->metaColumnNames($table);
			$colnames=array();
			for($i=1;$i<=8 && $v["o$i"] ;$i++) {
				$colnames[]=$coldest[$v["d$i"]-1]."=".$colorig[$v["o$i"]-1];
			}
			if($upper)
				$a[strtoupper($v["tabname"])] =  $colnames;
			else
				$a[$v["tabname"]] =  $colnames;
		}
		return $a;
	 }

   function UpdateBlob($table, $column, $val, $where, $blobtype = 'BLOB')
   {
   		$type = ($blobtype == 'TEXT') ? 1 : 0;
		$blobid = ifx_create_blob($type,0,$val);
		return $this->Execute("UPDATE $table SET $column=(?) WHERE $where",array($blobid));
   }

   function BlobDecode($blobid)
   {
   		return function_exists('ifx_byteasvarchar') ? $blobid : @ifx_get_blob($blobid);
   }
   
	// returns true or false
   function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('ifx_connect')) return null;
		
		$dbs = $argDatabasename . "@" . $argHostname;
		if ($argHostname) putenv("INFORMIXSERVER=$argHostname"); 
		putenv("INFORMIXSERVER=".trim($argHostname)); 
		$this->_connectionID = ifx_connect($dbs,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		#if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;
	}

	// returns true or false
   function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('ifx_connect')) return null;
		
		$dbs = $argDatabasename . "@" . $argHostname;
		putenv("INFORMIXSERVER=".trim($argHostname)); 
		$this->_connectionID = ifx_pconnect($dbs,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		#if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;
	}
/*
	// ifx_do does not accept bind parameters - weird ???
	function Prepare($sql)
	{
		$stmt = ifx_prepare($sql);
		if (!$stmt) return $sql;
		else return array($sql,$stmt);
	}
*/
	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr=false)
	{
	global $ADODB_COUNTRECS;
	
	  // String parameters have to be converted using ifx_create_char
	  if ($inputarr) {
		 foreach($inputarr as $v) {
			if (gettype($v) == 'string') {
			   $tab[] = ifx_create_char($v);
			}
			else {
			   $tab[] = $v;
			}
		 }
	  }

	  // In case of select statement, we use a scroll cursor in order
	  // to be able to call "move", or "movefirst" statements
	  if (!$ADODB_COUNTRECS && preg_match("/^\s*select/is", $sql)) {
		 if ($inputarr) {
			$this->lastQuery = ifx_query($sql,$this->_connectionID, $this->cursorType, $tab);
		 }
		 else {
			$this->lastQuery = ifx_query($sql,$this->_connectionID, $this->cursorType);
		 }
	  }
	  else {
		 if ($inputarr) {
			$this->lastQuery = ifx_query($sql,$this->_connectionID, $tab);
		 }
		 else {
			$this->lastQuery = ifx_query($sql,$this->_connectionID);
		 }
	  }

	  // Following line have been commented because autocommit mode is
	  // not supported by informix SE 7.2

	  //if ($this->_autocommit) ifx_query('COMMIT',$this->_connectionID);

		return $this->lastQuery;
	}

	// returns true or false
	function _close()
	{
		$this->lastQuery = false;
		return ifx_close($this->_connectionID);
	}
}


/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_informix72 extends ADORecordSet {

	var $databaseType = "informix72";
	var $canSeek = true;
	var $_fieldprops = false;

	function ADORecordset_informix72($id,$mode=false)
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		$this->fetchMode = $mode;
		return $this->ADORecordSet($id);
	}



	/*	Returns: an object containing field information.
		Get column information in the Recordset object. fetchField() can be used in order to obtain information about
		fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
		fetchField() is retrieved.	*/
	function FetchField($fieldOffset = -1)
	{
		if (empty($this->_fieldprops)) {
			$fp = ifx_fieldproperties($this->_queryID);
			foreach($fp as $k => $v) {
				$o = new ADOFieldObject;
				$o->name = $k;
				$arr = explode(';',$v); //"SQLTYPE;length;precision;scale;ISNULLABLE"
				$o->type = $arr[0];
				$o->max_length = $arr[1];
				$this->_fieldprops[] = $o;
				$o->not_null = $arr[4]=="N";
			}
		}
		$ret = $this->_fieldprops[$fieldOffset];
		return $ret;
	}

	function _initrs()
	{
		$this->_numOfRows = -1; // ifx_affected_rows not reliable, only returns estimate -- ($ADODB_COUNTRECS)? ifx_affected_rows($this->_queryID):-1;
		$this->_numOfFields = ifx_num_fields($this->_queryID);
	}

	function _seek($row)
	{
		return @ifx_fetch_row($this->_queryID, (int) $row);
	}

   function MoveLast()
   {
	  $this->fields = @ifx_fetch_row($this->_queryID, "LAST");
	  if ($this->fields) $this->EOF = false;
	  $this->_currentRow = -1;

	  if ($this->fetchMode == ADODB_FETCH_NUM) {
		 foreach($this->fields as $v) {
			$arr[] = $v;
		 }
		 $this->fields = $arr;
	  }

	  return true;
   }

   function MoveFirst()
	{
	  $this->fields = @ifx_fetch_row($this->_queryID, "FIRST");
	  if ($this->fields) $this->EOF = false;
	  $this->_currentRow = 0;

	  if ($this->fetchMode == ADODB_FETCH_NUM) {
		 foreach($this->fields as $v) {
			$arr[] = $v;
		 }
		 $this->fields = $arr;
	  }

	  return true;
   }

   function _fetch($ignore_fields=false)
   {

		$this->fields = @ifx_fetch_row($this->_queryID);

		if (!is_array($this->fields)) return false;

		if ($this->fetchMode == ADODB_FETCH_NUM) {
			foreach($this->fields as $v) {
				$arr[] = $v;
			}
			$this->fields = $arr;
		}
		return true;
	}

	/*	close() only needs to be called if you are worried about using too much memory while your script
		is running. All associated result memory for the specified result identifier will automatically be freed.	*/
	function _close()
	{
		return ifx_free_result($this->_queryID);
	}

}
/** !Eos
* Auxiliar function to Parse coltype,collength. Used by Metacolumns
* return: array ($mtype,$length,$precision,$nullable) (similar to ifx_fieldpropierties)
*/
function ifx_props($coltype,$collength){
	$itype=fmod($coltype+1,256);
	$nullable=floor(($coltype+1) /256) ?"N":"Y";
	$mtype=substr(" CIIFFNNDN TBXCC     ",$itype,1);
	switch ($itype){
		case 2:
			$length=4;
		case 6:
		case 9:
		case 14:
			$length=floor($collength/256);
			$precision=fmod($collength,256);
			break;
		default:
			$precision=0;
			$length=$collength;
	}
	return array($mtype,$length,$precision,$nullable);
}


?>