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
 * Adhoc task that performs single automated course backup.
 *
 * @package     core
 * @copyright   2019 John Yao <johnyao@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');

/**
 * Adhoc task that performs single automated course backup.
 *
 * @package     core
 * @copyright   2019 John Yao <johnyao@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_backup_task extends \core\task\adhoc_task {

    /**
     * Run the adhoc task and preform the backup.
     */
    public function execute() {
        global $DB;

        $lockfactory = \core\lock\lock_config::get_lock_factory('course_backup_adhoc');
        $courseid = $this->get_custom_data()->courseid;

        try {
            $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        } catch (moodle_exception $e) {
            mtrace('Invalid course id: ' . $courseid . ', task aborted.');
            return;
        }

        if (!$lock = $lockfactory->get_lock('course_backup_adhoc_task_' . $courseid, 10)) {
            mtrace('Backup adhoc task for: ' . $course->fullname . 'is already running.');
            return;
        } else {
            mtrace('Processing automated backup for course: ' . $course->fullname);
        }

        try {
            $backupcourse = $DB->get_record('backup_courses', array(
                'courseid' => $courseid,
                'laststatus' => \backup_cron_automated_helper::BACKUP_STATUS_QUEUED
            ), '*', MUST_EXIST);

            $adminid = $this->get_custom_data()->adminid;
            $backupcourse->laststarttime = time();
            $backupcourse->laststatus = \backup_cron_automated_helper::BACKUP_STATUS_UNFINISHED;
            $DB->update_record('backup_courses', $backupcourse);

            $backupcourse->laststatus = \backup_cron_automated_helper::launch_automated_backup($course, time(), $adminid);
            if ($backupcourse->laststatus == \backup_cron_automated_helper::BACKUP_STATUS_ERROR ||
                $backupcourse->laststatus == \backup_cron_automated_helper::BACKUP_STATUS_UNFINISHED) {
                mtrace('Automated backup for course: ' . $course->fullname . ' failed.');
                // Reset unfinished to error.
                $backupcourse->laststatus = \backup_cron_automated_helper::BACKUP_STATUS_ERROR;
            }

            // Remove excess backups.
            $removedcount = \backup_cron_automated_helper::remove_excess_backups($course, time());
            $backupcourse->lastendtime = time();
            $backupcourse->nextstarttime = \backup_cron_automated_helper::calculate_next_automated_backup(null, time());
            $DB->update_record('backup_courses', $backupcourse);
        } catch (moodle_exception $e) {
            mtrace('Automated backup for course: ' . $course->fullname . ' encounters an error.');
            mtrace('Exception: ' . $e->getMessage());
            mtrace('Debug: ' . $e->debuginfo);
        } finally {
            // Everything is finished release lock.
            $lock->release();
            mtrace('Automated backup for course: ' . $course->fullname . ' completed.');
        }
    }
}
