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

namespace core_cohort\customfield;

use advanced_testcase;
use context_system;
use context_coursecat;
use moodle_url;
use core_customfield\field_controller;

/**
 * Unit tests for cohort custom field handler.
 *
 * @package    core_cohort
 * @covers     \core_cohort\customfield\cohort_handler
 * @copyright  2022 Dmitrii Metelkin <dmitriim@catalys-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_handler_test extends advanced_testcase {
    /**
     * Test custom field handler.
     * @var \core_customfield\handler
     */
    protected $handler;

    /**
     * Setup.
     */
    public function setUp(): void {
        parent::setUp();
        $this->handler = cohort_handler::create();
    }

    /**
     * Create Cohort custom field for testing.
     *
     * @return field_controller
     */
    protected function create_cohort_custom_field(): field_controller {
        $fieldcategory = self::getDataGenerator()->create_custom_field_category([
            'component' => 'core_cohort',
            'area' => 'cohort',
        ]);

        return self::getDataGenerator()->create_custom_field([
            'shortname' => 'testfield1',
            'type' => 'text',
            'categoryid' => $fieldcategory->get('id'),
        ]);
    }

    /**
     * Test configuration context.
     */
    public function test_get_configuration_context(): void {
        $this->assertInstanceOf(context_system::class, $this->handler->get_configuration_context());
    }

    /**
     * Test getting config URL.
     */
    public function test_get_configuration_url(): void {
        $this->assertInstanceOf(moodle_url::class, $this->handler->get_configuration_url());
        $this->assertEquals('/cohort/customfield.php', $this->handler->get_configuration_url()->out_as_local_url());
    }

    /**
     * Test can configure check.
     */
    public function test_can_configure(): void {
        $this->resetAfterTest();

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->assertFalse($this->handler->can_configure());

        $roleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/cohort:configurecustomfields', CAP_ALLOW, $roleid, context_system::instance()->id, true);
        role_assign($roleid, $user->id, context_system::instance()->id);

        $this->assertTrue($this->handler->can_configure());
    }

    /**
     * Test getting instance context.
     */
    public function test_get_instance_context(): void {
        $this->resetAfterTest();

        $category = self::getDataGenerator()->create_category();
        $catcontext = context_coursecat::instance($category->id);
        $systemcontext = context_system::instance();
        $cohortsystem = self::getDataGenerator()->create_cohort();
        $cohortcategory = self::getDataGenerator()->create_cohort(['contextid' => $catcontext->id]);

        $this->assertInstanceOf(context_system::class, $this->handler->get_instance_context($cohortsystem->id));
        $this->assertSame($systemcontext, $this->handler->get_instance_context($cohortsystem->id));

        $this->assertInstanceOf(context_coursecat::class, $this->handler->get_instance_context($cohortcategory->id));
        $this->assertSame($catcontext, $this->handler->get_instance_context($cohortcategory->id));
    }

    /**
     * Test can edit functionality.
     */
    public function test_can_edit(): void {
        $this->resetAfterTest();

        $roleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/cohort:manage', CAP_ALLOW, $roleid, context_system::instance()->id, true);

        $field = $this->create_cohort_custom_field();

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->assertFalse($this->handler->can_edit($field, 0));

        role_assign($roleid, $user->id, context_system::instance()->id);
        $this->assertTrue($this->handler->can_edit($field, 0));
    }

    /**
     * Test can view functionality.
     */
    public function test_can_view(): void {
        $this->resetAfterTest();

        $manageroleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/cohort:manage', CAP_ALLOW, $manageroleid, context_system::instance()->id, true);

        $viewroleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/cohort:view', CAP_ALLOW, $viewroleid, context_system::instance()->id, true);

        $viewandmanageroleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/cohort:manage', CAP_ALLOW, $viewandmanageroleid, context_system::instance()->id, true);
        assign_capability('moodle/cohort:view', CAP_ALLOW, $viewandmanageroleid, context_system::instance()->id, true);

        $field = $this->create_cohort_custom_field();
        $cohort = self::getDataGenerator()->create_cohort();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        self::setUser($user1);
        $this->assertFalse($this->handler->can_view($field, $cohort->id));

        self::setUser($user2);
        $this->assertFalse($this->handler->can_view($field, $cohort->id));

        self::setUser($user3);
        $this->assertFalse($this->handler->can_view($field, $cohort->id));

        role_assign($manageroleid, $user1->id, context_system::instance()->id);
        role_assign($viewroleid, $user2->id, context_system::instance()->id);
        role_assign($viewandmanageroleid, $user3->id, context_system::instance()->id);

        self::setUser($user1);
        $this->assertTrue($this->handler->can_view($field, $cohort->id));

        self::setUser($user2);
        $this->assertTrue($this->handler->can_view($field, $cohort->id));

        self::setUser($user3);
        $this->assertTrue($this->handler->can_view($field, $cohort->id));
    }
}
