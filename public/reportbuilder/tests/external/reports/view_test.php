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

namespace core_reportbuilder\external\reports;

use core_reportbuilder_generator;
use core_external\external_api;
use externallib_advanced_testcase;
use core_reportbuilder\event\report_viewed;
use core_reportbuilder\exception\report_access_exception;
use core_reportbuilder\local\models\report;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/webservice/tests/helpers.php");

/**
 * Unit tests of external class for viewing reports
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\reports\view
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class view_test extends externallib_advanced_testcase {

    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Catch the events.
        $sink = $this->redirectEvents();

        $result = view::execute($report->get('id'));
        $result = external_api::clean_returnvalue(view::execute_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $sink->close();

        $this->assertValidKeys($result, ['status', 'warnings']);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(report_viewed::class, $event);
        $this->assertEquals(report::TABLE, $event->objecttable);
        $this->assertEquals($report->get('id'), $event->objectid);
        $this->assertEquals($report->get('name'), $event->other['name']);
        $this->assertEquals($report->get('source'), $event->other['source']);
        $this->assertEquals($report->get_context()->id, $event->contextid);
    }

    /**
     * Test execute method for a user without permission to view reports
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
        view::execute($report->get('id'));
    }
}
