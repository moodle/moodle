<?PHP // $Id$
////////////////////////////////////////////////////////////////////////////
/// Blackboard 6.0 Format
///
/// This Moodle class provides all functions necessary to import and export
///
///
////////////////////////////////////////////////////////////////////////////

// Based on default.php, included by ../import.php

require_once ("$CFG->libdir/xmlize.php");

class quiz_file_format extends quiz_default_format {

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
    $xml = xmlize($text);

    $questions = array();

    process_tf($xml, $questions);
    process_mc($xml, $questions);
    process_ma($xml, $questions);
    process_fib($xml, $questions);
    process_matching($xml, $questions);

    return $questions;
  }
}

//----------------------------------------
// Process True / False Questions
//----------------------------------------
function process_tf($xml, &$questions) {

    $tfquestions = $xml["POOL"]["#"]["QUESTION_TRUEFALSE"];

    for ($i = 0; $i < sizeof ($tfquestions); $i++) {
      
        $question = NULL;

        $question->qtype = TRUEFALSE;
        $question->defaultgrade = 1;
        $question->single = 1;	// Only one answer is allowed
        $question->image = "";	// No images with this format

	$thisquestion = $tfquestions[$i];
        // put questiontext in question object
	$question->questiontext = addslashes(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        // put name in question object
        $question->name = $question->questiontext;

	$choices = $thisquestion["#"]["ANSWER"];

        $correct_answer = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"][0]["@"]["answer_id"];

        // first choice is true, second is false.
        $id = $choices[0]["@"]["id"];

        if (strcmp($id, $correct_answer) == 0) {  // true is correct
            $question->answer = 1;
            $question->feedbacktrue = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
            $question->feedbackfalse = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
        } else {  // false is correct
            $question->answer = 0;
            $question->feedbacktrue = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
            $question->feedbackfalse = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
        }
        $questions[] = $question;
      }
}

//----------------------------------------
// Process Multiple Choice Questions
//----------------------------------------
function process_mc($xml, &$questions) {

    $mcquestions = $xml["POOL"]["#"]["QUESTION_MULTIPLECHOICE"];

    for ($i = 0; $i < sizeof ($mcquestions); $i++) {

        $question = NULL;

        $question->qtype = MULTICHOICE;
        $question->defaultgrade = 1;
        $question->single = 1;	// Only one answer is allowed
        $question->image = "";	// No images with this format

	$thisquestion = $mcquestions[$i];
        // put questiontext in question object
	$question->questiontext = addslashes(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        // put name of question in question object
        $question->name = $question->questiontext;

	$choices = $thisquestion["#"]["ANSWER"];
	for ($j = 0; $j < sizeof ($choices); $j++) {

	    $choice = trim($choices[$j]["#"]["TEXT"][0]["#"]);
            // put this choice in the question object.
            $question->answer[$j] = addslashes($choice);

	    $id = $choices[$j]["@"]["id"];
	    $correct_answer_id = $thisquestion["#"]["GRADABLE"][0]["#"]["CORRECTANSWER"][0]["@"]["answer_id"];
            // if choice is the answer, give 100%, otherwise give 0%
	    if (strcmp ($id, $correct_answer_id) == 0) {
	      $question->fraction[$j] = 1;
              $question->feedback[$j] = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
            } else {
	      $question->fraction[$j] = 0;
              $question->feedback[$j] = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
            }
        }
        $questions[] = $question;
    }
}

//----------------------------------------
// Process Multiple Choice Questions With Multiple Answers
//----------------------------------------
function process_ma($xml, &$questions) {

    $maquestions = $xml["POOL"]["#"]["QUESTION_MULTIPLEANSWER"];

    for ($i = 0; $i < sizeof ($maquestions); $i++) {

        $question = NULL;

        $question->qtype = MULTICHOICE;
        $question->defaultgrade = 1;
        $question->single = 0;	// More than one answers allowed
        $question->image = "";	// No images with this format

	$thisquestion = $maquestions[$i];
        // put questiontext in question object
	$question->questiontext = addslashes(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        // put name of question in question object
        $question->name = $question->questiontext;

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

    $fibquestions = $xml["POOL"]["#"]["QUESTION_FILLINBLANK"];
    for ($i = 0; $i < sizeof ($fibquestions); $i++) {

        $question = NULL;

        $question->qtype = SHORTANSWER;
        $question->defaultgrade = 1;
        $question->usecase = 0;	// Ignore case
        $question->image = "";	// No images with this format

	$thisquestion = $fibquestions[$i];
        // put questiontext in question object
	$question->questiontext = addslashes(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        // put name of question in question object
        $question->name = $question->questiontext;

	$answer = trim($thisquestion["#"]["ANSWER"][0]["#"]["TEXT"][0]["#"]);

        $question->answer[] = addslashes($answer);
        $question->fraction[] = 1;
        $question->feedback[0] = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_CORRECT"][0]["#"]));
        $question->feedback[1] = addslashes(trim($thisquestion["#"]["GRADABLE"][0]["#"]["FEEDBACK_WHEN_INCORRECT"][0]["#"]));
         
        $questions[] = $question;
      }
}

//----------------------------------------
// Process Matching Questions
//----------------------------------------
function process_matching($xml, &$questions) {

    $matchquestions = $xml["POOL"]["#"]["QUESTION_MATCH"];
    for ($i = 0; $i < sizeof ($matchquestions); $i++) {

        $question = NULL;

        $question->qtype = MATCH;
        $question->defaultgrade = 1;
        $question->image = "";	// No images with this format

	$thisquestion = $matchquestions[$i];
        // put questiontext in question object
	$question->questiontext = addslashes(trim($thisquestion["#"]["BODY"][0]["#"]["TEXT"][0]["#"]));
        // put name of question in question object
        $question->name = $question->questiontext;

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
?>
