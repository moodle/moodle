<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_jupyter
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_jupyter\gradeservice;
/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function jupyter_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_jupyter into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param stdClass $data An object from the form.
 * @return int The id of the newly inserted record.
 */
function jupyter_add_instance(stdClass $data): int {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    $cmid = $data->coursemodule;

    if (!$data->autograded) {
        $data->notebook_ready = true;
    }

    $data->id = $DB->insert_record('jupyter', $data);
    $DB->set_field('course_modules', 'instance', $data->id, ['id' => $cmid]);

    jupyter_set_mainfile($data);

    return $data->id;
}

/**
 * Updates an instance of the mod_jupyter in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param stdClass $data An object from the form in mod_form.php.
 * @return bool True if successful, false otherwise.
 */
function jupyter_update_instance(stdClass $data) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    jupyter_set_mainfile($data);

    return $DB->update_record('jupyter', $data);
}

/**
 * Removes an instance of the mod_jupyter from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function jupyter_delete_instance(int $id) {
    global $DB;

    $jupyter = $DB->get_record('jupyter', array('id' => $id));
    if (!$jupyter) {
        return false;
    }

    if ($jupyter->autograded) {
        gradeservice::delete_assignment($jupyter);
    }

    $DB->delete_records('jupyter', array('id' => $id));
    $DB->delete_records('jupyter_grades', array('jupyter' => $id));
    $DB->delete_records('jupyter_questions', array('jupyter' => $id));
    $DB->delete_records('jupyter_questions_points', array('jupyter' => $id));

    return true;
}

/**
 * Saves draft files as the activity package.
 *
 * @param stdClass $data an object from the form
 */
function jupyter_set_mainfile(stdClass $data): void {
    global $DB;
    $cmid = $data->coursemodule;
    $context = context_module::instance($cmid);

    if (!empty($data->packagefile)) {
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_jupyter', 'package');
        file_save_draft_area_files($data->packagefile, $context->id, 'mod_jupyter', 'package',
            0, ['subdirs' => 0, 'maxfiles' => 1]);

        $files = $fs->get_area_files($context->id, 'mod_jupyter', 'package', 0, 'id', false);
        $file = reset($files);
        $data->notebook_filename = $file->get_filename();
        $DB->update_record('jupyter', $data);
    }
}

/**
 * Creates or updates grade item for the given mod_jupyter instance.
 *
 * Needed by {@see grade_update_mod_grades()}.
 *
 * @param stdClass $jupyter Instance object with extra cmidnumber and modname property.
 * @param bool $reset Reset grades in the gradebook.
 * @return void.
 */
function jupyter_grade_item_update($jupyter, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($jupyter->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($jupyter->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $jupyter->grade;
        $item['grademin']  = 0;
    } else if ($jupyter->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$jupyter->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }
    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('/mod/jupyter', $jupyter->course, 'mod', 'jupyter', $jupyter->id, 0, null, $item);
}

/**
 * Delete grade item for given mod_jupyter instance.
 *
 * @param stdClass $jupyter Instance object.
 * @return grade_item.
 */
function jupyter_grade_item_delete($jupyter) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('/mod/jupyter', $jupyter->course, 'mod', 'mod_jupyter',
                        $jupyter->id, 0, null, array('deleted' => 1));
}

/**
 * Update mod_jupyter grades in the gradebook.
 *
 * Needed by {@see grade_update_mod_grades()}.
 *
 * @param stdClass $jupyter Instance object with extra cmidnumber and modname property.
 * @param int $userid Update grade of specific user only, 0 means all participants.
 * @param bool $nullifnone If a single user is specified and $nullifnone is true a grade item with a null rawgrade will be inserted
 */
function jupyter_update_grades($jupyter, $userid = 0, $nullifnone = true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = $DB->get_records('jupyter_grades', ['userid' => $userid]);

    grade_update('/mod/jupyter', $jupyter->course, 'mod', 'jupyter', $jupyter->id, 0, $grades);
}
