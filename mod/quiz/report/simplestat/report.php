<?PHP  // $Id$

/// Overview report just displays a big table of all the attempts

////////////////////////////////////////////////////////////////
/// With the refactoring of the quiz module in July-2004, some
/// of the functions in lib.php were moved here instead as they
/// are no longer in use by the other quiz components.
/// These functions are quiz_get_attempt_responses,
/// quiz_grade_attempt, quiz_grade_attempt_results,
/// quiz_remove_unwanted_questions and quiz_get_answers.
/// They were all properly renamed by exchanging quiz_
/// with quiz_report_simplestat_
//////////////////////////////////////////////////////////

function quiz_report_simplestat_get_attempt_responses($attempt) {
// Given an attempt object, this function gets all the
// stored responses and returns them in a format suitable
// for regrading using quiz_grade_attempt_results()
    global $CFG;

    if (!$responses = get_records_sql("SELECT q.id, q.qtype, q.category, q.questiontext,
                                              q.defaultgrade, q.image, r.answer
                                        FROM {$CFG->prefix}quiz_responses r,
                                             {$CFG->prefix}quiz_questions q
                                       WHERE r.attempt = '$attempt->id'
                                         AND q.id = r.question")) {
        notify("Could not find any responses for that attempt!");
        return false;
    }


    foreach ($responses as $key => $response) {
        if ($response->qtype == RANDOM) {
            $responses[$key]->random = $response->answer;
            $responses[$response->answer]->delete = true;

            $realanswer = $responses[$response->answer]->answer;

            if (is_array($realanswer)) {
                $responses[$key]->answer = $realanswer;
            } else {
                $responses[$key]->answer = explode(",", $realanswer);
            }

        } else if ($response->qtype == NUMERICAL or $response->qtype == SHORTANSWER) {
            $responses[$key]->answer = array($response->answer);
        } else {
            $responses[$key]->answer = explode(",",$response->answer);
        }
    }
    foreach ($responses as $key => $response) {
        if (!empty($response->delete)) {
            unset($responses[$key]);
        }
    }

    return $responses;
}

function quiz_report_simplestat_grade_attempt_question_result($question,
                                            $answers,
                                            $gradecanbenegative= false)
{
    $grade    = 0;   // default
    $correct  = array();
    $feedback = array();
    $response = array();

    switch ($question->qtype) {
        case SHORTANSWER:
            if ($question->answer) {
                $question->answer = trim(stripslashes($question->answer[0]));
            } else {
                $question->answer = "";
            }
            $response[0] = $question->answer;
            $feedback[0] = '';  // Default
            foreach ($answers as $answer) {  // There might be multiple right answers

                $answer->answer = trim($answer->answer);  // Just in case

                if ($answer->fraction >= 1.0) {
                    $correct[] = $answer->answer;
                }
                if (!$answer->usecase) {       // Don't compare case
                    $answer->answer = strtolower($answer->answer);
                    $question->answer = strtolower($question->answer);
                }

                $potentialgrade = (float)$answer->fraction * $question->grade;

                if ($potentialgrade >= $grade and (strpos(' '.$answer->answer, '*'))) {
                    $answer->answer = str_replace('\*','@@@@@@',$answer->answer);
                    $answer->answer = str_replace('*','.*',$answer->answer);
                    $answer->answer = str_replace('@@@@@@', '\*',$answer->answer);
                    $answer->answer = str_replace('+', '\+',$answer->answer);
                    if (eregi('^'.$answer->answer.'$', $question->answer)) {
                        $feedback[0] = $answer->feedback;
                        $grade = $potentialgrade;
                    }

                } else if ($answer->answer == $question->answer) {
                    $feedback[0] = $answer->feedback;
                    $grade = $potentialgrade;
                }
            }

            break;

        case NUMERICAL:
            if ($question->answer) {
                $question->answer = trim(stripslashes($question->answer[0]));
            } else {
                $question->answer = "";
            }
            $response[0] = $question->answer;
            $bestshortanswer = 0;
            foreach ($answers as $answer) {  // There might be multiple right answers
                if ($answer->fraction > $bestshortanswer) {
                    $correct[$answer->id] = $answer->answer;
                    $bestshortanswer = $answer->fraction;
                    $feedback[0] = $answer->feedback;  // Show feedback for best answer
                }
                if ('' != $question->answer           // Must not be mixed up with zero!
                    && (float)$answer->fraction > (float)$grade // Do we need to bother?
                    and                      // and has lower procedence than && and ||.
                    strtolower($question->answer) == strtolower($answer->answer)
                    || '' != trim($answer->min)
                    && ((float)$question->answer >= (float)$answer->min)
                    && ((float)$question->answer <= (float)$answer->max))
                {
                    //$feedback[0] = $answer->feedback;  No feedback was shown for wrong answers
                    $grade = (float)$answer->fraction;
                }
            }
            $grade *= $question->grade; // Normalize to correct weight
            break;

        case TRUEFALSE:
            if ($question->answer) {
                $question->answer = $question->answer[0];
            } else {
                $question->answer = NULL;
            }
            foreach($answers as $answer) {  // There should be two answers (true and false)
                $feedback[$answer->id] = $answer->feedback;
                if ($answer->fraction > 0) {
                    $correct[$answer->id]  = true;
                }
                if ($question->answer == $answer->id) {
                    $grade = (float)$answer->fraction * $question->grade;
                    $response[$answer->id] = true;
                }
            }
            break;


        case MULTICHOICE:
            foreach($answers as $answer) {  // There will be multiple answers, perhaps more than one is right
                $feedback[$answer->id] = $answer->feedback;
                if ($answer->fraction > 0) {
                    $correct[$answer->id] = true;
                }
                if (!empty($question->answer)) {
                    foreach ($question->answer as $questionanswer) {
                        if ($questionanswer == $answer->id) {
                            $response[$answer->id] = true;
                            if ($answer->single) {
                                $grade = (float)$answer->fraction * $question->grade;
                                continue;
                            } else {
                                $grade += (float)$answer->fraction * $question->grade;
                            }
                        }
                    }
                }
            }
            break;

        case MATCH:
            $matchcount = $totalcount = 0;

            foreach ($question->answer as $questionanswer) {  // Each answer is "subquestionid-answerid"
                $totalcount++;
                $qarr = explode('-', $questionanswer);        // Extract subquestion/answer.
                $subquestionid = $qarr[0];
                $subanswerid = $qarr[1];
                if ($subquestionid and $subanswerid and (($subquestionid == $subanswerid) or
                    ($answers[$subquestionid]->answertext == $answers[$subanswerid]->answertext))) {
                    // Either the ids match exactly, or the answertexts match exactly
                    // (in case two subquestions had the same answer)
                    $matchcount++;
                    $correct[$subquestionid] = true;
                } else {
                    $correct[$subquestionid] = false;
                }
                $response[$subquestionid] = $subanswerid;
            }

            $grade = $question->grade * $matchcount / $totalcount;

            break;

        case RANDOMSAMATCH:
            $bestanswer = array();
            foreach ($answers as $answer) {  // Loop through them all looking for correct answers
                if (empty($bestanswer[$answer->question])) {
                    $bestanswer[$answer->question] = 0;
                    $correct[$answer->question] = "";
                }
                if ($answer->fraction > $bestanswer[$answer->question]) {
                    $bestanswer[$answer->question] = $answer->fraction;
                    $correct[$answer->question] = $answer->answer;
                }
            }
            $answerfraction = 1.0 / (float) count($question->answer);
            foreach ($question->answer as $questionanswer) {  // For each random answered question
                $rqarr = explode('-', $questionanswer);   // Extract question/answer.
                $rquestion = $rqarr[0];
                $ranswer = $rqarr[1];
                $response[$rquestion] = $questionanswer;
                if (isset($answers[$ranswer])) {         // If the answer exists in the list
                    $answer = $answers[$ranswer];
                    $feedback[$rquestion] = $answer->feedback;
                    if ($answer->question == $rquestion) {    // Check that this answer matches the question
                        $grade += (float)$answer->fraction * $question->grade * $answerfraction;
                    }
                }
            }
            break;

        case MULTIANSWER:
            // Default setting that avoids a possible divide by zero:
            $subquestion->grade = 1.0;

            foreach ($question->answer as $questionanswer) {

                // Resetting default values for subresult:
                $subresult->grade = 0.0;
                $subresult->correct = array();
                $subresult->feedback = array();

                // Resetting subquestion responses:
                $subquestion->answer = array();

                $qarr = explode('-', $questionanswer, 2);
                $subquestion->answer[] = $qarr[1];  // Always single answer for subquestions
                foreach ($answers as $multianswer) {
                    if ($multianswer->id == $qarr[0]) {
                        $subquestion->qtype = $multianswer->answertype;
                        $subquestion->grade = $multianswer->norm;
                        $subresult = quiz_report_simplestat_grade_attempt_question_result($subquestion, $multianswer->subanswers, true);
                        break;
                    }
                }


                // Summarize subquestion results:
                $grade += $subresult->grade;
                $feedback[] = $subresult->feedback[0];
                $correct[]  = $subresult->correct[0];

                // Each response instance also contains the partial
                // fraction grade for the response:
                $response[] = $subresult->grade/$subquestion->grade
                              . '-' . $subquestion->answer[0];
            }
            // Normalize grade:
            $grade *= $question->grade/($question->defaultgrade);
            break;

        case DESCRIPTION:  // Descriptions are not graded.
            break;

        case RANDOM:   // Returns a recursive call with the real question
            $realquestion = get_record
                    ('quiz_questions', 'id', $question->random);
            $realquestion->answer = $question->answer;
            $realquestion->grade = $question->grade;
            return quiz_report_simplestat_grade_attempt_question_result($realquestion, $answers);
    }

    $result->grade =
            $gradecanbenegative ? $grade            // Grade can be negative
                                : max(0.0, $grade); // Grade must not be negative
    $result->correct = $correct;
    $result->feedback = $feedback;
    $result->response = $response;
    return $result;
}

function quiz_report_simplestat_remove_unwanted_questions(&$questions, $quiz) {
/// Given an array of questions, and a list of question IDs,
/// this function removes unwanted questions from the array
/// Used by review.php and attempt.php to counter changing quizzes

    $quizquestions = array();
    $quizids = explode(",", $quiz->questions);
    foreach ($quizids as $quizid) {
        $quizquestions[$quizid] = true;
    }
    foreach ($questions as $key => $question) {
        if (!isset($quizquestions[$question->id])) {
            unset($questions[$key]);
        }
    }
}

function quiz_report_simplestat_get_answers($question, $answerids=NULL) {
// Given a question, returns the correct answers for a given question
    global $CFG;

    if (empty($answerids)) {
        $answeridconstraint = '';
    } else {
        $answeridconstraint = " AND a.id IN ($answerids) ";
    }

    switch ($question->qtype) {
        case SHORTANSWER:       // Could be multiple answers
            return get_records_sql("SELECT a.*, sa.usecase
                                      FROM {$CFG->prefix}quiz_shortanswer sa,
                                           {$CFG->prefix}quiz_answers a
                                     WHERE sa.question = '$question->id'
                                       AND sa.question = a.question "
                                  . $answeridconstraint);

        case TRUEFALSE:         // Should be always two answers
            return get_records("quiz_answers", "question", $question->id);

        case MULTICHOICE:       // Should be multiple answers
            return get_records_sql("SELECT a.*, mc.single
                                      FROM {$CFG->prefix}quiz_multichoice mc,
                                           {$CFG->prefix}quiz_answers a
                                     WHERE mc.question = '$question->id'
                                       AND mc.question = a.question "
                                  . $answeridconstraint);

        case MATCH:
            return get_records("quiz_match_sub", "question", $question->id);

        case RANDOMSAMATCH:       // Could be any of many answers, return them all
            return get_records_sql("SELECT a.*
                                      FROM {$CFG->prefix}quiz_questions q,
                                           {$CFG->prefix}quiz_answers a
                                     WHERE q.category = '$question->category'
                                       AND q.qtype = ".SHORTANSWER."
                                       AND q.id = a.question ");

        case NUMERICAL:         // Logical support for multiple answers
            return get_records_sql("SELECT a.*, n.min, n.max
                                      FROM {$CFG->prefix}quiz_numerical n,
                                           {$CFG->prefix}quiz_answers a
                                     WHERE a.question = '$question->id'
                                       AND n.answer = a.id "
                                  . $answeridconstraint);

        case DESCRIPTION:
            return true; // there are no answers for description

        case RANDOM:
            return quiz_get_answers
                    (get_record('quiz_questions', 'id', $question->random));

        case MULTIANSWER:       // Includes subanswers
            $answers = array();

            $virtualquestion->id = $question->id;

            if ($multianswers = get_records('quiz_multianswers', 'question', $question->id)) {
                foreach ($multianswers as $multianswer) {
                    $virtualquestion->qtype = $multianswer->answertype;
                    // Recursive call for subanswers
                    $multianswer->subanswers = quiz_get_answers($virtualquestion, $multianswer->answers);
                    $answers[] = $multianswer;
                }
            }
            return $answers;

        default:
            return false;
    }
}


function quiz_report_simplestat_grade_attempt_results($quiz, $questions) {
/// Given a list of questions (including answers for each one)
/// this function does all the hard work of calculating the
/// grades for each question, as well as a total grade for
/// for the whole quiz.  It returns everything in a structure
/// that looks like:
/// $result->sumgrades    (sum of all grades for all questions)
/// $result->percentage   (Percentage of grades that were correct)
/// $result->grade        (final grade result for the whole quiz)
/// $result->grades[]     (array of grades, indexed by question id)
/// $result->response[]   (array of response arrays, indexed by question id)
/// $result->feedback[]   (array of feedback arrays, indexed by question id)
/// $result->correct[]    (array of feedback arrays, indexed by question id)

    if (!$questions) {
        error("No questions!");
    }

    if (!$grades = get_records_menu("quiz_question_grades", "quiz", $quiz->id, "", "question,grade")) {
        error("No grades defined for these quiz questions!");
    }

    $result->sumgrades = 0;

    foreach ($questions as $question) {

        $question->grade = $grades[$question->id];

        if (!$answers = quiz_report_simplestat_get_answers($question)) {
            error("No answers defined for question id $question->id!");
        }

        $questionresult = quiz_report_simplestat_grade_attempt_question_result($question,
                                                             $answers);
        // if time limit is enabled and exceeded, return zero grades
        if($quiz->timelimit > 0) {
            if(($quiz->timelimit + 60) <= $quiz->timesincestart) {
                $questionresult->grade = 0;
            }
        }

        $result->grades[$question->id] = round($questionresult->grade, 2);
        $result->sumgrades += $questionresult->grade;
        $result->feedback[$question->id] = $questionresult->feedback;
        $result->response[$question->id] = $questionresult->response;
        $result->correct[$question->id] = $questionresult->correct;
    }

    $fraction = (float)($result->sumgrades / $quiz->sumgrades);
    $result->percentage = format_float($fraction * 100.0);
    $result->grade      = format_float($fraction * $quiz->grade);
    $result->sumgrades = round($result->sumgrades, 2);

    return $result;
}


class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG;
        global $download;

        optional_variable($download, "");

    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&mode=simplestat");
        } else {
            $currentgroup = false;
        }

        if ($currentgroup) {
            $users = get_group_students($currentgroup, "u.lastname ASC");
        } else {
            $users = get_course_students($course->id, "u.lastname ASC");
        }

        $data = array();
        $questionorder = explode(',', $quiz->questions);

        $count = 0;
        foreach ($questionorder as $questionid) {
            $count++;
            $question[$count] = get_record("quiz_questions", "id", $questionid);
        }

    /// For each person in the class, get their best attempt
    /// and create a table listing results for each person
        if ($users) {
            foreach ($users as $user) {

                $data[$user->id]->firstname = $user->firstname;
                $data[$user->id]->lastname = $user->lastname;
                $data[$user->id]->grades = array(); // by default

                if (!$attempts = quiz_get_user_attempts($quiz->id, $user->id)) {
                    continue;
                }
                if (!$bestattempt = quiz_calculate_best_attempt($quiz, $attempts)) {
                    continue;
                }
                if (!$questions = quiz_report_simplestat_get_attempt_responses($bestattempt, $quiz)) {
                    continue;
                }
                quiz_report_simplestat_remove_unwanted_questions($questions, $quiz);

                if (!$results = quiz_report_simplestat_grade_attempt_results($quiz, $questions)) {
                    error("Could not re-grade this quiz attempt!");
                }

                $count = 0;
                foreach ($questionorder as $questionid) {
                    $count++;
                    $data[$user->id]->grades[$count] = $results->grades[$questionid];
                }
            }
        }

        $count = count($questionorder);
        $total = array();
        $average = array();
        for ($i=1; $i<=$count; $i++) {
            $total[$i] = 0.0;
            $average[$i] = 0.0;
        }

        $datacount = 0;
        foreach ($data as $userid => $datum) {
            if ($datum->grades) {
                $datacount++;
                foreach ($datum->grades as $key => $grade) {
                    $total[$key]+= $grade;
                }
            }
        }

        if ($datacount) {
            foreach ($total as $key => $sum) {
                $average[$key] = format_float($sum/$datacount, 2);
            }
        }

    /// If spreadsheet is wanted, produce one
        if ($download == "xls") {
            require_once("$CFG->libdir/excel/Worksheet.php");
            require_once("$CFG->libdir/excel/Workbook.php");
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$course->shortname ".$quiz->name.".xls" );
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");

            $workbook = new Workbook("-");
            // Creating the first worksheet
            $myxls = &$workbook->add_worksheet('Simple Quiz Statistics');

        /// Print names of all the fields
            $myxls->write_string(0,0,$quiz->name);
            $myxls->set_column(0,0,25);
                
            $myxls->set_column(1,$count,9);
            for ($i=1; $i<=$count; $i++) {
                $myxls->write_string(0,$i,$i);
            }
        
        /// Print all the user data

            $row=1;
            foreach ($data as $userid => $datum) {
                $myxls->write_string($row,0,fullname($datum));
                for ($i=1; $i<=$count; $i++) {
                    if (isset($datum->grades[$i])) {
                        $myxls->write_number($row,$i,$datum->grades[$i]);
                    }
                }
                $row++;
            }

        /// Print all the averages
            for ($i=1; $i<=$count; $i++) {
                $myxls->write_number($row,$i,$average[$i]);
            }

            $formatot =& $workbook->add_format();
            // format number 10 is percent, two digit
            $formatot->set_num_format(10);
        /// Print all the averages as percentages
            $row++;
            $myxls->write_string($row,0,"%");
            for ($i=1; $i<=$count; $i++) {
//                $percent = format_float($average[$i] * 100);
//                $myxls->write_text($row,$i,"$percent%");
                $myxls->write_number($row,$i,$average[$i],$formatot);
            }

            $workbook->close();
        
            exit;
        }
    

    /// If a text file is wanted, produce one
        if ($download == "txt") {
        /// Print header to force download
    
            header("Content-Type: application/download\n"); 
            header("Content-Disposition: attachment; filename=$course->shortname ".$quiz->name.".txt");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");


        /// Print names of all the fields
    
            echo "$quiz->name";
            for ($i=1; $i<=$count; $i++) {
                echo "\t$i";
            }
            echo "\n";
        
        /// Print all the user data

            foreach ($data as $userid => $datum) {
                echo fullname($datum);
                for ($i=1; $i<=$count; $i++) {
                    echo "\t";
                    if (isset($datum->grades[$i])) {
                        echo $datum->grades[$i];
                    }
                }
                echo "\n";
            }

        /// Print all the averages
            echo "\t";
            for ($i=1; $i<=$count; $i++) {
                echo "\t".$average[$i];
            }
            echo "\n";

        /// Print all the averages as percentages
            echo "\t%";
            for ($i=1; $i<=$count; $i++) {
                $percent = format_float($average[$i] * 100);
                echo "\t$percent";
            }
            echo "\n";
        
            exit;
        }




    /// Otherwise, display the table as HTML

        echo "<table border=1 align=\"center\">";
        echo "<tr>";
        echo "<td>&nbsp;</td>";
        for ($i=1; $i<=$count; $i++) {
            $title = '';
            if (!empty($question[$i]->questiontext)) {
                $title = strip_tags($question[$i]->questiontext);
            }
            echo "<th title=\"$title\">$i</th>";
        }
        echo "</tr>";

        foreach ($data as $userid => $datum) {
            echo "<tr>";
            echo "<td><b>".fullname($datum)."</b></td>";
            if ($datum->grades) {
                foreach ($datum->grades as $key => $grade) {
                    if (isset($grade)) {
                        echo "<td>$grade</td>";
                    } else {
                        echo "<td>&nbsp;</td>";
                    }
                }
            }
            echo "</tr>";
        }

        echo "<tr>";
        echo "<td>&nbsp;</td>";
        for ($i=1; $i<=$count; $i++) {
            echo "<td>".$average[$i]."</td>";
        }
        echo "</tr>";

        echo "</table>";

        echo "<br />";
        echo "<table border=0 align=center><tr>";
        echo "<td>";
        unset($options);
        $options["id"] = "$cm->id";
        $options["mode"] = "simplestat";
        $options["noheader"] = "yes";
        $options["download"] = "xls";
        print_single_button("report.php", $options, get_string("downloadexcel"));
        echo "<td>";
        $options["download"] = "txt";
        print_single_button("report.php", $options, get_string("downloadtext"));
        echo "</table>";
    

        return true;
    }
}

?>
