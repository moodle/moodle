<?php
/*
@version   v5.20.16  12-Jan-2020
@copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.org/

	Microsoft ADO data driver. Requires ADO. Works only on MS Windows.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

define("_ADODB_ADO_LAYER", 1 );
/*--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------*/


class ADODB_ado extends ADOConnection {
	var $databaseType = "ado";
	var $_bindInputArray = false;
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d, h:i:sA'";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $dataProvider = "ado";
	var $hasAffectedRows = true;
	var $adoParameterType = 201; // 201 = long varchar, 203=long wide varchar, 205 = long varbinary
	var $_affectedRows = false;
	var $_thisTransactions;
	var $_cursor_type = 3; // 3=adOpenStatic,0=adOpenForwardOnly,1=adOpenKeyset,2=adOpenDynamic
	var $_cursor_location = 3; // 2=adUseServer, 3 = adUseClient;
	var $_lock_type = -1;
	var $_execute_option = -1;
	var $poorAffectedRows = true;
	var $charPage;

	function __construct()
	{
		$this->_affectedRows = new VARIANT;
	}

	function ServerInfo()
	{
		if (!empty($this->_connectionID)) $desc = $this->_connectionID->provider;
		return array('description' => $desc, 'version' => '');
	}

	function _affectedrows()
	{
		if (PHP_VERSION >= 5) return $this->_affectedRows;

		return $this->_affectedRows->value;
	}

	// you can also pass a connection string like this:
	//
	// $DB->Connect('USER ID=sa;PASSWORD=pwd;SERVER=mangrove;DATABASE=ai',false,false,'SQLOLEDB');
	function _connect($argHostname, $argUsername, $argPassword, $argProvider= 'MSDASQL')
	{
		$u = 'UID';
		$p = 'PWD';

		if (!empty($this->charPage))
			$dbc = new COM('ADODB.Connection',null,$this->charPage);
		else
			$dbc = new COM('ADODB.Connection');

		if (! $dbc) return false;

		/* special support if provider is mssql or access */
		if ($argProvider=='mssql') {
			$u = 'User Id';  //User parameter name for OLEDB
			$p = 'Password';
			$argProvider = "SQLOLEDB"; // SQL Server Provider

			// not yet
			//if ($argDatabasename) $argHostname .= ";Initial Catalog=$argDatabasename";

			//use trusted conection for SQL if username not specified
			if (!$argUsername) $argHostname .= ";Trusted_Connection=Yes";
		} else if ($argProvider=='access')
			$argProvider = "Microsoft.Jet.OLEDB.4.0"; // Microsoft Jet Provider

		if ($argProvider) $dbc->Provider = $argProvider;

		if ($argUsername) $argHostname .= ";$u=$argUsername";
		if ($argPassword)$argHostname .= ";$p=$argPassword";

		if ($this->debug) ADOConnection::outp( "Host=".$argHostname."<BR>\n version=$dbc->version");
		// @ added below for php 4.0.1 and earlier
		@$dbc->Open((string) $argHostname);

		$this->_connectionID = $dbc;

		$dbc->CursorLocation = $this->_cursor_location;
		return  $dbc->State > 0;
	}

	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argProvider='MSDASQL')
	{
		return $this->_connect($argHostname,$argUsername,$argPassword,$argProvider);
	}

