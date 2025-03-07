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
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\filters\{boolean_select, date, number, select, text};
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\tests\core_reportbuilder_testcase;
use core_course\reportbuilder\datasource\{categories, courses};

/**
 * Unit tests for custom fields helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\custom_fields
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_fields_test extends core_reportbuilder_testcase {

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
            ['categoryid' => $category->get('id'), 'type' => 'text', 'name' => 'Text', 'shortname' => 'text',
                'configdata' => ['defaultvalue' => 'default']]);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'textarea', 'name' => 'Textarea', 'shortname' => 'textarea',
                'configdata' => ['defaultvalue' => 'Default']]);

        // This field is available only to course teachers.
        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'checkbox', 'name' => 'Checkbox', 'shortname' => 'checkbox',
                'configdata' => ['checkbydefault' => 1, 'visibility' => 1]]);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'date', 'name' => 'Date', 'shortname' => 'date']);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'select', 'name' => 'Select', 'shortname' => 'select',
                'configdata' => ['options' => "Cat\nDog\nFish", 'defaultvalue' => 'Cat']]);

        $generator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'number', 'name' => 'Number', 'shortname' => 'number',
                'configdata' => ['defaultvalue' => 1]]);

        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');

        // Create an instance of the customfields helper.
        return new custom_fields("{$coursealias}.id", $courseentity->get_entity_name(), 'core_course', 'course');
    }

    /**
     * Test for get_columns
     */
    public function test_get_columns(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $customfields = $this->generate_customfields();

        $columns = $customfields->get_columns();
        $this->assertCount(6, $columns);
        $this->assertContainsOnlyInstancesOf(column::class, $columns);

        // Column titles.
        $this->assertEquals([
            'Text',
            'Textarea',
            'Checkbox',
            'Date',
            'Select',
            'Number',
        ], array_map(
            fn(column $column) => $column->get_title(),
            $columns,
        ));

        // Column types.
        $this->assertEquals([
            column::TYPE_TEXT,
            column::TYPE_LONGTEXT,
            column::TYPE_BOOLEAN,
            column::TYPE_TIMESTAMP,
            column::TYPE_TEXT,
            column::TYPE_FLOAT,
        ], array_map(
            fn(column $column) => $column->get_type(),
            $columns,
        ));

        // Column sortable.
        $this->assertEquals([
            true,
            true,
            true,
            true,
            true,
            true,
        ], array_map(
            fn(column $column) => $column->get_is_sortable(),
            $columns,
        ));

        // Column available.
        $this->assertEquals([
            true,
            true,
            true,
            true,
            true,
            true,
        ], array_map(
            fn(column $column) => $column->get_is_available(),
            $columns,
        ));

        // Column available, for non-privileged user.
        $this->setUser(null);
        $this->assertEquals([
            true,
            true,
            false,
            true,
            true,
            true,
        ], array_map(
            fn(column $column) => $column->get_is_available(),
            $customfields->get_columns(),
        ));
    }

    /**
     * Test that joins added to the custom fields helper are present in its columns/filters
     */
    public function test_add_join(): void {
        $this->resetAfterTest();

        $customfields = $this->generate_customfields();

        // We always join on the customfield data table.
        $columnjoins = $customfields->get_columns()[0]->get_joins();
        $this->assertCount(1, $columnjoins);
        $this->assertStringStartsWith('LEFT JOIN {customfield_data}', $columnjoins[0]);

        $filterjoins = $customfields->get_filters()[0]->get_joins();
        $this->assertCount(1, $filterjoins);
        $this->assertStringStartsWith('LEFT JOIN {customfield_data}', $filterjoins[0]);

        // Add additional join.
        $customfields->add_join('JOIN {test} t ON t.id = id');

        $columnjoins = $customfields->get_columns()[0]->get_joins();
        $this->assertCount(2, $columnjoins);
        $this->assertEquals('JOIN {test} t ON t.id = id', $columnjoins[0]);
        $this->assertStringStartsWith('LEFT JOIN {customfield_data}', $columnjoins[1]);

        $filterjoins = $customfields->get_filters()[0]->get_joins();
        $this->assertCount(2, $filterjoins);
        $this->assertEquals('JOIN {test} t ON t.id = id', $filterjoins[0]);
        $this->assertStringStartsWith('LEFT JOIN {customfield_data}', $filterjoins[1]);
    }

    /**
     * Test for get_filters
     */
    public function test_get_filters(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $customfields = $this->generate_customfields();

        $filters = $customfields->get_filters();
        $this->assertCount(6, $filters);
        $this->assertContainsOnlyInstancesOf(filter::class, $filters);

        // Filter headers.
        $this->assertEquals([
            'Text',
            'Textarea',
            'Checkbox',
            'Date',
            'Select',
            'Number',
        ], array_map(
            fn(filter $filter) => $filter->get_header(),
            $filters,
        ));

        // Filter types.
        $this->assertEquals([
            text::class,
            text::class,
            boolean_select::class,
            date::class,
            select::class,
            number::class,
        ], array_map(
            fn(filter $filter) => $filter->get_filter_class(),
            $filters,
        ));

        // Filter available.
        $this->assertEquals([
            true,
            true,
            true,
            true,
            true,
            true,
        ], array_map(
            fn(filter $filter) => $filter->get_is_available(),
            $filters,
        ));

        // Filter available, for non-privileged user.
        $this->setUser(null);
        $this->assertEquals([
            true,
            true,
            false,
            true,
            true,
            true,
        ], array_map(
            fn(filter $filter) => $filter->get_is_available(),
            $customfields->get_filters(),
        ));
    }

    /**
     * Test that adding custom field columns to a report returns expected values
     */
    public function test_custom_report_content(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = $this->getDataGenerator()->create_category(['name' => 'Zebras']);
        $courseone = $this->getDataGenerator()->create_course(['category' => $category->id, 'fullname' => 'C1']);

        // Second course will populate each custom field.
        $this->generate_customfields();
        $coursetwo = $this->getDataGenerator()->create_course(['category' => $category->id, 'fullname' => 'C2', 'customfields' => [
            ['shortname' => 'text', 'value' => 'Hello'],
            ['shortname' => 'textarea_editor', 'value' => ['text' => 'Goodbye', 'format' => FORMAT_MOODLE]],
            ['shortname' => 'checkbox', 'value' => 0],
            ['shortname' => 'date', 'value' => 1669852800],
            ['shortname' => 'select', 'value' => 2],
            ['shortname' => 'number', 'value' => 42],
        ]]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Categories', 'source' => categories::class, 'default' => 0]);

        // Add custom field columns to the report.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:name',
            'sortenabled' => 1]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname',
            'sortenabled' => 1]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_text']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_textarea']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_checkbox']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_date']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_select']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:customfield_number']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [
                'Category 1',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                $category->name,
                $courseone->fullname,
                'default',
                format_text('Default'),
                'Yes',
                '',
                'Cat',
                1,
            ],
            [
                $category->name,
                $coursetwo->fullname,
                'Hello',
                format_text('Goodbye'),
                'No',
                userdate(1669852800),
                'Dog',
                42,
            ],
        ], array_map('array_values', $content));
    }

    /**
     * Data provider for {@see test_custom_report_filter}
     *
     * @return array[]
     */
    public static function custom_report_filter_provider(): array {
        return [
            'Filter by text custom field' => ['course:customfield_text', [
                'course:customfield_text_operator' => text::IS_EQUAL_TO,
                'course:customfield_text_value' => 'Hello',
            ], 'C2'],
            'Filter by text custom field (default)' => ['course:customfield_text', [
                'course:customfield_text_operator' => text::IS_EQUAL_TO,
                'course:customfield_text_value' => 'default',
            ], 'C1'],
            'Filter by text custom field (no match)' => ['course:customfield_text', [
                'course:customfield_text_operator' => text::IS_EQUAL_TO,
                'course:customfield_text_value' => 'Goodbye',
            ]],
            'Filter by textarea custom field' => ['course:customfield_textarea', [
                'course:customfield_textarea_operator' => text::IS_EQUAL_TO,
                'course:customfield_textarea_value' => 'Goodbye',
            ], 'C2'],
            'Filter by textarea custom field (default)' => ['course:customfield_textarea', [
                'course:customfield_textarea_operator' => text::IS_EQUAL_TO,
                'course:customfield_textarea_value' => 'Default',
            ], 'C1'],
            'Filter by textarea custom field (no match)' => ['course:customfield_textarea', [
                'course:customfield_textarea_operator' => text::IS_EQUAL_TO,
                'course:customfield_textarea_value' => 'Hello',
            ]],
            'Filter by checkbox custom field' => ['course:customfield_checkbox', [
                'course:customfield_checkbox_operator' => boolean_select::NOT_CHECKED,
            ], 'C2'],
            'Filter by checkbox custom field (default)' => ['course:customfield_checkbox', [
                'course:customfield_checkbox_operator' => boolean_select::CHECKED,
            ], 'C1'],
            'Filter by date custom field' => ['course:customfield_date', [
                'course:customfield_date_operator' => date::DATE_RANGE,
                'course:customfield_date_from' => 1622502000,
            ], 'C2'],
            'Filter by date custom field (no match)' => ['course:customfield_date', [
                'course:customfield_date_operator' => date::DATE_RANGE,
                'course:customfield_date_from' => 1672531200,
            ]],
            'Filter by select custom field' => ['course:customfield_select', [
                'course:customfield_select_operator' => select::EQUAL_TO,
                'course:customfield_select_value' => 2,
            ], 'C2'],
            'Filter by select custom field (default)' => ['course:customfield_select', [
                'course:customfield_select_operator' => select::EQUAL_TO,
                'course:customfield_select_value' => 1,
            ], 'C1'],
            'Filter by select custom field (no match)' => ['course:customfield_select', [
                'course:customfield_select_operator' => select::EQUAL_TO,
                'course:customfield_select_value' => 3,
            ]],
            'Filter by number custom field' => ['course:customfield_number', [
                'course:customfield_number_operator' => number::EQUAL_TO,
                'course:customfield_number_value1' => 42,
            ], 'C2'],
            'Filter by number custom field (default)' => ['course:customfield_number', [
                'course:customfield_number_operator' => number::EQUAL_TO,
                'course:customfield_number_value1' => 1,
            ], 'C1'],
            'Filter by number custom field (no match)' => ['course:customfield_number', [
                'course:customfield_number_operator' => number::EQUAL_TO,
                'course:customfield_number_value1' => 3,
            ]],
        ];
    }

    /**
     * Test filtering report by custom fields
     *
     * @param string $filtername
     * @param array $filtervalues
     * @param string|null $expectmatch
     *
     * @dataProvider custom_report_filter_provider
     */
    public function test_custom_report_filter(string $filtername, array $filtervalues, ?string $expectmatch = null): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->getDataGenerator()->create_course(['fullname' => 'C1']);

        // Second course will populate each custom field.
        $this->generate_customfields();
        $this->getDataGenerator()->create_course(['fullname' => 'C2', 'customfields' => [
            ['shortname' => 'text', 'value' => 'Hello'],
            ['shortname' => 'textarea_editor', 'value' => ['text' => 'Goodbye', 'format' => FORMAT_MOODLE]],
            ['shortname' => 'checkbox', 'value' => 0],
            ['shortname' => 'date', 'value' => 1669852800],
            ['shortname' => 'select', 'value' => 2],
            ['shortname' => 'number', 'value' => 42],
        ]]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Users', 'source' => courses::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch !== null) {
            $this->assertCount(1, $content);
            $this->assertEquals($expectmatch, reset($content[0]));
        } else {
            $this->assertEmpty($content);
        }
    }

    /**
     * Stress test course datasource using custom fields
     *
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function test_stress_datasource(): void {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $this->generate_customfields();
        $this->getDataGenerator()->create_course(['customfields' => [
            ['shortname' => 'text', 'value' => 'Hello'],
            ['shortname' => 'textarea_editor', 'value' => ['text' => 'Goodbye', 'format' => FORMAT_MOODLE]],
            ['shortname' => 'checkbox', 'value' => true],
            ['shortname' => 'date', 'value' => 1669852800],
            ['shortname' => 'select', 'value' => 2],
        ]]);

        $this->datasource_stress_test_columns(courses::class);
        $this->datasource_stress_test_columns_aggregation(courses::class);
        $this->datasource_stress_test_conditions(courses::class, 'course:idnumber');
    }
}

