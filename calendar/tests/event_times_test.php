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

use core_calendar\local\event\value_objects\event_times;

/**
 * Event times tests.
 *
 * @package core_calendar
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_times_test extends \advanced_testcase {
    /**
     * Test event times class getters.
     *
     * @dataProvider getters_testcases()
     * @param array $constructorparams Associative array of constructor parameters.
     */
    public function test_getters($constructorparams) {
        $eventtimes = new event_times(
            $constructorparams['start_time'],
            $constructorparams['end_time'],
            $constructorparams['sort_time'],
            $constructorparams['modified_time'],
            $constructorparams['usermidnight_time']
        );

        foreach ($constructorparams as $name => $value) {
            $this->assertEquals($eventtimes->{'get_' . $name}(), $value);
        }

        $this->assertEquals($eventtimes->get_duration(), $constructorparams['end_time']->diff($constructorparams['start_time']));
    }

    /**
     * Test cases for getters test.
     */
    public function getters_testcases() {
        return [
            'Dataset 1' => [
                'constructorparams' => [
                    'start_time' => (new \DateTimeImmutable())->setTimestamp(-386380800),
                    'end_time' => (new \DateTimeImmutable())->setTimestamp(115776000),
                    'sort_time' => (new \DateTimeImmutable())->setTimestamp(115776000),
                    'modified_time' => (new \DateTimeImmutable())->setTimestamp(time()),
                    'usermidnight_time' => (new \DateTimeImmutable())->setTimestamp(115776000),
                ]
            ],
            'Dataset 2' => [
                'constructorparams' => [
                    'start_time' => (new \DateTimeImmutable())->setTimestamp(123456),
                    'end_time' => (new \DateTimeImmutable())->setTimestamp(12345678),
                    'sort_time' => (new \DateTimeImmutable())->setTimestamp(1111),
                    'modified_time' => (new \DateTimeImmutable())->setTimestamp(time()),
                    'usermidnight_time' => (new \DateTimeImmutable())->setTimestamp(1111),
                ]
            ]
        ];
    }
}
