<?php
/**
 * Native MSSQL driver.
 *
 * Requires mssql client. Works on Windows.
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
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

//----------------------------------------------------------------
// MSSQL returns dates with the format Oct 13 2002 or 13 Oct 2002
// and this causes tons of problems because localized versions of
// MSSQL will return the dates in dmy or  mdy order; and also the
// month strings depends on what language has been configured. The
// following two variables allow you to control the localization
// settings - Ugh.
//
// MORE LOCALIZATION INFO
// ----------------------
// To configure datetime, look for and modify sqlcommn.loc,
//  	typically found in c:\mssql\install
// Also read :
//	 http://support.microsoft.com/default.aspx?scid=kb;EN-US;q220918
// Alternatively use:
// 	   CONVERT(char(12),datecol,120)
//----------------------------------------------------------------


ini_set('mssql.datetimeconvert',0);

class ADODB_mssql extends ADOConnection {
	var $databaseType = "mssql";
	var $dataProvider = "mssql";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d\TH:i:s'";
	var $hasInsertID = true;
	var $substr = "substring";
	var $length = 'len';
	var $hasAffectedRows = true;
	var $metaDatabasesSQL = "select name from sysdatabases where name <> 'master'";
	var $metaTablesSQL="select name,case when type='U' then 'T' else 'V' end from sysobjects where (type='U' or type='V') and (name not in ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE','dtproperties'))";
	var $metaColumnsSQL = # xtype==61 is datetime
	"select c.name,t.name,c.length,c.isnullable, c.status,
		(case when c.xusertype=61 then 0 else c.xprec end),
		(case when c.xusertype=61 then 0 else c.xscale end)
	from syscolumns c join systypes t on t.xusertype=c.xusertype join sysobjects o on o.id=c.id where o.name='%s'";
	var $hasTop = 'top';		// support mssql SELECT TOP 10 * FROM TABLE
	var $hasGenID = true;
	var $sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
	var $sysTimeStamp = 'GetDate()';
	var $maxParameterLen = 4000;
	var $arrayClass = 'ADORecordSet_array_mssql';
	var $uniqueSort = true;
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $ansiOuter = true; // for mssql7 or later
	var $poorAffectedRows = true;
	var $identitySQL = 'select SCOPE_IDENTITY()'; // 'select SCOPE_IDENTITY'; # for mssql 2000
	var $uniqueOrderBy = true;
	var $_bindInputArray = true;
	var $forceNewConnect = false;

	function ServerInfo()
	{
	global $ADODB_FETCH_MODE;


		if ($this->fetchMode === false) {
			$savem = $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		} else
			$savem = $this->SetFetchMode(ADODB_FETCH_NUM);

		if (0) {
			$stmt = $this->PrepareSP('sp_server_info');
			$val = 2;
			$this->Parameter($stmt,$val,'attribute_id');
			$row = $this->GetRow($stmt);
		}

		$row = $this->GetRow("execute sp_server_info 2");


		if ($this->fetchMode === false) {
			$ADODB_FETCH_MODE = $savem;
		} else
			$this->SetFetchMode($savem);

		$arr['description'] = $row[2];
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}

	function IfNull( $field, $ifNull )
	{
		return " ISNULL($field, $ifNull) "; // if MS SQL Server
	}

	protected function _insertID($table = '', $column = '')
	{
	// SCOPE_IDENTITY()
	// Returns the last IDENTITY value inserted into an IDENTITY column in
	// the same scope. A scope is a module -- a stored procedure, trigger,
	// function, or batch. Thus, two statements are in the same scope if
	// they are in the same stored procedure, function, or batch.
		if ($this->lastInsID !== false) {
			return $this->lastInsID; // InsID from sp_executesql call
		} else {
			return $this->GetOne($this->identitySQL);
		}
	}



	/**
	 * Correctly quotes a string so that all strings are escaped.
	 * We prefix and append to the string single-quotes.
	 * An example is  $db->qstr("Don't bother");
	 *
	 * @param string $s            The string to quote
	 * @param bool   $magic_quotes This param is not used since 5.21.0.
	 *                             It remains for backwards compatibility.
	 *
	 * @return string Quoted string to be sent back to database
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	function qStr($s, $magic_quotes=false)
	{
		return  "'" . str_replace("'", $this->replaceQuote, $s) . "'";
	}

	function _affectedrows()
	{
		return $this->GetOne('select @@rowcount');
	}

	var $_dropSeqSQL = "drop table %s";

	function CreateSequence($seq='adodbseq',$start=1)
	{

		$this->Execute('BEGIN TRANSACTION adodbseq');
		$start -= 1;
		$this->Execute("create table $seq (id float(53))");
		$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
		if (!$ok) {
				$this->Execute('ROLLBACK TRANSACTION adodbseq');
				return false;
		}
		$this->Execute('COMMIT TRANSACTION adodbseq');
		return true;
	}

	function GenID($seq='adodbseq',$start=1)
	{
		//$this->debug=1;
		$this->Execute('BEGIN TRANSACTION adodbseq');
		$ok = $this->Execute("update $seq with (tablock,holdlock) set id = id + 1");
		if (!$ok) {
			$this->Execute("create table $seq (id float(53))");
			$ok = $this->Execute("insert into $seq with (tablock,holdlock) values($start)");
			if (!$ok) {
				$this->Execute('ROLLBACK TRANSACTION adodbseq');
				return false;
			}
			$this->Execute('COMMIT TRANSACTION adodbseq');
			return $start;
		}
		$num = $this->GetOne("select id from $seq");
		$this->Execute('COMMIT TRANSACTION adodbseq');
		return $num;

		// in old implementation, pre 1.90, we returned GUID...
		//return $this->GetOne("SELECT CONVERT(varchar(255), NEWID()) AS 'Char'");
	}


	function SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0)
	{
		$nrows = (int) $nrows;
		$offset = (int) $offset;
		if ($nrows > 0 && $offset <= 0) {
			$sql = preg_replace(
				'/(^\s*select\s+(distinctrow|distinct)?)/i','\\1 '.$this->hasTop." $nrows ",$sql);

			if ($secs2cache)
				$rs = $this->CacheExecute($secs2cache, $sql, $inputarr);
			else
				$rs = $this->Execute($sql,$inputarr);
		} else
			$rs = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);

		return $rs;
	}


	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{
		if (!$col) $col = $this->sysTimeStamp;
		$s = '';

		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			if ($s) $s .= '+';
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= "datename(yyyy,$col)";
				break;
			case 'M':
				$s .= "convert(char(3),$col,0)";
				break;
			case 'm':
				$s .= "replace(str(month($col),2),' ','0')";
				break;
			case 'Q':
			case 'q':
				$s .= "datename(quarter,$col)";
				break;
			case 'D':
			case 'd':
				$s .= "replace(str(day($col),2),' ','0')";
				break;
			case 'h':
				$s .= "substring(convert(char(14),$col,0),13,2)";
				break;

			case 'H':
				$s .= "replace(str(datepart(hh,$col),2),' ','0')";
				break;

			case 'i':
				$s .= "replace(str(datepart(mi,$col),2),' ','0')";
				break;
			case 's':
				$s .= "replace(str(datepart(ss,$col),2),' ','0')";
				break;
			case 'a':
			case 'A':
				$s .= "substring(convert(char(19),$col,0),18,2)";
				break;
			case 'l':
				$s .= "datename(dw,$col)";
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


	function BeginTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt += 1;
		$ok = $this->Execute('BEGIN TRAN');
		return $ok;
	}

	function CommitTrans($ok=true)
	{
		if ($this->transOff) return true;
		if (!$ok) return $this->RollbackTrans();
		if ($this->transCnt) $this->transCnt -= 1;
		$ok = $this->Execute('COMMIT TRAN');
		return $ok;
	}
	function RollbackTrans()
	{
		if ($this->transOff) return true;
		if ($this->transCnt) $this->transCnt -= 1;
		$ok = $this->Execute('ROLLBACK TRAN');
		return $ok;
	}

	function SetTransactionMode( $transaction_mode )
	{
		$this->_transmode  = $transaction_mode;
		if (empty($transaction_mode)) {
			$this->Execute('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
			return;
		}
		if (!stristr($transaction_mode,'isolation')) $transaction_mode = 'ISOLATION LEVEL '.$transaction_mode;
		$this->Execute("SET TRANSACTION ".$transaction_mode);
	}

	/*
		Usage:

		$this->BeginTrans();
		$this->RowLock('table1,table2','table1.id=33 and table2.id=table1.id'); # lock row 33 for both tables

		# some operation on both tables table1 and table2

		$this->CommitTrans();

		See http://www.swynk.com/friends/achigrik/SQL70Locks.asp
	*/
	function RowLock($tables,$where,$col='1 as adodbignore')
	{
		if ($col == '1 as adodbignore') $col = 'top 1 null as ignore';
		if (!$this->transCnt) $this->BeginTrans();
		return $this->GetOne("select $col from $tables with (ROWLOCK,HOLDLOCK) where $where");
	}


	function MetaColumns($table, $normalize=true)
	{
//		$arr = ADOConnection::MetaColumns($table);
//		return $arr;

		$this->_findschema($table,$schema);
		if ($schema) {
			$dbName = $this->database;
			$this->SelectDB($schema);
		}
		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

		if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
		$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table));

		if ($schema) {
			$this->SelectDB($dbName);
		}

		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		if (!is_object($rs)) {
			$false = false;
			return $false;
		}

		$retarr = array();
		while (!$rs->EOF){
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];
			$fld->type = $rs->fields[1];

			$fld->not_null = (!$rs->fields[3]);
			$fld->auto_increment = ($rs->fields[4] == 128);		// sys.syscolumns status field. 0x80 = 128 ref: http://msdn.microsoft.com/en-us/library/ms186816.aspx

			if (isset($rs->fields[5]) && $rs->fields[5]) {
				if ($rs->fields[5]>0) $fld->max_length = $rs->fields[5];
				$fld->scale = $rs->fields[6];
				if ($fld->scale>0) $fld->max_length += 1;
			} else
				$fld->max_length = $rs->fields[2];

			if ($save == ADODB_FETCH_NUM) {
				$retarr[] = $fld;
			} else {
				$retarr[strtoupper($fld->name)] = $fld;
			}
				$rs->MoveNext();
			}

			$rs->Close();
			return $retarr;

	}


	function MetaIndexes($table,$primary=false, $owner=false)
	{
		$table = $this->qstr($table);

		$sql = "SELECT i.name AS ind_name, C.name AS col_name, USER_NAME(O.uid) AS Owner, c.colid, k.Keyno,
			CASE WHEN I.indid BETWEEN 1 AND 254 AND (I.status & 2048 = 2048 OR I.Status = 16402 AND O.XType = 'V') THEN 1 ELSE 0 END AS IsPK,
			CASE WHEN I.status & 2 = 2 THEN 1 ELSE 0 END AS IsUnique
			FROM dbo.sysobjects o INNER JOIN dbo.sysindexes I ON o.id = i.id
			INNER JOIN dbo.sysindexkeys K ON I.id = K.id AND I.Indid = K.Indid
			INNER JOIN dbo.syscolumns c ON K.id = C.id AND K.colid = C.Colid
			WHERE LEFT(i.name, 8) <> '_WA_Sys_' AND o.status >= 0 AND O.Name LIKE $table
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

	public function metaForeignKeys($table, $owner = '', $upper = false, $associative = false)
	{
	global $ADODB_FETCH_MODE;

		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$table = $this->qstr(strtoupper($table));

		$sql =
"select object_name(constid) as constraint_name,
	col_name(fkeyid, fkey) as column_name,
	object_name(rkeyid) as referenced_table_name,
	col_name(rkeyid, rkey) as referenced_column_name
from sysforeignkeys
where upper(object_name(fkeyid)) = $table
order by constraint_name, referenced_table_name, keyno";

		$constraints = $this->GetArray($sql);

		$ADODB_FETCH_MODE = $save;

		$arr = false;
		foreach($constraints as $constr) {
			//print_r($constr);
			$arr[$constr[0]][$constr[2]][] = $constr[1].'='.$constr[3];
		}
		if (!$arr) return false;

		$arr2 = false;

		foreach($arr as $k => $v) {
			foreach($v as $a => $b) {
				if ($upper) $a = strtoupper($a);
				if (is_array($arr2[$a])) {	// a previous foreign key was define for this reference table, we merge the new one
					$arr2[$a] = array_merge($arr2[$a], $b);
				} else {
					$arr2[$a] = $b;
				}
			}
		}
		return $arr2;
	}

	//From: Fernando Moreira <FMoreira@imediata.pt>
	function MetaDatabases()
	{
		if(@mssql_select_db("master")) {
			$qry = $this->metaDatabasesSQL;
			if($rs = @mssql_query($qry,$this->_connectionID)) {
				$tmpAr = $ar = array();
				while($tmpAr = @mssql_fetch_row($rs)) {
					$ar[]=$tmpAr[0];
				}
				@mssql_select_db($this->database);
				if(sizeof($ar)) {
					return($ar);
				} else {
					return(false);
				}
			} else {
				@mssql_select_db($this->database);
				return(false);
			}
		}
		return(false);
	}

	// "Stein-Aksel Basma" <basma@accelero.no>
	// tested with MSSQL 2000
	function MetaPrimaryKeys($table, $owner=false)
	{
	global $ADODB_FETCH_MODE;

		$schema = '';
		$this->_findschema($table,$schema);
		if (!$schema) $schema = $this->database;
		if ($schema) $schema = "and k.table_catalog like '$schema%'";

		$sql = "select distinct k.column_name,ordinal_position from information_schema.key_column_usage k,
		information_schema.table_constraints tc
		where tc.constraint_name = k.constraint_name and tc.constraint_type =
		'PRIMARY KEY' and k.table_name = '$table' $schema order by ordinal_position ";

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$a = $this->GetCol($sql);
		$ADODB_FETCH_MODE = $savem;

		if ($a && sizeof($a)>0) return $a;
		$false = false;
		return $false;
	}


	function MetaTables($ttype=false,$showSchema=false,$mask=false)
	{
		if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr(($mask));
			$this->metaTablesSQL .= " AND name like $mask";
		}
		$ret = ADOConnection::MetaTables($ttype,$showSchema);

		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}

	function SelectDB($dbName)
	{
		$this->database = $dbName;
		$this->databaseName = $dbName; # obsolete, retained for compat with older adodb versions
		if ($this->_connectionID) {
			return @mssql_select_db($dbName);
		}
		else return false;
	}

	function ErrorMsg()
	{
		if (empty($this->_errorMsg)){
			$this->_errorMsg = mssql_get_last_message();
		}
		return $this->_errorMsg;
	}

	function ErrorNo()
	{
		if ($this->_logsql && $this->_errorCode !== false) return $this->_errorCode;
		if (empty($this->_errorMsg)) {
			$this->_errorMsg = mssql_get_last_message();
		}
		$id = @mssql_query("select @@ERROR",$this->_connectionID);
		if (!$id) return false;
		$arr = mssql_fetch_array($id);
		@mssql_free_result($id);
		if (is_array($arr)) {
			return $arr[0];
		} else {
			return -1;
		}
	}

	// returns true or false, newconnect supported since php 5.1.0.
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename,$newconnect=false)
	{
		if (!function_exists('mssql_pconnect')) return null;
		if (!empty($this->port)) $argHostname .= ":".$this->port;
		$this->_connectionID = mssql_connect($argHostname,$argUsername,$argPassword,$newconnect);
		if ($this->_connectionID === false) return false;
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;
	}


	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		if (!function_exists('mssql_pconnect')) return null;
		if (!empty($this->port)) $argHostname .= ":".$this->port;
		$this->_connectionID = mssql_pconnect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;

		// persistent connections can forget to rollback on crash, so we do it here.
		if ($this->autoRollback) {
			$cnt = $this->GetOne('select @@TRANCOUNT');
			while (--$cnt >= 0) $this->Execute('ROLLBACK TRAN');
		}
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;
	}

	function _nconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename, true);
	}

	function Prepare($sql)
	{
		$sqlarr = explode('?',$sql);
		if (sizeof($sqlarr) <= 1) return $sql;
		$sql2 = $sqlarr[0];
		for ($i = 1, $max = sizeof($sqlarr); $i < $max; $i++) {
			$sql2 .=  '@P'.($i-1) . $sqlarr[$i];
		}
		return array($sql,$this->qstr($sql2),$max,$sql2);
	}

	function PrepareSP($sql,$param=true)
	{
		$stmt = mssql_init($sql,$this->_connectionID);
		if (!$stmt)  return $sql;
		return array($sql,$stmt);
	}

	// returns concatenated string
	// MSSQL requires integers to be cast as strings
	// automatically cast every datatype to VARCHAR(255)
	// @author David Rogers (introspectshun)
	function Concat()
	{
			$s = "";
			$arr = func_get_args();

			// Split single record on commas, if possible
			if (sizeof($arr) == 1) {
				foreach ($arr as $arg) {
					$args = explode(',', $arg);
				}
				$arr = $args;
			}

			array_walk(
				$arr,
				function(&$value, $key) {
					$value = "CAST(" . $value . " AS VARCHAR(255))";
				}
			);
			$s = implode('+',$arr);
			if (sizeof($arr) > 0) return "$s";

			return '';
	}

	/*
	Usage:
		$stmt = $db->PrepareSP('SP_RUNSOMETHING'); -- takes 2 params, @myid and @group

		# note that the parameter does not have @ in front!
		$db->Parameter($stmt,$id,'myid');
		$db->Parameter($stmt,$group,'group',false,64);
		$db->Execute($stmt);

		@param $stmt Statement returned by Prepare() or PrepareSP().
		@param $var PHP variable to bind to. Can set to null (for isNull support).
		@param $name Name of stored procedure variable name to bind to.
		@param [$isOutput] Indicates direction of parameter 0/false=IN  1=OUT  2= IN/OUT. This is ignored in oci8.
		@param [$maxLen] Holds an maximum length of the variable.
		@param [$type] The data type of $var. Legal values depend on driver.

		See mssql_bind documentation at php.net.
	*/
	function Parameter(&$stmt, &$var, $name, $isOutput=false, $maxLen=4000, $type=false)
	{
		$isNull = is_null($var); // php 4.0.4 and above...

		if ($type === false)
			switch(gettype($var)) {
			default:
			case 'string': $type = SQLVARCHAR; break;
			case 'double': $type = SQLFLT8; break;
			case 'integer': $type = SQLINT4; break;
			case 'boolean': $type = SQLINT1; break; # SQLBIT not supported in 4.1.0
		}

		if  ($this->debug) {
			$prefix = ($isOutput) ? 'Out' : 'In';
			$ztype = (empty($type)) ? 'false' : $type;
			ADOConnection::outp( "{$prefix}Parameter(\$stmt, \$php_var='$var', \$name='$name', \$maxLen=$maxLen, \$type=$ztype);");
		}
		/*
			See PHPLens Issue No: 7231

			RETVAL is HARD CODED into php_mssql extension:
			The return value (a long integer value) is treated like a special OUTPUT parameter,
			called "RETVAL" (without the @). See the example at mssql_execute to
			see how it works. - type: one of this new supported PHP constants.
				SQLTEXT, SQLVARCHAR,SQLCHAR, SQLINT1,SQLINT2, SQLINT4, SQLBIT,SQLFLT8
		*/
		if ($name !== 'RETVAL') $name = '@'.$name;
		return mssql_bind($stmt[1], $name, $var, $type, $isOutput, $isNull, $maxLen);
	}

	/*
		Unfortunately, it appears that mssql cannot handle varbinary > 255 chars
		So all your blobs must be of type "image".

		Remember to set in php.ini the following...

		; Valid range 0 - 2147483647. Default = 4096.
		mssql.textlimit = 0 ; zero to pass through

		; Valid range 0 - 2147483647. Default = 4096.
		mssql.textsize = 0 ; zero to pass through
	*/
	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{

		if (strtoupper($blobtype) == 'CLOB') {
			$sql = "UPDATE $table SET $column='" . $val . "' WHERE $where";
			return $this->Execute($sql) != false;
		}
		$sql = "UPDATE $table SET $column=0x".bin2hex($val)." WHERE $where";
		return $this->Execute($sql) != false;
	}

	// returns query ID if successful, otherwise false
	function _query($sql,$inputarr=false)
	{
		$this->_errorMsg = false;
		if (is_array($inputarr)) {

			# bind input params with sp_executesql:
			# see http://www.quest-pipelines.com/newsletter-v3/0402_F.htm
			# works only with sql server 7 and newer
			$getIdentity = false;
			if (!is_array($sql) && preg_match('/^\\s*insert/i', $sql)) {
				$getIdentity = true;
				$sql .= (preg_match('/;\\s*$/i', $sql) ? ' ' : '; ') . $this->identitySQL;
			}
			if (!is_array($sql)) $sql = $this->Prepare($sql);
			$params = '';
			$decl = '';
			$i = 0;
			foreach($inputarr as $v) {
				if ($decl) {
					$decl .= ', ';
					$params .= ', ';
				}
				if (is_string($v)) {
					$len = strlen($v);
					if ($len == 0) $len = 1;

					if ($len > 4000 ) {
						// NVARCHAR is max 4000 chars. Let's use NTEXT
						$decl .= "@P$i NTEXT";
					} else {
						$decl .= "@P$i NVARCHAR($len)";
					}

					if(substr($v,0,1) == "'" && substr($v,-1,1) == "'")
						/*
						* String is already fully quoted
						*/
						$inputVar = $v;
					else
						$inputVar = $db->this($v);

					$params .= "@P$i=N" . $inputVar;

				} else if (is_integer($v)) {
					$decl .= "@P$i INT";
					$params .= "@P$i=".$v;
				} else if (is_float($v)) {
					$decl .= "@P$i FLOAT";
					$params .= "@P$i=".$v;
				} else if (is_bool($v)) {
					$decl .= "@P$i INT"; # Used INT just in case BIT in not supported on the user's MSSQL version. It will cast appropriately.
					$params .= "@P$i=".(($v)?'1':'0'); # True == 1 in MSSQL BIT fields and acceptable for storing logical true in an int field
				} else {
					$decl .= "@P$i CHAR"; # Used char because a type is required even when the value is to be NULL.
					$params .= "@P$i=NULL";
					}
				$i += 1;
			}
			$decl = $this->qstr($decl);
			if ($this->debug) ADOConnection::outp("<font size=-1>sp_executesql N{$sql[1]},N$decl,$params</font>");
			$rez = mssql_query("sp_executesql N{$sql[1]},N$decl,$params", $this->_connectionID);
			if ($getIdentity) {
				$arr = @mssql_fetch_row($rez);
				$this->lastInsID = isset($arr[0]) ? $arr[0] : false;
				@mssql_data_seek($rez, 0);
			}

		} else if (is_array($sql)) {
			# PrepareSP()
			$rez = mssql_execute($sql[1]);
			$this->lastInsID = false;

		} else {
			$rez = mssql_query($sql,$this->_connectionID);
			$this->lastInsID = false;
		}
		return $rez;
	}

	// returns true or false
	function _close()
	{
		if ($this->transCnt) {
			$this->RollbackTrans();
		}
		if($this->_connectionID) {
			$rez = mssql_close($this->_connectionID);
		}
		$this->_connectionID = false;
		return $rez;
	}



	/**
	* Returns a substring of a varchar type field
	*
	* The SQL server version varies because the length is mandatory, so
	* we append a reasonable string length
	*
	* @param	string	$fld	The field to sub-string
	* @param	int		$start	The start point
	* @param	int		$length	An optional length
	*
	* @return	The SQL text
	*/
	function substr($fld,$start,$length=0)
	{
		if ($length == 0)
			/*
		     * The length available to varchar is 2GB, but that makes no
			 * sense in a substring, so I'm going to arbitrarily limit
			 * the length to 1K, but you could change it if you want
			 */
			$length = 1024;

		$text = "SUBSTRING($fld,$start,$length)";
		return $text;
	}
}

