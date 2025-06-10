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
 * Tests for file updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use Prophecy\Argument;
use tool_ally\push_config;
use tool_ally\push_file_updates;
use tool_ally\task\file_updates_task;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Tests for file updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_updates_task_test extends abstract_testcase {

    /**
     * First run should set the timestamp then exit.
     */
    public function test_initial_run() {
        $this->resetAfterTest();

        $this->assertEmpty(get_config('tool_ally', 'push_timestamp'));

        $task          = new file_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $task->updates = $this->prophesize(push_file_updates::class)->reveal();

        $expected = time();
        $task->execute();

        $this->assertGreaterThanOrEqual($expected, get_config('tool_ally', 'push_timestamp'));
    }

    /**
     * Nothing should happen if config is invalid.
     */
    public function test_invalid_config() {
        $task          = new file_updates_task();
        $task->updates = $this->prophesize(push_file_updates::class)->reveal();

        $task->execute();

        $this->assertEmpty(get_config('tool_ally', 'push_timestamp'));
    }

    /**
     * Ensure that basic execution and timestamp management is working.
     */
    public function test_push_updates() {
        $this->resetAfterTest();

        $this->setAdminUser();

        set_config('push_timestamp', time() - (WEEKSECS * 2), 'tool_ally');

        $course      = $this->getDataGenerator()->create_course();
        $resource    = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $resource2   = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $filecreated = $this->get_resource_file($resource);
        $fileupdated = $this->get_resource_file($resource2);

        $fileupdated->set_timemodified(time() - WEEKSECS);

        $task          = new file_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $updates = $this->prophesize(push_file_updates::class);
        $updates->send(Argument::type('array'))->willReturn(true);
        $task->updates = $updates->reveal();

        $task->execute();

        $this->assertEquals($filecreated->get_timemodified(), get_config('tool_ally', 'push_timestamp'));
    }

    /**
     * Ensure that our batch looping is working as expected.
     */
    public function test_push_updates_batching() {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('push_timestamp', time() - (WEEKSECS * 2), 'tool_ally');

        $course = $this->getDataGenerator()->create_course();
        for ($i = 0; $i < 5; $i++) {
            $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        }

        $updates = $this->prophesize(push_file_updates::class);
        $updates->send(Argument::type('array'))->willReturn(true);
        $updates->send(Argument::type('array'))->shouldBeCalledTimes(3);

        $task          = new file_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret', 2);
        $task->updates = $updates->reveal();

        $task->execute();

        $updates->checkProphecyMethodsPredictions();
    }

    /**
     * Test pushing of file deletions.
     */
    public function test_push_deletes() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('push_timestamp', time() - (WEEKSECS * 2), 'tool_ally');

        $this->dataset_from_array(include(__DIR__.'/fixtures/deleted_files.php'))->to_database();

        $updates = $this->prophesize(push_file_updates::class);
        $updates->send(Argument::type('array'))->willReturn(true);
        $updates->send(Argument::type('array'))->shouldBeCalledTimes(3);

        $task          = new file_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret', 2);
        $task->updates = $updates->reveal();

        $task->execute();

        $updates->checkProphecyMethodsPredictions();

        $this->assertEmpty($DB->get_records('tool_ally_deleted_files'));
    }
}
