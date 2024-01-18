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
use context_system;
use core_reportbuilder_generator;
use core_reportbuilder\reportbuilder\audience\manual;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for audience helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\audience
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience_test extends advanced_testcase {

     /**
      * Test reports list is empty for a normal user without any audience records configured
      */
    public function test_reports_list_no_access(): void {
        $this->resetAfterTest();

        $reports = audience::user_reports_list();
        $this->assertEmpty($reports);
    }

    /**
     * Test get_base_records()
     */
    public function test_get_base_records(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Report with no audiences.
        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);
        $baserecords = audience::get_base_records($report->get('id'));
        $this->assertEmpty($baserecords);

        // Create a couple of manual audience types.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $audience1 = $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user1->id, $user2->id]],
        ]);

        $user3 = $this->getDataGenerator()->create_user();
        $audience2 = $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user3->id]],
        ]);

        $baserecords = audience::get_base_records($report->get('id'));
        $this->assertCount(2, $baserecords);
        $this->assertContainsOnlyInstancesOf(manual::class, $baserecords);

        // Set invalid classname of first audience, should be excluded in subsequent request.
        $audience1->get_persistent()->set('classname', '\invalid')->save();

        $baserecords = audience::get_base_records($report->get('id'));
        $this->assertCount(1, $baserecords);

        $baserecord = reset($baserecords);
        $this->assertInstanceOf(manual::class, $baserecord);
        $this->assertEquals($audience2->get_persistent()->get('id'), $baserecord->get_persistent()->get('id'));
    }

    /**
     * Test get_allowed_reports()
     */
    public function test_get_allowed_reports(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        self::setUser($user1);

        // No reports.
        $reports = audience::get_allowed_reports();
        $this->assertEmpty($reports);

        $report1 = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);
        $report2 = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);
        $report3 = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        // Reports with no audiences set.
        $reports = audience::get_allowed_reports();
        $this->assertEmpty($reports);

        $generator->create_audience([
            'reportid' => $report1->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user1->id, $user2->id]],
        ]);
        $generator->create_audience([
            'reportid' => $report2->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user2->id]],
        ]);
        $generator->create_audience([
            'reportid' => $report3->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user1->id]],
        ]);

        // Purge cache, to ensure allowed reports are re-calculated.
        audience::purge_caches();

        $reports = audience::get_allowed_reports();
        $this->assertEqualsCanonicalizing([$report1->get('id'), $report3->get('id')], $reports);

        // User2 can access report1 and report2.
        $reports = audience::get_allowed_reports((int) $user2->id);
        $this->assertEqualsCanonicalizing([$report1->get('id'), $report2->get('id')], $reports);

        // Purge cache, to ensure allowed reports are re-calculated.
        audience::purge_caches();

        // Now delete one of our users, ensure they no longer have any allowed reports.
        delete_user($user2);

        $reports = audience::get_allowed_reports((int) $user2->id);
        $this->assertEmpty($reports);
    }

    /**
     * Test user_reports_list()
     */
    public function test_user_reports_list(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        self::setUser($user1);

        $reports = audience::user_reports_list();
        $this->assertEmpty($reports);

        $report1 = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);
        $report2 = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);
        $report3 = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $generator->create_audience([
            'reportid' => $report1->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user1->id, $user2->id]],
        ]);
        $generator->create_audience([
            'reportid' => $report2->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user2->id]],
        ]);
        $generator->create_audience([
            'reportid' => $report3->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user1->id]],
        ]);

        // Purge cache, to ensure allowed reports are re-calculated.
        audience::purge_caches();

        // User1 can access report1 and report3.
        $reports = audience::user_reports_list();
        $this->assertEqualsCanonicalizing([$report1->get('id'), $report3->get('id')], $reports);

        // User2 can access report1 and report2.
        $reports = audience::user_reports_list((int) $user2->id);
        $this->assertEqualsCanonicalizing([$report1->get('id'), $report2->get('id')], $reports);

        // User3 can not access any report.
        $reports = audience::user_reports_list((int) $user3->id);
        $this->assertEmpty($reports);
    }

    /**
     * Test retrieving full list of reports that user can access
     */
    public function test_user_reports_list_access_sql(): void {
        global $DB;

        $this->resetAfterTest();

        $userone = $this->getDataGenerator()->create_user();
        $usertwo = $this->getDataGenerator()->create_user();
        $userthree = $this->getDataGenerator()->create_user();
        $userfour = $this->getDataGenerator()->create_user();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Manager role gives users one and two capability to create own reports.
        $managerrole = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerrole, $userone->id, context_system::instance());
        role_assign($managerrole, $usertwo->id, context_system::instance());

        // Admin creates a report, no audience.
        $this->setAdminUser();
        $useradminreport = $generator->create_report(['name' => 'Admin report', 'source' => users::class]);

        // User one creates a report, adds users two and three to audience.
        $this->setUser($userone);
        $useronereport = $generator->create_report(['name' => 'User one report', 'source' => users::class]);
        $generator->create_audience(['reportid' => $useronereport->get('id'), 'classname' => manual::class, 'configdata' => [
            'users' => [$usertwo->id, $userthree->id],
        ]]);

        // User two creates a report, no audience.
        $this->setUser($usertwo);
        $usertworeport = $generator->create_report(['name' => 'User two report', 'source' => users::class]);

        // User one sees only the report they created.
        [$where, $params] = audience::user_reports_list_access_sql('r', (int) $userone->id);
        $reports = $DB->get_fieldset_sql("SELECT r.id FROM {reportbuilder_report} r WHERE {$where}", $params);
        $this->assertEquals([$useronereport->get('id')], $reports);

        // User two see the report they created and the one they are in the audience of.
        [$where, $params] = audience::user_reports_list_access_sql('r', (int) $usertwo->id);
        $reports = $DB->get_fieldset_sql("SELECT r.id FROM {reportbuilder_report} r WHERE {$where}", $params);
        $this->assertEqualsCanonicalizing([$useronereport->get('id'), $usertworeport->get('id')], $reports);

        // User three sees the report they are in the audience of.
        [$where, $params] = audience::user_reports_list_access_sql('r', (int) $userthree->id);
        $reports = $DB->get_fieldset_sql("SELECT r.id FROM {reportbuilder_report} r WHERE {$where}", $params);
        $this->assertEquals([$useronereport->get('id')], $reports);

        // User four sees no reports.
        [$where, $params] = audience::user_reports_list_access_sql('r', (int) $userfour->id);
        $reports = $DB->get_fieldset_sql("SELECT r.id FROM {reportbuilder_report} r WHERE {$where}", $params);
        $this->assertEmpty($reports);
    }

    /**
     * Data provider for {@see test_user_reports_list_access_sql_with_capability}
     *
     * @return array[]
     */
    public static function user_reports_list_access_sql_with_capability_provider(): array {
        return [
            ['moodle/reportbuilder:editall'],
            ['moodle/reportbuilder:viewall'],
        ];
    }

    /**
     * Test retrieving list of reports that user can access observes capability to view all reports
     *
     * @param string $capability
     *
     * @dataProvider user_reports_list_access_sql_with_capability_provider
     */
    public function test_user_reports_list_access_sql_with_capability(string $capability): void {
        global $DB;

        $this->resetAfterTest();

        // Admin creates a report, no audience.
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Admin report', 'source' => users::class]);

        // Switch to new user, assign capability.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability($capability, CAP_ALLOW, $userrole, context_system::instance());

        [$where, $params] = audience::user_reports_list_access_sql('r');
        $reports = $DB->get_fieldset_sql("SELECT r.id FROM {reportbuilder_report} r WHERE {$where}", $params);
        $this->assertEquals([$report->get('id')], $reports);
    }

    /**
     * Test getting list of audiences in use within schedules for a report
     */
    public function test_get_audiences_for_report_schedules(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $audienceone = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);
        $audiencetwo = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);
        $audiencethree = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);

        // The first schedule contains audience one and two.
        $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'Schedule one', 'audiences' =>
            json_encode([$audienceone->get_persistent()->get('id'), $audiencetwo->get_persistent()->get('id')])
        ]);

        // Second schedule contains only audience one.
        $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'Schedule two', 'audiences' =>
            json_encode([$audienceone->get_persistent()->get('id')])
        ]);

        // The first two audiences should be returned, the third omitted.
        $audiences = audience::get_audiences_for_report_schedules($report->get('id'));
        $this->assertEqualsCanonicalizing([
            $audienceone->get_persistent()->get('id'),
            $audiencetwo->get_persistent()->get('id'),
        ], $audiences);
    }
}
