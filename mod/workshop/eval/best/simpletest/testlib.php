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
 * Unit tests for grading evaluation method "best"
 *
 * @package    workshopeval
 * @subpackage best
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the code to test
require_once($CFG->dirroot . '/mod/workshop/locallib.php');
require_once($CFG->dirroot . '/mod/workshop/eval/best/lib.php');
require_once($CFG->libdir . '/gradelib.php');

global $DB;
Mock::generate(get_class($DB), 'mockDB');

/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_workshop_best_evaluation extends workshop_best_evaluation {

    public function normalize_grades(array $assessments, array $diminfo) {
        return parent::normalize_grades($assessments, $diminfo);
    }
    public function average_assessment(array $assessments) {
        return parent::average_assessment($assessments);
    }
    public function weighted_variance(array $assessments) {
        return parent::weighted_variance($assessments);
    }
    public function assessments_distance(stdclass $assessment, stdclass $referential, array $diminfo, stdclass $settings) {
        return parent::assessments_distance($assessment, $referential, $diminfo, $settings);
    }
}

class workshop_best_evaluation_test extends UnitTestCase {

    /** real database */
    protected $realDB;

    /** workshop instance emulation */
    protected $workshop;

    /** instance of the grading evaluator being tested */
    protected $evaluator;

    /**
     * Setup testing environment
     */
    public function setUp() {
        global $DB;
        $this->realDB   = $DB;
        $DB             = new mockDB();

        $cm             = new stdclass();
        $course         = new stdclass();
        $context        = new stdclass();
        $workshop       = (object)array('id' => 42, 'evaluation' => 'best');
        $this->workshop = new workshop($workshop, $cm, $course, $context);
        $this->evaluator = new testable_workshop_best_evaluation($this->workshop);
    }

    public function tearDown() {
        global $DB;
        $DB = $this->realDB;

        $this->workshop = null;
        $this->evaluator = null;
    }

    public function test_normalize_grades() {
        // fixture set-up
        $assessments = array();
        $assessments[1] = (object)array(
                'dimgrades' => array(3 => 1.0000, 4 => 13.42300),
            );
        $assessments[3] = (object)array(
                'dimgrades' => array(3 => 2.0000, 4 => 19.1000),
            );
        $assessments[7] = (object)array(
                'dimgrades' => array(3 => 3.0000, 4 => 0.00000),
            );
        $diminfo = array(
                3 => (object)array('min' => 1, 'max' => 3),
                4 => (object)array('min' => 0, 'max' => 20),
            );
        // exercise SUT
        $norm = $this->evaluator->normalize_grades($assessments, $diminfo);
        // validate
        $this->assertIsA($norm, 'array');
        // the following grades from a scale
        $this->assertEqual($norm[1]->dimgrades[3], 0);
        $this->assertEqual($norm[3]->dimgrades[3], 50);
        $this->assertEqual($norm[7]->dimgrades[3], 100);
        // the following grades from an interval 0 - 20
        $this->assertEqual($norm[1]->dimgrades[4], grade_floatval(13.423 / 20 * 100));
        $this->assertEqual($norm[3]->dimgrades[4], grade_floatval(19.1 / 20 * 100));
        $this->assertEqual($norm[7]->dimgrades[4], 0);
    }

    public function test_normalize_grades_max_equals_min() {
        // fixture set-up
        $assessments = array();
        $assessments[1] = (object)array(
                'dimgrades' => array(3 => 100.0000),
            );
        $diminfo = array(
                3 => (object)array('min' => 100, 'max' => 100),
            );
        // exercise SUT
        $norm = $this->evaluator->normalize_grades($assessments, $diminfo);
        // validate
        $this->assertIsA($norm, 'array');
        $this->assertEqual($norm[1]->dimgrades[3], 100);
    }

    public function test_average_assessment_same_weights() {
        // fixture set-up
        $assessments = array();
        $assessments[18] = (object)array(
                'weight'        => 1,
                'dimgrades'     => array(1 => 50, 2 => 33.33333),
            );
        $assessments[16] = (object)array(
                'weight'        => 1,
                'dimgrades'     => array(1 => 0, 2 => 66.66667),
            );
        // exercise SUT
        $average = $this->evaluator->average_assessment($assessments);
        // validate
        $this->assertIsA($average->dimgrades, 'array');
        $this->assertEqual(grade_floatval($average->dimgrades[1]), grade_floatval(25));
        $this->assertEqual(grade_floatval($average->dimgrades[2]), grade_floatval(50));
    }

