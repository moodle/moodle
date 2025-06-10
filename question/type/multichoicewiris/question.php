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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/wq/question.php');
require_once($CFG->dirroot . '/question/type/multichoice/question.php');

class qtype_multichoicewiris_question extends qtype_wq_question implements question_automatically_gradable_with_countback {

    // References to original multichoice question class.
    public $answers;
    public $shuffleanswers;
    public $answernumbering;
    public $layout;
    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;
    public $showstandardinstruction;

    public function join_all_text() {
        $text = parent::join_all_text();
        // Choices and feedbacks.
        foreach ($this->base->answers as $key => $value) {
            $text .= ' ' . $value->answer . ' ' . $value->feedback;
        }
        // Combined feedback.
        $text .= ' ' . $this->base->correctfeedback . ' ';
        $text .= $this->base->partiallycorrectfeedback . ' ' . $this->base->incorrectfeedback;

        return $text;
    }

    public function get_renderer(moodle_page $page) {
        if ($this->base instanceof qtype_multichoice_single_question) {
            return $page->get_renderer('qtype_multichoicewiris', 'single');
        } else {
            return $page->get_renderer('qtype_multichoicewiris', 'multi');
        }

    }
    public function get_response(question_attempt $qa) {
        return $this->base->get_response($qa);
    }
    public function get_order(question_attempt $qa) {
        return $this->base->get_order($qa);
    }
    public function is_choice_selected($response, $value) {
        return $this->base->is_choice_selected($response, $value);
    }
    public function make_html_inline($html) {
        return $this->base->make_html_inline($html);
    }
}
