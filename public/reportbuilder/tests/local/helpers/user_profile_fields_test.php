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

use core_reportbuilder_generator;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\tests\core_reportbuilder_testcase;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for user profile fields helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\user_profile_fields
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_profile_fields_test extends core_reportbuilder_testcase {

    /**
     * Generate custom profile fields, one of each type
     *
     * @return user_profile_fields
     */
    private function generate_userprofilefields(): user_profile_fields {
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'checkbox', 'name' => 'Checkbox field', 'datatype' => 'checkbox', 'defaultdata' => 1]);

        // This field is available only to admins.
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'datetime', 'name' => 'Date field', 'datatype' => 'datetime', 'param2' => 2022, 'param3' => 0,
                'defaultdata' => 0, 'visible' => PROFILE_VISIBLE_NONE]);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'menu', 'name' => 'Menu field', 'datatype' => 'menu', 'param1' => "Cat\nDog\nFish",
                'defaultdata' => 'Cat']);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'Social', 'name' => 'msn', 'datatype' => 'social', 'param1' => 'msn']);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'text', 'name' => 'Text field', 'datatype' => 'text', 'defaultdata' => 'default']);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'textarea', 'name' => 'Textarea field', 'datatype' => 'textarea', 'defaultdata' => 'Default']);

        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');

        // Create an instance of the userprofilefield helper.
        return new user_profile_fields("{$useralias}.id", $userentity->get_entity_name());
    }

    /**
     * Test for get_columns
     */
    public function test_get_columns(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Get pre-existing user profile fields.
        $userentity = new user();
        $initialcolumns = (new user_profile_fields(
            $userentity->get_table_alias('user') . '.id',
            $userentity->get_entity_name(),
        ))->get_columns();

        // Create a field which will duplicate one of the subsequently generated fields (case-insensitive shortname).
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'CHECKBOX',
            'name' => 'Duplicate checkbox field',
            'datatype' => 'checkbox',
        ]);

        // Add new custom profile fields.
        $userprofilefields = $this->generate_userprofilefields();

        // Ensure pre-existing fields are ignored in subsequent assertions.
        $columns = array_slice($userprofilefields->get_columns(), count($initialcolumns));
        $this->assertCount(6, $columns);
        $this->assertContainsOnlyInstancesOf(column::class, $columns);

        // Column titles.
        $this->assertEquals([
            'Checkbox field',
            'Date field',
            'Menu field',
            'MSN ID',
            'Text field',
            'Textarea field',
        ], array_map(
            fn(column $column): string => $column->get_title(),
            $columns,
        ));

        // Column types.
        $this->assertEquals([
            column::TYPE_BOOLEAN,
            column::TYPE_TIMESTAMP,
            column::TYPE_TEXT,
            column::TYPE_TEXT,
            column::TYPE_TEXT,
            column::TYPE_LONGTEXT,
        ], array_map(
            fn(column $column): int => $column->get_type(),
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
            fn(column $column): bool => $column->get_is_sortable(),
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
            fn(column $column): bool => $column->get_is_available(),
            $columns,
        ));

        // Column available, for non-privileged user.
        $this->setUser(null);
        $this->assertEquals([
            true,
            false,
            true,
            true,
            true,
            true,
        ], array_map(
            fn(column $column): bool => $column->get_is_available(),
            array_slice($userprofilefields->get_columns(), count($initialcolumns)),
        ));
    }

    /**
     * Test that joins added to the profile fields helper are present in its columns/filters
     */
    public function test_add_join(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();

        // We always join on the user info data table.
        $columnjoins = $userprofilefields->get_columns()[0]->get_joins();
        $this->assertCount(1, $columnjoins);
        $this->assertStringStartsWith('LEFT JOIN {user_info_data}', $columnjoins[0]);

        $filterjoins = $userprofilefields->get_filters()[0]->get_joins();
        $this->assertCount(1, $filterjoins);
        $this->assertStringStartsWith('LEFT JOIN {user_info_data}', $filterjoins[0]);

        // Add additional join.
        $userprofilefields->add_join('JOIN {test} t ON t.id = id');

        $columnjoins = $userprofilefields->get_columns()[0]->get_joins();
        $this->assertCount(2, $columnjoins);
        $this->assertEquals('JOIN {test} t ON t.id = id', $columnjoins[0]);
        $this->assertStringStartsWith('LEFT JOIN {user_info_data}', $columnjoins[1]);

        $filterjoins = $userprofilefields->get_filters()[0]->get_joins();
        $this->assertCount(2, $filterjoins);
        $this->assertEquals('JOIN {test} t ON t.id = id', $filterjoins[0]);
        $this->assertStringStartsWith('LEFT JOIN {user_info_data}', $filterjoins[1]);
    }

    /**
     * Test for get_filters
     */
    public function test_get_filters(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Get pre-existing user profile fields.
        $userentity = new user();
        $initialfilters = (new user_profile_fields(
            $userentity->get_table_alias('user') . '.id',
            $userentity->get_entity_name(),
        ))->get_filters();

        // Create a field which will duplicate one of the subsequently generated fields (case-insensitive shortname).
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'CHECKBOX',
            'name' => 'Duplicate checkbox field',
            'datatype' => 'checkbox',
        ]);

        // Add new custom profile fields.
        $userprofilefields = $this->generate_userprofilefields();

        // Ensure pre-existing fields are ignored in subsequent assertions.
        $filters = array_slice($userprofilefields->get_filters(), count($initialfilters));
        $this->assertCount(6, $filters);
        $this->assertContainsOnlyInstancesOf(filter::class, $filters);

        // Filter headers.
        $this->assertEquals([
            'Checkbox field',
            'Date field',
            'Menu field',
            'MSN ID',
            'Text field',
            'Textarea field',
        ], array_map(
            fn(filter $filter): string => $filter->get_header(),
            $filters,
        ));

        // Filter types.
        $this->assertEquals([
            boolean_select::class,
            date::class,
            select::class,
            text::class,
            text::class,
            text::class,
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
            fn(filter $filter): bool => $filter->get_is_available(),
            $filters,
        ));

        // Filter available, for non-privileged user.
        $this->setUser(null);
        $this->assertEquals([
            true,
            false,
            true,
            true,
            true,
            true,
        ], array_map(
            fn(filter $filter): bool => $filter->get_is_available(),
            array_slice($userprofilefields->get_filters(), count($initialfilters)),
        ));
    }

    /**
     * Test that adding user profile field columns to a report returns expected values
     */
    public function test_custom_report_content(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create test subject with user profile fields content.
        $this->generate_userprofilefields();
        $this->getDataGenerator()->create_user([
            'firstname' => 'Zebedee',
            'profile_field_checkbox' => 0,
            'profile_field_datetime' => '2021-12-09',
            'profile_field_menu' => 'Dog',
            'profile_field_Social' => 12345,
            'profile_field_text' => 'Hello',
            'profile_field_textarea' => 'Goodbye',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // Add user profile field columns to the report.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname', 'sortenabled' => 1]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_checkbox']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_datetime']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_menu']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_social']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_text']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_textarea']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [
                'Admin',
                'Yes',
                'Not set',
                'Cat',
                '',
                'default',
                format_text('Default', options: ['overflowdiv' => true]),
            ], [
                'Zebedee',
                'No',
                '9 December 2021',
                'Dog',
                '12345',
                'Hello',
                format_text('Goodbye', options: ['overflowdiv' => true]),
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
            'Filter by checkbox profile field' => ['user:profilefield_checkbox', [
                'user:profilefield_checkbox_operator' => boolean_select::NOT_CHECKED,
            ], 'testuser'],
            'Filter by checkbox profile field (default)' => ['user:profilefield_checkbox', [
                'user:profilefield_checkbox_operator' => boolean_select::CHECKED,
            ], 'admin'],
            'Filter by datetime profile field' => ['user:profilefield_datetime', [
                'user:profilefield_datetime_operator' => date::DATE_RANGE,
                'user:profilefield_datetime_from' => 1622502000,
            ], 'testuser'],
            'Filter by datetime profile field (no match)' => ['user:profilefield_datetime', [
                'user:profilefield_datetime_operator' => date::DATE_RANGE,
                'user:profilefield_datetime_from' => 1672531200,
            ]],
            'Filter by menu profile field' => ['user:profilefield_menu', [
                'user:profilefield_menu_operator' => select::EQUAL_TO,
                'user:profilefield_menu_value' => 'Dog',
            ], 'testuser'],
            'Filter by menu profile field (default)' => ['user:profilefield_menu', [
                'user:profilefield_menu_operator' => select::EQUAL_TO,
                'user:profilefield_menu_value' => 'Cat',
            ], 'admin'],
            'Filter by menu profile field (no match)' => ['user:profilefield_menu', [
                'user:profilefield_menu_operator' => select::EQUAL_TO,
                'user:profilefield_menu_value' => 'Fish',
            ]],
            'Filter by social profile field' => ['user:profilefield_social', [
                'user:profilefield_social_operator' => text::IS_EQUAL_TO,
                'user:profilefield_social_value' => '12345',
            ], 'testuser'],
            'Filter by social profile field (no match)' => ['user:profilefield_social', [
                'user:profilefield_social_operator' => text::IS_EQUAL_TO,
                'user:profilefield_social_value' => '54321',
            ]],
            'Filter by text profile field' => ['user:profilefield_text', [
                'user:profilefield_text_operator' => text::IS_EQUAL_TO,
                'user:profilefield_text_value' => 'Hello',
            ], 'testuser'],
            'Filter by text profile field (default)' => ['user:profilefield_text', [
                'user:profilefield_text_operator' => text::IS_EQUAL_TO,
                'user:profilefield_text_value' => 'default',
            ], 'admin'],
            'Filter by text profile field (no match)' => ['user:profilefield_text', [
                'user:profilefield_text_operator' => text::IS_EQUAL_TO,
                'user:profilefield_text_value' => 'hola',
            ]],
            'Filter by textarea profile field' => ['user:profilefield_textarea', [
                'user:profilefield_textarea_operator' => text::IS_EQUAL_TO,
                'user:profilefield_textarea_value' => 'Goodbye',
            ], 'testuser'],
            'Filter by textarea profile field (default)' => ['user:profilefield_textarea', [
                'user:profilefield_textarea_operator' => text::IS_EQUAL_TO,
                'user:profilefield_textarea_value' => 'Default',
            ], 'admin'],
            'Filter by textarea profile field (no match)' => ['user:profilefield_textarea', [
                'user:profilefield_textarea_operator' => text::IS_EMPTY,
                'user:profilefield_textarea_value' => 'Adios',
            ]],
        ];
    }

    /**
     * Test filtering report by custom profile fields
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

        // Create test subject with user profile fields content.
        $this->generate_userprofilefields();
        $this->getDataGenerator()->create_user([
            'username' => 'testuser',
            'profile_field_checkbox' => 0,
            'profile_field_datetime' => '2021-12-09',
            'profile_field_menu' => 'Dog',
            'profile_field_Social' => '12345',
            'profile_field_text' => 'Hello',
            'profile_field_textarea' => 'Goodbye',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username']);

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
     * Stress test user datasource using profile fields
     *
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function test_stress_datasource(): void {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $this->generate_userprofilefields();
        $this->getDataGenerator()->create_user([
            'profile_field_checkbox' => true,
            'profile_field_datetime' => '2021-12-09',
            'profile_field_menu' => 'Dog',
            'profile_field_Social' => '12345',
            'profile_field_text' => 'Hello',
            'profile_field_textarea' => 'Goodbye',
        ]);

        $this->datasource_stress_test_columns(users::class);
        $this->datasource_stress_test_columns_aggregation(users::class);
        $this->datasource_stress_test_conditions(users::class, 'user:idnumber');
    }
}
