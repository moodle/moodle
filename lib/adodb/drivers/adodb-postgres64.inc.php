<?php
/*
 V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
  
  Original version derived from Alberto Cerezal (acerezalp@dbnet.es) - DBNet Informatica & Comunicaciones. 
  08 Nov 2000 jlim - Minor corrections, removing mysql stuff
  09 Nov 2000 jlim - added insertid support suggested by "Christopher Kings-Lynne" <chriskl@familyhealth.com.au>
					jlim - changed concat operator to || and data types to MetaType to match documented pgsql types 
		 	see http://www.postgresql.org/devel-corner/docs/postgres/datatype.htm  
  22 Nov 2000 jlim - added changes to FetchField() and MetaTables() contributed by "raser" <raser@mail.zen.com.tw>
  27 Nov 2000 jlim - added changes to _connect/_pconnect from ideas by "Lennie" <leen@wirehub.nl>
  15 Dec 2000 jlim - added changes suggested by Additional code changes by "Eric G. Werk" egw@netguide.dk. 
  31 Jan 2002 jlim - finally installed postgresql. testing
  01 Mar 2001 jlim - Freek Dijkstra changes, also support for text type
  
  See http://www.varlena.com/varlena/GeneralBits/47.php
  
	-- What indexes are on my table?
	select * from pg_indexes where tablename = 'tablename';
	
	-- What triggers are on my table?
	select c.relname as "Table", t.tgname as "Trigger Name", 
	   t.tgconstrname as "Constraint Name", t.tgenabled as "Enabled",
	   t.tgisconstraint as "Is Constraint", cc.relname as "Referenced Table",
	   p.proname as "Function Name"
	from pg_trigger t, pg_class c, pg_class cc, pg_proc p
	where t.tgfoid = p.oid and t.tgrelid = c.oid
	   and t.tgconstrrelid = cc.oid
	   and c.relname = 'tablename';
	
	-- What constraints are on my table?
	select r.relname as "Table", c.conname as "Constraint Name",
	   contype as "Constraint Type", conkey as "Key Columns",
	   confkey as "Foreign Columns", consrc as "Source"
	from pg_class r, pg_constraint c
	where r.oid = c.conrelid
	   and relname = 'tablename';

*/

function adodb_addslashes($s)
{
	$len = strlen($s);
	if ($len == 0) return "''";
	if (strncmp($s,"'",1) === 0 && substr(s,$len-1) == "'") return $s; // already quoted
	
	return "'".addslashes($s)."'";
}

class ADODB_postgres64 extends ADOConnection{
	var $databaseType = 'postgres64';
	var $dataProvider = 'postgres';
	var $hasInsertID = true;
	var $_resultid = false;
  	var $concat_operator='||';
	var $metaDatabasesSQL = "select datname from pg_database where datname not in ('template0','template1') order by 1";
    var $metaTablesSQL = "select tablename,'T' from pg_tables where tablename not like 'pg\_%' union 
        select viewname,'V' from pg_views where viewname not like 'pg\_%'";
	//"select tablename from pg_tables where tablename not like 'pg_%' order by 1";
	var $isoDates = true; // accepts dates in ISO format
	var $sysDate = "CURRENT_DATE";
	var $sysTimeStamp = "CURRENT_TIMESTAMP";
	var $blobEncodeType = 'C';
	var $metaColumnsSQL = "SELECT a.attname,t.typname,a.attlen,a.atttypmod,a.attnotnull,a.atthasdef,a.attnum 
		FROM pg_class c, pg_attribute a,pg_type t 
		WHERE relkind = 'r' AND (c.relname='%s' or c.relname = lower('%s')) and a.attname not like '....%%'
AND a.attnum > 0 AND a.atttypid = t.oid AND a.attrelid = c.oid ORDER BY a.attnum";

	var $metaColumnsSQL1 = "SELECT a.attname, t.typname, a.attlen, a.atttypmod, a.attnotnull, a.atthasdef, a.attnum 
FROM pg_class c, pg_attribute a, pg_type t, pg_namespace n 
WHERE relkind = 'r' AND (c.relname='%s' or c.relname = lower('%s'))
 and c.relnamespace=n.oid and n.nspname='%s' 
	and a.attname not like '....%%' AND a.attnum > 0 
	AND a.atttypid = t.oid AND a.attrelid = c.oid ORDER BY a.attnum";
	
