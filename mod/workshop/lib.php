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
 * Library of functions needed by Moodle core and other subsystems
 *
 * All the functions neeeded by Moodle core, gradebook, file subsystem etc
 * are placed here.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

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
                                                'workshopallocation' => 'mod/workshop/allocation',
                                                'workshopeval'       => 'mod/workshop/eval',
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
 * @param stdclass $workshop An object from the form in mod_form.php
 * @return int The id of the newly inserted workshop record
 */
function workshop_add_instance(stdclass $workshop) {
    global $CFG, $DB;
    require_once(dirname(__FILE__) . '/locallib.php');

    $workshop->phase                = workshop::PHASE_SETUP;
    $workshop->timecreated          = time();
    $workshop->timemodified         = $workshop->timecreated;
    $workshop->useexamples          = (int)!empty($workshop->useexamples);          // unticked checkbox hack
    $workshop->usepeerassessment    = (int)!empty($workshop->usepeerassessment);    // unticked checkbox hack
    $workshop->useselfassessment    = (int)!empty($workshop->useselfassessment);    // unticked checkbox hack
    $workshop->latesubmissions      = (int)!empty($workshop->latesubmissions);      // unticked checkbox hack

    // insert the new record so we get the id
    $workshop->id = $DB->insert_record('workshop', $workshop);

    // we need to use context now, so we need to make sure all needed info is already in db
    $cmid = $workshop->coursemodule;
    $DB->set_field('course_modules', 'instance', $workshop->id, array('id' => $cmid));
    $context = get_context_instance(CONTEXT_MODULE, $cmid);

    // process the custom wysiwyg editors
    if ($draftitemid = $workshop->instructauthorseditor['itemid']) {
        $workshop->instructauthors = file_save_draft_area_files($draftitemid, $context->id, 'workshop_instructauthors',
                0, workshop::instruction_editors_options($context), $workshop->instructauthorseditor['text']);
        $workshop->instructauthorsformat = $workshop->instructauthorseditor['format'];
    }

    if ($draftitemid = $workshop->instructreviewerseditor['itemid']) {
        $workshop->instructreviewers = file_save_draft_area_files($draftitemid, $context->id, 'workshop_instructreviewers',
                0, workshop::instruction_editors_options($context), $workshop->instructreviewerseditor['text']);
        $workshop->instructreviewersformat = $workshop->instructreviewerseditor['format'];
    }

    // re-save the record with the replaced URLs in editor fields
    $DB->update_record('workshop', $workshop);

    // update gradebook item
    workshop_grade_item_update($workshop);

    return $workshop->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdclass $workshop An object from the form in mod_form.php
 * @return bool success
 */
function workshop_update_instance(stdclass $workshop) {
    global $CFG, $DB;
    require_once(dirname(__FILE__) . '/locallib.php');

    $workshop->timemodified         = time();
    $workshop->id                   = $workshop->instance;
    $workshop->useexamples          = (int)!empty($workshop->useexamples);          // unticked checkbox hack
    $workshop->usepeerassessment    = (int)!empty($workshop->usepeerassessment);    // unticked checkbox hack
    $workshop->useselfassessment    = (int)!empty($workshop->useselfassessment);    // unticked checkbox hack
    $workshop->latesubmissions      = (int)!empty($workshop->latesubmissions);      // unticked checkbox hack

    // todo - if the grading strategy is being changed, we must replace all aggregated peer grades with nulls
    // todo - if maximum grades are being changed, we should probably recalculate or invalidate them

    $DB->update_record('workshop', $workshop);
    $context = get_context_instance(CONTEXT_MODULE, $workshop->coursemodule);

    // process the custom wysiwyg editors
    if ($draftitemid = $workshop->instructauthorseditor['itemid']) {
        $workshop->instructauthors = file_save_draft_area_files($draftitemid, $context->id, 'workshop_instructauthors',
                0, workshop::instruction_editors_options($context), $workshop->instructauthorseditor['text']);
        $workshop->instructauthorsformat = $workshop->instructauthorseditor['format'];
    }

    if ($draftitemid = $workshop->instructreviewerseditor['itemid']) {
        $workshop->instructreviewers = file_save_draft_area_files($draftitemid, $context->id, 'workshop_instructreviewers',
                0, workshop::instruction_editors_options($context), $workshop->instructreviewerseditor['text']);
        $workshop->instructreviewersformat = $workshop->instructreviewerseditor['format'];
    }

    // re-save the record with the replaced URLs in editor fields
    $DB->update_record('workshop', $workshop);

    // update gradebook item
    workshop_grade_item_update($workshop);

    return true;
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
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');


    if (! $workshop = $DB->get_record('workshop', array('id' => $id))) {
        return false;
    }
    // delete all associated aggregations
    $DB->delete_records('workshop_aggregations', array('workshopid' => $workshop->id));
    // get the list of ids of all submissions
    $submissions = $DB->get_records('workshop_submissions', array('workshopid' => $workshop->id), '', 'id');
    // get the list of all allocated assessments
    $assessments = $DB->get_records_list('workshop_assessments', 'submissionid', array_keys($submissions), '', 'id');
    // delete the associated records from the workshop core tables
    $DB->delete_records_list('workshop_grades', 'assessmentid', array_keys($assessments));
    $DB->delete_records_list('workshop_assessments', 'id', array_keys($assessments));
    $DB->delete_records_list('workshop_submissions', 'id', array_keys($submissions));
    // todo call the static clean-up methods of all available subplugins
    // ...
    // finally remove the workshop record itself
    $DB->delete_records('workshop', array('id' => $workshop->id));

    // gradebook cleanup
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 0, null, array('deleted' => true));
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 1, null, array('deleted' => true));

    return true;
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
    $return = new stdclass();
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
 * Returns all other caps used in the module
 *
 * @return array
 */
