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

namespace core_reportbuilder\local\report;

use advanced_testcase;
use lang_string;
use moodle_exception;
use core_reportbuilder\local\filters\text;

/**
 * Unit tests for a report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\report\filter
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class filter_test extends advanced_testcase {

    /**
     * Test getting filter class
     */
    public function test_get_filter_class(): void {
        $filter = $this->create_filter('username');
        $this->assertEquals(text::class, $filter->get_filter_class());
    }

    /**
     * Test specifying invalid filter class
     */
    public function test_invalid_filter_class(): void {
        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid filter (sillyclass)');
        new filter('sillyclass', 'username', new lang_string('username'), 'filter_testcase');
    }

    /**
     * Test getting name
     */
    public function test_get_name(): void {
        $filter = $this->create_filter('username');
        $this->assertEquals('username', $filter->get_name());
    }

    /**
     * Test getting header
     */
    public function test_get_header(): void {
        $filter = $this->create_filter('username');
        $this->assertEquals('Username', $filter->get_header());
    }

    /**
     * Test setting header
     */
    public function test_set_header(): void {
        $filter = $this->create_filter('username')
            ->set_header(new lang_string('firstname'));

        $this->assertEquals('First name', $filter->get_header());
    }

    /**
     * Test getting entity name
     */
    public function test_get_entity_name(): void {
        $filter = $this->create_filter('username');
        $this->assertEquals('filter_testcase', $filter->get_entity_name());
    }

    /**
     * Test getting unique identifier
     */
    public function test_get_unique_identifier(): void {
        $filter = $this->create_filter('username');
        $this->assertEquals('filter_testcase:username', $filter->get_unique_identifier());
    }

    /**
     * Test getting field SQL
     */
    public function test_get_field_sql(): void {
        $filter = $this->create_filter('username', 'u.username');
        $this->assertEquals('u.username', $filter->get_field_sql());
    }

    /**
     * Test getting field params
     */
    public function test_get_field_params(): void {
        $filter = $this->create_filter('username', 'u.username = :foo', ['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $filter->get_field_params());
    }

    /**
     * Test getting field SQL and params, while providing index for uniqueness
     */
    public function test_get_field_sql_and_params(): void {
        $filter = $this->create_filter('username', 'u.username = :username AND u.idnumber = :idnumber',
            ['username' => 'test', 'idnumber' => 'bar']);

        [$sql, $params] = $filter->get_field_sql_and_params(1);
        $this->assertEquals('u.username = :username_1 AND u.idnumber = :idnumber_1', $sql);
        $this->assertEquals(['username_1' => 'test', 'idnumber_1' => 'bar'], $params);
    }

    /**
     * Test is available
     */
    public function test_is_available(): void {
        $filter = $this->create_filter('username', 'u.username');
        $this->assertTrue($filter->get_is_available());

        $filter->set_is_available(true);
        $this->assertTrue($filter->get_is_available());
    }

    /**
     * Test setting filter options
     */
    public function test_set_options(): void {
        $filter = $this->create_filter('username', 'u.username')
            ->set_options([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $filter->get_options());
    }

    /**
     * Test setting filter options via callback
     */
    public function test_set_options_callback(): void {
        $filter = $this->create_filter('username', 'u.username')
            ->set_options_callback(static function() {
                return 10 * 5;
            });

        $this->assertEquals(50, $filter->get_options());
    }

    /**
     * Test restricting filter operators
     */
    public function test_limited_operators(): void {
        $filter = $this->create_filter('username', 'u.username')
            ->set_limited_operators([
                text::IS_EQUAL_TO,
                text::IS_NOT_EQUAL_TO,
            ]);

        $limitedoperators = $filter->restrict_limited_operators([
            text::CONTAINS => 'Contains',
            text::DOES_NOT_CONTAIN => 'Does not contain',
            text::IS_EQUAL_TO => 'Is equal to',
            text::IS_NOT_EQUAL_TO => 'Is not equal to',
        ]);

        $this->assertEquals([
            text::IS_EQUAL_TO => 'Is equal to',
            text::IS_NOT_EQUAL_TO => 'Is not equal to',
        ], $limitedoperators);
    }

    /**
     * Test not restricting filter operators
     */
    public function test_unlimited_operators(): void {
        $filter = $this->create_filter('username', 'u.username');

        $operators = [
            text::CONTAINS => 'Contains',
            text::DOES_NOT_CONTAIN => 'Does not contain',
        ];

        // If no operator limit has been set for the filter, then all available operators should be present.
        $this->assertEquals($operators, $filter->restrict_limited_operators($operators));
    }

    /**
     * Helper method to create a filter instance
     *
     * @param string $name
     * @param string $fieldsql
     * @param array $fieldparams
     * @return filter
     */
    private function create_filter(string $name, string $fieldsql = '', array $fieldparams = []): filter {
        return new filter(text::class, $name, new lang_string($name), 'filter_testcase', $fieldsql, $fieldparams);
    }
}
