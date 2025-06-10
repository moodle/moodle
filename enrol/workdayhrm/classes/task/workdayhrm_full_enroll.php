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
 * The task for running workdayhrm enrollment.
 *
 * @package    enrol_workdayhrm
 * @copyright  2023 onwards LSUOnline & Continuing Education
 * @copyright  2023 onwards Robert Russo
 */
namespace enrol_workdayhrm\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Extend the Moodle scheduled task class with ours.
 */
class workdayhrm_full_enroll extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('workdayhrm_full_enroll', 'enrol_workdayhrm');

    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;
        // Get the library required.
        require_once($CFG->dirroot . '/enrol/workdayhrm/lib.php');

        // Instantiate the plugin.
        $workdayhrm = new \enrol_workdayhrm_plugin();

        // Run the enrollment.
        $workdayhrm->run_workdayhrm_full_enroll();
    }
}
