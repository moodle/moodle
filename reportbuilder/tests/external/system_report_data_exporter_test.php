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
use core\context\system;
use core_reportbuilder_generator;
use core_reportbuilder\system_report_factory;
use core_reportbuilder\local\systemreports\reports_list;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for system report data exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\system_report_data_exporter
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_data_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Two reports, created one second apart to ensure consistent ordering by time created.
        $generator->create_report(['name' => 'My first report', 'source' => users::class]);
        $this->waitForSecond();
        $generator->create_report(['name' => 'My second report', 'source' => users::class, 'tags' => ['cat', 'dog']]);

        $reportinstance = system_report_factory::create(reports_list::class, system::instance());

        $exporter = new system_report_data_exporter(null, ['report' => $reportinstance, 'page' => 0, 'perpage' => 1]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertEquals([
            'Name',
            'Report source',
            'Tags',
            'Time created',
            'Time modified',
            'Modified by',
        ], $export->headers);

        $this->assertCount(1, $export->rows);
        [$name, $source, $tags, $timecreated, $timemodified, $modifiedby] = $export->rows[0]['columns'];

        $this->assertStringContainsString('My second report', $name);
        $this->assertEquals(users::get_name(), $source);
        $this->assertEquals('cat, dog', $tags);
        $this->assertNotEmpty($timecreated);
        $this->assertNotEmpty($timemodified);
        $this->assertEquals('Admin User', $modifiedby);

        $this->assertEquals(2, $export->totalrowcount);
    }
}
