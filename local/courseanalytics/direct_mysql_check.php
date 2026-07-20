<?php
// Direct mysqli check bypassing Moodle DB layer
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
// Check config_plugins for our plugin
$res = $mysqli->query("SELECT name, value FROM {$prefix}config_plugins WHERE plugin='local_courseanalytics'");
if (!$res) {
    echo "query_err=" . $mysqli->error . "\n";
} else {
    if ($res->num_rows == 0) {
        echo "no_config_entries\n";
    } else {
        while ($row = $res->fetch_assoc()) {
            echo $row['name'] . "=" . $row['value'] . "\n";
        }
    }
}
$mysqli->close();
?>