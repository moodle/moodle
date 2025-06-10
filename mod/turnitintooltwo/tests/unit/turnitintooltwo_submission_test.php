<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
global $DB;

require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_assignment.class.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_submission.class.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/lti/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Tests for classes/view/members
 *
 * @package turnitintooltwo
 */
class mod_turnitintooltwo_submission_testcase extends advanced_testcase {
    /**
     * Test create submission function returns the expected bollean given a data array.
     */
    public function test_create_submission() {
        global $DB;

        $this->resetAfterTest();

        $turnitintooltwo = new stdClass();
        $turnitintooltwo->id = 1;

        $turnitintooltwoassignment = new turnitintooltwo_assignment(0, $turnitintooltwo);

        $submission = new turnitintooltwo_submission(0, "moodle", $turnitintooltwoassignment, 1);

        $data = array();
        $data['submissiontype'] = 1;
        $data['submissiontext'] = "Submission text";
        $data['submissiontitle'] = "Submission title";
        $data['studentsname'] = 1;
        $data['submissionpart'] = 1;
        $data['submissionagreement'] = 1;

        $response = $submission->create_submission($data);

        $this->assertEquals($response, true);

        /**
         * Test where create_submissions is false.
         * First we stub the class,
         * Then we stub the method insert_submission() to always return false.
         * Then we call our stubbed class with the method we want to test.
        */
        // Create a stub
        $stub = $this->getMockBuilder('turnitintooltwo_submission')
                     ->getMock();

        // Configure the stub.
        $stub->method('insert_submission')
             ->willReturn(false);

        $response = $stub->create_submission($data);

        $this->assertEquals($response, false);
    }

    /**
     * Test create submission function returns the expected bollean given a data array.
     */
    public function test_insert_submission() {
        global $DB;

        $this->resetAfterTest();

        $turnitintooltwo = new stdClass();
        $turnitintooltwo->id = 1;

        $turnitintooltwoassignment = new turnitintooltwo_assignment(0, $turnitintooltwo);

        $submission = new turnitintooltwo_submission(0, "moodle", $turnitintooltwoassignment, 1);

        $data = new stdClass();
        $data->userid = 1;
        $data->turnitintooltwoid = 1;
        $data->submission_part = 1;
        $data->submission_title = "Submission title";
        $data->submission_type = 1;
        $data->submission_objectid = null;
        $data->submission_unanon = 0;
        $data->submission_grade = null;
        $data->submission_gmimaged = 0;
        $data->submission_hash = $data->userid.'_'.$data->turnitintooltwoid.'_'.$data->submission_part;

        $response = $submission->insert_submission($data);
        $this->assertEquals($response, true);

        $response = $submission->insert_submission("");
        $this->assertEquals($response, false);
    }

    public function test_count_graded_submissions() {
      global $DB;

      $this->resetAfterTest();

      $turnitintooltwo = new stdClass();
      $turnitintooltwo->id = 1;

      $turnitintooltwoassignment = new turnitintooltwo_assignment(0, $turnitintooltwo);

      $submission = new turnitintooltwo_submission(0, "moodle", $turnitintooltwoassignment, 1);

      $data = new stdClass();
      $data->userid = 1;
      $data->turnitintooltwoid = $turnitintooltwo->id;
      $data->submission_part = 1;
      $data->submission_title = "Submission title";
      $data->submission_type = 1;
      $data->submission_objectid = null;
      $data->submission_unanon = 0;
      $data->submission_grade = 75;
      $data->submission_gmimaged = 0;
      $data->submission_hash = $data->userid.'_'.$data->turnitintooltwoid.'_'.$data->submission_part;

      $response = $submission->insert_submission($data);
      $count = $submission->count_graded_submissions($turnitintooltwo->id);

      $this->assertEquals($count, 1);

      // Testing when there is no grades
      $submissionrecord = $DB->get_record('turnitintooltwo_submissions', array('turnitintooltwoid' => $turnitintooltwo->id));
      $DB->update_record('turnitintooltwo_submissions', array('id' => $submissionrecord->id, 'submission_grade' => 0));

      $count = $submission->count_graded_submissions($turnitintooltwo->id);

      $this->assertEquals($count, 0);
    }
}
