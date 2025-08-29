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

namespace core_enrol;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/enrol/externallib.php');

/**
 * Role external PHPunit tests
 *
 * @package    core_enrol
 * @category   test
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */
final class role_external_test extends \core_external\tests\externallib_testcase {
    /**
     * Tests set up
     */
    protected function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/externallib.php');
        parent::setUp();
    }

    /**
     * Test assign_roles
     */
    public function test_assign_roles(): void {
        global $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/role:assign', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Add manager role to $USER.
        // So $USER is allowed to assign 'manager', 'editingteacher', 'teacher' and 'student'.
        role_assign(1, $USER->id, \context_system::instance()->id);

        // Check the teacher role has not been assigned to $USER.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 0);

        // Call the external function. Assign teacher role to $USER with contextid.
        \core_role_external::assign_roles(array(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id)));

        // Check the role has been assigned.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 1);

        // Unassign role.
        role_unassign(3, $USER->id, $context->id);
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 0);

        // Call the external function. Assign teacher role to $USER.
        \core_role_external::assign_roles(array(
            array('roleid' => 3, 'userid' => $USER->id, 'contextlevel' => "course", 'instanceid' => $course->id)));
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 1);

        // Call without required capability.
        $this->unassignUserCapability('moodle/role:assign', $context->id, $roleid);
        $this->expectException('moodle_exception');
        $categories = \core_role_external::assign_roles(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id));
    }

    /**
     * Test unassign_roles
     */
    public function test_unassign_roles(): void {
        global $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $context = \context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/role:assign', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Add manager role to $USER.
        // So $USER is allowed to assign 'manager', 'editingteacher', 'teacher' and 'student'.
        role_assign(1, $USER->id, \context_system::instance()->id);

        // Add teacher role to $USER on course context.
        role_assign(3, $USER->id, $context->id);

        // Check the teacher role has been assigned to $USER on course context.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 1);

        // Call the external function. Unassign teacher role using contextid.
        \core_role_external::unassign_roles(array(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id)));

        // Check the role has been unassigned on course context.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 0);

        // Add teacher role to $USER on course context.
        role_assign(3, $USER->id, $context->id);
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 1);

        // Call the external function. Unassign teacher role using context level and instanceid.
        \core_role_external::unassign_roles(array(
            array('roleid' => 3, 'userid' => $USER->id, 'contextlevel' => "course", 'instanceid' => $course->id)));

        // Check the role has been unassigned on course context.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 0);

        // Call without required capability.
        $this->unassignUserCapability('moodle/role:assign', $context->id, $roleid);
        $this->expectException('moodle_exception');
        $categories = \core_role_external::unassign_roles(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id));
    }
}
