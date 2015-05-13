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
 * Ajax point of entry for messaging API.
 *
 * @package    core_message
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require('../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/lib.php');

// Only real logged in users.
require_login(null, false, null, true, true);
if (isguestuser()) {
    throw new require_login_exception();
}

// Messaging needs to be enabled.
if (empty($CFG->messaging)) {
    throw new moodle_exception('disabled', 'core_message');
}

require_sesskey();
$action = optional_param('action', null, PARAM_ALPHA);
$response = null;

switch ($action) {

    // Sending a message.
    case 'sendmessage':

        require_capability('moodle/site:sendmessage', context_system::instance());

        $userid = required_param('userid', PARAM_INT);
        if (empty($userid) || isguestuser($userid) || $userid == $USER->id) {
            // Cannot send messags to self, nobody or a guest.
            throw new coding_exception('Invalid user to send the message to');
        }

        $message = required_param('message', PARAM_RAW);
        $user2 = core_user::get_user($userid);
        $messageid = message_post_message($USER, $user2, $message, FORMAT_MOODLE);

        if (!$messageid) {
            throw new moodle_exception('errorwhilesendingmessage', 'core_message');
        }

        $response = array();
        break;
}

if ($response !== null) {
    echo json_encode($response);
    exit();
}

throw new coding_exception('Invalid request');
