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
 * GIFT format question importer/exporter.
 *
 * @package    qformat_gift
 * @copyright  2003 Paul Tsuchido Shew
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * The GIFT import filter was designed as an easy to use method
 * for teachers writing questions as a text file. It supports most
 * question types and the missing word format.
 *
 * Multiple Choice / Missing Word
 *     Who's buried in Grant's tomb?{~Grant ~Jefferson =no one}
 *     Grant is {~buried =entombed ~living} in Grant's tomb.
 * True-False:
 *     Grant is buried in Grant's tomb.{FALSE}
 * Short-Answer.
 *     Who's buried in Grant's tomb?{=no one =nobody}
 * Numerical
 *     When was Ulysses S. Grant born?{#1822:5}
 * Matching
 *     Match the following countries with their corresponding
 *     capitals.{=Canada->Ottawa =Italy->Rome =Japan->Tokyo}
 *
 * Comment lines start with a double backslash (//).
 * Optional question names are enclosed in double colon(::).
 * Answer feedback is indicated with hash mark (#).
 * Percentage answer weights immediately follow the tilde (for
 * multiple choice) or equal sign (for short answer and numerical),
 * and are enclosed in percent signs (% %). See docs and examples.txt for more.
 *
 * This filter was written through the collaboration of numerous
 * members of the Moodle community. It was originally based on
 * the missingword format, which included code from Thomas Robb
 * and others. Paul Tsuchido Shew wrote this filter in December 2003.
 *
 * @copyright  2003 Paul Tsuchido Shew
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_gift extends qformat_default {

    public function provide_import() {
        return true;
    }

    public function provide_export() {
        return true;
    }

    public function export_file_extension() {
        return '.txt';
    }

    protected function answerweightparser(&$answer) {
        $answer = substr($answer, 1);                        // Removes initial %.
        $endposition  = strpos($answer, "%");
        $answerweight = substr($answer, 0, $endposition);  // Gets weight as integer.
        $answerweight = $answerweight/100;                 // Converts to percent.
        $answer = substr($answer, $endposition+1);          // Removes comment from answer.
        return $answerweight;
    }

    protected function commentparser($answer, $defaultformat) {
        $bits = explode('#', $answer, 2);
        $ans = $this->parse_text_with_format(trim($bits[0]), $defaultformat);
        if (count($bits) > 1) {
            $feedback = $this->parse_text_with_format(trim($bits[1]), $defaultformat);
        } else {
            $feedback = array('text' => '', 'format' => $defaultformat, 'files' => array());
        }
        return array($ans, $feedback);
    }

    protected function split_truefalse_comment($answer, $defaultformat) {
        $bits = explode('#', $answer, 3);
        $ans = $this->parse_text_with_format(trim($bits[0]), $defaultformat);
        if (count($bits) > 1) {
            $wrongfeedback = $this->parse_text_with_format(trim($bits[1]), $defaultformat);
        } else {
            $wrongfeedback = array('text' => '', 'format' => $defaultformat, 'files' => array());
        }
        if (count($bits) > 2) {
            $rightfeedback = $this->parse_text_with_format(trim($bits[2]), $defaultformat);
        } else {
            $rightfeedback = array('text' => '', 'format' => $defaultformat, 'files' => array());
        }
        return array($ans, $wrongfeedback, $rightfeedback);
    }

    protected function escapedchar_pre($string) {
        // Replaces escaped control characters with a placeholder BEFORE processing.

        $escapedcharacters = array("\\:",    "\\#",    "\\=",    "\\{",    "\\}",    "\\~",    "\\n"  );
        $placeholders      = array("&&058;", "&&035;", "&&061;", "&&123;", "&&125;", "&&126;", "&&010");

        $string = str_replace("\\\\", "&&092;", $string);
        $string = str_replace($escapedcharacters, $placeholders, $string);
        $string = str_replace("&&092;", "\\", $string);
        return $string;
    }

    protected function escapedchar_post($string) {
        // Replaces placeholders with corresponding character AFTER processing is done.
        $placeholders = array("&&058;", "&&035;", "&&061;", "&&123;", "&&125;", "&&126;", "&&010");
        $characters   = array(":",     "#",      "=",      "{",      "}",      "~",      "\n"  );
        $string = str_replace($placeholders, $characters, $string);
        return $string;
    }

    protected function check_answer_count($min, $answers, $text) {
        $countanswers = count($answers);
        if ($countanswers < $min) {
            $this->error(get_string('importminerror', 'qformat_gift'), $text);
            return false;
        }

        return true;
    }

    protected function parse_text_with_format($text, $defaultformat = FORMAT_MOODLE) {
        $result = array(
            'text' => $text,
            'format' => $defaultformat,
            'files' => array(),
        );
        if (strpos($text, '[') === 0) {
            $formatend = strpos($text, ']');
            $result['format'] = $this->format_name_to_const(substr($text, 1, $formatend - 1));
            if ($result['format'] == -1) {
                $result['format'] = $defaultformat;
            } else {
                $result['text'] = substr($text, $formatend + 1);
            }
        }
        $result['text'] = trim($this->escapedchar_post($result['text']));
        return $result;
    }

    public function readquestion($lines) {
        // Given an array of lines known to define a question in this format, this function
        // converts it into a question object suitable for processing and insertion into Moodle.

        $question = $this->defaultquestion();
        $comment = null;
        // Define replaced by simple assignment, stop redefine notices.
        $giftanswerweightregex = '/^%\-*([0-9]{1,2})\.?([0-9]*)%/';

        // REMOVED COMMENTED LINES and IMPLODE.
        foreach ($lines as $key => $line) {
            $line = trim($line);
            if (substr($line, 0, 2) == '//') {
                $lines[$key] = ' ';
            }
        }

        $text = trim(implode("\n", $lines));

        if ($text == '') {
            return false;
        }

        // Substitute escaped control characters with placeholders.
        $text = $this->escapedchar_pre($text);

        // Look for category modifier.
        if (preg_match('~^\$CATEGORY:~', $text)) {
            $newcategory = trim(substr($text, 10));

            // Build fake question to contain category.
            $question->qtype = 'category';
            $question->category = $newcategory;
            return $question;
        }

        // Question name parser.
        if (substr($text, 0, 2) == '::') {
            $text = substr($text, 2);

            $namefinish = strpos($text, '::');
            if ($namefinish === false) {
                $question->name = false;
                // Name will be assigned after processing question text below.
            } else {
                $questionname = substr($text, 0, $namefinish);
                $question->name = $this->clean_question_name($this->escapedchar_post($questionname));
                $text = trim(substr($text, $namefinish+2)); // Remove name from text.
            }
        } else {
            $question->name = false;
        }

        // Find the answer section.
        $answerstart = strpos($text, '{');
        $answerfinish = strpos($text, '}');

        $description = false;
        if ($answerstart === false && $answerfinish === false) {
            // No answer means it's a description.
            $description = true;
            $answertext = '';
            $answerlength = 0;

        } else if ($answerstart === false || $answerfinish === false) {
            $this->error(get_string('braceerror', 'qformat_gift'), $text);
            return false;

        } else {
            $answerlength = $answerfinish - $answerstart;
            $answertext = trim(substr($text, $answerstart + 1, $answerlength - 1));
        }

        // Format the question text, without answer, inserting "_____" as necessary.
        if ($description) {
            $questiontext = $text;
        } else if (substr($text, -1) == "}") {
            // No blank line if answers follow question, outside of closing punctuation.
            $questiontext = substr_replace($text, "", $answerstart, $answerlength + 1);
        } else {
            // Inserts blank line for missing word format.
            $questiontext = substr_replace($text, "_____", $answerstart, $answerlength + 1);
        }

        // Look to see if there is any general feedback.
        $gfseparator = strrpos($answertext, '####');
        if ($gfseparator === false) {
            $generalfeedback = '';
        } else {
            $generalfeedback = substr($answertext, $gfseparator + 4);
            $answertext = trim(substr($answertext, 0, $gfseparator));
        }

        // Get questiontext format from questiontext.
        $text = $this->parse_text_with_format($questiontext);
        $question->questiontextformat = $text['format'];
        $question->questiontext = $text['text'];

        // Get generalfeedback format from questiontext.
        $text = $this->parse_text_with_format($generalfeedback, $question->questiontextformat);
        $question->generalfeedback = $text['text'];
        $question->generalfeedbackformat = $text['format'];

        // Set question name if not already set.
        if ($question->name === false) {
            $question->name = $this->create_default_question_name($question->questiontext, get_string('questionname', 'question'));
        }

        // Determine question type.
        $question->qtype = null;

        // Give plugins first try.
        // Plugins must promise not to intercept standard qtypes
        // MDL-12346, this could be called from lesson mod which has its own base class =(.
        if (method_exists($this, 'try_importing_using_qtypes')
                && ($tryquestion = $this->try_importing_using_qtypes($lines, $question, $answertext))) {
            return $tryquestion;
        }

        if ($description) {
            $question->qtype = 'description';

        } else if ($answertext == '') {
            $question->qtype = 'essay';

        } else if ($answertext{0} == '#') {
            $question->qtype = 'numerical';

        } else if (strpos($answertext, '~') !== false) {
            // Only Multiplechoice questions contain tilde ~.
            $question->qtype = 'multichoice';

        } else if (strpos($answertext, '=')  !== false
                && strpos($answertext, '->') !== false) {
            // Only Matching contains both = and ->.
            $question->qtype = 'match';

        } else { // Either truefalse or shortanswer.

            // Truefalse question check.
            $truefalsecheck = $answertext;
            if (strpos($answertext, '#') > 0) {
                // Strip comments to check for TrueFalse question.
                $truefalsecheck = trim(substr($answertext, 0, strpos($answertext, "#")));
            }

            $validtfanswers = array('T', 'TRUE', 'F', 'FALSE');
            if (in_array($truefalsecheck, $validtfanswers)) {
                $question->qtype = 'truefalse';

            } else { // Must be shortanswer.
                $question->qtype = 'shortanswer';
            }
        }

        if (!isset($question->qtype)) {
            $giftqtypenotset = get_string('giftqtypenotset', 'qformat_gift');
            $this->error($giftqtypenotset, $text);
            return false;
        }

        switch ($question->qtype) {
            case 'description':
                $question->defaultmark = 0;
                $question->length = 0;
                return $question;

            case 'essay':
                $question->responseformat = 'editor';
                $question->responserequired = 1;
                $question->responsefieldlines = 15;
                $question->attachments = 0;
                $question->attachmentsrequired = 0;
                $question->graderinfo = array(
                        'text' => '', 'format' => FORMAT_HTML, 'files' => array());
                $question->responsetemplate = array(
                        'text' => '', 'format' => FORMAT_HTML);
                return $question;

            case 'multichoice':
                // "Temporary" solution to enable choice of answernumbering on GIFT import
                // by respecting default set for multichoice questions (MDL-59447)
                $question->answernumbering = get_config('qtype_multichoice', 'answernumbering');

                if (strpos($answertext, "=") === false) {
                    $question->single = 0; // Multiple answers are enabled if no single answer is 100% correct.
                } else {
                    $question->single = 1; // Only one answer allowed (the default).
                }
                $question = $this->add_blank_combined_feedback($question);

                $answertext = str_replace("=", "~=", $answertext);
                $answers = explode("~", $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }

                $countanswers = count($answers);

                if (!$this->check_answer_count(2, $answers, $text)) {
                    return false;
                }

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);

                    // Determine answer weight.
                    if ($answer[0] == '=') {
                        $answerweight = 1;
                        $answer = substr($answer, 1);

                    } else if (preg_match($giftanswerweightregex, $answer)) {    // Check for properly formatted answer weight.
                        $answerweight = $this->answerweightparser($answer);

                    } else {     // Default, i.e., wrong anwer.
                        $answerweight = 0;
                    }
                    list($question->answer[$key], $question->feedback[$key]) =
                            $this->commentparser($answer, $question->questiontextformat);
                    $question->fraction[$key] = $answerweight;
                }  // End foreach answer.

                return $question;

            case 'match':
                $question = $this->add_blank_combined_feedback($question);

                $answers = explode('=', $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }

                if (!$this->check_answer_count(2, $answers, $text)) {
                    return false;
                }

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);
                    if (strpos($answer, "->") === false) {
                        $this->error(get_string('giftmatchingformat', 'qformat_gift'), $answer);
                        return false;
                    }

                    $marker = strpos($answer, '->');
                    $question->subquestions[$key] = $this->parse_text_with_format(
                            substr($answer, 0, $marker), $question->questiontextformat);
                    $question->subanswers[$key] = trim($this->escapedchar_post(
                            substr($answer, $marker + 2)));
                }

                return $question;

            case 'truefalse':
                list($answer, $wrongfeedback, $rightfeedback) =
                        $this->split_truefalse_comment($answertext, $question->questiontextformat);

                if ($answer['text'] == "T" || $answer['text'] == "TRUE") {
                    $question->correctanswer = 1;
                    $question->feedbacktrue = $rightfeedback;
                    $question->feedbackfalse = $wrongfeedback;
                } else {
                    $question->correctanswer = 0;
                    $question->feedbacktrue = $wrongfeedback;
                    $question->feedbackfalse = $rightfeedback;
                }

                $question->penalty = 1;

                return $question;

            case 'shortanswer':
                // Shortanswer question.
                $answers = explode("=", $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }

                if (!$this->check_answer_count(1, $answers, $text)) {
                    return false;
                }

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);

                    // Answer weight.
                    if (preg_match($giftanswerweightregex, $answer)) {    // Check for properly formatted answer weight.
                        $answerweight = $this->answerweightparser($answer);
                    } else {     // Default, i.e., full-credit anwer.
                        $answerweight = 1;
                    }

                    list($answer, $question->feedback[$key]) = $this->commentparser(
                            $answer, $question->questiontextformat);

                    $question->answer[$key] = $answer['text'];
                    $question->fraction[$key] = $answerweight;
                }

                return $question;

            case 'numerical':
                // Note similarities to ShortAnswer.
                $answertext = substr($answertext, 1); // Remove leading "#".

                // If there is feedback for a wrong answer, store it for now.
                if (($pos = strpos($answertext, '~')) !== false) {
                    $wrongfeedback = substr($answertext, $pos);
                    $answertext = substr($answertext, 0, $pos);
                } else {
                    $wrongfeedback = '';
                }

                $answers = explode("=", $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }

                if (count($answers) == 0) {
                    // Invalid question.
                    $giftnonumericalanswers = get_string('giftnonumericalanswers', 'qformat_gift');
                    $this->error($giftnonumericalanswers, $text);
                    return false;
                }

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);

                    // Answer weight.
                    if (preg_match($giftanswerweightregex, $answer)) {    // Check for properly formatted answer weight.
                        $answerweight = $this->answerweightparser($answer);
                    } else {     // Default, i.e., full-credit anwer.
                        $answerweight = 1;
                    }

                    list($answer, $question->feedback[$key]) = $this->commentparser(
                            $answer, $question->questiontextformat);
                    $question->fraction[$key] = $answerweight;
                    $answer = $answer['text'];

                    // Calculate Answer and Min/Max values.
                    if (strpos($answer, "..") > 0) { // Optional [min]..[max] format.
                        $marker = strpos($answer, "..");
                        $max = trim(substr($answer, $marker + 2));
                        $min = trim(substr($answer, 0, $marker));
                        $ans = ($max + $min)/2;
                        $tol = $max - $ans;
                    } else if (strpos($answer, ':') > 0) { // Standard [answer]:[errormargin] format.
                        $marker = strpos($answer, ':');
                        $tol = trim(substr($answer, $marker+1));
                        $ans = trim(substr($answer, 0, $marker));
                    } else { // Only one valid answer (zero errormargin).
                        $tol = 0;
                        $ans = trim($answer);
                    }

                    if (!(is_numeric($ans) || $ans = '*') || !is_numeric($tol)) {
                            $errornotnumbers = get_string('errornotnumbers');
                            $this->error($errornotnumbers, $text);
                        return false;
                    }

                    // Store results.
                    $question->answer[$key] = $ans;
                    $question->tolerance[$key] = $tol;
                }

                if ($wrongfeedback) {
                    $key += 1;
                    $question->fraction[$key] = 0;
                    list($notused, $question->feedback[$key]) = $this->commentparser(
                            $wrongfeedback, $question->questiontextformat);
                    $question->answer[$key] = '*';
                    $question->tolerance[$key] = '';
                }

                return $question;

            default:
                $this->error(get_string('giftnovalidquestion', 'qformat_gift'), $text);
                return false;

        }
    }

    protected function repchar($text, $notused = 0) {
        // Escapes 'reserved' characters # = ~ {) :
        // Removes new lines.
        $reserved = array(  '\\',  '#', '=', '~', '{', '}', ':', "\n", "\r");
        $escaped =  array('\\\\', '\#', '\=', '\~', '\{', '\}', '\:', '\n', '');

        $newtext = str_replace($reserved, $escaped, $text);
        return $newtext;
    }

    /**
     * @param int $format one of the FORMAT_ constants.
     * @return string the corresponding name.
     */
    protected function format_const_to_name($format) {
        if ($format == FORMAT_MOODLE) {
            return 'moodle';
        } else if ($format == FORMAT_HTML) {
            return 'html';
        } else if ($format == FORMAT_PLAIN) {
            return 'plain';
        } else if ($format == FORMAT_MARKDOWN) {
            return 'markdown';
        } else {
            return 'moodle';
        }
    }

    /**
     * @param int $format one of the FORMAT_ constants.
     * @return string the corresponding name.
     */
    protected function format_name_to_const($format) {
        if ($format == 'moodle') {
            return FORMAT_MOODLE;
        } else if ($format == 'html') {
            return FORMAT_HTML;
        } else if ($format == 'plain') {
            return FORMAT_PLAIN;
        } else if ($format == 'markdown') {
            return FORMAT_MARKDOWN;
        } else {
            return -1;
        }
    }

    public function write_name($name) {
        return '::' . $this->repchar($name) . '::';
    }

    public function write_questiontext($text, $format, $defaultformat = FORMAT_MOODLE) {
        $output = '';
        if ($text != '' && $format != $defaultformat) {
            $output .= '[' . $this->format_const_to_name($format) . ']';
        }
        $output .= $this->repchar($text, $format);
        return $output;
    }

    /**
     * Outputs the general feedback for the question, if any. This needs to be the
     * last thing before the }.
     * @param object $question the question data.
     * @param string $indent to put before the general feedback. Defaults to a tab.
     *      If this is not blank, a newline is added after the line.
     */
    public function write_general_feedback($question, $indent = "\t") {
        $generalfeedback = $this->write_questiontext($question->generalfeedback,
                $question->generalfeedbackformat, $question->questiontextformat);

        if ($generalfeedback) {
            $generalfeedback = '####' . $generalfeedback;
            if ($indent) {
                $generalfeedback = $indent . $generalfeedback . "\n";
            }
        }

        return $generalfeedback;
    }

    public function writequestion($question) {
        global $OUTPUT;

        // Start with a comment.
        $expout = "// question: {$question->id}  name: {$question->name}\n";

        // Output depends on question type.
        switch($question->qtype) {

            case 'category':
                // Not a real question, used to insert category switch.
                $expout .= "\$CATEGORY: $question->category\n";
                break;

            case 'description':
                $expout .= $this->write_name($question->name);
                $expout .= $this->write_questiontext($question->questiontext, $question->questiontextformat);
                break;

            case 'essay':
                $expout .= $this->write_name($question->name);
                $expout .= $this->write_questiontext($question->questiontext, $question->questiontextformat);
                $expout .= "{";
                $expout .= $this->write_general_feedback($question, '');
                $expout .= "}\n";
                break;

            case 'truefalse':
                $trueanswer = $question->options->answers[$question->options->trueanswer];
                $falseanswer = $question->options->answers[$question->options->falseanswer];
                if ($trueanswer->fraction == 1) {
                    $answertext = 'TRUE';
                    $rightfeedback = $this->write_questiontext($trueanswer->feedback,
                            $trueanswer->feedbackformat, $question->questiontextformat);
                    $wrongfeedback = $this->write_questiontext($falseanswer->feedback,
                            $falseanswer->feedbackformat, $question->questiontextformat);
                } else {
                    $answertext = 'FALSE';
                    $rightfeedback = $this->write_questiontext($falseanswer->feedback,
                            $falseanswer->feedbackformat, $question->questiontextformat);
                    $wrongfeedback = $this->write_questiontext($trueanswer->feedback,
                            $trueanswer->feedbackformat, $question->questiontextformat);
                }

                $expout .= $this->write_name($question->name);
                $expout .= $this->write_questiontext($question->questiontext, $question->questiontextformat);
                $expout .= '{' . $this->repchar($answertext);
                if ($wrongfeedback) {
                    $expout .= '#' . $wrongfeedback;
                } else if ($rightfeedback) {
                    $expout .= '#';
                }
                if ($rightfeedback) {
                    $expout .= '#' . $rightfeedback;
                }
                $expout .= $this->write_general_feedback($question, '');
                $expout .= "}\n";
                break;

            case 'multichoice':
                $expout .= $this->write_name($question->name);
                $expout .= $this->write_questiontext($question->questiontext, $question->questiontextformat);
                $expout .= "{\n";
                foreach ($question->options->answers as $answer) {
                    if ($answer->fraction == 1 && $question->options->single) {
                        $answertext = '=';
                    } else if ($answer->fraction == 0) {
                        $answertext = '~';
                    } else {
                        $weight = $answer->fraction * 100;
                        $answertext = '~%' . $weight . '%';
                    }
                    $expout .= "\t" . $answertext . $this->write_questiontext($answer->answer,
                                $answer->answerformat, $question->questiontextformat);
                    if ($answer->feedback != '') {
                        $expout .= '#' . $this->write_questiontext($answer->feedback,
                                $answer->feedbackformat, $question->questiontextformat);
                    }
                    $expout .= "\n";
                }
                $expout .= $this->write_general_feedback($question);
                $expout .= "}\n";
                break;

            case 'shortanswer':
                $expout .= $this->write_name($question->name);
                $expout .= $this->write_questiontext($question->questiontext, $question->questiontextformat);
                $expout .= "{\n";
                foreach ($question->options->answers as $answer) {
                    $weight = 100 * $answer->fraction;
                    $expout .= "\t=%" . $weight . '%' . $this->repchar($answer->answer) .
                            '#' . $this->write_questiontext($answer->feedback,
                                $answer->feedbackformat, $question->questiontextformat) . "\n";
                }
                $expout .= $this->write_general_feedback($question);
                $expout .= "}\n";
                break;

            case 'numerical':
                $expout .= $this->write_name($question->name);
                $expout .= $this->write_questiontext($question->questiontext, $question->questiontextformat);
                $expout .= "{#\n";
                foreach ($question->options->answers as $answer) {
                    if ($answer->answer != '' && $answer->answer != '*') {
                        $weight = 100 * $answer->fraction;
                        $expout .= "\t=%" . $weight . '%' . $answer->answer . ':' .
                                (float)$answer->tolerance . '#' . $this->write_questiontext($answer->feedback,
                                $answer->feedbackformat, $question->questiontextformat) . "\n";
                    } else {
                        $expout .= "\t~#" . $this->write_questiontext($answer->feedback,
                                $answer->feedbackformat, $question->questiontextformat) . "\n";
                    }
                }
                $expout .= $this->write_general_feedback($question);
                $expout .= "}\n";
                break;

            case 'match':
                $expout .= $this->write_name($question->name);
                $expout .= $this->write_questiontext($question->questiontext, $question->questiontextformat);
                $expout .= "{\n";
                foreach ($question->options->subquestions as $subquestion) {
                    $expout .= "\t=" . $this->write_questiontext($subquestion->questiontext,
                            $subquestion->questiontextformat, $question->questiontextformat) .
                            ' -> ' . $this->repchar($subquestion->answertext) . "\n";
                }
                $expout .= $this->write_general_feedback($question);
                $expout .= "}\n";
                break;

            default:
                // Check for plugins.
                if ($out = $this->try_exporting_using_qtypes($question->qtype, $question)) {
                    $expout .= $out;
                }
        }

        // Add empty line to delimit questions.
        $expout .= "\n";
        return $expout;
    }
}
