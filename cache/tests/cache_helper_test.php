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

namespace core_cache;

/**
 * PHPunit tests for the cache_helper class.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_cache\helper
 */
final class cache_helper_test extends \advanced_testcase {
    /**
     * Test the result_found method.
     *
     * @param mixed $value
     * @param bool $expected
     * @dataProvider result_found_provider
     */
    public function test_result_found($value, bool $expected): void {
        $this->assertEquals($expected, helper::result_found($value));
    }

    /**
     * Data provider for result_found tests.
     *
     * @return array
     */
    public static function result_found_provider(): array {
        return [
            // Only false values are considered as not found.
            [false, false],

            // The rest are considered valid values.
            [null, true],
            [0, true],
            ['', true],
            [[], true],
            [new \stdClass(), true],
            [true, true],
            [1, true],
            ['a', true],
            [[1], true],
            [new \stdClass(), true],
        ];
    }
}
