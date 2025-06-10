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
 * Privacy provider tests.
 *
 * @package    mod_turnitintooltwo
 * @copyright  2018 John McGettrick
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use core_privacy\local\request\deletion_criteria;
use mod_turnitintooltwo\privacy\provider;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/turnitintooltwo/tests/unit/lib_test.php');

if (!class_exists('\core_privacy\tests\provider_testcase')) {
    return;
}

class mod_turnitintooltwo_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    public function setUp(): void {
        global $DB;

        $this->resetAfterTest();

        $this->testcase = new mod_lib_testcase();
        $generator = $this->getDataGenerator();

        // Set up test assignment.
        $this->turnitintooltwoassignment = $this->testcase->make_test_tii_assignment();

        // Set up test module.
        $cmid = $this->testcase->make_test_module(
            $this->turnitintooltwoassignment->turnitintooltwo->course,
            'turnitintooltwo',
            $this->turnitintooltwoassignment->turnitintooltwo->id
        );
        $this->cm = $DB->get_record("course_modules", array('id' => $cmid));
        context_module::instance($cmid);

        $this->parts = $this->testcase->make_test_parts(
            'turnitintooltwo',
            $this->turnitintooltwoassignment->turnitintooltwo->id,
            1
        );

        // Create and enrol student.
        $this->student1 = $generator->create_user();
        $this->studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user(
            $this->student1->id,
            $this->turnitintooltwoassignment->turnitintooltwo->course,
            $this->studentrole->id
        );

        // Create and enrol another student.
        $this->student2 = $this->getDataGenerator()->create_user();
        $generator->enrol_user(
            $this->student2->id,
            $this->turnitintooltwoassignment->turnitintooltwo->course,
            $this->studentrole->id
        );

