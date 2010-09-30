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
 * Unit tests for Accumulative grading strategy logic
 *
 * @package    workshopform
 * @subpackage accumulative
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the code to test
require_once($CFG->dirroot . '/mod/workshop/locallib.php');
require_once($CFG->dirroot . '/mod/workshop/form/accumulative/lib.php');

global $DB;
Mock::generate(get_class($DB), 'mockDB');

/**
 * Test subclass that makes all the protected methods we want to test public
 */
class testable_workshop_accumulative_strategy extends workshop_accumulative_strategy {

    /** allows to set dimensions manually */
    public $dimensions = array();

    /**
     * This is where the calculation of suggested grade for submission is done
     */
    public function calculate_peer_grade(array $grades) {
        return parent::calculate_peer_grade($grades);
    }
}

class workshop_accumulative_strategy_test extends UnitTestCase {

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

        $cm             = new stdclass();
        $course         = new stdclass();
        $context        = new stdclass();
        $workshop       = (object)array('id' => 42, 'strategy' => 'accumulative');
        $this->workshop = new workshop($workshop, $cm, $course, $context);
        $this->strategy = new testable_workshop_accumulative_strategy($this->workshop);
    }

    public function tearDown() {
        global $DB;
        $DB = $this->realDB;

        $this->workshop = null;
        $this->strategy = null;
    }

    public function test_calculate_peer_grade_null_grade() {
        // fixture set-up
        $this->strategy->dimensions = array();
        $grades = array();
        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertNull($suggested);
    }

    public function test_calculate_peer_grade_one_numerical() {
        // fixture set-up
        $this->strategy->dimensions[1003] = (object)array('grade' => '20', 'weight' => '1');
        $grades[] = (object)array('dimensionid' => 1003, 'grade' => '5.00000');
        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual(grade_floatval(5/20 * 100), $suggested);
    }

    public function test_calculate_peer_grade_negative_weight() {
        // fixture set-up
        $this->strategy->dimensions[1003] = (object)array('grade' => '20', 'weight' => '-1');
        $grades[] = (object)array('dimensionid' => 1003, 'grade' => '20');
        $this->expectException('coding_exception');
        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
    }

    public function test_calculate_peer_grade_one_numerical_weighted() {
        // fixture set-up
        $this->strategy->dimensions[1003] = (object)array('grade' => '20', 'weight' => '3');
        $grades[] = (object)array('dimensionid' => '1003', 'grade' => '5');
        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
        // validate
        $this->assertEqual(grade_floatval(5/20 * 100), $suggested);
    }

    public function test_calculate_peer_grade_three_numericals_same_weight() {
        // fixture set-up
        $this->strategy->dimensions[1003] = (object)array('grade' => '20', 'weight' => '2');
        $this->strategy->dimensions[1004] = (object)array('grade' => '100', 'weight' => '2');
        $this->strategy->dimensions[1005] = (object)array('grade' => '10', 'weight' => '2');

        $grades[] = (object)array('dimensionid' => 1003, 'grade' => '11.00000');
        $grades[] = (object)array('dimensionid' => 1004, 'grade' => '87.00000');
        $grades[] = (object)array('dimensionid' => 1005, 'grade' => '10.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);

        // validate
        $this->assertEqual(grade_floatval((11/20 + 87/100 + 10/10)/3 * 100), $suggested);
    }

    public function test_calculate_peer_grade_three_numericals_different_weights() {
        // fixture set-up
        $this->strategy->dimensions[1003] = (object)array('grade' => '15', 'weight' => 3);
        $this->strategy->dimensions[1004] = (object)array('grade' => '80', 'weight' => 1);
        $this->strategy->dimensions[1005] = (object)array('grade' => '5', 'weight' => 2);

        $grades[] = (object)array('dimensionid' => 1003, 'grade' => '7.00000');
        $grades[] = (object)array('dimensionid' => 1004, 'grade' => '66.00000');
        $grades[] = (object)array('dimensionid' => 1005, 'grade' => '4.00000');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);

        // validate
        $this->assertEqual(grade_floatval((7/15*3 + 66/80*1 + 4/5*2)/6 * 100), $suggested);
    }

    public function test_calculate_peer_grade_one_scale_max() {
        global $DB;

        // fixture set-up
        $mockscale = 'E,D,C,B,A';
        $this->strategy->dimensions[1008] = (object)array('grade' => '-10', 'weight' => 1);
        $grades[] = (object)array('dimensionid' => 1008, 'grade' => '5.00000');
        $DB->expectOnce('get_field', array('scale', 'scale', array('id' => 10), MUST_EXIST));
        $DB->setReturnValue('get_field', $mockscale);

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);

        // validate
        $this->assertEqual(100.00000, $suggested);
    }

    public function test_calculate_peer_grade_one_scale_min_with_scale_caching() {
        global $DB;

        // fixture set-up
        $this->strategy->dimensions[1008] = (object)array('grade' => '-10', 'weight' => 1);
        $grades[] = (object)array('dimensionid' => 1008, 'grade' => '1.00000');
        $DB->expectNever('get_field', array('scale', 'scale', array('id' => 10), MUST_EXIST)); // cached

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);

        // validate
        $this->assertEqual(0.00000, $suggested);
    }

    public function test_calculate_peer_grade_two_scales_weighted() {
        global $DB;

        // fixture set-up
        $mockscale13 = 'Poor,Good,Excellent';
        $mockscale17 = '-,*,**,***,****,*****,******';

        $this->strategy->dimensions[1012] = (object)array('grade' => '-13', 'weight' => 2);
        $this->strategy->dimensions[1019] = (object)array('grade' => '-17', 'weight' => 3);

        $grades[] = (object)array('dimensionid' => 1012, 'grade' => '2.00000'); // "Good"
        $grades[] = (object)array('dimensionid' => 1019, 'grade' => '5.00000'); // "****"

        $DB->expectAt(0, 'get_field', array('scale', 'scale', array('id' => 13), MUST_EXIST));
        $DB->setReturnValueAt(0, 'get_field', $mockscale13);

        $DB->expectAt(1, 'get_field', array('scale', 'scale', array('id' => 17), MUST_EXIST));
        $DB->setReturnValueAt(1, 'get_field', $mockscale17);

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);

        // validate
        $this->assertEqual(grade_floatval((1/2*2 + 4/6*3)/5 * 100), $suggested);
    }

    public function test_calculate_peer_grade_scale_exception() {
        global $DB;

        // fixture set-up
        $mockscale13 = 'Poor,Good,Excellent';
        $this->strategy->dimensions[1012] = (object)array('grade' => -13, 'weight' => 1);
        $DB->expectNever('get_field', array('scale', 'scale', array('id' => 13), MUST_EXIST)); // cached
        $grades[] = (object)array('dimensionid' => 1012, 'grade' => '4.00000'); // exceeds the number of scale items
        $this->expectException('coding_exception');

        // exercise SUT
        $suggested = $this->strategy->calculate_peer_grade($grades);
    }
}
