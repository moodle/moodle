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
use context_system;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for manual report audience type
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\reportbuilder\audience\manual
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manual_test extends advanced_testcase {

    /**
     * Test that this audience type description is generated correctly
     */
    public function test_get_description(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $user1 = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => 'A']);
        $user2 = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => 'B']);

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $canviewfullnames = has_capability('moodle/site:viewfullnames', context_system::instance());
        $audience = manual::create($report->get('id'), ['users' => [$user1->id, $user2->id]]);
        $this->assertEquals(implode(', ', [fullname($user1, $canviewfullnames), fullname($user2, $canviewfullnames)]),
            $audience->get_description());
    }

    /**
     * Test if user can add this audience type to the report
     */
    public function test_user_can_add(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $audience = manual::create($report->get('id'), ['users' => [$user1->id, $user2->id]]);

        // Admin user.
        self::setAdminUser();
        $this->assertTrue($audience->user_can_add());

        // Non-priveleged user.
        $user = self::getDataGenerator()->create_user();
        self::setUser($user);
        $this->assertFalse($audience->user_can_add());

        // Grant priveleges to user.
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('moodle/user:viewalldetails', CAP_ALLOW, $roleid, context_system::instance()->id);
        role_assign($roleid, $user->id, context_system::instance()->id);
        $this->assertTrue($audience->user_can_add());
    }

    /**
     * Test if user can edit this audience type
     */
    public function test_user_can_edit(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $audience = manual::create($report->get('id'), ['users' => [$user1->id, $user2->id]]);

        // Admin user.
        self::setAdminUser();
        $this->assertTrue($audience->user_can_edit());

        // Non-priveleged user.
        $user = self::getDataGenerator()->create_user();
        self::setUser($user);
        $this->assertFalse($audience->user_can_edit());

        // Grant priveleges to user.
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('moodle/user:viewalldetails', CAP_ALLOW, $roleid, context_system::instance()->id);
        role_assign($roleid, $user->id, context_system::instance()->id);
        $this->assertTrue($audience->user_can_edit());
    }

    /**
     * Test that sql generated is correct
     */
    public function test_get_sql(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $audience = manual::create($report->get('id'), ['users' => [$user1->id, $user3->id]]);

        [$join, $where, $params] = $audience->get_sql('u');
        $query = 'SELECT u.* FROM {user} u ' . $join . ' WHERE ' . $where;
        $records = $DB->get_records_sql($query, $params);

        $this->assertEqualsCanonicalizing([$user1->id, $user3->id], array_column($records, 'id'));
    }
}
