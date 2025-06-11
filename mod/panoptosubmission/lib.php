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
 * Main lib.php file for the Panopto Student Submission mod.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/calendar/lib.php');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $newactivity An object from the form in mod_form.php
 * @return int The id of the newly inserted newactivity record
 */
function panoptosubmission_add_instance($newactivity) {
    global $DB;

    $newactivity->timecreated = time();

    $newactivity->id = $DB->insert_record('panoptosubmission', $newactivity);

    if ($newactivity->timedue) {

        $event = new stdClass();
        $event->name = $newactivity->name;
        $event->description = format_module_intro('panoptosubmission', $newactivity, $newactivity->coursemodule, false);
        $event->format = FORMAT_HTML;
        $event->courseid = $newactivity->course;
        $event->groupid = 0;
        $event->userid = 0;
        $event->modulename = 'panoptosubmission';
        $event->instance = $newactivity->id;
        $event->eventtype = 'due';
        $event->timestart = $newactivity->timedue;
        $event->timeduration = 0;

        calendar_event::create($event);
    }

    panoptosubmission_grade_item_update($newactivity);

    return $newactivity->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $targetinstance An object from the form in mod_form.php
 * @return bool Returns true on success, otherwise false.
 */
function panoptosubmission_update_instance($targetinstance) {
    global $DB;

    $targetinstance->timemodified = time();
    $targetinstance->id = $targetinstance->instance;

    $updated = $DB->update_record('panoptosubmission', $targetinstance);

    if ($targetinstance->timedue) {
        $event = new stdClass();

        if ($event->id = $DB->get_field(
            'event', 'id', ['modulename' => 'panoptosubmission', 'instance' => $targetinstance->id])) {

            $event->name = $targetinstance->name;
            $event->description = format_module_intro('panoptosubmission', $targetinstance, $targetinstance->coursemodule, false);
            $event->format = FORMAT_HTML;
            $event->timestart = $targetinstance->timedue;

            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
        } else {
            $event = new stdClass();
            $event->name = $targetinstance->name;
            $event->description = format_module_intro('panoptosubmission', $targetinstance, $targetinstance->coursemodule, false);
            $event->format = FORMAT_HTML;
            $event->courseid = $targetinstance->course;
            $event->groupid = 0;
            $event->userid = 0;
            $event->modulename = 'panoptosubmission';
            $event->instance = $targetinstance->id;
            $event->eventtype = 'due';
            $event->timestart = $targetinstance->timedue;
            $event->timeduration = 0;

            calendar_event::create($event);
        }
    } else {
        $DB->delete_records('event', ['modulename' => 'panoptosubmission', 'instance' => $targetinstance->id]);
    }

    if ($updated) {
        panoptosubmission_grade_item_update($targetinstance);
    }

    return $updated;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return bool True on success, else false.
 */
function panoptosubmission_delete_instance($id) {
    global $DB;

    $result = true;

    if (! $targetinstance = $DB->get_record('panoptosubmission', ['id' => $id])) {
        return false;
    }

    if (! $DB->delete_records('panoptosubmission_submission', ['panactivityid' => $targetinstance->id])) {
        $result = false;
    }

    if (! $DB->delete_records('event', ['modulename' => 'panoptosubmission', 'instance' => $targetinstance->id])) {
        $result = false;
    }

    if (! $DB->delete_records('panoptosubmission', ['id' => $targetinstance->id])) {
        $result = false;
    }

    panoptosubmission_grade_item_delete($targetinstance);

    // Delete files associated with this module.
    $context = context_module::instance($id);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param object $course the current course
 * @param object $user the current user
 * @param object $mod the current module
 * @param object $data extra data
 *
 * @return object Returns time and info properties.
 */
function panoptosubmission_user_outline($course, $user, $mod, $data) {
    $return = new stdClass;

    $return->time = 0;
    $return->info = '';

    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param object $course the current course
 * @param object $user the current user
 * @param object $mod the current module
 * @param object $data extra data
 *
 * @return boolean always return true.
 */
function panoptosubmission_user_complete($course, $user, $mod, $data) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in panoptosubmission activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param object $course the current course
 * @param bool $viewfullnames show full or only shortened names in the activity page
 * @param string $timestart the time the activity started
 * @return boolean Always returns false.
 */
function panoptosubmission_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Must return an array of users who are participants an instance of the
 * Panopto Student Submission activity
 *
 * @param int $targetactivity ID of an instance of this module
 * @return bool Always returns false.
 */
function panoptosubmission_get_participants($targetactivity) {
    return false;
}

/**
 * This function returns if a scale is being used by one panoptosubmission
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $targetactivity ID of an instance of this module
 * @param int $scaleid the id of the scale the activity used.
 * @return bool Returns false as scales are not supportd by this module.
 */
function panoptosubmission_scale_used($targetactivity, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of panoptosubmission.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param int $scaleid The scale id.
 * @return bool True if the scale is used by any panoptosubmission
 */
function panoptosubmission_scale_used_anywhere($scaleid) {
    global $DB;

    $param = ['grade' => -$scaleid];
    if ($scaleid && $DB->record_exists('panoptosubmission', $param)) {
        return true;
    } else {
        return false;
    }
}

/**
 * This function tells Moodle what features this plugin supports
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function panoptosubmission_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_ADVANCED_GRADING:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION;
            return true;
        default:
            return null;
    }
}

/**
 * Lists all gradable areas for the advanced grading methods gramework
 *
 * @return ['string'=>'string'] An array with area names as keys and descriptions as values
 */
function panoptosubmission_grading_areas_list() {
    return ['submissions' => get_string('submissions', 'panoptosubmission')];
}

/**
 * Create/update grade item for given Panopto video activity
 *
 * @param object $targetinstance object with extra cmidnumber
 * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int, 0 if ok, error code otherwise
 */
function panoptosubmission_grade_item_update($targetinstance, $grades = null) {
    require_once(dirname(dirname(dirname(__FILE__))).'/lib/gradelib.php');

    $params = ['itemname' => $targetinstance->name, 'idnumber' => $targetinstance->cmidnumber];

    if ($targetinstance->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $targetinstance->grade;
        $params['grademin'] = 0;

    } else if ($targetinstance->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid'] = -$targetinstance->grade;

    } else {
        $params['gradetype'] = GRADE_TYPE_TEXT;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/panoptosubmission',
        $targetinstance->course, 'mod', 'panoptosubmission', $targetinstance->id, 0, $grades, $params);
}

/**
 * Update activity grades.
 *
 * @param stdClass $targetrecord database record
 * @param int $userid specific user only, 0 means all
 * @param bool $nullifnone - not used
 */
function panoptosubmission_update_grades($targetrecord, $userid = 0, $nullifnone = true) {
    panoptosubmission_grade_item_update($targetrecord, null);
}

/**
 * Removes all grades from gradebook
 *
 * @param int $courseid id of the current course
 * @param string $type Not used
 */
function panoptosubmission_reset_gradebook($courseid, $type = '') {
    global $DB;

    $sql = "SELECT l.*, cm.idnumber as cmidnumber, l.course as courseid " .
              "FROM {panoptosubmission} l, {course_modules} cm, {modules} m " .
             "WHERE m.name = 'panoptosubmission' AND m.id = cm.module AND cm.instance = l.id AND l.course = :course";

    $params = ['course' => $courseid];

    if ($existinggrades = $DB->get_records_sql($sql, $params)) {

        foreach ($existinggrades as $existinggrade) {
            panoptosubmission_grade_item_update($existinggrade, 'reset');
        }
    }
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * Panopto video submissions attempts for course $data->courseid.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function panoptosubmission_reset_userdata($data) {
    global $DB;

    $componentstr = get_string('modulenameplural', 'panoptosubmission');
    $status = [];

    if (!empty($data->reset_panoptosubmission)) {
        $panoptosubmissionsql = "SELECT l.id " .
                              "FROM {panoptosubmission} l " .
                             "WHERE l.course=:course";

        $params = ["course" => $data->courseid];
        $DB->delete_records_select('panoptosubmission_submission', "panactivityid IN ($panoptosubmissionsql)", $params);

        // Remove all grades from gradebook.
        if (empty($data->reset_gradebook_grades)) {
            panoptosubmission_reset_gradebook($data->courseid);
        }

        $status[] = [
            'component' => $componentstr,
            'item' => get_string('deleteallsubmissions', 'panoptosubmission'),
            'error' => false,
        ];
    }

    // Updating dates - shift may be negative too.
    if ($data->timeshift) {
        shift_course_mod_dates('panoptosubmission',
            ['timedue', 'timeavailable', 'cutofftime'],
            $data->timeshift, $data->courseid
        );
        $status[] = ['component' => $componentstr, 'item' => get_string('datechanged'), 'error' => false];
    }

    return $status;
}

/**
 * This functions deletes a grade item.
 * @param object $targetrecord a Panopto video activity data object.
 * @return int Returns GRADE_UPDATE_OK, GRADE_UPDATE_FAILED, GRADE_UPDATE_MULTIPLE or GRADE_UPDATE_ITEM_LOCKED.
 */
function panoptosubmission_grade_item_delete($targetrecord) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    return grade_update(
        'mod/panoptosubmission',
        $targetrecord->course,
        'mod',
        'panoptosubmission',
        $targetrecord->id,
        0,
        null,
        ['deleted' => 1],
    );
}

/**
 * Serves submission comment feedback files.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options - List of options affecting file serving.
 * @return bool false if file not found, does not return if found - just send the file
 */
function panoptosubmission_pluginfile(
        $course,
        $cm,
        context $context,
        $filearea,
        $args,
        $forcedownload,
        array $options = []) {

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    $itemid = (int)array_shift($args);

    $relativepath = implode('/', $args);
    $fullpath = "/{$context->id}/mod_panoptosubmission/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();

    if ((!$file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
        return false;
    }

    // Download MUST be forced - security!
    send_stored_file($file, 0, 0, true, $options);
}

/**
 * Retrieve a property in a case-insensitive way and assign default if missing or invalid.
 *
 * @param object $object The object to search.
 * @param string $property The property name to find.
 * @param mixed $default The default value to return if not found or invalid.
 */
function panoptosubmission_get_property_or_default($object, $property, $default) {
    // Ensure the input is an object, return default otherwise.
    if (!is_object($object)) {
        return $default;
    }

    // Iterate over object properties in a case-insensitive manner.
    foreach ($object as $key => $value) {
        if (strcasecmp($key, $property) === 0) { // strcasecmp is case-insensitive.
            // Return default if the value is strictly 0 or evaluates to false.
            return ($value === 0 || !$value) ? $default : $value;
        }
    }

    // Return default if the property is not found.
    return $default;
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * Finds all assignment notifications that have yet to be mailed out, and mails them.
 * @return bool Returns false as the this module doesn't support cron jobs
 */
function panoptosubmission_cron() {
    return false;
}
