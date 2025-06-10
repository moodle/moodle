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
 * Label module
 *
 * @package mod_library
 * @copyright  2014 and onwards LSU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$l = optional_param('l', 0, PARAM_INT); // Label ID.
global $OUTPUT;

if ($id) {
    $PAGE->set_url('/mod/library/index.php', array('id' => $id));
    if (! $cm = get_coursemodule_from_id('library', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }

    if (! $library = $DB->get_record("library", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }
} else {
    echo 'asdfasdf';
    echo $OUTPUT->box_start('generalbox', 'gradeinfobox');
    $PAGE->set_url('/mod/library/index.php', array('l' => $l));
    if (! $library = $DB->get_record("library", array("id" => $l))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $library->course)) ) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("library", $library->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);