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
 * Unit tests for attempt_submitted observer
 *
 * @package   quiz_statistics
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \quiz_statistics\hook_callbacks::quiz_attempt_submitted_or_deleted
 */
class quiz_attempt_submitted_test extends \advanced_testcase {
    use \quiz_question_helper_test_trait;
    use statistics_test_trait;

    /**
     * Attempting a quiz should queue the recalculation task for that quiz in 1 hour's time.
     *
     * @return void
     */
    public function test_queue_task_on_submission(): void {
        [$user, $quiz] = $this->create_test_data();

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        $this->attempt_quiz($quiz, $user);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);
        $task = reset($tasks);
        $this->assert_task_is_queued_for_quiz($task, $quiz);
    }

    /**
     * Attempting a quiz multiple times should only queue one instance of the task.
     *
     * @return void
     */
    public function test_queue_single_task_for_multiple_submissions(): void {
        [$user1, $quiz] = $this->create_test_data();
        $user2 = $this->getDataGenerator()->create_user();

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        $this->attempt_quiz($quiz, $user1);
        $this->attempt_quiz($quiz, $user2);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);
        $task = reset($tasks);
        $this->assert_task_is_queued_for_quiz($task, $quiz);
    }

    /**
     * Attempting the quiz again after processing the task should queue a new task.
     *
     * @return void
     */
    public function test_queue_new_task_after_processing(): void {
        [$user1, $quiz, $course] = $this->create_test_data();
        $user2 = $this->getDataGenerator()->create_user();

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        $this->attempt_quiz($quiz, $user1);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);

        $this->expectOutputRegex("~Re-calculating statistics for quiz {$quiz->name} \({$quiz->id}\) " .
            "from course {$course->shortname} \({$course->id}\) with 1 attempts~");
        statistics_helper::run_pending_recalculation_tasks();

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        $this->attempt_quiz($quiz, $user2);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);

        $task = reset($tasks);
        $this->assert_task_is_queued_for_quiz($task, $quiz);
    }

    /**
     * Attempting different quizzes will queue a task for each.
     *
     * @return void
     */
    public function test_queue_separate_tasks_for_multiple_quizzes(): void {
        [$user1, $quiz1] = $this->create_test_data();
        [$user2, $quiz2] = $this->create_test_data();

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertEmpty($tasks);

        $this->attempt_quiz($quiz1, $user1);
        $this->attempt_quiz($quiz2, $user2);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(2, $tasks);
        $task1 = array_shift($tasks);
        $this->assert_task_is_queued_for_quiz($task1, $quiz1);
        $task2 = array_shift($tasks);
        $this->assert_task_is_queued_for_quiz($task2, $quiz2);
    }
}
