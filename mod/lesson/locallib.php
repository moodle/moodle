<?php
/// mnielsen @ CDC
/// locallib.php is the new lib file for lesson module.
/// including locallib.php is the same as including the old lib.php
	
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
/// CDC-FLAG 6/14/04 ///
if (!defined("LESSON_UNSEENBRANCHPAGE")) {
	define("LESSON_UNSEENBRANCHPAGE", -50); // Unseen branch page
	}
if (!defined("LESSON_PREVIOUSPAGE")) {
	define("LESSON_PREVIOUSPAGE", -40); // previous page
	}
if (!defined("LESSON_RANDOMPAGE")) {
	define("LESSON_RANDOMPAGE", -60); // random branch page
	}
if (!defined("LESSON_RANDOMBRANCH")) {
	define("LESSON_RANDOMBRANCH", -70); // random branch
	}
if (!defined("LESSON_CLUSTERJUMP")) {
	define("LESSON_CLUSTERJUMP", -80); // random within a cluster
	}
/// CDC-FLAG ///	
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
/// CDC-FLAG /// 6/16/04
if (!defined("LESSON_ESSAY")) {
	define("LESSON_ESSAY", "10");
}
if (!defined("LESSON_CLUSTER")) {
    define("LESSON_CLUSTER",   "30");
}
if (!defined("LESSON_ENDOFCLUSTER")) {
    define("LESSON_ENDOFCLUSTER",   "31");
}
/// CDC-FLAG ///

$LESSON_QUESTION_TYPE = array ( LESSON_MULTICHOICE => get_string("multichoice", "quiz"),
                              LESSON_TRUEFALSE     => get_string("truefalse", "quiz"),
                              LESSON_SHORTANSWER   => get_string("shortanswer", "quiz"),
                              LESSON_NUMERICAL     => get_string("numerical", "quiz"),
                              LESSON_MATCHING      => get_string("match", "quiz"),
							  LESSON_ESSAY		   => get_string("essay", "lesson")  /// CDC-FLAG 6/16/04
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

//////////////////////////////////////////////////////////////////////////////////////
/// Any other lesson functions go here.  Each of them must have a name that 
/// starts with lesson_

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
                    /// CDC-FLAG changed some defaults
					/* Original Code
					if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
					Replaced with:                    */
					if ($answer->grade > 50 ) {
	                    $answer->jumpto = LESSON_NEXTPAGE;
                        $answer->score = 1;
                    }
					// end Replace
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
                $answertext = $question->subanswers[$key]; echo $answertext; echo "<br>"; exit;
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

    $output = "<label for=$name class=hidden-label>$name</label><SELECT id=$name NAME=$name $javascript>\n"; //CDC hidden label added.
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
function lesson_iscorrect($pageid, $jumpto) {
    // returns true is jumpto page is (logically) after the pageid page, other returns false
    
    // first test the special values
    if (!$jumpto) {
        // same page
        return false;
    } elseif ($jumpto == LESSON_NEXTPAGE) {
        return true;
	/// CDC-FLAG 6/21/04 ///
	} elseif ($jumpto == LESSON_UNSEENBRANCHPAGE) {
        return true;
    } elseif ($jumpto == LESSON_RANDOMPAGE) {
        return true;
    } elseif ($jumpto == LESSON_CLUSTERJUMP) {
        return true;
	/// CDC-FLAG ///
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

/// CDC-FLAG ///
/*******************************************************************/
function lesson_display_branch_jumps($lesson_id, $pageid) {
// this fucntion checks to see if a page is a branch or is
// a page that is enclosed by a branch table and an endofbranch/eol

	// NoticeFix  ... this may cause problems... not sure
	if($pageid == 0) {
		// first page
		return false;
	}
	// get all of the lesson pages
	if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson_id")) {
		// adding first page
		return false;
	}

	if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
		return true;
	}
	
	return lesson_is_page_in_branch($lessonpages, $pageid);
}

/*******************************************************************/
function lesson_display_cluster_jump($lesson_id, $pageid) {
// this fucntion checks to see if a page is a cluster page or is
// a page that is enclosed by a cluster page and an endofcluster/eol

	// NoticeFix  ... this may cause problems... not sure
	if($pageid == 0) {
		// first page
		return false;
	}
	// get all of the lesson pages
	if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson_id")) {
		// adding first page
		return false;
	}

	if ($lessonpages[$pageid]->qtype == LESSON_CLUSTER) {
		return true;
	}
	
	return lesson_is_page_in_cluster($lessonpages, $pageid);

}

