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
 * Sets the events filter for the calendar view.
 *
 * @package   core_calendar
 * @copyright 2003 Jon Papaioannou (pj@moodle.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');

$var = required_param('var', PARAM_ALPHA);
$return = clean_param(base64_decode(required_param('return', PARAM_RAW)), PARAM_LOCALURL);
$courseid = optional_param('id', -1, PARAM_INT);
if ($courseid != -1) {
    $return = new moodle_url($return, array('course' => $courseid));
} else {
    $return = new moodle_url($return);
}

if (!confirm_sesskey()) {
    // Do not call require_sesskey() since this page may be accessed without session (for example by bots).
    redirect($return);
}

$url = new moodle_url('/calendar/set.php', array('return'=>base64_encode($return->out_as_local_url(false)), 'course' => $courseid, 'var'=>$var, 'sesskey'=>sesskey()));
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

switch($var) {
    case 'showgroups':
        calendar_set_event_type_display(CALENDAR_EVENT_GROUP);
        break;
    case 'showcourses':
        calendar_set_event_type_display(CALENDAR_EVENT_COURSE);
        break;
    case 'showglobal':
        calendar_set_event_type_display(CALENDAR_EVENT_GLOBAL);
        break;
    case 'showuser':
        calendar_set_event_type_display(CALENDAR_EVENT_USER);
        break;
}

redirect($return);
