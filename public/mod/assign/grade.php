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
 * Handles the "Grade analysis" and "View Assignment results" links from the gradebook reports.
 *
 * @package   mod_assign
 * @copyright 2025 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

$cmid = required_param('id', PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

[$course, $cm] = get_course_and_cm_from_cmid($cmid);
require_login($course, false, $cm);
$assign = new assign(\core\context\module::instance($cmid), $cm, $course);

if ($assign->can_view_grades()) {
    // If a userid is provided (E.g. when performing grade analysis for a specific student), go to the grader pane.
    // Otherwise, show the submissions view.
    redirect(new moodle_url(
        '/mod/assign/view.php',
        [
            'id' => $cmid,
            ...($userid != 0) ? ['action' => 'grader'] : ['action' => 'grading'],
            ...($userid != 0) ? ['userid' => $userid] : [],
        ]
    ));
} else {
    // Students just see the submission view.
    redirect(new moodle_url(
        '/mod/assign/view.php',
        [
            'id' => $cmid,
            ...($userid != 0) ? ['userid' => $userid] : [],
        ]
    ));
}
