<?php
/* 
V4.01 23 Oct 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  SAPDB data driver. Requires ODBC.

*/

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}
if (!defined('ADODB_SAPDB')){
define('ADODB_SAPDB',1);

class ADODB_SAPDB extends ADODB_odbc {
	var $databaseType = "sapdb";	
	var $concat_operator = '||';
	var $sysDate = 'DATE';
	var $sysTimeStamp = 'TIMESTAMP';
	var $fmtDate = "\\D\\A\\T\\E('Y-m-d')";	/// used by DBDate() as the default date format used by the database
	var $fmtTimeStamp = "\\T\\I\\M\\E\\S\\T\\A\\M\\P('Y-m-d','H:i:s')"; /// used by DBTimeStamp as the default timestamp fmt.
	
	function ADODB_SAPDB()
	{
		//if (strncmp(PHP_OS,'WIN',3) === 0) $this->curmode = SQL_CUR_USE_ODBC;
		$this->ADODB_odbc();
	}
	
	/*
		SelectLimit implementation problems:
	
	 	The following will return random 10 rows as order by performed after "WHERE rowno<10"
	 	which is not ideal...
		
	  		select * from table where rowno < 10 order by 1
	  
	  	This means that we have to use the adoconnection base class SelectLimit when
	  	there is an "order by".
		
		See http://listserv.sap.com/pipermail/sapdb.general/2002-January/010405.html
	 */
	
};
 

class  ADORecordSet_sapdb extends ADORecordSet_odbc {	
	
	var $databaseType = "sapdb";		
	
	function ADORecordSet_sapdb($id,$mode=false)
	{
		$this->ADORecordSet_odbc($id,$mode);
	}
}

} //define
?>