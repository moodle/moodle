<?PHP  // $Id$

/// Library of functions and constants for module lesson
/// (replace lesson with the name of your module and delete this line)


if (!defined("LESSON_UNSEENPAGE")) {
	define("LESSON_UNSEENPAGE", 1); // Next page -> any page not seen before
	}
if (!defined("LESSON_UNANSWEREDPAGE")) {
	define("LESSON_UNANSWEREDPAGE", 2); // Next page -> any page not answered correctly
	}

$LESSON_NEXTPAGE_ACTION = array (0 => get_string("normal", "lesson"),
                          LESSON_UNSEENPAGE => get_string("showanunseenpage", "lesson"),
                          LESSON_UNANSWEREDPAGE => get_string("showanunansweredpage", "lesson") );


if (!defined("LESSON_NEXTPAGE")) {
	define("LESSON_NEXTPAGE", -1); // Next page
	}
if (!defined("LESSON_EOL")) {
	define("LESSON_EOL", -9); // End of Lesson
	}
if (!defined("LESSON_UNDEFINED")) {
	define("LESSON_UNDEFINED", -99); // undefined
	}

if (!defined("LESSON_SHORTANSWER")) {
    define("LESSON_SHORTANSWER",   "1");
}        
if (!defined("LESSON_TRUEFALSE")) {
    define("LESSON_TRUEFALSE",     "2");
}
if (!defined("LESSON_MULTICHOICE")) {
    define("LESSON_MULTICHOICE",   "3");
}
if (!defined("LESSON_RANDOM")) {
    define("LESSON_RANDOM",        "4");
}
if (!defined("LESSON_MATCHING")) {
    define("LESSON_MATCHING",         "5");
}
if (!defined("LESSON_RANDOMSAMATCH")) {
    define("LESSON_RANDOMSAMATCH", "6");
}
if (!defined("LESSON_DESCRIPTION")) {
    define("LESSON_DESCRIPTION",   "7");
}
if (!defined("LESSON_NUMERICAL")) {
    define("LESSON_NUMERICAL",     "8");
}
if (!defined("LESSON_MULTIANSWER")) {
    define("LESSON_MULTIANSWER",   "9");
}

$LESSON_QUESTION_TYPE = array ( LESSON_MULTICHOICE => get_string("multichoice", "quiz"),
                              LESSON_TRUEFALSE     => get_string("truefalse", "quiz"),
                              LESSON_SHORTANSWER   => get_string("shortanswer", "quiz"),
                              LESSON_NUMERICAL     => get_string("numerical", "quiz"),
                              LESSON_MATCHING      => get_string("match", "quiz")
//                            LESSON_DESCRIPTION   => get_string("description", "quiz"),
//                            LESSON_RANDOM        => get_string("random", "quiz"),
//                            LESSON_RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
//                            LESSON_MULTIANSWER   => get_string("multianswer", "quiz"),
                              );

if (!defined("LESSON_BRANCHTABLE")) {
    define("LESSON_BRANCHTABLE",   "20");
}
if (!defined("LESSON_ENDOFBRANCH")) {
    define("LESSON_ENDOFBRANCH",   "21");
}

if (!defined("LESSON_ANSWER_EDITOR")) {
    define("LESSON_ANSWER_EDITOR",   "1");
}
if (!defined("LESSON_RESPONSE_EDITOR")) {
    define("LESSON_RESPONSE_EDITOR",   "2");
}
/*******************************************************************/
function lesson_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
/// Given an array of value, creates a popup menu to be part of a form
/// $options["value"]["label"]
    
    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    if ($script) {
        $javascript = "onChange=\"$script\"";
    } else {
        $javascript = "";
    }

    $output = "<SELECT NAME=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <OPTION VALUE=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " SELECTED";
        }
        $output .= ">$nothing</OPTION>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <OPTION VALUE=\"$value\"";
            if ($value == $selected) {
                $output .= " SELECTED";
            }
			// stop zero label being replaced by array index value
            // if ($label) {
            //    $output .= ">$label</OPTION>\n";
            // } else {
            //     $output .= ">$value</OPTION>\n";
			//  }
			$output .= ">$label</OPTION>\n";
            
        }
    }
    $output .= "</SELECT>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}   


