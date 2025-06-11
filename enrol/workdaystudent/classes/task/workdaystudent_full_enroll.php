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
 * The task for running workdaystudent enrollment.
 *
 * @package    enrol_workdaystudent
 * @copyright  2023 onwards LSUOnline & Continuing Education
 * @copyright  2023 onwards Robert Russo
 */
namespace enrol_workdaystudent\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Extend the Moodle scheduled task class with ours.
 */
class workdaystudent_full_enroll extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('workdaystudent_full_enroll', 'enrol_workdaystudent');

    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        // Try to acquire a lock to prevent simultaneous execution with related tasks.
        $factory = \core\lock\lock_config::get_lock_factory('core');
        $lock = $factory->get_lock('workdaystudent_enroll_lock', 600);

        if (!$lock) {
            mtrace('Quick Enroll: Skipping because another task is holding the lock.');
            return;
        }

        try {
            // Get the library required.
            require_once($CFG->dirroot . '/enrol/workdaystudent/lib.php');

            // Instantiate the plugin.
            $workdaystudent = new \enrol_workdaystudent_plugin();

            // Run the enrollment.
            $workdaystudent->run_workdaystudent_full_enroll();

        } finally {
            // Always release the lock.
            $lock->release();
        }
    }
}
