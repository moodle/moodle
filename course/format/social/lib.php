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
 * This file contains general functions for the course format Social
 *
 * @since 2.0
 * @package moodlecore
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Used to display the course structure for a course where format=social
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = weeks.
 *
 * @param array $path An array of keys to the course node in the navigation
 * @param stdClass $modinfo The mod info object for the current course
 * @return bool Returns true
 */
function callback_social_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'social');
}

/**
 * Used to display the course structure for a course where format=social
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = weeks and the navigation was requested via AJAX
 *
 * @param array $path An array of keys to the course node in the navigation
 * @param stdClass $modinfo The mod info object for the current course
 * @return bool Returns true
 */
function limited_load_section_social(&$navigation, $keys, $course, $section) {
    $navigation->limited_load_section_generic($keys, $course, $section, get_string('social'), 'social');
}

/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_social_uses_sections() {
    return false;
}

/**
 * The string that is used to describe a section of the course
 * e.g. Topic, Week...
 *
 * @return string
 */
function callback_social_definition() {
    return get_string('topic');
}

/**
 * The GET argument variable that is used to identify the section being
 * viewed by the user (if there is one)
 *
 * @return string
 */
function callback_social_request_key() {
    return 'social';
}

/**
 * Toogle display of course contents (sections, activities)
 *
 * @return bool
 */
function callback_social_display_content() {
    return false;
}


