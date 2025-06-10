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
 * The only other natsane function.
 * Update Kaltura resources to have the appropriate course and uiconf_id.
 *
 * @package    local_natsane
 * @copyright  2017 Robert Russo, Louisiana State University
 */
namespace local_natsane\task;

defined('MOODLE_INTERNAL') || die();

// Extend the Moodle scheduled task class with our mods.
class fix_kaltura extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {

        return get_string('fix_kaltura_task', 'local_natsane');

    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        global $CFG;
        require_once($CFG->dirroot . '/local/natsane/lib.php');
        $natsane = new \natsane();
        $natsane->run_fix_kaltura();

    }
}
