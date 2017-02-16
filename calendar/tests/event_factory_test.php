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
 * Event factory test.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\factories\event_factory;
use core_calendar\local\interfaces\event_interface;

/**
 * Event factory testcase.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_event_factory_testcase extends advanced_testcase {
    /**
     * Test event class getters.
     *
     * @dataProvider create_instance_testcases()
     * @param \stdClass $dbrow Row from the event table.
     * @param callable  $actioncallbackapplier     Action callback applier.
     * @param callable  $visibilitycallbackapplier Visibility callback applier.
     * @param string    $expectedclass             Class the factory is expected to produce.
     * @param mixed     $expectedattributevalue    Expected value of the modified attribute.
     */
    public function test_create_instance(
        $dbrow,
        callable $actioncallbackapplier,
        callable $visibilitycallbackapplier,
        $expectedclass,
        $expectedattributevalue
    ) {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $event = $this->create_event();

        $factory = new event_factory($actioncallbackapplier, $visibilitycallbackapplier);
        $dbrow->id = $event->id;
        $instance = $factory->create_instance($dbrow);

        if ($expectedclass) {
            $this->assertInstanceOf($expectedclass, $instance);
        }

        if (is_null($expectedclass)) {
            $this->assertNull($instance);
        }

        if ($expectedattributevalue) {
            $this->assertEquals($instance->testattribute, $expectedattributevalue);
        }
    }

    /**
     * Test invalid callback exception.
     *
     * @expectedException \core_calendar\local\event\exceptions\invalid_callback_exception
     */
    public function test_invalid_action_callback() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $event = $this->create_event();
        $factory = new event_factory(
            function () {
                return 'hello';
            },
            function () {
                return true;
            }
        );

        $factory->create_instance(
            (object)[
                'id' => $event->id,
                'name' => 'test',
                'description' => 'Test description',
                'format' => 2,
                'courseid' => 1,
                'groupid' => 1,
                'userid' => 1,
                'repeatid' => 1,
                'modulename' => 'assign',
                'instance' => 1,
                'eventtype' => 'due',
                'timestart' => 123456789,
                'timeduration' => 12,
                'timemodified' => 123456789,
                'timesort' => 123456789,
                'visible' => 1,
                'subscriptionid' => 1
            ]
        );
    }

    /**
     * Test invalid callback exception.
     *
     * @expectedException \core_calendar\local\event\exceptions\invalid_callback_exception
     */
    public function test_invalid_visibility_callback() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $event = $this->create_event();
        $factory = new event_factory(
            function ($event) {
                return $event;
            },
            function () {
                return 'asdf';
            }
        );

        $factory->create_instance(
            (object)[
                'id' => $event->id,
                'name' => 'test',
                'description' => 'Test description',
                'format' => 2,
                'courseid' => 1,
                'groupid' => 1,
                'userid' => 1,
                'repeatid' => 1,
                'modulename' => 'assign',
                'instance' => 1,
                'eventtype' => 'due',
                'timestart' => 123456789,
                'timeduration' => 12,
                'timemodified' => 123456789,
                'timesort' => 123456789,
                'visible' => 1,
                'subscriptionid' => 1
            ]
        );
    }

    public function create_instance_testcases() {
        return [
            'Sample event record with event exposed' => [
                'dbrow' => (object)[
                    'name' => 'Test event',
                    'description' => 'Hello',
                    'format' => 1,
                    'courseid' => 1,
                    'groupid' => 1,
                    'userid' => 1,
                    'repeatid' => null,
                    'modulename' => 'Test module',
                    'instance' => 1,
                    'eventtype' => 'Due',
                    'timestart' => 123456789,
                    'timeduration' => 123456789,
                    'timemodified' => 123456789,
                    'timesort' => 123456789,
                    'visible' => true,
                    'subscriptionid' => 1
                ],
                'actioncallbackapplier' => function(event_interface $event) {
                    $event->testattribute = 'Hello';
                    return $event;
                },
                'visibilitycallbackapplier' => function(event_interface $event) {
                    return true;
                },
                event_interface::class,
                'Hello'
            ],
            'Sample event record with event hidden' => [
                'dbrow' => (object)[
                    'name' => 'Test event',
                    'description' => 'Hello',
                    'format' => 1,
                    'courseid' => 1,
                    'groupid' => 1,
                    'userid' => 1,
                    'repeatid' => null,
                    'modulename' => 'Test module',
                    'instance' => 1,
                    'eventtype' => 'Due',
                    'timestart' => 123456789,
                    'timeduration' => 123456789,
                    'timemodified' => 123456789,
                    'timesort' => 123456789,
                    'visible' => true,
                    'subscriptionid' => 1
                ],
                'actioncallbackapplier' => function(event_interface $event) {
                    $event->testattribute = 'Hello';
                    return $event;
                },
                'visibilitycallbackapplier' => function(event_interface $event) {
                    return false;
                },
                null,
                null
            ]
        ];
    }

    /**
     * Helper function to create calendar events using the old code.
     *
     * @param array $properties A list of calendar event properties to set
     * @return event
     */
    protected function create_event($properties = []) {
        $record = new \stdClass();
        $record->name = 'event name';
        $record->eventtype = 'global';
        $record->timestart = time();
        $record->timeduration = 0;
        $record->timesort = 0;
        $record->type = 1;
        $record->courseid = 0;

        foreach ($properties as $name => $value) {
            $record->$name = $value;
        }

        $event = new \core_calendar\event($record);
        return $event->create($record, false);
    }
}
