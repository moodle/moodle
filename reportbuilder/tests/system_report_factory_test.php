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

namespace core_reportbuilder;

use advanced_testcase;
use context_system;
use stdClass;

/**
 * Unit tests for the system report factory class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\system_report_factory
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_factory_test extends advanced_testcase {

    /**
     * Test creating a valid/available system report
     */
    public function test_create(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $this->assertInstanceOf(system_report::class, $systemreport);
    }

    /**
     * Test that creating a system report returns the same persistent if it already exists
     */
    public function test_create_previously_exists(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();

        $systemreportone = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreporttwo = system_report_factory::create(system_report_available::class, context_system::instance());

        // Assert we have the same persistent for each.
        $this->assertEquals($systemreportone->get_report_persistent()->get('id'),
            $systemreporttwo->get_report_persistent()->get('id'));
    }

    /**
     * Test creating an system report with an invalid source
     */
    public function test_create_invalid(): void {
        $this->expectException(source_invalid_exception::class);
        system_report_factory::create(stdClass::class, context_system::instance());
    }

    /**
     * Test creating an unavailable system report
     */
    public function test_create_unavailable(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_unavailable.php");

        $this->resetAfterTest();

        $this->expectException(source_unavailable_exception::class);
        system_report_factory::create(system_report_unavailable::class, context_system::instance());
    }
}
