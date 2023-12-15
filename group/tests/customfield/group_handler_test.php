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

namespace core_group\customfield;

use advanced_testcase;
use context_course;
use context_system;
use moodle_url;
use core_customfield\field_controller;

/**
 * Unit tests for group custom field handler.
 *
 * @package   core_group
 * @covers    \core_group\customfield\group_handler
 * @author    Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @copyright 2023 Catalyst IT Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_handler_test extends advanced_testcase {
    /**
     * Test custom field handler.
     * @var group_handler
     */
    protected $handler;

    /**
     * Setup.
     */
    public function setUp(): void {
        $this->handler = group_handler::create();
    }

    /**
     * Create group custom field for testing.
     *
     * @return field_controller
     */
    protected function create_group_custom_field(): field_controller {
        $fieldcategory = self::getDataGenerator()->create_custom_field_category([
            'component' => 'core_group',
            'area' => 'group',
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
    public function test_get_configuration_context() {
        $this->assertInstanceOf(context_system::class, $this->handler->get_configuration_context());
    }

    /**
     * Test getting config URL.
     */
    public function test_get_configuration_url() {
        $this->assertInstanceOf(moodle_url::class, $this->handler->get_configuration_url());
        $this->assertEquals('/group/customfield.php', $this->handler->get_configuration_url()->out_as_local_url());
    }

    /**
     * Test getting instance context.
     */
    public function test_get_instance_context() {
        global $COURSE;
        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $group = self::getDataGenerator()->create_group(['courseid' => $course->id]);

        $this->assertInstanceOf(context_course::class, $this->handler->get_instance_context());
        $this->assertSame(context_course::instance($COURSE->id), $this->handler->get_instance_context());

        $this->assertInstanceOf(context_course::class, $this->handler->get_instance_context($group->id));
        $this->assertSame(context_course::instance($course->id), $this->handler->get_instance_context($group->id));
    }

    /**
     * Test can configure check.
     */
    public function test_can_configure() {
        $this->resetAfterTest();

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->assertFalse($this->handler->can_configure());

        $roleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/group:configurecustomfields', CAP_ALLOW, $roleid, context_system::instance()->id, true);
        role_assign($roleid, $user->id, context_system::instance()->id);

        $this->assertTrue($this->handler->can_configure());
    }

    /**
     * Test can edit functionality.
     */
    public function test_can_edit() {
        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $contextid = context_course::instance($course->id)->id;
        $group = self::getDataGenerator()->create_group(['courseid' => $course->id]);
        $roleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/course:managegroups', CAP_ALLOW, $roleid, $contextid, true);

        $field = $this->create_group_custom_field();

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $this->assertFalse($this->handler->can_edit($field, $group->id));

        role_assign($roleid, $user->id, $contextid);
        $this->assertTrue($this->handler->can_edit($field, $group->id));
    }

    /**
     * Test can view functionality.
     */
    public function test_can_view() {
        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $contextid = context_course::instance($course->id)->id;
        $group = self::getDataGenerator()->create_group(['courseid' => $course->id]);
        $manageroleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/course:managegroups', CAP_ALLOW, $manageroleid, $contextid, true);

        $viewroleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/course:view', CAP_ALLOW, $viewroleid, $contextid, true);

        $viewandmanageroleid = self::getDataGenerator()->create_role();
        assign_capability('moodle/course:managegroups', CAP_ALLOW, $viewandmanageroleid, $contextid, true);
        assign_capability('moodle/course:view', CAP_ALLOW, $viewandmanageroleid, $contextid, true);

        $field = $this->create_group_custom_field();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        self::setUser($user1);
        $this->assertFalse($this->handler->can_view($field, $group->id));

        self::setUser($user2);
        $this->assertFalse($this->handler->can_view($field, $group->id));

        self::setUser($user3);
        $this->assertFalse($this->handler->can_view($field, $group->id));

        role_assign($manageroleid, $user1->id, $contextid);
        role_assign($viewroleid, $user2->id, $contextid);
        role_assign($viewandmanageroleid, $user3->id, $contextid);

        self::setUser($user1);
        $this->assertTrue($this->handler->can_view($field, $group->id));

        self::setUser($user2);
        $this->assertTrue($this->handler->can_view($field, $group->id));

        self::setUser($user3);
        $this->assertTrue($this->handler->can_view($field, $group->id));
    }
}
