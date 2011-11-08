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
 * Blackboard question importer.
 *
 * @package    qformat
 * @subpackage blackboard
 * @copyright  2003 Scott Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once ($CFG->libdir . '/xmlize.php');


/**
 * Blackboard question importer.
 *
 * @copyright  2003 Scott Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_blackboard extends qformat_default {

    public function provide_import() {
        return true;
    }

    function readquestions ($lines) {
        /// Parses an array of lines into an array of questions,
        /// where each item is a question object as defined by
        /// readquestion().

        $text = implode($lines, " ");
        $xml = xmlize($text, 0);

        $questions = array();

        $this->process_tf($xml, $questions);
        $this->process_mc($xml, $questions);
        $this->process_ma($xml, $questions);
        $this->process_fib($xml, $questions);
        $this->process_matching($xml, $questions);
        $this->process_essay($xml, $questions);

        return $questions;
    }

//----------------------------------------
// Process Essay Questions
//----------------------------------------
    function process_essay($xml, &$questions ) {

        if (isset($xml["POOL"]["#"]["QUESTION_ESSAY"])) {
            $essayquestions = $xml["POOL"]["#"]["QUESTION_ESSAY"];
        }
        else {
            return;
        }

        foreach ($essayquestions as $essayquestion) {

            $question = $this->defaultquestion();

            $question->qtype = ESSAY;

            // determine if the question is already escaped html
            $ishtml = $essayquestion["#"]["BODY"][0]["#"]["FLAGS"][0]["#"]["ISHTML"][0]["@"]["value"];

            // put questiontext in question object
            if ($ishtml) {
                $question->questiontext = html_entity_decode(trim($essayquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
            }

            // put name in question object
            $question->name = substr($question->questiontext, 0, 254);
            $question->answer = '';
            $question->feedback = '';
            $question->fraction = 0;

            $questions[] = $question;
        }
    }

    //----------------------------------------
    // Process True / False Questions
    //----------------------------------------
    function process_tf($xml, &$questions) {

        if (isset($xml["POOL"]["#"]["QUESTION_TRUEFALSE"])) {
            $tfquestions = $xml["POOL"]["#"]["QUESTION_TRUEFALSE"];
        }
        else {
            return;
        }

        for ($i = 0; $i < sizeof ($tfquestions); $i++) {

            $question = $this->defaultquestion();

            $question->qtype = TRUEFALSE;
            $question->single = 1; // Only one answer is allowed

            $thisquestion = $tfquestions[$i];

            // determine if the question is already escaped html
            $ishtml = $thisquestion["#"]["BODY"][0]["#"]["FLAGS"][0]["#"]["ISHTML"][0]["@"]["value"];

            // put questiontext in question object
            if ($ishtml) {
                $question->questiontext = html_entity_decode(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]),ENT_QUOTES,'UTF-8');
            }
            // put name in question object
            $question->name = shorten_text($question->questiontext, 254);

            $choices = $thisquestion["#"]["ANSWER"];

            $correct_answer = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"][0]["@"]["answer_id"];

            // first choice is true, second is false.
            $id = $choices[0]["@"]["id"];

            if (strcmp($id, $correct_answer) == 0) {  // true is correct
                $question->answer = 1;
                $question->feedbacktrue = trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]);
                $question->feedbackfalse = trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]);
            } else {  // false is correct
                $question->answer = 0;
                $question->feedbacktrue = trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]);
                $question->feedbackfalse = trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]);
            }
            $question->correctanswer = $question->answer;
            $questions[] = $question;
          }
    }

    //----------------------------------------
    // Process Multiple Choice Questions
    //----------------------------------------
    function process_mc($xml, &$questions) {

        if (isset($xml["POOL"]["#"]["QUESTION_MULTIPLECHOICE"])) {
            $mcquestions = $xml["POOL"]["#"]["QUESTION_MULTIPLECHOICE"];
        }
        else {
            return;
        }

        for ($i = 0; $i < sizeof ($mcquestions); $i++) {

            $question = $this->defaultquestion();

            $question->qtype = MULTICHOICE;
            $question->single = 1; // Only one answer is allowed

            $thisquestion = $mcquestions[$i];

            // determine if the question is already escaped html
            $ishtml = $thisquestion["#"]["BODY"][0]["#"]["FLAGS"][0]["#"]["ISHTML"][0]["@"]["value"];

            // put questiontext in question object
            if ($ishtml) {
                $question->questiontext = html_entity_decode(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]),ENT_QUOTES,'UTF-8');
            }

            // put name of question in question object, careful of length
            $question->name = shorten_text($question->questiontext, 254);

            $choices = $thisquestion["#"]["ANSWER"];
            for ($j = 0; $j < sizeof ($choices); $j++) {

                $choice = trim($choices[$j]["#"]["TEXT"][0]["#"]);
                // put this choice in the question object.
                if ($ishtml) {
                    $question->answer[$j] = html_entity_decode($choice,ENT_QUOTES,'UTF-8');
                }
                $question->answer[$j] = $question->answer[$j];

                $id = $choices[$j]["@"]["id"];
                $correct_answer_id = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"][0]["@"]["answer_id"];
                // if choice is the answer, give 100%, otherwise give 0%
                if (strcmp ($id, $correct_answer_id) == 0) {
                    $question->fraction[$j] = 1;
                    if ($ishtml) {
                        $question->feedback[$j] = html_entity_decode(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]),ENT_QUOTES,'UTF-8');
                    }
                    $question->feedback[$j] = $question->feedback[$j];
                } else {
                    $question->fraction[$j] = 0;
                    if ($ishtml) {
                        $question->feedback[$j] = html_entity_decode(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]),ENT_QUOTES,'UTF-8');
                    }
                    $question->feedback[$j] = $question->feedback[$j];
                }
            }
            $questions[] = $question;
        }
    }

    //----------------------------------------
    // Process Multiple Choice Questions With Multiple Answers
    //----------------------------------------
    function process_ma($xml, &$questions) {

        if (isset($xml["POOL"]["#"]["QUESTION_MULTIPLEANSWER"])) {
            $maquestions = $xml["POOL"]["#"]["QUESTION_MULTIPLEANSWER"];
        }
        else {
            return;
        }

        for ($i = 0; $i < sizeof ($maquestions); $i++) {

            $question = $this->defaultquestion();

            $question->qtype = MULTICHOICE;
            $question->defaultmark = 1;
            $question->single = 0; // More than one answers allowed
            $question->image = ""; // No images with this format

            $thisquestion = $maquestions[$i];

            // determine if the question is already escaped html
            $ishtml = $thisquestion["#"]["BODY"][0]["#"]["FLAGS"][0]["#"]["ISHTML"][0]["@"]["value"];

            // put questiontext in question object
            if ($ishtml) {
                $question->questiontext = html_entity_decode(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]),ENT_QUOTES,'UTF-8');
            }
            // put name of question in question object
            $question->name = shorten_text($question->questiontext, 254);

            $choices = $thisquestion["#"]["ANSWER"];
            $correctanswers = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"];

            for ($j = 0; $j < sizeof ($choices); $j++) {

                $choice = trim($choices[$j]["#"]["TEXT"][0]["#"]);
                // put this choice in the question object.
                $question->answer[$j] = $choice;

                $correctanswercount = sizeof($correctanswers);
                $id = $choices[$j]["@"]["id"];
                $iscorrect = 0;
                for ($k = 0; $k < $correctanswercount; $k++) {

                    $correct_answer_id = trim($correctanswers[$k]["@"]["answer_id"]);
                    if (strcmp ($id, $correct_answer_id) == 0) {
                        $iscorrect = 1;
                    }

                }
                if ($iscorrect) {
                    $question->fraction[$j] = floor(100000/$correctanswercount)/100000; // strange behavior if we have more than 5 decimal places
                    $question->feedback[$j] = trim($thisquestion["#"]["GRADABLE"][$j]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]);
                } else {
                    $question->fraction[$j] = 0;
                    $question->feedback[$j] = trim($thisquestion["#"]["GRADABLE"][$j]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]);
                }
            }

            $questions[] = $question;
        }
    }

    //----------------------------------------
    // Process Fill in the Blank Questions
    //----------------------------------------
    function process_fib($xml, &$questions) {

        if (isset($xml["POOL"]["#"]["QUESTION_FILLINBLANK"])) {
            $fibquestions = $xml["POOL"]["#"]["QUESTION_FILLINBLANK"];
        }
        else {
            return;
        }

        for ($i = 0; $i < sizeof ($fibquestions); $i++) {
            $question = $this->defaultquestion();

            $question->qtype = SHORTANSWER;
            $question->usecase = 0; // Ignore case

            $thisquestion = $fibquestions[$i];

            // determine if the question is already escaped html
            $ishtml = $thisquestion["#"]["BODY"][0]["#"]["FLAGS"][0]["#"]["ISHTML"][0]["@"]["value"];

            // put questiontext in question object
            if ($ishtml) {
                $question->questiontext = html_entity_decode(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]),ENT_QUOTES,'UTF-8');
            }
            // put name of question in question object
            $question->name = shorten_text($question->questiontext, 254);

            $answer = trim($thisquestion["#"]["ANSWER"][0]["#"]["TEXT"][0]["#"]);

            $question->answer[] = $answer;
            $question->fraction[] = 1;
            $question->feedback = array();

            if (is_array( $thisquestion['#']['GRADABLE'][0]['#'] )) {
                $question->feedback[0] = trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]);
            }
            else {
                $question->feedback[0] = '';
            }
            if (is_array( $thisquestion["#"]["GRADABLE"][0]["#"] )) {
                $question->feedback[1] = trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]);
            }
            else {
                $question->feedback[1] = '';
            }

            $questions[] = $question;
        }
    }

    //----------------------------------------
    // Process Matching Questions
    //----------------------------------------
    function process_matching($xml, &$questions) {

        if (isset($xml["POOL"]["#"]["QUESTION_MATCH"])) {
            $matchquestions = $xml["POOL"]["#"]["QUESTION_MATCH"];
        }
        else {
            return;
        }

        for ($i = 0; $i < sizeof ($matchquestions); $i++) {

            $question = $this->defaultquestion();

            $question->qtype = MATCH;

            $thisquestion = $matchquestions[$i];

            // determine if the question is already escaped html
            $ishtml = $thisquestion["#"]["BODY"][0]["#"]["FLAGS"][0]["#"]["ISHTML"][0]["@"]["value"];

            // put questiontext in question object
            if ($ishtml) {
                $question->questiontext = html_entity_decode(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]),ENT_QUOTES,'UTF-8');
            }
            // put name of question in question object
            $question->name = shorten_text($question->questiontext, 254);

            $choices = $thisquestion["#"]["CHOICE"];
            for ($j = 0; $j < sizeof ($choices); $j++) {

                $subquestion = NULL;

                $choice = $choices[$j]["#"]["TEXT"][0]["#"];
                $choice_id = $choices[$j]["@"]["id"];

                $question->subanswers[] = trim($choice);

                $correctanswers = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"];
                for ($k = 0; $k < sizeof ($correctanswers); $k++) {

                    if (strcmp($choice_id, $correctanswers[$k]["@"]["choice_id"]) == 0) {

                        $answer_id = $correctanswers[$k]["@"]["answer_id"];

                        $answers = $thisquestion["#"]["ANSWER"];
                        for ($m = 0; $m < sizeof ($answers); $m++) {

                            $answer = $answers[$m];
                            $current_ans_id = $answer["@"]["id"];
                            if (strcmp ($current_ans_id, $answer_id) == 0) {

                                $answer = $answer["#"]["TEXT"][0]["#"];
                                $question->subquestions[] = trim($answer);
                                break;
                            }
                        }
                        break;
                    }
                }
            }

            $questions[] = $question;

        }
    }
}
