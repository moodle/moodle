<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * @package   mod_iomadcertificate
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   mod_certificate by Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add iomadcertificate instance.
 *
 * @param stdClass $iomadcertificate
 * @return int new iomadcertificate instance id
 */
function iomadcertificate_add_instance($iomadcertificate) {
    global $DB;

    // Create the iomadcertificate.
    $iomadcertificate->timecreated = time();
    $iomadcertificate->timemodified = $iomadcertificate->timecreated;

    return $DB->insert_record('iomadcertificate', $iomadcertificate);
}

/**
 * Update iomadcertificate instance.
 *
 * @param stdClass $iomadcertificate
 * @return bool true
 */
function iomadcertificate_update_instance($iomadcertificate) {
    global $DB;

    // Update the iomadcertificate.
    $iomadcertificate->timemodified = time();
    $iomadcertificate->id = $iomadcertificate->instance;

    return $DB->update_record('iomadcertificate', $iomadcertificate);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id
 * @return bool true if successful
 */
function iomadcertificate_delete_instance($id) {
    global $DB;

    // Ensure the iomadcertificate exists
    if (!$iomadcertificate = $DB->get_record('iomadcertificate', array('id' => $id))) {
        return false;
    }

    // Prepare file record object
    if (!$cm = get_coursemodule_from_instance('iomadcertificate', $id)) {
        return false;
    }

    $result = true;
    $DB->delete_records('iomadcertificate_issues', array('iomadcertificateid' => $id));
    if (!$DB->delete_records('iomadcertificate', array('id' => $id))) {
        $result = false;
    }

    // Delete any files associated with the iomadcertificate
    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    return $result;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all posts from the specified iomadcertificate
 * and clean up any related data.
 *
 * Written by Jean-Michel Vedrine
 *
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function iomadcertificate_reset_userdata($data) {
    global $DB;

    $componentstr = get_string('modulenameplural', 'iomadcertificate');
    $status = array();

    if (!empty($data->reset_iomadcertificate)) {
        $sql = "SELECT cert.id
                  FROM {iomadcertificate} cert
                 WHERE cert.course = :courseid";
        $params = array('courseid' => $data->courseid);
        $iomadcertificates = $DB->get_records_sql($sql, $params);
        $fs = get_file_storage();
        if ($iomadcertificates) {
            foreach ($iomadcertificates as $certid => $unused) {
                if (!$cm = get_coursemodule_from_instance('iomadcertificate', $certid)) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                $fs->delete_area_files($context->id, 'mod_iomadcertificate', 'issue');
            }
        }

        $DB->delete_records_select('iomadcertificate_issues', "iomadcertificateid IN ($sql)", $params);
        $status[] = array('component' => $componentstr, 'item' => get_string('removecert', 'iomadcertificate'), 'error' => false);
    }
    // Updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('iomadcertificate', array('timeopen', 'timeclose'), $data->timeshift, $data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('datechanged'), 'error' => false);
    }

    return $status;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the iomadcertificate.
 *
 * Written by Jean-Michel Vedrine
 *
 * @param $mform form passed by reference
 */
function iomadcertificate_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'iomadcertificateheader', get_string('modulenameplural', 'iomadcertificate'));
    $mform->addElement('advcheckbox', 'reset_iomadcertificate', get_string('deletissuediomadcertificates', 'iomadcertificate'));
}

/**
 * Course reset form defaults.
 *
 * Written by Jean-Michel Vedrine
 *
 * @param stdClass $course
 * @return array
 */
function iomadcertificate_reset_course_form_defaults($course) {
    return array('reset_iomadcertificate' => 1);
}

/**
 * Returns information about received iomadcertificate.
 * Used for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $iomadcertificate
 * @return stdClass the user outline object
 */
function iomadcertificate_user_outline($course, $user, $mod, $iomadcertificate) {
    global $DB;

    $result = new stdClass;
    if ($issue = $DB->get_record('iomadcertificate_issues', array('iomadcertificateid' => $iomadcertificate->id, 'userid' => $user->id))) {
        $result->info = get_string('issued', 'iomadcertificate');
        $result->time = $issue->timecreated;
    } else {
        $result->info = get_string('notissued', 'iomadcertificate');
    }

    return $result;
}