	// get primary key etc -- from Freek Dijkstra
	var $metaKeySQL = "SELECT ic.relname AS index_name, a.attname AS column_name,i.indisunique AS unique_key, i.indisprimary AS primary_key 
	FROM pg_class bc, pg_class ic, pg_index i, pg_attribute a WHERE bc.oid = i.indrelid AND ic.oid = i.indexrelid AND (i.indkey[0] = a.attnum OR i.indkey[1] = a.attnum OR i.indkey[2] = a.attnum OR i.indkey[3] = a.attnum OR i.indkey[4] = a.attnum OR i.indkey[5] = a.attnum OR i.indkey[6] = a.attnum OR i.indkey[7] = a.attnum) AND a.attrelid = bc.oid AND bc.relname = '%s'";
	
	var $hasAffectedRows = true;
	var $hasLimit = false;	// set to true for pgsql 7 only. support pgsql/mysql SELECT * FROM TABLE LIMIT 10
	// below suggested by Freek Dijkstra 
	var $true = 't';		// string that represents TRUE for a database
	var $false = 'f';		// string that represents FALSE for a database
	var $fmtDate = "'Y-m-d'";	// used by DBDate() as the default date format used by the database
	var $fmtTimeStamp = "'Y-m-d G:i:s'"; // used by DBTimeStamp as the default timestamp fmt.
	var $hasMoveFirst = true;
	var $hasGenID = true;
	var $_genIDSQL = "SELECT NEXTVAL('%s')";
	var $_genSeqSQL = "CREATE SEQUENCE %s START %s";
	var $_dropSeqSQL = "DROP SEQUENCE %s";
	var $metaDefaultsSQL = "SELECT d.adnum as num, d.adsrc as def from pg_attrdef d, pg_class c where d.adrelid=c.oid and c.relname='%s' order by d.adnum";
	var $random = 'random()';		/// random function
	var $autoRollback = true; // apparently pgsql does not autorollback properly before 4.3.4
							// http://bugs.php.net/bug.php?id=25404
							
	var $_bindInputArray = false; // requires postgresql 7.3+ and ability to modify database
	
	// The last (fmtTimeStamp is not entirely correct: 
	// PostgreSQL also has support for time zones, 
	// and writes these time in this format: "2001-03-01 18:59:26+02". 
	// There is no code for the "+02" time zone information, so I just left that out. 
	// I'm not familiar enough with both ADODB as well as Postgres 
	// to know what the concequences are. The other values are correct (wheren't in 0.94)
	// -- Freek Dijkstra 

	function ADODB_postgres64() 
	{
	// changes the metaColumnsSQL, adds columns: attnum[6]
	}
	
