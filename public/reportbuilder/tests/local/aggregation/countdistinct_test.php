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
use core_reportbuilder\tests\core_reportbuilder_testcase;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for count distinct aggregation
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\aggregation\base
 * @covers      \core_reportbuilder\local\aggregation\countdistinct
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class countdistinct_test extends core_reportbuilder_testcase {

    /**
     * Test aggregation when applied to column
     */
    public function test_column_aggregation(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Apple']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // Report columns, aggregated/sorted by user lastname.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'aggregation' => countdistinct::get_class_name(),
            'sortenabled' => 1,
            'sortdirection' => SORT_DESC,
        ]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['Bob', 2],
            ['Admin', 1],
        ], array_map('array_values', $content));
    }

    /**
     * Test aggregation when applied to column with multiple fields
     */
    public function test_column_aggregation_multiple_fields(): void {
        $this->resetAfterTest();

        // Create a user with the same firstname as existing admin.
        $this->getDataGenerator()->create_user(['firstname' => 'Admin', 'lastname' => 'Test']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // This is the column we'll aggregate.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:fullname',
            'aggregation' => countdistinct::get_class_name(),
        ]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        // There are two distinct fullnames ("Admin User" & "Admin Test").
        $countdistinct = reset($content[0]);
        $this->assertEquals(2, $countdistinct);
    }

    /**
     * Test aggregation with custom callback option when applied to column
     */
    public function test_column_aggregation_callback_option(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Apple']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // Report columns, aggregated/sorted by user lastname.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'aggregation' => countdistinct::get_class_name(),
            'sortenabled' => 1,
            'sortdirection' => SORT_DESC,
        ]);

        // Set aggregation option for callback.
        $instance = manager::get_report_from_persistent($report);
        $instance->get_column('user:lastname')
            ->set_aggregation_options(countdistinct::get_class_name(), [
                'callback' => fn(int $count): string => "{$count} users",
            ]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['Bob', '2 users'],
            ['Admin', '1 users'],
        ], array_map('array_values', $content));
    }
}
