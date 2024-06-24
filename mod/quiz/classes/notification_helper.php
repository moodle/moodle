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

namespace mod_quiz;

use stdClass;

/**
 * Helper for sending quiz related notifications.
 *
 * @package    mod_quiz
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_helper {
    /**
     * @var int Default date range of 48 hours.
     */
    private const DEFAULT_DATE_RANGE = (DAYSECS * 2);

    /**
     * Get all quizzes that have an approaching open date (includes users and groups with open date overrides).
     *
     * @return \moodle_recordset Returns the matching quiz records.
     */
    public static function get_quizzes_within_date_range(): \moodle_recordset {
        global $DB;

        $timenow = self::get_time_now();
        $futuretime = self::get_future_time();

        $sql = "SELECT DISTINCT q.id
                  FROM {quiz} q
                  JOIN {course_modules} cm ON q.id = cm.instance
                  JOIN {modules} m ON cm.module = m.id AND m.name = :modulename
             LEFT JOIN {quiz_overrides} qo ON q.id = qo.quiz
                 WHERE (q.timeopen < :futuretime OR qo.timeopen < :qo_futuretime)
                   AND (q.timeopen > :timenow OR qo.timeopen > :qo_timenow)";

        $params = [
            'timenow' => $timenow,
            'futuretime' => $futuretime,
            'qo_timenow' => $timenow,
            'qo_futuretime' => $futuretime,
            'modulename' => 'quiz',
        ];

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Get all users that have an approaching open date within a quiz.
     *
     * @param int $quizid The quiz id.
     * @return array The users after all filtering has been applied.
     */
    public static function get_users_within_quiz(int $quizid): array {
        // Get quiz data.
        $quizobj = quiz_settings::create($quizid);
        $quiz = $quizobj->get_quiz();

        // Get our users.
        $users = get_enrolled_users(
            context: \context_module::instance($quizobj->get_cm()->id),
            withcapability: 'mod/quiz:attempt',
            userfields: 'u.id, u.firstname',
        );

        // Check for any override dates.
        $overrides = $quizobj->get_override_manager()->get_all_overrides();

        foreach ($users as $key => $user) {
            // Time open and time close dates can be user specific with an override.
            // We begin by assuming it is the same as recorded in the quiz.
            $user->timeopen = $quiz->timeopen;
            $user->timeclose = $quiz->timeclose;

            // Set the override type to 'none' to begin with.
            $user->overridetype = 'none';

            // Update this user with any applicable override dates.
            if (!empty($overrides)) {
                self::update_user_with_date_overrides($overrides, $user);
            }

            // If the 'timeopen' date has no value, even after overriding, unset this user.
            if (empty($quiz->timeopen) && empty($user->timeopen)) {
                unset($users[$key]);
                continue;
            }

            // Check the date is within our range.
            // We have to check here because we don't know if this quiz was selected because it only had users with overrides.
            if (!self::is_time_within_range($user->timeopen)) {
                unset($users[$key]);
                continue;
            }

            // Check if the user has already received this notification.
            $match = [
                'quizid' => strval($quizid),
                'timeopen' => $user->timeopen,
                'overridetype' => $user->overridetype,
            ];

            if (self::has_user_been_sent_a_notification_already($user->id, json_encode($match))) {
                unset($users[$key]);
            }
        }

        return $users;
    }

    /**
     * Send the notification to the user.
     *
     * @param stdClass $user The user's custom data.
     */
    public static function send_notification_to_user(stdClass $user): void {
        // Check if the user has submitted already.
        if (self::has_user_attempted($user)) {
            return;
        }

        // Get quiz data.
        $quizobj = quiz_settings::create($user->quizid);
        $quiz = $quizobj->get_quiz();
        $url = $quizobj->view_url();

        $stringparams = [
            'firstname' => $user->firstname,
            'quizname' => $quiz->name,
            'coursename' => $quizobj->get_course()->fullname,
            'timeopen' => userdate($user->timeopen),
            'timeclose' => !empty($user->timeclose) ? userdate($user->timeclose) : get_string('statusna'),
            'url' => $url,
        ];

        $messagedata = [
            'user' => \core_user::get_user($user->id),
            'url' => $url->out(false),
            'subject' => get_string('quizopendatesoonsubject', 'mod_quiz', $stringparams),
            'quizname' => $quiz->name,
            'html' => get_string('quizopendatesoonhtml', 'mod_quiz', $stringparams),
        ];

        // Prepare message object.
        $message = new \core\message\message();
        $message->component = 'mod_quiz';
        $message->name = 'quiz_open_soon';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $messagedata['user'];
        $message->subject = $messagedata['subject'];
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessage = html_to_text($messagedata['html']);
        $message->fullmessagehtml = $messagedata['html'];
        $message->smallmessage = $messagedata['subject'];
        $message->notification = 1;
        $message->contexturl = $messagedata['url'];
        $message->contexturlname = $messagedata['quizname'];
        // Use custom data to avoid future notifications being sent again.
        $message->customdata = [
            'quizid' => $user->quizid,
            'timeopen' => $user->timeopen,
            'overridetype' => $user->overridetype,
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
     * Get a future time that serves as the cut-off for this notification.
     *
     * @param int|null $range Amount of seconds added to the now time (optional).
     * @return int The time now value plus the range.
     */
    protected static function get_future_time(?int $range = null): int {
        $range = $range ?? self::DEFAULT_DATE_RANGE;
        return self::get_time_now() + $range;
    }

    /**
     * Check if a time is within the current time now and the future time values.
     *
     * @param int $time The timestamp to check.
     * @return boolean
     */
    protected static function is_time_within_range(int $time): bool {
        return ($time > self::get_time_now() && $time < self::get_future_time());
    }

    /**
     * Update user's recorded date based on the overrides.
     *
     * @param array $overrides The overrides to check.
     * @param stdClass $user The user records we will be updating.
     */
    protected static function update_user_with_date_overrides(array $overrides, stdClass $user): void {

        foreach ($overrides as $override) {
            // User override.
            if ($override->userid === $user->id) {
                $user->timeopen = !empty($override->timeopen) ? $override->timeopen : $user->timeopen;
                $user->timeclose = !empty($override->timeclose) ? $override->timeclose : $user->timeclose;
                $user->overridetype = 'user';
                // User override has precedence over group. Return here.
                return;
            }
            // Group override.
            if (!empty($override->groupid) && groups_is_member($override->groupid, $user->id)) {
                // If user is a member of multiple groups, and we have set this already, use the earliest date.
                if ($user->overridetype === 'group' && $user->timeopen < $override->timeopen) {
                    continue;
                }
                $user->timeopen = !empty($override->timeopen) ? $override->timeopen : $user->timeopen;
                $user->timeclose = !empty($override->timeclose) ? $override->timeclose : $user->timeclose;
                $user->overridetype = 'group';
            }
        }
    }

    /**
     * Check if a user has attempted this quiz already.
     *
     * @param stdClass $user The user record we will be checking.
     * @return bool Return true if attempt found.
     */
    protected static function has_user_attempted(stdClass $user): bool {
        global $DB;

        return $DB->record_exists('quiz_attempts', [
            'quiz' => $user->quizid,
            'userid' => $user->id,
        ]);
    }

    /**
     * Check if a user has been sent a notification already.
     *
     * @param int $userid The user id.
     * @param string $match The custom data string to match on.
     * @return bool Returns true if already sent.
     */
    protected static function has_user_been_sent_a_notification_already(int $userid, string $match): bool {
        global $DB;

        $sql = "SELECT COUNT(n.id)
                  FROM {notifications} n
                 WHERE " . $DB->sql_compare_text('n.customdata', 255) . " = " . $DB->sql_compare_text(':match', 255) . "
                   AND n.useridto = :userid";

        $result = $DB->count_records_sql($sql, [
            'userid' => $userid,
            'match' => $match,
        ]);

        return ($result > 0);
    }
}
