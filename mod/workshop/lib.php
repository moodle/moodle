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
 * Returns the information if the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function workshop_supports($feature) {
    switch($feature) {
        case FEATURE_GRADE_HAS_GRADE:   return true;
        case FEATURE_GROUPS:            return true;
        case FEATURE_GROUPINGS:         return true;
        case FEATURE_GROUPMEMBERSONLY:  return true;
        case FEATURE_MOD_INTRO:         return true;
        case FEATURE_MOD_SUBPLUGINS:    return array(
                                                'workshopform'       => 'mod/workshop/form',
                                                'workshopallocation' => 'mod/workshop/allocation'
                                                );
        default:                        return null;
    }
}

/**
 * Saves a new instance of the workshop into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will save a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $data An object from the form in mod_form.php
 * @return int The id of the newly inserted workshop record
 */
function workshop_add_instance($data) {
    global $CFG, $DB;
    require_once(dirname(__FILE__) . '/locallib.php');

    $data->phase        = workshop::PHASE_SETUP;
    $data->timecreated  = time();
    $data->timemodified = $data->timecreated;

    // insert the new record so we get the id
    $data->id = $DB->insert_record('workshop', $data);

    // we need to use context now, so we need to make sure all needed info is already in db
    $cmid = $data->coursemodule;
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));
    $context = get_context_instance(CONTEXT_MODULE, $cmid);

    // process the custom wysiwyg editors
    if ($draftitemid = $data->instructauthorseditor['itemid']) {
        $data->instructauthors = file_save_draft_area_files($draftitemid, $context->id, 'workshop_instructauthors',
                false, workshop::instruction_editors_options($context), $data->instructauthorseditor['text']);
        $data->instructauthorsformat = $data->instructauthorseditor['format'];
    }

    // re-save the record with the replaced URLs in editor fields
    $DB->update_record('workshop', $data);

    return $data->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $data An object from the form in mod_form.php
 * @return bool success
 */
