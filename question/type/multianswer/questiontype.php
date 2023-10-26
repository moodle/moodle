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

require_once($CFG->dirroot . '/question/type/questiontypebase.php');
require_once($CFG->dirroot . '/question/type/multichoice/question.php');
require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');

/**
 * The multi-answer question type class.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer extends question_type {

    /**
     * Generate a subquestion replacement question class.
     *
     * Due to a bug, subquestions can be lost (see MDL-54724). This class exists to take
     * the place of those lost questions so that the system can keep working and inform
     * the user of the corrupted data.
     *
     * @return question_automatically_gradable The replacement question class.
     */
    public static function deleted_subquestion_replacement(): question_automatically_gradable {
        return new class implements question_automatically_gradable {
            public $qtype;

            public function __construct() {
                $this->qtype = new class() {
                    public function name() {
                        return 'subquestion_replacement';
                    }
                };
            }

            public function is_gradable_response(array $response) {
                return false;
            }

            public function is_complete_response(array $response) {
                return false;
            }

            public function is_same_response(array $prevresponse, array $newresponse) {
                return false;
            }

            public function summarise_response(array $response) {
                return '';
            }

            public function un_summarise_response(string $summary) {
                return [];
            }

            public function classify_response(array $response) {
                return [];
            }

            public function get_validation_error(array $response) {
                return '';
            }

            public function grade_response(array $response) {
                return [];
            }

            public function get_hint($hintnumber, question_attempt $qa) {
                return;
            }

            public function get_right_answer_summary() {
                return null;
            }
        };
    }

    public function can_analyse_responses() {
        return false;
    }

    public function get_question_options($question) {
        global $DB;

        parent::get_question_options($question);
        // Get relevant data indexed by positionkey from the multianswers table.
        $sequence = $DB->get_field('question_multianswer', 'sequence',
                array('question' => $question->id), MUST_EXIST);

        if (empty($sequence)) {
            $question->options->questions = [];
            return true;
        }

        $wrappedquestions = $DB->get_records_list('question', 'id',
                explode(',', $sequence), 'id ASC');

        // We want an array with question ids as index and the positions as values.
        $sequence = array_flip(explode(',', $sequence));
        array_walk($sequence, function(&$val) {
            $val++;
        });

        // Due to a bug, questions can be lost (see MDL-54724). So we first fill the question
        // options with this dummy "replacement" type. These are overridden in the loop below
        // leaving behind only those questions which no longer exist. The renderer then looks
        // for this deleted type to display information to the user about the corrupted question
        // data.
        foreach ($sequence as $seq) {
            $question->options->questions[$seq] = (object)[
                'qtype' => 'subquestion_replacement',
                'defaultmark' => 1,
                'options' => (object)[
                    'answers' => []
                ]
            ];
        }

        foreach ($wrappedquestions as $wrapped) {
            question_bank::get_qtype($wrapped->qtype)->get_question_options($wrapped);
            // For wrapped questions the maxgrade is always equal to the defaultmark,
            // there is no entry in the question_instances table for them.
            $wrapped->maxmark = $wrapped->defaultmark;
            $wrapped->category = $question->categoryobject->id;
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
        $oldwrappedquestions = [];
        if (isset($question->oldparent)) {
            if ($oldwrappedids = $DB->get_field('question_multianswer', 'sequence',
                ['question' => $question->oldparent])) {
                $oldwrappedidsarray = explode(',', $oldwrappedids);
                $unorderedquestions = $DB->get_records_list('question', 'id', $oldwrappedidsarray);

                // Keep the order as given in the sequence field.
                foreach ($oldwrappedidsarray as $questionid) {
                    if (isset($unorderedquestions[$questionid])) {
                        $oldwrappedquestions[] = $unorderedquestions[$questionid];
                    }
                }
            }
        }

        $sequence = array();
        foreach ($question->options->questions as $wrapped) {
            if (!empty($wrapped)) {
                // If we still have some old wrapped question ids, reuse the next of them.
                $wrapped->id = 0;
                if (is_array($oldwrappedquestions) &&
                        $oldwrappedquestion = array_shift($oldwrappedquestions)) {
                    $wrapped->oldid = $oldwrappedquestion->id;
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
                        }
                    }
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

        $question->category = $form->category;
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
                -1, PREG_SPLIT_DELIM_CAPTURE);
        $question->textfragments[0] = array_shift($bits);
        $i = 1;
        while (!empty($bits)) {
            $question->places[$i] = array_shift($bits);
            $question->textfragments[$i] = array_shift($bits);
            $i += 1;
        }
        foreach ($questiondata->options->questions as $key => $subqdata) {
            if ($subqdata->qtype == 'subquestion_replacement') {
                continue;
            }

            $subqdata->contextid = $questiondata->contextid;
            if ($subqdata->qtype == 'multichoice') {
                $answerregs = array();
                if ($subqdata->options->shuffleanswers == 1 &&  isset($questiondata->options->shuffleanswers)
                    && $questiondata->options->shuffleanswers == 0 ) {
                    $subqdata->options->shuffleanswers = 0;
                }
            }
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
            if ($subqdata->qtype == 'subquestion_replacement') {
                continue;
            }
            $fractionmax += $subqdata->defaultmark;
            $fractionsum += question_bank::get_qtype(
                    $subqdata->qtype)->get_random_guess_score($subqdata);
        }
        if ($fractionmax > question_utils::MARK_TOLERANCE) {
            return $fractionsum / $fractionmax;
        } else {
            return null;
        }
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
       '=|%(-?[0-9]+(?:[.,][0-9]*)?)%');
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
        '(SHORTANSWER|SA|MW)|(SHORTANSWER_C|SAC|MWC)|' .
        '(MULTICHOICE_S|MCS)|(MULTICHOICE_VS|MCVS)|(MULTICHOICE_HS|MCHS)|'.
        '(MULTIRESPONSE|MR)|(MULTIRESPONSE_H|MRH)|(MULTIRESPONSE_S|MRS)|(MULTIRESPONSE_HS|MRHS)');
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
define('ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_SHUFFLED', 9);
define('ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR_SHUFFLED', 10);
define('ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL_SHUFFLED', 11);
define('ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE', 12);
define('ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE_HORIZONTAL', 13);
define('ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE_SHUFFLED', 14);
define('ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE_HORIZONTAL_SHUFFLED', 15);
define('ANSWER_REGEX_ALTERNATIVES', 16);

