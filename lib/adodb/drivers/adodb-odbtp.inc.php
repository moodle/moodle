<?php
/**
 * ODBTP driver
 *
 * @deprecated will be removed in ADOdb version 6
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
 * @author stefan bogdan <sbogdan@rsb.ro>
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

define("_ADODB_ODBTP_LAYER", 2 );

class ADODB_odbtp extends ADOConnection{
	var $databaseType = "odbtp";
	var $dataProvider = "odbtp";
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d, h:i:sA'";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $odbc_driver = 0;
	var $hasAffectedRows = true;
	var $hasInsertID = false;
	var $hasGenID = true;
	var $hasMoveFirst = true;

	var $_genSeqSQL = "create table %s (seq_name char(30) not null unique , seq_value integer not null)";
	var $_dropSeqSQL = "delete from adodb_seq where seq_name = '%s'";
	var $_bindInputArray = false;
	var $_useUnicodeSQL = false;
	var $_canPrepareSP = false;
	var $_dontPoolDBC = true;

	/** @var string DBMS name. */
	var $odbc_name;

	/** @var bool */
	var $_canSelectDb = false;

	/** @var mixed */
	var $_lastAffectedRows;

	function ServerInfo()
	{
		return array('description' => @odbtp_get_attr( ODB_ATTR_DBMSNAME, $this->_connectionID),
		             'version' => @odbtp_get_attr( ODB_ATTR_DBMSVER, $this->_connectionID));
	}

	function ErrorMsg()
	{
		if ($this->_errorMsg !== false) return $this->_errorMsg;
		if (empty($this->_connectionID)) return @odbtp_last_error();
		return @odbtp_last_error($this->_connectionID);
	}

	function ErrorNo()
	{
		if ($this->_errorCode !== false) return $this->_errorCode;
		if (empty($this->_connectionID)) return @odbtp_last_error_state();
			return @odbtp_last_error_state($this->_connectionID);
	}
