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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

namespace mod_lti\local;

use mod_lti_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/mod/lti/tests/mod_lti_testcase.php');

/**
 * Types helper tests.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_lti\local\types_helper
 */
final class types_helper_test extends mod_lti_testcase {

    /**
     * Test fetching tool types for a given course and user.
     *
     * @covers ::get_lti_types_by_course
     * @return void.
     */
    public function test_get_lti_types_by_course(): void {
        $this->resetAfterTest();

        global $DB;
        $coursecat1 = $this->getDataGenerator()->create_category();
        $coursecat2 = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $coursecat1->id]);
        $course2 = $this->getDataGenerator()->create_course(['category' => $coursecat2->id]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $teacher2 = $this->getDataGenerator()->create_and_enrol($course2, 'editingteacher');

        $this->setUser($teacher);

        // Create the following tool types for testing:
        // - Site tool configured as "Do not show" (LTI_COURSEVISIBLE_NO).
        // - Site tool configured as "Show as a preconfigured tool only" (LTI_COURSEVISIBLE_PRECONFIGURED).
        // - Site tool configured as "Show as a preconfigured tool and in the activity chooser" (LTI_COURSEVISIBLE_ACTIVITYCHOOSER).
        // - Course tool which, by default, is configured as LTI_COURSEVISIBLE_ACTIVITYCHOOSER).
        // - Site tool configured to "Show as a preconfigured tool and in the activity chooser" but restricted to a category.

        /** @var \mod_lti_generator $ltigenerator */
        $ltigenerator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $ltigenerator->create_tool_types([
            'name' => 'site tool do not show',
            'baseurl' => 'http://example.com/tool/1',
            'coursevisible' => LTI_COURSEVISIBLE_NO,
            'state' => LTI_TOOL_STATE_CONFIGURED
        ]);
        $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured only',
            'baseurl' => 'http://example.com/tool/2',
            'coursevisible' => LTI_COURSEVISIBLE_PRECONFIGURED,
            'state' => LTI_TOOL_STATE_CONFIGURED
        ]);
        $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured and activity chooser',
            'baseurl' => 'http://example.com/tool/3',
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'state' => LTI_TOOL_STATE_CONFIGURED
        ]);
        $ltigenerator->create_course_tool_types([
            'name' => 'course tool preconfigured and activity chooser',
            'baseurl' => 'http://example.com/tool/4',
            'course' => $course->id
        ]);
        $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured and activity chooser, restricted to category 2',
            'baseurl' => 'http://example.com/tool/5',
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'lti_coursecategories' => $coursecat2->id
        ]);

        // Request using the default 'coursevisible' param will include all tools except the one configured as "Do not show" and
        // the tool restricted to category 2.
        $coursetooltypes = types_helper::get_lti_types_by_course($course->id, $teacher->id);
        $this->assertCount(3, $coursetooltypes);
        $expected = [
            'http://example.com/tool/2',
            'http://example.com/tool/3',
            'http://example.com/tool/4',
        ];
        sort($expected);
        $actual = array_column($coursetooltypes, 'baseurl');
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Request for only those tools configured to show in the activity chooser for the teacher.
        $coursetooltypes = types_helper::get_lti_types_by_course($course->id, $teacher->id,
            [LTI_COURSEVISIBLE_ACTIVITYCHOOSER]);
        $this->assertCount(2, $coursetooltypes);
        $expected = [
            'http://example.com/tool/3',
            'http://example.com/tool/4',
        ];
        sort($expected);
        $actual = array_column($coursetooltypes, 'baseurl');
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Request for only those tools configured to show as a preconfigured tool for the teacher.
        $coursetooltypes = types_helper::get_lti_types_by_course($course->id, $teacher->id,
            [LTI_COURSEVISIBLE_PRECONFIGURED]);
        $this->assertCount(1, $coursetooltypes);
        $expected = [
            'http://example.com/tool/2',
        ];
        $actual = array_column($coursetooltypes, 'baseurl');
        $this->assertEquals($expected, $actual);

        // Request for teacher2 in course2 (course category 2).
        $coursetooltypes = types_helper::get_lti_types_by_course($course2->id, $teacher2->id);
        $this->assertCount(3, $coursetooltypes);
        $expected = [
            'http://example.com/tool/2',
            'http://example.com/tool/3',
            'http://example.com/tool/5',
        ];
        sort($expected);
        $actual = array_column($coursetooltypes, 'baseurl');
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Request for a teacher who cannot use preconfigured tools in the course.
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        assign_capability('mod/lti:addpreconfiguredinstance', CAP_PROHIBIT, $teacherrole->id,
            \core\context\course::instance($course->id));
        $coursetooltypes = types_helper::get_lti_types_by_course($course->id, $teacher->id);
        $this->assertCount(0, $coursetooltypes);
    }

    /**
     * Test fetching tool types for a given course and user.
     *
     * @covers ::override_type_showinactivitychooser
     * @return void.
     */
    public function test_override_type_showinactivitychooser(): void {
        $this->resetAfterTest();

        global $DB;
        $coursecat1 = $this->getDataGenerator()->create_category();
        $coursecat2 = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $coursecat1->id]);
        $course2 = $this->getDataGenerator()->create_course(['category' => $coursecat2->id]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $teacher2 = $this->getDataGenerator()->create_and_enrol($course2, 'editingteacher');
        $context =  \core\context\course::instance($course->id);

        $this->setUser($teacher);

        /*
            Create the following tool types for testing:
            | tooltype | coursevisible                     | restrictedtocategory |
            | site     | LTI_COURSEVISIBLE_NO              |                      |
            | site     | LTI_COURSEVISIBLE_PRECONFIGURED   |                      |
            | site     | LTI_COURSEVISIBLE_ACTIVITYCHOOSER | yes                  |
            | site     | LTI_COURSEVISIBLE_ACTIVITYCHOOSER | yes                  |
            | course   | LTI_COURSEVISIBLE_ACTIVITYCHOOSER |                      |
        */

        /** @var \mod_lti_generator $ltigenerator */
        $ltigenerator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $tool1id = $ltigenerator->create_tool_types([
            'name' => 'site tool do not show',
            'baseurl' => 'http://example.com/tool/1',
            'coursevisible' => LTI_COURSEVISIBLE_NO,
            'state' => LTI_TOOL_STATE_CONFIGURED
        ]);
        $tool2id = $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured only',
            'baseurl' => 'http://example.com/tool/2',
            'coursevisible' => LTI_COURSEVISIBLE_PRECONFIGURED,
            'state' => LTI_TOOL_STATE_CONFIGURED
        ]);
        $tool3id = $ltigenerator->create_course_tool_types([
            'name' => 'course tool preconfigured and activity chooser',
            'baseurl' => 'http://example.com/tool/3',
            'course' => $course->id
        ]);
        $tool4id = $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured and activity chooser, restricted to category 2',
            'baseurl' => 'http://example.com/tool/4',
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'lti_coursecategories' => $coursecat2->id
        ]);
        $tool5id = $ltigenerator->create_tool_types([
            'name' => 'site tool preconfigured and activity chooser, restricted to category 1',
            'baseurl' => 'http://example.com/tool/5',
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'lti_coursecategories' => $coursecat1->id
        ]);

        // LTI_COURSEVISIBLE_NO can't be updated.
        $result = types_helper::override_type_showinactivitychooser($tool1id, $course->id, $context, true);
        $this->assertFalse($result);

        // Tool not exist.
        $result = types_helper::override_type_showinactivitychooser($tool5id + 1, $course->id, $context, false);
        $this->assertFalse($result);

        $result = types_helper::override_type_showinactivitychooser($tool2id, $course->id, $context, true);
        $this->assertTrue($result);
        $coursevisibleoverriden = $DB->get_field('lti_coursevisible', 'coursevisible',
            ['typeid' => $tool2id, 'courseid' => $course->id]);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $coursevisibleoverriden);

        $result = types_helper::override_type_showinactivitychooser($tool3id, $course->id, $context, false);
        $this->assertTrue($result);
        $coursevisible = $DB->get_field('lti_types', 'coursevisible', ['id' => $tool3id]);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $coursevisible);

        // Restricted category no allowed.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('You are not allowed to change this setting for this tool.');
        types_helper::override_type_showinactivitychooser($tool4id, $course->id, $context, false);

        // Restricted category allowed.
        $result = types_helper::override_type_showinactivitychooser($tool5id, $course->id, $context, false);
        $this->assertTrue($result);
        $coursevisibleoverriden = $DB->get_field('lti_coursevisible', 'coursevisible',
            ['typeid' => $tool5id, 'courseid' => $course->id]);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $coursevisibleoverriden);

        $this->setUser($teacher2);
        $this->expectException(\required_capability_exception::class);
        types_helper::override_type_showinactivitychooser($tool5id, $course->id, $context, false);
    }

}
