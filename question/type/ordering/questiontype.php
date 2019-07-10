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
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The ordering question type.
 *
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering extends question_type {

    /** @var array Combined feedback fields */
    public $feedbackfields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');

    /**
     * Initialise the common question_definition fields.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->answernumbering = $questiondata->options->answernumbering;
        $this->initialise_combined_feedback($question, $questiondata);
    }

    /**
     * Saves question-type specific options
     *
     * This is called by {@link save_question()} to save the question-type specific data
     * @return object $result->error or $result->notice
     * @param object $question  This holds the information from the editing form,
     *      it is not a standard question object.
     */
    public function save_question_options($question) {
        global $DB;

        $result = new stdClass();
        $context = $question->context;

        // Remove empty answers.
        $question->answer = array_filter($question->answer, array($this, 'is_not_blank'));
        $question->answer = array_values($question->answer); // Make keys sequential.

        // Count how many answers we have.
        $countanswers = count($question->answer);

        // Search/replace strings to reduce simple <p>...</p> to plain text.
        $psearch = '/^\s*<p>\s*(.*?)(\s*<br\s*\/?>)*\s*<\/p>\s*$/';
        $preplace = '$1';

        // Search/replace strings to standardize vertical align of <img> tags.
        $imgsearch = '/(<img[^>]*)\bvertical-align:\s*[a-zA-Z0-9_-]+([^>]*>)/';
        $imgreplace = '$1'.'vertical-align:text-top'.'$2';

        // Check at least two answers exist.
        if ($countanswers < 2) {
            $result->notice = get_string('notenoughanswers', 'qtype_ordering', '2');
            return $result;
        }

        $question->feedback = range(1, $countanswers);

        if ($answerids = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC', 'id,question')) {
            $answerids = array_keys($answerids);
        } else {
            $answerids = array();
        }

        // Insert all the new answers.
        foreach ($question->answer as $i => $answer) {
            $answertext = '';
            $answerformat = 0;
            $answeritemid = null;

            // Extract $answer fields.
            if (is_string($answer)) {
                // Import from file.
                $answertext = $answer;
            } else if (is_array($answer)) {
                // Input from browser.
                if (isset($answer['text'])) {
                    $answertext = $answer['text'];
                }
                if (isset($answer['format'])) {
                    $answerformat = $answer['format'];
                }
                if (isset($answer['itemid'])) {
                    $answeritemid = $answer['itemid'];
                }
            }

            // Reduce simple <p>...</p> to plain text.
            if (substr_count($answertext, '<p>') == 1) {
                $answertext = preg_replace($psearch, $preplace, $answertext);
            }
            $answertext = trim($answertext);

            // Skip empty answers.
            if ($answertext == '') {
                continue;
            }

            // Standardize vertical align of img tags.
            $answertext = preg_replace($imgsearch, $imgreplace, $answertext);

            // Prepare the $answer object.
            $answer = (object)array(
                'question'       => $question->id,
                'fraction'       => ($i + 1), // Start at 1.
                'answer'         => $answertext,
                'answerformat'   => $answerformat,
                'feedback'       => '',
                'feedbackformat' => FORMAT_MOODLE,
            );

            // Add/insert $answer into the database.
            if ($answer->id = array_shift($answerids)) {
                if (! $DB->update_record('question_answers', $answer)) {
                    $result->error = get_string('cannotupdaterecord', 'error', 'question_answers (id='.$answer->id.')');
                    return $result;
                }
            } else {
                unset($answer->id);
                if (! $answer->id = $DB->insert_record('question_answers', $answer)) {
                    $result->error = get_string('cannotinsertrecord', 'error', 'question_answers');
                    return $result;
                }
            }

            // Copy files across from draft files area.
            // Note: we must do this AFTER inserting the answer record
            // because the answer id is used as the file's "itemid".
            if ($answeritemid) {
                $answertext = file_save_draft_area_files($answeritemid, $context->id, 'question', 'answer', $answer->id,
                        $this->fileoptions, $answertext);
                $DB->set_field('question_answers', 'answer', $answertext, array('id' => $answer->id));
            }
        }
        // Create $options for this ordering question.
        $options = (object)array(
            'questionid' => $question->id,
            'layouttype' => $question->layouttype,
            'selecttype' => $question->selecttype,
            'selectcount' => $question->selectcount,
            'gradingtype' => $question->gradingtype,
            'showgrading' => $question->showgrading,
            'answernumbering' => $question->answernumbering
        );
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $this->save_hints($question, false);

        // Add/update $options for this ordering question.
        if ($options->id = $DB->get_field('qtype_ordering_options', 'id', array('questionid' => $question->id))) {
            if (! $DB->update_record('qtype_ordering_options', $options)) {
                $result->error = get_string('cannotupdaterecord', 'error', 'qtype_ordering_options (id='.$options->id.')');
                return $result;
            }
        } else {
            unset($options->id);
            if (! $options->id = $DB->insert_record('qtype_ordering_options', $options)) {
                $result->error = get_string('cannotinsertrecord', 'error', 'qtype_ordering_options');
                return $result;
            }
        }

        // Delete old answer records, if any.
        if (count($answerids)) {
            $fs = get_file_storage();
            foreach ($answerids as $answerid) {
                $fs->delete_area_files($context->id, 'question', 'answer', $answerid);
                $DB->delete_records('question_answers', array('id' => $answerid));
            }
        }

        return true;
    }

    /**
     * This method should return all the possible types of response that are
     * recognised for this question.
     *
     * The question is modelled as comprising one or more subparts. For each
     * subpart, there are one or more classes that that students response
     * might fall into, each of those classes earning a certain score.
     *
     * For example, in a shortanswer question, there is only one subpart, the
     * text entry field. The response the student gave will be classified according
     * to which of the possible $question->options->answers it matches.
     *
     * For the matching question type, there will be one subpart for each
     * question stem, and for each stem, each of the possible choices is a class
     * of student's response.
     *
     * A response is an object with two fields, ->responseclass is a string
     * presentation of that response, and ->fraction, the credit for a response
     * in that class.
     *
     * Array keys have no specific meaning, but must be unique, and must be
     * the same if this function is called repeatedly.
     *
     * @param object $questiondata the question definition data.
     * @return array keys are subquestionid, values are arrays of possible
     *      responses to that subquestion.
     */
    public function get_possible_responses($questiondata) {
        $responseclasses = array();
        $itemcount = count($questiondata->options->answers);

        $position = 0;
        foreach ($questiondata->options->answers as $answerid => $answer) {
            $position += 1;
            $classes = array();
            for ($i = 1; $i <= $itemcount; $i++) {
                $classes[$i] = new question_possible_response(
                        get_string('positionx', 'qtype_ordering', $i),
                        ($i === $position) / $itemcount);
            }
            $responseclasses[question_utils::to_plain_text(
                    $answer->answer, $answer->answerformat)] = $classes;
        }

        return $responseclasses;
    }

    /**
     * Callback function for filtering answers with array_filter
     *
     * @param mixed $value
     * @return bool If true, this item should be saved.
     */
    public function is_not_blank($value) {
        if (is_array($value)) {
            $value = $value['text'];
        }
        $value = trim($value);
        return ($value || $value === '0');
    }

    /**
     * Loads the question type specific options for the question.
     *
     * This function loads any question type specific options for the
     * question from the database into the question object. This information
     * is placed in the $question->options field. A question type is
     * free, however, to decide on a internal structure of the options field.
     * @return bool            Indicates success or failure.
     * @param object $question The question object for the question. This object
     *                         should be updated to include the question type
     *                         specific information (it is passed by reference).
     */
    public function get_question_options($question) {
        global $DB, $OUTPUT;

        // Load the options.
        if (!$question->options = $DB->get_record('qtype_ordering_options', array('questionid' => $question->id))) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }

        // Load the answers - "fraction" is used to signify the order of the answers.
        if (!$question->options->answers = $DB->get_records('question_answers',
                array('question' => $question->id), 'fraction ASC')) {
            echo $OUTPUT->notification('Error: Missing question answers for ordering question ' . $question->id . '!');
            return false;
        }

        parent::get_question_options($question);
        return true;
    }

    /**
     * Deletes the question-type specific data when a question is deleted.
     *
     * @param int $questionid The id of question being deleted.
     * @param int $contextid the context this quesiotn belongs to.
     */
    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_ordering_options', array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

    /**
     * Import question from GIFT format
     *
     * @param array $lines
     * @param object $question
     * @param qformat_gift $format
     * @param string $extra (optional, default=null)
     * @return object Question instance
     */
    public function import_from_gift($lines, $question, $format, $extra=null) {
        global $CFG;
        require_once($CFG->dirroot.'/question/type/ordering/question.php');

        // Extract question info from GIFT file $lines.
        $questionname = '[^{]*';
        $selectcount = '\d+';
        $selecttype  = '(?:ALL|EXACT|'.
                          'RANDOM|REL|'.
                          'CONTIGUOUS|CONTIG)?';
        $layouttype  = '(?:HORIZONTAL|HORI|H|1|'.
                          'VERTICAL|VERT|V|0)?';
        $gradingtype = '(?:ALL_OR_NOTHING|'.
                          'ABSOLUTE_POSITION|'.
                          'ABSOLUTE|ABS|'.
                          'RELATIVE_NEXT_EXCLUDE_LAST|'.
                          'RELATIVE_NEXT_INCLUDE_LAST|'.
                          'RELATIVE_ONE_PREVIOUS_AND_NEXT|'.
                          'RELATIVE_ALL_PREVIOUS_AND_NEXT|'.
                          'RELATIVE_TO_CORRECT|'.
                          'RELATIVE|REL'.
                          'LONGEST_ORDERED_SUBSET|'.
                          'LONGEST_CONTIGUOUS_SUBSET)?';
        $showgrading = '(?:SHOW|TRUE|YES|1|HIDE|FALSE|NO|0)?';
        $search = '/^\s*>\s*('.$selectcount.')\s*'.
                           '('.$selecttype.')\s*'.
                           '('.$layouttype.')\s*'.
                           '('.$gradingtype.')\s*'.
                           '('.$showgrading.')\s*'.
                           '(.*?)\s*$/s';
        // Item $1 the number of items to be shown.
        // Item $2 the extraction/grading type.
        // Item $3 the layout type.
        // Item $4 the grading type.
        // Item $5 show the grading details (SHOW/HIDE).
        // Item $6 the lines of items to be ordered.
        if (! $extra) {
            return false; // Format not recognized.
        }
        if (! preg_match($search, $extra, $matches)) {
            return false; // Format not recognized.
        }

        $selectcount = trim($matches[1]);
        $selecttype = trim($matches[2]);
        $layouttype = trim($matches[3]);
        $gradingtype = trim($matches[4]);
        $showgrading = trim($matches[5]);

        $answers = preg_split('/[\r\n]+/', $matches[6]);
        $answers = array_filter($answers);

        if (empty($question)) {
            $text = implode(PHP_EOL, $lines);
            $text = trim($text);
            if ($pos = strpos($text, '{')) {
                $text = substr($text, 0, $pos);
            }

            // Extract name.
            $name = false;
            if (substr($text, 0, 2) == '::') {
                $text = substr($text, 2);
                $pos = strpos($text, '::');
                if (is_numeric($pos)) {
                    $name = substr($text, 0, $pos);
                    $name = $format->clean_question_name($name);
                    $text = trim(substr($text, $pos + 2));
                }
            }

            // Extract question text format.
            $format = FORMAT_MOODLE;
            if (substr($text, 0, 1) == '[') {
                $text = substr($text, 1);
                $pos = strpos($text, ']');
                if (is_numeric($pos)) {
                    $format = substr($text, 0, $pos);
                    switch ($format) {
                        case 'html':     $format = FORMAT_HTML;     break;
                        case 'plain':    $format = FORMAT_PLAIN;    break;
                        case 'markdown': $format = FORMAT_MARKDOWN; break;
                        case 'moodle':   $format = FORMAT_MOODLE;   break;
                    }
                    $text = trim(substr($text, $pos + 1)); // Remove name from text.
                }
            }

            $question = new stdClass();
            $question->name = $name;
            $question->questiontext = $text;
            $question->questiontextformat = $format;
            $question->generalfeedback = '';
            $question->generalfeedbackformat = FORMAT_MOODLE;
        }

        $question->qtype = 'ordering';

        // Set "selectcount" field from $selectcount.
        if (is_numeric($selectcount) && $selectcount > 2 && $selectcount <= count($answers)) {
            $selectcount = intval($selectcount);
        } else {
            $selectcount = min(6, count($answers));
        }
        $this->set_layout_select_count_grading($question, $layouttype, $selecttype, $selectcount, $gradingtype, $showgrading);

        // Remove blank items.
        $answers = array_map('trim', $answers);
        $answers = array_filter($answers); // Remove blanks.

        // Set up answer arrays.
        $question->answer = array();
        $question->answerformat = array();
        $question->fraction = array();
        $question->feedback = array();
        $question->feedbackformat = array();

        // Note that "fraction" field is used to denote sort order
        // "fraction" fields will be set to correct values later
        // in the save_question_options() method of this class.

        foreach ($answers as $i => $answer) {
            $question->answer[$i] = $answer;
            $question->answerformat[$i] = FORMAT_MOODLE;
            $question->fraction[$i] = 1; // Will be reset later in save_question_options().
            $question->feedback[$i] = '';
            $question->feedbackformat[$i] = FORMAT_MOODLE;
        }

        // Check that the required feedback fields exist.
        $this->check_ordering_combined_feedback($question);

        return $question;
    }

    /**
     * Check that the required feedback fields exist
     *
     * @param object $question
     */
    protected function check_ordering_combined_feedback(&$question) {
        foreach ($this->feedbackfields as $field) {
            if (empty($question->$field)) {
                $question->$field = array('text' => '', 'format' => FORMAT_MOODLE, 'itemid' => 0, 'files' => null);
            }
        }
    }

    /**
     * Given question object, returns array with array layouttype, selecttype, selectcount, gradingtype, showgrading
     * where layouttype, selecttype, gradingtype and showgrading are string representations.
     *
     * @param object $question
     * @return array(layouttype, selecttype, selectcount, gradingtype)
     */
    public function extract_layout_select_count_grading($question) {

        switch ($question->options->layouttype) {
            case qtype_ordering_question::LAYOUT_VERTICAL:
                $layout = 'VERTICAL';
                break;
            case qtype_ordering_question::LAYOUT_HORIZONTAL:
                $layout = 'HORIZONTAL';
                break;
            default:
                $layout = ''; // Shouldn't happen !!
        }

        switch ($question->options->selecttype) {
            case qtype_ordering_question::SELECT_ALL:
                $select = 'ALL';
                break;
            case qtype_ordering_question::SELECT_RANDOM:
                $select = 'RANDOM';
                break;
            case qtype_ordering_question::SELECT_CONTIGUOUS:
                $select = 'CONTIGUOUS';
                break;
            default:
                $select = ''; // Shouldn't happen !!
        }

        switch ($question->options->gradingtype) {
            case qtype_ordering_question::GRADING_ALL_OR_NOTHING:
                $grading = 'ALL_OR_NOTHING';
                break;
            case qtype_ordering_question::GRADING_ABSOLUTE_POSITION:
                $grading = 'ABSOLUTE_POSITION';
                break;
            case qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST:
                $grading = 'RELATIVE_NEXT_EXCLUDE_LAST';
                break;
            case qtype_ordering_question::GRADING_RELATIVE_NEXT_INCLUDE_LAST:
                $grading = 'RELATIVE_NEXT_INCLUDE_LAST';
                break;
            case qtype_ordering_question::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT:
                $grading = 'RELATIVE_ONE_PREVIOUS_AND_NEXT';
                break;
            case qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT:
                $grading = 'RELATIVE_ALL_PREVIOUS_AND_NEXT';
                break;
            case qtype_ordering_question::GRADING_LONGEST_ORDERED_SUBSET:
                $grading = 'LONGEST_ORDERED_SUBSET';
                break;
            case qtype_ordering_question::GRADING_LONGEST_CONTIGUOUS_SUBSET:
                $grading = 'LONGEST_CONTIGUOUS_SUBSET';
                break;
            case qtype_ordering_question::GRADING_RELATIVE_TO_CORRECT:
                $grading = 'RELATIVE_TO_CORRECT';
                break;
            default:
                $grading = ''; // Shouldn't happen !!
        }

        switch ($question->options->showgrading) {
            case 0:
                $show = 'HIDE';
                break;
            case 1:
                $show = 'SHOW';
                break;
            default:
                $show = ''; // Shouldn't happen !!
        }

        // Note: this used to be (selectcount + 2).
        $count = $question->options->selectcount;

        return array($layout, $select, $count, $grading, $show);
    }

    /**
     * Exports question to GIFT format
     *
     * @param object $question
     * @param qformat_gift $format
     * @param string $extra (optional, default=null)
     * @return string GIFT representation of question
     */
    public function export_to_gift($question, $format, $extra=null) {
        global $CFG;
        require_once($CFG->dirroot.'/question/type/ordering/question.php');

        $output = '';

        if ($question->name) {
            $output .= '::'.$question->name.'::';
        }

        switch ($question->questiontextformat) {
            case FORMAT_HTML:     $output .= '[html]';     break;
            case FORMAT_PLAIN:    $output .= '[plain]';    break;
            case FORMAT_MARKDOWN: $output .= '[markdown]'; break;
            case FORMAT_MOODLE:   $output .= '[moodle]';   break;
        }

        $output .= $question->questiontext.'{';

        list($layouttype, $selecttype, $selectcount, $gradingtype, $showgrading) =
                $this->extract_layout_select_count_grading($question);
        $output .= ">$selectcount $selecttype $layouttype $gradingtype $showgrading".PHP_EOL;

        foreach ($question->options->answers as $answer) {
            $output .= $answer->answer.PHP_EOL;
        }

        $output .= '}';
        return $output;
    }

    /**
     * Exports question to XML format
     *
     * @param object $question
     * @param qformat_xml $format
     * @param string $extra (optional, default=null)
     * @return string XML representation of question
     */
    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        global $CFG;
        require_once($CFG->dirroot.'/question/type/ordering/question.php');

        list($layouttype, $selecttype, $selectcount, $gradingtype, $showgrading) =
                $this->extract_layout_select_count_grading($question);

        $output = '';
        $output .= "    <layouttype>$layouttype</layouttype>\n";
        $output .= "    <selecttype>$selecttype</selecttype>\n";
        $output .= "    <selectcount>$selectcount</selectcount>\n";
        $output .= "    <gradingtype>$gradingtype</gradingtype>\n";
        $output .= "    <showgrading>$showgrading</showgrading>\n";
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);

        foreach ($question->options->answers as $answer) {
            $output .= '    <answer fraction="'.$answer->fraction.'" '.$format->format($answer->answerformat).">\n";
            $output .= $format->writetext($answer->answer, 3);
            $output .= $format->write_files($answer->answerfiles);
            if ($feedback = trim($answer->feedback)) { // Usually there is no feedback.
                $output .= '      <feedback '.$format->format($answer->feedbackformat).">\n";
                $output .= $format->writetext($answer->feedback, 4);
                $output .= $format->write_files($answer->feedbackfiles);
                $output .= "      </feedback>\n";
            }
            $output .= "    </answer>\n";
        }

        return $output;
    }

    /**
     * Imports question from the Moodle XML format
     *
     * Imports question using information from extra_question_fields function
     * If some of you fields contains id's you'll need to reimplement this
     *
     * @param array $data
     * @param qtype_ordering $question (or null)
     * @param qformat_xml $format
     * @param string $extra (optional, default=null)
     * @return object New question object
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        global $CFG;
        require_once($CFG->dirroot.'/question/type/ordering/question.php');

        $questiontype = $format->getpath($data, array('@', 'type'), '');

        if ($questiontype != 'ordering') {
            return false;
        }

        $newquestion = $format->import_headers($data);
        $newquestion->qtype = $questiontype;

        // Fix empty or long question name.
        $newquestion->name = $this->fix_questionname($newquestion->name, $newquestion->questiontext);

        // Extra fields - "selecttype" and "selectcount"
        // (these fields used to be called "logical" and "studentsee").
        if (isset($data['#']['selecttype'])) {
            $selecttype = 'selecttype';
            $selectcount = 'selectcount';
        } else {
            $selecttype = 'logical';
            $selectcount = 'studentsee';
        }
        $layouttype = $format->getpath($data, array('#', 'layouttype', 0, '#'), 'VERTICAL');
        $selecttype = $format->getpath($data, array('#', $selecttype, 0, '#'), 'RANDOM');
        $selectcount = $format->getpath($data, array('#', $selectcount, 0, '#'), 6);
        $gradingtype = $format->getpath($data, array('#', 'gradingtype', 0, '#'), 'RELATIVE');
        $showgrading = $format->getpath($data, array('#', 'showgrading', 0, '#'), '1');
        $this->set_layout_select_count_grading($newquestion, $layouttype, $selecttype, $selectcount, $gradingtype, $showgrading);

        $newquestion->answer = array();
        $newquestion->answerformat = array();
        $newquestion->fraction = array();
        $newquestion->feedback = array();
        $newquestion->feedbackformat = array();

        $i = 0;
        while ($answer = $format->getpath($data, array('#', 'answer', $i), '')) {
            $ans = $format->import_answer($answer, true, $format->get_format($newquestion->questiontextformat));
            $newquestion->answer[$i] = $ans->answer;
            $newquestion->fraction[$i] = 1; // Will be reset later in save_question_options().
            $newquestion->feedback[$i] = $ans->feedback;
            $i++;
        }

        $format->import_combined_feedback($newquestion, $data, false);
        // Check that the required feedback fields exist.
        $this->check_ordering_combined_feedback($newquestion);

        $format->import_hints($newquestion, $data, false);

        return $newquestion;
    }

    /**
     * Fix empty or long question name
     *
     * @param string $name
     * @param string $defaultname (optional, default='')
     * @param integer $maxnamelength (optional, default=42)
     * @return string Fixed name
     */
    public function fix_questionname($name, $defaultname='', $maxnamelength = 42) {
        if (trim($name) == '') {
            if ($defaultname) {
                $name = $defaultname;
            } else {
                $name = get_string('defaultquestionname', 'qtype_ordering');
            }
        }
        if (strlen($name) > $maxnamelength) {
            $name = substr($name, 0, $maxnamelength);
            if ($pos = strrpos($name, ' ')) {
                $name = substr($name, 0, $pos);
            }
            $name .= ' ...';
        }
        return $name;
    }

    /**
     * Set layouttype, selecttype, selectcount, gradingtype, showgrading based on their textual representation
     *
     * @param object $question (passed by reference)
     * @param string $layout the layout type
     * @param string $select the select type
     * @param string $count the number of items to display
     * @param string $grading the grading type
     * @param string $show the grading details or not
     */
    public function set_layout_select_count_grading(&$question, $layout, $select, $count, $grading, $show) {

        // Set default values.
        $layouttype  = qtype_ordering_question::LAYOUT_VERTICAL;
        $selecttype  = qtype_ordering_question::SELECT_RANDOM;
        $selectcount = 3;
        $gradingtype = qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST;
        $showgrading = 1;

        switch (strtoupper($layout)) {

            case 'HORIZONTAL':
            case 'HORI':
            case 'H':
            case '1':
                $layouttype = qtype_ordering_question::LAYOUT_HORIZONTAL;
                break;

            case 'VERTICAL':
            case 'VERT':
            case 'V':
            case '0':
                $layouttype = qtype_ordering_question::LAYOUT_VERTICAL;
                break;
        }

        // Set "selecttype" from $select.
        switch (strtoupper($select)) {
            case 'ALL':
            case 'EXACT':
                $selecttype = qtype_ordering_question::SELECT_ALL;
                break;
            case 'RANDOM':
            case 'REL':
                $selecttype = qtype_ordering_question::SELECT_RANDOM;
                break;
            case 'CONTIGUOUS':
            case 'CONTIG':
                $selecttype = qtype_ordering_question::SELECT_CONTIGUOUS;
                break;
        }

        // Set "selectcount" from $count
        // this used to be ($count - 2).
        if (is_numeric($count)) {
            $selectcount = intval($count);
        }

        // Set "gradingtype" from $grading.
        switch (strtoupper($grading)) {
            case 'ALL_OR_NOTHING':
                $gradingtype = qtype_ordering_question::GRADING_ALL_OR_NOTHING;
                break;
            case 'ABS':
            case 'ABSOLUTE':
            case 'ABSOLUTE_POSITION':
                $gradingtype = qtype_ordering_question::GRADING_ABSOLUTE_POSITION;
                break;
            case 'REL':
            case 'RELATIVE':
            case 'RELATIVE_NEXT_EXCLUDE_LAST':
                $gradingtype = qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST;
                break;
            case 'RELATIVE_NEXT_INCLUDE_LAST':
                $gradingtype = qtype_ordering_question::GRADING_RELATIVE_NEXT_INCLUDE_LAST;
                break;
            case 'RELATIVE_ONE_PREVIOUS_AND_NEXT':
                $gradingtype = qtype_ordering_question::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT;
                break;
            case 'RELATIVE_ALL_PREVIOUS_AND_NEXT':
                $gradingtype = qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT;
                break;
            case 'LONGEST_ORDERED_SUBSET':
                $gradingtype = qtype_ordering_question::GRADING_LONGEST_ORDERED_SUBSET;
                break;
            case 'LONGEST_CONTIGUOUS_SUBSET':
                $gradingtype = qtype_ordering_question::GRADING_LONGEST_CONTIGUOUS_SUBSET;
                break;
            case 'RELATIVE_TO_CORRECT':
                $gradingtype = qtype_ordering_question::GRADING_RELATIVE_TO_CORRECT;
                break;
        }

        // Set "showgrading" from $show.
        switch (strtoupper($show)) {
            case 'SHOW':
            case 'TRUE':
            case 'YES':
                $showgrading = 1;
                break;
            case 'HIDE':
            case 'FALSE':
            case 'NO':
                $showgrading = 0;
                break;
        }

        $question->layouttype  = $layouttype;
        $question->selecttype  = $selecttype;
        $question->selectcount = $selectcount;
        $question->gradingtype = $gradingtype;
        $question->showgrading = $showgrading;
    }

    /**
     * Return the answer numbering style
     * @param $questiondata
     * @return string
     */
    public function get_answernumbering($questiondata) {
        return $questiondata->options->answernumbering;
    }

    /**
     * @return array of the numbering styles supported. For each one, there
     *      should be a lang string answernumberingxxx in the qtype_ordering
     *      language file, and a case in the switch statement in number_in_style,
     *      and it should be listed in the definition of this column in install.xml.
     */
    public static function get_numbering_styles() {
        $styles = [];
        $numberingoptions = ['abc', 'ABCD', '123', 'iii', 'IIII', \qtype_ordering_question::ANSWER_NUMBERING_DEFAULT];
        foreach ($numberingoptions as $numberingoption) {
            $styles[$numberingoption] =
                    get_string('answernumbering' . $numberingoption, 'qtype_ordering');
        }
        return $styles;
    }
}
