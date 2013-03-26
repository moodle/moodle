<?php
/* 
V5.18 3 Sep 2012  (c) 2000-2012 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. See License.txt. 
  Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
  Library for basic performance monitoring and tuning 
  
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class perf_mysql extends adodb_perf{
	
	var $tablesSQL = 'show table status';
	
	var $createTableSQL = "CREATE TABLE adodb_logsql (
		  created datetime NOT NULL,
		  sql0 varchar(250) NOT NULL,
		  sql1 text NOT NULL,
		  params text NOT NULL,
		  tracer text NOT NULL,
		  timer decimal(16,6) NOT NULL
		)";
		
	var $settings = array(
	'Ratios',
		'MyISAM cache hit ratio' => array('RATIO',
			'=GetKeyHitRatio',
			'=WarnCacheRatio'),
		'InnoDB cache hit ratio' => array('RATIO',
			'=GetInnoDBHitRatio',
			'=WarnCacheRatio'),
		'data cache hit ratio' => array('HIDE', # only if called
			'=FindDBHitRatio',
			'=WarnCacheRatio'),
		'sql cache hit ratio' => array('RATIO',
			'=GetQHitRatio',
			''),
	'IO',
		'data reads' => array('IO',
			'=GetReads',
			'Number of selects (Key_reads is not accurate)'),
		'data writes' => array('IO',
			'=GetWrites',
			'Number of inserts/updates/deletes * coef (Key_writes is not accurate)'),
		
	'Data Cache',
		'MyISAM data cache size' => array('DATAC',
			array("show variables", 'key_buffer_size'),
			'' ),
		'BDB data cache size' => array('DATAC',
			array("show variables", 'bdb_cache_size'),
			'' ),
		'InnoDB data cache size' => array('DATAC',
			array("show variables", 'innodb_buffer_pool_size'),
			'' ),
	'Memory Usage',
		'read buffer size' => array('CACHE',
			array("show variables", 'read_buffer_size'),
			'(per session)'),
		'sort buffer size' => array('CACHE',
			array("show variables", 'sort_buffer_size'),
			'Size of sort buffer (per session)' ),
		'table cache' => array('CACHE',
			array("show variables", 'table_cache'),
			'Number of tables to keep open'),
	'Connections',	
		'current connections' => array('SESS',
			array('show status','Threads_connected'),
			''),
		'max connections' => array( 'SESS',
			array("show variables",'max_connections'),
			''),
	
		false
	);
	
	function perf_mysql(&$conn)
	{
		$this->conn = $conn;
	}
	
	function Explain($sql,$partial=false)
	{
		
		if (strtoupper(substr(trim($sql),0,6)) !== 'SELECT') return '<p>Unable to EXPLAIN non-select statement</p>';
		$save = $this->conn->LogSQL(false);
		if ($partial) {
			$sqlq = $this->conn->qstr($sql.'%');
			$arr = $this->conn->GetArray("select distinct sql1 from adodb_logsql where sql1 like $sqlq");
			if ($arr) {
				foreach($arr as $row) {
					$sql = reset($row);
					if (crc32($sql) == $partial) break;
				}
			}
		}
		$sql = str_replace('?',"''",$sql);
		
		if ($partial) {
			$sqlq = $this->conn->qstr($sql.'%');
			$sql = $this->conn->GetOne("select sql1 from adodb_logsql where sql1 like $sqlq");
		}
		
		$s = '<p><b>Explain</b>: '.htmlspecialchars($sql).'</p>';
		$rs = $this->conn->Execute('EXPLAIN '.$sql);
		$s .= rs2html($rs,false,false,false,false);
		$this->conn->LogSQL($save);
		$s .= $this->Tracer($sql);
		return $s;
	}
	
	function Tables()
	{
		if (!$this->tablesSQL) return false;
		
		$rs = $this->conn->Execute($this->tablesSQL);
		if (!$rs) return false;
		
		$html = rs2html($rs,false,false,false,false);
		return $html;
	}
	
	function GetReads()
	{
	global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->conn->fetchMode !== false) $savem = $this->conn->SetFetchMode(false);
		
		$rs = $this->conn->Execute('show status');
		
		if (isset($savem)) $this->conn->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		
		if (!$rs) return 0;
		$val = 0;
		while (!$rs->EOF) {
			switch($rs->fields[0]) {
			case 'Com_select': 
				$val = $rs->fields[1];
				$rs->Close();
				return $val;
			}
			$rs->MoveNext();
		} 
		
		$rs->Close();
		
		return $val;
	}
	
	function GetWrites()
	{
	global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->conn->fetchMode !== false) $savem = $this->conn->SetFetchMode(false);
		
		$rs = $this->conn->Execute('show status');
		
		if (isset($savem)) $this->conn->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		
		if (!$rs) return 0;
		$val = 0.0;
		while (!$rs->EOF) {
			switch($rs->fields[0]) {
			case 'Com_insert': 
				$val += $rs->fields[1]; break;
			case 'Com_delete': 
				$val += $rs->fields[1]; break;
			case 'Com_update': 
				$val += $rs->fields[1]/2;
				$rs->Close();
				return $val;
			}
			$rs->MoveNext();
		} 
		
		$rs->Close();
		
		return $val;
	}
	
	function FindDBHitRatio()
	{
		// first find out type of table
		//$this->conn->debug=1;
		
		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->conn->fetchMode !== false) $savem = $this->conn->SetFetchMode(false);
		
		$rs = $this->conn->Execute('show table status');
		
		if (isset($savem)) $this->conn->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		
		if (!$rs) return '';
		$type = strtoupper($rs->fields[1]);
		$rs->Close();
		switch($type){
		case 'MYISAM':
		case 'ISAM':
			return $this->DBParameter('MyISAM cache hit ratio').' (MyISAM)';
		case 'INNODB':
			return $this->DBParameter('InnoDB cache hit ratio').' (InnoDB)';
		default:
			return $type.' not supported';
		}
		
	}
	
	function GetQHitRatio()
	{
		//Total number of queries = Qcache_inserts + Qcache_hits + Qcache_not_cached
		$hits = $this->_DBParameter(array("show status","Qcache_hits"));
		$total = $this->_DBParameter(array("show status","Qcache_inserts"));
		$total += $this->_DBParameter(array("show status","Qcache_not_cached"));
		
		$total += $hits;
		if ($total) return round(($hits*100)/$total,2);
		return 0;
	}
	
	/*
		Use session variable to store Hit percentage, because MySQL
		does not remember last value of SHOW INNODB STATUS hit ratio
		
		# 1st query to SHOW INNODB STATUS
		0.00 reads/s, 0.00 creates/s, 0.00 writes/s
		Buffer pool hit rate 1000 / 1000
		
		# 2nd query to SHOW INNODB STATUS
		0.00 reads/s, 0.00 creates/s, 0.00 writes/s
		No buffer pool activity since the last printout
	*/
	function GetInnoDBHitRatio()
	{
	global $ADODB_FETCH_MODE;
	
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($this->conn->fetchMode !== false) $savem = $this->conn->SetFetchMode(false);
		
		$rs = $this->conn->Execute('show engine innodb status');
		
		if (isset($savem)) $this->conn->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		
		if (!$rs || $rs->EOF) return 0;
		$stat = $rs->fields[0];
		$rs->Close();
		$at = strpos($stat,'Buffer pool hit rate');
		$stat = substr($stat,$at,200);
		if (preg_match('!Buffer pool hit rate\s*([0-9]*) / ([0-9]*)!',$stat,$arr)) {
			$val = 100*$arr[1]/$arr[2];
			$_SESSION['INNODB_HIT_PCT'] = $val;
			return round($val,2);
		} else {
			if (isset($_SESSION['INNODB_HIT_PCT'])) return $_SESSION['INNODB_HIT_PCT'];
			return 0;
		}
		return 0;
	}
	
	function GetKeyHitRatio()
	{
		$hits = $this->_DBParameter(array("show status","Key_read_requests"));
		$reqs = $this->_DBParameter(array("show status","Key_reads"));
		if ($reqs == 0) return 0;
		
		return round(($hits/($reqs+$hits))*100,2);
	}
	
    // start hack 
    var $optimizeTableLow = 'CHECK TABLE %s FAST QUICK';
    var $optimizeTableHigh = 'OPTIMIZE TABLE %s';
    
    /** 
     * @see adodb_perf#optimizeTable
     */
     function optimizeTable( $table, $mode = ADODB_OPT_LOW) 
     {
        if ( !is_string( $table)) return false;
        
        $conn = $this->conn;
        if ( !$conn) return false;
        
        $sql = '';
        switch( $mode) {
            case ADODB_OPT_LOW : $sql = $this->optimizeTableLow; break;
            case ADODB_OPT_HIGH : $sql = $this->optimizeTableHigh; break;
            default : 
            {
                // May dont use __FUNCTION__ constant for BC (__FUNCTION__ Added in PHP 4.3.0)
                ADOConnection::outp( sprintf( "<p>%s: '%s' using of undefined mode '%s'</p>", __CLASS__, __FUNCTION__, $mode));
                return false;
            }
        }
        $sql = sprintf( $sql, $table);
        
        return $conn->Execute( $sql) !== false;
     }
    // end hack 
}
?>