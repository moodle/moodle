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
 * Unit tests for (some of) mod/workshop/grading/noerrors/strategy.php
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the code to test
require_once($CFG->dirroot . '/mod/workshop/grading/noerrors/strategy.php');

global $DB;
Mock::generate(get_class($DB), 'mockDB');

/**
 * Test subclass that makes all the protected methods we want to test public
 */
class testable_workshop_noerrors_strategy extends workshop_noerrors_strategy {

    /** allows to set dimensions manually */
    public $dimensions = array();

    /** allow to set mappings manually */
    public $mappings = array();

    /**
     * This is where the calculation of suggested grade for submission is done
     */
    public function calculate_peer_grade(array $grades) {
        return parent::calculate_peer_grade($grades);
    }
}

class workshop_noerrors_strategy_test extends UnitTestCase {

    /** real database */
    protected $realDB;

    /** workshop instance emulation */
    protected $workshop;

    /** instance of the strategy logic class being tested */
    protected $strategy;

    /**
     * Setup testing environment
     */
    public function setUp() {
        global $DB;
        $this->realDB   = $DB;
        $DB             = new mockDB();

        $cm             = (object)array('id' => 3);
        $course         = (object)array('id' => 11);
        $workshop       = (object)array('id' => 42, 'strategy' => 'noerrors');
        $this->workshop = new workshop($workshop, $cm, $course);
        $this->strategy = new testable_workshop_noerrors_strategy($this->workshop);
    }

    public function tearDown() {
        global $DB;
        $DB = $this->realDB;

        $this->workshop = null;
        $this->strategy = null;
    }

    public function test_calculate_peer_grade_null_grade() {
        // fixture set-up
        $this->strategy->dimensions   = array();
        $this->strategy->mappings     = array();
        $grades = array();
        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertNull($suggested);
    }

    public function test_calculate_peer_grade_no_error() {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => 1);
        $this->strategy->dimensions[109] = (object)array('weight' => 1);
        $this->strategy->dimensions[111] = (object)array('weight' => 1);
        $this->strategy->mappings        = array();
        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => 1);
        $grades[] = (object)array('dimensionid' => 111, 'grade' => 1);
        $grades[] = (object)array('dimensionid' => 109, 'grade' => 1);
        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual($suggested, 1.0);
    }

    public function test_calculate_peer_grade_one_error() {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => 1);
        $this->strategy->dimensions[109] = (object)array('weight' => 1);
        $this->strategy->dimensions[111] = (object)array('weight' => 1);

        $this->strategy->mappings        = array(
                                                1 => (object)array('grade' => 80.0),
                                                2 => (object)array('grade' => 60.0),
                                            );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => 1);
        $grades[] = (object)array('dimensionid' => 111, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 109, 'grade' => 1);

        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual($suggested, 0.8);
    }

    public function test_calculate_peer_grade_three_errors_same_weight_a() {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => 1);
        $this->strategy->dimensions[109] = (object)array('weight' => 1);
        $this->strategy->dimensions[111] = (object)array('weight' => 1);

        $this->strategy->mappings        = array(
                                                1 => (object)array('grade' => 80.0),
                                                2 => (object)array('grade' => 60.0),
                                                3 => (object)array('grade' => 10.0),
                                            );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 111, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 109, 'grade' => 0);

        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual($suggested, 0.1);
    }

    public function test_calculate_peer_grade_three_errors_same_weight_b() {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => 1);
        $this->strategy->dimensions[109] = (object)array('weight' => 1);
        $this->strategy->dimensions[111] = (object)array('weight' => 1);

        $this->strategy->mappings        = array(
                                                1 => (object)array('grade' => 80.0),
                                                2 => (object)array('grade' => 60.0),
                                                3 => (object)array('grade' => 0.0),
                                            );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 111, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 109, 'grade' => 0);

        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual($suggested, 0);
    }

    public function test_calculate_peer_grade_one_error_weighted() {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => 1);
        $this->strategy->dimensions[109] = (object)array('weight' => 2);
        $this->strategy->dimensions[111] = (object)array('weight' => 0);

        $this->strategy->mappings        = array(
                                                1 => (object)array('grade' => 66.0),
                                                2 => (object)array('grade' => 33.0),
                                                3 => (object)array('grade' => 0.0),
                                            );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => 1);
        $grades[] = (object)array('dimensionid' => 111, 'grade' => 1);
        $grades[] = (object)array('dimensionid' => 109, 'grade' => 0);

        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual($suggested, 0.33);
    }

    public function test_calculate_peer_grade_zero_weight() {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => 1);
        $this->strategy->dimensions[109] = (object)array('weight' => 2);
        $this->strategy->dimensions[111] = (object)array('weight' => 0);

        $this->strategy->mappings        = array(
                                                1 => (object)array('grade' => 66.0),
                                                2 => (object)array('grade' => 33.0),
                                                3 => (object)array('grade' => 0.0),
                                            );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => 1);
        $grades[] = (object)array('dimensionid' => 111, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 109, 'grade' => 1);

        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual($suggested, 1.0);
    }

    public function test_calculate_peer_grade_sum_weight() {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => 1);
        $this->strategy->dimensions[109] = (object)array('weight' => 2);
        $this->strategy->dimensions[111] = (object)array('weight' => 3);

        $this->strategy->mappings        = array(
                                                1 => (object)array('grade' => 90.0),
                                                2 => (object)array('grade' => 80.0),
                                                3 => (object)array('grade' => 70.0),
                                                4 => (object)array('grade' => 60.0),
                                                5 => (object)array('grade' => 30.0),
                                                6 => (object)array('grade' => 5.0),
                                                7 => (object)array('grade' => 0.0),
                                            );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 111, 'grade' => 0);
        $grades[] = (object)array('dimensionid' => 109, 'grade' => 0);

        // excercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual($suggested, 0.05);
    }
}
