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
 * Calendar event exporter tests tests.
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_calendar\external\calendar_event_exporter;
use core_calendar\local\event\container;

/**
 * Calendar event exporter testcase.
 *
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_event_exporter_testcase extends advanced_testcase {
    /**
     * Data provider for the module timestamp min limit test case to confirm
     * that the minimum time limit is set correctly on the boundary cases.
     */
    public function get_module_timestamp_min_limit_test_cases() {
        $now = time();
        $todaymidnight = usergetmidnight($now);
        $tomorrowmidnight = $todaymidnight + DAYSECS;
        $eightam = $todaymidnight + (60 * 60 * 8);
        $starttime = (new DateTime())->setTimestamp($eightam);

        return [
            'before min' => [
                $starttime,
                [
                    ($starttime->getTimestamp() + 1),
                    'some error'
                ],
                $tomorrowmidnight
            ],
            'equal min' => [
                $starttime,
                [
                    $starttime->getTimestamp(),
                    'some error'
                ],
                $todaymidnight
            ],
            'after min' => [
                $starttime,
                [
                    ($starttime->getTimestamp() - 1),
                    'some error'
                ],
                $todaymidnight
            ]
        ];
    }

    /**
     * @dataProvider get_module_timestamp_min_limit_test_cases()
     */
    public function test_get_module_timestamp_min_limit($starttime, $min, $expected) {
        $class = \core_calendar\external\calendar_event_exporter::class;
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $reflector = new ReflectionClass($class);
        $method = $reflector->getMethod('get_module_timestamp_min_limit');
        $method->setAccessible(true);

        $result = $method->invoke($mock, $starttime, $min);
        $this->assertEquals($expected, $result['mindaytimestamp']);
        $this->assertEquals($min[1], $result['mindayerror']);
    }

    /**
     * Data provider for the module timestamp min limit test case to confirm
     * that the minimum time limit is set correctly on the boundary cases.
     */
    public function get_module_timestamp_max_limit_test_cases() {
        $now = time();
        $todaymidnight = usergetmidnight($now);
        $yesterdaymidnight = $todaymidnight - DAYSECS;
        $eightam = $todaymidnight + (60 * 60 * 8);
        $starttime = (new DateTime())->setTimestamp($eightam);

        return [
            'before max' => [
                $starttime,
                [
                    ($starttime->getTimestamp() + 1),
                    'some error'
                ],
                $todaymidnight
            ],
            'equal max' => [
                $starttime,
                [
                    $starttime->getTimestamp(),
                    'some error'
                ],
                $todaymidnight
            ],
            'after max' => [
                $starttime,
                [
                    ($starttime->getTimestamp() - 1),
                    'some error'
                ],
                $yesterdaymidnight
            ]
        ];
    }

    /**
     * @dataProvider get_module_timestamp_max_limit_test_cases()
     */
    public function test_get_module_timestamp_max_limit($starttime, $max, $expected) {
        $class = \core_calendar\external\calendar_event_exporter::class;
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $reflector = new ReflectionClass($class);
        $method = $reflector->getMethod('get_module_timestamp_max_limit');
        $method->setAccessible(true);

        $result = $method->invoke($mock, $starttime, $max);
        $this->assertEquals($expected, $result['maxdaytimestamp']);
        $this->assertEquals($max[1], $result['maxdayerror']);
    }
}
