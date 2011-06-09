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
 * Missing word question importer.
 *
 * @package    qformat
 * @subpackage missingword
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Missing word question importer.
 *
 * This Moodle class provides all functions necessary to import and export
 * one-correct-answer multiple choice questions in this format:
 *
 *    As soon as we begin to explore our body parts as infants
 *    we become students of {=anatomy and physiology ~reflexology
 *    ~science ~experiment}, and in a sense we remain students for life.
 *
 * Each answer is separated with a tilde ~, and the correct answer is
 * prefixed with an equals sign =
 *
 * Percentage weights can be included by following the tilde with the
 * desired percent.  Comments can be included for each choice by following
 * the comment with a hash mark ("#") and the comment.  Example:
 *
 *    This is {=the best answer#comment on the best answer ~75%a good
 *    answer#comment on the good answer ~a wrong one#comment on the bad answer}
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_missingword extends qformat_default {

    function provide_import() {
      return true;
    }

    function readquestion($lines) {
    /// Given an array of lines known to define a question in
    /// this format, this function converts it into a question
    /// object suitable for processing and insertion into Moodle.

        $question = $this->defaultquestion();
        ///$comment added by T Robb
        $comment = NULL;
        $text = implode(" ", $lines);

        /// Find answer section

        $answerstart = strpos($text, "{");
        if ($answerstart === false) {
            if ($this->displayerrors) {
                echo "<p>$text<p>Could not find a {";
            }
            return false;
        }

        $answerfinish = strpos($text, "}");
        if ($answerfinish === false) {
            if ($this->displayerrors) {
                echo "<p>$text<p>Could not find a }";
            }
            return false;
        }

        $answerlength = $answerfinish - $answerstart;
        $answertext = substr($text, $answerstart + 1, $answerlength - 1);

        /// Save the new question text
        $question->questiontext = substr_replace($text, "_____", $answerstart, $answerlength+1);
        $question->name = $question->questiontext;


        /// Parse the answers
        $answertext = str_replace("=", "~=", $answertext);
        $answers = explode("~", $answertext);
        if (isset($answers[0])) {
            $answers[0] = trim($answers[0]);
        }
        if (empty($answers[0])) {
            array_shift($answers);
        }

        $countanswers = count($answers);

        switch ($countanswers) {
            case 0:  // invalid question
                if ($this->displayerrors) {
                    echo "<p>No answers found in $answertext";
                }
                return false;

            case 1:
                $question->qtype = SHORTANSWER;

                $answer = trim($answers[0]);
                if ($answer[0] == "=") {
                    $answer = substr($answer, 1);
                }
                $question->answer[]   = $answer;
                $question->fraction[] = 1;
                $question->feedback[] = "";

                return $question;

            default:
                $question->qtype = MULTICHOICE;

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);

                    // Tom's addition starts here
                    $answeight = 0;
                    if (strspn($answer,"1234567890%") > 0){
                        //Make sure that the percent sign is the last in the span
                        if (strpos($answer,"%") == strspn($answer,"1234567890%") - 1) {
                            $answeight0 = substr($answer,0,strspn($answer,"1234567890%"));
                            $answeight = round(($answeight0/100),2);
                            $answer = substr($answer,(strspn($answer,"1234567890%")));
                        }
                    }
                    if ($answer[0] == "="){
                        $answeight = 1;
                    }
                    //remove the protective underscore for leading numbers in answers
                    if ($answer[0] == "_"){
                        $answer = substr($answer, 1);
                    }
                    $answer = trim($answer);

                    if (strpos($answer,"#") > 0){
                        $hashpos = strpos($answer,"#");
                        $comment = substr(($answer),$hashpos+1);
                        $answer  = substr($answer,0,$hashpos);
                    } else {
                        $comment = " ";
                    }
                    // End of Tom's addition

                    if ($answer[0] == "=") {
#                       $question->fraction[$key] = 1;
                        $question->fraction[$key] = $answeight;
                        $answer = substr($answer, 1);
                    } else {
#                       $question->fraction[$key] = 0;
                        $question->fraction[$key] = $answeight;
                    }
                    $question->answer[$key]   = $answer;
                    $question->feedback[$key] = $comment;
                }

                return $question;
        }
    }
}


