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
 * A page displaying the user's contact requests.
 *
 * This is a temporary (well, should be) page until the new UI is introduced for 3.6.
 *
 * @package    core_message
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot . '/message/externallib.php');

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

$userid = optional_param('userid', '', PARAM_INT); // The userid of the request.
$action = optional_param('action', '', PARAM_ALPHA);

// Confirm the request is able to be approved/disapproved.
if ($userid) {
    $request = $DB->get_record('message_contact_requests', ['userid' => $userid, 'requesteduserid' => $USER->id], '*', MUST_EXIST);
}

// Use external functions as these are what we will be using in the new UI.
if ($userid && $action && confirm_sesskey()) {
    if ($action == 'approve') {
        core_message_external::confirm_contact_request($request->userid, $USER->id);
    } else if ($action == 'decline') {
        core_message_external::decline_contact_request($request->userid, $USER->id);
    }

    redirect(new moodle_url('/message/pendingcontactrequests.php'));
}

$table = new html_table();

$headers = [];
$headers[] = '';
$headers[] = '';

$table->head = $headers;

// Use external functions as these are what we will be using in the new UI.
if ($contactrequests = core_message_external::get_contact_requests($USER->id)) {
    foreach ($contactrequests as $contactrequest) {
        $approvelink = new moodle_url('/message/pendingcontactrequests.php', ['userid' => $contactrequest->id,
            'action' => 'approve', 'sesskey' => sesskey()]);
        $declinelink = new moodle_url('/message/pendingcontactrequests.php', ['userid' => $contactrequest->id,
            'action' => 'decline', 'sesskey' => sesskey()]);

        $cells = array();
        $cells[] = $contactrequest->fullname;
        $cells[] = html_writer::link($approvelink, get_string('approve')) . " | " .
            html_writer::link($declinelink, get_string('cancel'));
        $table->data[] = new html_table_row($cells);
    }
}

$url = new moodle_url('/message/pendingcontactrequests.php');
$PAGE->set_url($url);

$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Pending contact requests');
$PAGE->set_heading('Pending contact requests');

echo $OUTPUT->header();
echo html_writer::table($table);
echo $OUTPUT->footer();
