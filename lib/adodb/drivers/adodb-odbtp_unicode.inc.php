<?php
/*
V4.50 6 July 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence. See License.txt.
  Set tabs to 4 for best viewing.
  Latest version is available at http://adodb.sourceforge.net
*/

// Code contributed by "Robert Twitty" <rtwitty#neutron.ushmm.org>

// security - hide paths
if (!defined('ADODB_DIR')) die();

/*
    Because the ODBTP server sends and reads UNICODE text data using UTF-8
    encoding, the following HTML meta tag must be included within the HTML
    head section of every HTML form and script page:

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    Also, all SQL query strings must be submitted as UTF-8 encoded text.
*/

if (!defined('_ADODB_ODBTP_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbtp.inc.php");
}

class ADODB_odbtp_unicode extends ADODB_odbtp {
	var $databaseType = "odbtp_unicode";
	var $_useUnicodeSQL = true;

	function ADODB_odbtp_unicode()
	{
		$this->ADODB_odbtp();
	}
}

class ADORecordSet_odbtp_unicode extends ADORecordSet_odbtp {
	var $databaseType = 'odbtp_unicode';

	function ADORecordSet_odbtp_unicode($queryID,$mode=false)
	{
		$this->ADORecordSet_odbtp($queryID, $mode);
	}

	function _initrs()
	{
		$this->_numOfFields = @odbtp_num_fields($this->_queryID);
		if (!($this->_numOfRows = @odbtp_num_rows($this->_queryID)))
			$this->_numOfRows = -1;

		if ($this->connection->odbc_driver == ODB_DRIVER_JET) {
			for ($f = 0; $f < $this->_numOfFields; $f++) {
				if (odbtp_field_bindtype($this->_queryID, $f) == ODB_CHAR)
					odbtp_bind_field($this->_queryID, $f, ODB_WCHAR);
			}
		}
	}
}
?>