// 6/21/04
/*******************************************************************/
function execute_teacherwarning($lesson) {
// this function checks to see if a LESSON_CLUSTERJUMP or 
// a LESSON_UNSEENBRANCHPAGE is used in a lesson.
// This function is only executed when a teacher is 
// checking the navigation for a lesson.

	// get all of the lesson answers
	if (!$lessonanswers = get_records_select("lesson_answers", "lessonid = $lesson")) {
		// no answers, then not useing cluster or unseen
		return false;
	}
	// just check for the first one that fulfills the requirements
	foreach ($lessonanswers as $lessonanswer) {
		if ($lessonanswer->jumpto == LESSON_CLUSTERJUMP || $lessonanswer->jumpto == LESSON_UNSEENBRANCHPAGE) {
			return true;
		}
	}
	
	// if no answers use either of the two jumps
	return false;
}


// 6/18/04
/*******************************************************************/
function lesson_cluster_jump($lesson, $user, $pageid) {
// this fucntion interprets LESSON_CLUSTERJUMP
// it will select a page randomly
// and the page selected will be inbetween a cluster page and endofcluter/eol
// and the page selected will be a page that has not been viewed already
// and if any pages are within a branchtable/endofbranch then only 1 page within 
// the branchtable/endofbranch will be randomly selected (sub clustering)

	// get the number of retakes
    if (!$retakes = count_records("lesson_grades", "lessonid", $lesson, "userid", $user)) {
		$retakes = 0;
	}

	// get all the lesson_attempts aka what the user has seen
	if ($seen = get_records_select("lesson_attempts", "lessonid = $lesson AND userid = $user AND retry = $retakes", "timeseen DESC")) {
		foreach ($seen as $value) { // load it into an array that I can more easily use
			$seenpages[$value->pageid] = $value->pageid;
		}
	} else {
		$seenpages = array();
	}

	// get the lesson pages
	if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
		error("Error: could not find records in lesson_pages table");
	}
	// find the start of the cluster
	while ($pageid != 0) { // this condition should not be satisfied... should be a cluster page
		if ($lessonpages[$pageid]->qtype == LESSON_CLUSTER) {
			break;
		}
		$pageid = $lessonpages[$pageid]->prevpageid;
	}

	$pageid = $lessonpages[$pageid]->nextpageid; // move down from the cluster page

	while (true) {  // now load all the pages into the cluster that are not already inside of a branch table.
		if ($lessonpages[$pageid]->qtype == LESSON_ENDOFCLUSTER) {
			// store the endofcluster page's jump
			$exitjump = get_field("lesson_answers", "jumpto", "pageid", $pages[$count][0], "lessonid", $lesson);
			if ($exitjump == LESSON_NEXTPAGE) {
				$exitjump = $lessonpages[$pageid]->nextpageid;
			}
			if ($exitjump == 0) {
				$exitjump = LESSON_EOL;
			}
			break;
		} elseif (!lesson_is_page_in_branch($lessonpages, $pageid) && $lessonpages[$pageid]->qtype != LESSON_ENDOFBRANCH) {
			// load page into array when it is not in a branch table and when it is not an endofbranch
			$clusterpages[] = $lessonpages[$pageid];
		}
		if ($lessonpages[$pageid]->nextpageid == 0) {
			// shouldn't ever get here... should be using endofcluster
			$exitjump = LESSON_EOL;
			break;
		} else {
			$pageid = $lessonpages[$pageid]->nextpageid;
		}
	}

	// filter out the ones we have seen
	foreach ($clusterpages as $clusterpage) {
		if ($clusterpage->qtype == LESSON_BRANCHTABLE) {			// if branchtable, check to see if any pages inside have been viewed
			$branchpages = lesson_pages_in_branch($lessonpages, $clusterpage->id); // get the pages in the branchtable
			$flag = true;
			foreach ($branchpages as $branchpage) {
				if (array_key_exists($branchpage->id, $seenpages)) {  // check if any of the pages have been viewed
					$flag = false;
				}
			}
			if ($flag && count($branchpages) > 0) {
				// add branch table
				$unseen[] = $clusterpage;
			}		
		} else {
			// add any other type of page that has not already been viewed
			if (!array_key_exists($clusterpage->id, $seenpages)) {
				$unseen[] = $clusterpage;
			}
		}
	}

	if (isset($unseen)) { // if not set, then use exitjump, otherwise find out next page/branch
		$nextpage = $unseen[rand(0, count($unseen)-1)];
	} else {
		return $exitjump; // seen all there is to see, leave the cluster
	}
	
	if ($nextpage->qtype == LESSON_BRANCHTABLE) { // if branch table, then pick a random page inside of it
		$branchpages = lesson_pages_in_branch($lessonpages, $nextpage->id);
		return $branchpages[rand(0, count($branchpages)-1)]->id;
	} else { // otherwise, return the page's id
		return $nextpage->id;
	}
}

