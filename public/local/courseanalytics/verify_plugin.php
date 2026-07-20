<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
$host = $CFG->dbhost;
$port = isset($CFG->dboptions['dbport']) ? $CFG->dboptions['dbport'] : null;
$user = $CFG->dbuser;
$pass = $CFG->dbpass;
$db = $CFG->dbname;
$prefix = $CFG->prefix;
$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    echo "connect_err=" . $mysqli->connect_error . "\n";
    exit;
}
$sql = "SELECT name, value FROM {$prefix}config_plugins WHERE plugin='local_courseanalytics' ORDER BY name";
$res = $mysqli->query($sql);
if (!$res) {
    echo "query_err=" . $mysqli->error . "\n";
} else {
    if ($res->num_rows == 0) {
        echo "not_registered\n";
    } else {
        echo "registered\n";
        while ($row = $res->fetch_assoc()) {
            echo $row['name'] . "=" . $row['value'] . "\n";
        }
    }
}
$mysqli->close();
?>