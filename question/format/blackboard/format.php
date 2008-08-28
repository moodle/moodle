<?php // $Id$

////////////////////////////////////////////////////////////////////////////
/// Blackboard 6.0 Format
///
/// This Moodle class provides all functions necessary to import and export
///
///
////////////////////////////////////////////////////////////////////////////

// Based on default.php, included by ../import.php
/**
 * @package questionbank
 * @subpackage importexport
 */
require_once ("$CFG->libdir/xmlize.php");
require_once ("$CFG->libdir/tcpdf/html_entity_decode_php4.php");

class qformat_blackboard extends qformat_default {

    function provide_import() {
        return true;
    }


/********************************

    function readdata($filename) {
    /// Returns complete file with an array, one item per line

        if (is_readable($filename)) {

            $zip = zip_open($filename);
            $zip_entry = $zip_read($zip);
            if (strstr($zip_entry_name($zip_entry), "imsmanifest") == 0)
              $zip_entry = $zip_read($zip); // skip past manifest file

            if (zip_entry_open($zip, $zip_entry, "r")) {

              $strbuf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
              $buf = explode("\n", $strbuf);
              zip_entry_close($zip_entry);
              zip_close($zip);
              return $buf;

            } else {

              zip_close($zip);
              return false;

            }

        }

        return false;
    }

********************************/

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
            $question->questiontext = html_entity_decode_php4(trim($essayquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        }
        $question->questiontext = addslashes($question->questiontext);
        
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
            $question->questiontext = html_entity_decode_php4(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        }
        $question->questiontext = addslashes($question->questiontext);
        // put name in question object
        $question->name = substr($question->questiontext, 0, 254);

        $choices = $thisquestion["#"]["ANSWER"];

        $correct_answer = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"][0]["@"]["answer_id"];

        // first choice is true, second is false.
        $id = $choices[0]["@"]["id"];

        if (strcmp($id, $correct_answer) == 0) {  // true is correct
            $question->answer = 1;
            $question->feedbacktrue = addslashes(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
            $question->feedbackfalse = addslashes(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
        } else {  // false is correct
            $question->answer = 0;
            $question->feedbacktrue = addslashes(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
            $question->feedbackfalse = addslashes(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
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
            $question->questiontext = html_entity_decode_php4(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        }
        $question->questiontext = addslashes($question->questiontext);

        // put name of question in question object, careful of length
        $question->name = substr($question->questiontext, 0, 254);

        $choices = $thisquestion["#"]["ANSWER"];
        for ($j = 0; $j < sizeof ($choices); $j++) {

            $choice = trim($choices[$j]["#"]["TEXT"][0]["#"]);
            // put this choice in the question object.
            if ($ishtml) {
                $question->answer[$j] = html_entity_decode_php4($choice);
            }
            $question->answer[$j] = addslashes($question->answer[$j]);

            $id = $choices[$j]["@"]["id"];
            $correct_answer_id = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"][0]["@"]["answer_id"];
            // if choice is the answer, give 100%, otherwise give 0%
            if (strcmp ($id, $correct_answer_id) == 0) {
                $question->fraction[$j] = 1;
                if ($ishtml) {
                    $question->feedback[$j] = html_entity_decode_php4(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
                }
                $question->feedback[$j] = addslashes($question->feedback[$j]);
            } else {
                $question->fraction[$j] = 0;
                if ($ishtml) {
                    $question->feedback[$j] = html_entity_decode_php4(trim(@$thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
                }
                $question->feedback[$j] = addslashes($question->feedback[$j]);
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
        $question->defaultgrade = 1;
        $question->single = 0; // More than one answers allowed
        $question->image = ""; // No images with this format

        $thisquestion = $maquestions[$i];

        // determine if the question is already escaped html
        $ishtml = $thisquestion["#"]["BODY"][0]["#"]["FLAGS"][0]["#"]["ISHTML"][0]["@"]["value"];

        // put questiontext in question object
        if ($ishtml) {
            $question->questiontext = html_entity_decode_php4(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        }
        $question->questiontext = addslashes($question->questiontext);
        // put name of question in question object
        $question->name = substr($question->questiontext, 0, 254);

        $choices = $thisquestion["#"]["ANSWER"];
        $correctanswers = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"];

        for ($j = 0; $j < sizeof ($choices); $j++) {

            $choice = trim($choices[$j]["#"]["TEXT"][0]["#"]);
            // put this choice in the question object.
            $question->answer[$j] = addslashes($choice);

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
                $question->feedback[$j] = addslashes(trim($thisquestion["#"]["GRADABLE"][$j]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
            } else {
                $question->fraction[$j] = 0;
                $question->feedback[$j] = addslashes(trim($thisquestion["#"]["GRADABLE"][$j]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
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
            $question->questiontext = html_entity_decode_php4(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        }
        $question->questiontext = addslashes($question->questiontext);
        // put name of question in question object
        $question->name = substr($question->questiontext, 0, 254);

        $answer = trim($thisquestion["#"]["ANSWER"][0]["#"]["TEXT"][0]["#"]);

        $question->answer[] = addslashes($answer);
        $question->fraction[] = 1;
        $question->feedback = array();

        if (is_array( $thisquestion['#']['GRADABLE'][0]['#'] )) {
            $question->feedback[0] = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
        }
        else {
            $question->feedback[0] = '';
        }      
        if (is_array( $thisquestion["#"]["GRADABLE"][0]["#"] )) {
            $question->feedback[1] = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
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
            $question->questiontext = html_entity_decode_php4(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        }
        $question->questiontext = addslashes($question->questiontext);
        // put name of question in question object
        $question->name = substr($question->questiontext, 0, 254);

        $choices = $thisquestion["#"]["CHOICE"];
        for ($j = 0; $j < sizeof ($choices); $j++) {

            $subquestion = NULL;

            $choice = $choices[$j]["#"]["TEXT"][0]["#"];
            $choice_id = $choices[$j]["@"]["id"];
          
            $question->subanswers[] = addslashes(trim($choice));
 
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
                            $question->subquestions[] = addslashes(trim($answer));
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
?>