/*******************************************************************/
function lesson_pages_in_branch($lessonpages, $branchid) {
// returns pages that are within a branch
	
	$pageid = $lessonpages[$branchid]->nextpageid;  // move to the first page after the branch table
	$pagesinbranch = array();
	
	while (true) { 
		if ($pageid == 0) { // EOL
			break;
		} elseif ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
			break;
		} elseif ($lessonpages[$pageid]->qtype == LESSON_ENDOFBRANCH) {
			break;
		}
		$pagesinbranch[] = $lessonpages[$pageid];
		$pageid = $lessonpages[$pageid]->nextpageid;
	}
	
	return $pagesinbranch;
}

/*******************************************************************/
function lesson_unseen_question_jump($lesson, $user, $pageid) {
// This function interprets the LESSON_UNSEENBRANCHPAGE jump.
// will return the pageid of a random unseen page that is within a branch

	// get the number of retakes
    if (!$retakes = count_records("lesson_grades", "lessonid", $lesson, "userid", $user)) {
		$retakes = 0;
	}

	// get all the lesson_attempts aka what the user has seen
	if ($viewedpages = get_records_select("lesson_attempts", "lessonid = $lesson AND userid = $user AND retry = $retakes", "timeseen DESC")) {
		foreach($viewedpages as $viewed) {
			$seenpages[] = $viewed->pageid;
		}
	} else {
		$seenpages = array();
	}

	// get the lesson pages
	if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
		error("Error: could not find records in lesson_pages table");
	}
	
	if ($pageid == LESSON_UNSEENBRANCHPAGE) {  // this only happens when a student leaves in the middle of an unseen question within a branch series
		$pageid = $seenpages[0];  // just change the pageid to the last page viewed inside the branch table
	}

	// go up the pages till branch table
	while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page
		if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
			break;
		}
		$pageid = $lessonpages[$pageid]->prevpageid;
	}
	
	$pagesinbranch = lesson_pages_in_branch($lessonpages, $pageid);
	
	// this foreach loop stores all the pages that are within the branch table but are not in the $seenpages array
	foreach($pagesinbranch as $page) {	
		if (!in_array($page->id, $seenpages)) {
			$unseen[] = $page->id;
		}
	}

	if(!isset($unseen)) {
		if(isset($pagesinbranch)) {
			$temp = end($pagesinbranch);
			$nextpage = $temp->nextpageid; // they have seen all the pages in the branch, so go to EOB/next branch table/EOL
		} else {
			// there are no pages inside the branch, so return the next page
			$nextpage = $lessonpages[$pageid]->nextpageid;
		}
		if ($nextpage == 0) {
			return LESSON_EOL;
		} else {
			return $nextpage;
		}
	} else {
		return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
	}
}

