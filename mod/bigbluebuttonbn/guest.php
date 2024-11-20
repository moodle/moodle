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
 * Guest access implementation
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */

use mod_bigbluebuttonbn\form\guest_login;
use mod_bigbluebuttonbn\local\exceptions\server_not_available_exception;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\plugin;

require(__DIR__.'/../../config.php');
global $PAGE, $OUTPUT, $DB, $SITE;
// Note here that we do not use require_login as the $CFG->forcelogin would prevent guest user from accessing this page.
$PAGE->set_course($SITE); // Intialise the page and run through the setup.
$uid = required_param('uid', PARAM_ALPHANUMEXT);

$bbid = $DB->get_field('bigbluebuttonbn', 'id', ['guestlinkuid' => trim($uid)]);
if (empty($bbid)) {
    throw new moodle_exception('guestaccess_activitynotfound', 'mod_bigbluebuttonbn');
}
$instance = \mod_bigbluebuttonbn\instance::get_from_instanceid($bbid);
// Prevent access to this page if the guest access has been disabled on this instance.
if (!$instance->is_guest_allowed()) {
    throw new moodle_exception('guestaccess_feature_disabled', 'mod_bigbluebuttonbn');
}

// Get the guest matching guest access link.
$PAGE->set_url('/mod/bigbluebuttonbn/guest.php', ['uid' => $uid]);
$title = $instance->get_course()->shortname . ': ' . format_string($instance->get_meeting_name());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');
$form = new guest_login(null, ['uid' => $uid, 'instance' => $instance]);
// Specific for the tests: we allow to set the password in the form here.
if (defined('BEHAT_SITE_RUNNING')) {
    $form->set_data(['password' => optional_param('password', '', PARAM_RAW)]);
}

if ($data = $form->get_data()) {
    $username = $data->username;
    try {
        $meeting = new meeting($instance);
        // As the meeting doesn't exist, we raise an exception.
        if (!empty($meeting->get_meeting_info()->createtime)) {
            $url = $meeting->get_guest_join_url($username);
            redirect($url);
        } else {
            \core\notification::add(
                get_string('guestaccess_meeting_not_started', 'mod_bigbluebuttonbn'),
                \core\output\notification::NOTIFY_ERROR
            );
        }
    } catch (server_not_available_exception $e) {
        bigbluebutton_proxy::handle_server_not_available($instance);
    }
}
echo $OUTPUT->header();
echo $form->render();
echo $OUTPUT->footer();