/*******************************************************************/
function lesson_add_instance($lesson) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $lesson->timemodified = time();

    $lesson->available = make_timestamp($lesson->availableyear, 
			$lesson->availablemonth, $lesson->availableday, $lesson->availablehour, 
			$lesson->availableminute);

    $lesson->deadline = make_timestamp($lesson->deadlineyear, 
			$lesson->deadlinemonth, $lesson->deadlineday, $lesson->deadlinehour, 
			$lesson->deadlineminute);

    return insert_record("lesson", $lesson);
}


/*******************************************************************/
function lesson_update_instance($lesson) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    $lesson->timemodified = time();
    $lesson->available = make_timestamp($lesson->availableyear, 
			$lesson->availablemonth, $lesson->availableday, $lesson->availablehour, 
			$lesson->availableminute);
    $lesson->deadline = make_timestamp($lesson->deadlineyear, 
			$lesson->deadlinemonth, $lesson->deadlineday, $lesson->deadlinehour, 
			$lesson->deadlineminute);
    $lesson->id = $lesson->instance;

    return update_record("lesson", $lesson);
}


/*******************************************************************/
function lesson_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    if (! $lesson = get_record("lesson", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("lesson", "id", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_pages", "lessonid", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_answers", "lessonid", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_attempts", "lessonid", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_grades", "lessonid", "$lesson->id")) {
        $result = false;
    }

    return $result;
}

/*******************************************************************/
function lesson_user_outline($course, $user, $mod, $lesson) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $user->id",
                "grade DESC")) {
        foreach ($grades as $grade) {
            $max_grade = number_format($grade->grade * $lesson->grade / 100.0, 1);
            break;
        }
        $return->time = $grade->completed;
        if ($lesson->retake) {
            $return->info = get_string("gradeis", "lesson", $max_grade)." (".
                get_string("attempt", "lesson", count($grades)).")";
        } else {
            $return->info = get_string("gradeis", "lesson", $max_grade);
        }
    } else {
        $return->info = get_string("no")." ".get_string("attempts", "lesson");
    }
    return $return;
}

/*******************************************************************/
function lesson_user_complete($course, $user, $mod, $lesson) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.

    if ($attempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $user->id",
                "retry, timeseen")) {
        print_simple_box_start();
		$table->head = array (get_string("attempt", "lesson"),  get_string("numberofpagesviewed", "lesson"),
			get_string("numberofcorrectanswers", "lesson"), get_string("time"));
		$table->width = "100%";
		$table->align = array ("center", "center", "center", "center");
		$table->size = array ("*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;

        $retry = 0;
        $npages = 0;
        $ncorrect = 0;
        
		foreach ($attempts as $attempt) {
			if ($attempt->retry == $retry) {
				$npages++;
                if ($attempt->correct) {
                    $ncorrect++;
                }
                $timeseen = $attempt->timeseen;
            } else {
			    $table->data[] = array($retry + 1, $npages, $ncorrect, userdate($timeseen));
                $retry++;
                $npages = 1;
                if ($attempt->correct) {
                    $ncorrect = 1;
                } else {
                    $ncorrect = 0;
                }
			}
		}
        if ($npages) {
			    $table->data[] = array($retry + 1, $npages, $ncorrect, userdate($timeseen));
        }
		print_table($table);
	    print_simple_box_end();
        // also print grade summary
        if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $user->id",
                    "grade DESC")) {
            foreach ($grades as $grade) {
                $max_grade = number_format($grade->grade * $lesson->grade / 100.0, 1);
                break;
            }
            if ($lesson->retake) {
                echo "<p>".get_string("gradeis", "lesson", $max_grade)." (".
                    get_string("attempts", "lesson").": ".count($grades).")</p>";
            } else {
                echo "<p>".get_string("gradeis", "lesson", $max_grade)."</p>";
            }
        }
    } else {
        echo get_string("no")." ".get_string("attempts", "lesson");
    }

    
    return true;
}

/*******************************************************************/
function lesson_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity 
/// that has occurred in lesson activities and print it out. 
/// Return true if there was output, or false is there was none.

    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}

/*******************************************************************/
function lesson_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

    global $CFG;

    return true;
}

