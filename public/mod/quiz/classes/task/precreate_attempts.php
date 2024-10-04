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

use core\task\scheduled_task;
use mod_quiz\quiz_settings;
use question_engine;

/**
 * Pre-create attempts for quizzes that have passed their threshold.
 *
 * @package   mod_quiz
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class precreate_attempts extends scheduled_task {
    /**
     * Create new instance of the task.
     *
     * @param int $maxruntime The number of seconds to allow the task to start processing new quizzes.
     */
    public function __construct(
        /** @var int $maxruntime The number of seconds to allow the task to start processing new quizzes. */
        protected int $maxruntime = 600,
    ) {
    }

    #[\Override]
    public function get_name(): string {
        return get_string('precreatetask', 'mod_quiz');
    }

    /**
     * Pre-create quiz attempts for configured quizzes.
     *
     * Find all quizzes with timeopen where the current time is later
     * than timeopen-precreateperiod, the quiz has questions, but no attempts.
     *
     * If the precreateperiod setting is unlocked, also filter by quizzes with precreateattempts enabled.
     *
     * Find all the users enrolled on the course who can attempt the quiz and create an attempt
     * in the NOT_STARTED state.
     *
     * This will run for $this->maxruntime seconds, then stop to avoid hogging the cron process. Remaining quizzes will be
     * processed on subsequent runs.
     *
     * @return void
     */
    public function execute(): void {
        global $DB;
        $starttime = time();
        $precreateperiod = (int)get_config('quiz', 'precreateperiod');
        $precreatedefault  = (int)get_config('quiz', 'precreateattempts');
        if ($precreateperiod === 0) {
            mtrace('Pre-creation of quiz attempts is disabled. Nothing to do.');
            return;
        }
        $sql = "
            SELECT DISTINCT q.id, q.name, q.course, q.timeopen
              FROM {quiz} q
              JOIN {quiz_slots} qs ON q.id = qs.quizid
         LEFT JOIN {quiz_attempts} qa ON q.id = qa.quiz
             WHERE qa.id IS NULL
                   AND q.timeopen > :now
                   AND q.timeopen < :threshold
                   AND (
                       q.precreateattempts = :precreateattempts
                       OR (1 = :precreatedefault AND q.precreateattempts IS NULL)
                   )
          ORDER BY q.timeopen ASC";
        $params = [
            'now' => $starttime,
            'threshold' => $starttime + $precreateperiod,
            'precreateattempts' => 1,
            'precreatedefault' => $precreatedefault,
        ];

        $quizzes = $DB->get_records_sql($sql, $params);
        mtrace('Found ' . count($quizzes) . ' quizzes to create attempts for.');
        $quizcount = 0;
        foreach ($quizzes as $quiz) {
            $transaction = $DB->start_delegated_transaction();
            try {
                $quizstart = microtime(true);
                mtrace('Creating attempts for ' . $quiz->name);
                $attemptcount = self::precreate_attempts_for_quiz($quiz->id, $quiz->course);
                $quizend = microtime(true);
                $quizduration = round($quizend - $quizstart, 2);
                mtrace('Created ' . $attemptcount . ' attempts for ' . $quiz->name . ' in ' . $quizduration . ' seconds');
                $quizcount++;
                $transaction->allow_commit();
            } catch (\Throwable $e) {
                mtrace('Failed to create attempts for ' . $quiz->name);
                $transaction->rollback($e);
            }

            if (microtime(true) - $starttime > $this->maxruntime) {
                // Stop to let other tasks run, then do some more next run.
                mtrace('Time limit reached.');
                break;
            }
        }
        mtrace('Created attempts for ' . $quizcount . ' quizzes.');
    }

    /**
     * Pre-create attempts for a quiz.
     *
     * @param int $quizid
     * @param int $courseid
     * @return int The number of attempts created.
     */
    public static function precreate_attempts_for_quiz(int $quizid, int $courseid): int {
        global $DB;
        $coursecontext = \context_course::instance($courseid);
        $users = get_enrolled_users($coursecontext, 'mod/quiz:attempt');
        $attemptcount = 0;
        $timenow = time();
        foreach ($users as $user) {
            if ($DB->record_exists('quiz_attempts', ['userid' => $user->id, 'quiz' => $quizid])) {
                // Last-minute safety check in case the quiz opened and the user started an attempt since the task started.
                continue;
            }
            $quizobj = quiz_settings::create($quizid, $user->id);
            $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
            $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
            $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user->id);
            quiz_start_new_attempt(
                $quizobj,
                $quba,
                $attempt,
                1,
                $timenow,
            );
            quiz_attempt_save_not_started($quba, $attempt);
            $attemptcount++;
        }
        return $attemptcount;
    }
}
