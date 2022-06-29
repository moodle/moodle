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
use restore_controller;
use restore_dbops;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Asyncronhous restore tests.
 *
 * @package    core_backup
 * @copyright  2018 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class async_restore_test extends \advanced_testcase {

    /**
     * Tests the asynchronous backup.
     */
    public function test_async_restore() {
        global $CFG, $USER, $DB;

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

        // Backup the course.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);
        $bc->finish_ui();
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Get the backup file.
        $coursecontext = \context_course::instance($course->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($coursecontext->id, 'backup', 'course', false, 'id ASC');
        $backupfile = reset($files);

        // Extract backup file.
        $backupdir = "restore_" . uniqid();
        $path = $CFG->tempdir . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $backupdir;

        $fp = get_file_packer('application/vnd.moodle.backup');
        $fp->extract_to_pathname($backupfile, $path);

        // Create restore controller.
        $newcourseid = restore_dbops::create_new_course(
                $course->fullname, $course->shortname . '_2', $course->category);
        $rc = new restore_controller($backupdir, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_ASYNC, $USER->id,
                backup::TARGET_NEW_COURSE);
        $this->assertTrue($rc->execute_precheck());
        $restoreid = $rc->get_restoreid();

        $prerestorerec = $DB->get_record('backup_controllers', array('backupid' => $restoreid));
        $prerestorerec->controller = '';

        $rc->destroy();

        // Create the adhoc task.
        $asynctask = new \core\task\asynchronous_restore_task();
        $asynctask->set_blocking(false);
        $asynctask->set_custom_data(array('backupid' => $restoreid));
        $asynctask->set_userid($USER->id);
        \core\task\manager::queue_adhoc_task($asynctask);

        // We are expecting trace output during this test.
        $this->expectOutputRegex("/$restoreid/");

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_restore_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        $postrestorerec = $DB->get_record('backup_controllers', array('backupid' => $restoreid));

        // Check backup was created successfully.
        $this->assertEquals(backup::STATUS_FINISHED_OK, $postrestorerec->status);
        $this->assertEquals(1.0, $postrestorerec->progress);
        $this->assertEquals($USER->id, $postrestorerec->userid);
    }

    /**
     * Tests the asynchronous restore will resolve in duplicate cases where the controller is already removed.
     */
    public function test_async_restore_missing_controller() {
        global $CFG, $USER, $DB;

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

        // Backup the course.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);
        $bc->finish_ui();
        $bc->execute_plan();
        $bc->destroy();

        // Get the backup file.
        $coursecontext = \context_course::instance($course->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($coursecontext->id, 'backup', 'course', false, 'id ASC');
        $backupfile = reset($files);

        // Extract backup file.
        $backupdir = "restore_" . uniqid();
        $path = $CFG->tempdir . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $backupdir;

        $fp = get_file_packer('application/vnd.moodle.backup');
        $fp->extract_to_pathname($backupfile, $path);

        // Create restore controller.
        $newcourseid = restore_dbops::create_new_course(
                $course->fullname, $course->shortname . '_2', $course->category);
        $rc = new restore_controller($backupdir, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_ASYNC, $USER->id,
                backup::TARGET_NEW_COURSE);
        $restoreid = $rc->get_restoreid();
        $controllerid = $DB->get_field('backup_controllers', 'id', ['backupid' => $restoreid]);

        // Now hack the record to remove the controller and set the status fields to complete.
        // This emulates a duplicate run for an already finished controller.
        $data = [
            'id' => $controllerid,
            'controller' => '',
            'progress' => 1.0,
            'status' => backup::STATUS_FINISHED_OK
        ];
        $DB->update_record('backup_controllers', $data);
        $rc->destroy();

        // Create the adhoc task.
        $asynctask = new \core\task\asynchronous_restore_task();
        $asynctask->set_blocking(false);
        $asynctask->set_custom_data(['backupid' => $restoreid]);
        \core\task\manager::queue_adhoc_task($asynctask);

        // We are expecting a specific message output during this test.
        $this->expectOutputRegex('/invalid controller/');

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_restore_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        // Check the task record is removed.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));

        // Now delete the record and confirm an entirely missing controller is handled.
        $DB->delete_records('backup_controllers');

        // Create the adhoc task.
        $asynctask = new \core\task\asynchronous_restore_task();
        $asynctask->set_blocking(false);
        $asynctask->set_custom_data(['backupid' => $restoreid]);
        \core\task\manager::queue_adhoc_task($asynctask);

        // We are expecting a specific message output during this test.
        $this->expectOutputRegex('/Unable to find restore controller/');

        // Execute adhoc task.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_restore_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        // Check the task record is removed.
        $this->assertEquals(0, $DB->count_records('task_adhoc'));
    }
}
