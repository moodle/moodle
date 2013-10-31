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
 * trainingevent module
 *
 * @package    mod
 * @subpackage trainingevent
 * @copyright  2003 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id = optional_param('id', 0, PARAM_INT);    // Course Module ID, or
$l = optional_param('l', 0, PARAM_INT);     // Label ID.

if ($id) {
    $PAGE->set_url('/mod/trainingevent/index.php', array('id' => $id));
    if (! $cm = get_coursemodule_from_id('trainingevent', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }

    if (! $trainingevent = $DB->get_record("trainingevent", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    $PAGE->set_url('/mod/trainingevent/index.php', array('l' => $l));
    if (! $trainingevent = $DB->get_record("trainingevent", array("id" => $l))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $trainingevent->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("trainingevent", $trainingevent->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);

redirect("$CFG->wwwroot/course/view.php?id=$course->id");


