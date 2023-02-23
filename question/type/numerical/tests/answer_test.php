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

namespace qtype_numerical;

use qtype_numerical_answer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/numerical/question.php');

/**
 * Unit tests for the numerical question definition class.
 *
 * @package   qtype_numerical
 * @category  test
 * @copyright 2008 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class answer_test extends \advanced_testcase {
    public function test_within_tolerance_nominal() {
        $answer = new qtype_numerical_answer(13, 7.0, 1.0, '', FORMAT_MOODLE, 1.0);

        $this->assertFalse($answer->within_tolerance(5.99));
        $this->assertTrue($answer->within_tolerance(6));
        $this->assertTrue($answer->within_tolerance(7));
        $this->assertTrue($answer->within_tolerance(8));
        $this->assertFalse($answer->within_tolerance(8.01));
    }

    public function test_within_tolerance_nominal_zero() {
        // Either an answer or tolerance of 0 requires special care. We still
        // don't want to end up comparing two floats for absolute equality.

        // Zero tol, non-zero answer.
        $answer = new qtype_numerical_answer(13, 1e-20, 1.0, '', FORMAT_MOODLE, 0.0);
        $this->assertFalse($answer->within_tolerance(0.9999999e-20));
        $this->assertTrue($answer->within_tolerance(1e-20));
        $this->assertFalse($answer->within_tolerance(1.0000001e-20));

        // Non-zero tol, zero answer.
        $answer = new qtype_numerical_answer(13, 0.0, 1.0, '', FORMAT_MOODLE, 1e-24);
        $this->assertFalse($answer->within_tolerance(-2e-24));
        $this->assertTrue($answer->within_tolerance(-1e-24));
        $this->assertTrue($answer->within_tolerance(0));
        $this->assertTrue($answer->within_tolerance(1e-24));
        $this->assertFalse($answer->within_tolerance(2e-24));

        // Zero tol, zero answer.
        $answer = new qtype_numerical_answer(13, 0.0, 1.0, '', FORMAT_MOODLE, 1e-24);
        $this->assertFalse($answer->within_tolerance(-1e-20));
        $this->assertTrue($answer->within_tolerance(-1e-35));
        $this->assertTrue($answer->within_tolerance(0));
        $this->assertTrue($answer->within_tolerance(1e-35));
        $this->assertFalse($answer->within_tolerance(1e-20));

        // Non-zero tol, non-zero answer.
        $answer = new qtype_numerical_answer(13, 1e-20, 1.0, '', FORMAT_MOODLE, 1e-24);
        $this->assertFalse($answer->within_tolerance(1.0002e-20));
        $this->assertTrue($answer->within_tolerance(1.0001e-20));
        $this->assertTrue($answer->within_tolerance(1e-20));
        $this->assertTrue($answer->within_tolerance(1.0001e-20));
        $this->assertFalse($answer->within_tolerance(1.0002e-20));
    }

    public function test_within_tolerance_blank() {
        $answer = new qtype_numerical_answer(13, 1234, 1.0, '', FORMAT_MOODLE, '');
        $this->assertTrue($answer->within_tolerance(1234));
        $this->assertFalse($answer->within_tolerance(1234.000001));
        $this->assertFalse($answer->within_tolerance(0));
    }

    public function test_within_tolerance_relative() {
        $answer = new qtype_numerical_answer(13, 7.0, 1.0, '', FORMAT_MOODLE, 0.1);
        $answer->tolerancetype = 1;

        $this->assertFalse($answer->within_tolerance(6.29));
        $this->assertTrue($answer->within_tolerance(6.3));
        $this->assertTrue($answer->within_tolerance(7));
        $this->assertTrue($answer->within_tolerance(7.7));
        $this->assertFalse($answer->within_tolerance(7.71));
    }

    public function test_within_tolerance_geometric() {
        $answer = new qtype_numerical_answer(13, 7.0, 1.0, '', FORMAT_MOODLE, 1.0);
        $answer->tolerancetype = 3;

        $this->assertFalse($answer->within_tolerance(3.49));
        $this->assertTrue($answer->within_tolerance(3.5));
        $this->assertTrue($answer->within_tolerance(7));
        $this->assertTrue($answer->within_tolerance(14));
        $this->assertFalse($answer->within_tolerance(14.01));

        // Geometric tolerance, negative answer.
        $answer = new qtype_numerical_answer(13, -7.0, 1.0, '', FORMAT_MOODLE, 1.0);
        $answer->tolerancetype = 3;

        $this->assertFalse($answer->within_tolerance(-3.49));
        $this->assertTrue($answer->within_tolerance(-3.5));
        $this->assertTrue($answer->within_tolerance(-7));
        $this->assertTrue($answer->within_tolerance(-14));
        $this->assertFalse($answer->within_tolerance(-14.01));
    }
}
