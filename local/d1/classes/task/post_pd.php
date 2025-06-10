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
 * A scheduled task.
 *
 * Posts all ODL grades for students who have completed their course.
 *
 * @package    local_d1
 * @copyright  2022 Robert Russo, Louisiana State University
 */
namespace local_d1\task;

defined('MOODLE_INTERNAL') || die();

// Extend the Moodle scheduled task class with our mods.
class post_pd extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('post_pd_task', 'local_d1');
    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        global $CFG;
        require_once($CFG->dirroot . '/local/d1/lib.php');
        $d1 = new \d1();
        $d1->run_post_pd();

    }
}