/*
	function DBDate($d,$isfld=false)
	{
		if (empty($d) && $d !== 0) return 'null';
		if ($isfld) return "convert(date, $d, 120)";

		if (is_string($d)) $d = ADORecordSet::UnixDate($d);
		$d = adodb_date($this->fmtDate,$d);
		return "convert(date, $d, 120)";
	}

	function DBTimeStamp($d,$isfld=false)
	{
		if (empty($d) && $d !== 0) return 'null';
		if ($isfld) return "convert(datetime, $d, 120)";

		if (is_string($d)) $d = ADORecordSet::UnixDate($d);
		$d = adodb_date($this->fmtDate,$d);
		return "convert(datetime, $d, 120)";
	}
*/

	protected function _insertID($table = '', $column = '')
	{
	// SCOPE_IDENTITY()
	// Returns the last IDENTITY value inserted into an IDENTITY column in
	// the same scope. A scope is a module -- a stored procedure, trigger,
	// function, or batch. Thus, two statements are in the same scope if
	// they are in the same stored procedure, function, or batch.
			return $this->GetOne($this->identitySQL);
	}

	function _affectedrows()
	{
		if ($this->_queryID) {
			return @odbtp_affected_rows ($this->_queryID);
	   } else
		return 0;
	}

	function CreateSequence($seqname='adodbseq',$start=1)
	{
		//verify existence
		$num = $this->GetOne("select seq_value from adodb_seq");
		$seqtab='adodb_seq';
		if( $this->odbc_driver == ODB_DRIVER_FOXPRO ) {
			$path = @odbtp_get_attr( ODB_ATTR_DATABASENAME, $this->_connectionID );
			//if using vfp dbc file
			if( !strcasecmp(strrchr($path, '.'), '.dbc') )
                $path = substr($path,0,strrpos($path,'\/'));
           	$seqtab = $path . '/' . $seqtab;
        }
		if($num == false) {
			if (empty($this->_genSeqSQL)) return false;
			$ok = $this->Execute(sprintf($this->_genSeqSQL ,$seqtab));
		}
		$num = $this->GetOne("select seq_value from adodb_seq where seq_name='$seqname'");
		if ($num) {
			return false;
		}
		$start -= 1;
		return $this->Execute("insert into adodb_seq values('$seqname',$start)");
	}

	function DropSequence($seqname = 'adodbseq')
	{
		if (empty($this->_dropSeqSQL)) return false;
		return $this->Execute(sprintf($this->_dropSeqSQL,$seqname));
	}

	function GenID($seq='adodbseq',$start=1)
	{
		$seqtab='adodb_seq';
		if( $this->odbc_driver == ODB_DRIVER_FOXPRO) {
			$path = @odbtp_get_attr( ODB_ATTR_DATABASENAME, $this->_connectionID );
			//if using vfp dbc file
			if( !strcasecmp(strrchr($path, '.'), '.dbc') )
                $path = substr($path,0,strrpos($path,'\/'));
           	$seqtab = $path . '/' . $seqtab;
        }
		$MAXLOOPS = 100;
		while (--$MAXLOOPS>=0) {
			$num = $this->GetOne("select seq_value from adodb_seq where seq_name='$seq'");
			if ($num === false) {
				//verify if abodb_seq table exist
				$ok = $this->GetOne("select seq_value from adodb_seq ");
				if(!$ok) {
					//creating the sequence table adodb_seq
					$this->Execute(sprintf($this->_genSeqSQL ,$seqtab));
				}
				$start -= 1;
				$num = '0';
				$ok = $this->Execute("insert into adodb_seq values('$seq',$start)");
				if (!$ok) return false;
			}
			$ok = $this->Execute("update adodb_seq set seq_value=seq_value+1 where seq_name='$seq'");
			if($ok) {
				$num += 1;
				$this->genID = $num;
				return $num;
			}
		}
	if ($fn = $this->raiseErrorFn) {
		$fn($this->databaseType,'GENID',-32000,"Unable to generate unique id after $MAXLOOPS attempts",$seq,$num);
	}
		return false;
	}

	//example for $UserOrDSN
	//for visual fox : DRIVER={Microsoft Visual FoxPro Driver};SOURCETYPE=DBF;SOURCEDB=c:\YourDbfFileDir;EXCLUSIVE=NO;
	//for visual fox dbc: DRIVER={Microsoft Visual FoxPro Driver};SOURCETYPE=DBC;SOURCEDB=c:\YourDbcFileDir\mydb.dbc;EXCLUSIVE=NO;
	//for access : DRIVER={Microsoft Access Driver (*.mdb)};DBQ=c:\path_to_access_db\base_test.mdb;UID=root;PWD=;
	//for mssql : DRIVER={SQL Server};SERVER=myserver;UID=myuid;PWD=mypwd;DATABASE=OdbtpTest;
	//if uid & pwd can be separate
    function _connect($HostOrInterface, $UserOrDSN='', $argPassword='', $argDatabase='')
	{
		if ($argPassword && stripos($UserOrDSN,'DRIVER=') !== false) {
			$this->_connectionID = odbtp_connect($HostOrInterface,$UserOrDSN.';PWD='.$argPassword);
		} else
			$this->_connectionID = odbtp_connect($HostOrInterface,$UserOrDSN,$argPassword,$argDatabase);
		if ($this->_connectionID === false) {
			$this->_errorMsg = $this->ErrorMsg() ;
			return false;
		}

		odbtp_convert_datetime($this->_connectionID,true);

		if ($this->_dontPoolDBC) {
			if (function_exists('odbtp_dont_pool_dbc'))
				@odbtp_dont_pool_dbc($this->_connectionID);
		}
		else {
			$this->_dontPoolDBC = true;
		}
		$this->odbc_driver = @odbtp_get_attr(ODB_ATTR_DRIVER, $this->_connectionID);
		$dbms = strtolower(@odbtp_get_attr(ODB_ATTR_DBMSNAME, $this->_connectionID));
		$this->odbc_name = $dbms;

		// Account for inconsistent DBMS names
		if( $this->odbc_driver == ODB_DRIVER_ORACLE )
			$dbms = 'oracle';
		else if( $this->odbc_driver == ODB_DRIVER_SYBASE )
			$dbms = 'sybase';

		// Set DBMS specific attributes
		switch( $dbms ) {
			case 'microsoft sql server':
				$this->databaseType = 'odbtp_mssql';
				$this->fmtDate = "'Y-m-d'";
				$this->fmtTimeStamp = "'Y-m-d h:i:sA'";
				$this->sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
				$this->sysTimeStamp = 'GetDate()';
				$this->ansiOuter = true;
				$this->leftOuter = '*=';
				$this->rightOuter = '=*';
                $this->hasTop = 'top';
				$this->hasInsertID = true;
				$this->hasTransactions = true;
				$this->_bindInputArray = true;
				$this->_canSelectDb = true;
				$this->substr = "substring";
				$this->length = 'len';
				$this->identitySQL = 'select SCOPE_IDENTITY()';
				$this->metaDatabasesSQL = "select name from master..sysdatabases where name <> 'master'";
				$this->_canPrepareSP = true;
				break;
			case 'access':
				$this->databaseType = 'odbtp_access';
				$this->fmtDate = "#Y-m-d#";
				$this->fmtTimeStamp = "#Y-m-d h:i:sA#";
				$this->sysDate = "FORMAT(NOW,'yyyy-mm-dd')";
				$this->sysTimeStamp = 'NOW';
                $this->hasTop = 'top';
				$this->hasTransactions = false;
				$this->_canPrepareSP = true;  // For MS Access only.
				break;
			case 'visual foxpro':
				$this->databaseType = 'odbtp_vfp';
				$this->fmtDate = "{^Y-m-d}";
				$this->fmtTimeStamp = "{^Y-m-d, h:i:sA}";
				$this->sysDate = 'date()';
				$this->sysTimeStamp = 'datetime()';
				$this->ansiOuter = true;
                $this->hasTop = 'top';
				$this->hasTransactions = false;
				$this->replaceQuote = "'+chr(39)+'";
				$this->true = '.T.';
				$this->false = '.F.';

				break;
			case 'oracle':
				$this->databaseType = 'odbtp_oci8';
				$this->fmtDate = "'Y-m-d 00:00:00'";
				$this->fmtTimeStamp = "'Y-m-d h:i:sA'";
				$this->sysDate = 'TRUNC(SYSDATE)';
				$this->sysTimeStamp = 'SYSDATE';
				$this->hasTransactions = true;
				$this->_bindInputArray = true;
				$this->concat_operator = '||';
				break;
			case 'sybase':
				$this->databaseType = 'odbtp_sybase';
				$this->fmtDate = "'Y-m-d'";
				$this->fmtTimeStamp = "'Y-m-d H:i:s'";
				$this->sysDate = 'GetDate()';
				$this->sysTimeStamp = 'GetDate()';
				$this->leftOuter = '*=';
				$this->rightOuter = '=*';
				$this->hasInsertID = true;
				$this->hasTransactions = true;
				$this->identitySQL = 'select SCOPE_IDENTITY()';
				break;
			default:
				$this->databaseType = 'odbtp';
				if( @odbtp_get_attr(ODB_ATTR_TXNCAPABLE, $this->_connectionID) )
					$this->hasTransactions = true;
				else
					$this->hasTransactions = false;
		}
        @odbtp_set_attr(ODB_ATTR_FULLCOLINFO, TRUE, $this->_connectionID );

		if ($this->_useUnicodeSQL )
			@odbtp_set_attr(ODB_ATTR_UNICODESQL, TRUE, $this->_connectionID);

        return true;
	}

	function _pconnect($HostOrInterface, $UserOrDSN='', $argPassword='', $argDatabase='')
	{
		$this->_dontPoolDBC = false;
  		return $this->_connect($HostOrInterface, $UserOrDSN, $argPassword, $argDatabase);
	}

	function SelectDB($dbName)
	{
		if (!@odbtp_select_db($dbName, $this->_connectionID)) {
			return false;
		}
		$this->database = $dbName;
		return true;
	}

	function MetaTables($ttype='',$showSchema=false,$mask=false)
	{
	global $ADODB_FETCH_MODE;

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== false) $savefm = $this->SetFetchMode(false);

		$arr = $this->GetArray("||SQLTables||||$ttype");

		if (isset($savefm)) $this->SetFetchMode($savefm);
		$ADODB_FETCH_MODE = $savem;

		$arr2 = array();
		for ($i=0; $i < sizeof($arr); $i++) {
			if ($arr[$i][3] == 'SYSTEM TABLE' )	continue;
			if ($arr[$i][2])
				$arr2[] = $showSchema && $arr[$i][1]? $arr[$i][1].'.'.$arr[$i][2] : $arr[$i][2];
		}
		return $arr2;
	}

	function MetaColumns($table,$upper=true)
	{
	global $ADODB_FETCH_MODE;

		$schema = false;
		$this->_findschema($table,$schema);
		if ($upper) $table = strtoupper($table);

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== false) $savefm = $this->SetFetchMode(false);

		$rs = $this->Execute( "||SQLColumns||$schema|$table" );

		if (isset($savefm)) $this->SetFetchMode($savefm);
		$ADODB_FETCH_MODE = $savem;

		if (!$rs || $rs->EOF) {
			$false = false;
			return $false;
		}
		$retarr = array();
		while (!$rs->EOF) {
			//print_r($rs->fields);
			if (strtoupper($rs->fields[2]) == $table) {
				$fld = new ADOFieldObject();
				$fld->name = $rs->fields[3];
				$fld->type = $rs->fields[5];
				$fld->max_length = $rs->fields[6];
    			$fld->not_null = !empty($rs->fields[9]);
 				$fld->scale = $rs->fields[7];
				if (isset($rs->fields[12])) // vfp does not have field 12
	 				if (!is_null($rs->fields[12])) {
	 					$fld->has_default = true;
	 					$fld->default_value = $rs->fields[12];
					}
				$retarr[strtoupper($fld->name)] = $fld;
			} else if (!empty($retarr))
				break;
			$rs->MoveNext();
		}
		$rs->Close();

		return $retarr;
	}

	function MetaPrimaryKeys($table, $owner='')
	{
	global $ADODB_FETCH_MODE;

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$arr = $this->GetArray("||SQLPrimaryKeys||$owner|$table");
		$ADODB_FETCH_MODE = $savem;

		//print_r($arr);
		$arr2 = array();
		for ($i=0; $i < sizeof($arr); $i++) {
			if ($arr[$i][3]) $arr2[] = $arr[$i][3];
		}
		return $arr2;
	}

	public function metaForeignKeys($table, $owner = '', $upper = false, $associative = false)
	{
	global $ADODB_FETCH_MODE;

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$constraints = $this->GetArray("||SQLForeignKeys|||||$owner|$table");
		$ADODB_FETCH_MODE = $savem;

		$arr = false;
		foreach($constraints as $constr) {
			//print_r($constr);
			$arr[$constr[11]][$constr[2]][] = $constr[7].'='.$constr[3];
		}
		if (!$arr) {
			$false = false;
			return $false;
		}

		$arr2 = array();

		foreach($arr as $k => $v) {
			foreach($v as $a => $b) {
				if ($upper) $a = strtoupper($a);
				$arr2[$a] = $b;
			}
		}
		return $arr2;
	}

	function BeginTrans()
	{
		if (!$this->hasTransactions) return false;
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$this->autoCommit = false;
		if (defined('ODB_TXN_DEFAULT'))
			$txn = ODB_TXN_DEFAULT;
		else
			$txn = ODB_TXN_READUNCOMMITTED;
		$rs = @odbtp_set_attr(ODB_ATTR_TRANSACTIONS,$txn,$this->_connectionID);
		if(!$rs) return false;
		return true;
	}

	function CommitTrans($ok=true)
	{
		if ($this->transOff) return true;
		if (!$ok) return $this->RollbackTrans();
		if ($this->transCnt) $this->transCnt -= 1;
		$this->autoCommit = true;
		if( ($ret = @odbtp_commit($this->_connectionID)) )
			$ret = @odbtp_set_attr(ODB_ATTR_TRANSACTIONS, ODB_TXN_NONE, $this->_connectionID);//set transaction off
		return $ret;
	}

	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$this->autoCommit = true;
		if( ($ret = @odbtp_rollback($this->_connectionID)) )
			$ret = @odbtp_set_attr(ODB_ATTR_TRANSACTIONS, ODB_TXN_NONE, $this->_connectionID);//set transaction off
		return $ret;
	}

	function SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0)
	{
		// TOP requires ORDER BY for Visual FoxPro
		if( $this->odbc_driver == ODB_DRIVER_FOXPRO ) {
			if (!preg_match('/ORDER[ \t\r\n]+BY/is',$sql)) $sql .= ' ORDER BY 1';
		}
		$ret = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);
		return $ret;
	}

	function Prepare($sql)
	{
		if (! $this->_bindInputArray) return $sql; // no binding

        $this->_errorMsg = false;
		$this->_errorCode = false;

		$stmt = @odbtp_prepare($sql,$this->_connectionID);
		if (!$stmt) {
		//	print "Prepare Error for ($sql) ".$this->ErrorMsg()."<br>";
			return $sql;
		}
		return array($sql,$stmt,false);
	}

	function PrepareSP($sql, $param = true)
	{
		if (!$this->_canPrepareSP) return $sql; // Can't prepare procedures

        $this->_errorMsg = false;
		$this->_errorCode = false;

		$stmt = @odbtp_prepare_proc($sql,$this->_connectionID);
		if (!$stmt) return false;
		return array($sql,$stmt);
	}

	/*
	Usage:
		$stmt = $db->PrepareSP('SP_RUNSOMETHING'); -- takes 2 params, @myid and @group

		# note that the parameter does not have @ in front!
		$db->Parameter($stmt,$id,'myid');
		$db->Parameter($stmt,$group,'group',false,64);
		$db->Parameter($stmt,$group,'photo',false,100000,ODB_BINARY);
		$db->Execute($stmt);

		@param $stmt Statement returned by Prepare() or PrepareSP().
		@param $var PHP variable to bind to. Can set to null (for isNull support).
		@param $name Name of stored procedure variable name to bind to.
		@param [$isOutput] Indicates direction of parameter 0/false=IN  1=OUT  2= IN/OUT. This is ignored in odbtp.
		@param [$maxLen] Holds an maximum length of the variable.
		@param [$type] The data type of $var. Legal values depend on driver.

		See odbtp_attach_param documentation at http://odbtp.sourceforge.net.
	*/
	function Parameter(&$stmt, &$var, $name, $isOutput=false, $maxLen=0, $type=0)
	{
		if ( $this->odbc_driver == ODB_DRIVER_JET ) {
			$name = '['.$name.']';
			if( !$type && $this->_useUnicodeSQL
				&& @odbtp_param_bindtype($stmt[1], $name) == ODB_CHAR )
			{
				$type = ODB_WCHAR;
			}
		}
		else {
			$name = '@'.$name;
		}
		return @odbtp_attach_param($stmt[1], $name, $var, $type, $maxLen);
	}

	/*
		Insert a null into the blob field of the table first.
		Then use UpdateBlob to store the blob.

		Usage:

		$conn->Execute('INSERT INTO blobtable (id, blobcol) VALUES (1, null)');
		$conn->UpdateBlob('blobtable','blobcol',$blob,'id=1');
	*/

	function UpdateBlob($table,$column,$val,$where,$blobtype='image')
	{
		$sql = "UPDATE $table SET $column = ? WHERE $where";
		if( !($stmt = @odbtp_prepare($sql, $this->_connectionID)) )
			return false;
		if( !@odbtp_input( $stmt, 1, ODB_BINARY, 1000000, $blobtype ) )
			return false;
		if( !@odbtp_set( $stmt, 1, $val ) )
			return false;
		return @odbtp_execute( $stmt ) != false;
	}

	function MetaIndexes($table,$primary=false, $owner=false)
	{
		switch ( $this->odbc_driver) {
			case ODB_DRIVER_MSSQL:
				return $this->MetaIndexes_mssql($table, $primary);
			default:
				return array();
		}
	}

	function MetaIndexes_mssql($table,$primary=false, $owner = false)
	{
		$table = strtolower($this->qstr($table));

		$sql = "SELECT i.name AS ind_name, C.name AS col_name, USER_NAME(O.uid) AS Owner, c.colid, k.Keyno,
			CASE WHEN I.indid BETWEEN 1 AND 254 AND (I.status & 2048 = 2048 OR I.Status = 16402 AND O.XType = 'V') THEN 1 ELSE 0 END AS IsPK,
			CASE WHEN I.status & 2 = 2 THEN 1 ELSE 0 END AS IsUnique
			FROM dbo.sysobjects o INNER JOIN dbo.sysindexes I ON o.id = i.id
			INNER JOIN dbo.sysindexkeys K ON I.id = K.id AND I.Indid = K.Indid
			INNER JOIN dbo.syscolumns c ON K.id = C.id AND K.colid = C.Colid
			WHERE LEFT(i.name, 8) <> '_WA_Sys_' AND o.status >= 0 AND lower(O.Name) = $table
			ORDER BY O.name, I.Name, K.keyno";

		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
        	$savem = $this->SetFetchMode(FALSE);
        }

        $rs = $this->Execute($sql);
        if (isset($savem)) {
        	$this->SetFetchMode($savem);
        }
        $ADODB_FETCH_MODE = $save;

        if (!is_object($rs)) {
        	return FALSE;
        }

		$indexes = array();
		while ($row = $rs->FetchRow()) {
			if ($primary && !$row[5]) continue;

            $indexes[$row[0]]['unique'] = $row[6];
            $indexes[$row[0]]['columns'][] = $row[1];
    	}
        return $indexes;
	}

	function IfNull( $field, $ifNull )
	{
		switch( $this->odbc_driver ) {
			case ODB_DRIVER_MSSQL:
				return " ISNULL($field, $ifNull) ";
			case ODB_DRIVER_JET:
				return " IIF(IsNull($field), $ifNull, $field) ";
		}
		return " CASE WHEN $field is null THEN $ifNull ELSE $field END ";
	}

	function _query($sql,$inputarr=false)
	{
		$last_php_error = $this->resetLastError();
		$this->_errorMsg = false;
		$this->_errorCode = false;

 		if ($inputarr) {
			if (is_array($sql)) {
				$stmtid = $sql[1];
			} else {
				$stmtid = @odbtp_prepare($sql,$this->_connectionID);
				if ($stmtid == false) {
					$this->_errorMsg = $this->getChangedErrorMsg($last_php_error);
					return false;
				}
			}
			$num_params = @odbtp_num_params( $stmtid );
			/*
			for( $param = 1; $param <= $num_params; $param++ ) {
				@odbtp_input( $stmtid, $param );
				@odbtp_set( $stmtid, $param, $inputarr[$param-1] );
			}*/

			$param = 1;
			foreach($inputarr as $v) {
				@odbtp_input( $stmtid, $param );
				@odbtp_set( $stmtid, $param, $v );
				$param += 1;
				if ($param > $num_params) break;
			}

			if (!@odbtp_execute($stmtid) ) {
				return false;
			}
		} else if (is_array($sql)) {
			$stmtid = $sql[1];
			if (!@odbtp_execute($stmtid)) {
				return false;
			}
		} else {
			$stmtid = odbtp_query($sql,$this->_connectionID);
   		}
		$this->_lastAffectedRows = 0;
		if ($stmtid) {
				$this->_lastAffectedRows = @odbtp_affected_rows($stmtid);
		}
        return $stmtid;
	}

	function _close()
	{
		$ret = @odbtp_close($this->_connectionID);
		$this->_connectionID = false;
		return $ret;
	}
}

