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
 * Library of interface functions and constants for module workshop
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the workshop specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The internal codes of the example assessment modes
 */
define('WORKSHOP_EXAMPLES_VOLUNTARY',           0);
define('WORKSHOP_EXAMPLES_BEFORE_SUBMISSION',   1);
define('WORKSHOP_EXAMPLES_BEFORE_ASSESSMENT',   2);

/**
 * The internal codes of the required level of assessment similarity
 */
define('WORKSHOP_COMPARISON_VERYLOW',   0);     /* f = 1.00 */
define('WORKSHOP_COMPARISON_LOW',       1);     /* f = 1.67 */
define('WORKSHOP_COMPARISON_NORMAL',    2);     /* f = 2.50 */
define('WORKSHOP_COMPARISON_HIGH',      3);     /* f = 3.00 */
define('WORKSHOP_COMPARISON_VERYHIGH',  4);     /* f = 5.00 */

/**
 * Saves a new instance of the workshop into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will save a new instance and return the id number
 * of the new instance.
 *
 * @param object $data An object from the form in mod_form.php
 * @return int The id of the newly inserted workshop record
 */
function workshop_add_instance($data) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    return $DB->insert_record('workshop', $data);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $workshop An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function workshop_update_instance($workshop) {
    global $DB;

    $workshop->timemodified = time();
    $workshop->id = $workshop->instance;

    return $DB->update_record('workshop', $workshop);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function workshop_delete_instance($id) {
    global $DB;

    if (! $workshop = $DB->get_record('workshop', array('id' => $id))) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! $DB->delete_records('workshop', array('id' => $workshop->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function workshop_user_outline($course, $user, $mod, $workshop) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function workshop_user_complete($course, $user, $mod, $workshop) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in workshop activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function workshop_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function workshop_cron () {
    return true;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of workshop. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $workshopid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function workshop_get_participants($workshopid) {
    return false;
}

/**
 * This function returns if a scale is being used by one workshop
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $workshopid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function workshop_scale_used($workshopid, $scaleid) {
    $return = false;

    //$rec = get_record("workshop","id","$workshopid","scale","-$scaleid");
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of workshop.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any workshop
 */
function workshop_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('workshop', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function workshop_install() {
    return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function workshop_uninstall() {
    return true;
}

/**
 * Returns the information if the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @todo review and add features
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function workshop_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:            return true;
        case FEATURE_GROUPINGS:         return true;
        case FEATURE_GROUPMEMBERSONLY:  return true;
        case FEATURE_MOD_INTRO:         return true;
        case FEATURE_GRADE_HAS_GRADE:   return true;
        default:                        return null;
    }
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function workshop_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area workshop_intro for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_module()}
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @return array of [(string)filearea] => (string)description
 */
function workshop_get_file_areas($course, $cm, $context) {
    $areas = array();
    if (has_capability('moodle/course:managefiles', $context)) {
        $areas['workshop_dimension_description']    = get_string('areadimensiondescription', 'workshop');
        $areas['workshop_submission_content']       = get_string('areasubmissioncontent', 'workshop');
        $areas['workshop_submission_attachment']    = get_string('areasubmissionattachment', 'workshop');
    }
    return $areas;
}

/**
 * Serves the files from the workshop file areas
 *
 * Apart from module intro (handled by pluginfile.php automatically), workshop files may be
 * media inserted into submission content (like images) and submission attachments. For these two,
 * the fileareas workshop_submission_content and workshop_submission_attachment are used.
 * The access rights to the files are checked here. The user must be either a peer-reviewer
 * of the submission or have capability ... (todo) to access the submission files.
 *
 * @param object $course
 * @param object $cminfo
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function workshop_pluginfile($course, $cminfo, $context, $filearea, $args, $forcedownload) {
    global $DB;

    if (!$cminfo->uservisible) {
        return false;
    }
    if (!$cm = get_coursemodule_from_instance('workshop', $cminfo->instance, $course->id)) {
        return false;
    }
    require_course_login($course, true, $cm);

    if ($filearea === 'workshop_dimension_description') {
        $itemid = (int)array_shift($args);
        if (!$dimension = $DB->get_record('workshop_forms', array('id' => $itemid))) {
            return false;
        }
        if (!$workshop = $DB->get_record('workshop', array('id' => $cminfo->instance))) {
            return false;
        }
        if ($workshop->id !== $dimension->workshopid) {
            // this should never happen but just in case
            return false;
        }
        // TODO now make sure the user is allowed to see the file
        // media embedded by teacher into the dimension description
        $fs = get_file_storage();
        $relativepath = '/' . implode('/', $args);
        $fullpath = $context->id . $filearea . $itemid . $relativepath;
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        // finally send the file
        send_stored_file($file);
    }

    if ($filearea === 'workshop_submission_content' or $filearea === 'workshop_submission_attachment') {
        $itemid = (int)array_shift($args);
        if (!$submission = $DB->get_record('workshop_submissions', array('id' => $itemid))) {
            return false;
        }
        if (!$workshop = $DB->get_record('workshop', array('id' => $cminfo->instance))) {
            return false;
        }
        // TODO now make sure the user is allowed to see the file
        $fs = get_file_storage();
        $relativepath = '/' . implode('/', $args);
        $fullpath = $context->id . $filearea . $itemid . $relativepath;
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        // finally send the file
        // these files are uploaded by students - forcing download for security reasons
        send_stored_file($file, 0, 0, true);
    }

    return false;
}

/**
 * File browsing support for workshop file areas
 *
 * @param object $browser
 * @param object $areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return object file_info instance or null if not found
 */
function workshop_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB;

    if (!has_capability('moodle/course:managefiles', $context)) {
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'workshop_submission_content' or $filearea === 'workshop_submission_attachment') {

        if (is_null($itemid)) {
            require_once($CFG->dirroot . '/mod/workshop/fileinfolib.php');
            return new workshop_file_info_submissions_container($browser, $course, $cm, $context, $areas, $filearea);
        }

        // we are inside the submission container

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($context->id, $filearea, $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, $itemid);
            } else {
                // not found
                return null;
            }
        }

        // let us display the author's name instead of itemid (submission id)
        // todo some sort of caching should happen here

        $sql = "SELECT s.id, u.lastname, u.firstname
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.userid = u.id)
                 WHERE s.workshopid = ?";
        $params         = array($cm->instance);
        $authors        = $DB->get_records_sql($sql, $params);
        $urlbase        = $CFG->wwwroot . '/pluginfile.php';
        $topvisiblename = fullname($authors[$itemid]);
        // do not allow manual modification of any files!
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $topvisiblename, true, true, false, false);
    }

    if ($filearea === 'workshop_dimension_description') {
        // always only itemid 0

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, 0);
            } else {
                // not found
                return null;
            }
        }
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $areas[$filearea], false, true, true, false);
    }
}

