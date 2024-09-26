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

namespace core_reportbuilder;

use advanced_testcase;
use context_system;
use core_reportbuilder\exception\report_access_exception;
use core_reportbuilder_generator;
use Throwable;
use core_user\reportbuilder\datasource\users;
use core_reportbuilder\reportbuilder\audience\manual;

/**
 * Unit tests for the report permission class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\permission
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class permission_test extends advanced_testcase {

    /**
     * Test whether user can view reports list
     */
    public function test_require_can_view_reports_list(): void {
        global $DB;

        $this->resetAfterTest();

        // User with default permission.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        try {
            permission::require_can_view_reports_list();
        } catch (Throwable $exception) {
            $this->fail($exception->getMessage());
        }

        // User without permission.
        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        unassign_capability('moodle/reportbuilder:view', $userrole, context_system::instance());

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot view this report');
        permission::require_can_view_reports_list();
    }

    /**
     * Data provider for {@see test_require_can_view_reports_list_with_capability}
     *
     * @return array[]
     */
    public static function require_can_view_reports_list_with_capability_provider(): array {
        return [
            ['moodle/reportbuilder:edit'],
            ['moodle/reportbuilder:editall'],
            ['moodle/reportbuilder:viewall'],
        ];
    }

    /**
     * Test that viewing reports list observes capability to do so
     *
     * @param string $capability
     *
     * @dataProvider require_can_view_reports_list_with_capability_provider
     */
    public function test_require_can_view_reports_list_with_capability(string $capability): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);

        // Remove default capability, allow additional.
        unassign_capability('moodle/reportbuilder:view', $userrole, context_system::instance());
        assign_capability($capability, CAP_ALLOW, $userrole, context_system::instance());

        try {
            permission::require_can_view_reports_list();
        } catch (Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /**
     * Test whether user can view reports list when custom reports are disabled
     */
    public function test_require_can_view_reports_list_disabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('enablecustomreports', 0);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot view this report');
        permission::require_can_view_reports_list();
    }

    /**
     * Test whether user can view specific report
     */
    public function test_require_can_view_report(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // User with permission.
        $this->setAdminUser();
        try {
            permission::require_can_view_report($report);
        } catch (Throwable $exception) {
            $this->fail($exception->getMessage());
        }

        // User without permission.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot view this report');
        permission::require_can_view_report($report);
    }

    /**
     * Data provider for {@see test_require_can_view_report_with_capability}
     *
     * @return array[]
     */
    public static function require_can_view_report_with_capability_provider(): array {
        return [
            ['moodle/reportbuilder:editall'],
            ['moodle/reportbuilder:viewall'],
        ];
    }

    /**
     * Test whether user can view specific report when they have capability to view all reports
     *
     * @param string $capability
     *
     * @dataProvider require_can_view_report_with_capability_provider
     */
    public function test_require_can_view_report_with_capability(string $capability): void {
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

        try {
            permission::require_can_view_report($report);
        } catch (Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /**
     * Test whether user can view specific report when it belongs to an audience
     */
    public function test_require_can_view_report_with_audience(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // User without permission.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user->id]],
        ]);

        // User has view capability and belongs to an audience.
        permission::require_can_view_report($report);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        unassign_capability('moodle/reportbuilder:view', $userrole, context_system::instance());

        // User does not have view capability and belongs to an audience.
        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot view this report');
        permission::require_can_view_report($report);
    }

    /**
     * Test whether user can view report when custom reports are disabled
     */
    public function test_require_can_view_report_disabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('enablecustomreports', 0);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot view this report');
        permission::require_can_view_report($report);
    }

    /**
     * Test that user cannot edit system reports
     */
    public function test_require_can_edit_report_system_report(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();
        $this->setAdminUser();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot edit this report');
        permission::require_can_edit_report($systemreport->get_report_persistent());
    }

    /**
     * Test that user cannot edit any reports without capabilities
     */
    public function test_require_can_edit_report_none(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'User', 'source' => users::class]);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot edit this report');
        permission::require_can_edit_report($report);
    }

    /**
     * Test that user can edit their own reports
     */
    public function test_require_can_edit_report_own(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability('moodle/reportbuilder:edit', CAP_ALLOW, $userrole, context_system::instance());

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Confirm user can edit their own report.
        $reportuser = $generator->create_report(['name' => 'User', 'source' => users::class]);
        permission::require_can_edit_report($reportuser);

        // Create a report by another user, confirm current user cannot edit it.
        $reportadmin = $generator->create_report(['name' => 'Admin', 'source' => users::class, 'usercreated' => get_admin()->id]);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot edit this report');
        permission::require_can_edit_report($reportadmin);
    }

    /**
     * Test that user can edit any reports
     */
    public function test_require_can_edit_report_all(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability('moodle/reportbuilder:editall', CAP_ALLOW, $userrole, context_system::instance());

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Confirm user can edit their own report.
        $reportuser = $generator->create_report(['name' => 'User', 'source' => users::class]);
        permission::require_can_edit_report($reportuser);

        // Create a report by another user, confirm current user can edit it.
        $reportadmin = $generator->create_report(['name' => 'Admin', 'source' => users::class, 'usercreated' => get_admin()->id]);
        permission::require_can_edit_report($reportadmin);
    }

    /**
     * Test whether user can edit report when custom reports are disabled
     */
    public function test_require_can_edit_report_disabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('enablecustomreports', 0);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot edit this report');
        permission::require_can_edit_report($report);
    }

    /**
     * Test that user can create a new report
     */
    public function test_require_can_create_report(): void {
        $this->resetAfterTest();

        // User has edit capability.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('moodle/reportbuilder:edit', CAP_ALLOW, $roleid, context_system::instance());
        role_assign($roleid, $user->id, context_system::instance()->id);

        try {
            permission::require_can_create_report((int)$user->id);
        } catch (Throwable $exception) {
            $this->fail($exception->getMessage());
        }

        // User has editall capability.
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $roleid2 = create_role('Dummy role 2', 'dummyrole2', 'dummy role 2 description');
        assign_capability('moodle/reportbuilder:editall', CAP_ALLOW, $roleid2, context_system::instance());
        role_assign($roleid2, $user2->id, context_system::instance()->id);

        try {
            permission::require_can_create_report((int)$user2->id);
        } catch (Throwable $exception) {
            $this->fail($exception->getMessage());
        }

        // User has no capability.
        $user3 = $this->getDataGenerator()->create_user();
        $this->setUser($user3);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot create a new report');
        permission::require_can_create_report((int)$user3->id);
    }

    /**
     * Test whether user can create report when custom reports are disabled
     */
    public function test_require_can_create_report_disabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('enablecustomreports', 0);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot create a new report');
        permission::require_can_create_report();
    }

    /**
     * Data provider for {@see test_can_create_report_limit_reached}
     *
     * @return array
     */
    public static function can_create_report_limit_reached_provider(): array {
        return [
            [0, 1, true],
            [1, 1, false],
            [2, 1, true],
            [1, 2, false],
        ];
    }

    /**
     * Test whether user can create report when limit report are reache
     * @param int $customreportslimit
     * @param int $existingreports
     * @param bool $expected
     * @dataProvider can_create_report_limit_reached_provider
     */
    public function test_can_create_report_limit_reached(int $customreportslimit, int $existingreports, bool $expected): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        for ($i = 1; $i <= $existingreports; $i++) {
            $generator->create_report(['name' => 'Report limited '.$i, 'source' => users::class]);
        }

        // Set current custom report limit, and check whether user can create reports.
        $CFG->customreportslimit = $customreportslimit;
        $this->assertEquals($expected, permission::can_create_report());
    }

    /**
     * Test that user can duplicate a report
     */
    public function test_require_can_duplicate_report(): void {
        global $DB, $CFG;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability('moodle/reportbuilder:edit', CAP_ALLOW, $userrole, context_system::instance());

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Confirm user can duplicate their own report.
        $reportuser = $generator->create_report(['name' => 'User', 'source' => users::class]);
        permission::require_can_duplicate_report($reportuser);

        // Create a report by another user, confirm current user cannot duplicate it without proper permission.
        $reportadmin = $generator->create_report(['name' => 'Admin', 'source' => users::class, 'usercreated' => get_admin()->id]);
        try {
            permission::require_can_duplicate_report($reportadmin);
            $this->fail('Exception expected');
        } catch (report_access_exception $e) {
            $this->assertStringContainsString('You cannot duplicate this report', $e->getMessage());
        }

        assign_capability('moodle/reportbuilder:editall', CAP_ALLOW, $userrole, context_system::instance());
        permission::require_can_duplicate_report($reportadmin);

        // Set current custom report limit, and check whether user can duplicate the report.
        $CFG->customreportslimit = 1;
        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot duplicate this report');
        permission::require_can_duplicate_report($reportuser);
    }
}
