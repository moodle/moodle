<?php  // $Id$

// This code has been adapted from mod/quiz/attempt.php
// if called only with parameter = q, it returns an imsmanifest in xml format
// if the results are submitted, it saves the results

    require_once("../../config.php");
    //require_once("lib.php");  //for version 1.4.x
    require_once("locallib.php");  //for version 1.5.x
    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID
	optional_variable ($questionIds); // set of questionids.  if present, only these questions from the quiz will be exported.
	optional_variable($lang);
    global $USER, $CFG;
	
    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }
    require_login($course->id);

    if (!$lang) {
        empty($USER->lang) ? 'de' : $USER->lang;
    }
    
/// Set number for next attempt:

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $attemptnumber = 2;
        foreach ($attempts as $attempt) {
            if ($attempt->attempt >= $attemptnumber) {
                $attemptnumber = $attempt->attempt + 1;
            }
        }
    } else {
        $attemptnumber = 1;
    }

    $strattemptnum = get_string("attempt", "quiz", $attemptnumber);


/// Check availability

    if ($quiz->attempts and $attempts and count($attempts) >= $quiz->attempts) {
        error("Sorry, you've had $quiz->attempts attempts already.", "view.php?id=$cm->id");
    }

/// Check subnet access
    if ($quiz->subnet and !address_in_subnet(getremoteaddr(), $quiz->subnet)) {
        error(get_string("subneterror", "quiz"), "view.php?id=$cm->id");
    }

/// Check password access
    if ($quiz->password and empty($_POST['q'])) {
        if (empty($_POST['quizpassword'])) {
    
            print_heading($quiz->name);
            print_heading(get_string("attempt", "quiz", $attemptnumber));
            if (trim(strip_tags($quiz->intro))) {
                print_simple_box(format_text($quiz->intro), "CENTER");
            }
            echo "<br />\n";
        
            echo "<form name=\"passwordform\" method=\"post\" action=\"attempt.php?id=$cm->id\">\n";
            print_simple_box_start("center");
            
            echo "<div align=\"center\">\n";
            print_string("requirepasswordmessage", "quiz");
            echo "<br /><br />\n";
            echo " <input name=\"quizpassword\" type=\"password\" value=\"\" />";
            echo " <input type=\"submit\" value=\"".get_string("ok")."\" />\n";
            echo "</div>\n";

            print_simple_box_end();
            echo "</form>\n";
            
            print_footer();
            exit;

        } else {
            if (strcmp($quiz->password, $_POST['quizpassword']) !== 0) {
                error(get_string("passworderror", "quiz"), "view.php?id=$cm->id");
            }
            unset($_POST['quizpassword']); /// needed so as not to confuse later code dealing with submitted answers
        }
    }
    

//    $unattempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id);
    $timenow = time();
    $available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose);
/// Check to see if they are submitting answers
    if ($rawanswers = data_submitted()) {

        $error = null;
        $rawanswers = (array)$rawanswers;

        $shuffleorder = NULL;

        unset($rawanswers["q"]);  // quiz id
        if (! count($rawanswers) and ! $timelimit) {
            frageplayer_error(get_string("noanswers", "quiz"));
        }

        if (!$questions = get_records_list("quiz_questions", "id", $quiz->questions)) {
            frageplayer_error('No questions found while trying to grade test attempt!');
        }

        foreach ($rawanswers as $key => $value) { // Parse input for question->response

            if ($postedquestionid = quiz_extract_posted_id($key)) {
                $questions[$postedquestionid]->response[$key] = trim($value);

            } else if ('shuffleorder' == $key) {
                $shuffleorder = explode(",", $value);   // Actual order questions were given in

            } else {  // Useful for debugging new question types.  Must be last.
                $error .= "Unrecognizable input has been posted ($key -> $value)...";
            }
        }
        if (!is_null($error)) {
            frageplayer_error($error);
        }

        /// Retrieve ->maxgrade for all questions
        If (!($grades = quiz_get_question_grades($quiz->id, $quiz->questions))) {
            $grades = array();
        }
        foreach ($grades as $qid => $grade) {
            $questions[$qid]->maxgrade = $grade->grade;
        }

        if (isset($timesincestart)) {
            $quiz->timesincestart = $timesincestart;   // To pass it on to quiz_grade_responses
        }

        if (!$result = quiz_grade_responses($quiz, $questions)) {
            frageplayer_error("Could not grade your quiz attempt");
        }

        if ($attempt = quiz_save_attempt($quiz, $questions, $result, $attemptnumber)) {
            add_to_log($course->id, "quiz", "submit",
                       "review.php?id=$cm->id&amp;attempt=$attempt->id", "$quiz->id", $cm->id);
        } else {
            frageplayer_error(get_string("alreadysubmitted", "quiz"));
        }

        if (! quiz_save_best_grade($quiz, $USER->id)) {
            frageplayer_error("Sorry! Could not calculate your best grade!");
        }

        if ($quiz->grade) {
            $strgrade = get_string("grade");
            $strscore = get_string("score", "quiz");
            frageplayer_success("$strscore: $result->sumgrades/$quiz->sumgrades ($result->percentage %)"
                    . "$strgrade: $result->grade/$quiz->grade");
        } else {
            // todo: ungraded quiz response
            frageplayer_success("Ungraded quiz OK");
        }
        
        // we shouldn't get here ...
        echo "&success=unknown";
        exit;
    }


