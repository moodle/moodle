<?php
/*

  version V5.18 3 Sep 2012 (c) 2000-2012 John Lim. All rights reserved.

  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.

  Latest version is available at http://adodb.sourceforge.net
  
  Code contributed by George Fourlanos <fou@infomap.gr>
  
  13 Nov 2000 jlim - removed all ora_* references.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

/*
NLS_Date_Format
Allows you to use a date format other than the Oracle Lite default. When a literal
character string appears where a date value is expected, the Oracle Lite database
tests the string to see if it matches the formats of Oracle, SQL-92, or the value
specified for this parameter in the POLITE.INI file. Setting this parameter also
defines the default format used in the TO_CHAR or TO_DATE functions when no
other format string is supplied.

For Oracle the default is dd-mon-yy or dd-mon-yyyy, and for SQL-92 the default is
yy-mm-dd or yyyy-mm-dd.

Using 'RR' in the format forces two-digit years less than or equal to 49 to be
interpreted as years in the 21st century (2000–2049), and years over 50 as years in
the 20th century (1950–1999). Setting the RR format as the default for all two-digit
year entries allows you to become year-2000 compliant. For example:
NLS_DATE_FORMAT='RR-MM-DD'

You can also modify the date format using the ALTER SESSION command. 
*/

# define the LOB descriptor type for the given type
# returns false if no LOB descriptor
function oci_lob_desc($type) {
	switch ($type) {
		case OCI_B_BFILE: $result = OCI_D_FILE; break;
		case OCI_B_CFILEE: $result = OCI_D_FILE; break;
		case OCI_B_CLOB: $result = OCI_D_LOB; break;
		case OCI_B_BLOB: $result = OCI_D_LOB; break;
		case OCI_B_ROWID: $result = OCI_D_ROWID; break;
		default: $result = false; break;
	}
	return $result;
}

class ADODB_oci8 extends ADOConnection {
	var $databaseType = 'oci8';
	var $dataProvider = 'oci8';
	var $replaceQuote = "''"; // string to use to replace quotes
	var $concat_operator='||';
	var $sysDate = "TRUNC(SYSDATE)";
	var $sysTimeStamp = 'SYSDATE'; // requires oracle 9 or later, otherwise use SYSDATE
	var $metaDatabasesSQL = "SELECT USERNAME FROM ALL_USERS WHERE USERNAME NOT IN ('SYS','SYSTEM','DBSNMP','OUTLN') ORDER BY 1";
	var $_stmt;
	var $_commit = OCI_COMMIT_ON_SUCCESS;
	var $_initdate = true; // init date to YYYY-MM-DD
	var $metaTablesSQL = "select table_name,table_type from cat where table_type in ('TABLE','VIEW') and table_name not like 'BIN\$%'"; // bin$ tables are recycle bin tables
	var $metaColumnsSQL = "select cname,coltype,width, SCALE, PRECISION, NULLS, DEFAULTVAL from col where tname='%s' order by colno"; //changed by smondino@users.sourceforge. net
	var $metaColumnsSQL2 = "select column_name,data_type,data_length, data_scale, data_precision, 
    case when nullable = 'Y' then 'NULL'
    else 'NOT NULL' end as nulls,
    data_default from all_tab_cols 
  where owner='%s' and table_name='%s' order by column_id"; // when there is a schema
	var $_bindInputArray = true;
	var $hasGenID = true;
	var $_genIDSQL = "SELECT (%s.nextval) FROM DUAL";	
	var $_genSeqSQL = "
DECLARE
  PRAGMA AUTONOMOUS_TRANSACTION;
BEGIN
	execute immediate 'CREATE SEQUENCE %s START WITH %s';
END;
";

	var $_dropSeqSQL = "DROP SEQUENCE %s";
	var $hasAffectedRows = true;
	var $random = "abs(mod(DBMS_RANDOM.RANDOM,10000001)/10000000)";
	var $noNullStrings = false;
	var $connectSID = false;
	var $_bind = false;
	var $_nestedSQL = true;
	var $_hasOciFetchStatement = false;
	var $_getarray = false; // currently not working
	var $leftOuter = '';  // oracle wierdness, $col = $value (+) for LEFT OUTER, $col (+)= $value for RIGHT OUTER
	var $session_sharing_force_blob = false; // alter session on updateblob if set to true 
	var $firstrows = true; // enable first rows optimization on SelectLimit()
	var $selectOffsetAlg1 = 1000; // when to use 1st algorithm of selectlimit.
	var $NLS_DATE_FORMAT = 'YYYY-MM-DD';  // To include time, use 'RRRR-MM-DD HH24:MI:SS'
	var $dateformat = 'YYYY-MM-DD'; // DBDate format
 	var $useDBDateFormatForTextInput=false;
	var $datetime = false; // MetaType('DATE') returns 'D' (datetime==false) or 'T' (datetime == true)
	var $_refLOBs = array();
		
	// var $ansiOuter = true; // if oracle9
    
	function ADODB_oci8() 
	{
		$this->_hasOciFetchStatement = ADODB_PHPVER >= 0x4200;
		if (defined('ADODB_EXTENSION')) $this->rsPrefix .= 'ext_';
	}
	
	/*  function MetaColumns($table, $normalize=true) added by smondino@users.sourceforge.net*/
	function MetaColumns($table, $normalize=true) 
	{
	global $ADODB_FETCH_MODE;
		
		$schema = '';
		$this->_findschema($table, $schema);
		
		$false = false;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);

		if ($schema)
			$rs = $this->Execute(sprintf($this->metaColumnsSQL2, strtoupper($schema), strtoupper($table)));
		else
			$rs = $this->Execute(sprintf($this->metaColumnsSQL,strtoupper($table)));
		
		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		if (!$rs) {
			return $false;
		}
		$retarr = array();
		while (!$rs->EOF) {
			$fld = new ADOFieldObject();
	   		$fld->name = $rs->fields[0];
	   		$fld->type = $rs->fields[1];
	   		$fld->max_length = $rs->fields[2];
			$fld->scale = $rs->fields[3];
			if ($rs->fields[1] == 'NUMBER') {
				if ($rs->fields[3] == 0) $fld->type = 'INT';
	     		$fld->max_length = $rs->fields[4];
	    	}	
		   	$fld->not_null = (strncmp($rs->fields[5], 'NOT',3) === 0);
			$fld->binary = (strpos($fld->type,'BLOB') !== false);
			$fld->default_value = $rs->fields[6];
			
			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] = $fld;	
			else $retarr[strtoupper($fld->name)] = $fld;
			$rs->MoveNext();
		}
		$rs->Close();
		if (empty($retarr))
			return  $false;
		else 
			return $retarr;
	}
	
	function Time()
	{
		$rs = $this->Execute("select TO_CHAR($this->sysTimeStamp,'YYYY-MM-DD HH24:MI:SS') from dual");
		if ($rs && !$rs->EOF) return $this->UnixTimeStamp(reset($rs->fields));
		
		return false;
	}
 