// 6/15/04
/*******************************************************************/
function lesson_unseen_branch_jump($lesson, $user) {
// This will return a random unseen branch table

    if (!$retakes = count_records("lesson_grades", "lessonid", $lesson, "userid", $user)) {
		$retakes = 0;
	}

	if (!$seenbranches = get_records_select("lesson_branch", "lessonid = $lesson AND userid = $user AND retry = $retakes",
				"timeseen DESC")) {
		error("Error: could not find records in lesson_branch table");
	}

	// get the lesson pages
	if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
		error("Error: could not find records in lesson_pages table");
	}
	
	// this loads all the viewed branch tables into $seen untill it finds the branch table with the flag
	// which is the branch table that starts the unseenbranch function
	$seen = array();	
	foreach ($seenbranches as $seenbranch) {
		if (!$seenbranch->flag) {
			$seen[$seenbranch->pageid] = $seenbranch->pageid;
		} else {
			$start = $seenbranch->pageid;
			break;
		}
	}
	// this function searches through the lesson pages to find all the branch tables
	// that follow the flagged branch table
	$pageid = $lessonpages[$start]->nextpageid; // move down from the flagged branch table
	while ($pageid != 0) {  // grab all of the branch table till eol
		if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
			$branchtables[] = $lessonpages[$pageid]->id;
		}
		$pageid = $lessonpages[$pageid]->nextpageid;
	}
	
	foreach ($branchtables as $branchtable) {
		// load all of the unseen branch tables into unseen
		if (!array_key_exists($branchtable, $seen)) {
			$unseen[] = $branchtable;
		}
	}
	if (isset($unseen)) {
		return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
	} else {
		return LESSON_EOL;  // has viewed all of the branch tables
	}
}

/*******************************************************************/
function lesson_random_question_jump($lesson, $pageid) {
// This function will return the pageid of a random page 
// that is within a branch table

	// get the lesson pages
	if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
		error("Error: could not find records in lesson_pages table");
	}

	// go up the pages till branch table
	while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page

		if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
			break;
		}
		$pageid = $lessonpages[$pageid]->prevpageid;
	}

	// get the pages within the branch	
	$pagesinbranch = lesson_pages_in_branch($lessonpages, $pageid);
	
	if(!isset($pagesinbranch)) {
		// there are no pages inside the branch, so return the next page
		return $lessonpages[$pageid]->nextpageid;
	} else {
		return $pagesinbranch[rand(0, count($pagesinbranch)-1)]->id;  // returns a random page id for the next page
	}
}

// 6/15/04
/*******************************************************************/
function lesson_is_page_in_branch($pages, $pageid) {
// This function's purpose is to check if a page is within a branch or not

	$pageid = $pages[$pageid]->prevpageid; // move up one

	// go up the pages till branch table	
	while (true) {
		if ($pageid == 0) {  // ran into the beginning of the lesson
			return false;
		} elseif ($pages[$pageid]->qtype == LESSON_ENDOFBRANCH) { // ran into the end of another branch table
			return false;
		} elseif ($pages[$pageid]->qtype == LESSON_CLUSTER) { // do not look beyond a cluster
			return false;
		} elseif ($pages[$pageid]->qtype == LESSON_BRANCHTABLE) { // hit a branch table
			return true;
		}
		$pageid = $pages[$pageid]->prevpageid;
	}

}

/*******************************************************************/
function lesson_is_page_in_cluster($pages, $pageid) {
// This function checks to see if a page is within a cluster or not

	$pageid = $pages[$pageid]->prevpageid; // move up one

	// go up the pages till branch table	
	while (true) {
		if ($pageid == 0) {  // ran into the beginning of the lesson
			return false;
		} elseif ($pages[$pageid]->qtype == LESSON_ENDOFCLUSTER) { // ran into the end of another branch table
			return false;
		} elseif ($pages[$pageid]->qtype == LESSON_CLUSTER) { // hit a branch table
			return true;
		}
		$pageid = $pages[$pageid]->prevpageid;
	}
}

/*******************************************************************/
function lesson_print_tree_menu($lessonid, $pageid, $id) {
// prints the contents of the left menu

	if(!$pages = get_records_select("lesson_pages", "lessonid = $lessonid")) {
		error("Error: could not find lesson pages");
	}
	while ($pageid != 0) {
		lesson_print_tree_link_menu($pages[$pageid], $id);			
		$pageid = $pages[$pageid]->nextpageid;
	}
}

/*******************************************************************/
function lesson_print_tree_link_menu($page, $id) { 
// prints the actual link for the left menu

	if ($page->qtype == LESSON_BRANCHTABLE && !$page->display) {
		return false;
	}
	
	// set up some variables  NoticeFix  changed whole function
	$output = "";
	$close = false;
	$title=$page->title;  //CDC Chris Berri took out parsing of title in left menu on 6/11
	$link="id=$id&action=navigation&pageid=".$page->id;
	
	if($_SERVER['QUERY_STRING']==$link) { 
		$close=true; 
		$output.="<div class='active'><em>"; 
	} 
	if (($page->qtype!=LESSON_BRANCHTABLE)||($page->qtype==LESSON_ENDOFBRANCH)||($page->qtype==21)) {
		$output .= "";
	} else {
		$output .= "<li><a href=\"view.php?id=$id&action=navigation&pageid=$page->id\">".$title."</a></li>\n"; 
	}
	if($close) {
		$output.="</em></div>";
	}
	echo $output;

} 

