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
 * Mark a notification read and redirect to the relevant content.
 *
 * @package    message_popup
 * @copyright  2018 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

$notificationid = required_param('notificationid', PARAM_INT);
$redirecturl = optional_param('redirecturl', '', PARAM_URL);

$notification = $DB->get_record('message', array('id' => $notificationid, 'notification' => 1));

// If the redirect URL after filtering is empty, or it was never passed, then redirect to the notification page.
if (empty($redirecturl)) {
    $redirecturl = new moodle_url('/message/output/popup/notifications.php', ['notificationid' => $notificationid]);
}

// If found, is unread, so mark read if belongs to this user.
if ($notification) {
    if ($USER->id == $notification->useridto) {
        message_mark_message_read($notification, time());
    } else {
        $redirecturl = $CFG->wwwroot;
    }
}

redirect(new moodle_url($redirecturl));
