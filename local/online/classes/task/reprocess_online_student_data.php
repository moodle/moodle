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
 * @copyright  2019 Louisiana State University
 */

namespace local_online\task;

use core\task\scheduled_task;
use core\task\manager as task_manager;

defined('MOODLE_INTERNAL') || die();

class reprocess_online_student_data extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('reprocess', 'local_online');
    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/online/student_reprocess.php');
        $online = new \local_online_plugin();
        $online->run_online_student_reprocess();

    }
}
