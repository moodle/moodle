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
use core_reportbuilder\event\audience_created;
use core_reportbuilder\event\audience_deleted;
use core_reportbuilder\event\audience_updated;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the audience model
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience_test extends advanced_testcase {

    /**
     * Tests for audience_created event
     *
     * @covers \core_reportbuilder\event\audience_created
     */
    public function test_audience_created_event(): void {
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
        $audience = $generator->create_audience([
            'reportid' => $report->get('id'),
            'configdata' => [],
        ]);
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf(audience_created::class, $event);
        $this->assertEquals(audience::TABLE, $event->objecttable);
        $this->assertEquals($audience->get_persistent()->get('id'), $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
    }

    /**
     * Tests for audience_deleted event
     *
     * @covers \core_reportbuilder\event\audience_deleted
     */
    public function test_audience_deleted_event(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $audience = $generator->create_audience([
            'reportid' => $report->get('id'),
            'configdata' => [],
        ]);
        $audienceid = $audience->get_persistent()->get('id');

        // Catch the events.
        $sink = $this->redirectEvents();
        $audience->get_persistent()->delete();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf(audience_deleted::class, $event);
        $this->assertEquals(audience::TABLE, $event->objecttable);
        $this->assertEquals($audienceid, $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
    }

    /**
     * Tests for audience_updated event
     *
     * @covers \core_reportbuilder\event\audience_updated
     */
    public function test_audience_updated_event(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $audience = $generator->create_audience([
            'reportid' => $report->get('id'),
            'configdata' => [],
        ]);

        // Catch the events.
        $sink = $this->redirectEvents();
        $audience->get_persistent()->set('heading', 'Hello');
        $audience->get_persistent()->update();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf(audience_updated::class, $event);
        $this->assertEquals(audience::TABLE, $event->objecttable);
        $this->assertEquals($audience->get_persistent()->get('id'), $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
    }
}
