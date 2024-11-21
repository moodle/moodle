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

use local_intelliboard\bb_collaborate\session_attendances;
use local_intelliboard\event\local_intelliboard_bb_collaborate_session_tracking_finished;
use local_intelliboard\event\local_intelliboard_bb_collaborate_session_tracking_started;
use local_intelliboard\tools\bb_collaborate_tool;

/**
 * Task to process active BB collaborate meetings
 *
 * @copyright  2019 Intelliboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_active_bb_collaborate_meetings extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_name() {
        return get_string('check_active_bb_col_meetings', 'local_intelliboard');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     * @return bool
     * @throws \dml_exception
     */
    public function execute() {
        global $USER;
        if(!get_config('local_intelliboard', 'enable_bb_col_meetings')) {
            return false;
        }

        $service = bb_collaborate_tool::service();
        $repository = bb_collaborate_tool::repository();
        $adapter = bb_collaborate_tool::adapter();

        foreach($repository->getNonTrackedSessions() as $session) {
            if (get_config('local_intelliboard', 'bb_col_debug')) {
                $event = local_intelliboard_bb_collaborate_session_tracking_started::create([
                    'objectid' => $session->id,
                    'userid' => $USER->id,
                    'relateduserid' => $USER->id,
                    'context' => \context_system::instance(),
                    'other' => ['sessionuid' => $session->sessionuid]
                ]);
                $event->trigger();
            }

            // skip session tracking if enabled synchronization with attendance
            // but session not be synchronized with attendance service
            if (
                get_config('local_intelliboard', 'enablesyncattendance') &&
                !$session->sync_data
            ) {
                continue;
            }

            try {
                $sesioninstances = $adapter->get_session_instances(
                    $session->sessionuid
                );
            } catch (\Exception $e) {
                // Mark a session tracked if we more than 72 hours can not get API data of the session
                if ((time() - $session->timestart) > (DAYSECS * 3)) {
                    if (get_config('local_intelliboard', 'bb_col_debug')) {
                        $event = local_intelliboard_bb_collaborate_session_tracking_finished::create([
                            'objectid' => $session->id,
                            'userid' => $USER->id,
                            'relateduserid' => $USER->id,
                            'context' => \context_system::instance(),
                            'other' => [
                                'sessionuid' => $session->sessionuid,
                                'message' => 'Have not received data from API for 72 hours',
                                'api_response' => $e->getMessage()
                            ]
                        ]);
                        $event->trigger();
                    }

                    $service->mark_session_tracked($session->sessionuid);
                }

                continue;
            }

            try {
                if (empty($sesioninstances) && (time() - $session->timestart) < (DAYSECS * 3)) {
                    continue;
                }

                $service->mark_session_tracked($session->sessionuid);

                if($sesioninstances) {
                    $sessionattendees = new session_attendances(
                        $session,
                        $adapter->get_session_attendees(
                            $session->sessionuid, $sesioninstances[0]['id']
                        )
                    );
                    $service->insert_session_attendees(
                        $session->sessionuid, $sessionattendees->get_attendances()
                    );

                    if (get_config('local_intelliboard', 'enablesyncattendance')) {
                        $service->synchronize_attendances(
                            $session, $sessionattendees
                        );
                    }
                }

                if (get_config('local_intelliboard', 'bb_col_debug')) {
                    $event = local_intelliboard_bb_collaborate_session_tracking_finished::create([
                        'objectid' => $session->id,
                        'userid' => $USER->id,
                        'relateduserid' => $USER->id,
                        'context' => \context_system::instance(),
                        'other' => [
                            'sessionuid' => $session->sessionuid,
                            'message' => 'Successful tracking'
                        ]
                    ]);
                    $event->trigger();
                }
            } catch (\Exception $e) {
                if (get_config('local_intelliboard', 'bb_col_debug')) {
                    $event = local_intelliboard_bb_collaborate_session_tracking_finished::create([
                        'objectid' => $session->id,
                        'userid' => $USER->id,
                        'relateduserid' => $USER->id,
                        'context' => \context_system::instance(),
                        'other' => [
                            'sessionuid' => $session->sessionuid,
                            'message' => 'Tracking error',
                            'api_response' => $e->getMessage()
                        ]
                    ]);
                    $event->trigger();
                }

                continue;
            }
        }

        return true;
    }

}