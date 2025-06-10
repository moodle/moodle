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
 * A scheduled task for Backadel.
 *
 * @package    block_backadel
 * @copyright  2016 Louisiana State University, David Elliott, Robert Russo, Chad Mazilly <delliott@lsu.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_backadel\task;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/blocks/backadel/block_backadel.php');

/**
 * A scheduled task class for Backing up courses using the LSU Backadel Block.
 */
class backup_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('backuptask', 'block_backadel');
    }

    /**
     * Run backups
     */
    public function execute() {
        global $CFG;
        begin_backup_task();
    }

}

/**
 * Set up the backup task
 */
function begin_backup_task() {
    global $DB, $CFG;

    mtrace('Begin cron for BACKADEL!!!!!!!!!!!!!!!!!!!');

    // Grab the running status.
    $running = get_config('block_backadel', 'running');

    // If the task is running, let the user know how long it's been running.
    if ($running) {
        $minutesrun = round((time() - $running) / 60);
        echo "\n" . get_string('cron_already_running', 'block_backadel', $minutesrun) . "\n";
	// We no longer need ot do this now that scheduled tasks take care of this for us.
        // return;
    }

    // Set up the params.
    $params = array('status' => 'BACKUP');

    // Return true for courses where status = BACKUP.
    if (!$backups = $DB->get_records('block_backadel_statuses', $params)) {
        return true;
    }

    $error = false;
    $errorlog = '';

    // Set the running status to now.
    set_config('running', time(), 'block_backadel');

    // Loop through the courses to get backed up.
    foreach ($backups as $b) {
        $course = $DB->get_record('course', array('id' => $b->coursesid));
        echo "\n" . get_string('backing_up', 'block_backadel') . ' ' . $course->shortname . "\n";

        // Log any failures.
        $error = !backadel_backup_course($course);
        if ($error) {
            $errorlog .= get_string('cron_backup_error', 'block_backadel', $course->shortname) . "\n";
        } else {
           $errorlog .= $course->shortname . " backed up successfully.\n";
        }

        // Convert the status to the acceptable FAIL / SUCCESS keyword.
        $b->status = $error ? 'FAIL' : 'SUCCESS';
        // Update the DB with the appropriate status.
        $DB->update_record('block_backadel_statuses', $b);
    }

    // Clear the running flag.
    set_config('running', '', 'block_backadel');

    // Email the admins about the backup status.
    backadel_email_admins($errorlog);

    return true;
}