function workshop_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Creates or updates grade items for the give workshop instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php. Also used by
 * {@link workshop_update_grades()}.
 *
 * @param stdclass $workshop instance object with extra cmidnumber and modname property
 * @param stdclass $submissiongrades data for the first grade item
 * @param stdclass $assessmentgrades data for the second grade item
 * @return void
 */
function workshop_grade_item_update(stdclass $workshop, $submissiongrades=null, $assessmentgrades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $a = new stdclass();
    $a->workshopname = clean_param($workshop->name, PARAM_NOTAGS);

    $item = array();
    $item['itemname'] = get_string('gradeitemsubmission', 'workshop', $a);
    $item['idnumber'] = $workshop->cmidnumber;
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $workshop->grade;
    $item['grademin']  = 0;
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 0, $submissiongrades , $item);

    $item = array();
    $item['itemname'] = get_string('gradeitemassessment', 'workshop', $a);
    $item['idnumber'] = $workshop->cmidnumber;
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $workshop->gradinggrade;
    $item['grademin']  = 0;
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 1, $assessmentgrades, $item);
}

/**
 * Update workshop grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdclass $workshop instance object with extra cmidnumber and modname property
 * @param int $userid        update grade of specific user only, 0 means all participants
 * @return void
 */
function workshop_update_grades(stdclass $workshop, $userid=0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    $whereuser = $userid ? ' AND authorid = :userid' : '';
    $params = array('workshopid' => $workshop->id, 'userid' => $userid);
    $sql = 'SELECT authorid, grade, gradeover, gradeoverby, feedbackauthor, feedbackauthorformat, timemodified, timegraded
              FROM {workshop_submissions}
             WHERE workshopid = :workshopid AND example=0' . $whereuser;
    $records = $DB->get_records_sql($sql, $params);
    $submissiongrades = array();
    foreach ($records as $record) {
        $grade = new stdclass();
        $grade->userid = $record->authorid;
        if (!is_null($record->gradeover)) {
            $grade->rawgrade = grade_floatval($workshop->grade * $record->gradeover / 100);
            $grade->usermodified = $record->gradeoverby;
        } else {
            $grade->rawgrade = grade_floatval($workshop->grade * $record->grade / 100);
        }
        $grade->feedback = $record->feedbackauthor;
        $grade->feedbackformat = $record->feedbackauthorformat;
        $grade->datesubmitted = $record->timemodified;
        $grade->dategraded = $record->timegraded;
        $submissiongrades[$record->authorid] = $grade;
    }

    $whereuser = $userid ? ' AND userid = :userid' : '';
    $params = array('workshopid' => $workshop->id, 'userid' => $userid);
    $sql = 'SELECT userid, gradinggrade, timegraded
              FROM {workshop_aggregations}
             WHERE workshopid = :workshopid' . $whereuser;
    $records = $DB->get_records_sql($sql, $params);
    $assessmentgrades = array();
    foreach ($records as $record) {
        $grade = new stdclass();
        $grade->userid = $record->userid;
        $grade->rawgrade = grade_floatval($workshop->gradinggrade * $record->gradinggrade / 100);
        $grade->dategraded = $record->timegraded;
        $assessmentgrades[$record->userid] = $grade;
    }

    workshop_grade_item_update($workshop, $submissiongrades, $assessmentgrades);
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
 * @param stdclass $course
 * @param stdclass $cm
 * @param stdclass $context
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
 * @param stdclass $course
 * @param stdclass $cminfo
 * @param stdclass $context
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

    if ($filearea === 'workshop_instructreviewers') {
        // submission instructions may contain sensitive data
        if (!has_any_capability(array('moodle/course:manageactivities', 'mod/workshop:peerassess'), $context)) {
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

    // the following file areas are for the files embedded into the assessment forms
    if (in_array($filearea, array(
            'workshopform_comments_description',
            'workshopform_accumulative_description',
            'workshopform_numerrors_description',
            'workshopform_rubric_description',
        ))) {
        $itemid = (int)array_shift($args); // the id of the assessment form dimension
        if (!$workshop = $DB->get_record('workshop', array('id' => $cminfo->instance))) {
            send_file_not_found();
        }
        switch ($filearea) {
            case 'workshopform_comments_description':
                $dimension = $DB->get_record('workshopform_comments', array('id' => $itemid));
                break;
            case 'workshopform_accumulative_description':
                $dimension = $DB->get_record('workshopform_accumulative', array('id' => $itemid));
                break;
            case 'workshopform_numerrors_description':
                $dimension = $DB->get_record('workshopform_numerrors', array('id' => $itemid));
                break;
            case 'workshopform_rubric_description':
                $dimension = $DB->get_record('workshopform_rubric', array('id' => $itemid));
                break;
            default:
                $dimension = false;
        }
        if (empty($dimension)) {
            send_file_not_found();
        }
        if ($workshop->id != $dimension->workshopid) {
            // this should never happen but just in case
            send_file_not_found();
        }
        // TODO now make sure the user is allowed to see the file
        // (media embedded into the dimension description)
        $fs = get_file_storage();
        $relativepath = '/' . implode('/', $args);
        $fullpath = $context->id . $filearea . $itemid . $relativepath;
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        // finally send the file
        send_stored_file($file);
    }

    if ($filearea == 'workshop_submission_content' or $filearea == 'workshop_submission_attachment') {
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
 * @param stdclass $browser
 * @param stdclass $areas
 * @param stdclass $course
 * @param stdclass $cm
 * @param stdclass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return stdclass file_info instance or null if not found
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
            INNER JOIN {user} u ON (s.authorid = u.id)
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
 * @param stdclass $course
 * @param stdclass $module
 * @param stdclass $cm
 */
function workshop_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, stdclass $cm) {
    global $CFG;

    if (has_capability('mod/workshop:submit', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        $url = new moodle_url('/mod/workshop/submission.php', array('cmid' => $cm->id));
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
 * @param navigation_node $workshopnode {@link navigation_node}
 */
function workshop_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $workshopnode=null) {
    global $PAGE;

    //$workshopobject = $DB->get_record("workshop", array("id" => $PAGE->cm->instance));

    if (has_capability('mod/workshop:editdimensions', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/workshop/editform.php', array('cmid' => $PAGE->cm->id));
        $workshopnode->add(get_string('editassessmentform', 'workshop'), $url, settings_navigation::TYPE_SETTING);
    }
    if (has_capability('mod/workshop:allocate', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/workshop/allocation.php', array('cmid' => $PAGE->cm->id));
        $workshopnode->add(get_string('allocate', 'workshop'), $url, settings_navigation::TYPE_SETTING);
    }
}
