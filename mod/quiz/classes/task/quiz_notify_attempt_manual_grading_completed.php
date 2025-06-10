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

namespace mod_quiz\task;

defined('MOODLE_INTERNAL') || die();

use context_course;
use core_user;
use moodle_recordset;
use question_display_options;
use mod_quiz_display_options;
use quiz_attempt;

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Cron Quiz Notify Attempts Graded Task.
 *
 * @package    mod_quiz
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class quiz_notify_attempt_manual_grading_completed extends \core\task\scheduled_task {
    /**
     * @var int|null For using in unit testing only. Override the time we consider as now.
     */
    protected $forcedtime = null;

    /**
     * Get name of schedule task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('notifyattemptsgradedtask', 'mod_quiz');
    }

    /**
     * To let this class be unit tested, we wrap all accesses to the current time in this method.
     *
     * @return int The current time.
     */
    protected function get_time(): int {
        if (PHPUNIT_TEST && $this->forcedtime !== null) {
            return $this->forcedtime;
        }

        return time();
    }

    /**
     * For testing only, pretend the current time is different.
     *
     * @param int $time The time to set as the current time.
     */
    public function set_time_for_testing(int $time): void {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('set_time_for_testing should only be used in unit tests.');
        }
        $this->forcedtime = $time;
    }

    /**
     * Execute sending notification for manual graded attempts.
     */
    public function execute() {
        global $DB;

        mtrace('Looking for quiz attempts which may need a graded notification sent...');

        $attempts = $this->get_list_of_attempts();
        $course = null;
        $quiz = null;
        $cm = null;

        foreach ($attempts as $attempt) {
            mtrace('Checking attempt ' . $attempt->id . ' at quiz ' . $attempt->quiz . '.');

            if (!$quiz || $attempt->quiz != $quiz->id) {
                $quiz = $DB->get_record('quiz', ['id' => $attempt->quiz], '*', MUST_EXIST);
                $cm = get_coursemodule_from_instance('quiz', $attempt->quiz);
            }

            if (!$course || $course->id != $quiz->course) {
                $course = $DB->get_record('course', ['id' => $quiz->course], '*', MUST_EXIST);
                $coursecontext = context_course::instance($quiz->course);
            }

            $quiz = quiz_update_effective_access($quiz, $attempt->userid);
            $attemptobj = new quiz_attempt($attempt, $quiz, $cm, $course, false);
            $options = mod_quiz_display_options::make_from_quiz($quiz, quiz_attempt_state($quiz, $attempt));

            if ($options->manualcomment == question_display_options::HIDDEN) {
                // User cannot currently see the feedback, so don't message them.
                // However, this may change in future, so leave them on the list.
                continue;
            }

            if (!has_capability('mod/quiz:emailnotifyattemptgraded', $coursecontext, $attempt->userid, false)) {
                // User not eligible to get a notification. Mark them done while doing nothing.
                $DB->set_field('quiz_attempts', 'gradednotificationsenttime', $attempt->timefinish, ['id' => $attempt->id]);
                continue;
            }

            // OK, send notification.
            mtrace('Sending email to user ' . $attempt->userid . '...');
            $ok = quiz_send_notify_manual_graded_message($attemptobj, core_user::get_user($attempt->userid));
            if ($ok) {
                mtrace('Send email successfully!');
                $attempt->gradednotificationsenttime = $this->get_time();
                $DB->set_field('quiz_attempts', 'gradednotificationsenttime', $attempt->gradednotificationsenttime,
                        ['id' => $attempt->id]);
                $attemptobj->fire_attempt_manual_grading_completed_event();
            }
        }

        $attempts->close();
    }

    /**
     * Get a number of records as an array of quiz_attempts using a SQL statement.
     *
     * @return moodle_recordset Of quiz_attempts that need to be processed.
     */
    public function get_list_of_attempts(): moodle_recordset {
        global $DB;

        $delaytime = $this->get_time() - get_config('quiz', 'notifyattemptgradeddelay');

        $sql = "SELECT qa.*
                  FROM {quiz_attempts} qa
                  JOIN {quiz} quiz ON quiz.id = qa.quiz
                 WHERE qa.state = 'finished'
                       AND qa.gradednotificationsenttime IS NULL
                       AND qa.sumgrades IS NOT NULL
                       AND qa.timemodified < :delaytime
              ORDER BY quiz.course, qa.quiz";

        return $DB->get_recordset_sql($sql, ['delaytime' => $delaytime]);
    }
}
