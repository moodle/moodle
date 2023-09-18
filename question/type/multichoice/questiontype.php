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
 * The questiontype class for the multiple choice question type.
 *
 * @package    qtype
 * @subpackage multichoice
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');


/**
 * The multiple choice question type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice extends question_type {
    public function get_question_options($question) {
        global $DB, $OUTPUT;

        $question->options = $DB->get_record('qtype_multichoice_options', ['questionid' => $question->id]);

        if ($question->options === false) {
            // If this has happened, then we have a problem.
            // For the user to be able to edit or delete this question, we need options.
            debugging("Question ID {$question->id} was missing an options record. Using default.", DEBUG_DEVELOPER);

            $question->options = $this->create_default_options($question);
        }

        parent::get_question_options($question);
    }

    /**
     * Create a default options object for the provided question.
     *
     * @param object $question The queston we are working with.
     * @return object The options object.
     */
    protected function create_default_options($question) {
        // Create a default question options record.
        $options = new stdClass();
        $options->questionid = $question->id;

        // Get the default strings and just set the format.
        $options->correctfeedback = get_string('correctfeedbackdefault', 'question');
        $options->correctfeedbackformat = FORMAT_HTML;
        $options->partiallycorrectfeedback = get_string('partiallycorrectfeedbackdefault', 'question');;
        $options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $options->incorrectfeedback = get_string('incorrectfeedbackdefault', 'question');
        $options->incorrectfeedbackformat = FORMAT_HTML;

        $config = get_config('qtype_multichoice');
        $options->single = $config->answerhowmany;
        if (isset($question->layout)) {
            $options->layout = $question->layout;
        }
        $options->answernumbering = $config->answernumbering;
        $options->shuffleanswers = $config->shuffleanswers;
        $options->showstandardinstruction = 0;
        $options->shownumcorrect = 1;

        return $options;
    }

    public function save_defaults_for_new_questions(stdClass $fromform): void {
        parent::save_defaults_for_new_questions($fromform);
        $this->set_default_value('single', $fromform->single);
        $this->set_default_value('shuffleanswers', $fromform->shuffleanswers);
        $this->set_default_value('answernumbering', $fromform->answernumbering);
        $this->set_default_value('showstandardinstruction', $fromform->showstandardinstruction);
    }

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $result = new stdClass();

        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // Following hack to check at least two answers exist.
        $answercount = 0;
        foreach ($question->answer as $key => $answer) {
            if ($answer != '') {
                $answercount++;
            }
        }
        if ($answercount < 2) { // Check there are at lest 2 answers for multiple choice.
            $result->error = get_string('notenoughanswers', 'qtype_multichoice', '2');
            return $result;
        }

        // Insert all the new answers.
        $totalfraction = 0;
        $maxfraction = -1;
        foreach ($question->answer as $key => $answerdata) {
            if (trim($answerdata['text']) == '') {
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

            // Doing an import.
            $answer->answer = $this->import_or_save_files($answerdata,
                    $context, 'question', 'answer', $answer->id);
            $answer->answerformat = $answerdata['format'];
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];

            $DB->update_record('question_answers', $answer);

            if ($question->fraction[$key] > 0) {
                $totalfraction += $question->fraction[$key];
            }
            if ($question->fraction[$key] > $maxfraction) {
                $maxfraction = $question->fraction[$key];
            }
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        $options = $DB->get_record('qtype_multichoice_options', array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->showstandardinstruction = 0;
            $options->id = $DB->insert_record('qtype_multichoice_options', $options);
        }

        $options->single = $question->single;
        if (isset($question->layout)) {
            $options->layout = $question->layout;
        }
        $options->answernumbering = $question->answernumbering;
        $options->shuffleanswers = $question->shuffleanswers;
        $options->showstandardinstruction = !empty($question->showstandardinstruction);
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('qtype_multichoice_options', $options);

        $this->save_hints($question, true);

        // Perform sanity checks on fractional grades.
        if ($options->single) {
            if ($maxfraction != 1) {
                $result->noticeyesno = get_string('fractionsnomax', 'qtype_multichoice',
                        $maxfraction * 100);
                return $result;
            }
        } else {
            $totalfraction = round($totalfraction, 2);
            if ($totalfraction != 1) {
                $result->noticeyesno = get_string('fractionsaddwrong', 'qtype_multichoice',
                        $totalfraction * 100);
                return $result;
            }
        }
    }

    protected function make_question_instance($questiondata) {
        question_bank::load_question_definition_classes($this->name());
        if ($questiondata->options->single) {
            $class = 'qtype_multichoice_single_question';
        } else {
            $class = 'qtype_multichoice_multi_question';
        }
        return new $class();
    }

    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->shuffleanswers = $questiondata->options->shuffleanswers;
        $question->answernumbering = $questiondata->options->answernumbering;
        $question->showstandardinstruction = $questiondata->options->showstandardinstruction;
        if (!empty($questiondata->options->layout)) {
            $question->layout = $questiondata->options->layout;
        } else {
            $question->layout = qtype_multichoice_single_question::LAYOUT_VERTICAL;
        }
        $this->initialise_combined_feedback($question, $questiondata, true);

        $this->initialise_question_answers($question, $questiondata, false);
    }

    public function make_answer($answer) {
        // Overridden just so we can make it public for use by question.php.
        return parent::make_answer($answer);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_multichoice_options', array('questionid' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    public function get_random_guess_score($questiondata) {
        if (!$questiondata->options->single) {
            // Pretty much impossible to compute for _multi questions. Don't try.
            return null;
        }

        if (empty($questiondata->options->answers)) {
            // A multi-choice question with no choices is senseless,
            // but, seemingly, it can happen (presumably as a side-effect of bugs).
            // Therefore, ensure it does not lead to errors here.
            return null;
        }

        // Single choice questions - average choice fraction.
        $totalfraction = 0;
        foreach ($questiondata->options->answers as $answer) {
            $totalfraction += $answer->fraction;
        }
        return $totalfraction / count($questiondata->options->answers);
    }

    public function get_possible_responses($questiondata) {
        if ($questiondata->options->single) {
            $responses = array();

            foreach ($questiondata->options->answers as $aid => $answer) {
                $responses[$aid] = new question_possible_response(
                        question_utils::to_plain_text($answer->answer, $answer->answerformat),
                        $answer->fraction);
            }

            $responses[null] = question_possible_response::no_response();
            return array($questiondata->id => $responses);
        } else {
            $parts = array();

            foreach ($questiondata->options->answers as $aid => $answer) {
                $parts[$aid] = array($aid => new question_possible_response(
                        question_utils::to_plain_text($answer->answer, $answer->answerformat),
                        $answer->fraction));
            }

            return $parts;
        }
    }

    /**
     * @return array of the numbering styles supported. For each one, there
     *      should be a lang string answernumberingxxx in teh qtype_multichoice
     *      language file, and a case in the switch statement in number_in_style,
     *      and it should be listed in the definition of this column in install.xml.
     */
    public static function get_numbering_styles() {
        $styles = array();
        foreach (array('abc', 'ABCD', '123', 'iii', 'IIII', 'none') as $numberingoption) {
            $styles[$numberingoption] =
                    get_string('answernumbering' . $numberingoption, 'qtype_multichoice');
        }
        return $styles;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid, true);
        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid, true);
        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
}
