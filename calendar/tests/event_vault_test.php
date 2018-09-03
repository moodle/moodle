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
 * This file contains the class that handles testing of the calendar event vault.
 *
 * @package core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/calendar/tests/helpers.php');

use core_calendar\local\event\data_access\event_vault;
use core_calendar\local\event\strategies\raw_event_retrieval_strategy;

/**
 * This file contains the class that handles testing of the calendar event vault.
 *
 * @package core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_event_vault_testcase extends advanced_testcase {

    /**
     * Test that get_action_events_by_timesort returns events after the
     * provided timesort value.
     */
    public function test_get_action_events_by_timesort_after_time() {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $this->setUser($user);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION
            ]);
        }

        $events = $vault->get_action_events_by_timesort($user, 3);

        $this->assertCount(3, $events);
        $this->assertEquals('Event 3', $events[0]->get_name());
        $this->assertEquals('Event 4', $events[1]->get_name());
        $this->assertEquals('Event 5', $events[2]->get_name());

        $events = $vault->get_action_events_by_timesort($user, 3, null, null, 1);

        $this->assertCount(1, $events);
        $this->assertEquals('Event 3', $events[0]->get_name());

        $events = $vault->get_action_events_by_timesort($user, 6);

        $this->assertCount(0, $events);
    }

    /**
     * Test that get_action_events_by_timesort returns events before the
     * provided timesort value.
     */
    public function test_get_action_events_by_timesort_before_time() {
        $this->resetAfterTest(true);
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => 1
            ]);
        }

        $events = $vault->get_action_events_by_timesort($user, null, 3);

        $this->assertCount(3, $events);
        $this->assertEquals('Event 1', $events[0]->get_name());
        $this->assertEquals('Event 2', $events[1]->get_name());
        $this->assertEquals('Event 3', $events[2]->get_name());

        $events = $vault->get_action_events_by_timesort($user, null, 3, null, 1);

        $this->assertCount(1, $events);
        $this->assertEquals('Event 1', $events[0]->get_name());

        $events = $vault->get_action_events_by_timesort($user, 6);

        $this->assertCount(0, $events);
    }

    /**
     * Test that get_action_events_by_timesort returns events between the
     * provided timesort values.
     */
    public function test_get_action_events_by_timesort_between_time() {
        $this->resetAfterTest(true);
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => 1
            ]);
        }

        $events = $vault->get_action_events_by_timesort($user, 2, 4);

        $this->assertCount(3, $events);
        $this->assertEquals('Event 2', $events[0]->get_name());
        $this->assertEquals('Event 3', $events[1]->get_name());
        $this->assertEquals('Event 4', $events[2]->get_name());

        $events = $vault->get_action_events_by_timesort($user, 2, 4, null, 1);

        $this->assertCount(1, $events);
        $this->assertEquals('Event 2', $events[0]->get_name());
    }

    /**
     * Test that get_action_events_by_timesort returns events between the
     * provided timesort values and after the last seen event when one is
     * provided.
     */
    public function test_get_action_events_by_timesort_between_time_after_event() {
        $this->resetAfterTest(true);
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $records = [];
        for ($i = 1; $i < 21; $i++) {
            $records[] = create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => 1
            ]);
        }

        $aftereventid = $records[6]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        $events = $vault->get_action_events_by_timesort($user, 3, 15, $afterevent);

        $this->assertCount(8, $events);
        $this->assertEquals('Event 8', $events[0]->get_name());

        $events = $vault->get_action_events_by_timesort($user, 3, 15, $afterevent, 3);

        $this->assertCount(3, $events);
    }

    /**
     * Test that get_action_events_by_timesort returns events between the
     * provided timesort values and the last seen event can be provided to
     * get paginated results.
     */
    public function test_get_action_events_by_timesort_between_time_skip_even_records() {
        $this->resetAfterTest(true);
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        // The factory will return every event that is divisible by 2.
        $factory = new action_event_test_factory(function($actionevent) {
            static $count = 0;
            $count++;
            return ($count % 2) ? true : false;
        });
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        for ($i = 1; $i < 41; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => 1
            ]);
        }

        $events = $vault->get_action_events_by_timesort($user, 3, 35, null, 5);

        $this->assertCount(5, $events);
        $this->assertEquals('Event 3', $events[0]->get_name());
        $this->assertEquals('Event 5', $events[1]->get_name());
        $this->assertEquals('Event 7', $events[2]->get_name());
        $this->assertEquals('Event 9', $events[3]->get_name());
        $this->assertEquals('Event 11', $events[4]->get_name());

        $afterevent = $events[4];
        $events = $vault->get_action_events_by_timesort($user, 3, 35, $afterevent, 5);

        $this->assertCount(5, $events);
        $this->assertEquals('Event 13', $events[0]->get_name());
        $this->assertEquals('Event 15', $events[1]->get_name());
        $this->assertEquals('Event 17', $events[2]->get_name());
        $this->assertEquals('Event 19', $events[3]->get_name());
        $this->assertEquals('Event 21', $events[4]->get_name());
    }

    /**
     * Test that get_action_events_by_timesort returns events between the
     * provided timesort values. The database will continue to be read until the
     * number of events requested has been satisfied. In this case the first
     * five events are rejected so it should require two database requests.
     */
    public function test_get_action_events_by_timesort_between_time_skip_first_records() {
        $this->resetAfterTest(true);
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        $limit = 5;
        $seen = 0;
        // The factory will skip the first $limit events.
        $factory = new action_event_test_factory(function($actionevent) use (&$seen, $limit) {
            if ($seen < $limit) {
                $seen++;
                return false;
            } else {
                return true;
            }
        });
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        for ($i = 1; $i < 21; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => 1
            ]);
        }

        $events = $vault->get_action_events_by_timesort($user, 1, 20, null, $limit);

        $this->assertCount($limit, $events);
        $this->assertEquals(sprintf('Event %d', $limit + 1), $events[0]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 2), $events[1]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 3), $events[2]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 4), $events[3]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 5), $events[4]->get_name());
    }

    /**
     * Test that get_action_events_by_timesort returns events between the
     * provided timesort values and after the last seen event when one is
     * provided. This should work even when the event ids aren't ordered the
     * same as the timesort order.
     */
    public function test_get_action_events_by_timesort_non_consecutive_ids() {
        $this->resetAfterTest(true);
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        /*
         * The events should be ordered by timesort as follows:
         *
         * 1 event 1
         * 2 event 1
         * 1 event 2
         * 2 event 2
         * 1 event 3
         * 2 event 3
         * 1 event 4
         * 2 event 4
         * 1 event 5
         * 2 event 5
         * 1 event 6
         * 2 event 6
         * 1 event 7
         * 2 event 7
         * 1 event 8
         * 2 event 8
         * 1 event 9
         * 2 event 9
         * 1 event 10
         * 2 event 10
         */
        $records = [];
        for ($i = 1; $i < 11; $i++) {
            $records[] = create_event([
                'name' => sprintf('1 event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => 1
            ]);
        }

        for ($i = 1; $i < 11; $i++) {
            $records[] = create_event([
                'name' => sprintf('2 event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => 1
            ]);
        }

        /*
         * Expected result set:
         *
         * 2 event 4
         * 1 event 5
         * 2 event 5
         * 1 event 6
         * 2 event 6
         * 1 event 7
         * 2 event 7
         * 1 event 8
         * 2 event 8
         */
        $aftereventid = $records[3]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        // Offset results by event with name "1 event 4" which has the same timesort
        // value as the lower boundary of this query (3). Confirm that the given
        // $afterevent is used to ignore events with the same timesortfrom values.
        $events = $vault->get_action_events_by_timesort($user, 3, 8, $afterevent);

        $this->assertCount(9, $events);
        $this->assertEquals('2 event 4', $events[0]->get_name());
        $this->assertEquals('2 event 8', $events[8]->get_name());

        /*
         * Expected result set:
         *
         * 2 event 4
         * 1 event 5
         */
        $events = $vault->get_action_events_by_timesort($user, 3, 8, $afterevent, 2);

        $this->assertCount(2, $events);
        $this->assertEquals('2 event 4', $events[0]->get_name());
        $this->assertEquals('1 event 5', $events[1]->get_name());

        /*
         * Expected result set:
         *
         * 2 event 8
         */
        $aftereventid = $records[7]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        // Offset results by event with name "1 event 8" which has the same timesort
        // value as the upper boundary of this query (8). Confirm that the given
        // $afterevent is used to ignore events with the same timesortto values.
        $events = $vault->get_action_events_by_timesort($user, 3, 8, $afterevent);

        $this->assertCount(1, $events);
        $this->assertEquals('2 event 8', $events[0]->get_name());

        /*
         * Expected empty result set.
         */
        $aftereventid = $records[18]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        // Offset results by event with name "2 event 9" which has a timesort
        // value larger than the upper boundary of this query (9 > 8). Confirm
        // that the given $afterevent is used for filtering events.
        $events = $vault->get_action_events_by_timesort($user, 3, 8, $afterevent);
        $this->assertEmpty($events);
    }

    /**
     * There are subtle cases where the priority of an event override may be identical to another.
     * For example, if you duplicate a group override, but make it apply to a different group. Now
     * there are two overrides with exactly the same overridden dates. In this case the priority of
     * both is 1.
     *
     * In this situation:
     * - A user in group A should see only the A override
     * - A user in group B should see only the B override
     * - A user in both A and B should see both
     */
    public function test_get_action_events_by_timesort_with_identical_group_override_priorities() {
        $this->resetAfterTest();
        $this->setAdminuser();

        $course = $this->getDataGenerator()->create_course();

        // Create an assign instance.
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assigninstance = $assigngenerator->create_instance(['course' => $course->id]);

        // Create users.
        $users = [
            'Only in group A'  => $this->getDataGenerator()->create_user(),
            'Only in group B'  => $this->getDataGenerator()->create_user(),
            'In group A and B' => $this->getDataGenerator()->create_user(),
            'In no groups'     => $this->getDataGenerator()->create_user()
        ];

        // Enrol users.
        foreach ($users as $user) {
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }

        // Create groups.
        $groupa = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $groupb = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        // Add members to groups.
        // Group A.
        $this->getDataGenerator()->create_group_member(['groupid' => $groupa->id, 'userid' => $users['Only in group A']->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $groupa->id, 'userid' => $users['In group A and B']->id]);

        // Group B.
        $this->getDataGenerator()->create_group_member(['groupid' => $groupb->id, 'userid' => $users['Only in group B']->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $groupb->id, 'userid' => $users['In group A and B']->id]);

        // Events with the same module name, instance and event type.
        $events = [
            [
                'name' => 'Assignment 1 due date - Group A override',
                'description' => '',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $groupa->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $assigninstance->id,
                'eventtype' => 'due',
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'timestart' => 1,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 1
            ],
            [
                'name' => 'Assignment 1 due date - Group B override',
                'description' => '',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $groupb->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $assigninstance->id,
                'eventtype' => 'due',
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'timestart' => 1,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 1
            ],
            [
                'name' => 'Assignment 1 due date',
                'description' => '',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $assigninstance->id,
                'eventtype' => 'due',
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'timestart' => 1,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => null,
            ]
        ];

        foreach ($events as $event) {
            calendar_event::create($event, false);
        }

        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $usersevents = array_reduce(array_keys($users), function($carry, $description) use ($users, $vault) {
            // NB: This is currently needed to make get_action_events_by_timesort return the right thing.
            // It needs to be fixed, see MDL-58736.
            $this->setUser($users[$description]);
            return $carry + ['For user ' . lcfirst($description) => $vault->get_action_events_by_timesort($users[$description])];
        }, []);

        foreach ($usersevents as $description => $userevents) {
            if ($description == 'For user in group A and B') {
                // User is in both A and B, so they should see the override for both
                // given that the priority is the same.
                $this->assertCount(2, $userevents);
                continue;
            }

            // Otherwise there should be only one assign event for each user.
            $this->assertCount(1, $userevents);
        }

        // User in only group A should see the group A override.
        $this->assertEquals('Assignment 1 due date - Group A override', $usersevents['For user only in group A'][0]->get_name());

        // User in only group B should see the group B override.
        $this->assertEquals('Assignment 1 due date - Group B override', $usersevents['For user only in group B'][0]->get_name());

        // User in group A and B should see see both overrides since the priorities are the same.
        $this->assertEquals('Assignment 1 due date - Group A override', $usersevents['For user in group A and B'][0]->get_name());
        $this->assertEquals('Assignment 1 due date - Group B override', $usersevents['For user in group A and B'][1]->get_name());

        // User in no groups should see the plain assignment event.
        $this->assertEquals('Assignment 1 due date', $usersevents['For user in no groups'][0]->get_name());
    }

    /**
     * Test that get_action_events_by_course returns events after the
     * provided timesort value.
     */
    public function test_get_action_events_by_course_after_time() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $this->resetAfterTest(true);
        $this->setAdminuser();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        for ($i = 6; $i < 12; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course2->id,
            ]);
        }

        $events = $vault->get_action_events_by_course($user, $course1, 3);
        $this->assertCount(3, $events);
        $this->assertEquals('Event 3', $events[0]->get_name());
        $this->assertEquals('Event 4', $events[1]->get_name());
        $this->assertEquals('Event 5', $events[2]->get_name());

        $events = $vault->get_action_events_by_course($user, $course1, 3, null, null, 1);

        $this->assertCount(1, $events);
        $this->assertEquals('Event 3', $events[0]->get_name());

        $events = $vault->get_action_events_by_course($user, $course1, 6);

        $this->assertCount(0, $events);
    }

    /**
     * Test that get_action_events_by_course returns events before the
     * provided timesort value.
     */
    public function test_get_action_events_by_course_before_time() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $this->resetAfterTest(true);
        $this->setAdminuser();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        for ($i = 6; $i < 12; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course2->id,
            ]);
        }

        $events = $vault->get_action_events_by_course($user, $course1, null, 3);

        $this->assertCount(3, $events);
        $this->assertEquals('Event 1', $events[0]->get_name());
        $this->assertEquals('Event 2', $events[1]->get_name());
        $this->assertEquals('Event 3', $events[2]->get_name());

        $events = $vault->get_action_events_by_course($user, $course1, null, 3, null, 1);

        $this->assertCount(1, $events);
        $this->assertEquals('Event 1', $events[0]->get_name());

        $events = $vault->get_action_events_by_course($user, $course1, 6);

        $this->assertCount(0, $events);
    }

    /**
     * Test that get_action_events_by_course returns events between the
     * provided timesort values.
     */
    public function test_get_action_events_by_course_between_time() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $this->resetAfterTest(true);
        $this->setAdminuser();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        for ($i = 6; $i < 12; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course2->id,
            ]);
        }

        $events = $vault->get_action_events_by_course($user, $course1, 2, 4);

        $this->assertCount(3, $events);
        $this->assertEquals('Event 2', $events[0]->get_name());
        $this->assertEquals('Event 3', $events[1]->get_name());
        $this->assertEquals('Event 4', $events[2]->get_name());

        $events = $vault->get_action_events_by_course($user, $course1, 2, 4, null, 1);

        $this->assertCount(1, $events);
        $this->assertEquals('Event 2', $events[0]->get_name());
    }

    /**
     * Test that get_action_events_by_course returns events between the
     * provided timesort values and after the last seen event when one is
     * provided.
     */
    public function test_get_action_events_by_course_between_time_after_event() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);
        $records = [];

        $this->resetAfterTest(true);
        $this->setAdminuser();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        for ($i = 1; $i < 21; $i++) {
            $records[] = create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        for ($i = 21; $i < 41; $i++) {
            $records[] = create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course2->id,
            ]);
        }

        $aftereventid = $records[6]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        $events = $vault->get_action_events_by_course($user, $course1, 3, 15, $afterevent);

        $this->assertCount(8, $events);
        $this->assertEquals('Event 8', $events[0]->get_name());

        $events = $vault->get_action_events_by_course($user, $course1, 3, 15, $afterevent, 3);

        $this->assertCount(3, $events);
    }

    /**
     * Test that get_action_events_by_course returns events between the
     * provided timesort values and the last seen event can be provided to
     * get paginated results.
     */
    public function test_get_action_events_by_course_between_time_skip_even_records() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        // The factory will return every event that is divisible by 2.
        $factory = new action_event_test_factory(function($actionevent) {
            static $count = 0;
            $count++;
            return ($count % 2) ? true : false;
        });
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $this->resetAfterTest(true);
        $this->setAdminuser();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        for ($i = 1; $i < 41; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        for ($i = 41; $i < 81; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course2->id,
            ]);
        }

        $events = $vault->get_action_events_by_course($user, $course1, 3, 35, null, 5);

        $this->assertCount(5, $events);
        $this->assertEquals('Event 3', $events[0]->get_name());
        $this->assertEquals('Event 5', $events[1]->get_name());
        $this->assertEquals('Event 7', $events[2]->get_name());
        $this->assertEquals('Event 9', $events[3]->get_name());
        $this->assertEquals('Event 11', $events[4]->get_name());

        $afterevent = $events[4];
        $events = $vault->get_action_events_by_course($user, $course1, 3, 35, $afterevent, 5);

        $this->assertCount(5, $events);
        $this->assertEquals('Event 13', $events[0]->get_name());
        $this->assertEquals('Event 15', $events[1]->get_name());
        $this->assertEquals('Event 17', $events[2]->get_name());
        $this->assertEquals('Event 19', $events[3]->get_name());
        $this->assertEquals('Event 21', $events[4]->get_name());
    }

    /**
     * Test that get_action_events_by_course returns events between the
     * provided timesort values. The database will continue to be read until the
     * number of events requested has been satisfied. In this case the first
     * five events are rejected so it should require two database requests.
     */
    public function test_get_action_events_by_course_between_time_skip_first_records() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $limit = 5;
        $seen = 0;
        // The factory will skip the first $limit events.
        $factory = new action_event_test_factory(function($actionevent) use (&$seen, $limit) {
            if ($seen < $limit) {
                $seen++;
                return false;
            } else {
                return true;
            }
        });
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $this->resetAfterTest(true);
        $this->setAdminuser();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        for ($i = 1; $i < 21; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        for ($i = 21; $i < 41; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course2->id,
            ]);
        }

        $events = $vault->get_action_events_by_course($user, $course1, 1, 20, null, $limit);

        $this->assertCount($limit, $events);
        $this->assertEquals(sprintf('Event %d', $limit + 1), $events[0]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 2), $events[1]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 3), $events[2]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 4), $events[3]->get_name());
        $this->assertEquals(sprintf('Event %d', $limit + 5), $events[4]->get_name());
    }

    /**
     * Test that get_action_events_by_course returns events between the
     * provided timesort values and after the last seen event when one is
     * provided. This should work even when the event ids aren't ordered the
     * same as the timesort order.
     */
    public function test_get_action_events_by_course_non_consecutive_ids() {
        $this->resetAfterTest(true);
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $this->setAdminuser();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        /*
         * The events should be ordered by timesort as follows:
         *
         * 1 event 1
         * 2 event 1
         * 1 event 2
         * 2 event 2
         * 1 event 3
         * 2 event 3
         * 1 event 4
         * 2 event 4
         * 1 event 5
         * 2 event 5
         * 1 event 6
         * 2 event 6
         * 1 event 7
         * 2 event 7
         * 1 event 8
         * 2 event 8
         * 1 event 9
         * 2 event 9
         * 1 event 10
         * 2 event 10
         */
        $records = [];
        for ($i = 1; $i < 11; $i++) {
            $records[] = create_event([
                'name' => sprintf('1 event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        for ($i = 1; $i < 11; $i++) {
            $records[] = create_event([
                'name' => sprintf('2 event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course1->id,
            ]);
        }

        // Create events for the other course.
        for ($i = 1; $i < 11; $i++) {
            $records[] = create_event([
                'name' => sprintf('3 event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'courseid' => $course2->id,
            ]);
        }

        /*
         * Expected result set:
         *
         * 2 event 4
         * 1 event 5
         * 2 event 5
         * 1 event 6
         * 2 event 6
         * 1 event 7
         * 2 event 7
         * 1 event 8
         * 2 event 8
         */
        $aftereventid = $records[3]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        // Offset results by event with name "1 event 4" which has the same timesort
        // value as the lower boundary of this query (3). Confirm that the given
        // $afterevent is used to ignore events with the same timesortfrom values.
        $events = $vault->get_action_events_by_course($user, $course1, 3, 8, $afterevent);

        $this->assertCount(9, $events);
        $this->assertEquals('2 event 4', $events[0]->get_name());
        $this->assertEquals('2 event 8', $events[8]->get_name());

        /*
         * Expected result set:
         *
         * 2 event 4
         * 1 event 5
         */
        $events = $vault->get_action_events_by_course($user, $course1, 3, 8, $afterevent, 2);

        $this->assertCount(2, $events);
        $this->assertEquals('2 event 4', $events[0]->get_name());
        $this->assertEquals('1 event 5', $events[1]->get_name());

        /*
         * Expected result set:
         *
         * 2 event 8
         */
        $aftereventid = $records[7]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        // Offset results by event with name "1 event 8" which has the same timesort
        // value as the upper boundary of this query (8). Confirm that the given
        // $afterevent is used to ignore events with the same timesortto values.
        $events = $vault->get_action_events_by_course($user, $course1, 3, 8, $afterevent);

        $this->assertCount(1, $events);
        $this->assertEquals('2 event 8', $events[0]->get_name());

        /*
         * Expected empty result set.
         */
        $aftereventid = $records[18]->id;
        $afterevent = $vault->get_event_by_id($aftereventid);
        // Offset results by event with name "2 event 9" which has a timesort
        // value larger than the upper boundary of this query (9 > 8). Confirm
        // that the given $afterevent is used for filtering events.
        $events = $vault->get_action_events_by_course($user, $course1, 3, 8, $afterevent);

        $this->assertEmpty($events);
    }

    /**
     * There are subtle cases where the priority of an event override may be identical to another.
     * For example, if you duplicate a group override, but make it apply to a different group. Now
     * there are two overrides with exactly the same overridden dates. In this case the priority of
     * both is 1.
     *
     * In this situation:
     * - A user in group A should see only the A override
     * - A user in group B should see only the B override
     * - A user in both A and B should see both
     */
    public function test_get_action_events_by_course_with_identical_group_override_priorities() {
        $this->resetAfterTest();
        $this->setAdminuser();

        $course = $this->getDataGenerator()->create_course();

        // Create an assign instance.
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assigninstance = $assigngenerator->create_instance(['course' => $course->id]);

        // Create users.
        $users = [
            'Only in group A'  => $this->getDataGenerator()->create_user(),
            'Only in group B'  => $this->getDataGenerator()->create_user(),
            'In group A and B' => $this->getDataGenerator()->create_user(),
            'In no groups'     => $this->getDataGenerator()->create_user()
        ];

        // Enrol users.
        foreach ($users as $user) {
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }

        // Create groups.
        $groupa = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $groupb = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        // Add members to groups.
        // Group A.
        $this->getDataGenerator()->create_group_member(['groupid' => $groupa->id, 'userid' => $users['Only in group A']->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $groupa->id, 'userid' => $users['In group A and B']->id]);

        // Group B.
        $this->getDataGenerator()->create_group_member(['groupid' => $groupb->id, 'userid' => $users['Only in group B']->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $groupb->id, 'userid' => $users['In group A and B']->id]);

        // Events with the same module name, instance and event type.
        $events = [
            [
                'name' => 'Assignment 1 due date - Group A override',
                'description' => '',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $groupa->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $assigninstance->id,
                'eventtype' => 'due',
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'timestart' => 1,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 1
            ],
            [
                'name' => 'Assignment 1 due date - Group B override',
                'description' => '',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $groupb->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $assigninstance->id,
                'eventtype' => 'due',
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'timestart' => 1,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 1
            ],
            [
                'name' => 'Assignment 1 due date',
                'description' => '',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $assigninstance->id,
                'eventtype' => 'due',
                'type' => CALENDAR_EVENT_TYPE_ACTION,
                'timestart' => 1,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => null,
            ]
        ];

        foreach ($events as $event) {
            calendar_event::create($event, false);
        }

        $factory = new action_event_test_factory();
        $strategy = new raw_event_retrieval_strategy();
        $vault = new event_vault($factory, $strategy);

        $usersevents = array_reduce(array_keys($users), function($carry, $description) use ($users, $course, $vault) {
            // NB: This is currently needed to make get_action_events_by_timesort return the right thing.
            // It needs to be fixed, see MDL-58736.
            $this->setUser($users[$description]);
            return $carry + [
                'For user ' . lcfirst($description) => $vault->get_action_events_by_course($users[$description], $course)
            ];
        }, []);

        foreach ($usersevents as $description => $userevents) {
            if ($description == 'For user in group A and B') {
                // User is in both A and B, so they should see the override for both
                // given that the priority is the same.
                $this->assertCount(2, $userevents);
                continue;
            }

            // Otherwise there should be only one assign event for each user.
            $this->assertCount(1, $userevents);
        }

        // User in only group A should see the group A override.
        $this->assertEquals('Assignment 1 due date - Group A override', $usersevents['For user only in group A'][0]->get_name());

        // User in only group B should see the group B override.
        $this->assertEquals('Assignment 1 due date - Group B override', $usersevents['For user only in group B'][0]->get_name());

        // User in group A and B should see see both overrides since the priorities are the same.
        $this->assertEquals('Assignment 1 due date - Group A override', $usersevents['For user in group A and B'][0]->get_name());
        $this->assertEquals('Assignment 1 due date - Group B override', $usersevents['For user in group A and B'][1]->get_name());

        // User in no groups should see the plain assignment event.
        $this->assertEquals('Assignment 1 due date', $usersevents['For user in no groups'][0]->get_name());
    }
}
