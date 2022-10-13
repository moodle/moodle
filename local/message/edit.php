<?php

/**
 * Version details
 *
 * @package    local_message
 * @author  Albohtori
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use local_message\form\edit;
use local_message\manager;

require_once(__DIR__ . '/../../config.php');
global $DB;

$PAGE->set_url(new moodle_url('/local/message/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('edit_messages', 'local_message'));

// we want to display our form
$mform = new edit();


//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //go back to manage.php page
    redirect($CFG->wwwroot . '/local/message/manage.php', get_string('cancelled_form', 'local_message'));
} else if ($fromform = $mform->get_data()) {
    // insert data into database table

    $manager = new manager();
    $manager->create_message($fromform->messagetext, $fromform->messagetype);


    //go back to manage.php page
    redirect($CFG->wwwroot . '/local/message/manage.php', get_string('created_form', 'local_message') . $fromform->messagetext);
}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
