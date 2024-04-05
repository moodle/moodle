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

namespace qtype_ordering\output;

/**
 * Create an array for the correct response based on the question and current step state.
 *
 * @package    qtype_ordering
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class correct_response extends renderable_base {
    public function export_for_template(\renderer_base $output): array {

        $data = [];
        $question = $this->qa->get_question();
        $correctresponse = $question->correctresponse;
        $data['hascorrectresponse'] = !empty($correctresponse);
        // Early return if a correct response does not exist.
        if (!$data['hascorrectresponse']) {
            return $data;
        }

        $step = $this->qa->get_last_step();
        // The correct response should be displayed only for partially correct or incorrect answers.
        $data['showcorrect'] = $step->get_state() == 'gradedpartial' || $step->get_state() == 'gradedwrong';
        // Early return if the correct response should not be displayed.
        if (!$data['showcorrect']) {
            return $data;
        }

        $data['orderinglayoutclass'] = $question->get_ordering_layoutclass();
        $data['correctanswers'] = [];

        foreach ($correctresponse as $answerid) {
            $answer = $question->answers[$answerid];
            $answertext = $question->format_text($answer->answer, $answer->answerformat,
                $this->qa, 'question', 'answer', $answerid);

            $data['correctanswers'][] = [
                'answertext' => $answertext,
            ];
        }

        return $data;
    }
}
