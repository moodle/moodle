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
 * Unit tests of mathslib wrapper and underlying EvalMath library.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2007 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/mathslib.php');


class mathsslib_testcase extends basic_testcase {

    /**
     * Tests the basic formula evaluation
     */
    public function test__basic() {
        $formula = new calc_formula('=1+2');
        $res = $formula->evaluate();
        $this->assertEquals($res, 3, '3+1 is: %s');
    }

    /**
     * Tests the formula params
     */
    public function test__params() {
        $formula = new calc_formula('=a+b+c', array('a'=>10, 'b'=>20, 'c'=>30));
        $res = $formula->evaluate();
        $this->assertEquals($res, 60, '10+20+30 is: %s');
    }

    /**
     * Tests the changed params
     */
    public function test__changing_params() {
        $formula = new calc_formula('=a+b+c', array('a'=>10, 'b'=>20, 'c'=>30));
        $res = $formula->evaluate();
        $this->assertEquals($res, 60, '10+20+30 is: %s');
        $formula->set_params(array('a'=>1, 'b'=>2, 'c'=>3));
        $res = $formula->evaluate();
        $this->assertEquals($res, 6, 'changed params 1+2+3 is: %s');
    }

    /**
     * Tests the spreadsheet emulation function in formula
     */
    public function test__calc_function() {
        $formula = new calc_formula('=sum(a, b, c)', array('a'=>10, 'b'=>20, 'c'=>30));
        $res = $formula->evaluate();
        $this->assertEquals($res, 60, 'sum(a, b, c) is: %s');
    }

    public function test_other_functions() {
        $formula = new calc_formula('=average(1,2,3)');
        $this->assertEquals($formula->evaluate(), 2);

        $formula = new calc_formula('=mod(10,3)');
        $this->assertEquals($formula->evaluate(), 1);

        $formula = new calc_formula('=power(2,3)');
        $this->assertEquals($formula->evaluate(), 8);
    }

    /**
     * Tests the min and max functions
     */
    public function test__minmax_function() {
        $formula = new calc_formula('=min(a, b, c)', array('a'=>10, 'b'=>20, 'c'=>30));
        $res = $formula->evaluate();
        $this->assertEquals($res, 10, 'minimum is: %s');
        $formula = new calc_formula('=max(a, b, c)', array('a'=>10, 'b'=>20, 'c'=>30));
        $res = $formula->evaluate();
        $this->assertEquals($res, 30, 'maximum is: %s');
    }

    /**
     * Tests special chars
     */
    public function test__specialchars() {
        $formula = new calc_formula('=gi1 + gi2 + gi11', array('gi1'=>10, 'gi2'=>20, 'gi11'=>30));
        $res = $formula->evaluate();
        $this->assertEquals($res, 60, 'sum is: %s');
    }

    /**
     * Tests some slightly more complex expressions
     */
    public function test__more_complex_expressions() {
        $formula = new calc_formula('=pi() + a', array('a'=>10));
        $res = $formula->evaluate();
        $this->assertEquals($res, pi()+10);
        $formula = new calc_formula('=pi()^a', array('a'=>10));
        $res = $formula->evaluate();
        $this->assertEquals($res, pow(pi(), 10));
        $formula = new calc_formula('=-8*(5/2)^2*(1-sqrt(4))-8');
        $res = $formula->evaluate();
        $this->assertEquals($res, -8*pow((5/2), 2)*(1-sqrt(4))-8);
    }

    /**
     * Tests some slightly more complex expressions
     */
    public function test__error_handling() {
        $formula = new calc_formula('=pi( + a', array('a'=>10));
        $res = $formula->evaluate();
        $this->assertEquals($res, false);
        $this->assertEquals($formula->get_error(),
            get_string('unexpectedoperator', 'mathslib', '+'));

        $formula = new calc_formula('=pi(');
        $res = $formula->evaluate();
        $this->assertEquals($res, false);
        $this->assertEquals($formula->get_error(),
            get_string('expectingaclosingbracket', 'mathslib'));

        $formula = new calc_formula('=pi()^');
        $res = $formula->evaluate();
        $this->assertEquals($res, false);
        $this->assertEquals($formula->get_error(),
            get_string('operatorlacksoperand', 'mathslib', '^'));

    }

