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
use core_reportbuilder\event\audience_created;
use core_reportbuilder\event\audience_deleted;
use core_reportbuilder\event\audience_updated;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the audience model
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\models\audience
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class audience_test extends advanced_testcase {

    /**
     * Tests for audience_created event
     *
     * @return persistent[]
     *
     * @covers \core_reportbuilder\event\audience_created
     */
    public function test_audience_created_event(): array {
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
        $audience = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []])
            ->get_persistent();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(audience_created::class, $event);
        $this->assertEquals(audience::TABLE, $event->objecttable);
        $this->assertEquals($audience->get('id'), $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
        $this->assertEquals($report->get_context()->id, $event->contextid);

        return [$report, $audience];
    }

    /**
     * Tests for audience_updated event
     *
     * @param persistent[] $persistents
     * @return persistent[]
     *
     * @depends test_audience_created_event
     * @covers \core_reportbuilder\event\audience_updated
     */
    public function test_audience_updated_event(array $persistents): array {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Re-create the persistents.
        [$report, $audience] = $persistents;
        $report = new report($DB->insert_record(report::TABLE, $report->to_record()));
        $audience = new audience($DB->insert_record(audience::TABLE, $audience->to_record()));

        // Catch the events.
        $sink = $this->redirectEvents();
        $audience->set('heading', 'Hello')->update();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(audience_updated::class, $event);
        $this->assertEquals(audience::TABLE, $event->objecttable);
        $this->assertEquals($audience->get('id'), $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
        $this->assertEquals($report->get_context()->id, $event->contextid);

        return [$report, $audience];
    }

    /**
     * Tests for audience_deleted event
     *
     * @param persistent[] $persistents
     *
     * @depends test_audience_updated_event
     * @covers \core_reportbuilder\event\audience_deleted
     */
    public function test_audience_deleted_event(array $persistents): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Re-create the persistents (remembering audience ID which is removed from persistent upon deletion).
        [$report, $audience] = $persistents;
        $report = new report($DB->insert_record(report::TABLE, $report->to_record()));

        $audienceid = $DB->insert_record(audience::TABLE, $audience->to_record());
        $audience = new audience($audienceid);

        // Catch the events.
        $sink = $this->redirectEvents();
        $audience->delete();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf(audience_deleted::class, $event);
        $this->assertEquals(audience::TABLE, $event->objecttable);
        $this->assertEquals($audienceid, $event->objectid);
        $this->assertEquals($report->get('id'), $event->other['reportid']);
        $this->assertEquals($report->get_context()->id, $event->contextid);
    }
}
