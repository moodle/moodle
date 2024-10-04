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

namespace mod_lti\external;

use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/lti/tests/mod_lti_testcase.php');

/**
 * PHPUnit tests for delete_course_tool_type external function.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_lti\external\delete_course_tool_type
 */
final class delete_course_tool_type_test extends \mod_lti_testcase {

    /**
     * Test delete_course_tool() for a course tool.
     * @covers ::execute
     */
    public function test_delete_course_tool(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($editingteacher);

        $typeid = lti_add_type(
            (object) [
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => $course->id
            ],
            (object) [
                'lti_typename' => "My course tool",
                'lti_toolurl' => 'http://example.com',
                'lti_ltiversion' => 'LTI-1p0'
            ]
        );

        $data = delete_course_tool_type::execute($typeid);
        $data = external_api::clean_returnvalue(delete_course_tool_type::execute_returns(), $data);

        $this->assertTrue($data);
    }

    /**
     * Test delete_course_tool() for a site tool, which is forbidden.
     * @covers ::execute
     */
    public function test_delete_course_tool_site_tool(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($editingteacher);

        $type = $this->generate_tool_type(123); // Creates a site tool.

        $this->expectException(\invalid_parameter_exception::class);
        delete_course_tool_type::execute($type->id);
    }
}
