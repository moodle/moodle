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
 * Unit tests for base entity
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_reportbuilder\local\entities;

use advanced_testcase;
use coding_exception;
use lang_string;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for base entity
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\entities\base
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class base_test extends advanced_testcase {

    /**
     * Test entity table alias
     */
    public function test_get_table_alias(): void {
        $entity = new base_test_entity();

        $mytablealias = $entity->get_table_alias('mytable');
        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', $mytablealias);

        $myothertablealias = $entity->get_table_alias('myothertable');
        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', $myothertablealias);

        // They must differ.
        $this->assertNotEquals($mytablealias, $myothertablealias);

        // Re-request both, ensure they are identical to what we previously received.
        $this->assertEquals($mytablealias, $entity->get_table_alias('mytable'));
        $this->assertEquals($myothertablealias, $entity->get_table_alias('myothertable'));
    }

    /**
     * Test for invalid get table alias
     */
    public function test_get_table_alias_invalid(): void {
        $entity = new base_test_entity();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: ' .
            'Invalid table name (nonexistingalias)');
        $entity->get_table_alias('nonexistingalias');
    }

    /**
     * Test getting all table aliases
     */
    public function test_get_table_aliases(): void {
        $entity = new base_test_entity();

        [
            'mytable' => $mytablealias,
            'myothertable' => $myothertablealias,
        ] = $entity->get_table_aliases();

        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', $mytablealias);
        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', $myothertablealias);

        // They must differ.
        $this->assertNotEquals($mytablealias, $myothertablealias);
    }

    /**
     * Test setting table alias
     */
    public function test_set_table_alias(): void {
        $entity = new base_test_entity();

        $entity->set_table_alias('mytable', 'newalias');
        $this->assertEquals('newalias', $entity->get_table_alias('mytable'));
    }

    /**
     * Test invalid entity set table alias
     */
    public function test_set_table_alias_invalid(): void {
        $entity = new base_test_entity();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: Invalid table name (nonexistent)');
        $entity->set_table_alias('nonexistent', 'newalias');
    }

    /**
     * Test setting multiple table aliases
     */
    public function test_set_table_aliases(): void {
        $entity = new base_test_entity();

        $entity->set_table_aliases([
            'mytable' => 'newalias',
            'myothertable' => 'newalias2',
        ]);
        $this->assertEquals([
            'mytable' => 'newalias',
            'myothertable' => 'newalias2',
        ], $entity->get_table_aliases());
    }

    /**
     * Test setting multiple table aliases, containing an invalid table
     */
    public function test_set_table_aliases_invalid(): void {
        $entity = new base_test_entity();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: Invalid table name (nonexistent)');
        $entity->set_table_aliases([
            'mytable' => 'newalias',
            'nonexistent' => 'newalias2',
        ]);
    }

    /**
     * Test setting table join alias
     */
    public function test_set_table_join_alias(): void {
        $entity = new base_test_entity();

        $entity->set_table_join_alias('mytable', 'newalias');
        $this->assertTrue($entity->has_table_join_alias('mytable'));
        $this->assertEquals('newalias', $entity->get_table_alias('mytable'));
    }

    /**
     * Test that entity doesn't have table join alias by default
     *
     * {@see test_set_table_join_alias} for assertion where it does
     */
    public function test_has_table_join_alias(): void {
        $entity = new base_test_entity();
        $this->assertFalse($entity->has_table_join_alias('mytable'));
    }

    /**
     * Test entity name
     */
    public function test_set_entity_name(): void {
        $entity = new base_test_entity();

        $this->assertEquals('base_test_entity', $entity->get_entity_name());

        $entity->set_entity_name('newentityname');
        $this->assertEquals('newentityname', $entity->get_entity_name());
    }

    /**
     * Test entity title
     */
    public function test_set_entity_title(): void {
        $entity = new base_test_entity();

        $this->assertEquals(new lang_string('yes'), $entity->get_entity_title());

        $newtitle = new lang_string('fullname');
        $entity->set_entity_title($newtitle);
        $this->assertEquals($newtitle, $entity->get_entity_title());
    }

    /**
     * Test getting column
     */
    public function test_get_column(): void {
        $entity = (new base_test_entity())->initialise();

        $column = $entity->get_column('test');
        $this->assertEquals('base_test_entity:test', $column->get_unique_identifier());
    }

    /**
     * Test for invalid get column
     */
    public function test_get_column_invalid(): void {
        $entity = (new base_test_entity())->initialise();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: ' .
            'Invalid column name (nonexistingcolumn)');
        $entity->get_column('nonexistingcolumn');
    }

    /**
     * Test getting columns
     */
    public function test_get_columns(): void {
        $entity = (new base_test_entity())->initialise();

        $columns = $entity->get_columns();
        $this->assertCount(1, $columns);
        $this->assertContainsOnlyInstancesOf(column::class, $columns);
    }

    /**
     * Test getting filter
     */
    public function test_get_filter(): void {
        $entity = (new base_test_entity())->initialise();

        $filter = $entity->get_filter('test');
        $this->assertEquals('base_test_entity:test', $filter->get_unique_identifier());
    }

    /**
     * Test for invalid get filter
     */
    public function test_get_filter_invalid(): void {
        $entity = (new base_test_entity())->initialise();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: ' .
            'Invalid filter name (nonexistingfilter)');
        $entity->get_filter('nonexistingfilter');
    }

    /**
     * Test getting filters
     */
    public function test_get_filters(): void {
        $entity = (new base_test_entity())->initialise();

        $filters = $entity->get_filters();
        $this->assertCount(1, $filters);
        $this->assertContainsOnlyInstancesOf(filter::class, $filters);
    }

    /**
     * Test getting condition
     */
    public function test_get_condition(): void {
        $entity = (new base_test_entity())->initialise();

        $condition = $entity->get_condition('test');
        $this->assertEquals('base_test_entity:test', $condition->get_unique_identifier());
    }

    /**
     * Test for invalid get condition
     */
    public function test_get_condition_invalid(): void {
        $entity = (new base_test_entity())->initialise();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: ' .
            'Invalid condition name (nonexistingcondition)');
        $entity->get_condition('nonexistingcondition');
    }

    /**
     * Test getting conditions
     */
    public function test_get_conditions(): void {
        $entity = (new base_test_entity())->initialise();

        $conditions = $entity->get_conditions();
        $this->assertCount(1, $conditions);
        $this->assertContainsOnlyInstancesOf(filter::class, $conditions);
    }
}

/**
 * Simple implementation of the base entity
 */
class base_test_entity extends base {

    /**
     * Table aliases
     *
     * @return array
     */
    protected function get_default_tables(): array {
        return [
            'mytable',
            'myothertable',
        ];
    }

    /**
     * Entity title
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('yes');
    }

    /**
     * Initialise entity
     *
     * @return base
     */
    public function initialise(): base {
        $column = (new column(
            'test',
            new lang_string('no'),
            $this->get_entity_name()
        ))
            ->add_field('no');

        $filter = (new filter(
            text::class,
            'test',
            new lang_string('no'),
            $this->get_entity_name(),
        ))
            ->set_field_sql('no');

        return $this
            ->add_column($column)
            ->add_filter($filter)
            ->add_condition($filter);
    }
}
