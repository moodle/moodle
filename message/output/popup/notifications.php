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
 * View a user's notifications.
 *
 * @package    message_popup
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$notificationid = optional_param('notificationid', 0, PARAM_INT);
$offset = optional_param('offset', 0, PARAM_INT);
$limit = optional_param('limit', 0, PARAM_INT);
$userid = $USER->id;

$url = new moodle_url('/message/output/popup/notifications.php');
$url->param('id', $notificationid);

$PAGE->set_url($url);

require_login();

if (isguestuser()) {
    print_error('guestnoeditmessage', 'message');
}

if (!$user = $DB->get_record('user', ['id' => $userid])) {
    print_error('invaliduserid');
}

$personalcontext = context_user::instance($user->id);

$PAGE->set_context($personalcontext);
$PAGE->set_pagelayout('admin');

// Display page header.
$title = get_string('notifications', 'message');
$PAGE->set_title("{$SITE->shortname}: " . $title);
$PAGE->set_heading(fullname($user));

// Grab the renderer.
$renderer = $PAGE->get_renderer('core', 'message');
$context = [
    'notificationid' => $notificationid,
    'userid' => $userid,
    'limit' => $limit,
    'offset' => $offset,
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('notifications', 'message'));

// Display a message if the notifications have not been migrated yet.
if (!get_user_preferences('core_message_migrate_data', false, $userid)) {
    $notify = new \core\output\notification(get_string('notificationdatahasnotbeenmigrated', 'message'),
        \core\output\notification::NOTIFY_WARNING);
    echo $OUTPUT->render($notify);
}

echo $renderer->render_from_template('message_popup/notification_area', $context);
echo $OUTPUT->footer();