/*******************************************************************/
function lesson_print_tree($pageid, $lessonid, $cmid, $pixpath) {
// this function prints out the tree view list

	if(!$pages = get_records_select("lesson_pages", "lessonid = $lessonid")) {
		error("Error: could not find lesson pages");
	}
	echo "<table>";
	while ($pageid != 0) {
		echo "<tr><td>";
		if(($pages[$pageid]->qtype != LESSON_BRANCHTABLE) && ($pages[$pageid]->qtype != LESSON_ENDOFBRANCH)) {
			$output = "<a style='color:#DF041E;' href=\"view.php?id=$cmid&display=".$pages[$pageid]->id."\">".$pages[$pageid]->title."</a>\n";
		} else {
			$output = "<a href=\"view.php?id=$cmid&display=".$pages[$pageid]->id."\">".$pages[$pageid]->title."</a>\n";
			
			if($answers = get_records_select("lesson_answers", "lessonid = $lessonid and pageid = $pageid")) {
				$output .= "Jumps to: ";
				$end = end($answers);
				foreach ($answers as $answer) {
					if ($answer->jumpto == 0) {
						$output .= get_string("thispage", "lesson");
					} elseif ($answer->jumpto == LESSON_NEXTPAGE) {
						$output .= get_string("nextpage", "lesson");
					} elseif ($answer->jumpto == LESSON_EOL) {
						$output .= get_string("endoflesson", "lesson");
					} elseif ($answer->jumpto == LESSON_UNSEENBRANCHPAGE) {
						$output .= get_string("unseenpageinbranch", "lesson");  
					} elseif ($answer->jumpto == LESSON_PREVIOUSPAGE) {
						$output .= get_string("previouspage", "lesson");
					} elseif ($answer->jumpto == LESSON_RANDOMPAGE) {
						$output .= get_string("randompageinbranch", "lesson");
					} elseif ($answer->jumpto == LESSON_RANDOMBRANCH) {
						$output .= get_string("randombranch", "lesson");
					} elseif ($answer->jumpto == LESSON_CLUSTERJUMP) {
						$output .= get_string("clusterjump", "lesson");			
					} else {
						$output .= $pages[$answer->jumpto]->title;
					}
					if ($answer->id != $end->id) {
						$output .= ", ";
					}
				}
			}
		}
		
		echo $output;		
		if (count($pages) > 1) {
			echo "<a title=\"move\" href=\"lesson.php?id=$cmid&action=move&pageid=".$pages[$pageid]->id."\">\n".
				"<img src=\"$pixpath/t/move.gif\" hspace=\"2\" height=11 width=11 alt=\"move\" border=0></a>\n"; //CDC alt text added.
		}
		echo "<a title=\"update\" href=\"lesson.php?id=$cmid&action=editpage&pageid=".$pages[$pageid]->id."\">\n".
			"<img src=\"$pixpath/t/edit.gif\" hspace=\"2\" height=11 width=11 alt=\"edit\" border=0></a>\n".
			"<a title=\"delete\" href=\"lesson.php?id=$cmid&action=confirmdelete&pageid=".$pages[$pageid]->id."\">\n".
			"<img src=\"$pixpath/t/delete.gif\" hspace=\"2\" height=11 width=11 alt=\"delete\" border=0></a>\n"; //CDC alt text added.

		echo "</tr></td>";
		$pageid = $pages[$pageid]->nextpageid;
	}
	echo "</table>";
}

