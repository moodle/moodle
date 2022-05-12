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

namespace core_calendar;

use core_calendar\local\event\container;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/helpers.php');

/**
 * Class contaning unit tests for the calendar local API.
 *
 * @package    core_calendar
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_api_test extends \advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Create a feedback activity instance and a calendar event for
     * that instance.
     *
     * @param array $feedbackproperties Properties to set on the feedback activity
     * @param array $eventproperties Properties to set on the calendar event
     * @return array The feedback activity and the calendar event
     */
    protected function create_feedback_activity_and_event(array $feedbackproperties = [], array $eventproperties = []) {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $mapper = container::get_event_mapper();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $feedback = $feedbackgenerator->create_instance(array_merge(
            ['course' => $course->id],
            $feedbackproperties
        ));

        $event = create_event(array_merge(
            [
                'courseid' => $course->id,
                'modulename' => 'feedback',
                'instance' => $feedback->id
            ],
             $eventproperties
        ));
        $event = $mapper->from_legacy_event_to_event($event);

        return [$feedback, $event];
    }

    /**
     * Requesting calendar events from a given time should return all events with a sort
     * time at or after the requested time. All events prior to that time should not
     * be return.
     *
     * If there are no events on or after the given time then an empty result set should
     * be returned.
     */
    public function test_get_calendar_action_events_by_timesort_after_time() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
            'modulename' => 'assign',
            'instance' => $moduleinstance->id,
            'userid' => $user->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));

        $this->setUser($user);
        $result = \core_calendar\local\api::get_action_events_by_timesort(5);

        $this->assertCount(4, $result);
        $this->assertEquals('Event 5', $result[0]->get_name());
        $this->assertEquals('Event 6', $result[1]->get_name());
        $this->assertEquals('Event 7', $result[2]->get_name());
        $this->assertEquals('Event 8', $result[3]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_timesort(9);

        $this->assertEmpty($result);

        $this->setAdminUser();
        $result = \core_calendar\local\api::get_action_events_by_timesort(5, null, null, 20, false, $user);
        $this->assertCount(4, $result);
    }

    /**
     * Requesting calendar events before a given time should return all events with a sort
     * time at or before the requested time (inclusive). All events after that time
     * should not be returned.
     *
     * If there are no events before the given time then an empty result set should be
     * returned.
     */
    public function test_get_calendar_action_events_by_timesort_before_time() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
            'modulename' => 'assign',
            'instance' => $moduleinstance->id,
            'userid' => 1,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 2]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 3]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 4]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 5]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 6]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 7]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 8]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 9]));

        $this->setUser($user);
        $result = \core_calendar\local\api::get_action_events_by_timesort(null, 5);

        $this->assertCount(4, $result);
        $this->assertEquals('Event 1', $result[0]->get_name());
        $this->assertEquals('Event 2', $result[1]->get_name());
        $this->assertEquals('Event 3', $result[2]->get_name());
        $this->assertEquals('Event 4', $result[3]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_timesort(null, 1);

        $this->assertEmpty($result);
    }

    /**
     * Requesting calendar events within a given time range should return all events with
     * a sort time between the lower and upper time bound (inclusive).
     *
     * If there are no events in the given time range then an empty result set should be
     * returned.
     */
    public function test_get_calendar_action_events_by_timesort_time_range() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
            'modulename' => 'assign',
            'instance' => $moduleinstance->id,
            'userid' => 1,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));

        $this->setUser($user);
        $result = \core_calendar\local\api::get_action_events_by_timesort(3, 6);

        $this->assertCount(4, $result);
        $this->assertEquals('Event 3', $result[0]->get_name());
        $this->assertEquals('Event 4', $result[1]->get_name());
        $this->assertEquals('Event 5', $result[2]->get_name());
        $this->assertEquals('Event 6', $result[3]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_timesort(10, 15);

        $this->assertEmpty($result);
    }

    /**
     * Requesting calendar events within a given time range and a limit and offset should return
     * the number of events up to the given limit value that have a sort time between the lower
     * and uppper time bound (inclusive) where the result set is shifted by the offset value.
     *
     * If there are no events in the given time range then an empty result set should be
     * returned.
     */
    public function test_get_calendar_action_events_by_timesort_time_limit_offset() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
            'modulename' => 'assign',
            'instance' => $moduleinstance->id,
            'userid' => 1,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));

        $this->setUser($user);
        $result = \core_calendar\local\api::get_action_events_by_timesort(2, 7, $event3->id, 2);

        $this->assertCount(2, $result);
        $this->assertEquals('Event 4', $result[0]->get_name());
        $this->assertEquals('Event 5', $result[1]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_timesort(2, 7, $event5->id, 2);

        $this->assertCount(2, $result);
        $this->assertEquals('Event 6', $result[0]->get_name());
        $this->assertEquals('Event 7', $result[1]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_timesort(2, 7, $event7->id, 2);

        $this->assertEmpty($result);
    }

    /**
     * Test get_calendar_action_events_by_timesort with search feature.
     */
    public function test_get_calendar_action_events_by_timesort_with_search() {
        // Generate data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
            'modulename' => 'assign',
            'instance' => $moduleinstance->id,
            'userid' => 1,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));

        $this->setUser($user);

        // No result found for fake search.
        $result = \core_calendar\local\api::get_action_events_by_timesort(0, null, null, 6, false, null, 'Fake search');
        $this->assertEmpty($result);

        // Search for event name called 'Event 1'.
        $result = \core_calendar\local\api::get_action_events_by_timesort(0, 8, null, 20, false, null, 'Event 1');
        $this->assertCount(1, $result);
        $this->assertEquals('Event 1', $result[0]->get_name());

        // Search for activity type called 'assign'.
        $result = \core_calendar\local\api::get_action_events_by_timesort(0, 8, null, 20, false, null, 'assign');
        $this->assertCount(8, $result);
        $this->assertEquals('Event 1', $result[0]->get_name());
        $this->assertEquals('Event 2', $result[1]->get_name());
        $this->assertEquals('Event 3', $result[2]->get_name());
        $this->assertEquals('Event 4', $result[3]->get_name());
        $this->assertEquals('Event 5', $result[4]->get_name());
        $this->assertEquals('Event 6', $result[5]->get_name());
        $this->assertEquals('Event 7', $result[6]->get_name());
        $this->assertEquals('Event 8', $result[7]->get_name());
    }

    /**
     * Requesting calendar events from a given course and time should return all
     * events with a sort time at or after the requested time. All events prior
     * to that time should not be return.
     *
     * If there are no events on or after the given time then an empty result set should
     * be returned.
     */
    public function test_get_calendar_action_events_by_course_after_time() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance1 = $generator->create_instance(['course' => $course1->id]);
        $moduleinstance2 = $generator->create_instance(['course' => $course2->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);
        $this->resetAfterTest(true);
        $this->setUser($user);

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'modulename' => 'assign',
            'instance' => $moduleinstance1->id,
            'userid' => $user->id,
            'courseid' => $course1->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));

        $params['courseid'] = $course2->id;
        $params['instance'] = $moduleinstance2->id;
        $event9 = create_event(array_merge($params, ['name' => 'Event 9', 'timesort' => 1]));
        $event10 = create_event(array_merge($params, ['name' => 'Event 10', 'timesort' => 2]));
        $event11 = create_event(array_merge($params, ['name' => 'Event 11', 'timesort' => 3]));
        $event12 = create_event(array_merge($params, ['name' => 'Event 12', 'timesort' => 4]));
        $event13 = create_event(array_merge($params, ['name' => 'Event 13', 'timesort' => 5]));
        $event14 = create_event(array_merge($params, ['name' => 'Event 14', 'timesort' => 6]));
        $event15 = create_event(array_merge($params, ['name' => 'Event 15', 'timesort' => 7]));
        $event16 = create_event(array_merge($params, ['name' => 'Event 16', 'timesort' => 8]));

        $result = \core_calendar\local\api::get_action_events_by_course($course1, 5);

        $this->assertCount(4, $result);
        $this->assertEquals('Event 5', $result[0]->get_name());
        $this->assertEquals('Event 6', $result[1]->get_name());
        $this->assertEquals('Event 7', $result[2]->get_name());
        $this->assertEquals('Event 8', $result[3]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_course($course1, 9);

        $this->assertEmpty($result);
    }

    /**
     * Requesting calendar events for a course and before a given time should return
     * all events with a sort time at or before the requested time (inclusive). All
     * events after that time should not be returned.
     *
     * If there are no events before the given time then an empty result set should be
     * returned.
     */
    public function test_get_calendar_action_events_by_course_before_time() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance1 = $generator->create_instance(['course' => $course1->id]);
        $moduleinstance2 = $generator->create_instance(['course' => $course2->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);
        $this->resetAfterTest(true);
        $this->setUser($user);

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'modulename' => 'assign',
            'instance' => $moduleinstance1->id,
            'userid' => $user->id,
            'courseid' => $course1->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 2]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 3]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 4]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 5]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 6]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 7]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 8]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 9]));

        $params['courseid'] = $course2->id;
        $params['instance'] = $moduleinstance2->id;
        $event9 = create_event(array_merge($params, ['name' => 'Event 9', 'timesort' => 2]));
        $event10 = create_event(array_merge($params, ['name' => 'Event 10', 'timesort' => 3]));
        $event11 = create_event(array_merge($params, ['name' => 'Event 11', 'timesort' => 4]));
        $event12 = create_event(array_merge($params, ['name' => 'Event 12', 'timesort' => 5]));
        $event13 = create_event(array_merge($params, ['name' => 'Event 13', 'timesort' => 6]));
        $event14 = create_event(array_merge($params, ['name' => 'Event 14', 'timesort' => 7]));
        $event15 = create_event(array_merge($params, ['name' => 'Event 15', 'timesort' => 8]));
        $event16 = create_event(array_merge($params, ['name' => 'Event 16', 'timesort' => 9]));

        $result = \core_calendar\local\api::get_action_events_by_course($course1, null, 5);

        $this->assertCount(4, $result);
        $this->assertEquals('Event 1', $result[0]->get_name());
        $this->assertEquals('Event 2', $result[1]->get_name());
        $this->assertEquals('Event 3', $result[2]->get_name());
        $this->assertEquals('Event 4', $result[3]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_course($course1, null, 1);

        $this->assertEmpty($result);
    }

    /**
     * Requesting calendar events for a course and within a given time range should
     * return all events with a sort time between the lower and upper time bound
     * (inclusive).
     *
     * If there are no events in the given time range then an empty result set should be
     * returned.
     */
    public function test_get_calendar_action_events_by_course_time_range() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance1 = $generator->create_instance(['course' => $course1->id]);
        $moduleinstance2 = $generator->create_instance(['course' => $course2->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);
        $this->resetAfterTest(true);
        $this->setUser($user);

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'modulename' => 'assign',
            'instance' => $moduleinstance1->id,
            'userid' => $user->id,
            'courseid' => $course1->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));

        $params['courseid'] = $course2->id;
        $params['instance'] = $moduleinstance2->id;
        $event9 = create_event(array_merge($params, ['name' => 'Event 9', 'timesort' => 1]));
        $event10 = create_event(array_merge($params, ['name' => 'Event 10', 'timesort' => 2]));
        $event11 = create_event(array_merge($params, ['name' => 'Event 11', 'timesort' => 3]));
        $event12 = create_event(array_merge($params, ['name' => 'Event 12', 'timesort' => 4]));
        $event13 = create_event(array_merge($params, ['name' => 'Event 13', 'timesort' => 5]));
        $event14 = create_event(array_merge($params, ['name' => 'Event 14', 'timesort' => 6]));
        $event15 = create_event(array_merge($params, ['name' => 'Event 15', 'timesort' => 7]));
        $event16 = create_event(array_merge($params, ['name' => 'Event 16', 'timesort' => 8]));

        $result = \core_calendar\local\api::get_action_events_by_course($course1, 3, 6);

        $this->assertCount(4, $result);
        $this->assertEquals('Event 3', $result[0]->get_name());
        $this->assertEquals('Event 4', $result[1]->get_name());
        $this->assertEquals('Event 5', $result[2]->get_name());
        $this->assertEquals('Event 6', $result[3]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_course($course1, 10, 15);

        $this->assertEmpty($result);
    }

    /**
     * Requesting calendar events for a course and within a given time range and a limit
     * and offset should return the number of events up to the given limit value that have
     * a sort time between the lower and uppper time bound (inclusive) where the result
     * set is shifted by the offset value.
     *
     * If there are no events in the given time range then an empty result set should be
     * returned.
     */
    public function test_get_calendar_action_events_by_course_time_limit_offset() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance1 = $generator->create_instance(['course' => $course1->id]);
        $moduleinstance2 = $generator->create_instance(['course' => $course2->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);
        $this->resetAfterTest(true);
        $this->setUser($user);

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'modulename' => 'assign',
            'instance' => $moduleinstance1->id,
            'userid' => $user->id,
            'courseid' => $course1->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));

        $params['courseid'] = $course2->id;
        $params['instance'] = $moduleinstance2->id;
        $event9 = create_event(array_merge($params, ['name' => 'Event 9', 'timesort' => 1]));
        $event10 = create_event(array_merge($params, ['name' => 'Event 10', 'timesort' => 2]));
        $event11 = create_event(array_merge($params, ['name' => 'Event 11', 'timesort' => 3]));
        $event12 = create_event(array_merge($params, ['name' => 'Event 12', 'timesort' => 4]));
        $event13 = create_event(array_merge($params, ['name' => 'Event 13', 'timesort' => 5]));
        $event14 = create_event(array_merge($params, ['name' => 'Event 14', 'timesort' => 6]));
        $event15 = create_event(array_merge($params, ['name' => 'Event 15', 'timesort' => 7]));
        $event16 = create_event(array_merge($params, ['name' => 'Event 16', 'timesort' => 8]));

        $result = \core_calendar\local\api::get_action_events_by_course($course1, 2, 7, $event3->id, 2);

        $this->assertCount(2, $result);
        $this->assertEquals('Event 4', $result[0]->get_name());
        $this->assertEquals('Event 5', $result[1]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_course($course1, 2, 7, $event5->id, 2);

        $this->assertCount(2, $result);
        $this->assertEquals('Event 6', $result[0]->get_name());
        $this->assertEquals('Event 7', $result[1]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_course($course1, 2, 7, $event7->id, 2);

        $this->assertEmpty($result);
    }

    /**
     * Test that get_action_events_by_courses will return a list of events for each
     * course you provided as long as the user is enrolled in the course.
     */
    public function test_get_action_events_by_courses() {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance1 = $generator->create_instance(['course' => $course1->id]);
        $moduleinstance2 = $generator->create_instance(['course' => $course2->id]);
        $moduleinstance3 = $generator->create_instance(['course' => $course3->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user->id, $course3->id);
        $this->resetAfterTest(true);
        $this->setUser($user);

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'modulename' => 'assign',
            'instance' => $moduleinstance1->id,
            'userid' => $user->id,
            'courseid' => $course1->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $event1 = create_event(array_merge($params, ['name' => 'Event 1', 'timesort' => 1]));
        $event2 = create_event(array_merge($params, ['name' => 'Event 2', 'timesort' => 2]));

        $params['courseid'] = $course2->id;
        $params['instance'] = $moduleinstance2->id;
        $event3 = create_event(array_merge($params, ['name' => 'Event 3', 'timesort' => 3]));
        $event4 = create_event(array_merge($params, ['name' => 'Event 4', 'timesort' => 4]));
        $event5 = create_event(array_merge($params, ['name' => 'Event 5', 'timesort' => 5]));

        $params['courseid'] = $course3->id;
        $params['instance'] = $moduleinstance3->id;
        $event6 = create_event(array_merge($params, ['name' => 'Event 6', 'timesort' => 6]));
        $event7 = create_event(array_merge($params, ['name' => 'Event 7', 'timesort' => 7]));
        $event8 = create_event(array_merge($params, ['name' => 'Event 8', 'timesort' => 8]));
        $event9 = create_event(array_merge($params, ['name' => 'Event 9', 'timesort' => 9]));

        $result = \core_calendar\local\api::get_action_events_by_courses([], 1);

        $this->assertEmpty($result);

        $result = \core_calendar\local\api::get_action_events_by_courses([$course1], 3);

        $this->assertEmpty($result[$course1->id]);

        $result = \core_calendar\local\api::get_action_events_by_courses([$course1], 1);

        $this->assertCount(2, $result[$course1->id]);
        $this->assertEquals('Event 1', $result[$course1->id][0]->get_name());
        $this->assertEquals('Event 2', $result[$course1->id][1]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_courses([$course1, $course2], 1);

        $this->assertCount(2, $result[$course1->id]);
        $this->assertEquals('Event 1', $result[$course1->id][0]->get_name());
        $this->assertEquals('Event 2', $result[$course1->id][1]->get_name());
        $this->assertCount(3, $result[$course2->id]);
        $this->assertEquals('Event 3', $result[$course2->id][0]->get_name());
        $this->assertEquals('Event 4', $result[$course2->id][1]->get_name());
        $this->assertEquals('Event 5', $result[$course2->id][2]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_courses([$course1, $course2], 2, 4);

        $this->assertCount(1, $result[$course1->id]);
        $this->assertEquals('Event 2', $result[$course1->id][0]->get_name());
        $this->assertCount(2, $result[$course2->id]);
        $this->assertEquals('Event 3', $result[$course2->id][0]->get_name());
        $this->assertEquals('Event 4', $result[$course2->id][1]->get_name());

        $result = \core_calendar\local\api::get_action_events_by_courses([$course1, $course2, $course3], 1, null, 1);

        $this->assertCount(1, $result[$course1->id]);
        $this->assertEquals('Event 1', $result[$course1->id][0]->get_name());
        $this->assertCount(1, $result[$course2->id]);
        $this->assertEquals('Event 3', $result[$course2->id][0]->get_name());
        $this->assertCount(1, $result[$course3->id]);
        $this->assertEquals('Event 6', $result[$course3->id][0]->get_name());
    }

    /**
     * Test that the get_legacy_events() function only returns activity events that are enabled.
     */
    public function test_get_legacy_events_with_disabled_module() {
        global $DB;

        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assigninstance = $assigngenerator->create_instance(['course' => $course->id]);

        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $lessoninstance = $lessongenerator->create_instance(['course' => $course->id]);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
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
                'instance' => $assigninstance->id,
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
                'instance' => $lessoninstance->id,
                'eventtype' => 'end',
                'timestart' => time(),
                'timeduration' => 86400,
                'visible' => 1
            ]
        ];
        foreach ($events as $event) {
            \calendar_event::create($event, false);
        }
        $timestart = time() - 60;
        $timeend = time() + 60;

        // Get all events.
        $events = calendar_get_legacy_events($timestart, $timeend, true, 0, true);
        $this->assertCount(2, $events);

        // Disable the lesson module.
        $modulerecord = $DB->get_record('modules', ['name' => 'lesson']);
        $modulerecord->visible = 0;
        $DB->update_record('modules', $modulerecord);

        // Check that we only return the assign event.
        $events = calendar_get_legacy_events($timestart, $timeend, true, 0, true);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('assign', $event->modulename);
    }

    /**
     * Test for \core_calendar\local\api::get_legacy_events() when there are user and group overrides.
     */
    public function test_get_legacy_events_with_overrides() {
        $generator = $this->getDataGenerator();

        $course = $generator->create_course();

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        if (!isset($params['course'])) {
            $params['course'] = $course->id;
        }

        $instance = $plugingenerator->create_instance($params);

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
        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);

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
            \calendar_event::create($event, false);
        }

        $timestart = $now - 100;
        $timeend = $now + (3 * 86400);
        $groups = [$group1->id, $group2->id];

        // Get user override events.
        $this->setUser($useroverridestudent);
        $events = calendar_get_legacy_events($timestart, $timeend, $useroverridestudent->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - User override', $event->name);

        // Get event for user with override but with the timestart and timeend parameters only covering the original event.
        $events = calendar_get_legacy_events($timestart, $now, $useroverridestudent->id, $groups, $course->id);
        $this->assertCount(0, $events);

        // Get events for user that does not belong to any group and has no user override events.
        $this->setUser($nogroupstudent);
        $events = calendar_get_legacy_events($timestart, $timeend, $nogroupstudent->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date', $event->name);

        // Get events for user that belongs to groups A and B and has no user override events.
        $this->setUser($group12student);
        $events = calendar_get_legacy_events($timestart, $timeend, $group12student->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - Group A override', $event->name);

        // Get events for user that belongs to group A and has no user override events.
        $this->setUser($group1student);
        $events = calendar_get_legacy_events($timestart, $timeend, $group1student->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - Group A override', $event->name);

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
                'repeatid' => $event->id,
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
                'repeatid' => $event->id,
                'modulename' => '0',
                'instance' => 0,
                'eventtype' => 'site',
                'timestart' => $now + (2 * 86400),
                'timeduration' => 0,
                'visible' => 1,
            ],
        ];

        foreach ($repeatingevents as $event) {
            \calendar_event::create($event, false);
        }

        // Make sure repeating events are not filtered out.
        $events = calendar_get_legacy_events($timestart, $timeend, true, true, true);
        $this->assertCount(3, $events);
    }

    /**
     * Setting the start date on the calendar event should update the date
     * of the event but should leave the time of day unchanged.
     */
    public function test_update_event_start_day_updates_date() {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $roleid = $generator->create_role();
        $context = \context_system::instance();
        $originalstarttime = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2018-02-2T10:00:00+08:00');
        $expected = new \DateTimeImmutable('2018-02-2T15:00:00+08:00');
        $mapper = container::get_event_mapper();

        $generator->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:manageownentries', CAP_ALLOW, $roleid, $context, true);

        $this->setUser($user);
        $this->resetAfterTest(true);

        $event = create_event([
            'name' => 'Test event',
            'userid' => $user->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => $originalstarttime->getTimestamp(),
        ]);
        $event = $mapper->from_legacy_event_to_event($event);

        $newEvent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
        $actual = $newEvent->get_times()->get_start_time();

        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
    }

    /**
     * A user should not be able to update the start date of the event
     * that they don't have the capabilities to modify.
     */
    public function test_update_event_start_day_no_permission() {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $roleid = $generator->create_role();
        $context = \context_system::instance();
        $originalstarttime = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2018-02-2T10:00:00+08:00');
        $expected = new \DateTimeImmutable('2018-02-2T15:00:00+08:00');
        $mapper = container::get_event_mapper();

        $generator->role_assign($roleid, $user->id, $context->id);

        $this->setUser($user);
        $this->resetAfterTest(true);

        $event = create_event([
            'name' => 'Test event',
            'userid' => $user->id,
            'eventtype' => 'user',
            'repeats' => 0,
            'timestart' => $originalstarttime->getTimestamp(),
        ]);
        $event = $mapper->from_legacy_event_to_event($event);

        assign_capability('moodle/calendar:manageownentries', CAP_PROHIBIT, $roleid, $context, true);
        $this->expectException('moodle_exception');
        $newEvent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
    }

    /**
     * Updating the start day of an event with no maximum cutoff should
     * update the corresponding activity property.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_no_max() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeopen = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2018-02-2T10:00:00+08:00');
        $expected = new \DateTimeImmutable('2018-02-2T15:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => $timeopen->getTimestamp(),
                'timeclose' => 0
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
                'timestart' => $timeopen->getTimestamp()
            ]
        );
        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
        $actual = $newevent->get_times()->get_start_time();
        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);

        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
        $this->assertEquals($expected->getTimestamp(), $feedback->timeopen);
    }

    /**
     * Updating the start day of an event belonging to an activity to a value
     * less than the maximum cutoff should update the corresponding activity
     * property.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_less_than_max() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeopen = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $timeclose = new \DateTimeImmutable('2019-01-1T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2018-02-2T10:00:00+08:00');
        $expected = new \DateTimeImmutable('2018-02-2T15:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => $timeopen->getTimestamp(),
                'timeclose' => $timeclose->getTimestamp()
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
                'timestart' => $timeopen->getTimestamp()
            ]
        );

        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
        $actual = $newevent->get_times()->get_start_time();
        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);

        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
        $this->assertEquals($expected->getTimestamp(), $feedback->timeopen);
    }

    /**
     * Updating the start day of an event belonging to an activity to a value
     * equal to the maximum cutoff should update the corresponding activity
     * property.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_equal_to_max() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeopen = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $timeclose = new \DateTimeImmutable('2018-02-2T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2018-02-2T10:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => $timeopen->getTimestamp(),
                'timeclose' => $timeclose->getTimestamp(),
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
                'timestart' => $timeopen->getTimestamp()
            ]
        );

        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
        $actual = $newevent->get_times()->get_start_time();
        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);

        $this->assertEquals($timeclose->getTimestamp(), $actual->getTimestamp());
        $this->assertEquals($timeclose->getTimestamp(), $feedback->timeopen);
    }

    /**
     * Updating the start day of an event belonging to an activity to a value
     * after the maximum cutoff should not update the corresponding activity
     * property. Instead it should throw an exception.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_after_max() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeopen = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $timeclose = new \DateTimeImmutable('2017-02-2T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2018-02-2T10:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => $timeopen->getTimestamp(),
                'timeclose' => $timeclose->getTimestamp(),
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
                'timestart' => $timeopen->getTimestamp()
            ]
        );

        $this->expectException('moodle_exception');
        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
    }

    /**
     * Updating the start day of an event with no minimum cutoff should
     * update the corresponding activity property.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_no_min() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeclose = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2016-02-2T10:00:00+08:00');
        $expected = new \DateTimeImmutable('2016-02-2T15:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => 0,
                'timeclose' => $timeclose->getTimestamp()
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
                'timestart' => $timeclose->getTimestamp()
            ]
        );

        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
        $actual = $newevent->get_times()->get_start_time();
        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);

        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
        $this->assertEquals($expected->getTimestamp(), $feedback->timeopen);
    }

    /**
     * Updating the start day of an event belonging to an activity to a value
     * greater than the minimum cutoff should update the corresponding activity
     * property.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_greater_than_min() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeopen = new \DateTimeImmutable('2016-01-1T15:00:00+08:00');
        $timeclose = new \DateTimeImmutable('2019-01-1T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2018-02-2T10:00:00+08:00');
        $expected = new \DateTimeImmutable('2018-02-2T15:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => $timeopen->getTimestamp(),
                'timeclose' => $timeclose->getTimestamp()
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
                'timestart' => $timeclose->getTimestamp()
            ]
        );

        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
        $actual = $newevent->get_times()->get_start_time();
        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);

        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
        $this->assertEquals($expected->getTimestamp(), $feedback->timeclose);
    }

    /**
     * Updating the start day of an event belonging to an activity to a value
     * equal to the minimum cutoff should update the corresponding activity
     * property.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_equal_to_min() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeopen = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $timeclose = new \DateTimeImmutable('2018-02-2T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2017-01-1T10:00:00+08:00');
        $expected = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => $timeopen->getTimestamp(),
                'timeclose' => $timeclose->getTimestamp(),
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
                'timestart' => $timeclose->getTimestamp()
            ]
        );

        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
        $actual = $newevent->get_times()->get_start_time();
        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);

        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
        $this->assertEquals($expected->getTimestamp(), $feedback->timeclose);
    }

    /**
     * Updating the start day of an event belonging to an activity to a value
     * before the minimum cutoff should not update the corresponding activity
     * property. Instead it should throw an exception.
     *
     * Note: This test uses the feedback activity because it requires
     * module callbacks to be in place to test.
     */
    public function test_update_event_start_day_activity_event_before_min() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $timeopen = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $timeclose = new \DateTimeImmutable('2017-02-2T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2016-02-2T10:00:00+08:00');
        list($feedback, $event) = $this->create_feedback_activity_and_event(
            [
                'timeopen' => $timeopen->getTimestamp(),
                'timeclose' => $timeclose->getTimestamp(),
            ],
            [
                'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
                'timestart' => $timeclose->getTimestamp()
            ]
        );

        $this->expectException('moodle_exception');
        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
    }

    /**
     * Updating the start day of an overridden event belonging to an activity
     * should result in an exception. This is to prevent the drag and drop
     * of override events.
     *
     * Note: This test uses the quiz activity because it requires
     * module callbacks to be in place and override event support to test.
     */
    public function test_update_event_start_day_activity_event_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');
        require_once($CFG->dirroot . '/mod/quiz/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $mapper = container::get_event_mapper();
        $timeopen = new \DateTimeImmutable('2017-01-1T15:00:00+08:00');
        $newstartdate = new \DateTimeImmutable('2016-02-2T10:00:00+08:00');
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => $timeopen->getTimestamp(),
        ]);
        $event = create_event([
            'courseid' => $course->id,
            'userid' => $user->id,
            'modulename' => 'quiz',
            'instance' => $quiz->id,
            'eventtype' => QUIZ_EVENT_TYPE_OPEN,
            'timestart' => $timeopen->getTimestamp()
        ]);
        $event = $mapper->from_legacy_event_to_event($event);
        $record = (object) [
            'quiz' => $quiz->id,
            'userid' => $user->id
        ];

        $DB->insert_record('quiz_overrides', $record);

        $this->expectException('moodle_exception');
        $newevent = \core_calendar\local\api::update_event_start_day($event, $newstartdate);
    }
}
