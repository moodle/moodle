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
 * This file contains tests for the question_attempt_step class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__) . '/../lib.php');
require_once(dirname(__FILE__) . '/helpers.php');


/**
 * Unit tests for the {@link question_attempt_step} class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_step_test extends advanced_testcase {
    public function test_initial_state_unprocessed() {
        $step = new question_attempt_step();
        $this->assertEquals(question_state::$unprocessed, $step->get_state());
    }

    public function test_get_set_state() {
        $step = new question_attempt_step();
        $step->set_state(question_state::$gradedright);
        $this->assertEquals(question_state::$gradedright, $step->get_state());
    }

    public function test_initial_fraction_null() {
        $step = new question_attempt_step();
        $this->assertNull($step->get_fraction());
    }

    public function test_get_set_fraction() {
        $step = new question_attempt_step();
        $step->set_fraction(0.5);
        $this->assertEquals(0.5, $step->get_fraction());
    }

    public function test_has_var() {
        $step = new question_attempt_step(array('x' => 1, '-y' => 'frog'));
        $this->assertTrue($step->has_qt_var('x'));
        $this->assertTrue($step->has_behaviour_var('y'));
        $this->assertFalse($step->has_qt_var('y'));
        $this->assertFalse($step->has_behaviour_var('x'));
    }

    public function test_get_var() {
        $step = new question_attempt_step(array('x' => 1, '-y' => 'frog'));
        $this->assertEquals('1', $step->get_qt_var('x'));
        $this->assertEquals('frog', $step->get_behaviour_var('y'));
        $this->assertNull($step->get_qt_var('y'));
    }

    public function test_set_var() {
        $step = new question_attempt_step();
        $step->set_qt_var('_x', 1);
        $step->set_behaviour_var('_x', 2);
        $this->assertEquals('1', $step->get_qt_var('_x'));
        $this->assertEquals('2', $step->get_behaviour_var('_x'));
    }

    public function test_cannot_set_qt_var_without_underscore() {
        $step = new question_attempt_step();
        $this->setExpectedException('moodle_exception');
        $step->set_qt_var('x', 1);
    }

    public function test_cannot_set_behaviour_var_without_underscore() {
        $step = new question_attempt_step();
        $this->setExpectedException('moodle_exception');
        $step->set_behaviour_var('x', 1);
    }

    public function test_get_data() {
        $step = new question_attempt_step(array('x' => 1, '-y' => 'frog', ':flagged' => 1));
        $this->assertEquals(array('x' => '1'), $step->get_qt_data());
        $this->assertEquals(array('y' => 'frog'), $step->get_behaviour_data());
        $this->assertEquals(array('x' => 1, '-y' => 'frog', ':flagged' => 1), $step->get_all_data());
    }

    public function test_get_submitted_data() {
        $step = new question_attempt_step(array('x' => 1, '-y' => 'frog'));
        $step->set_qt_var('_x', 1);
        $step->set_behaviour_var('_x', 2);
        $this->assertEquals(array('x' => 1, '-y' => 'frog'), $step->get_submitted_data());
    }

    public function test_constructor_default_params() {
        global $USER;
        $step = new question_attempt_step();
        $this->assertEquals(time(), $step->get_timecreated(), '', 5);
        $this->assertEquals($USER->id, $step->get_user_id());
        $this->assertEquals(array(), $step->get_qt_data());
        $this->assertEquals(array(), $step->get_behaviour_data());

    }

    public function test_constructor_given_params() {
        global $USER;
        $step = new question_attempt_step(array(), 123, 5);
        $this->assertEquals(123, $step->get_timecreated());
        $this->assertEquals(5, $step->get_user_id());
        $this->assertEquals(array(), $step->get_qt_data());
        $this->assertEquals(array(), $step->get_behaviour_data());

    }
}
