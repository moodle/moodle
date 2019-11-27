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
 * This file contains tests that just test the display mark/penalty information.
 *
 * @package   qbehaviour_adaptive
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../../engine/lib.php');
require_once(__DIR__ . '/../behaviour.php');


/**
 * Unit tests for the adaptive behaviour the display of mark/penalty information.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_adaptive_mark_display_test extends basic_testcase {
    /** @var qbehaviour_adaptive_renderer the renderer to test. */
    protected $renderer;

    /** @var question_display_options display options to use when rendering. */
    protected $options;

    protected function setUp() {
        global $PAGE;
        parent::setUp();
        $this->renderer = $PAGE->get_renderer('qbehaviour_adaptive');
        $this->options = new question_display_options();
    }

    public function test_blank_before_graded() {
        $this->assertEquals('',
                $this->renderer->render_adaptive_marks(new qbehaviour_adaptive_mark_details(
                        question_state::$todo), $this->options));
    }

    public function test_correct_no_penalty() {
        $this->assertEquals('<div class="correctness badge correct">' . get_string('correct', 'question') . '</div>' .
                '<div class="gradingdetails">' .
                get_string('gradingdetails', 'qbehaviour_adaptive',
                        array('cur' => '1.00', 'raw' => '1.00', 'max' => '1.00')) . '</div>',
                $this->renderer->render_adaptive_marks(new qbehaviour_adaptive_mark_details(
                        question_state::$gradedright, 1, 1, 1, 0, 0, false), $this->options));
    }

    public function test_partial_first_try() {
        $this->assertEquals('<div class="correctness badge partiallycorrect">' . get_string('partiallycorrect', 'question') .
                '</div><div class="gradingdetails">' .
                get_string('gradingdetails', 'qbehaviour_adaptive',
                        array('cur' => '0.50', 'raw' => '0.50', 'max' => '1.00')) . ' ' .
                get_string('gradingdetailspenalty', 'qbehaviour_adaptive', '0.10') . '</div>',
                $this->renderer->render_adaptive_marks(new qbehaviour_adaptive_mark_details(
                        question_state::$gradedpartial, 1, 0.5, 0.5, 0.1, 0.1, true), $this->options));
    }

    public function test_partial_second_try() {
        $mark = array('cur' => '0.80', 'raw' => '0.90', 'max' => '1.00');
        $this->assertEquals('<div class="correctness badge partiallycorrect">' . get_string('partiallycorrect', 'question') .
                '</div><div class="gradingdetails">' .
                get_string('gradingdetails', 'qbehaviour_adaptive', $mark) . ' ' .
                get_string('gradingdetailsadjustment', 'qbehaviour_adaptive', $mark) . ' ' .
                get_string('gradingdetailspenalty', 'qbehaviour_adaptive', '0.10') . ' ' .
                get_string('gradingdetailspenaltytotal', 'qbehaviour_adaptive', '0.20') . '</div>',
                $this->renderer->render_adaptive_marks(new qbehaviour_adaptive_mark_details(
                        question_state::$gradedpartial, 1, 0.8, 0.9, 0.1, 0.2, true), $this->options));
    }

    public function test_correct_third_try() {
        $mark = array('cur' => '0.80', 'raw' => '1.00', 'max' => '1.00');
        $this->assertEquals('<div class="correctness badge partiallycorrect">' . get_string('partiallycorrect', 'question') .
                '</div><div class="gradingdetails">' .
                get_string('gradingdetails', 'qbehaviour_adaptive', $mark) . ' ' .
                get_string('gradingdetailsadjustment', 'qbehaviour_adaptive', $mark) . '</div>',
                $this->renderer->render_adaptive_marks(new qbehaviour_adaptive_mark_details(
                        question_state::$gradedpartial, 1, 0.8, 1.0, 0.1, 0.3, false), $this->options));
    }

    public function test_correct_third_try_if_we_dont_increase_penalties_for_wrong() {
        $mark = array('cur' => '0.80', 'raw' => '1.00', 'max' => '1.00');
        $this->assertEquals('<div class="correctness badge partiallycorrect">' . get_string('partiallycorrect', 'question') .
                '</div><div class="gradingdetails">' .
                get_string('gradingdetails', 'qbehaviour_adaptive', $mark) . ' ' .
                get_string('gradingdetailsadjustment', 'qbehaviour_adaptive', $mark) . '</div>',
                $this->renderer->render_adaptive_marks(new qbehaviour_adaptive_mark_details(
                        question_state::$gradedpartial, 1, 0.8, 1.0, 0, 0.2, false), $this->options));
    }
}
