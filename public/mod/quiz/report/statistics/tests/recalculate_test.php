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
use quiz_statistics\tests\statistics_test_trait;

/**
 * Unit tests for the recalculate adhoc task.
 *
 * @package   quiz_statistics
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \quiz_statistics\task\recalculate
 */
final class recalculate_test extends \advanced_testcase {
    use \quiz_question_helper_test_trait;
    use statistics_test_trait;

    /**
     * Create a quiz and return its ID. No attempts are needed for queue_future_run tests.
     *
     * @return int
     */
    protected function create_quiz_id(): int {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->create_test_quiz($course);
        return $quiz->id;
    }

    /**
     * Data provider for queue_future_run scheduling tests.
     *
     * Each case describes:
     *   - debounce config value (null = use default)
     *   - $now argument
     *   - whether a task is expected to be queued
     *   - expected delay in seconds relative to time() (null when no task expected)
     *
     * @return array[]
     */
    public static function queue_future_run_provider(): array {
        return [
            'default delay, not now' => [
                'debounce'      => null,
                'now'           => false,
                'expecttask'    => true,
                'expecteddelay' => HOURSECS,
            ],
            'custom delay of 5 minutes, not now' => [
                'debounce'      => 300,
                'now'           => false,
                'expecttask'    => true,
                'expecteddelay' => 300,
            ],
            'default delay, now = true' => [
                'debounce'      => null,
                'now'           => true,
                'expecttask'    => true,
                'expecteddelay' => 0,
            ],
            'zero delay, not now — no task queued' => [
                'debounce'      => 0,
                'now'           => false,
                'expecttask'    => false,
                'expecteddelay' => null,
            ],
            'zero delay, now = true — queued immediately' => [
                'debounce'      => 0,
                'now'           => true,
                'expecttask'    => true,
                'expecteddelay' => 0,
            ],
        ];
    }

    /**
     * Test that queue_future_run schedules the task at the correct time.
     *
     * @dataProvider queue_future_run_provider
     * @param int|null $debounce Config value to set, or null to leave as default.
     * @param bool $now Passed directly to queue_future_run().
     * @param bool $expecttask Whether a task should be queued at all.
     * @param int|null $expecteddelay Expected seconds from now for the run time.
     */
    public function test_queue_future_run(
        ?int $debounce,
        bool $now,
        bool $expecttask,
        ?int $expecteddelay,
    ): void {
        $this->resetAfterTest();
        $clock = $this->mock_clock_with_frozen();
        $quizid = $this->create_quiz_id();

        if ($debounce !== null) {
            set_config('recalculatedebounce', $debounce, 'quiz_statistics');
        }

        recalculate::queue_future_run($quizid, $now);

        $tasks = manager::get_adhoc_tasks(recalculate::class);

        if (!$expecttask) {
            $this->assertEmpty($tasks);
            return;
        }

        $this->assertCount(1, $tasks);
        $task = reset($tasks);
        $this->assertEquals($quizid, $task->get_custom_data()->quizid);
        $this->assertEquals($clock->time() + $expecteddelay, $task->get_next_run_time());
    }

    /**
     * Calling queue_future_run multiple times for the same quiz should only produce one queued task.
     */
    public function test_queue_future_run_multiple_calls_same_quiz(): void {
        $this->resetAfterTest();
        $this->mock_clock_with_frozen();
        $quizid = $this->create_quiz_id();

        recalculate::queue_future_run($quizid);
        recalculate::queue_future_run($quizid);
        recalculate::queue_future_run($quizid);

        $tasks = manager::get_adhoc_tasks(recalculate::class);
        $this->assertCount(1, $tasks);
        $this->assertEquals($quizid, reset($tasks)->get_custom_data()->quizid);
    }

    /**
     * Calling queue_future_run for different quizzes should queue a separate task for each.
     */
    public function test_queue_future_run_separate_tasks_for_different_quizzes(): void {
        $this->resetAfterTest();
        $this->mock_clock_with_frozen();
        $quizid1 = $this->create_quiz_id();
        $quizid2 = $this->create_quiz_id();

        recalculate::queue_future_run($quizid1);
        recalculate::queue_future_run($quizid2);

        $tasks = manager::get_adhoc_tasks(recalculate::class);

        $this->assertCount(2, $tasks);
        $quizids = array_map(fn($t) => $t->get_custom_data()->quizid, $tasks);
        $this->assertContains($quizid1, $quizids);
        $this->assertContains($quizid2, $quizids);
    }

    /**
     * Data provider for task_due_in tests.
     *
     * Each case describes:
     *   - debounce config value (null = use default)
     *   - $now argument passed to queue_future_run
     *   - whether a task is queued at all
     *   - expected minimum seconds until task runs (null when no task expected)
     *   - expected maximum seconds until task runs (null when no task expected)
     *
     * @return array[]
     */
    public static function task_due_in_provider(): array {
        return [
            'no task queued returns null' => [
                'debounce'    => null,
                'queuetask'   => false,
                'expectnull'  => true,
                'expectedsecs' => null,
            ],
            'task queued with default delay returns HOURSECS' => [
                'debounce'    => null,
                'queuetask'   => true,
                'expectnull'  => false,
                'expectedsecs' => HOURSECS,
            ],
            'task queued with custom delay of 5 minutes returns 300' => [
                'debounce'    => 300,
                'queuetask'   => true,
                'expectnull'  => false,
                'expectedsecs' => 300,
            ],
            'task queued with now = true returns 0' => [
                'debounce'    => null,
                'queuetask'   => true,
                'expectnull'  => false,
                'expectedsecs' => 0,
            ],
        ];
    }

    /**
     * Test that task_due_in returns the correct number of seconds until the task runs.
     *
     * @dataProvider task_due_in_provider
     * @param int|null $debounce Config value to set, or null to leave as default.
     * @param bool $queuetask Whether to queue a task before calling task_due_in.
     * @param bool $expectnull Whether null is the expected return value.
     * @param int|null $expectedsecs Expected return value in seconds (when a task is queued).
     */
    public function test_task_due_in(
        ?int $debounce,
        bool $queuetask,
        bool $expectnull,
        ?int $expectedsecs,
    ): void {
        $this->resetAfterTest();
        $clock = $this->mock_clock_with_frozen();
        $quizid = $this->create_quiz_id();

        if ($debounce !== null) {
            set_config('recalculatedebounce', $debounce, 'quiz_statistics');
        }

        if ($queuetask) {
            // Use $now = true only for the "immediate" case (expectedsecs === 0).
            $now = $expectedsecs === 0;
            recalculate::queue_future_run($quizid, $now);
        }

        $result = recalculate::task_due_in($quizid);

        if ($expectnull) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expectedsecs, $result);
        }
    }

    /**
     * task_due_in should return null for a different quiz even when another quiz has a pending task.
     */
    public function test_task_due_in_is_null_for_different_quiz(): void {
        $this->resetAfterTest();
        $this->mock_clock_with_frozen();
        $quizid1 = $this->create_quiz_id();
        $quizid2 = $this->create_quiz_id();

        recalculate::queue_future_run($quizid1);

        $this->assertNotNull(recalculate::task_due_in($quizid1));
        $this->assertNull(recalculate::task_due_in($quizid2));
    }
}
