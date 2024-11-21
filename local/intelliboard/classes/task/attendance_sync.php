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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\task;

use local_intelliboard\attendance\attendance_api;
use local_intelliboard\tools\bb_collaborate_tool;

/**
 * Task to sync data with attendance
 *
 * @copyright  2019 Intelliboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_sync extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_name() {
        return get_string('sync_data_with_attendance', 'local_intelliboard');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     * @return bool
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \Exception
     */
    public function execute() {
        if(
            !get_config('local_intelliboard', 'enable_bb_col_meetings') or
            !get_config('local_intelliboard', 'enablesyncattendance')
        ) {
            return false;
        }

        // Sync BlackBoard Collaborate sessions
        $repository = bb_collaborate_tool::repository();
        $sessions = $repository->not_synchronized_sessions();
        $attendanceapi = new attendance_api();

        foreach($sessions as $session) {
            $session->timestart = date(
                'c', $session->timestart
            );
            $session->timeend = date(
                'c', $session->timeend
            );
            $session->external_session_type = 'bb_collaborate';

            try {
                $response = json_decode(
                    $attendanceapi->create_session((array) $session)
                );
            } catch(\Exception $e) {
                if(get_config('local_intelliboard', 'bb_col_debug')) {
                    var_dump($e);
                }
                continue;
            }

            if(isset($response->created) && $response->created) {
                bb_collaborate_tool::service()->mark_session_synchronized(
                    $session->id, $response->instance_id
                );
            }
        }

        return true;
    }

}