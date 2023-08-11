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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/lti/tests/mod_lti_testcase.php');

/**
 * PHPUnit tests for toggle_showinactivitychooser external function.
 *
 * @package    mod_lti
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_lti\external\toggle_showinactivitychooser
 */
class toggle_showinactivitychooser_test extends \mod_lti_testcase {

    /**
     * Test toggle_showinactivitychooser for course tool.
     * @covers ::execute
     */
    public function test_toggle_showinactivitychooser_course_tool() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($editingteacher);

        $typeid = lti_add_type(
            (object) [
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => $course->id,
                'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER
            ],
            (object) [
                'lti_typename' => "My course tool",
                'lti_toolurl' => 'http://example.com',
                'lti_ltiversion' => 'LTI-1p0',
                'lti_coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER
            ]
        );
        toggle_showinactivitychooser::execute($typeid, $course->id, false);
        $sql = "SELECT lt.coursevisible coursevisible1, ltc.value AS coursevisible2
                  FROM {lti_types} lt
             LEFT JOIN {lti_types_config} ltc ON lt.id = ltc.typeid
                 WHERE lt.id = ?
                   AND ltc.name = 'coursevisible'";
        $actual = $DB->get_record_sql($sql, [$typeid]);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $actual->coursevisible1);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $actual->coursevisible2);

        toggle_showinactivitychooser::execute($typeid, $course->id, true);
        $actual = $DB->get_record_sql($sql, [$typeid]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible1);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible2);
    }

    /**
     * Test toggle_showinactivitychooser for site tool.
     * @covers ::execute
     */
    public function test_toggle_showinactivitychooser_site_tool() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($editingteacher);

        $type = $this->generate_tool_type(123); // Creates a site tool.

        toggle_showinactivitychooser::execute($type->id, $course->id, false);
        $sql = "SELECT lt.coursevisible coursevisible1, ltc.value AS coursevisible2, lc.coursevisible AS coursevisible3
                  FROM {lti_types} lt
             LEFT JOIN {lti_types_config} ltc ON lt.id = ltc.typeid
             LEFT JOIN {lti_coursevisible} lc ON lt.id = lc.typeid
                 WHERE lt.id = ?
                   AND lc.courseid = ?
                   AND ltc.name = 'coursevisible'";
        $actual = $DB->get_record_sql($sql, [$type->id, $course->id]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible1);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible2);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $actual->coursevisible3);

        toggle_showinactivitychooser::execute($type->id, $course->id, true);
        $actual = $DB->get_record_sql($sql, [$type->id, $course->id]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible1);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible2);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible3);
    }

}
