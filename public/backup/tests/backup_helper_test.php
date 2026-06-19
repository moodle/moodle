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

use backup;
use restore_controller;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Tests for backup_helper class.
 *
 * @package    core
 * @copyright  2025 ISB Bayern
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \backup_helper
 */
final class backup_helper_test extends \advanced_testcase {
    /**
     * Data provider for test_is_async_pending_for_course.
     *
     * @return array Test scenarios
     */
    public static function is_async_pending_for_course_provider(): array {
        return [
            'Course backup in progress' => [
                'type' => backup::TYPE_1COURSE,
                'operation' => 'backup',
                'itemtype' => 'course',
                'status' => backup::STATUS_EXECUTING,
                'expected' => true,
            ],
            'Course restore in progress' => [
                'type' => backup::TYPE_1COURSE,
                'operation' => 'restore',
                'itemtype' => 'course',
                'status' => backup::STATUS_EXECUTING,
                'expected' => true,
            ],
            'Section backup in progress' => [
                'type' => backup::TYPE_1SECTION,
                'operation' => 'backup',
                'itemtype' => 'section',
                'status' => backup::STATUS_EXECUTING,
                'expected' => true,
            ],
            'Section restore in progress' => [
                'type' => backup::TYPE_1SECTION,
                'operation' => 'restore',
                'itemtype' => 'section',
                'status' => backup::STATUS_EXECUTING,
                'expected' => true,
            ],
            'Activity backup in progress' => [
                'type' => backup::TYPE_1ACTIVITY,
                'operation' => 'backup',
                'itemtype' => 'activity',
                'status' => backup::STATUS_EXECUTING,
                'expected' => true,
            ],
            'Activity restore in progress' => [
                'type' => backup::TYPE_1ACTIVITY,
                'operation' => 'restore',
                'itemtype' => 'activity',
                'status' => backup::STATUS_EXECUTING,
                'expected' => true,
            ],
            'Course backup awaiting execution' => [
                'type' => backup::TYPE_1COURSE,
                'operation' => 'backup',
                'itemtype' => 'course',
                'status' => backup::STATUS_AWAITING,
                'expected' => true,
            ],
            'Course restore awaiting execution' => [
                'type' => backup::TYPE_1COURSE,
                'operation' => 'restore',
                'itemtype' => 'course',
                'status' => backup::STATUS_AWAITING,
                'expected' => true,
            ],
            'Activity backup configured' => [
                'type' => backup::TYPE_1ACTIVITY,
                'operation' => 'backup',
                'itemtype' => 'activity',
                'status' => backup::STATUS_CONFIGURED,
                'expected' => false,
            ],
            'Course backup finished with error' => [
                'type' => backup::TYPE_1COURSE,
                'operation' => 'backup',
                'itemtype' => 'course',
                'status' => backup::STATUS_FINISHED_ERR,
                'expected' => false,
            ],
            'Course restore finished successfully' => [
                'type' => backup::TYPE_1COURSE,
                'operation' => 'restore',
                'itemtype' => 'course',
                'status' => backup::STATUS_FINISHED_OK,
                'expected' => false,
            ],
        ];
    }

    /**
     * Test is_async_pending_for_course with various scenarios.
     *
     * @dataProvider is_async_pending_for_course_provider
     * @param string $type The type of backup/restore operation
     * @param string $operation The operation ('backup' or 'restore')
     * @param string $itemtype The item type ('course', 'section' or 'activity')
     * @param int $status The backup controller status
     * @param bool $expected Expected result
     */
    public function test_is_async_pending_for_course(
        string $type,
        string $operation,
        string $itemtype,
        int $status,
        bool $expected
    ): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Determine the itemid based on type.
        $itemid = $course->id;
        if ($itemtype === 'section') {
            // Get the first section of the course.
            $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 0], '*', MUST_EXIST);
            $itemid = $section->id;
        } else if ($itemtype === 'activity') {
            // Create an activity in the course.
            $forum = $generator->create_module('forum', ['course' => $course->id]);
            $itemid = $forum->cmid;
        }

        // Create a backup controller record manually.
        $backupid = \restore_controller::get_tempdir_name($course->id, $USER->id);
        $controller = new \stdClass();
        $controller->backupid = $backupid;
        $controller->operation = $operation;
        $controller->type = $type;
        $controller->itemid = $itemid;
        $controller->format = 'moodle2';
        $controller->interactive = 1;
        $controller->purpose = backup::MODE_GENERAL;
        $controller->userid = $USER->id;
        $controller->status = $status;
        $controller->execution = backup::EXECUTION_DELAYED;
        $controller->executiontime = 0;
        $controller->checksum = md5('test');
        $controller->timecreated = time();
        $controller->timemodified = time();
        $controller->progress = 0;
        $controller->controller = '';

        $DB->insert_record('backup_controllers', $controller);

        // Test the function.
        $result = \backup_helper::is_async_pending_for_course($course->id);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test is_async_pending_for_course returns false when no operations exist.
     */
    public function test_is_async_pending_for_course_no_operations(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Test with no backup/restore operations.
        $result = \backup_helper::is_async_pending_for_course($course->id);
        $this->assertFalse($result);
    }

    /**
     * Test is_async_pending_for_course doesn't detect operations in other courses.
     */
    public function test_is_async_pending_for_course_other_course(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create two courses.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();

        // Create a backup operation for course2.
        $backupid = \restore_controller::get_tempdir_name($course2->id, $USER->id);
        $controller = new \stdClass();
        $controller->backupid = $backupid;
        $controller->operation = 'backup';
        $controller->type = backup::TYPE_1COURSE;
        $controller->itemid = $course2->id;
        $controller->format = 'moodle2';
        $controller->interactive = 1;
        $controller->purpose = backup::MODE_GENERAL;
        $controller->userid = $USER->id;
        $controller->status = backup::STATUS_EXECUTING;
        $controller->execution = backup::EXECUTION_DELAYED;
        $controller->executiontime = 0;
        $controller->checksum = md5('test');
        $controller->timecreated = time();
        $controller->timemodified = time();
        $controller->progress = 0;
        $controller->controller = '';

        $DB->insert_record('backup_controllers', $controller);

        // Should NOT detect operations for course1.
        $result = \backup_helper::is_async_pending_for_course($course1->id);
        $this->assertFalse($result);

        // But SHOULD detect operations for course2.
        $result = \backup_helper::is_async_pending_for_course($course2->id);
        $this->assertTrue($result);
    }
}
