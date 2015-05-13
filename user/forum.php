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
 * Allows you to edit a users forum preferences
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/user/forum_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/lib.php');

$userid = optional_param('id', $USER->id, PARAM_INT);    // User id.
$courseid = optional_param('course', SITEID, PARAM_INT);   // Course id (defaults to Site).

$PAGE->set_url('/user/forum.php', array('id' => $userid, 'course' => $courseid));

list($user, $course) = useredit_setup_preference_page($userid, $courseid);

// Create form.
$forumform = new user_edit_forum_form(null, array('userid' => $user->id));

$forumform->set_data($user);

$redirect = new moodle_url("/user/preferences.php", array('userid' => $user->id));
if ($forumform->is_cancelled()) {
    redirect($redirect);
} else if ($data = $forumform->get_data()) {

    $user->maildigest = $data->maildigest;
    $user->autosubscribe = $data->autosubscribe;
    $user->trackforums = $data->trackforums;

    user_update_user($user, false, false);

    // Trigger event.
    \core\event\user_updated::create_from_userid($user->id)->trigger();

    if ($USER->id == $user->id) {
        $USER->maildigest = $data->maildigest;
        $USER->autosubscribe = $data->autosubscribe;
        $USER->trackforums = $data->trackforums;
    }

    redirect($redirect);
}

// Display page header.
$streditmyforum = get_string('forumpreferences');
$userfullname     = fullname($user, true);

$PAGE->navbar->includesettingsbase = true;

$PAGE->set_title("$course->shortname: $streditmyforum");
$PAGE->set_heading($userfullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($streditmyforum);

// Finally display THE form.
$forumform->display();

// And proper footer.
echo $OUTPUT->footer();

