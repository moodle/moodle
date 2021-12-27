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
use core_reportbuilder\event\schedule_created;
use core_reportbuilder\event\schedule_deleted;
use core_reportbuilder\event\schedule_updated;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the schedule model
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule_test extends advanced_testcase {

    /**
     * Tests for schedule_created event
     *
     * @covers \core_reportbuilder\event\schedule_created
     */
    public function test_schedule_created_event(): void {
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
    }

    /**
     * Tests for schedule_deleted event
     *
     * @covers \core_reportbuilder\event\schedule_deleted
     */
    public function test_schedule_deleted_event(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);
        $scheduleid = $schedule->get('id');

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
    }

    /**
     * Tests for schedule_updated event
     *
     * @covers \core_reportbuilder\event\schedule_updated
     */
    public function test_schedule_updated_event(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        // Catch the events.
        $sink = $this->redirectEvents();
        $schedule->set('name', 'My new schedule');
        $schedule->update();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf(schedule_updated::class, $event);
        $this->assertEquals(schedule::TABLE, $event->objecttable);
        $this->assertEquals($schedule->get('id'), $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
    }
}
