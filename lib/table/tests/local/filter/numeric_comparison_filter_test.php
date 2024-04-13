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
 * Unit tests for core_table\local\filter\numeric_comparison_filter.
 *
 * @package   core_table
 * @category  test
 * @copyright 2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

declare(strict_types=1);

namespace core_table\local\filter;

use advanced_testcase;
use InvalidArgumentException;
use TypeError;

/**
 * Unit tests for core_table\local\filter\numeric_comparison_filter.
 *
 * @package   core_table
 * @category  test
 * @copyright 2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class numeric_comparison_filter_test extends advanced_testcase {
    /**
     * Ensure that the add_filter_value function works as expected with valid values.
     */
    public function test_add_filter_value_valid(): void {
        $filter = new numeric_comparison_filter('example');

        // Initially an empty list.
        $this->assertEmpty($filter->get_filter_values());

        // Adding a value should return that value.
        $filter->add_filter_value(json_encode((object) [
            'direction' => '>',
            'value' => 100,
        ]));
        $this->assertEquals([
            (object) [
                'direction' => '>',
                'value' => 100,
            ],
        ], $filter->get_filter_values());

        // Adding a second value should add that value.
        // The values should sorted.
        $filter->add_filter_value(json_encode((object) [
            'direction' => '<=',
            'value' => 1000,
        ]));
        $this->assertEquals([
            (object) [
                'direction' => '<=',
                'value' => 1000,
            ],
            (object) [
                'direction' => '>',
                'value' => 100,
            ],
        ], $filter->get_filter_values());

        // Adding a duplicate value should not lead to that value being added again.
        $filter->add_filter_value(json_encode((object) [
            'direction' => '>',
            'value' => 100,
        ]));
        $this->assertEquals([
            (object) [
                'direction' => '<=',
                'value' => 1000,
            ],
            (object) [
                'direction' => '>',
                'value' => 100,
            ],
        ], $filter->get_filter_values());
    }

    /**
     * Ensure that the add_filter_value function rejects invalid types.
     *
     * @dataProvider add_filter_value_invalid_types_provider
     * @param mixed $values
     * @param string $exceptiontype
     * @param string $exceptionmessage
     */
    public function test_add_filter_value_type_invalid($values, string $exceptiontype, string $exceptionmessage): void {
        $filter = new numeric_comparison_filter('example');

        // Adding empty string is not supported.
        $this->expectException($exceptiontype);
        $this->expectExceptionMessage($exceptionmessage);
        call_user_func_array([$filter, 'add_filter_value'], $values);
    }

    /**
     * Data provider for add_filter_value tests with invalid types.
     *
     * @return array
     */
    public static function add_filter_value_invalid_types_provider(): array {
        return [
            'Null' => [
                [null],
                TypeError::class,
                "The value supplied was of type 'NULL'. A string representing a json-encoded value was expected.",
            ],
            'Single value string' => [
                [''],
                InvalidArgumentException::class,
                "A json-encoded object containing both a direction, and comparison value was expected.",
            ],
            'Single value integer' => [
                [42],
                TypeError::class,
                "The value supplied was of type 'integer'. A string representing a json-encoded value was expected.",
            ],
            'Single value float' => [
                [4.2],
                TypeError::class,
                "The value supplied was of type 'double'. A string representing a json-encoded value was expected.",
            ],
            'Single value bool' => [
                [false],
                TypeError::class,
                "The value supplied was of type 'boolean'. A string representing a json-encoded value was expected.",
            ],
            'Single value array' => [
                [[]],
                TypeError::class,
                "The value supplied was of type 'array'. A string representing a json-encoded value was expected.",
            ],
            'Single value object' => [
                [(object) []],
                TypeError::class,
                "The value supplied was of type 'stdClass'. A string representing a json-encoded value was expected.",
            ],
            'Single value class' => [
                [new filter('example')],
                TypeError::class,
                "The value supplied was of type '" . filter::class . "'. A string representing a json-encoded value was expected.",
            ],

            'json-encoded single value null' => [
                // Note a json-encoded null is the stringy 'null'.
                [json_encode(null)],
                InvalidArgumentException::class,
                "A json-encoded object containing both a direction, and comparison value was expected.",
            ],
            'json-encoded single value string' => [
                [json_encode('')],
                InvalidArgumentException::class,
                "The value supplied was a json encoded 'string'. " .
                    "An object containing both a direction, and comparison value was expected.",
            ],
            'json-encoded single value integer' => [
                [json_encode(42)],
                InvalidArgumentException::class,
                "The value supplied was a json encoded 'string'. " .
                    "An object containing both a direction, and comparison value was expected.",
            ],
            'json-encoded single value double' => [
                [json_encode(4.2)],
                InvalidArgumentException::class,
                "The value supplied was a json encoded 'string'. " .
                    "An object containing both a direction, and comparison value was expected.",
            ],
            'json-encoded single value bool' => [
                [json_encode(false)],
                InvalidArgumentException::class,
                "The value supplied was a json encoded 'string'. " .
                    "An object containing both a direction, and comparison value was expected.",
            ],
            'json-encoded single value array' => [
                [json_encode([])],
                InvalidArgumentException::class,
                "The value supplied was a json encoded 'string'. " .
                    "An object containing both a direction, and comparison value was expected.",
            ],

            'json-encoded empty object' => [
                [json_encode((object) [])],
                InvalidArgumentException::class,
                "A 'direction' must be provided.",
            ],
            'json-encoded single value class' => [
                // A class will contain any public properties when json-encoded. It is treated in the same was a stdClass.
                [json_encode(new filter('example'))],
                InvalidArgumentException::class,
                "A 'direction' must be provided.",
            ],

            'Direction provided, value missing' => [
                [json_encode([
                    'direction' => '>',
                ])],
                InvalidArgumentException::class,
                "A 'value' must be provided.",
            ],

            'Direction invalid +' => [
                [json_encode([
                    'direction' => '+',
                    'value' => 100,
                ])],
                InvalidArgumentException::class,
                "Invalid direction specified '+'."
            ],
            'Direction invalid -' => [
                [json_encode([
                    'direction' => '-',
                    'value' => 100,
                ])],
                InvalidArgumentException::class,
                "Invalid direction specified '-'."
            ],

            'Value string' => [
                [json_encode([
                    'direction' => '>',
                    'value' => "example",
                ])],
                TypeError::class,
                "The value supplied was of type 'string'. A numeric value was expected."
            ],
            'Value bool' => [
                [json_encode([
                    'direction' => '>',
                    'value' => false,
                ])],
                TypeError::class,
                "The value supplied was of type 'boolean'. A numeric value was expected."
            ],
            'Value array' => [
                [json_encode([
                    'direction' => '>',
                    'value' => [],
                ])],
                TypeError::class,
                "The value supplied was of type 'array'. A numeric value was expected."
            ],
            'Value stdClass' => [
                [json_encode([
                    'direction' => '>',
                    'value' => (object) [],
                ])],
                TypeError::class,
                "The value supplied was of type 'stdClass'. A numeric value was expected."
            ],
            'Value class' => [
                // A class will contain any public properties when json-encoded. It is treated in the same was a stdClass.
                [json_encode([
                    'direction' => '>',
                    'value' => new filter('example'),
                ])],
                TypeError::class,
                "The value supplied was of type 'stdClass'. A numeric value was expected."
            ],
        ];
    }
}
