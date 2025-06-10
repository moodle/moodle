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
 * The only other kalpanmaps function.
 *
 * @package    local_kalpanmaps
 * @copyright  2021 onwards LSUOnline & Continuing Education
 * @copyright  2021 onwards Robert Russo
 */
namespace local_kalpanmaps\task;

defined('MOODLE_INTERNAL') || die();

// Extend the Moodle scheduled task class with our mods.
class convert_kalvidres extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {

        return get_string('convert_kalvidres', 'local_kalpanmaps');

    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        global $CFG;
        require_once($CFG->dirroot . '/local/kalpanmaps/lib.php');
        $kalpanmaps = new \kalpanmaps();
        $kalpanmaps->run_convert_kalvidres();

    }
}
