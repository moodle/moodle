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
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/user/language_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/lib.php');

$userid = optional_param('id', $USER->id, PARAM_INT);    // User id.
$courseid = optional_param('course', SITEID, PARAM_INT);   // Course id (defaults to Site).

$PAGE->set_url('/user/language.php', array('id' => $userid, 'course' => $courseid));

list($user, $course) = useredit_setup_preference_page($userid, $courseid);

// Create form.
$languageform = new user_edit_language_form(null, array('userid' => $user->id));
$languageform->set_data($user);

$redirect = new moodle_url("/user/preferences.php", array('userid' => $user->id));
if ($languageform->is_cancelled()) {
    redirect($redirect);
} else if ($data = $languageform->get_data()) {
    $lang = $data->lang;
    // If the specified language does not exist, use the site default.
    if (!get_string_manager()->translation_exists($lang, false)) {
        $lang = core_user::get_property_default('lang');
    }

    $user->lang = $lang;
    // Update user with new language.
    user_update_user($user, false, false);

    // Trigger event.
    \core\event\user_updated::create_from_userid($user->id)->trigger();

    if ($USER->id == $user->id) {
        $USER->lang = $lang;
    }

    redirect($redirect, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Display page header.
$streditmylanguage = get_string('preferredlanguage');
$userfullname     = fullname($user, true);

$PAGE->navbar->includesettingsbase = true;

$PAGE->set_title("$course->shortname: $streditmylanguage");
$PAGE->set_heading($userfullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($streditmylanguage);

// Finally display THE form.
$languageform->display();

// And proper footer.
echo $OUTPUT->footer();

