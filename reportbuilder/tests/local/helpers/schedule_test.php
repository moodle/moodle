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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use core_reportbuilder_generator;
use invalid_parameter_exception;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the schedule helper class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\schedule
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule_test extends advanced_testcase {

    /**
     * Test create schedule
     */
    public function test_create_schedule(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $timescheduled = time() + DAYSECS;
        $schedule = schedule::create_schedule((object) [
            'name' => 'My schedule',
            'reportid' => $report->get('id'),
            'format' => 'csv',
            'subject' => 'Hello',
            'message' => 'Hola',
            'timescheduled' => $timescheduled,
        ]);

        $this->assertEquals('My schedule', $schedule->get('name'));
        $this->assertEquals($report->get('id'), $schedule->get('reportid'));
        $this->assertEquals('csv', $schedule->get('format'));
        $this->assertEquals('Hello', $schedule->get('subject'));
        $this->assertEquals('Hola', $schedule->get('message'));
        $this->assertEquals($timescheduled, $schedule->get('timescheduled'));
    }

    /**
     * Test update schedule
     */
    public function test_update_schedule(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        // Update some record properties.
        $record = $schedule->to_record();
        $record->name = 'My updated schedule';
        $record->timescheduled = 1861340400; // 25/12/2028 07:00 UTC.

        $schedule = schedule::update_schedule($record);
        $this->assertEquals($record->name, $schedule->get('name'));
        $this->assertEquals($record->timescheduled, $schedule->get('timescheduled'));
    }

    /**
     * Test update invalid schedule
     */
    public function test_update_schedule_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid schedule');
        schedule::update_schedule((object) ['id' => 42, 'reportid' => $report->get('id')]);
    }

    /**
     * Test toggle schedule
     */
    public function test_toggle_schedule(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        // Disable the schedule.
        schedule::toggle_schedule($report->get('id'), $schedule->get('id'), false);
        $schedule = $schedule->read();
        $this->assertFalse($schedule->get('enabled'));

        // Enable the schedule.
        schedule::toggle_schedule($report->get('id'), $schedule->get('id'), true);
        $schedule = $schedule->read();
        $this->assertTrue($schedule->get('enabled'));
    }

    /**
     * Test toggle invalid schedule
     */
    public function test_toggle_schedule_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid schedule');
        schedule::toggle_schedule($report->get('id'), 42, true);
    }

    /**
     * Test delete schedule
     */
    public function test_delete_schedule(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        $scheduleid = $schedule->get('id');

        schedule::delete_schedule($report->get('id'), $scheduleid);
        $this->assertFalse($schedule::record_exists($scheduleid));
    }

    /**
     * Test delete invalid schedule
     */
    public function test_delete_schedule_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid schedule');
        schedule::delete_schedule($report->get('id'), 42);
    }
}
