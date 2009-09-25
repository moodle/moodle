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
 * Allows a user to request a course be created for them.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/course/request_form.php');

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/course/request.php'));

/// Where we came from. Used in a number of redirects.
$returnurl = $CFG->wwwroot . '/course/index.php';

/// Check permissions.
require_login();
if (isguestuser()) {
    print_error('guestsarenotallowed', '', $returnurl);
}
if (empty($CFG->enablecourserequests)) {
    print_error('courserequestdisabled', '', $returnurl);
}
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/course:request', $systemcontext);

/// Set up the form.
$requestform = new course_request_form($CFG->wwwroot . '/course/request.php');

$strtitle = get_string('courserequest');

/// Standard form processing if statement.
if ($requestform->is_cancelled()){
    redirect($returnurl);

} else if ($data = $requestform->get_data()) {
    $PAGE->set_title($strtitle);
    $PAGE->set_heading($strtitle);
    $PAGE->navbar->add($strtitle);
    $PAGE->set_focuscontrol($requestform->focus());
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strtitle);

/// Record the request.
    $data->requester = $USER->id;
    $DB->insert_record('course_request', $data);

/// Notify the admin if required.
    if ($CFG->courserequestnotify) {
        $users = get_users_from_config($CFG->courserequestnotify, 'moodle/site:approvecourse');
        foreach ($users as $user) {
            $eventdata = new object();
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'course';
            $eventdata->name              = 'courserequested';
            $eventdata->userfrom          = $USER;
            $eventdata->userto            = $user;
            $eventdata->subject = get_string('courserequest');
            $a = new object();
            $a->link = "$CFG->wwwroot/course/pending.php";
            $a->user = fullname($USER);
            $eventdata->fullmessage = get_string('courserequestnotifyemail', 'admin', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            events_trigger('message_send', $eventdata);
        }
    }

/// and redirect back to the course listing.
    notice(get_string('courserequestsuccess'), $returnurl);
}

/// Show the request form.

$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
$PAGE->navbar->add($strtitle);
$PAGE->set_focuscontrol($requestform->focus());
echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
$requestform->display();
echo $OUTPUT->footer();

?>