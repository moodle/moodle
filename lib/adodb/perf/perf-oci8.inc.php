<?php
/*
@version   v5.20.14  06-Jan-2019
@copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence. See License.txt.
  Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.org/

  Library for basic performance monitoring and tuning

*/

// security - hide paths
if (!defined('ADODB_DIR')) die();


class perf_oci8 extends ADODB_perf{

	var $noShowIxora = 15; // if the sql for suspicious sql is taking too long, then disable ixora

	var $tablesSQL = "select segment_name as \"tablename\", sum(bytes)/1024 as \"size_in_k\",tablespace_name as \"tablespace\",count(*) \"extents\" from sys.user_extents
	   group by segment_name,tablespace_name";

	var $version;

	var $createTableSQL = "CREATE TABLE adodb_logsql (
		  created date NOT NULL,
		  sql0 varchar(250) NOT NULL,
		  sql1 varchar(4000) NOT NULL,
		  params varchar(4000),
		  tracer varchar(4000),
		  timer decimal(16,6) NOT NULL
		)";

	var $settings = array(
	'Ratios',
		'data cache hit ratio' => array('RATIOH',
			"select round((1-(phy.value / (cur.value + con.value)))*100,2)
			from v\$sysstat cur, v\$sysstat con, v\$sysstat phy
			where cur.name = 'db block gets' and
			      con.name = 'consistent gets' and
			      phy.name = 'physical reads'",
			'=WarnCacheRatio'),

		'sql cache hit ratio' => array( 'RATIOH',
			'select round(100*(sum(pins)-sum(reloads))/sum(pins),2)  from v$librarycache',
			'increase <i>shared_pool_size</i> if too ratio low'),

		'datadict cache hit ratio' => array('RATIOH',
		"select
           round((1 - (sum(getmisses) / (sum(gets) +
   		 sum(getmisses))))*100,2)
		from  v\$rowcache",
		'increase <i>shared_pool_size</i> if too ratio low'),

		'memory sort ratio' => array('RATIOH',
		"SELECT ROUND((100 * b.VALUE) /DECODE ((a.VALUE + b.VALUE),
       0,1,(a.VALUE + b.VALUE)),2)
FROM   v\$sysstat a,
       v\$sysstat b
