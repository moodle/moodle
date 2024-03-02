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

namespace core_reportbuilder\external;

use advanced_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\manager;
use core_reportbuilder\local\helpers\report;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for custom report column sorting exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_columns_sorting_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_columns_sorting_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add a couple of columns.
        $columnfullname = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $columnemail = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        // Enable sorting on the email column, move it to first place.
        report::toggle_report_column_sorting($report->get('id'), $columnemail->get('id'), true, SORT_DESC);
        report::reorder_report_column_sorting($report->get('id'), $columnemail->get('id'), 1);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_columns_sorting_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertTrue($export->hassortablecolumns);
        $this->assertCount(2, $export->sortablecolumns);
        [$sortcolumnemail, $sortcolumnfullname] = $export->sortablecolumns;

        // Email column.
        $this->assertEquals($columnemail->get('id'), $sortcolumnemail['id']);
        $this->assertEquals('Email address', $sortcolumnemail['heading']);
        $this->assertTrue($sortcolumnemail['sortenabled']);
        $this->assertEquals(1, $sortcolumnemail['sortorder']);
        $this->assertEquals(SORT_DESC, $sortcolumnemail['sortdirection']);
        $this->assertEquals('Disable initial sorting for column \'Email address\'', $sortcolumnemail['sortenabledtitle']);
        $this->assertEquals('Sort column \'Email address\' ascending', $sortcolumnemail['sorticon']['title']);

        // Fullname column.
        $this->assertEquals($columnfullname->get('id'), $sortcolumnfullname['id']);
        $this->assertEquals('Full name', $sortcolumnfullname['heading']);
        $this->assertFalse($sortcolumnfullname['sortenabled']);
        $this->assertEquals(2, $sortcolumnfullname['sortorder']);
        $this->assertEquals(SORT_ASC, $sortcolumnfullname['sortdirection']);
        $this->assertEquals('Enable initial sorting for column \'Full name\'', $sortcolumnfullname['sortenabledtitle']);
        $this->assertEquals('Sort column \'Full name\' descending', $sortcolumnfullname['sorticon']['title']);

        $this->assertNotEmpty($export->helpicon);
    }

    /**
     * Test exported data structure for report with columns, but they aren't sortable
     */
    public function test_export_no_sortable_columns(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:picture']);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_columns_sorting_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertFalse($export->hassortablecolumns);
        $this->assertEmpty($export->sortablecolumns);
    }

    /**
     * Test exported data structure for report with no columns
     */
    public function test_export_no_columns(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_columns_sorting_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertFalse($export->hassortablecolumns);
        $this->assertEmpty($export->sortablecolumns);
    }
}
