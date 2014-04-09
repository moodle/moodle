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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to delete old password reset records.
 */
class password_reset_cleanup_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskpasswordresetcleanup', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $DB, $CFG;

        // Cleanup user password reset records.
        // Delete any reset request records which are expired by more than a day.
        // (We keep recently expired requests around so we can give a different error msg to users who
        // are trying to user a recently expired reset attempt).
        $pwresettime = isset($CFG->pwresettime) ? $CFG->pwresettime : 1800;
        $earliestvalid = time() - $pwresettime - DAYSECS;
        $DB->delete_records_select('user_password_resets', "timerequested < ?", array($earliestvalid));
        mtrace(' Cleaned up old password reset records');

    }

}
