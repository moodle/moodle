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
 * Tests for the capability overview helper functions.
 *
 * @package   tool_capability
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace tool_capability;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/capability/locallib.php');


/**
 * Tests for the capability overview helper functions.
 */
final class locallib_test extends \advanced_testcase {

    /**
     * Test the function that gets the data - simple case.
     */
    public function test_tool_capability_calculate_role_data(): void {
        global $DB;

        $data = tool_capability_calculate_role_data('mod/quiz:attempt', get_all_roles());

        $systcontext = \context_system::instance();
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);

        $this->assertArrayHasKey($systcontext->id, $data);
        $this->assertCount(1, $data);
        foreach ($data[$systcontext->id]->rolecapabilities as $roleid => $permission) {
            if ($roleid == $studentroleid) {
                $this->assertEquals(CAP_ALLOW, $permission);
            } else {
                $this->assertEquals(CAP_INHERIT, $permission);
            }
        }
    }

    /**
     * Test the function that gets the data - simple case.
     */
    public function test_tool_capability_calculate_role_data_orphan_contexts(): void {
        global $DB;
        $this->resetAfterTest();

        // This simulates a situation that seems to happen sometimes, where
        // we end up with contexts with path = NULL in the database.
        $systcontext = \context_system::instance();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        role_change_permission($studentroleid, $coursecontext, 'mod/quiz:attempt', CAP_PREVENT);
        // This is where we simulate the breakage.
        $DB->set_field('context', 'path', null, ['id' => $coursecontext->id]);

        // Now call the function. We mainly just want to know there is no exception.
        $data = tool_capability_calculate_role_data('mod/quiz:attempt', get_all_roles());

        $this->assertArrayHasKey($systcontext->id, $data);
        $this->assertCount(1, $data);
        foreach ($data[$systcontext->id]->rolecapabilities as $roleid => $permission) {
            if ($roleid == $studentroleid) {
                $this->assertEquals(CAP_ALLOW, $permission);
            } else {
                $this->assertEquals(CAP_INHERIT, $permission);
            }
        }
    }
}
