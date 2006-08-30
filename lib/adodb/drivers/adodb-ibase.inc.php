<?php
/*
V4.92a 29 Aug 2006  (c) 2000-2006 John Lim (jlim#natsoft.com.my). All rights reserved.  
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.

  Latest version is available at http://adodb.sourceforge.net
  
  Interbase data driver. Requires interbase client. Works on Windows and Unix.

  3 Jan 2002 -- suggestions by Hans-Peter Oeri <kampfcaspar75@oeri.ch>
  	changed transaction handling and added experimental blob stuff
  
  Docs to interbase at the website
   http://www.synectics.co.za/php3/tutorial/IB_PHP3_API.html
   
  To use gen_id(), see
   http://www.volny.cz/iprenosil/interbase/ip_ib_code.htm#_code_creategen
   
   $rs = $conn->Execute('select gen_id(adodb,1) from rdb$database');
   $id = $rs->fields[0];
   $conn->Execute("insert into table (id, col1,...) values ($id, $val1,...)");
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB_ibase extends ADOConnection {
	var $databaseType = "ibase";
	var $dataProvider = "ibase";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $ibase_datefmt = '%Y-%m-%d'; // For hours,mins,secs change to '%Y-%m-%d %H:%M:%S';
	var $fmtDate = "'Y-m-d'";
	var $ibase_timestampfmt = "%Y-%m-%d %H:%M:%S";
	var $ibase_timefmt = "%H:%M:%S";
	var $fmtTimeStamp = "'Y-m-d, H:i:s'";
	var $concat_operator='||';
	var $_transactionID;
	var $metaTablesSQL = "select rdb\$relation_name from rdb\$relations where rdb\$relation_name not like 'RDB\$%'";
	//OPN STUFF start
	var $metaColumnsSQL = "select a.rdb\$field_name, a.rdb\$null_flag, a.rdb\$default_source, b.rdb\$field_length, b.rdb\$field_scale, b.rdb\$field_sub_type, b.rdb\$field_precision, b.rdb\$field_type from rdb\$relation_fields a, rdb\$fields b where a.rdb\$field_source = b.rdb\$field_name and a.rdb\$relation_name = '%s' order by a.rdb\$field_position asc";
	//OPN STUFF end
	var $ibasetrans;
	var $hasGenID = true;
	var $_bindInputArray = true;
	var $buffers = 0;
	var $dialect = 1;
	var $sysDate = "cast('TODAY' as timestamp)";
	var $sysTimeStamp = "CURRENT_TIMESTAMP"; //"cast('NOW' as timestamp)";
	var $ansiOuter = true;
	var $hasAffectedRows = false;
	var $poorAffectedRows = true;
	var $blobEncodeType = 'C';
	var $role = false;
	
	function ADODB_ibase() 
	{
		 if (defined('IBASE_DEFAULT')) $this->ibasetrans = IBASE_DEFAULT;
  	}
	
	
	   // returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename,$persist=false)
	{  
		if (!function_exists('ibase_pconnect')) return null;
		if ($argDatabasename) $argHostname .= ':'.$argDatabasename;
		$fn = ($persist) ? 'ibase_pconnect':'ibase_connect';
		if ($this->role)
			$this->_connectionID = $fn($argHostname,$argUsername,$argPassword,
					$this->charSet,$this->buffers,$this->dialect,$this->role);
		else	
			$this->_connectionID = $fn($argHostname,$argUsername,$argPassword,
					$this->charSet,$this->buffers,$this->dialect);
		
		if ($this->dialect != 1) { // http://www.ibphoenix.com/ibp_60_del_id_ds.html
			$this->replaceQuote = "''";
		}
		if ($this->_connectionID === false) {
			$this->_handleerror();
			return false;
		}
		
		// PHP5 change.
		if (function_exists('ibase_timefmt')) {
			ibase_timefmt($this->ibase_datefmt,IBASE_DATE );
			if ($this->dialect == 1) ibase_timefmt($this->ibase_datefmt,IBASE_TIMESTAMP );
			else ibase_timefmt($this->ibase_timestampfmt,IBASE_TIMESTAMP );
			ibase_timefmt($this->ibase_timefmt,IBASE_TIME );
			
		} else {
			ini_set("ibase.timestampformat", $this->ibase_timestampfmt);
			ini_set("ibase.dateformat", $this->ibase_datefmt);
			ini_set("ibase.timeformat", $this->ibase_timefmt);
		}
		return true;
	}
	   // returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename,true);
	}	
	
	
	function MetaPrimaryKeys($table,$owner_notused=false,$internalKey=false)
	{	
		if ($internalKey) return array('RDB$DB_KEY');
		
		$table = strtoupper($table);
		
		$sql = 'SELECT S.RDB$FIELD_NAME AFIELDNAME
	FROM RDB$INDICES I JOIN RDB$INDEX_SEGMENTS S ON I.RDB$INDEX_NAME=S.RDB$INDEX_NAME  
	WHERE I.RDB$RELATION_NAME=\''.$table.'\' and I.RDB$INDEX_NAME like \'RDB$PRIMARY%\'
	ORDER BY I.RDB$INDEX_NAME,S.RDB$FIELD_POSITION';

		$a = $this->GetCol($sql,false,true);
		if ($a && sizeof($a)>0) return $a;
		return false;	  
	}
	
	function ServerInfo()
	{
		$arr['dialect'] = $this->dialect;
		switch($arr['dialect']) {
		case '': 
		case '1': $s = 'Interbase 5.5 or earlier'; break;
		case '2': $s = 'Interbase 5.6'; break;
		default:
		case '3': $s = 'Interbase 6.0'; break;
		}
		$arr['version'] = ADOConnection::_findvers($s);
		$arr['description'] = $s;
		return $arr;
	}

	function BeginTrans()
	{	 
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->autoCommit = false;
	 	$this->_transactionID = $this->_connectionID;//ibase_trans($this->ibasetrans, $this->_connectionID);
		return $this->_transactionID;
	}
	
	function CommitTrans($ok=true) 
	{ 
		if (!$ok) return $this->RollbackTrans();
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$ret = false;
		$this->autoCommit = true;
		if ($this->_transactionID) {
			   		//print ' commit ';
			$ret = ibase_commit($this->_transactionID);
		}
		$this->_transactionID = false;
		return $ret;
	}
	
	// there are some compat problems with ADODB_COUNTRECS=false and $this->_logsql currently.
	// it appears that ibase extension cannot support multiple concurrent queryid's
	function &_Execute($sql,$inputarr=false) 
	{
	global $ADODB_COUNTRECS;
	
		if ($this->_logsql) {
			$savecrecs = $ADODB_COUNTRECS;
			$ADODB_COUNTRECS = true; // force countrecs
			$ret =& ADOConnection::_Execute($sql,$inputarr);
			$ADODB_COUNTRECS = $savecrecs;
		} else {
			$ret =& ADOConnection::_Execute($sql,$inputarr);
		}
		return $ret;
	}
	
	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$ret = false;
		$this->autoCommit = true;
		if ($this->_transactionID) 
				  $ret = ibase_rollback($this->_transactionID);
		$this->_transactionID = false;   
		
		return $ret;
	}
	
	function &MetaIndexes ($table, $primary = FALSE, $owner=false)
	{
        // save old fetch mode
        global $ADODB_FETCH_MODE;
        $false = false;
        $save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
               $savem = $this->SetFetchMode(FALSE);
        }
        $table = strtoupper($table);
        $sql = "SELECT * FROM RDB\$INDICES WHERE RDB\$RELATION_NAME = '".$table."'";
        if (!$primary) {
        	$sql .= " AND RDB\$INDEX_NAME NOT LIKE 'RDB\$%'";
        } else {
        	$sql .= " AND RDB\$INDEX_NAME NOT LIKE 'RDB\$FOREIGN%'";
        }
        // get index details
        $rs = $this->Execute($sql);
        if (!is_object($rs)) {
	        // restore fetchmode
	        if (isset($savem)) {
	            $this->SetFetchMode($savem);
	        }
	        $ADODB_FETCH_MODE = $save;
            return $false;
        }
        
        $indexes = array();
		while ($row = $rs->FetchRow()) {
			$index = $row[0];
             if (!isset($indexes[$index])) {
             		if (is_null($row[3])) {$row[3] = 0;}
                     $indexes[$index] = array(
                             'unique' => ($row[3] == 1),
                             'columns' => array()
                     );
             }
			$sql = "SELECT * FROM RDB\$INDEX_SEGMENTS WHERE RDB\$INDEX_NAME = '".$index."' ORDER BY RDB\$FIELD_POSITION ASC";
			$rs1 = $this->Execute($sql);
            while ($row1 = $rs1->FetchRow()) {
             	$indexes[$index]['columns'][$row1[2]] = $row1[1];
        	}
		}
        // restore fetchmode
        if (isset($savem)) {
            $this->SetFetchMode($savem);
        }
        $ADODB_FETCH_MODE = $save;
        
        return $indexes;
	}

	
	// See http://community.borland.com/article/0,1410,25844,00.html
	function RowLock($tables,$where,$col)
	{
		if ($this->autoCommit) $this->BeginTrans();
		$this->Execute("UPDATE $table SET $col=$col WHERE $where "); // is this correct - jlim?
		return 1;
	}
	
	
	function CreateSequence($seqname,$startID=1)
	{
		$ok = $this->Execute(("INSERT INTO RDB\$GENERATORS (RDB\$GENERATOR_NAME) VALUES (UPPER('$seqname'))" ));
		if (!$ok) return false;
		return $this->Execute("SET GENERATOR $seqname TO ".($startID-1).';');
	}
	
	function DropSequence($seqname)
	{
		$seqname = strtoupper($seqname);
		$this->Execute("delete from RDB\$GENERATORS where RDB\$GENERATOR_NAME='$seqname'");
	}
	
	function GenID($seqname='adodbseq',$startID=1)
	{
		$getnext = ("SELECT Gen_ID($seqname,1) FROM RDB\$DATABASE");
		$rs = @$this->Execute($getnext);
		if (!$rs) {
			$this->Execute(("INSERT INTO RDB\$GENERATORS (RDB\$GENERATOR_NAME) VALUES (UPPER('$seqname'))" ));
			$this->Execute("SET GENERATOR $seqname TO ".($startID-1).';');
			$rs = $this->Execute($getnext);
		}
		if ($rs && !$rs->EOF) $this->genID = (integer) reset($rs->fields);
		else $this->genID = 0; // false
		
		if ($rs) $rs->Close();
		
		return $this->genID;
	}

	function SelectDB($dbName) 
	{
		   return false;
	}

	function _handleerror()
	{
		$this->_errorMsg = ibase_errmsg();
	}

	function ErrorNo() 
	{
		if (preg_match('/error code = ([\-0-9]*)/i', $this->_errorMsg,$arr)) return (integer) $arr[1];
		else return 0;
	}

	function ErrorMsg() 
	{
			return $this->_errorMsg;
	}

	function Prepare($sql)
	{
		$stmt = ibase_prepare($this->_connectionID,$sql);
		if (!$stmt) return false;
		return array($sql,$stmt);
	}

	   // returns query ID if successful, otherwise false
	   // there have been reports of problems with nested queries - the code is probably not re-entrant?
	function _query($sql,$iarr=false)
	{ 

		if (!$this->autoCommit && $this->_transactionID) {
			$conn = $this->_transactionID;
			$docommit = false;
		} else {
			$conn = $this->_connectionID;
			$docommit = true;
		}
		if (is_array($sql)) {
			$fn = 'ibase_execute';
			$sql = $sql[1];
			if (is_array($iarr)) {
				if  (ADODB_PHPVER >= 0x4050) { // actually 4.0.4
					if ( !isset($iarr[0]) ) $iarr[0] = ''; // PHP5 compat hack
					$fnarr =& array_merge( array($sql) , $iarr);
					$ret = call_user_func_array($fn,$fnarr);
				} else {
					switch(sizeof($iarr)) {
					case 1: $ret = $fn($sql,$iarr[0]); break;
					case 2: $ret = $fn($sql,$iarr[0],$iarr[1]); break;
					case 3: $ret = $fn($sql,$iarr[0],$iarr[1],$iarr[2]); break;
					case 4: $ret = $fn($sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3]); break;
					case 5: $ret = $fn($sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4]); break;
					case 6: $ret = $fn($sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4],$iarr[5]); break;
					case 7: $ret = $fn($sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4],$iarr[5],$iarr[6]); break;
					default: ADOConnection::outp( "Too many parameters to ibase query $sql");
					case 8: $ret = $fn($sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4],$iarr[5],$iarr[6],$iarr[7]); break;
					}
				}
			} else $ret = $fn($sql); 
		} else {
			$fn = 'ibase_query';
		
			if (is_array($iarr)) {	
				if (ADODB_PHPVER >= 0x4050) { // actually 4.0.4
					if (sizeof($iarr) == 0) $iarr[0] = ''; // PHP5 compat hack
					$fnarr =& array_merge( array($conn,$sql) , $iarr);
					$ret = call_user_func_array($fn,$fnarr);
				} else {
					switch(sizeof($iarr)) {
					case 1: $ret = $fn($conn,$sql,$iarr[0]); break;
					case 2: $ret = $fn($conn,$sql,$iarr[0],$iarr[1]); break;
					case 3: $ret = $fn($conn,$sql,$iarr[0],$iarr[1],$iarr[2]); break;
					case 4: $ret = $fn($conn,$sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3]); break;
					case 5: $ret = $fn($conn,$sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4]); break;
					case 6: $ret = $fn($conn,$sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4],$iarr[5]); break;
					case 7: $ret = $fn($conn,$sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4],$iarr[5],$iarr[6]); break;
					default: ADOConnection::outp( "Too many parameters to ibase query $sql");
					case 8: $ret = $fn($conn,$sql,$iarr[0],$iarr[1],$iarr[2],$iarr[3],$iarr[4],$iarr[5],$iarr[6],$iarr[7]); break;
					}
				}
			} else $ret = $fn($conn,$sql); 
		}
		if ($docommit && $ret === true) ibase_commit($this->_connectionID);

		$this->_handleerror();
		return $ret;
	}

	 // returns true or false
	 function _close()
	 {	   
		if (!$this->autoCommit) @ibase_rollback($this->_connectionID);
		return @ibase_close($this->_connectionID);
	 }
	
	//OPN STUFF start
	function _ConvertFieldType(&$fld, $ftype, $flen, $fscale, $fsubtype, $fprecision, $dialect3)
	{
		$fscale = abs($fscale);
		$fld->max_length = $flen;
		$fld->scale = null;
		switch($ftype){
			case 7: 
			case 8:
				if ($dialect3) {
				    switch($fsubtype){
				    	case 0: 
				    		$fld->type = ($ftype == 7 ? 'smallint' : 'integer');
				    		break;
				    	case 1: 
				    		$fld->type = 'numeric';
							$fld->max_length = $fprecision;
							$fld->scale = $fscale;
				    		break;
				    	case 2:
				    		$fld->type = 'decimal';
							$fld->max_length = $fprecision;
							$fld->scale = $fscale;
				    		break;
				    } // switch
				} else {
					if ($fscale !=0) {
					    $fld->type = 'decimal';
						$fld->scale = $fscale;
						$fld->max_length = ($ftype == 7 ? 4 : 9);
					} else {
						$fld->type = ($ftype == 7 ? 'smallint' : 'integer');
					}
				}
				break;
			case 16: 
				if ($dialect3) {
				    switch($fsubtype){
				    	case 0: 
				    		$fld->type = 'decimal';
							$fld->max_length = 18;
							$fld->scale = 0;
				    		break;
				    	case 1: 
				    		$fld->type = 'numeric';
							$fld->max_length = $fprecision;
							$fld->scale = $fscale;
				    		break;
				    	case 2:
				    		$fld->type = 'decimal';
							$fld->max_length = $fprecision;
							$fld->scale = $fscale;
				    		break;
				    } // switch
				}
				break;
			case 10:
				$fld->type = 'float';
				break;
			case 14:
				$fld->type = 'char';
				break;
			case 27:
				if ($fscale !=0) {
				    $fld->type = 'decimal';
					$fld->max_length = 15;
					$fld->scale = 5;
				} else {
					$fld->type = 'double';
				}
				break;
			case 35:
				if ($dialect3) {
				    $fld->type = 'timestamp';
				} else {
					$fld->type = 'date';
				}
				break;
			case 12:
				$fld->type = 'date';
				break;
			case 13:
				$fld->type = 'time';
				break;
			case 37:
				$fld->type = 'varchar';
				break;
			case 40:
				$fld->type = 'cstring';
				break;
			case 261:
				$fld->type = 'blob';
				$fld->max_length = -1;
				break;
		} // switch
	}
	//OPN STUFF end
		// returns array of ADOFieldObjects for current table
	function &MetaColumns($table) 
	{
	global $ADODB_FETCH_MODE;
		
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	
		$rs = $this->Execute(sprintf($this->metaColumnsSQL,strtoupper($table)));
	
		$ADODB_FETCH_MODE = $save;
		$false = false;
		if ($rs === false) {
			return $false;
		}
		
		$retarr = array();
		//OPN STUFF start
		$dialect3 = ($this->dialect==3 ? true : false);
		//OPN STUFF end
		while (!$rs->EOF) { //print_r($rs->fields);
			$fld = new ADOFieldObject();
			$fld->name = trim($rs->fields[0]);
			//OPN STUFF start
			$this->_ConvertFieldType($fld, $rs->fields[7], $rs->fields[3], $rs->fields[4], $rs->fields[5], $rs->fields[6], $dialect3);
			if (isset($rs->fields[1]) && $rs->fields[1]) {
				$fld->not_null = true;
			}				
			if (isset($rs->fields[2])) {
				
				$fld->has_default = true;
				$d = substr($rs->fields[2],strlen('default '));
				switch ($fld->type)
				{
				case 'smallint':
				case 'integer': $fld->default_value = (int) $d; break;
				case 'char': 
				case 'blob':
				case 'text':
				case 'varchar': $fld->default_value = (string) substr($d,1,strlen($d)-2); break;
				case 'double':
				case 'float': $fld->default_value = (float) $d; break;
				default: $fld->default_value = $d; break;
				}
		//	case 35:$tt = 'TIMESTAMP'; break;
			}
			if ((isset($rs->fields[5])) && ($fld->type == 'blob')) {
				$fld->sub_type = $rs->fields[5];
			} else {
				$fld->sub_type = null;
			}
			//OPN STUFF end
			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] = $fld;	
			else $retarr[strtoupper($fld->name)] = $fld;
			
			$rs->MoveNext();
		}
		$rs->Close();
		if ( empty($retarr)) return $false;
		else return $retarr;	
	}
	
	function BlobEncode( $blob ) 
	{
		$blobid = ibase_blob_create( $this->_connectionID);
		ibase_blob_add( $blobid, $blob );
		return ibase_blob_close( $blobid );
	}
	
	// since we auto-decode all blob's since 2.42, 
	// BlobDecode should not do any transforms
	function BlobDecode($blob)
	{
		return $blob; 
	}
	
	
	
	
	// old blobdecode function
	// still used to auto-decode all blob's
	function _BlobDecode_old( $blob ) 
	{
		$blobid = ibase_blob_open($this->_connectionID, $blob );
		$realblob = ibase_blob_get( $blobid,$this->maxblobsize); // 2nd param is max size of blob -- Kevin Boillet <kevinboillet@yahoo.fr>
		while($string = ibase_blob_get($blobid, 8192)){ 
			$realblob .= $string; 
		}
		ibase_blob_close( $blobid );

		return( $realblob );
	} 
	
	function _BlobDecode( $blob ) 
    {
        if  (ADODB_PHPVER >= 0x5000) {
            $blob_data = ibase_blob_info($this->_connectionID, $blob );
            $blobid = ibase_blob_open($this->_connectionID, $blob );
        } else {

            $blob_data = ibase_blob_info( $blob );
            $blobid = ibase_blob_open( $blob );
        }

        if( $blob_data[0] > $this->maxblobsize ) {

            $realblob = ibase_blob_get($blobid, $this->maxblobsize);

            while($string = ibase_blob_get($blobid, 8192)){
                $realblob .= $string; 
            }
        } else {
            $realblob = ibase_blob_get($blobid, $blob_data[0]);
        }

        ibase_blob_close( $blobid );
        return( $realblob );
	}
	
	function UpdateBlobFile($table,$column,$path,$where,$blobtype='BLOB') 
	{ 
		$fd = fopen($path,'rb'); 
		if ($fd === false) return false; 
		$blob_id = ibase_blob_create($this->_connectionID); 
		
		/* fill with data */ 
		
		while ($val = fread($fd,32768)){ 
			ibase_blob_add($blob_id, $val); 
		} 
		
		/* close and get $blob_id_str for inserting into table */ 
		$blob_id_str = ibase_blob_close($blob_id); 
		
		fclose($fd); 
		return $this->Execute("UPDATE $table SET $column=(?) WHERE $where",array($blob_id_str)) != false; 
	} 
	
	/*
		Insert a null into the blob field of the table first.
		Then use UpdateBlob to store the blob.
		
		Usage:
		 
		$conn->Execute('INSERT INTO blobtable (id, blobcol) VALUES (1, null)');
		$conn->UpdateBlob('blobtable','blobcol',$blob,'id=1');
	*/
	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB') 
	{ 
	$blob_id = ibase_blob_create($this->_connectionID); 
	
	// ibase_blob_add($blob_id, $val); 
	
	// replacement that solves the problem by which only the first modulus 64K / 
	// of $val are stored at the blob field //////////////////////////////////// 
	// Thx Abel Berenstein  aberenstein#afip.gov.ar
	$len = strlen($val); 
	$chunk_size = 32768; 
	$tail_size = $len % $chunk_size; 
	$n_chunks = ($len - $tail_size) / $chunk_size; 
	
	for ($n = 0; $n < $n_chunks; $n++) { 
		$start = $n * $chunk_size; 
		$data = substr($val, $start, $chunk_size); 
		ibase_blob_add($blob_id, $data); 
	} 
	
	if ($tail_size) {
		$start = $n_chunks * $chunk_size; 
		$data = substr($val, $start, $tail_size); 
		ibase_blob_add($blob_id, $data); 
	}
	// end replacement ///////////////////////////////////////////////////////// 
	
	$blob_id_str = ibase_blob_close($blob_id); 
	
	return $this->Execute("UPDATE $table SET $column=(?) WHERE $where",array($blob_id_str)) != false; 
	
	} 
	
	
	function OldUpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{
		$blob_id = ibase_blob_create($this->_connectionID);
		ibase_blob_add($blob_id, $val);
		$blob_id_str = ibase_blob_close($blob_id);
		return $this->Execute("UPDATE $table SET $column=(?) WHERE $where",array($blob_id_str)) != false;
	}
	
	// Format date column in sql string given an input format that understands Y M D
	// Only since Interbase 6.0 - uses EXTRACT
	// problem - does not zero-fill the day and month yet
	function SQLDate($fmt, $col=false)
	{	
		if (!$col) $col = $this->sysDate;
		$s = '';
		
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			if ($s) $s .= '||';
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= "extract(year from $col)";
				break;
			case 'M':
			case 'm':
				$s .= "extract(month from $col)";
				break;
			case 'Q':
			case 'q':
				$s .= "cast(((extract(month from $col)+2) / 3) as integer)";
				break;
			case 'D':
			case 'd':
				$s .= "(extract(day from $col))";
				break;
			case 'H':
			case 'h':
			  $s .= "(extract(hour from $col))";
			  break;                        
			case 'I':
			case 'i':
			  $s .= "(extract(minute from $col))";
			  break;                
			case 'S':
			case 's':
			  $s .= "CAST((extract(second from $col)) AS INTEGER)";
			  break;        

			default:
				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				$s .= $this->qstr($ch);
				break;
			}
		}
		return $s;
	}
}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_ibase extends ADORecordSet
{

	var $databaseType = "ibase";
	var $bind=false;
	var $_cacheType;
	
	function ADORecordset_ibase($id,$mode=false)
	{
	global $ADODB_FETCH_MODE;
	
			$this->fetchMode = ($mode === false) ? $ADODB_FETCH_MODE : $mode;
			$this->ADORecordSet($id);
	}

	/*		Returns: an object containing field information.
			Get column information in the Recordset object. fetchField() can be used in order to obtain information about
			fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
			fetchField() is retrieved.		*/

	function &FetchField($fieldOffset = -1)
	{
			 $fld = new ADOFieldObject;
			 $ibf = ibase_field_info($this->_queryID,$fieldOffset);
			 switch (ADODB_ASSOC_CASE) {
			 case 2: // the default
			 	$fld->name = ($ibf['alias']);
				 if (empty($fld->name)) $fld->name = ($ibf['name']);
				 break;
			 case 0: 
				 $fld->name = strtoupper($ibf['alias']);
				 if (empty($fld->name)) $fld->name = strtoupper($ibf['name']);
				 break;
			 case 1: 
			 	$fld->name = strtolower($ibf['alias']);
				 if (empty($fld->name)) $fld->name = strtolower($ibf['name']);
				 break;
			 }
			 
			 $fld->type = $ibf['type'];
			 $fld->max_length = $ibf['length'];
			 
			 /*       This needs to be populated from the metadata */ 
			 $fld->not_null = false;
			 $fld->has_default = false;
			 $fld->default_value = 'null';
			 return $fld;
	}

	function _initrs()
	{
		$this->_numOfRows = -1;
		$this->_numOfFields = @ibase_num_fields($this->_queryID);

		// cache types for blob decode check
		for ($i=0, $max = $this->_numOfFields; $i < $max; $i++) { 
			$f1 = $this->FetchField($i); 
			$this->_cacheType[] = $f1->type;
		}				
	}

	function _seek($row)
	{
		return false;
	}
	
	function _fetch() 
	{
		$f = @ibase_fetch_row($this->_queryID); 
		if ($f === false) {
			$this->fields = false;
			return false;
		}
		// OPN stuff start - optimized
		// fix missing nulls and decode blobs automatically
	
		global $ADODB_ANSI_PADDING_OFF;
		//$ADODB_ANSI_PADDING_OFF=1;
		$rtrim = !empty($ADODB_ANSI_PADDING_OFF);
		
		for ($i=0, $max = $this->_numOfFields; $i < $max; $i++) { 
			if ($this->_cacheType[$i]=="BLOB") {
				if (isset($f[$i])) { 
					$f[$i] = $this->connection->_BlobDecode($f[$i]); 
				} else { 
					$f[$i] = null; 
				} 
			} else { 
				if (!isset($f[$i])) { 
					$f[$i] = null; 
				} else if ($rtrim && is_string($f[$i])) {
					$f[$i] = rtrim($f[$i]);
				}
			} 
		} 
		// OPN stuff end 
		
		$this->fields = $f;
		if ($this->fetchMode == ADODB_FETCH_ASSOC) {
			$this->fields = &$this->GetRowAssoc(ADODB_ASSOC_CASE);
		} else if ($this->fetchMode == ADODB_FETCH_BOTH) {
			$this->fields =& array_merge($this->fields,$this->GetRowAssoc(ADODB_ASSOC_CASE));
		}
		return true;
	}

	/* Use associative array to get fields array */
	function Fields($colname)
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) return $this->fields[$colname];
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		
		return $this->fields[$this->bind[strtoupper($colname)]];
		
	}
	

	function _close() 
	{
			return @ibase_free_result($this->_queryID);
	}

	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		switch (strtoupper($t)) {
		case 'CHAR':
			return 'C';
			
		case 'TEXT':
		case 'VARCHAR':
		case 'VARYING':
		if ($len <= $this->blobSize) return 'C';
			return 'X';
		case 'BLOB':
			return 'B';
			   
		case 'TIMESTAMP':
		case 'DATE': return 'D';
		case 'TIME': return 'T';
				//case 'T': return 'T';

				//case 'L': return 'L';
		case 'INT': 
		case 'SHORT':
		case 'INTEGER': return 'I';
		default: return 'N';
		}
	}

}
?>