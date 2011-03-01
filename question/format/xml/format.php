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
 * Moodle XML question importer.
 *
 * @package qformat
 * @subpackage qformat_xml
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Importer for Moodle XML question format.
 *
 * See http://docs.moodle.org/en/Moodle_XML_format for a description of the format.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . '/xmlize.php');

class qformat_xml extends qformat_default {

    function provide_import() {
        return true;
    }

    function provide_export() {
        return true;
    }

    function mime_type() {
        return 'application/xml';
    }

    // IMPORT FUNCTIONS START HERE

    /**
     * Translate human readable format name
     * into internal Moodle code number
     * @param string name format name from xml file
     * @return int Moodle format code
     */
    function trans_format($name) {
        $name = trim($name);

        if ($name == 'moodle_auto_format') {
            $id = 0;
        } else if ($name == 'html') {
            $id = 1;
        } else if ($name == 'plain_text') {
            $id = 2;
        } else if ($name == 'wiki_like') {
            $id = 3;
        } else if ($name == 'markdown') {
            $id = 4;
        } else {
            $id = 0; // or maybe warning required
        }
        return $id;
    }

    /**
     * Translate human readable single answer option
     * to internal code number
     * @param string name true/false
     * @return int internal code number
     */
    function trans_single( $name ) {
        $name = trim($name);
        if ($name == "false" || !$name) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * process text string from xml file
     * @param array $text bit of xml tree after ['text']
     * @return string processed text
     */
    function import_text( $text ) {
        // quick sanity check
        if (empty($text)) {
            return '';
        }
        $data = $text[0]['#'];
        return trim($data);
    }

    /**
     * return the value of a node, given a path to the node
     * if it doesn't exist return the default value
     * @param array xml data to read
     * @param array path path to node expressed as array
     * @param mixed default
     * @param bool istext process as text
     * @param string error if set value must exist, return false and issue message if not
     * @return mixed value
     */
    function getpath($xml, $path, $default, $istext=false, $error='') {
        foreach ($path as $index) {
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error( $error );
                    return false;
                } else {
                    return $default;
                }
            }
            else $xml = $xml[$index];
        }
        if ($istext) {
            if (!is_string($xml)) {
                $this->error( get_string('invalidxml','qformat_xml') );
            }
            $xml = trim($xml);
        }

