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
use core\persistent;
use core_reportbuilder\event\schedule_created;
use core_reportbuilder\event\schedule_deleted;
use core_reportbuilder\event\schedule_updated;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the schedule model
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\models\schedule
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class schedule_test extends advanced_testcase {

    /**
     * Tests for schedule_created event
     *
     * @return persistent[]
     *
     * @covers \core_reportbuilder\event\schedule_created
     */
    public function test_schedule_created_event(): array {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        // Catch the events.
        $sink = $this->redirectEvents();
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(schedule_created::class, $event);
        $this->assertEquals(schedule::TABLE, $event->objecttable);
        $this->assertEquals($schedule->get('id'), $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
        $this->assertEquals($report->get_context()->id, $event->contextid);

        return [$report, $schedule];
    }

    /**
     * Tests for schedule_updated event
     *
     * @param persistent[] $persistents
     * @return persistent[]
     *
     * @depends test_schedule_created_event
     * @covers \core_reportbuilder\event\schedule_updated
     */
    public function test_schedule_updated_event(array $persistents): array {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Re-create the persistents.
        [$report, $schedule] = $persistents;
        $report = new report($DB->insert_record(report::TABLE, $report->to_record()));
        $schedule = new schedule($DB->insert_record(schedule::TABLE, $schedule->to_record()));

        // Catch the events.
        $sink = $this->redirectEvents();
        $schedule->set('name', 'My new schedule')->update();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(schedule_updated::class, $event);
        $this->assertEquals(schedule::TABLE, $event->objecttable);
        $this->assertEquals($schedule->get('id'), $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
        $this->assertEquals($report->get_context()->id, $event->contextid);

        return [$report, $schedule];
    }

    /**
     * Tests for schedule_deleted event
     *
     * @param persistent[] $persistents
     *
     * @depends test_schedule_updated_event
     * @covers \core_reportbuilder\event\schedule_deleted
     */
    public function test_schedule_deleted_event(array $persistents): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Re-create the persistents (remembering schedule ID which is removed from persistent upon deletion).
        [$report, $schedule] = $persistents;
        $report = new report($DB->insert_record(report::TABLE, $report->to_record()));

        $scheduleid = $DB->insert_record(schedule::TABLE, $schedule->to_record());
        $schedule = new schedule($scheduleid);

        // Catch the events.
        $sink = $this->redirectEvents();
        $schedule->delete();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(schedule_deleted::class, $event);
        $this->assertEquals(schedule::TABLE, $event->objecttable);
        $this->assertEquals($scheduleid, $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
        $this->assertEquals($report->get_context()->id, $event->contextid);
    }
}