    public function test_average_assessment_different_weights() {
        // fixture set-up
        $assessments = array();
        $assessments[11] = (object)array(
                'weight'        => 1,
                'dimgrades'     => array(3 => 10.0, 4 => 13.4, 5 => 95.0),
            );
        $assessments[13] = (object)array(
                'weight'        => 3,
                'dimgrades'     => array(3 => 11.0, 4 => 10.1, 5 => 92.0),
            );
        $assessments[17] = (object)array(
                'weight'        => 1,
                'dimgrades'     => array(3 => 11.0, 4 => 8.1, 5 => 88.0),
            );
        // exercise SUT
        $average = $this->evaluator->average_assessment($assessments);
        // validate
        $this->assertIsA($average->dimgrades, 'array');
        $this->assertEqual(grade_floatval($average->dimgrades[3]), grade_floatval((10.0 + 11.0*3 + 11.0)/5));
        $this->assertEqual(grade_floatval($average->dimgrades[4]), grade_floatval((13.4 + 10.1*3 + 8.1)/5));
        $this->assertEqual(grade_floatval($average->dimgrades[5]), grade_floatval((95.0 + 92.0*3 + 88.0)/5));
    }

    public function test_average_assessment_noweight() {
        // fixture set-up
        $assessments = array();
        $assessments[11] = (object)array(
                'weight'        => 0,
                'dimgrades'     => array(3 => 10.0, 4 => 13.4, 5 => 95.0),
            );
        $assessments[17] = (object)array(
                'weight'        => 0,
                'dimgrades'     => array(3 => 11.0, 4 => 8.1, 5 => 88.0),
            );
        // exercise SUT
        $average = $this->evaluator->average_assessment($assessments);
        // validate
        $this->assertNull($average);
    }

    public function test_weighted_variance() {
        // fixture set-up
        $assessments[11] = (object)array(
                'weight'        => 1,
                'dimgrades'     => array(3 => 11, 4 => 2),
            );
        $assessments[13] = (object)array(
                'weight'        => 3,
                'dimgrades'     => array(3 => 11, 4 => 4),
            );
        $assessments[17] = (object)array(
                'weight'        => 2,
                'dimgrades'     => array(3 => 11, 4 => 5),
            );
        $assessments[20] = (object)array(
                'weight'        => 1,
                'dimgrades'     => array(3 => 11, 4 => 7),
            );
        $assessments[25] = (object)array(
                'weight'        => 1,
                'dimgrades'     => array(3 => 11, 4 => 9),
            );
        // exercise SUT
        $variance = $this->evaluator->weighted_variance($assessments);
        // validate
        // dimension [3] have all the grades equal to 11
        $this->assertEqual($variance[3], 0);
        // dimension [4] represents data 2, 4, 4, 4, 5, 5, 7, 9 having stdev=2 (stdev is sqrt of variance)
        $this->assertEqual($variance[4], 4);
    }

    public function test_assessments_distance_zero() {
        // fixture set-up
        $diminfo = array(
            3 => (object)array('weight' => 1, 'min' => 0, 'max' => 100, 'variance' => 12.34567),
            4 => (object)array('weight' => 1, 'min' => 1, 'max' => 5,   'variance' => 98.76543),
        );
        $assessment1 = (object)array('dimgrades' => array(3 => 15, 4 => 2));
        $assessment2 = (object)array('dimgrades' => array(3 => 15, 4 => 2));
        $settings = (object)array('comparison' => 5);
        // exercise SUT and validate
        $this->assertEqual($this->evaluator->assessments_distance($assessment1, $assessment2, $diminfo, $settings), 0);
    }

    public function test_assessments_distance_equals() {
        /*
        // fixture set-up
        $diminfo = array(
            3 => (object)array('weight' => 1, 'min' => 0, 'max' => 100, 'variance' => 12.34567),
            4 => (object)array('weight' => 1, 'min' => 0, 'max' => 100, 'variance' => 12.34567),
        );
        $assessment1 = (object)array('dimgrades' => array(3 => 25, 4 => 4));
        $assessment2 = (object)array('dimgrades' => array(3 => 75, 4 => 2));
        $referential = (object)array('dimgrades' => array(3 => 50, 4 => 3));
        $settings = (object)array('comparison' => 5);
        // exercise SUT and validate
        $this->assertEqual($this->evaluator->assessments_distance($assessment1, $referential, $diminfo, $settings),
                           $this->evaluator->assessments_distance($assessment2, $referential, $diminfo, $settings));
        */
        // fixture set-up
        $diminfo = array(
            1 => (object)array('min' => 0, 'max' => 2, 'weight' => 1, 'variance' => 625),
            2 => (object)array('min' => 0, 'max' => 3, 'weight' => 1, 'variance' => 277.7778888889),
        );
        $assessment1 = (object)array('dimgrades' => array(1 => 0,  2 => 66.66667));
        $assessment2 = (object)array('dimgrades' => array(1 => 50, 2 => 33.33333));
        $referential = (object)array('dimgrades' => array(1 => 25, 2 => 50));
        $settings = (object)array('comparison' => 9);
        // exercise SUT and validate
        $this->assertEqual($this->evaluator->assessments_distance($assessment1, $referential, $diminfo, $settings),
                           $this->evaluator->assessments_distance($assessment2, $referential, $diminfo, $settings));

    }
}
