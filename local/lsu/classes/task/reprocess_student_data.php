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
 * Top-level LSU student data function.
 *
 * @package    enrol_ues
 * @copyright  2015 Louisiana State University
 */

namespace local_lsu\task;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;
use core\task\manager as task_manager;

class reprocess_student_data extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('reprocess', 'local_lsu');
    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/lsu/student_reprocess.php');
        $lsu = new \local_lsu_plugin();
        $lsu->run_student_reprocess();

    }
}
