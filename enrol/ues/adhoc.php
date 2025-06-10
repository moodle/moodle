<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The core of the Universal Enrollment System constellation of plugins.
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the required files.
require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');

// Ensure the user is logged in.
require_login();

// Make sure the user is admin otherwise redirect them home.
if (!is_siteadmin($USER->id)) {
    redirect(new moodle_url('/my'));
}

// Set up some varuables for use later.
$confirmed = optional_param('confirmed', null, PARAM_INT);
$success = optional_param('success', null, PARAM_INT);

// Build a quicker string system.
$s = ues::gen_str();

// Set the plugin name.
$pluginname = $s('pluginname');

$action = $s('run_adhoc');

$baseurl = new moodle_url('/admin/settings.php', array(
    'section' => 'enrolsettingsues'
));

// Set the page up.
$PAGE->set_context(context_system::instance());
$PAGE->set_title($pluginname . ': '. $action);
$PAGE->set_heading($pluginname . ': '. $action);
$PAGE->set_url('/enrol/ues/adhoc.php');
$PAGE->set_pagetype('admin-settings-ues-semester-adhoc');
$PAGE->set_pagelayout('admin');

// Set up the navigation bar.
$PAGE->navbar->add($pluginname, $baseurl);
$PAGE->navbar->add($action);

if ($confirmed and $data = data_submitted()) {

    // Create the scheduled task instance.
    $fullprocess = new \enrol_ues\task\full_process_adhoc();

    // Queue the task.
    \core\task\manager::queue_adhoc_task($fullprocess);

    // Now that the task is schduled and queued, redirect.
    redirect(new moodle_url('/enrol/ues/adhoc.php?success=1'));
    exit;
}

// Begin outputting the page.
echo $OUTPUT->header();
echo $OUTPUT->heading($action);

echo html_writer::tag('p', $s('run_adhoc_desc'));

// Provide a success notification.
if ($success) {
    $internalnotification = $OUTPUT->notification($s('run_adhoc_success'), 'notifysuccess'); // Fix for success notification.
} else {
    $internalnotification = ''; // Fix for success notification.
}

// Define the confirm URL.
$confirmurl = new moodle_url('/enrol/ues/adhoc.php', array(
    'confirmed' => 1
));

// Define the cancel URL.
$cancelurl = new moodle_url('/admin/settings.php?section=enrolsettingsues');

// Generate a status/confirmation message.
$taskstatusdescription = ues::get_task_status_description();

// Id there is a status/confirmation, display it.
if ($taskstatusdescription) {
    $confirmmsg = $taskstatusdescription . '<br><br>' . $s('run_adhoc_confirm_msg');
} else {
    $confirmmsg = $s('run_adhoc_confirm_msg');
}

// Finish outputting the page.
if ($success) {
    echo $OUTPUT->confirm('Click \'Go Back\' to return to the UES Enrollment page.', $confirmurl, $cancelurl, $internalnotification);
} else {
    echo $OUTPUT->confirm($confirmmsg, $confirmurl, $cancelurl, $internalnotification);
}

echo $OUTPUT->footer();