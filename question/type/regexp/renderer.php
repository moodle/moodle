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
 * Regexp question renderer class.
 *
 * @package    qtype_regexp
 * @copyright  2011 Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generates the output for regexp questions.
 *
 * @copyright  2011 Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexp_renderer extends qtype_renderer {

    /**
     * Generate the display of the formulation part of the question shown at runtime
     * in a quiz.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $CFG, $currentanswerwithhint;
        require_once($CFG->dirroot.'/question/type/regexp/locallib.php');
        $question = $qa->get_question();
        $inputname = $qa->get_qt_field_name('answer');
        $ispreview = !isset($options->attempt);
        $currentanswer = remove_blanks ($qa->get_last_qt_var('answer') );
        $response = $qa->get_last_qt_data();
        $laststep = $qa->get_reverse_step_iterator();
        $hintadded = false;
        foreach ($qa->get_reverse_step_iterator() as $step) {
            $hintadded = $step->has_behaviour_var('_helps') === true;
                break;
        }
        $closest = find_closest($question, $currentanswer, $correctresponse = false, $hintadded);
        $question->closest = $closest;
        // If regexpadaptive behaviours replace current student response with correct beginning.
        $currbehaviourname = get_class($qa->get_behaviour() );
        $currstate = $qa->get_state();
        // Showing / hiding regexp generated alternative sentences (for teacher only).
        // Changed from javascript to print_collapsible_region OCT 2012.
        // Removed for compatibility with the Embed questions plugin see https://moodle.org/plugins/filter_embedquestion.

        $inputattributes = [
            'type' => 'text',
            'name' => $inputname,
            'value' => $currentanswer,
            'id' => $inputname,
            'size' => 80,
            'class' => 'form-control d-inline',
        ];

        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
        }

        $feedbackimg = '';
        if ($options->correctness) {
            $answer = $question->get_matching_answer(['answer' => $currentanswer]);
            if ($answer) {
                $fraction = $answer->fraction;
            } else {
                $fraction = 0;
            }
            $inputattributes['class'] .= ' ' . $this->feedback_class($fraction);
            $feedbackimg = $this->feedback_image($fraction);
        }
        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
            $inputattributes['size'] = round(strlen($placeholder) * 1.1);

            // Added for correct display of input inside question text in the mobile version.
            $inputattributes['class'] .= ' ' . 'inlineinput';
        }

        $input = html_writer::empty_tag('input', $inputattributes) . $feedbackimg;

        if ($placeholder) {
            $questiontext = substr_replace($questiontext, $input,
                    strpos($questiontext, $placeholder), strlen($placeholder));
        }

        $result = html_writer::tag('div', $questiontext, ['class' => 'qtext']);

        if (!$placeholder) {
            $result .= html_writer::start_tag('div', ['class' => 'ablock']);
            $result .= get_string('answer', 'qtype_shortanswer',
                    html_writer::tag('div', $input, ['class' => 'answer']));
            $result .= html_writer::end_tag('div');
        }

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(['answer' => $currentanswer]),
                    ['class' => 'validationerror']);
        }

        return $result;
    }

    /**
     * Get feedback.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function feedback(question_attempt $qa, question_display_options $options) {
        $result = '';
        $hint = null;
        if ($options->feedback) {
            $result .= html_writer::nonempty_tag('div', $this->specific_feedback($qa),
                    ['class' => 'specificfeedback qtype-regexp']);
            $hint = $qa->get_applicable_hint();
        }

        if ($options->numpartscorrect) {
            $result .= html_writer::nonempty_tag('div', $this->num_parts_correct($qa),
                    ['class' => 'numpartscorrect']);
        }

        if ($hint) {
            $result .= $this->hint($qa, $hint);
        }

        if ($options->generalfeedback) {
            $result .= html_writer::nonempty_tag('div', $this->general_feedback($qa),
                    ['class' => 'generalfeedback']);
        }

        if ($options->rightanswer) {
            $displaycorrectanswers = $this->correct_response($qa);
            $result .= html_writer::nonempty_tag('div', $displaycorrectanswers,
                    ['class' => 'rightanswer']);
        }

        return $result;
    }

    /**
     * Get feedback/hint information
     *
     * @param question_attempt $qa
     * @return string
     */
    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        $currentanswer = remove_blanks($qa->get_last_qt_var('answer') );
        $ispreview = false;
        $completemessage = '';
        $closestcomplete = false;
        foreach ($qa->get_reverse_step_iterator() as $step) {
            $hintadded = $step->has_behaviour_var('_helps') === true;
            break;
        }
        $closest = $question->closest;
        if ($hintadded) { // Hint added one letter or hint added letter and answer is complete.
            $answer = $question->get_matching_answer(['answer' => $closest[0]]);
            // Help has added letter OR word and answer is complete.
            $isstateimprovable = $qa->get_behaviour()->is_state_improvable($qa->get_state());
            if ($closest[2] == 'complete' && $isstateimprovable) {
                $closestcomplete = true;
                $class = '"validationerror"';
                $completemessage = '<div class='.$class.'>'.get_string("clicktosubmit", "qtype_regexp").'</div>';

            }
        } else {
            $answer = $question->get_matching_answer(['answer' => $qa->get_last_qt_var('answer')]);
        }

        $labelerrors = '';
        $f = '';
        if (!empty($closest)) {
            $guesserrors = $closest[5];
            if ($guesserrors) {
                $labelwrongwords = '<span class="labelwrongword">'.get_string("wrongwords", "qtype_regexp").'</span>';
                $labelmisplacedwords = '<span class="labelmisplacedword">'.get_string("misplacedwords", "qtype_regexp").'</span>';
                switch ($guesserrors) {
                    case 1 :
                        $labelerrors = '<div>'.$labelmisplacedwords.'</div>';
                        break;
                    case 10 :
                        $labelerrors = '<div>'.$labelwrongwords.'</div>';
                        break;
                    case 11 :
                        $labelerrors = '<div>'.$labelwrongwords. ' '. $labelmisplacedwords.'</div>';
                        break;
                }
            }
            // Student's response with corrections to be displayed in feedback div.
            $f = '<div><span class="correctword">'.$closest[1].'<strong>'.$closest[4].'</strong></span> '.$closest[3].'</div>';
        }

        if ($closest[2] == 'complete') {
            $answer->feedback = '';
        }
        if ($answer && $answer->feedback || $closestcomplete == true) {
            return $question->format_text($f.$labelerrors.$answer->feedback.$completemessage,
                $answer->feedbackformat, $qa, 'question', 'answerfeedback', $answer->id);
        } else {
            return $f.$labelerrors;
        }
    }

    /**
     * Get correct response
     *
     * @param question_attempt $qa
     * @return string
     */
    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $displayresponses = '';
        $alternateanswers = get_alternateanswers($question);
        $bestcorrectanswer = $alternateanswers[1]['answers'][0];

        if (count($alternateanswers) == 1 ) { // No alternative answers besides the only "correct" answer.
            $displayresponses .= get_string('correctansweris', 'qtype_regexp', $bestcorrectanswer);
            // No need to display alternate answers!
            return $displayresponses;
        } else {
            $displayresponses .= get_string('bestcorrectansweris', 'qtype_regexp', $bestcorrectanswer).'<br />';
        }
        // Teacher can always view alternate answers; student can only view if question is set to studentshowalternate.
        $canview = question_has_capability_on($question, 'view');
        if ($question->studentshowalternate || $canview) {
            $displayresponses .= print_collapsible_region_start('expandalternateanswers', 'id'.
                            $question->id, get_string('showhidealternate', 'qtype_regexp'),
                            'showhidealternate', true, true);
            foreach ($alternateanswers as $key => $alternateanswer) {
                if ($key == 1) { // First (correct) Answer.
                    if (count($alternateanswers) > 1) {
                        $displayresponses .= get_string('correctanswersare', 'qtype_regexp').'<br />';
                    }
                } else {
                    $fraction = $alternateanswer['fraction'];
                    $displayresponses .= "<strong>$fraction</strong><br />";
                    foreach ($alternateanswer['answers'] as $alternate) {
                        $displayresponses .= $alternate.'<br />';
                    }
                }
            }
            $displayresponses .= print_collapsible_region_end(true);
        }
        return $displayresponses;
    }
}
