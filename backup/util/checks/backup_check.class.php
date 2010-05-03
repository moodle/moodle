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
 * @package    moodlecore
 * @subpackage backup-factories
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing different backup checks
 *
 * This class contains various static methods available in order to easily
 * perform a bunch of backup architecture tests
 *
 * TODO: Finish phpdocs
 */
abstract class backup_check {

    public static function check_format_and_type($format, $type) {
        global $CFG;

        $file = $CFG->dirroot . '/backup/' . $format . '/backup_plan_builder.class.php';
        if (! file_exists($file)) {
            throw new backup_controller_exception('backup_check_unsupported_format', $format);
        }
        require_once($file);
        if (!in_array($type, backup_plan_builder::supported_backup_types())) {
            throw new backup_controller_exception('backup_check_unsupported_type', $type);
        }

        require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
    }

    public static function check_id($type, $id) {
        global $DB;
        switch ($type) {
            case backup::TYPE_1ACTIVITY:
                // id must exist in course_modules table
                if (! $DB->record_exists('course_modules', array('id' => $id))) {
                    throw new backup_controller_exception('backup_check_module_not_exists', $id);
                }
                break;
            case backup::TYPE_1SECTION:
                // id must exist in course_sections table
                if (! $DB->record_exists('course_sections', array('id' => $id))) {
                    throw new backup_controller_exception('backup_check_section_not_exists', $id);
                }
                break;
            case backup::TYPE_1COURSE:
                // id must exist in course table
                if (! $DB->record_exists('course', array('id' => $id))) {
                    throw new backup_controller_exception('backup_check_course_not_exists', $id);
                }
                break;
            default:
                throw new backup_controller_exception('backup_check_incorrect_type', $type);
        }
        return true;
    }

    public static function check_user($userid) {
        global $DB;
        // userid must exist in user table
        if (! $DB->record_exists('user', array('id' => $userid))) {
            throw new backup_controller_exception('backup_check_user_not_exists', $userid);
        }
        return true;
    }

    public static function check_security($backup_controller, $apply) {
        global $DB;

        if (! $backup_controller instanceof backup_controller) {
            throw new backup_controller_exception('backup_check_security_requires_backup_controller');
        }
        $backup_controller->log('checking plan security', backup::LOG_INFO);

        // Some handy vars
        $type     = $backup_controller->get_type();
        $mode     = $backup_controller->get_mode();
        $courseid = $backup_controller->get_courseid();
        $coursectx= get_context_instance(CONTEXT_COURSE, $courseid);
        $userid   = $backup_controller->get_userid();
        $id       = $backup_controller->get_id(); // courseid / sectionid / cmid

        // Note: all the checks along the function MUST be performed for $userid, that
        // is the user who "requested" the course backup, not current $USER at all!!

        // First of all, check the main backup[course|section|activity] principal caps
        // Lacking the corresponding one makes this to break with exception always
        switch ($type) {
            case backup::TYPE_1COURSE :
                $DB->get_record('course', array('id' => $id), '*', MUST_EXIST); // course exists
                if (!has_capability('moodle/backup:backupcourse', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/backup:backupcourse';
                    throw new backup_controller_exception('backup_user_missing_capability', $a);
                }
                break;
            case backup::TYPE_1SECTION :
                $DB->get_record('course_sections', array('course' => $courseid, 'id' => $id), '*', MUST_EXIST); // sec exists
                if (!has_capability('moodle/backup:backupsection', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/backup:backupsection';
                    throw new backup_controller_exception('backup_user_missing_capability', $a);
                }
                break;
            case backup::TYPE_1ACTIVITY :
                get_coursemodule_from_id(null, $id, $courseid, false, MUST_EXIST); // cm exists
                $modulectx = get_context_instance(CONTEXT_MODULE, $id);
                if (!has_capability('moodle/backup:backupactivity', $modulectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->cmid = $id;
                    $a->capability = 'moodle/backup:backupactivity';
                    throw new backup_controller_exception('backup_user_missing_capability', $a);
                }
                break;
            default :
                print_error('unknownbackuptype');
        }

        // Now, if backup mode is hub or import, check userid has permissions for those modes
        switch ($mode) {
            case backup::MODE_HUB:
                if (!has_capability('moodle/backup:backuptargethub', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/backup:backuptargethub';
                    throw new backup_controller_exception('backup_user_missing_capability', $a);
                }
                break;
            case backup::MODE_IMPORT:
                if (!has_capability('moodle/backup:backuptargetimport', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/backup:backuptargetimport';
                    throw new backup_controller_exception('backup_user_missing_capability', $a);
                }
                break;
        }

        // Now, enforce 'moodle/backup:userinfo' to 'users' setting, applying changes if allowed,
        // else throwing exception
        $userssetting = $backup_controller->get_plan()->get_setting('users');
        $prevvalue    = $userssetting->get_value();
        $prevstatus   = $userssetting->get_status();
        $hasusercap   = has_capability('moodle/backup:userinfo', $coursectx, $userid);

        // If setting is enabled but user lacks permission
        if (!$hasusercap && $prevvalue) { // If user has not the capability and setting is enabled
            // Now analyse if we are allowed to apply changes or must stop with exception
            if (!$apply) { // Cannot apply changes, throw exception
                $a = new stdclass();
                $a->setting = 'users';
                $a->value = $prevvalue;
                $a->capability = 'moodle/backup:userinfo';
                throw new backup_controller_exception('backup_setting_value_wrong_for_capability', $a);

            } else { // Can apply changes
                $userssetting->set_value(false);                              // Set the value to false
                $userssetting->set_status(base_setting::LOCKED_BY_PERMISSION);// Set the status to locked by perm
            }
        }

        // Now, enforce 'moodle/backup:anonymise' to 'anonymise' setting, applying changes if allowed,
        // else throwing exception
        $anonsetting = $backup_controller->get_plan()->get_setting('anonymize');
        $prevvalue   = $userssetting->get_value();
        $prevstatus  = $userssetting->get_status();
        $hasanoncap  = has_capability('moodle/backup:anonymise', $coursectx, $userid);

        // If setting is enabled but user lacks permission
        if (!$hasanoncap && $prevvalue) { // If user has not the capability and setting is enabled
            // Now analyse if we are allowed to apply changes or must stop with exception
            if (!$apply) { // Cannot apply changes, throw exception
                $a = new stdclass();
                $a->setting = 'anonymize';
                $a->value = $prevvalue;
                $a->capability = 'moodle/backup:anonymise';
                throw new backup_controller_exception('backup_setting_value_wrong_for_capability', $a);

            } else { // Can apply changes
                $anonsetting->set_value(false);                              // Set the value to false
                $anonsetting->set_status(base_setting::LOCKED_BY_PERMISSION);// Set the status to locked by perm
            }
        }

        // Now, if mode is HUB or IMPORT, and still we are including users in backup, turn them off
        // Defaults processing should have handled this, but we need to be 100% sure
        if ($mode == backup::MODE_IMPORT || $mode == backup::MODE_HUB) {
            $userssetting = $backup_controller->get_plan()->get_setting('users');
            if ($userssetting->get_value()) {
                $userssetting->set_value(false);                              // Set the value to false
                $userssetting->set_status(base_setting::LOCKED_BY_PERMISSION);// Set the status to locked by perm
            }
        }

        return true;
    }
}
