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

namespace block_timeline\external;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/calendar/lib.php');

/**
 * PHPUnit tests for block_timeline\external\get_courses_with_events.
 *
 * @package    block_timeline
 * @covers     \block_timeline\external\get_courses_with_events
 * @copyright  2026 Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_courses_with_events_test extends advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Enrol a user and configure the course to be 'in-progress'.
     *
     * @param \stdClass $user
     * @param \stdClass $course
     */
    private function enrol_user_in_progress(\stdClass $user, \stdClass $course): void {
        global $DB;

        $now = time();
        $DB->set_field('course', 'startdate', $now - DAYSECS, ['id' => $course->id]);
        $DB->set_field('course', 'enddate', $now + YEARSECS, ['id' => $course->id]);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
    }

    /**
     * Create an action event backed by a real mod_assign instance.
     *
     * @param \stdClass $user The user to own the event.
     * @param \stdClass $course The course the event belongs to.
     * @param int $timesort Unix timestamp used for sorting.
     * @param string $name Event name.
     * @return \calendar_event
     */
    private function create_action_event(\stdClass $user, \stdClass $course, int $timesort, string $name): \calendar_event {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $module    = $generator->create_instance(['course' => $course->id]);

        $record = new \stdClass();
        $record->name         = $name;
        $record->type         = CALENDAR_EVENT_TYPE_ACTION;
        $record->courseid     = $course->id;
        $record->modulename   = 'assign';
        $record->instance     = $module->id;
        $record->userid       = $user->id;
        $record->eventtype    = 'due';
        $record->timestart    = $timesort;
        $record->timesort     = $timesort;
        $record->timeduration = 0;
        $record->repeats      = 0;
        $record->repeat       = 0;
        return (new \calendar_event($record))->create($record);
    }

    /**
     * Test that execute() returns enrolled in-progress courses.
     */
    public function test_execute_returns_in_progress_courses(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $course    = $generator->create_course(['fullname' => 'Test Course']);
        $this->enrol_user_in_progress($user, $course);
        $this->setUser($user);

        $result = get_courses_with_events::execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('courses', $result);

        $coursenames = array_column($result['courses'], 'fullname');
        $this->assertContains('Test Course', $coursenames);
    }

    /**
     * Test that execute() embeds action events inside each course.
     */
    public function test_execute_includes_course_events(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $course    = $generator->create_course(['fullname' => 'Course With Events']);
        $this->enrol_user_in_progress($user, $course);
        $this->setAdminUser();

        $now = time();
        $this->create_action_event($user, $course, $now + DAYSECS, 'Assignment due');

        $this->setUser($user);
        $result = get_courses_with_events::execute(starttime: $now - HOURSECS);

        $this->assertNotEmpty($result['courses']);

        // Find the course by fullname and check its events.
        $found = null;
        foreach ($result['courses'] as $c) {
            if ($c['fullname'] === 'Course With Events') {
                $found = $c;
                break;
            }
        }
        $this->assertNotNull($found, 'Course With Events not found in results');
        $names = array_column($found['events'], 'name');
        $this->assertContains('Assignment due', $names);
    }

    /**
     * Test pagination: limit and offset work correctly.
     */
    public function test_execute_pagination(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $this->setUser($user);

        // Create 4 in-progress courses.
        for ($i = 1; $i <= 4; $i++) {
            $course = $generator->create_course(['fullname' => "Pagination Course $i"]);
            $this->enrol_user_in_progress($user, $course);
        }

        $page1 = get_courses_with_events::execute(limit: 2, offset: 0);
        $this->assertCount(2, $page1['courses']);
        $this->assertTrue($page1['morecoursesavailable']);
        $this->assertEquals(2, $page1['nextoffset']);

        $page2 = get_courses_with_events::execute(limit: 2, offset: $page1['nextoffset']);
        $this->assertCount(2, $page2['courses']);
        $this->assertFalse($page2['morecoursesavailable']);
    }

    /**
     * Test that execute() returns empty when user has no enrolled courses.
     */
    public function test_execute_no_courses(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $result = get_courses_with_events::execute();

        $this->assertEmpty($result['courses']);
        $this->assertFalse($result['morecoursesavailable']);
        $this->assertEquals(0, $result['nextoffset']);
    }

    /**
     * Test that morecoursesavailable is false when all courses fit in a single page.
     */
    public function test_execute_no_more_courses(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $this->setUser($user);

        $course = $generator->create_course();
        $this->enrol_user_in_progress($user, $course);

        $result = get_courses_with_events::execute(limit: 5);
        $this->assertFalse($result['morecoursesavailable']);
    }

    /**
     * Test that execute() requires the user to be logged in.
     */
    public function test_execute_requires_login(): void {
        $this->setGuestUser();
        $this->expectException(\require_login_exception::class);
        get_courses_with_events::execute();
    }
}
