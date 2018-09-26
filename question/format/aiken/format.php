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
 * Aiken format question importer.
 *
 * @package    qformat_aiken
 * @copyright  2003 Tom Robb <tom@robb.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Aiken format - a simple format for creating multiple choice questions (with
 * only one correct choice, and no feedback).
 *
 * The format looks like this:
 *
 * Question text
 * A) Choice #1
 * B) Choice #2
 * C) Choice #3
 * D) Choice #4
 * ANSWER: B
 *
 * That is,
 *  + question text all one one line.
 *  + then a number of choices, one to a line. Each line must comprise a letter,
 *    then ')' or '.', then a space, then the choice text.
 *  + Then a line of the form 'ANSWER: X' to indicate the correct answer.
 *
 * Be sure to word "All of the above" type choices like "All of these" in
 * case choices are being shuffled.
 *
 * @copyright  2003 Tom Robb <tom@robb.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_aiken extends qformat_default {

    public function provide_import() {
        return true;
    }

    public function readquestions($lines) {
        $questions = array();
        $question = null;
        $endchar = chr(13);
        $linenumber = 0;
        foreach ($lines as $line) {
            $stp = strpos($line, $endchar, 0);
            $newlines = explode($endchar, $line);
            $linescount = count($newlines);
            for ($i=0; $i < $linescount; $i++) {
                $linenumber++;
                $nowline = trim($newlines[$i]);
                // Go through the array and build an object called $question
                // When done, add $question to $questions.
                if (strlen($nowline) < 2) {
                    continue;
                }
                if (preg_match('/^[A-Z][).][ \t]?/', $nowline)) {
                    if (is_null($question)) {
                        // We have a response line, but we aren't currently in a question.
                        $this->error(get_string('questionnotstarted', 'qformat_aiken', $linenumber));
                        continue;
                    }

                    // A choice. Trim off the label and space, then save.
                    $question->answer[] = $this->text_field(
                            htmlspecialchars(trim(substr($nowline, 2)), ENT_NOQUOTES));
                    $question->fraction[] = 0;
                    $question->feedback[] = $this->text_field('');
                } else if (preg_match('/^ANSWER:/', $nowline)) {
                    if (is_null($question)) {
                        // We have an answer line, but we aren't currently in a question.
                        $this->error(get_string('questionnotstarted', 'qformat_aiken', $linenumber));
                        continue;
                    }

                    // The line that indicates the correct answer. This question is finised.
                    $ans = trim(substr($nowline, strpos($nowline, ':') + 1));
                    $ans = substr($ans, 0, 1);
                    // We want to map A to 0, B to 1, etc.
                    $rightans = ord($ans) - ord('A');

                    if (count($question->answer) < 2) {
                        // The multichoice question requires at least 2 answers, or there will be a failure later.
                        $this->error(get_string('questionmissinganswers', 'qformat_aiken', $linenumber), '', $question->name);
                        $question = null;
                        continue;
                    }

                    $question->fraction[$rightans] = 1;
                    $questions[] = $question;

                    // Clear variable for next question set.
                    $question = null;
                    continue;
                } else {
                    // Must be the first line of a new question, since no recognised prefix.
                    if (!is_null($question)) {
                        // In this case, there was already an open question that we didn't complete. It is being discarded.
                        $this->error(get_string('questionnotcomplete', 'qformat_aiken', $linenumber), '', $question->name);
                    }

                    $question = $this->defaultquestion();
                    $question->qtype = 'multichoice';
                    $question->name = $this->create_default_question_name($nowline, get_string('questionname', 'question'));
                    $question->questiontext = htmlspecialchars(trim($nowline), ENT_NOQUOTES);
                    $question->questiontextformat = FORMAT_HTML;
                    $question->generalfeedback = '';
                    $question->generalfeedbackformat = FORMAT_HTML;
                    $question->single = 1;
                    $question->answer = array();
                    $question->fraction = array();
                    $question->feedback = array();
                    $question->correctfeedback = $this->text_field('');
                    $question->partiallycorrectfeedback = $this->text_field('');
                    $question->incorrectfeedback = $this->text_field('');
                }
            }
        }
        return $questions;
    }

    protected function text_field($text) {
        return array(
            'text' => htmlspecialchars(trim($text), ENT_NOQUOTES),
            'format' => FORMAT_HTML,
            'files' => array(),
        );
    }

    public function readquestion($lines) {
        // This is no longer needed but might still be called by default.php.
        return;
    }
}


