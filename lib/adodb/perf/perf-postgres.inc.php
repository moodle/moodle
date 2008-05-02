<?php

/* 
V5.04a 25 Mar 2008   (c) 2000-2008 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. See License.txt. 
  Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Library for basic performance monitoring and tuning 
  
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

/*
	Notice that PostgreSQL has no sql query cache
*/
class perf_postgres extends adodb_perf{
	
	var $tablesSQL = 
	"select a.relname as tablename,(a.relpages+CASE WHEN b.relpages is null THEN 0 ELSE b.relpages END+CASE WHEN c.relpages is null THEN 0 ELSE c.relpages END)*8 as size_in_K,a.relfilenode as \"OID\"  from pg_class a left join pg_class b
		on b.relname = 'pg_toast_'||trim(a.relfilenode) 
		left join pg_class c on c.relname = 'pg_toast_'||trim(a.relfilenode)||'_index'
		where a.relname in (select tablename from pg_tables where tablename not like 'pg_%')";
	
	var $createTableSQL = "CREATE TABLE adodb_logsql (
		  created timestamp NOT NULL,
		  sql0 varchar(250) NOT NULL,
		  sql1 text NOT NULL,
		  params text NOT NULL,
		  tracer text NOT NULL,
		  timer decimal(16,6) NOT NULL
		)";	
	
	var $settings = array(
	'Ratios',
		'statistics collector' => array('RATIO',
			"select case when count(*)=3 then 'TRUE' else 'FALSE' end from pg_settings where (name='stats_block_level' or name='stats_row_level' or name='stats_start_collector') and setting='on' ",
			'Value must be TRUE to enable hit ratio statistics (<i>stats_start_collector</i>,<i>stats_row_level</i> and <i>stats_block_level</i> must be set to true in postgresql.conf)'),
		'data cache hit ratio' => array('RATIO',
			"select case when blks_hit=0 then 0 else round( ((1-blks_read::float/blks_hit)*100)::numeric, 2) end from pg_stat_database where datname='\$DATABASE'",
			'=WarnCacheRatio'),
	'IO',
		'data reads' => array('IO',
		'select sum(heap_blks_read+toast_blks_read) from pg_statio_user_tables',
		),
		'data writes' => array('IO',
		'select round((sum(n_tup_ins/4.0+n_tup_upd/8.0+n_tup_del/4.0)/16)::numeric,2) from pg_stat_user_tables',
		'Count of inserts/updates/deletes * coef'),

	'Data Cache',
		'data cache buffers' => array('DATAC',
			"select setting from pg_settings where name='shared_buffers'",
			'Number of cache buffers. <a href=http://www.varlena.com/GeneralBits/Tidbits/perf.html#basic>Tuning</a>'),
		'cache blocksize' => array('DATAC',
			'select 8192',
			'(estimate)' ),
		'data cache size' => array( 'DATAC',
		"select setting::integer*8192 from pg_settings where name='shared_buffers'",
			'' ),
		'operating system cache size' => array( 'DATA',
		"select setting::integer*8192 from pg_settings where name='effective_cache_size'",
			'(effective cache size)' ),
	'Memory Usage',
	# Postgres 7.5 changelog: Rename server parameters SortMem and VacuumMem to work_mem and maintenance_work_mem;
		'sort/work buffer size' => array('CACHE',
			"select setting::integer*1024 from pg_settings where name='sort_mem' or name = 'work_mem' order by name",
			'Size of sort buffer (per query)' ),
	'Connections',
		'current connections' => array('SESS',
			'select count(*) from pg_stat_activity',
			''),
		'max connections' => array('SESS',
			"select setting from pg_settings where name='max_connections'",
			''),
	'Parameters',
		'rollback buffers' => array('COST',
			"select setting from pg_settings where name='wal_buffers'",
			'WAL buffers'),
		'random page cost' => array('COST',
			"select setting from pg_settings where name='random_page_cost'",
			'Cost of doing a seek (default=4). See <a href=http://www.varlena.com/GeneralBits/Tidbits/perf.html#less>random_page_cost</a>'),
		false
	);
	
	function perf_postgres(&$conn)
	{
		$this->conn = $conn;
	}
	
	var $optimizeTableLow  = 'VACUUM %s'; 
	var $optimizeTableHigh = 'VACUUM ANALYZE %s';

/**
 * @see adodb_perf#optimizeTable
 */

	function optimizeTable($table, $mode = ADODB_OPT_LOW) 
	{
	    if(! is_string($table)) return false;
	    
	    $conn = $this->conn;
	    if (! $conn) return false;
	    
	    $sql = '';
	    switch($mode) {
	        case ADODB_OPT_LOW : $sql = $this->optimizeTableLow;  break;
	        case ADODB_OPT_HIGH: $sql = $this->optimizeTableHigh; break;
	        default            : 
	        {
	            ADOConnection::outp(sprintf("<p>%s: '%s' using of undefined mode '%s'</p>", __CLASS__, 'optimizeTable', $mode));
	            return false;
	        }
	    }
	    $sql = sprintf($sql, $table);
	    
	    return $conn->Execute($sql) !== false;  
	}
	
	function Explain($sql,$partial=false)
	{
		$save = $this->conn->LogSQL(false);
		
		if ($partial) {
			$sqlq = $this->conn->qstr($sql.'%');
			$arr = $this->conn->GetArray("select distinct distinct sql1 from adodb_logsql where sql1 like $sqlq");
			if ($arr) {
				foreach($arr as $row) {
					$sql = reset($row);
					if (crc32($sql) == $partial) break;
				}
			}
		}
		$sql = str_replace('?',"''",$sql);
		$s = '<p><b>Explain</b>: '.htmlspecialchars($sql).'</p>';
		$rs = $this->conn->Execute('EXPLAIN '.$sql);
		$this->conn->LogSQL($save);
		$s .= '<pre>';
		if ($rs)
			while (!$rs->EOF) {
				$s .= reset($rs->fields)."\n";
				$rs->MoveNext();
			}
		$s .= '</pre>';
		$s .= $this->Tracer($sql,$partial);
		return $s;
	}
}
?>