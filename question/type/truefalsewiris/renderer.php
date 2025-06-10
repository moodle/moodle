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
require_once($CFG->dirroot . '/question/type/wq/renderer.php');
require_once($CFG->dirroot . '/question/type/truefalse/renderer.php');

class qtype_truefalsewiris_renderer extends qtype_wq_renderer {
    public function __construct(moodle_page $page, $target) {
        parent::__construct(new qtype_truefalse_renderer($page, $target), $page, $target);
    }

    private function swap_feedbacks($question) {
        $aux = $question->truefeedback;
        $question->truefeedback = $question->falsefeedback;
        $question->falsefeedback = $aux;

        $aux = $question->truefeedbackformat;
        $question->truefeedbackformat = $question->falsefeedbackformat;
        $question->falsefeedbackformat = $aux;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        // Replace truefeedback by right feedback and false feedback by wrong feedback.
        if ($question->rightanswer === false) {
            $this->swap_feedbacks($question);
        }
        $result = parent::specific_feedback($qa);
        // Restore state.
        if ($question->rightanswer === false) {
            $this->swap_feedbacks($question);
        }
        return $result;
    }
}
