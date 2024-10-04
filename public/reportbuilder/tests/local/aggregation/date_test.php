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
use core_reportbuilder\tests\core_reportbuilder_testcase;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for date aggregation
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\aggregation\date
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class date_test extends core_reportbuilder_testcase {

    /**
     * Mock the clock
     */
    protected function setUp(): void {
        parent::setUp();

        $this->setTimezone('Europe/London', 'Europe/London');
        $this->mock_clock_with_frozen(1609495200);
    }

    /**
     * Test aggregation when applied to column
     */
    public function test_column_aggregation(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Andy', 'lastaccess' => 1622523600]);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastaccess' => 1622530800]);
        $this->getDataGenerator()->create_user(['firstname' => 'Charlie', 'lastaccess' => 1622624400]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // Report columns, aggregated/sorted by user lastaccess.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastaccess',
            'aggregation' => date::get_class_name(),
            'sortenabled' => 1,
            'sortdirection' => SORT_ASC,
        ]);

        // Date aggregation requires other columns to be aggregated to make any sense.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:firstname',
            'aggregation' => groupconcat::get_class_name(),
        ]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['', 'Admin'],
            ['Tuesday, 1 June 2021', 'Andy, Bob'],
            ['Wednesday, 2 June 2021', 'Charlie'],
        ], array_map('array_values', $content));
    }
}
