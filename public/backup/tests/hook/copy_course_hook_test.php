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

namespace core_backup\hook;

use core\di;
use copy_helper;
use advanced_testcase;
use core\hook\manager;
use core\task\manager as taskmanager;
use core\event\course_restored;
use core_backup\hook\fixtures\copy_course_hook_callbacks;

/**
 * Class to test the hook inside asynchronous_copy_task.
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class copy_course_hook_test extends advanced_testcase {

    /**
     * Test the hook.
     *
     * @covers \core\task\asynchronous_copy_task::execute
     * @covers \core_backup\hook\before_copy_course_execute
     */
    public function test_copy_course_hook(): void {
        // Load the callback classes.
        require_once(__DIR__ . '/fixtures/copy_course_hooks.php');

        // Replace the version of the manager in the DI container with a phpunit one.
        di::set(
            manager::class,
            manager::phpunit_get_instance([
                // Load a list of hooks for `test_plugin1` from the fixture file.
                'test_plugin1' => __DIR__ .
                    '/fixtures/copy_course_hooks.php',
            ]),
        );

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $copydata = (object) [
            'courseid' => $course->id,
            'fullname' => 'Name',
            'shortname' => 'name',
            'category' => 1,
            'visible' => 1,
            'startdate' => '123456789',
            'enddate' => '123456789',
            'idnumber' => 'dnum',
            'userdata' => false,
        ];

        $processed = copy_helper::process_formdata($copydata);
        copy_helper::create_copy($processed);
        $sink = $this->redirectEvents();

        // Capture mtrace output.
        ob_start();

        // Execute adhoc task.
        $now = time();
        $task = taskmanager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_copy_task', $task);
        $task->execute();
        taskmanager::adhoc_task_complete($task);

        ob_get_clean();

        $this->assertGreaterThan(0, copy_course_hook_callbacks::$count);

        // Check that the restore settings have been added to the event data.
        $events = $sink->get_events();
        $count = 0;
        foreach ($events as $event) {
            if ($event instanceof course_restored) {
                $count++;
                $data = $event->get_data();
                $this->assertNotEmpty($data['other']['settings']);
            }
        }
        $this->assertGreaterThan(0, $count);
    }
}
