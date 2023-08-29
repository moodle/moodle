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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p1;

use advanced_testcase;
use enrol_oneroster\local\interfaces\coursecat_representation;
use stdClass;
use InvalidArgumentException;
use ReflectionClass;

/**
 * One Roster tests for filters.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  enrol_oneroster\local\filter
 * @covers  enrol_oneroster\local\v1p1\filter
 */
class filter_testcase extends advanced_testcase {
    /**
     * Ensure that the filter constructor adds filters correctly.
     *
     * @dataProvider    filter_constructor_data_provider
     * @param   array $args Constructor rags
     * @param   string $expected The expected filter
     */
    public function test_constructor_adds_filter(array $args, string $expected): void {
        $rc  = new ReflectionClass(filter::class);
        $filter = $rc->newInstanceArgs($args);

        $this->assertEquals($expected, (string) $filter);
    }

    /**
     * Data provider for the filter.
     *
     * @return  array
     */
    public function filter_constructor_data_provider(): array {
        return [
            [
                ['sourcedId', 'example'],
                "sourcedId='example'",
            ],
            [
                ['sourcedId', 'example', '~'],
                "sourcedId~'example'",
            ],
            [
                ['name', '0', '<='],
                "name<='0'",
            ],
        ];
    }

    /**
     * Ensure that it is not possible to add more than 2 filters.
     */
    public function test_filter_count_limit(): void {
        $filter = new filter();
        $filter->add_filter('sourcedId', 'example');
        $filter->add_filter('sourcedId', 'otherexample');

        $this->expectException(InvalidArgumentException::class);
        $filter->add_filter('sourcedId', 'otherexample');
    }

    /**
     * Ensure that the add_filter function adds filters correctly.
     *
     * @dataProvider    add_filter_data_provider
     * @param   array $filters Constructor rags
     * @param   string $jointype The type of join to apply
     * @param   string $expected The expected filter
     */
    public function test_add_filter(array $filters, string $jointype, string $expected): void {
        $filter = new filter();
        foreach ($filters as $filterarg) {
            call_user_func_array([$filter, 'add_filter'], $filterarg);
        }

        $filter->set_operator($jointype);

        $this->assertEquals($expected, (string) $filter);
    }

    /**
     * Data provider for the filter.
     *
     * @return  array
     */
    public function add_filter_data_provider(): array {
        return [
            [
                [
                    ['sourcedId', 'example'],
                ],
                'AND',
                "sourcedId='example'",
            ],
            [
                [
                    ['sourcedId', 'example'],
                    ['name', 'otherthings'],
                ],
                'AND',
                "sourcedId='example' AND name='otherthings'",
            ],
            [
                [
                    ['name', 'otherthings'],
                    ['sourcedId', 'example'],
                ],
                'OR',
                "name='otherthings' OR sourcedId='example'",
            ],
            [
                [
                    ['org.sourcedId', 'bcc2347a-817b-4320-b208-1a9397895b14'],
                    ['dateLastModified', '2020-07-01T12:00:00.000Z', '<='],
                ],
                'AND',
                "org.sourcedId='bcc2347a-817b-4320-b208-1a9397895b14' AND dateLastModified<='2020-07-01T12:00:00.000Z'",
            ],
            [
                [
                    ['org.sourcedId', 'bcc2347a-817b-4320-b208-1a9397895b14'],
                    ['dateLastModified', '2020-07-01T12:00:00.000Z', '<='],
                ],
                'and',
                "org.sourcedId='bcc2347a-817b-4320-b208-1a9397895b14' AND dateLastModified<='2020-07-01T12:00:00.000Z'",
            ],
        ];
    }

    /**
     * Check that invalid operators are picked up.
     *
     * @dataProvider    invalid_operator_provider
     * @param   string $operator
     */
    public function test_invalid_operator(string $operator): void {
        $this->expectException(InvalidArgumentException::class);

        $filter = new filter();
        $filter->set_operator($operator);
    }

    /**
     * Data provider for invalid operators.
     *
     * @return  array
     */
    public function invalid_operator_provider(): array {
        return [
            ['but'],
            ['not'],
        ];
    }
}
