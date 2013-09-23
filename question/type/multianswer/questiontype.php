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
 * Question type class for the multi-answer question type.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/multichoice/question.php');


/**
 * The multi-answer question type class.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer extends question_type {

    public function can_analyse_responses() {
        return false;
    }

    public function get_question_options($question) {
        global $DB, $OUTPUT;

        // Get relevant data indexed by positionkey from the multianswers table.
        $sequence = $DB->get_field('question_multianswer', 'sequence',
                array('question' => $question->id), '*', MUST_EXIST);

        $wrappedquestions = $DB->get_records_list('question', 'id',
                explode(',', $sequence), 'id ASC');

        // We want an array with question ids as index and the positions as values.
        $sequence = array_flip(explode(',', $sequence));
        array_walk($sequence, create_function('&$val', '$val++;'));

        // If a question is lost, the corresponding index is null
        // so this null convention is used to test $question->options->questions
        // before using the values.
        // First all possible questions from sequence are nulled
        // then filled with the data if available in  $wrappedquestions.
        foreach ($sequence as $seq) {
            $question->options->questions[$seq] = '';
        }

        foreach ($wrappedquestions as $wrapped) {
            question_bank::get_qtype($wrapped->qtype)->get_question_options($wrapped);
            // For wrapped questions the maxgrade is always equal to the defaultmark,
            // there is no entry in the question_instances table for them.
            $wrapped->maxmark = $wrapped->defaultmark;
            $question->options->questions[$sequence[$wrapped->id]] = $wrapped;
        }

        $question->hints = $DB->get_records('question_hints',
                array('questionid' => $question->id), 'id ASC');

        return true;
    }

    public function save_question_options($question) {
        global $DB;
        $result = new stdClass();

        // This function needs to be able to handle the case where the existing set of wrapped
        // questions does not match the new set of wrapped questions so that some need to be
        // created, some modified and some deleted.
        // Unfortunately the code currently simply overwrites existing ones in sequence. This
        // will make re-marking after a re-ordering of wrapped questions impossible and
        // will also create difficulties if questiontype specific tables reference the id.

        // First we get all the existing wrapped questions.
        if (!$oldwrappedids = $DB->get_field('question_multianswer', 'sequence',
                array('question' => $question->id))) {
            $oldwrappedquestions = array();
        } else {
            $oldwrappedquestions = $DB->get_records_list('question', 'id',
                    explode(',', $oldwrappedids), 'id ASC');
        }

        $sequence = array();
        foreach ($question->options->questions as $wrapped) {
            if (!empty($wrapped)) {
                // If we still have some old wrapped question ids, reuse the next of them.

                if (is_array($oldwrappedquestions) &&
                        $oldwrappedquestion = array_shift($oldwrappedquestions)) {
                    $wrapped->id = $oldwrappedquestion->id;
                    if ($oldwrappedquestion->qtype != $wrapped->qtype) {
                        switch ($oldwrappedquestion->qtype) {
                            case 'multichoice':
                                $DB->delete_records('qtype_multichoice_options',
                                        array('questionid' => $oldwrappedquestion->id));
                                break;
                            case 'shortanswer':
                                $DB->delete_records('qtype_shortanswer_options',
                                        array('questionid' => $oldwrappedquestion->id));
                                break;
                            case 'numerical':
                                $DB->delete_records('question_numerical',
                                        array('question' => $oldwrappedquestion->id));
                                break;
                            default:
                                throw new moodle_exception('qtypenotrecognized',
                                        'qtype_multianswer', '', $oldwrappedquestion->qtype);
                                $wrapped->id = 0;
                        }
                    }
                } else {
                    $wrapped->id = 0;
                }
            }
            $wrapped->name = $question->name;
            $wrapped->parent = $question->id;
            $previousid = $wrapped->id;
            // Save_question strips this extra bit off the category again.
            $wrapped->category = $question->category . ',1';
            $wrapped = question_bank::get_qtype($wrapped->qtype)->save_question(
                    $wrapped, clone($wrapped));
            $sequence[] = $wrapped->id;
            if ($previousid != 0 && $previousid != $wrapped->id) {
                // For some reasons a new question has been created
                // so delete the old one.
                question_delete_question($previousid);
            }
        }

        // Delete redundant wrapped questions.
        if (is_array($oldwrappedquestions) && count($oldwrappedquestions)) {
            foreach ($oldwrappedquestions as $oldwrappedquestion) {
                question_delete_question($oldwrappedquestion->id);
            }
        }

        if (!empty($sequence)) {
            $multianswer = new stdClass();
            $multianswer->question = $question->id;
            $multianswer->sequence = implode(',', $sequence);
            if ($oldid = $DB->get_field('question_multianswer', 'id',
                    array('question' => $question->id))) {
                $multianswer->id = $oldid;
                $DB->update_record('question_multianswer', $multianswer);
            } else {
                $DB->insert_record('question_multianswer', $multianswer);
            }
        }

        $this->save_hints($question, true);
    }

    public function save_question($authorizedquestion, $form) {
        $question = qtype_multianswer_extract_question($form->questiontext);
        if (isset($authorizedquestion->id)) {
            $question->id = $authorizedquestion->id;
        }

        $question->category = $authorizedquestion->category;
        $form->defaultmark = $question->defaultmark;
        $form->questiontext = $question->questiontext;
        $form->questiontextformat = 0;
        $form->options = clone($question->options);
        unset($question->options);
        return parent::save_question($question, $form);
    }

    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_multianswer', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);

        $bits = preg_split('/\{#(\d+)\}/', $question->questiontext,
                null, PREG_SPLIT_DELIM_CAPTURE);
        $question->textfragments[0] = array_shift($bits);
        $i = 1;
        while (!empty($bits)) {
            $question->places[$i] = array_shift($bits);
            $question->textfragments[$i] = array_shift($bits);
            $i += 1;
        }

        foreach ($questiondata->options->questions as $key => $subqdata) {
            $subqdata->contextid = $questiondata->contextid;
            $question->subquestions[$key] = question_bank::make_question($subqdata);
            $question->subquestions[$key]->maxmark = $subqdata->defaultmark;
            if (isset($subqdata->options->layout)) {
                $question->subquestions[$key]->layout = $subqdata->options->layout;
            }
        }
    }

    public function get_random_guess_score($questiondata) {
        $fractionsum = 0;
        $fractionmax = 0;
        foreach ($questiondata->options->questions as $key => $subqdata) {
            $fractionmax += $subqdata->defaultmark;
            $fractionsum += question_bank::get_qtype(
                    $subqdata->qtype)->get_random_guess_score($subqdata);
        }
        return $fractionsum / $fractionmax;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
}


// ANSWER_ALTERNATIVE regexes.
define('ANSWER_ALTERNATIVE_FRACTION_REGEX',
       '=|%(-?[0-9]+)%');
// For the syntax '(?<!' see http://www.perl.com/doc/manual/html/pod/perlre.html#item_C.
define('ANSWER_ALTERNATIVE_ANSWER_REGEX',
        '.+?(?<!\\\\|&|&amp;)(?=[~#}]|$)');
define('ANSWER_ALTERNATIVE_FEEDBACK_REGEX',
        '.*?(?<!\\\\)(?=[~}]|$)');
define('ANSWER_ALTERNATIVE_REGEX',
       '(' . ANSWER_ALTERNATIVE_FRACTION_REGEX .')?' .
       '(' . ANSWER_ALTERNATIVE_ANSWER_REGEX . ')' .
       '(#(' . ANSWER_ALTERNATIVE_FEEDBACK_REGEX .'))?');

// Parenthesis positions for ANSWER_ALTERNATIVE_REGEX.
define('ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION', 2);
define('ANSWER_ALTERNATIVE_REGEX_FRACTION', 1);
define('ANSWER_ALTERNATIVE_REGEX_ANSWER', 3);
define('ANSWER_ALTERNATIVE_REGEX_FEEDBACK', 5);

// NUMBER_FORMATED_ALTERNATIVE_ANSWER_REGEX is used
// for identifying numerical answers in ANSWER_ALTERNATIVE_REGEX_ANSWER.
define('NUMBER_REGEX',
        '-?(([0-9]+[.,]?[0-9]*|[.,][0-9]+)([eE][-+]?[0-9]+)?)');
define('NUMERICAL_ALTERNATIVE_REGEX',
        '^(' . NUMBER_REGEX . ')(:' . NUMBER_REGEX . ')?$');

// Parenthesis positions for NUMERICAL_FORMATED_ALTERNATIVE_ANSWER_REGEX.
define('NUMERICAL_CORRECT_ANSWER', 1);
define('NUMERICAL_ABS_ERROR_MARGIN', 6);

// Remaining ANSWER regexes.
define('ANSWER_TYPE_DEF_REGEX',
        '(NUMERICAL|NM)|(MULTICHOICE|MC)|(MULTICHOICE_V|MCV)|(MULTICHOICE_H|MCH)|' .
                '(SHORTANSWER|SA|MW)|(SHORTANSWER_C|SAC|MWC)');
define('ANSWER_START_REGEX',
       '\{([0-9]*):(' . ANSWER_TYPE_DEF_REGEX . '):');

define('ANSWER_REGEX',
        ANSWER_START_REGEX
        . '(' . ANSWER_ALTERNATIVE_REGEX
        . '(~'
        . ANSWER_ALTERNATIVE_REGEX
        . ')*)\}');

// Parenthesis positions for singulars in ANSWER_REGEX.
define('ANSWER_REGEX_NORM', 1);
define('ANSWER_REGEX_ANSWER_TYPE_NUMERICAL', 3);
define('ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE', 4);
define('ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR', 5);
define('ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL', 6);
define('ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER', 7);
define('ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER_C', 8);
define('ANSWER_REGEX_ALTERNATIVES', 9);

function qtype_multianswer_extract_question($text) {
    // Variable $text is an array [text][format][itemid].
    $question = new stdClass();
    $question->qtype = 'multianswer';
    $question->questiontext = $text;
    $question->generalfeedback['text'] = '';
    $question->generalfeedback['format'] = FORMAT_HTML;
    $question->generalfeedback['itemid'] = '';

    $question->options = new stdClass();
    $question->options->questions = array();
    $question->defaultmark = 0; // Will be increased for each answer norm.

    for ($positionkey = 1;
            preg_match('/'.ANSWER_REGEX.'/s', $question->questiontext['text'], $answerregs);
            ++$positionkey) {
        $wrapped = new stdClass();
        $wrapped->generalfeedback['text'] = '';
        $wrapped->generalfeedback['format'] = FORMAT_HTML;
        $wrapped->generalfeedback['itemid'] = '';
        if (isset($answerregs[ANSWER_REGEX_NORM])&& $answerregs[ANSWER_REGEX_NORM]!== '') {
            $wrapped->defaultmark = $answerregs[ANSWER_REGEX_NORM];
        } else {
            $wrapped->defaultmark = '1';
        }
        if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])) {
            $wrapped->qtype = 'numerical';
            $wrapped->multiplier = array();
            $wrapped->units      = array();
            $wrapped->instructions['text'] = '';
            $wrapped->instructions['format'] = FORMAT_HTML;
            $wrapped->instructions['itemid'] = '';
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER])) {
            $wrapped->qtype = 'shortanswer';
            $wrapped->usecase = 0;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER_C])) {
            $wrapped->qtype = 'shortanswer';
            $wrapped->usecase = 1;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->shuffleanswers = 1;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback['text'] = '';
            $wrapped->correctfeedback['format'] = FORMAT_HTML;
            $wrapped->correctfeedback['itemid'] = '';
            $wrapped->partiallycorrectfeedback['text'] = '';
            $wrapped->partiallycorrectfeedback['format'] = FORMAT_HTML;
            $wrapped->partiallycorrectfeedback['itemid'] = '';
            $wrapped->incorrectfeedback['text'] = '';
            $wrapped->incorrectfeedback['format'] = FORMAT_HTML;
            $wrapped->incorrectfeedback['itemid'] = '';
            $wrapped->layout = qtype_multichoice_base::LAYOUT_DROPDOWN;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->shuffleanswers = 0;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback['text'] = '';
            $wrapped->correctfeedback['format'] = FORMAT_HTML;
            $wrapped->correctfeedback['itemid'] = '';
            $wrapped->partiallycorrectfeedback['text'] = '';
            $wrapped->partiallycorrectfeedback['format'] = FORMAT_HTML;
            $wrapped->partiallycorrectfeedback['itemid'] = '';
            $wrapped->incorrectfeedback['text'] = '';
            $wrapped->incorrectfeedback['format'] = FORMAT_HTML;
            $wrapped->incorrectfeedback['itemid'] = '';
            $wrapped->layout = qtype_multichoice_base::LAYOUT_VERTICAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->shuffleanswers = 0;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback['text'] = '';
            $wrapped->correctfeedback['format'] = FORMAT_HTML;
            $wrapped->correctfeedback['itemid'] = '';
            $wrapped->partiallycorrectfeedback['text'] = '';
            $wrapped->partiallycorrectfeedback['format'] = FORMAT_HTML;
            $wrapped->partiallycorrectfeedback['itemid'] = '';
            $wrapped->incorrectfeedback['text'] = '';
            $wrapped->incorrectfeedback['format'] = FORMAT_HTML;
            $wrapped->incorrectfeedback['itemid'] = '';
            $wrapped->layout = qtype_multichoice_base::LAYOUT_HORIZONTAL;
        } else {
            print_error('unknownquestiontype', 'question', '', $answerregs[2]);
            return false;
        }

        // Each $wrapped simulates a $form that can be processed by the
        // respective save_question and save_question_options methods of the
        // wrapped questiontypes.
        $wrapped->answer   = array();
        $wrapped->fraction = array();
        $wrapped->feedback = array();
        $wrapped->questiontext['text'] = $answerregs[0];
        $wrapped->questiontext['format'] = FORMAT_HTML;
        $wrapped->questiontext['itemid'] = '';
        $answerindex = 0;

        $remainingalts = $answerregs[ANSWER_REGEX_ALTERNATIVES];
        while (preg_match('/~?'.ANSWER_ALTERNATIVE_REGEX.'/s', $remainingalts, $altregs)) {
            if ('=' == $altregs[ANSWER_ALTERNATIVE_REGEX_FRACTION]) {
                $wrapped->fraction["$answerindex"] = '1';
            } else if ($percentile = $altregs[ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION]) {
                $wrapped->fraction["$answerindex"] = .01 * $percentile;
            } else {
                $wrapped->fraction["$answerindex"] = '0';
            }
            if (isset($altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK])) {
                $feedback = html_entity_decode(
                        $altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK], ENT_QUOTES, 'UTF-8');
                $feedback = str_replace('\}', '}', $feedback);
                $wrapped->feedback["$answerindex"]['text'] = str_replace('\#', '#', $feedback);
                $wrapped->feedback["$answerindex"]['format'] = FORMAT_HTML;
                $wrapped->feedback["$answerindex"]['itemid'] = '';
            } else {
                $wrapped->feedback["$answerindex"]['text'] = '';
                $wrapped->feedback["$answerindex"]['format'] = FORMAT_HTML;
                $wrapped->feedback["$answerindex"]['itemid'] = '';

            }
            if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])
                    && preg_match('~'.NUMERICAL_ALTERNATIVE_REGEX.'~s',
                            $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], $numregs)) {
                $wrapped->answer[] = $numregs[NUMERICAL_CORRECT_ANSWER];
                if (array_key_exists(NUMERICAL_ABS_ERROR_MARGIN, $numregs)) {
                    $wrapped->tolerance["$answerindex"] =
                    $numregs[NUMERICAL_ABS_ERROR_MARGIN];
                } else {
                    $wrapped->tolerance["$answerindex"] = 0;
                }
            } else { // Tolerance can stay undefined for non numerical questions.
                // Undo quoting done by the HTML editor.
                $answer = html_entity_decode(
                        $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], ENT_QUOTES, 'UTF-8');
                $answer = str_replace('\}', '}', $answer);
                $wrapped->answer["$answerindex"] = str_replace('\#', '#', $answer);
                if ($wrapped->qtype == 'multichoice') {
                    $wrapped->answer["$answerindex"] = array(
                            'text' => $wrapped->answer["$answerindex"],
                            'format' => FORMAT_HTML,
                            'itemid' => '');
                }
            }
            $tmp = explode($altregs[0], $remainingalts, 2);
            $remainingalts = $tmp[1];
            $answerindex++;
        }

        $question->defaultmark += $wrapped->defaultmark;
        $question->options->questions[$positionkey] = clone($wrapped);
        $question->questiontext['text'] = implode("{#$positionkey}",
                    explode($answerregs[0], $question->questiontext['text'], 2));
    }
    return $question;
}