class ADORecordSet_odbtp extends ADORecordSet {

	var $databaseType = 'odbtp';
	var $canSeek = true;

	function __construct($queryID,$mode=false)
	{
		if ($mode === false) {
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		$this->fetchMode = $mode;
		parent::__construct($queryID);
	}

	function _initrs()
	{
		$this->_numOfFields = @odbtp_num_fields($this->_queryID);
		if (!($this->_numOfRows = @odbtp_num_rows($this->_queryID)))
			$this->_numOfRows = -1;

		if (!$this->connection->_useUnicodeSQL) return;

		if ($this->connection->odbc_driver == ODB_DRIVER_JET) {
			if (!@odbtp_get_attr(ODB_ATTR_MAPCHARTOWCHAR,
			                     $this->connection->_connectionID))
			{
				for ($f = 0; $f < $this->_numOfFields; $f++) {
					if (@odbtp_field_bindtype($this->_queryID, $f) == ODB_CHAR)
						@odbtp_bind_field($this->_queryID, $f, ODB_WCHAR);
				}
			}
		}
	}

	function FetchField($fieldOffset = 0)
	{
		$off=$fieldOffset; // offsets begin at 0
		$o= new ADOFieldObject();
		$o->name = @odbtp_field_name($this->_queryID,$off);
		$o->type = @odbtp_field_type($this->_queryID,$off);
        $o->max_length = @odbtp_field_length($this->_queryID,$off);
		if (ADODB_ASSOC_CASE == 0) $o->name = strtolower($o->name);
		else if (ADODB_ASSOC_CASE == 1) $o->name = strtoupper($o->name);
		return $o;
	}

	function _seek($row)
	{
		return @odbtp_data_seek($this->_queryID, $row);
	}

	function fields($colname)
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) return $this->fields[$colname];

		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$name = @odbtp_field_name( $this->_queryID, $i );
				$this->bind[strtoupper($name)] = $i;
			}
		}
		return $this->fields[$this->bind[strtoupper($colname)]];
	}

	function _fetch_odbtp($type=0)
	{
		switch ($this->fetchMode) {
			case ADODB_FETCH_NUM:
				$this->fields = @odbtp_fetch_row($this->_queryID, $type);
				break;
			case ADODB_FETCH_ASSOC:
				$this->fields = @odbtp_fetch_assoc($this->_queryID, $type);
				break;
            default:
				$this->fields = @odbtp_fetch_array($this->_queryID, $type);
		}
		if ($this->databaseType = 'odbtp_vfp') {
			if ($this->fields)
			foreach($this->fields as $k => $v) {
				if (strncmp($v,'1899-12-30',10) == 0) $this->fields[$k] = '';
			}
		}
		return is_array($this->fields);
	}

	function _fetch()
	{
		return $this->_fetch_odbtp();
	}

	function MoveFirst()
	{
		if (!$this->_fetch_odbtp(ODB_FETCH_FIRST)) return false;
		$this->EOF = false;
		$this->_currentRow = 0;
		return true;
    }

	function MoveLast()
	{
		if (!$this->_fetch_odbtp(ODB_FETCH_LAST)) return false;
		$this->EOF = false;
		$this->_currentRow = $this->_numOfRows - 1;
		return true;
	}

	function NextRecordSet()
	{
		if (!@odbtp_next_result($this->_queryID)) return false;
		$this->_inited = false;
		$this->bind = false;
		$this->_currentRow = -1;
		$this->Init();
		return true;
	}

	function _close()
	{
		return @odbtp_free_query($this->_queryID);
	}
}

class ADORecordSet_odbtp_mssql extends ADORecordSet_odbtp {

	var $databaseType = 'odbtp_mssql';

}

class ADORecordSet_odbtp_access extends ADORecordSet_odbtp {

	var $databaseType = 'odbtp_access';

}

class ADORecordSet_odbtp_vfp extends ADORecordSet_odbtp {

	var $databaseType = 'odbtp_vfp';

}

class ADORecordSet_odbtp_oci8 extends ADORecordSet_odbtp {

	var $databaseType = 'odbtp_oci8';

}

class ADORecordSet_odbtp_sybase extends ADORecordSet_odbtp {

	var $databaseType = 'odbtp_sybase';

}
