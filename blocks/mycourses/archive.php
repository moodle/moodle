<?php
require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE
require_once('locallib.php');

require_login();

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

$context = context_system::instance();

$url = '/blocks/mycourses/archive.php';
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('archivetitle', 'block_mycourses'));
$PAGE->set_url($url);
$PAGE->set_heading($SITE->fullname);

$output = $PAGE->get_renderer('block_mycourses');

// Get the cut off date.
$cutoffdate = time() - ($CFG->mycourses_archivecutoff * 24 * 60 * 60);

$myarchive = mycourses_get_my_archive($cutoffdate);

echo $output->header();

echo $output->display_archive($myarchive);

echo $output->footer();
