<?php

include_once('../adodb-perf.inc.php');

error_reporting(E_ALL);
session_start();

if (isset($_GET)) {
	foreach($_GET as $k => $v) {
		if (strncmp($k,'test',4) == 0) $_SESSION['_db'] = $k;
	}
} 

if (isset($_SESSION['_db'])) {
	$_db = $_SESSION['_db'];
	$_GET[$_db] = 1;
	$$_db = 1;
}

echo "<h1>Performance Monitoring</h1>";
include_once('testdatabases.inc.php');


function testdb($db) 
{
	if (!$db) return;
	echo "<font size=1>";print_r($db->ServerInfo()); echo " user=".$db->user."</font>";
	
	$perf = NewPerfMonitor($db); 
	
	# unit tests
	if (0) {
		//$DB->debug=1;
		echo "Data Cache Size=".$perf->DBParameter('data cache size').'<p>';
		echo $perf->HealthCheck();
		echo($perf->SuspiciousSQL());
		echo($perf->ExpensiveSQL());
		echo($perf->InvalidSQL());
		echo $perf->Tables();
	
		echo "<pre>";
		echo $perf->HealthCheckCLI();
		$perf->Poll(3);
		die();
	}
	
	if ($perf) $perf->UI(3);
}
 
?>
