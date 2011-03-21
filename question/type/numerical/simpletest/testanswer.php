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
 * Unit tests for the numerical question definition class.
 *
 * @package moodlecore
 * @subpackage questiontypes
 * @copyright 2008 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/type/numerical/question.php');

class qtype_numerical_answer_test extends UnitTestCase {
    public function test_within_tolerance_nominal() {
        $answer = new qtype_numerical_answer(13, 7.0, 1.0, '', FORMAT_MOODLE, 1.0);

        $this->assertFalse($answer->within_tolerance(5.99));
        $this->assertTrue($answer->within_tolerance(6));
        $this->assertTrue($answer->within_tolerance(7));
        $this->assertTrue($answer->within_tolerance(8));
        $this->assertFalse($answer->within_tolerance(8.01));
    }

    public function test_within_tolerance_blank() {
        $answer = new qtype_numerical_answer(13, 1234, 1.0, '', FORMAT_MOODLE, '');
        $this->assertTrue($answer->within_tolerance(1234));
        $this->assertFalse($answer->within_tolerance(1234.000001));
        $this->assertFalse($answer->within_tolerance(0));

        $answer = new qtype_numerical_answer(13, 0, 1.0, '', FORMAT_MOODLE, '');
        $this->assertTrue($answer->within_tolerance(0));
        $this->assertFalse($answer->within_tolerance(pow(10, -1 * ini_get('precision') + 1)));
        $this->assertTrue($answer->within_tolerance(pow(10, -1 * ini_get('precision'))));
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
    }
}
