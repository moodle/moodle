<?php
/* 
version V4.98 13 Feb 2008 (c) 2000-2008  John Lim (jlim#natsoft.com.my).  All rights
reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.sourceforge.net

  21.02.2002 - Wade Johnson wade@wadejohnson.de
			   Extended ODBC class for Sybase SQLAnywhere.
   1) Added support to retrieve the last row insert ID on tables with
	  primary key column using autoincrement function.

   2) Added blob support.  Usage:
		 a) create blob variable on db server:

		$dbconn->create_blobvar($blobVarName);

	  b) load blob var from file.  $filename must be complete path

	  $dbcon->load_blobvar_from_file($blobVarName, $filename);

	  c) Use the $blobVarName in SQL insert or update statement in the values
	  clause:

		$recordSet = $dbconn->Execute('INSERT INTO tabname (idcol, blobcol) '
		.
	   'VALUES (\'test\', ' . $blobVarName . ')');

	 instead of loading blob from a file, you can also load from 
	  an unformatted (raw) blob variable:
	  $dbcon->load_blobvar_from_var($blobVarName, $varName);

	  d) drop blob variable on db server to free up resources:
	  $dbconn->drop_blobvar($blobVarName);

  Sybase_SQLAnywhere data driver. Requires ODBC.

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ODBC_LAYER')) {
 include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}

if (!defined('ADODB_SYBASE_SQLANYWHERE')){

 define('ADODB_SYBASE_SQLANYWHERE',1);

 class ADODB_sqlanywhere extends ADODB_odbc {
  	var $databaseType = "sqlanywhere";	
	var $hasInsertID = true;
	
	function ADODB_sqlanywhere()
	{
		$this->ADODB_odbc();
	}

	 function _insertid() {
  	   return $this->GetOne('select @@identity');
	 }

  function create_blobvar($blobVarName) {
   $this->Execute("create variable $blobVarName long binary");
   return;
  }

  function drop_blobvar($blobVarName) {
   $this->Execute("drop variable $blobVarName");
   return;
  }

  function load_blobvar_from_file($blobVarName, $filename) {
   $chunk_size = 1000;

   $fd = fopen ($filename, "rb");

   $integer_chunks = (integer)filesize($filename) / $chunk_size;
   $modulus = filesize($filename) % $chunk_size;
   if ($modulus != 0){
	$integer_chunks += 1;
   }

   for($loop=1;$loop<=$integer_chunks;$loop++){
	$contents = fread ($fd, $chunk_size);
	$contents = bin2hex($contents);

	$hexstring = '';

	for($loop2=0;$loop2<strlen($contents);$loop2+=2){
	 $hexstring .= '\x' . substr($contents,$loop2,2);
	 }

	$hexstring = $this->qstr($hexstring);

	$this->Execute("set $blobVarName = $blobVarName || " . $hexstring);
   }

   fclose ($fd);
   return;
  }

  function load_blobvar_from_var($blobVarName, &$varName) {
   $chunk_size = 1000;

   $integer_chunks = (integer)strlen($varName) / $chunk_size;
   $modulus = strlen($varName) % $chunk_size;
   if ($modulus != 0){
	$integer_chunks += 1;
   }

   for($loop=1;$loop<=$integer_chunks;$loop++){
	$contents = substr ($varName, (($loop - 1) * $chunk_size), $chunk_size);
	$contents = bin2hex($contents);

	$hexstring = '';

	for($loop2=0;$loop2<strlen($contents);$loop2+=2){
	 $hexstring .= '\x' . substr($contents,$loop2,2);
	 }

	$hexstring = $this->qstr($hexstring);

	$this->Execute("set $blobVarName = $blobVarName || " . $hexstring);
   }

   return;
  }

 /*
  Insert a null into the blob field of the table first.
  Then use UpdateBlob to store the blob.

  Usage:

  $conn->Execute('INSERT INTO blobtable (id, blobcol) VALUES (1, null)');
  $conn->UpdateBlob('blobtable','blobcol',$blob,'id=1');
 */
  function UpdateBlob($table,$column,&$val,$where,$blobtype='BLOB')
  {
   $blobVarName = 'hold_blob';
   $this->create_blobvar($blobVarName);
   $this->load_blobvar_from_var($blobVarName, $val);
   $this->Execute("UPDATE $table SET $column=$blobVarName WHERE $where");
   $this->drop_blobvar($blobVarName);
   return true;
  }
 }; //class

 class  ADORecordSet_sqlanywhere extends ADORecordSet_odbc {	

  var $databaseType = "sqlanywhere";		

 function ADORecordSet_sqlanywhere($id,$mode=false)
 {
  $this->ADORecordSet_odbc($id,$mode);
 }


 }; //class


} //define
?>
