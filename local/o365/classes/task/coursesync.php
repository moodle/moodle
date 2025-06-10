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
 * Create any needed groups in Microsoft 365.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\task;

use local_o365\feature\coursesync\main;
use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Create any needed groups in Microsoft 365.
 */
class coursesync extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_coursesync', 'local_o365');
    }

    /**
     * Do the job.
     *
     * @return bool|void
     */
    public function execute() {
        if (utils::is_connected() !== true) {
            return false;
        }

        if (\local_o365\feature\coursesync\utils::is_enabled() !== true) {
            mtrace('Course synchronisation not enabled, skipping...');
            return true;
        }

        try {
            $graphclient = utils::get_api();
        } catch (\Exception $e) {
            utils::debug('Exception: ' . $e->getMessage(), __METHOD__, $e);
            return false;
        }

        $coursesync = new main($graphclient, true);
        $coursesync->sync_courses();
        $coursesync->update_teams_cache();
    }
}
