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
 * List of deprecated mod_resource functions.
 *
 * @package   mod_resource
 * @copyright 2021 Peter D
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Print resource heading.
 *
 * @deprecated since Moodle 4.0
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @param bool $notused This variable is no longer used
 * @return void
 */
function resource_print_heading($resource, $cm, $course, $notused = false) {
    global $OUTPUT;
    debugging('resource_print_heading is deprecated. Handled by activity_header now.', DEBUG_DEVELOPER);
    echo $OUTPUT->heading(format_string($resource->name), 2);
}

/**
 * Print resource introduction.
 *
 * @deprecated since Moodle 4.0
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function resource_print_intro($resource, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;
    debugging('resource_print_intro is deprecated. Handled by activity_header now.', DEBUG_DEVELOPER);
    if ($intro = resource_get_intro($resource, $cm, $ignoresettings)) {
        echo $OUTPUT->box_start('mod_introbox', 'resourceintro');
        echo $intro;
        echo $OUTPUT->box_end();
    }
}
