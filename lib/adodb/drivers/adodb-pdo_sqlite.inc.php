<?php

/*
 @version   v5.20.7  20-Sep-2016
 @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
 @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence. See License.txt.
  Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.sourceforge.net

  Thanks Diogo Toscano (diogo#scriptcase.net) for the code.
	And also Sid Dunayer [sdunayer#interserv.com] for extensive fixes.
*/

class ADODB_pdo_sqlite extends ADODB_pdo {
	var $metaTablesSQL   = "SELECT name FROM sqlite_master WHERE type='table'";
	var $sysDate         = 'current_date';
	var $sysTimeStamp    = 'current_timestamp';
	var $nameQuote       = '`';
	var $replaceQuote    = "''";
	var $hasGenID        = true;
	var $_genIDSQL       = "UPDATE %s SET id=id+1 WHERE id=%s";
	var $_genSeqSQL      = "CREATE TABLE %s (id integer)";
	var $_genSeqCountSQL = 'SELECT COUNT(*) FROM %s';
	var $_genSeq2SQL     = 'INSERT INTO %s VALUES(%s)';
	var $_dropSeqSQL     = 'DROP TABLE %s';
	var $concat_operator = '||';
    var $pdoDriver       = false;
	var $random='abs(random())';

	function _init($parentDriver)
	{
		$this->pdoDriver = $parentDriver;
		$parentDriver->_bindInputArray = true;
		$parentDriver->hasTransactions = false; // // should be set to false because of PDO SQLite driver not supporting changing autocommit mode
		$parentDriver->hasInsertID = true;
	}

	function ServerInfo()
	{
		$parent = $this->pdoDriver;
		@($ver = array_pop($parent->GetCol("SELECT sqlite_version()")));
		@($enc = array_pop($parent->GetCol("PRAGMA encoding")));

		$arr['version']     = $ver;
		$arr['description'] = 'SQLite ';
		$arr['encoding']    = $enc;

		return $arr;
	}

	function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0)
	{
		$parent = $this->pdoDriver;
		$offsetStr = ($offset >= 0) ? " OFFSET $offset" : '';
		$limitStr  = ($nrows >= 0)  ? " LIMIT $nrows" : ($offset >= 0 ? ' LIMIT 999999999' : '');
	  	if ($secs2cache)
	   		$rs = $parent->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr);
	  	else
	   		$rs = $parent->Execute($sql."$limitStr$offsetStr",$inputarr);

		return $rs;
	}

	function GenID($seq='adodbseq',$start=1)
	{
		$parent = $this->pdoDriver;
		// if you have to modify the parameter below, your database is overloaded,
		// or you need to implement generation of id's yourself!
		$MAXLOOPS = 100;
		while (--$MAXLOOPS>=0) {
			@($num = array_pop($parent->GetCol("SELECT id FROM {$seq}")));
			if ($num === false || !is_numeric($num)) {
				@$parent->Execute(sprintf($this->_genSeqSQL ,$seq));
				$start -= 1;
				$num = '0';
				$cnt = $parent->GetOne(sprintf($this->_genSeqCountSQL,$seq));
				if (!$cnt) {
					$ok = $parent->Execute(sprintf($this->_genSeq2SQL,$seq,$start));
				}
				if (!$ok) return false;
			}
			$parent->Execute(sprintf($this->_genIDSQL,$seq,$num));

			if ($parent->affected_rows() > 0) {
                	        $num += 1;
                		$parent->genID = intval($num);
                		return intval($num);
			}
		}
		if ($fn = $parent->raiseErrorFn) {
			$fn($parent->databaseType,'GENID',-32000,"Unable to generate unique id after $MAXLOOPS attempts",$seq,$num);
		}
		return false;
	}

	function CreateSequence($seqname='adodbseq',$start=1)
	{
		$parent = $this->pdoDriver;
		$ok = $parent->Execute(sprintf($this->_genSeqSQL,$seqname));
		if (!$ok) return false;
		$start -= 1;
		return $parent->Execute("insert into $seqname values($start)");
	}

	function SetTransactionMode($transaction_mode)
	{
		$parent = $this->pdoDriver;
		$parent->_transmode = strtoupper($transaction_mode);
	}

	function BeginTrans()
	{
		$parent = $this->pdoDriver;
		if ($parent->transOff) return true;
		$parent->transCnt += 1;
		$parent->_autocommit = false;
		return $parent->Execute("BEGIN {$parent->_transmode}");
	}

	function CommitTrans($ok=true)
	{
		$parent = $this->pdoDriver;
		if ($parent->transOff) return true;
		if (!$ok) return $parent->RollbackTrans();
		if ($parent->transCnt) $parent->transCnt -= 1;
		$parent->_autocommit = true;

		$ret = $parent->Execute('COMMIT');
		return $ret;
	}

	function RollbackTrans()
	{
		$parent = $this->pdoDriver;
		if ($parent->transOff) return true;
		if ($parent->transCnt) $parent->transCnt -= 1;
		$parent->_autocommit = true;

		$ret = $parent->Execute('ROLLBACK');
		return $ret;
	}


    // mark newnham
	function MetaColumns($tab,$normalize=true)
	{
	  global $ADODB_FETCH_MODE;

	  $parent = $this->pdoDriver;
	  $false = false;
	  $save = $ADODB_FETCH_MODE;
	  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	  if ($parent->fetchMode !== false) $savem = $parent->SetFetchMode(false);
	  $rs = $parent->Execute("PRAGMA table_info('$tab')");
	  if (isset($savem)) $parent->SetFetchMode($savem);
	  if (!$rs) {
	    $ADODB_FETCH_MODE = $save;
	    return $false;
	  }
	  $arr = array();
	  while ($r = $rs->FetchRow()) {
	    $type = explode('(',$r['type']);
	    $size = '';
	    if (sizeof($type)==2)
	    $size = trim($type[1],')');
	    $fn = strtoupper($r['name']);
	    $fld = new ADOFieldObject;
	    $fld->name = $r['name'];
	    $fld->type = $type[0];
	    $fld->max_length = $size;
	    $fld->not_null = $r['notnull'];
	    $fld->primary_key = $r['pk'];
	    $fld->default_value = $r['dflt_value'];
	    $fld->scale = 0;
	    if ($save == ADODB_FETCH_NUM) $arr[] = $fld;
	    else $arr[strtoupper($fld->name)] = $fld;
	  }
	  $rs->Close();
	  $ADODB_FETCH_MODE = $save;
	  return $arr;
	}

	function MetaTables($ttype=false,$showSchema=false,$mask=false)
	{
		$parent = $this->pdoDriver;

		if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr(strtoupper($mask));
			$this->metaTablesSQL .= " AND name LIKE $mask";
		}

		$ret = $parent->GetCol($this->metaTablesSQL);

		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
   }
}
