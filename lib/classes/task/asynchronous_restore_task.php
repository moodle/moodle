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
 * Adhoc task that performs asynchronous restores.
 *
 * @package    core
 * @copyright  2018 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

use async_helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Adhoc task that performs asynchronous restores.
 *
 * @package     core
 * @copyright   2018 Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class asynchronous_restore_task extends adhoc_task {

    /**
     * Run the adhoc task and preform the restore.
     */
    public function execute() {
        global $DB;
        $started = time();

        $customdata = $this->get_custom_data();
        $restoreid = $customdata->backupid;
        $restorerecord = $DB->get_record('backup_controllers', array('backupid' => $restoreid), 'id, controller', IGNORE_MISSING);
        // If the record doesn't exist, the backup controller failed to create. Unable to proceed.
        if (empty($restorerecord)) {
            mtrace('Unable to find restore controller, ending restore execution.');
            return;
        }

        mtrace('Processing asynchronous restore for id: ' . $restoreid);

        // Get the backup controller by backup id. If controller is invalid, this task can never complete.
        if ($restorerecord->controller === '') {
            mtrace('Bad restore controller status, invalid controller, ending restore execution.');
            return;
        }
        $rc = \restore_controller::load_controller($restoreid);
        try {
            $rc->set_progress(new \core\progress\db_updater($restorerecord->id, 'backup_controllers', 'progress'));

            // Do some preflight checks on the restore.
            $status = $rc->get_status();
            $execution = $rc->get_execution();

            // Check that the restore is in the correct status and
            // that is set for asynchronous execution.
            if ($status == \backup::STATUS_AWAITING && $execution == \backup::EXECUTION_DELAYED) {
                // Execute the restore.
                $rc->execute_plan();

                // Send message to user if enabled.
                $messageenabled = (bool) get_config('backup', 'backup_async_message_users');
                if ($messageenabled && $rc->get_status() == \backup::STATUS_FINISHED_OK) {
                    $asynchelper = new async_helper('restore', $restoreid);
                    $asynchelper->send_message();
                }

            } else {
                // If status isn't 700, it means the process has failed.
                // Retrying isn't going to fix it, so marked operation as failed.
                $rc->set_status(\backup::STATUS_FINISHED_ERR);
                mtrace('Bad backup controller status, is: ' . $status . ' should be 700, marking job as failed.');

            }

            $duration = time() - $started;
            mtrace('Restore completed in: ' . $duration . ' seconds');
        } catch (\Exception $e) {
            // If an exception is thrown, mark the restore as failed.
            $rc->set_status(\backup::STATUS_FINISHED_ERR);

            // Retrying isn't going to fix this, so add a no-retry flag to customdata.
            // We can cancel the task in the task manager.
            $customdata->noretry = true;
            $this->set_custom_data($customdata);

            mtrace('Exception thrown during restore execution, marking job as failed.');
            mtrace($e->getMessage());
        } finally {
            // Cleanup.
            // Always destroy the controller.
            $rc->destroy();
        }
    }
}
