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

namespace core;

use core\task\manager;

/**
 * Tests for core\cron.
 *
 * @package     core
 * @copyright   2023 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\cron
 */
final class cron_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        require_once(__DIR__ . '/fixtures/task_fixtures.php');
    }

    /**
     * Reset relevant caches between tests.
     */
    public function setUp(): void {
        parent::setUp();
        cron::reset_user_cache();
    }

    /**
     * Test the setup_user function.
     */
    public function test_setup_user(): void {
        // This function uses the $GLOBALS super global. Disable the VariableNameLowerCase sniff for this function.
        // phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
        global $PAGE, $USER, $SESSION, $SITE, $CFG;
        $this->resetAfterTest();

        $admin = get_admin();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        cron::setup_user();
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertSame($CFG->timezone, $USER->timezone);
        $this->assertSame('', $USER->lang);
        $this->assertSame('', $USER->theme);
        $SESSION->test1 = true;
        $adminsession = $SESSION;
        $adminuser = $USER;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user(null, $course);
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($course->id));
        $this->assertSame($adminsession, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertObjectNotHasProperty('test1', $SESSION);
        $this->assertEmpty((array)$SESSION);
        $usersession1 = $SESSION;
        $SESSION->test2 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertSame($usersession1, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user2);
        $this->assertSame($user2->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertEmpty((array)$SESSION);
        $usersession2 = $SESSION;
        $usersession2->test3 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user2, $course);
        $this->assertSame($user2->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($course->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertSame($usersession2, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertEmpty((array)$SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user();
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertSame($adminsession, $SESSION);
        $this->assertSame($adminuser, $USER);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::reset_user_cache();
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user();
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($adminuser, $USER);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        // phpcs:enable
    }

    /**
     * Test that run_inner_adhoc_task() routes to adhoc_task_delayed() when the task
     * calls set_soft_retry_delay() from within execute(), and that the task remains
     * in the DB (not deleted), with fail_delay reset to 0, attemptsavailable decremented,
     * and nextruntime advanced by the requested delay.
     *
     * @covers \core\cron::run_inner_adhoc_task
     * @covers \core\task\manager::adhoc_task_delayed
     * @dataProvider run_inner_adhoc_task_delayed_provider
     * @param int|null $softretrydelay Soft retry delay, or null for exponential backoff.
     * @param int $now Frozen clock value.
     * @param int $expectednextruntime Expected nextruntime stored in the DB after the delay.
     */
    public function test_run_inner_adhoc_task_routes_to_delayed_when_soft_retry_set(
        ?int $softretrydelay,
        int $now,
        int $expectednextruntime,
    ): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();
        cron::reset_user_cache();

        $CFG->task_logtostdout = true;

        // Freeze the clock.
        $clock = $this->mock_clock_with_frozen($now);

        // Queue a task that will call set_soft_retry_delay() during execute().
        $task = new \core\task\soft_retry_adhoc_test_task();
        if ($softretrydelay !== null) {
            $task->set_custom_data(['delay' => $softretrydelay]);
        }
        $taskid = manager::queue_adhoc_task($task);

        // Retrieve the task as the cron runner would.
        $task = manager::get_next_adhoc_task($clock->time());
        $this->assertNotNull($task, 'Task should be retrievable from the queue.');

        $initialattempts = $task->get_attempts_available();

        // Run it through the full cron runner path.
        ob_start();
        cron::run_inner_adhoc_task($task);
        $output = ob_get_clean();

        // The task must NOT have been deleted. It is delayed, not complete.
        $record = $DB->get_record('task_adhoc', ['id' => $taskid]);
        $this->assertNotFalse($record);

        // Nextruntime must be the expected future time.
        $this->assertEquals($expectednextruntime, (int) $record->nextruntime);

        // Fail_delay must be 0, a soft retry is not a failure.
        $this->assertEquals(0, (int) $record->faildelay);

        // Attemptsavailable must have been decremented by one.
        $this->assertEquals($initialattempts - 1, (int) $record->attemptsavailable);

        // The cron log should contain the "delayed" message, not the "complete" message.
        $this->assertStringContainsString('Adhoc task delayed:', $output);
        $this->assertStringNotContainsString('Adhoc task complete:', $output);

        // Metadata (timestarted, hostname, pid) must be cleared.
        $this->assertEmpty($record->timestarted);
        $this->assertEmpty($record->hostname);
        $this->assertEmpty($record->pid);
    }

    /**
     * Data provider for test_run_inner_adhoc_task_routes_to_delayed_when_soft_retry_set.
     *
     * @return array
     */
    public static function run_inner_adhoc_task_delayed_provider(): array {
        return [
            // Explicit delay: nextruntime = now(1000) + 120 = 1120.
            'explicit_delay' => [
                'softretrydelay'      => 120,
                'now'                 => 1000,
                'expectednextruntime' => 1120,
            ],
            // Exponential backoff: retrycount = max(0, 12 - attemptsavailable(12)) = 0,
            // delay = min(86400, 60 * pow(2, 0)) = 60, nextruntime = 1000 + 60 = 1060.
            'exponential_backoff' => [
                'softretrydelay'      => null,
                'now'                 => 1000,
                'expectednextruntime' => 1060,
            ],
        ];
    }

    /**
     * Test that run_inner_adhoc_task() routes to adhoc_task_complete() when the task
     * executes successfully WITHOUT calling set_soft_retry_delay(), and that the task
     * is deleted from the DB (not kept for retry).
     *
     * @covers \core\cron::run_inner_adhoc_task
     * @covers \core\task\manager::adhoc_task_complete
     */
    public function test_run_inner_adhoc_task_routes_to_complete_when_no_soft_retry(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        // See test_run_inner_adhoc_task_routes_to_delayed_when_soft_retry_set for explanation.
        $this->preventResetByRollback();
        cron::reset_user_cache();

        $CFG->task_logtostdout = true;

        $clock = $this->mock_clock_with_frozen(1000);

        // A plain task whose execute() does nothing, no set_soft_retry_delay() call.
        $task = new \core\task\adhoc_test_task();
        $taskid = manager::queue_adhoc_task($task);

        $task = manager::get_next_adhoc_task($clock->time());
        $this->assertNotNull($task);

        ob_start();
        cron::run_inner_adhoc_task($task);
        $output = ob_get_clean();

        // Task must have been deleted after successful completion.
        $this->assertFalse($DB->record_exists('task_adhoc', ['id' => $taskid]));

        // The cron log must contain "complete" and NOT "delayed".
        $this->assertStringContainsString('Adhoc task complete:', $output);
        $this->assertStringNotContainsString('Adhoc task delayed:', $output);
    }
}
