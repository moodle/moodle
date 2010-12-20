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
 * Defines the 'qtype_missingtype' question definition class.
 *
 * @package qtype_missingtype
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * This question definition class is used when the actual question type of this
 * question cannot be found.
 *
 * Why does this this class implement question_automatically_gradable? I am not
 * sure at the moment. Perhaps it is important for it to work with as many
 * behaviours as possible.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_missingtype_question extends question_definition implements question_automatically_gradable {
    public function get_expected_data() {
        return array();
    }

    public function get_correct_response() {
        return array();
    }

    public function is_complete_response(array $response) {
        return false;
    }

    public function is_gradable_response(array $response) {
        return false;
    }

    public function get_validation_error(array $response) {
        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return true;
    }

    public function get_right_answer_summary() {
        return '';
    }

    public function summarise_response(array $response) {
        return null;
    }

    public function classify_response(array $response) {
        return array();
    }

    public function init_first_step(question_attempt_step $step) {
    }

    public function grade_response(array $response) {
        throw new Exception('This question is of a type that is not installed on your system. No processing is possible.');
    }

    public function get_hint($hintnumber, question_attempt $qa) {
        return null;
    }
}
