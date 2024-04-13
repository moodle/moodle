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
 * Contains unit tests for mod_data\dates.
 *
 * @package   mod_data
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_data;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_data\dates.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates_test extends advanced_testcase {

    /**
     * Data provider for get_dates_for_module().
     * @return array[]
     */
    public static function get_dates_for_module_provider(): array {
        $now = time();
        $before = $now - DAYSECS;
        $earlier = $before - DAYSECS;
        $after = $now + DAYSECS;
        $later = $after + DAYSECS;

        return [
            'without any dates' => [
                null, null, []
            ],
            'only with opening time' => [
                $after, null, [
                    ['label' => 'Opens:', 'timestamp' => $after, 'dataid' => 'timeavailablefrom'],
                ]
            ],
            'only with closing time' => [
                null, $after, [
                    ['label' => 'Closes:', 'timestamp' => $after, 'dataid' => 'timeavailableto'],
                ]
            ],
            'with both times' => [
                $after, $later, [
                    ['label' => 'Opens:', 'timestamp' => $after, 'dataid' => 'timeavailablefrom'],
                    ['label' => 'Closes:', 'timestamp' => $later, 'dataid' => 'timeavailableto'],
                ]
            ],
            'between the dates' => [
                $before, $after, [
                    ['label' => 'Opened:', 'timestamp' => $before, 'dataid' => 'timeavailablefrom'],
                    ['label' => 'Closes:', 'timestamp' => $after, 'dataid' => 'timeavailableto'],
                ]
            ],
            'dates are past' => [
                $earlier, $before, [
                    ['label' => 'Opened:', 'timestamp' => $earlier, 'dataid' => 'timeavailablefrom'],
                    ['label' => 'Closed:', 'timestamp' => $before, 'dataid' => 'timeavailableto'],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $availablefrom The "available from" time in the database activity.
     * @param int|null $availableto The "available to" time in the database activity.
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $availablefrom, ?int $availableto, array $expected): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $data = ['course' => $course->id];
        if ($availablefrom) {
            $data['timeavailablefrom'] = $availablefrom;
        }
        if ($availableto) {
            $data['timeavailableto'] = $availableto;
        }
        $moddata = $this->getDataGenerator()->create_module('data', $data);

        $this->setUser($user);

        $cm = get_coursemodule_from_instance('data', $moddata->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }
}
