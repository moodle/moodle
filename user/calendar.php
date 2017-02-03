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
 * @copyright 2015 Shamim Rezaie  http://foodle.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/lib.php');

$userid = optional_param('id', $USER->id, PARAM_INT);    // User id.

$PAGE->set_url('/user/calendar.php', array('id' => $userid));

list($user, $course) = useredit_setup_preference_page($userid, SITEID);

$defaultlookahead = CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD;
if (isset($CFG->calendar_lookahead)) {
    $defaultlookahead = intval($CFG->calendar_lookahead);
}
$defaultmaxevents = CALENDAR_DEFAULT_UPCOMING_MAXEVENTS;
if (isset($CFG->calendar_maxevents)) {
    $defaultmaxevents = intval($CFG->calendar_maxevents);
}

// Create form.
$calendarform = new core_user\form\calendar_form(null, array('userid' => $user->id));

$user->timeformat = get_user_preferences('calendar_timeformat', '');
$user->startwday  = calendar_get_starting_weekday();
$user->maxevents  = get_user_preferences('calendar_maxevents', $defaultmaxevents);
$user->lookahead  = get_user_preferences('calendar_lookahead', $defaultlookahead);
$user->persistflt = get_user_preferences('calendar_persistflt', 0);
$calendarform->set_data($user);

$redirect = new moodle_url("/user/preferences.php", array('userid' => $user->id));
if ($calendarform->is_cancelled()) {
    redirect($redirect);
} else if ($calendarform->is_submitted() && $calendarform->is_validated() && confirm_sesskey()) {
    $data = $calendarform->get_data();

    // Time format.
    if ($data->timeformat != CALENDAR_TF_12 && $data->timeformat != CALENDAR_TF_24) {
        $data->timeformat = '';
    }
    set_user_preference('calendar_timeformat', $data->timeformat);

    // Start weekday.
    $data->startwday = intval($data->startwday);
    if ($data->startwday < 0 || $data->startwday > 6) {
        $data->startwday = abs($data->startwday % 7);
    }
    set_user_preference('calendar_startwday', $data->startwday);

    // Calendar events.
    if (intval($data->maxevents) >= 1) {
        set_user_preference('calendar_maxevents', $data->maxevents);
    }

    // Calendar lookahead.
    if (intval($data->lookahead) >= 1) {
        set_user_preference('calendar_lookahead', $data->lookahead);
    }

    set_user_preference('calendar_persistflt', intval($data->persistflt));

    // Calendar type.
    $calendartype = $data->calendartype;
    // If the specified calendar type does not exist, use the site default.
    if (!array_key_exists($calendartype, \core_calendar\type_factory::get_list_of_calendar_types())) {
        $calendartype = $CFG->calendartype;
    }

    $user->calendartype = $calendartype;
    // Update user with new calendar type.
    user_update_user($user, false, false);

    // Trigger event.
    \core\event\user_updated::create_from_userid($user->id)->trigger();

    if ($USER->id == $user->id) {
        $USER->calendartype = $calendartype;
    }

    redirect($redirect);
}

// Display page header.
$streditmycalendar = get_string('calendarpreferences', 'calendar');
$userfullname     = fullname($user, true);

$PAGE->navbar->includesettingsbase = true;

$PAGE->set_title("$course->shortname: $streditmycalendar");
$PAGE->set_heading($userfullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($streditmycalendar);

// Finally display THE form.
$calendarform->display();

// And proper footer.
echo $OUTPUT->footer();
