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
 * Unit tests for formula validation code.
 *
 * @package    qtype_calculated
 * @copyright  2014 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/calculated/questiontype.php');


/**
 * Unit tests for formula validation code.
 *
 * @copyright  2014 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_formula_validation_testcase extends basic_testcase {
    protected function assert_nonempty_string($actual) {
        $this->assertInternalType('string', $actual);
        $this->assertNotEquals('', $actual);
    }

    public function test_simple_equations_ok() {
        $this->assertFalse(qtype_calculated_find_formula_errors(1));
        $this->assertFalse(qtype_calculated_find_formula_errors('1 + 1'));
        $this->assertFalse(qtype_calculated_find_formula_errors('{x} + {y}'));
        $this->assertFalse(qtype_calculated_find_formula_errors('{x}*{y}'));
    }

    public function test_safe_functions_ok() {
        $this->assertFalse(qtype_calculated_find_formula_errors('abs(-1)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('tan(pi())'));
        $this->assertFalse(qtype_calculated_find_formula_errors('log(10)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('log(64, 2)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('atan2(1.0, 1.0)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('max(1.0, 1.0)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('max(1.0, 1.0, 2.0)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('max(1.0, 1.0, 2, 3)'));
    }

    public function test_dangerous_functions_blocked() {
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('eval(1)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('system(1)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('base64_decode(1)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('unserialize(1)'));

        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('cos(tan(1) + abs(cos(eval)) * pi())'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('eval (CONSTANTREADASSTRING)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors("eval \t ()"));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('"eval"()'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('?><?php()'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('?><?php+1'));
    }

    public function test_functions_with_wrong_num_args_caught() {
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('abs(-1, 1)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('abs()'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('pi(1)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('log()'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('log(64, 2, 3)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('atan2(1.0)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('atan2(1.0, 1.0, 2.0)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('max(1.0)'));
    }
}
