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

use core_calendar\local\event\value_objects\event_description;

/**
 * Action testcase.
 *
 * @package core_calendar
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_description_test extends \advanced_testcase {
    /**
     * Test event description class getters.
     *
     * @dataProvider getters_testcases()
     * @param array $constructorparams Associative array of constructor parameters.
     */
    public function test_getters($constructorparams) {
        $eventdescription = new event_description(
            $constructorparams['value'],
            $constructorparams['format']
        );
        foreach ($constructorparams as $name => $value) {
            $this->assertEquals($eventdescription->{'get_' . $name}(), $value);
        }
    }

    /**
     * Test cases for getters test.
     */
    public function getters_testcases() {
        return [
            'Dataset 1' => [
                'constructorparams' => [
                    'value' => 'Hello',
                    'format' => 1
                ]
            ],
            'Dataset 2' => [
                'constructorparams' => [
                    'value' => 'Goodbye',
                    'format' => 2
                ]
            ]
        ];
    }
}
