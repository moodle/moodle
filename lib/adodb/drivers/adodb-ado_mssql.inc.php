<?php
/* 
V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Microsoft SQL Server ADO data driver. Requires ADO and MSSQL client. 
  Works only on MS Windows.
  
  It is normally better to use the mssql driver directly because it is much faster. 
  This file is only a technology demonstration and for test purposes.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ADO_LAYER')) {
	if (PHP_VERSION >= 5) include(ADODB_DIR."/drivers/adodb-ado5.inc.php");
	else include(ADODB_DIR."/drivers/adodb-ado.inc.php");
}


class  ADODB_ado_mssql extends ADODB_ado {        
	var $databaseType = 'ado_mssql';
	var $hasTop = 'top';
	var $hasInsertID = true;
	var $sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
	var $sysTimeStamp = 'GetDate()';
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $ansiOuter = true; // for mssql7 or later
	var $substr = "substring";
	var $length = 'len';
	
	//var $_inTransaction = 1; // always open recordsets, so no transaction problems.
	
	function ADODB_ado_mssql()
	{
	        $this->ADODB_ado();
	}
	
	function _insertid()
	{
	        return $this->GetOne('select @@identity');
	}
	
	function _affectedrows()
	{
	        return $this->GetOne('select @@rowcount');
	}
	
	function MetaColumns($table)
	{
        $table = strtoupper($table);
        $arr= array();
        $dbc = $this->_connectionID;
        
        $osoptions = array();
        $osoptions[0] = null;
        $osoptions[1] = null;
        $osoptions[2] = $table;
        $osoptions[3] = null;
        
        $adors=@$dbc->OpenSchema(4, $osoptions);//tables

        if ($adors){
                while (!$adors->EOF){
                        $fld = new ADOFieldObject();
                        $c = $adors->Fields(3);
                        $fld->name = $c->Value;
                        $fld->type = 'CHAR'; // cannot discover type in ADO!
                        $fld->max_length = -1;
                        $arr[strtoupper($fld->name)]=$fld;
        
                        $adors->MoveNext();
                }
                $adors->Close();
        }
        $false = false;
		return empty($arr) ? $false : $arr;
	}
	
	} // end class 
	
	class  ADORecordSet_ado_mssql extends ADORecordSet_ado {        
	
	var $databaseType = 'ado_mssql';
	
	function ADORecordSet_ado_mssql($id,$mode=false)
	{
	        return $this->ADORecordSet_ado($id,$mode);
	}
}
?>