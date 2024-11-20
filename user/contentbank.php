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
 * Allows you to edit a users profile
 *
 * @copyright 2020 François Moreau
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/lib.php');

require_login();

$userid = optional_param('id', $USER->id, PARAM_INT);    // User id.

$PAGE->set_url('/user/contentbank.php', ['id' => $userid]);

list($user, $course) = useredit_setup_preference_page($userid, SITEID);

$form = new \core_user\form\contentbank_user_preferences_form(null, ['userid' => $user->id]);

$user->contentvisibility = get_user_preferences('core_contentbank_visibility',
    $CFG->defaultpreference_core_contentbank_visibility, $user->id);

$form->set_data($user);

$redirect = new moodle_url("/user/preferences.php", ['userid' => $user->id]);

if ($form->is_cancelled()) {
    redirect($redirect);
} else if ($data = $form->get_data()) {
    $data = $form->get_data();
    $usernew = [
        'id' => $user->id,
        'preference_core_contentbank_visibility' => $data->contentvisibility
    ];
    useredit_update_user_preference($usernew);

    \core\event\user_updated::create_from_userid($user->id)->trigger();
    redirect($redirect);
}

$title = get_string('contentbankpreferences', 'core_contentbank');
$userfullname = fullname($user, true);

$PAGE->navbar->includesettingsbase = true;

$PAGE->set_title("$course->shortname: $title");
$PAGE->set_heading($userfullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
$form->display();
echo $OUTPUT->footer();
