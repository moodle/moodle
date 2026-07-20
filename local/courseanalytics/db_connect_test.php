<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
$host = $CFG->dbhost;
$port = isset($CFG->dboptions['dbport']) ? $CFG->dboptions['dbport'] : null;
$user = $CFG->dbuser;
$pass = $CFG->dbpass;
$db = $CFG->dbname;
$mysqli = mysqli_init();
$connected = @$mysqli->real_connect($host, $user, $pass, $db, $port);
if ($connected) {
    echo "mysqli_connected\n";
    $mysqli->close();
} else {
    echo "mysqli_error=" . mysqli_connect_error() . "\n";
}
?>