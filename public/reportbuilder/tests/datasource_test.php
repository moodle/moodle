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
use core\exception\coding_exception;
use core\lang_string;
use core_reportbuilder_generator;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\{column, filter};
use ReflectionClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for base datasource
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\datasource
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
                [
                    'dummy:test',
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
            'Include columns (first, extra1, extra2)' => [
                ['first', 'extra*'],
                [],
                [
                    'dummy:test',
                    'entityone:first',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
            'Exclude columns (first, extra1, extra2)' => [
                [],
                ['first', 'extra*'],
                [
                    'dummy:test',
                    'entityone:second',
                ],
            ],
        ];
    }

    /**
     * Test adding columns from entity
     *
     * @param string[] $include
     * @param string[] $exclude
     * @param string[] $expectedcolumns
     *
     * @dataProvider add_columns_from_entity_provider
     */
    public function test_add_columns_from_entity(
        array $include,
        array $exclude,
        array $expectedcolumns,
    ): void {
        $instance = $this->get_datasource_test_source();

        // Assert we can pass the entity name when adding columns.
        $method = (new ReflectionClass($instance))->getMethod('add_columns_from_entity');
        $method->invoke($instance, 'entityone', $include, $exclude);

        $this->assertEquals(
            $expectedcolumns,
            array_map(
                fn(column $column) => $column->get_unique_identifier(),
                array_values($instance->get_columns()),
            ),
        );
    }

    /**
     * Test adding columns from entity instance
     */
    public function test_add_columns_from_entity_instance(): void {
        $instance = $this->get_datasource_test_source();

        // Get the entity instance.
        $method = (new ReflectionClass($instance))->getMethod('get_entity');
        $entity = $method->invoke($instance, 'entityone');

        // Assert we can pass the entity instance itself when adding columns.
        $method = (new ReflectionClass($instance))->getMethod('add_columns_from_entity');
        $method->invoke($instance, $entity, ['first']);

        $this->assertEquals(
            [
                'dummy:test',
                'entityone:first',
            ],
            array_map(
                fn(column $column) => $column->get_unique_identifier(),
                array_values($instance->get_columns()),
            ),
        );
    }

    /**
     * Test adding columns from entity that has not been added to report
     */
    public function test_add_columns_from_entity_invalid(): void {
        $instance = $this->get_datasource_test_source();
        $method = (new ReflectionClass($instance))->getMethod('add_columns_from_entity');

        // Invalid entity name.
        try {
            $method->invoke($instance, 'invalid');
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            $this->assertStringContainsString("Invalid entity name (invalid)", $exception->getMessage());
        }

        // Invalid entity instance.
        try {
            $method->invoke($instance, new datasource_test_entity());
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            $this->assertStringContainsString("Invalid entity name (datasource_test_entity)", $exception->getMessage());
        }
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
                [
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
            'Include filters (first, extra1, extra2)' => [
                ['first', 'extra*'],
                [],
                [
                    'entityone:first',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
            'Exclude filters (first, extra1, extra2)' => [
                [],
                ['first', 'extra*'],
                [
                    'entityone:second',
                ],
            ],
        ];
    }

    /**
     * Test adding filters from entity
     *
     * @param string[] $include
     * @param string[] $exclude
     * @param string[] $expectedfilters
     *
     * @dataProvider add_filters_from_entity_provider
     */
    public function test_add_filters_from_entity(
        array $include,
        array $exclude,
        array $expectedfilters,
    ): void {
        $instance = $this->get_datasource_test_source();

        // Assert we can pass the entity name when adding filters.
        $method = (new ReflectionClass($instance))->getMethod('add_filters_from_entity');
        $method->invoke($instance, 'entityone', $include, $exclude);

        $this->assertEquals(
            $expectedfilters,
            array_map(
                fn(filter $filter) => $filter->get_unique_identifier(),
                array_values($instance->get_filters()),
            ),
        );
    }

    /**
     * Test adding filters from entity instance
     */
    public function test_add_filters_from_entity_instance(): void {
        $instance = $this->get_datasource_test_source();

        // Get the entity instance.
        $method = (new ReflectionClass($instance))->getMethod('get_entity');
        $entity = $method->invoke($instance, 'entityone');

        // Assert we can pass the entity instance itself when adding filters.
        $method = (new ReflectionClass($instance))->getMethod('add_filters_from_entity');
        $method->invoke($instance, $entity, ['first']);

        $this->assertEquals(
            [
                'entityone:first',
            ],
            array_map(
                fn(filter $filter) => $filter->get_unique_identifier(),
                array_values($instance->get_filters()),
            ),
        );
    }

    /**
     * Test adding filters from entity that has not been added to report
     */
    public function test_add_filters_from_entity_invalid(): void {
        $instance = $this->get_datasource_test_source();
        $method = (new ReflectionClass($instance))->getMethod('add_filters_from_entity');

        // Invalid entity name.
        try {
            $method->invoke($instance, 'invalid');
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            $this->assertStringContainsString("Invalid entity name (invalid)", $exception->getMessage());
        }

        // Invalid entity instance.
        try {
            $method->invoke($instance, new datasource_test_entity());
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            $this->assertStringContainsString("Invalid entity name (datasource_test_entity)", $exception->getMessage());
        }
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
                [
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
            'Include conditions (first, extra1, extra2)' => [
                ['first', 'extra*'],
                [],
                [
                    'entityone:first',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
            'Exclude conditions (first, extra1, extra2)' => [
                [],
                ['first', 'extra*'],
                [
                    'entityone:second',
                ],
            ],
        ];
    }

    /**
     * Test adding conditions from entity
     *
     * @param string[] $include
     * @param string[] $exclude
     * @param string[] $expectedconditions
     *
     * @dataProvider add_conditions_from_entity_provider
     */
    public function test_add_conditions_from_entity(
        array $include,
        array $exclude,
        array $expectedconditions,
    ): void {
        $instance = $this->get_datasource_test_source();

        // Assert we can pass the entity name when adding conditions.
        $method = (new ReflectionClass($instance))->getMethod('add_conditions_from_entity');
        $method->invoke($instance, 'entityone', $include, $exclude);

        $this->assertEquals(
            $expectedconditions,
            array_map(
                fn(filter $condition) => $condition->get_unique_identifier(),
                array_values($instance->get_conditions()),
            ),
        );
    }

    /**
     * Test adding conditions from entity instance
     */
    public function test_add_conditions_from_entity_instance(): void {
        $instance = $this->get_datasource_test_source();

        // Get the entity instance.
        $method = (new ReflectionClass($instance))->getMethod('get_entity');
        $entity = $method->invoke($instance, 'entityone');

        // Assert we can pass the entity instance itself when adding conditions.
        $method = (new ReflectionClass($instance))->getMethod('add_conditions_from_entity');
        $method->invoke($instance, $entity, ['first']);

        $this->assertEquals(
            [
                'entityone:first',
            ],
            array_map(
                fn(filter $condition) => $condition->get_unique_identifier(),
                array_values($instance->get_conditions()),
            ),
        );
    }

    /**
     * Test adding conditions from entity that has not been added to report
     */
    public function test_add_conditions_from_entity_invalid(): void {
        $instance = $this->get_datasource_test_source();
        $method = (new ReflectionClass($instance))->getMethod('add_conditions_from_entity');

        // Invalid entity name.
        try {
            $method->invoke($instance, 'invalid');
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            $this->assertStringContainsString("Invalid entity name (invalid)", $exception->getMessage());
        }

        // Invalid entity instance.
        try {
            $method->invoke($instance, new datasource_test_entity());
            $this->fail('Exception expected');
        } catch (coding_exception $exception) {
            $this->assertStringContainsString("Invalid entity name (datasource_test_entity)", $exception->getMessage());
        }
    }

    /**
     * Test adding all from entity
     */
    public function test_add_all_from_entity(): void {
        $instance = $this->get_datasource_test_source();

        $method = (new ReflectionClass($instance))->getMethod('add_all_from_entity');
        $method->invoke($instance, 'entityone', ['first'], ['second'], ['extra1']);

        // Assert the column we added (plus one we didn't).
        $this->assertInstanceOf(column::class, $instance->get_column('entityone:first'));
        $this->assertNull($instance->get_column('entitytwo:second'));

        // Assert the filter we added (plus one we didn't).
        $this->assertInstanceOf(filter::class, $instance->get_filter('entityone:second'));
        $this->assertNull($instance->get_filter('entitytwo:first'));

        // Assert the condition we added (plus one we didn't).
        $this->assertInstanceOf(filter::class, $instance->get_condition('entityone:extra1'));
        $this->assertNull($instance->get_condition('entitytwo:extra2'));
    }

    /**
     * Data provider for {@see get_datasource_test_source}
     *
     * @return array[]
     */
    public static function add_all_from_entities_provider(): array {
        return [
            'All' => [
                [],
                [
                    'dummy:test',
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                    'entitytwo:first',
                    'entitytwo:second',
                    'entitytwo:extra1',
                    'entitytwo:extra2',
                    'entitythree:first',
                    'entitythree:second',
                    'entitythree:extra1',
                    'entitythree:extra2',
                ],
                [
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                    'entitytwo:first',
                    'entitytwo:second',
                    'entitytwo:extra1',
                    'entitytwo:extra2',
                    'entitythree:first',
                    'entitythree:second',
                    'entitythree:extra1',
                    'entitythree:extra2',
                ],
                [
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                    'entitytwo:first',
                    'entitytwo:second',
                    'entitytwo:extra1',
                    'entitytwo:extra2',
                    'entitythree:first',
                    'entitythree:second',
                    'entitythree:extra1',
                    'entitythree:extra2',
                ],
            ],
            'Multiple entities' => [
                ['entitythree', 'entityone'],
                [
                    'dummy:test',
                    'entitythree:first',
                    'entitythree:second',
                    'entitythree:extra1',
                    'entitythree:extra2',
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
                [
                    'entitythree:first',
                    'entitythree:second',
                    'entitythree:extra1',
                    'entitythree:extra2',
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
                [
                    'entitythree:first',
                    'entitythree:second',
                    'entitythree:extra1',
                    'entitythree:extra2',
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
            'Single entity' => [
                ['entityone'],
                [
                    'dummy:test',
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
                [
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
                [
                    'entityone:first',
                    'entityone:second',
                    'entityone:extra1',
                    'entityone:extra2',
                ],
            ],
        ];
    }

    /**
     * Test adding from all entities
     *
     * @param string[] $entitynames
     * @param string[] $expectedcolumns
     * @param string[] $expectedfilters
     * @param string[] $expectedconditions
     *
     * @dataProvider add_all_from_entities_provider
     */
    public function test_add_all_from_entities(
        array $entitynames,
        array $expectedcolumns,
        array $expectedfilters,
        array $expectedconditions,
    ): void {
        $instance = $this->get_datasource_test_source();

        $method = (new ReflectionClass($instance))->getMethod('add_all_from_entities');
        $method->invoke($instance, $entitynames);

        // Get all our entity columns.
        $this->assertEquals(
            $expectedcolumns,
            array_map(
                fn(column $column) => $column->get_unique_identifier(),
                array_values($instance->get_columns()),
            ),
        );

        // Get all our entity filters.
        $this->assertEquals(
            $expectedfilters,
            array_map(
                fn(filter $filter) => $filter->get_unique_identifier(),
                array_values($instance->get_filters()),
            ),
        );

        // Get all our entity conditions.
        $this->assertEquals(
            $expectedconditions,
            array_map(
                fn(filter $condition) => $condition->get_unique_identifier(),
                array_values($instance->get_conditions()),
            ),
        );
    }

    /**
     * Test getting active conditions
     */
    public function test_get_active_conditions(): void {
        $instance = $this->get_datasource_test_source();

        $method = (new ReflectionClass($instance))->getMethod('add_conditions_from_entity');
        $method->invoke($instance, 'entityone');

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $reportid = $instance->get_report_persistent()->get('id');
        $generator->create_condition(['reportid' => $reportid, 'uniqueidentifier' => 'entityone:first']);
        $generator->create_condition(['reportid' => $reportid, 'uniqueidentifier' => 'entityone:second']);

        // Set the second condition as unavailable.
        $instance->get_condition('entityone:second')->set_is_available(false);

        $this->assertEquals([
            'entityone:first',
        ], array_keys($instance->get_active_conditions(true)));

        // Ensure report elements are reloaded.
        $instance::report_elements_modified($reportid);

        $this->assertEquals([
            'entityone:first',
            'entityone:second',
        ], array_keys($instance->get_active_conditions(false)));
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

        // Because we must have at least one column in the report.
        $this->annotate_entity('dummy', new lang_string('yes'));
        $this->add_column(new column('test', null, 'dummy'));

        // These are the entities from which we'll add additional report elements.
        $this->add_entity((new datasource_test_entity())->set_entity_name('entityone'));
        $this->add_entity((new datasource_test_entity())->set_entity_name('entitytwo'));
        $this->add_entity((new datasource_test_entity())->set_entity_name('entitythree'));
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
