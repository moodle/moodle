<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * The description question type is not acutally a question, it is just a way
 * to add some static content in the middle of a quiz, or other place that
 * questions are used.
 *
 * @package questionbank
 * @subpackage questiontypes
 */
class description_qtype extends default_questiontype {

    function name() {
        return 'description';
    }

    function is_real_question_type() {
        return false;
    }

    function is_usable_by_random() {
        return false;
    }

    function save_question($question, $form) {
        // Make very sure that descriptions can'e be created with a grade of
        // anything other than 0.
        $form->defaultgrade = 0;
        return parent::save_question($question, $form);
    }

    function get_question_options(&$question) {
        return true;
    }

    function save_question_options($question) {
        return true;
    }

    function print_question(&$question, &$state, $number, $cmoptions, $options) {
        global $CFG;
        $isfinished = question_state_is_graded($state->last_graded) || $state->event == QUESTION_EVENTCLOSE;

        // For editing teachers print a link to an editing popup window
        $editlink = $this->get_question_edit_link($question, $cmoptions, $options);

        $questiontext = $this->format_text($question->questiontext, $question->questiontextformat, $cmoptions);

        $generalfeedback = '';
        if ($isfinished && $options->generalfeedback) {
            $generalfeedback = $this->format_text($question->generalfeedback,
                    $question->generalfeedbackformat, $cmoptions);
        }

        include "$CFG->dirroot/question/type/description/question.html";
    }

    function actual_number_of_questions($question) {
        return 0;
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $state->raw_grade = 0;
        $state->penalty = 0;
        return true;
    }
}
// Register this question type with questionlib.php.
question_register_questiontype(new description_qtype());
