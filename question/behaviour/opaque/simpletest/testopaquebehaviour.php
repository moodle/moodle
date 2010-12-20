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
 * This file contains tests for the Opaque behaviour.
 *
 * @package qbehaviour_opaque
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../engine/lib.php');
require_once(dirname(__FILE__) . '/../../../engine/simpletest/helpers.php');
require_once(dirname(__FILE__) . '/../behaviour.php');

class qbehaviour_opaque_test extends qbehaviour_walkthrough_test_base {
    /**
     * Makes an Opaque question that refers to one of the sample questions
     * supplied by OpenMark.
     * @return unknown_type
     */
    protected function make_standard_om_question() {
        $engineid = get_field('question_opaque_engines', 'MIN(id)', '', '');
        if (empty($engineid)) {
            throw new Exception('Cannot test Opaque. No question engines configured.');
        }

        question_bank::load_question_definition_classes('opaque');
        $q = new qtype_opaque_question();
        test_question_maker::initialise_a_question($q);

        $q->name = 'samples.mu120.module5.question01';
        $q->qtype = question_bank::get_qtype('opaque');
        $q->defaultmark = 3;

        $q->engineid = $engineid;
        $q->remoteid = 'samples.mu120.module5.question01';
        $q->remoteversion = '1.0';

        return $q;
    }

    public function test_wrong_three_times() {
        $q = $this->make_standard_om_question();
        $this->start_attempt_at_question($q, 'interactive');
        $qa = $this->quba->get_question_attempt($this->slot);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/Below is a plan of a proposed garden/'),
                new PatternExpectation('/You have 3 attempts/'),
                $this->get_contains_button_expectation($qa->get_qt_field_name('omact_gen_14'), 'Check'));
        $this->assertPattern('/^\s*Below is a plan of a proposed garden./',
                $qa->get_question_summary());
        $this->assertNull($qa->get_right_answer_summary());

        // Submit a wrong answer.
        $this->process_submission(array('omval_response1' => 1, 'omval_response2' => 666,
                'omact_gen_14' => 'Check'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/Below is a plan of a proposed garden/'),
                new PatternExpectation('/incorrect/'),
                new PatternExpectation('/' . preg_quote(get_string('notcomplete', 'qbehaviour_opaque')) . '/'),
                $this->get_contains_button_expectation($qa->get_qt_field_name('omact_ok'), 'Try again'));

        // Try again.
        $this->process_submission(array('omact_ok' => 'Try again'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/You have 2 attempts/'));

        // Submit a wrong answer again.
        $this->process_submission(array('omval_response1' => 1, 'omval_response2' => 666,
                'omact_gen_14' => 'Check'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/Below is a plan of a proposed garden/'),
                new PatternExpectation('/still incorrect/'),
                new PatternExpectation('/' . preg_quote(get_string('notcomplete', 'qbehaviour_opaque')) . '/'));

        // Try again.
        $this->process_submission(array('omact_ok' => 'Try again'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/This is your last attempt/'));

        // Submit a wrong answer third time.
        $this->process_submission(array('omval_response1' => 1, 'omval_response2' => 666,
                'omact_gen_14' => 'Check'));

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0);
        $this->check_current_output(
                new PatternExpectation('/Please see MU120 Preparatory Resource Book B section 5.1/'),
                new PatternExpectation('/still incorrect/'));
        $this->assertTrue(preg_match('/What is \(X\*W\) (\d+\.\d+)\*(\d+), \(X\*L\)(\d+\.\d+)\*(\d+)\?/',
                $qa->get_question_summary(), $matches));
        $this->assertNull($qa->get_right_answer_summary());
        $this->assertPattern('/' . $matches[1]*$matches[2] . '.*, ' . $matches[3]*$matches[4] . '/',
                $qa->get_response_summary());
    }

    public function test_right_first_time() {
        $q = $this->make_standard_om_question();
        $this->start_attempt_at_question($q, 'interactive');
        $qa = $this->quba->get_question_attempt($this->slot);

        // Work out right answer (yuck!)
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        preg_match('/(0\.5|2\.0|3\.0) metres/', $html, $matches);
        $scale = $matches[1];
        preg_match('/Patio|Summer House|Flowerbed|Vegetable Plot|Pond/', $html, $matches);
        $feature = $matches[0];
        $sizes = array(
            'Patio' => array(4, 7),
            'Summer House' => array(3, 5),
            'Flowerbed' => array(2, 7),
            'Vegetable Plot' => array(3, 10),
            'Pond' => array(2, 3),
        );
        $size = $sizes[$feature];

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/Below is a plan of a proposed garden/'),
                new PatternExpectation('/You have 3 attempts/'),
                $this->get_contains_button_expectation($qa->get_qt_field_name('omact_gen_14'), 'Check'));

        // Submit the right answer.
        $this->process_submission(array('omval_response1' => $size[0] * $scale,
                'omval_response2' => $size[1] * $scale, 'omact_gen_14' => 'Check'));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                new PatternExpectation('/Below is a plan of a proposed garden/'),
                new PatternExpectation('/correct/'));
    }

    public function test_different_max() {
        $q = $this->make_standard_om_question();
        $this->start_attempt_at_question($q, 'interactive', 6.0);
        $qa = $this->quba->get_question_attempt($this->slot);

        // Work out right answer (yuck!)
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        preg_match('/(0\.5|2\.0|3\.0) metres/', $html, $matches);
        $scale = $matches[1];
        preg_match('/Patio|Summer House|Flowerbed|Vegetable Plot|Pond/', $html, $matches);
        $feature = $matches[0];
        $sizes = array(
            'Patio' => array(4, 7),
            'Summer House' => array(3, 5),
            'Flowerbed' => array(2, 7),
            'Vegetable Plot' => array(3, 10),
            'Pond' => array(2, 3),
        );
        $size = $sizes[$feature];

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/Below is a plan of a proposed garden/'),
                new PatternExpectation('/You have 3 attempts/'),
                $this->get_contains_button_expectation($qa->get_qt_field_name('omact_gen_14'), 'Check'));

        // Submit the right answer.
        $this->process_submission(array('omval_response1' => $size[0] * $scale,
                'omval_response2' => $size[1] * $scale, 'omact_gen_14' => 'Check'));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(6);
        $this->check_current_output(
                new PatternExpectation('/Below is a plan of a proposed garden/'),
                new PatternExpectation('/correct/'));
    }

    public function test_gave_up() {
        $q = $this->make_standard_om_question();
        $this->start_attempt_at_question($q, 'interactive');

        $this->quba->finish_all_questions();

        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
                new PatternExpectation('/' .
                        preg_quote(get_string('notcompletedmessage', 'qtype_opaque')) . '/'));
    }
}
