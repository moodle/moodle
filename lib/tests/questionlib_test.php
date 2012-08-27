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
 * Unit tests for (some of) ../questionlib.php.
 *
 * @package    core_question
 * @category   phpunit
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for (some of) ../questionlib.php.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questionlib_testcase extends basic_testcase {

    public function test_question_reorder_qtypes() {
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');

        $this->assertEquals(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't1', +1),
            array(0 => 't2', 1 => 't1', 2 => 't3'));
        $this->assertEquals(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't1', -1),
            array(0 => 't1', 1 => 't2', 2 => 't3'));
        $this->assertEquals(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't2', -1),
            array(0 => 't2', 1 => 't1', 2 => 't3'));
        $this->assertEquals(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't3', +1),
            array(0 => 't1', 1 => 't2', 2 => 't3'));
        $this->assertEquals(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 'missing', +1),
            array(0 => 't1', 1 => 't2', 2 => 't3'));
    }

    public function test_match_grade_options() {
        $gradeoptions = question_bank::fraction_options_full();

        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.3333333, 'error'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.333333, 'error'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33333, 'error'));
        $this->assertFalse(match_grade_options($gradeoptions, 0.3333, 'error'));

        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.3333333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.333333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33, 'nearest'));

        $this->assertEquals(-0.1428571, match_grade_options($gradeoptions, -0.15, 'nearest'));
    }
}
