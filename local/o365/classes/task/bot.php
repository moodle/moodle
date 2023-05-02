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
 * Scheduled task to trigger sending messages from Moodle to user via the bot.
 *
 * @package local_o365
 * @author Tomasz Muras <tomek.muras@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to trigger sending messages from Moodle to user via the bot.
 */
class bot extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_bot', 'local_o365');
    }

    /**
     * Execute the task.
     *
     * @return bool|void
     */
    public function execute() {
        global $DB;

        mtrace('Starting bot task.');

        if (\local_o365\utils::is_connected() !== true) {
            mtrace('Microsoft 365 not configured');
            return false;
        }

        $botfeatureenabled = get_config('local_o365', 'bot_feature_enabled');

        if (empty($botfeatureenabled)) {
            mtrace('Microsoft 365 bot feature is disabled');
            return false;
        }

        // Get all courses to be included.
        $sql = 'SELECT crs.id, crs.id AS id2
        			  FROM {course} crs
        			  JOIN {local_o365_objects} obj ON obj.type = ? AND obj.subtype = ? AND obj.moodleid = crs.id
        			  JOIN {assign} a ON a.course = crs.id
        			  WHERE crs.id != ?
        			  GROUP BY crs.id';

        $params = ['group', 'course', SITEID];
        $courses = $DB->get_records_sql_menu($sql, $params);
        mtrace(count($courses) . " synchronized courses with assignments found.");
        $this->courses = $courses;

        $botframework = new \local_o365\rest\botframework();
        if (!$botframework->has_token()) {
            // Cannot get token, exit.
            debugging('SKIPPED: handle_notification_sent - cannot get token from bot framework', DEBUG_DEVELOPER);
            return true;
        }
        $this->botframework = $botframework;

        $notificationendpoint = get_config('local_o365', 'bot_webhook_endpoint');
        if (empty($notificationendpoint)) {
            debugging('SKIPPED: handle_notification_sent - incomplete settings, bot_webhook_endpoint empty', DEBUG_DEVELOPER);
            return true;
        }

        $this->notificationendpoint = $notificationendpoint;
        // $this->assignment_past_due_date(); - will be used for custom proactive notifications.

        mtrace('Finishing bot task.');
    }

    /**
     * Notify teachers just after assignment due date.
     * TODO: Cards data needs to be reviewed
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function assignment_past_due_date() {
        global $DB, $CFG, $OUTPUT;

        // Get all assignments up to 1 day after due date that were not processed yet.
        $yesterday = time() - DAYSECS;
        $today = time();
        [$sql, $params] = $DB->get_in_or_equal($this->courses);

        $sql = "SELECT a.*
                      FROM {assign} a
                      LEFT JOIN {local_o365_notif} n ON a.id = n.assignid
                      WHERE n.id IS NULL AND a.duedate > ? AND a.duedate < ?
                            AND a.course $sql";

        $params = array_merge([$yesterday, $today], $params);
        $assignments = $DB->get_records_sql($sql, $params);
        mtrace(count($assignments). ' not processed assignment(s) after due date found.');

        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        foreach ($assignments as $assignment) {
            [$course, $cm] = get_course_and_cm_from_instance($assignment->id, 'assign');
            $context = \context_module::instance($cm->id);

            $o365course = $DB->get_record('local_o365_objects',
                    ['type' => 'group', 'subtype' => 'course', 'moodleid' => $course->id]);
            if (!$o365course) {
                // Course record doesn't have an object ID can't send for this assignment.
                debugging('SKIPPED: handle_notification_sent - course record doesn\'t have an object ID', DEBUG_DEVELOPER);
                continue;
            }

            $assign = new \assign($context, $cm, $course);

            // Get all participants due.
            $participants = $assign->list_participants(null, true);
            $tonotify = array();
            foreach ($participants as $participant) {
                $submission = $assign->get_user_submission($participant->id, false);
                if ($submission && $submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                    continue;
                }
                $tonotify[$participant->id] = $participant->id;
            }

            mtrace(count($tonotify) .' user(s) for notification for assignment ID:' . $assignment->id);

            foreach ($tonotify as $iduser) {
                $o365user = $DB->get_record('local_o365_objects', ['type' => 'user', 'moodleid' => $iduser]);
                if (!$o365user) {
                    debugging("SKIPPED: handle_notification_sent - recipient user ID:$iduser doesn\'t have an object ID",
                        DEBUG_DEVELOPER);
                    continue;
                }

                $cardsdata[] = [
                        'title' => "Your assignment is due.",
                        'subtitle' => '',
                        'url' => 'url',
                        'icon' => $OUTPUT->image_url('icon', 'assign')->out()
                ];

                $this->botframework->send_notification($o365course->objectid, $o365user->objectid,
                        "Your assignment is due", $cardsdata, $this->notificationendpoint);

            }

            // Remember that the notification for this assignment has been processed.
            $record = new \stdClass();
            $record->assignid = $assignment->id;
            $record->timenotified = time();
            $DB->insert_record('local_o365_notif', $record);
        }
    }
}