////////////////////////////////////////////////////////////////////////////////
// Other functions needed by Moodle core follows. They can't be put into      //
// locallib.php because they are used by some core scripts (like modedit.php) //
// where locallib.php is not included.                                        //
////////////////////////////////////////////////////////////////////////////////

/**
 * Return an array of numeric values that can be used as maximum grades
 *
 * Used at several places where maximum grade for submission and grade for
 * assessment are defined via a HTML select form element. By default it returns
 * an array 0, 1, 2, ..., 98, 99, 100.
 *
 * @return array Array of integers
 */
function workshop_get_maxgrades() {
    $grades = array();
    for ($i=100; $i>=0; $i--) {
        $grades[$i] = $i;
    }
    return $grades;
}

/**
 * Return an array of possible numbers of assessments to be done
 *
 * Should always contain numbers 1, 2, 3, ... 10 and possibly others up to a reasonable value
 *
 * @return array Array of integers
 */
function workshop_get_numbers_of_assessments() {
    $options = array();
    $options[30] = 30;
    $options[20] = 20;
    $options[15] = 15;
    for ($i=10; $i>0; $i--) {
        $options[$i] = $i;
    }
    return $options;
}

/**
 * Return an array of possible values for weight of teacher assessment
 *
 * @return array Array of integers 0, 1, 2, ..., 10
 */
