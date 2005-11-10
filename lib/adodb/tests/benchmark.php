<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>ADODB Benchmarks</title>
</head> 

<body>
<?php 
/*
V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  
  Benchmark code to test the speed to the ADODB library with different databases.
  This is a simplistic benchmark to be used as the basis for further testing.
  It should not be used as proof of the superiority of one database over the other.
*/ 
 
$testmssql = true;
//$testvfp = true;
$testoracle = true;
$testado = true; 
$testibase = true;
$testaccess = true;
$testmysql = true;
$testsqlite = true;;

set_time_limit(240); // increase timeout

include("../tohtml.inc.php");
include("../adodb.inc.php");

function testdb(&$db,$createtab="create table ADOXYZ (id int, firstname char(24), lastname char(24), created date)")
{
GLOBAL $ADODB_version,$ADODB_FETCH_MODE;

	adodb_backtrace();
	
	$max = 100;
	$sql = 'select * from ADOXYZ';
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	
	//print "<h3>ADODB Version: $ADODB_version Host: <i>$db->host</i> &nbsp; Database: <i>$db->database</i></h3>";
	
	// perform query once to cache results so we are only testing throughput 
	$rs = $db->Execute($sql);
	if (!$rs){
		print "Error in recordset<p>";
		return;
	}	
	$arr = $rs->GetArray();
	//$db->debug = true;
	global $ADODB_COUNTRECS;
	$ADODB_COUNTRECS = false;
	$start = microtime();
	for ($i=0; $i < $max; $i++) {
		$rs =& $db->Execute($sql);	
		$arr =& $rs->GetArray();
	   //		 print $arr[0][1];
	}
	$end =  microtime();
	$start = explode(' ',$start);
	$end = explode(' ',$end);
	
	//print_r($start);
	//print_r($end);
	
	  //  print_r($arr);
	$total = $end[0]+trim($end[1]) - $start[0]-trim($start[1]);
	printf ("<p>seconds = %8.2f for %d iterations each with %d records</p>",$total,$max, sizeof($arr));
	flush();


		//$db->Close();
}
include("testdatabases.inc.php");

?>


</body>
</html>
