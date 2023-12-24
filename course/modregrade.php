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
 * Regrades a module that has been added or updated when it might take a long time.
 *
 * A progress bar will be displayed in that case, and then the user can click 'Continue' to proceed.
 * If for some reason you get to this page when regrading will not take a long time, then it will
 * redirect immediately.
 *
 * @package core_course
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);
require('../config.php');
require_once($CFG->libdir . '/gradelib.php');

$cmid = required_param('id', PARAM_INT);
$urlpath = required_param('url', PARAM_LOCALURL);
$url = new moodle_url($urlpath);

// Check that an absolute url/path was specified e.g. /course/view.php (it probably isn't a
// security risk to allow a relative one, but isn't necessary here).
if (!$url->get_host()) {
    throw new moodle_exception('missingparam', 'error', '', 'url');
}

// Get course.
[$course, $cm] = get_course_and_cm_from_cmid($cmid);
$context = \context_module::instance($cm->id);

// Set up page for display.
$PAGE->set_url(new moodle_url('/course/modregrade.php', ['id' => $cmid, 'url' => $url]));
$PAGE->set_context($context);
$PAGE->set_title($course->shortname . ': ' . get_string('recalculatinggrades', 'grades'));

// Security check: must be logged in with manage activities permission.
require_login($course, false, $cm);
require_capability('moodle/course:manageactivities', $context);

// Do the regrade if necessary.
grade_regrade_final_grades_if_required($course);

// Redirect back to the target URL.
redirect($url);
