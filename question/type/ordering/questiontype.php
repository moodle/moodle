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
    $register_questiontype = false;
} else {
    $register_questiontype = true; // Moodle 2.0
    require_once(__DIR__.'/legacy/20.php');
}

/**
 * The ordering question type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering extends question_type {

    public function is_not_blank($value) {
        $value = trim($value);
        return ($value || $value==='0');
    }

    public function save_question_options($question) {
        global $DB;

        $result = new stdClass();

        // remove empty answers
        $question->answer = array_filter($question->answer, array($this, 'is_not_blank'));
        $question->answer = array_values($question->answer); // make keys sequential

        // count how many answers we have
        $countanswers = count($question->answer);

        // check at least two answers exist
        if ($countanswers < 2) {
            $result->notice = get_string('ordering_notenoughanswers', 'qtype_ordering', '2');
            return $result;
        }

        $question->feedback = range(1, $countanswers);

        if ($answerids = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC', 'id,question')) {
            $answerids = array_keys($answerids);
        } else {
            $answerids = array();
        }

        // Insert all the new answers

        foreach ($question->answer as $i => $text) {
            $answer = (object)array(
                'question' => $question->id,
                'fraction' => ($i + 1), // start at 1
                'answer'   => $text,
                'answerformat' => FORMAT_MOODLE, // =0
                'feedback' => '',
                'feedbackformat' => FORMAT_MOODLE, // =0
            );

            if ($answer->id = array_shift($answerids)) {
                if (! $DB->update_record('question_answers', $answer)) {
                    $result->error = get_string('cannotupdaterecord', 'error', 'question_answers (id='.$answer->id.')');
                    return $result;
                }
            } else {
                if (! $answer->id = $DB->insert_record('question_answers', $answer)) {
                    $result->error = get_string('cannotinsertrecord', 'error', 'question_answers');
                    return $result;
                }
            }
        }

        // create $options for this ordering question
        $options = (object)array(
            'question' => $question->id,
            'logical' => $question->logical,
            'studentsee' => $question->studentsee,
            'correctfeedback' => $question->correctfeedback,
            'incorrectfeedback' => $question->incorrectfeedback,
            'partiallycorrectfeedback' => $question->partiallycorrectfeedback
        );

        // add/update $options for this ordering question
        if ($options->id = $DB->get_field('question_ordering', 'id', array('question' => $question->id))) {
            if (! $DB->update_record('question_ordering', $options)) {
                $result->error = get_string('cannotupdaterecord', 'error', 'question_ordering (id='.$options->id.')');
                return $result;
            }
        } else {
            if (! $options->id = $DB->insert_record('question_ordering', $options)) {
                $result->error = get_string('cannotinsertrecord', 'error', 'question_ordering');
                return $result;
            }
        }

        // delete old answer records, if any
        if (count($answerids)) {
            $DB->delete_records_list('question_answers', 'id', $answerids);
        }

        return true;
    }

    public function get_question_options($question) {
        global $DB, $OUTPUT;

        // load the options
        if (! $question->options = $DB->get_record('question_ordering', array('question' => $question->id))) {
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

    // following seems to be unnecessary ...
    // initialise_question_instance(question_definition $question, $questiondata)

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_ordering', array('question' => $questionid));
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
    function import_from_gift($lines, $question, $format, $extra=null) {

        // convert $lines to a single a string - for preg_match()
        $lines = implode(PHP_EOL, $lines);

        // extract question info from GIFT file $lines
        $search = '/^([^{]*)\s*\{>\s*(\d+)\s*((?:ALL|EXACT|RANDOM|REL|CONTIGUOUS|CONTIG)?)\s*(.*?)\s*\}\s*$/s';
        // $1 the question name
        // $2 the number of items to be shown
        // $3 the extraction type
        // $4 the lines of items to be ordered
        if (empty($extra) || ! preg_match($search, $lines, $matches)) {
            return false; // format not recognized
        }

        $questionname = trim($matches[1]);
        $numberofitems = trim($matches[2]);
        $extractiontype = trim($matches[3]);
        $lines = explode(PHP_EOL, $matches[4]);
        unset($matches);

        $question->qtype = 'ordering';
        $question->name = trim($question->name);

        // fix empty or long question name
        $question->name = $this->fix_questionname($question->name, $questionname);

        // set "studentsee" field from $numberofitems
        if (is_numeric($numberofitems) && $numberofitems > 2 && $numberofitems <= count($lines)) {
            $numberofitems = intval($numberofitems);
        } else {
            $numberofitems = min(6, count($lines));
        }
        $this->set_num_and_type($question, $numberofitems, $extractiontype);

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

        return $question;
    }

    /**
     * extract_num_and_type
     *
     * @param stdClass      $question
     * @todo Finish documenting this function
     */
    function extract_num_and_type($question) {
        switch ($question->options->logical) {
            case 0:  $type = 'EXACT';  break; // all
            case 1:  $type = 'REL';    break; // random subset
            case 2:  $type = 'CONTIG'; break; // contiguous subset
            default: $type = ''; // shouldn't happen !!
        }
        $num = $question->options->studentsee + 2;
        return array($num, $type);
    }

    /**
     * export_to_gift
     *
     * @param stdClass      $question
     * @param qformat_gift $format
     * @param string        $extra (optional, default=null)
     * @todo Finish documenting this function
     */
    function export_to_gift($question, $format, $extra=null) {
        list($num, $type) = $this->extract_num_and_type($question);

        $expout = $question->questiontext.'{>'.$num.' '.$type.' '."\n";
        foreach ($question->options->answers as $answer) {
            $expout .= $answer->answer."\n";
        }
        $expout .= '}';

        return $expout;
    }

    /**
     * export_to_xml
     *
     * @param stdClass    $question
     * @param qformat_xml $format
     * @param string      $extra (optional, default=null)
     * @todo Finish documenting this function
     */
    function export_to_xml($question, qformat_xml $format, $extra=null) {

        list($num, $type) = $this->extract_num_and_type($question);

        $output = '';
        $output .= "    <logical>$type</logical>\n";
        $output .= "    <studentsee>$num</studentsee>\n";

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

        // extra fields fields fields - "logical" and "studentsee"
        $numberofitems = $format->getpath($data, array('#', 'studentsee', 0, '#'), 6);
        $extractiontype = $format->getpath($data, array('#', 'logical', 0, '#'), 'RANDOM');
        $this->set_num_and_type($newquestion, $numberofitems, $extractiontype);

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
     * set_num_and_type
     *
     * @param object $question (passed by reference)
     * @param integer $num the number of items to display
     * @param integer $type the extraction type
     * @param integer $default_type (optional, default=1)
     */
    function set_num_and_type(&$question, $num, $type, $default_type=1) {

        // set "studentsee" from $num(ber of items)
        $question->studentsee = ($num - 2);

        // set "logical" from (extraction) $type
        switch ($type) {
            case 'ALL':
            case 'EXACT':
                $question->logical = 0;
                break;

            case 'RANDOM':
            case 'REL':
                $question->logical = 1;
                break;

            case 'CONTIGUOUS':
            case 'CONTIG':
                $question->logical = 2;
                break;

            // otherwise
            default:
                $question->logical = $default_type;
        }
    }
}

if ($register_questiontype) {
    class question_ordering_qtype extends qtype_ordering {
        function name() {
            return 'ordering';
        }
    }
    question_register_questiontype(new question_ordering_qtype());
}