        return $xml;
    }


    /**
     * import parts of question common to all types
     * @param $question array question question array from xml tree
     * @return object question object
     */
    function import_headers($question) {
        global $CFG;

        // get some error strings
        $error_noname = get_string('xmlimportnoname','quiz');
        $error_noquestion = get_string('xmlimportnoquestion','quiz');

        // this routine initialises the question object
        $qo = $this->defaultquestion();

        // question name
        $qo->name = $this->getpath( $question, array('#','name',0,'#','text',0,'#'), '', true, $error_noname );
        $qo->questiontext       = $this->getpath($question, array('#','questiontext',0,'#','text',0,'#'), '', true );
        $qo->questiontextformat = $this->trans_format(
                $this->getpath($question, array('#','questiontext',0,'@','format'), 'moodle_auto_format'));

        $qo->questiontextfiles = array();

        // restore files in questiontext
        $files = $this->getpath($question, array('#', 'questiontext', 0, '#','file'), array(), false);
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->encoding = $file['@']['encoding'];
            $data->name = $file['@']['name'];
            $qo->questiontextfiles[] = $data;
        }

        // Backwards compatibility, deal with the old image tag.
        $filedata = $this->getpath($question, array('#', 'image_base64', '0', '#'), null, false);
        $filename = $this->getpath($question, array('#', 'image', '0', '#'), null, false);
        if ($filedata && $filename) {
            $data = new stdclass;
            $data->content = $filedata;
            $data->encoding = 'base64';
            $data->name = $filename;
            $qo->questiontextfiles[] = $data;
            $qo->questiontext .= ' <img src="@@PLUGINFILE@@/' . $filename . '" />';
        }

        // restore files in generalfeedback
        $qo->generalfeedback = $this->getpath($question, array('#','generalfeedback',0,'#','text',0,'#'), $qo->generalfeedback, true);
        $qo->generalfeedbackfiles = array();
        $qo->generalfeedbackformat = $this->trans_format(
                $this->getpath($question, array('#', 'generalfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $files = $this->getpath($question, array('#', 'generalfeedback', 0, '#', 'file'), array(), false);
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->encoding = $file['@']['encoding'];
            $data->name = $file['@']['name'];
            $qo->generalfeedbackfiles[] = $data;
        }

        $qo->defaultgrade = $this->getpath( $question, array('#','defaultgrade',0,'#'), $qo->defaultgrade );
        $qo->penalty = $this->getpath( $question, array('#','penalty',0,'#'), $qo->penalty );

        // Read the question tags.
        if (!empty($CFG->usetags) && array_key_exists('tags', $question['#'])
                && !empty($question['#']['tags'][0]['#']['tag'])) {
            require_once($CFG->dirroot.'/tag/lib.php');
            $qo->tags = array();
            foreach ($question['#']['tags'][0]['#']['tag'] as $tagdata) {
                $qo->tags[] = $this->getpath($tagdata, array('#', 'text', 0, '#'), '', true);
            }
        }

        return $qo;
    }

    /**
     * import the common parts of a single answer
     * @param array answer xml tree for single answer
     * @return object answer object
     */
    function import_answer($answer) {
        $fraction = $this->getpath($answer, array('@', 'fraction'), 0);
        $answertext = $this->getpath($answer, array('#', 'text', 0, '#'), '', true);
        $answerformat = $this->trans_format($this->getpath($answer,
                array('#', 'text', 0, '#'), 'moodle_auto_format'));
        $answerfiles = array();
        $files = $this->getpath($answer, array('#', 'answer', 0, '#', 'file'), array());
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->name = $file['@']['name'];
            $data->encoding = $file['@']['encoding'];
            $answerfiles[] = $data;
        }

        $feedbacktext = $this->getpath($answer, array('#', 'feedback', 0, '#', 'text', 0, '#'), '', true);
        $feedbackformat = $this->trans_format($this->getpath($answer,
                array('#', 'feedback', 0, '@', 'format'), 'moodle_auto_format'));
        $feedbackfiles = array();
        $files = $this->getpath($answer, array('#', 'feedback', 0, '#', 'file'), array());
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->name = $file['@']['name'];
            $data->encoding = $file['@']['encoding'];
            $feedbackfiles[] = $data;
        }

        $ans = new stdclass;

        $ans->answer = array();
        $ans->answer['text']   = $answertext;
        $ans->answer['format'] = $answerformat;
        $ans->answer['files']  = $answerfiles;

        $ans->feedback = array();
        $ans->feedback['text']   = $feedbacktext;
        $ans->feedback['format'] = $feedbackformat;
        $ans->feedback['files']  = $feedbackfiles;

        $ans->fraction = $fraction / 100;
        return $ans;
    }

    /**
     * import multiple choice question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_multichoice($question) {
        // get common parts
        $qo = $this->import_headers($question);

        // 'header' parts particular to multichoice
        $qo->qtype = MULTICHOICE;
        $single = $this->getpath( $question, array('#','single',0,'#'), 'true' );
        $qo->single = $this->trans_single( $single );
        $shuffleanswers = $this->getpath( $question, array('#','shuffleanswers',0,'#'), 'false' );
        $qo->answernumbering = $this->getpath( $question, array('#','answernumbering',0,'#'), 'abc' );
        $qo->shuffleanswers = $this->trans_single($shuffleanswers);

        $qo->correctfeedback = array();
        $qo->correctfeedback['text'] = $this->getpath($question, array('#', 'correctfeedback', 0, '#', 'text', 0, '#'), '', true);
        $qo->correctfeedback['format'] = $this->trans_format(
                $this->getpath($question, array('#', 'correctfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->correctfeedback['files'] = array();
        // restore files in correctfeedback
        $files = $this->getpath($question, array('#', 'correctfeedback', 0, '#','file'), array(), false);
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->encoding = $file['@']['encoding'];
            $data->name = $file['@']['name'];
            $qo->correctfeedback['files'][] = $data;
        }

        $qo->partiallycorrectfeedback = array();
        $qo->partiallycorrectfeedback['text'] = $this->getpath( $question, array('#','partiallycorrectfeedback',0,'#','text',0,'#'), '', true );
        $qo->partiallycorrectfeedback['format'] = $this->trans_format(
                $this->getpath($question, array('#', 'partiallycorrectfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->partiallycorrectfeedback['files'] = array();
        // restore files in partiallycorrectfeedback
        $files = $this->getpath($question, array('#', 'partiallycorrectfeedback', 0, '#','file'), array(), false);
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->encoding = $file['@']['encoding'];
            $data->name = $file['@']['name'];
            $qo->partiallycorrectfeedback['files'][] = $data;
        }

        $qo->incorrectfeedback = array();
        $qo->incorrectfeedback['text'] = $this->getpath( $question, array('#','incorrectfeedback',0,'#','text',0,'#'), '', true );
        $qo->incorrectfeedback['format'] = $this->trans_format(
                $this->getpath($question, array('#', 'incorrectfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->incorrectfeedback['files'] = array();
        // restore files in incorrectfeedback
        $files = $this->getpath($question, array('#', 'incorrectfeedback', 0, '#','file'), array(), false);
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->encoding = $file['@']['encoding'];
            $data->name = $file['@']['name'];
            $qo->incorrectfeedback['files'][] = $data;
        }

        // There was a time on the 1.8 branch when it could output an empty answernumbering tag, so fix up any found.
        if (empty($qo->answernumbering)) {
            $qo->answernumbering = 'abc';
        }

        // run through the answers
        $answers = $question['#']['answer'];
        $a_count = 0;
        foreach ($answers as $answer) {
            $ans = $this->import_answer($answer);
            $qo->answer[$a_count] = $ans->answer;
            $qo->fraction[$a_count] = $ans->fraction;
            $qo->feedback[$a_count] = $ans->feedback;
            ++$a_count;
        }

        return $qo;
    }

    /**
     * import cloze type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_multianswer( $questions ) {
        $questiontext = array();
        $questiontext['text'] = $this->import_text($questions['#']['questiontext'][0]['#']['text']);
        $questiontext['format'] = '1';
        $questiontext['itemid'] = ''; 
        $qo = qtype_multianswer_extract_question($questiontext);

        // 'header' parts particular to multianswer
        $qo->qtype = MULTIANSWER;
        $qo->course = $this->course;
        $qo->generalfeedback = '' ;
        // restore files in generalfeedback
        $qo->generalfeedback = $this->getpath($questions, array('#','generalfeedback',0,'#','text',0,'#'), $qo->generalfeedback, true);
        $qo->generalfeedbackfiles = array();
        $qo->generalfeedbackformat = $this->trans_format(
                $this->getpath($questions, array('#', 'generalfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $files = $this->getpath($questions, array('#', 'generalfeedback', 0, '#', 'file'), array(), false);
        foreach ($files as $file) {
            $data = new stdclass;
            $data->content = $file['#'];
            $data->encoding = $file['@']['encoding'];
            $data->name = $file['@']['name'];
            $qo->generalfeedbackfiles[] = $data;
        }
        if (!empty($questions)) {
            $qo->name = $this->import_text( $questions['#']['name'][0]['#']['text'] );
        }
        $qo->questiontext =  $qo->questiontext['text'] ;
        $qo->questiontextformat = '' ;

        return $qo;
    }

    /**
     * import true/false type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_truefalse( $question ) {
        // get common parts
        global $OUTPUT;
        $qo = $this->import_headers( $question );

        // 'header' parts particular to true/false
        $qo->qtype = TRUEFALSE;

        // get answer info
        //
        // In the past, it used to be assumed that the two answers were in the file
        // true first, then false. Howevever that was not always true. Now, we
        // try to match on the answer text, but in old exports, this will be a localised
        // string, so if we don't find true or false, we fall back to the old system.
        $first = true;
        $warning = false;
        foreach ($question['#']['answer'] as $answer) {
            $answertext = $this->getpath( $answer, array('#','text',0,'#'), '', true);
            $feedback = $this->getpath($answer, array('#','feedback',0,'#','text',0,'#'), '', true);
            $feedbackformat = $this->getpath($answer, array('#','feedback',0, '@', 'format'), 'moodle_auto_format');
            $feedbackfiles = $this->getpath($answer, array('#', 'feedback', 0, '#', 'file'), array());
            $files = array();
            foreach ($feedbackfiles as $file) {
                $data = new stdclass;
                $data->content = $file['#'];
                $data->encoding = $file['@']['encoding'];
                $data->name = $file['@']['name'];
                $files[] = $data;
            }
            if ($answertext != 'true' && $answertext != 'false') {
                $warning = true;
                $answertext = $first ? 'true' : 'false'; // Old style file, assume order is true/false.
            }
            if ($answertext == 'true') {
                $qo->answer = ($answer['@']['fraction'] == 100);
                $qo->correctanswer = $qo->answer;
                $qo->feedbacktrue = array();
                $qo->feedbacktrue['text'] = $feedback;
                $qo->feedbacktrue['format'] = $this->trans_format($feedbackformat);
                $qo->feedbacktrue['itemid'] = null;
                $qo->feedbacktruefiles = $files;
            } else {
                $qo->answer = ($answer['@']['fraction'] != 100);
                $qo->correctanswer = $qo->answer;
                $qo->feedbackfalse = array();
                $qo->feedbackfalse['text'] = $feedback;
                $qo->feedbackfalse['format'] = $this->trans_format($feedbackformat);
                $qo->feedbackfalse['itemid'] = null;
                $qo->feedbackfalsefiles = $files;
            }
            $first = false;
        }

        if ($warning) {
            $a = new stdClass;
            $a->questiontext = $qo->questiontext;
            $a->answer = get_string($qo->answer ? 'true' : 'false', 'quiz');
            echo $OUTPUT->notification(get_string('truefalseimporterror', 'quiz', $a));
        }
        return $qo;
    }

    /**
     * import short answer type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_shortanswer( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to shortanswer
        $qo->qtype = SHORTANSWER;

        // get usecase
        $qo->usecase = $this->getpath($question, array('#','usecase',0,'#'), $qo->usecase );

        // run through the answers
        $answers = $question['#']['answer'];
        $a_count = 0;
        foreach ($answers as $answer) {
            $ans = $this->import_answer($answer);
            $qo->answer[$a_count] = $ans->answer['text'];
            $qo->fraction[$a_count] = $ans->fraction;
            $qo->feedback[$a_count] = $ans->feedback;
            ++$a_count;
        }

        return $qo;
    }

    /**
     * import description type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_description( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );
        // header parts particular to shortanswer
        $qo->qtype = DESCRIPTION;
        $qo->defaultgrade = 0;
        $qo->length = 0;
        return $qo;
    }

    /**
     * import numerical type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_numerical($question) {
        // get common parts
        $qo = $this->import_headers($question);

        // header parts particular to numerical
        $qo->qtype = NUMERICAL;

        // get answers array
        $answers = $question['#']['answer'];
        $qo->answer = array();
        $qo->feedback = array();
        $qo->fraction = array();
        $qo->tolerance = array();
        foreach ($answers as $answer) {
            // answer outside of <text> is deprecated
            $obj = $this->import_answer($answer);
            $qo->answer[] = $obj->answer['text'];
            if (empty($qo->answer)) {
                $qo->answer = '*';
            }
            $qo->feedback[]  = $obj->feedback;
            $qo->tolerance[] = $this->getpath($answer, array('#', 'tolerance', 0, '#'), 0);

            // fraction as a tag is deprecated
            $fraction = $this->getpath($answer, array('@', 'fraction'), 0) / 100;
            $qo->fraction[] = $this->getpath($answer, array('#', 'fraction', 0, '#'), $fraction); // deprecated
        }

        // get units array
        $qo->unit = array();
        $units = $this->getpath( $question, array('#','units',0,'#','unit'), array() );
        if (!empty($units)) {
            $qo->multiplier = array();
            foreach ($units as $unit) {
                $qo->multiplier[] = $this->getpath( $unit, array('#','multiplier',0,'#'), 1 );
                $qo->unit[] = $this->getpath( $unit, array('#','unit_name',0,'#'), '', true );
            }
        }
        $qo->unitgradingtype = $this->getpath( $question, array('#','unitgradingtype',0,'#'), 0 );
        $qo->unitpenalty = $this->getpath( $question, array('#','unitpenalty',0,'#'), 0 );
        $qo->showunits = $this->getpath( $question, array('#','showunits',0,'#'), 0 );
        $qo->unitsleft = $this->getpath( $question, array('#','unitsleft',0,'#'), 0 );
        $qo->instructions['text'] = '';
        $qo->instructions['format'] = FORMAT_HTML;
        $instructions = $this->getpath($question, array('#', 'instructions'), array());
        if (!empty($instructions)) {
            $qo->instructions = array();
            $qo->instructions['text'] = $this->getpath($instructions,
                    array('0', '#', 'text', '0', '#'), '', true);
            $qo->instructions['format'] = $this->trans_format($this->getpath($instructions,
                    array('0', '@', 'format'), 'moodle_auto_format'));
            $files = $this->getpath($instructions, array('0', '#', 'file'), array());
            $qo->instructions['files'] = array();
            foreach ($files as $file) {
                $data = new stdclass;
                $data->content = $file['#'];
                $data->encoding = $file['@']['encoding'];
                $data->name = $file['@']['name'];
                $qo->instructions['files'][]= $data;
            }
        }
        return $qo;
    }

    /**
     * import matching type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_matching($question) {
        // get common parts
        $qo = $this->import_headers($question);

        // header parts particular to matching
        $qo->qtype = MATCH;
        $qo->shuffleanswers = $this->getpath($question, array('#', 'shuffleanswers', 0, '#'), 1);

        // get subquestions
        $subquestions = $question['#']['subquestion'];
        $qo->subquestions = array();
        $qo->subanswers = array();

        // run through subquestions
        foreach ($subquestions as $subquestion) {
            $question = array();
            $question['text'] = $this->getpath($subquestion, array('#', 'text', 0, '#'), '', true);
            $question['format'] = $this->trans_format(
                    $this->getpath($subquestion, array('@', 'format'), 'moodle_auto_format'));
            $question['files'] = array();

            $files = $this->getpath($subquestion, array('#', 'file'), array());
            foreach ($files as $file) {
                $data = new stdclass();
                $data->content = $file['#'];
                $data->encoding = $file['@']['encoding'];
                $data->name = $file['@']['name'];
                $question['files'][] = $data;
            }
            $qo->subquestions[] = $question;
            $answers = $this->getpath($subquestion, array('#', 'answer'), array());
            $qo->subanswers[] = $this->getpath($subquestion, array('#','answer',0,'#','text',0,'#'), '', true);
        }
        return $qo;
    }

    /**
     * import  essay type question
     * @param array question question array from xml tree
     * @return object question object
     */
    function import_essay( $question ) {
        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to essay
        $qo->qtype = ESSAY;

        $answers = $this->getpath($question, array('#', 'answer'), null);
        if ($answers) {
            $answer = array_pop($answers);
            $answer = $this->import_answer($answer);
            // get feedback
            $qo->feedback = $answer->feedback;
        } else {
            $qo->feedback = array('text' => '', 'format' => FORMAT_MOODLE, 'files' => array());
        }

        // get fraction - <fraction> tag is deprecated
        $qo->fraction = $this->getpath($question, array('@','fraction'), 0 ) / 100;
        $q0->fraction = $this->getpath($question, array('#','fraction',0,'#'), $qo->fraction );

        return $qo;
    }

    function import_calculated($question,$qtype) {
    // import calculated question

        // get common parts
        $qo = $this->import_headers( $question );

        // header parts particular to calculated
        $qo->qtype = CALCULATED ;//CALCULATED;
        $qo->synchronize = $this->getpath( $question, array( '#','synchronize',0,'#' ), 0 );
        $single = $this->getpath( $question, array('#','single',0,'#'), 'true' );
        $qo->single = $this->trans_single( $single );
        $shuffleanswers = $this->getpath( $question, array('#','shuffleanswers',0,'#'), 'false' );
        $qo->answernumbering = $this->getpath( $question, array('#','answernumbering',0,'#'), 'abc' );
        $qo->shuffleanswers = $this->trans_single($shuffleanswers);

        $qo->correctfeedback = array();
        $qo->correctfeedback['text'] = $this->getpath($question, array('#','correctfeedback',0,'#','text',0,'#'), '', true );
        $qo->correctfeedback['format'] = $this->trans_format($this->getpath(
                $question, array('#', 'correctfeedback', 0, '@', 'formath'), 'moodle_auto_format'));
        $qo->correctfeedback['files'] = array();

        $files = $this->getpath($question, array('#', 'correctfeedback', '0', '#', 'file'), array());
        foreach ($files as $file) {
            $data = new stdclass();
            $data->content = $file['#'];
            $data->name = $file['@']['name'];
            $data->encoding = $file['@']['encoding'];
            $qo->correctfeedback['files'][] = $data;
        }

        $qo->partiallycorrectfeedback = array();
        $qo->partiallycorrectfeedback['text'] = $this->getpath( $question, array('#','partiallycorrectfeedback',0,'#','text',0,'#'), '', true );
        $qo->partiallycorrectfeedback['format'] = $this->trans_format(
                $this->getpath($question, array('#','partiallycorrectfeedback', 0, '@','format'), 'moodle_auto_format'));
        $qo->partiallycorrectfeedback['files'] = array();

        $files = $this->getpath($question, array('#', 'partiallycorrectfeedback', '0', '#', 'file'), array());
        foreach ($files as $file) {
            $data = new stdclass();
            $data->content = $file['#'];
            $data->name = $file['@']['name'];
            $data->encoding = $file['@']['encoding'];
            $qo->partiallycorrectfeedback['files'][] = $data;
        }

        $qo->incorrectfeedback = array();
        $qo->incorrectfeedback['text'] = $this->getpath( $question, array('#','incorrectfeedback',0,'#','text',0,'#'), '', true );
        $qo->incorrectfeedback['format'] = $this->trans_format($this->getpath(
                $question, array('#','incorrectfeedback', 0, '@','format'), 'moodle_auto_format'));
        $qo->incorrectfeedback['files'] = array();

        $files = $this->getpath($question, array('#', 'incorrectfeedback', '0', '#', 'file'), array());
        foreach ($files as $file) {
            $data = new stdclass();
            $data->content = $file['#'];
            $data->name = $file['@']['name'];
            $data->encoding = $file['@']['encoding'];
            $qo->incorrectfeedback['files'][] = $data;
        }

        $qo->unitgradingtype = $this->getpath($question, array('#','unitgradingtype',0,'#'), 0 );
        $qo->unitpenalty = $this->getpath($question, array('#','unitpenalty',0,'#'), 0 );
        $qo->showunits = $this->getpath($question, array('#','showunits',0,'#'), 0 );
        $qo->unitsleft = $this->getpath($question, array('#','unitsleft',0,'#'), 0 );
        $qo->instructions = $this->getpath( $question, array('#','instructions',0,'#','text',0,'#'), '', true );
        if (!empty($instructions)) {
            $qo->instructions = array();
            $qo->instructions['text'] = $this->getpath($instructions,
                    array('0', '#', 'text', '0', '#'), '', true);
            $qo->instructions['format'] = $this->trans_format($this->getpath($instructions,
                    array('0', '@', 'format'), 'moodle_auto_format'));
            $files = $this->getpath($instructions,
                    array('0', '#', 'file'), array());
            $qo->instructions['files'] = array();
            foreach ($files as $file) {
                $data = new stdclass;
                $data->content = $file['#'];
                $data->encoding = $file['@']['encoding'];
                $data->name = $file['@']['name'];
                $qo->instructions['files'][]= $data;
            }
        }

        $files = $this->getpath($question, array('#', 'instructions', 0, '#', 'file', 0, '@'), '', false);

        // get answers array
        $answers = $question['#']['answer'];
        $qo->answers = array();
        $qo->feedback = array();
        $qo->fraction = array();
        $qo->tolerance = array();
        $qo->tolerancetype = array();
        $qo->correctanswerformat = array();
        $qo->correctanswerlength = array();
        $qo->feedback = array();
        foreach ($answers as $answer) {
            $ans = $this->import_answer($answer);
            // answer outside of <text> is deprecated
            if (empty($ans->answer['text'])) {
                $ans->answer['text'] = '*';
            }
            $qo->answers[] = $ans->answer;
            $qo->feedback[] = $ans->feedback;
            $qo->tolerance[] = $answer['#']['tolerance'][0]['#'];
            // fraction as a tag is deprecated
            if (!empty($answer['#']['fraction'][0]['#'])) {
                $qo->fraction[] = $answer['#']['fraction'][0]['#'];
            } else {
                $qo->fraction[] = $answer['@']['fraction'] / 100;
            }
            $qo->tolerancetype[] = $answer['#']['tolerancetype'][0]['#'];
            $qo->correctanswerformat[] = $answer['#']['correctanswerformat'][0]['#'];
            $qo->correctanswerlength[] = $answer['#']['correctanswerlength'][0]['#'];
        }
        // get units array
        $qo->unit = array();
        if (isset($question['#']['units'][0]['#']['unit'])) {
            $units = $question['#']['units'][0]['#']['unit'];
            $qo->multiplier = array();
            foreach ($units as $unit) {
                $qo->multiplier[] = $unit['#']['multiplier'][0]['#'];
                $qo->unit[] = $unit['#']['unit_name'][0]['#'];
            }
        }
        $instructions = $this->getpath($question, array('#', 'instructions'), array());
        if (!empty($instructions)) {
            $qo->instructions = array();
            $qo->instructions['text'] = $this->getpath($instructions,
                    array('0', '#', 'text', '0', '#'), '', true);
            $qo->instructions['format'] = $this->trans_format($this->getpath($instructions,
                    array('0', '@', 'format'), 'moodle_auto_format'));
            $files = $this->getpath($instructions,
                    array('0', '#', 'file'), array());
            $qo->instructions['files'] = array();
            foreach ($files as $file) {
                $data = new stdclass;
                $data->content = $file['#'];
                $data->encoding = $file['@']['encoding'];
                $data->name = $file['@']['name'];
                $qo->instructions['files'][]= $data;
            }
        }
        $datasets = $question['#']['dataset_definitions'][0]['#']['dataset_definition'];
        $qo->dataset = array();
        $qo->datasetindex= 0 ;
        foreach ($datasets as $dataset) {
            $qo->datasetindex++;
            $qo->dataset[$qo->datasetindex] = new stdClass();
            $qo->dataset[$qo->datasetindex]->status = $this->import_text( $dataset['#']['status'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->name = $this->import_text( $dataset['#']['name'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->type =  $dataset['#']['type'][0]['#'];
            $qo->dataset[$qo->datasetindex]->distribution = $this->import_text( $dataset['#']['distribution'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->max = $this->import_text( $dataset['#']['maximum'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->min = $this->import_text( $dataset['#']['minimum'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->length = $this->import_text( $dataset['#']['decimals'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->distribution = $this->import_text( $dataset['#']['distribution'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->itemcount = $dataset['#']['itemcount'][0]['#'];
            $qo->dataset[$qo->datasetindex]->datasetitem = array();
            $qo->dataset[$qo->datasetindex]->itemindex = 0;
            $qo->dataset[$qo->datasetindex]->number_of_items=$dataset['#']['number_of_items'][0]['#'];
            $datasetitems = $dataset['#']['dataset_items'][0]['#']['dataset_item'];
            foreach ($datasetitems as $datasetitem) {
                $qo->dataset[$qo->datasetindex]->itemindex++;
                $qo->dataset[$qo->datasetindex]->datasetitem[$qo->dataset[$qo->datasetindex]->itemindex] = new stdClass();
                $qo->dataset[$qo->datasetindex]->datasetitem[$qo->dataset[$qo->datasetindex]->itemindex]->itemnumber =  $datasetitem['#']['number'][0]['#']; //[0]['#']['number'][0]['#'] ; // [0]['numberitems'] ;//['#']['number'][0]['#'];// $datasetitems['#']['number'][0]['#'];
                $qo->dataset[$qo->datasetindex]->datasetitem[$qo->dataset[$qo->datasetindex]->itemindex]->value = $datasetitem['#']['value'][0]['#'] ;//$datasetitem['#']['value'][0]['#'];
            }
        }

        // echo "<pre>loaded qo";print_r($qo);echo "</pre>";
        return $qo;
    }

    /**
     * this is not a real question type. It's a dummy type used
     * to specify the import category
     * format is:
     * <question type="category">
     *     <category>tom/dick/harry</category>
     * </question>
     */
    function import_category( $question ) {
        $qo = new stdClass;
        $qo->qtype = 'category';
        $qo->category = $this->import_text($question['#']['category'][0]['#']['text']);
        return $qo;
    }

    /**
     * parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array (of objects) question objects
     */
    function readquestions($lines) {
        // we just need it as one big string
        $text = implode($lines, " ");
        unset($lines);

        // this converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        // print_r it if you want to see what it looks like!
        $xml = xmlize($text, 0);

        // set up array to hold all our questions
        $questions = array();

        // iterate through questions
        foreach ($xml['quiz']['#']['question'] as $question) {
            $question_type = $question['@']['type'];
            $questiontype = get_string( 'questiontype','quiz',$question_type );

            if ($question_type=='multichoice') {
                $qo = $this->import_multichoice( $question );
            }
            elseif ($question_type=='truefalse') {
                $qo = $this->import_truefalse( $question );
            }
            elseif ($question_type=='shortanswer') {
                $qo = $this->import_shortanswer( $question );
            }
            elseif ($question_type=='numerical') {
                $qo = $this->import_numerical( $question );
            }
            elseif ($question_type=='description') {
                $qo = $this->import_description( $question );
            }
            elseif ($question_type=='matching') {
                $qo = $this->import_matching( $question );
            }
            elseif ($question_type=='cloze') {
                $qo = $this->import_multianswer( $question );
            }
            elseif ($question_type=='essay') {
                $qo = $this->import_essay( $question );
            }
            elseif ($question_type=='calculated') {
                $qo = $this->import_calculated( $question,CALCULATED  );
            }
            elseif ($question_type=='calculatedsimple') {
                $qo = $this->import_calculated( $question,CALCULATEDMULTI  );
                $qo->qtype = CALCULATEDSIMPLE ;
            }
            elseif ($question_type=='calculatedmulti') {
                $qo = $this->import_calculated( $question,CALCULATEDMULTI );
                $qo->qtype = CALCULATEDMULTI ;
            }
            elseif ($question_type=='category') {
                $qo = $this->import_category( $question );
            }
            else {
                // try for plugin support
                // no default question, as the plugin can call
                // import_headers() itself if it wants to
                if (!$qo = $this->try_importing_using_qtypes( $question, null, null, $question_type)) {
                    $notsupported = get_string( 'xmltypeunsupported','quiz',$question_type );
                    $this->error( $notsupported );
                    $qo = null;
                }
            }

            // stick the result in the $questions array
            if ($qo) {
                $questions[] = $qo;
            }
        }
        return $questions;
    }

    // EXPORT FUNCTIONS START HERE

    function export_file_extension() {
        return '.xml';
    }

    /**
     * Turn the internal question code into a human readable form
     * (The code used to be numeric, but this remains as some of
     * the names don't match the new internal format)
     * @param mixed type_id Internal code
     * @return string question type string
     */
    function get_qtype( $type_id ) {
        switch( $type_id ) {
        case TRUEFALSE:
            $name = 'truefalse';
            break;
        case MULTICHOICE:
            $name = 'multichoice';
            break;
        case SHORTANSWER:
            $name = 'shortanswer';
            break;
        case NUMERICAL:
            $name = 'numerical';
            break;
        case MATCH:
            $name = 'matching';
            break;
        case DESCRIPTION:
            $name = 'description';
            break;
        case MULTIANSWER:
            $name = 'cloze';
            break;
        case ESSAY:
            $name = 'essay';
            break;
        case CALCULATED:
            $name = 'calculated';
            break;
        case CALCULATEDSIMPLE:
            $name = 'calculatedsimple';
            break;
        case CALCULATEDMULTI:
            $name = 'calculatedmulti';
            break;
        default:
            $name = false;
        }
        return $name;
    }

    /**
     * Convert internal Moodle text format code into
     * human readable form
     * @param int id internal code
     * @return string format text
     */
    function get_format( $id ) {
        switch( $id ) {
        case 0:
            $name = "moodle_auto_format";
            break;
        case 1:
            $name = "html";
            break;
        case 2:
            $name = "plain_text";
            break;
        case 3:
            $name = "wiki_like";
            break;
        case 4:
            $name = "markdown";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

    /**
     * Convert internal single question code into
     * human readable form
     * @param int id single question code
     * @return string single question string
     */
    function get_single( $id ) {
        switch( $id ) {
        case 0:
            $name = "false";
            break;
        case 1:
            $name = "true";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

    /**
     * generates <text></text> tags, processing raw text therein
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */
    function writetext($raw, $ilev = 0, $short = true) {
        $indent = str_repeat('  ', $ilev);

        // if required add CDATA tags
        if (!empty($raw) and (htmlspecialchars($raw) != $raw)) {
            $raw = "<![CDATA[$raw]]>";
        }

        if ($short) {
            $xml = "$indent<text>$raw</text>";
        } else {
            $xml = "$indent<text>\n$raw\n$indent</text>\n";
        }

        return $xml;
    }

    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<quiz>\n" .
                       $content . "\n" .
                       "</quiz>";
        return $content;
    }

    /**
     * Turns question into an xml segment
     * @param object question object
     * @param int context id
     * @return string xml segment
     */
    function writequestion($question) {
        global $CFG, $QTYPES, $OUTPUT;

        $fs = get_file_storage();
        $contextid = $question->contextid;
        // initial string;
        $expout = "";

        // add comment
        $expout .= "\n\n<!-- question: $question->id  -->\n";

        // check question type
        if (!$question_type = $this->get_qtype( $question->qtype )) {
            // must be a plugin then, so just accept the name supplied
            $question_type = $question->qtype;
        }

        // add opening tag
        // generates specific header for Cloze and category type question
        if ($question->qtype == 'category') {
            $categorypath = $this->writetext( $question->category );
            $expout .= "  <question type=\"category\">\n";
            $expout .= "    <category>\n";
            $expout .= "        $categorypath\n";
            $expout .= "    </category>\n";
            $expout .= "  </question>\n";
            return $expout;
        } elseif ($question->qtype != MULTIANSWER) {
            // for all question types except Close
            $name_text = $this->writetext($question->name);
            $qtformat = $this->get_format($question->questiontextformat);
            $generalfeedbackformat = $this->get_format($question->generalfeedbackformat);

            $question_text = $this->writetext($question->questiontext);
            $question_text_files = $this->writefiles($question->questiontextfiles);

            $generalfeedback = $this->writetext($question->generalfeedback);
            $generalfeedback_files = $this->writefiles($question->generalfeedbackfiles);

            $expout .= "  <question type=\"$question_type\">\n";
            $expout .= "    <name>$name_text</name>\n";
            $expout .= "    <questiontext format=\"$qtformat\">\n";
            $expout .= $question_text;
            $expout .= $question_text_files;
            $expout .= "    </questiontext>\n";
            $expout .= "    <generalfeedback format=\"$generalfeedbackformat\">\n";
            $expout .= $generalfeedback;
            $expout .= $generalfeedback_files;
            $expout .= "    </generalfeedback>\n";
            $expout .= "    <defaultgrade>{$question->defaultgrade}</defaultgrade>\n";
            $expout .= "    <penalty>{$question->penalty}</penalty>\n";
            $expout .= "    <hidden>{$question->hidden}</hidden>\n";
        } else {
            // for Cloze type only
            $name_text = $this->writetext( $question->name );
            $question_text = $this->writetext( $question->questiontext );
            $generalfeedback = $this->writetext( $question->generalfeedback );
            $expout .= "  <question type=\"$question_type\">\n";
            $expout .= "    <name>$name_text</name>\n";
            $expout .= "    <questiontext>\n";
            $expout .= $question_text;
            $expout .= "    </questiontext>\n";
            $expout .= "    <generalfeedback>\n";
            $expout .= $generalfeedback;
            $expout .= "    </generalfeedback>\n";
        }

        if (!empty($question->options->shuffleanswers)) {
            $expout .= "    <shuffleanswers>{$question->options->shuffleanswers}</shuffleanswers>\n";
        }
        else {
            $expout .= "    <shuffleanswers>0</shuffleanswers>\n";
        }

        // output depends on question type
        switch($question->qtype) {
        case 'category':
            // not a qtype really - dummy used for category switching
            break;
        case TRUEFALSE:
            foreach ($question->options->answers as $answer) {
                $fraction_pc = round( $answer->fraction * 100 );
                if ($answer->id == $question->options->trueanswer) {
                    $answertext = 'true';
                } else {
                    $answertext = 'false';
                }
                $expout .= "    <answer fraction=\"$fraction_pc\">\n";
                $expout .= $this->writetext($answertext, 3) . "\n";
                $feedbackformat = $this->get_format($answer->feedbackformat);
                $expout .= "      <feedback format=\"$feedbackformat\">\n";
                $expout .= $this->writetext($answer->feedback,4,false);
                $expout .= $this->writefiles($answer->feedbackfiles);
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
            }
            break;
        case MULTICHOICE:
            $expout .= "    <single>".$this->get_single($question->options->single)."</single>\n";
            $expout .= "    <shuffleanswers>".$this->get_single($question->options->shuffleanswers)."</shuffleanswers>\n";

            $textformat = $this->get_format($question->options->correctfeedbackformat);
            $files = $fs->get_area_files($contextid, 'qtype_multichoice', 'correctfeedback', $question->id);
            $expout .= "    <correctfeedback format=\"$textformat\">\n";
            $expout .= $this->writetext($question->options->correctfeedback, 3);
            $expout .= $this->writefiles($files);
            $expout .= "    </correctfeedback>\n";

            $textformat = $this->get_format($question->options->partiallycorrectfeedbackformat);
            $files = $fs->get_area_files($contextid, 'qtype_multichoice', 'partiallycorrectfeedback', $question->id);
            $expout .= "    <partiallycorrectfeedback format=\"$textformat\">\n";
            $expout .= $this->writetext($question->options->partiallycorrectfeedback, 3);
            $expout .= $this->writefiles($files);
            $expout .= "    </partiallycorrectfeedback>\n";

            $textformat = $this->get_format($question->options->incorrectfeedbackformat);
            $files = $fs->get_area_files($contextid, 'qtype_multichoice', 'incorrectfeedback', $question->id);
            $expout .= "    <incorrectfeedback format=\"$textformat\">\n";
            $expout .= $this->writetext($question->options->incorrectfeedback, 3);
            $expout .= $this->writefiles($files);
            $expout .= "    </incorrectfeedback>\n";

            $expout .= "    <answernumbering>{$question->options->answernumbering}</answernumbering>\n";
            foreach($question->options->answers as $answer) {
                $percent = $answer->fraction * 100;
                $expout .= "      <answer fraction=\"$percent\">\n";
                $expout .= $this->writetext($answer->answer,4,false);
                $feedbackformat = $this->get_format($answer->feedbackformat);
                $expout .= "      <feedback format=\"$feedbackformat\">\n";
                $expout .= $this->writetext($answer->feedback,5,false);
                $expout .= $this->writefiles($answer->feedbackfiles);
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
                }
            break;
        case SHORTANSWER:
            $expout .= "    <usecase>{$question->options->usecase}</usecase>\n ";
            foreach($question->options->answers as $answer) {
                $percent = 100 * $answer->fraction;
                $expout .= "    <answer fraction=\"$percent\">\n";
                $expout .= $this->writetext( $answer->answer,3,false );
                $feedbackformat = $this->get_format($answer->feedbackformat);
                $expout .= "      <feedback format=\"$feedbackformat\">\n";
                $expout .= $this->writetext($answer->feedback);
                $expout .= $this->writefiles($answer->feedbackfiles);
                $expout .= "      </feedback>\n";
                $expout .= "    </answer>\n";
            }
            break;
        case NUMERICAL:
            foreach ($question->options->answers as $answer) {
                $tolerance = $answer->tolerance;
                $percent = 100 * $answer->fraction;
                $expout .= "<answer fraction=\"$percent\">\n";
                // <text> tags are an added feature, old filed won't have them
                $expout .= "    <text>{$answer->answer}</text>\n";
                $expout .= "    <tolerance>$tolerance</tolerance>\n";
                $feedbackformat = $this->get_format($answer->feedbackformat);
                $expout .= "    <feedback format=\"$feedbackformat\">\n";
                $expout .= $this->writetext($answer->feedback);
                $expout .= $this->writefiles($answer->feedbackfiles);
                $expout .= "    </feedback>\n";
                // fraction tag is deprecated
                // $expout .= "    <fraction>{$answer->fraction}</fraction>\n";
                $expout .= "</answer>\n";
            }

            $units = $question->options->units;
            if (count($units)) {
                $expout .= "<units>\n";
                foreach ($units as $unit) {
                    $expout .= "  <unit>\n";
                    $expout .= "    <multiplier>{$unit->multiplier}</multiplier>\n";
                    $expout .= "    <unit_name>{$unit->unit}</unit_name>\n";
                    $expout .= "  </unit>\n";
                }
                $expout .= "</units>\n";
            }
            if (isset($question->options->unitgradingtype)) {
                $expout .= "    <unitgradingtype>{$question->options->unitgradingtype}</unitgradingtype>\n";
            }
            if (isset($question->options->unitpenalty)) {
                $expout .= "    <unitpenalty>{$question->options->unitpenalty}</unitpenalty>\n";
            }
            if (isset($question->options->showunits)) {
                $expout .= "    <showunits>{$question->options->showunits}</showunits>\n";
            }
            if (isset($question->options->unitsleft)) {
                $expout .= "    <unitsleft>{$question->options->unitsleft}</unitsleft>\n";
            }
            if (!empty($question->options->instructionsformat)) {
                $textformat = $this->get_format($question->options->instructionsformat);
                $files = $fs->get_area_files($contextid, 'qtype_numerical', 'instruction', $question->id);
                $expout .= "    <instructions format=\"$textformat\">\n";
                $expout .= $this->writetext($question->options->instructions, 3);
                $expout .= $this->writefiles($files);
                $expout .= "    </instructions>\n";
            }
            break;
        case MATCH:
            foreach($question->options->subquestions as $subquestion) {
                $files = $fs->get_area_files($contextid, 'qtype_match', 'subquestion', $subquestion->id);
                $textformat = $this->get_format($subquestion->questiontextformat);
                $expout .= "<subquestion format=\"$textformat\">\n";
                $expout .= $this->writetext($subquestion->questiontext);
                $expout .= $this->writefiles($files);
                $expout .= "<answer>";
                $expout .= $this->writetext($subquestion->answertext);
                $expout .= "</answer>\n";
                $expout .= "</subquestion>\n";
            }
            break;
        case DESCRIPTION:
            // nothing more to do for this type
            break;
        case MULTIANSWER:
            $a_count=1;
            foreach($question->options->questions as $question) {
                $thispattern = preg_quote("{#".$a_count."}"); //TODO: is this really necessary?
                $thisreplace = $question->questiontext;
                $expout=preg_replace("~$thispattern~", $thisreplace, $expout );
                $a_count++;
            }
        break;
        case ESSAY:
            if (!empty($question->options->answers)) {
                foreach ($question->options->answers as $answer) {
                    $percent = 100 * $answer->fraction;
                    $expout .= "<answer fraction=\"$percent\">\n";
                    $feedbackformat = $this->get_format($answer->feedbackformat);
                    $expout .= "    <feedback format=\"$feedbackformat\">\n";
                    $expout .= $this->writetext($answer->feedback);
                    $expout .= $this->writefiles($answer->feedbackfiles);
                    $expout .= "    </feedback>\n";
                    // fraction tag is deprecated
                    // $expout .= "    <fraction>{$answer->fraction}</fraction>\n";
                    $expout .= "</answer>\n";
                }
            }
            break;
        case CALCULATED:
        case CALCULATEDSIMPLE:
        case CALCULATEDMULTI:
            $expout .= "    <synchronize>{$question->options->synchronize}</synchronize>\n";
            $expout .= "    <single>{$question->options->single}</single>\n";
            $expout .= "    <answernumbering>{$question->options->answernumbering}</answernumbering>\n";
            $expout .= "    <shuffleanswers>".$this->writetext($question->options->shuffleanswers, 3)."</shuffleanswers>\n";

            $component = 'qtype_' . $question->qtype;
            $files = $fs->get_area_files($contextid, $component, 'correctfeedback', $question->id);
            $expout .= "    <correctfeedback>\n";
            $expout .= $this->writetext($question->options->correctfeedback, 3);
            $expout .= $this->writefiles($files);
            $expout .= "    </correctfeedback>\n";

            $files = $fs->get_area_files($contextid, $component, 'partiallycorrectfeedback', $question->id);
            $expout .= "    <partiallycorrectfeedback>\n";
            $expout .= $this->writetext($question->options->partiallycorrectfeedback, 3);
            $expout .= $this->writefiles($files);
            $expout .= "    </partiallycorrectfeedback>\n";

            $files = $fs->get_area_files($contextid, $component, 'incorrectfeedback', $question->id);
            $expout .= "    <incorrectfeedback>\n";
            $expout .= $this->writetext($question->options->incorrectfeedback, 3);
            $expout .= $this->writefiles($files);
            $expout .= "    </incorrectfeedback>\n";

            foreach ($question->options->answers as $answer) {
                $tolerance = $answer->tolerance;
                $tolerancetype = $answer->tolerancetype;
                $correctanswerlength= $answer->correctanswerlength ;
                $correctanswerformat= $answer->correctanswerformat;
                $percent = 100 * $answer->fraction;
                $expout .= "<answer fraction=\"$percent\">\n";
                // "<text/>" tags are an added feature, old files won't have them
                $expout .= "    <text>{$answer->answer}</text>\n";
                $expout .= "    <tolerance>$tolerance</tolerance>\n";
                $expout .= "    <tolerancetype>$tolerancetype</tolerancetype>\n";
                $expout .= "    <correctanswerformat>$correctanswerformat</correctanswerformat>\n";
                $expout .= "    <correctanswerlength>$correctanswerlength</correctanswerlength>\n";
                $feedbackformat = $this->get_format($answer->feedbackformat);
                $expout .= "    <feedback format=\"$feedbackformat\">\n";
                $expout .= $this->writetext($answer->feedback);
                $expout .= $this->writefiles($answer->feedbackfiles);
                $expout .= "    </feedback>\n";
                $expout .= "</answer>\n";
            }
            if (isset($question->options->unitgradingtype)) {
                $expout .= "    <unitgradingtype>{$question->options->unitgradingtype}</unitgradingtype>\n";
            }
            if (isset($question->options->unitpenalty)) {
                $expout .= "    <unitpenalty>{$question->options->unitpenalty}</unitpenalty>\n";
            }
            if (isset($question->options->showunits)) {
                $expout .= "    <showunits>{$question->options->showunits}</showunits>\n";
            }
            if (isset($question->options->unitsleft)) {
                $expout .= "    <unitsleft>{$question->options->unitsleft}</unitsleft>\n";
            }

            if (isset($question->options->instructionsformat)) {
                $textformat = $this->get_format($question->options->instructionsformat);
                $files = $fs->get_area_files($contextid, $component, 'instruction', $question->id);
                $expout .= "    <instructions format=\"$textformat\">\n";
                $expout .= $this->writetext($question->options->instructions, 3);
                $expout .= $this->writefiles($files);
                $expout .= "    </instructions>\n";
            }

            if (isset($question->options->units)) {
                $units = $question->options->units;
                if (count($units)) {
                    $expout .= "<units>\n";
                    foreach ($units as $unit) {
                        $expout .= "  <unit>\n";
                        $expout .= "    <multiplier>{$unit->multiplier}</multiplier>\n";
                        $expout .= "    <unit_name>{$unit->unit}</unit_name>\n";
                        $expout .= "  </unit>\n";
                    }
                    $expout .= "</units>\n";
                }
            }
            //The tag $question->export_process has been set so we get all the data items in the database
            //   from the function $QTYPES['calculated']->get_question_options(&$question);
            //  calculatedsimple defaults to calculated
            if( isset($question->options->datasets)&&count($question->options->datasets)){// there should be
                $expout .= "<dataset_definitions>\n";
                foreach ($question->options->datasets as $def) {
                    $expout .= "<dataset_definition>\n";
                    $expout .= "    <status>".$this->writetext($def->status)."</status>\n";
                    $expout .= "    <name>".$this->writetext($def->name)."</name>\n";
                    if ( $question->qtype == CALCULATED){
                        $expout .= "    <type>calculated</type>\n";
                    }else {
                        $expout .= "    <type>calculatedsimple</type>\n";
                    }
                    $expout .= "    <distribution>".$this->writetext($def->distribution)."</distribution>\n";
                    $expout .= "    <minimum>".$this->writetext($def->minimum)."</minimum>\n";
                    $expout .= "    <maximum>".$this->writetext($def->maximum)."</maximum>\n";
                    $expout .= "    <decimals>".$this->writetext($def->decimals)."</decimals>\n";
                    $expout .= "    <itemcount>$def->itemcount</itemcount>\n";
                    if ($def->itemcount > 0 ) {
                        $expout .= "    <dataset_items>\n";
                        foreach ($def->items as $item ){
                              $expout .= "        <dataset_item>\n";
                              $expout .= "           <number>".$item->itemnumber."</number>\n";
                              $expout .= "           <value>".$item->value."</value>\n";
                              $expout .= "        </dataset_item>\n";
                        }
                        $expout .= "    </dataset_items>\n";
                        $expout .= "    <number_of_items>".$def-> number_of_items."</number_of_items>\n";
                     }
                    $expout .= "</dataset_definition>\n";
                }
                $expout .= "</dataset_definitions>\n";
            }
            break;
        default:
            // try support by optional plugin
            if (!$data = $this->try_exporting_using_qtypes( $question->qtype, $question )) {
                echo $OUTPUT->notification( get_string( 'unsupportedexport','qformat_xml',$QTYPES[$question->qtype]->local_name() ) );
            }
            $expout .= $data;
        }

        // Write the question tags.
        if (!empty($CFG->usetags)) {
            require_once($CFG->dirroot.'/tag/lib.php');
            $tags = tag_get_tags_array('question', $question->id);
            if (!empty($tags)) {
                $expout .= "    <tags>\n";
                foreach ($tags as $tag) {
                    $expout .= "      <tag>" . $this->writetext($tag, 0, true) . "</tag>\n";
                }
                $expout .= "    </tags>\n";
            }
        }

        // close the question tag
        $expout .= "</question>\n";

        return $expout;
    }
}
