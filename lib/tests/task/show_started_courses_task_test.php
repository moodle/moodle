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

namespace core\task;

use core\tests\courses_tasks_testcase;

/**
 * Class containing unit tests for the show started courses task.
 *
 * It automatically sets the course visibility to shown when the course start date matches the current day.
 *
 * @package   core
 * @copyright 2023 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\task\show_started_courses_task
 */
final class show_started_courses_task_test extends courses_tasks_testcase {
    /**
     * Test show_started_courses cron task.
     *
     * @dataProvider get_courses_provider
     * @covers ::execute
     *
     * @param int $lastweek Number of courses with the start date set to last week to be created.
     * @param int $yesterday Number of courses with the start date set to yesterday to be created.
     * @param int $tomorrow Number of courses with the start date set to tomorrow to be created.
     * @param bool $createvisible Whether visible courses should be created or not.
     */
    public function test_show_started_courses(
        int $lastweekcount,
        int $yesterdaycount,
        int $tomorrowcount,
        bool $createvisible = true
    ): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $visiblecourses = [];
        $hiddencourses = [];

        $now = time();
        $lastweek = $now - WEEKSECS;
        $yesterday = $now - DAYSECS + 60;
        $tomorrow = $now + DAYSECS;

        // Hidden course that started last week.
        for ($i = 0; $i < $lastweekcount; $i++) {
             $generator->create_course(['visible' => false, 'startdate' => $lastweek]);
        }
        // Hidden course that started yesterday.
        for ($i = 0; $i < $yesterdaycount; $i++) {
            $hiddencourses[] = $generator->create_course(['visible' => false, 'startdate' => $yesterday])->id;
        }
        // Hidden course that hasn't started yet.
        for ($i = 0; $i < $tomorrowcount; $i++) {
            $generator->create_course(['visible' => false, 'startdate' => $tomorrow]);
        }
        if ($createvisible) {
            // Visible course that already started.
            $visiblecourses[] = $generator->create_course(['visible' => true, 'startdate' => $yesterday])->id;
            // Visible course that hasn't started yet.
            $visiblecourses[] = $generator->create_course(['visible' => true, 'startdate' => $tomorrow])->id;
        }
        $visibletotal = count($visiblecourses) + 1;
        $coursetotal = $visibletotal + $lastweekcount + $yesterdaycount + $tomorrowcount;

        // Check current courses have been created correctly.
        $this->assertEquals($coursetotal, $DB->count_records('course'));
        $this->assertEquals($visibletotal, $DB->count_records('course', ['visible' => 1]));

        $sink = $this->redirectEvents();

        // Run the show started courses task.
        ob_start();
        $task = new show_started_courses_task();
        $task->execute();
        ob_end_clean();

        // Confirm the courses with yesterday as starting date are visible too. The rest should remain hidden.
        $this->assertEquals($coursetotal, $DB->count_records('course'));
        $courses = $DB->get_records('course', ['visible' => 1], '', 'id');
        $this->assertCount($visibletotal + $yesterdaycount, $courses);
        $expected = array_merge($hiddencourses, $visiblecourses);
        $this->assertEquals(asort($expected), asort($courses));

        // Check the started course event has been raised.
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount($yesterdaycount, $events);
        foreach ($events as $event) {
            $this->assertInstanceOf('\\core\\event\\course_started', $event);
            $this->assertArrayHasKey($event->courseid, array_flip($expected));
        }
    }
}
