<?php
/* 
V5.11 5 May 2010   (c) 2000-2010 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. See License.txt. 
  Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Library for basic performance monitoring and tuning 
  
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

//
// Thx to  Fernando Ortiz, mailto:fortiz#lacorona.com.mx
// With info taken from http://www.oninit.com/oninit/sysmaster/index.html
//
class perf_informix extends adodb_perf{

	// Maximum size on varchar upto 9.30 255 chars
	// better truncate varchar to 255 than char(4000) ?
	var $createTableSQL = "CREATE TABLE adodb_logsql (
		created datetime year to second NOT NULL,
		sql0 varchar(250) NOT NULL,
		sql1 varchar(255) NOT NULL,
		params varchar(255) NOT NULL,
		tracer varchar(255) NOT NULL,
		timer decimal(16,6) NOT NULL
	)";
	
	var $tablesSQL = "select a.tabname tablename, ti_nptotal*2 size_in_k, ti_nextns extents, ti_nrows records from systables c, sysmaster:systabnames a, sysmaster:systabinfo b where c.tabname not matches 'sys*' and c.partnum = a.partnum and c.partnum = b.ti_partnum";
	
	var $settings = array(
	'Ratios',
		'data cache hit ratio' => array('RATIOH',
		"select round((1-(wt.value / (rd.value + wr.value)))*100,2)
		from sysmaster:sysprofile wr, sysmaster:sysprofile rd, sysmaster:sysprofile wt
		where rd.name = 'pagreads' and
		wr.name = 'pagwrites' and
		wt.name = 'buffwts'",
		'=WarnCacheRatio'),
	'IO',
		'data reads' => array('IO',
		"select value from sysmaster:sysprofile where name='pagreads'",
		'Page reads'),
		
		'data writes' => array('IO',
		"select value from sysmaster:sysprofile where name='pagwrites'",
		'Page writes'),
	
	'Connections',
		'current connections' => array('SESS',
		'select count(*) from sysmaster:syssessions',
		'Number of sessions'),
	
	false
	
	);
	
	function perf_informix(&$conn)
	{
		$this->conn = $conn;
	}

}
?>
