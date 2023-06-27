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

        $restoreid = $this->get_custom_data()->backupid;
        $restorerecordid = $DB->get_field('backup_controllers', 'id', array('backupid' => $restoreid), MUST_EXIST);
        mtrace('Processing asynchronous restore for id: ' . $restoreid);

        // Get the restore controller by backup id.
        $rc = \restore_controller::load_controller($restoreid);
        $rc->set_progress(new \core\progress\db_updater($restorerecordid, 'backup_controllers', 'progress'));

        // Do some preflight checks on the restore.
        $status = $rc->get_status();
        $execution = $rc->get_execution();

        // Check that the restore is in the correct status and
        // that is set for asynchronous execution.
        if ($status == \backup::STATUS_AWAITING && $execution == \backup::EXECUTION_DELAYED) {
            // Execute the restore.
            $rc->execute_plan();

            // Send message to user if enabled.
            $messageenabled = (bool)get_config('backup', 'backup_async_message_users');
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

        // Cleanup.
        $rc->destroy();

        $duration = time() - $started;
        mtrace('Restore completed in: ' . $duration . ' seconds');
    }
}

