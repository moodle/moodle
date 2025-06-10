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
require_once($CFG->dirroot . '/question/type/match/question.php');

class qtype_matchwiris_question extends qtype_wq_question implements question_automatically_gradable_with_countback {

    // References to moodle's question object.
    public $shufflestems;
    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;
    public $stems;
    public $stemformat;
    public $choices;
    public $right;

    public function join_all_text() {
        $text = parent::join_all_text();
        // Stems (matching left hand side).
        foreach ($this->stems as $key => $value) {
            $text .= ' ' . $value;
        }
        // Choices (matching right hand side).
        foreach ($this->choices as $key => $value) {
            $text .= ' ' . $value;
        }
        // Combined feedback.
        $text .= ' ' . $this->correctfeedback . ' ' . $this->partiallycorrectfeedback . ' ' . $this->incorrectfeedback;

        return $text;
    }

    public function get_stem_order() {
        return $this->base->get_stem_order();
    }
    public function get_choice_order() {
        return $this->base->get_choice_order();
    }
    public function get_right_choice_for($stemid) {
        return $this->base->get_right_choice_for($stemid);
    }

}
