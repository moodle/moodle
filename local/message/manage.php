<?php

/**
 * Version details
 *
 * @package    local_message
 * @author  Albohtori
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */

require_once(__DIR__ . '/../../config.php');

global $DB;

$PAGE->set_url(new moodle_url('/local/message/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('manage_messages', 'local_message'));

$messages = $DB->get_records('local_message');

echo $OUTPUT->header();


$templatecontext = (object) [
    'messages' => array_values($messages),
    'editurl' => new moodle_url('/local/message/edit.php'),
];

echo $OUTPUT->render_from_template('local_message/manage', $templatecontext);

echo $OUTPUT->footer();
