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

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/show_started_courses_task_test.php');

/**
 * Class containing unit tests for the hide ended courses task.
 *
 * It automatically sets the course visibility to hidden when the course end date matches the current day.
 *
 * @package   core
 * @copyright 2023 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\task\hide_ended_courses_task
 */
class hide_ended_courses_task_test extends \core\task\show_started_courses_task_test {
    /**
     * Test hide_ended_courses cron task.
     *
     * @dataProvider get_courses_provider
     * @covers ::execute
     *
     * @param int $nextweekvisible Number of courses with the end date set to next week to be created.
     * @param int $yesterdayvisible Number of courses with the end date set to yesterday to be created.
     * @param int $tomorrowvisible Number of courses with the end date set to tomorrow to be created.
     * @param bool $createhidden Whether hidden courses should be created or not.
     */
    public function test_hide_ended_courses(
        int $nextweekvisible,
        int $yesterdayvisible,
        int $tomorrowvisible,
        bool $createhidden = true
    ): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $visiblecourses = [];
        $hiddencourses = [];

        $now = time();
        $nextweek = $now + WEEKSECS;
        $yesterday = $now - DAYSECS + MINSECS;
        $tomorrow = $now + DAYSECS;

        // Visible course that finishes last week.
        for ($i = 0; $i < $nextweekvisible; $i++) {
             $generator->create_course(['visible' => true, 'enddate' => $nextweek]);
        }
        // Visible course that finished yesterday.
        for ($i = 0; $i < $yesterdayvisible; $i++) {
            $visiblecourses[] = $generator->create_course(
                ['visible' => true, 'startdate' => $yesterday - MINSECS , 'enddate' => $yesterday]
            )->id;
        }
        // Visible course that hasn't finished yet.
        for ($i = 0; $i < $tomorrowvisible; $i++) {
            $generator->create_course(['visible' => true, 'enddate' => $tomorrow]);
        }
        if ($createhidden) {
            // Visible course that already finished.
            $hiddencourses[] = $generator->create_course(
                ['visible' => false, 'startdate' => $yesterday - MINSECS, 'enddate' => $yesterday]
            )->id;
            // Visible course that hasn't finished yet.
            $hiddencourses[] = $generator->create_course(['visible' => false, 'enddate' => $tomorrow])->id;
        }
        $hiddentotal = count($hiddencourses);
        // Course total also includes site course.
        $coursetotal = $hiddentotal + $nextweekvisible + $yesterdayvisible + $tomorrowvisible + 1;

        // Check current courses have been created correctly.
        $this->assertEquals($coursetotal, $DB->count_records('course'));
        $this->assertEquals(count($hiddencourses), $DB->count_records('course', ['visible' => 0]));

        $sink = $this->redirectEvents();

        // Run the hide ended courses task.
        ob_start();
        $task = new hide_ended_courses_task();
        $task->execute();
        ob_end_clean();

        // Confirm the courses with yesterday as ending date are hidden too. The rest should remain visible.
        $courses = $DB->get_records('course', ['visible' => 0], '', 'id');
        $this->assertCount($hiddentotal + $yesterdayvisible, $courses);
        $expected = array_merge($hiddencourses, $visiblecourses);
        $this->assertEquals(asort($expected), asort($courses));

        // Check the ended course event has been raised.
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount($yesterdayvisible, $events);
        foreach ($events as $event) {
            $this->assertInstanceOf('\\core\\event\\course_ended', $event);
            $this->assertArrayHasKey($event->courseid, array_flip($expected));
        }
    }
}
