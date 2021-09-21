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
 * Moodle 404 Error page
 *
 * This is for 404 error pages served by the webserver and then passed
 * to Moodle to be rendered using the site theme.
 *
 * ErrorDocument 404 /error/index.php
 *
 * @package    core
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php'); // phpcs:ignore

// Until we have a more robust routing api in place this is a very simple
// and clean way to handle arbitrary urls without a php extension.
if ($ME === '/.well-known/change-password') {
    redirect(new moodle_url('/login/change_password.php'));
}

$context = context_system::instance();
$title = get_string('pagenotexisttitle', 'error');
$PAGE->set_url('/error/index.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);

// This allows the webserver to dictate wether the http status should remain
// what it would have been, or force it to be a 404. Under other conditions
// it could most often be a 403, 405 or a 50x error.
$code = optional_param('code', 0, PARAM_INT);
if ($code == 404) {
    header("HTTP/1.0 404 Not Found");
}

$canmessage = has_capability('moodle/site:senderrormessage', $context);

$supportuser = core_user::get_support_user();

// We can only message support if both the user has the capability
// and the support user is a real user.
if ($canmessage) {
    $canmessage = core_user::is_real_user($supportuser->id);
}

$mform = new \core\form\error_feedback($CFG->wwwroot . '/error/index.php');

if ($data = $mform->get_data()) {

    if (!$canmessage) {
        redirect($CFG->wwwroot);
    }

    // Send the message and redirect.
    $message = new \core\message\message();
    $message->courseid         = SITEID;
    $message->component        = 'moodle';
    $message->name             = 'errors';
    $message->userfrom          = $USER;
    $message->userto            = core_user::get_support_user();
    $message->subject           = 'Error: '. $data->referer .' -> '. $data->requested;
    $message->fullmessage       = $data->text;
    $message->fullmessageformat = FORMAT_PLAIN;
    $message->fullmessagehtml   = '';
    $message->smallmessage      = '';
    $message->contexturl = $data->requested;
    message_send($message);

    redirect($CFG->wwwroot, get_string('sendmessagesent', 'error', $data->requested), 5);
    exit;
}

echo $OUTPUT->header();
echo $OUTPUT->notification(get_string('pagenotexist', 'error', s($ME)), 'error');
echo $OUTPUT->supportemail(['class' => 'text-center d-block mb-3 font-weight-bold']);

if ($canmessage) {
    echo \html_writer::tag('h4', get_string('sendmessage', 'error'));
    $mform->display();
} else {
    echo $OUTPUT->continue_button($CFG->wwwroot);
}

echo $OUTPUT->footer();
