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
        $modulerecord = $DB->get_record('modules', ['name' => 'lesson']);
        $modulerecord->visible = 0;
        $DB->update_record('modules', $modulerecord);

        // Check that we only return the assign event.
        $events = $retrievalstrategy->get_raw_events(null, [0], null);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('assign', $event->modulename);
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

        $timestart = $now - 100;
        $timeend = $now + (3 * 86400);
        $groups = [$group1->id, $group2->id];

        // Get user override events.
        $this->setUser($useroverridestudent);
        $events = $retrievalstrategy->get_raw_events([$useroverridestudent->id], $groups, [$course->id]);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - User override', $event->name);

        // Get events for user that does not belong to any group and has no user override events.
        $this->setUser($nogroupstudent);
        $events = $retrievalstrategy->get_raw_events([$nogroupstudent->id], $groups, [$course->id]);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date', $event->name);

        // Get events for user that belongs to groups A and B and has no user override events.
        $this->setUser($group12student);
        $events = $retrievalstrategy->get_raw_events([$group12student->id], $groups, [$course->id]);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - Group A override', $event->name);

        // Get events for user that belongs to group A and has no user override events.
        $this->setUser($group1student);
        $events = $retrievalstrategy->get_raw_events([$group1student->id], $groups, [$course->id]);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - Group A override', $event->name);

        // Add repeating events.
        $repeatingevents = [
            [
                'name' => 'Repeating site event',
                'description' => '',
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
        global $DB;

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
                'format' => 1,
                'categoryid' => $category1->id,
                'userid' => 2,
                'timestart' => time(),
            ],
            [
                'name' => 'E2',
                'eventtype' => 'category',
                'description' => '',
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
}

