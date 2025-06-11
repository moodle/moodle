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

namespace core_reportbuilder\external\filters;

use core_reportbuilder_generator;
use core_reportbuilder\manager;
use core_reportbuilder\exception\report_access_exception;
use core_external\external_api;
use externallib_advanced_testcase;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/webservice/tests/helpers.php");

/**
 * Unit tests external filters set class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\filters\set
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class set_test extends externallib_advanced_testcase {

    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $values = [
            'entity:filter_operator' => 'something',
            'entity:filter_value' => 42,
        ];

        $result = set::execute($report->get('id'), '', json_encode($values));
        $result = external_api::clean_returnvalue(set::execute_returns(), $result);

        $this->assertTrue($result);

        // We should get our original filter values back.
        $instance = manager::get_report_from_persistent($report);
        $this->assertEquals($values, $instance->get_filter_values());
    }

    /**
     * Test execute method for a user without permission to view the report
     */
    public function test_execute_access_exception(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot view this report');
        set::execute($report->get('id'), '', json_encode(['foo' => 1]));
    }
}
