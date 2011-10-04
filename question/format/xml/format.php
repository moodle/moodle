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
 * Code for exporting questions as Moodle XML.
 *
 * @package    qformat
 * @subpackage xml
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/xmlize.php');
if (!class_exists('qformat_default')) {
    // This is ugly, but this class is also (ab)used by mod/lesson, which defines
    // a different base class in mod/lesson/format.php. Thefore, we can only
    // include the proper base class conditionally like this. (We have to include
    // the base class like this, otherwise it breaks third-party question types.)
    // This may be reviewd, and a better fix found one day.
    require_once($CFG->dirroot . '/question/format.php');
}


/**
 * Importer for Moodle XML question format.
 *
 * See http://docs.moodle.org/en/Moodle_XML_format for a description of the format.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_xml extends qformat_default {

    public function provide_import() {
        return true;
    }

    public function provide_export() {
        return true;
    }

    public function mime_type() {
        return 'application/xml';
    }

    // IMPORT FUNCTIONS START HERE

    /**
     * Translate human readable format name
     * into internal Moodle code number
     * @param string name format name from xml file
     * @return int Moodle format code
     */
    protected function trans_format($name) {
        $name = trim($name);

        if ($name == 'moodle_auto_format') {
            return FORMAT_MOODLE;
        } else if ($name == 'html') {
            return FORMAT_HTML;
        } else if ($name == 'plain_text') {
            return FORMAT_PLAIN;
        } else if ($name == 'wiki_like') {
            return FORMAT_WIKI;
        } else if ($name == 'markdown') {
            return FORMAT_MARKDOWN;
        } else {
            return 0; // or maybe warning required
        }
    }

    /**
     * Translate human readable single answer option
     * to internal code number
     * @param string name true/false
     * @return int internal code number
     */
    public function trans_single($name) {
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
     * @return string processed text.
     */
    public function import_text($text) {
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
    public function getpath($xml, $path, $default, $istext=false, $error='') {
        foreach ($path as $index) {
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error($error);
                    return false;
                } else {
                    return $default;
                }
            }

            $xml = $xml[$index];
        }

        if ($istext) {
            if (!is_string($xml)) {
                $this->error(get_string('invalidxml', 'qformat_xml'));
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
    public function import_headers($question) {
        global $CFG;

        // get some error strings
        $error_noname = get_string('xmlimportnoname', 'qformat_xml');
        $error_noquestion = get_string('xmlimportnoquestion', 'qformat_xml');

        // this routine initialises the question object
        $qo = $this->defaultquestion();

        // Question name
        $qo->name = $this->getpath($question,
                array('#', 'name', 0, '#', 'text', 0, '#'), '', true,
                get_string('xmlimportnoname', 'qformat_xml'));
        $qo->questiontext = $this->getpath($question,
                array('#', 'questiontext', 0, '#', 'text', 0, '#'), '', true);
        $qo->questiontextformat = $this->trans_format($this->getpath(
                $question, array('#', 'questiontext', 0, '@', 'format'), ''));

        $qo->questiontextfiles = $this->import_files($this->getpath($question,
                array('#', 'questiontext', 0, '#', 'file'), array(), false));

        // Backwards compatibility, deal with the old image tag.
        $filedata = $this->getpath($question, array('#', 'image_base64', '0', '#'), null, false);
        $filename = $this->getpath($question, array('#', 'image', '0', '#'), null, false);
        if ($filedata && $filename) {
            $data = new stdClass();
            $data->content = $filedata;
            $data->encoding = 'base64';
            $data->name = $filename;
            $qo->questiontextfiles[] = $data;
            $qo->questiontext .= ' <img src="@@PLUGINFILE@@/' . $filename . '" />';
        }

        // restore files in generalfeedback
        $qo->generalfeedback = $this->getpath($question,
                array('#', 'generalfeedback', 0, '#', 'text', 0, '#'), $qo->generalfeedback, true);
        $qo->generalfeedbackfiles = array();
        $qo->generalfeedbackformat = $this->trans_format($this->getpath($question,
                array('#', 'generalfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->generalfeedbackfiles = $this->import_files($this->getpath($question,
                array('#', 'generalfeedback', 0, '#', 'file'), array(), false));

        $qo->defaultmark = $this->getpath($question,
                array('#', 'defaultgrade', 0, '#'), $qo->defaultmark);
        $qo->penalty = $this->getpath($question,
                array('#', 'penalty', 0, '#'), $qo->penalty);

        // Fix problematic rounding from old files:
        if (abs($qo->penalty - 0.3333333) < 0.005) {
            $qo->penalty = 0.3333333;
        }

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
     * Import the common parts of a single answer
     * @param array answer xml tree for single answer
     * @return object answer object
     */
    public function import_answer($answer, $withanswerfiles = false) {
        $ans = new stdClass();

        $ans->answer = array();
        $ans->answer['text']   = $this->getpath($answer, array('#', 'text', 0, '#'), '', true);
        $ans->answer['format'] = $this->trans_format($this->getpath($answer,
                array('@', 'format'), 'moodle_auto_format'));
        if ($withanswerfiles) {
            $ans->answer['files']  = $this->import_files($this->getpath($answer,
                    array('#', 'file'), array()));
        }

        $ans->feedback = array();
        $ans->feedback['text']   = $this->getpath($answer,
                array('#', 'feedback', 0, '#', 'text', 0, '#'), '', true);
        $ans->feedback['format'] = $this->trans_format($this->getpath($answer,
                array('#', 'feedback', 0, '@', 'format'), 'moodle_auto_format'));
        $ans->feedback['files']  = $this->import_files($this->getpath($answer,
                array('#', 'feedback', 0, '#', 'file'), array()));

        $ans->fraction = $this->getpath($answer, array('@', 'fraction'), 0) / 100;

        return $ans;
    }

    /**
     * Import the common overall feedback fields.
     * @param object $question the part of the XML relating to this question.
     * @param object $qo the question data to add the fields to.
     * @param bool $withshownumpartscorrect include the shownumcorrect field.
     */
    public function import_combined_feedback($qo, $questionxml, $withshownumpartscorrect = false) {
        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        foreach ($fields as $field) {
            $text = array();
            $text['text'] = $this->getpath($questionxml,
                    array('#', $field, 0, '#', 'text', 0, '#'), '', true);
            $text['format'] = $this->trans_format($this->getpath($questionxml,
                    array('#', $field, 0, '@', 'format'), 'moodle_auto_format'));
            $text['files'] = $this->import_files($this->getpath($questionxml,
                    array('#', $field, 0, '#', 'file'), array(), false));

            $qo->$field = $text;
        }

        if ($withshownumpartscorrect) {
            $qo->shownumcorrect = array_key_exists('shownumcorrect', $questionxml['#']);

            // Backwards compatibility:
            if (array_key_exists('correctresponsesfeedback', $questionxml['#'])) {
                $qo->shownumcorrect = $this->trans_single($this->getpath($questionxml,
                        array('#', 'correctresponsesfeedback', 0, '#'), 1));
            }
        }
    }

    /**
     * Import a question hint
     * @param array $hintxml hint xml fragment.
     * @return object hint for storing in the database.
     */
    public function import_hint($hintxml) {
        if (array_key_exists('hintcontent', $hintxml['#'])) {
            // Backwards compatibility:

            $hint = new stdClass();
            $hint->hint = array('format' => FORMAT_HTML, 'files' => array());
            $hint->hint['text'] = $this->getpath($hintxml,
                    array('#', 'hintcontent', 0, '#', 'text', 0, '#'), '', true);
            $hint->shownumcorrect = $this->getpath($hintxml,
                    array('#', 'statenumberofcorrectresponses', 0, '#'), 0);
            $hint->clearwrong = $this->getpath($hintxml,
                    array('#', 'clearincorrectresponses', 0, '#'), 0);
            $hint->options = $this->getpath($hintxml,
                    array('#', 'showfeedbacktoresponses', 0, '#'), 0);

            return $hint;
        }

        $hint = new stdClass();
        $hint->hint['text'] = $this->getpath($hintxml,
                array('#', 'text', 0, '#'), '', true);
        $hint->hint['format'] = $this->trans_format($this->getpath($hintxml,
                array('@', 'format'), 'moodle_auto_format'));
        $hint->hint['files'] = $this->import_files($this->getpath($hintxml,
                array('#', 'file'), array(), false));
        $hint->shownumcorrect = array_key_exists('shownumcorrect', $hintxml['#']);
        $hint->clearwrong = array_key_exists('clearwrong', $hintxml['#']);
        $hint->options = $this->getpath($hintxml, array('#', 'options', 0, '#'), '', true);

        return $hint;
    }

    /**
     * Import all the question hints
     *
     * @param object $qo the question data that is being constructed.
     * @param array $hintsxml hints xml fragment.
     */
    public function import_hints($qo, $questionxml, $withparts = false, $withoptions = false) {
        if (!isset($questionxml['#']['hint'])) {
            return;
        }

        foreach ($questionxml['#']['hint'] as $hintxml) {
            $hint = $this->import_hint($hintxml);
            $qo->hint[] = $hint->hint;

            if ($withparts) {
                $qo->hintshownumcorrect[] = $hint->shownumcorrect;
                $qo->hintclearwrong[] = $hint->clearwrong;
            }

            if ($withoptions) {
                $qo->hintoptions[] = $hint->options;
            }
        }
    }

    /**
     * Import files from a node in the XML.
     * @param array $xml an array of <file> nodes from the the parsed XML.
     * @return array of things representing files - in the form that save_question expects.
     */
    public function import_files($xml) {
        $files = array();
        foreach ($xml as $file) {
            $data = new stdClass();
            $data->content = $file['#'];
            $data->encoding = $file['@']['encoding'];
            $data->name = $file['@']['name'];
            $files[] = $data;
        }
        return $files;
    }

    /**
     * import multiple choice question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_multichoice($question) {
        // get common parts
        $qo = $this->import_headers($question);

        // 'header' parts particular to multichoice
        $qo->qtype = MULTICHOICE;
        $single = $this->getpath($question, array('#', 'single', 0, '#'), 'true');
        $qo->single = $this->trans_single($single);
        $shuffleanswers = $this->getpath($question,
                array('#', 'shuffleanswers', 0, '#'), 'false');
        $qo->answernumbering = $this->getpath($question,
                array('#', 'answernumbering', 0, '#'), 'abc');
        $qo->shuffleanswers = $this->trans_single($shuffleanswers);

        // There was a time on the 1.8 branch when it could output an empty
        // answernumbering tag, so fix up any found.
        if (empty($qo->answernumbering)) {
            $qo->answernumbering = 'abc';
        }

        // Run through the answers
        $answers = $question['#']['answer'];
        $acount = 0;
        foreach ($answers as $answer) {
            $ans = $this->import_answer($answer, true);
            $qo->answer[$acount] = $ans->answer;
            $qo->fraction[$acount] = $ans->fraction;
            $qo->feedback[$acount] = $ans->feedback;
            ++$acount;
        }

        $this->import_combined_feedback($qo, $question, true);
        $this->import_hints($qo, $question, true);

        return $qo;
    }

    /**
     * Import cloze type question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_multianswer($question) {
        question_bank::get_qtype('multianswer');

        $questiontext['text'] = $this->import_text($question['#']['questiontext'][0]['#']['text']);
        $questiontext['format'] = '1';
        $questiontext['itemid'] = '';
        $qo = qtype_multianswer_extract_question($questiontext);

        // 'header' parts particular to multianswer
        $qo->qtype = 'multianswer';
        $qo->course = $this->course;
        $qo->generalfeedback = '';
        // restore files in generalfeedback
        $qo->generalfeedback = $this->getpath($question,
                array('#', 'generalfeedback', 0, '#', 'text', 0, '#'), $qo->generalfeedback, true);
        $qo->generalfeedbackformat = $this->trans_format($this->getpath($question,
                array('#', 'generalfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->generalfeedbackfiles = $this->import_files($this->getpath($question,
                array('#', 'generalfeedback', 0, '#', 'file'), array(), false));

        $qo->name = $this->import_text($question['#']['name'][0]['#']['text']);
        $qo->questiontext = $qo->questiontext['text'];
        $qo->questiontextformat = '';

        $qo->penalty = $this->getpath($question,
                array('#', 'penalty', 0, '#'), $this->defaultquestion()->penalty);
        // Fix problematic rounding from old files:
        if (abs($qo->penalty - 0.3333333) < 0.005) {
            $qo->penalty = 0.3333333;
        }

        $this->import_hints($qo, $question);

        return $qo;
    }

    /**
     * Import true/false type question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_truefalse($question) {
        // get common parts
        global $OUTPUT;
        $qo = $this->import_headers($question);

        // 'header' parts particular to true/false
        $qo->qtype = TRUEFALSE;

        // In the past, it used to be assumed that the two answers were in the file
        // true first, then false. Howevever that was not always true. Now, we
        // try to match on the answer text, but in old exports, this will be a localised
        // string, so if we don't find true or false, we fall back to the old system.
        $first = true;
        $warning = false;
        foreach ($question['#']['answer'] as $answer) {
            $answertext = $this->getpath($answer,
                    array('#', 'text', 0, '#'), '', true);
            $feedback = $this->getpath($answer,
                    array('#', 'feedback', 0, '#', 'text', 0, '#'), '', true);
            $feedbackformat = $this->getpath($answer,
                    array('#', 'feedback', 0, '@', 'format'), 'moodle_auto_format');
            $feedbackfiles = $this->getpath($answer,
                    array('#', 'feedback', 0, '#', 'file'), array());
            $files = array();
            foreach ($feedbackfiles as $file) {
                $data = new stdClass();
                $data->content = $file['#'];
                $data->encoding = $file['@']['encoding'];
                $data->name = $file['@']['name'];
                $files[] = $data;
            }
            if ($answertext != 'true' && $answertext != 'false') {
                // Old style file, assume order is true/false.
                $warning = true;
                if ($first) {
                    $answertext = 'true';
                } else {
                    $answertext = 'false';
                }
            }

            if ($answertext == 'true') {
                $qo->answer = ($answer['@']['fraction'] == 100);
                $qo->correctanswer = $qo->answer;
                $qo->feedbacktrue = array();
                $qo->feedbacktrue['text'] = $feedback;
                $qo->feedbacktrue['format'] = $this->trans_format($feedbackformat);
                $qo->feedbacktrue['files'] = $files;
            } else {
                $qo->answer = ($answer['@']['fraction'] != 100);
                $qo->correctanswer = $qo->answer;
                $qo->feedbackfalse = array();
                $qo->feedbackfalse['text'] = $feedback;
                $qo->feedbackfalse['format'] = $this->trans_format($feedbackformat);
                $qo->feedbackfalse['files'] = $files;
            }
            $first = false;
        }

        if ($warning) {
            $a = new stdClass();
            $a->questiontext = $qo->questiontext;
            $a->answer = get_string($qo->correctanswer ? 'true' : 'false', 'qtype_truefalse');
            echo $OUTPUT->notification(get_string('truefalseimporterror', 'qformat_xml', $a));
        }

        $this->import_hints($qo, $question);

        return $qo;
    }

    /**
     * Import short answer type question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_shortanswer($question) {
        // get common parts
        $qo = $this->import_headers($question);

        // header parts particular to shortanswer
        $qo->qtype = SHORTANSWER;

        // get usecase
        $qo->usecase = $this->getpath($question, array('#', 'usecase', 0, '#'), $qo->usecase);

        // Run through the answers
        $answers = $question['#']['answer'];
        $acount = 0;
        foreach ($answers as $answer) {
            $ans = $this->import_answer($answer);
            $qo->answer[$acount] = $ans->answer['text'];
            $qo->fraction[$acount] = $ans->fraction;
            $qo->feedback[$acount] = $ans->feedback;
            ++$acount;
        }

        $this->import_hints($qo, $question);

        return $qo;
    }

    /**
     * Import description type question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_description($question) {
        // get common parts
        $qo = $this->import_headers($question);
        // header parts particular to shortanswer
        $qo->qtype = DESCRIPTION;
        $qo->defaultmark = 0;
        $qo->length = 0;
        return $qo;
    }

    /**
     * Import numerical type question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_numerical($question) {
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
            $qo->fraction[] = $this->getpath($answer,
                    array('#', 'fraction', 0, '#'), $fraction); // deprecated
        }

        // Get the units array
        $qo->unit = array();
        $units = $this->getpath($question, array('#', 'units', 0, '#', 'unit'), array());
        if (!empty($units)) {
            $qo->multiplier = array();
            foreach ($units as $unit) {
                $qo->multiplier[] = $this->getpath($unit, array('#', 'multiplier', 0, '#'), 1);
                $qo->unit[] = $this->getpath($unit, array('#', 'unit_name', 0, '#'), '', true);
            }
        }
        $qo->unitgradingtype = $this->getpath($question, array('#', 'unitgradingtype', 0, '#'), 0);
        $qo->unitpenalty = $this->getpath($question, array('#', 'unitpenalty', 0, '#'), 0);
        $qo->showunits = $this->getpath($question, array('#', 'showunits', 0, '#'), 0);
        $qo->unitsleft = $this->getpath($question, array('#', 'unitsleft', 0, '#'), 0);
        $qo->instructions['text'] = '';
        $qo->instructions['format'] = FORMAT_HTML;
        $instructions = $this->getpath($question, array('#', 'instructions'), array());
        if (!empty($instructions)) {
            $qo->instructions = array();
            $qo->instructions['text'] = $this->getpath($instructions,
                    array('0', '#', 'text', '0', '#'), '', true);
            $qo->instructions['format'] = $this->trans_format($this->getpath($instructions,
                    array('0', '@', 'format'), 'moodle_auto_format'));
            $qo->instructions['files'] = $this->import_files($this->getpath(
                    $instructions, array('0', '#', 'file'), array()));
        }

        $this->import_hints($qo, $question);

        return $qo;
    }

    /**
     * Import matching type question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_match($question) {
        // get common parts
        $qo = $this->import_headers($question);

        // header parts particular to matching
        $qo->qtype = 'match';
        $qo->shuffleanswers = $this->trans_single($this->getpath($question,
                array('#', 'shuffleanswers', 0, '#'), 1));

        // run through subquestions
        $qo->subquestions = array();
        $qo->subanswers = array();
        foreach ($question['#']['subquestion'] as $subqxml) {
            $subquestion = array();
            $subquestion['text'] = $this->getpath($subqxml, array('#', 'text', 0, '#'), '', true);
            $subquestion['format'] = $this->trans_format($this->getpath($subqxml,
                    array('@', 'format'), 'moodle_auto_format'));
            $subquestion['files'] = $this->import_files($this->getpath($subqxml,
                    array('#', 'file'), array()));

            $qo->subquestions[] = $subquestion;
            $answers = $this->getpath($subqxml, array('#', 'answer'), array());
            $qo->subanswers[] = $this->getpath($subqxml,
                    array('#', 'answer', 0, '#', 'text', 0, '#'), '', true);
        }

        $this->import_combined_feedback($qo, $question, true);
        $this->import_hints($qo, $question, true);

        return $qo;
    }

    /**
     * Import essay type question
     * @param array question question array from xml tree
     * @return object question object
     */
    public function import_essay($question) {
        // get common parts
        $qo = $this->import_headers($question);

        // header parts particular to essay
        $qo->qtype = ESSAY;

        $qo->responseformat = $this->getpath($question,
                array('#', 'responseformat', 0, '#'), 'editor');
        $qo->responsefieldlines = $this->getpath($question,
                array('#', 'responsefieldlines', 0, '#'), 15);
        $qo->attachments = $this->getpath($question,
                array('#', 'attachments', 0, '#'), 0);
        $qo->graderinfo['text'] = $this->getpath($question,
                array('#', 'graderinfo', 0, '#', 'text', 0, '#'), '', true);
        $qo->graderinfo['format'] = $this->trans_format($this->getpath($question,
                array('#', 'graderinfo', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->graderinfo['files'] = $this->import_files($this->getpath($question,
                array('#', 'graderinfo', '0', '#', 'file'), array()));

        return $qo;
    }

    /**
     * Import a calculated question
     * @param object $question the imported XML data.
     */
    public function import_calculated($question) {

        // get common parts
        $qo = $this->import_headers($question);

        // header parts particular to calculated
        $qo->qtype = CALCULATED;
        $qo->synchronize = $this->getpath($question, array('#', 'synchronize', 0, '#'), 0);
        $single = $this->getpath($question, array('#', 'single', 0, '#'), 'true');
        $qo->single = $this->trans_single($single);
        $shuffleanswers = $this->getpath($question, array('#', 'shuffleanswers', 0, '#'), 'false');
        $qo->answernumbering = $this->getpath($question,
                array('#', 'answernumbering', 0, '#'), 'abc');
        $qo->shuffleanswers = $this->trans_single($shuffleanswers);

        $qo->correctfeedback = array();
        $qo->correctfeedback['text'] = $this->getpath(
                $question, array('#', 'correctfeedback', 0, '#', 'text', 0, '#'), '', true);
        $qo->correctfeedback['format'] = $this->trans_format($this->getpath(
                $question, array('#', 'correctfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->correctfeedback['files'] = $this->import_files($this->getpath(
                $question, array('#', 'correctfeedback', '0', '#', 'file'), array()));

        $qo->partiallycorrectfeedback = array();
        $qo->partiallycorrectfeedback['text'] = $this->getpath($question,
                array('#', 'partiallycorrectfeedback', 0, '#', 'text', 0, '#'), '', true);
        $qo->partiallycorrectfeedback['format'] = $this->trans_format(
                $this->getpath($question, array('#', 'partiallycorrectfeedback', 0, '@', 'format'),
                'moodle_auto_format'));
        $qo->partiallycorrectfeedback['files'] = $this->import_files($this->getpath(
                $question, array('#', 'partiallycorrectfeedback', '0', '#', 'file'), array()));

        $qo->incorrectfeedback = array();
        $qo->incorrectfeedback['text'] = $this->getpath($question,
                array('#', 'incorrectfeedback', 0, '#', 'text', 0, '#'), '', true);
        $qo->incorrectfeedback['format'] = $this->trans_format($this->getpath($question,
                array('#', 'incorrectfeedback', 0, '@', 'format'), 'moodle_auto_format'));
        $qo->incorrectfeedback['files'] = $this->import_files($this->getpath($question,
                array('#', 'incorrectfeedback', '0', '#', 'file'), array()));

        $qo->unitgradingtype = $this->getpath($question,
                array('#', 'unitgradingtype', 0, '#'), 0);
        $qo->unitpenalty = $this->getpath($question, array('#', 'unitpenalty', 0, '#'), 0);
        $qo->showunits = $this->getpath($question, array('#', 'showunits', 0, '#'), 0);
        $qo->unitsleft = $this->getpath($question, array('#', 'unitsleft', 0, '#'), 0);
        $qo->instructions = $this->getpath($question,
                array('#', 'instructions', 0, '#', 'text', 0, '#'), '', true);
        if (!empty($instructions)) {
            $qo->instructions = array();
            $qo->instructions['text'] = $this->getpath($instructions,
                    array('0', '#', 'text', '0', '#'), '', true);
            $qo->instructions['format'] = $this->trans_format($this->getpath($instructions,
                    array('0', '@', 'format'), 'moodle_auto_format'));
            $qo->instructions['files'] = $this->import_files($this->getpath($instructions,
                    array('0', '#', 'file'), array()));
        }

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
            $ans = $this->import_answer($answer, true);
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
            $qo->instructions['files'] = $this->import_files($this->getpath($instructions,
                    array('0', '#', 'file'), array()));
        }
        $datasets = $question['#']['dataset_definitions'][0]['#']['dataset_definition'];
        $qo->dataset = array();
        $qo->datasetindex= 0;
        foreach ($datasets as $dataset) {
            $qo->datasetindex++;
            $qo->dataset[$qo->datasetindex] = new stdClass();
            $qo->dataset[$qo->datasetindex]->status =
                    $this->import_text($dataset['#']['status'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->name =
                    $this->import_text($dataset['#']['name'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->type =
                    $dataset['#']['type'][0]['#'];
            $qo->dataset[$qo->datasetindex]->distribution =
                    $this->import_text($dataset['#']['distribution'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->max =
                    $this->import_text($dataset['#']['maximum'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->min =
                    $this->import_text($dataset['#']['minimum'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->length =
                    $this->import_text($dataset['#']['decimals'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->distribution =
                    $this->import_text($dataset['#']['distribution'][0]['#']['text']);
            $qo->dataset[$qo->datasetindex]->itemcount = $dataset['#']['itemcount'][0]['#'];
            $qo->dataset[$qo->datasetindex]->datasetitem = array();
            $qo->dataset[$qo->datasetindex]->itemindex = 0;
            $qo->dataset[$qo->datasetindex]->number_of_items =
                    $dataset['#']['number_of_items'][0]['#'];
            $datasetitems = $dataset['#']['dataset_items'][0]['#']['dataset_item'];
            foreach ($datasetitems as $datasetitem) {
                $qo->dataset[$qo->datasetindex]->itemindex++;
                $qo->dataset[$qo->datasetindex]->datasetitem[
                        $qo->dataset[$qo->datasetindex]->itemindex] = new stdClass();
                $qo->dataset[$qo->datasetindex]->datasetitem[
                        $qo->dataset[$qo->datasetindex]->itemindex]->itemnumber =
                                $datasetitem['#']['number'][0]['#'];
                $qo->dataset[$qo->datasetindex]->datasetitem[
                        $qo->dataset[$qo->datasetindex]->itemindex]->value =
                                $datasetitem['#']['value'][0]['#'];
            }
        }

        $this->import_hints($qo, $question);

        return $qo;
    }

    /**
     * This is not a real question type. It's a dummy type used to specify the
     * import category. The format is:
     * <question type="category">
     *     <category>tom/dick/harry</category>
     * </question>
     */
    protected function import_category($question) {
        $qo = new stdClass();
        $qo->qtype = 'category';
        $qo->category = $this->import_text($question['#']['category'][0]['#']['text']);
        return $qo;
    }

    /**
     * Parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array of lines from the input file.
     * @return array (of objects) question objects.
     */
    protected function readquestions($lines) {
        // We just need it as one big string
        $text = implode($lines, ' ');
        unset($lines);

        // This converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        try {
            $xml = xmlize($text, 0, 'UTF-8', true);
        } catch (xml_format_exception $e) {
            $this->error($e->getMessage(), '');
            return false;
        }
        // Set up array to hold all our questions
        $questions = array();

        // Iterate through questions
        foreach ($xml['quiz']['#']['question'] as $question) {
            $questiontype = $question['@']['type'];

            if ($questiontype == 'multichoice') {
                $qo = $this->import_multichoice($question);
            } else if ($questiontype == 'truefalse') {
                $qo = $this->import_truefalse($question);
            } else if ($questiontype == 'shortanswer') {
                $qo = $this->import_shortanswer($question);
            } else if ($questiontype == 'numerical') {
                $qo = $this->import_numerical($question);
            } else if ($questiontype == 'description') {
                $qo = $this->import_description($question);
            } else if ($questiontype == 'matching' || $questiontype == 'match') {
                $qo = $this->import_match($question);
            } else if ($questiontype == 'cloze' || $questiontype == 'multianswer') {
                $qo = $this->import_multianswer($question);
            } else if ($questiontype == 'essay') {
                $qo = $this->import_essay($question);
            } else if ($questiontype == 'calculated') {
                $qo = $this->import_calculated($question);
            } else if ($questiontype == 'calculatedsimple') {
                $qo = $this->import_calculated($question);
                $qo->qtype = 'calculatedsimple';
            } else if ($questiontype == 'calculatedmulti') {
                $qo = $this->import_calculated($question);
                $qo->qtype = 'calculatedmulti';
            } else if ($questiontype == 'category') {
                $qo = $this->import_category($question);

            } else {
                // Not a type we handle ourselves. See if the question type wants
                // to handle it.
                if (!$qo = $this->try_importing_using_qtypes(
                        $question, null, null, $questiontype)) {
                    $this->error(get_string('xmltypeunsupported', 'qformat_xml', $questiontype));
                    $qo = null;
                }
            }

            // Stick the result in the $questions array
            if ($qo) {
                $questions[] = $qo;
            }
        }
        return $questions;
    }

    // EXPORT FUNCTIONS START HERE

    public function export_file_extension() {
        return '.xml';
    }

    /**
     * Turn the internal question type name into a human readable form.
     * (In the past, the code used to use integers internally. Now, it uses
     * strings, so there is less need for this, but to maintain
     * backwards-compatibility we change two of the type names.)
     * @param string $qtype question type plugin name.
     * @return string $qtype string to use in the file.
     */
    protected function get_qtype($qtype) {
        switch($qtype) {
            case 'match':
                return 'matching';
            case 'multianswer':
                return 'cloze';
            default:
                return $qtype;
        }
    }

    /**
     * Convert internal Moodle text format code into
     * human readable form
     * @param int id internal code
     * @return string format text
     */
    protected function get_format($id) {
        switch($id) {
            case FORMAT_MOODLE:
                return 'moodle_auto_format';
            case FORMAT_HTML:
                return 'html';
            case FORMAT_PLAIN:
                return 'plain_text';
            case FORMAT_WIKI:
                return 'wiki_like';
            case FORMAT_MARKDOWN:
                return 'markdown';
            default:
                return 'unknown';
        }
    }

    /**
     * Convert internal single question code into
     * human readable form
     * @param int id single question code
     * @return string single question string
     */
    public function get_single($id) {
        switch($id) {
            case 0:
                return 'false';
            case 1:
                return 'true';
            default:
                return 'unknown';
        }
    }

    /**
     * Take a string, and wrap it in a CDATA secion, if that is required to make
     * the output XML valid.
     * @param string $string a string
     * @return string the string, wrapped in CDATA if necessary.
     */
    public function xml_escape($string) {
        if (!empty($string) && htmlspecialchars($string) != $string) {
            return "<![CDATA[{$string}]]>";
        } else {
            return $string;
        }
    }

    /**
     * Generates <text></text> tags, processing raw text therein
     * @param string $raw the content to output.
     * @param int $indent the current indent level.
     * @param bool $short stick it on one line.
     * @return string formatted text.
     */
    public function writetext($raw, $indent = 0, $short = true) {
        $indent = str_repeat('  ', $indent);
        $raw = $this->xml_escape($raw);

        if ($short) {
            $xml = "$indent<text>$raw</text>\n";
        } else {
            $xml = "$indent<text>\n$raw\n$indent</text>\n";
        }

        return $xml;
    }

    protected function presave_process($content) {
        // Override to allow us to add xml headers and footers
        return '<?xml version="1.0" encoding="UTF-8"?>
<quiz>
' . $content . '</quiz>';
    }

    /**
     * Turns question into an xml segment
     * @param object $question the question data.
     * @return string xml segment
     */
    public function writequestion($question) {
        global $CFG, $OUTPUT;

        $fs = get_file_storage();
        $contextid = $question->contextid;
        // Get files used by the questiontext.
        $question->questiontextfiles = $fs->get_area_files(
                $contextid, 'question', 'questiontext', $question->id);
        // Get files used by the generalfeedback.
        $question->generalfeedbackfiles = $fs->get_area_files(
                $contextid, 'question', 'generalfeedback', $question->id);
        if (!empty($question->options->answers)) {
            foreach ($question->options->answers as $answer) {
                $answer->answerfiles = $fs->get_area_files(
                        $contextid, 'question', 'answer', $answer->id);
                $answer->feedbackfiles = $fs->get_area_files(
                        $contextid, 'question', 'answerfeedback', $answer->id);
            }
        }

        $expout = '';

        // Add a comment linking this to the original question id.
        $expout .= "<!-- question: $question->id  -->\n";

        // Check question type
        $questiontype = $this->get_qtype($question->qtype);

        // Categories are a special case.
        if ($question->qtype == 'category') {
            $categorypath = $this->writetext($question->category);
            $expout .= "  <question type=\"category\">\n";
            $expout .= "    <category>\n";
            $expout .= "        $categorypath\n";
            $expout .= "    </category>\n";
            $expout .= "  </question>\n";
            return $expout;
        }

        // Now we know we are are handing a real question.
        // Output the generic information.
        $expout .= "  <question type=\"$questiontype\">\n";
        $expout .= "    <name>\n";
        $expout .= $this->writetext($question->name, 3);
        $expout .= "    </name>\n";
        $expout .= "    <questiontext {$this->format($question->questiontextformat)}>\n";
        $expout .= $this->writetext($question->questiontext, 3);
        $expout .= $this->writefiles($question->questiontextfiles);
        $expout .= "    </questiontext>\n";
        $expout .= "    <generalfeedback {$this->format($question->generalfeedbackformat)}>\n";
        $expout .= $this->writetext($question->generalfeedback, 3);
        $expout .= $this->writefiles($question->generalfeedbackfiles);
        $expout .= "    </generalfeedback>\n";
        if ($question->qtype != 'multianswer') {
            $expout .= "    <defaultgrade>{$question->defaultmark}</defaultgrade>\n";
        }
        $expout .= "    <penalty>{$question->penalty}</penalty>\n";
        $expout .= "    <hidden>{$question->hidden}</hidden>\n";

        // The rest of the output depends on question type.
        switch($question->qtype) {
            case 'category':
                // not a qtype really - dummy used for category switching
                break;

            case 'truefalse':
                $trueanswer = $question->options->answers[$question->options->trueanswer];
                $trueanswer->answer = 'true';
                $expout .= $this->write_answer($trueanswer);

                $falseanswer = $question->options->answers[$question->options->falseanswer];
                $falseanswer->answer = 'false';
                $expout .= $this->write_answer($falseanswer);
                break;

            case 'multichoice':
                $expout .= "    <single>" . $this->get_single($question->options->single) .
                        "</single>\n";
                $expout .= "    <shuffleanswers>" .
                        $this->get_single($question->options->shuffleanswers) .
                        "</shuffleanswers>\n";
                $expout .= "    <answernumbering>" . $question->options->answernumbering .
                        "</answernumbering>\n";
                $expout .= $this->write_combined_feedback($question->options);
                $expout .= $this->write_answers($question->options->answers);
                break;

            case 'shortanswer':
                $expout .= "    <usecase>{$question->options->usecase}</usecase>\n";
                $expout .= $this->write_answers($question->options->answers);
                break;

            case 'numerical':
                foreach ($question->options->answers as $answer) {
                    $expout .= $this->write_answer($answer,
                            "      <tolerance>$answer->tolerance</tolerance>\n");
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
                    $expout .= "    <unitgradingtype>" . $question->options->unitgradingtype .
                            "</unitgradingtype>\n";
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
                    $files = $fs->get_area_files($contextid, 'qtype_numerical',
                            'instruction', $question->id);
                    $expout .= "    <instructions " .
                            $this->format($question->options->instructionsformat) . ">\n";
                    $expout .= $this->writetext($question->options->instructions, 3);
                    $expout .= $this->writefiles($files);
                    $expout .= "    </instructions>\n";
                }
                break;

            case 'match':
                $expout .= "    <shuffleanswers>" .
                        $this->get_single($question->options->shuffleanswers) .
                        "</shuffleanswers>\n";
                $expout .= $this->write_combined_feedback($question->options);
                foreach ($question->options->subquestions as $subquestion) {
                    $files = $fs->get_area_files($contextid, 'qtype_match',
                            'subquestion', $subquestion->id);
                    $expout .= "    <subquestion " .
                            $this->format($subquestion->questiontextformat) . ">\n";
                    $expout .= $this->writetext($subquestion->questiontext, 3);
                    $expout .= $this->writefiles($files);
                    $expout .= "      <answer>\n";
                    $expout .= $this->writetext($subquestion->answertext, 4);
                    $expout .= "      </answer>\n";
                    $expout .= "    </subquestion>\n";
                }
                break;

            case 'description':
                // Nothing else to do.
                break;

            case 'multianswer':
                foreach ($question->options->questions as $index => $subq) {
                    $expout = preg_replace('~{#' . $index . '}~', $subq->questiontext, $expout);
                }
                break;

            case 'essay':
                $expout .= "    <responseformat>" . $question->options->responseformat .
                        "</responseformat>\n";
                $expout .= "    <responsefieldlines>" . $question->options->responsefieldlines .
                        "</responsefieldlines>\n";
                $expout .= "    <attachments>" . $question->options->attachments .
                        "</attachments>\n";
                $expout .= "    <graderinfo " .
                        $this->format($question->options->graderinfoformat) . ">\n";
                $expout .= $this->writetext($question->options->graderinfo, 3);
                $expout .= $this->writefiles($fs->get_area_files($contextid, 'qtype_essay',
                        'graderinfo', $question->id));
                $expout .= "    </graderinfo>\n";
                break;

            case 'calculated':
            case 'calculatedsimple':
            case 'calculatedmulti':
                $expout .= "    <synchronize>{$question->options->synchronize}</synchronize>\n";
                $expout .= "    <single>{$question->options->single}</single>\n";
                $expout .= "    <answernumbering>" . $question->options->answernumbering .
                        "</answernumbering>\n";
                $expout .= "    <shuffleanswers>" . $question->options->shuffleanswers .
                        "</shuffleanswers>\n";

                $component = 'qtype_' . $question->qtype;
                $files = $fs->get_area_files($contextid, $component,
                        'correctfeedback', $question->id);
                $expout .= "    <correctfeedback>\n";
                $expout .= $this->writetext($question->options->correctfeedback, 3);
                $expout .= $this->writefiles($files);
                $expout .= "    </correctfeedback>\n";

                $files = $fs->get_area_files($contextid, $component,
                        'partiallycorrectfeedback', $question->id);
                $expout .= "    <partiallycorrectfeedback>\n";
                $expout .= $this->writetext($question->options->partiallycorrectfeedback, 3);
                $expout .= $this->writefiles($files);
                $expout .= "    </partiallycorrectfeedback>\n";

                $files = $fs->get_area_files($contextid, $component,
                        'incorrectfeedback', $question->id);
                $expout .= "    <incorrectfeedback>\n";
                $expout .= $this->writetext($question->options->incorrectfeedback, 3);
                $expout .= $this->writefiles($files);
                $expout .= "    </incorrectfeedback>\n";

                foreach ($question->options->answers as $answer) {
                    $percent = 100 * $answer->fraction;
                    $expout .= "<answer fraction=\"$percent\">\n";
                    // "<text/>" tags are an added feature, old files won't have them
                    $expout .= "    <text>{$answer->answer}</text>\n";
                    $expout .= "    <tolerance>{$answer->tolerance}</tolerance>\n";
                    $expout .= "    <tolerancetype>{$answer->tolerancetype}</tolerancetype>\n";
                    $expout .= "    <correctanswerformat>" .
                            $answer->correctanswerformat . "</correctanswerformat>\n";
                    $expout .= "    <correctanswerlength>" .
                            $answer->correctanswerlength . "</correctanswerlength>\n";
                    $expout .= "    <feedback {$this->format($answer->feedbackformat)}>\n";
                    $files = $fs->get_area_files($contextid, $component,
                            'instruction', $question->id);
                    $expout .= $this->writetext($answer->feedback);
                    $expout .= $this->writefiles($answer->feedbackfiles);
                    $expout .= "    </feedback>\n";
                    $expout .= "</answer>\n";
                }
                if (isset($question->options->unitgradingtype)) {
                    $expout .= "    <unitgradingtype>" .
                            $question->options->unitgradingtype . "</unitgradingtype>\n";
                }
                if (isset($question->options->unitpenalty)) {
                    $expout .= "    <unitpenalty>" .
                            $question->options->unitpenalty . "</unitpenalty>\n";
                }
                if (isset($question->options->showunits)) {
                    $expout .= "    <showunits>{$question->options->showunits}</showunits>\n";
                }
                if (isset($question->options->unitsleft)) {
                    $expout .= "    <unitsleft>{$question->options->unitsleft}</unitsleft>\n";
                }

                if (isset($question->options->instructionsformat)) {
                    $files = $fs->get_area_files($contextid, $component,
                            'instruction', $question->id);
                    $expout .= "    <instructions " .
                            $this->format($question->options->instructionsformat) . ">\n";
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

                // The tag $question->export_process has been set so we get all the
                // data items in the database from the function
                // qtype_calculated::get_question_options calculatedsimple defaults
                // to calculated
                if (isset($question->options->datasets) && count($question->options->datasets)) {
                    $expout .= "<dataset_definitions>\n";
                    foreach ($question->options->datasets as $def) {
                        $expout .= "<dataset_definition>\n";
                        $expout .= "    <status>".$this->writetext($def->status)."</status>\n";
                        $expout .= "    <name>".$this->writetext($def->name)."</name>\n";
                        if ($question->qtype == CALCULATED) {
                            $expout .= "    <type>calculated</type>\n";
                        } else {
                            $expout .= "    <type>calculatedsimple</type>\n";
                        }
                        $expout .= "    <distribution>" . $this->writetext($def->distribution) .
                                "</distribution>\n";
                        $expout .= "    <minimum>" . $this->writetext($def->minimum) .
                                "</minimum>\n";
                        $expout .= "    <maximum>" . $this->writetext($def->maximum) .
                                "</maximum>\n";
                        $expout .= "    <decimals>" . $this->writetext($def->decimals) .
                                "</decimals>\n";
                        $expout .= "    <itemcount>$def->itemcount</itemcount>\n";
                        if ($def->itemcount > 0) {
                            $expout .= "    <dataset_items>\n";
                            foreach ($def->items as $item) {
                                  $expout .= "        <dataset_item>\n";
                                  $expout .= "           <number>".$item->itemnumber."</number>\n";
                                  $expout .= "           <value>".$item->value."</value>\n";
                                  $expout .= "        </dataset_item>\n";
                            }
                            $expout .= "    </dataset_items>\n";
                            $expout .= "    <number_of_items>" . $def->number_of_items .
                                    "</number_of_items>\n";
                        }
                        $expout .= "</dataset_definition>\n";
                    }
                    $expout .= "</dataset_definitions>\n";
                }
                break;

            default:
                // try support by optional plugin
                if (!$data = $this->try_exporting_using_qtypes($question->qtype, $question)) {
                    notify(get_string('unsupportedexport', 'qformat_xml', $question->qtype));
                }
                $expout .= $data;
        }

        // Output any hints.
        $expout .= $this->write_hints($question);

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
        $expout .= "  </question>\n";

        return $expout;
    }

    public function write_answers($answers) {
        if (empty($answers)) {
            return;
        }
        $output = '';
        foreach ($answers as $answer) {
            $output .= $this->write_answer($answer);
        }
        return $output;
    }

    public function write_answer($answer, $extra = '') {
        $percent = $answer->fraction * 100;
        $output = '';
        $output .= "    <answer fraction=\"$percent\" {$this->format($answer->answerformat)}>\n";
        $output .= $this->writetext($answer->answer, 3);
        $output .= $this->writefiles($answer->answerfiles);
        $output .= "      <feedback {$this->format($answer->feedbackformat)}>\n";
        $output .= $this->writetext($answer->feedback, 4);
        $output .= $this->writefiles($answer->feedbackfiles);
        $output .= "      </feedback>\n";
        $output .= $extra;
        $output .= "    </answer>\n";
        return $output;
    }

    public function write_hints($question) {
        if (empty($question->hints)) {
            return '';
        }

        $output = '';
        foreach ($question->hints as $hint) {
            $output .= $this->write_hint($hint);
        }
        return $output;
    }

    /**
     * @param unknown_type $format a FORMAT_... constant.
     * @return string the attribute to add to an XML tag.
     */
    protected function format($format) {
        return 'format="' . $this->get_format($format) . '"';
    }

    public function write_hint($hint) {
        $output = '';
        $output .= "    <hint {$this->format($hint->hintformat)}>\n";
        $output .= '      ' . $this->writetext($hint->hint);
        if (!empty($hint->shownumcorrect)) {
            $output .= "      <shownumcorrect/>\n";
        }
        if (!empty($hint->clearwrong)) {
            $output .= "      <clearwrong/>\n";
        }
        if (!empty($hint->options)) {
            $output .= '      <options>' . $this->xml_escape($hint->options) . "</options>\n";
        }
        $output .= "    </hint>\n";
        return $output;
    }

    public function write_combined_feedback($questionoptions) {
        $output = "    <correctfeedback {$this->format($questionoptions->correctfeedbackformat)}>
      {$this->writetext($questionoptions->correctfeedback)}    </correctfeedback>
    <partiallycorrectfeedback {$this->format($questionoptions->partiallycorrectfeedbackformat)}>
      {$this->writetext($questionoptions->partiallycorrectfeedback)}    </partiallycorrectfeedback>
    <incorrectfeedback {$this->format($questionoptions->incorrectfeedbackformat)}>
      {$this->writetext($questionoptions->incorrectfeedback)}    </incorrectfeedback>\n";
        if (!empty($questionoptions->shownumcorrect)) {
            $output .= "    <shownumcorrect/>\n";
        }
        return $output;
    }
}