/*

  Multiple modes of connection are supported:
  
  a. Local Database
    $conn->Connect(false,'scott','tiger');
  
  b. From tnsnames.ora
    $conn->Connect(false,'scott','tiger',$tnsname); 
    $conn->Connect($tnsname,'scott','tiger'); 
  
  c. Server + service name
    $conn->Connect($serveraddress,'scott,'tiger',$service_name);
  
  d. Server + SID
  	$conn->connectSID = true;
	$conn->Connect($serveraddress,'scott,'tiger',$SID);


Example TNSName:
---------------
NATSOFT.DOMAIN =
  (DESCRIPTION =
	(ADDRESS_LIST =
	  (ADDRESS = (PROTOCOL = TCP)(HOST = kermit)(PORT = 1523))
	)
	(CONNECT_DATA =
	  (SERVICE_NAME = natsoft.domain)
	)
  )
  
  There are 3 connection modes, 0 = non-persistent, 1 = persistent, 2 = force new connection
	
*/
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename,$mode=0)
	{
		if (!function_exists('oci_pconnect')) return null;
		#adodb_backtrace(); 
		
        $this->_errorMsg = false;
		$this->_errorCode = false;
		
		if($argHostname) { // added by Jorma Tuomainen <jorma.tuomainen@ppoy.fi>
			if (empty($argDatabasename)) $argDatabasename = $argHostname;
			else {
				if(strpos($argHostname,":")) {
					$argHostinfo=explode(":",$argHostname);
				   	$argHostname=$argHostinfo[0];
					$argHostport=$argHostinfo[1];
			 	} else {
					$argHostport = empty($this->port)?  "1521" : $this->port;
	   			}
				
				if (strncasecmp($argDatabasename,'SID=',4) == 0) {
					$argDatabasename = substr($argDatabasename,4);
					$this->connectSID = true;
				}
				
				if ($this->connectSID) {
					$argDatabasename="(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$argHostname
					.")(PORT=$argHostport))(CONNECT_DATA=(SID=$argDatabasename)))";
				} else
					$argDatabasename="(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$argHostname
					.")(PORT=$argHostport))(CONNECT_DATA=(SERVICE_NAME=$argDatabasename)))";
			}
		}
				
 		//if ($argHostname) print "<p>Connect: 1st argument should be left blank for $this->databaseType</p>";
		if ($mode==1) {
			$this->_connectionID = ($this->charSet) ? 
				oci_pconnect($argUsername,$argPassword, $argDatabasename,$this->charSet)
				:
				oci_pconnect($argUsername,$argPassword, $argDatabasename)
				;
			if ($this->_connectionID && $this->autoRollback)  oci_rollback($this->_connectionID);
		} else if ($mode==2) {
			$this->_connectionID = ($this->charSet) ? 
				oci_new_connect($argUsername,$argPassword, $argDatabasename,$this->charSet)
				:
				oci_new_connect($argUsername,$argPassword, $argDatabasename);
				
		} else {
			$this->_connectionID = ($this->charSet) ? 
				oci_connect($argUsername,$argPassword, $argDatabasename,$this->charSet)
				:
				oci_connect($argUsername,$argPassword, $argDatabasename);
		}
		if (!$this->_connectionID) return false;
		if ($this->_initdate) {
			$this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='".$this->NLS_DATE_FORMAT."'");
		}
		
		// looks like: 
		// Oracle8i Enterprise Edition Release 8.1.7.0.0 - Production With the Partitioning option JServer Release 8.1.7.0.0 - Production
		// $vers = oci_server_version($this->_connectionID);
		// if (strpos($vers,'8i') !== false) $this->ansiOuter = true;
		return true;
   	}
	
	function ServerInfo()
	{
		$arr['compat'] = $this->GetOne('select value from sys.database_compatible_level');
		$arr['description'] = @oci_server_version($this->_connectionID);
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}
		// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename,1);
	}
	
	// returns true or false
	function _nconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename,2);
	}
	
	function _affectedrows()
	{
		if (is_resource($this->_stmt)) return @oci_num_rows($this->_stmt);
		return 0;
	}
	
	function IfNull( $field, $ifNull ) 
	{
		return " NVL($field, $ifNull) "; // if Oracle
	}
	
	// format and return date string in database date format
	function DBDate($d,$isfld=false)
	{
		if (empty($d) && $d !== 0) return 'null';
		
		if ($isfld) {
			$d = _adodb_safedate($d);
			return 'TO_DATE('.$d.",'".$this->dateformat."')";
		}
		
		if (is_string($d)) $d = ADORecordSet::UnixDate($d);
		
		if (is_object($d)) $ds = $d->format($this->fmtDate);
		else $ds = adodb_date($this->fmtDate,$d);
		
		return "TO_DATE(".$ds.",'".$this->dateformat."')";
	}

	function BindDate($d)
	{
		$d = ADOConnection::DBDate($d);
		if (strncmp($d,"'",1)) return $d;
		
		return substr($d,1,strlen($d)-2);
	}
	
	function BindTimeStamp($ts)
	{
		if (empty($ts) && $ts !== 0) return 'null';
		if (is_string($ts)) $ts = ADORecordSet::UnixTimeStamp($ts);
		
		if (is_object($ts)) $tss = $ts->format("'Y-m-d H:i:s'");
		else $tss = adodb_date("'Y-m-d H:i:s'",$ts);
		
		return $tss;
	}
	
	// format and return date string in database timestamp format
	function DBTimeStamp($ts,$isfld=false)
	{
		if (empty($ts) && $ts !== 0) return 'null';
		if ($isfld) return 'TO_DATE(substr('.$ts.",1,19),'RRRR-MM-DD, HH24:MI:SS')";
		if (is_string($ts)) $ts = ADORecordSet::UnixTimeStamp($ts);
	
		if (is_object($ts)) $tss = $ts->format("'Y-m-d H:i:s'");
		else $tss = date("'Y-m-d H:i:s'",$ts);
		
		return 'TO_DATE('.$tss.",'RRRR-MM-DD, HH24:MI:SS')";
	}
	
	function RowLock($tables,$where,$col='1 as adodbignore') 
	{
		if ($this->autoCommit) $this->BeginTrans();
		return $this->GetOne("select $col from $tables where $where for update");
	}
	
	function MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{
		if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr(strtoupper($mask));
			$this->metaTablesSQL .= " AND upper(table_name) like $mask";
		}
		$ret = ADOConnection::MetaTables($ttype,$showSchema);
		
		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}
	
	// Mark Newnham 
	function MetaIndexes ($table, $primary = FALSE, $owner=false)
	{
        // save old fetch mode
        global $ADODB_FETCH_MODE;

        $save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;

        if ($this->fetchMode !== FALSE) {
               $savem = $this->SetFetchMode(FALSE);
        }

		// get index details
		$table = strtoupper($table);

		// get Primary index
		$primary_key = '';

		$false = false;
		$rs = $this->Execute(sprintf("SELECT * FROM ALL_CONSTRAINTS WHERE UPPER(TABLE_NAME)='%s' AND CONSTRAINT_TYPE='P'",$table));
		if (!is_object($rs)) {
			if (isset($savem)) 
				$this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
            return $false;
        }
		
		if ($row = $rs->FetchRow())
		   $primary_key = $row[1]; //constraint_name

		if ($primary==TRUE && $primary_key=='') {
			 if (isset($savem)) 
                $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
			return $false; //There is no primary key
		}

        $rs = $this->Execute(sprintf("SELECT ALL_INDEXES.INDEX_NAME, ALL_INDEXES.UNIQUENESS, ALL_IND_COLUMNS.COLUMN_POSITION, ALL_IND_COLUMNS.COLUMN_NAME FROM ALL_INDEXES,ALL_IND_COLUMNS WHERE UPPER(ALL_INDEXES.TABLE_NAME)='%s' AND ALL_IND_COLUMNS.INDEX_NAME=ALL_INDEXES.INDEX_NAME",$table));

		
        if (!is_object($rs)) {
			if (isset($savem)) 
				$this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
            return $false;
        }

		$indexes = array ();
        // parse index data into array

        while ($row = $rs->FetchRow()) {
			if ($primary && $row[0] != $primary_key) continue;
            if (!isset($indexes[$row[0]])) {
				$indexes[$row[0]] = array(
				   'unique' => ($row[1] == 'UNIQUE'),
				   'columns' => array()
				);
            }
            $indexes[$row[0]]['columns'][$row[2] - 1] = $row[3];
        }

        // sort columns by order in the index
        foreach ( array_keys ($indexes) as $index ) {
            ksort ($indexes[$index]['columns']);
        }

		if (isset($savem)) { 
            $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
		}
        return $indexes;
	}
	
	function BeginTrans()
	{	
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->autoCommit = false;
		$this->_commit = OCI_DEFAULT;
		
		if ($this->_transmode) $ok = $this->Execute("SET TRANSACTION ".$this->_transmode);
		else $ok = true;
		
		return $ok ? true : false;
	}
	
	function CommitTrans($ok=true) 
	{ 
		if ($this->transOff) return true;
		if (!$ok) return $this->RollbackTrans();
		
		if ($this->transCnt) $this->transCnt -= 1;
		$ret = oci_commit($this->_connectionID);
		$this->_commit = OCI_COMMIT_ON_SUCCESS;
		$this->autoCommit = true;
		return $ret;
	}
	
	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$ret = oci_rollback($this->_connectionID);
		$this->_commit = OCI_COMMIT_ON_SUCCESS;
		$this->autoCommit = true;
		return $ret;
	}
	
	
	function SelectDB($dbName) 
	{
		return false;
	}

	function ErrorMsg() 
	{
		if ($this->_errorMsg !== false) return $this->_errorMsg;

		if (is_resource($this->_stmt)) $arr = @oci_error($this->_stmt);
		if (empty($arr)) {
			if (is_resource($this->_connectionID)) $arr = @oci_error($this->_connectionID);
			else $arr = @oci_error();
			if ($arr === false) return '';
		}
		$this->_errorMsg = $arr['message'];
		$this->_errorCode = $arr['code'];
		return $this->_errorMsg;
	}

	function ErrorNo() 
	{
		if ($this->_errorCode !== false) return $this->_errorCode;
		
		if (is_resource($this->_stmt)) $arr = @oci_error($this->_stmt);
		if (empty($arr)) {
			$arr = @oci_error($this->_connectionID);
			if ($arr == false) $arr = @oci_error();
			if ($arr == false) return '';
		}
		
		$this->_errorMsg = $arr['message'];
		$this->_errorCode = $arr['code'];
		
		return $arr['code'];
	}
	
	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{	
		if (!$col) $col = $this->sysTimeStamp;
		$s = 'TO_CHAR('.$col.",'";
		
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= 'YYYY';
				break;
			case 'Q':
			case 'q':
				$s .= 'Q';
				break;
				
			case 'M':
				$s .= 'Mon';
				break;
				
			case 'm':
				$s .= 'MM';
				break;
			case 'D':
			case 'd':
				$s .= 'DD';
				break;
			
			case 'H':
				$s.= 'HH24';
				break;
				
			case 'h':
				$s .= 'HH';
				break;
				
			case 'i':
				$s .= 'MI';
				break;
			
			case 's':
				$s .= 'SS';
				break;
			
			case 'a':
			case 'A':
				$s .= 'AM';
				break;
				
			case 'w':
				$s .= 'D';
				break;
				
			case 'l':
				$s .= 'DAY';
				break;
				
			 case 'W':
				$s .= 'WW';
				break;
				
			default:
			// handle escape characters...
				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				if (strpos('-/.:;, ',$ch) !== false) $s .= $ch;
				else $s .= '"'.$ch.'"';
				
			}
		}
		return $s. "')";
	}
	
	function GetRandRow($sql, $arr = false)
	{
		$sql = "SELECT * FROM ($sql ORDER BY dbms_random.value) WHERE rownum = 1";
		
		return $this->GetRow($sql,$arr);
	}
	
	/*
	This algorithm makes use of
	
	a. FIRST_ROWS hint
	The FIRST_ROWS hint explicitly chooses the approach to optimize response time, 
	that is, minimum resource usage to return the first row. Results will be returned 
	as soon as they are identified. 

	b. Uses rownum tricks to obtain only the required rows from a given offset.
	 As this uses complicated sql statements, we only use this if the $offset >= 100. 
	 This idea by Tomas V V Cox.
	 
	 This implementation does not appear to work with oracle 8.0.5 or earlier. Comment
	 out this function then, and the slower SelectLimit() in the base class will be used.
	*/
	function SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0)
	{
		// seems that oracle only supports 1 hint comment in 8i
		if ($this->firstrows) {
			if ($nrows > 500 && $nrows < 1000) $hint = "FIRST_ROWS($nrows)";
			else $hint = 'FIRST_ROWS';
			
			if (strpos($sql,'/*+') !== false)
				$sql = str_replace('/*+ ',"/*+$hint ",$sql);
			else
				$sql = preg_replace('/^[ \t\n]*select/i',"SELECT /*+$hint*/",$sql);
		}
		
		if ($offset == -1 || ($offset < $this->selectOffsetAlg1 && 0 < $nrows && $nrows < 1000)) {
			if ($nrows > 0) {	
				if ($offset > 0) $nrows += $offset;
				//$inputarr['adodb_rownum'] = $nrows;
				if ($this->databaseType == 'oci8po') {
					$sql = "select * from (".$sql.") where rownum <= ?";
				} else {
					$sql = "select * from (".$sql.") where rownum <= :adodb_offset";
				} 
				$inputarr['adodb_offset'] = $nrows;
				$nrows = -1;
			}
			// note that $nrows = 0 still has to work ==> no rows returned

			$rs = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);
			return $rs;
			
		} else {
			 // Algorithm by Tomas V V Cox, from PEAR DB oci8.php
			
			 // Let Oracle return the name of the columns
			$q_fields = "SELECT * FROM (".$sql.") WHERE NULL = NULL";
		
			$false = false;
			if (! $stmt_arr = $this->Prepare($q_fields)) {
				return $false;
			}
			$stmt = $stmt_arr[1];
			 
			if (is_array($inputarr)) {
			 	foreach($inputarr as $k => $v) {
					if (is_array($v)) {
						if (sizeof($v) == 2) // suggested by g.giunta@libero.
							oci_bind_by_name($stmt,":$k",$inputarr[$k][0],$v[1]);
						else
							oci_bind_by_name($stmt,":$k",$inputarr[$k][0],$v[1],$v[2]);
					} else {
						$len = -1;
						if ($v === ' ') $len = 1;
						if (isset($bindarr)) {	// is prepared sql, so no need to oci_bind_by_name again
							$bindarr[$k] = $v;
						} else { 				// dynamic sql, so rebind every time
							oci_bind_by_name($stmt,":$k",$inputarr[$k],$len);
							
						}
					}
				}
			}
			
			 if (!oci_execute($stmt, OCI_DEFAULT)) {
				 oci_free_statement($stmt); 
				 return $false;
			 }
			 
			 $ncols = oci_num_fields($stmt);
			 for ( $i = 1; $i <= $ncols; $i++ ) {
				 $cols[] = '"'.oci_field_name($stmt, $i).'"';
			 }
			 $result = false;
			
			 oci_free_statement($stmt); 
			 $fields = implode(',', $cols);
			 if ($nrows <= 0) $nrows = 999999999999;
			 else $nrows += $offset;
			 $offset += 1; // in Oracle rownum starts at 1
			
			if ($this->databaseType == 'oci8po') {
					 $sql = "SELECT /*+ FIRST_ROWS */ $fields FROM".
					  "(SELECT rownum as adodb_rownum, $fields FROM".
					  " ($sql) WHERE rownum <= ?".
					  ") WHERE adodb_rownum >= ?";
				} else {
					 $sql = "SELECT /*+ FIRST_ROWS */ $fields FROM".
					  "(SELECT rownum as adodb_rownum, $fields FROM".
					  " ($sql) WHERE rownum <= :adodb_nrows".
					  ") WHERE adodb_rownum >= :adodb_offset";
				} 
				$inputarr['adodb_nrows'] = $nrows;
				$inputarr['adodb_offset'] = $offset;
				
			if ($secs2cache>0) $rs = $this->CacheExecute($secs2cache, $sql,$inputarr);
			else $rs = $this->Execute($sql,$inputarr);
			return $rs;
		}
	
	}
	
	/**
	* Usage:
	* Store BLOBs and CLOBs
	*
	* Example: to store $var in a blob
	*
	*	$conn->Execute('insert into TABLE (id,ablob) values(12,empty_blob())');
	*	$conn->UpdateBlob('TABLE', 'ablob', $varHoldingBlob, 'ID=12', 'BLOB');
	*	
	*	$blobtype supports 'BLOB' and 'CLOB', but you need to change to 'empty_clob()'.
	*
	*  to get length of LOB:
	*  	select DBMS_LOB.GETLENGTH(ablob) from TABLE
	*
	* If you are using CURSOR_SHARING = force, it appears this will case a segfault
	* under oracle 8.1.7.0. Run:
	*	 $db->Execute('ALTER SESSION SET CURSOR_SHARING=EXACT');
	* before UpdateBlob() then...
	*/

	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{
		
		//if (strlen($val) < 4000) return $this->Execute("UPDATE $table SET $column=:blob WHERE $where",array('blob'=>$val)) != false;
		
		switch(strtoupper($blobtype)) {
		default: ADOConnection::outp("<b>UpdateBlob</b>: Unknown blobtype=$blobtype"); return false;
		case 'BLOB': $type = OCI_B_BLOB; break;
		case 'CLOB': $type = OCI_B_CLOB; break;
		}
		
		if ($this->databaseType == 'oci8po') 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO ?";
		else 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO :blob";
		
		$desc = oci_new_descriptor($this->_connectionID, OCI_D_LOB);
		$arr['blob'] = array($desc,-1,$type);
		if ($this->session_sharing_force_blob) $this->Execute('ALTER SESSION SET CURSOR_SHARING=EXACT');
		$commit = $this->autoCommit;
		if ($commit) $this->BeginTrans();
		$rs = $this->_Execute($sql,$arr);
		if ($rez = !empty($rs)) $desc->save($val);
		$desc->free();
		if ($commit) $this->CommitTrans();
		if ($this->session_sharing_force_blob) $this->Execute('ALTER SESSION SET CURSOR_SHARING=FORCE');
		
		if ($rez) $rs->Close();
		return $rez;
	}
	
	/**
	* Usage:  store file pointed to by $val in a blob
	*/
	function UpdateBlobFile($table,$column,$val,$where,$blobtype='BLOB')
	{
		switch(strtoupper($blobtype)) {
		default: ADOConnection::outp( "<b>UpdateBlob</b>: Unknown blobtype=$blobtype"); return false;
		case 'BLOB': $type = OCI_B_BLOB; break;
		case 'CLOB': $type = OCI_B_CLOB; break;
		}
		
		if ($this->databaseType == 'oci8po') 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO ?";
		else 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO :blob";
		
		$desc = oci_new_descriptor($this->_connectionID, OCI_D_LOB);
		$arr['blob'] = array($desc,-1,$type);
		
		$this->BeginTrans();
		$rs = ADODB_oci8::Execute($sql,$arr);
		if ($rez = !empty($rs)) $desc->savefile($val);
		$desc->free();
		$this->CommitTrans();
		
		if ($rez) $rs->Close();
		return $rez;
	}

		/**
	 * Execute SQL 
	 *
	 * @param sql		SQL statement to execute, or possibly an array holding prepared statement ($sql[0] will hold sql text)
	 * @param [inputarr]	holds the input data to bind to. Null elements will be set to null.
	 * @return 		RecordSet or false
	 */
	function Execute($sql,$inputarr=false) 
	{
		if ($this->fnExecute) {
			$fn = $this->fnExecute;
			$ret = $fn($this,$sql,$inputarr);
			if (isset($ret)) return $ret;
		}
		if ($inputarr) {
			#if (!is_array($inputarr)) $inputarr = array($inputarr);
			
			$element0 = reset($inputarr); 
			$array2d =  $this->bulkBind && is_array($element0) && !is_object(reset($element0));
			
			# see http://phplens.com/lens/lensforum/msgs.php?id=18786
			if ($array2d || !$this->_bindInputArray) {
			
			# is_object check because oci8 descriptors can be passed in
			if ($array2d && $this->_bindInputArray) {
				if (is_string($sql))
					$stmt = $this->Prepare($sql);
				else
					$stmt = $sql;
					
				foreach($inputarr as $arr) {
					$ret = $this->_Execute($stmt,$arr);
					if (!$ret) return $ret;
				}
				return $ret;
			} else {
				$sqlarr = explode(':',$sql);
				$sql = '';
				$lastnomatch = -2;
				#var_dump($sqlarr);echo "<hr>";var_dump($inputarr);echo"<hr>";
				foreach($sqlarr as $k => $str) {
						if ($k == 0) { $sql = $str; continue; }
						// we need $lastnomatch because of the following datetime, 
						// eg. '10:10:01', which causes code to think that there is bind param :10 and :1
						$ok = preg_match('/^([0-9]*)/', $str, $arr); 
			
						if (!$ok) $sql .= $str;
						else {
							$at = $arr[1];
							if (isset($inputarr[$at]) || is_null($inputarr[$at])) {
								if ((strlen($at) == strlen($str) && $k < sizeof($arr)-1)) {
									$sql .= ':'.$str;
									$lastnomatch = $k;
								} else if ($lastnomatch == $k-1) {
									$sql .= ':'.$str;
								} else {
									if (is_null($inputarr[$at])) $sql .= 'null';
									else $sql .= $this->qstr($inputarr[$at]);
									$sql .= substr($str, strlen($at));
								}
							} else {
								$sql .= ':'.$str;
							}
							
						}
					}
					$inputarr = false;
				}
			}
			$ret = $this->_Execute($sql,$inputarr);
			
			
		} else {
			$ret = $this->_Execute($sql,false);
		}

		return $ret;
	}
	
	/*
		Example of usage:
		
		$stmt = $this->Prepare('insert into emp (empno, ename) values (:empno, :ename)');
	*/
	function Prepare($sql,$cursor=false)
	{
	static $BINDNUM = 0;
	
		$stmt = oci_parse($this->_connectionID,$sql);

		if (!$stmt) {
			$this->_errorMsg = false;
			$this->_errorCode = false;
			$arr = @oci_error($this->_connectionID);
			if ($arr === false) return false;
		
			$this->_errorMsg = $arr['message'];
			$this->_errorCode = $arr['code'];
			return false;
		}
		
		$BINDNUM += 1;
		
		$sttype = @oci_statement_type($stmt);
		if ($sttype == 'BEGIN' || $sttype == 'DECLARE') {
			return array($sql,$stmt,0,$BINDNUM, ($cursor) ? oci_new_cursor($this->_connectionID) : false);
		}
		return array($sql,$stmt,0,$BINDNUM);
	}
	
	/*
		Call an oracle stored procedure and returns a cursor variable as a recordset. 
		Concept by Robert Tuttle robert@ud.com
		
		Example:
			Note: we return a cursor variable in :RS2
			$rs = $db->ExecuteCursor("BEGIN adodb.open_tab(:RS2); END;",'RS2');
			
			$rs = $db->ExecuteCursor(
				"BEGIN :RS2 = adodb.getdata(:VAR1); END;", 
				'RS2',
				array('VAR1' => 'Mr Bean'));
			
	*/
	function ExecuteCursor($sql,$cursorName='rs',$params=false)
	{
		if (is_array($sql)) $stmt = $sql;
		else $stmt = ADODB_oci8::Prepare($sql,true); # true to allocate oci_new_cursor
	
		if (is_array($stmt) && sizeof($stmt) >= 5) {
			$hasref = true;
			$ignoreCur = false;
			$this->Parameter($stmt, $ignoreCur, $cursorName, false, -1, OCI_B_CURSOR);
			if ($params) {
				foreach($params as $k => $v) {
					$this->Parameter($stmt,$params[$k], $k);
				}
			}
		} else
			$hasref = false;
			
		$rs = $this->Execute($stmt);
		if ($rs) {
			if ($rs->databaseType == 'array') oci_free_cursor($stmt[4]);
			else if ($hasref) $rs->_refcursor = $stmt[4];
		}
		return $rs;
	}
	
	/*
		Bind a variable -- very, very fast for executing repeated statements in oracle. 
		Better than using
			for ($i = 0; $i < $max; $i++) {	
				$p1 = ?; $p2 = ?; $p3 = ?;
				$this->Execute("insert into table (col0, col1, col2) values (:0, :1, :2)", 
					array($p1,$p2,$p3));
			}
		
		Usage:
			$stmt = $DB->Prepare("insert into table (col0, col1, col2) values (:0, :1, :2)");
			$DB->Bind($stmt, $p1);
			$DB->Bind($stmt, $p2);
			$DB->Bind($stmt, $p3);
			for ($i = 0; $i < $max; $i++) {	
				$p1 = ?; $p2 = ?; $p3 = ?;
				$DB->Execute($stmt);
			}
			
		Some timings:		
			** Test table has 3 cols, and 1 index. Test to insert 1000 records
			Time 0.6081s (1644.60 inserts/sec) with direct oci_parse/oci_execute
			Time 0.6341s (1577.16 inserts/sec) with ADOdb Prepare/Bind/Execute
			Time 1.5533s ( 643.77 inserts/sec) with pure SQL using Execute
			
		Now if PHP only had batch/bulk updating like Java or PL/SQL...
	
		Note that the order of parameters differs from oci_bind_by_name,
		because we default the names to :0, :1, :2
	*/
	function Bind(&$stmt,&$var,$size=4000,$type=false,$name=false,$isOutput=false)
	{
		
		if (!is_array($stmt)) return false;
        
        if (($type == OCI_B_CURSOR) && sizeof($stmt) >= 5) { 
            return oci_bind_by_name($stmt[1],":".$name,$stmt[4],$size,$type);
        }
        
		if ($name == false) {
			if ($type !== false) $rez = oci_bind_by_name($stmt[1],":".$stmt[2],$var,$size,$type);
			else $rez = oci_bind_by_name($stmt[1],":".$stmt[2],$var,$size); // +1 byte for null terminator
			$stmt[2] += 1;
		} else if (oci_lob_desc($type)) {
			if ($this->debug) {
				ADOConnection::outp("<b>Bind</b>: name = $name");
			}
            //we have to create a new Descriptor here
			$numlob = count($this->_refLOBs);
        	$this->_refLOBs[$numlob]['LOB'] = oci_new_descriptor($this->_connectionID, oci_lob_desc($type));
			$this->_refLOBs[$numlob]['TYPE'] = $isOutput;
			
			$tmp = $this->_refLOBs[$numlob]['LOB'];
	        $rez = oci_bind_by_name($stmt[1], ":".$name, $tmp, -1, $type);
			if ($this->debug) {
				ADOConnection::outp("<b>Bind</b>: descriptor has been allocated, var (".$name.") binded");
			}
			
			// if type is input then write data to lob now
			if ($isOutput == false) {
				$var = $this->BlobEncode($var);
				$tmp->WriteTemporary($var);
				$this->_refLOBs[$numlob]['VAR'] = &$var;
				if ($this->debug) {
					ADOConnection::outp("<b>Bind</b>: LOB has been written to temp");
				}
			} else {
				$this->_refLOBs[$numlob]['VAR'] = &$var;
			}
			$rez = $tmp;
		} else {
			if ($this->debug) 
				ADOConnection::outp("<b>Bind</b>: name = $name");
			
			if ($type !== false) $rez = oci_bind_by_name($stmt[1],":".$name,$var,$size,$type);
			else $rez = oci_bind_by_name($stmt[1],":".$name,$var,$size); // +1 byte for null terminator
		}
		
		return $rez;
	}
	
	function Param($name,$type='C')
	{
		return ':'.$name;
	}
	
	/* 
	Usage:
		$stmt = $db->Prepare('select * from table where id =:myid and group=:group');
		$db->Parameter($stmt,$id,'myid');
		$db->Parameter($stmt,$group,'group');
		$db->Execute($stmt);
		
		@param $stmt Statement returned by Prepare() or PrepareSP().
		@param $var PHP variable to bind to
		@param $name Name of stored procedure variable name to bind to.
		@param [$isOutput] Indicates direction of parameter 0/false=IN  1=OUT  2= IN/OUT. This is ignored in oci8.
		@param [$maxLen] Holds an maximum length of the variable.
		@param [$type] The data type of $var. Legal values depend on driver.
		
		See oci_bind_by_name documentation at php.net.
	*/
	function Parameter(&$stmt,&$var,$name,$isOutput=false,$maxLen=4000,$type=false)
	{
			if  ($this->debug) {
				$prefix = ($isOutput) ? 'Out' : 'In';
				$ztype = (empty($type)) ? 'false' : $type;
				ADOConnection::outp( "{$prefix}Parameter(\$stmt, \$php_var='$var', \$name='$name', \$maxLen=$maxLen, \$type=$ztype);");
			}
			return $this->Bind($stmt,$var,$maxLen,$type,$name,$isOutput);
	}
	
	/*
	returns query ID if successful, otherwise false
	this version supports:
	
	   1. $db->execute('select * from table');
	   
	   2. $db->prepare('insert into table (a,b,c) values (:0,:1,:2)');
		  $db->execute($prepared_statement, array(1,2,3));
		  
	   3. $db->execute('insert into table (a,b,c) values (:a,:b,:c)',array('a'=>1,'b'=>2,'c'=>3));
	   
	   4. $db->prepare('insert into table (a,b,c) values (:0,:1,:2)');
		  $db->bind($stmt,1); $db->bind($stmt,2); $db->bind($stmt,3); 
		  $db->execute($stmt);
	*/ 
	function _query($sql,$inputarr=false)
	{
		if (is_array($sql)) { // is prepared sql
			$stmt = $sql[1];
			
			// we try to bind to permanent array, so that oci_bind_by_name is persistent
			// and carried out once only - note that max array element size is 4000 chars
			if (is_array($inputarr)) {
				$bindpos = $sql[3];
				if (isset($this->_bind[$bindpos])) {
				// all tied up already
					$bindarr = $this->_bind[$bindpos];
				} else {
				// one statement to bind them all
					$bindarr = array();
					foreach($inputarr as $k => $v) {
						$bindarr[$k] = $v;
						oci_bind_by_name($stmt,":$k",$bindarr[$k],is_string($v) && strlen($v)>4000 ? -1 : 4000);
					}
					$this->_bind[$bindpos] = $bindarr;
				}
			}
		} else {
			$stmt=oci_parse($this->_connectionID,$sql);
		}
			
		$this->_stmt = $stmt;
		if (!$stmt) return false;
	
		if (defined('ADODB_PREFETCH_ROWS')) @oci_set_prefetch($stmt,ADODB_PREFETCH_ROWS);
			
		if (is_array($inputarr)) {
			foreach($inputarr as $k => $v) {
				if (is_array($v)) {
					if (sizeof($v) == 2) // suggested by g.giunta@libero.
						oci_bind_by_name($stmt,":$k",$inputarr[$k][0],$v[1]);
					else
						oci_bind_by_name($stmt,":$k",$inputarr[$k][0],$v[1],$v[2]);
					
					if ($this->debug==99) {
						if (is_object($v[0])) 
							echo "name=:$k",' len='.$v[1],' type='.$v[2],'<br>';
						else
							echo "name=:$k",' var='.$inputarr[$k][0],' len='.$v[1],' type='.$v[2],'<br>';
						
					}
				} else {
					$len = -1;
					if ($v === ' ') $len = 1;
					if (isset($bindarr)) {	// is prepared sql, so no need to oci_bind_by_name again
						$bindarr[$k] = $v;
					} else { 				// dynamic sql, so rebind every time
						oci_bind_by_name($stmt,":$k",$inputarr[$k],$len);
					}
				}
			}
		}
		
        $this->_errorMsg = false;
		$this->_errorCode = false;
		if (oci_execute($stmt,$this->_commit)) {
			
			if (count($this -> _refLOBs) > 0) {
		
				foreach ($this -> _refLOBs as $key => $value) {
					if ($this -> _refLOBs[$key]['TYPE'] == true) {
						$tmp = $this -> _refLOBs[$key]['LOB'] -> load();
						if ($this -> debug) {
							ADOConnection::outp("<b>OUT LOB</b>: LOB has been loaded. <br>");
						}
						//$_GLOBALS[$this -> _refLOBs[$key]['VAR']] = $tmp;
						$this -> _refLOBs[$key]['VAR'] = $tmp;
					} else {
                        $this->_refLOBs[$key]['LOB']->save($this->_refLOBs[$key]['VAR']);
						$this -> _refLOBs[$key]['LOB']->free();
						unset($this -> _refLOBs[$key]);
                        if ($this->debug) {
							ADOConnection::outp("<b>IN LOB</b>: LOB has been saved. <br>");
						}
                    }					
				}
			}
		
            switch (@oci_statement_type($stmt)) {
                case "SELECT":
					return $stmt;
				
				case 'DECLARE':
                case "BEGIN":
                    if (is_array($sql) && !empty($sql[4])) {
						$cursor = $sql[4];
						if (is_resource($cursor)) {
							$ok = oci_execute($cursor);	
	                        return $cursor;
						}
						return $stmt;
                    } else {
						if (is_resource($stmt)) {
							oci_free_statement($stmt);
							return true;
						}
                        return $stmt;
                    }
                    break;
                default :
					
                    return true;
            }
		}
		return false;
	}
	
	// From Oracle Whitepaper: PHP Scalability and High Availability
	function IsConnectionError($err)
	{
		switch($err) {
			case 378: /* buffer pool param incorrect */
			case 602: /* core dump */
			case 603: /* fatal error */
			case 609: /* attach failed */
			case 1012: /* not logged in */
			case 1033: /* init or shutdown in progress */
			case 1043: /* Oracle not available */
			case 1089: /* immediate shutdown in progress */
			case 1090: /* shutdown in progress */
			case 1092: /* instance terminated */
			case 3113: /* disconnect */
			case 3114: /* not connected */
			case 3122: /* closing window */
			case 3135: /* lost contact */
			case 12153: /* TNS: not connected */
			case 27146: /* fatal or instance terminated */
			case 28511: /* Lost RPC */
			return true;
		}
		return false;
	}
	
	// returns true or false
	function _close()
	{
		if (!$this->_connectionID) return;
		
		if (!$this->autoCommit) oci_rollback($this->_connectionID);
		if (count($this->_refLOBs) > 0) {
			foreach ($this ->_refLOBs as $key => $value) {
				$this->_refLOBs[$key]['LOB']->free();
				unset($this->_refLOBs[$key]);
			}
		}
		oci_close($this->_connectionID);
		
		$this->_stmt = false;
		$this->_connectionID = false;
	}
	
	function MetaPrimaryKeys($table, $owner=false,$internalKey=false)
	{
		if ($internalKey) return array('ROWID');
		
	// tested with oracle 8.1.7
		$table = strtoupper($table);
		if ($owner) {
			$owner_clause = "AND ((a.OWNER = b.OWNER) AND (a.OWNER = UPPER('$owner')))";
			$ptab = 'ALL_';
		} else {
			$owner_clause = '';
			$ptab = 'USER_';
		}
		$sql = "
SELECT /*+ RULE */ distinct b.column_name
   FROM {$ptab}CONSTRAINTS a
	  , {$ptab}CONS_COLUMNS b
  WHERE ( UPPER(b.table_name) = ('$table'))
	AND (UPPER(a.table_name) = ('$table') and a.constraint_type = 'P')
	$owner_clause
	AND (a.constraint_name = b.constraint_name)";

 		$rs = $this->Execute($sql);
		if ($rs && !$rs->EOF) {
			$arr = $rs->GetArray();
			$a = array();
			foreach($arr as $v) {
				$a[] = reset($v);
			}
			return $a;
		}
		else return false;
	}
	
	// http://gis.mit.edu/classes/11.521/sqlnotes/referential_integrity.html
	function MetaForeignKeys($table, $owner=false)
	{
	global $ADODB_FETCH_MODE;
	
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$table = $this->qstr(strtoupper($table));
		if (!$owner) {
			$owner = $this->user;
			$tabp = 'user_';
		} else
			$tabp = 'all_';
			
		$owner = ' and owner='.$this->qstr(strtoupper($owner));
		
		$sql = 
"select constraint_name,r_owner,r_constraint_name 
	from {$tabp}constraints
	where constraint_type = 'R' and table_name = $table $owner";
		
		$constraints = $this->GetArray($sql);
		$arr = false;
		foreach($constraints as $constr) {
			$cons = $this->qstr($constr[0]);
			$rowner = $this->qstr($constr[1]);
			$rcons = $this->qstr($constr[2]);
			$cols = $this->GetArray("select column_name from {$tabp}cons_columns where constraint_name=$cons $owner order by position");
			$tabcol = $this->GetArray("select table_name,column_name from {$tabp}cons_columns where owner=$rowner and constraint_name=$rcons order by position");
			
			if ($cols && $tabcol) 
				for ($i=0, $max=sizeof($cols); $i < $max; $i++) {
					$arr[$tabcol[$i][0]] = $cols[$i][0].'='.$tabcol[$i][1];
				}
		}
		$ADODB_FETCH_MODE = $save;
		
		return $arr;
	}

	
	function CharMax()
	{
		return 4000;
	}
	
	function TextMax()
	{
		return 4000;
	}
	
	/**
	 * Quotes a string.
	 * An example is  $db->qstr("Don't bother",magic_quotes_runtime());
	 * 
	 * @param s			the string to quote
	 * @param [magic_quotes]	if $s is GET/POST var, set to get_magic_quotes_gpc().
	 *				This undoes the stupidity of magic quotes for GPC.
	 *
	 * @return  quoted string to be sent back to database
	 */
	function qstr($s,$magic_quotes=false)
	{	
		//$nofixquotes=false;
	
		if ($this->noNullStrings && strlen($s)==0)$s = ' ';
		if (!$magic_quotes) {	
			if ($this->replaceQuote[0] == '\\'){
				$s = str_replace('\\','\\\\',$s);
			}
			return  "'".str_replace("'",$this->replaceQuote,$s)."'";
		}
		
		// undo magic quotes for " unless sybase is on
		if (!ini_get('magic_quotes_sybase')) {
			$s = str_replace('\\"','"',$s);
			$s = str_replace('\\\\','\\',$s);
			return "'".str_replace("\\'",$this->replaceQuote,$s)."'";
		} else {
			return "'".$s."'";
		}
	}
	
}

