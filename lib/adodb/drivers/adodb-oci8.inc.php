<?php
/*
V2.00 13 May 2002 (c) 2000-2002 John Lim. All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.

  Latest version is available at http://php.weblogs.com/
  
  Code contributed by George Fourlanos <fou@infomap.gr>
  
  13 Nov 2000 jlim - removed all ora_* references.
*/

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
class ADODB_oci8 extends ADOConnection {
    var $databaseType = 'oci8';
	var $dataProvider = 'oci8';
    var $replaceQuote = "''"; // string to use to replace quotes
    var $concat_operator='||';
	var $sysDate = "TO_DATE(TO_CHAR(SYSDATE,'YYYY-MM-DD'),'YYYY-MM-DD')";
	var $sysTimeStamp = 'SYSDATE';
	
	var $_stmt;
	var $_commit = OCI_COMMIT_ON_SUCCESS;
	var $_initdate = true; // init date to YYYY-MM-DD
	var $metaTablesSQL = "select table_name from cat where table_type in ('TABLE','VIEW')";
	var $metaColumnsSQL = "select cname,coltype,width from col where tname='%s' order by colno";
	var $_bindInputArray = true;
	var $hasGenID = true;
	var $_genIDSQL = "SELECT %s.nextval FROM DUAL";
	var $_genSeqSQL = "CREATE SEQUENCE %s START WITH %s";
	var $hasAffectedRows = true;
	var $upperCase = 'upper';
	var $noNullStrings = false;
	var $connectSID = false;
	var $_bind = false;
	var $_hasOCIFetchStatement = false;
	var $_getarray = false; // currently not working
	
    function ADODB_oci8() 
	{
		$this->_hasOCIFetchStatement = (strnatcmp(PHP_VERSION,'4.2.0')>=0);;
    }
	
	function Affected_Rows()
	{
		return OCIRowCount($this->_stmt);
	}
	
	// format and return date string in database date format
	function DBDate($d)
	{
		if (empty($d) && $d !== 0) return 'null';
		
		if (is_string($d)) $d = ADORecordSet::UnixDate($d);
		return 'TO_DATE('.date($this->fmtDate,$d).",'YYYY-MM-DD')";
	}
	
	// format and return date string in database timestamp format
	function DBTimeStamp($ts)
	{
		if (empty($ts) && $ts !== 0) return 'null';
		if (is_string($ts)) $ts = ADORecordSet::UnixTimeStamp($ts);
		return 'TO_DATE('.date($this->fmtTimeStamp,$ts).",'RRRR-MM-DD, HH:MI:SS AM')";
	}
	
	function RowLock($tables,$where) 
	{
		if ($this->autoCommit) $this->BeginTrans();
		return $this->GetOne("select 1 as ignore from $tables where $where for update");
	}
	
    function BeginTrans()
	{      
         $this->autoCommit = false;
         $this->_commit = OCI_DEFAULT;
         return true;
	}
	
	function CommitTrans($ok=true) 
	{ 
		if (!$ok) return $this->RollbackTrans();
        $ret = OCIcommit($this->_connectionID);
	    $this->_commit = OCI_COMMIT_ON_SUCCESS;
	    $this->autoCommit = true;
	    return $ret;
	}
	
	function RollbackTrans()
	{
        $ret = OCIrollback($this->_connectionID);
		$this->_commit = OCI_COMMIT_ON_SUCCESS;
	    $this->autoCommit = true;
		return $ret;
	}
	
	
    function SelectDB($dbName) 
	{
        return false;
    }

	/* there seems to be a bug in the oracle extension -- always returns ORA-00000 - no error */
    function ErrorMsg() 
	{
		$arr = @OCIerror($this->_stmt);
		if ($arr === false) {
			$arr = @OCIerror($this->_connectionID);
			if ($arr === false) $arr = @OCIError();
			if ($arr === false) return '';
		}
           $this->_errorMsg = $arr['message'];
           return $this->_errorMsg;
    }

