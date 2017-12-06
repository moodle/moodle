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
 * Dataform embed
 *
 * @package    mod_dataformembed
 * @copyright  2012 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id = optional_param('id', 0, PARAM_INT);    // Course Module ID.
$de = optional_param('de', 0, PARAM_INT);    // Dataform embed ID.

if ($id) {
    $PAGE->set_url('/mod/dataformembed/index.php', array('id' => $id));
    if (!$cm = get_coursemodule_from_id('dataformembed', $id)) {
        print_error('invalidcoursemodule');
    }

    if (!$course = $DB->get_record("course", array('id' => $cm->course))) {
        print_error('coursemisconf');
    }

    if (!$dataformembed = $DB->get_record("dataformembed", array('id' => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    $PAGE->set_url('/mod/dataformembed/index.php', array('l' => $l));
    if (! $dataformembed = $DB->get_record("dataformembed", array('id' => $l))) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record("course", array('id' => $dataformembed->course)) ) {
        print_error('coursemisconf');
    }
    if (!$cm = get_coursemodule_from_instance("dataformembed", $dataformembed->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);

redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
