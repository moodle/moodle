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
 * OU multiple response question type class.
 *
 * @package    qtype
 * @subpackage oumultiresponse
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/multichoice/questiontype.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');


/**
 * This questions type is like the standard multiplechoice question type, but
 * with these differences:
 *
 * 1. The focus is just on the multiple response case.
 *
 * 2. The correct answer is just indicated on the editing form by a indicating
 * which choices are correct. There is no complex but flexible scoring system.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_oumultiresponse extends question_type {
    public function has_html_answers() {
        return true;
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('question_oumultiresponse',
                array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $result = new stdClass();

        $oldanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC');

        // following hack to check at least two answers exist
        $answercount = 0;
        foreach ($question->answer as $key => $answer) {
            if ($answer != '') {
                $answercount++;
            }
        }
        if ($answercount < 2) { // check there are at lest 2 answers for multiple choice
            $result->notice = get_string('notenoughanswers', 'qtype_multichoice', '2');
            return $result;
        }

        // Insert all the new answers
        $answers = array();
        foreach ($question->answer as $key => $answerdata) {
            if ($answerdata == '') {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            if (is_array($answerdata)) {
                // Doing an import
                $answer->answer = $this->import_or_save_files($answerdata,
                        $context, 'question', 'answer', $answer->id);
                $answer->answerformat = $answerdata['format'];
            } else {
                // Saving the form
                $answer->answer = $answerdata;
                $answer->answerformat = FORMAT_HTML;
            }
            $answer->fraction = !empty($question->correctanswer[$key]);
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];

            $DB->update_record('question_answers', $answer);
            $answers[] = $answer->id;
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        $options = $DB->get_record('question_oumultiresponse', array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('question_oumultiresponse', $options);
        }

        $options->answernumbering = $question->answernumbering;
        $options->shuffleanswers = $question->shuffleanswers;
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('question_oumultiresponse', $options);

        $this->save_hints($question, true);
    }

    public function save_hints($formdata, $withparts = false) {
        global $DB;
        $context = $formdata->context;

        $oldhints = $DB->get_records('question_hints', array('questionid' => $formdata->id));

        if (!empty($formdata->hint)) {
            $numhints = max(array_keys($formdata->hint)) + 1;
        } else {
            $numhints = 0;
        }

        if ($withparts) {
            if (!empty($formdata->hintclearwrong)) {
                $numclears = max(array_keys($formdata->hintclearwrong)) + 1;
            } else {
                $numclears = 0;
            }
            if (!empty($formdata->hintshownumcorrect)) {
                $numshows = max(array_keys($formdata->hintshownumcorrect)) + 1;
            } else {
                $numshows = 0;
            }
            $numhints = max($numhints, $numclears, $numshows);
        }

        if (!empty($formdata->hintshowchoicefeedback)) {
            $numshowfeedbacks = max(array_keys($formdata->hintshowchoicefeedback)) + 1;
        } else {
            $numshowfeedbacks = 0;
        }
        $numhints = max($numhints, $numshowfeedbacks);

        for ($i = 0; $i < $numhints; $i += 1) {
            if (html_is_blank($formdata->hint[$i]['text'])) {
                $formdata->hint[$i]['text'] = '';
            }

            if ($withparts) {
                $clearwrong = !empty($formdata->hintclearwrong[$i]);
                $shownumcorrect = !empty($formdata->hintshownumcorrect[$i]);
            }

            $showchoicefeedback = !empty($formdata->hintshowchoicefeedback[$i]);

            if (empty($hint) && empty($clearwrong) &&
                    empty($shownumcorrect) && empty($showchoicefeedback)) {
                continue;
            }

            // Update an existing answer if possible.
            $hint = array_shift($oldhints);
            if (!$hint) {
                $hint = new stdClass();
                $hint->questionid = $formdata->id;
                $hint->hint = '';
                $hint->id = $DB->insert_record('question_hints', $hint);
            }

            $hint->hint = $this->import_or_save_files($formdata->hint[$i],
                    $context, 'question', 'hint', $hint->id);
            $hint->hintformat = $formdata->hint[$i]['format'];
            if ($withparts) {
                $hint->clearwrong = $clearwrong;
                $hint->shownumcorrect = $shownumcorrect;
            }
            $hint->options = $showchoicefeedback;
            $DB->update_record('question_hints', $hint);
        }
    }

    protected function make_hint($hint) {
        return qtype_oumultiresponse_hint::load_from_record($hint);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->shuffleanswers = $questiondata->options->shuffleanswers;
        $question->answernumbering = $questiondata->options->answernumbering;
        $this->initialise_combined_feedback($question, $questiondata, true);
        $this->initialise_question_answers($question, $questiondata);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_oumultiresponse', array('questionid' => $questionid));
        return parent::delete_question($questionid, $contextid);
    }

    protected function get_num_correct_choices($questiondata) {
        $numright = 0;
        foreach ($questiondata->options->answers as $answer) {
            if (!question_state::graded_state_for_fraction($answer->fraction)->is_incorrect()) {
                $numright += 1;
            }
        }
        return $numright;
    }

    public function get_random_guess_score($questiondata) {
        // We compute the randome guess score here on the assumption we are using
        // the deferred feedback behaviour, and the question text tells the
        // student how many of the responses are correct.
        // Amazingly, the forumla for this works out to be
        // # correct choices / total # choices in all cases.
        return $this->get_num_correct_choices($questiondata) /
                count($questiondata->options->answers);
    }

    public function get_possible_responses($questiondata) {
        $numright = $this->get_num_correct_choices($questiondata);
        $parts = array();

        foreach ($questiondata->options->answers as $aid => $answer) {
            $parts[$aid] = array($aid =>
                    new question_possible_response($answer->answer, $answer->fraction / $numright));
        }

        return $parts;
    }

    public function import_from_xml($data, $question, $format, $extra=null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'oumultiresponse') {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = 'oumultiresponse';

        $question->shuffleanswers = $format->trans_single(
                $format->getpath($data, array('#', 'shuffleanswers', 0, '#'), 1));
        $question->answernumbering = $format->getpath($data,
                array('#', 'answernumbering', 0, '#'), 'abc');

        $format->import_combined_feedback($question, $data, true);

        // Run through the answers
        $answers = $data['#']['answer'];
        foreach ($answers as $answer) {
            $ans = $format->import_answer($answer);
            $question->answer[] = $ans->answer;
            $question->correctanswer[] = !empty($ans->fraction);
            $question->feedback[] = $ans->feedback;

            // Backwards compatibility.
            if (array_key_exists('correctanswer', $answer['#'])) {
                $key = end(array_keys($question->correctanswer));
                $question->correctanswer[$key] = $format->getpath($answer,
                        array('#', 'correctanswer', 0, '#'), 0);
            }
        }

        $format->import_hints($question, $data, true, true);

        // Get extra choicefeedback setting from each hint.
        if (!empty($question->hintoptions)) {
            foreach ($question->hintoptions as $key => $options) {
                $question->hintshowchoicefeedback[$key] = !empty($options);
            }
        }

        return $question;
    }

    public function export_to_xml($question, $format, $extra = null) {
        $output = '';

        $output .= "    <shuffleanswers>" . $format->get_single($question->options->shuffleanswers) . "</shuffleanswers>\n";
        $output .= "    <answernumbering>{$question->options->answernumbering}</answernumbering>\n";

        $output .= $format->write_combined_feedback($question->options);
        $output .= $format->write_answers($question->options->answers);

        return $output;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid, true);

        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'correctfeedback', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'partiallycorrectfeedback', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'incorrectfeedback', $questionid);
    }

    protected function delete_files($questionid, $contextid) {
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid, true);
        $fs->delete_area_files($contextid, 'question', 'correctfeedback', $questionid);
        $fs->delete_area_files($contextid, 'question', 'partiallycorrectfeedback', $questionid);
        $fs->delete_area_files($contextid, 'question', 'incorrectfeedback', $questionid);
    }
}


/**
 * An extension of {@link question_hint_with_parts} for oumultirespone questions
 * with an extra option for whether to show the feedback for each choice.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_oumultiresponse_hint extends question_hint_with_parts {
    /** @var boolean whether to show the feedback for each choice. */
    public $showchoicefeedback;

    /**
     * Constructor.
     * @param string $hint The hint text
     * @param bool $shownumcorrect whether the number of right parts should be shown
     * @param bool $clearwrong whether the wrong parts should be reset.
     * @param bool $showchoicefeedback whether to show the feedback for each choice.
     */
    public function __construct($id, $hint, $hintformat, $shownumcorrect, $clearwrong, $showchoicefeedback) {
        parent::__construct($id, $hint, $hintformat, $shownumcorrect, $clearwrong);
        $this->showchoicefeedback = $showchoicefeedback;
    }

    /**
     * Create a basic hint from a row loaded from the question_hints table in the database.
     * @param object $row with $row->hint, ->shownumcorrect and ->clearwrong set.
     * @return question_hint_with_parts
     */
    public static function load_from_record($row) {
        return new qtype_oumultiresponse_hint($row->id, $row->hint, $row->hintformat,
                $row->shownumcorrect, $row->clearwrong, !empty($row->options));
    }

    public function adjust_display_options(question_display_options $options) {
        parent::adjust_display_options($options);
        $options->suppresschoicefeedback = !$this->showchoicefeedback;
    }
}
