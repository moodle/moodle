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
use context_system;
use moodle_url;
use core_reportbuilder\system_report_available;
use core_reportbuilder\system_report_factory;

/**
 * Unit tests for system report exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\system_report_exporter
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_exporter_test extends advanced_testcase {

    /**
     * Load test fixture
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");
    }

    /**
     * Data provider for {@see test_export}
     *
     * @return array[]
     */
    public function export_provider(): array {
        return [
            ['With filters' => true],
            ['Without filters' => false],
        ];
    }

    /**
     * Text execute method
     *
     * @param bool $withfilters
     *
     * @dataProvider export_provider
     */
    public function test_export(bool $withfilters): void {
        global $PAGE;

        $this->resetAfterTest();

        // Prevent debug warnings from flexible_table.
        $PAGE->set_url(new moodle_url('/'));

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance(), '', '', 0,
            ['withfilters' => $withfilters]);

        $exporter = new system_report_exporter($systemreport->get_report_persistent(), [
            'source' => $systemreport,
            'parameters' => json_encode($systemreport->get_parameters()),
        ]);

        $data = $exporter->export($PAGE->get_renderer('core_reportbuilder'));
        $this->assertNotEmpty($data->table);

        if ($withfilters) {
            $this->assertEquals('{"withfilters":true}', $data->parameters);
            $this->assertTrue($data->filterspresent);
            $this->assertNotEmpty($data->filtersform);
        } else {
            $this->assertEquals('{"withfilters":false}', $data->parameters);
            $this->assertFalse($data->filterspresent);
            $this->assertEmpty($data->filtersform);
        }
    }
}
