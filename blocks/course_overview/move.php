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
 * Move/order course functionality for course_overview block.
 *
 * @package    block_course_overview
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');

require_sesskey();
require_login();

$coursetomove = required_param('courseid', PARAM_INT);
$moveto = required_param('moveto', PARAM_INT);

list($courses, $sitecourses, $coursecount) = block_course_overview_get_sorted_courses();
$sortedcourses = array_keys($courses);

$currentcourseindex = array_search($coursetomove, $sortedcourses);
// If coursetomove is not found or moveto < 0 or > count($sortedcourses) then throw error.
if ($currentcourseindex === false) {
    print_error("invalidcourseid", null, null, $coursetomove);
} else if (($moveto < 0) || ($moveto >= count($sortedcourses))) {
    print_error("invalidaction");
}

// If current course index is same as destination index then don't do anything.
if ($currentcourseindex === $moveto) {
    redirect(new moodle_url('/my/index.php'));
}

// Create neworder list for courses.
$neworder = array();

unset($sortedcourses[$currentcourseindex]);
$neworder = array_slice($sortedcourses, 0, $moveto, true);
$neworder[] = $coursetomove;
$remaningcourses = array_slice($sortedcourses, $moveto);
foreach ($remaningcourses as $courseid) {
    $neworder[] = $courseid;
}
block_course_overview_update_myorder(array_values($neworder));
redirect(new moodle_url('/my/index.php'));
