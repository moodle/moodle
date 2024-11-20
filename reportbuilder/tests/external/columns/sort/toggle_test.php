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

namespace core_reportbuilder\external\columns\sort;

use core_reportbuilder_generator;
use core_external\external_api;
use externallib_advanced_testcase;
use core_reportbuilder\exception\report_access_exception;
use core_reportbuilder\local\models\column;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/webservice/tests/helpers.php");

/**
 * Unit tests of external class for toggling report column sorting
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\columns\sort\toggle
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class toggle_test extends externallib_advanced_testcase {

    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);
        $column = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        // Toggle sort descending.
        $result = toggle::execute($report->get('id'), $column->get('id'), true, SORT_DESC);
        $result = external_api::clean_returnvalue(toggle::execute_returns(), $result);

        $this->assertTrue($result['hassortablecolumns']);
        $this->assertCount(1, $result['sortablecolumns']);

        $sortablecolumn = reset($result['sortablecolumns']);
        $this->assertEquals('Email address', $sortablecolumn['title']);
        $this->assertEquals(SORT_DESC, $sortablecolumn['sortdirection']);
        $this->assertEquals(1, $sortablecolumn['sortenabled']);
        $this->assertEquals(1, $sortablecolumn['sortorder']);
        $this->assertArrayHasKey('sorticon', $sortablecolumn);

        // Confirm column was updated.
        $columnupdated = new column($column->get('id'));
        $this->assertTrue($columnupdated->get('sortenabled'));
        $this->assertEquals(SORT_DESC, $columnupdated->get('sortdirection'));
    }

    /**
     * Test execute method for a user without permission to edit reports
     */
    public function test_execute_access_exception(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $column = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot edit this report');
        toggle::execute($report->get('id'), $column->get('id'), true, SORT_DESC);
    }
}
