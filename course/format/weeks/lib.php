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
 * This file contains general functions for the course format Week
 *
 * @since 2.0
 * @package moodlecore
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_weeks_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=weeks
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = weeks.
 *
 * @param navigation_node $navigation The course node
 * @param array $path An array of keys to the course node
 * @param stdClass $course The course we are loading the section for
 */
function callback_weeks_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'weeks');
}

/**
 * The string that is used to describe a section of the course
 * e.g. Topic, Week...
 *
 * @return string
 */
function callback_weeks_definition() {
    return get_string('week');
}

/**
 * Gets the name for the provided section.
 *
 * @param stdClass $course
 * @param stdClass $section
 * @return string
 */
function callback_weeks_get_section_name($course, $section) {
    // We can't add a node without text
    if ((string)$section->name !== '') {
        // Return the name the user set.
        return format_string($section->name, true, array('context' => context_course::instance($course->id)));
    } else if ($section->section == 0) {
        // Return the general section.
        return get_string('section0name', 'format_weeks');
    } else {
        $dates = format_weeks_get_section_dates($section, $course);

        // We subtract 24 hours for display purposes.
        $dates->end = ($dates->end - 86400);

        $dateformat = ' '.get_string('strftimedateshort');
        $weekday = userdate($dates->start, $dateformat);
        $endweekday = userdate($dates->end, $dateformat);
        return $weekday.' - '.$endweekday;
    }
}

/**
 * Declares support for course AJAX features
 *
 * @see course_format_ajax_support()
 * @return stdClass
 */
function callback_weeks_ajax_support() {
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = true;
    $ajaxsupport->testedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Safari' => 531, 'Chrome' => 6.0);
    return $ajaxsupport;
}

/**
 * Return the start and end date of the passed section
 *
 * @param stdClass $section The course_section entry from the DB
 * @param stdClass $course The course entry from DB
 * @return stdClass property start for startdate, property end for enddate
 */
function format_weeks_get_section_dates($section, $course) {
    $oneweekseconds = 604800;
    // Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
    // savings and the date changes.
    $startdate = $course->startdate + 7200;

    $dates = new stdClass();
    $dates->start = $startdate + ($oneweekseconds * ($section->section - 1));
    $dates->end = $dates->start + $oneweekseconds;

    return $dates;
}

/**
 * Callback function to do some action after section move
 *
 * @param stdClass $course The course entry from DB
 * @return array This will be passed in ajax respose.
 */
function callback_weeks_ajax_section_move($course) {
    global $COURSE, $PAGE;

    $titles = array();
    rebuild_course_cache($course->id);
    $modinfo = get_fast_modinfo($COURSE);
    $renderer = $PAGE->get_renderer('format_weeks');
    if ($renderer && ($sections = $modinfo->get_section_info_all())) {
        foreach ($sections as $number => $section) {
            $titles[$number] = $renderer->section_title($section, $course);
        }
    }
    return array('sectiontitles' => $titles, 'action' => 'move');
}
