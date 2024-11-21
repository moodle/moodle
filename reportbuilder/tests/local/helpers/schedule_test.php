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
use invalid_parameter_exception;
use core\clock;
use core_cohort\reportbuilder\audience\cohortmember;
use core_reportbuilder_generator;
use core_reportbuilder\local\models\schedule as model;
use core_reportbuilder\reportbuilder\audience\manual;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the schedule helper class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\schedule
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class schedule_test extends advanced_testcase {

    /** @var clock $clock */
    private readonly clock $clock;

    /**
     * Mock the clock
     */
    protected function setUp(): void {
        parent::setUp();
        $this->clock = $this->mock_clock_with_frozen(1622847600);
    }

    /**
     * Test create schedule
     */
    public function test_create_schedule(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Create schedule for tomorrow.
        $timescheduled = $this->clock->time() + DAYSECS;
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
        $this->assertEquals($timescheduled, $schedule->get('timenextsend'));
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
        $record->timescheduled += (12 * HOURSECS);

        $schedule = schedule::update_schedule($record);
        $this->assertEquals($record->name, $schedule->get('name'));
        $this->assertEquals($record->timescheduled, $schedule->get('timescheduled'));
        $this->assertEquals($record->timescheduled, $schedule->get('timenextsend'));
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

    /**
     * Test getting schedule report users (those in matching audience)
     */
    public function test_get_schedule_report_users(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Create cohort, with some members.
        $cohort = $this->getDataGenerator()->create_cohort();
        $cohortuserone = $this->getDataGenerator()->create_user(['firstname' => 'Zoe', 'lastname' => 'Zebra']);
        cohort_add_member($cohort->id, $cohortuserone->id);
        $cohortusertwo = $this->getDataGenerator()->create_user(['firstname' => 'Henrietta', 'lastname' => 'Hamster']);
        cohort_add_member($cohort->id, $cohortusertwo->id);

        // Create a third user, to be added manually.
        $manualuserone = $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Badger']);

        $audiencecohort = $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => cohortmember::class,
            'configdata' => ['cohorts' => [$cohort->id]],
        ]);

        $audiencemanual = $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$manualuserone->id]],
        ]);

        // Now create our schedule.
        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'audiences' => json_encode([
                $audiencecohort->get_persistent()->get('id'),
                $audiencemanual->get_persistent()->get('id'),
            ]),
        ]);

        $users = schedule::get_schedule_report_users($schedule);
        $this->assertEquals([
            'Bob',
            'Henrietta',
            'Zoe',
        ], array_column($users, 'firstname'));

        // Now delete one of our users, ensure they are no longer returned.
        delete_user($manualuserone);

        $users = schedule::get_schedule_report_users($schedule);
        $this->assertEquals([
            'Henrietta',
            'Zoe',
        ], array_column($users, 'firstname'));
    }

    /**
     * Test getting schedule report row count
     */
    public function test_get_schedule_report_count(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        // There is only one row in the report (the only user on the site).
        $count = schedule::get_schedule_report_count($schedule);
        $this->assertDebuggingCalled();
        $this->assertEquals(1, $count);
    }

    /**
     * Data provider for {@see test_get_schedule_report_file}
     *
     * @return string[]
     */
    public static function get_schedule_report_file_format(): array {
        return [
            ['csv'],
            ['excel'],
            ['html'],
            ['json'],
            ['ods'],
            ['pdf'],
        ];
    }

    /**
     * Test getting schedule report exported file, in each supported format
     *
     * @param string $format
     *
     * @dataProvider get_schedule_report_file_format
     */
    public function test_get_schedule_report_file(string $format): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule', 'format' => $format]);

        // There is only one row in the report (the only user on the site).
        $file = schedule::get_schedule_report_file($schedule);
        $this->assertGreaterThan(64, $file->get_filesize());
    }

    /**
     * Data provider for {@see test_should_send_schedule}
     *
     * @return array[]
     */
    public static function should_send_schedule_provider(): array {
        return [
            'Time scheduled in the past' => [
                model::RECURRENCE_NONE, '-1 hour', null, null, true,
            ],
            'Time scheduled in the past, already sent prior to schedule' => [
                model::RECURRENCE_NONE, '-1 hour', '-2 hour', null, true,
            ],
            'Time scheduled in the past, already sent on schedule' => [
                model::RECURRENCE_NONE, '-1 hour', '-1 hour', null, false,
            ],
            'Time scheduled in the future' => [
                model::RECURRENCE_NONE, '+1 hour', null, null, false,
            ],
            'Time scheduled in the future, already sent prior to schedule' => [
                model::RECURRENCE_NONE, '+1 hour', '-1 hour', null, false,
            ],
            'Next send in the past' => [
                model::RECURRENCE_DAILY, '-1 hour', null, '-1 hour', true,
            ],
            'Next send in the future' => [
                model::RECURRENCE_DAILY, '-1 hour', null, '+1 hour', false,
            ],
        ];
    }

    /**
     * Test for whether a schedule should be sent
     *
     * @param int $recurrence
     * @param string $timescheduled Relative time suitable for passing to {@see strtotime}
     * @param string|null $timelastsent Relative time suitable for passing to {@see strtotime}, or null to ignore
     * @param string|null $timenextsend Relative time suitable for passing to {@see strtotime}, or null to ignore
     * @param bool $expected
     *
     * @dataProvider should_send_schedule_provider
     */
    public function test_should_send_schedule(
        int $recurrence,
        string $timescheduled,
        ?string $timelastsent,
        ?string $timenextsend,
        bool $expected,
    ): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Use relative time period if present, otherwise default to zero (never sent).
        $scheduletimelastsent = 0;
        if ($timelastsent !== null) {
            $scheduletimelastsent = strtotime($timelastsent, $this->clock->time());
        }

        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'recurrence' => $recurrence,
            'timescheduled' => strtotime($timescheduled, $this->clock->time()),
            'timelastsent' => $scheduletimelastsent,
        ]);

        // If "Time next send" is specified, then override calculated value.
        if ($timenextsend !== null) {
            $schedule->set('timenextsend', strtotime($timenextsend, $this->clock->time()));
        }

        $this->assertEquals($expected, schedule::should_send_schedule($schedule));
    }

    /**
     * Test for whether a schedule should be sent that has been disabled
     */
    public function test_should_send_schedule_disabled(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'enabled' => 0,
        ]);

        $this->assertFalse(schedule::should_send_schedule($schedule));
    }

    /**
     * Data provider for {@see test_calculate_next_send_time}
     *
     * @return array[]
     */
    public static function calculate_next_send_time_provider(): array {
        // Times are based on the current clock time (Fri Jun 04 2021 23:00:00 UTC).
        return [
            'No recurrence' => [
                model::RECURRENCE_NONE, '2021-06-03 12:00', '2021-06-03 12:00',
            ],
            'Recurrence, time scheduled in future' => [
                model::RECURRENCE_DAILY, '2021-06-05 12:00', '2021-06-05 12:00',
            ],
            'Daily recurrence' => [
                model::RECURRENCE_DAILY, '2021-06-02 12:00', '2021-06-05 12:00',
            ],
            'Weekday recurrence' => [
                model::RECURRENCE_WEEKDAYS, '2021-06-02 12:00', '2021-06-07 12:00',
            ],
            'Weekly recurrence' => [
                model::RECURRENCE_WEEKLY, '2021-05-18 12:00', '2021-06-08 12:00',
            ],
            'Monthy recurrence' => [
                model::RECURRENCE_MONTHLY, '2021-03-19 12:00', '2021-06-19 12:00',
            ],
            'Annual recurrence' => [
                model::RECURRENCE_ANNUALLY, '2019-05-01 12:00', '2022-05-01 12:00',
            ],
         ];
    }

    /**
     * Test for calculating next schedule send time
     *
     * @param int $recurrence
     * @param string $timescheduled Absolute time suitable for passing to {@see strtotime}
     * @param string $expected Absolute time suitable for passing to {@see strtotime}
     *
     * @dataProvider calculate_next_send_time_provider
     */
    public function test_calculate_next_send_time(int $recurrence, string $timescheduled, string $expected): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Create model manually, as the generator automatically calculates next send itself.
        $schedule = new model(0, (object) [
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'recurrence' => $recurrence,
            'timescheduled' => strtotime("{$timescheduled} UTC"),
        ]);

        $scheduleexpected = strtotime("{$expected} UTC");
        $this->assertEquals($scheduleexpected, schedule::calculate_next_send_time($schedule));
    }
}
