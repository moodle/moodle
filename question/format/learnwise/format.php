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
 * Examview question importer.
 *
 * @package    qformat
 * @subpackage learnwise
 * @copyright  2005 Alton College, Hampshire, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Examview question importer.
 *
 * Alton College, Hampshire, UK - Tom Flannaghan, Andrew Walker
 *
 * Imports learnwise multiple choice questions (single and multiple answers)
 * currently ignores the deduct attribute for multiple answer questions
 * deductions are currently simply found by dividing the award for the incorrect
 * answer by the total number of options
 *
 * @copyright  2005 Alton College, Hampshire, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_learnwise extends qformat_default {

    public function provide_import() {
        return true;
    }

    public function export_file_extension() {
        return '.xml';
    }

    protected function readquestions($lines) {
        $questions = array();
        $currentquestion = array();

        foreach($lines as $line) {
            $line = trim($line);
            $currentquestion[] = $line;

            if ($question = $this->readquestion($currentquestion)) {
                $questions[] = $question;
                $currentquestion = array();
            }
        }
        return $questions;
    }

    protected function readquestion($lines) {
        global $OUTPUT;

        $text = implode(' ', $lines);
        $text = str_replace(array('\t','\n','\r'), array('','',''), $text);

        $startpos = strpos($text, '<question type');
        $endpos = strpos($text, '</question>');
        if ($startpos === false || $endpos === false) {
            return false;
        }

        preg_match("/<question type=[\"\']([^\"\']+)[\"\']>/i", $text, $matches);
        $type = strtolower($matches[1]); // multichoice or multianswerchoice

        $questiontext = textlib::entities_to_utf8($this->stringbetween($text, '<text>', '</text>'));
        $questionhint = textlib::entities_to_utf8($this->stringbetween($text, '<hint>', '</hint>'));
        $questionaward = $this->stringbetween($text, '<award>', '</award>');
        $optionlist = $this->stringbetween($text, '<answer>', '</answer>');

        $optionlist = explode('<option', $optionlist);

        $n = 0;

        $optionscorrect = array();
        $optionstext = array();

        if ($type == 'multichoice') {
            foreach ($optionlist as $option) {
                if (trim($option) === '') {
                    continue;
                }
                $correct = $this->stringbetween($option, ' correct="', '">');
                $answer = $this->stringbetween($option, '">', '</option>');
                $optionscorrect[$n] = $correct;
                $optionstext[$n] = textlib::entities_to_utf8($answer);
                ++$n;
            }
        } else if ($type == 'multianswerchoice') {
            $numcorrect = 0;
            $totalaward = 0;

            $optionsaward = array();

            foreach ($optionlist as $option) {
                if (trim($option) === '') {
                    continue;
                }
                preg_match("/correct=\"([^\"]*)\"/i", $option, $correctmatch);
                preg_match("/award=\"([^\"]*)\"/i", $option, $awardmatch);

                $correct = $correctmatch[1];
                $award = $awardmatch[1];
                if ($correct == 'yes') {
                    $totalaward += $award;
                    ++$numcorrect;
                }

                $answer = $this->stringbetween($option, '">', '</option>');

                $optionscorrect[$n] = $correct;
                $optionstext[$n] = textlib::entities_to_utf8($answer);
                $optionsaward[$n] = $award;
                ++$n;
            }

        } else {
            echo $OUTPUT->notification(get_string('unknownorunhandledtype', 'question', $type));
        }

        $question = $this->defaultquestion();
        $question->qtype = 'multichoice';
        $question->name = $this->create_default_question_name($questiontext, get_string('questionname', 'question'));
        $this->add_blank_combined_feedback($question);

        $question->questiontext = $questiontext;
        $question->questiontextformat = FORMAT_HTML;
        $question->single = ($type == 'multichoice') ? 1 : 0;

        $question->fraction = array();
        $question->answer = array();
        for ($n = 0; $n < count($optionstext); ++$n) {
            if ($optionstext[$n]) {
                if (!isset($numcorrect)) {
                    // Single answer.
                    if ($optionscorrect[$n] == 'yes') {
                        $fraction = (int) $questionaward;
                    } else {
                        $fraction = 0;
                    }
                } else {
                    // Multiple answers.
                    if ($optionscorrect[$n] == 'yes') {
                        $fraction = $optionsaward[$n] / $totalaward;
                    } else {
                        $fraction = -$optionsaward[$n] / count($optionstext);
                    }
                }
                $question->fraction[] = $fraction;
                $question->answer[] = array('text' => $optionstext[$n], 'format' => FORMAT_HTML);
                $question->feedback[] = array('text' => '', 'format' => FORMAT_HTML); // No feedback in this type.
            }
        }

        return $question;
    }

    /**
     * Extract the substring of $text between $start and $end.
     * @param string $text text to analyse.
     * @param string $start opening delimiter.
     * @param string $end closing delimiter.
     * @return string the requested substring.
     */
    protected function stringbetween($text, $start, $end) {
        $startpos = strpos($text, $start) + strlen($start);
        $endpos = strpos($text, $end);

        if ($startpos <= $endpos) {
            return substr($text, $startpos, $endpos - $startpos);
        }
    }
}
