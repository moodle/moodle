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

namespace core\task;

/**
 * Report task for core automation backup.
 *
 * @package    core
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class automated_backup_report_task extends scheduled_task {

    /**
     * Get a descriptive name for the task (shown to admins).
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskautomatedbackup_report', 'admin');
    }

    /**
     * Do the job.
     */
    public function execute(): void {
        global $DB, $CFG;
        $queuedtasks = [];
        if ($value = get_config('backup', 'backup_auto_adhoctasks')) {
            $queuedtasks = explode(',', $value);
        }
        if (!empty($queuedtasks)) {
            // Some automated backup tasks are still running.
            // Check the status for each task.
            foreach ($queuedtasks as $taskid) {
                if (!$DB->record_exists('task_adhoc', ['id' => $taskid])) {
                    // The task has been completed. Remove it from the queue.
                    if (($key = array_search($taskid, $queuedtasks)) !== false) {
                        unset($queuedtasks[$key]);
                    }
                }
            }
            // Update the queue.
            set_config(
                'backup_auto_adhoctasks',
                implode(',', $queuedtasks),
                'backup',
            );
        }
        if (empty($queuedtasks) && get_config('backup', 'backup_auto_emailpending')) {
            // All the automated backup tasks have been completed. Send the report.
            $admin = get_admin();
            if (!$admin) {
                mtrace("Error: No admin account was found");
                return;
            }
            // Send email to admin if necessary.
            require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
            require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');
            \backup_cron_automated_helper::send_backup_status_to_admin($admin);
            // Remove the configs.
            unset_config(
                'backup_auto_adhoctasks',
                'backup',
            );
            unset_config(
                'backup_auto_emailpending',
                'backup',
            );
        }
    }
}
