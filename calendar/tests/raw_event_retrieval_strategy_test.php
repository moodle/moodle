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
 * Raw event retrieval strategy tests.
 *
 * @package core_calendar
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/calendar/tests/helpers.php');

use core_calendar\local\event\strategies\raw_event_retrieval_strategy;

/**
 * Raw event retrieval strategy testcase.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_raw_event_retrieval_strategy_testcase extends advanced_testcase {
    /**
     * Test retrieval strategy when module is disabled.
     */
    public function test_get_raw_events_with_disabled_module() {
        global $DB;

        $this->resetAfterTest();
        $retrievalstrategy = new raw_event_retrieval_strategy();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $student = $generator->create_user();
        $generator->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $events = [
            [
                'name' => 'Start of assignment',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => 1,
                'eventtype' => 'due',
                'timestart' => time(),
                'timeduration' => 86400,
                'visible' => 1
            ], [
                'name' => 'Start of lesson',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'lesson',
                'instance' => 1,
                'eventtype' => 'end',
                'timestart' => time(),
                'timeduration' => 86400,
                'visible' => 1
            ]
        ];

        foreach ($events as $event) {
            calendar_event::create($event, false);
        }

        // Get all events.
        $events = $retrievalstrategy->get_raw_events(null, [0], null);
        $this->assertCount(2, $events);

        // Disable the lesson module.
        $DB->set_field('modules', 'visible', 0, ['name' => 'lesson']);

        // Check that we only return the assign event.
        $events = $retrievalstrategy->get_raw_events(null, [0], null);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('assign', $event->modulename);

        // Now, log out and repeat the above test in the reverse order.
        $this->setUser();

        // Check that we only return the assign event (given that the lesson module is still disabled).
        $events = $retrievalstrategy->get_raw_events([$student->id], [0], null);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('assign', $event->modulename);

        // Enable the lesson module.
        $DB->set_field('modules', 'visible', 1, ['name' => 'lesson']);

        // Get all events.
        $events = $retrievalstrategy->get_raw_events(null, [0], null);
        $this->assertCount(2, $events);
    }

    /**
     * Test retrieval strategy when there are overrides.
     */
    public function test_get_raw_event_strategy_with_overrides() {
        $this->resetAfterTest();

        $retrievalstrategy = new raw_event_retrieval_strategy();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');

        $instance = $plugingenerator->create_instance(['course' => $course->id]);

        // Create users.
        $useroverridestudent = $generator->create_user();
        $group1student = $generator->create_user();
        $group2student = $generator->create_user();
        $group12student = $generator->create_user();
        $nogroupstudent = $generator->create_user();

        // Enrol users.
        $generator->enrol_user($useroverridestudent->id, $course->id, 'student');
        $generator->enrol_user($group1student->id, $course->id, 'student');
        $generator->enrol_user($group2student->id, $course->id, 'student');
        $generator->enrol_user($group12student->id, $course->id, 'student');

        $generator->enrol_user($nogroupstudent->id, $course->id, 'student');

        // Create groups.
        $group1 = $generator->create_group(['courseid' => $course->id, 'name' => 'Group 1']);
        $group2 = $generator->create_group(['courseid' => $course->id, 'name' => 'Group 2']);

        // Add members to groups.
        $generator->create_group_member(['groupid' => $group1->id, 'userid' => $group1student->id]);
        $generator->create_group_member(['groupid' => $group2->id, 'userid' => $group2student->id]);
        $generator->create_group_member(['groupid' => $group1->id, 'userid' => $group12student->id]);
        $generator->create_group_member(['groupid' => $group2->id, 'userid' => $group12student->id]);

        $now = time();

        // Events with the same module name, instance and event type.
        $events = [
            [
                'name' => 'Assignment 1 due date',
                'description' => '',
                'location' => 'Test',
                'format' => 0,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now,
                'timeduration' => 0,
                'visible' => 1
            ], [
                'name' => 'Assignment 1 due date - User override',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => 0,
                'groupid' => 0,
                'userid' => $useroverridestudent->id,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now + 86400,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => CALENDAR_EVENT_USER_OVERRIDE_PRIORITY
            ], [
                'name' => 'Assignment 1 due date - Group A override',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $group1->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now + (2 * 86400),
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 1,
            ], [
                'name' => 'Assignment 1 due date - Group B override',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $group2->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now + (3 * 86400),
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 2,
            ],
        ];

        foreach ($events as $event) {
            calendar_event::create($event, false);
        }

        $groups = [$group1->id, $group2->id];

        // Do the following tests multiple times when logged in with different users. Also run the whole set when logged out.
        // In any cases, the tests should not depend on the logged-in user.
        foreach ([$useroverridestudent, $nogroupstudent, $group12student, $group1student, null] as $login) {
            $this->setUser($login);

            // Get user override events.
            $events = $retrievalstrategy->get_raw_events([$useroverridestudent->id], $groups, [$course->id]);
            $this->assertCount(1, $events);
            $event = reset($events);
            $this->assertEquals('Assignment 1 due date - User override', $event->name);

            // Get events for user that does not belong to any group and has no user override events.
            $events = $retrievalstrategy->get_raw_events([$nogroupstudent->id], $groups, [$course->id]);
            $this->assertCount(1, $events);
            $event = reset($events);
            $this->assertEquals('Assignment 1 due date', $event->name);

            // Get events for user that belongs to groups A and B and has no user override events.
            $events = $retrievalstrategy->get_raw_events([$group12student->id], $groups, [$course->id]);
            $this->assertCount(1, $events);
            $event = reset($events);
            $this->assertEquals('Assignment 1 due date - Group A override', $event->name);

            // Get events for user that belongs to group A and has no user override events.
            $events = $retrievalstrategy->get_raw_events([$group1student->id], $groups, [$course->id]);
            $this->assertCount(1, $events);
            $event = reset($events);
            $this->assertEquals('Assignment 1 due date - Group A override', $event->name);
        }

        // Add repeating events.
        $repeatingevents = [
            [
                'name' => 'Repeating site event',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => SITEID,
                'groupid' => 0,
                'userid' => 2,
                'repeatid' => 1,
                'modulename' => '0',
                'instance' => 0,
                'eventtype' => 'site',
                'timestart' => $now + 86400,
                'timeduration' => 0,
                'visible' => 1,
            ],
            [
                'name' => 'Repeating site event',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => SITEID,
                'groupid' => 0,
                'userid' => 2,
                'repeatid' => 1,
                'modulename' => '0',
                'instance' => 0,
                'eventtype' => 'site',
                'timestart' => $now + (2 * 86400),
                'timeduration' => 0,
                'visible' => 1,
            ],
        ];

        foreach ($repeatingevents as $event) {
            calendar_event::create($event, false);
        }

        // Make sure repeating events are not filtered out.
        $events = $retrievalstrategy->get_raw_events();
        $this->assertCount(3, $events);
    }

    /**
     * Test retrieval strategy with category specifications.
     */
    public function test_get_raw_events_category() {
        $this->resetAfterTest();
        $retrievalstrategy = new raw_event_retrieval_strategy();
        $generator = $this->getDataGenerator();
        $category1 = $generator->create_category();
        $category2 = $generator->create_category();
        $events = [
            [
                'name' => 'E1',
                'eventtype' => 'category',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'categoryid' => $category1->id,
                'userid' => 2,
                'timestart' => time(),
            ],
            [
                'name' => 'E2',
                'eventtype' => 'category',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'categoryid' => $category2->id,
                'userid' => 2,
                'timestart' => time() + 1,
            ],
        ];

        foreach ($events as $event) {
            calendar_event::create($event, false);
        }

        // Get all events.
        $events = $retrievalstrategy->get_raw_events(null, null, null, null);
        $this->assertCount(2, $events);

        $event = array_shift($events);
        $this->assertEquals('E1', $event->name);
        $event = array_shift($events);
        $this->assertEquals('E2', $event->name);

        // Get events for C1 events.
        $events = $retrievalstrategy->get_raw_events(null, null, null, [$category1->id]);
        $this->assertCount(1, $events);

        $event = array_shift($events);
        $this->assertEquals('E1', $event->name);

        // Get events for C2 events.
        $events = $retrievalstrategy->get_raw_events(null, null, null, [$category2->id]);
        $this->assertCount(1, $events);

        $event = array_shift($events);
        $this->assertEquals('E2', $event->name);

        // Get events for several categories.
        $events = $retrievalstrategy->get_raw_events(null, null, null, [$category1->id, $category2->id]);
        $this->assertCount(2, $events);
    }

    public function test_get_raw_events_for_multiple_users() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        // Create user events.
        $events = [
            [
                'name' => 'User1 Event',
                'eventtype' => 'user',
                'userid' => $user1->id,
                'timestart' => time(),
            ], [
                'name' => 'User2 Event',
                'eventtype' => 'user',
                'userid' => $user2->id,
                'timestart' => time(),
            ], [
                'name' => 'User3 Event',
                'eventtype' => 'user',
                'userid' => $user3->id,
                'timestart' => time(),
            ]
        ];
        foreach ($events as $event) {
            calendar_event::create($event, false);
        }

        $retrievalstrategy = new raw_event_retrieval_strategy();

        // Get all events.
        $events = $retrievalstrategy->get_raw_events([$user1->id, $user2->id]);
        $this->assertCount(2, $events);
        $this->assertEquals(
                ['User1 Event', 'User2 Event'],
                array_column($events, 'name'),
                '', 0.0, 10, true);
    }

    public function test_get_raw_events_for_groups_with_no_members() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();

        // Create groups.
        $group1 = $generator->create_group(['courseid' => $course->id, 'name' => 'Group 1']);
        $group2 = $generator->create_group(['courseid' => $course->id, 'name' => 'Group 2']);

        // Create group events.
        $events = [
            [
                'name' => 'Group 1 Event',
                'eventtype' => 'group',
                'groupid' => $group1->id,
                'timestart' => time(),
            ], [
                'name' => 'Group 2 Event',
                'eventtype' => 'group',
                'groupid' => $group2->id,
                'timestart' => time(),
            ]
        ];
        foreach ($events as $event) {
            calendar_event::create($event, false);
        }

        $retrievalstrategy = new raw_event_retrieval_strategy;

        // Get group eventsl.
        $events = $retrievalstrategy->get_raw_events(null, [$group1->id, $group2->id]);
        $this->assertCount(2, $events);
        $this->assertEquals(
                ['Group 1 Event', 'Group 2 Event'],
                array_column($events, 'name'),
                '', 0.0, 10, true);
    }

    /**
     * Test retrieval strategy with empty filters.
     * This covers a edge case not covered elsewhere to ensure its SQL is cross
     * db compatible. The test is ensuring we don't get a DML Exception with
     * the filters setup this way.
     */
    public function test_get_raw_events_with_empty_user_and_category_lists() {
        $retrievalstrategy = new raw_event_retrieval_strategy;
        $retrievalstrategy->get_raw_events([], null, null, []);
    }
}