        // Create test submission for student1.
        $this->testcase->create_test_submission(
            $this->turnitintooltwoassignment,
            $this->student1->id,
            current($this->parts)->id
        );
    }

    /**
     * Test that metadata is returned.
     */
    public function test_get_metadata() {
        $this->resetAfterTest();

        $collection = new collection('mod_turnitintooltwo');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();

        $this->assertCount(4, $itemcollection);

        // Verify core_files data is returned.
        $this->assertEquals('core_files', $itemcollection[0]->get_name());
        $this->assertEquals('privacy:metadata:core_files', $itemcollection[0]->get_summary());

        // Verify turnitintooltwo_users data is returned.
        $this->assertEquals('turnitintooltwo_users', $itemcollection[1]->get_name());

        $privacyfields = $itemcollection[1]->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('turnitin_uid', $privacyfields);
        $this->assertArrayHasKey('instructor_defaults', $privacyfields);
        $this->assertArrayHasKey('instructor_rubrics', $privacyfields);
        $this->assertArrayHasKey('user_agreement_accepted', $privacyfields);

        $this->assertEquals('privacy:metadata:turnitintooltwo_users', $itemcollection[1]->get_summary());

        // Verify turnitintooltwo_submissions data is returned.
        $this->assertEquals('turnitintooltwo_submissions', $itemcollection[2]->get_name());

        $privacyfields = $itemcollection[2]->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('submission_title', $privacyfields);
        $this->assertArrayHasKey('submission_filename', $privacyfields);
        $this->assertArrayHasKey('submission_objectid', $privacyfields);
        $this->assertArrayHasKey('submission_score', $privacyfields);
        $this->assertArrayHasKey('submission_grade', $privacyfields);
        $this->assertArrayHasKey('submission_attempts', $privacyfields);
        $this->assertArrayHasKey('submission_modified', $privacyfields);
        $this->assertArrayHasKey('submission_unanon', $privacyfields);
        $this->assertArrayHasKey('submission_unanonreason', $privacyfields);
        $this->assertArrayHasKey('submission_transmatch', $privacyfields);
        $this->assertArrayHasKey('submission_orcapable', $privacyfields);
        $this->assertArrayHasKey('submission_hash', $privacyfields);

        $this->assertEquals('privacy:metadata:turnitintooltwo_submissions', $itemcollection[2]->get_summary());

        // Verify turnitintooltwo_client data is returned.
        $this->assertEquals('turnitintooltwo_client', $itemcollection[3]->get_name());

        $privacyfields = $itemcollection[3]->get_privacy_fields();
        $this->assertArrayHasKey('email', $privacyfields);
        $this->assertArrayHasKey('firstname', $privacyfields);
        $this->assertArrayHasKey('lastname', $privacyfields);
        $this->assertArrayHasKey('submission_title', $privacyfields);
        $this->assertArrayHasKey('submission_filename', $privacyfields);
        $this->assertArrayHasKey('submission_content', $privacyfields);

        $this->assertEquals('privacy:metadata:turnitintooltwo_client', $itemcollection[3]->get_summary());
    }

    /**
     * Test that user's contexts are exported.
     */
    public function test_get_contexts_for_userid() {
        $contextlist = provider::get_contexts_for_userid($this->student1->id);

        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $cmcontext = context_module::instance($this->cm->id);
        $this->assertEquals($cmcontext->id, $contextforuser->id);
    }

    /**
     * Test that export_user_data returns data.
     */
    public function test_export_user_data() {
        $cm = get_coursemodule_from_instance('turnitintooltwo', $this->turnitintooltwoassignment->turnitintooltwo->id);
        $context = context_module::instance($cm->id);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student1->id, $context, 'mod_turnitintooltwo');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test data is deleted for all users in a given context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $this->resetAfterTest();

        $turnitintooltwoassignment = $this->turnitintooltwoassignment;

        // Create test submission for student2.
        $this->testcase->create_test_submission(
            $turnitintooltwoassignment,
            $this->student2->id,
            current($this->parts)->id
        );

        // Before deletion, we should have 2 submissions.
        $count = $DB->count_records(
            'turnitintooltwo_submissions',
            array('turnitintooltwoid' => $turnitintooltwoassignment->turnitintooltwo->id)
        );
        $this->assertEquals(2, $count);

        // Delete data based on context.
        $cmcontext = context_module::instance($this->cm->id);
        provider::delete_data_for_all_users_in_context($cmcontext);

        // After deletion, there should be no submissions for that assignment.
        $count = $DB->count_records(
            'turnitintooltwo_submissions',
            array('turnitintooltwoid' => $turnitintooltwoassignment->turnitintooltwo->id)
        );
        $this->assertEquals(0, $count);
    }

    /**
     * Test all data is deleted for a user.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $turnitintooltwoassignment = $this->turnitintooltwoassignment;

        // Create a second assignment.
        $turnitintooltwoassignment2 = $this->testcase->make_test_tii_assignment();

        // Set up second module.
        $cm2id = $this->testcase->make_test_module(
            $turnitintooltwoassignment2->turnitintooltwo->course,
            'turnitintooltwo',
            $turnitintooltwoassignment2->turnitintooltwo->id
        );

        // Submit to second assignment as student1.
        $this->testcase->create_test_submission(
            $turnitintooltwoassignment2,
            $this->student1->id,
            current($this->parts)->id
        );

        // Submit to first assignment as student2.
        $this->testcase->create_test_submission(
            $turnitintooltwoassignment,
            $this->student2->id,
            current($this->parts)->id
        );

        // Before deletion, we should have 2 responses to the first assignment.
        $count = $DB->count_records(
            'turnitintooltwo_submissions',
            array('turnitintooltwoid' => $turnitintooltwoassignment->turnitintooltwo->id)
        );
        $this->assertEquals(2, $count);

        $context1 = context_module::instance($this->cm->id);
        $context2 = context_module::instance($cm2id);
        $contextlist = new \core_privacy\local\request\approved_contextlist(
            $this->student1,
            'turnitintooltwo',
            array($context1->id, $context2->id)
        );
        provider::delete_data_for_user($contextlist);

        // After deletion, the submissions for the first student should have been deleted.
        $count = $DB->count_records(
            'turnitintooltwo_submissions',
            array(
                'turnitintooltwoid' => $turnitintooltwoassignment->turnitintooltwo->id,
                'userid' => $this->student1->id
            )
        );
        $this->assertEquals(0, $count);

        // Confirm that there is only one submission in total available.
        $submissions = $DB->get_records('turnitintooltwo_submissions');
        $this->assertCount(1, $submissions);

        // Check that it belongs to student2.
        $submission = reset($submissions);
        $this->assertEquals($this->student2->id, $submission->userid);
    }
}
