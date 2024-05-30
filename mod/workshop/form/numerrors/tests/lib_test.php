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
 * Unit tests for Number of errors grading logic
 *
 * @package    workshopform_numerrors
 * @category   test
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace workshopform_numerrors;

use workshop;
use workshop_numerrors_strategy;

defined('MOODLE_INTERNAL') || die();

// Include the code to test
global $CFG;
require_once($CFG->dirroot . '/mod/workshop/locallib.php');
require_once($CFG->dirroot . '/mod/workshop/form/numerrors/lib.php');

/**
 * Unit tests for Number of errors grading lib.php
 */
class lib_test extends \advanced_testcase {

    /** workshop instance emulation */
    protected $workshop;

    /** instance of the strategy logic class being tested */
    protected $strategy;

    /**
     * Setup testing environment
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', array('strategy' => 'numerrors', 'course' => $course));
        $cm = get_fast_modinfo($course)->instances['workshop'][$workshop->id];
        $this->workshop = new workshop($workshop, $cm, $course);
        $this->strategy = new testable_workshop_numerrors_strategy($this->workshop);
    }

    protected function tearDown(): void {
        $this->workshop = null;
        $this->strategy = null;
        parent::tearDown();
    }

    public function test_calculate_peer_grade_null_grade(): void {
        // fixture set-up
        $this->strategy->dimensions   = array();
        $this->strategy->mappings     = array();
        $grades = array();
        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertNull($suggested);
    }

    public function test_calculate_peer_grade_no_error(): void {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => '1');
        $this->strategy->dimensions[109] = (object)array('weight' => '1');
        $this->strategy->dimensions[111] = (object)array('weight' => '1');
        $this->strategy->mappings        = array();
        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => '1.00000');
        $grades[] = (object)array('dimensionid' => 111, 'grade' => '1.00000');
        $grades[] = (object)array('dimensionid' => 109, 'grade' => '1.00000');
        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEquals($suggested, 100.00000);
    }

    public function test_calculate_peer_grade_one_error(): void {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => '1');
        $this->strategy->dimensions[109] = (object)array('weight' => '1');
        $this->strategy->dimensions[111] = (object)array('weight' => '1');

        $this->strategy->mappings        = array(
            1 => (object)array('grade' => '80.00000'),
            2 => (object)array('grade' => '60.00000'),
        );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => '1.00000');
        $grades[] = (object)array('dimensionid' => 111, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 109, 'grade' => '1.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEquals($suggested, 80.00000);
    }

    public function test_calculate_peer_grade_three_errors_same_weight_a(): void {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => '1.00000');
        $this->strategy->dimensions[109] = (object)array('weight' => '1.00000');
        $this->strategy->dimensions[111] = (object)array('weight' => '1.00000');

        $this->strategy->mappings        = array(
            1 => (object)array('grade' => '80.00000'),
            2 => (object)array('grade' => '60.00000'),
            3 => (object)array('grade' => '10.00000'),
        );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 111, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 109, 'grade' => '0.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEquals($suggested, 10.00000);
    }

    public function test_calculate_peer_grade_three_errors_same_weight_b(): void {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => '1.00000');
        $this->strategy->dimensions[109] = (object)array('weight' => '1.00000');
        $this->strategy->dimensions[111] = (object)array('weight' => '1.00000');

        $this->strategy->mappings        = array(
            1 => (object)array('grade' => '80.00000'),
            2 => (object)array('grade' => '60.00000'),
            3 => (object)array('grade' => '0.00000'),
        );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 111, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 109, 'grade' => '0.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEquals($suggested, 0.00000);
    }

    public function test_calculate_peer_grade_one_error_weighted(): void {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => '1');
        $this->strategy->dimensions[109] = (object)array('weight' => '2');
        $this->strategy->dimensions[111] = (object)array('weight' => '0');

        $this->strategy->mappings        = array(
            1 => (object)array('grade' => '66.00000'),
            2 => (object)array('grade' => '33.00000'),
            3 => (object)array('grade' => '0.00000'),
        );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => '1.00000');
        $grades[] = (object)array('dimensionid' => 111, 'grade' => '1.00000');
        $grades[] = (object)array('dimensionid' => 109, 'grade' => '0.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEquals($suggested, 33.00000);
    }

    public function test_calculate_peer_grade_zero_weight(): void {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => '1');
        $this->strategy->dimensions[109] = (object)array('weight' => '2');
        $this->strategy->dimensions[111] = (object)array('weight' => '0');

        $this->strategy->mappings        = array(
            1 => (object)array('grade' => '66.00000'),
            2 => (object)array('grade' => '33.00000'),
            3 => (object)array('grade' => '0.00000'),
        );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => '1.00000');
        $grades[] = (object)array('dimensionid' => 111, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 109, 'grade' => '1.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEquals($suggested, 100.00000);
    }

    public function test_calculate_peer_grade_sum_weight(): void {
        // fixture set-up
        $this->strategy->dimensions      = array();
        $this->strategy->dimensions[108] = (object)array('weight' => '1');
        $this->strategy->dimensions[109] = (object)array('weight' => '2');
        $this->strategy->dimensions[111] = (object)array('weight' => '3');

        $this->strategy->mappings        = array(
            1 => (object)array('grade' => '90.00000'),
            2 => (object)array('grade' => '80.00000'),
            3 => (object)array('grade' => '70.00000'),
            4 => (object)array('grade' => '60.00000'),
            5 => (object)array('grade' => '30.00000'),
            6 => (object)array('grade' => '5.00000'),
            7 => (object)array('grade' => '0.00000'),
        );

        $grades = array();
        $grades[] = (object)array('dimensionid' => 108, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 111, 'grade' => '0.00000');
        $grades[] = (object)array('dimensionid' => 109, 'grade' => '0.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEquals($suggested, 5.00000);
    }
}


/**
 * Test subclass that makes all the protected methods we want to test public
 */
class testable_workshop_numerrors_strategy extends workshop_numerrors_strategy {

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
