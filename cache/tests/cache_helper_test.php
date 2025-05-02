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

    /**
     * Test the filter_sorted_keys_by_prefixes method.
     *
     * @param array $keys
     * @param array $prefixes
     * @param array $expected
     * @dataProvider filter_sorted_keys_by_prefixes_provider
     */
    public function test_filter_sorted_keys_by_prefixes(array $keys, array $prefixes, array $expected): void {
        $this->assertEquals($expected, helper::filter_sorted_keys_by_prefixes($keys, $prefixes));
    }

    /**
     * Data provider for filter_sorted_keys_by_prefixes tests.
     *
     * @return array
     */
    public static function filter_sorted_keys_by_prefixes_provider(): array {
        return [
            'simple match' => [
                'keys' => ['aa', 'ab', 'ba', 'bb'],
                'prefixes' => ['a'],
                'expected' => ['aa', 'ab'],
            ],
            'multiple prefixes match' => [
                'keys' => ['aa', 'ab', 'ba', 'bb', 'ca', 'cb'],
                'prefixes' => ['a', 'c'],
                'expected' => ['aa', 'ab', 'ca', 'cb'],
            ],
            'consecutive prefixes match' => [
                'keys' => ['aa', 'ab', 'ba', 'bb', 'ca', 'cb'],
                'prefixes' => ['a', 'b'],
                'expected' => ['aa', 'ab', 'ba', 'bb'],
            ],
            'overlapping prefixes' => [
                'keys' => ['a', 'ab', 'abc', 'abcd'],
                'prefixes' => ['ab', 'abc'],
                'expected' => ['ab', 'abc', 'abcd'],
            ],
            'exact match' => [
                'keys' => ['a', 'b', 'c'],
                'prefixes' => ['a', 'c'],
                'expected' => ['a', 'c'],
            ],
            'duplicate keys' => [
                'keys' => ['a', 'a', 'b', 'c'],
                'prefixes' => ['a'],
                'expected' => ['a', 'a'],
            ],
            'duplicate prefixes' => [
                'keys' => ['a', 'b', 'c'],
                'prefixes' => ['a', 'a', 'b'],
                'expected' => ['a', 'b'],
            ],
            'unsorted keys boundry' => [
                'keys' => ['c', 'b', 'a'],
                'prefixes' => ['a'],
                'expected' => [],
            ],
            'unsorted prefixes boundry' => [
                'keys' => ['a', 'b', 'c'],
                'prefixes' => ['d', 'a'],
                'expected' => [],
            ],
            'empty keys' => [
                'keys' => [],
                'prefixes' => ['a'],
                'expected' => [],
            ],
            'empty prefixes' => [
                'keys' => ['a', 'b', 'c'],
                'prefixes' => [],
                'expected' => [],
            ],
        ];
    }
}
