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

namespace core_reportbuilder\reportbuilder\audience;

use advanced_testcase;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the administrators audience type
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\reportbuilder\audience\admins
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class admins_test extends advanced_testcase {

    /**
     * Test whether user can add this audience
     */
    public function test_user_can_add(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $audience = admins::create($report->get('id'), []);
        $this->assertFalse($audience->user_can_add());

        // Switch to privileged user.
        $this->setAdminUser();
        $this->assertTrue($audience->user_can_add());
    }

    /**
     * Test whether user can edit this audience
     */
    public function test_user_can_edit(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $audience = admins::create($report->get('id'), []);
        $this->assertFalse($audience->user_can_edit());

        // Switch to privileged user.
        $this->setAdminUser();
        $this->assertTrue($audience->user_can_edit());
    }

    /**
     * Test retrieving audience SQL for matching users
     */
    public function test_get_sql(): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Create three users, set only the initial two as admins.
        $userone = $this->getDataGenerator()->create_user();
        $usertwo = $this->getDataGenerator()->create_user();
        $userthree = $this->getDataGenerator()->create_user();

        $CFG->siteadmins = "{$userone->id},{$usertwo->id}";

        $audience = admins::create($report->get('id'), []);
        [$join, $select, $params] = $audience->get_sql('u');

        $users = $DB->get_fieldset_sql("SELECT u.id FROM {user} u {$join} WHERE {$select}", $params);
        $this->assertEqualsCanonicalizing([$userone->id, $usertwo->id], $users);
    }

    /**
     * Test showing audience description
     */
    public function test_get_description(): void {
        global $CFG;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Create three users, set only the initial two as admins.
        $userone = $this->getDataGenerator()->create_user();
        $usertwo = $this->getDataGenerator()->create_user();
        $userthree = $this->getDataGenerator()->create_user();

        $CFG->siteadmins = "{$userone->id},{$usertwo->id}";

        $audience = admins::create($report->get('id'), []);
        $this->assertEquals(fullname($userone) . ', ' . fullname($usertwo), $audience->get_description());
    }
}
