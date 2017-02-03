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
        $this->setAdminuser();

        $user = $this->getDataGenerator()->create_user();
        $factory = new action_event_test_factory();
        $vault = new event_vault($factory);

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
        $vault = new event_vault($factory);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION
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
        $vault = new event_vault($factory);

        for ($i = 1; $i < 6; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION
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
        $vault = new event_vault($factory);

        $records = [];
        for ($i = 1; $i < 21; $i++) {
            $records[] = create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION
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
        // The factory will skip events with even ids.
        $factory = new action_event_test_factory(function($actionevent) {
            return ($actionevent->get_id() % 2) ? false : true;
        });
        $vault = new event_vault($factory);

        for ($i = 1; $i < 41; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION
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
        $vault = new event_vault($factory);

        for ($i = 1; $i < 21; $i++) {
            create_event([
                'name' => sprintf('Event %d', $i),
                'eventtype' => 'user',
                'userid' => $user->id,
                'timesort' => $i,
                'type' => CALENDAR_EVENT_TYPE_ACTION
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
}
