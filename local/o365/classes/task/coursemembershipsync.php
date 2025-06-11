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
 * A scheduled task to sync Microsoft Teams owners and members to Moodle courses.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\task;

use core\task\scheduled_task;
use local_o365\feature\coursesync\main;
use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * A scheduled task to sync Microsoft Teams owners and members to Moodle courses.
 *
 * @package     local_o365
 * @subpackage  local_o365\task
 */
class coursemembershipsync extends scheduled_task {
    /**
     * Get the name of the task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('task_coursemembershipsync', 'local_o365');
    }

    /**
     * Execute the task.
     *
     * @return bool
     */
    public function execute(): bool {
        // If the sync direction is Moodle to Teams, we don't want to sync the course membership.
        $courseusersyncdirection = get_config('local_o365', 'courseusersyncdirection');
        if ($courseusersyncdirection == COURSE_USER_SYNC_DIRECTION_MOODLE_TO_TEAMS) {
            mtrace('Sync direction is Moodle to Teams. Exiting.');
            return false;
        }

        // Get role IDs from config.
        $ownerroleid = get_config('local_o365', 'coursesyncownerrole');
        $memberroleid = get_config('local_o365', 'coursesyncmemberrole');
        if (!$ownerroleid || !$memberroleid) {
            mtrace('Owner or member role ID is not set. Exiting.');
            return false;
        }

        if (utils::is_connected() !== true || \local_o365\feature\coursesync\utils::is_enabled() !== true) {
            return false;
        }

        $graphclient = \local_o365\feature\coursesync\utils::get_unified_api();

        if ($graphclient) {
            $coursesync = new main($graphclient, true);
            $coursesenabled = \local_o365\feature\coursesync\utils::get_enabled_courses(true);

            $connectedusers = utils::get_connected_users();

            // Sync the course membership for each course.
            foreach ($coursesenabled as $courseid) {
                $coursesync->process_course_team_user_sync_from_microsoft_to_moodle($courseid, '', $connectedusers);
            }
        }

        return true;
    }
}
