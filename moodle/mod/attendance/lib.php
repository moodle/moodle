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
 * Library of functions and constants for module attendance
 *
 * @package   mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/classes/calendar_helpers.php');

/**
 * Returns the information if the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function attendance_supports($feature) {
    switch($feature) {
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        // Artem Andreev: AFAIK it's not tested.
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        default:
            return null;
    }
}

/**
 * Add default set of statuses to the new attendance.
 *
 * @param int $attid - id of attendance instance.
 */
function att_add_default_statuses($attid) {
    global $DB;

    $statuses = $DB->get_recordset('attendance_statuses', array('attendanceid' => 0), 'id');
    foreach ($statuses as $st) {
        $rec = $st;
        $rec->attendanceid = $attid;
        $DB->insert_record('attendance_statuses', $rec);
    }
    $statuses->close();
}

/**
 * Add default set of warnings to the new attendance.
 *
 * @param int $id - id of attendance instance.
 */
function attendance_add_default_warnings($id) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/attendance/locallib.php');

    $warnings = $DB->get_recordset('attendance_warning',
        array('idnumber' => 0), 'id');
    foreach ($warnings as $n) {
        $rec = $n;
        $rec->idnumber = $id;
        $DB->insert_record('attendance_warning', $rec);
    }
    $warnings->close();
}

/**
 * Add new attendance instance.
 *
 * @param stdClass $attendance
 * @return bool|int
 */
function attendance_add_instance($attendance) {
    global $DB;

    $attendance->timemodified = time();

    $attendance->id = $DB->insert_record('attendance', $attendance);

    att_add_default_statuses($attendance->id);

    attendance_add_default_warnings($attendance->id);

    attendance_grade_item_update($attendance);

    return $attendance->id;
}

/**
 * Update existing attendance instance.
 *
 * @param stdClass $attendance
 * @return bool
 */
function attendance_update_instance($attendance) {
    global $DB;

    $attendance->timemodified = time();
    $attendance->id = $attendance->instance;

    if (! $DB->update_record('attendance', $attendance)) {
        return false;
    }

    attendance_grade_item_update($attendance);

    return true;
}

/**
 * Delete existing attendance
 *
 * @param int $id
 * @return bool
 */
function attendance_delete_instance($id) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/attendance/locallib.php');

    if (! $attendance = $DB->get_record('attendance', array('id' => $id))) {
        return false;
    }

    if ($sessids = array_keys($DB->get_records('attendance_sessions', array('attendanceid' => $id), '', 'id'))) {
        if (attendance_existing_calendar_events_ids($sessids)) {
            attendance_delete_calendar_events($sessids);
        }
        $DB->delete_records_list('attendance_log', 'sessionid', $sessids);
        $DB->delete_records('attendance_sessions', array('attendanceid' => $id));
    }
    $DB->delete_records('attendance_statuses', array('attendanceid' => $id));

    $DB->delete_records('attendance_warning', array('idnumber' => $id));

    $DB->delete_records('attendance', array('id' => $id));

    attendance_grade_item_delete($attendance);

    return true;
}

/**
 * Called by course/reset.php
 * @param moodleform $mform form passed by reference
 */
function attendance_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'attendanceheader', get_string('modulename', 'attendance'));

    $mform->addElement('static', 'description', get_string('description', 'attendance'),
                                get_string('resetdescription', 'attendance'));
    $mform->addElement('checkbox', 'reset_attendance_log', get_string('deletelogs', 'attendance'));

    $mform->addElement('checkbox', 'reset_attendance_sessions', get_string('deletesessions', 'attendance'));
    $mform->disabledIf('reset_attendance_sessions', 'reset_attendance_log', 'notchecked');

    $mform->addElement('checkbox', 'reset_attendance_statuses', get_string('resetstatuses', 'attendance'));
    $mform->setAdvanced('reset_attendance_statuses');
    $mform->disabledIf('reset_attendance_statuses', 'reset_attendance_log', 'notchecked');
}

/**
 * Course reset form defaults.
 *
 * @param stdClass $course
 * @return array
 */
function attendance_reset_course_form_defaults($course) {
    return array('reset_attendance_log' => 0, 'reset_attendance_statuses' => 0, 'reset_attendance_sessions' => 0);
}

