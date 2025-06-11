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
 * Tests for course updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\push_config;
use tool_ally\push_course_updates;
use tool_ally\task\course_updates_task;
use tool_ally\prophesize_deprecation_workaround_mixin;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');
require_once(__DIR__.'/prophesize_deprecation_workaround_mixin.php');

/**
 * Tests for course updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_updates_task_test extends abstract_testcase {
    use prophesize_deprecation_workaround_mixin;

    /**
     * Ensure that basic execution and timestamp management is working.
     */
    public function test_push_updates() {
        $this->resetAfterTest();

        $this->setAdminUser();

        set_config('push_cli_only', 1, 'tool_ally');
        set_config('deferredcourseevents', 1, 'tool_ally');

        $this->getDataGenerator()->create_course();
        $task          = new course_updates_task();
        $task->config  = new push_config('url', 'key', 'secret');
        $updates       = $this->createMock(push_course_updates::class);
        $updates->expects($this->once())
            ->method('send')
            ->with($this->isType('array'));
        $task->updates = $updates;

        $task->execute();
    }

    /**
     * Ensure that our batch looping is working as expected.
     */
    public function test_push_updates_batching() {
        $this->resetAfterTest();

        set_config('push_cli_only', 1, 'tool_ally');
        set_config('deferredcourseevents', 1, 'tool_ally');

        $courses = [];
        // Create 5 courses.
        for ($i = 0; $i < 5; $i++) {
            $courses[] = $this->getDataGenerator()->create_course();
        }

        $updates = $this->createMock(push_course_updates::class);
        $updates->expects($this->exactly(3))
            ->method('send')
            ->with($this->isType('array'));

        $task          = new course_updates_task();
        $task->config  = new push_config('url', 'key', 'secret', 2);
        $task->updates = $updates;

        $task->execute();
    }

    /**
     * Test pushing of content deletions.
     */
    public function test_push_deletes() {
        global $DB;

        $this->resetAfterTest();

        set_config('deferredcourseevents', 1, 'tool_ally');

        $courses = [];
        // Create 5 courses.
        for ($i = 0; $i < 5; $i++) {
            $courses[] = $this->getDataGenerator()->create_course();
        }

        // Wipe out course event queue - it will already have been populated by events triggered whilst creating course.
        $DB->delete_records('tool_ally_course_event');

        foreach ($courses as $course) {
            // Course deletion triggers the event, so creating the Moodle course deletion event.
            $delevent = \core\event\course_deleted::create([
                'objectid' => $course->id,
                'context'  => \context_course::instance($course->id),
                'other'    => [
                    'shortname' => $course->shortname,
                    'fullname' => $course->fullname,
                    'idnumber' => $course->idnumber
                ]
            ]);
            $delevent->add_record_snapshot('course', $course);
            $delevent->trigger();
        }

        $updates = $this->createMock(push_course_updates::class);
        $updates->expects($this->exactly(3))
            ->method('send')
            ->with($this->isType('array'));

        $task          = new course_updates_task();
        $task->config  = new push_config('url', 'key', 'secret', 2);
        $task->updates = $updates;

        $task->execute();

        // The deleted content queue should still be populated at this point.
        $this->assertNotEmpty($DB->get_records('tool_ally_course_event'));
    }

}
