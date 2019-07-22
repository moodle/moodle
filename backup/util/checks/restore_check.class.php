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
 * Non instantiable helper class providing different restore checks
 *
 * This class contains various static methods available in order to easily
 * perform a bunch of restore architecture tests
 *
 * TODO: Finish phpdocs
 */
abstract class restore_check {

    public static function check_courseid($courseid) {
        global $DB;
        // id must exist in course table
        if (! $DB->record_exists('course', array('id' => $courseid))) {
            throw new restore_controller_exception('restore_check_course_not_exists', $courseid);
        }
        return true;
    }

    public static function check_user($userid) {
        global $DB;
        // userid must exist in user table
        if (! $DB->record_exists('user', array('id' => $userid))) {
            throw new restore_controller_exception('restore_check_user_not_exists', $userid);
        }
        return true;
    }

    public static function check_security($restore_controller, $apply) {
        global $DB;

        if (! $restore_controller instanceof restore_controller) {
            throw new restore_controller_exception('restore_check_security_requires_restore_controller');
        }
        $restore_controller->log('checking plan security', backup::LOG_INFO);

        // Some handy vars
        $type     = $restore_controller->get_type();
        $mode     = $restore_controller->get_mode();
        $courseid = $restore_controller->get_courseid();
        $coursectx= context_course::instance($courseid);
        $userid   = $restore_controller->get_userid();

        // Note: all the checks along the function MUST be performed for $userid, that
        // is the user who "requested" the course restore, not current $USER at all!!

        // First of all, decide which caps/contexts are we going to check
        // for common backups (general, automated...) based exclusively
        // in the type (course, section, activity). And store them into
        // one capability => context array structure
        $typecapstocheck = array();
        switch ($type) {
            case backup::TYPE_1COURSE :
                $typecapstocheck['moodle/restore:restorecourse'] = $coursectx;
                break;
            case backup::TYPE_1SECTION :
                $typecapstocheck['moodle/restore:restoresection'] = $coursectx;
                break;
            case backup::TYPE_1ACTIVITY :
                $typecapstocheck['moodle/restore:restoreactivity'] = $coursectx;
                break;
            default :
                throw new restore_controller_exception('restore_unknown_restore_type', $type);
        }

        // Now, if restore mode is hub or import, check userid has permissions for those modes
        // other modes will perform common checks only (restorexxxx capabilities in $typecapstocheck)
        switch ($mode) {
            case backup::MODE_IMPORT:
                if (!has_capability('moodle/restore:restoretargetimport', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/restore:restoretargetimport';
                    throw new restore_controller_exception('restore_user_missing_capability', $a);
                }
                break;
            // Common backup (general, automated...), let's check all the $typecapstocheck
            // capability => context pairs
            default:
                foreach ($typecapstocheck as $capability => $context) {
                    if (!has_capability($capability, $context, $userid)) {
                        $a = new stdclass();
                        $a->userid = $userid;
                        $a->courseid = $courseid;
                        $a->capability = $capability;
                        throw new restore_controller_exception('restore_user_missing_capability', $a);
                    }
                }
        }

        // Now, enforce 'moodle/restore:userinfo' to 'users' setting, applying changes if allowed,
        // else throwing exception
        $userssetting = $restore_controller->get_plan()->get_setting('users');
        $prevvalue    = $userssetting->get_value();
        $prevstatus   = $userssetting->get_status();
        $hasusercap   = has_capability('moodle/restore:userinfo', $coursectx, $userid);

        // If setting is enabled but user lacks permission
        if (!$hasusercap && $prevvalue) { // If user has not the capability and setting is enabled
            // Now analyse if we are allowed to apply changes or must stop with exception
            if (!$apply) { // Cannot apply changes, throw exception
                $a = new stdclass();
                $a->setting = 'users';
                $a->value = $prevvalue;
                $a->capability = 'moodle/restore:userinfo';
                throw new restore_controller_exception('restore_setting_value_wrong_for_capability', $a);

            } else { // Can apply changes
                $userssetting->set_value(false);                              // Set the value to false
                $userssetting->set_status(base_setting::LOCKED_BY_PERMISSION);// Set the status to locked by perm
            }
        }

        // Now, if mode is HUB or IMPORT, and still we are including users in restore, turn them off
        // Defaults processing should have handled this, but we need to be 100% sure
        if ($mode == backup::MODE_IMPORT || $mode == backup::MODE_HUB) {
            $userssetting = $restore_controller->get_plan()->get_setting('users');
            if ($userssetting->get_value()) {
                $userssetting->set_value(false);                              // Set the value to false
                $userssetting->set_status(base_setting::LOCKED_BY_PERMISSION);// Set the status to locked by perm
            }
        }

        // Check the user has the ability to configure the restore. If not then we need
        // to lock all settings by permission so that no changes can be made. This does
        // not apply to the import facility, where all the activities (picked on backup)
        // are restored automatically without restore UI
        if ($mode != backup::MODE_IMPORT) {
            $hasconfigcap = has_capability('moodle/restore:configure', $coursectx, $userid);
            if (!$hasconfigcap) {
                $settings = $restore_controller->get_plan()->get_settings();
                foreach ($settings as $setting) {
                    $setting->set_status(base_setting::LOCKED_BY_PERMISSION);
                }
            }
        }

        if ($type == backup::TYPE_1COURSE) {
            // Ensure the user has the rolldates capability. If not we want to lock this
            // settings so that they cannot change it.
            $hasrolldatescap = has_capability('moodle/restore:rolldates', $coursectx, $userid);
            if (!$hasrolldatescap) {
                $startdatesetting = $restore_controller->get_plan()->get_setting('course_startdate');
                if ($startdatesetting) {
                    $startdatesetting->set_status(base_setting::NOT_LOCKED); // Permission lock overrides config lock.
                    $startdatesetting->set_value(false);
                    $startdatesetting->set_status(base_setting::LOCKED_BY_PERMISSION);
                }
            }

            // Ensure the user has the changefullname capability. If not we want to lock
            // the setting so that they cannot change it.
            $haschangefullnamecap = has_capability('moodle/course:changefullname', $coursectx, $userid);
            if (!$haschangefullnamecap) {
                $fullnamesetting = $restore_controller->get_plan()->get_setting('course_fullname');
                $fullnamesetting->set_status(base_setting::NOT_LOCKED); // Permission lock overrides config lock.
                $fullnamesetting->set_value(false);
                $fullnamesetting->set_status(base_setting::LOCKED_BY_PERMISSION);
            }

            // Ensure the user has the changeshortname capability. If not we want to lock
            // the setting so that they cannot change it.
            $haschangeshortnamecap = has_capability('moodle/course:changeshortname', $coursectx, $userid);
            if (!$haschangeshortnamecap) {
                $shortnamesetting = $restore_controller->get_plan()->get_setting('course_shortname');
                $shortnamesetting->set_status(base_setting::NOT_LOCKED); // Permission lock overrides config lock.
                $shortnamesetting->set_value(false);
                $shortnamesetting->set_status(base_setting::LOCKED_BY_PERMISSION);
            }

            // Ensure the user has the update capability. If not we want to lock
            // the overwrite setting so that they cannot change it.
            $hasupdatecap = has_capability('moodle/course:update', $coursectx, $userid);
            if (!$hasupdatecap) {
                $overwritesetting = $restore_controller->get_plan()->get_setting('overwrite_conf');
                $overwritesetting->set_status(base_setting::NOT_LOCKED); // Permission lock overrides config lock.
                $overwritesetting->set_value(false);
                $overwritesetting->set_status(base_setting::LOCKED_BY_PERMISSION);
            }

            // Ensure the user has the capability to manage enrolment methods. If not we want to unset and lock
            // the setting so that they cannot change it.
            $hasmanageenrolcap = has_capability('moodle/course:enrolconfig', $coursectx, $userid);
            if (!$hasmanageenrolcap) {
                if ($restore_controller->get_plan()->setting_exists('enrolments')) {
                    $enrolsetting = $restore_controller->get_plan()->get_setting('enrolments');
                    if ($enrolsetting->get_value() != backup::ENROL_NEVER) {
                        $enrolsetting->set_status(base_setting::NOT_LOCKED); // In case it was locked earlier.
                        $enrolsetting->set_value(backup::ENROL_NEVER);
                    }
                    $enrolsetting->set_status(base_setting::LOCKED_BY_PERMISSION);
                }
            }
        }

        return true;
    }
}
