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

namespace core_reportbuilder\privacy;

use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types\database_table;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\local\helpers\user_filter_manager;
use core_reportbuilder\local\models\{audience, column, filter, report, schedule, user_filter};
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for privacy provider
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\privacy\provider
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    /**
     * Test provider metadata
     */
    public function test_get_metadata(): void {
        $collection = new collection('core_reportbuilder');
        $metadata = provider::get_metadata($collection)->get_collection();

        $this->assertCount(6, $metadata);
        $this->assertContainsOnlyInstancesOf(database_table::class, $metadata);

        $this->assertEquals(report::TABLE, $metadata[0]->get_name());
        $this->assertEquals(column::TABLE, $metadata[1]->get_name());
        $this->assertEquals(filter::TABLE, $metadata[2]->get_name());
        $this->assertEquals(user_filter::TABLE, $metadata[3]->get_name());
        $this->assertEquals(audience::TABLE, $metadata[4]->get_name());
        $this->assertEquals(schedule::TABLE, $metadata[5]->get_name());
    }

    /**
     * Test getting contexts for user who created a report
     */
    public function test_get_contexts_for_userid_report(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $generator->create_report(['name' => 'Users', 'source' => users::class]);

        $contextlist = $this->get_contexts_for_userid((int) $user->id, 'core_reportbuilder');
        $this->assertCount(1, $contextlist);
        $this->assertInstanceOf(context_system::class, $contextlist->current());
    }

    /**
     * Test getting contexts for user who created a user filter for a report by another user
     */
    public function test_get_contexts_for_userid_user_filter(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class]);

        // Switch user, create a report audience.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        user_filter_manager::set($report->get('id'), ['entity:filter_name' => 1]);

        $contextlist = $this->get_contexts_for_userid((int) $user->id, 'core_reportbuilder');
        $this->assertCount(1, $contextlist);
        $this->assertInstanceOf(context_system::class, $contextlist->current());
    }

    /**
     * Test getting contexts for user who created an audience for a report by another user
     */
    public function test_get_contexts_for_userid_audience(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class]);

        // Switch user, create a report audience.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);

        $contextlist = $this->get_contexts_for_userid((int) $user->id, 'core_reportbuilder');
        $this->assertCount(1, $contextlist);
        $this->assertInstanceOf(context_system::class, $contextlist->current());
    }

    /**
     * Test getting contexts for user who created a schedule for a report by another user
     */
    public function test_get_contexts_for_userid_schedule(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class]);

        // Switch user, create a report schedule.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        $contextlist = $this->get_contexts_for_userid((int) $user->id, 'core_reportbuilder');
        $this->assertCount(1, $contextlist);
        $this->assertInstanceOf(context_system::class, $contextlist->current());
    }

    /**
     * Test getting users in given context
     */
    public function test_get_users_in_context(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Switch user, create a report.
        $reportuser = $this->getDataGenerator()->create_user();
        $this->setUser($reportuser);

        $report = $generator->create_report(['name' => 'Users', 'source' => users::class]);

        // Switch user, create a user filter.
        $filteruser  = $this->getDataGenerator()->create_user();
        $this->setUser($filteruser);

        user_filter_manager::set($report->get('id'), ['entity:filter_name' => 1]);

        // Switch user, create a report audience.
        $audienceuser = $this->getDataGenerator()->create_user();
        $this->setUser($audienceuser);

        $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);

        // Switch user, create a report schedule.
        $scheduleuser = $this->getDataGenerator()->create_user();
        $this->setUser($scheduleuser);

        $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        $userlist = new userlist(context_system::instance(), 'core_reportbuilder');
        provider::get_users_in_context($userlist);

        $this->assertEqualsCanonicalizing([
            $reportuser->id,
            $filteruser->id,
            $audienceuser->id,
            $scheduleuser->id,
        ], $userlist->get_userids());
    }

    /**
     * Test export of user data
     */
    public function test_export_user_data(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create some report elements for the user.
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $audience = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => [], 'heading' => 'Beans']);
        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'audiences' => json_encode([$audience->get_persistent()->get('id')]),
            'name' => 'My schedule',
        ]);

        user_filter_manager::set($report->get('id'), ['entity:filter_name' => 1]);

        $context = context_system::instance();
        $this->export_context_data_for_user((int) $user->id, $context, 'core_reportbuilder');

        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        $subcontext = provider::get_export_subcontext($report);

        // Exported report data.
        $reportdata = $writer->get_data($subcontext);
        $this->assertEquals($report->get_formatted_name(), $reportdata->name);
        $this->assertEquals(users::get_name(), $reportdata->source);
        $this->assertEquals($user->id, $reportdata->usercreated);
        $this->assertEquals($user->id, $reportdata->usermodified);
        $this->assertNotEmpty($reportdata->timecreated);
        $this->assertNotEmpty($reportdata->timemodified);

        // Exported user filter data.
        $userfilterdata = $writer->get_related_data($subcontext, 'userfilters')->data;

        $this->assertCount(1, $userfilterdata);
        $userfilterdata = reset($userfilterdata);

        $this->assertEquals('{"entity:filter_name":1}', $userfilterdata->filterdata);
        $this->assertNotEmpty($userfilterdata->timecreated);
        $this->assertNotEmpty($userfilterdata->timemodified);

        // Exported audience data.
        $audiencedata = $writer->get_related_data($subcontext, 'audiences')->data;

        $this->assertCount(1, $audiencedata);
        $audiencedata = reset($audiencedata);

        $audiencepersistent = $audience->get_persistent();
        $audienceclassname = $audiencepersistent->get('classname');

        $this->assertEquals($audienceclassname::instance()->get_name(), $audiencedata->classname);
        $this->assertEquals($audiencepersistent->get('configdata'), $audiencedata->configdata);
        $this->assertEquals($audiencepersistent->get_formatted_heading(), $audiencedata->heading);
        $this->assertEquals($user->id, $audiencedata->usercreated);
        $this->assertEquals($user->id, $audiencedata->usermodified);
        $this->assertNotEmpty($audiencedata->timecreated);
        $this->assertNotEmpty($audiencedata->timemodified);

        // Exported schedule data.
        $scheduledata = $writer->get_related_data($subcontext, 'schedules')->data;

        $this->assertCount(1, $scheduledata);
        $scheduledata = reset($scheduledata);

        $this->assertEquals($schedule->get_formatted_name(), $scheduledata->name);
        $this->assertEquals('Yes', $scheduledata->enabled);
        $this->assertEquals('Comma separated values (.csv)', $scheduledata->format);
        $this->assertNotEmpty($scheduledata->timescheduled);
        $this->assertEquals('None', $scheduledata->recurrence);
        $this->assertEquals('Schedule creator', $scheduledata->userviewas);
        $this->assertEquals(json_encode([$audiencepersistent->get('id')]), $scheduledata->audiences);
        $this->assertEquals($schedule->get('subject'), $scheduledata->subject);
        $this->assertEquals(format_text($schedule->get('message'), $schedule->get('messageformat')), $scheduledata->message);
        $this->assertEquals('Send message with empty report', $scheduledata->reportempty);
        $this->assertEquals($user->id, $scheduledata->usercreated);
        $this->assertEquals($user->id, $scheduledata->usermodified);
        $this->assertNotEmpty($scheduledata->timecreated);
        $this->assertNotEmpty($scheduledata->timemodified);
    }

    /**
     * Test export of user data where there is nothing to export
     */
    public function test_export_user_data_empty(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $context = context_system::instance();
        $this->export_context_data_for_user((int) $user->id, $context, 'core_reportbuilder');

        $this->assertFalse(writer::with_context($context)->has_any_data());
    }
}
