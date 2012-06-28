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

$source = required_param('source', PARAM_INT);
$move = required_param('move', PARAM_INT);

list($courses_sorted, $sitecourses, $coursecount) = block_course_overview_get_sorted_courses();
$sortorder = array_keys($courses_sorted);
// Now resort based on new weight for chosen course.
$neworder = array();

$sourcekey = array_search($source, $sortorder);
if ($sourcekey === false) {
    print_error("invalidcourseid", null, null, $source);
}

$destination = $sourcekey + $move;
if ($destination < 0) {
    print_error("listcantmoveup");
} else if ($destination >= count($courses_sorted)) {
    print_error("listcantmovedown");
}

// Create neworder list for courses.
unset($sortorder[$sourcekey]);
if ($move == -1) {
    if ($destination > 0) {
        $neworder = array_slice($sortorder, 0, $destination, true);
    }
    $neworder[] = $source;
    $remaningcourses = array_slice($sortorder, $destination);
    foreach ($remaningcourses as $courseid) {
        $neworder[] = $courseid;
    }
} else if (($move == 1)) {
    $neworder = array_slice($sortorder, 0, $destination);
    $neworder[] = $source;
    if (($destination) < count($courses_sorted)) {
        $remaningcourses = array_slice($sortorder, $destination);
        foreach ($remaningcourses as $courseid) {
            $neworder[] = $courseid;
        }
    }
}

block_course_overview_update_myorder($neworder);
redirect(new moodle_url('/my/index.php'));
