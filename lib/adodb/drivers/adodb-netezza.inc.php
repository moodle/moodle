<?php
/*
  @version   v5.20.7  20-Sep-2016
  @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
  @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community

  First cut at the Netezza Driver by Josh Eldridge joshuae74#hotmail.com
 Based on the previous postgres drivers.
 http://www.netezza.com/
 Major Additions/Changes:
    MetaDatabasesSQL, MetaTablesSQL, MetaColumnsSQL
    Note: You have to have admin privileges to access the system tables
    Removed non-working keys code (Netezza has no concept of keys)
    Fixed the way data types and lengths are returned in MetaColumns()
    as well as added the default lengths for certain types
    Updated public variables for Netezza
    Still need to remove blob functions, as Netezza doesn't suppport blob
*/
// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR.'/drivers/adodb-postgres64.inc.php');

class ADODB_netezza extends ADODB_postgres64 {
    var $databaseType = 'netezza';
	var $dataProvider = 'netezza';
	var $hasInsertID = false;
	var $_resultid = false;
  	var $concat_operator='||';
  	var $random = 'random';
	var $metaDatabasesSQL = "select objname from _v_object_data where objtype='database' order by 1";
    var $metaTablesSQL = "select objname from _v_object_data where objtype='table' order by 1";
	var $isoDates = true; // accepts dates in ISO format
	var $sysDate = "CURRENT_DATE";
	var $sysTimeStamp = "CURRENT_TIMESTAMP";
	var $blobEncodeType = 'C';
	var $metaColumnsSQL = "SELECT attname, atttype FROM _v_relation_column_def WHERE name = '%s' AND attnum > 0 ORDER BY attnum";
	var $metaColumnsSQL1 = "SELECT attname, atttype FROM _v_relation_column_def WHERE name = '%s' AND attnum > 0 ORDER BY attnum";
	// netezza doesn't have keys. it does have distributions, so maybe this is
	// something that can be pulled from the system tables
	var $metaKeySQL = "";
	var $hasAffectedRows = true;
	var $hasLimit = true;
	var $true = 't';		// string that represents TRUE for a database
	var $false = 'f';		// string that represents FALSE for a database
	var $fmtDate = "'Y-m-d'";	// used by DBDate() as the default date format used by the database
	var $fmtTimeStamp = "'Y-m-d G:i:s'"; // used by DBTimeStamp as the default timestamp fmt.
	var $ansiOuter = true;
	var $autoRollback = true; // apparently pgsql does not autorollback properly before 4.3.4
							// http://bugs.php.net/bug.php?id=25404


	function __construct()
	{

	}

	function MetaColumns($table,$upper=true)
	{

	// Changed this function to support Netezza which has no concept of keys
	// could posisbly work on other things from the system table later.

	global $ADODB_FETCH_MODE;

		$table = strtolower($table);

		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);

		$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table,$table));
		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;

		if ($rs === false) return false;

		$retarr = array();
		while (!$rs->EOF) {
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];

			// since we're returning type and length as one string,
			// split them out here.

			if ($first = strstr($rs->fields[1], "(")) {
			 $fld->max_length = trim($first, "()");
			} else {
			 $fld->max_length = -1;
			}

			if ($first = strpos($rs->fields[1], "(")) {
			 $fld->type = substr($rs->fields[1], 0, $first);
			} else {
			 $fld->type = $rs->fields[1];
			}

			switch ($fld->type) {
			 case "byteint":
			 case "boolean":
			 $fld->max_length = 1;
			 break;
			 case "smallint":
			 $fld->max_length = 2;
			 break;
			 case "integer":
			 case "numeric":
			 case "date":
			 $fld->max_length = 4;
			 break;
			 case "bigint":
			 case "time":
			 case "timestamp":
			 $fld->max_length = 8;
			 break;
			 case "timetz":
			 case "time with time zone":
			 $fld->max_length = 12;
			 break;
			}

			if ($ADODB_FETCH_MODE == ADODB_FETCH_NUM) $retarr[] = $fld;
			else $retarr[($upper) ? strtoupper($fld->name) : $fld->name] = $fld;

			$rs->MoveNext();
		}
		$rs->Close();
		return $retarr;

	}


}

/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_netezza extends ADORecordSet_postgres64
{
	var $databaseType = "netezza";
	var $canSeek = true;

	function __construct($queryID,$mode=false)
	{
		parent::__construct($queryID,$mode);
	}

	// _initrs modified to disable blob handling
	function _initrs()
	{
	global $ADODB_COUNTRECS;
		$this->_numOfRows = ($ADODB_COUNTRECS)? @pg_num_rows($this->_queryID):-1;
		$this->_numOfFields = @pg_num_fields($this->_queryID);
	}

}
