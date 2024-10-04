<?php
/**
 * SAP SQL Anywhere driver (previously Sybase SQL Anywhere)
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ODBC_LAYER')) {
 include_once(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}

if (!defined('ADODB_SYBASE_SQLANYWHERE')){

 define('ADODB_SYBASE_SQLANYWHERE',1);

 class ADODB_sqlanywhere extends ADODB_odbc
 {
	 var $databaseType = "sqlanywhere";
	 var $hasInsertID = true;

	protected function _insertID($table = '', $column = '')
	{
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
  function updateBlob($table, $column, $val, $where, $blobtype = 'BLOB')
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


 }; //class


} //define