    public function test_rounding_function() {
        // Rounding to the default number of decimal places
        // The default == 0

        $formula = new calc_formula('=round(2.5)');
        $this->assertEquals($formula->evaluate(), 3);

        $formula = new calc_formula('=round(1.5)');
        $this->assertEquals($formula->evaluate(), 2);

        $formula = new calc_formula('=round(-1.49)');
        $this->assertEquals($formula->evaluate(), -1);

        $formula = new calc_formula('=round(-2.49)');
        $this->assertEquals($formula->evaluate(), -2);

        $formula = new calc_formula('=round(-1.5)');
        $this->assertEquals($formula->evaluate(), -2);

        $formula = new calc_formula('=round(-2.5)');
        $this->assertEquals($formula->evaluate(), -3);

        $formula = new calc_formula('=ceil(2.5)');
        $this->assertEquals($formula->evaluate(), 3);

        $formula = new calc_formula('=ceil(1.5)');
        $this->assertEquals($formula->evaluate(), 2);

        $formula = new calc_formula('=ceil(-1.49)');
        $this->assertEquals($formula->evaluate(), -1);

        $formula = new calc_formula('=ceil(-2.49)');
        $this->assertEquals($formula->evaluate(), -2);

        $formula = new calc_formula('=ceil(-1.5)');
        $this->assertEquals($formula->evaluate(), -1);

        $formula = new calc_formula('=ceil(-2.5)');
        $this->assertEquals($formula->evaluate(), -2);

        $formula = new calc_formula('=floor(2.5)');
        $this->assertEquals($formula->evaluate(), 2);

        $formula = new calc_formula('=floor(1.5)');
        $this->assertEquals($formula->evaluate(), 1);

        $formula = new calc_formula('=floor(-1.49)');
        $this->assertEquals($formula->evaluate(), -2);

        $formula = new calc_formula('=floor(-2.49)');
        $this->assertEquals($formula->evaluate(), -3);

        $formula = new calc_formula('=floor(-1.5)');
        $this->assertEquals($formula->evaluate(), -2);

        $formula = new calc_formula('=floor(-2.5)');
        $this->assertEquals($formula->evaluate(), -3);

        // Rounding to an explicit number of decimal places

        $formula = new calc_formula('=round(2.5, 1)');
        $this->assertEquals($formula->evaluate(), 2.5);

        $formula = new calc_formula('=round(2.5, 0)');
        $this->assertEquals($formula->evaluate(), 3);

        $formula = new calc_formula('=round(1.2345, 2)');
        $this->assertEquals($formula->evaluate(), 1.23);

        $formula = new calc_formula('=round(123.456, -1)');
        $this->assertEquals($formula->evaluate(), 120);
    }

    public function test_scientific_notation() {
        $formula = new calc_formula('=10e10');
        $this->assertEquals($formula->evaluate(), 1e11, '', 1e11*1e-15);

        $formula = new calc_formula('=10e-10');
        $this->assertEquals($formula->evaluate(), 1e-9, '', 1e11*1e-15);

        $formula = new calc_formula('=10e+10');
        $this->assertEquals($formula->evaluate(), 1e11, '', 1e11*1e-15);

        $formula = new calc_formula('=10e10*5');
        $this->assertEquals($formula->evaluate(), 5e11, '', 1e11*1e-15);

        $formula = new calc_formula('=10e10^2');
        $this->assertEquals($formula->evaluate(), 1e22, '', 1e22*1e-15);

    }

    public function test_rand_float() {
        $formula = new calc_formula('=rand_float()');
        $result = $formula->evaluate();
        $this->assertTrue(is_float($result));
    }
}