/**
 * Initialise subquestion fields that are constant across all MULTICHOICE
 * types.
 *
 * @param objet $wrapped  The subquestion to initialise
 *
 */
function qtype_multianswer_initialise_multichoice_subquestion($wrapped) {
    $wrapped->qtype = 'multichoice';
    $wrapped->single = 1;
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
}

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
        if (isset($answerregs[ANSWER_REGEX_NORM]) && $answerregs[ANSWER_REGEX_NORM] !== '') {
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
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->shuffleanswers = 0;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_DROPDOWN;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_SHUFFLED])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->shuffleanswers = 1;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_DROPDOWN;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->shuffleanswers = 0;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_VERTICAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR_SHUFFLED])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->shuffleanswers = 1;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_VERTICAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->shuffleanswers = 0;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_HORIZONTAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL_SHUFFLED])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->shuffleanswers = 1;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_HORIZONTAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->single = 0;
            $wrapped->shuffleanswers = 0;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_VERTICAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE_HORIZONTAL])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->single = 0;
            $wrapped->shuffleanswers = 0;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_HORIZONTAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE_SHUFFLED])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->single = 0;
            $wrapped->shuffleanswers = 1;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_VERTICAL;
        } else if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTIRESPONSE_HORIZONTAL_SHUFFLED])) {
            qtype_multianswer_initialise_multichoice_subquestion($wrapped);
            $wrapped->single = 0;
            $wrapped->shuffleanswers = 1;
            $wrapped->layout = qtype_multichoice_base::LAYOUT_HORIZONTAL;
        } else {
            throw new \moodle_exception('unknownquestiontype', 'question', '', $answerregs[2]);
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

        $hasspecificfraction = false;
        $remainingalts = $answerregs[ANSWER_REGEX_ALTERNATIVES];
        while (preg_match('/~?'.ANSWER_ALTERNATIVE_REGEX.'/s', $remainingalts, $altregs)) {
            if ('=' == $altregs[ANSWER_ALTERNATIVE_REGEX_FRACTION]) {
                $wrapped->fraction["{$answerindex}"] = '1';
            } else if ($percentile = $altregs[ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION]) {
                // Accept either decimal place character.
                $wrapped->fraction["{$answerindex}"] = .01 * str_replace(',', '.', $percentile);
                $hasspecificfraction = true;
            } else {
                $wrapped->fraction["{$answerindex}"] = '0';
            }
            if (isset($altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK])) {
                $feedback = html_entity_decode(
                        $altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK], ENT_QUOTES, 'UTF-8');
                $feedback = str_replace('\}', '}', $feedback);
                $wrapped->feedback["{$answerindex}"]['text'] = str_replace('\#', '#', $feedback);
                $wrapped->feedback["{$answerindex}"]['format'] = FORMAT_HTML;
                $wrapped->feedback["{$answerindex}"]['itemid'] = '';
            } else {
                $wrapped->feedback["{$answerindex}"]['text'] = '';
                $wrapped->feedback["{$answerindex}"]['format'] = FORMAT_HTML;
                $wrapped->feedback["{$answerindex}"]['itemid'] = '';

            }
            if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])
                    && preg_match('~'.NUMERICAL_ALTERNATIVE_REGEX.'~s',
                            $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], $numregs)) {
                $wrapped->answer[] = $numregs[NUMERICAL_CORRECT_ANSWER];
                if (array_key_exists(NUMERICAL_ABS_ERROR_MARGIN, $numregs)) {
                    $wrapped->tolerance["{$answerindex}"] =
                    $numregs[NUMERICAL_ABS_ERROR_MARGIN];
                } else {
                    $wrapped->tolerance["{$answerindex}"] = 0;
                }
            } else { // Tolerance can stay undefined for non numerical questions.
                // Undo quoting done by the HTML editor.
                $answer = html_entity_decode(
                        $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], ENT_QUOTES, 'UTF-8');
                $answer = str_replace('\}', '}', $answer);
                $wrapped->answer["{$answerindex}"] = str_replace('\#', '#', $answer);
                if ($wrapped->qtype == 'multichoice') {
                    $wrapped->answer["{$answerindex}"] = array(
                            'text' => $wrapped->answer["{$answerindex}"],
                            'format' => FORMAT_HTML,
                            'itemid' => '');
                }
            }
            $tmp = explode($altregs[0], $remainingalts, 2);
            $remainingalts = $tmp[1];
            $answerindex++;
        }

        // Fix the score for multichoice_multi questions (as positive scores should add up to 1, not have a maximum of 1).
        if (isset($wrapped->single) && $wrapped->single == 0) {
            $total = 0;
            foreach ($wrapped->fraction as $idx => $fraction) {
                if ($fraction > 0) {
                    $total += $fraction;
                }
            }
            if ($total) {
                foreach ($wrapped->fraction as $idx => $fraction) {
                    if ($fraction > 0) {
                        $wrapped->fraction[$idx] = $fraction / $total;
                    } else if (!$hasspecificfraction) {
                        // If no specific fractions are given, set incorrect answers to each cancel out one correct answer.
                        $wrapped->fraction[$idx] = -(1.0 / $total);
                    }
                }
            }
        }

        $question->defaultmark += $wrapped->defaultmark;
        $question->options->questions[$positionkey] = clone($wrapped);
        $question->questiontext['text'] = implode("{#$positionkey}",
                    explode($answerregs[0], $question->questiontext['text'], 2));
    }
    return $question;
}

