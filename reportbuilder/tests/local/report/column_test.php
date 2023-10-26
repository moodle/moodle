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
use coding_exception;
use lang_string;
use stdClass;
use core_reportbuilder\local\helpers\database;

/**
 * Unit tests for a report column
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\report\column
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_test extends advanced_testcase {

    /**
     * Test column name getter/setter
     */
    public function test_name(): void {
        $column = $this->create_column('test');
        $this->assertEquals('test', $column->get_name());

        $this->assertEquals('another', $column
            ->set_name('another')
            ->get_name()
        );
    }

    /**
     * Test column title getter/setter
     */
    public function test_title(): void {
        $column = $this->create_column('test', new lang_string('show'));
        $this->assertEquals('Show', $column->get_title());
        $this->assertFalse($column->has_custom_title());

        $this->assertEquals('Hide', $column
            ->set_title(new lang_string('hide'))
            ->get_title()
        );
        $this->assertTrue($column->has_custom_title());

        // Column titles can also be empty.
        $this->assertEmpty($column
            ->set_title(null)
            ->get_title());
    }

    /**
     * Test entity name getter
     */
    public function test_get_entity_name(): void {
        $column = $this->create_column('test', null, 'entityname');
        $this->assertEquals('entityname', $column->get_entity_name());
    }

    /**
     * Test getting unique identifier
     */
    public function test_get_unique_identifier(): void {
        $column = $this->create_column('test', null, 'entityname');
        $this->assertEquals('entityname:test', $column->get_unique_identifier());
    }

    /**
     * Test column type getter/setter
     */
    public function test_type(): void {
        $column = $this->create_column('test');
        $this->assertEquals(column::TYPE_INTEGER, $column
            ->set_type(column::TYPE_INTEGER)
            ->get_type());
    }

    /**
     * Test column default type
     */
    public function test_type_default(): void {
        $column = $this->create_column('test');
        $this->assertEquals(column::TYPE_TEXT, $column->get_type());
    }

    /**
     * Test column type with invalid value
     */
    public function test_type_invalid(): void {
        $column = $this->create_column('test');

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid column type');
        $column->set_type(-1);
    }

    /**
     * Test adding single join
     */
    public function test_add_join(): void {
        $column = $this->create_column('test');
        $this->assertEquals([], $column->get_joins());

        $column->add_join('JOIN {user} u ON u.id = table.userid');
        $this->assertEquals(['JOIN {user} u ON u.id = table.userid'], $column->get_joins());
    }

    /**
     * Test adding multiple joins
     */
    public function test_add_joins(): void {
        $tablejoins = [
            "JOIN {course} c2 ON c2.id = c1.id",
            "JOIN {course} c3 ON c3.id = c1.id",
        ];

        $column = $this->create_column('test')
            ->add_joins($tablejoins);

        $this->assertEquals($tablejoins, $column->get_joins());
    }

    /**
     * Data provider for {@see test_add_field}
     *
     * @return array
     */
    public function add_field_provider(): array {
        return [
            ['foo', '', ['foo AS c1_foo']],
            ['foo', 'bar', ['foo AS c1_bar']],
            ['t.foo', '', ['t.foo AS c1_foo']],
            ['t.foo', 'bar', ['t.foo AS c1_bar']],
        ];
    }

    /**
     * Test adding single field, and retrieving it
     *
     * @param string $sql
     * @param string $alias
     * @param array $expectedselect
     *
     * @dataProvider add_field_provider
     */
    public function test_add_field(string $sql, string $alias, array $expectedselect): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field($sql, $alias);

        $this->assertEquals($expectedselect, $column->get_fields());
    }

    /**
     * Test adding params to field, and retrieving them
     */
    public function test_add_field_with_params(): void {
        [$param0, $param1] = database::generate_param_names(2);

        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field(":{$param0}", 'foo', [$param0 => 'foo'])
            ->add_field(":{$param1}", 'bar', [$param1 => 'bar']);

        // Select will look like the following: "p<index>_rbparam<counter>", where index is the column index and counter is
        // a static value of the report helper class.
        $fields = $column->get_fields();
        $this->assertCount(2, $fields);

        preg_match('/:(?<paramname>p1_rbparam[\d]+) AS c1_foo/', $fields[0], $matches);
        $this->assertArrayHasKey('paramname', $matches);
        $fieldparam0 = $matches['paramname'];

        preg_match('/:(?<paramname>p1_rbparam[\d]+) AS c1_bar/', $fields[1], $matches);
        $this->assertArrayHasKey('paramname', $matches);
        $fieldparam1 = $matches['paramname'];

        // Ensure column parameters have been renamed appropriately.
        $this->assertEquals([
            $fieldparam0 => 'foo',
            $fieldparam1 => 'bar',
        ], $column->get_params());
    }

    /**
     * Test adding field with alias as part of SQL throws an exception
     */
    public function test_add_field_alias_in_sql(): void {
        $column = $this->create_column('test')
            ->set_index(1);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Column alias must be passed as a separate argument');
        $column->add_field('foo AS bar');
    }

    /**
     * Test adding field with complex SQL without an alias throws an exception
     */
    public function test_add_field_complex_without_alias(): void {
        global $DB;

        $column = $this->create_column('test')
            ->set_index(1);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Complex columns must have an alias');
        $column->add_field($DB->sql_concat('foo', 'bar'));
    }

    /**
     * Data provider for {@see test_add_fields}
     *
     * @return array
     */
    public function add_fields_provider(): array {
        return [
            ['t.foo', ['t.foo AS c1_foo']],
            ['t.foo bar', ['t.foo AS c1_bar']],
            ['t.foo AS bar', ['t.foo AS c1_bar']],
            ['t.foo1, t.foo2 bar, t.foo3 AS baz', ['t.foo1 AS c1_foo1', 't.foo2 AS c1_bar', 't.foo3 AS c1_baz']],
        ];
    }

    /**
     * Test adding fields to a column, and retrieving them
     *
     * @param string $sql
     * @param array $expectedselect
     *
     * @dataProvider add_fields_provider
     */
    public function test_add_fields(string $sql, array $expectedselect): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_fields($sql);

        $this->assertEquals($expectedselect, $column->get_fields());
    }

    /**
     * Test column alias
     */
    public function test_column_alias(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_fields('t.foo, t.bar');

        $this->assertEquals('c1_foo', $column->get_column_alias());
    }

    /**
     * Test column alias with a field containing an alias
     */
    public function test_column_alias_with_field_alias(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field('COALESCE(t.foo, t.bar)', 'lionel');

        $this->assertEquals('c1_lionel', $column->get_column_alias());
    }

    /**
     * Test alias of column without any fields throws exception
     */
    public function test_column_alias_no_fields(): void {
        $column = $this->create_column('test');

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Column ' . $column->get_unique_identifier() . ' contains no fields');
        $column->add_field($column->get_column_alias());
    }

    /**
     * Test setting column group by SQL
     */
    public function test_set_groupby_sql(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field('COALESCE(t.foo, t.bar)', 'lionel')
            ->set_groupby_sql('t.id');

        $this->assertEquals(['t.id'], $column->get_groupby_sql());
    }

    /**
     * Test getting default column group by SQL
     */
    public function test_get_groupby_sql(): void {
        global $DB;

        $column = $this->create_column('test')
            ->set_index(1)
            ->add_fields('t.foo, t.bar');

        // The behaviour of this method differs due to DB limitations.
        $usealias = in_array($DB->get_dbfamily(), ['mysql', 'postgres']);
        if ($usealias) {
            $expected = ['c1_foo', 'c1_bar'];
        } else {
            $expected = ['t.foo', 't.bar'];
        }

        $this->assertEquals($expected, $column->get_groupby_sql());
    }

    /**
     * Data provider for {@see test_get_default_value} and {@see test_format_value}
     *
     * @return array[]
     */
    public function column_type_provider(): array {
        return [
            [column::TYPE_INTEGER, 42],
            [column::TYPE_TEXT, 'Hello'],
            [column::TYPE_TIMESTAMP, HOURSECS],
            [column::TYPE_BOOLEAN, 1, true],
            [column::TYPE_FLOAT, 1.23],
            [column::TYPE_LONGTEXT, 'Amigos'],
        ];
    }

    /**
     * Test default value is returned from selected values, with correct type
     *
     * @param int $columntype
     * @param mixed $value
     * @param mixed|null $expected Expected value, or null to indicate it should be identical to value
     *
     * @dataProvider column_type_provider
     */
    public function test_get_default_value(int $columntype, $value, $expected = null): void {
        $defaultvalue = column::get_default_value([
            'value' => $value,
            'foo' => 'bar',
        ], $columntype);

        $this->assertSame($expected ?? $value, $defaultvalue);
    }

    /**
     * Test that column value is returned correctly, with correct type
     *
     * @param int $columntype
     * @param mixed $value
     * @param mixed|null $expected Expected value, or null to indicate it should be identical to value
     *
     * @dataProvider column_type_provider
     */
    public function test_format_value(int $columntype, $value, $expected = null): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->set_type($columntype)
            ->add_field('t.foo');

        $this->assertSame($expected ?? $value, $column->format_value([
            'c1_foo' => $value,
        ]));
    }

    /**
     * Test that column value with callback is returned
     */
    public function test_format_value_callback(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field('t.foo')
            ->set_type(column::TYPE_INTEGER)
            ->add_callback(static function(int $value, stdClass $values) {
                return $value * 2;
            });

        $this->assertEquals(84, $column->format_value([
            'c1_bar' => 10,
            'c1_foo' => 42,
        ]));
    }

    /**
     * Test that column value with callback (using all fields) is returned
     */
    public function test_format_value_callback_fields(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_fields('t.foo, t.baz')
            ->set_type(column::TYPE_INTEGER)
            ->add_callback(static function(int $value, stdClass $values) {
                return $values->foo + $values->baz;
            });

        $this->assertEquals(60, $column->format_value([
            'c1_bar' => 10,
            'c1_foo' => 42,
            'c1_baz' => 18,
        ]));
    }

    /**
     * Test that column value with callback (using arguments) is returned
     */
    public function test_format_value_callback_arguments(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field('t.foo')
            ->set_type(column::TYPE_INTEGER)
            ->add_callback(static function(int $value, stdClass $values, int $argument) {
                return $value - $argument;
            }, 10);

        $this->assertEquals(32, $column->format_value([
            'c1_bar' => 10,
            'c1_foo' => 42,
        ]));
    }

    /**
     * Test adding multiple callbacks to a column
     */
    public function test_add_multiple_callback(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field('t.foo')
            ->set_type(column::TYPE_TEXT)
            ->add_callback(static function(string $value): string {
                return strrev($value);
            })
            ->add_callback(static function(string $value): string {
                return strtoupper($value);
            });

        $this->assertEquals('LIONEL', $column->format_value([
            'c1_foo' => 'lenoil',
        ]));
    }

    /**
     * Test that setting column callback overwrites previous callbacks
     */
    public function test_set_callback(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field('t.foo')
            ->set_type(column::TYPE_TEXT)
            ->add_callback(static function(string $value): string {
                return strrev($value);
            })
            ->set_callback(static function(string $value): string {
                return strtoupper($value);
            });

        $this->assertEquals('LENOIL', $column->format_value([
            'c1_foo' => 'lenoil',
        ]));
    }

    /**
     * Test is sortable
     */
    public function test_is_sortable(): void {
        $column = $this->create_column('test');
        $this->assertFalse($column->get_is_sortable());

        $column->set_is_sortable(true);
        $this->assertTrue($column->get_is_sortable());
    }

    /**
     * Test retrieving sort fields
     */
    public function test_get_sortfields(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_fields('t.foo, t.bar, t.baz')
            ->set_is_sortable(true, ['t.baz', 't.bar']);

        $this->assertEquals(['c1_baz', 'c1_bar'], $column->get_sort_fields());
    }

    /**
     * Test retrieving sort fields when an aliased field is set as sortable
     */
    public function test_get_sortfields_with_field_alias(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_field('t.foo')
            ->add_field('COALESCE(t.foo, t.bar)', 'lionel')
            ->set_is_sortable(true, ['lionel']);

        $this->assertEquals(['c1_lionel'], $column->get_sort_fields());
    }

    /**
     * Test retrieving sort fields when an unknown field is set as sortable
     */
    public function test_get_sortfields_unknown_field(): void {
        $column = $this->create_column('test')
            ->set_index(1)
            ->add_fields('t.foo')
            ->set_is_sortable(true, ['t.baz']);

        $this->assertEquals(['t.baz'], $column->get_sort_fields());
    }

    /**
     * Test is available
     */
    public function test_is_available(): void {
        $column = $this->create_column('test');
        $this->assertTrue($column->get_is_available());

        $column->set_is_available(true);
        $this->assertTrue($column->get_is_available());
    }

    /**
     * Helper method to create a column instance
     *
     * @param string $name
     * @param lang_string|null $title
     * @param string $entityname
     * @return column
     */
    private function create_column(string $name, ?lang_string $title = null, string $entityname = 'column_testcase'): column {
        return new column($name, $title, $entityname);
    }
}
