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
 * A page displaying the user's contacts and messages
 *
 * @package    core_message
 * @copyright  2010 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

// The id of the user we want to view messages from.
$id = optional_param('id', 0, PARAM_INT);
// It's possible a user may come from a link where these parameters are specified.
// We no longer support viewing another user's messaging area (that can be achieved
// via the 'Log-in as' feature). The 'user2' value takes preference over 'id'.
$userid = optional_param('user2', $id, PARAM_INT);

if (!core_user::is_real_user($userid)) {
    $userid = null;
}

if ($userid) {
    $recipient = new stdClass();
    $recipient->id = $userid;
    if (!\core_message\api::can_post_message($recipient)) {
        throw new moodle_exception('Can not contact user');
    }
}

$url = new moodle_url('/message/index.php');
if ($userid) {
    $url->param('id', $userid);
}
$PAGE->set_url($url);
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');

$strmessages = get_string('messages', 'message');

$PAGE->set_title("$strmessages");
$PAGE->set_heading("$strmessages");

// Remove the user node from the main navigation for this page.
$usernode = $PAGE->navigation->find('users', null);
$usernode->remove();

$settings = $PAGE->settingsnav->find('messages', null);
$settings->make_active();

// Check if there is an existing conversation with the supplied user (if there is one).
$conversationid = null;
if ($userid) {
    $conversationid = \core_message\api::get_conversation_between_users([$USER->id, $userid]);
}

echo $OUTPUT->header();
// Display a message if the messages have not been migrated yet.
if (!get_user_preferences('core_message_migrate_data', false)) {
    $notify = new \core\output\notification(get_string('messagingdatahasnotbeenmigrated', 'message'),
        \core\output\notification::NOTIFY_WARNING);
    echo $OUTPUT->render($notify);
}
echo $OUTPUT->heading(get_string('messages', 'message'));
echo \core_message\helper::render_messaging_widget(false, $userid, $conversationid);
echo $OUTPUT->footer();
