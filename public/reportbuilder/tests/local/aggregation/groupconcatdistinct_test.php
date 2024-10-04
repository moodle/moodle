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

namespace core_reportbuilder\local\aggregation;

use core_reportbuilder_generator;
use core_reportbuilder\manager;
use core_reportbuilder\local\report\column;
use core_reportbuilder\tests\core_reportbuilder_testcase;
use core_user\reportbuilder\datasource\users;
use stdClass;

/**
 * Unit tests for group concatenation distinct aggregation
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\aggregation\base
 * @covers      \core_reportbuilder\local\aggregation\groupconcatdistinct
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class groupconcatdistinct_test extends core_reportbuilder_testcase {

    /**
     * Test setup, we need to skip these tests on non-supported databases
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();

        if (!groupconcatdistinct::compatible(column::TYPE_TEXT)) {
            $this->markTestSkipped('Distinct group concatenation not supported in ' . $DB->get_dbfamily());
        }
    }

    /**
     * Test aggregation when applied to column
     */
    public function test_column_aggregation(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Apple']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // Report columns, aggregated/sorted by user lastname.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'aggregation' => groupconcatdistinct::get_class_name(),
            'sortenabled' => 1,
            'sortdirection' => SORT_ASC,
        ]);

        // Assert lastname column was aggregated, and itself also sorted predictably.
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['Bob', 'Apple, Banana'],
            ['Admin', 'User'],
        ], array_map('array_values', $content));
    }

    /**
     * Test aggregation with custom separator option when applied to column
     */
    public function test_column_aggregation_separator_option(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Apple']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // Report columns, aggregated/sorted by user lastname.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'aggregation' => groupconcatdistinct::get_class_name(),
            'sortenabled' => 1,
            'sortdirection' => SORT_ASC,
        ]);

        // Set aggregation option for separator.
        $instance = manager::get_report_from_persistent($report);
        $instance->get_column('user:lastname')
            ->set_aggregation_options(groupconcatdistinct::get_class_name(), ['separator' => '<br />']);

        // Assert lastname column was aggregated, with defined separator between each item.
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['Bob', 'Apple<br />Banana'],
            ['Admin', 'User'],
        ], array_map('array_values', $content));
    }

    /**
     * Test aggregation when applied to column with multiple fields
     */
    public function test_column_aggregation_multiple_fields(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['firstname' => 'Adam', 'lastname' => 'Apple']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // This is the column we'll aggregate.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:fullnamewithlink',
            'aggregation' => groupconcatdistinct::get_class_name(),
        ]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        // Ensure users are sorted predictably (Adam -> Admin).
        [$userone, $usertwo] = explode(', ', reset($content[0]));
        $this->assertStringContainsString(fullname($user, true), $userone);
        $this->assertStringContainsString(fullname(get_admin(), true), $usertwo);
    }

    /**
     * Test aggregation when applied to column with callback
     */
    public function test_column_aggregation_with_callback(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'confirmed' => 1]);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'confirmed' => 0]);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'confirmed' => 1]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // First column, sorted.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname', 'sortenabled' => 1]);

        // This is the column we'll aggregate.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:confirmed',
            'aggregation' => groupconcatdistinct::get_class_name(),
        ]);

        // Add callback to format the column.
        $instance = manager::get_report_from_persistent($report);
        $instance->get_column('user:confirmed')
            ->add_callback(static function(string $value, stdClass $row, $arguments, ?string $aggregation): string {
                // Simple callback to return the given value, and append aggregation type.
                return "{$value} ({$aggregation})";
            });

        // Assert confirmed column was aggregated, and sorted predictably with callback applied.
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['Admin', 'Yes (groupconcatdistinct)'],
            ['Bob', 'No (groupconcatdistinct), Yes (groupconcatdistinct)'],
        ], array_map('array_values', $content));
    }
}
