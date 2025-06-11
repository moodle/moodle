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
 * Ad-hoc task to sync Moodle course role assignment changes to Microsoft Groups.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2022 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\task;

use core\task\adhoc_task;
use local_o365\feature\coursesync\main;
use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Ad-hoc task to sync Moodle course role assignment changes to Microsoft Groups.
 *
 * @package     local_o365
 * @subpackage  local_o365\task
 */
class groupmembershipsync extends adhoc_task {
    /**
     * Check if the course sync feature is enabled, get all courses that are enabled for sync, and resync owners and members.
     *
     * @return false|void
     */
    public function execute() {
        // If the sync direction is Teams to Moodle, we don't want to sync the course membership. Exiting.
        $courseusersyncdirection = get_config('local_o365', 'courseusersyncdirection');
        if ($courseusersyncdirection == COURSE_USER_SYNC_DIRECTION_TEAMS_TO_MOODLE) {
            mtrace('Sync direction is Teams to Moodle. Exiting.');
            return false;
        }

        if (utils::is_connected() !== true || \local_o365\feature\coursesync\utils::is_enabled() !== true) {
            return false;
        }

        $graphclient = \local_o365\feature\coursesync\utils::get_unified_api();
        if ($graphclient) {
            $coursesync = new main($graphclient);

            $coursesenabled = \local_o365\feature\coursesync\utils::get_enabled_courses(true);
            foreach ($coursesenabled as $courseid) {
                $coursesync->process_course_team_user_sync_from_moodle_to_microsoft($courseid);
            }
        }
    }
}
