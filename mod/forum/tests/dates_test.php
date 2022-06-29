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
 * Contains unit tests for mod_forum\dates.
 *
 * @package   mod_forum
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_forum;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_forum\dates.
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
        $after = $now + DAYSECS;

        return [
            'without any dates' => [
                null, []
            ],
            'future due date' => [
                $after, [
                    ['label' => 'Due:', 'timestamp' => $after],
                ]
            ],
            'due date is past' => [
                $before, [
                    ['label' => 'Due:', 'timestamp' => $before],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $duedate Forum's due date.
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $duedate, array $expected) {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $data = ['course' => $course->id];
        if ($duedate) {
            $data['duedate'] = $duedate;
        }

        $this->setAdminUser();
        $forum = $this->getDataGenerator()->create_module('forum', $data);

        $this->setUser($user);

        $cm = get_coursemodule_from_instance('forum', $forum->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }
}
