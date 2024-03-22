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
use core_reportbuilder\local\entities\user;
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
class datasource_test extends advanced_testcase {

    /**
     * Data provider for {@see test_add_columns_from_entity}
     *
     * @return array[]
     */
    public static function add_columns_from_entity_provider(): array {
        return [
            'All column' => [
                [],
                [],
                31,
            ],
            'Include columns (picture, fullname, fullnamewithlink, fullnamewithpicture, fullnamewithpicturelink)' => [
                ['picture', 'fullname*'],
                [],
                5,
            ],
            'Exclude columns (picture, fullname, fullnamewithlink, fullnamewithpicture, fullnamewithpicturelink)' => [
                [],
                ['picture', 'fullname*'],
                26,
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
        $method->invoke($instance, 'user', $include, $exclude);

        // Get all our user entity columns.
        $columns = array_filter(
            $instance->get_columns(),
            fn(string $columnname) => strpos($columnname, 'user:') === 0,
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
                28,
            ],
            'Include filters (department, phone1, phone2)' => [
                ['department', 'phone*'],
                [],
                3,
            ],
            'Exclude filters (department, phone1, phone2)' => [
                [],
                ['department', 'phone*'],
                25,
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
        $method->invoke($instance, 'user', $include, $exclude);

        // Get all our user entity filters.
        $filters = array_filter(
            $instance->get_filters(),
            fn(string $filtername) => strpos($filtername, 'user:') === 0,
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
                28,
            ],
            'Include conditions (department, phone1, phone2)' => [
                ['department', 'phone*'],
                [],
                3,
            ],
            'Exclude conditions (department, phone1, phone2)' => [
                [],
                ['department', 'phone*'],
                25,
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
        $method->invoke($instance, 'user', $include, $exclude);

        // Get all our user entity conditions.
        $conditions = array_filter(
            $instance->get_conditions(),
            fn(string $conditionname) => strpos($conditionname, 'user:') === 0,
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
        $method->invoke($instance, 'user', ['username'], ['firstname'], ['lastname']);

        // Assert the column we added (plus one we didn't).
        $this->assertInstanceOf(column::class, $instance->get_column('user:username'));
        $this->assertNull($instance->get_column('user:email'));

        // Assert the filter we added (plus one we didn't).
        $this->assertInstanceOf(filter::class, $instance->get_filter('user:firstname'));
        $this->assertNull($instance->get_filter('user:email'));

        // Assert the condition we added (plus one we didn't).
        $this->assertInstanceOf(filter::class, $instance->get_condition('user:lastname'));
        $this->assertNull($instance->get_condition('user:email'));
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
        $this->add_entity(new user());
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
