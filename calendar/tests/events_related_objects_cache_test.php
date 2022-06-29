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

use core_calendar\external\events_related_objects_cache;
use core_calendar\local\event\container;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/helpers.php');

/**
 * Tests for the events_related_objects_cache.
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_related_objects_cache_test extends \advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * An event with no module should return null when trying to retrieve
     * the module instance.
     */
    public function test_get_module_instance_no_module() {
        $this->setAdminUser();
        $mapper = container::get_event_mapper();
        $legacyevent = create_event([
            'modulename' => '',
            'instance' => 0
        ]);
        $event = $mapper->from_legacy_event_to_event($legacyevent);
        $cache = new events_related_objects_cache([$event]);

        $this->assertNull($cache->get_module_instance($event));
    }

    /**
     * The get_module_instance should return the correct module instances
     * for the given set of events in the cache.
     */
    public function test_get_module_instance_with_modules() {
        $this->setAdminUser();
        $mapper = container::get_event_mapper();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $plugingenerator = $generator->get_plugin_generator('mod_assign');
        $instance1 = $plugingenerator->create_instance(['course' => $course->id]);
        $instance2 = $plugingenerator->create_instance(['course' => $course->id]);
        unset($instance1->cmid);
        unset($instance2->cmid);

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
            'modulename' => 'assign',
            'userid' => 0,
            'eventtype' => 'due',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $legacyevent1 = create_event(array_merge($params, ['name' => 'Event 1', 'instance' => $instance1->id]));
        $legacyevent2 = create_event(array_merge($params, ['name' => 'Event 2', 'instance' => $instance1->id]));
        $legacyevent3 = create_event(array_merge($params, ['name' => 'Event 3', 'instance' => $instance2->id]));
        $event1 = $mapper->from_legacy_event_to_event($legacyevent1);
        $event2 = $mapper->from_legacy_event_to_event($legacyevent2);
        $event3 = $mapper->from_legacy_event_to_event($legacyevent3);
        $cache = new events_related_objects_cache([$event1, $event2, $event3]);

        $eventinstance1 = $cache->get_module_instance($event1);
        $eventinstance2 = $cache->get_module_instance($event2);
        $eventinstance3 = $cache->get_module_instance($event3);

        $this->assertEquals($instance1, $eventinstance1);
        $this->assertEquals($instance1, $eventinstance2);
        $this->assertEquals($instance2, $eventinstance3);
    }

    /**
     * Trying to load the course module of an event that isn't in
     * the cache should return null.
     */
    public function test_module_instance_unknown_event() {
        $this->setAdminUser();
        $mapper = container::get_event_mapper();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $plugingenerator = $generator->get_plugin_generator('mod_assign');
        $instance1 = $plugingenerator->create_instance(['course' => $course->id]);
        $instance2 = $plugingenerator->create_instance(['course' => $course->id]);
        unset($instance1->cmid);
        unset($instance2->cmid);

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
            'modulename' => 'assign',
            'userid' => 0,
            'eventtype' => 'due',
            'repeats' => 0,
            'timestart' => 1,
        ];

        $legacyevent1 = create_event(array_merge($params, ['name' => 'Event 1', 'instance' => $instance1->id]));
        $legacyevent2 = create_event(array_merge($params, ['name' => 'Event 2', 'instance' => $instance2->id]));
        $event1 = $mapper->from_legacy_event_to_event($legacyevent1);
        $event2 = $mapper->from_legacy_event_to_event($legacyevent2);
        $cache = new events_related_objects_cache([$event1]);

        $this->assertNull($cache->get_module_instance($event2));
    }
}
