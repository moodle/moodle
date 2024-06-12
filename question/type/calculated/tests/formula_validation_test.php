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

namespace qtype_calculated;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/calculated/questiontype.php');

/**
 * Unit tests for formula validation code.
 *
 * @package    qtype_calculated
 * @copyright  2014 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class formula_validation_test extends \basic_testcase {
    protected function assert_nonempty_string($actual) {
        $this->assertIsString($actual);
        $this->assertNotEquals('', $actual);
    }

    public function test_simple_equations_ok(): void {
        $this->assertFalse(qtype_calculated_find_formula_errors(1));
        $this->assertFalse(qtype_calculated_find_formula_errors('1 + 1'));
        $this->assertFalse(qtype_calculated_find_formula_errors('{x} + {y}'));
        $this->assertFalse(qtype_calculated_find_formula_errors('{x}*{y}'));
        $this->assertFalse(qtype_calculated_find_formula_errors('{x}*({y}+1)'));
    }

    public function test_simple_equations_errors(): void {
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('{a{b}}'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('{a{b}}'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('{a}({b})'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('2({b})'));
    }

    public function test_safe_functions_ok(): void {
        $this->assertFalse(qtype_calculated_find_formula_errors('abs(-1)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('tan(pi())'));
        $this->assertFalse(qtype_calculated_find_formula_errors('log(10)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('log(64, 2)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('atan2(1.0, 1.0)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('max(1.0, 1.0)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('max(1.0, 1.0, 2.0)'));
        $this->assertFalse(qtype_calculated_find_formula_errors('max(1.0, 1.0, 2, 3)'));
    }

    public function test_php_comments_blocked(): void {
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('# No need for this.'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('/* Also blocked. */'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('1 + 1 /* Blocked too. */'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('// As is this.'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('1/*2'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('/*{a*///{x}}'));
    }

    public function test_dangerous_functions_blocked(): void {
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

    public function test_functions_with_wrong_num_args_caught(): void {
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('abs(-1, 1)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('abs()'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('pi(1)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('log()'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('log(64, 2, 3)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('atan2(1.0)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('atan2(1.0, 1.0, 2.0)'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors('max(1.0)'));
    }

    public function test_validation_of_formulas_in_text_ok(): void {
        $this->assertFalse(qtype_calculated_find_formula_errors_in_text(
                '<p>Look no equations.</p>'));
        $this->assertFalse(qtype_calculated_find_formula_errors_in_text(
                '<p>Simple variable: {x}.</p>'));
        $this->assertFalse(qtype_calculated_find_formula_errors_in_text(
                '<p>This is an equation: {=1+1}, as is this: {={x}+{y}}.</p>' .
                '<p>Here is a more complex one: {=sin(2*pi()*{theta})}.</p>'));
    }

    public function test_validation_of_formulas_in_text_bad_function(): void {
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors_in_text(
                '<p>This is an equation: {=eval(1)}.</p>'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors_in_text(
                '<p>Good: {=1+1}, bad: {=eval(1)}, good: {={x}+{y}}.</p>'));
        $this->assert_nonempty_string(qtype_calculated_find_formula_errors_in_text(
                '<p>Bad: {=eval(1)}, bad: {=system(1)}.</p>'));
    }
}
