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
 * Unit tests for qtype_calculated_variable_substituter.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/calculated/question.php');


/**
 * Unit tests for {@link qtype_calculated_variable_substituter}.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_variable_substituter_test extends UnitTestCase {
    public function test_simple_equation() {
        $vs = new qtype_calculated_variable_substituter(array('a' => 1, 'b' => 2));
        $this->assertEqual(3, $vs->calculate('{a} + {b}'));
    }

    public function test_simple_equation_negatives() {
        $vs = new qtype_calculated_variable_substituter(array('a' => -1, 'b' => -2));
        $this->assertEqual(1, $vs->calculate('{a}-{b}'));
    }

    public function test_cannot_use_nonnumbers() {
        $this->expectException();
        $vs = new qtype_calculated_variable_substituter(array('a' => 'frog', 'b' => -2));
    }
}
