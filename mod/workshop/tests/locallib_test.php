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
 * @package    mod_workshop
 * @category   phpunit
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/workshop/locallib.php'); // Include the code to test


/**
 * Test cases for the internal workshop api
 */
class mod_workshop_internal_api_testcase extends advanced_testcase {

    /** workshop instance emulation */
    protected $workshop;

    /** setup testing environment */
    protected function setUp() {
        parent::setUp();

        $this->workshop = new testable_workshop();
    }

    protected function tearDown() {
        $this->workshop = null;
        parent::tearDown();
    }

    public function test_aggregate_submission_grades_process_notgraded() {
        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => null);
        //$DB->expectNever('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_single() {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => 10.12345);
        $expected = 10.12345;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_null_doesnt_influence() {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => 45.54321);
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => null);
        $expected = 45.54321;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_weighted_single() {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 4, 'grade' => 14.00012);
        $expected = 14.00012;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean() {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 0.00000);
        $expected = 19.67750;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean_changed() {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 0.00000);
        $expected = 19.67750;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean_nochange() {
        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 0.00000);
        //$DB->expectNever('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_rounding() {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 4.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 2.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 1.00000);
        $expected = 2.33333;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_weighted_mean() {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 3, 'grade' => 12.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 2, 'grade' => 30.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 0, 'grade' => 1000.00000);
        $expected = 17.66667;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_nograding() {
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>2, 'gradinggrade'=>null, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        // expectation
        //$DB->expectNever('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_single_grade_new() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>3, 'gradinggrade'=>82.87670, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        // expectation
        $now = time();
        $expected = new stdclass();
        $expected->workshopid = $this->workshop->id;
        $expected->userid = 3;
        $expected->gradinggrade = 82.87670;
        $expected->timegraded = $now;
        //$DB->expectOnce('insert_record', array('workshop_aggregations', $expected));
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch, $now);
    }

    public function test_aggregate_grading_grades_process_single_grade_update() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>3, 'gradinggrade'=>90.00000, 'gradinggradeover'=>null, 'aggregationid'=>1, 'aggregatedgrade'=>82.87670);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_single_grade_uptodate() {
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>3, 'gradinggrade'=>90.00000, 'gradinggradeover'=>null, 'aggregationid'=>1, 'aggregatedgrade'=>90.00000);
        // expectation
        //$DB->expectNever('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_single_grade_overridden() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>4, 'gradinggrade'=>91.56700, 'gradinggradeover'=>82.32105, 'aggregationid'=>2, 'aggregatedgrade'=>91.56700);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_new() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>99.45670, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>87.34311, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>51.12000, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        // expectation
        $now = time();
        $expected = new stdclass();
        $expected->workshopid = $this->workshop->id;
        $expected->userid = 5;
        $expected->gradinggrade = 79.3066;
        $expected->timegraded = $now;
        //$DB->expectOnce('insert_record', array('workshop_aggregations', $expected));
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch, $now);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_update() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>56.23400, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>79.30660);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>87.34311, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>79.30660);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>51.12000, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>79.30660);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_overriden() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>56.23400, 'gradinggradeover'=>99.45670, 'aggregationid'=>2, 'aggregatedgrade'=>64.89904);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>87.34311, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>64.89904);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>51.12000, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>64.89904);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_one_missing() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>50.00000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>null, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>52.20000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_missing_overridden() {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>50.00000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>null, 'gradinggradeover'=>69.00000, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>52.20000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_percent_to_value() {
        // fixture setup
        $total = 185;
        $percent = 56.6543;
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
        // verify
        $this->assertEquals($part, $total * $percent / 100);
    }

    public function test_percent_to_value_negative() {
        // fixture setup
        $total = 185;
        $percent = -7.098;
        // set expectation
        $this->setExpectedException('coding_exception');
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
    }

    public function test_percent_to_value_over_hundred() {
        // fixture setup
        $total = 185;
        $percent = 121.08;
        // set expectation
        $this->setExpectedException('coding_exception');
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
    }

    public function test_lcm() {
        // fixture setup + exercise SUT + verify in one step
        $this->assertEquals(workshop::lcm(1,4), 4);
        $this->assertEquals(workshop::lcm(2,4), 4);
        $this->assertEquals(workshop::lcm(4,2), 4);
        $this->assertEquals(workshop::lcm(2,3), 6);
        $this->assertEquals(workshop::lcm(6,4), 12);
    }

    public function test_lcm_array() {
        // fixture setup
        $numbers = array(5,3,15);
        // excersise SUT
        $lcm = array_reduce($numbers, 'workshop::lcm', 1);
        // verify
        $this->assertEquals($lcm, 15);
    }

    public function test_prepare_example_assessment() {
        // fixture setup
        $fakerawrecord = (object)array(
            'id'                => 42,
            'submissionid'      => 56,
            'weight'            => 0,
            'timecreated'       => time() - 10,
            'timemodified'      => time() - 5,
            'grade'             => null,
            'gradinggrade'      => null,
            'gradinggradeover'  => null,
            'feedbackauthor'    => null,
            'feedbackauthorformat' => 0,
            'feedbackauthorattachment' => 0,
        );
        // excersise SUT
        $a = $this->workshop->prepare_example_assessment($fakerawrecord);
        // verify
        $this->assertTrue($a instanceof workshop_example_assessment);
        $this->assertTrue($a->url instanceof moodle_url);

        // modify setup
        $fakerawrecord->weight = 1;
        $this->setExpectedException('coding_exception');
        // excersise SUT
        $a = $this->workshop->prepare_example_assessment($fakerawrecord);
    }

    public function test_prepare_example_reference_assessment() {
        global $USER;
        // fixture setup
        $fakerawrecord = (object)array(
            'id'                => 38,
            'submissionid'      => 56,
            'weight'            => 1,
            'timecreated'       => time() - 100,
            'timemodified'      => time() - 50,
            'grade'             => 0.75000,
            'gradinggrade'      => 1.00000,
            'gradinggradeover'  => null,
            'feedbackauthor'    => null,
            'feedbackauthorformat' => 0,
            'feedbackauthorattachment' => 0,
        );
        // excersise SUT
        $a = $this->workshop->prepare_example_reference_assessment($fakerawrecord);
        // verify
        $this->assertTrue($a instanceof workshop_example_reference_assessment);

        // modify setup
        $fakerawrecord->weight = 0;
        $this->setExpectedException('coding_exception');
        // excersise SUT
        $a = $this->workshop->prepare_example_reference_assessment($fakerawrecord);
    }
}


/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_workshop extends workshop {

    public function __construct() {
        $this->id       = 16;
        $this->cm       = new stdclass();
        $this->course   = new stdclass();
        $this->context  = new stdclass();
    }

    public function aggregate_submission_grades_process(array $assessments) {
        parent::aggregate_submission_grades_process($assessments);
    }

    public function aggregate_grading_grades_process(array $assessments, $timegraded = null) {
        parent::aggregate_grading_grades_process($assessments, $timegraded);
    }

}