function workshop_update_instance($data) {
    global $CFG, $DB;
    require_once(dirname(__FILE__) . '/locallib.php');

    $data->timemodified = time();
    $data->id = $data->instance;

    // todo - if the grading strategy is being changed, we must replace all aggregated peer grades with nulls
    // todo - if maximum grades are being changed, we should probably recalculate or invalidate them

    $DB->update_record('workshop', $data);
    $context = get_context_instance(CONTEXT_MODULE, $data->coursemodule);

    // process the custom wysiwyg editors
    if ($draftitemid = $data->instructauthorseditor['itemid']) {
        $data->instructauthors = file_save_draft_area_files($draftitemid, $context->id, 'workshop_instructauthors',
                false, workshop::instruction_editors_options($context), $data->instructauthorseditor['text']);
        $data->instructauthorsformat = $data->instructauthorseditor['format'];
    }

    // re-save the record with the replaced URLs in editor fields
    return $DB->update_record('workshop', $data);
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
    $return = new stdClass();
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
 * TODO: we use the following areas
 * workshopform_accumulative_description
 * workshopform_numerrors_description
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function workshop_get_file_areas($course, $cm, $context) {
    $areas = array();
    if (has_capability('moodle/course:managefiles', $context)) {
        $areas['workshop_instructauthors']          = get_string('areainstructauthors', 'workshop');
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
 * Besides that, areas workshop_instructauthors and workshop_instructreviewers contain the media
 * embedded using the mod_form.php.
 *
 * @param stdClass $course
 * @param stdClass $cminfo
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function workshop_pluginfile($course, $cminfo, $context, $filearea, array $args, $forcedownload) {
    global $DB;

    if (!$cminfo->uservisible) {
        send_file_not_found();
    }
    if (!$cm = get_coursemodule_from_instance('workshop', $cminfo->instance, $course->id)) {
        send_file_not_found();
    }
    require_login($course, true, $cm);

    if ($filearea === 'workshop_instructauthors') {
        // submission instructions may contain sensitive data
        if (!has_any_capability(array('moodle/course:manageactivities', 'mod/workshop:submit'), $context)) {
            send_file_not_found();
        }

        array_shift($args); // we do not use itemids here
        $relativepath = '/' . implode('/', $args);
        $fullpath = $context->id . $filearea . '0' . $relativepath; // beware, slashes are not used here!

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        $lifetime = isset($CFG->filelifetime) ? $CFG->filelifetime : 86400;

        // finally send the file
        send_stored_file($file, $lifetime, 0);
    }

    /** todo - this filearea has been replaced by subplugins' areas
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
    */

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
 * @param stdClass $browser
 * @param stdClass $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return stdClass file_info instance or null if not found
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

        $sql = 'SELECT s.id, u.lastname, u.firstname
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.userid = u.id)
                 WHERE s.workshopid = ?';
        $params         = array($cm->instance);
        $authors        = $DB->get_records_sql($sql, $params);
        $urlbase        = $CFG->wwwroot . '/pluginfile.php';
        $topvisiblename = fullname($authors[$itemid]);
        // do not allow manual modification of any files!
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $topvisiblename, true, true, false, false);
    }

    /* todo was replaced by subplugins' areas
    if ($filearea === 'workshop_dimension_description') {
        // always only itemid 0 - TODO not true, review

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
     */

    if ($filearea === 'workshop_instructauthors') {
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
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding workshop nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the workshop module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param stdClass $cm
 */
function workshop_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, stdClass $cm) {
    global $CFG;

    if (has_capability('mod/workshop:submit', $cm->context)) {
        $url = new moodle_url($CFG->wwwroot.'/mod/workshop/submission.php', array('cmid' => $cm->id));
        $mysubmissionkey = $navref->add(get_string('mysubmission', 'workshop'), $url);
        $navref->get($mysubmissionkey)->mainnavonly = true;
    }
}

/**
 * Extends the settings navigation with the Workshop settings

 * This function is called when the context for the page is a workshop module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param stdClass $module
 * @return void|mixed The key to the modules branch
 */
function workshop_extend_settings_navigation(settings_navigation $settingsnav, stdClass $module=null) {
    global $CFG, $PAGE;

    $workshopkey = $settingsnav->add(get_string('workshopadministration', 'workshop'));
    $workshopnode = $settingsnav->get($workshopkey);
    $workshopnode->forceopen = true;
    //$workshopobject = $DB->get_record("workshop", array("id" => $PAGE->cm->instance));

    if (has_capability('mod/workshop:editdimensions', $PAGE->cm->context)) {
        $url = new moodle_url($CFG->wwwroot . '/mod/workshop/editform.php', array('cmid' => $PAGE->cm->id));
        $workshopnode->add(get_string('editassessmentform', 'workshop'), $url, settings_navigation::TYPE_SETTING);
    }
    if (has_capability('mod/workshop:allocate', $PAGE->context)) {
        $url = new moodle_url($CFG->wwwroot . '/mod/workshop/allocation.php', array('cmid' => $PAGE->cm->id));
        $workshopnode->add(get_string('allocate', 'workshop'), $url, settings_navigation::TYPE_SETTING);
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
 * @todo remove this function from lib.php
 * $return array Array ['string' => 'string']
 */
function workshop_get_strategies() {
    $installed = get_plugin_list('workshopform');
    $forms = array();
    foreach ($installed as $strategy => $strategypath) {
        if (file_exists($strategypath . '/lib.php')) {
            $forms[$strategy] = get_string('pluginname', 'workshopform_' . $strategy);
        }
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
 * array[code int] of stdClass (
 *                      ->name string,
 *                      ->value number,
 *                      )
 * where code if the integer code that is actually stored in the database.
 *
 * @return array Array of objects
 */
function workshop_get_comparison_levels() {
    $levels = array();

    $levels[WORKSHOP_COMPARISON_VERYHIGH] = new stdClass();
    $levels[WORKSHOP_COMPARISON_VERYHIGH]->name = get_string('comparisonveryhigh', 'workshop');
    $levels[WORKSHOP_COMPARISON_VERYHIGH]->value = 5.00;

    $levels[WORKSHOP_COMPARISON_HIGH] = new stdClass();
    $levels[WORKSHOP_COMPARISON_HIGH]->name = get_string('comparisonhigh', 'workshop');
    $levels[WORKSHOP_COMPARISON_HIGH]->value = 3.00;

    $levels[WORKSHOP_COMPARISON_NORMAL] = new stdClass();
    $levels[WORKSHOP_COMPARISON_NORMAL]->name = get_string('comparisonnormal', 'workshop');
    $levels[WORKSHOP_COMPARISON_NORMAL]->value = 2.50;

    $levels[WORKSHOP_COMPARISON_LOW] = new stdClass();
    $levels[WORKSHOP_COMPARISON_LOW]->name = get_string('comparisonlow', 'workshop');
    $levels[WORKSHOP_COMPARISON_LOW]->value = 1.67;

    $levels[WORKSHOP_COMPARISON_VERYLOW] = new stdClass();
    $levels[WORKSHOP_COMPARISON_VERYLOW]->name = get_string('comparisonverylow', 'workshop');
    $levels[WORKSHOP_COMPARISON_VERYLOW]->value = 1.00;

    return $levels;
}
