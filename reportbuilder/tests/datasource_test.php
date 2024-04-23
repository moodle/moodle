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
 * Unit tests for base datasource
 *
 * @package     core_reportbuilder
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_reportbuilder;

use advanced_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\{column, filter};
use lang_string;
use ReflectionClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for base datasource
 *
 * @package     core_reportbuilder
 * @coversDefaultClass \core_reportbuilder\datasource
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class datasource_test extends advanced_testcase {

    /**
     * Data provider for {@see test_add_columns_from_entity}
     *
     * @return array[]
     */
    public static function add_columns_from_entity_provider(): array {
        return [
            'All columns' => [
                [],
                [],
                4,
            ],
            'Include columns (first, extra1, extra2)' => [
                ['first', 'extra*'],
                [],
                3,
            ],
            'Exclude columns (first, extra1, extra2)' => [
                [],
                ['first', 'extra*'],
                1,
            ],
        ];
    }

    /**
     * Test adding columns from entity
     *
     * @param string[] $include
     * @param string[] $exclude
     * @param int $expectedcount
     *
     * @covers ::add_columns_from_entity
     *
     * @dataProvider add_columns_from_entity_provider
     */
    public function test_add_columns_from_entity(
        array $include,
        array $exclude,
        int $expectedcount,
    ): void {
        $instance = $this->get_datasource_test_source();

        $method = (new ReflectionClass($instance))->getMethod('add_columns_from_entity');
        $method->invoke($instance, 'datasource_test_entity', $include, $exclude);

        // Get all our entity columns.
        $columns = array_filter(
            $instance->get_columns(),
            fn(string $columnname) => strpos($columnname, 'datasource_test_entity:') === 0,
            ARRAY_FILTER_USE_KEY,
        );

        $this->assertCount($expectedcount, $columns);
    }

    /**
     * Data provider for {@see test_add_filters_from_entity}
     *
     * @return array[]
     */
    public static function add_filters_from_entity_provider(): array {
        return [
            'All filters' => [
                [],
                [],
                4,
            ],
            'Include filters (first, extra1, extra2)' => [
                ['first', 'extra*'],
                [],
                3,
            ],
            'Exclude filters (first, extra1, extra2)' => [
                [],
                ['first', 'extra*'],
                1,
            ],
        ];
    }

    /**
     * Test adding filters from entity
     *
     * @param string[] $include
     * @param string[] $exclude
     * @param int $expectedcount
     *
     * @covers ::add_filters_from_entity
     *
     * @dataProvider add_filters_from_entity_provider
     */
    public function test_add_filters_from_entity(
        array $include,
        array $exclude,
        int $expectedcount,
    ): void {
        $instance = $this->get_datasource_test_source();

        $method = (new ReflectionClass($instance))->getMethod('add_filters_from_entity');
        $method->invoke($instance, 'datasource_test_entity', $include, $exclude);

        // Get all our entity filters.
        $filters = array_filter(
            $instance->get_filters(),
            fn(string $filtername) => strpos($filtername, 'datasource_test_entity:') === 0,
            ARRAY_FILTER_USE_KEY,
        );

        $this->assertCount($expectedcount, $filters);
    }

    /**
     * Data provider for {@see test_add_conditions_from_entity}
     *
     * @return array[]
     */
    public static function add_conditions_from_entity_provider(): array {
        return [
            'All conditions' => [
                [],
                [],
                4,
            ],
            'Include conditions (first, extra1, extra2)' => [
                ['first', 'extra*'],
                [],
                3,
            ],
            'Exclude conditions (first, extra1, extra2)' => [
                [],
                ['first', 'extra*'],
                1,
            ],
        ];
    }

    /**
     * Test adding conditions from entity
     *
     * @param string[] $include
     * @param string[] $exclude
     * @param int $expectedcount
     *
     * @covers ::add_conditions_from_entity
     *
     * @dataProvider add_conditions_from_entity_provider
     */
    public function test_add_conditions_from_entity(
        array $include,
        array $exclude,
        int $expectedcount,
    ): void {
        $instance = $this->get_datasource_test_source();

        $method = (new ReflectionClass($instance))->getMethod('add_conditions_from_entity');
        $method->invoke($instance, 'datasource_test_entity', $include, $exclude);

        // Get all our entity conditions.
        $conditions = array_filter(
            $instance->get_conditions(),
            fn(string $conditionname) => strpos($conditionname, 'datasource_test_entity:') === 0,
            ARRAY_FILTER_USE_KEY,
        );

        $this->assertCount($expectedcount, $conditions);
    }

    /**
     * Test adding all from entity
     *
     * @covers ::add_all_from_entity
     */
    public function test_add_all_from_entity(): void {
        $instance = $this->get_datasource_test_source();

        $method = (new ReflectionClass($instance))->getMethod('add_all_from_entity');
        $method->invoke($instance, 'datasource_test_entity', ['first'], ['second'], ['extra1']);

        // Assert the column we added (plus one we didn't).
        $this->assertInstanceOf(column::class, $instance->get_column('datasource_test_entity:first'));
        $this->assertNull($instance->get_column('datasource_test_entity:second'));

        // Assert the filter we added (plus one we didn't).
        $this->assertInstanceOf(filter::class, $instance->get_filter('datasource_test_entity:second'));
        $this->assertNull($instance->get_filter('datasource_test_entity:first'));

        // Assert the condition we added (plus one we didn't).
        $this->assertInstanceOf(filter::class, $instance->get_condition('datasource_test_entity:extra1'));
        $this->assertNull($instance->get_condition('datasource_test_entity:extra2'));
    }

    /**
     * Create and return our test datasource instance
     *
     * @return datasource_test_source
     */
    protected function get_datasource_test_source(): datasource_test_source {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Test', 'source' => datasource_test_source::class, 'default' => 0]);

        /** @var datasource_test_source $instance */
        $instance = manager::get_report_from_persistent($report);
        return $instance;
    }
}

/**
 * Simple implementation of the base datasource
 */
class datasource_test_source extends datasource {

    protected function initialise(): void {
        $this->set_main_table('user', 'u');
        $this->annotate_entity('dummy', new lang_string('yes'));
        $this->add_column(new column('test', null, 'dummy'));

        // This is the entity from which we'll add our report elements.
        $this->add_entity(new datasource_test_entity());
    }

    public static function get_name(): string {
        return self::class;
    }

    public function get_default_columns(): array {
        return [];
    }

    public function get_default_filters(): array {
        return [];
    }

    public function get_default_conditions(): array {
        return [];
    }
}

/**
 * Simple implementation of the base entity
 */
class datasource_test_entity extends base {

    protected function get_default_tables(): array {
        return ['course'];
    }

    protected function get_default_entity_title(): lang_string {
        return new lang_string('course');
    }

    /**
     * We're going to add multiple columns/filters/conditions, each named as following:
     *
     * [first, second, extra1, extra2]
     *
     * @return base
     */
    public function initialise(): base {
        foreach (['first', 'second', 'extra1', 'extra2'] as $field) {
            $name = new lang_string('customfieldcolumn', 'core_reportbuilder', $field);

            $this->add_column(new column($field, $name, $this->get_entity_name()));
            $this->add_filter(new filter(text::class, $field, $name, $this->get_entity_name()));
            $this->add_condition(new filter(text::class, $field, $name, $this->get_entity_name()));
        }

        return $this;
    }
}
