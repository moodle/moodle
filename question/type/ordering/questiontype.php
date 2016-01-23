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
 * @subpackage ordering
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (class_exists('question_type')) {
    $register_ordering_questiontype = false;
} else {
    $register_ordering_questiontype = true; // Moodle 2.0
    require_once($CFG->dirroot.'/question/type/ordering/legacy/questiontypebase.php');
}

/**
 * The ORDERING question type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering extends question_type {

    /** combined feedback fields */
    public $feedback_fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');

    /**
     * Utility method used by {@link qtype_renderer::head_code()}
     * It looks for any of the files script.js or script.php that
     * exist in the plugin folder and ensures they get included.
     * It also includes the jquery files required for this plugin
     */
    public function find_standard_scripts() {
        global $CFG, $PAGE;

        // include "script.js" and/or "script.php" in the normal way
        parent::find_standard_scripts();

        $version = '';
        $min_version = '1.11.0'; // Moodle 2.7
        $search = '/jquery-([0-9.]+)(\.min)?\.js$/';

        // make sure jQuery version is high enough
        // (required if Quiz is in a popup window)
        //     Moodle 2.5 has jQuery 1.9.1
        //     Moodle 2.6 has jQuery 1.10.2
        //     Moodle 2.7 has jQuery 1.11.0
        //     Moodle 2.8 has jQuery 1.11.1
        //     Moodle 2.9 has jQuery 1.11.1
        if (method_exists($PAGE->requires, 'jquery')) {
            // Moodle >= 2.5
            if ($version=='') {
                include($CFG->dirroot.'/lib/jquery/plugins.php');
                if (isset($plugins['jquery']['files'][0])) {
                    if (preg_match($search, $plugins['jquery']['files'][0], $matches)) {
                        $version = $matches[1];
                    }
                }
            }
            if ($version=='') {
                $filename = $CFG->dirroot.'/lib/jquery/jquery*.js';
                foreach (glob($filename) as $filename) {
                    if (preg_match($search, $filename, $matches)) {
                        $version = $matches[1];
                        break;
                    }
                }
            }
            if (version_compare($version, $min_version) < 0) {
                $version = '';
            }
        }

        // include jquery files
        if ($version) {
            // Moodle >= 2.7
            $PAGE->requires->jquery();
            $PAGE->requires->jquery_plugin('ui');
            $PAGE->requires->jquery_plugin('ui.touch-punch', 'qtype_ordering');
        } else {
            // Moodle <= 2.6
            $jquery = '/question/type/' . $this->name().'/jquery';
            $PAGE->requires->js($jquery.'/jquery.js', true);
            $PAGE->requires->js($jquery.'/jquery-ui.js', true);
            $PAGE->requires->js($jquery.'/jquery-ui.touch-punch.js', true);
        }
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_ordering_feedback($question, $questiondata);
    }

    public function save_question_options($question) {
        global $DB;

        $result = new stdClass();
        $context = $question->context;
        //$context = $this->get_context_by_category_id($question->category);

        // remove empty answers
        $question->answer = array_filter($question->answer, array($this, 'is_not_blank'));
        $question->answer = array_values($question->answer); // make keys sequential

        // count how many answers we have
        $countanswers = count($question->answer);

        // search/replace strings to reduce simple <p>...</p> to plain text
        $p_search = '/^\s*<p>\s*(.*?)(\s*<br\s*\/?>)*\s*<\/p>\s*$/';
        $p_replace = '$1';

        // search/replace strings to standardize vertical align of <img> tags
        $img_search = '/(<img[^>]*)\bvertical-align:\s*[a-zA-Z0-9_-]+([^>]*>)/';
        $img_replace = '$1'.'vertical-align:text-top'.'$2';

        // check at least two answers exist
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

        // Insert all the new answers
        foreach ($question->answer as $i => $answer) {

            // extract $answer fields
            if (is_array($answer)) {
                // editor
                $answertext   = $answer['text'];
                $answerformat = $answer['format'];
                $answeritemid = $answer['itemid'];
            } else {
                // textarea
                $answertext   = $answer;
                $answerformat = FORMAT_MOODLE;
                $answeritemid = 0; // i.e. no editor
            }

            // reduce simple <p>...</p> to plain text
            if (substr_count($answertext, '<p>')==1) {
                $answertext = preg_replace($p_search, $p_replace, $answertext);
            }
            $answertext = trim($answertext);

            // skip empty answers
            if ($answertext=='') {
                continue;
            }

            // standardize vertical align of img tags
            $answertext = preg_replace($img_search, $img_replace, $answertext);

            // prepare the $answer object
            $answer = (object)array(
                'question'       => $question->id,
                'fraction'       => ($i + 1), // start at 1
                'answer'         => $answertext,
                'answerformat'   => $answerformat,
                'feedback'       => '',
                'feedbackformat' => FORMAT_MOODLE,
            );

            // add/insert $answer into the database
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

            // copy files across from draft files area
            // Note: we must do this AFTER inserting the answer record
            //       because the answer id is used as the file's "itemid"
            if ($answeritemid) {
                $answertext = file_save_draft_area_files($answeritemid, $context->id, 'question', 'answer', $answer->id, $this->fileoptions, $answertext);
                $DB->set_field('question_answers', 'answer', $answertext, array('id' => $answer->id));
            }
        }

        // create $options for this ordering question
        $options = (object)array(
            'questionid' => $question->id,
            'layouttype' => $question->layouttype,
            'selecttype' => $question->selecttype,
            'selectcount' => $question->selectcount,
            'gradingtype' => $question->gradingtype
        );
        $options = $this->save_ordering_feedback_helper($options, $question, $context, true);

        // add/update $options for this ordering question
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

        // delete old answer records, if any
        if (count($answerids)) {
            $fs = get_file_storage();
            foreach ($answerids as $answerid) {
                $fs->delete_area_files($context->id, 'question', 'answer', $answerid);
                $DB->delete_records('question_answers', array('id' => $answerid));
            }
        }

        return true;
    }

    protected function initialise_ordering_feedback($question, $questiondata, $shownumcorrect=false) {
        if (method_exists($this, 'initialise_combined_feedback')) {
            // Moodle >= 2.1
            $options = $this->initialise_combined_feedback($question, $questiondata, $shownumcorrect);
        } else {
            // Moodle 2.0
            foreach ($this->feedback_fields as $field) {
                $format = $field.'format';
                $question->$field = $questiondata->options->$field;
                $question->$format = $questiondata->options->$format;
            }
            if ($shownumcorrect) {
                $question->shownumcorrect = $questiondata->options->shownumcorrect;
            }
        }
        return $options;
    }

    public function get_possible_responses($questiondata) {
        $responses = array();
        $question = $this->make_question($questiondata);
        foreach ($question->correctresponse as $position => $answerid) {
            $responses[] = $position.': '.$question->answers[$answerid]->answer;
        }
        $responses = array(
            0 => question_possible_response::no_response(),
            1 => implode(', ', $responses)
        );
        return ;
    }

    protected function save_ordering_feedback_helper($options, $question, $context, $shownumcorrect=false) {
        if (method_exists($this, 'save_combined_feedback_helper')) {
            // Moodle >= 2.1
            $options = $this->save_combined_feedback_helper($options, $question, $context, $shownumcorrect);
        } else {
            // Moodle 2.0
            foreach ($this->feedback_fields as $field) {
                $text = $question->$field;
                $format = $field.'format';
                $options->$field = $this->import_or_save_files($text, $context, 'qtype_ordering', $field, $question->id);
                $options->$format = $text['format'];
            }
            if ($shownumcorrect) {
                $options->shownumcorrect = (isset($question->shownumcorrect) && $question->shownumcorrect);
            }
        }
        return $options;
    }

    public function is_not_blank($value) {
        if (is_array($value)) {
            $value = $value['text'];
        }
        $value = trim($value);
        return ($value || $value==='0');
    }

    public function get_question_options($question) {
        global $DB, $OUTPUT;

        // load the options
        if (! $question->options = $DB->get_record('qtype_ordering_options', array('questionid' => $question->id))) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }

        // Load the answers - "fraction" is used to signify the order of the answers
        if (! $question->options->answers = $DB->get_records('question_answers', array('question' => $question->id), 'fraction ASC')) {
            echo $OUTPUT->notification('Error: Missing question answers for ordering question ' . $question->id . '!');
            return false;
        }

        //parent::get_question_options($question);
        return true;
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_ordering_options', array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

    /**
     * import_from_gift
     *
     * @param array         $data
     * @param stdClass      $question
     * @param qformat_gift $format
     * @param string        $extra (optional, default=null)
     * @todo Finish documenting this function
     */
    public function import_from_gift($lines, $question, $format, $extra=null) {
        // convert $lines to a single a string - for preg_match()
        $lines = implode(PHP_EOL, $lines);

        // extract question info from GIFT file $lines
        $search = '/^([^{]*)\s*\{>\s*(\d+)\s*((?:ALL|EXACT|RANDOM|REL|CONTIGUOUS|CONTIG)?)\s*((?:HORIZONTAL|HORI|H|1|VERTICAL|VERT|V|0)?)\s*((?:ABSOLUTE|ABS||RELATIVE|REL)?)\s*(.*?)\s*\}\s*$/s';
        // $1 the question name
        // $2 the number of items to be shown
        // $3 the extraction/grading type
        // $4 the layout type
        // $5 the grading type
        // $6 the lines of items to be ordered
        if (empty($extra) || ! preg_match($search, $lines, $matches)) {
            return false; // format not recognized
        }

        $questionname = trim($matches[1]);
        $selectcount = trim($matches[2]);
        $selecttype = trim($matches[3]);
        $layouttype = trim($matches[4]);
        $gradingtype = trim($matches[5]);
        $lines = explode(PHP_EOL, $matches[6]);
        unset($matches);

        $question->qtype = 'ordering';
        $question->name = trim($question->name);

        // fix empty or long question name
        $question->name = $this->fix_questionname($question->name, $questionname);

        // set "selectcount" field from $selectcount
        if (is_numeric($selectcount) && $selectcount > 2 && $selectcount <= count($lines)) {
            $selectcount = intval($selectcount);
        } else {
            $selectcount = min(6, count($lines));
        }
        $this->set_layout_count_select_grading($question, $layouttype, $selectcount, $selecttype, $gradingtype);

        // remove blank items
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines); // remove blanks

        // set up answer arrays
        $question->answer = array();
        $question->answerformat = array();
        $question->fraction = array();
        $question->feedback = array();
        $question->feedbackformat = array();

        // Note that "fraction" field is used to denote sort order
        // "fraction" fields will be set to correct values later
        // in the save_question_options() method of this class

        foreach ($lines as $i => $line) {
            $question->answer[$i] = $line;
            $question->answerformat[$i] = FORMAT_MOODLE; // =0
            $question->fraction[$i] = 1; // will be reset later in save_question_options()
            $question->feedback[$i] = '';
            $question->feedbackformat[$i] = FORMAT_MOODLE; // =0
        }

        // check that the required feedback fields exist
        $this->check_ordering_combined_feedback($question);

        return $question;
    }

    /**
     * check_ordering_combined_feedback
     *
     * @param stdClass $question (passed by reference)
     * @todo Finish documenting this function
     */
    protected function check_ordering_combined_feedback(&$question) {
        foreach ($this->feedback_fields as $field) {
            if (empty($question->$field)) {
                $question->$field = array('text' => '', 'format' => 0, 'itemid' => 0, 'files' => null);
            }
        }
    }

    /**
     * extract_layout_count_select_grading
     *
     * @param stdClass $question
     * @todo Finish documenting this function
     */
    public function extract_layout_count_select_grading($question) {

        switch ($question->options->layouttype) {
            case 0:  $layout = 'VERTICAL'; break;
            case 1:  $layout = 'HORIZONTAL'; break;
            default: $layout = ''; // shouldn't happen !!
        }

        switch ($question->options->selecttype) {
            case 0:  $select = 'ALL';        break; // all items
            case 1:  $select = 'RANDOM';     break; // random subset
            case 2:  $select = 'CONTIGUOUS'; break; // contiguous subset
            default: $select = '';                  // shouldn't happen !!
        }

        switch ($question->options->gradingtype) {
            case 0:  $grading = 'ABSOLUTE'; break; // absolute position
            case 1:  $grading = 'RELATIVE'; break; // relative position
            default: $grading = '';                // shouldn't happen !!
        }

        // Note: this used to be (selectcount + 2)
        $count = $question->options->selectcount;

        return array($layout, $count, $select, $grading);
    }

    /**
     * export_to_gift
     *
     * @param stdClass      $question
     * @param qformat_gift $format
     * @param string        $extra (optional, default=null)
     * @todo Finish documenting this function
     */
    public function export_to_gift($question, $format, $extra=null) {
        list($layouttype, $selectcount, $selecttype, $gradingtype) = $this->extract_layout_count_select_grading($question);
        $output = $question->questiontext.'{>'.$selectcount.' '.
                                               $selecttype.' '.
                                               $layouttype.' '.
                                               $gradingtype."\n";
        foreach ($question->options->answers as $answer) {
            $output .= $answer->answer."\n";
        }
        $output .= '}';
        return $output;
    }

    /**
     * export_to_xml
     *
     * @param stdClass    $question
     * @param qformat_xml $format
     * @param string      $extra (optional, default=null)
     * @todo Finish documenting this function
     */
    public function export_to_xml($question, qformat_xml $format, $extra=null) {

        list($layouttype, $selectcount, $selecttype, $gradingtype) = $this->extract_layout_count_select_grading($question);

        $output = '';
        $output .= "    <layouttype>$layouttype</layouttype>\n";
        $output .= "    <selecttype>$selecttype</selecttype>\n";
        $output .= "    <selectcount>$selectcount</selectcount>\n";
        $output .= "    <gradingtype>$gradingtype</gradingtype>\n";

        foreach($question->options->answers as $answer) {
            $output .= '    <answer fraction="'.$answer->fraction.'" '.$format->format($answer->answerformat).">\n";
            $output .= $format->writetext($answer->answer, 3);
            if ($feedback = trim($answer->feedback)) { // usually there is no feedback
                $output .= '      <feedback '.$format->format($answer->feedbackformat).">\n";
                $output .= $format->writetext($answer->feedback, 4);
                $output .= "      </feedback>\n";
            }
            $output .= "    </answer>\n";
        }

        return $output;
    }

    /*
     * Imports question from the Moodle XML format
     *
     * Imports question using information from extra_question_fields function
     * If some of you fields contains id's you'll need to reimplement this
     *
     * @param array          $data
     * @param qtype_ordering $question (or null)
     * @param qformat_xml    $format
     * @param string         $extra (optional, default=null)
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {

        $question_type = $format->getpath($data, array('@', 'type'), '');

        if ($question_type != 'ordering') {
            return false;
        }

        $newquestion = $format->import_headers($data);
        $newquestion->qtype = $question_type;

        // fix empty or long question name
        $newquestion->name = $this->fix_questionname($newquestion->name, $newquestion->questiontext);

        // extra fields fields fields - "selecttype" and "selectcount"
        // (these fields used to be called "logical" and "studentsee")
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
        $this->set_layout_count_select_grading($newquestion, $layouttype, $selectcount, $selecttype, $gradingtype);

        $newquestion->answer = array();
        $newquestion->answerformat = array();
        $newquestion->fraction = array();
        $newquestion->feedback = array();
        $newquestion->feedbackformat = array();

        $i = 0;
        while ($answer = $format->getpath($data, array('#', 'answer', $i), '')) {
            if ($text = $format->getpath($answer, array('#', 'text', 0, '#'), '')) {
                $newquestion->answer[] = $text;
                $answerformat = $format->getpath($answer, array('@', 'format'), 'moodle_auto_format');
                $newquestion->answerformat[] = $format->trans_format($answerformat);
                $newquestion->fraction[] = 1; // will be reset later in save_question_options()
                $newquestion->feedback[] = $format->getpath($answer, array('#', 'feedback', 0, '#', 'text', 0, '#'), '');
                $feedbackformat = $format->getpath($answer, array('#', 'format', 0, '@', 'format'), 'moodle_auto_format');
                $newquestion->feedbackformat[] = $format->trans_format($feedbackformat);
            }
            $i++;
        }

        // check that the required feedback fields exist
        $this->check_ordering_combined_feedback($newquestion);

        return $newquestion;
    }

    /*
     * fix_questionname
     *
     * @param string $name
     * @param string $defaultname (optional, default='')
     * @param integer $maxnamelength (optional, default=42)
     */
    public function fix_questionname($name, $defaultname='', $maxnamelength = 42) {
        if (trim($name)=='') {
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

    /*
     * set_layout_count_select_grading
     *
     * @param object $question (passed by reference)
     * @param integer $layout the layout type
     * @param integer $count the number of items to display
     * @param integer $type the extraction type
     * @param integer $default_select (optional, default=1)
     */
    public function set_layout_count_select_grading(&$question, $layout, $count, $select, $grading) {

        $default_layout  = 0; // horizontal
        $default_select  = 1; // random
        $default_count   = 3;
        $default_grading = 1; // relative

        switch (strtoupper($layout)) {
            case 'HORIZONTAL':
            case 'HORI':
            case 'H':
            case '1':
                $question->layouttype = 1;
                break;
            case 'VERTICAL':
            case 'VERT':
            case 'V':
            case '0':
                $question->layouttype = 0;
                break;
            default:
                $question->layouttype = $default_layout;
        }


        // set "selectcount" from $count
        // this used to be ($count - 2)
        if (is_numeric($count)) {
            $question->selectcount = intval($count);
        } else {
            $question->selectcount = $default_count;
        }

        // set "selecttype" from $select
        switch (strtoupper($select)) {
            case 'ALL':
            case 'EXACT':
                $question->selecttype = 0;
                break;
            case 'RANDOM':
            case 'REL':
                $question->selecttype = 1;
                break;
            case 'CONTIGUOUS':
            case 'CONTIG':
                $question->selecttype = 2;
                break;
            default:
                $question->selecttype = $default_select;
        }

        // set "gradingtype" from $grading
        switch (strtoupper($grading)) {
            case 'ABS':
            case 'ABSOLUTE':
                $question->gradingtype = 0;
                break;
            case 'REL':
            case 'RELATIVE':
                $question->gradingtype = 1;
                break;
            default:
                $question->gradingtype = $default_grading;
        }
    }
}

if ($register_ordering_questiontype) {
    class question_ordering_qtype extends qtype_ordering {
    }
    question_register_questiontype(new question_ordering_qtype());
}
unset($register_ordering_questiontype);
