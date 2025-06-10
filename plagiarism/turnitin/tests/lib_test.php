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
 * Unit tests for (some of) plagiarism/turnitin/lib.php.
 *
 * @package    plagiarism_turnitin
 * @copyright  2017 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot . '/mod/assign/externallib.php');

/**
 * Tests for API comms class
 *
 * @package turnitin
 */
class plagiarism_turnitin_lib_testcase extends advanced_testcase {

    public function test_is_plugin_configured() {
        $this->resetAfterTest();

        $plagiarismturnitin = new plagiarism_plugin_turnitin();

        // Check if plugin is configured with no plugin config set.
        $ispluginconfigured = $plagiarismturnitin->is_plugin_configured();
        $this->assertEquals(false, $ispluginconfigured);

        // Check if plugin is configured with only account id set.
        set_config('plagiarism_turnitin_accountid', '1001', 'plagiarism_turnitin');
        $ispluginconfigured = $plagiarismturnitin->is_plugin_configured();
        $this->assertEquals(false, $ispluginconfigured);

        // Check if plugin is configured with account id and apiurl set.
        set_config('plagiarism_turnitin_apiurl', 'http://www.test.com', 'plagiarism_turnitin');
        $ispluginconfigured = $plagiarismturnitin->is_plugin_configured();
        $this->assertEquals(false, $ispluginconfigured);

        // Check if plugin is configured with account id, apiurl and secretkey set.
        set_config('plagiarism_turnitin_secretkey', 'ABCDEFGH', 'plagiarism_turnitin');
        $ispluginconfigured = $plagiarismturnitin->is_plugin_configured();
        $this->assertEquals(true, $ispluginconfigured);
    }

    public function test_check_group_submission() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

        $this->resetAfterTest(true);

        $result = $this->create_assign_with_student_and_teacher(array(
            'assignsubmission_onlinetext_enabled' => 1,
            'teamsubmission' => 1
        ));
        $assignmodule = $result['assign'];
        $student = $result['student'];
        $course = $result['course'];
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $cm = get_coursemodule_from_instance('assign', $assignmodule->id);
        $context = context_module::instance($cm->id);
        $assign = new testable_assign($context, $cm, $course);

        groups_add_member($group, $student);

        $this->setUser($student);
        $submission = $assign->get_group_submission($student->id, $group->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid' => file_get_unused_draft_itemid(),
                                         'text' => 'Submission text',
                                         'format' => FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        $plagiarismturnitin = new plagiarism_plugin_turnitin();
        $response = $plagiarismturnitin->check_group_submission($cm, $student->id);

        // Test should pass as we return the correct group ID.
        $this->assertEquals($group->id, $response);

        // Test a non-group submission.
        $result = $this->create_assign_with_student_and_teacher(array(
            'assignsubmission_onlinetext_enabled' => 1,
            'teamsubmission' => 0
        ));
        $assignmodule = $result['assign'];
        $student = $result['student'];
        $course = $result['course'];
        $cm = get_coursemodule_from_instance('assign', $assignmodule->id);
        $context = context_module::instance($cm->id);
        $assign = new testable_assign($context, $cm, $course);

        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid' => file_get_unused_draft_itemid(),
                                         'text' => 'Submission text',
                                         'format' => FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        $plagiarismturnitin = new plagiarism_plugin_turnitin();
        $response = $plagiarismturnitin->check_group_submission($cm, $student->id);

        // Test should pass as we return false when checking the group ID.
        $this->assertFalse($response);
    }

    /**
     * Create a a course, assignment module instance, student and teacher and enrol them in
     * the course.
     *
     * @param array $params parameters to be provided to the assignment module creation
     * @return array containing the course, assignment module, student and teacher
     */
    public function create_assign_with_student_and_teacher($params = array()) {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $params = array_merge(array(
            'course' => $course->id,
            'name' => 'assignment',
            'intro' => 'assignment intro text',
        ), $params);

        // Create a course and assignment and users.
        $assign = $this->getDataGenerator()->create_module('assign', $params);

        $cm = get_coursemodule_from_instance('assign', $assign->id);
        $context = context_module::instance($cm->id);

        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);
        $teacher = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        assign_capability('mod/assign:view', CAP_ALLOW, $teacherrole->id, $context->id, true);
        assign_capability('mod/assign:viewgrades', CAP_ALLOW, $teacherrole->id, $context->id, true);
        assign_capability('mod/assign:grade', CAP_ALLOW, $teacherrole->id, $context->id, true);
        accesslib_clear_all_caches_for_unit_testing();

        return array(
            'course' => $course,
            'assign' => $assign,
            'student' => $student,
            'teacher' => $teacher
        );
    }

    /**
     * Test that the data returned from the report gen speed param function is what we expect.
     */
    public function test_plagiarism_get_report_gen_speed_params() {
        $this->resetAfterTest();

        $expected = new stdClass();
        $expected->num_resubmissions = 3;
        $expected->num_hours = 24;

        $plagiarismturnitin = new plagiarism_plugin_turnitin();
        $response = $plagiarismturnitin->plagiarism_get_report_gen_speed_params();

        $this->assertEquals($expected, $response);
    }

    /**
     * Test that the set config function saves a config.
     */
    public function test_plagiarism_set_config() {
        $this->resetAfterTest();

        $plagiarismturnitin = new plagiarism_plugin_turnitin();

        // Check that we can set config value when a full property name is given.
        $data = new stdClass();
        $data->plagiarism_turnitin_accountid = 123456789;
        $property = "plagiarism_turnitin_accountid";

        $plagiarismturnitin->plagiarism_set_config($data, $property);

        // Get the config.
        $config = $plagiarismturnitin->plagiarism_turnitin_admin_config();

        $this->assertEquals(123456789, $config->plagiarism_turnitin_accountid);

        // Check that we can set config value when a partial property name is given.
        $data = new stdClass();
        $data->secretkey = "Test";
        $property = "secretkey";
        $plagiarismturnitin->plagiarism_set_config($data, $property);

        // Get the config.
        $config = $plagiarismturnitin->plagiarism_turnitin_admin_config();

        $this->assertEquals("Test", $config->plagiarism_turnitin_secretkey);

        // Check that an undefined property does not set a config value.
        $data = new stdClass();
        $data->test = "Test";
        $property = "NotTest";
        $plagiarismturnitin->plagiarism_set_config($data, $property);

        // Get the config.
        $config = $plagiarismturnitin->plagiarism_turnitin_admin_config();

        $this->assertObjectNotHasProperty("plagiarism_turnitin_test", $config);
    }
}