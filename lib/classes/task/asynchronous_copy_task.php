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
 * Adhoc task that performs asynchronous course copies.
 *
 * @package    core
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

use async_helper;
use cache_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');

/**
 * Adhoc task that performs asynchronous course copies.
 *
 * @package     core
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class asynchronous_copy_task extends adhoc_task {

    /**
     * Run the adhoc task and preform the backup.
     */
    public function execute() {
        global $CFG, $DB;
        $started = time();

        $backupid = $this->get_custom_data()->backupid;
        $restoreid = $this->get_custom_data()->restoreid;
        $backuprecord = $DB->get_record('backup_controllers', array('backupid' => $backupid), 'id, itemid', MUST_EXIST);
        $restorerecord = $DB->get_record('backup_controllers', array('backupid' => $restoreid), 'id, itemid', MUST_EXIST);

        // First backup the course.
        mtrace('Course copy: Processing asynchronous course copy for course id: ' . $backuprecord->itemid);
        try {
            $bc = \backup_controller::load_controller($backupid); // Get the backup controller by backup id.
        } catch (\backup_dbops_exception $e) {
            mtrace('Course copy: Can not load backup controller for copy, marking job as failed');
            delete_course($restorerecord->itemid, false); // Clean up partially created destination course.
            return; // Return early as we can't continue.
        }

        $rc = \restore_controller::load_controller($restoreid);  // Get the restore controller by restore id.
        $bc->set_progress(new \core\progress\db_updater($backuprecord->id, 'backup_controllers', 'progress'));
        $copyinfo = $rc->get_copy();
        $backupplan = $bc->get_plan();

        $keepuserdata = (bool)$copyinfo->userdata;
        $keptroles = $copyinfo->keptroles;

        $bc->set_kept_roles($keptroles);

        // If we are not keeping user data don't include users or data in the backup.
        // In this case we'll add the user enrolments at the end.
        // Also if we have no roles to keep don't backup users.
        if (empty($keptroles) || !$keepuserdata) {
            $backupplan->get_setting('users')->set_status(\backup_setting::NOT_LOCKED);
            $backupplan->get_setting('users')->set_value('0');
        } else {
            $backupplan->get_setting('users')->set_value('1');
        }

        // Do some preflight checks on the backup.
        $status = $bc->get_status();
        $execution = $bc->get_execution();
        // Check that the backup is in the correct status and
        // that is set for asynchronous execution.
        if ($status == \backup::STATUS_AWAITING && $execution == \backup::EXECUTION_DELAYED) {
            // Execute the backup.
            mtrace('Course copy: Backing up course, id: ' . $backuprecord->itemid);
            $bc->execute_plan();

        } else {
            // If status isn't 700, it means the process has failed.
            // Retrying isn't going to fix it, so marked operation as failed.
            mtrace('Course copy: Bad backup controller status, is: ' . $status . ' should be 700, marking job as failed.');
            $bc->set_status(\backup::STATUS_FINISHED_ERR);
            delete_course($restorerecord->itemid, false); // Clean up partially created destination course.
            $bc->destroy();
            return; // Return early as we can't continue.

        }

        $results = $bc->get_results();
        $backupbasepath = $backupplan->get_basepath();
        $file = $results['backup_destination'];
        $file->extract_to_pathname(get_file_packer('application/vnd.moodle.backup'), $backupbasepath);
        // Start the restore process.
        $rc->set_progress(new \core\progress\db_updater($restorerecord->id, 'backup_controllers', 'progress'));
        $rc->prepare_copy();

        // Set the course settings we can do now (the remaining settings will be done after restore completes).
        $plan = $rc->get_plan();

        $startdate = $plan->get_setting('course_startdate');
        $startdate->set_value($copyinfo->startdate);
        $fullname = $plan->get_setting('course_fullname');
        $fullname->set_value($copyinfo->fullname);
        $shortname = $plan->get_setting('course_shortname');
        $shortname->set_value($copyinfo->shortname);

        // Do some preflight checks on the restore.
        $rc->execute_precheck();
        $status = $rc->get_status();
        $execution = $rc->get_execution();

        // Check that the restore is in the correct status and
        // that is set for asynchronous execution.
        if ($status == \backup::STATUS_AWAITING && $execution == \backup::EXECUTION_DELAYED) {
            // Execute the restore.
            mtrace('Course copy: Restoring into course, id: ' . $restorerecord->itemid);
            $rc->execute_plan();

        } else {
            // If status isn't 700, it means the process has failed.
            // Retrying isn't going to fix it, so marked operation as failed.
            mtrace('Course copy: Bad backup controller status, is: ' . $status . ' should be 700, marking job as failed.');
            $rc->set_status(\backup::STATUS_FINISHED_ERR);
            delete_course($restorerecord->itemid, false); // Clean up partially created destination course.
            $file->delete();
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($backupbasepath);
            }
            $rc->destroy();
            return; // Return early as we can't continue.

        }

        // Copy user enrolments from source course to destination.
        if (!empty($keptroles) && !$keepuserdata) {
            mtrace('Course copy: Creating user enrolments in destination course.');
            $context = \context_course::instance($backuprecord->itemid);

            $enrol = enrol_get_plugin('manual');
            $instance = null;
            $enrolinstances = enrol_get_instances($restorerecord->itemid, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if ($courseenrolinstance->enrol == 'manual') {
                    $instance = $courseenrolinstance;
                    break;
                }
            }

            // Abort if there enrolment plugin problems.
            if (empty($enrol) || empty($instance)) {
                mtrace('Course copy: Could not enrol users in course.');;
                delete_course($restorerecord->itemid, false);
                return;
            }

            // Enrol the users from the source course to the destination.
            foreach ($keptroles as $roleid) {
                $sourceusers = get_role_users($roleid, $context);
                foreach ($sourceusers as $sourceuser) {
                    $enrol->enrol_user($instance, $sourceuser->id, $roleid);
                }
            }
        }

        // Set up remaining course settings.
        $course = $DB->get_record('course', array('id' => $restorerecord->itemid), '*', MUST_EXIST);
        $course->visible = $copyinfo->visible;
        $course->idnumber = $copyinfo->idnumber;
        $course->enddate = $copyinfo->enddate;

        $DB->update_record('course', $course);

        // Send message to user if enabled.
        $messageenabled = (bool)get_config('backup', 'backup_async_message_users');
        if ($messageenabled && $rc->get_status() == \backup::STATUS_FINISHED_OK) {
            mtrace('Course copy: Sending user notification.');
            $asynchelper = new async_helper('copy', $restoreid);
            $messageid = $asynchelper->send_message();
            mtrace('Course copy: Sent message: ' . $messageid);
        }

        // Cleanup.
        $bc->destroy();
        $rc->destroy();
        $file->delete();
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backupbasepath);
        }

        rebuild_course_cache($restorerecord->itemid, true);
        cache_helper::purge_by_event('changesincourse');

        $duration = time() - $started;
        mtrace('Course copy: Copy completed in: ' . $duration . ' seconds');
    }
}
