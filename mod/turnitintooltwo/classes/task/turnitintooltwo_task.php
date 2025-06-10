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

namespace mod_turnitintooltwo\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Library function for turnitintooltwo task function.
 */

class turnitintooltwo_task extends \core\task\scheduled_task {

    public function get_name() {
        // Shown in admin screens.
        return get_string('task_name', 'mod_turnitintooltwo');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $tiitaskcall;

        // Run turnitintooltwo cron.
        require_once("{$CFG->dirroot}/mod/turnitintooltwo/lib.php");
        $tiitaskcall = true;
        turnitintooltwo_cron();
    }

}