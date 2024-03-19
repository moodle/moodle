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
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for custom report details exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_details_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_details_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $exporter = new custom_report_details_exporter($report);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        // The exporter outputs the persistent details, plus two other properties.
        $this->assertEquals($report->get('name'), $export->name);
        $this->assertEquals($report->get('source'), $export->source);

        // Source name should be the name of the source.
        $this->assertEquals(users::get_name(), $export->sourcename);

        // We use the user exporter for the modifier of the report.
        $this->assertObjectHasProperty('modifiedby', $export);
        $this->assertEquals(fullname($user), $export->modifiedby->fullname);
    }
}