WHERE  a.name = 'sorts (disk)'
AND    b.name = 'sorts (memory)'",
	"% of memory sorts compared to disk sorts - should be over 95%"),

	'IO',
		'data reads' => array('IO',
		"select value from v\$sysstat where name='physical reads'"),

	'data writes' => array('IO',
		"select value from v\$sysstat where name='physical writes'"),

	'Data Cache',

		'data cache buffers' => array( 'DATAC',
		"select a.value/b.value  from v\$parameter a, v\$parameter b
			where a.name = 'db_cache_size' and b.name= 'db_block_size'",
			'Number of cache buffers. Tune <i>db_cache_size</i> if the <i>data cache hit ratio</i> is too low.'),
		'data cache blocksize' => array('DATAC',
			"select value from v\$parameter where name='db_block_size'",
			'' ),

	'Memory Pools',
		'Mem Max Target (11g+)' => array( 'DATAC',
		"select value from v\$parameter where name = 'memory_max_target'",
			'The memory_max_size is the maximum value to which memory_target can be set.' ),
	'Memory target (11g+)' => array( 'DATAC',
		"select value from v\$parameter where name = 'memory_target'",
			'If memory_target is defined then SGA and PGA targets are consolidated into one memory_target.' ),
		'SGA Max Size' => array( 'DATAC',
		"select nvl(value,0)/1024.0/1024 || 'M' from v\$parameter where name = 'sga_max_size'",
			'The sga_max_size is the maximum value to which sga_target can be set.' ),
	'SGA target' => array( 'DATAC',
		"select nvl(value,0)/1024.0/1024 || 'M'  from v\$parameter where name = 'sga_target'",
			'If sga_target is defined then data cache, shared, java and large pool size can be 0. This is because all these pools are consolidated into one sga_target.' ),
	'PGA aggr target' => array( 'DATAC',
		"select nvl(value,0)/1024.0/1024 || 'M' from v\$parameter where name = 'pga_aggregate_target'",
			'If pga_aggregate_target is defined then this is the maximum memory that can be allocated for cursor operations such as sorts, group by, joins, merges. When in doubt, set it to 20% of sga_target.' ),
	'data cache size' => array('DATAC',
			"select value from v\$parameter where name = 'db_cache_size'",
			'db_cache_size' ),
		'shared pool size' => array('DATAC',
			"select value from v\$parameter where name = 'shared_pool_size'",
			'shared_pool_size, which holds shared sql, stored procedures, dict cache and similar shared structs' ),
		'java pool size' => array('DATAJ',
			"select value from v\$parameter where name = 'java_pool_size'",
			'java_pool_size' ),
		'large pool buffer size' => array('CACHE',
			"select value from v\$parameter where name='large_pool_size'",
			'this pool is for large mem allocations (not because it is larger than shared pool), for MTS sessions, parallel queries, io buffers (large_pool_size) ' ),

		'dynamic memory usage' => array('CACHE', "select '-' from dual", '=DynMemoryUsage'),

		'Connections',
		'current connections' => array('SESS',
			'select count(*) from sys.v_$session where username is not null',
			''),
		'max connections' => array( 'SESS',
			"select value from v\$parameter where name='sessions'",
			''),

	'Memory Utilization',
		'data cache utilization ratio' => array('RATIOU',
			"select round((1-bytes/sgasize)*100, 2)
			from (select sum(bytes) sgasize from sys.v_\$sgastat) s, sys.v_\$sgastat f
			where name = 'free memory' and pool = 'shared pool'",
		'Percentage of data cache actually in use - should be over 85%'),

		'shared pool utilization ratio' => array('RATIOU',
		'select round((sga.bytes/case when p.value=0 then sga.bytes else to_number(p.value) end)*100,2)
		from v$sgastat sga, v$parameter p
		where sga.name = \'free memory\' and sga.pool = \'shared pool\'
		and p.name = \'shared_pool_size\'',
		'Percentage of shared pool actually used - too low is bad, too high is worse'),

		'large pool utilization ratio' => array('RATIOU',
			"select round((1-bytes/sgasize)*100, 2)
			from (select sum(bytes) sgasize from sys.v_\$sgastat) s, sys.v_\$sgastat f
			where name = 'free memory' and pool = 'large pool'",
		'Percentage of large_pool actually in use - too low is bad, too high is worse'),
		'sort buffer size' => array('CACHE',
			"select value from v\$parameter where name='sort_area_size'",
			'max in-mem sort_area_size (per query), uses memory in pga' ),

		/*'pga usage at peak' => array('RATIOU',
		'=PGA','Mb utilization at peak transactions (requires Oracle 9i+)'),*/
	'Transactions',
		'rollback segments' => array('ROLLBACK',
			"select count(*) from sys.v_\$rollstat",
			''),

		'peak transactions' => array('ROLLBACK',
			"select max_utilization  tx_hwm
    		from sys.v_\$resource_limit
    		where resource_name = 'transactions'",
			'Taken from high-water-mark'),
		'max transactions' => array('ROLLBACK',
			"select value from v\$parameter where name = 'transactions'",
			'max transactions / rollback segments < 3.5 (or transactions_per_rollback_segment)'),
	'Parameters',
		'cursor sharing' => array('CURSOR',
			"select value from v\$parameter where name = 'cursor_sharing'",
			'Cursor reuse strategy. Recommended is FORCE (8i+) or SIMILAR (9i+). See <a href=http://www.praetoriate.com/oracle_tips_cursor_sharing.htm>cursor_sharing</a>.'),
		/*
		'cursor reuse' => array('CURSOR',
			"select count(*) from (select sql_text_wo_constants, count(*)
  from t1
 group by sql_text_wo_constants
having count(*) > 100)",'These are sql statements that should be using bind variables'),*/
		'index cache cost' => array('COST',
			"select value from v\$parameter where name = 'optimizer_index_caching'",
			'=WarnIndexCost'),
		'random page cost' => array('COST',
			"select value from v\$parameter where name = 'optimizer_index_cost_adj'",
			'=WarnPageCost'),
	'Waits',
		'Recent wait events' => array('WAITS','select \'Top 5 events\' from dual','=TopRecentWaits'),
