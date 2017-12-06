<?php
/*
 @version   v5.20.7  20-Sep-2016
 @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
 @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 4.

  Postgres9 support.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR."/drivers/adodb-postgres8.inc.php");

class ADODB_postgres9 extends ADODB_postgres8
{
	var $databaseType = 'postgres9';
}

class ADORecordSet_postgres9 extends ADORecordSet_postgres8
{
	var $databaseType = "postgres9";
}

class ADORecordSet_assoc_postgres9 extends ADORecordSet_assoc_postgres8
{
	var $databaseType = "postgres9";
}
