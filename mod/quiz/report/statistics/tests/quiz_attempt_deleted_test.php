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
namespace quiz_statistics;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

use core\task\manager;
use quiz_statistics\task\recalculate;
use quiz_statistics\tests\statistics_helper;
use quiz_statistics\tests\statistics_test_trait;

/**
 * Unit tests for attempt_deleted observer
 *
 * @package   quiz_statistics
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \quiz_statistics\hook_callbacks::quiz_attempt_submitted_or_deleted
 */
class quiz_attempt_deleted_test extends \advanced_testcase {
    use \quiz_question_helper_test_trait;
    use statistics_test_trait;

    /**
     * Deleting an attempt should queue the recalculation task for that quiz in 1 hour's time.
     *
     * @return void
     */
    public function test_queue_task_on_deletion(): void {
        [$user, $quiz] = $this->create_test_data();
        $this->attempt_quiz($quiz, $user);
        [, , $attempt] = $this->attempt_quiz($quiz, $user, 2);
        statistics_helper::run_pending_recalculation_tasks(true);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        quiz_delete_attempt($attempt->get_attemptid(), $quiz);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);
        $task = reset($tasks);
        $this->assert_task_is_queued_for_quiz($task, $quiz);
    }

    /**
     * Deleting multiple attempts of the same quiz should only queue one instance of the task.
     *
     * @return void
     */
    public function test_queue_single_task_for_multiple_deletions(): void {
        [$user1, $quiz] = $this->create_test_data();
        $user2 = $this->getDataGenerator()->create_user();
        $this->attempt_quiz($quiz, $user1);
        [, , $attempt1] = $this->attempt_quiz($quiz, $user1, 2);
        $this->attempt_quiz($quiz, $user2);
        [, , $attempt2] = $this->attempt_quiz($quiz, $user2, 2);
        statistics_helper::run_pending_recalculation_tasks(true);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        quiz_delete_attempt($attempt1->get_attemptid(), $quiz);
        quiz_delete_attempt($attempt2->get_attemptid(), $quiz);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);
        $task = reset($tasks);
        $this->assert_task_is_queued_for_quiz($task, $quiz);
    }

    /**
     * Deleting another attempt after processing the task should queue a new task.
     *
     * @return void
     */
    public function test_queue_new_task_after_processing(): void {
        [$user1, $quiz, $course] = $this->create_test_data();
        $user2 = $this->getDataGenerator()->create_user();
        $this->attempt_quiz($quiz, $user1);
        [, , $attempt1] = $this->attempt_quiz($quiz, $user1, 2);
        $this->attempt_quiz($quiz, $user2);
        [, , $attempt2] = $this->attempt_quiz($quiz, $user2, 2);
        statistics_helper::run_pending_recalculation_tasks(true);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        quiz_delete_attempt($attempt1->get_attemptid(), $quiz);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);

        $this->expectOutputRegex("~Re-calculating statistics for quiz {$quiz->name} \({$quiz->id}\) " .
            "from course {$course->shortname} \({$course->id}\) with 3 attempts~");
        statistics_helper::run_pending_recalculation_tasks();

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        quiz_delete_attempt($attempt2->get_attemptid(), $quiz);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);

        $task = reset($tasks);
        $this->assert_task_is_queued_for_quiz($task, $quiz);
    }

    /**
     * Deleting attempts from different quizzes will queue a task for each.
     *
     * @return void
     */
    public function test_queue_separate_tasks_for_multiple_quizzes(): void {
        [$user1, $quiz1] = $this->create_test_data();
        [$user2, $quiz2] = $this->create_test_data();
        $this->attempt_quiz($quiz1, $user1);
        [, , $attempt1] = $this->attempt_quiz($quiz1, $user1, 2);
        $this->attempt_quiz($quiz2, $user2);
        [, , $attempt2] = $this->attempt_quiz($quiz2, $user2, 2);
        statistics_helper::run_pending_recalculation_tasks(true);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        quiz_delete_attempt($attempt1->get_attemptid(), $quiz1);
        quiz_delete_attempt($attempt2->get_attemptid(), $quiz2);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(2, $tasks);
        $task1 = array_shift($tasks);
        $this->assert_task_is_queued_for_quiz($task1, $quiz1);
        $task2 = array_shift($tasks);
        $this->assert_task_is_queued_for_quiz($task2, $quiz2);
    }
}