/**
 * Validate a multianswer question.
 *
 * @param object $question  The multianswer question to validate as returned by qtype_multianswer_extract_question
 * @return array Array of error messages with questions field names as keys.
 */
function qtype_multianswer_validate_question(stdClass $question) : array {
    $errors = array();
    if (!isset($question->options->questions)) {
        $errors['questiontext'] = get_string('questionsmissing', 'qtype_multianswer');
    } else {
        $subquestions = fullclone($question->options->questions);
        if (count($subquestions)) {
            $sub = 1;
            foreach ($subquestions as $subquestion) {
                $prefix = 'sub_'.$sub.'_';
                $answercount = 0;
                $maxgrade = false;
                $maxfraction = -1;

                foreach ($subquestion->answer as $key => $answer) {
                    if (is_array($answer)) {
                        $answer = $answer['text'];
                    }
                    $trimmedanswer = trim($answer);
                    if ($trimmedanswer !== '') {
                        $answercount++;
                        if ($subquestion->qtype == 'numerical' &&
                                !(qtype_numerical::is_valid_number($trimmedanswer) || $trimmedanswer == '*')) {
                            $errors[$prefix.'answer['.$key.']'] =
                                    get_string('answermustbenumberorstar', 'qtype_numerical');
                        }
                        if ($subquestion->fraction[$key] == 1) {
                            $maxgrade = true;
                        }
                        if ($subquestion->fraction[$key] > $maxfraction) {
                            $maxfraction = $subquestion->fraction[$key];
                        }
                        // For 'multiresponse' we are OK if there is at least one fraction > 0.
                        if ($subquestion->qtype == 'multichoice' && $subquestion->single == 0 &&
                            $subquestion->fraction[$key] > 0) {
                            $maxgrade = true;
                        }
                    }
                }
                if ($subquestion->qtype == 'multichoice' && $answercount < 2) {
                    $errors[$prefix.'answer[0]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
                } else if ($answercount == 0) {
                    $errors[$prefix.'answer[0]'] = get_string('notenoughanswers', 'question', 1);
                }
                if ($maxgrade == false) {
                    $errors[$prefix.'fraction[0]'] = get_string('fractionsnomax', 'question');
                }
                $sub++;
            }
        } else {
            $errors['questiontext'] = get_string('questionsmissing', 'qtype_multianswer');
        }
    }
    return $errors;
}
