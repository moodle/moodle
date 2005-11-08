<?php
/*
 * Wrapper script redirecting user operations to correct destination
 */
require_once("../config.php");

if (empty($_POST) || empty($_POST['formaction']) || empty($_POST['id'])) {
    die();
}

// Add every page will be redirected by this script
$actions = array(
        'messageselect.php',
        'extendenrol.php'
    );

if (array_search($_POST['formaction'], $actions) === false) {
    die();
}

if (!confirm_sesskey()) {
    die();
}

require_once($_POST['formaction']);
?>