/*
	adSchemaCatalogs	= 1,
	adSchemaCharacterSets	= 2,
	adSchemaCollations	= 3,
	adSchemaColumns	= 4,
	adSchemaCheckConstraints	= 5,
	adSchemaConstraintColumnUsage	= 6,
	adSchemaConstraintTableUsage	= 7,
	adSchemaKeyColumnUsage	= 8,
	adSchemaReferentialContraints	= 9,
	adSchemaTableConstraints	= 10,
	adSchemaColumnsDomainUsage	= 11,
	adSchemaIndexes	= 12,
	adSchemaColumnPrivileges	= 13,
	adSchemaTablePrivileges	= 14,
	adSchemaUsagePrivileges	= 15,
	adSchemaProcedures	= 16,
	adSchemaSchemata	= 17,
	adSchemaSQLLanguages	= 18,
	adSchemaStatistics	= 19,
	adSchemaTables	= 20,
	adSchemaTranslations	= 21,
	adSchemaProviderTypes	= 22,
	adSchemaViews	= 23,
	adSchemaViewColumnUsage	= 24,
	adSchemaViewTableUsage	= 25,
	adSchemaProcedureParameters	= 26,
	adSchemaForeignKeys	= 27,
	adSchemaPrimaryKeys	= 28,
	adSchemaProcedureColumns	= 29,
	adSchemaDBInfoKeywords	= 30,
	adSchemaDBInfoLiterals	= 31,
	adSchemaCubes	= 32,
	adSchemaDimensions	= 33,
	adSchemaHierarchies	= 34,
	adSchemaLevels	= 35,
	adSchemaMeasures	= 36,
	adSchemaProperties	= 37,
	adSchemaMembers	= 38

*/

	function MetaTables($ttype = false, $showSchema = false, $mask = false)
	{
		$arr= array();
		$dbc = $this->_connectionID;

		$adors=@$dbc->OpenSchema(20);//tables
		if ($adors){
			$f = $adors->Fields(2);//table/view name
			$t = $adors->Fields(3);//table type
			while (!$adors->EOF){
				$tt=substr($t->value,0,6);
				if ($tt!='SYSTEM' && $tt !='ACCESS')
					$arr[]=$f->value;
				//print $f->value . ' ' . $t->value.'<br>';
				$adors->MoveNext();
			}
			$adors->Close();
		}

		return $arr;
	}

	function MetaColumns($table, $normalize=true)
	{
		$table = strtoupper($table);
		$arr = array();
		$dbc = $this->_connectionID;

		$adors=@$dbc->OpenSchema(4);//tables

		if ($adors){
			$t = $adors->Fields(2);//table/view name
			while (!$adors->EOF){


				if (strtoupper($t->Value) == $table) {

					$fld = new ADOFieldObject();
					$c = $adors->Fields(3);
					$fld->name = $c->Value;
					$fld->type = 'CHAR'; // cannot discover type in ADO!
					$fld->max_length = -1;
					$arr[strtoupper($fld->name)]=$fld;
				}

				$adors->MoveNext();
			}
			$adors->Close();
		}
		$false = false;
		return empty($arr) ? $false : $arr;
	}




	/* returns queryID or false */
	function _query($sql,$inputarr=false)
	{

		$dbc = $this->_connectionID;
		$false = false;

	//	return rs
		if ($inputarr) {

			if (!empty($this->charPage))
				$oCmd = new COM('ADODB.Command',null,$this->charPage);
			else
				$oCmd = new COM('ADODB.Command');
			$oCmd->ActiveConnection = $dbc;
			$oCmd->CommandText = $sql;
			$oCmd->CommandType = 1;

      // Map by http://msdn.microsoft.com/library/default.asp?url=/library/en-us/ado270/htm/mdmthcreateparam.asp
      // Check issue http://bugs.php.net/bug.php?id=40664 !!!
			foreach ($inputarr as $val) {
				$type = gettype($val);
				$len=strlen($val);
				if ($type == 'boolean')
					$this->adoParameterType = 11;
				else if ($type == 'integer')
					$this->adoParameterType = 3;
				else if ($type == 'double')
					$this->adoParameterType = 5;
				elseif ($type == 'string')
					$this->adoParameterType = 202;
				else if (($val === null) || (!defined($val)))
					$len=1;
				else
					$this->adoParameterType = 130;

				// name, type, direction 1 = input, len,
        		$p = $oCmd->CreateParameter('name',$this->adoParameterType,1,$len,$val);

				$oCmd->Parameters->Append($p);
			}
			$p = false;
			$rs = $oCmd->Execute();
			$e = $dbc->Errors;
			if ($dbc->Errors->Count > 0) return $false;
			return $rs;
		}

		$rs = @$dbc->Execute($sql,$this->_affectedRows, $this->_execute_option);

		if ($dbc->Errors->Count > 0) return $false;
		if (! $rs) return $false;

		if ($rs->State == 0) {
			$true = true;
			return $true; // 0 = adStateClosed means no records returned
		}
		return $rs;
	}


	function BeginTrans()
	{
		if ($this->transOff) return true;

		if (isset($this->_thisTransactions))
			if (!$this->_thisTransactions) return false;
		else {
			$o = $this->_connectionID->Properties("Transaction DDL");
			$this->_thisTransactions = $o ? true : false;
			if (!$o) return false;
		}
		@$this->_connectionID->BeginTrans();
		$this->transCnt += 1;
		return true;
	}

	function CommitTrans($ok=true)
	{
		if (!$ok) return $this->RollbackTrans();
		if ($this->transOff) return true;

		@$this->_connectionID->CommitTrans();
		if ($this->transCnt) @$this->transCnt -= 1;
		return true;
	}
	function RollbackTrans() {
		if ($this->transOff) return true;
		@$this->_connectionID->RollbackTrans();
		if ($this->transCnt) @$this->transCnt -= 1;
		return true;
	}

	/*	Returns: the last error message from previous database operation	*/

	function ErrorMsg()
	{
		if (!$this->_connectionID) return "No connection established";
		$errc = $this->_connectionID->Errors;
		if (!$errc) return "No Errors object found";
		if ($errc->Count == 0) return '';
		$err = $errc->Item($errc->Count-1);
		return $err->Description;
	}

	function ErrorNo()
	{
		$errc = $this->_connectionID->Errors;
		if ($errc->Count == 0) return 0;
		$err = $errc->Item($errc->Count-1);
		return $err->NativeError;
	}

	// returns true or false
	function _close()
	{
		if ($this->_connectionID) $this->_connectionID->Close();
		$this->_connectionID = false;
		return true;
	}


}

