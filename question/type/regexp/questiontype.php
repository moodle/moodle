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
 * The question type class for the regexp question type.
 *
 * @package    qtype_regexp
 * @copyright  Jean-Michel Vedrine  & Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/regexp/question.php');

/**
 * The question type class for the regexp question type.
 *
 * @package    qtype_regexp
 * @copyright  Jean-Michel Vedrine  & Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexp extends question_type {

    /**
     * data used by export_to_xml (among other things possibly
     * @return array
     */
    public function extra_question_fields() {
        return ['qtype_regexp', 'usehint', 'usecase', 'studentshowalternate'];
    }

    /**
     * Move all the files belonging to this question from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     *
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
    }

    /**
     * Delete all the files belonging to this question.Seems the same as in the parent
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
    }

    /**
     * Saves question options.
     * @param stdClass $question
     * @return object
     */
    public function save_question_options($question) {
        global $DB, $SESSION, $CFG;
        require_once($CFG->dirroot.'/question/type/regexp/locallib.php');
        $result = new stdClass;

        $context = $question->context;

        $oldanswers = $DB->get_records('question_answers',
                ['question' => $question->id], 'id ASC');

        $answers = [];

        // Insert all the new answers.
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ignore, completely blank answer from the form.
            if (trim($answerdata) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key]['text'])) {
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
            // JR august 2012 remove any superfluous blanks in expressions before saving.
            $answer->answer = remove_blanks($answerdata);
            // Set grade for Answer 1 to 1 (100%).
            if ($key === 0) {
                $question->fraction[$key] = 1;
            }
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];
            $DB->update_record('question_answers', $answer);

            $answers[] = $answer->id;
        }

        $question->answers = implode(',', $answers);
        $parentresult = parent::save_question_options($question);
        if ($parentresult !== null) {
            // Parent function returns null if all is OK.
            return $parentresult;
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', ['id' => $oldanswer->id]);
        }
        $this->save_hints($question);

        // JR dec 2011 unset alternateanswers and alternatecorrectanswers after question has been edited, just in case.
        $qid = $question->id;
        if (isset($SESSION->qtype_regexp_question->alternateanswers[$qid])) {
            unset($SESSION->qtype_regexp_question->alternateanswers[$qid]);
        }
        if (isset($SESSION->qtype_regexp_question->alternatecorrectanswers[$qid])) {
            unset($SESSION->qtype_regexp_question->alternatecorrectanswers[$qid]);
        }
    }

    /**
     * Called when previewing or at runtime in a quiz.
     *
     * @param question_definition $question
     * @param stdClass $questiondata
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->usecase = $questiondata->options->usecase;
        $question->usehint = $questiondata->options->usehint;
        $question->studentshowalternate = $questiondata->options->studentshowalternate;
        $this->initialise_question_answers($question, $questiondata);
        $qid = $question->id;
    }

    /**
     * Gets random guess score.
     *
     * @param stdClass $questiondata
     */
    public function get_random_guess_score($questiondata) {
        foreach ($questiondata->options->answers as $aid => $answer) {
            if ('*' == trim($answer->answer)) {
                return $answer->fraction;
            }
        }
        return 0;
    }

    /**
     * Gets possible responses.
     *
     * @param stdClass $questiondata
     */
    public function get_possible_responses($questiondata) {
        $responses = [];

        foreach ($questiondata->options->answers as $aid => $answer) {
            $responses[$aid] = new question_possible_response($answer->answer, $answer->fraction);
        }
        $responses[null] = question_possible_response::no_response();
        return [$questiondata->id => $responses];
    }

    /**
     * Export question to the Moodle XML format
     *
     * @param object $question
     * @param qformat_xml $format
     * @param object $extra
     * @return string
     */
    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $extraquestionfields = $this->extra_question_fields();
        if (!is_array($extraquestionfields)) {
            return false;
        }
        // Omit table name (question).
        array_shift($extraquestionfields);
        $expout = '';
        foreach ($extraquestionfields as $field) {
            $exportedvalue = $question->options->$field;
            if (!empty($exportedvalue) && htmlspecialchars($exportedvalue) != $exportedvalue) {
                $exportedvalue = '<![CDATA[' . $exportedvalue . ']]>';
            }
            $expout .= "    <$field>{$exportedvalue}</$field>\n";
        }
        foreach ($question->options->answers as $answer) {
            $percent = 100 * $answer->fraction;
            $expout .= "    <answer fraction=\"$percent\">\n";
            $expout .= $format->writetext($answer->answer, 3, false);
            $expout .= "      <feedback format=\"html\">\n";
            $expout .= $format->writetext($answer->feedback, 4, false);
            $expout .= "      </feedback>\n";
            $expout .= "    </answer>\n";
        }
        return $expout;
    }

    /**
     * Create a question from reading in a file in Moodle xml format
     *
     * @param array $data
     * @param stdClass $question (might be an array)
     * @param qformat_xml $format
     * @param stdClass $extra
     * @return boolean
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        // Check question is for us.
        $qtype = $data['@']['type'];
        if ($qtype == 'regexp') {
            $qo = $format->import_headers( $data );

            // Header parts particular to regexp.
            $qo->qtype = "regexp";
            $qo->usehint = 0;

            // Get usehint.
            $qo->usehint = $format->getpath($data, ['#', 'usehint', 0, '#'], $qo->usehint );
            // Get usecase.
            $qo->usecase = $format->getpath($data, ['#', 'usecase', 0, '#'], $qo->usecase );
            // Get studentshowalternate.
            $qo->studentshowalternate = new stdClass;
            $qo->studentshowalternate = $format->getpath($data, ['#', 'studentshowalternate', 0, '#'],
                            $qo->studentshowalternate );

            // Run through the answers.
            $answers = $data['#']['answer'];
            $acount = 0;
            foreach ($answers as $answer) {
                $ans = $format->import_answer($answer);
                $qo->answer[$acount] = $ans->answer['text'];
                $qo->fraction[$acount] = $ans->fraction;
                $qo->feedback[$acount] = $ans->feedback;
                ++$acount;
            }
            $format->import_combined_feedback($qo, $data, true);
            $format->import_hints($qo, $data, true, false,
                $format->get_format($qo->questiontextformat));
            return $qo;
        } else {
            return false;
        }
    }
}
