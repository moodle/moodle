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
 * PHPUnit tests for block_timeline\external\get_timeline_events.
 *
 * @package    block_timeline
 * @covers     \block_timeline\external\get_timeline_events
 * @copyright  2026 Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_timeline_events_test extends advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Create an action event backed by a real mod_assign instance.
     *
     * The event vault only surfaces events that are tied to an actual CM.
     *
     * @param \stdClass $user The user to own the event.
     * @param \stdClass $course The course the event belongs to.
     * @param int $timesort Unix timestamp used for sorting.
     * @param string $name Event name.
     * @return \calendar_event
     */
    private function create_action_event(
        \stdClass $user,
        \stdClass $course,
        int $timesort,
        string $name = 'Test event'
    ): \calendar_event {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $module    = $generator->create_instance(['course' => $course->id]);

        $record = new \stdClass();
        $record->name       = $name;
        $record->type       = CALENDAR_EVENT_TYPE_ACTION;
        $record->courseid   = $course->id;
        $record->modulename = 'assign';
        $record->instance   = $module->id;
        $record->userid     = $user->id;
        $record->eventtype  = 'due';
        $record->timestart  = $timesort;
        $record->timesort   = $timesort;
        $record->timeduration = 0;
        $record->repeats    = 0;
        $record->repeat     = 0;
        return (new \calendar_event($record))->create($record);
    }

    /**
     * Test that execute() returns events for the authenticated user.
     */
    public function test_execute_returns_events_for_user(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $course    = $generator->create_course();
        $generator->enrol_user($user->id, $course->id);
        $this->setAdminUser();

        $now    = time();
        $future = $now + DAYSECS;

        $this->create_action_event($user, $course, $future, 'Upcoming event');

        $this->setUser($user);
        $result = get_timeline_events::execute(
            timesortfrom: $now - HOURSECS,
            timesortto:   $future + HOURSECS,
            limitnum:     20,
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
        $this->assertNotEmpty($result['events']);

        $names = array_column($result['events'], 'name');
        $this->assertContains('Upcoming event', $names);
    }

    /**
     * Test that execute() only returns events within the given time window.
     */
    public function test_execute_respects_time_window(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $course    = $generator->create_course();
        $generator->enrol_user($user->id, $course->id);
        $this->setAdminUser();

        $now  = time();
        $past = $now - WEEKSECS;

        $this->create_action_event($user, $course, $past, 'Past event');
        $this->create_action_event($user, $course, $now + DAYSECS, 'Future event');

        $this->setUser($user);
        // Only look at events in the next two days — should exclude past.
        $result = get_timeline_events::execute(
            timesortfrom: $now,
            timesortto:   $now + 2 * DAYSECS,
            limitnum:     20,
        );

        $names = array_column($result['events'], 'name');
        $this->assertContains('Future event', $names);
        $this->assertNotContains('Past event', $names);
    }

    /**
     * Test that execute() respects limitnum.
     */
    public function test_execute_respects_limit(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $course    = $generator->create_course();
        $generator->enrol_user($user->id, $course->id);
        $this->setAdminUser();

        $now = time();
        for ($i = 1; $i <= 5; $i++) {
            $this->create_action_event($user, $course, $now + $i * DAYSECS, "Event $i");
        }

        $this->setUser($user);
        $result = get_timeline_events::execute(
            timesortfrom: $now - HOURSECS,
            timesortto:   $now + 10 * DAYSECS,
            limitnum:     2,
        );

        $this->assertCount(2, $result['events']);
    }

    /**
     * Test that execute() requires the user to be logged in.
     */
    public function test_execute_requires_login(): void {
        $this->setGuestUser();
        $this->expectException(\require_login_exception::class);
        get_timeline_events::execute(timesortfrom: time() - DAYSECS, timesortto: time() + DAYSECS);
    }

    /**
     * Test that aftereventid is respected — only events after the given id are returned.
     */
    public function test_execute_respects_aftereventid(): void {
        $generator = $this->getDataGenerator();
        $user      = $generator->create_user();
        $course    = $generator->create_course();
        $generator->enrol_user($user->id, $course->id);
        $this->setAdminUser();

        $now = time();
        $e1  = $this->create_action_event($user, $course, $now + 1 * DAYSECS, 'First');
        $this->create_action_event($user, $course, $now + 2 * DAYSECS, 'Second');

        $this->setUser($user);
        $result = get_timeline_events::execute(
            timesortfrom: $now - HOURSECS,
            timesortto:   $now + 10 * DAYSECS,
            aftereventid: $e1->id,
            limitnum:     20,
        );

        $names = array_column($result['events'], 'name');
        $this->assertContains('Second', $names);
        $this->assertNotContains('First', $names);
    }

    /**
     * Test that an empty result is returned when no events match the window.
     */
    public function test_execute_returns_empty_for_no_events(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $now = time();
        $result = get_timeline_events::execute(
            timesortfrom: $now,
            timesortto:   $now + DAYSECS,
            limitnum:     20,
        );

        $this->assertIsArray($result['events']);
        $this->assertEmpty($result['events']);
    }
}
