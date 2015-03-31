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

        // Test hourly job executed on Sundays only.
        $testclass = new \core\task\scheduled_test_task();
        $testclass->set_minute('0');
        $testclass->set_day_of_week('7');

        $nexttime = $testclass->get_next_scheduled_time();

        $this->assertEquals(7, date('N', $nexttime));
        $this->assertEquals(0, date('i', $nexttime));

        // Test monthly job
        $testclass = new \core\task\scheduled_test_task();
        $testclass->set_minute('32');
        $testclass->set_hour('0');
        $testclass->set_day('1');

        $nexttime = $testclass->get_next_scheduled_time();

        $this->assertEquals(32, date('i', $nexttime));
        $this->assertEquals(0, date('G', $nexttime));
        $this->assertEquals(1, date('j', $nexttime));
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

    public function test_reset_scheduled_tasks_for_component() {
        global $DB;

        $this->resetAfterTest(true);
        // Remember the defaults.
        $defaulttasks = \core\task\manager::load_scheduled_tasks_for_component('moodle');
        $initcount = count($defaulttasks);
        // Customise a task.
        $firsttask = reset($defaulttasks);
        $firsttask->set_minute('1');
        $firsttask->set_hour('2');
        $firsttask->set_month('3');
        $firsttask->set_day_of_week('4');
        $firsttask->set_day('5');
        $firsttask->set_customised('1');
        \core\task\manager::configure_scheduled_task($firsttask);
        $firsttaskrecord = \core\task\manager::record_from_scheduled_task($firsttask);
        // We reset this field, because we do not want to compare it.
        $firsttaskrecord->nextruntime = '0';

        // Delete a task to simulate the fact that its new.
        $secondtask = next($defaulttasks);
        $DB->delete_records('task_scheduled', array('classname' => '\\' . trim(get_class($secondtask), '\\')));
        $this->assertFalse(\core\task\manager::get_scheduled_task(get_class($secondtask)));

        // Edit a task to simulate a change in its definition (as if it was not customised).
        $thirdtask = next($defaulttasks);
        $thirdtask->set_minute('1');
        $thirdtask->set_hour('2');
        $thirdtask->set_month('3');
        $thirdtask->set_day_of_week('4');
        $thirdtask->set_day('5');
        $thirdtaskbefore = \core\task\manager::get_scheduled_task(get_class($thirdtask));
        $thirdtaskbefore->set_next_run_time(null);      // Ignore this value when comparing.
        \core\task\manager::configure_scheduled_task($thirdtask);
        $thirdtask = \core\task\manager::get_scheduled_task(get_class($thirdtask));
        $thirdtask->set_next_run_time(null);            // Ignore this value when comparing.
        $this->assertNotEquals($thirdtaskbefore, $thirdtask);

        // Now call reset on all the tasks.
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');

        // Load the tasks again.
        $defaulttasks = \core\task\manager::load_scheduled_tasks_for_component('moodle');
        $finalcount = count($defaulttasks);
        // Compare the first task.
        $newfirsttask = reset($defaulttasks);
        $newfirsttaskrecord = \core\task\manager::record_from_scheduled_task($newfirsttask);
        // We reset this field, because we do not want to compare it.
        $newfirsttaskrecord->nextruntime = '0';

        // Assert a customised task was not altered by reset.
        $this->assertEquals($firsttaskrecord, $newfirsttaskrecord);

        // Assert that the second task was added back.
        $secondtaskafter = \core\task\manager::get_scheduled_task(get_class($secondtask));
        $secondtaskafter->set_next_run_time(null);   // Do not compare the nextruntime.
        $secondtask->set_next_run_time(null);
        $this->assertEquals($secondtask, $secondtaskafter);

        // Assert that the third task edits were overridden.
        $thirdtaskafter = \core\task\manager::get_scheduled_task(get_class($thirdtask));
        $thirdtaskafter->set_next_run_time(null);
        $this->assertEquals($thirdtaskbefore, $thirdtaskafter);

        // Assert we have the same number of tasks.
        $this->assertEquals($initcount, $finalcount);
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

    public function test_get_broken_scheduled_task() {
        global $DB;

        $this->resetAfterTest(true);
        // Delete all existing scheduled tasks.
        $DB->delete_records('task_scheduled');
        // Add a scheduled task.

        // A broken task that runs all the time.
        $record = new stdClass();
        $record->blocking = true;
        $record->minute = '*';
        $record->hour = '*';
        $record->dayofweek = '*';
        $record->day = '*';
        $record->month = '*';
        $record->component = 'test_scheduled_task';
        $record->classname = '\core\task\scheduled_test_task_broken';

        $DB->insert_record('task_scheduled', $record);

        $now = time();
        // Should not get any task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertDebuggingCalled();
        $this->assertNull($task);
    }

    /**
     * Tests the use of 'R' syntax in time fields of tasks to get
     * tasks be configured with a non-uniform time.
     */
    public function test_random_time_specification() {

        // Testing non-deterministic things in a unit test is not really
        // wise, so we just test the values have changed within allowed bounds.
        $testclass = new \core\task\scheduled_test_task();

        // The test task defaults to '*'.
        $this->assertInternalType('string', $testclass->get_minute());
        $this->assertInternalType('string', $testclass->get_hour());

        // Set a random value.
        $testclass->set_minute('R');
        $testclass->set_hour('R');
        $testclass->set_day_of_week('R');

        // Verify the minute has changed within allowed bounds.
        $minute = $testclass->get_minute();
        $this->assertInternalType('int', $minute);
        $this->assertGreaterThanOrEqual(0, $minute);
        $this->assertLessThanOrEqual(59, $minute);

        // Verify the hour has changed within allowed bounds.
        $hour = $testclass->get_hour();
        $this->assertInternalType('int', $hour);
        $this->assertGreaterThanOrEqual(0, $hour);
        $this->assertLessThanOrEqual(23, $hour);

        // Verify the dayofweek has changed within allowed bounds.
        $dayofweek = $testclass->get_day_of_week();
        $this->assertInternalType('int', $dayofweek);
        $this->assertGreaterThanOrEqual(0, $dayofweek);
        $this->assertLessThanOrEqual(6, $dayofweek);
    }

    /**
     * Test that the file_temp_cleanup_task removes directories and
     * files as expected.
     */
    public function test_file_temp_cleanup_task() {
        global $CFG;

        // Create directories.
        $dir = $CFG->tempdir . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'backup01' . DIRECTORY_SEPARATOR . 'courses';
        mkdir($dir, 0777, true);

        // Create files to be checked and then deleted.
        $file01 = $dir . DIRECTORY_SEPARATOR . 'sections.xml';
        file_put_contents($file01, 'test data 001');
        $file02 = $dir . DIRECTORY_SEPARATOR . 'modules.xml';
        file_put_contents($file02, 'test data 002');
        // Change the time modified for the first file, to a time that will be deleted by the task (greater than seven days).
        touch($file01, time() - (8 * 24 * 3600));

        $task = \core\task\manager::get_scheduled_task('\\core\\task\\file_temp_cleanup_task');
        $this->assertInstanceOf('\core\task\file_temp_cleanup_task', $task);
        $task->execute();

        // Scan the directory. Only modules.xml should be left.
        $filesarray = scandir($dir);
        $this->assertEquals('modules.xml', $filesarray[2]);
        $this->assertEquals(3, count($filesarray));

        // Change the time modified on modules.xml.
        touch($file02, time() - (8 * 24 * 3600));
        // Change the time modified on the courses directory.
        touch($CFG->tempdir . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'backup01' . DIRECTORY_SEPARATOR .
                'courses', time() - (8 * 24 * 3600));
        // Run the scheduled task to remove the file and directory.
        $task->execute();
        $filesarray = scandir($CFG->tempdir . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'backup01');
        // There should only be two items in the array, '.' and '..'.
        $this->assertEquals(2, count($filesarray));

        // Change the time modified on all of the files and directories.
        $dir = new \RecursiveDirectoryIterator($CFG->tempdir);
        // Show all child nodes prior to their parent.
        $iter = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);

        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            if ($iter->isDir() && !$iter->isDot()) {
                $node = $iter->getRealPath();
                touch($node, time() - (8 * 24 * 3600));
            }
        }

        // Run the scheduled task again to remove all of the files and directories.
        $task->execute();
        $filesarray = scandir($CFG->tempdir);
        // All of the files and directories should be deleted.
        // There should only be two items in the array, '.' and '..'.
        $this->assertEquals(2, count($filesarray));
    }
}