/**
 * Reset user data within attendance.
 *
 * @param stdClass $data
 * @return array
 */
function attendance_reset_userdata($data) {
    global $DB;

    $status = array();

    $attids = array_keys($DB->get_records('attendance', array('course' => $data->courseid), '', 'id'));

    if (!empty($data->reset_attendance_log)) {
        $sess = $DB->get_records_list('attendance_sessions', 'attendanceid', $attids, '', 'id');
        if (!empty($sess)) {
            list($sql, $params) = $DB->get_in_or_equal(array_keys($sess));
            $DB->delete_records_select('attendance_log', "sessionid $sql", $params);
            list($sql, $params) = $DB->get_in_or_equal($attids);
            $DB->set_field_select('attendance_sessions', 'lasttaken', 0, "attendanceid $sql", $params);
            if (empty($data->reset_attendance_sessions)) {
                // If sessions are being retained, clear automarkcompleted value.
                $DB->set_field_select('attendance_sessions', 'automarkcompleted', 0, "attendanceid $sql", $params);
            }

            $status[] = array(
                'component' => get_string('modulenameplural', 'attendance'),
                'item' => get_string('attendancedata', 'attendance'),
                'error' => false
            );
        }
    }

    if (!empty($data->reset_attendance_statuses)) {
        $DB->delete_records_list('attendance_statuses', 'attendanceid', $attids);
        foreach ($attids as $attid) {
            att_add_default_statuses($attid);
        }

        $status[] = array(
            'component' => get_string('modulenameplural', 'attendance'),
            'item' => get_string('sessions', 'attendance'),
            'error' => false
        );
    }

    if (!empty($data->reset_attendance_sessions)) {
        $sessionsids = array_keys($DB->get_records_list('attendance_sessions', 'attendanceid', $attids, '', 'id'));
        if (attendance_existing_calendar_events_ids($sessionsids)) {
            attendance_delete_calendar_events($sessionsids);
        }
        $DB->delete_records_list('attendance_sessions', 'attendanceid', $attids);

        $status[] = array(
            'component' => get_string('modulenameplural', 'attendance'),
            'item' => get_string('statuses', 'attendance'),
            'error' => false
        );
    }

    return $status;
}
/**
 * Return a small object with summary information about what a
 *  user has done with a given particular instance of this module
 *  Used for user activity reports.
 *  $return->time = the time they did it
 *  $return->info = a short text description
 *
 * @param stdClass $course - full course record.
 * @param stdClass $user - full user record
 * @param stdClass $mod
 * @param stdClass $attendance
 * @return stdClass.
 */
function attendance_user_outline($course, $user, $mod, $attendance) {
    global $CFG;
    require_once(dirname(__FILE__).'/locallib.php');
    require_once($CFG->libdir.'/gradelib.php');

    $grades = grade_get_grades($course->id, 'mod', 'attendance', $attendance->id, $user->id);

    $result = new stdClass();
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        $result->time = $grade->dategraded;
    } else {
        $result->time = 0;
    }
    if (has_capability('mod/attendance:canbelisted', $mod->context, $user->id)) {
        $summary = new mod_attendance_summary($attendance->id, $user->id);
        $usersummary = $summary->get_all_sessions_summary_for($user->id);

        $result->info = format_float($usersummary->takensessionspoints, 1, true, true) . ' / ' .
                        format_float($usersummary->allsessionsmaxpoints, 1, true, true);
    }

    return $result;
}
/**
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $attendance
 */
function attendance_user_complete($course, $user, $mod, $attendance) {
    global $CFG;

    require_once(dirname(__FILE__).'/renderhelpers.php');
    require_once($CFG->libdir.'/gradelib.php');

    if (has_capability('mod/attendance:canbelisted', $mod->context, $user->id)) {
        echo construct_full_user_stat_html_table($attendance, $user);
    }
}

/**
 * Dummy function - must exist to allow quick editing of module name.
 *
 * @param stdClass $attendance
 * @param int $userid
 * @param bool $nullifnone
 */