/*******************************************************************/
function lesson_grades($lessonid) {
/// Must return an array of grades for a given instance of this module, 
/// indexed by user.  It also returns a maximum allowed grade.
    global $CFG;

	if (!$lesson = get_record("lesson", "id", $lessonid)) {
		error("Lesson record not found");
	}
    if (!empty($lesson->usemaxgrade)) {
        $grades = get_records_sql_menu("SELECT userid,MAX(grade) FROM {$CFG->prefix}lesson_grades WHERE
                lessonid = $lessonid GROUP BY userid");
    } else {
        $grades = get_records_sql_menu("SELECT userid,AVG(grade) FROM {$CFG->prefix}lesson_grades WHERE
            lessonid = $lessonid GROUP BY userid");
    }
    
    // convert grades from percentages and tidy the numbers
    if ($grades) {
        foreach ($grades as $userid => $grade) {
            $return->grades[$userid] = number_format($grade * $lesson->grade / 100.0, 1);
        }
    }
    $return->maxgrade = $lesson->grade;

    return $return;
}

/*******************************************************************/
function lesson_get_participants($lessonid) {
//Must return an array of user records (all data) who are participants
//for a given instance of lesson. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)

    global $CFG;
    
    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}lesson_attempts a
                                 WHERE a.lessonid = '$lessonid' and
                                       u.id = a.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other lesson functions go here.  Each of them must have a name that 
/// starts with lesson_

/*******************************************************************/
function lesson_iscorrect($pageid, $jumpto) {
    // returns true is jumpto page is (logically) after the pageid page, other returns false
    
    // first test the special values
    if (!$jumpto) {
        // same page
        return false;
    } elseif ($jumpto == LESSON_NEXTPAGE) {
        return true;
    } elseif ($jumpto == LESSON_EOL) {
        return true;
    }
    // we have to run through the pages from pageid looking for jumpid
    $apageid = get_field("lesson_pages", "nextpageid", "id", $pageid);
    while (true) {
        if ($jumpto == $apageid) {
            return true;
        }
        if ($apageid) {
            $apageid = get_field("lesson_pages", "nextpageid", "id", $apageid);
        } else {
            return false;
        }
    }
    return false; // should never be reached
}


/*******************************************************************/
function lesson_save_question_options($question) {
/// Given some question info and some data about the the answers
/// this function parses, organises and saves the question
/// This is only used when IMPORTING questions and is only called
/// from format.php
/// Lifted from mod/quiz/lib.php - 
///    1. all reference to oldanswers removed
///    2. all reference to quiz_multichoice table removed
///    3. In SHORTANSWER questions usecase is store in the qoption field
///    4. In NUMERIC questions store the range as two answers
///    5. TRUEFALSE options are ignored
///    6. For MULTICHOICE questions with more than one answer the qoption field is true
///
/// Returns $result->error or $result->notice
    
    $timenow = time();
    switch ($question->qtype) {
        case LESSON_SHORTANSWER:

            $answers = array();
            $maxfraction = -1;

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    unset($answer);
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    if ($question->fraction[$key] >=0.5) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $answer->answer   = $dataanswer;
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert shortanswer quiz answer!";
                        return $result;
                    }
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }


            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
            break;

        case LESSON_NUMERICAL:   // Note similarities to SHORTANSWER

            $answers = array();
            $maxfraction = -1;

            
            // for each answer store the pair of min and max values even if they are the same 
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    unset($answer);
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->jumpto = LESSON_NEXTPAGE;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $answer->answer   = $question->min[$key].":".$question->max[$key];
                    $answer->response = $question->feedback[$key];
                    if (!$answer->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert numerical quiz answer!";
                        return $result;
                    }
                    
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
        break;


        case LESSON_TRUEFALSE:

            // the truth
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("true", "quiz");
            $answer->grade = $question->answer * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbacktrue)) {
                $answer->response = $question->feedbacktrue;
            }
            if (!$true->id = insert_record("lesson_answers", $answer)) {
                $result->error = "Could not insert quiz answer \"true\")!";
                return $result;
            }

            // the lie    
            unset($answer);
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("false", "quiz");
            $answer->grade = (1 - (int)$question->answer) * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbackfalse)) {
                $answer->response = $question->feedbackfalse;
            }
            if (!$false->id = insert_record("lesson_answers", $answer)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }

          break;


        case LESSON_MULTICHOICE:

            $totalfraction = 0;
            $maxfraction = -1;

            $answers = array();

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    unset($answer);
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    $answer->answer   = $dataanswer;
                    $answer->response = $question->feedback[$key];
                    if (!$answer->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert multichoice quiz answer! ";
                        return $result;
                    }
                    // for Sanity checks
                    if ($question->fraction[$key] > 0) {                 
                        $totalfraction += $question->fraction[$key];
                    }
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($question->single) {
                if ($maxfraction != 1) {
                    $maxfraction = $maxfraction * 100;
                    $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                    return $result;
                }
            } else {
                $totalfraction = round($totalfraction,2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $result->notice = get_string("fractionsaddwrong", "quiz", $totalfraction);
                    return $result;
                }
            }
        break;

        case LESSON_MATCHING:

            $subquestions = array();

            $i = 0;
            // Insert all the new question+answer pairs
            foreach ($question->subquestions as $key => $questiontext) {
                $answertext = $question->subanswers[$key];
                if (!empty($questiontext) and !empty($answertext)) {
                    unset($answer);
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->timecreated   = $timenow;
                    $answer->answer = $questiontext;
                    $answer->response   = $answertext;
                    if ($i == 0) {
                        // first answer contains the correct answer jump
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    if (!$subquestion->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert quiz match subquestion!";
                        return $result;
                    }
                    $subquestions[] = $subquestion->id;
                    $i++;
                }
            }

            if (count($subquestions) < 3) {
                $result->notice = get_string("notenoughsubquestions", "quiz");
                return $result;
            }

            break;


        case LESSON_RANDOMSAMATCH:
            $options->question = $question->id;
            $options->choose = $question->choose;
            if ($existing = get_record("quiz_randomsamatch", "question", $options->question)) {
                $options->id = $existing->id;
                if (!update_record("quiz_randomsamatch", $options)) {
                    $result->error = "Could not update quiz randomsamatch options!";
                    return $result;
                }
            } else {
                if (!insert_record("quiz_randomsamatch", $options)) {
                    $result->error = "Could not insert quiz randomsamatch options!";
                    return $result;
                }
            }
        break;

        case LESSON_MULTIANSWER:
            if (!$oldmultianswers = get_records("quiz_multianswers", "question", $question->id, "id ASC")) {
                $oldmultianswers = array();
            }

            // Insert all the new multi answers
            foreach ($question->answers as $dataanswer) {
                if ($oldmultianswer = array_shift($oldmultianswers)) {  // Existing answer, so reuse it
                    $multianswer = $oldmultianswer;
                    $multianswer->positionkey = $dataanswer->positionkey;
                    $multianswer->norm = $dataanswer->norm;
                    $multianswer->answertype = $dataanswer->answertype;

                    if (! $multianswer->answers = quiz_save_multianswer_alternatives
                            ($question->id, $dataanswer->answertype,
                             $dataanswer->alternatives, $oldmultianswer->answers))
                    {
                        $result->error = "Could not update multianswer alternatives! (id=$multianswer->id)";
                        return $result;
                    }
                    if (!update_record("quiz_multianswers", $multianswer)) {
                        $result->error = "Could not update quiz multianswer! (id=$multianswer->id)";
                        return $result;
                    }
                } else {    // This is a completely new answer
                    unset($multianswer);
                    $multianswer->question = $question->id;
                    $multianswer->positionkey = $dataanswer->positionkey;
                    $multianswer->norm = $dataanswer->norm;
                    $multianswer->answertype = $dataanswer->answertype;

                    if (! $multianswer->answers = quiz_save_multianswer_alternatives
                            ($question->id, $dataanswer->answertype,
                             $dataanswer->alternatives))
                    {
                        $result->error = "Could not insert multianswer alternatives! (questionid=$question->id)";
                        return $result;
                    }
                    if (!insert_record("quiz_multianswers", $multianswer)) {
                        $result->error = "Could not insert quiz multianswer!";
                        return $result;
                    }
                }
            }
        break;

        case LESSON_RANDOM:
        break;

        case LESSON_DESCRIPTION:
        break;

        default:
            $result->error = "Unsupported question type ($question->qtype)!";
            return $result;
        break;
    }
    return true;
}


?>
