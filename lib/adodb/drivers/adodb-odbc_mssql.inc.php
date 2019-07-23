<?php
/*
@version   v5.20.14  06-Jan-2019
@copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.org/

  MSSQL support via ODBC. Requires ODBC. Works on Windows and Unix.
  For Unix configuration, see http://phpbuilder.com/columns/alberto20000919.php3
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}


class  ADODB_odbc_mssql extends ADODB_odbc {
	var $databaseType = 'odbc_mssql';
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d\TH:i:s'";
	var $_bindInputArray = true;
	var $metaDatabasesSQL = "select name from sysdatabases where name <> 'master'";
	var $metaTablesSQL="select name,case when type='U' then 'T' else 'V' end from sysobjects where (type='U' or type='V') and (name not in ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE'))";
	var $metaColumnsSQL = # xtype==61 is datetime
	"select c.name,t.name,c.length,c.isnullable, c.status,
		(case when c.xusertype=61 then 0 else c.xprec end),
		(case when c.xusertype=61 then 0 else c.xscale end)
		from syscolumns c join systypes t on t.xusertype=c.xusertype join sysobjects o on o.id=c.id where o.name='%s'";
	var $hasTop = 'top';		// support mssql/interbase SELECT TOP 10 * FROM TABLE
	var $sysDate = 'GetDate()';
	var $sysTimeStamp = 'GetDate()';
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $substr = 'substring';
	var $length = 'len';
	var $ansiOuter = true; // for mssql7 or later
	var $identitySQL = 'select SCOPE_IDENTITY()'; // 'select SCOPE_IDENTITY'; # for mssql 2000
	var $hasInsertID = true;
	var $connectStmt = 'SET CONCAT_NULL_YIELDS_NULL OFF'; # When SET CONCAT_NULL_YIELDS_NULL is ON,
														  # concatenating a null value with a string yields a NULL result

	function __construct()
	{
		parent::__construct();
		//$this->curmode = SQL_CUR_USE_ODBC;
	}

	// crashes php...
	function ServerInfo()
	{
	global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$row = $this->GetRow("execute sp_server_info 2");
		$ADODB_FETCH_MODE = $save;
		if (!is_array($row)) return false;
		$arr['description'] = $row[2];
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}

	function IfNull( $field, $ifNull )
	{
		return " ISNULL($field, $ifNull) "; // if MS SQL Server
	}

	function _insertid()
	{
	// SCOPE_IDENTITY()
	// Returns the last IDENTITY value inserted into an IDENTITY column in
	// the same scope. A scope is a module -- a stored procedure, trigger,
	// function, or batch. Thus, two statements are in the same scope if
	// they are in the same stored procedure, function, or batch.
			return $this->GetOne($this->identitySQL);
	}


	function MetaForeignKeys($table, $owner=false, $upper=false)
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
				$arr2[$a] = $b;
			}
		}
		return $arr2;
	}

	function MetaTables($ttype=false,$showSchema=false,$mask=false)
	{
		if ($mask) {//$this->debug=1;
			$save = $this->metaTablesSQL;
			$mask = $this->qstr($mask);
			$this->metaTablesSQL .= " AND name like $mask";
		}
		$ret = ADOConnection::MetaTables($ttype,$showSchema);

		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}

	function MetaColumns($table, $normalize=true)
	{

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
			if (!$primary && $row[5]) continue;

            $indexes[$row[0]]['unique'] = $row[6];
            $indexes[$row[0]]['columns'][] = $row[1];
    	}
        return $indexes;
	}

	function _query($sql,$inputarr=false)
	{
		if (is_string($sql)) $sql = str_replace('||','+',$sql);
		return ADODB_odbc::_query($sql,$inputarr);
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

	// "Stein-Aksel Basma" <basma@accelero.no>
	// tested with MSSQL 2000
	function MetaPrimaryKeys($table, $owner = false)
	{
	global $ADODB_FETCH_MODE;

		$schema = '';
		$this->_findschema($table,$schema);
		//if (!$schema) $schema = $this->database;
		if ($schema) $schema = "and k.table_catalog like '$schema%'";

		$sql = "select distinct k.column_name,ordinal_position from information_schema.key_column_usage k,
		information_schema.table_constraints tc
		where tc.constraint_name = k.constraint_name and tc.constraint_type =
		'PRIMARY KEY' and k.table_name = '$table' $schema order by ordinal_position ";

		$savem = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$a = $this->GetCol($sql);
		$ADODB_FETCH_MODE = $savem;

		if ($a && sizeof($a)>0) return $a;
		$false = false;
		return $false;
	}

	function SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0)
	{
		$nrows = (int) $nrows;
		$offset = (int) $offset;
		if ($nrows > 0 && $offset <= 0) {
			$sql = preg_replace(
				'/(^\s*select\s+(distinctrow|distinct)?)/i','\\1 '.$this->hasTop." $nrows ",$sql);
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

class  ADORecordSet_odbc_mssql extends ADORecordSet_odbc {

	var $databaseType = 'odbc_mssql';

	function __construct($id,$mode=false)
	{
		return parent::__construct($id,$mode);
	}
}
