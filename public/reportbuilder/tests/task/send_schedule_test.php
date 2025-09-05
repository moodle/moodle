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

namespace core_reportbuilder\task;

use advanced_testcase;
use core_collator;
use core_reportbuilder_generator;
use core_reportbuilder\manager;
use core_reportbuilder\local\filters\user;
use core_reportbuilder\local\models\schedule;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\reportbuilder\audience\manual;
use core_user;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for ad-hoc task for sending report schedule
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\task\send_schedule
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class send_schedule_test extends advanced_testcase {
    /**
     * Test executing task where the schedule "View as user" is an inactive account
     */
    public function test_execute_report_viewas_user_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $audience = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);

        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'userviewas' => 42,
            'audiences' => json_encode([$audience->get_persistent()->get('id')]),
        ]);

        $this->expectOutputRegex("/^Sending schedule: My schedule \(Schedule an email\)\n" .
            "Invalid schedule view as user: Invalid user/");
        $sendschedule = new send_schedule();
        $sendschedule->set_custom_data(['reportid' => $report->get('id'), 'scheduleid' => $schedule->get('id')]);
        $sendschedule->execute();
    }

    /**
     * Test executing task for a schedule that contains no recipients
     */
    public function test_execute_schedule_no_recipients(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        $this->expectOutputString("Sending schedule: My schedule (Schedule an email)\n" .
            "Sending schedule complete\n");
        $sendschedule = new send_schedule();
        $sendschedule->set_custom_data(['reportid' => $report->get('id'), 'scheduleid' => $schedule->get('id')]);
        $sendschedule->execute();
    }

    /**
     * Test executing task where the schedule creator is an inactive account
     */
    public function test_execute_schedule_creator_invalid(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule', 'usercreated' => 42]);

        $this->expectOutputRegex("/^Sending schedule: My schedule \(Schedule an email\)\n" .
            "Invalid schedule creator: Invalid user/");
        $sendschedule = new send_schedule();
        $sendschedule->set_custom_data(['reportid' => $report->get('id'), 'scheduleid' => $schedule->get('id')]);
        $sendschedule->execute();
    }

    /**
     * Test executing task given invalid schedule data
     */
    public function test_execute_schedule_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectOutputString("Invalid schedule\n");
        $sendschedule = new send_schedule();
        $sendschedule->set_custom_data(['reportid' => $report->get('id'), 'scheduleid' => 42]);
        $sendschedule->execute();
    }
}
