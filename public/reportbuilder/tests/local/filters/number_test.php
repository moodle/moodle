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

declare(strict_types=1);

namespace core_reportbuilder\local\filters;

use advanced_testcase;
use lang_string;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for number report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\number
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class number_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter_simple}
     *
     * @return array[]
     */
    public static function get_sql_filter_simple_provider(): array {
        return [
            [number::ANY_VALUE, null, null, true],
            [number::IS_NOT_EMPTY, null, null, true],
            [number::IS_EMPTY, null, null, false],
            [number::LESS_THAN, 122.5, null, false],
            [number::LESS_THAN, 123, null, false],
            [number::LESS_THAN, 123.5, null, true],
            [number::GREATER_THAN, 122.5, null, true],
            [number::GREATER_THAN, 123, null, false],
            [number::GREATER_THAN, 123.5, null, false],
            [number::EQUAL_TO, 122, null, false],
            [number::EQUAL_TO, 123, null, true],
            [number::EQUAL_TO, 124, null, false],
            [number::EQUAL_OR_LESS_THAN, 122, null, false],
            [number::EQUAL_OR_LESS_THAN, 123, null, true],
            [number::EQUAL_OR_LESS_THAN, 124, null, true],
            [number::EQUAL_OR_GREATER_THAN, 122, null, true],
            [number::EQUAL_OR_GREATER_THAN, 123, null, true],
            [number::EQUAL_OR_GREATER_THAN, 124, null, false],
            [number::RANGE, 121, 122, false],
            [number::RANGE, 122, 124, true],
            [number::RANGE, 122, 123, true],
            [number::RANGE, 122.5, 123.5, true],
            [number::RANGE, 123, 124, true],
            [number::RANGE, 124, 125, false],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param float|null $value1
     * @param float|null $value2
     * @param bool $expectmatch
     *
     * @dataProvider get_sql_filter_simple_provider
     */
    public function test_get_sql_filter_simple(int $operator, ?float $value1, ?float $value2, bool $expectmatch): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'timecreated' => 123,
        ]);

        $filter = new filter(
            number::class,
            'test',
            new lang_string('course'),
            'testentity',
            'timecreated'
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = number::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_value1' => $value1,
            $filter->get_unique_identifier() . '_value2' => $value2,
            $filter->get_unique_identifier() . '_operator' => $operator,
        ]);

        $fullnames = $DB->get_fieldset_select('course', 'fullname', $select, $params);
        if ($expectmatch) {
            $this->assertContains($course->fullname, $fullnames);
        } else {
            $this->assertNotContains($course->fullname, $fullnames);
        }
    }

    /**
     * Data provider for {@see test_get_sql_filter_invalid}
     *
     * @return array[]
     */
    public static function get_sql_filter_invalid_provider(): array {
        return [
            [number::LESS_THAN],
            [number::GREATER_THAN],
            [number::EQUAL_TO],
            [number::EQUAL_OR_LESS_THAN],
            [number::EQUAL_OR_GREATER_THAN],
            [number::RANGE],
        ];
    }

    /**
     * Test getting filter SQL for operators that require values
     *
     * @param int $operator
     *
     * @dataProvider get_sql_filter_invalid_provider
     */
    public function test_get_sql_filter_invalid(int $operator): void {
        $filter = new filter(
            number::class,
            'test',
            new lang_string('course'),
            'testentity',
            'timecreated'
        );

        [$select, $params] = number::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
        ]);

        $this->assertEquals('', $select);
        $this->assertEquals([], $params);
    }
}
