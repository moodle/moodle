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
 * Availability role - Tests for role restrictions
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
               on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use availability_role\condition;

/**
 * Availability role - Unit tests for the condition
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
               on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_role_condition_testcase extends advanced_testcase {
    /**
     * Load required classes.
     */
    public function setUp() {
        // Load the mock info class so that it can be used.
        global $CFG;
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
    }

    /**
     * Tests constructing and using condition.
     */
    public function test_usage() {
        global $CFG, $USER, $DB;
        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Make a test course and user.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $teacher = $generator->create_user();
        $student = $generator->create_user();
        $roleswithnames = array();
        $contextroleids = get_roles_for_contextlevels(CONTEXT_COURSE);
        $roles = $DB->get_records_list('role', 'id', $contextroleids, '', 'shortname, id');
        $generator->enrol_user($student->id, $course->id, $roles['student']->id);
        $generator->enrol_user($teacher->id, $course->id, $roles['editingteacher']->id);
        $studentinfo = new \core_availability\mock_info($course, $student->id);
        $teacherinfo = new \core_availability\mock_info($course, $teacher->id);

        // Do test (not in grouping).
        $structure = (object)array('type' => 'role', 'id' => (int) $roles['editingteacher']->id);
        $cond = new condition($structure);

        // Check if available (when not available).
        $this->assertFalse($cond->is_available(false, $studentinfo, true, $student->id));
        $this->assertTrue($cond->is_available(false, $teacherinfo, true, $teacher->id));
        $information = $cond->get_description(false, false, $studentinfo);
    }

    /**
     * Tests the save() function.
     */
    public function test_save() {
        $structure = (object)array('id' => 123);
        $cond = new condition($structure);
        $structure->type = 'role';
        $this->assertEquals($structure, $cond->save());
    }
}
