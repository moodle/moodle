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
 * Unit tests for (some of) question/type/numerical/questiontype.php.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');


/**
 * Unit tests for question/type/numerical/questiontype.php.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_numerical_qtype_test extends UnitTestCase {
    public static $includecoverage = array('question/type/questiontype.php', 'question/type/numerical/questiontype.php');
    var $tolerance = 0.00000001;
    var $qtype;

    function setUp() {
        $this->qtype = new question_numerical_qtype();
    }

    function tearDown() {
        $this->qtype = null;
    }

    function test_name() {
        $this->assertEqual($this->qtype->name(), 'numerical');
    }

    function test_get_tolerance_interval() {
        $answer = new stdClass();
        $answer->tolerance = 0.01;
        $answer->tolerancetype = 'relative';
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0.99, $this->tolerance);
        $this->assertWithinMargin($answer->max, 1.01, $this->tolerance);

        $answer = new stdClass();
        $answer->tolerance = 0.01;
        $answer->tolerancetype = 'relative';
        $answer->answer = 10.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 9.9, $this->tolerance);
        $this->assertWithinMargin($answer->max, 10.1, $this->tolerance);

        $answer = new stdClass();
        $answer->tolerance = 0.01;
        $answer->tolerancetype = 'nominal';
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0.99, $this->tolerance);
        $this->assertWithinMargin($answer->max, 1.01, $this->tolerance);

        $answer = new stdClass();
        $answer->tolerance = 2.0;
        $answer->tolerancetype = 'nominal';
        $answer->answer = 10.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 8, $this->tolerance);
        $this->assertWithinMargin($answer->max, 12, $this->tolerance);

        $answer = new stdClass(); // Test default tolerance 0.
        $answer->tolerancetype = 'nominal';
        $answer->answer = 0.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0, $this->tolerance);
        $this->assertWithinMargin($answer->max, 0, $this->tolerance);

        $answer = new stdClass(); // Test default type nominal.
        $answer->tolerance = 1.0;
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0, $this->tolerance);
        $this->assertWithinMargin($answer->max, 2, $this->tolerance);

        $answer = new stdClass();
        $answer->tolerance = 1.0;
        $answer->tolerancetype = 'geometric';
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0.5, $this->tolerance);
        $this->assertWithinMargin($answer->max, 2.0, $this->tolerance);
    }

    function test_apply_unit() {
        $units = array(
            (object) array('unit' => 'm', 'multiplier' => 1),
            (object) array('unit' => 'cm', 'multiplier' => 100),
            (object) array('unit' => 'mm', 'multiplier' => 1000),
            (object) array('unit' => 'inch', 'multiplier' => 1.0/0.0254)
        );

        $this->assertWithinMargin($this->qtype->apply_unit('1', $units), 1, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('1.0', $units), 1, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('-1e0', $units), -1, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('100m', $units), 100, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('1cm', $units), 0.01, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('12inch', $units), .3048, $this->tolerance);
    //    $this->assertIdentical($this->qtype->apply_unit('1km', $units), false);
        $this->assertWithinMargin($this->qtype->apply_unit('-100', array()), -100, $this->tolerance);
    //    $this->assertIdentical($this->qtype->apply_unit('1000 miles', array()), false);
    }

//    function test_backup() {
//    }
//
//    function test_restore() {
//    }
}