/*--------------------------------------------------------------------------------------
		 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oci8 extends ADORecordSet {

	var $databaseType = 'oci8';
	var $bind=false;
	var $_fieldobjs;
	
	//var $_arr = false;
		
	function ADORecordset_oci8($queryID,$mode=false)
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch ($mode)
		{
		case ADODB_FETCH_ASSOC:$this->fetchMode = OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH:$this->fetchMode = OCI_NUM+OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		case ADODB_FETCH_NUM: 
		default:
		$this->fetchMode = OCI_NUM+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		}
		
		$this->adodbFetchMode = $mode;
		$this->_queryID = $queryID;
	}


	function Init()
	{
		if ($this->_inited) return;
		
		$this->_inited = true;
		if ($this->_queryID) {
			
			$this->_currentRow = 0;
			@$this->_initrs();
			if ($this->_numOfFields) $this->EOF = !$this->_fetch();
			else $this->EOF = true;
			
			/*
			// based on idea by Gaetano Giunta to detect unusual oracle errors
			// see http://phplens.com/lens/lensforum/msgs.php?id=6771
			$err = oci_error($this->_queryID);
			if ($err && $this->connection->debug) ADOConnection::outp($err);
			*/
			
			if (!is_array($this->fields)) {
				$this->_numOfRows = 0;
				$this->fields = array();
			}
		} else {
			$this->fields = array();
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
			$this->EOF = true;
		}
	}
	
	function _initrs()
	{
		$this->_numOfRows = -1;
		$this->_numOfFields = oci_num_fields($this->_queryID);
		if ($this->_numOfFields>0) {
			$this->_fieldobjs = array();
			$max = $this->_numOfFields;
			for ($i=0;$i<$max; $i++) $this->_fieldobjs[] = $this->_FetchField($i);
		}
	}

	  /*		Returns: an object containing field information.
			  Get column information in the Recordset object. fetchField() can be used in order to obtain information about
			  fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
			  fetchField() is retrieved.		*/

	function _FetchField($fieldOffset = -1)
	{
		$fld = new ADOFieldObject;
		$fieldOffset += 1;
		$fld->name =oci_field_name($this->_queryID, $fieldOffset);
		$fld->type = oci_field_type($this->_queryID, $fieldOffset);
		$fld->max_length = oci_field_size($this->_queryID, $fieldOffset);
	 	switch($fld->type) {
		case 'NUMBER':
	 		$p = oci_field_precision($this->_queryID, $fieldOffset);
			$sc = oci_field_scale($this->_queryID, $fieldOffset);
			if ($p != 0 && $sc == 0) $fld->type = 'INT';
			$fld->scale = $p;
			break;
		
	 	case 'CLOB':
		case 'NCLOB':
		case 'BLOB': 
			$fld->max_length = -1;
			break;
		}
		return $fld;
	}
	
	/* For some reason, oci_field_name fails when called after _initrs() so we cache it */
	function FetchField($fieldOffset = -1)
	{
		return $this->_fieldobjs[$fieldOffset];
	}
	
	
	/*
	// 10% speedup to move MoveNext to child class
	function _MoveNext() 
	{
	//global $ADODB_EXTENSION;if ($ADODB_EXTENSION) return @adodb_movenext($this);
		
		if ($this->EOF) return false;
		
		$this->_currentRow++;
		if($this->fields = @oci_fetch_array($this->_queryID,$this->fetchMode))
			return true;
		$this->EOF = true;
		
		return false;
	}	*/
	
	
	function MoveNext()
	{
		if ($this->fields = @oci_fetch_array($this->_queryID,$this->fetchMode)) {
			$this->_currentRow += 1;
			return true;
		}
		if (!$this->EOF) {
			$this->_currentRow += 1;
			$this->EOF = true;
		}
		return false;
	}
	
	/*
	# does not work as first record is retrieved in _initrs(), so is not included in GetArray()
	function GetArray($nRows = -1) 
	{
	global $ADODB_OCI8_GETARRAY;
	
		if (true ||  !empty($ADODB_OCI8_GETARRAY)) {
			# does not support $ADODB_ANSI_PADDING_OFF
	
			//OCI_RETURN_NULLS and OCI_RETURN_LOBS is set by oci_fetch_all
			switch($this->adodbFetchMode) {
			case ADODB_FETCH_NUM:
			
				$ncols = @oci_fetch_all($this->_queryID, $results, 0, $nRows, oci_fetch_all_BY_ROW+OCI_NUM);
				$results = array_merge(array($this->fields),$results);
				return $results;
				
			case ADODB_FETCH_ASSOC: 
				if (ADODB_ASSOC_CASE != 2 || $this->databaseType != 'oci8') break;
				
				$ncols = @oci_fetch_all($this->_queryID, $assoc, 0, $nRows, oci_fetch_all_BY_ROW);
				$results = array_merge(array($this->fields),$assoc);
				return $results;
			
			default:
				break;
			}
		}
			
		$results = ADORecordSet::GetArray($nRows);
		return $results;
		
	} */
	
	/* Optimize SelectLimit() by using oci_fetch() */
	function GetArrayLimit($nrows,$offset=-1) 
	{
		if ($offset <= 0) {
			$arr = $this->GetArray($nrows);
			return $arr;
		}
		$arr = array();
		for ($i=1; $i < $offset; $i++) 
			if (!@oci_fetch($this->_queryID)) return $arr;
			
		if (!$this->fields = @oci_fetch_array($this->_queryID,$this->fetchMode)) return $arr;;
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nrows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}
		
		return $results;
	}

	
	/* Use associative array to get fields array */
	function Fields($colname)
	{
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		
		 return $this->fields[$this->bind[strtoupper($colname)]];
	}
	


	function _seek($row)
	{
		return false;
	}

	function _fetch() 
	{
		return $this->fields = @oci_fetch_array($this->_queryID,$this->fetchMode);
	}

	/*		close() only needs to be called if you are worried about using too much memory while your script
			is running. All associated result memory for the specified result identifier will automatically be freed.		*/

	function _close() 
	{
		if ($this->connection->_stmt === $this->_queryID) $this->connection->_stmt = false;
		if (!empty($this->_refcursor)) {
			oci_free_cursor($this->_refcursor);
			$this->_refcursor = false;
		}
		@oci_free_statement($this->_queryID);
 		$this->_queryID = false;
		
	}

	function MetaType($t,$len=-1)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		switch (strtoupper($t)) {
	 	case 'VARCHAR':
	 	case 'VARCHAR2':
		case 'CHAR':
		case 'VARBINARY':
		case 'BINARY':
		case 'NCHAR':
		case 'NVARCHAR':
		case 'NVARCHAR2':
				 if ($len <= $this->blobSize) return 'C';
		
		case 'NCLOB':
		case 'LONG':
		case 'LONG VARCHAR':
		case 'CLOB':
		return 'X';
		
		case 'LONG RAW':
		case 'LONG VARBINARY':
		case 'BLOB':
			return 'B';
		
		case 'DATE': 
			return  ($this->connection->datetime) ? 'T' : 'D';
		
		
		case 'TIMESTAMP': return 'T';
		
		case 'INT': 
		case 'SMALLINT':
		case 'INTEGER': 
			return 'I';
			
		default: return 'N';
		}
	}
}

class ADORecordSet_ext_oci8 extends ADORecordSet_oci8 {	
	function ADORecordSet_ext_oci8($queryID,$mode=false) 
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch ($mode)
		{
		case ADODB_FETCH_ASSOC:$this->fetchMode = OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH:$this->fetchMode = OCI_NUM+OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		case ADODB_FETCH_NUM: 
		default: $this->fetchMode = OCI_NUM+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		}
		$this->adodbFetchMode = $mode;
		$this->_queryID = $queryID;
	}
	
	function MoveNext()
	{
		return adodb_movenext($this);
	}
}
?>