/// Actually seeing the questions marks the start of an attempt

    if (isset($unattempt) && $unattempt) {
        $attempt = $unattempt;

    } else if ($attempt = quiz_start_attempt($quiz->id, $USER->id, $attemptnumber)) {
        add_to_log($course->id, "quiz", "attempt", 
                "review.php?id=$cm->id&amp;attempt=$attempt->id", "$quiz->id", $cm->id);
    } else {
        frageplayer_error("Sorry! Could not start the quiz (could not save starting time)");
    }


    if (!$available) {
        frageplayer_error("Sorry, this quiz is not available");
    }

/// Export all the questions
    $questions = quiz_get_attempt_questions($quiz, $attempt, true);
	if ($questionIds){
		$subSetOfQuestions = array ();
		$questionIds = explode ("_", $questionIds);
		foreach ($questions as $key => $question){
			if (in_array ($key, $questionIds)){
				$subSetOfQuestions[$key] = $question;
			}
		
		}
		$questions = $subSetOfQuestions;
	
	}
	
	/*
	echo "<pre>";
	print (var_export ($questions, 1));
	
	die();
	*/
	
    if ($quiz->attemptonlast && $attemptnumber >= 2 and
            $quiz->attempts == 0 || !unattempt) {
        // There are unlimited attempts or it is a new attempt.
        // As the attempt also builds on the last, we can here
        // have the student see the scores of the pre-entered
        // responses that we here will have graded:
        $result = quiz_grade_responses($quiz, $questions);
        $result->attemptbuildsonthelast = true;
    } else {
        $result = NULL;
    }
    
    // we want all information in the ims export, so the below was commented out
    // the qti player can determine how it wants to handle it.
    /*// We do not show feedback or correct answers during an attempt:
    // changed from false to '0' to get a meaningful qti export
    $quiz->feedback = $quiz->correctanswers = 0;*/
    if (!$quiz->feedback) {
        $quiz->feedback = 0;
    }
    if (!$quiz->correctanswers) {
        $quiz->correctanswers = 0;
    }
    
    // shuffle the order of the questions
    if (!$shuffleorder) {
        if (!empty($quiz->shufflequestions)) {              // Mix everything up
            $questions = swapshuffle_assoc($questions);
        } else {
            if ($questionIds) {
                // only a subset of questions are being used
                $shuffleorder = $questionIds;
            } else {
                $shuffleorder = explode(",", $quiz->questions);  // Use originally defined order
            }
        }
    }

    if ($shuffleorder) { // Order has been defined, so reorder questions
        $oldquestions = $questions;
        $questions = array();
        foreach ($shuffleorder as $key) {
            $questions[] = $oldquestions[$key];      // This loses the index key, but doesn't matter
        }
    }
    
// create the imsmanifest file    
    require("format.php");  // Parent class
    require("format/qti/format.php");
    
    $redirecturl = "{$CFG->wwwroot}/mod/quiz/index.php?id={$course->id}";
    //$submiturl = "{$CFG->wwwroot}/mod/quiz/attempt.php?q={$quiz->id}";
    $submiturl = "{$CFG->wwwroot}/mod/quiz/imsattempt.php";
    $format = new quiz_file_format();
    $format->export_quiz($course, $quiz, $questions, $result, $redirecturl, $submiturl);

    
    function frageplayer_error($errorstr, $exit = true) {
        echo "&success=false&faultstring=$errorstr";
        if ($exit) {
            exit;
        }
    }
    function frageplayer_success($successstr, $exit = true) {
        echo "&success=true&resultstring=$successstr&";
        if ($exit) {
            exit;
        }
    }
    
?>
