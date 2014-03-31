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
 * This file contains the unittests for scheduled tasks.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/fixtures/task_fixtures.php');

/**
 * Test class for scheduled task.
 *
 * @package core
 * @category task
 * @copyright 2013 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_scheduled_task_testcase extends advanced_testcase {

    /**
     * Test the cron scheduling method
     */
    public function test_eval_cron_field() {
        $testclass = new \core\task\scheduled_test_task();

        $this->assertEquals(20, count($testclass->eval_cron_field('*/3', 0, 59)));
        $this->assertEquals(31, count($testclass->eval_cron_field('1,*/2', 0, 59)));
        $this->assertEquals(15, count($testclass->eval_cron_field('1-10,5-15', 0, 59)));
        $this->assertEquals(13, count($testclass->eval_cron_field('1-10,5-15/2', 0, 59)));
        $this->assertEquals(3, count($testclass->eval_cron_field('1,2,3,1,2,3', 0, 59)));
        $this->assertEquals(1, count($testclass->eval_cron_field('-1,10,80', 0, 59)));
    }

    public function test_get_next_scheduled_time() {
        // Test job run at 1 am.
        $testclass = new \core\task\scheduled_test_task();

        // All fields default to '*'.
        $testclass->set_hour('1');
        $testclass->set_minute('0');
        // Next valid time should be 1am of the next day.
        $nexttime = $testclass->get_next_scheduled_time();

        $oneam = mktime(1, 0, 0);
        // Make it 1 am tomorrow if the time is after 1am.
        if ($oneam < time()) {
            $oneam += 86400;
        }

        $this->assertEquals($oneam, $nexttime, 'Next scheduled time is 1am.');

        // Disabled flag does not affect next time.
        $testclass->set_disabled(true);
        $nexttime = $testclass->get_next_scheduled_time();
        $this->assertEquals($oneam, $nexttime, 'Next scheduled time is 1am.');

        // Now test for job run every 10 minutes.
        $testclass = new \core\task\scheduled_test_task();

        // All fields default to '*'.
        $testclass->set_minute('*/10');
        // Next valid time should be next 10 minute boundary.
        $nexttime = $testclass->get_next_scheduled_time();

        $minutes = ((intval(date('i') / 10))+1) * 10;
        $nexttenminutes = mktime(date('H'), $minutes, 0);

        $this->assertEquals($nexttenminutes, $nexttime, 'Next scheduled time is in 10 minutes.');

        // Disabled flag does not affect next time.
        $testclass->set_disabled(true);
        $nexttime = $testclass->get_next_scheduled_time();
        $this->assertEquals($nexttenminutes, $nexttime, 'Next scheduled time is in 10 minutes.');
    }

    public function test_timezones() {
        global $CFG, $USER;

        // The timezones used in this test are chosen because they do not use DST - that would break the test.

        $currenttimezonephp = date_default_timezone_get();
        $currenttimezonecfg = null;
        if (!empty($CFG->timezone)) {
            $currenttimezonecfg = $CFG->timezone;
        }
        $userstimezone = null;
        if (!empty($USER->timezone)) {
            $userstimezone = $USER->timezone;
        }

        // We are testing a difference between $CFG->timezone and the php.ini timezone.
        // GMT+8.
        date_default_timezone_set('Australia/Perth');
        // GMT-04:30.
        $CFG->timezone = 'America/Caracas';

        $testclass = new \core\task\scheduled_test_task();

        // Scheduled tasks should always use servertime - so this is 03:30 GMT.
        $testclass->set_hour('1');
        $testclass->set_minute('0');

        // Next valid time should be 1am of the next day.
        $nexttime = $testclass->get_next_scheduled_time();

        // GMT+05:45.
        $USER->timezone = 'Asia/Kathmandu';
        $userdate = userdate($nexttime);

        // Should be displayed in user timezone.
        // I used http://www.timeanddate.com/worldclock/fixedtime.html?msg=Moodle+Test&iso=20140314T01&p1=58
        // to verify this time.
        $this->assertContains('11:15 AM', core_text::strtoupper($userdate));

        $CFG->timezone = $currenttimezonecfg;
        date_default_timezone_set($currenttimezonephp);
    }

    public function test_get_next_scheduled_task() {
        global $DB;

        $this->resetAfterTest(true);
        // Delete all existing scheduled tasks.
        $DB->delete_records('task_scheduled');
        // Add a scheduled task.

        // A task that runs once per hour.
        $record = new stdClass();
        $record->blocking = true;
        $record->minute = '0';
        $record->hour = '0';
        $record->dayofweek = '*';
        $record->day = '*';
        $record->month = '*';
        $record->component = 'test_scheduled_task';
        $record->classname = '\core\task\scheduled_test_task';

        $DB->insert_record('task_scheduled', $record);
        // And another one to test failures.
        $record->classname = '\core\task\scheduled_test2_task';
        $DB->insert_record('task_scheduled', $record);
        // And disabled test.
        $record->classname = '\core\task\scheduled_test3_task';
        $record->disabled = 1;
        $DB->insert_record('task_scheduled', $record);

        $now = time();

        // Should get handed the first task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test_task', $task);
        $task->execute();

        \core\task\manager::scheduled_task_complete($task);
        // Should get handed the second task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();

        \core\task\manager::scheduled_task_failed($task);
        // Should not get any task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertNull($task);

        // Should get the second task (retry after delay).
        $task = \core\task\manager::get_next_scheduled_task($now + 120);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();

        \core\task\manager::scheduled_task_complete($task);

        // Should not get any task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertNull($task);
    }
}
