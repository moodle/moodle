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

namespace core_backup;

use backup;
use backup_controller;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Asyncronhous backup tests.
 *
 * @package    core_backup
 * @copyright  2018 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class async_backup_test extends \advanced_testcase {

    /**
     * Tests the asynchronous backup.
     */
    public function test_async_backup() {
        global $CFG, $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('format' => 'topics', 'numsections' => 3,
                        'enablecompletion' => COMPLETION_ENABLED),
                array('createsections' => true));
        $forum = $generator->create_module('forum', array(
                'course' => $course->id));
        $forum2 = $generator->create_module('forum', array(
                'course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));

        // Create a teacher user to call the backup.
        $teacher = $generator->create_user();
        $generator->enrol_user($teacher->id, $course->id, 'editingteacher');

        // We need a grade, easiest is to add an assignment.
        $assignrow = $generator->create_module('assign', array(
                'course' => $course->id));
        $assign = new \assign(\context_module::instance($assignrow->cmid), false, false);
        $item = $assign->get_grade_item();

        // Make a test grouping as well.
        $grouping = $generator->create_grouping(array('courseid' => $course->id,
                'name' => 'Grouping!'));

        $availability = '{"op":"|","show":false,"c":[' .
                '{"type":"completion","cm":' . $forum2->cmid .',"e":1},' .
                '{"type":"grade","id":' . $item->id . ',"min":4,"max":94},' .
                '{"type":"grouping","id":' . $grouping->id . '}' .
                ']}';
        $DB->set_field('course_modules', 'availability', $availability, array(
                'id' => $forum->cmid));
        $DB->set_field('course_sections', 'availability', $availability, array(
                'course' => $course->id, 'section' => 1));

        // Enable logging.
        $this->preventResetByRollback();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        get_log_manager(true);

        // Start backup process.
        $this->setUser($teacher->id);

        // Make the backup controller for an async backup.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, backup::MODE_ASYNC, $USER->id);
        $bc->finish_ui();
        $backupid = $bc->get_backupid();
        $bc->destroy();

        $prebackuprec = $DB->get_record('backup_controllers', array('backupid' => $backupid));

        // Check the initial backup controller was created correctly.
        $this->assertEquals(backup::STATUS_AWAITING, $prebackuprec->status);
        $this->assertEquals(2, $prebackuprec->execution);

        // Create the adhoc task.
        $asynctask = new \core\task\asynchronous_backup_task();
        $asynctask->set_blocking(false);
        $asynctask->set_custom_data(['backupid' => $backupid]);
        $asynctask->set_userid($USER->id);
        \core\task\manager::queue_adhoc_task($asynctask);

        // We are expecting trace output during this test.
        $this->expectOutputRegex("/$backupid/");

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_backup_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        $postbackuprec = $DB->get_record('backup_controllers', ['backupid' => $backupid]);

        // Check backup was created successfully.
        $this->assertEquals(backup::STATUS_FINISHED_OK, $postbackuprec->status);
        $this->assertEquals(1.0, $postbackuprec->progress);
        $this->assertEquals($teacher->id, $postbackuprec->userid);

        // Check that the backupid was logged correctly.
        $logrec = $DB->get_record('logstore_standard_log', ['userid' => $postbackuprec->userid,
                'target' => 'course_backup'], '*', MUST_EXIST);
        $otherdata = json_decode($logrec->other);
        $this->assertEquals($backupid, $otherdata->backupid);
    }

    /**
     * Tests the asynchronous backup will resolve in duplicate cases.
     */
    public function test_complete_async_backup() {
        global $CFG, $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('format' => 'topics', 'numsections' => 3,
                        'enablecompletion' => COMPLETION_ENABLED),
                array('createsections' => true));
        $forum = $generator->create_module('forum', array(
                'course' => $course->id));
        $forum2 = $generator->create_module('forum', array(
                'course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));

        // We need a grade, easiest is to add an assignment.
        $assignrow = $generator->create_module('assign', array(
                'course' => $course->id));
        $assign = new \assign(\context_module::instance($assignrow->cmid), false, false);
        $item = $assign->get_grade_item();

        // Make a test grouping as well.
        $grouping = $generator->create_grouping(array('courseid' => $course->id,
                'name' => 'Grouping!'));

        $availability = '{"op":"|","show":false,"c":[' .
                '{"type":"completion","cm":' . $forum2->cmid .',"e":1},' .
                '{"type":"grade","id":' . $item->id . ',"min":4,"max":94},' .
                '{"type":"grouping","id":' . $grouping->id . '}' .
                ']}';
        $DB->set_field('course_modules', 'availability', $availability, array(
                'id' => $forum->cmid));
        $DB->set_field('course_sections', 'availability', $availability, array(
                'course' => $course->id, 'section' => 1));

        // Make the backup controller for an async backup.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, backup::MODE_ASYNC, $USER->id);
        $bc->finish_ui();
        $backupid = $bc->get_backupid();
        $bc->destroy();

        // Now hack the record to remove the controller and set the status fields to complete.
        // This emulates a duplicate run for an already finished controller.
        $id = $DB->get_field('backup_controllers', 'id', ['backupid' => $backupid]);
        $data = [
            'id' => $id,
            'controller' => '',
            'progress' => 1.0,
            'status' => backup::STATUS_FINISHED_OK
        ];
        $DB->update_record('backup_controllers', $data);

        // Now queue an adhoc task and check it handles and completes gracefully.
        $asynctask = new \core\task\asynchronous_backup_task();
        $asynctask->set_blocking(false);
        $asynctask->set_custom_data(array('backupid' => $backupid));
        \core\task\manager::queue_adhoc_task($asynctask);

        // We are expecting a specific message output during this test.
        $this->expectOutputRegex('/invalid controller/');

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_backup_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        // Check the task record is removed.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));
    }
}
