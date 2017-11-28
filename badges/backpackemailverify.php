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
 * Endpoint for the verification email link.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once(__DIR__ . '/lib/backpacklib.php');

$data = optional_param('data', '', PARAM_RAW);
require_login();
$PAGE->set_url('/badges/openbackpackemailverify.php');
$PAGE->set_context(context_user::instance($USER->id));
$redirect = '/badges/mybackpack.php';

// Confirm the secret and create the backpack connection.
$storedsecret = get_user_preferences('badges_email_verify_secret');
if (!is_null($storedsecret)) {
    if ($data === $storedsecret) {
        $storedemail = get_user_preferences('badges_email_verify_address');

        $data = new stdClass();
        $data->backpackurl = BADGE_BACKPACKURL;
        $data->email = $storedemail;
        $bp = new OpenBadgesBackpackHandler($data);

        // Make sure we have all the required information before trying to save the connection.
        $backpackuser = $bp->curl_request('user');
        if (isset($backpackuser->status) && $backpackuser->status === 'okay' && isset($backpackuser->userId)) {
            $backpackuid = $backpackuser->userId;
        } else {
            redirect(new moodle_url($redirect), get_string('backpackconnectionunexpectedresult', 'badges'),
                null, \core\output\notification::NOTIFY_ERROR);
        }

        $obj = new stdClass();
        $obj->userid = $USER->id;
        $obj->email = $data->email;
        $obj->backpackurl = $data->backpackurl;
        $obj->backpackuid = $backpackuid;
        $obj->autosync = 0;
        $obj->password = '';
        $DB->insert_record('badge_backpack', $obj);

        // Remove the verification vars and redirect to the mypackpack page.
        unset_user_preference('badges_email_verify_secret');
        unset_user_preference('badges_email_verify_address');
        redirect(new moodle_url($redirect), get_string('backpackemailverifysuccess', 'badges'),
            null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        // Stored secret doesn't match the supplied secret. Take user back to the mybackpack page and present a warning message.
        redirect(new moodle_url($redirect), get_string('backpackemailverifytokenmismatch', 'badges'),
            null, \core\output\notification::NOTIFY_ERROR);
    }
} else {
    // Stored secret is null. Either the email address has already been verified, or there is no record of a verification attempt
    // for the current user. Either way, just redirect to the mybackpack page.
    redirect(new moodle_url($redirect));
}
