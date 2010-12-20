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
 * @package moodlecore
 * @subpackage questionengine
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../lib.php');
require_once(dirname(__FILE__) . '/helpers.php');

class question_attempt_step_test extends UnitTestCase {
    public function test_initial_state_unprocessed() {
        $step = new question_attempt_step();
        $this->assertEqual(question_state::$unprocessed, $step->get_state());
    }

    public function test_get_set_state() {
        $step = new question_attempt_step();
        $step->set_state(question_state::$gradedright);
        $this->assertEqual(question_state::$gradedright, $step->get_state());
    }

    public function test_initial_fraction_null() {
        $step = new question_attempt_step();
        $this->assertNull($step->get_fraction());
    }

    public function test_get_set_fraction() {
        $step = new question_attempt_step();
        $step->set_fraction(0.5);
        $this->assertEqual(0.5, $step->get_fraction());
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
        $this->assertEqual('1', $step->get_qt_var('x'));
        $this->assertEqual('frog', $step->get_behaviour_var('y'));
        $this->assertNull($step->get_qt_var('y'));
    }

    public function test_set_var() {
        $step = new question_attempt_step();
        $step->set_qt_var('_x', 1);
        $step->set_behaviour_var('_x', 2);
        $this->assertEqual('1', $step->get_qt_var('_x'));
        $this->assertEqual('2', $step->get_behaviour_var('_x'));
    }

    public function test_cannot_set_qt_var_without_underscore() {
        $step = new question_attempt_step();
        $this->expectException();
        $step->set_qt_var('x', 1);
    }

    public function test_cannot_set_behaviour_var_without_underscore() {
        $step = new question_attempt_step();
        $this->expectException();
        $step->set_behaviour_var('x', 1);
    }

    public function test_get_data() {
        $step = new question_attempt_step(array('x' => 1, '-y' => 'frog', ':flagged' => 1));
        $this->assertEqual(array('x' => '1'), $step->get_qt_data());
        $this->assertEqual(array('y' => 'frog'), $step->get_behaviour_data());
        $this->assertEqual(array('x' => 1, '-y' => 'frog', ':flagged' => 1), $step->get_all_data());
    }

    public function test_get_submitted_data() {
        $step = new question_attempt_step(array('x' => 1, '-y' => 'frog'));
        $step->set_qt_var('_x', 1);
        $step->set_behaviour_var('_x', 2);
        $this->assertEqual(array('x' => 1, '-y' => 'frog'), $step->get_submitted_data());
    }

    public function test_constructor_default_params() {
        global $USER;
        $step = new question_attempt_step();
        $this->assertWithinMargin(time(), $step->get_timecreated(), 5);
        $this->assertEqual($USER->id, $step->get_user_id());
        $this->assertEqual(array(), $step->get_qt_data());
        $this->assertEqual(array(), $step->get_behaviour_data());

    }

    public function test_constructor_given_params() {
        global $USER;
        $step = new question_attempt_step(array(), 123, 5);
        $this->assertEqual(123, $step->get_timecreated());
        $this->assertEqual(5, $step->get_user_id());
        $this->assertEqual(array(), $step->get_qt_data());
        $this->assertEqual(array(), $step->get_behaviour_data());

    }
}


class question_attempt_step_db_test extends data_loading_method_test_base {
    public function test_load_with_data() {
        $records = $this->build_db_records(array(
            array('id', 'attemptstepid', 'questionattemptid', 'sequencenumber', 'state', 'fraction', 'timecreated', 'userid', 'name', 'value'),
            array(  1,               1,                   1,                0,  'todo',       null,    1256228502,       13,   null,    null),
            array(  2,               2,                   1,                1,  'complete',   null,    1256228505,       13,    'x',     'a'),
            array(  3,               2,                   1,                1,  'complete',   null,    1256228505,       13,   '_y',    '_b'),
            array(  4,               2,                   1,                1,  'complete',   null,    1256228505,       13,   '-z',    '!c'),
            array(  5,               2,                   1,                1,  'complete',   null,    1256228505,       13, '-_t',    '!_d'),
            array(  6,               3,                   1,                2,  'gradedright', 1.0,    1256228515,       13, '-finish',  '1'),
        ));

        $step = question_attempt_step::load_from_records($records, 2);
        $this->assertEqual(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEqual(1256228505, $step->get_timecreated());
        $this->assertEqual(13, $step->get_user_id());
        $this->assertEqual(array('x' => 'a', '_y' => '_b', '-z' => '!c', '-_t' => '!_d'), $step->get_all_data());
    }

    public function test_load_without_data() {
        $records = $this->build_db_records(array(
            array('id', 'attemptstepid', 'questionattemptid', 'sequencenumber', 'state', 'fraction', 'timecreated', 'userid', 'name', 'value'),
            array(  2,               2,                   1,                1,  'complete',   null,    1256228505,       13,   null,    null),
        ));

        $step = question_attempt_step::load_from_records($records, 2);
        $this->assertEqual(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEqual(1256228505, $step->get_timecreated());
        $this->assertEqual(13, $step->get_user_id());
        $this->assertEqual(array(), $step->get_all_data());
    }

    public function test_load_dont_be_too_greedy() {
        $records = $this->build_db_records(array(
            array('id', 'attemptstepid', 'questionattemptid', 'sequencenumber', 'state', 'fraction', 'timecreated', 'userid', 'name', 'value'),
            array(  1,               1,                   1,                0,  'todo',       null,    1256228502,       13,    'x',     'right'),
            array(  2,               2,                   2,                0,  'complete',   null,    1256228505,       13,    'x',     'wrong'),
        ));

        $step = question_attempt_step::load_from_records($records, 1);
        $this->assertEqual(array('x' => 'right'), $step->get_all_data());
    }
}