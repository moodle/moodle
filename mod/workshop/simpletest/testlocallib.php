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
 * Unit tests for workshop api class defined in mod/workshop/locallib.php
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/workshop/locallib.php'); // Include the code to test

global $DB;
Mock::generate(get_class($DB), 'mockDB');

/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_workshop extends workshop {

    public function __construct() {
        $this->cm       = new stdClass();
        $this->course   = new stdClass();
        $this->context  = new stdClass();
    }

    public function aggregate_submission_grades_process(array $assessments) {
        parent::aggregate_submission_grades_process($assessments);
    }
}

/**
 * Test cases for the internal workshop api
 */
class workshop_internal_api_test extends UnitTestCase {

    /** workshop instance emulation */
    protected $workshop;

    /** setup testing environment */
    public function setUp() {
        global $DB;
        $this->realDB = $DB;
        $DB = new mockDB();

        $this->workshop = new testable_workshop();
    }

    public function tearDown() {
        global $DB;
        $DB = $this->realDB;
        $this->workshop = null;
    }

    public function test_aggregate_submission_grades_process_notgraded() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => null);
        $DB->expectNever('set_field');
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_single() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 10.12345);
        $expected = 10.12345;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 12)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_null_doesnt_influence() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 45.54321);
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => null);
        $expected = 45.54321;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 12)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_weighted_single() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'gradeover' => null,
                'weight' => 4, 'grade' => 14.00012);
        $expected = 14.00012;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 12)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 0.00000);
        $expected = 19.67750;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 45)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean_changed() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'gradeover' => null, 'weight' => 1,
                'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'gradeover' => null, 'weight' => 1,
                'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'gradeover' => null, 'weight' => 1,
                'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'gradeover' => null, 'weight' => 1,
                'grade' => 0.00000);
        $expected = 19.67750;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 45)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean_nochange() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'gradeover' => null, 'weight' => 1,
                'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'gradeover' => null, 'weight' => 1,
                'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'gradeover' => null, 'weight' => 1,
                'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'gradeover' => null, 'weight' => 1,
                'grade' => 0.00000);
        $DB->expectNever('set_field');
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_rounding() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 4.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 2.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null, 'weight' => 1,
                'grade' => 1.00000);
        $expected = 2.33333;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 45)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_weighted_mean() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null,
                'weight' => 3, 'grade' => 12.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null,
                'weight' => 2, 'grade' => 30.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null,
                'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'gradeover' => null,
                'weight' => 0, 'grade' => 1000.00000);
        $expected = 17.66667;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 45)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_overriden_different() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 11, 'submissiongrade' => null, 'gradeover' => 95.00000,
                'weight' => 3, 'grade' => 100.00000);
        $batch[] = (object)array('submissionid' => 11, 'submissiongrade' => null, 'gradeover' => 95.00000,
                'weight' => 2, 'grade' => 96.50000);
        $expected = 95.00000;
        $DB->expectOnce('set_field', array('workshop_submissions', 'grade', $expected, array('id' => 11)));
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_overriden_equals() {
        global $DB;

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 11, 'submissiongrade' => 95.00000, 'gradeover' => 95.00000,
                'weight' => 3, 'grade' => 100.00000);
        $batch[] = (object)array('submissionid' => 11, 'submissiongrade' => 95.00000, 'gradeover' => 95.00000,
                'weight' => 2, 'grade' => 96.50000);
        $DB->expectNever('set_field');
        // excercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_percent_to_value() {
        // fixture setup
        $total = 185;
        $percent = 56.6543;
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
        // verify
        $this->assertEqual($part, $total * $percent / 100);
    }

    public function test_percent_to_value_negative() {
        // fixture setup
        $total = 185;
        $percent = -7.098;
        // set expectation
        $this->expectException('coding_exception');
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
    }

    public function test_percent_to_value_over_hundred() {
        // fixture setup
        $total = 185;
        $percent = 121.08;
        // set expectation
        $this->expectException('coding_exception');
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
    }

    public function test_lcm() {
        // fixture setup + excercise SUT + verify in one step
        $this->assertEqual(workshop::lcm(1,4), 4);
        $this->assertEqual(workshop::lcm(2,4), 4);
        $this->assertEqual(workshop::lcm(4,2), 4);
        $this->assertEqual(workshop::lcm(2,3), 6);
        $this->assertEqual(workshop::lcm(6,4), 12);
    }

    public function test_lcm_array() {
        // fixture setup
        $numbers = array(5,3,15);
        // excersise SUT
        $lcm = array_reduce($numbers, 'workshop::lcm', 1);
        // verify
        $this->assertEqual($lcm, 15);
    }
}