/*--------------------------------------------------------------------------------------
	Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_mssql extends ADORecordSet {

	var $databaseType = "mssql";
	var $canSeek = true;
	var $hasFetchAssoc; // see PHPLens Issue No: 6083
	// _mths works only in non-localised system

	function __construct($id,$mode=false)
	{
		// freedts check...
		$this->hasFetchAssoc = function_exists('mssql_fetch_assoc');

		if ($mode === false) {
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;

		}
		$this->fetchMode = $mode;
		return parent::__construct($id);
	}


	function _initrs()
	{
	GLOBAL $ADODB_COUNTRECS;
		$this->_numOfRows = ($ADODB_COUNTRECS)? @mssql_num_rows($this->_queryID):-1;
		$this->_numOfFields = @mssql_num_fields($this->_queryID);
	}


	//Contributed by "Sven Axelsson" <sven.axelsson@bokochwebb.se>
	// get next resultset - requires PHP 4.0.5 or later
	function NextRecordSet()
	{
		if (!mssql_next_result($this->_queryID)) return false;
		$this->_inited = false;
		$this->bind = false;
		$this->_currentRow = -1;
		$this->Init();
		return true;
	}

	/* Use associative array to get fields array */
	function Fields($colname)
	{
		if ($this->fetchMode != ADODB_FETCH_NUM) return $this->fields[$colname];
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}

		return $this->fields[$this->bind[strtoupper($colname)]];
	}

	/*	Returns: an object containing field information.
		Get column information in the Recordset object. fetchField() can be used in order to obtain information about
		fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
		fetchField() is retrieved.	*/

	function FetchField($fieldOffset = -1)
	{
		if ($fieldOffset != -1) {
			$f = @mssql_fetch_field($this->_queryID, $fieldOffset);
		}
		else if ($fieldOffset == -1) {	/*	The $fieldOffset argument is not provided thus its -1 	*/
			$f = @mssql_fetch_field($this->_queryID);
		}
		$false = false;
		if (empty($f)) return $false;
		return $f;
	}

	function _seek($row)
	{
		return @mssql_data_seek($this->_queryID, $row);
	}

	// speedup
	function MoveNext()
	{
		if ($this->EOF) return false;

		$this->_currentRow++;

		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			if ($this->fetchMode & ADODB_FETCH_NUM) {
				//ADODB_FETCH_BOTH mode
				$this->fields = @mssql_fetch_array($this->_queryID);
			}
			else {
				if ($this->hasFetchAssoc) {// only for PHP 4.2.0 or later
					$this->fields = @mssql_fetch_assoc($this->_queryID);
				} else {
					$flds = @mssql_fetch_array($this->_queryID);
					if (is_array($flds)) {
						$fassoc = array();
						foreach($flds as $k => $v) {
							if (is_numeric($k)) continue;
							$fassoc[$k] = $v;
						}
						$this->fields = $fassoc;
					} else
						$this->fields = false;
				}
			}

			if (is_array($this->fields)) {
				if (ADODB_ASSOC_CASE == 0) {
					foreach($this->fields as $k=>$v) {
						$kn = strtolower($k);
						if ($kn <> $k) {
							unset($this->fields[$k]);
							$this->fields[$kn] = $v;
						}
					}
				} else if (ADODB_ASSOC_CASE == 1) {
					foreach($this->fields as $k=>$v) {
						$kn = strtoupper($k);
						if ($kn <> $k) {
							unset($this->fields[$k]);
							$this->fields[$kn] = $v;
						}
					}
				}
			}
		} else {
			$this->fields = @mssql_fetch_row($this->_queryID);
		}
		if ($this->fields) return true;
		$this->EOF = true;

		return false;
	}


	// INSERT UPDATE DELETE returns false even if no error occurs in 4.0.4
	// also the date format has been changed from YYYY-mm-dd to dd MMM YYYY in 4.0.4. Idiot!
	function _fetch($ignore_fields=false)
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			if ($this->fetchMode & ADODB_FETCH_NUM) {
				//ADODB_FETCH_BOTH mode
				$this->fields = @mssql_fetch_array($this->_queryID);
			} else {
				if ($this->hasFetchAssoc) // only for PHP 4.2.0 or later
					$this->fields = @mssql_fetch_assoc($this->_queryID);
				else {
					$this->fields = @mssql_fetch_array($this->_queryID);
					if (@is_array($this->fields)) {
						$fassoc = array();
						foreach($this->fields as $k => $v) {
							if (is_integer($k)) continue;
							$fassoc[$k] = $v;
						}
						$this->fields = $fassoc;
					}
				}
			}

			if (!$this->fields) {
			} else if (ADODB_ASSOC_CASE == 0) {
				foreach($this->fields as $k=>$v) {
					$kn = strtolower($k);
					if ($kn <> $k) {
						unset($this->fields[$k]);
						$this->fields[$kn] = $v;
					}
				}
			} else if (ADODB_ASSOC_CASE == 1) {
				foreach($this->fields as $k=>$v) {
					$kn = strtoupper($k);
					if ($kn <> $k) {
						unset($this->fields[$k]);
						$this->fields[$kn] = $v;
					}
				}
			}
		} else {
			$this->fields = @mssql_fetch_row($this->_queryID);
		}
		return $this->fields;
	}

	/*	close() only needs to be called if you are worried about using too much memory while your script
		is running. All associated result memory for the specified result identifier will automatically be freed.	*/

	function _close()
	{
		if($this->_queryID) {
			$rez = mssql_free_result($this->_queryID);
			$this->_queryID = false;
			return $rez;
		}
		return true;
	}

	/**
	* Returns the maximum size of a MetaType C field. Because of the
	* database design, SQL Server places no limits on the size of data inserted
	* Although the actual limit is 2^31-1 bytes.
	*
	* @return int
	*/
	function charMax()
	{
		return ADODB_STRINGMAX_NOLIMIT;
	}

	/**
	* Returns the maximum size of a MetaType X field. Because of the
	* database design, SQL Server places no limits on the size of data inserted
	* Although the actual limit is 2^31-1 bytes.
	*
	* @return int
	*/
	function textMax()
	{
		return ADODB_STRINGMAX_NOLIMIT;
	}

}


class ADORecordSet_array_mssql extends ADORecordSet_array {}

/*
Code Example 1:

select	object_name(constid) as constraint_name,
		object_name(fkeyid) as table_name,
		col_name(fkeyid, fkey) as column_name,
	object_name(rkeyid) as referenced_table_name,
	col_name(rkeyid, rkey) as referenced_column_name
from sysforeignkeys
where object_name(fkeyid) = x
order by constraint_name, table_name, referenced_table_name,  keyno

Code Example 2:
select 	constraint_name,
	column_name,
	ordinal_position
from information_schema.key_column_usage
where constraint_catalog = db_name()
and table_name = x
order by constraint_name, ordinal_position

http://www.databasejournal.com/scripts/article.php/1440551
*/
