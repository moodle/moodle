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
 * Event tests.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\entities\event;
use core_calendar\local\event\proxies\std_proxy;
use core_calendar\local\event\value_objects\event_description;
use core_calendar\local\event\value_objects\event_times;
use core_calendar\local\event\entities\event_collection_interface;

/**
 * Event testcase.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_event_testcase extends advanced_testcase {
    /**
     * Test event class getters.
     *
     * @dataProvider getters_testcases()
     * @param array $constructorparams Associative array of constructor parameters.
     */
    public function test_getters($constructorparams) {
        $event = new event(
            $constructorparams['id'],
            $constructorparams['name'],
            $constructorparams['description'],
            $constructorparams['course'],
            $constructorparams['group'],
            $constructorparams['user'],
            $constructorparams['repeats'],
            $constructorparams['course_module'],
            $constructorparams['type'],
            $constructorparams['times'],
            $constructorparams['visible'],
            $constructorparams['subscription']
        );

        foreach ($constructorparams as $name => $value) {
            if ($name !== 'visible') {
                $this->assertEquals($event->{'get_' . $name}(), $value);
            }
        }

        $this->assertEquals($event->is_visible(), $constructorparams['visible']);
    }

    /**
     * Test cases for getters test.
     */
    public function getters_testcases() {
        $lamecallable = function($id) {
            return (object)['id' => $id];
        };

        return [
            'Dataset 1' => [
                'constructorparams' => [
                    'id' => 1,
                    'name' => 'Test event 1',
                    'description' => new event_description('asdf', 1),
                    'course' => new std_proxy(1, $lamecallable),
                    'group' => new std_proxy(1, $lamecallable),
                    'user' => new std_proxy(1, $lamecallable),
                    'repeats' => new core_calendar_event_test_event_collection(),
                    'course_module' => new std_proxy(1, $lamecallable),
                    'type' => 'dunno what this actually is meant to be',
                    'times' => new event_times(
                        (new \DateTimeImmutable())->setTimestamp(-386380800),
                        (new \DateTimeImmutable())->setTimestamp(115776000),
                        (new \DateTimeImmutable())->setTimestamp(115776000),
                        (new \DateTimeImmutable())->setTimestamp(time())
                    ),
                    'visible' => true,
                    'subscription' => new std_proxy(1, $lamecallable)
                ]
            ],
        ];
    }
}

/**
 * Test event class.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_event_test_event_collection implements event_collection_interface {
    /**
     * @var array $events Array of events.
     */
    protected $events;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->events = [
            'not really an event hahaha',
            'also not really. gottem.'
        ];
    }

    public function get_id() {
        return 1729;
    }

    public function get_num() {
        return 2;
    }

    public function getIterator() {
        foreach ($this->events as $event) {
            yield $event;
        }
    }
}