	function ErrorNo() 
	{
		if (is_resource($this->_stmt))
			$arr = @ocierror($this->_stmt);
		else {
			$arr = @ocierror($this->_connectionID);
			if ($arr === false) $arr = @ocierror();
			if ($arr == false) return '';
		}
        return $arr['code'];
    }
	
	
/*
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
*/
    // returns true or false
    function _connect($argHostname, $argUsername, $argPassword, $argDatabasename,$persist=false)
    {
		         
    	if($argHostname) { // added by Jorma Tuomainen <jorma.tuomainen@ppoy.fi>
        	if(strpos($argHostname,":")) {
				$argHostinfo=explode(":",$argHostname);
               	$argHostname=$argHostinfo[0];
                $argHostport=$argHostinfo[1];
         	} else {
    			$argHostport="1521";
   			}
			
			if ($this->connectSID) {
				$argDatabasename="(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$argHostname
				.")(PORT=$argHostport))(CONNECT_DATA=(SID=$argDatabasename)))";
			} else
				$argDatabasename="(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$argHostname
				.")(PORT=$argHostport))(CONNECT_DATA=(SERVICE_NAME=$argDatabasename)))";
    	}
				
 		//if ($argHostname) print "<p>Connect: 1st argument should be left blank for $this->databaseType</p>";
       if ($persist)$this->_connectionID = OCIPLogon($argUsername,$argPassword, $argDatabasename);
	   else $this->_connectionID = OCILogon($argUsername,$argPassword, $argDatabasename);
	   
        if ($this->_connectionID === false) return false;
		if ($this->_initdate) {
			$this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");
		}
		
		//print OCIServerVersion($this->_connectionID);
        return true;
   	}
        // returns true or false
    function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
        return $this->_connect($argHostname, $argUsername, $argPassword, $argDatabasename,true);
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
	function &SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$arg3=false,$secs2cache=0)
	{
		// seems that oracle only supports 1 hint comment in 8i
		if (strpos($sql,'/*+') !== false)
			$sql = str_replace('/*+ ','/*+FIRST_ROWS ',$sql);
		else
			$sql = preg_replace('/^[ \t\n]*select/i','SELECT /*+FIRST_ROWS*/',$sql);
			
		if ($offset < 100) {
			if ($nrows > 0) {	
				if ($offset > 0) $nrows += $offset;
				//$inputarr['adodb_rownum'] = $nrows;
				$sql = "select * from ($sql) where rownum <= $nrows";
				$nrows = -1;
			}
			// note that $nrows = 0 still has to work ==> no rows returned

			return ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$arg3,$secs2cache);
		} else {
			 // Algorithm by Tomas V V Cox, from PEAR DB oci8.php
	        
	         // Let Oracle return the name of the columns
	         $q_fields = "SELECT * FROM ($sql) WHERE NULL = NULL";
	         if (!$result = OCIParse($this->_connectionID, $q_fields)) {
	             return false;
	         }
	         if (!$success = OCIExecute($result, OCI_DEFAULT)) {
	             return false;
	         }
	         $ncols = OCINumCols($result);
	         for ( $i = 1; $i <= $ncols; $i++ ) {
	             $cols[] = OCIColumnName($result, $i);
	         }
			 $result = false;
			 
	         $fields = implode(',', $cols);
	         $nrows += $offset;
			 $offset += 1; // in Oracle rownum starts at 1
			 
	         $sql = "SELECT $fields FROM".
	                  "(SELECT rownum as adodb_rownum, $fields FROM".
	                  " ($sql) WHERE rownum <= $nrows".
	                  ") WHERE adodb_rownum >= $offset";
		
			if ($secs2cache>0) return $this->CacheExecute($secs2cache, $sql,$inputarr,$arg3);
			else return $this->Execute($sql,$inputarr,$arg3);
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
	*     $db->Execute('ALTER SESSION SET CURSOR_SHARING=EXACT');
	* before UpdateBlob() then...
	*/

	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB',$commit=true)
	{
		switch(strtoupper($blobtype)) {
		default: print "<b>UpdateBlob</b>: Unknown blobtype=$blobtype<br>"; return false;
		case 'BLOB': $type = OCI_B_BLOB; break;
		case 'CLOB': $type = OCI_B_CLOB; break;
		}
		
		if ($this->databaseType == 'oci8po') 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO ?";
		else 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO :blob";
		
		$desc = OCINewDescriptor($this->_connectionID, OCI_D_LOB);
		$arr['blob'] = array($desc,-1,$type);
		
		if ($commit) $this->BeginTrans();
		$rs = ADODB_oci8::Execute($sql,$arr);
		if ($rez = !empty($rs)) $desc->save($val);
		$desc->free();
		if ($commit) $this->CommitTrans();
		
		if ($rez) $rs->Close();
		return $rez;
	}
	
	/**
	* Usage:  store file pointed to by $var in a blob
	*/
	function UpdateBlobFile($table,$column,$val,$where,$blobtype='BLOB')
	{
		switch(strtoupper($blobtype)) {
		default: print "<b>UpdateBlob</b>: Unknown blobtype=$blobtype<br>"; return false;
		case 'BLOB': $type = OCI_B_BLOB; break;
		case 'CLOB': $type = OCI_B_CLOB; break;
		}
		
		if ($this->databaseType == 'oci8po') 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO ?";
		else 
			$sql = "UPDATE $table set $column=EMPTY_{$blobtype}() WHERE $where RETURNING $column INTO :blob";
		
		$desc = OCINewDescriptor($this->_connectionID, OCI_D_LOB);
		$arr['blob'] = array($desc,-1,$type);
		
		$this->BeginTrans();
		$rs = ADODB_oci8::Execute($sql,$arr);
		if ($rez = !empty($rs)) $desc->savefile($val);
		$desc->free();
		$this->CommitTrans();
		
		if ($rez) $rs->Close();
		return $rez;
	}
	
	/*
		Example of usage:
		
		$stmt = $this->Prepare('insert into emp (empno, ename) values (:empno, :ename)');
	*/
	function Prepare($sql)
	{
	static $BINDNUM = 0;
	
		$stmt = OCIParse($this->_connectionID,$sql);
		if (!$stmt) return $sql; // error in statement, let Execute() handle the error
		
		$BINDNUM += 1;
		return array($sql,$stmt,0,$BINDNUM);
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
			Time 0.6081s (1644.60 inserts/sec) with direct OCIParse/OCIExecute
			Time 0.6341s (1577.16 inserts/sec) with ADOdb Prepare/Bind/Execute
			Time 1.5533s ( 643.77 inserts/sec) with pure SQL using Execute
			
		Now if PHP only had batch/bulk updating like Java or PL/SQL...
	
		Note that the order of parameters differs from OCIBindByName,
		because we default the names to :0, :1, :2
	*/
	function Bind(&$stmt,&$var,$size=4000,$type=false,$name=false)
	{
		if (!is_array($stmt)) return false;
		if ($name == false) {
			if ($type !== false) $rez = OCIBindByName($stmt[1],":".$name,$var,$size,$type);
			else $rez = OCIBindByName($stmt[1],":".$stmt[2],$var,$size); // +1 byte for null terminator
			$stmt[2] += 1;
		} else {
			if ($type !== false) $rez = OCIBindByName($stmt[1],":".$name,$var,$size,$type);
			else $rez = OCIBindByName($stmt[1],":".$name,$var,$size); // +1 byte for null terminator
		}
		
		return $rez;
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
		
		See OCIBindByName documentation at php.net.
	*/
	function Parameter(&$stmt,&$var,$name,$isOutput=false,$maxLen=4000,$type=false)
	{
			if  ($this->debug) {
				print "Parameter(\$stmt, \$php_var='$var', \$name='$name');<br>\n";
			}
			return $this->Bind($stmt,$var,$maxLen,$type,$name);
	}
	
	/*
	returns query ID if successful, otherwise false
	this version supports:
	
	   1. $db->execute('select * from table');
	   
	   2. $db->prepare('insert into table (a,b,c) values (:0,:1,:2)');
	      $db->execute($prepared_statement, array(1,2,3));
		  
	   3. $db->execute('insert into table (a,b,c) values (:a,:b,:c)',array('a'=>1,'b'=>2,'c'=>3));
	   
	   4. $db->prepare('insert into table (a,b,c) values (:0,:1,:2)');
	      $db->$bind($stmt,1); $db->bind($stmt,2); $db->bind($stmt,3); 
		  $db->execute($stmt);
	*/ 
	function _query($sql,$inputarr)
	{
		if (is_array($sql)) { // is prepared sql
			$stmt = $sql[1];
			
			// we try to bind to permanent array, so that OCIBindByName is persistent
			// and carried out once only - note that max array element size is 4000 chars
			if (is_array($inputarr)) {
				$bindpos = $sql[3];
				if (isset($this->_bind[$bindpos])) {
				// all tied up already
					$bindarr = &$this->_bind[$bindpos];
				} else {
				// one statement to bind them all
					$bindarr = array();
					foreach($inputarr as $k => $v) {
						$bindarr[$k] = $v;
						OCIBindByName($stmt,":$k",$bindarr[$k],4000);
					}
					$this->_bind[$bindpos] = &$bindarr;
				}
			}
		} else
			$stmt=@OCIParse($this->_connectionID,$sql);
		
		$this->_stmt = $stmt;
		if (!$stmt) return false;
	
		if (defined('ADODB_PREFETCH_ROWS')) @OCISetPrefetch($stmt,ADODB_PREFETCH_ROWS);
			
		if (is_array($inputarr)) {
			foreach($inputarr as $k => $v) {
				if (is_array($v)) {
					if (sizeof($v) == 2) // suggested by g.giunta@libero.
						OCIBindByName($stmt,":$k",$inputarr[$k][0],$v[1]);
					else
						OCIBindByName($stmt,":$k",$inputarr[$k][0],$v[1],$v[2]);
				} else {
					$len = -1;
					if ($v === ' ') $len = 1;
					if (isset($bindarr)) {	// is prepared sql, so no need to ocibindbyname again
						$bindarr[$k] = $v;
					} else { 				// dynamic sql, so rebind every time
						OCIBindByName($stmt,":$k",$inputarr[$k],$len);
					}
				}
			}
		}
		
		if (OCIExecute($stmt,$this->_commit)) {
		   /* Now this could be an Update/Insert or Delete */
			if (@OCIStatementType($stmt) != 'SELECT') return true;
			return $stmt;
		}
	    return false;
	}
	
	// returns true or false
	function _close()
	{
		if (!$this->autoCommit) OCIRollback($this->_connectionID);
		OCILogoff($this->_connectionID);
		$this->_stmt = false;
		$this->_connectionID = false;
	}

	function MetaPrimaryKeys($table)
	{
	// tested with oracle 8.1.7
		$table = strtoupper($table);
		$sql = "SELECT /*+ RULE */ distinct b.column_name
   FROM ALL_CONSTRAINTS a
      , ALL_CONS_COLUMNS b
  WHERE ( UPPER(b.table_name) = ('$table'))
    AND (UPPER(a.table_name) = ('$table') and a.constraint_type = 'P')
    AND (a.constraint_name = b.constraint_name)";
 		$rs = $this->Execute($sql);
		if ($rs && !$rs->EOF) {
			$arr = $rs->GetArray();
			$a = array();
			foreach($arr as $v) {
				$a[] = $v[0];
			}
			return $a;
		}
		else return false;
	}
	

 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'X': return 'VARCHAR(4000)';
		
		case 'C2': return 'NVARCHAR';
		case 'X2': return 'NVARCHAR(4000)';
		
		case 'B': return 'BLOB';
			
		case 'D': 
		case 'T': return 'DATE';
		case 'L': return 'NUMBER(1)';
		case 'R': return false;
		case 'I': return 'NUMBER(16)';  // enough for 9 petabytes!
		
		case 'F': return 'NUMBER';
		case 'N': return 'NUMBER';
		default:
			return false;
		}
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
	$nofixquotes=false;
	
	
		if ($this->noNullStrings && $s === '')$s = ' ';
		if (!$magic_quotes) {	
			if ($this->replaceQuote[0] == '\\'){
				$s = str_replace('\\','\\\\',$s);
			}
			return  "'".str_replace("'",$this->replaceQuote,$s)."'";
		}
		
		// undo magic quotes for "
		$s = str_replace('\\"','"',$s);
		
		if ($this->replaceQuote == "\\'")  // ' already quoted, no need to change anything
			return "'$s'";
		else {// change \' to '' for sybase/mssql
			$s = str_replace('\\\\','\\',$s);
			return "'".str_replace("\\'",$this->replaceQuote,$s)."'";
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
		
    function ADORecordset_oci8($queryID)
    {
	global $ADODB_FETCH_MODE;

		switch ($ADODB_FETCH_MODE)
		{
		default:
		case ADODB_FETCH_NUM: $this->fetchMode = OCI_NUM+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		case ADODB_FETCH_ASSOC:$this->fetchMode = OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH:$this->fetchMode = OCI_NUM+OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS; break;
		}
		$this->_queryID = $queryID;
    }


	function Init()
	{
		if ($this->_inited) return;
		
		$this->_inited = true;
		$this->fields = array();
		
		if ($this->_queryID) {
		/*
			if ($this->connection->_getarray) { 
				if ($this->connection->_hasOCIFetchStatement) {
					$arr = array();
					if (OCIFetchStatement($this->_queryID,$arr,0,(integer)-1,OCI_FETCHSTATEMENT_BY_ROW|$this->fetchMode)) {
						$this->_arr = $arr;
					}
					$this->EOF = false;
				}
			} else */
			{		
				$this->EOF = !$this->_fetch(); 			
				$this->_currentRow = 0;
			}
		
			@$this->_initrs();
		
		} else {
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
			$this->EOF = true;
		}
	}
	
	function _initrs()
    {
        $this->_numOfRows = -1;
        $this->_numOfFields = OCInumcols($this->_queryID);
		if ($this->_numOfFields>0) {
			$this->_fieldobjs = array();
			$max = $this->_numOfFields;
			for ($i=0;$i<$max; $i++) $this->_fieldobjs[] = $this->_FetchField($i);
		}
    }

      /*        Returns: an object containing field information.
              Get column information in the Recordset object. fetchField() can be used in order to obtain information about
              fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
              fetchField() is retrieved.        */

	function &_FetchField($fieldOffset = -1)
	{
		$fld = new ADOFieldObject;
		$fieldOffset += 1;
        $fld->name =OCIcolumnname($this->_queryID, $fieldOffset);
        $fld->type = OCIcolumntype($this->_queryID, $fieldOffset);
        $fld->max_length = OCIcolumnsize($this->_queryID, $fieldOffset);
	 	if ($fld->type == 'NUMBER') {
	 		//$p = OCIColumnPrecision($this->_queryID, $fieldOffset);
			$sc = OCIColumnScale($this->_queryID, $fieldOffset);
			if ($sc == 0) $fld->type = 'INT';
	 	}
        return $fld;
	}
	
	/* For some reason, OCIcolumnname fails when called after _initrs() so we cache it */
	function &FetchField($fieldOffset = -1)
	{
		return $this->_fieldobjs[$fieldOffset];
	}
	
	/**
	 * return recordset as a 2-dimensional array.
	 *
	 * @param [nRows]  is the number of rows to return. -1 means every row.
	 *
	 * @return an array indexed by the rows (0-based) from the recordset
	 */
	function GetArray($nRows = -1) 
	{
	//	if ($this->_arr) return $this->_arr;
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nRows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}
		return $results;
	}
	
	// 10% speedup to move MoveNext to child class
	function MoveNext() 
	{
		if (!$this->EOF) {		
			$this->_currentRow++;
			if(@OCIfetchinto($this->_queryID,$this->fields,$this->fetchMode))
				return true;
			
			$this->EOF = true;
		}
		return false;
	}	
	
	/* Optimize SelectLimit() by using OCIFetch() instead of OCIFetchInto() */
	function GetArrayLimit($nrows,$offset=-1) 
	{
		if ($offset <= 0) return $this->GetArray($nrows);
		for ($i=1; $i < $offset; $i++) 
			if (!@OCIFetch($this->_queryID)) return array();
			
		if (!@OCIfetchinto($this->_queryID,$this->fields,$this->fetchMode)) return array();
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
		return @OCIfetchinto($this->_queryID,$this->fields,$this->fetchMode);
    }

    /*        close() only needs to be called if you are worried about using too much memory while your script
            is running. All associated result memory for the specified result identifier will automatically be freed.        */

    function _close() 
	{
    	OCIFreeStatement($this->_queryID);
 		$this->_queryID = false;
    }

    function MetaType($t,$len=-1)
    {
		switch (strtoupper($t)) {
     	case 'VARCHAR':
     	case 'VARCHAR2':
		case 'CHAR':
		case 'VARBINARY':
		case 'BINARY':
		case 'NCHAR':
		case 'NVARCHAR':
		         if ($len <= $this->blobSize) return 'C';
		
		case 'NCLOB':
		     case 'LONG':
		case 'LONG VARCHAR':
		case 'CLOB';
		return 'X';
		
		case 'LONG RAW':
		case 'LONG VARBINARY':
		case 'BLOB':
			return 'B';
		
		case 'DATE': 
			return 'D';
		
		     //case 'T': return 'T';
		
		case 'INT': 
		case 'SMALLINT':
		case 'INTEGER': 
			return 'I';
			
        default: return 'N';
        }
    }
}
?>
