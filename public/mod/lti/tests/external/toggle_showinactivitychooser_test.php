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
 * PHPUnit tests for toggle_showinactivitychooser external function.
 *
 * @package    mod_lti
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_lti\external\toggle_showinactivitychooser
 */
final class toggle_showinactivitychooser_test extends \mod_lti_testcase {

    /**
     * Test toggle_showinactivitychooser for course tool.
     * @covers ::execute
     */
    public function test_toggle_showinactivitychooser_course_tool(): void {
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
        $result = toggle_showinactivitychooser::execute($typeid, $course->id, false);
        $result = external_api::clean_returnvalue(toggle_showinactivitychooser::execute_returns(), $result);
        $this->assertTrue($result);

        $sql = "SELECT lt.coursevisible coursevisible
                  FROM {lti_types} lt
                 WHERE lt.id = ?";
        $actual = $DB->get_record_sql($sql, [$typeid]);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $actual->coursevisible);

        $result = toggle_showinactivitychooser::execute($typeid, $course->id, true);
        $result = external_api::clean_returnvalue(toggle_showinactivitychooser::execute_returns(), $result);
        $this->assertTrue($result);
        $actual = $DB->get_record_sql($sql, [$typeid]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible);
    }

    /**
     * Test toggle_showinactivitychooser for site tool.
     * @covers ::execute
     */
    public function test_toggle_showinactivitychooser_site_tool(): void {
        global $DB;

        $this->resetAfterTest();

        $coursecat1 = $this->getDataGenerator()->create_category();
        $coursecat2 = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $coursecat1->id]);

        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($editingteacher);

        $type = $this->generate_tool_type(123); // Creates a site tool.

        $result = toggle_showinactivitychooser::execute($type->id, $course->id, false);
        $result = external_api::clean_returnvalue(toggle_showinactivitychooser::execute_returns(), $result);
        $this->assertTrue($result);

        $sql = "SELECT lt.coursevisible coursevisible1, lc.coursevisible AS coursevisible2
                  FROM {lti_types} lt
             LEFT JOIN {lti_coursevisible} lc ON lt.id = lc.typeid
                 WHERE lt.id = ?
                   AND lc.courseid = ?";
        $actual = $DB->get_record_sql($sql, [$type->id, $course->id]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible1);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $actual->coursevisible2);

        $result = toggle_showinactivitychooser::execute($type->id, $course->id, true);
        $result = external_api::clean_returnvalue(toggle_showinactivitychooser::execute_returns(), $result);
        $this->assertTrue($result);

        $actual = $DB->get_record_sql($sql, [$type->id, $course->id]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible1);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible2);

        $ltigenerator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured and activity chooser, restricted to category 1',
            'baseurl' => 'http://example.com/tool/1',
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'lti_coursecategories' => $coursecat1->id
        ]);
        $tool = $DB->get_record('lti_types', ['name' => 'site tool preconfigured and activity chooser, restricted to category 1']);
        $result = toggle_showinactivitychooser::execute($tool->id, $course->id, false);
        $result = external_api::clean_returnvalue(toggle_showinactivitychooser::execute_returns(), $result);
        $this->assertTrue($result);

        $actual = $DB->get_record_sql($sql, [$tool->id, $course->id]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $actual->coursevisible1);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $actual->coursevisible2);

        $ltigenerator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured and activity chooser, restricted to category 2',
            'baseurl' => 'http://example.com/tool/1',
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'lti_coursecategories' => $coursecat2->id
        ]);
        $tool = $DB->get_record('lti_types', ['name' => 'site tool preconfigured and activity chooser, restricted to category 2']);
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('You are not allowed to change this setting for this tool.');
        toggle_showinactivitychooser::execute($tool->id, $course->id, true);

        $ltigenerator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $ltigenerator->create_tool_types([
            'name' => 'site tool dont show',
            'baseurl' => 'http://example.com/tool/1',
            'coursevisible' => LTI_COURSEVISIBLE_NO,
            'state' => LTI_TOOL_STATE_CONFIGURED,
        ]);
        $tool = $DB->get_record('lti_types', ['name' => 'site tool dont show']);
        $result = toggle_showinactivitychooser::execute($tool->id, $course->id, false);
        $result = external_api::clean_returnvalue(toggle_showinactivitychooser::execute_returns(), $result);
        $this->assertFalse($result);

    }

}
