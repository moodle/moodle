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
 * Question type class for the randomsamatch question type.
 *
 * @package    qtype_randomsamatch
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/type/numerical/question.php');

/**
 * The randomsamatch question type class.
 *
 * TODO: Make sure short answer questions chosen by a randomsamatch question
 * can not also be used by a random question
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch extends question_type {
    /**
     * Cache of available shortanswer question ids from a particular category.
     * @var array two-dimensional array. The first key is a category id, the
     * second key is wether subcategories should be included.
     */
    private $availablesaquestionsbycategory = array();
    const MAX_SUBQUESTIONS = 10;

    public function is_usable_by_random() {
        return false;
    }

    public function get_question_options($question) {
        global $DB;
        parent::get_question_options($question);
        $question->options = $DB->get_record('qtype_randomsamatch_options',
                array('questionid' => $question->id));

        return true;

    }

    public function save_question_options($question) {
        global $DB;

        if (2 > $question->choose) {
            $result = new stdClass();
            $result->error = "At least two shortanswer questions need to be chosen!";
            return $result;
        }

        $context = $question->context;

        // Save the question options.
        $options = $DB->get_record('qtype_randomsamatch_options', array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('qtype_randomsamatch_options', $options);
        }

        $options->choose = $question->choose;
        $options->subcats = $question->subcats;
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('qtype_randomsamatch_options', $options);

        $this->save_hints($question, true);

        return true;
    }

    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_randomsamatch_options', array('questionid' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);

        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);

        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $availablesaquestions = $this->get_available_saquestions_from_category(
                $question->category, $questiondata->options->subcats);
        $question->shufflestems = false;
        $question->stems = array();
        $question->choices = array();
        $question->right = array();
        $this->initialise_combined_feedback($question, $questiondata);
        $question->questionsloader = new qtype_randomsamatch_question_loader(
                $availablesaquestions, $questiondata->options->choose);
    }

    public function can_analyse_responses() {
        return false;
    }

    /**
     * Get all the usable shortanswer questions from a particular question category.
     *
     * @param integer $categoryid the id of a question category.
     * @param bool $subcategories whether to include questions from subcategories.
     * @return array of question records.
     */
    public function get_available_saquestions_from_category($categoryid, $subcategories) {
        if (isset($this->availablesaquestionsbycategory[$categoryid][$subcategories])) {
            return $this->availablesaquestionsbycategory[$categoryid][$subcategories];
        }

        if ($subcategories) {
            $categoryids = question_categorylist($categoryid);
        } else {
            $categoryids = array($categoryid);
        }

        $questionids = question_bank::get_finder()->get_questions_from_categories(
                $categoryids, "qtype = 'shortanswer'");
        $this->availablesaquestionsbycategory[$categoryid][$subcategories] = $questionids;
        return $questionids;
    }

    /**
     * @param object $question
     * @return mixed either a integer score out of 1 that the average random
     * guess by a student might give or an empty string which means will not
     * calculate.
     */
    public function get_random_guess_score($question) {
        return 1/$question->options->choose;
    }

    /**
     * Defines the table which extends the question table. This allows the base questiontype
     * to automatically save, backup and restore the extra fields.
     *
     * @return an array with the table name (first) and then the column names (apart from id and questionid)
     */
    public function extra_question_fields() {
        return array('qtype_randomsamatch_options',
                     'choose',        // Number of shortanswer questions to choose.
                     'subcats',       // Questions can be choosen from subcategories.
                     );
    }

    /**
     * Imports the question from Moodle XML format.
     *
     * @param array $xml structure containing the XML data
     * @param object $fromform question object to fill: ignored by this function (assumed to be null)
     * @param qformat_xml $format format class exporting the question
     * @param object $extra extra information (not required for importing this question in this format)
     * @return object question object
     */
    public function import_from_xml($xml, $fromform, qformat_xml $format, $extra=null) {
        // Return if data type is not our own one.
        if (!isset($xml['@']['type']) || $xml['@']['type'] != $this->name()) {
            return false;
        }

        // Import the common question headers and set the corresponding field.
        $fromform = $format->import_headers($xml);
        $fromform->qtype = $this->name();
        $format->import_combined_feedback($fromform, $xml, true);
        $format->import_hints($fromform, $xml, true);

        $extras = $this->extra_question_fields();
        array_shift($extras);
        foreach ($extras as $extra) {
            $fromform->$extra = $format->getpath($xml, array('#', $extra, 0, '#'), '', true);
        }

        return $fromform;
    }

    /**
     * Exports the question to Moodle XML format.
     *
     * @param object $question question to be exported into XML format
     * @param qformat_xml $format format class exporting the question
     * @param object $extra extra information (not required for exporting this question in this format)
     * @return string containing the question data in XML format
     */
    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $expout = '';
        $expout .= $format->write_combined_feedback($question->options,
                                                    $question->id,
                                                    $question->contextid);
        $extraquestionfields = $this->extra_question_fields();
        array_shift($extraquestionfields);
        foreach ($extraquestionfields as $extra) {
            $expout .= "    <$extra>" . $question->options->$extra . "</$extra>\n";
        }
        return $expout;
    }
}
