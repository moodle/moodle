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

namespace mod_assign\external;

use mod_assign_test_generator;
use mod_assign_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->dirroot/mod/assign/tests/generator.php");
require_once("$CFG->dirroot/mod/assign/tests/fixtures/event_mod_assign_fixtures.php");
require_once("$CFG->dirroot/mod/assign/tests/externallib_advanced_testcase.php");

/**
 * Test the remove submission external function.
 *
 * @package    mod_assign
 * @category   test
 *
 * @copyright  2024 Daniel Ureña <durenadev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_assign\external\remove_submission
 */
final class remove_submission_test extends \mod_assign\externallib_advanced_testcase {
    // Use the generator helper.
    use mod_assign_test_generator;

    /**
     * Prepare course with users, teacher and assign.
     *
     * @return array Containing course, student1, student2, teacher assign and instance data
     */
    protected function prepare_course(): array {

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course->id;
        $params['assignsubmission_onlinetext_enabled'] = 1;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);
        $assign = new \mod_assign_testable_assign($context, $cm, $course);

        $teacher  = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        return [$course, $student1, $student2, $teacher, $assign, $instance];
    }

    /**
     * Test remove submission by WS with invalid assign id.
     * @covers ::execute
     */
    public function test_remove_submission_with_invalid_assign_id(): void {
        $this->resetAfterTest();
        [$course, $student1, $student2, $teacher, $assign, $instance] = $this->prepare_course();
        $this->expectException(\moodle_exception::class);
        remove_submission::execute($student1->id, 123);
    }

    /**
     * Test remove submission by WS with invalid user id.
     * @covers ::execute
     */
    public function test_remove_submission_with_invalid_user_id(): void {
        $this->resetAfterTest();
        [$course, $student1, $student2, $teacher, $assign, $instance] = $this->prepare_course();
        $this->setUser($student1);
        $result = remove_submission::execute(123, $assign->get_instance()->id);
        $this->assertFalse($result['status']);
        $this->assertEquals('submissionnotfoundtoremove', $result['warnings'][0]['warningcode']);
    }

    /**
     * Test teacher can't remove student submissions by WS.
     * @covers ::execute
     */
    public function test_teacher_remove_submissions(): void {
        $this->resetAfterTest();
        [$course, $student1, $student2, $teacher, $assign, $instance] = $this->prepare_course();
        $this->add_submission($student1, $assign);
        $this->add_submission($student2, $assign);
        $this->setUser($teacher);

        $result = remove_submission::execute($student1->id, $assign->get_instance()->id);
        $this->assertFalse($result['status']);
        $this->assertEquals('couldnotremovesubmission', $result['warnings'][0]['warningcode']);

        $result = remove_submission::execute($student2->id, $assign->get_instance()->id);
        $this->assertFalse($result['status']);
        $this->assertEquals('couldnotremovesubmission', $result['warnings'][0]['warningcode']);
    }

    /**
     * Test teacher can remove student submissions by WS if they have added capability.
     * @covers ::execute
     */
    public function test_teacher_editothersubmission_remove_submissions(): void {
        global $DB;
        $this->resetAfterTest();
        [$course, $student1, $student2, $teacher, $assign, $instance] = $this->prepare_course();
        $this->add_submission($student1, $assign);
        $this->add_submission($student2, $assign);

        $capability = 'mod/assign:editothersubmission';
        $roleteacher = $DB->get_record('role', ['shortname' => 'teacher']);
        assign_capability($capability, CAP_ALLOW, $roleteacher->id, $assign->get_context()->id);
        role_assign($roleteacher->id, $teacher->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($teacher);

        $result = remove_submission::execute($student1->id, $assign->get_instance()->id);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        $result = remove_submission::execute($student2->id, $assign->get_instance()->id);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Test user can't remove their own non-existent submission.
     * @covers ::execute
     */
    public function test_remove_own_notexists_submission(): void {
        $this->resetAfterTest();
        [$course, $student1, $student2, $teacher, $assign, $instance] = $this->prepare_course();

        // Remove own submission when user has no submission to remove.
        $this->setUser($student1);
        $result = remove_submission::execute($student1->id, $assign->get_instance()->id);
        $this->assertFalse($result['status']);
        $this->assertEquals('submissionnotfoundtoremove', $result['warnings'][0]['warningcode']);
    }

    /**
     * Test user can remove their own existing submission.
     * @covers ::execute
     */
    public function test_remove_own_submission(): void {
        global $DB;
        $this->resetAfterTest();
        [$course, $student1, $student2, $teacher, $assign, $instance] = $this->prepare_course();

        // Remove own submission.
        $this->add_submission($student2, $assign);
        $this->setUser($student2);

        $result = remove_submission::execute($student2->id, $assign->get_instance()->id);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        // Make sure submission was removed.
        $submission      = $assign->get_user_submission($student2->id, 0);
        $submissionquery = $DB->get_record('assign_submission', ['id' => $submission->id]);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_NEW, $submissionquery->status);

        // Try to remove after removed.
        $result = remove_submission::execute($student2->id, $assign->get_instance()->id);
        $this->assertFalse($result['status']);
        $this->assertEquals('submissionnotfoundtoremove', $result['warnings'][0]['warningcode']);
    }

    /**
     * Test user can remove their own reopened submission.
     * @covers ::execute
     */
    public function test_remove_own_submission_reopened(): void {
        global $DB, $USER;
        $this->resetAfterTest();
        [$course, $student1, $student2, $teacher, $assign, $instance] = $this->prepare_course();

        // Create submission and reopen.
        $this->add_submission($student2, $assign);
        // Grade and reopen.
        $this->setUser($teacher);
        $feedbackpluginparams = [];
        $feedbackpluginparams['files_filemanager'] = file_get_unused_draft_itemid();
        $feedbackeditorparams = ['text' => 'Yeeha!', 'format' => 1];
        $feedbackpluginparams['assignfeedbackcomments_editor'] = $feedbackeditorparams;
        mod_assign_external::save_grade(
            $instance->id,
            $student1->id,
            50.0,
            -1,
            false,
            'released',
            false,
            $feedbackpluginparams
        );
        $USER->ignoresesskey = true;
        $assign->testable_process_add_attempt($student1->id);

        // Create submission.
        $this->add_submission($student2, $assign);

        // Remove own submission.
        $this->setUser($student2);
        $result = remove_submission::execute($student2->id, $assign->get_instance()->id);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        // Make sure submission was removed.
        $submission      = $assign->get_user_submission($student2->id, 0);
        $submissionquery = $DB->get_record('assign_submission', ['id' => $submission->id]);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_NEW, $submissionquery->status);
    }
}
