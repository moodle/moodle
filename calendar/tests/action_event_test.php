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
 * Action event tests.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar;

use core_calendar\local\event\entities\action_event;
use core_calendar\local\event\value_objects\action;
use core_calendar\local\event\value_objects\event_description;
use core_calendar\local\event\value_objects\event_times;
use core_calendar\local\event\entities\event_collection_interface;
use core_calendar\local\event\entities\event_interface;

defined('MOODLE_INTERNAL') || die;

/**
 * Action event testcase.
 *
 * @package core_calendar
 * @category test
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_event_test extends \advanced_testcase {
    /**
     * Test event class getters.
     *
     * @dataProvider getters_testcases
     * @param array $constructorparams Associative array of constructor parameters.
     */
    public function test_getters($constructorparams): void {
        $event = new action_event(
            $constructorparams['event'],
            $constructorparams['action']
        );

        foreach ($constructorparams as $name => $value) {
            if ($name !== 'event') {
                $this->assertEquals($event->{'get_' . $name}(), $value);
            }
        }
    }

    /**
     * Test cases for getters test.
     */
    public static function getters_testcases(): array {
        return [
            'Dataset 1' => [
                'constructorparams' => [
                    'event' => new core_calendar_action_event_test_event(),
                    'action' => new action(
                        'action 1',
                        new \moodle_url('http://example.com'),
                        2,
                        true
                    )
                ]
            ],
            'Dataset 2' => [
                'constructorparams' => [
                    'event' => new core_calendar_action_event_test_event(),
                    'action' => new action(
                        'action 2',
                        new \moodle_url('http://example.com'),
                        5,
                        false
                    )
                ]
            ],
        ];
    }
}

/**
 * Test event.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_action_event_test_event implements event_interface {

    public function get_id() {
        return 1729;
    }

    public function get_name() {
        return 'Jeff';
    }

    public function get_description() {
        return new event_description('asdf', 1);
    }

    public function get_location() {
        return 'Cube office';
    }

    public function get_category() {
        return new \stdClass();
    }

    public function get_course() {
        return new \stdClass();
    }

    public function get_course_module() {
        return new \stdClass();
    }

    public function get_group() {
        return new \stdClass();
    }

    public function get_user() {
        return new \stdClass();
    }

    public function get_type() {
        return 'asdf';
    }

    public function get_times() {
        return new event_times(
            (new \DateTimeImmutable())->setTimestamp(-386380800),
            (new \DateTimeImmutable())->setTimestamp(115776000),
            (new \DateTimeImmutable())->setTimestamp(115776000),
            (new \DateTimeImmutable())->setTimestamp(time())
        );
    }

    public function get_repeats() {
        return new core_calendar_action_event_test_event_collection();
    }

    public function get_subscription() {
        return new \stdClass();
    }

    public function is_visible() {
        return true;
    }

    /**
     * Component
     * @return string|null
     */
    public function get_component() {
        return null;
    }
}

/**
 * Test event collection.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_action_event_test_event_collection implements event_collection_interface {
    /**
     * @var array
     */
    protected $events;

    /**
     * core_calendar_action_event_test_event_collection constructor.
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

    public function getIterator(): \Traversable {
        foreach ($this->events as $event) {
            yield $event;
        }
    }
}
