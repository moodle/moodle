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
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for custom report data exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_data_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_report_data_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();

        $this->getDataGenerator()->create_user(['firstname' => 'Zoe', 'lastname' => 'Zebra', 'email' => 'u1@example.com']);
        $this->getDataGenerator()->create_user(['firstname' => 'Charlie', 'lastname' => 'Carrot', 'email' => 'u2@example.com']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:fullname',
            'heading' => 'Lovely user',
            'sortenabled' => 1,
        ]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_data_exporter(null, ['report' => $reportinstance, 'page' => 0, 'perpage' => 2]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        // There are three users (admin plus the two previouly created), but we're paging the first two only.
        $this->assertEquals(['Lovely user', 'Email address'], $export->headers);
        $this->assertEquals([
            [
                'columns' => ['Admin User', 'admin@example.com'],
            ],
            [
                'columns' => ['Charlie Carrot', 'u2@example.com'],
            ],
        ], $export->rows);
        $this->assertEquals(3, $export->totalrowcount);
    }
}