/*******************************************************************/
function lesson_calculate_ongoing_score($lesson, $USER) {
// this calculates and prints the ongoing score for students

	// get the number of retries
    if (!$retries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id)) {
		$retries = 0;
	}

	if (!$lesson->custom) {
		$ncorrect = 0;						
		if ($pagesanswered = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND 
				userid = $USER->id AND retry = $retries order by timeseen")) {

			foreach ($pagesanswered as $pageanswered) {
				if (@!array_key_exists($pageanswered->pageid, $temp)) {
					$temp[$pageanswered->pageid] = array($pageanswered->correct, 1);
				} else {
					if ($temp[$pageanswered->pageid][1] < $lesson->maxattempts) {
						$n = $temp[$pageanswered->pageid][1] + 1;
						$temp[$pageanswered->pageid] = array($pageanswered->correct, $n);
					}
				}
			}
			foreach ($temp as $value => $key) {
				if ($key[0] == 1) {
					$ncorrect += 1;
				}
			}
		}
		$nviewed = count($temp); // this counts number of Questions the user viewed
		
		$output->correct = $ncorrect;
		$output->viewed = $nviewed;
		print_simple_box(get_string("ongoingnormal", "lesson", $output), "center");

	} else {
		$score = 0;
		$currenthigh = 0;
		if ($useranswers = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND 
				userid = $USER->id AND retry = $retries", "timeseen")) {

			foreach ($useranswers as $useranswer) {
				if (@!array_key_exists($useranswer->pageid, $temp)) {
					$temp[$useranswer->pageid] = array($useranswer->answerid, 1);
				} else {
					if ($temp[$useranswer->pageid][1] < $lesson->maxattempts) {
						$n = $temp[$useranswer->pageid][1] + 1;
						$temp[$useranswer->pageid] = array($useranswer->answerid, $n);
					}
				}
			}
			if ($answervalues = get_records_select("lesson_answers",  "lessonid = $lesson->id")) {
				if ($pages = get_records_select("lesson_pages", "lessonid = $lesson->id")) {
					foreach ($pages as $page) {
						$questions[$page->id] = $page->qtype;
					}
				} else {
					$questions = array();
				}
				$currenthighscore = array();
				foreach ($answervalues as $answervalue) {
					if (array_key_exists($answervalue->pageid, $temp)) {
						if ($temp[$answervalue->pageid][0] == $answervalue->id && $questions[$answervalue->pageid] != LESSON_ESSAY) {
							$score = $score + $answervalue->score;
							if (isset($currenthighscore[$answervalue->pageid])) {
								if ($currenthighscore[$answervalue->pageid] < $answervalue->score) {
									$currenthighscore[$answervalue->pageid] = $answervalue->score;
								}
							} else {
								$currenthighscore[$answervalue->pageid] = $answervalue->score;
							}
						} elseif ($questions[$answervalue->pageid] != LESSON_ESSAY) {
							if (isset($currenthighscore[$answervalue->pageid])) {
								if ($currenthighscore[$answervalue->pageid] < $answervalue->score) {
									$currenthighscore[$answervalue->pageid] = $answervalue->score;
								}
							} else {
								$currenthighscore[$answervalue->pageid] = $answervalue->score;
							}
						}
					}
				}
				// add up the current high score
				foreach ($currenthighscore as $value) {
					$currenthigh += $value;
				}
			} else {
				error("Error: Could not find answers!");
			}
		}
		if ($score > $lesson->grade) {
			$score = $lesson->grade;
		} elseif ($score < 0) {
			$score = 0;
		}
		
		$ongoingoutput->grade = $lesson->grade;
		$ongoingoutput->score = $score;
		$ongoingoutput->currenthigh = $currenthigh;
		print_simple_box(get_string("ongoingcustom", "lesson", $ongoingoutput), "center");
	}
}

/*******************************************************************/
function lesson_qtype_menu($qtypes, $selected="", $link="", $onclick="") {
// prints the question types for when editing and adding a page

	$output = "";
	foreach ($qtypes as $value => $label) {
		if ($value == $selected) {
			$output .= "<b>$label</b>";
			$output .= "<input type=\"hidden\" name=\"qtype\" value=\"$value\"> \n";
		} else {
			$output .= "<a onClick=\"$onclick\" href=\"$link"."&qtype=$value\">$label</a>";
		}
		if ($label != end($qtypes)) {
			$output .= " | ";
		}
	}
	echo $output;
}

/*******************************************************************/
function lesson_check_nickname($name) {
// used to check high score nicknames.
// checks nickname agains a list of "bad words" in filter.php

	if ($name == NULL) {
		return false;
	}
	
	require_once('filter.php');
	
	foreach ($filterwords as $filterword) {
		if (strstr($name, $filterword)) {
			return false;
		}
	}
	return true;
}
/// CDC-FLAG ///

?>