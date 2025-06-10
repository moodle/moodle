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
 * @package    plagiarism_turnitin
 * @copyright  2018 Turnitin
 * @author     David Winn <dwinn@turnitin.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use core_privacy\local\request\deletion_criteria;
use plagiarism_turnitin\privacy\provider;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot . '/mod/assign/externallib.php');
require_once($CFG->dirroot . '/plagiarism/turnitin/tests/lib_test.php');

if (!class_exists('\core_privacy\tests\provider_testcase')) {
    return;
}

class plagiarism_turnitin_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Test for _get_metadata shim.
     */
    public function test_get_metadata() {
        $this->resetAfterTest();

        $collection = new collection('plagiarism_turnitin');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();

        $this->assertCount(4, $itemcollection);

        // Verify core_files data is returned.
        $this->assertEquals('core_files', $itemcollection[0]->get_name());
        $this->assertEquals('privacy:metadata:core_files', $itemcollection[0]->get_summary());

        // Verify plagiarism_turnitin_files data is returned.
        $this->assertEquals('plagiarism_turnitin_files', $itemcollection[1]->get_name());

        $privacyfields = $itemcollection[1]->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('similarityscore', $privacyfields);
        $this->assertArrayHasKey('attempt', $privacyfields);
        $this->assertArrayHasKey('transmatch', $privacyfields);
        $this->assertArrayHasKey('lastmodified', $privacyfields);
        $this->assertArrayHasKey('lastmodified', $privacyfields);
        $this->assertArrayHasKey('grade', $privacyfields);
        $this->assertArrayHasKey('orcapable', $privacyfields);
        $this->assertArrayHasKey('student_read', $privacyfields);

        // Verify plagiarism_turnitin_user data is returned.
        $this->assertEquals('plagiarism_turnitin_users', $itemcollection[2]->get_name());

        $privacyfields = $itemcollection[2]->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('turnitin_uid', $privacyfields);
        $this->assertArrayHasKey('instructor_defaults', $privacyfields);
        $this->assertArrayHasKey('instructor_rubrics', $privacyfields);
        $this->assertArrayHasKey('user_agreement_accepted', $privacyfields);

        // Verify plagiarism_turnitin_client data is returned.
        $this->assertEquals('plagiarism_turnitin_client', $itemcollection[3]->get_name());

        $privacyfields = $itemcollection[3]->get_privacy_fields();
        $this->assertArrayHasKey('email', $privacyfields);
        $this->assertArrayHasKey('firstname', $privacyfields);
        $this->assertArrayHasKey('lastname', $privacyfields);
        $this->assertArrayHasKey('submission_title', $privacyfields);
        $this->assertArrayHasKey('submission_filename', $privacyfields);
        $this->assertArrayHasKey('submission_content', $privacyfields);

        $this->assertEquals('privacy:metadata:plagiarism_turnitin_client', $itemcollection[3]->get_summary());
    }

    /**
     * Test that user's contexts are exported.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();
        global $DB;

        $csresponse = $this->create_submission();

        $submissions = $DB->get_records('plagiarism_turnitin_files');

        $this->assertEquals(1, count($submissions));

        $contextlist = provider::get_contexts_for_userid($csresponse["Student"]->id);

        $this->assertCount(1, $contextlist);
    }

    public function test_export_plagiarism_user_data() {
        $this->resetAfterTest();
        global $DB;

        $csresponse = $this->create_submission();

        $submissions = $DB->get_records('plagiarism_turnitin_files');
        $this->assertEquals(1, count($submissions));

        // Export all of the data for the user.
        provider::export_plagiarism_user_data($csresponse["Student"]->id, $csresponse["Context"], array(), array());
        $writer = \core_privacy\local\request\writer::with_context($csresponse["Context"]);
        $this->assertTrue($writer->has_any_data());
    }

    public function test_delete_plagiarism_for_user() {
        $this->resetAfterTest();
        global $DB;

        $csresponse = $this->create_submission();
        $csresponse2 = $this->create_submission();

        $submissions = $DB->get_records('plagiarism_turnitin_files');
        $this->assertEquals(2, count($submissions));

        // Delete all of the data for the user for the first submission.
        provider::delete_plagiarism_for_user($csresponse["Student"]->id, $csresponse["Context"]);

        $submissions = $DB->get_records('plagiarism_turnitin_files');
        $this->assertEquals(1, count($submissions));

        provider::delete_plagiarism_for_user($csresponse2["Student"]->id, $csresponse2["Context"]);
        $submissions = $DB->get_records('plagiarism_turnitin_files');
        $this->assertEquals(0, count($submissions));
    }

    public function test_delete_plagiarism_for_context() {
        $this->resetAfterTest();
        global $DB;

        $csresponse = $this->create_submission(3);

        $submissions = $DB->get_records('plagiarism_turnitin_files');
        $this->assertEquals(3, count($submissions));

        // Delete all of the data for the user.
        provider::delete_plagiarism_for_context($csresponse["Context"]);

        $submissions = $DB->get_records('plagiarism_turnitin_files');
        $this->assertEquals(0, count($submissions));
    }

    public function create_submission($numsubmissions = 1) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

        $libtest = new plagiarism_turnitin_lib_testcase();
        $result = $libtest->create_assign_with_student_and_teacher(array(
            'assignsubmission_onlinetext_enabled' => 1,
            'teamsubmission' => 0
        ));

        $assignmodule = $result['assign'];
        $student = $result['student'];
        $cm = get_coursemodule_from_instance('assign', $assignmodule->id);
        $context = context_module::instance($cm->id);

        $plagiarismfile = new stdClass();
        $plagiarismfile->cm = $cm->id;
        $plagiarismfile->userid = $student->id;
        $plagiarismfile->identifier = "abcd";
        $plagiarismfile->statuscode = "success";
        $plagiarismfile->similarityscore = 50;
        $plagiarismfile->externalid = 123456789;
        $plagiarismfile->attempt = 1;
        $plagiarismfile->transmatch = 0;
        $plagiarismfile->lastmodified = time();
        $plagiarismfile->submissiontype = 2;
        $plagiarismfile->itemid = 12;
        $plagiarismfile->submitter = $student->id;

        for ($i = 0; $i < $numsubmissions; $i++) {
            $DB->insert_record('plagiarism_turnitin_files', $plagiarismfile);
        }

        $this->setUser($student);

        return array("Student" => $student, "Context" => $context);
    }
}