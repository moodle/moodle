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

/**
 * Enrol manual external PHPunit tests
 *
 * @package    enrol_manual
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/enrol/manual/externallib.php');

class enrol_manual_external_testcase extends externallib_advanced_testcase {

    /**
     * Test get_enrolled_users
     */
    public function test_enrol_users() {
        global $USER, $CFG;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set the required capabilities by the external function.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('enrol/manual:enrol', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Add manager role to $USER.
        // So $USER is allowed to assign 'manager', 'editingteacher', 'teacher' and 'student'.
        role_assign(1, $USER->id, context_system::instance()->id);

        // Call the external function.
        enrol_manual_external::enrol_users(array(
            array('roleid' => 3, 'userid' => $user1->id, 'courseid' => $course->id),
            array('roleid' => 3, 'userid' => $user2->id, 'courseid' => $course->id)
        ));

        // Check we retrieve the good total number of enrolled users.
        require_once($CFG->dirroot . '/enrol/externallib.php');
        $enrolledusers = core_enrol_external::get_enrolled_users($course->id);
        $this->assertEquals(2, count($enrolledusers));

        // Call without required capability.
        $this->unassignUserCapability('enrol/manual:enrol', $context->id, $roleid);
        $this->setExpectedException('moodle_exception');
        $categories = enrol_manual_external::enrol_users($course->id);
    }
}