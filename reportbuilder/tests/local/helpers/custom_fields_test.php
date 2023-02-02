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

use core_customfield_generator;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\helpers\user_filter_manager;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core_course\reportbuilder\datasource\courses;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for custom fields helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\custom_fields
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_fields_test extends core_reportbuilder_testcase {

    /**
     * Generate custom fields, one of each type
     *
     * @return custom_fields
     */
    private function generate_customfields(): custom_fields {

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $category = $generator->create_category([
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
            'contextid' => \context_system::instance()->id
        ]);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'text', 'name' => 'Text', 'shortname' => 'text']);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'checkbox', 'name' => 'Checkbox', 'shortname' => 'checkbox']);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'date', 'name' => 'Date', 'shortname' => 'date']);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'select', 'name' => 'Select', 'shortname' => 'select',
                'configdata' => ['options' => "Cat\nDog", 'defaultvalue' => 'Cat']]);

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

        $customfields = $this->generate_customfields();
        $columns = $customfields->get_columns();

        $this->assertCount(4, $columns);
        $this->assertContainsOnlyInstancesOf(column::class, $columns);

        [$column0, $column1, $column2, $column3] = $columns;
        $this->assertEqualsCanonicalizing(['Text', 'Checkbox', 'Date', 'Select'],
            [$column0->get_title(), $column1->get_title(), $column2->get_title(), $column3->get_title()]);

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

        $customfields = $this->generate_customfields();
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

        $customfields = $this->generate_customfields();
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

        $customfields = $this->generate_customfields();
        $filters = $customfields->get_filters();

        $this->assertCount(4, $filters);
        $this->assertContainsOnlyInstancesOf(filter::class, $filters);

        [$filter0, $filter1, $filter2, $filter3] = $filters;
        $this->assertEqualsCanonicalizing(['Text', 'Checkbox', 'Date', 'Select'],
            [$filter0->get_header(), $filter1->get_header(), $filter2->get_header(), $filter3->get_header()]);
    }

    /**
     * Test that adding custom field columns to a report returns expected values
     */
    public function test_custom_report_content(): void {
        $this->resetAfterTest();

        $this->generate_customfields();

        $course = $this->getDataGenerator()->create_course(['customfields' => [
            ['shortname' => 'text', 'value' => 'Hello'],
            ['shortname' => 'checkbox', 'value' => true],
            ['shortname' => 'date', 'value' => 1669852800],
            ['shortname' => 'select', 'value' => 2],
        ]]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 0]);

        // Add user profile field columns to the report.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_text']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_checkbox']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_date']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_select']);

        $content = $this->get_custom_report_content($report->get('id'));

        $this->assertEquals([
            $course->fullname,
            'Hello',
            'Yes',
            userdate(1669852800),
            'Dog'
        ], array_values($content[0]));
    }

    /**
     * Data provider for {@see test_custom_report_filter}
     *
     * @return array[]
     */
    public function custom_report_filter_provider(): array {
        return [
            'Filter by text custom field' => ['course:customfield_text', [
                'course:customfield_text_operator' => text::IS_EQUAL_TO,
                'course:customfield_text_value' => 'Hello',
            ], true],
            'Filter by text custom field (no match)' => ['course:customfield_text', [
                'course:customfield_text_operator' => text::IS_EQUAL_TO,
                'course:customfield_text_value' => 'Goodbye',
            ], false],
            'Filter by checkbox custom field' => ['course:customfield_checkbox', [
                'course:customfield_checkbox_operator' => boolean_select::CHECKED,
            ], true],
            'Filter by checkbox custom field (no match)' => ['course:customfield_checkbox', [
                'course:customfield_checkbox_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Filter by date custom field' => ['course:customfield_date', [
                'course:customfield_date_operator' => date::DATE_RANGE,
                'course:customfield_date_from' => 1622502000,
            ], true],
            'Filter by date custom field (no match)' => ['course:customfield_date', [
                'course:customfield_date_operator' => date::DATE_RANGE,
                'course:customfield_date_to' => 1622502000,
            ], false],
            'Filter by select custom field' => ['course:customfield_select', [
                'course:customfield_select_operator' => select::EQUAL_TO,
                'course:customfield_select_value' => 2,
            ], true],
            'Filter by select custom field (no match)' => ['course:customfield_select', [
                'course:customfield_select_operator' => select::EQUAL_TO,
                'course:customfield_select_value' => 1,
            ], false],
        ];
    }

    /**
     * Test filtering report by custom fields
     *
     * @param string $filtername
     * @param array $filtervalues
     * @param bool $expectmatch
     *
     * @dataProvider custom_report_filter_provider
     */
    public function test_custom_report_filter(string $filtername, array $filtervalues, bool $expectmatch): void {
        $this->resetAfterTest();

        $this->generate_customfields();

        $course = $this->getDataGenerator()->create_course(['customfields' => [
            ['shortname' => 'text', 'value' => 'Hello'],
            ['shortname' => 'checkbox', 'value' => true],
            ['shortname' => 'date', 'value' => 1669852800],
            ['shortname' => 'select', 'value' => 2],
        ]]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Users', 'source' => courses::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        user_filter_manager::set($report->get('id'), $filtervalues);

        $content = $this->get_custom_report_content($report->get('id'));

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals($course->fullname, reset($content[0]));
        } else {
            $this->assertEmpty($content);
        }
    }
}

