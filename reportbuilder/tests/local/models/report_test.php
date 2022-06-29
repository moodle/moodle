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

namespace core_reportbuilder\local\models;

use advanced_testcase;
use core_reportbuilder\event\report_created;
use core_reportbuilder\event\report_deleted;
use core_reportbuilder\event\report_updated;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the report model
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\models\report
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_test extends advanced_testcase {

    /**
     * Tests for report_created event
     *
     * @return report
     *
     * @covers \core_reportbuilder\event\report_created
     */
    public function test_report_created_event(): report {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Catch the events.
        $sink = $this->redirectEvents();
        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(report_created::class, $event);
        $this->assertEquals(report::TABLE, $event->objecttable);
        $this->assertEquals($report->get('id'), $event->objectid);
        $this->assertEquals($report->get('name'), $event->other['name']);
        $this->assertEquals($report->get('source'), $event->other['source']);
        $this->assertEquals($report->get_context()->id, $event->contextid);

        return $report;
    }

    /**
     * Tests for report_updated event
     *
     * @param report $report
     * @return report
     *
     * @depends test_report_created_event
     * @covers \core_reportbuilder\event\report_updated
     */
    public function test_report_updated_event(report $report): report {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Re-create the persistent.
        $report = new report($DB->insert_record(report::TABLE, $report->to_record()));

        // Catch the events.
        $sink = $this->redirectEvents();
        $report->set('name', 'New report name')->update();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(report_updated::class, $event);
        $this->assertEquals(report::TABLE, $event->objecttable);
        $this->assertEquals($report->get('id'), $event->objectid);
        $this->assertEquals('New report name', $event->other['name']);
        $this->assertEquals($report->get('source'), $event->other['source']);
        $this->assertEquals($report->get_context()->id, $event->contextid);

        return $report;
    }

    /**
     * Tests for report_deleted event
     *
     * @param report $report
     *
     * @depends test_report_updated_event
     * @covers \core_reportbuilder\event\report_deleted
     */
    public function test_report_deleted_event(report $report): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Re-create the persistent (remembering report ID which is removed from persistent upon deletion).
        $reportid = $DB->insert_record(report::TABLE, $report->to_record());
        $report = new report($reportid);

        // Catch the events.
        $sink = $this->redirectEvents();
        $report->delete();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(report_deleted::class, $event);
        $this->assertEquals(report::TABLE, $event->objecttable);
        $this->assertEquals($reportid, $event->objectid);
        $this->assertEquals($report->get('name'), $event->other['name']);
        $this->assertEquals($report->get('source'), $event->other['source']);
        $this->assertEquals($report->get_context()->id, $event->contextid);
    }
}