/**
 * Returns information about received iomadcertificate.
 * Used for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $iomadcertificate
 * @return string the user complete information
 */
function iomadcertificate_user_complete($course, $user, $mod, $iomadcertificate) {
    global $DB, $OUTPUT, $CFG;
    require_once($CFG->dirroot.'/mod/iomadcertificate/locallib.php');

    if ($issue = $DB->get_record('iomadcertificate_issues', array('iomadcertificateid' => $iomadcertificate->id, 'userid' => $user->id))) {
        echo $OUTPUT->box_start();
        echo get_string('issued', 'iomadcertificate') . ": ";
        echo userdate($issue->timecreated);
        $cm = get_coursemodule_from_instance('iomadcertificate', $iomadcertificate->id, $course->id);
        iomadcertificate_print_user_files($iomadcertificate, $user->id, context_module::instance($cm->id)->id);
        echo '<br />';
        echo $OUTPUT->box_end();
    } else {
        print_string('notissuedyet', 'iomadcertificate');
    }
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of iomadcertificate.
 *
 * @param int $iomadcertificateid
 * @return stdClass list of participants
 */
function iomadcertificate_get_participants($iomadcertificateid) {
    global $DB;

    $sql = "SELECT DISTINCT u.id, u.id
              FROM {user} u, {iomadcertificate_issues} a
             WHERE a.iomadcertificateid = :iomadcertificateid
               AND u.id = a.userid";
    return  $DB->get_records_sql($sql, array('iomadcertificateid' => $iomadcertificateid));
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function iomadcertificate_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Serves iomadcertificate issues and other files.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool|nothing false if file not found, does not return anything if found - just send the file
 */
function iomadcertificate_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB, $certuser;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    if (!$iomadcertificate = $DB->get_record('iomadcertificate', array('id' => $cm->instance))) {
        return false;
    }

    require_login($course, false, $cm);

    require_once($CFG->libdir.'/filelib.php');

    $certrecord = (int)array_shift($args);

    if (!$certrecord = $DB->get_record('iomadcertificate_issues', array('id' => $certrecord))) {
        return false;
    }

    $canmanageiomadcertificate = has_capability('mod/iomadcertificate:manage', $context);
    if ($certuser->id != $certrecord->userid and !$canmanageiomadcertificate) {
        return false;
    }

    if ($filearea === 'issue') {
        $relativepath = implode('/', $args);
        $fullpath = "/{$context->id}/mod_iomadcertificate/issue/$certrecord->id/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        send_stored_file($file, 0, 0, true); // download MUST be forced - security!
    } else if ($filearea === 'onthefly') {
        require_once($CFG->dirroot.'/mod/iomadcertificate/locallib.php');
        require_once("$CFG->libdir/pdflib.php");

        if (!$iomadcertificate = $DB->get_record('iomadcertificate', array('id' => $certrecord->iomadcertificateid))) {
            return false;
        }

        if ($iomadcertificate->requiredtime && !$canmanageiomadcertificate) {
            if (iomadcertificate_get_course_time($course->id) < ($iomadcertificate->requiredtime * 60)) {
                return false;
            }
        }

        // Load the specific iomadcertificate type. It will fill the $pdf var.
        require("$CFG->dirroot/mod/iomadcertificate/type/$iomadcertificate->iomadcertificatetype/iomadcertificate.php");
        $filename = iomadcertificate_get_iomadcertificate_filename($iomadcertificate, $cm, $course) . '.pdf';
        $filecontents = $pdf->Output('', 'S');
        send_file($filecontents, $filename, 0, 0, true, true, 'application/pdf');
    }
}

/**
 * Used for course participation report (in case iomadcertificate is added).
 *
 * @return array
 */
function iomadcertificate_get_view_actions() {
    return array('view', 'view all', 'view report');
}

/**
 * Used for course participation report (in case iomadcertificate is added).
 *
 * @return array
 */
function iomadcertificate_get_post_actions() {
    return array('received');
}
