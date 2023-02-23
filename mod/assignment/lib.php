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
 * assignment_base is the base class for assignment types
 *
 * This class provides all the functionality for an assignment
 *
 * @package   mod_assignment
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adds an assignment instance
 *
 * Only used by generators so we can create old assignments to test the upgrade.
 *
 * @param stdClass $assignment
 * @param mod_assignment_mod_form $mform
 * @return int intance id
 */
function assignment_add_instance($assignment, $mform = null) {
    global $DB;

    $assignment->timemodified = time();
    $assignment->courseid = $assignment->course;
    $returnid = $DB->insert_record("assignment", $assignment);
    $assignment->id = $returnid;
    return $returnid;
}

/**
 * Deletes an assignment instance
 *
 * @param $id
 */
function assignment_delete_instance($id){
    global $CFG, $DB;

    if (! $assignment = $DB->get_record('assignment', array('id'=>$id))) {
        return false;
    }

    $result = true;
    // Now get rid of all files
    $fs = get_file_storage();
    if ($cm = get_coursemodule_from_instance('assignment', $assignment->id)) {
        $context = context_module::instance($cm->id);
        $fs->delete_area_files($context->id);
    }

    if (! $DB->delete_records('assignment_submissions', array('assignment'=>$assignment->id))) {
        $result = false;
    }

    if (! $DB->delete_records('event', array('modulename'=>'assignment', 'instance'=>$assignment->id))) {
        $result = false;
    }

    grade_update('mod/assignment', $assignment->course, 'mod', 'assignment', $assignment->id, 0, NULL, array('deleted'=>1));

    // We must delete the module record after we delete the grade item.
    if (! $DB->delete_records('assignment', array('id'=>$assignment->id))) {
        $result = false;
    }

    return $result;
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function assignment_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_MOD_PURPOSE:             return MOD_PURPOSE_ASSESSMENT;

        default: return null;
    }
}