function workshop_get_teacher_weights() {
    $weights = array();
    for ($i=10; $i>=0; $i--) {
        $weights[$i] = $i;
    }
    return $weights;
}

/**
 * Return an array of possible values of assessment dimension weight
 *
 * @return array Array of integers 0, 1, 2, ..., 16
 */
function workshop_get_dimension_weights() {
    $weights = array();
    for ($i=16; $i>=0; $i--) {
        $weights[$i] = $i;
    }
    return $weights;
}

/**
 * Return an array of the localized grading strategy names
 *
 * $return array Array ['string' => 'string']
 */
function workshop_get_strategies() {
    $installed = get_list_of_plugins('mod/workshop/grading');
    $forms = array();
    foreach ($installed as $strategy) {
        $forms[$strategy] = get_string('strategy' . $strategy, 'workshop');
    }

    return $forms;
}

/**
 * Return an array of available example assessment modes
 *
 * @return array Array 'mode DB code'=>'mode name'
 */
function workshop_get_example_modes() {
    $modes = array();
    $modes[WORKSHOP_EXAMPLES_VOLUNTARY]         = get_string('examplesvoluntary', 'workshop');
    $modes[WORKSHOP_EXAMPLES_BEFORE_SUBMISSION] = get_string('examplesbeforesubmission', 'workshop');
    $modes[WORKSHOP_EXAMPLES_BEFORE_ASSESSMENT] = get_string('examplesbeforeassessment', 'workshop');

    return $modes;
}

/**
 * Return array of assessment comparison levels
 *
 * The assessment comparison level influence how the grade for assessment is calculated.
 * Each object in the returned array provides information about the name of the level
 * and the value of the factor to be used in the calculation.
 * The structure of the returned array is
 * array[code int] of object (
 *                      ->name string,
 *                      ->value number,
 *                      )
 * where code if the integer code that is actually stored in the database.
 *
 * @return array Array of objects
 */
function workshop_get_comparison_levels() {
    $levels = array();

    $levels[WORKSHOP_COMPARISON_VERYHIGH] = new stdClass;
    $levels[WORKSHOP_COMPARISON_VERYHIGH]->name = get_string('comparisonveryhigh', 'workshop');
    $levels[WORKSHOP_COMPARISON_VERYHIGH]->value = 5.00;

    $levels[WORKSHOP_COMPARISON_HIGH] = new stdClass;
    $levels[WORKSHOP_COMPARISON_HIGH]->name = get_string('comparisonhigh', 'workshop');
    $levels[WORKSHOP_COMPARISON_HIGH]->value = 3.00;

    $levels[WORKSHOP_COMPARISON_NORMAL] = new stdClass;
    $levels[WORKSHOP_COMPARISON_NORMAL]->name = get_string('comparisonnormal', 'workshop');
    $levels[WORKSHOP_COMPARISON_NORMAL]->value = 2.50;

    $levels[WORKSHOP_COMPARISON_LOW] = new stdClass;
    $levels[WORKSHOP_COMPARISON_LOW]->name = get_string('comparisonlow', 'workshop');
    $levels[WORKSHOP_COMPARISON_LOW]->value = 1.67;

    $levels[WORKSHOP_COMPARISON_VERYLOW] = new stdClass;
    $levels[WORKSHOP_COMPARISON_VERYLOW]->name = get_string('comparisonverylow', 'workshop');
    $levels[WORKSHOP_COMPARISON_VERYLOW]->value = 1.00;

    return $levels;
}
