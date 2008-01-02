<?php
/* 
V4.96 24 Sept 2007  (c) 2000-2007 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Microsoft SQL Server ADO data driver. Requires ADO and MSSQL client. 
  Works only on MS Windows.
  
  Warning: Some versions of PHP (esp PHP4) leak memory when ADO/COM is used. 
  Please check http://bugs.php.net/ for more info.
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
	var $_dropSeqSQL = "drop table %s";
	
	//var $_inTransaction = 1; // always open recordsets, so no transaction problems.
	
	function ADODB_ado_mssql()
	{
	        $this->ADODB_ado();
	}
	
	function _insertid()
	{
	        return $this->GetOne('select SCOPE_IDENTITY()');
	}
	
	function _affectedrows()
	{
	        return $this->GetOne('select @@rowcount');
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
	
	function qstr($s,$magic_quotes=false)
	{
		$s = ADOConnection::qstr($s, $magic_quotes);
		return str_replace("\0", "\\\\000", $s);
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
	
	} // end class 
	
	class  ADORecordSet_ado_mssql extends ADORecordSet_ado {        
	
	var $databaseType = 'ado_mssql';
	
	function ADORecordSet_ado_mssql($id,$mode=false)
	{
	        return $this->ADORecordSet_ado($id,$mode);
	}
}
?>