function attendance_update_grades($attendance, $userid=0, $nullifnone=true) {
    // We need this function to exist so that quick editing of module name is passed to gradebook.
}
/**
 * Create grade item for given attendance
 *
 * @param stdClass $attendance object with extra cmidnumber
 * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function attendance_grade_item_update($attendance, $grades=null) {
    global $CFG, $DB;

    require_once('locallib.php');

    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    if (!isset($attendance->courseid)) {
        $attendance->courseid = $attendance->course;
    }
    if (!$DB->get_record('course', array('id' => $attendance->course))) {
        error("Course is misconfigured");
    }

    if (!empty($attendance->cmidnumber)) {
        $params = array('itemname' => $attendance->name, 'idnumber' => $attendance->cmidnumber);
    } else {
        // MDL-14303.
        $params = array('itemname' => $attendance->name);
    }

    if ($attendance->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $attendance->grade;
        $params['grademin']  = 0;
    } else if ($attendance->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$attendance->grade;

    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/attendance', $attendance->courseid, 'mod', 'attendance', $attendance->id, 0, $grades, $params);
}

/**
 * Delete grade item for given attendance
 *
 * @param object $attendance object
 * @return object attendance
 */
function attendance_grade_item_delete($attendance) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if (!isset($attendance->courseid)) {
        $attendance->courseid = $attendance->course;
    }

    return grade_update('mod/attendance', $attendance->courseid, 'mod', 'attendance',
                        $attendance->id, 0, null, array('deleted' => 1));
}

/**
 * This function returns if a scale is being used by one attendance
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See book, glossary or journal modules
 * as reference.
 *
 * @param int $attendanceid
 * @param int $scaleid
 * @return boolean True if the scale is used by any attendance
 */
function attendance_scale_used ($attendanceid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of attendance
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $scaleid
 * @return bool true if the scale is used by any book
 */
function attendance_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Serves the attendance sessions descriptions files.
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function attendance_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, false, $cm);

    if (!$DB->record_exists('attendance', array('id' => $cm->instance))) {
        return false;
    }

    // Session area is served by pluginfile.php.
    $fileareas = array('session');
    if (!in_array($filearea, $fileareas)) {
        return false;
    }

    $sessid = (int)array_shift($args);
    if (!$DB->record_exists('attendance_sessions', array('id' => $sessid))) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_attendance/$filearea/$sessid/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, true);
}

/**
 * Print tabs on attendance settings page.
 *
 * @param string $selected - current selected tab.
 */
function attendance_print_settings_tabs($selected = 'settings') {
    global $CFG;
    // Print tabs for different settings pages.
    $tabs = array();
    $tabs[] = new tabobject('settings', $CFG->wwwroot.'/admin/settings.php?section=modsettingattendance',
        get_string('settings', 'attendance'), get_string('settings'), false);

    $tabs[] = new tabobject('defaultstatus', $CFG->wwwroot.'/mod/attendance/defaultstatus.php',
        get_string('defaultstatus', 'attendance'), get_string('defaultstatus', 'attendance'), false);

    if (get_config('attendance', 'enablewarnings')) {
        $tabs[] = new tabobject('defaultwarnings', $CFG->wwwroot . '/mod/attendance/warnings.php',
            get_string('defaultwarnings', 'attendance'), get_string('defaultwarnings', 'attendance'), false);
    }

    $tabs[] = new tabobject('coursesummary', $CFG->wwwroot.'/mod/attendance/coursesummary.php',
        get_string('coursesummary', 'attendance'), get_string('coursesummary', 'attendance'), false);

    if (get_config('attendance', 'enablewarnings')) {
        $tabs[] = new tabobject('absentee', $CFG->wwwroot . '/mod/attendance/absentee.php',
            get_string('absenteereport', 'attendance'), get_string('absenteereport', 'attendance'), false);
    }

    $tabs[] = new tabobject('resetcalendar', $CFG->wwwroot.'/mod/attendance/resetcalendar.php',
        get_string('resetcalendar', 'attendance'), get_string('resetcalendar', 'attendance'), false);

    ob_start();
    print_tabs(array($tabs), $selected);
    $tabmenu = ob_get_contents();
    ob_end_clean();

    return $tabmenu;
}