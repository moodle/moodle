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
 * Multianswer question definition class.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2010 Pierre Pichet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/type/shortanswer/question.php');
require_once($CFG->dirroot . '/question/type/numerical/question.php');
require_once($CFG->dirroot . '/question/type/multichoice/question.php');


/**
 * Represents a multianswer question.
 *
 * A multi-answer question is made of of several subquestions of various types.
 * You can think of it as an application of the composite pattern to qusetion
 * types.
 *
 * @copyright  2010 Pierre Pichet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_question extends question_graded_automatically {
    /** @var array of question_graded_automatically. */
    public $subquestions = array();

    /**
     * @var array place number => insex in the $subquestions array. Places are
     * numbered from 1.
     */
    public $places;

    /**
     * @var array of strings, one longer than $places, which is achieved by
     * indexing from 0. The bits of question text that go between the subquestions.
     */
    public $textfragments;

    /**
     * Get a question_attempt_step_subquestion_adapter
     * @param question_attempt_step $step the step to adapt.
     * @param int $i the subquestion index.
     * @return question_attempt_step_subquestion_adapter.
     */
    protected function get_substep($step, $i) {
        return new question_attempt_step_subquestion_adapter($step, 'sub' . $i . '_');
    }

    public function start_attempt(question_attempt_step $step, $variant) {
        foreach ($this->subquestions as $i => $subq) {
            $subq->start_attempt($this->get_substep($step, $i), $variant);
        }
    }

    public function apply_attempt_state(question_attempt_step $step) {
        foreach ($this->subquestions as $i => $subq) {
            $subq->apply_attempt_state($this->get_substep($step, $i));
        }
    }

    public function get_question_summary() {
        $summary = $this->html_to_text($this->questiontext, $this->questiontextformat);
        foreach ($this->subquestions as $i => $subq) {
            switch ($subq->qtype->name()) {
                case 'multichoice':
                    $choices = array();
                    $dummyqa = new question_attempt($subq, $this->contextid);
                    foreach ($subq->get_order($dummyqa) as $ansid) {
                        $choices[] = $this->html_to_text($subq->answers[$ansid]->answer,
                                $subq->answers[$ansid]->answerformat);
                    }
                    $answerbit = '{' . implode('; ', $choices) . '}';
                    break;
                case 'numerical':
                case 'shortanswer':
                    $answerbit = '_____';
                    break;
                default:
                    $answerbit = '{ERR unknown sub-question type}';
            }
            $summary = str_replace('{#' . $i . '}', $answerbit, $summary);
        }
        return $summary;
    }

    public function get_min_fraction() {
        $fractionsum = 0;
        $fractionmax = 0;
        foreach ($this->subquestions as $i => $subq) {
            $fractionmax += $subq->defaultmark;
            $fractionsum += $subq->defaultmark * $subq->get_min_fraction();
        }
        return $fractionsum / $fractionmax;
    }

    public function get_expected_data() {
        $expected = array();
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            foreach ($subq->get_expected_data() as $name => $type) {
                if ($subq->qtype->name() == 'multichoice' &&
                        $subq->layout == qtype_multichoice_base::LAYOUT_DROPDOWN) {
                    // Hack or MC inline does not work.
                    $expected[$substep->add_prefix($name)] = PARAM_RAW;
                } else {
                    $expected[$substep->add_prefix($name)] = $type;
                }
            }
        }
        return $expected;
    }

    public function get_correct_response() {
        $right = array();
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            foreach ($subq->get_correct_response() as $name => $type) {
                $right[$substep->add_prefix($name)] = $type;
            }
        }
        return $right;
    }

    public function is_complete_response(array $response) {
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            if (!$subq->is_complete_response($substep->filter_array($response))) {
                return false;
            }
        }
        return true;
    }

    public function is_gradable_response(array $response) {
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            if ($subq->is_gradable_response($substep->filter_array($response))) {
                return true;
            }
        }
        return false;
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            if (!$subq->is_same_response($substep->filter_array($prevresponse),
                    $substep->filter_array($newresponse))) {
                return false;
            }
        }
        return true;
    }

    public function get_validation_error(array $response) {
        $errors = array();
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            $errors[] = $subq->get_validation_error($substep->filter_array($response));
        }
        return implode('<br />', $errors);
    }

    /**
     * Used by grade_response to combine the states of the subquestions.
     * The combined state is accumulates in $overallstate. That will be right
     * if all the separate states are right; and wrong if all the separate states
     * are wrong, otherwise, it will be partially right.
     * @param question_state $overallstate the result so far.
     * @param question_state $newstate the new state to add to the combination.
     * @return question_state the new combined state.
     */
    protected function combine_states($overallstate, $newstate) {
        if (is_null($overallstate)) {
            return $newstate;
        } else if ($overallstate == question_state::$gaveup &&
                $newstate == question_state::$gaveup) {
            return question_state::$gaveup;
        } else if ($overallstate == question_state::$gaveup &&
                $newstate == question_state::$gradedwrong) {
            return question_state::$gradedwrong;
        } else if ($overallstate == question_state::$gradedwrong &&
                $newstate == question_state::$gaveup) {
            return question_state::$gradedwrong;
        } else if ($overallstate == question_state::$gradedwrong &&
                $newstate == question_state::$gradedwrong) {
            return question_state::$gradedwrong;
        } else if ($overallstate == question_state::$gradedright &&
                $newstate == question_state::$gradedright) {
            return question_state::$gradedright;
        } else {
            return question_state::$gradedpartial;
        }
    }

    public function grade_response(array $response) {
        $overallstate = null;
        $fractionsum = 0;
        $fractionmax = 0;
        foreach ($this->subquestions as $i => $subq) {
            $fractionmax += $subq->defaultmark;
            $substep = $this->get_substep(null, $i);
            $subresp = $substep->filter_array($response);
            if (!$subq->is_gradable_response($subresp)) {
                $overallstate = $this->combine_states($overallstate, question_state::$gaveup);
            } else {
                list($subfraction, $newstate) = $subq->grade_response($subresp);
                $fractionsum += $subfraction * $subq->defaultmark;
                $overallstate = $this->combine_states($overallstate, $newstate);
            }
        }
        return array($fractionsum / $fractionmax, $overallstate);
    }

    public function clear_wrong_from_response(array $response) {
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            $subresp = $substep->filter_array($response);
            list($subfraction, $newstate) = $subq->grade_response($subresp);
            if ($newstate != question_state::$gradedright) {
                foreach ($subresp as $ind => $resp) {
                    if ($subq->qtype == 'multichoice' && ($subq->layout == qtype_multichoice_base::LAYOUT_VERTICAL
                            || $subq->layout == qtype_multichoice_base::LAYOUT_HORIZONTAL)) {
                        $response[$substep->add_prefix($ind)] = '-1';
                    } else {
                        $response[$substep->add_prefix($ind)] = '';
                    }
                }
            }
        }
        return $response;
    }

    public function get_num_parts_right(array $response) {
        $numright = 0;
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            $subresp = $substep->filter_array($response);
            list($subfraction, $newstate) = $subq->grade_response($subresp);
            if ($newstate == question_state::$gradedright) {
                $numright += 1;
            }
        }
        return array($numright, count($this->subquestions));
    }

    public function summarise_response(array $response) {
        $summary = array();
        foreach ($this->subquestions as $i => $subq) {
            $substep = $this->get_substep(null, $i);
            $a = new stdClass();
            $a->i = $i;
            $a->response = $subq->summarise_response($substep->filter_array($response));
            $summary[] = get_string('subqresponse', 'qtype_multianswer', $a);
        }

        return implode('; ', $summary);
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'answer') {
            return true;

        } else if ($component == 'question' && $filearea == 'answerfeedback') {
            // Full logic to control which feedbacks a student can see is too complex.
            // Just allow access to all images. There is a theoretical chance the
            // students could see files they are not meant to see by guessing URLs,
            // but it is remote.
            return $options->feedback;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }
}
