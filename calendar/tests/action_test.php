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

use core_calendar\local\event\value_objects\action;

/**
 * Action testcase.
 *
 * @package    core_calendar
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_test extends \advanced_testcase {
    /**
     * Test action class getters.
     *
     * @dataProvider getters_testcases()
     * @param array $constructorparams Associative array of constructor parameters.
     */
    public function test_getters($constructorparams) {
        $action = new action(
            $constructorparams['name'],
            $constructorparams['url'],
            $constructorparams['item_count'],
            $constructorparams['actionable']
        );

        foreach ($constructorparams as $name => $value) {
            if ($name == 'actionable') {
                $this->assertEquals($action->is_actionable(), $value);
            } else {
                $this->assertEquals($action->{'get_' . $name}(), $value);
            }
        }
    }

    /**
     * Test cases for getters test.
     */
    public function getters_testcases() {
        return [
            'Dataset 1' => [
                'constructorparams' => [
                    'name' => 'Hello',
                    'url' => new \moodle_url('http://example.com'),
                    'item_count' => 1,
                    'actionable' => true
                ]
            ],
            'Dataset 2' => [
                'constructorparams' => [
                    'name' => 'Goodbye',
                    'url' => new \moodle_url('http://example.com'),
                    'item_count' => 2,
                    'actionable' => false
                ]
            ]
        ];
    }
}
