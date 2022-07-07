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
 * Contains unit tests for mod_feedback\dates.
 *
 * @package   mod_feedback
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_feedback;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_feedback\dates.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates_test extends advanced_testcase {

    /**
     * Data provider for get_dates_for_module().
     * @return array[]
     */
    public function get_dates_for_module_provider(): array {
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
                    ['label' => 'Opens:', 'timestamp' => $after, 'dataid' => 'timeopen'],
                ]
            ],
            'only with closing time' => [
                null, $after, [
                    ['label' => 'Closes:', 'timestamp' => $after, 'dataid' => 'timeclose'],
                ]
            ],
            'with both times' => [
                $after, $later, [
                    ['label' => 'Opens:', 'timestamp' => $after, 'dataid' => 'timeopen'],
                    ['label' => 'Closes:', 'timestamp' => $later, 'dataid' => 'timeclose'],
                ]
            ],
            'between the dates' => [
                $before, $after, [
                    ['label' => 'Opened:', 'timestamp' => $before, 'dataid' => 'timeopen'],
                    ['label' => 'Closes:', 'timestamp' => $after, 'dataid' => 'timeclose'],
                ]
            ],
            'dates are past' => [
                $earlier, $before, [
                    ['label' => 'Opened:', 'timestamp' => $earlier, 'dataid' => 'timeopen'],
                    ['label' => 'Closed:', 'timestamp' => $before, 'dataid' => 'timeclose'],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $timeopen The "allow answers from" time in the feedback activity.
     * @param int|null $timeclose The "allow answers to" time in the feedback activity.
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $timeopen, ?int $timeclose, array $expected) {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $data = ['course' => $course->id];
        if ($timeopen) {
            $data['timeopen'] = $timeopen;
        }
        if ($timeclose) {
            $data['timeclose'] = $timeclose;
        }
        $feedback = $this->getDataGenerator()->create_module('feedback', $data);

        $this->setUser($user);

        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }
}
