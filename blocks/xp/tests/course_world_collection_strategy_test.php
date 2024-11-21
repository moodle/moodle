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
 * Course world collection strategy tests.
 *
 * @package    block_xp
 * @copyright  2019 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;
defined('MOODLE_INTERNAL') || die();

use block_xp\local\strategy\course_world_collection_strategy;

global $CFG;

/**
 * Course world collection strategy testcase.
 *
 * @package    block_xp
 * @copyright  2019 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_xp\local\stragey\course_world_collection_strategy
 */
final class course_world_collection_strategy_test extends \advanced_testcase {

    public function test_is_action_accepted_no_limit(): void {
        $now = time();
        $log = ['a' => [$now, $now, $now, $now, $now, $now, $now, $now, $now]];
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 0, 0));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 1, 0, 0));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 1, 0));
    }

    public function test_is_action_accepted_not_in_log(): void {
        $now = time();
        $log = [
            'a' => [$now - 1000, $now - 2000, $now - 100, $now],
            'b' => [$now - 1000, $now - 2000, $now - 100, $now],
        ];
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('c', $now, $log, 4, 1000, 0));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('c', $now, $log, 5, 1000, 0));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('c', $now, $log, 0, 0, 1));
    }

    public function test_is_action_accepted_max_actions_in_timeframe(): void {
        $now = time();

        $log = ['a' => [$now, $now, $now, $now, $now, $now, $now, $now, $now]];
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('a', $now, $log, 1, 1, 0));
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('a', $now, $log, 1, 1000, 0));

        $log = ['b' => [$now]];
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('a', $now, $log, 1, 1, 0));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 2, 1, 0));

        $log = ['a' => [$now - 1000, $now - 2000, $now - 100]];
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 3, 1, 0));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 3, 1000, 0));
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('a', $now, $log, 3, 10000, 0));
    }

    public function test_is_action_accepted_time_between_repeated(): void {
        $now = time();

        $log = ['a' => [$now, $now, $now, $now, $now, $now, $now, $now, $now]];
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 0, 1));
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 0, 1000));

        $log = ['b' => [$now, $now, $now, $now, $now, $now, $now, $now, $now]];
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 0, 1));

        $log = ['a' => [$now - 1000, $now - 2000, $now - 100]];
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 0, 99));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 0, 100));
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('a', $now, $log, 0, 0, 101));
    }

    public function test_is_action_accepted(): void {
        $now = time();

        $log = [
            'a' => [$now - 300, $now - 1000],
            'b' => [$now, $now - 500],
            'c' => [$now - 10, $now - 2000],
        ];

        $this->assertTrue(course_world_collection_strategy::is_action_accepted('a', $now, $log, 5, 600, 200));
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('b', $now, $log, 5, 100, 2000));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('c', $now, $log, 5, 100, 10));
        $this->assertTrue(course_world_collection_strategy::is_action_accepted('d', $now, $log, 0, 0, 10));
        $this->assertFalse(course_world_collection_strategy::is_action_accepted('d', $now, $log, 5, 8000, 10));
    }

}
