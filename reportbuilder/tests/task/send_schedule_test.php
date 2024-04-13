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
class send_schedule_test extends advanced_testcase {

    /**
     * Data provider for {@see test_execute_viewas_user}
     *
     * @return array[]
     */
    public static function execute_report_viewas_user_provider(): array {
        return [
            'View report as schedule creator' => [schedule::REPORT_VIEWAS_CREATOR, null, 'admin', 'admin'],
            'View report as schedule recipient' => [schedule::REPORT_VIEWAS_RECIPIENT, null, 'userone', 'usertwo'],
            'View report as specific user' => [null, 'userone', 'userone', 'userone'],
        ];
    }

    /**
     * Test executing task for a schedule with differing "View as user" configuration
     *
     * @param int|null $viewasuser
     * @param string|null $viewasusername
     * @param string $useronesees
     * @param string $usertwosees
     *
     * @dataProvider execute_report_viewas_user_provider
     */
    public function test_execute_report_viewas_user(
        ?int $viewasuser,
        ?string $viewasusername,
        string $useronesees,
        string $usertwosees
    ): void {
        $this->preventResetByRollback();
        $this->resetAfterTest();
        $this->setAdminUser();

        $userone = $this->getDataGenerator()->create_user([
            'username' => 'userone',
            'email' => 'user1@example.com',
            'firstname' => 'Zoe',
            'lastname' => 'Zebra',
        ]);
        $usertwo = $this->getDataGenerator()->create_user([
            'username' => 'usertwo',
            'email' => 'user2@example.com',
            'firstname' => 'Henrietta',
            'lastname' => 'Hamster',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create a report, with a single column and condition that the current user only sees themselves.
        $report = $generator->create_report(['name' => 'Myself', 'source' => users::class, 'default' => false]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username']);

        $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:userselect']);
        manager::get_report_from_persistent($report)
            ->set_condition_values(['user:userselect_operator' => user::USER_CURRENT]);

        // Add audience/schedule for our two test users.
        $audience = $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => manual::class,
            'configdata' => [
                'users' => [$userone->id, $usertwo->id],
            ],
        ]);

        // If "View as user" isn't specified, it should be the ID of the given "View as username".
        if ($viewasuser === null) {
            $viewasuser = core_user::get_user_by_username($viewasusername, '*', null, MUST_EXIST)->id;
        }
        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'userviewas' => $viewasuser,
            'audiences' => json_encode([$audience->get_persistent()->get('id')]),
        ]);

        // Send the schedule, catch emails in sink (noting the users are sorted alphabetically).
        $sink = $this->redirectEmails();

        $this->expectOutputRegex("/^Sending schedule: My schedule\n" .
            "  Sending to: " . fullname($usertwo) . "\n" .
            "  Sending to: " . fullname($userone) . "\n" .
            "Sending schedule complete\n/"
        );
        $sendschedule = new send_schedule();
        $sendschedule->set_custom_data(['reportid' => $report->get('id'), 'scheduleid' => $schedule->get('id')]);
        $sendschedule->execute();

        $messages = $sink->get_messages();
        $this->assertCount(2, $messages);

        $sink->close();

        // Ensure caught messages are consistently ordered by recipient email prior to assertions.
        core_collator::asort_objects_by_property($messages, 'to');
        $messages = array_values($messages);

        $messageoneattachment = self::extract_message_attachment($messages[0]->body);
        $this->assertEquals($userone->email, $messages[0]->to);
        $this->assertStringEndsWith("Username\n{$useronesees}\n", $messageoneattachment);

        $messagetwoattachment = self::extract_message_attachment($messages[1]->body);
        $this->assertEquals($usertwo->email, $messages[1]->to);
        $this->assertStringEndsWith("Username\n{$usertwosees}\n", $messagetwoattachment);
    }

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

        $this->expectOutputRegex("/^Sending schedule: My schedule\nInvalid schedule view as user: Invalid user/");
        $sendschedule = new send_schedule();
        $sendschedule->set_custom_data(['reportid' => $report->get('id'), 'scheduleid' => $schedule->get('id')]);
        $sendschedule->execute();
    }

    /**
     * Test executing task for a schedule that is configured to not send empty reports
     */
    public function test_execute_report_empty(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create a report that won't return any data.
        $report = $generator->create_report(['name' => 'Myself', 'source' => users::class, 'default' => false]);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username']);
        $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username']);

        manager::get_report_from_persistent($report)->set_condition_values([
            'user:username_operator' => text::IS_EQUAL_TO,
            'user:username_value' => 'baconlettucetomato',
        ]);

        $audience = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);
        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'audiences' => json_encode([$audience->get_persistent()->get('id')]),
            'reportempty' => schedule::REPORT_EMPTY_DONT_SEND,
        ]);

        $this->expectOutputString("Sending schedule: My schedule\n" .
            "  Empty report, skipping\n" .
            "Sending schedule complete\n");
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

        $this->expectOutputString("Sending schedule: My schedule\n" .
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

        $this->expectOutputRegex("/^Sending schedule: My schedule\nInvalid schedule creator: Invalid user/");
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

    /**
     * Given a multi-part message in MIME format, return the base64 encoded attachment contained within
     *
     * @param string $messagebody
     * @return string
     */
    private static function extract_message_attachment(string $messagebody): string {
        $mimepart = preg_split('/Content-Disposition: attachment; filename="My schedule.csv"\s+/m', $messagebody);

        // Extract the base64 encoded content after the "Content-Disposition" header.
        preg_match_all('/^([A-Z0-9\/\+=]+)\s/im', $mimepart[1], $matches);

        return base64_decode(implode($matches[0]));
    }
}