/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_ado extends ADORecordSet {

	var $bind = false;
	var $databaseType = "ado";
	var $dataProvider = "ado";
	var $_tarr = false; // caches the types
	var $_flds; // and field objects
	var $canSeek = true;
  	var $hideErrors = true;

	function __construct($id,$mode=false)
	{
		if ($mode === false) {
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		$this->fetchMode = $mode;
		return parent::__construct($id,$mode);
	}


	// returns the field object
	function FetchField($fieldOffset = -1) {
		$off=$fieldOffset+1; // offsets begin at 1

		$o= new ADOFieldObject();
		$rs = $this->_queryID;
		$f = $rs->Fields($fieldOffset);
		$o->name = $f->Name;
		$t = $f->Type;
		$o->type = $this->MetaType($t);
		$o->max_length = $f->DefinedSize;
		$o->ado_type = $t;

		//print "off=$off name=$o->name type=$o->type len=$o->max_length<br>";
		return $o;
	}

	/* Use associative array to get fields array */
	function Fields($colname)
	{
		if ($this->fetchMode & ADODB_FETCH_ASSOC) return $this->fields[$colname];
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}

		 return $this->fields[$this->bind[strtoupper($colname)]];
	}


	function _initrs()
	{
		$rs = $this->_queryID;
		$this->_numOfRows = $rs->RecordCount;

		$f = $rs->Fields;
		$this->_numOfFields = $f->Count;
	}


	 // should only be used to move forward as we normally use forward-only cursors
	function _seek($row)
	{
	   $rs = $this->_queryID;
		// absoluteposition doesn't work -- my maths is wrong ?
		//	$rs->AbsolutePosition->$row-2;
		//	return true;
		if ($this->_currentRow > $row) return false;
		@$rs->Move((integer)$row - $this->_currentRow-1); //adBookmarkFirst
		return true;
	}

/*
	OLEDB types

	 enum DBTYPEENUM
	{	DBTYPE_EMPTY	= 0,
	DBTYPE_NULL	= 1,
	DBTYPE_I2	= 2,
	DBTYPE_I4	= 3,
	DBTYPE_R4	= 4,
	DBTYPE_R8	= 5,
	DBTYPE_CY	= 6,
	DBTYPE_DATE	= 7,
	DBTYPE_BSTR	= 8,
	DBTYPE_IDISPATCH	= 9,
	DBTYPE_ERROR	= 10,
	DBTYPE_BOOL	= 11,
	DBTYPE_VARIANT	= 12,
	DBTYPE_IUNKNOWN	= 13,
	DBTYPE_DECIMAL	= 14,
	DBTYPE_UI1	= 17,
	DBTYPE_ARRAY	= 0x2000,
	DBTYPE_BYREF	= 0x4000,
	DBTYPE_I1	= 16,
	DBTYPE_UI2	= 18,
	DBTYPE_UI4	= 19,
	DBTYPE_I8	= 20,
	DBTYPE_UI8	= 21,
	DBTYPE_GUID	= 72,
	DBTYPE_VECTOR	= 0x1000,
	DBTYPE_RESERVED	= 0x8000,
	DBTYPE_BYTES	= 128,
	DBTYPE_STR	= 129,
	DBTYPE_WSTR	= 130,
	DBTYPE_NUMERIC	= 131,
	DBTYPE_UDT	= 132,
	DBTYPE_DBDATE	= 133,
	DBTYPE_DBTIME	= 134,
	DBTYPE_DBTIMESTAMP	= 135

	ADO Types

   	adEmpty	= 0,
	adTinyInt	= 16,
	adSmallInt	= 2,
	adInteger	= 3,
	adBigInt	= 20,
	adUnsignedTinyInt	= 17,
	adUnsignedSmallInt	= 18,
	adUnsignedInt	= 19,
	adUnsignedBigInt	= 21,
	adSingle	= 4,
	adDouble	= 5,
	adCurrency	= 6,
	adDecimal	= 14,
	adNumeric	= 131,
	adBoolean	= 11,
	adError	= 10,
	adUserDefined	= 132,
	adVariant	= 12,
	adIDispatch	= 9,
	adIUnknown	= 13,
	adGUID	= 72,
	adDate	= 7,
	adDBDate	= 133,
	adDBTime	= 134,
	adDBTimeStamp	= 135,
	adBSTR	= 8,
	adChar	= 129,
	adVarChar	= 200,
	adLongVarChar	= 201,
	adWChar	= 130,
	adVarWChar	= 202,
	adLongVarWChar	= 203,
	adBinary	= 128,
	adVarBinary	= 204,
	adLongVarBinary	= 205,
	adChapter	= 136,
	adFileTime	= 64,
	adDBFileTime	= 137,
	adPropVariant	= 138,
	adVarNumeric	= 139
*/
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}

		if (!is_numeric($t)) return $t;

		switch ($t) {
		case 0:
		case 12: // variant
		case 8: // bstr
		case 129: //char
		case 130: //wc
		case 200: // varc
		case 202:// varWC
		case 128: // bin
		case 204: // varBin
		case 72: // guid
			if ($len <= $this->blobSize) return 'C';

		case 201:
		case 203:
			return 'X';
		case 128:
		case 204:
		case 205:
			 return 'B';
		case 7:
		case 133: return 'D';

		case 134:
		case 135: return 'T';

		case 11: return 'L';

		case 16://	adTinyInt	= 16,
		case 2://adSmallInt	= 2,
		case 3://adInteger	= 3,
		case 4://adBigInt	= 20,
		case 17://adUnsignedTinyInt	= 17,
		case 18://adUnsignedSmallInt	= 18,
		case 19://adUnsignedInt	= 19,
		case 20://adUnsignedBigInt	= 21,
			return 'I';
		default: return 'N';
		}
	}

	// time stamp not supported yet
	function _fetch()
	{
		$rs = $this->_queryID;
		if (!$rs or $rs->EOF) {
			$this->fields = false;
			return false;
		}
		$this->fields = array();

		if (!$this->_tarr) {
			$tarr = array();
			$flds = array();
			for ($i=0,$max = $this->_numOfFields; $i < $max; $i++) {
				$f = $rs->Fields($i);
				$flds[] = $f;
				$tarr[] = $f->Type;
			}
			// bind types and flds only once
			$this->_tarr = $tarr;
			$this->_flds = $flds;
		}
		$t = reset($this->_tarr);
		$f = reset($this->_flds);

		if ($this->hideErrors)  $olde = error_reporting(E_ERROR|E_CORE_ERROR);// sometimes $f->value be null
		for ($i=0,$max = $this->_numOfFields; $i < $max; $i++) {
			//echo "<p>",$t,' ';var_dump($f->value); echo '</p>';
			switch($t) {
			case 135: // timestamp
				if (!strlen((string)$f->value)) $this->fields[] = false;
				else {
					if (!is_numeric($f->value)) # $val = variant_date_to_timestamp($f->value);
						// VT_DATE stores dates as (float) fractional days since 1899/12/30 00:00:00
						$val=(float) variant_cast($f->value,VT_R8)*3600*24-2209161600;
					else
						$val = $f->value;
					$this->fields[] = adodb_date('Y-m-d H:i:s',$val);
				}
				break;
			case 133:// A date value (yyyymmdd)
				if ($val = $f->value) {
					$this->fields[] = substr($val,0,4).'-'.substr($val,4,2).'-'.substr($val,6,2);
				} else
					$this->fields[] = false;
				break;
			case 7: // adDate
				if (!strlen((string)$f->value)) $this->fields[] = false;
				else {
					if (!is_numeric($f->value)) $val = variant_date_to_timestamp($f->value);
					else $val = $f->value;

					if (($val % 86400) == 0) $this->fields[] = adodb_date('Y-m-d',$val);
					else $this->fields[] = adodb_date('Y-m-d H:i:s',$val);
				}
				break;
			case 1: // null
				$this->fields[] = false;
				break;
			case 6: // currency is not supported properly;
				ADOConnection::outp( '<b>'.$f->Name.': currency type not supported by PHP</b>');
				$this->fields[] = (float) $f->value;
				break;
			case 11: //BIT;
				$val = "";
				if(is_bool($f->value))	{
					if($f->value==true) $val = 1;
					else $val = 0;
				}
				if(is_null($f->value)) $val = null;

				$this->fields[] = $val;
				break;
			default:
				$this->fields[] = $f->value;
				break;
			}
			//print " $f->value $t, ";
			$f = next($this->_flds);
			$t = next($this->_tarr);
		} // for
		if ($this->hideErrors) error_reporting($olde);
		@$rs->MoveNext(); // @ needed for some versions of PHP!

		if ($this->fetchMode & ADODB_FETCH_ASSOC) {
			$this->fields = $this->GetRowAssoc();
		}
		return true;
	}

		function NextRecordSet()
		{
			$rs = $this->_queryID;
			$this->_queryID = $rs->NextRecordSet();
			//$this->_queryID = $this->_QueryId->NextRecordSet();
			if ($this->_queryID == null) return false;

			$this->_currentRow = -1;
			$this->_currentPage = -1;
			$this->bind = false;
			$this->fields = false;
			$this->_flds = false;
			$this->_tarr = false;

			$this->_inited = false;
			$this->Init();
			return true;
		}

	function _close() {
		$this->_flds = false;
		@$this->_queryID->Close();// by Pete Dishman (peterd@telephonetics.co.uk)
		$this->_queryID = false;
	}

}
