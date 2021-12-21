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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for custom fields helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\custom_fields
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_fields_test extends advanced_testcase {

    /**
     * Generate a course with customfields
     */
    public function generate_course_with_customfields(): custom_fields {
        $course = $this->getDataGenerator()->create_course();

        // Add some customfields to the course.
        $cfgenerator = self::getDataGenerator()->get_plugin_generator('core_customfield');
        $params = [
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
            'contextid' => \context_system::instance()->id
        ];
        $category = $cfgenerator->create_category($params);
        $field1 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'text', 'name' => 'Customfield text 1', 'shortname' => 'cf1']);
        $cfgenerator->add_instance_data($field1, (int)$course->id, 'C-3PO');
        $field2 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'text', 'name' => 'Customfield text 2', 'shortname' => 'cf2']);
        $cfgenerator->add_instance_data($field2, (int)$course->id, 'R2-D2');

        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');

        // Create an instance of the customfields helper.
        return new custom_fields($coursealias . '.id', $courseentity->get_entity_name(),
            'core_course', 'course');
    }

    /**
     * Test for get_columns
     */
    public function test_get_columns(): void {
        $this->resetAfterTest();

        $customfields = $this->generate_course_with_customfields();
        $columns = $customfields->get_columns();
        $this->assertCount(2, $columns);
        [$column0, $column1] = $columns;
        $this->assertInstanceOf(column::class, $column0);
        $this->assertInstanceOf(column::class, $column1);
        $this->assertEqualsCanonicalizing(['Customfield text 1', 'Customfield text 2'],
            [$column0->get_title(), $column1->get_title()]);
        $this->assertEquals(column::TYPE_TEXT, $column0->get_type());
        $this->assertEquals('course', $column0->get_entity_name());
        $this->assertStringStartsWith('LEFT JOIN {customfield_data}', $column0->get_joins()[0]);
        // Column of type TEXT is sortable.
        $this->assertTrue($column0->get_is_sortable());
    }

    /**
     * Test for add_join
     */
    public function test_add_join(): void {
        $this->resetAfterTest();

        $customfields = $this->generate_course_with_customfields();
        $columns = $customfields->get_columns();
        $this->assertCount(1, ($columns[0])->get_joins());

        $customfields->add_join('JOIN {test} t ON t.id = id');
        $columns = $customfields->get_columns();
        $this->assertCount(2, ($columns[0])->get_joins());
    }

    /**
     * Test for add_joins
     */
    public function test_add_joins(): void {
        $this->resetAfterTest();

        $customfields = $this->generate_course_with_customfields();
        $columns = $customfields->get_columns();
        $this->assertCount(1, ($columns[0])->get_joins());

        $customfields->add_joins(['JOIN {test} t ON t.id = id', 'JOIN {test2} t2 ON t2.id = id']);
        $columns = $customfields->get_columns();
        $this->assertCount(3, ($columns[0])->get_joins());
    }

    /**
     * Test for get_filters
     */
    public function test_get_filters(): void {
        $this->resetAfterTest();

        $customfields = $this->generate_course_with_customfields();
        $filters = $customfields->get_filters();
        $this->assertCount(2, $filters);
        [$filter0, $filter1] = $filters;
        $this->assertInstanceOf(filter::class, $filter0);
        $this->assertInstanceOf(filter::class, $filter1);
        $this->assertEqualsCanonicalizing(['Customfield text 1', 'Customfield text 2'],
            [$filter0->get_header(), $filter1->get_header()]);
    }
}

