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

use question_attempt;
use question_display_options;

/**
 * Create the question formulation, controls ready for output.
 *
 * @package    qtype_ordering
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class formulation_and_controls extends renderable_base {

    /**
     * Construct the rendarable as we also need to pass the question options.
     *
     * @param question_attempt $qa The question attempt object.
     * @param question_display_options $options The question options.
     */
    public function __construct(
        question_attempt $qa,
        /** @var question_display_options The question display options. */
        protected question_display_options $options
    ) {
        parent::__construct($qa);
    }

    public function export_for_template(\renderer_base $output): array {
        global $PAGE;

        $data = [];
        $question = $this->qa->get_question();

        $response = $this->qa->get_last_qt_data();
        $question->update_current_response($response);

        $currentresponse = $question->currentresponse;
        $correctresponse = $question->correctresponse;

        // If we are running behat, force the question into a consistently known state for the sake of avoiding DnD funkyness.
        if (defined('BEHAT_SITE_RUNNING')) {
            $currentresponse = array_reverse($correctresponse);
        }

        // Generate fieldnames and ids.
        $responsefieldname = $question->get_response_fieldname();
        $responsename = $this->qa->get_qt_field_name($responsefieldname);
        $data['questiontext'] = $question->format_questiontext($this->qa);
        $data['ablockid'] = 'id_ablock_' . $question->id;
        $data['sortableid'] = 'id_sortable_' . $question->id;
        $data['responsename'] = $responsename;
        $data['responseid'] = 'id_' . preg_replace('/[^a-zA-Z0-9]+/', '_', $responsename);

        // Set CSS classes for sortable list.
        if ($class = $question->get_ordering_layoutclass()) {
            $data['layoutclass'] = $class;
        }
        if ($numberingstyle = $question->numberingstyle) {
            $data['numberingstyle'] = $numberingstyle;
        }

        $data['horizontallayout'] = $question->layouttype == \qtype_ordering_question::LAYOUT_HORIZONTAL;

        // In the multi-tries, the highlight response base on the hint highlight option.
        if (
            (isset($this->options->highlightresponse) && $this->options->highlightresponse) ||
            !$this->qa->get_state()->is_active()
        ) {
            $data['active'] = false;
        } else if ($this->qa->get_state()->is_active()) {
            $data['active'] = true;
        }

        $data['readonly'] = $this->options->readonly;

        if (count($currentresponse)) {

            // Initialize the cache for the  answers' md5keys
            // this represents the initial position of the items.
            $md5keys = [];

            // Generate ordering items.
            foreach ($currentresponse as $position => $answerid) {

                if (!array_key_exists($answerid, $question->answers) || !array_key_exists($position, $correctresponse)) {
                    // @codeCoverageIgnoreStart
                    continue; // This shouldn't happen.
                    // @codeCoverageIgnoreEnd
                }

                // Format the answer text.
                $answer = $question->answers[$answerid];
                $answertext = $question->format_text($answer->answer, $answer->answerformat,
                    $this->qa, 'question', 'answer', $answerid);

                // The original "id" revealed the correct order of the answers
                // because $answer->fraction holds the correct order number.
                // Therefore, we use the $answer's md5key for the "id".
                $answerdata = [
                    'answertext' => $answertext,
                    'id' => $answer->md5key,
                ];

                if ($this->options->correctness === question_display_options::VISIBLE ||
                        !empty($this->options->highlightresponse)) {
                    $score = $question->get_ordering_item_score($question, $position, $answerid);
                    if (isset($score['maxscore'])) {
                        $renderer = $PAGE->get_renderer('qtype_ordering');
                        $answerdata['feedbackimage'] = $renderer->feedback_image($score['fraction']);
                    }
                    $answerdata['scoreclass'] = $score['class'];
                }

                $data['answers'][] = $answerdata;

                // Cache this answer key.
                $md5keys[] = $answer->md5key;
            }
        }

        $data['value'] = implode(',', $md5keys);

        return $data;
    }
}
