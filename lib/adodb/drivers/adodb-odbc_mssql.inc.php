<?php
/* 
V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  MSSQL support via ODBC. Requires ODBC. Works on Windows and Unix. 
  For Unix configuration, see http://phpbuilder.com/columns/alberto20000919.php3
*/

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}

 
class  ADODB_odbc_mssql extends ADODB_odbc {	
	var $databaseType = 'odbc_mssql';
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d h:i:sA'";
	var $_bindInputArray = true;
	var $metaTablesSQL="select name,case when type='U' then 'T' else 'V' end from sysobjects where (type='U' or type='V') and (name not in ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE'))";
	var $metaColumnsSQL = "select c.name,t.name,c.length from syscolumns c join systypes t on t.xusertype=c.xusertype join sysobjects o on o.id=c.id where o.name='%s'";
	var $hasTop = 'top';		// support mssql/interbase SELECT TOP 10 * FROM TABLE
	var $sysDate = 'GetDate()';
	var $sysTimeStamp = 'GetDate()';
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $upperCase = 'upper';
	var $substr = 'substring';
	var $length = 'len';
	var $ansiOuter = true; // for mssql7 or later
	var $identitySQL = 'select @@IDENTITY'; // 'select SCOPE_IDENTITY'; # for mssql 2000
	var $hasInsertID = true;
	var $connectStmt = 'SET CONCAT_NULL_YIELDS_NULL OFF'; # When SET CONCAT_NULL_YIELDS_NULL is ON, 
														  # concatenating a null value with a string yields a NULL result
	
	function ADODB_odbc_mssql()
	{
		$this->ADODB_odbc();
		$this->curmode = SQL_CUR_USE_ODBC;	
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
		
		$constraints =& $this->GetArray($sql);
		
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
	
	function &MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{
		if ($mask) {$this->debug=1;
			$save = $this->metaTablesSQL;
			$mask = $this->qstr($mask);
			$this->metaTablesSQL .= " AND name like $mask";
		}
		$ret =& ADOConnection::MetaTables($ttype,$showSchema);
		
		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}
	
	function &MetaColumns($table)
	{
		return ADOConnection::MetaColumns($table);
	}
	
	function _query($sql,$inputarr)
	{
		if (is_string($sql)) $sql = str_replace('||','+',$sql);
		return ADODB_odbc::_query($sql,$inputarr);
	}
	
	// "Stein-Aksel Basma" <basma@accelero.no>
	// tested with MSSQL 2000
	function &MetaPrimaryKeys($table)
	{
		$sql = "select k.column_name from information_schema.key_column_usage k,
		information_schema.table_constraints tc 
		where tc.constraint_name = k.constraint_name and tc.constraint_type =
		'PRIMARY KEY' and k.table_name = '$table'";
		
		$a = $this->GetCol($sql);
		if ($a && sizeof($a)>0) return $a;
		return false;	  
	}
	
	function &SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0)
	{
		if ($nrows > 0 && $offset <= 0) {
			$sql = preg_replace(
				'/(^\s*select\s+(distinctrow|distinct)?)/i','\\1 '.$this->hasTop." $nrows ",$sql);
			$rs =& $this->Execute($sql,$inputarr);
		} else
			$rs =& ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);
			
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
	
	function ADORecordSet_odbc_mssql($id,$mode=false)
	{
		return $this->ADORecordSet_odbc($id,$mode);
	}	
}
?>