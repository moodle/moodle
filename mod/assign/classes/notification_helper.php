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

namespace mod_assign;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Helper for sending assignment related notifications.
 *
 * @package    mod_assign
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_helper {

    /**
     * @var int Due soon time interval of 48 hours.
     */
    private const INTERVAL_DUE_SOON = (DAYSECS * 2);

    /**
     * @var int Overdue time interval of 2 hours.
     */
    private const INTERVAL_OVERDUE = (HOURSECS * 2);

    /**
     * @var string Due soon notification type.
     */
    public const TYPE_DUE_SOON = 'assign_due_soon';

    /**
     * @var string Overdue notification type.
     */
    public const TYPE_OVERDUE = 'assign_overdue';

    /**
     * Get all assignments that have an approaching due date (includes users and groups with due date overrides).
     *
     * @return \moodle_recordset Returns the matching assignment records.
     */
    public static function get_due_soon_assignments(): \moodle_recordset {
        global $DB;

        $timenow = self::get_time_now();
        $futuretime = self::get_future_time(self::INTERVAL_DUE_SOON);

        $sql = "SELECT DISTINCT a.id
                  FROM {assign} a
                  JOIN {course_modules} cm ON a.id = cm.instance
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
             LEFT JOIN {assign_overrides} ao ON a.id = ao.assignid
                 WHERE (a.duedate < :futuretime OR ao.duedate < :ao_futuretime)
                   AND (a.duedate > :timenow OR ao.duedate > :ao_timenow)";

        $params = [
            'timenow' => $timenow,
            'futuretime' => $futuretime,
            'ao_timenow' => $timenow,
            'ao_futuretime' => $futuretime,
            'modulename' => 'assign',
        ];

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Get all assignments that are overdue, but not exceeding the cut-off date (includes users and groups with due date overrides).
     *
     * We don't want to get every single overdue assignment ever.
     * We just want the ones within the specified window.
     *
     * @return \moodle_recordset Returns the matching assignment records.
     */
    public static function get_overdue_assignments(): \moodle_recordset {
        global $DB;

        $timenow = self::get_time_now();
        $timewindow = self::get_time_now() - self::INTERVAL_OVERDUE;

        // Get all assignments that:
        // - Are overdue.
        // - Do not exceed the window of time in the past.
        // - Are still within the cut-off (if it is set).
        $sql = "SELECT DISTINCT a.id
                  FROM {assign} a
                  JOIN {course_modules} cm ON a.id = cm.instance
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
             LEFT JOIN {assign_overrides} ao ON a.id = ao.assignid
                 WHERE (a.duedate < :dd_timenow OR ao.duedate < :dd_ao_timenow)
                   AND (a.duedate > :dd_timewindow OR ao.duedate > :dd_ao_timewindow)
                   AND ((a.cutoffdate > :co_timenow OR a.cutoffdate = 0) OR
                       (ao.cutoffdate > :co_ao_timenow OR ao.cutoffdate = 0))";

        $params = [
            'dd_timenow' => $timenow,
            'dd_ao_timenow' => $timenow,
            'dd_timewindow' => $timewindow,
            'dd_ao_timewindow' => $timewindow,
            'co_timenow' => $timenow,
            'co_ao_timenow' => $timenow,
            'modulename' => 'assign',
        ];

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Get all assignment users that we should send the notification to.
     *
     * @param int $assignmentid The assignment id.
     * @param string $type The notification type.
     * @return array The users after all filtering has been applied.
     */
    public static function get_users_within_assignment(int $assignmentid, string $type): array {
        // Get assignment data.
        $assignmentobj = self::get_assignment_data($assignmentid);

        // Get our assignment users.
        $users = $assignmentobj->list_participants(0, true);

        foreach ($users as $key => $user) {
            // Check if the user has submitted already.
            if ($assignmentobj->get_user_submission($user->id, false)) {
                unset($users[$key]);
                continue;
            }

            // Determine key dates with respect to any overrides.
            $duedate = $assignmentobj->override_exists($user->id)->duedate ?? $assignmentobj->get_instance()->duedate;
            $cutoffdate = $assignmentobj->override_exists($user->id)->cutoffdate ?? $assignmentobj->get_instance()->cutoffdate;

            // If the due date has no value, unset this user.
            if (empty($duedate)) {
                unset($users[$key]);
                continue;
            }

            // Perform some checks depending on the notification type.
            $match = [];
            switch ($type) {
                case self::TYPE_DUE_SOON:
                    $range = [
                        'lower' => self::get_time_now(),
                        'upper' => self::get_future_time(self::INTERVAL_DUE_SOON),
                    ];
                    if (!self::is_time_within_range($duedate, $range)) {
                        unset($users[$key]);
                        break;
                    }
                    $match = [
                        'assignmentid' => $assignmentid,
                        'duedate' => $duedate,
                    ];
                    break;

                case self::TYPE_OVERDUE:
                    if ($duedate > self::get_time_now()) {
                        unset($users[$key]);
                        break;
                    }
                    // Check if the cut-off date is set and passed already.
                    if (!empty($cutoffdate) && self::get_time_now() > $cutoffdate) {
                        unset($users[$key]);
                        break;
                    }
                    $match = [
                        'assignmentid' => $assignmentid,
                        'duedate' => $duedate,
                        'cutoffdate' => $cutoffdate,
                    ];
                    break;

                default:
                    break;
            }

            // Check if the user has already received this notification.
            if (self::has_user_been_sent_a_notification_already($user->id, json_encode($match), $type)) {
                unset($users[$key]);
            }
        }

        return $users;
    }

    /**
     * Send the due soon notification to the user.
     *
     * @param int $assignmentid The assignment id.
     * @param int $userid The user id.
     */
    public static function send_due_soon_notification_to_user(int $assignmentid, int $userid): void {
        // Get assignment data.
        $assignmentobj = self::get_assignment_data($assignmentid);

        // Check if the due date still within range.
        $assignmentobj->update_effective_access($userid);
        $duedate = $assignmentobj->get_instance($userid)->duedate;
        $range = [
            'lower' => self::get_time_now(),
            'upper' => self::get_future_time(self::INTERVAL_DUE_SOON),
        ];
        if (!self::is_time_within_range($duedate, $range)) {
            return;
        }

        // Check if the user has submitted already.
        if ($assignmentobj->get_user_submission($userid, false)) {
            return;
        }

        // Build the user's notification message.
        $user = $assignmentobj->get_participant($userid);
        $urlparams = [
            'id' => $assignmentobj->get_course_module()->id,
            'action' => 'view',
        ];
        $url = new \moodle_url('/mod/assign/view.php', $urlparams);

        $stringparams = [
            'firstname' => $user->firstname,
            'assignmentname' => $assignmentobj->get_instance()->name,
            'coursename' => $assignmentobj->get_course()->fullname,
            'duedate' => userdate($duedate),
            'url' => $url,
        ];

        $messagedata = [
            'user' => \core_user::get_user($user->id),
            'url' => $url->out(false),
            'subject' => get_string('assignmentduesoonsubject', 'mod_assign', $stringparams),
            'assignmentname' => $assignmentobj->get_instance()->name,
            'html' => get_string('assignmentduesoonhtml', 'mod_assign', $stringparams),
        ];

        $message = new \core\message\message();
        $message->component = 'mod_assign';
        $message->name = self::TYPE_DUE_SOON;
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $messagedata['user'];
        $message->subject = $messagedata['subject'];
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessage = html_to_text($messagedata['html']);
        $message->fullmessagehtml = $messagedata['html'];
        $message->smallmessage = $messagedata['subject'];
        $message->notification = 1;
        $message->contexturl = $messagedata['url'];
        $message->contexturlname = $messagedata['assignmentname'];
        // Use custom data to avoid future notifications being sent again.
        $message->customdata = [
            'assignmentid' => $assignmentid,
            'duedate' => $duedate,
        ];

        message_send($message);
    }

    /**
     * Send the overdue notification to the user.
     *
     * @param int $assignmentid The assignment id.
     * @param int $userid The user id.
     */
    public static function send_overdue_notification_to_user(int $assignmentid, int $userid): void {
        // Get assignment data.
        $assignmentobj = self::get_assignment_data($assignmentid);

        // Get the user and check they are a still a valid participant.
        $user = $assignmentobj->get_participant($userid);
        if (empty($user)) {
            return;
        }

        // Check if the due date still considered overdue.
        $assignmentobj->update_effective_access($userid);
        $duedate = $assignmentobj->get_instance($userid)->duedate;
        if ($duedate > self::get_time_now()) {
            return;
        }

        // Check if the cut-off date is set and passed already.
        $cutoffdate = $assignmentobj->get_instance($userid)->cutoffdate;
        if (!empty($cutoffdate) && self::get_time_now() > $cutoffdate) {
            return;
        }

        // Check if the user has submitted already.
        if ($assignmentobj->get_user_submission($userid, false)) {
            return;
        }

        // Build the user's notification message.
        $urlparams = [
            'id' => $assignmentobj->get_course_module()->id,
            'action' => 'view',
        ];
        $url = new \moodle_url('/mod/assign/view.php', $urlparams);

        // Prepare the cut-off date html string.
        $snippet = '';
        if (!empty($cutoffdate)) {
            $snippet = get_string('assignmentoverduehtmlcutoffsnippet', 'mod_assign', ['cutoffdate' => userdate($cutoffdate)]);
        }

        $stringparams = [
            'firstname' => $user->firstname,
            'assignmentname' => $assignmentobj->get_instance()->name,
            'coursename' => $assignmentobj->get_course()->fullname,
            'duedate' => userdate($duedate),
            'url' => $url,
            'cutoffsnippet' => $snippet,
        ];

        $messagedata = [
            'user' => \core_user::get_user($user->id),
            'url' => $url->out(false),
            'subject' => get_string('assignmentoverduesubject', 'mod_assign', $stringparams),
            'assignmentname' => $assignmentobj->get_instance()->name,
            'html' => get_string('assignmentoverduehtml', 'mod_assign', $stringparams),
        ];

        $message = new \core\message\message();
        $message->component = 'mod_assign';
        $message->name = self::TYPE_OVERDUE;
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $messagedata['user'];
        $message->subject = $messagedata['subject'];
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessage = html_to_text($messagedata['html']);
        $message->fullmessagehtml = $messagedata['html'];
        $message->smallmessage = $messagedata['subject'];
        $message->notification = 1;
        $message->contexturl = $messagedata['url'];
        $message->contexturlname = $messagedata['assignmentname'];
        // Use custom data to avoid future notifications being sent again.
        $message->customdata = [
            'assignmentid' => $assignmentid,
            'duedate' => $duedate,
            'cutoffdate' => $cutoffdate,
        ];

        message_send($message);
    }

    /**
     * Get the time now.
     *
     * @return int The time now as a timestamp.
     */
    protected static function get_time_now(): int {
        return \core\di::get(\core\clock::class)->time();
    }

    /**
     * Get a future time.
     *
     * @param int $interval Amount of seconds added to the now time.
     * @return int The time now value plus the interval.
     */
    protected static function get_future_time(int $interval): int {
        return self::get_time_now() + $interval;
    }

    /**
     * Check if a time is within the current time now and the future time values.
     *
     * @param int $time The timestamp to check.
     * @param array $range Lower and upper times to check.
     * @return boolean
     */
    protected static function is_time_within_range(int $time, array $range): bool {
        return ($time > $range['lower'] && $time < $range['upper']);
    }

    /**
     * Check if a user has been sent a notification already.
     *
     * @param int $userid The user id.
     * @param string $match The custom data string to match on.
     * @param string $type The notification/event type to match.
     * @return bool Returns true if already sent.
     */
    protected static function has_user_been_sent_a_notification_already(int $userid, string $match, string $type): bool {
        global $DB;

        $sql = $DB->sql_compare_text('customdata', 255) . " = " . $DB->sql_compare_text(':match', 255) . "
            AND useridto = :userid
            AND component = :component
            AND eventtype = :eventtype";

        return $DB->record_exists_select('notifications', $sql, [
            'userid' => $userid,
            'match' => $match,
            'component' => 'mod_assign',
            'eventtype' => $type,
        ]);
    }

    /**
     * Get the assignment object, including the course and course module.
     *
     * @param int $assignmentid The assignment id.
     * @return \assign Returns the assign object.
     */
    protected static function get_assignment_data(int $assignmentid): \assign {
        [$course, $assigncm] = get_course_and_cm_from_instance($assignmentid, 'assign');
        $cmcontext = \context_module::instance($assigncm->id);
        return new \assign($cmcontext, $assigncm, $course);
    }
}
