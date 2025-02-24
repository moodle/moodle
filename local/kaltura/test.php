<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/locallib.php');

global $CFG, $USER, $PAGE;

require_login();

$context = context_system::instance();

$url = new moodle_url('/local/kaltura/test.php');

$PAGE->set_url($url);
$PAGE->set_context($context);

echo $OUTPUT->header();

require_capability('moodle/site:config', $context);

$session = local_kaltura_login(true, '', 2);

if ($session) {
    echo 'Connection successful';
} else {
    echo 'Connection not successful';
}
