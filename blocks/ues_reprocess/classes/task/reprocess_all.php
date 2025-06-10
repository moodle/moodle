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
 *
 * @package    block_ues_reprocess
 * @copyright  Louisiana State University
 * @copyright  The guy who did stuff: David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ues_reprocess\task;

/**
 * reprocess_all courses
 *
 * @package   block_ues reprocess_all 
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reprocess_all extends \core\task\scheduled_task {

    /**
     * Get task name
     */
    public function get_name() {
        return get_string('reprocess_all_courses', 'block_ues_reprocess');
    }

    /**
     * Execute task
     */
    public function execute() {
        global $CFG;

        // Log that we're starting.
        mtrace("Reprocessing Task Starting.");

        // Get the required class.
        require_once(dirname(dirname(__DIR__)). '/classes/repall.php');

        // Instantiate the class object.
        $repall = new \repall();

        // Run the task.
        $repall->run_it_all_task();
    }
}