//		'Historical wait SQL' => array('WAITS','select \'Last 2 days\' from dual','=TopHistoricalWaits'), -- requires AWR license
	'Backup',
		'Achivelog Mode' => array('BACKUP', 'select log_mode from v$database', '=LogMode'),

		'DBID' => array('BACKUP','select dbid from v$database','Primary key of database, used for recovery with an RMAN Recovery Catalog'),
		'Archive Log Dest' => array('BACKUP', "SELECT NVL(v1.value,v2.value)
FROM v\$parameter v1, v\$parameter v2 WHERE v1.name='log_archive_dest' AND v2.name='log_archive_dest_10'", ''),

		'Flashback Area' => array('BACKUP', "select nvl(value,'Flashback Area not used') from v\$parameter where name=lower('DB_RECOVERY_FILE_DEST')", 'Flashback area is a folder where all backup data and logs can be stored and managed by Oracle. If Error: message displayed, then it is not in use.'),

		'Flashback Usage' => array('BACKUP', "select nvl('-','Flashback Area not used') from v\$parameter where name=lower('DB_RECOVERY_FILE_DEST')", '=FlashUsage', 'Flashback area usage.'),

		'Control File Keep Time' => array('BACKUP', "select value from v\$parameter where name='control_file_record_keep_time'",'No of days to keep RMAN info in control file.  Recommended set to x2 or x3 times the frequency of your full backup.'),
		'Recent RMAN Jobs' => array('BACKUP', "select '-' from dual", "=RMAN"),

		//		'Control File Keep Time' => array('BACKUP', "select value from v\$parameter where name='control_file_record_keep_time'",'No of days to keep RMAN info in control file. I recommend it be set to x2 or x3 times the frequency of your full backup.'),
      'Storage', 'Tablespaces' => array('TABLESPACE', "select '-' from dual", "=TableSpace"),
		false

	);


	function __construct(&$conn)
	{
	global $gSQLBlockRows;

		$gSQLBlockRows = 1000;
		$savelog = $conn->LogSQL(false);
		$this->version = $conn->ServerInfo();
		$conn->LogSQL($savelog);
		$this->conn = $conn;
	}

	function LogMode()
	{
		$mode = $this->conn->GetOne("select log_mode from v\$database");

		if ($mode == 'ARCHIVELOG') return 'To turn off archivelog:<br>
	<pre><font size=-2>
        SQLPLUS> connect sys as sysdba;
        SQLPLUS> shutdown immediate;

        SQLPLUS> startup mount exclusive;
        SQLPLUS> alter database noarchivelog;
        SQLPLUS> alter database open;
</font></pre>';

		return 'To turn on archivelog:<br>
	<pre><font size=-2>
        SQLPLUS> connect sys as sysdba;
        SQLPLUS> shutdown immediate;

        SQLPLUS> startup mount exclusive;
        SQLPLUS> alter database archivelog;
        SQLPLUS> archive log start;
        SQLPLUS> alter database open;
</font></pre>';
	}

	function TopRecentWaits()
	{

		$rs = $this->conn->Execute("select * from (
		select event, round(100*time_waited/(select sum(time_waited) from v\$system_event where wait_class <> 'Idle'),1) \"% Wait\",
    total_waits,time_waited, average_wait,wait_class from v\$system_event where wait_class <> 'Idle' order by 2 desc
	) where rownum <=5");

		$ret = rs2html($rs,false,false,false,false);
		return "&nbsp;<p>".$ret."&nbsp;</p>";

	}

	function TopHistoricalWaits()
	{
		$days = 2;

		$rs = $this->conn->Execute("select * from (   SELECT
         b.wait_class,B.NAME,
        round(sum(wait_time+TIME_WAITED)/1000000) waitsecs,
        parsing_schema_name,
        C.SQL_TEXT, a.sql_id
FROM    V\$ACTIVE_SESSION_HISTORY A
        join V\$EVENT_NAME B  on  A.EVENT# = B.EVENT#
       join V\$SQLAREA C  on  A.SQL_ID = C.SQL_ID
WHERE   A.SAMPLE_TIME BETWEEN sysdate-$days and sysdate
       and parsing_schema_name not in ('SYS','SYSMAN','DBSNMP','SYSTEM')
GROUP BY b.wait_class,parsing_schema_name,C.SQL_TEXT, B.NAME,A.sql_id
order by 3 desc) where rownum <=10");

		$ret = rs2html($rs,false,false,false,false);
		return "&nbsp;<p>".$ret."&nbsp;</p>";

	}

	function TableSpace()
	{

		$rs = $this->conn->Execute(
	"select tablespace_name,round(sum(bytes)/1024/1024) as Used_MB,round(sum(maxbytes)/1024/1024) as Max_MB, round(sum(bytes)/sum(maxbytes),4) * 100 as PCT
	from dba_data_files
   group by tablespace_name order by 2 desc");

		$ret = "<p><b>Tablespace</b>".rs2html($rs,false,false,false,false);

		$rs = $this->conn->Execute("select * from dba_data_files order by tablespace_name, 1");
		$ret .= "<p><b>Datafile</b>".rs2html($rs,false,false,false,false);

		return "&nbsp;<p>".$ret."&nbsp;</p>";
	}

	function RMAN()
	{
		$rs = $this->conn->Execute("select * from (select start_time, end_time, operation, status, mbytes_processed, output_device_type
			from V\$RMAN_STATUS order by start_time desc) where rownum <=10");

		$ret = rs2html($rs,false,false,false,false);
		return "&nbsp;<p>".$ret."&nbsp;</p>";

	}

	function DynMemoryUsage()
	{
		if (@$this->version['version'] >= 11) {
			$rs = $this->conn->Execute("select component, current_size/1024./1024 as \"CurrSize (M)\" from  V\$MEMORY_DYNAMIC_COMPONENTS");

		} else
			$rs = $this->conn->Execute("select name, round(bytes/1024./1024,2) as \"CurrSize (M)\" from  V\$sgainfo");


		$ret = rs2html($rs,false,false,false,false);
		return "&nbsp;<p>".$ret."&nbsp;</p>";
	}

	function FlashUsage()
	{
        $rs = $this->conn->Execute("select * from  V\$FLASH_RECOVERY_AREA_USAGE");
		$ret = rs2html($rs,false,false,false,false);
		return "&nbsp;<p>".$ret."&nbsp;</p>";
	}

	function WarnPageCost($val)
	{
		if ($val == 100 && $this->version['version'] < 10) $s = '<font color=red><b>Too High</b>. </font>';
		else $s = '';

		return $s.'Recommended is 20-50 for TP, and 50 for data warehouses. Default is 100. See <a href=http://www.dba-oracle.com/oracle_tips_cost_adj.htm>optimizer_index_cost_adj</a>. ';
	}

	function WarnIndexCost($val)
	{
		if ($val == 0 && $this->version['version'] < 10) $s = '<font color=red><b>Too Low</b>. </font>';
		else $s = '';

		return $s.'Percentage of indexed data blocks expected in the cache.
			Recommended is 20 (fast disk array) to 30 (slower hard disks). Default is 0.
			 See <a href=http://www.dba-oracle.com/oracle_tips_cbo_part1.htm>optimizer_index_caching</a>.';
		}

	function PGA()
	{

		//if ($this->version['version'] < 9) return 'Oracle 9i or later required';
	}

	function PGA_Advice()
	{
		$t = "<h3>PGA Advice Estimate</h3>";
		if ($this->version['version'] < 9) return $t.'Oracle 9i or later required';

		$rs = $this->conn->Execute('select a.MB,
			case when a.targ = 1 then \'<<= Current \'
			when a.targ < 1  or a.pct <= b.pct then null
			else
			\'- BETTER than Current by \'||round(a.pct/b.pct*100-100,2)||\'%\' end as "Percent Improved",
	a.targ as  "PGA Size Factor",a.pct "% Perf"
	from
       (select round(pga_target_for_estimate/1024.0/1024.0,0) MB,
              pga_target_factor targ,estd_pga_cache_hit_percentage pct,rownum as r
              from v$pga_target_advice) a left join
       (select round(pga_target_for_estimate/1024.0/1024.0,0) MB,
              pga_target_factor targ,estd_pga_cache_hit_percentage pct,rownum as r
              from v$pga_target_advice) b on
      a.r = b.r+1 where
          b.pct < 100');
		if (!$rs) return $t."Only in 9i or later";
	//	$rs->Close();
		if ($rs->EOF) return $t."PGA could be too big";

		return $t.rs2html($rs,false,false,true,false);
	}

	function Explain($sql,$partial=false)
	{
		$savelog = $this->conn->LogSQL(false);
		$rs = $this->conn->SelectLimit("select ID FROM PLAN_TABLE");
		if (!$rs) {
			echo "<p><b>Missing PLAN_TABLE</b></p>
<pre>
CREATE TABLE PLAN_TABLE (
  STATEMENT_ID                    VARCHAR2(30),
  TIMESTAMP                       DATE,
  REMARKS                         VARCHAR2(80),
  OPERATION                       VARCHAR2(30),
  OPTIONS                         VARCHAR2(30),
  OBJECT_NODE                     VARCHAR2(128),
  OBJECT_OWNER                    VARCHAR2(30),
  OBJECT_NAME                     VARCHAR2(30),
  OBJECT_INSTANCE                 NUMBER(38),
  OBJECT_TYPE                     VARCHAR2(30),
  OPTIMIZER                       VARCHAR2(255),
  SEARCH_COLUMNS                  NUMBER,
  ID                              NUMBER(38),
  PARENT_ID                       NUMBER(38),
  POSITION                        NUMBER(38),
  COST                            NUMBER(38),
  CARDINALITY                     NUMBER(38),
  BYTES                           NUMBER(38),
  OTHER_TAG                       VARCHAR2(255),
  PARTITION_START                 VARCHAR2(255),
  PARTITION_STOP                  VARCHAR2(255),
  PARTITION_ID                    NUMBER(38),
  OTHER                           LONG,
  DISTRIBUTION                    VARCHAR2(30)
);
</pre>";
			return false;
		}

		$rs->Close();
	//	$this->conn->debug=1;

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

		$s = "<p><b>Explain</b>: ".htmlspecialchars($sql)."</p>";

		$this->conn->BeginTrans();
		$id = "ADODB ".microtime();

		$rs = $this->conn->Execute("EXPLAIN PLAN SET STATEMENT_ID='$id' FOR $sql");
		$m = $this->conn->ErrorMsg();
		if ($m) {
			$this->conn->RollbackTrans();
			$this->conn->LogSQL($savelog);
			$s .= "<p>$m</p>";
			return $s;
		}
		$rs = $this->conn->Execute("
		select
  '<pre>'||lpad('--', (level-1)*2,'-') || trim(operation) || ' ' || trim(options)||'</pre>'  as Operation,
  object_name,COST,CARDINALITY,bytes
		FROM plan_table
START WITH id = 0  and STATEMENT_ID='$id'
CONNECT BY prior id=parent_id and statement_id='$id'");

		$s .= rs2html($rs,false,false,false,false);
		$this->conn->RollbackTrans();
		$this->conn->LogSQL($savelog);
		$s .= $this->Tracer($sql,$partial);
		return $s;
	}

	function CheckMemory()
	{
		if ($this->version['version'] < 9) return 'Oracle 9i or later required';

		 $rs = $this->conn->Execute("
select  a.name Buffer_Pool, b.size_for_estimate as cache_mb_estimate,
	case when b.size_factor=1 then
   		'&lt;&lt;= Current'
	 when a.estd_physical_read_factor-b.estd_physical_read_factor > 0.001 and b.estd_physical_read_factor<1 then
		'- BETTER than current by ' || round((1-b.estd_physical_read_factor)/b.estd_physical_read_factor*100,2) || '%'
	else ' ' end as RATING,
   b.estd_physical_read_factor \"Phys. Reads Factor\",
   round((a.estd_physical_read_factor-b.estd_physical_read_factor)/b.estd_physical_read_factor*100,2) as \"% Improve\"
   from (select size_for_estimate,size_factor,estd_physical_read_factor,rownum  r,name from v\$db_cache_advice order by name,1) a ,
   (select size_for_estimate,size_factor,estd_physical_read_factor,rownum r,name from v\$db_cache_advice order by name,1) b
   where a.r = b.r-1 and a.name = b.name
  ");
		if (!$rs) return false;

		/*
		The v$db_cache_advice utility show the marginal changes in physical data block reads for different sizes of db_cache_size
		*/
		$s = "<h3>Data Cache Advice Estimate</h3>";
		if ($rs->EOF) {
			$s .= "<p>Cache that is 50% of current size is still too big</p>";
		} else {
			$s .= "Ideal size of Data Cache is when %BETTER gets close to zero.";
			$s .= rs2html($rs,false,false,false,false);
		}
		return $s.$this->PGA_Advice();
	}

	/*
		Generate html for suspicious/expensive sql
	*/
	function tohtml(&$rs,$type)
	{
		$o1 = $rs->FetchField(0);
		$o2 = $rs->FetchField(1);
		$o3 = $rs->FetchField(2);
		if ($rs->EOF) return '<p>None found</p>';
		$check = '';
		$sql = '';
		$s = "\n\n<table border=1 bgcolor=white><tr><td><b>".$o1->name.'</b></td><td><b>'.$o2->name.'</b></td><td><b>'.$o3->name.'</b></td></tr>';
		while (!$rs->EOF) {
			if ($check != $rs->fields[0].'::'.$rs->fields[1]) {
				if ($check) {
					$carr = explode('::',$check);
					$prefix = "<a href=\"?$type=1&sql=".rawurlencode($sql).'&x#explain">';
					$suffix = '</a>';
					if (strlen($prefix)>2000) {
						$prefix = '';
						$suffix = '';
					}

					$s .=  "\n<tr><td align=right>".$carr[0].'</td><td align=right>'.$carr[1].'</td><td>'.$prefix.$sql.$suffix.'</td></tr>';
				}
				$sql = $rs->fields[2];
				$check = $rs->fields[0].'::'.$rs->fields[1];
			} else
				$sql .= $rs->fields[2];
			if (substr($sql,strlen($sql)-1) == "\0") $sql = substr($sql,0,strlen($sql)-1);
			$rs->MoveNext();
		}
		$rs->Close();

		$carr = explode('::',$check);
		$prefix = "<a target=".rand()." href=\"?&hidem=1&$type=1&sql=".rawurlencode($sql).'&x#explain">';
		$suffix = '</a>';
		if (strlen($prefix)>2000) {
			$prefix = '';
			$suffix = '';
		}
		$s .=  "\n<tr><td align=right>".$carr[0].'</td><td align=right>'.$carr[1].'</td><td>'.$prefix.$sql.$suffix.'</td></tr>';

		return $s."</table>\n\n";
	}

	// code thanks to Ixora.
	// http://www.ixora.com.au/scripts/query_opt.htm
	// requires oracle 8.1.7 or later
	function SuspiciousSQL($numsql=10)
	{
		$sql = "
select
  substr(to_char(s.pct, '99.00'), 2) || '%'  load,
  s.executions  executes,
  p.sql_text
from
  (
    select
      address,
      buffer_gets,
      executions,
      pct,
      rank() over (order by buffer_gets desc)  ranking
    from
      (
	select
	  address,
	  buffer_gets,
	  executions,
	  100 * ratio_to_report(buffer_gets) over ()  pct
	from
	  sys.v_\$sql
	where
	  command_type != 47 and module != 'T.O.A.D.'
      )
    where
      buffer_gets > 50 * executions
  )  s,
  sys.v_\$sqltext  p
where
  s.ranking <= $numsql and
  p.address = s.address
order by
  1 desc, s.address, p.piece";

  		global $ADODB_CACHE_MODE;
  		if (isset($_GET['expsixora']) && isset($_GET['sql'])) {
				$partial = empty($_GET['part']);
				echo "<a name=explain></a>".$this->Explain($_GET['sql'],$partial)."\n";
		}

		if (isset($_GET['sql'])) return $this->_SuspiciousSQL($numsql);

		$s = '';
		$timer = time();
		$s .= $this->_SuspiciousSQL($numsql);
		$timer = time() - $timer;

		if ($timer > $this->noShowIxora) return $s;
		$s .= '<p>';

		$save = $ADODB_CACHE_MODE;
		$ADODB_CACHE_MODE = ADODB_FETCH_NUM;
		if ($this->conn->fetchMode !== false) $savem = $this->conn->SetFetchMode(false);

		$savelog = $this->conn->LogSQL(false);
		$rs = $this->conn->SelectLimit($sql);
		$this->conn->LogSQL($savelog);

		if (isset($savem)) $this->conn->SetFetchMode($savem);
		$ADODB_CACHE_MODE = $save;
		if ($rs) {
			$s .= "\n<h3>Ixora Suspicious SQL</h3>";
			$s .= $this->tohtml($rs,'expsixora');
		}

		return $s;
	}

	// code thanks to Ixora.
	// http://www.ixora.com.au/scripts/query_opt.htm
	// requires oracle 8.1.7 or later
	function ExpensiveSQL($numsql = 10)
	{
		$sql = "
select
  substr(to_char(s.pct, '99.00'), 2) || '%'  load,
  s.executions  executes,
  p.sql_text
from
  (
    select
      address,
      disk_reads,
      executions,
      pct,
      rank() over (order by disk_reads desc)  ranking
    from
      (
	select
	  address,
	  disk_reads,
	  executions,
	  100 * ratio_to_report(disk_reads) over ()  pct
	from
	  sys.v_\$sql
	where
	  command_type != 47 and module != 'T.O.A.D.'
      )
    where
      disk_reads > 50 * executions
  )  s,
  sys.v_\$sqltext  p
where
  s.ranking <= $numsql and
  p.address = s.address
order by
  1 desc, s.address, p.piece
";
		global $ADODB_CACHE_MODE;
  		if (isset($_GET['expeixora']) && isset($_GET['sql'])) {
			$partial = empty($_GET['part']);
			echo "<a name=explain></a>".$this->Explain($_GET['sql'],$partial)."\n";
		}
		if (isset($_GET['sql'])) {
			 $var = $this->_ExpensiveSQL($numsql);
			 return $var;
		}

		$s = '';
		$timer = time();
		$s .= $this->_ExpensiveSQL($numsql);
		$timer = time() - $timer;
		if ($timer > $this->noShowIxora) return $s;

		$s .= '<p>';
		$save = $ADODB_CACHE_MODE;
		$ADODB_CACHE_MODE = ADODB_FETCH_NUM;
		if ($this->conn->fetchMode !== false) $savem = $this->conn->SetFetchMode(false);

		$savelog = $this->conn->LogSQL(false);
		$rs = $this->conn->Execute($sql);
		$this->conn->LogSQL($savelog);

		if (isset($savem)) $this->conn->SetFetchMode($savem);
		$ADODB_CACHE_MODE = $save;

		if ($rs) {
			$s .= "\n<h3>Ixora Expensive SQL</h3>";
			$s .= $this->tohtml($rs,'expeixora');
		}

		return $s;
	}

	function clearsql()
	{
		$perf_table = adodb_perf::table();
	// using the naive "delete from $perf_table where created<".$this->conn->sysTimeStamp will cause the table to lock, possibly
	// for a long time
		$sql =
"DECLARE cnt pls_integer;
BEGIN
	cnt := 0;
	FOR rec IN (SELECT ROWID AS rr FROM $perf_table WHERE created<SYSDATE)
	LOOP
	  cnt := cnt + 1;
	  DELETE FROM $perf_table WHERE ROWID=rec.rr;
	  IF cnt = 1000 THEN
	  	COMMIT;
		cnt := 0;
	  END IF;
	END LOOP;
	commit;
END;";

		$ok = $this->conn->Execute($sql);
	}

}