	function ServerInfo()
	{
		if (isset($this->version)) return $this->version;
		
		$arr['description'] = $this->GetOne("select version()");
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		$this->version = $arr;
		return $arr;
	}
/*
	function IfNull( $field, $ifNull ) 
	{
		return " NULLIF($field, $ifNull) "; // if PGSQL
	}
*/
	// get the last id - never tested
	function pg_insert_id($tablename,$fieldname)
	{
		$result=pg_exec($this->_connectionID, "SELECT last_value FROM ${tablename}_${fieldname}_seq");
		if ($result) {
			$arr = @pg_fetch_row($result,0);
			pg_freeresult($result);
			if (isset($arr[0])) return $arr[0];
		}
		return false;
	}
	
/* Warning from http://www.php.net/manual/function.pg-getlastoid.php:
Using a OID as a unique identifier is not generally wise. 
Unless you are very careful, you might end up with a tuple having 
a different OID if a database must be reloaded. */
	function _insertid()
	{
		if (!is_resource($this->_resultid) || get_resource_type($this->_resultid) !== 'pgsql result') return false;
	   	return pg_getlastoid($this->_resultid);
	}

// I get this error with PHP before 4.0.6 - jlim
// Warning: This compilation does not support pg_cmdtuples() in d:/inetpub/wwwroot/php/adodb/adodb-postgres.inc.php on line 44
   function _affectedrows()
   {
   		if (!is_resource($this->_resultid) || get_resource_type($this->_resultid) !== 'pgsql result') return false;
	   	return pg_cmdtuples($this->_resultid);
   }
   
	
		// returns true/false
	function BeginTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt += 1;
		return @pg_Exec($this->_connectionID, "begin");
	}
	
	function RowLock($tables,$where) 
	{
		if (!$this->transCnt) $this->BeginTrans();
		return $this->GetOne("select 1 as ignore from $tables where $where for update");
	}

	// returns true/false. 
	function CommitTrans($ok=true) 
	{ 
		if ($this->transOff) return true;
		if (!$ok) return $this->RollbackTrans();
		
		$this->transCnt -= 1;
		return @pg_Exec($this->_connectionID, "commit");
	}
	
	// returns true/false
	function RollbackTrans()
	{
		if ($this->transOff) return true;
		$this->transCnt -= 1;
		return @pg_Exec($this->_connectionID, "rollback");
	}
	
	function &MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{	
		if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr(strtolower($mask));
			$this->metaTablesSQL = "
select tablename,'T' from pg_tables where tablename like $mask union 
select viewname,'V' from pg_views where viewname like $mask";
		}
		$ret =& ADOConnection::MetaTables($ttype,$showSchema);
		
		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}
	
	/*
	// if magic quotes disabled, use pg_escape_string()
	function qstr($s,$magic_quotes=false)
	{
		if (!$magic_quotes) {
			if (ADODB_PHPVER >= 0x4200) {
				return  "'".pg_escape_string($s)."'";
			}
			if ($this->replaceQuote[0] == '\\'){
				$s = adodb_str_replace(array('\\',"\0"),array('\\\\',"\\\0"),$s);
			}
			return  "'".str_replace("'",$this->replaceQuote,$s)."'"; 
		}
		
		// undo magic quotes for "
		$s = str_replace('\\"','"',$s);
		return "'$s'";
	}
	*/
	
	
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
	
	
	
	/* 
	* Load a Large Object from a file 
	* - the procedure stores the object id in the table and imports the object using 
	* postgres proprietary blob handling routines 
	*
	* contributed by Mattia Rossi mattia@technologist.com
	* modified for safe mode by juraj chlebec
	*/ 
	function UpdateBlobFile($table,$column,$path,$where,$blobtype='BLOB') 
	{ 
		pg_exec ($this->_connectionID, "begin"); 
		
		$fd = fopen($path,'r');
		$contents = fread($fd,filesize($path));
		fclose($fd);
		
		$oid = pg_lo_create($this->_connectionID);
		$handle = pg_lo_open($this->_connectionID, $oid, 'w');
		pg_lo_write($handle, $contents);
		pg_lo_close($handle);
		
		// $oid = pg_lo_import ($path); 
		pg_exec($this->_connectionID, "commit"); 
		$rs = ADOConnection::UpdateBlob($table,$column,$oid,$where,$blobtype); 
		$rez = !empty($rs); 
		return $rez; 
	} 
	
	/* 
	* If an OID is detected, then we use pg_lo_* to open the oid file and read the
	* real blob from the db using the oid supplied as a parameter. If you are storing
	* blobs using bytea, we autodetect and process it so this function is not needed.
	*
	* contributed by Mattia Rossi mattia@technologist.com
	*
	* see http://www.postgresql.org/idocs/index.php?largeobjects.html
	*/ 
	function BlobDecode( $blob) 
	{ 
		if (strlen($blob) > 24) return $blob;
		
		@pg_exec($this->_connectionID,"begin"); 
		$fd = @pg_lo_open($this->_connectionID,$blob,"r");
		if ($fd === false) {
			@pg_exec($this->_connectionID,"commit");
			return $blob;
		}
		$realblob = @pg_loreadall($fd); 
		@pg_loclose($fd); 
		@pg_exec($this->_connectionID,"commit"); 
		return $realblob;
	} 
	
	/* 
		See http://www.postgresql.org/idocs/index.php?datatype-binary.html
	 	
		NOTE: SQL string literals (input strings) must be preceded with two backslashes 
		due to the fact that they must pass through two parsers in the PostgreSQL 
		backend.
	*/
	function BlobEncode($blob)
	{
		if (ADODB_PHPVER >= 0x4200) return pg_escape_bytea($blob);
		
		/*92=backslash, 0=null, 39=single-quote*/
		$badch = array(chr(92),chr(0),chr(39)); # \  null  '
		$fixch = array('\\\\134','\\\\000','\\\\047');
		return adodb_str_replace($badch,$fixch,$blob);
		
		// note that there is a pg_escape_bytea function only for php 4.2.0 or later
	}
	
	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
	{
		// do not use bind params which uses qstr(), as blobencode() already quotes data
		return $this->Execute("UPDATE $table SET $column='".$this->BlobEncode($val)."'::bytea WHERE $where");
	}
	
	function OffsetDate($dayFraction,$date=false)
	{		
		if (!$date) $date = $this->sysDate;
		return "($date+interval'$dayFraction days')";
	}
	

	// for schema support, pass in the $table param "$schema.$tabname".
	// converts field names to lowercase, $upper is ignored
	function &MetaColumns($table,$upper=true) 
	{
	global $ADODB_FETCH_MODE;
	
		$schema = false;
		$this->_findschema($table,$schema);
		
		$table = strtolower($table);

		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
		
		if ($schema) $rs =& $this->Execute(sprintf($this->metaColumnsSQL1,$table,$table,$schema));
		else $rs =& $this->Execute(sprintf($this->metaColumnsSQL,$table,$table));
		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		
		if ($rs === false) return false;
		
		if (!empty($this->metaKeySQL)) {
			// If we want the primary keys, we have to issue a separate query
			// Of course, a modified version of the metaColumnsSQL query using a 
			// LEFT JOIN would have been much more elegant, but postgres does 
			// not support OUTER JOINS. So here is the clumsy way.
			
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
			
			$rskey = $this->Execute(sprintf($this->metaKeySQL,($table)));
			// fetch all result in once for performance.
			$keys =& $rskey->GetArray();
			if (isset($savem)) $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
			
			$rskey->Close();
			unset($rskey);
		}

		$rsdefa = array();
		if (!empty($this->metaDefaultsSQL)) {
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
			$sql = sprintf($this->metaDefaultsSQL, ($table));
			$rsdef = $this->Execute($sql);
			if (isset($savem)) $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
			
			if ($rsdef) {
				while (!$rsdef->EOF) {
					$num = $rsdef->fields['num'];
					$s = $rsdef->fields['def'];
					if (substr($s, 0, 1) == "'") { /* quoted strings hack... for now... fixme */
						$s = substr($s, 1);
						$s = substr($s, 0, strlen($s) - 1);
					}

					$rsdefa[$num] = $s;
					$rsdef->MoveNext();
				}
			} else {
				ADOConnection::outp( "==> SQL => " . $sql);
			}
			unset($rsdef);
		}
	
		$retarr = array();
		while (!$rs->EOF) { 	
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];
			$fld->type = $rs->fields[1];
			$fld->max_length = $rs->fields[2];
			if ($fld->max_length <= 0) $fld->max_length = $rs->fields[3]-4;
			if ($fld->max_length <= 0) $fld->max_length = -1;
			
			// dannym
			// 5 hasdefault; 6 num-of-column
			$fld->has_default = ($rs->fields[5] == 't');
			if ($fld->has_default) {
				$fld->default_value = $rsdefa[$rs->fields[6]];
			}

			//Freek
			if ($rs->fields[4] == $this->true) {
				$fld->not_null = true;
			}
			
			// Freek
			if (is_array($keys)) {
				foreach($keys as $key) {
					if ($fld->name == $key['column_name'] AND $key['primary_key'] == $this->true) 
						$fld->primary_key = true;
					if ($fld->name == $key['column_name'] AND $key['unique_key'] == $this->true) 
						$fld->unique = true; // What name is more compatible?
				}
			}
			
			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] = $fld;	
			else $retarr[($upper) ? strtoupper($fld->name) : $fld->name] = $fld;
			
			$rs->MoveNext();
		}
		$rs->Close();
		return $retarr;	
		
	}

	  function &MetaIndexes ($table, $primary = FALSE)
      {
         global $ADODB_FETCH_MODE;
                
				$schema = false;
				$this->_findschema($table,$schema);
				
				if ($schema) { // requires pgsql 7.3+ - pg_namespace used.
					$sql = '
SELECT c.relname as "Name", i.indisunique as "Unique", i.indkey as "Columns" 
FROM pg_catalog.pg_class c 
JOIN pg_catalog.pg_index i ON i.indexrelid=c.oid 
JOIN pg_catalog.pg_class c2 ON c2.oid=i.indrelid
	,pg_namespace n 
WHERE c2.relname=\'%s\' and c.relnamespace=c2.relnamespace and c.relnamespace=n.oid and n.nspname=\'%s\' AND i.indisprimary=false';
				} else {
	                $sql = '
SELECT c.relname as "Name", i.indisunique as "Unique", i.indkey as "Columns"
FROM pg_catalog.pg_class c
JOIN pg_catalog.pg_index i ON i.indexrelid=c.oid
JOIN pg_catalog.pg_class c2 ON c2.oid=i.indrelid
WHERE c2.relname=\'%s\'';
    			}
				            
                if ($primary == FALSE) {
                        $sql .= ' AND i.indisprimary=false;';
                }
                
                $save = $ADODB_FETCH_MODE;
                $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
                if ($this->fetchMode !== FALSE) {
                        $savem = $this->SetFetchMode(FALSE);
                }
                
                $rs = $this->Execute(sprintf($sql,$table,$schema));
                
                if (isset($savem)) {
                        $this->SetFetchMode($savem);
                }
                $ADODB_FETCH_MODE = $save;
                
                if (!is_object($rs)) {
                        return FALSE;
                }
                
                $col_names = $this->MetaColumnNames($table);
                $indexes = array();
                
                while ($row = $rs->FetchRow()) {
                        $columns = array();
                        
                        foreach (explode(' ', $row[2]) as $col) {
                                $columns[] = $col_names[$col - 1];
                        }
                        
                        $indexes[$row[0]] = array(
                                'unique' => ($row[1] == 't'),
                                'columns' => $columns
                        );
                }
                
                return $indexes;
        }

	// returns true or false
	//
	// examples:
	// 	$db->Connect("host=host1 user=user1 password=secret port=4341");
	// 	$db->Connect('host1','user1','secret');
	function _connect($str,$user='',$pwd='',$db='',$ctype=0)
	{
		
		if (!function_exists('pg_pconnect')) return false;
		
		$this->_errorMsg = false;
		
		if ($user || $pwd || $db) {
			$user = adodb_addslashes($user);
			$pwd = adodb_addslashes($pwd);
			if (strlen($db) == 0) $db = 'template1';
			$db = adodb_addslashes($db);
		   	if ($str)  {
			 	$host = split(":", $str);
				if ($host[0]) $str = "host=".adodb_addslashes($host[0]);
				else $str = 'host=localhost';
				if (isset($host[1])) $str .= " port=$host[1]";
			}
		   		if ($user) $str .= " user=".$user;
		   		if ($pwd)  $str .= " password=".$pwd;
				if ($db)   $str .= " dbname=".$db;
		}

		//if ($user) $linea = "user=$user host=$linea password=$pwd dbname=$db port=5432";
		
		if ($ctype === 1) { // persistent
			$this->_connectionID = pg_pconnect($str);
		} else {
			if ($ctype === -1) { // nconnect, we trick pgsql ext by changing the connection str
			static $ncnt;
			
				if (empty($ncnt)) $ncnt = 1;
				else $ncnt += 1;
				
				$str .= str_repeat(' ',$ncnt);
			}
			$this->_connectionID = pg_connect($str);
		}
		if ($this->_connectionID === false) return false;
		$this->Execute("set datestyle='ISO'");
		return true;
	}
	
	function _nconnect($argHostname, $argUsername, $argPassword, $argDatabaseName)
	{
	 	return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabaseName,-1);
	}
	 
	// returns true or false
	//
	// examples:
	// 	$db->PConnect("host=host1 user=user1 password=secret port=4341");
	// 	$db->PConnect('host1','user1','secret');
	function _pconnect($str,$user='',$pwd='',$db='')
	{
		return $this->_connect($str,$user,$pwd,$db,1);
	}
	

	// returns queryID or false
	function _query($sql,$inputarr)
	{
		
		if ($inputarr) {
		/*
			It appears that PREPARE/EXECUTE is slower for many queries.
			
			For query executed 1000 times:
			"select id,firstname,lastname from adoxyz 
				where firstname not like ? and lastname not like ? and id = ?"
				
			with plan = 1.51861286163 secs
			no plan =   1.26903700829 secs

			

		*/
			$plan = 'P'.md5($sql);
				
			$execp = '';
			foreach($inputarr as $v) {
				if ($execp) $execp .= ',';
				if (is_string($v)) {
					if (strncmp($v,"'",1) !== 0) $execp .= $this->qstr($v);
				} else {
					$execp .= $v;
				}
			}
			
			if ($execp) $exsql = "EXECUTE $plan ($execp)";
			else $exsql = "EXECUTE $plan";
			
			$rez = @pg_exec($this->_connectionID,$exsql);
			if (!$rez) {
			# Perhaps plan does not exist? Prepare/compile plan.
				$params = '';
				foreach($inputarr as $v) {
					if ($params) $params .= ',';
					if (is_string($v)) {
						$params .= 'VARCHAR';
					} else if (is_integer($v)) {
						$params .= 'INTEGER';
					} else {
						$params .= "REAL";
					}
				}
				$sqlarr = explode('?',$sql);
				//print_r($sqlarr);
				$sql = '';
				$i = 1;
				foreach($sqlarr as $v) {
					$sql .= $v.' $'.$i;
					$i++;
				}
				$s = "PREPARE $plan ($params) AS ".substr($sql,0,strlen($sql)-2);		
				//adodb_pr($s);
				pg_exec($this->_connectionID,$s);
				echo $this->ErrorMsg();
			}
			
			$rez = pg_exec($this->_connectionID,$exsql);
		} else {
			$this->_errorMsg = false;
			//adodb_backtrace();
			$rez = pg_exec($this->_connectionID,$sql);
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
	

	/*	Returns: the last error message from previous database operation	*/	
	function ErrorMsg() 
	{
		if ($this->_errorMsg !== false) return $this->_errorMsg;
		if (ADODB_PHPVER >= 0x4300) {
			if (!empty($this->_resultid)) {
				$this->_errorMsg = @pg_result_error($this->_resultid);
				if ($this->_errorMsg) return $this->_errorMsg;
			}
			
			if (!empty($this->_connectionID)) {
				$this->_errorMsg = @pg_last_error($this->_connectionID);
			} else $this->_errorMsg = @pg_last_error();
		} else {
			if (empty($this->_connectionID)) $this->_errorMsg = @pg_errormessage();
			else $this->_errorMsg = @pg_errormessage($this->_connectionID);
		}
		return $this->_errorMsg;
	}
	
	function ErrorNo()
	{
		$e = $this->ErrorMsg();
		return strlen($e) ? $e : 0;
	}

	// returns true or false
	function _close()
	{
		if ($this->transCnt) $this->RollbackTrans();
		if ($this->_resultid) {
			@pg_freeresult($this->_resultid);
			$this->_resultid = false;
		}
		@pg_close($this->_connectionID);
		$this->_connectionID = false;
		return true;
	}
	
	
	/*
	* Maximum size of C field
	*/
	function CharMax()
	{
		return 1000000000;  // should be 1 Gb?
	}
	
	/*
	* Maximum size of X field
	*/
	function TextMax()
	{
		return 1000000000; // should be 1 Gb?
	}
	
		
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_postgres64 extends ADORecordSet{
	var $_blobArr;
	var $databaseType = "postgres64";
	var $canSeek = true;
	function ADORecordSet_postgres64($queryID,$mode=false) 
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch ($mode)
		{
		case ADODB_FETCH_NUM: $this->fetchMode = PGSQL_NUM; break;
		case ADODB_FETCH_ASSOC:$this->fetchMode = PGSQL_ASSOC; break;
		default:
		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH:$this->fetchMode = PGSQL_BOTH; break;
		}
		$this->ADORecordSet($queryID);
	}
	
	function &GetRowAssoc($upper=true)
	{
		if ($this->fetchMode == PGSQL_ASSOC && !$upper) return $this->fields;
		$row =& ADORecordSet::GetRowAssoc($upper);
		return $row;
	}

	function _initrs()
	{
	global $ADODB_COUNTRECS;
		$this->_numOfRows = ($ADODB_COUNTRECS)? @pg_numrows($this->_queryID):-1;
		$this->_numOfFields = @pg_numfields($this->_queryID);
		
		// cache types for blob decode check
		for ($i=0, $max = $this->_numOfFields; $i < $max; $i++) { 
			$f1 = $this->FetchField($i);
			//print_r($f1);
			if ($f1->type == 'bytea') $this->_blobArr[$i] = $f1->name;
		}		
	}

		/* Use associative array to get fields array */
	function Fields($colname)
	{
		if ($this->fetchMode != PGSQL_NUM) return @$this->fields[$colname];
		
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		 return $this->fields[$this->bind[strtoupper($colname)]];
	}

	function &FetchField($fieldOffset = 0) 
	{
		$off=$fieldOffset; // offsets begin at 0
		
		$o= new ADOFieldObject();
		$o->name = @pg_fieldname($this->_queryID,$off);
		$o->type = @pg_fieldtype($this->_queryID,$off);
		$o->max_length = @pg_fieldsize($this->_queryID,$off);
		//print_r($o);		
		//print "off=$off name=$o->name type=$o->type len=$o->max_length<br>";
		return $o;	
	}

	function _seek($row)
	{
		return @pg_fetch_row($this->_queryID,$row);
	}
	
	function _decode($blob)
	{
		eval('$realblob="'.adodb_str_replace(array('"','$'),array('\"','\$'),$blob).'";');
		return $realblob;	
	}
	
	function _fixblobs()
	{
		if ($this->fetchMode == PGSQL_NUM || $this->fetchMode == PGSQL_BOTH) {
			foreach($this->_blobArr as $k => $v) {
				$this->fields[$k] = ADORecordSet_postgres64::_decode($this->fields[$k]);
			}
		}
		if ($this->fetchMode == PGSQL_ASSOC || $this->fetchMode == PGSQL_BOTH) {
			foreach($this->_blobArr as $k => $v) {
				$this->fields[$v] = ADORecordSet_postgres64::_decode($this->fields[$v]);
			}
		}
	}
	
	// 10% speedup to move MoveNext to child class
	function MoveNext() 
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_numOfRows < 0 || $this->_numOfRows > $this->_currentRow) {
				$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
				if (is_array($this->fields) && $this->fields) {
					if ($this->fields && isset($this->_blobArr)) $this->_fixblobs();
					return true;
				}
			}
			$this->fields = false;
			$this->EOF = true;
		}
		return false;
	}		
	
	function _fetch()
	{
				
		if ($this->_currentRow >= $this->_numOfRows && $this->_numOfRows >= 0)
        	return false;

		$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
		
	if ($this->fields && isset($this->_blobArr)) $this->_fixblobs();
			
		return (is_array($this->fields));
	}

	function _close() 
	{ 
		return @pg_freeresult($this->_queryID);
	}

	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		switch (strtoupper($t)) {
				case 'MONEY': // stupid, postgres expects money to be a string
				case 'INTERVAL':
				case 'CHAR':
				case 'CHARACTER':
				case 'VARCHAR':
				case 'NAME':
		   		case 'BPCHAR':
				case '_VARCHAR':
					if ($len <= $this->blobSize) return 'C';
				
				case 'TEXT':
					return 'X';
		
				case 'IMAGE': // user defined type
				case 'BLOB': // user defined type
				case 'BIT':	// This is a bit string, not a single bit, so don't return 'L'
				case 'VARBIT':
				case 'BYTEA':
					return 'B';
				
				case 'BOOL':
				case 'BOOLEAN':
					return 'L';
				
				case 'DATE':
					return 'D';
				
				case 'TIME':
				case 'DATETIME':
				case 'TIMESTAMP':
				case 'TIMESTAMPTZ':
					return 'T';
				
				case 'SMALLINT': 
				case 'BIGINT': 
				case 'INTEGER': 
				case 'INT8': 
				case 'INT4':
				case 'INT2':
					if (isset($fieldobj) &&
				empty($fieldobj->primary_key) && empty($fieldobj->unique)) return 'I';
				
				case 'OID':
				case 'SERIAL':
					return 'R';
				
				 default:
				 	return 'N';
			}
	}

}
?>
