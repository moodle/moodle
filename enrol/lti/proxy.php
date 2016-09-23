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
 * Tool proxy.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$toolid = null;
$token = null;
$filearguments = get_file_argument();
$arguments = explode('/', trim($filearguments, '/'));
if (count($arguments) == 2) {
    list($toolid, $token) = $arguments;
}

$toolid = optional_param('id', $toolid, PARAM_INT);
$token = optional_param('token', $token, PARAM_BASE64);

$messagetype = optional_param('lti_message_type', '', PARAM_TEXT);
$userid = optional_param('user_id', null, PARAM_INT);
$roles = optional_param('roles', null, PARAM_TEXT);
$tcprofileurl = optional_param('tc_profile_url', '', PARAM_URL);
$regkey = optional_param('reg_key', '', PARAM_URL);
$regpassword = optional_param('reg_password', '', PARAM_URL);
$launchpresentationreturnurl = optional_param('launch_presentation_return_url', '', PARAM_URL);

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/enrol/lti/tp.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('popup');
$PAGE->set_title(get_string('registration', 'enrol_lti'));

// Only show the cartridge if the token parameter is correct.
// If we do not compare with a shared secret, someone could very easily
// guess an id for the enrolment.
\enrol_lti\helper::verify_tool_token($toolid, $token);

$toolprovider = new \enrol_lti\tool_provider($toolid);
$toolprovider->handleRequest();
echo $OUTPUT->header();
echo $OUTPUT->footer();
