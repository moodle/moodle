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
use core_reportbuilder_testcase;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for user profile fields helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\user_profile_fields
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_profile_fields_test extends core_reportbuilder_testcase {

    /**
     * Generate custom profile fields, one of each type
     *
     * @return user_profile_fields
     */
    private function generate_userprofilefields(): user_profile_fields {
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'checkbox', 'name' => 'Checkbox field', 'datatype' => 'checkbox']);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'datetime', 'name' => 'Date field', 'datatype' => 'datetime', 'param2' => 2022, 'param3' => 0]);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'menu', 'name' => 'Menu field', 'datatype' => 'menu', 'param1' => "Cat\nDog"]);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'Social', 'name' => 'msn', 'datatype' => 'social', 'param1' => 'msn']);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'text', 'name' => 'Text field', 'datatype' => 'text']);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'textarea', 'name' => 'Textarea field', 'datatype' => 'textarea']);

        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');

        // Create an instance of the userprofilefield helper.
        return new user_profile_fields("$useralias.id", $userentity->get_entity_name());
    }

    /**
     * Test for get_columns
     */
    public function test_get_columns(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $columns = $userprofilefields->get_columns();

        $this->assertCount(6, $columns);
        $this->assertContainsOnlyInstancesOf(column::class, $columns);

        // Assert column titles.
        $columntitles = array_map(static function(column $column): string {
            return $column->get_title();
        }, $columns);
        $this->assertEquals([
            'Checkbox field',
            'Date field',
            'Menu field',
            'MSN ID',
            'Text field',
            'Textarea field',
        ], $columntitles);

        // Assert column types.
        $columntypes = array_map(static function(column $column): int {
            return $column->get_type();
        }, $columns);
        $this->assertEquals([
            column::TYPE_BOOLEAN,
            column::TYPE_TIMESTAMP,
            column::TYPE_TEXT,
            column::TYPE_TEXT,
            column::TYPE_TEXT,
            column::TYPE_LONGTEXT,
        ], $columntypes);

    }

    /**
     * Test for add_join
     */
    public function test_add_join(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $columns = $userprofilefields->get_columns();
        $this->assertCount(1, ($columns[0])->get_joins());

        $userprofilefields->add_join('JOIN {test} t ON t.id = id');
        $columns = $userprofilefields->get_columns();
        $this->assertCount(2, ($columns[0])->get_joins());
    }

    /**
     * Test for add_joins
     */
    public function test_add_joins(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $columns = $userprofilefields->get_columns();
        $this->assertCount(1, ($columns[0])->get_joins());

        $userprofilefields->add_joins(['JOIN {test} t ON t.id = id', 'JOIN {test2} t2 ON t2.id = id']);
        $columns = $userprofilefields->get_columns();
        $this->assertCount(3, ($columns[0])->get_joins());
    }

    /**
     * Test for get_filters
     */
    public function test_get_filters(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $filters = $userprofilefields->get_filters();

        $this->assertCount(6, $filters);
        $this->assertContainsOnlyInstancesOf(filter::class, $filters);

        // Assert filter headers.
        $filterheaders = array_map(static function(filter $filter): string {
            return $filter->get_header();
        }, $filters);
        $this->assertEquals([
            'Checkbox field',
            'Date field',
            'Menu field',
            'MSN ID',
            'Text field',
            'Textarea field',
        ], $filterheaders);
    }

    /**
     * Test that adding user profile field columns to a report returns expected values
     */
    public function test_custom_report_content(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();

        // Create test subject with user profile fields content.
        $user = $this->getDataGenerator()->create_user([
            'firstname' => 'Zebedee',
            'profile_field_checkbox' => true,
            'profile_field_datetime' => '2021-12-09',
            'profile_field_menu' => 'Cat',
            'profile_field_Social' => 12345,
            'profile_field_text' => 'Hello',
            'profile_field_textarea' => 'Goodbye',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // Add user profile field columns to the report.
        $firstname = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_checkbox']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_datetime']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_menu']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_Social']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_text']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_textarea']);

        // Sort the report, Admin -> Zebedee for consistency.
        report::toggle_report_column_sorting($report->get('id'), $firstname->get('id'), true);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [
                'c0_firstname' => 'Admin',
                'c1_data' => 'No',
                'c2_data' => 'Not set',
                'c3_data' => '',
                'c4_data' => '',
                'c5_data' => '',
                'c6_data' => '',
            ], [
                'c0_firstname' => 'Zebedee',
                'c1_data' => 'Yes',
                'c2_data' => '9 December 2021',
                'c3_data' => 'Cat',
                'c4_data' => '12345',
                'c5_data' => 'Hello',
                'c6_data' => '<div class="no-overflow">Goodbye</div>',
            ],
        ], $content);
    }
}
