<?php


/*
@version   v5.21.0  2021-02-27
@copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 8.

*/

class ADODB_pdo_oci extends ADODB_pdo_base {

	var $concat_operator='||';
	var $sysDate = "TRUNC(SYSDATE)";
	var $sysTimeStamp = 'SYSDATE';
	var $NLS_DATE_FORMAT = 'YYYY-MM-DD';  // To include time, use 'RRRR-MM-DD HH24:MI:SS'
	var $random = "abs(mod(DBMS_RANDOM.RANDOM,10000001)/10000000)";
	var $metaTablesSQL = "select table_name,table_type from cat where table_type in ('TABLE','VIEW')";
	var $metaColumnsSQL = "select cname,coltype,width, SCALE, PRECISION, NULLS, DEFAULTVAL from col where tname='%s' order by colno";

 	var $_initdate = true;
	var $_hasdual = true;

	function _init($parentDriver)
	{
		$parentDriver->_bindInputArray = true;
		$parentDriver->_nestedSQL = true;
		if ($this->_initdate) {
			$parentDriver->Execute("ALTER SESSION SET NLS_DATE_FORMAT='".$this->NLS_DATE_FORMAT."'");
		}
	}

	function MetaTables($ttype=false,$showSchema=false,$mask=false)
	{
		if ($mask) {
			$save = $this->metaTablesSQL;
			$mask = $this->qstr(strtoupper($mask));
			$this->metaTablesSQL .= " AND table_name like $mask";
		}
		$ret = ADOConnection::MetaTables($ttype,$showSchema);

		if ($mask) {
			$this->metaTablesSQL = $save;
		}
		return $ret;
	}

	function MetaColumns($table,$normalize=true)
	{
	global $ADODB_FETCH_MODE;

		$false = false;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);

		$rs = $this->Execute(sprintf($this->metaColumnsSQL,strtoupper($table)));

		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		if (!$rs) {
			return $false;
		}
		$retarr = array();
		while (!$rs->EOF) { //print_r($rs->fields);
			$fld = new ADOFieldObject();
	   		$fld->name = $rs->fields[0];
	   		$fld->type = $rs->fields[1];
	   		$fld->max_length = $rs->fields[2];
			$fld->scale = $rs->fields[3];
			if ($rs->fields[1] == 'NUMBER' && $rs->fields[3] == 0) {
				$fld->type ='INT';
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

    /**
     * @param bool $auto_commit
     * @return void
     */
    function SetAutoCommit($auto_commit)
    {
        $this->_connectionID->setAttribute(PDO::ATTR_AUTOCOMMIT, $auto_commit);
    }
}
