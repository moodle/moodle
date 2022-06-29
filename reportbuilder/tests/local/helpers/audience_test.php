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

        // Report with no audiences.
        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

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
        $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user1->id, $user2->id]],
        ]);
        $user3 = $this->getDataGenerator()->create_user();
        $generator->create_audience([
            'reportid' => $report->get('id'),
            'classname' => manual::class,
            'configdata' => ['users' => [$user3->id]],
        ]);

        $baserecords = audience::get_base_records($report->get('id'));
        $this->assertCount(2, $baserecords);
        $this->assertInstanceOf(manual::class, $baserecords[0]);
        $this->assertInstanceOf(manual::class, $baserecords[1]);
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
     * Test get_all_audiences_menu_types()
     */
    public function test_get_all_audiences_menu_types(): void {
        $this->resetAfterTest();

        // Test with user that has no permission to add audiences.
        $user1 = $this->getDataGenerator()->create_user();
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('moodle/user:viewalldetails', CAP_PROHIBIT, $roleid, context_system::instance()->id);
        role_assign($roleid, $user1->id, context_system::instance()->id);
        self::setUser($user1);
        $categories = audience::get_all_audiences_menu_types();
        $this->assertEmpty($categories);

        self::setAdminUser();
        $categories = audience::get_all_audiences_menu_types();
        $category = array_filter($categories, function ($category) {
            return $category['name'] === 'Site';
        });
        $category = reset($category);
        // We don't use assertEqual here to avoid this test failing when more audience types get created.
        $this->assertGreaterThanOrEqual(3, $category['items']);
    }
}
