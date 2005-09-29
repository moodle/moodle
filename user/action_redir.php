<?php
/*
 * Wrapper script redirecting user operations to correct destination
 */
require_once("../config.php");

if (empty($_GET) || empty($_GET['formaction']) || empty($_GET['id'])) {
    die();
}

// Add every page will be redirected by this script
$actions = array(
        'messageselect.php',
        'extendenrol.php'
    );

if (array_search($_GET['formaction'], $actions) === false) {
    die();
}

if (!confirm_sesskey()) {
    die();
}

$pass = false;
foreach ($_GET as $k => $v) {
    $pass = $pass || preg_match('/^user(\d+)$/',$k);
}
if (!$pass) {
    die();
}

header("Location: $CFG->wwwroot/user/" . $_GET['formaction'] . '?' . $_SERVER['QUERY_STRING']);
?>