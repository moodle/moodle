<?php  // $Id$

include_once('lib.php');

/// Library of function for module quiz

/// CONSTANTS ///////////////////////////////////////////////////////////////////

define("GRADEHIGHEST", "1");
define("GRADEAVERAGE", "2");
define("ATTEMPTFIRST", "3");
define("ATTEMPTLAST",  "4");
$QUIZ_GRADE_METHOD = array ( GRADEHIGHEST => get_string("gradehighest", "quiz"),
                             GRADEAVERAGE => get_string("gradeaverage", "quiz"),
                             ATTEMPTFIRST => get_string("attemptfirst", "quiz"),
                             ATTEMPTLAST  => get_string("attemptlast", "quiz"));

define("SHORTANSWER",   "1");
define("TRUEFALSE",     "2");
define("MULTICHOICE",   "3");
define("RANDOM",        "4");
define("MATCH",         "5");
define("RANDOMSAMATCH", "6");
define("DESCRIPTION",   "7");
define("NUMERICAL",     "8");
define("MULTIANSWER",   "9");
define("CALCULATED",   "10");

// The $QUIZ_QUESTION_TYPE array holds the names of all the question types that the user should
// be able to create directly. Some internal question types like random questions are excluded.
// The complete list of question types can be found in $QUIZ_QTYPES.
$QUIZ_QUESTION_TYPE = array ( MULTICHOICE   => get_string("multichoice", "quiz"),
                              TRUEFALSE     => get_string("truefalse", "quiz"),
                              SHORTANSWER   => get_string("shortanswer", "quiz"),
                              NUMERICAL     => get_string("numerical", "quiz"),
                              CALCULATED    => get_string("calculated", "quiz"),
                              MATCH         => get_string("match", "quiz"),
                              DESCRIPTION   => get_string("description", "quiz"),
                              RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
                              MULTIANSWER   => get_string("multianswer", "quiz")
                              );


define("QUIZ_PICTURE_MAX_HEIGHT", "600");   // Not currently implemented
define("QUIZ_PICTURE_MAX_WIDTH",  "600");   // Not currently implemented

define("QUIZ_MAX_NUMBER_ANSWERS", "10");

define("QUIZ_CATEGORIES_SORTORDER", "999");

define('QUIZ_REVIEW_AFTER', 1);
define('QUIZ_REVIEW_BEFORE', 2);

$QUIZ_QTYPES= array();

/// QUIZ_QTYPES INITIATION //////////////////
class quiz_default_questiontype {

    function name() {
        return 'default';
    }

    function uses_quizfile($question, $relativefilepath) {
        // The default does only check whether the file is used as image:
        return $question->image == $relativefilepath;
    }

    function save_question_options($question) {
    /// Given some question info and some data about the the answers
    /// this function parses, organises and saves the question
    /// It is used by question.php through ->save_question when
    /// saving new data from a form, and also by import.php when
    /// importing questions
    ///
    /// If this is an update, and old answers already exist, then
    /// these are overwritten using an update().  To do this, it
    /// it is assumed that the IDs in quiz_answers are in the same
    /// sort order as the new answers being saved.  This should always
    /// be true, but it's something to keep in mind if fiddling with
    /// question.php
    ///
    /// Returns $result->error or $result->noticeyesno or $result->notice

        /// This default implementation must be overridden:

        $result->error = "Unsupported question type ($question->qtype)!";
        return $result;
    }

    function save_question($question, $form, $course) {
        // This default implementation is suitable for most
        // question types.

        // First, save the basic question itself

        $question->name               = trim($form->name);
        $question->questiontext       = trim($form->questiontext);
        $question->questiontextformat = $form->questiontextformat;

        if (empty($form->image)) {
            $question->image = "";
        } else {
            $question->image = $form->image;
        }

        if (empty($question->name)) {
            $question->name = strip_tags($question->questiontext);
            if (empty($question->name)) {
                $question->name = '-';
            }
        }

        if (isset($form->defaultgrade)) {
            $question->defaultgrade = $form->defaultgrade;
        }

        if (!empty($question->id)) { // Question already exists
            $question->version ++;    // Update version number of question
            if (!update_record("quiz_questions", $question)) {
                error("Could not update question!");
            }
        } else {         // Question is a new one
            $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
            $question->version = 1;
            if (!$question->id = insert_record("quiz_questions", $question)) {
                error("Could not insert new question!");
            }
        }

        // Now to save all the answers and type-specific options

        $form->id       = $question->id;
        $form->qtype    = $question->qtype;
        $form->category = $question->category;

        $result = $this->save_question_options($form);

        if (!empty($result->error)) {
            error($result->error);
        }

        if (!empty($result->notice)) {
            notice($result->notice, "question.php?id=$question->id");
        }

        if (!empty($result->noticeyesno)) {
            notice_yesno($result->noticeyesno, "question.php?id=$question->id", "edit.php");
            print_footer($course);
            exit;
        }

        redirect("edit.php");
    }

    /// Convenience function that is used within the question types only
    function extract_response_id($responsekey) {
        if (ereg('[0-9]'.$this->name().'([0-9]+)', $responsekey, $regs)) {
            return $regs[1];
        } else {
            return false;
        }
    }


    function wrapped_questions($question) {
    /// Overridden only by question types whose questions can
    /// wrap other questions. Two question types that do this
    /// are RANDOMSAMATCH and RANDOM

    /// If there are wrapped questions, then this method returns
    /// comma separated list of them...

        return false;
    }

    function convert_to_response_answer_field($questionresponse) {
    /// This function is very much the inverse of extract_response
    /// This function and extract_response, should be
    /// obsolete as soon as we get a better response storage
    /// Right now they are a bridge between a consistent
    /// response model and the old field answer in quiz_responses

    /// This is the default implemention...
        return implode(',', $questionresponse);
    }

    function get_answers($question) {
        // Returns the answers for the specified question

        // The default behaviour that signals that something is wrong
        return false;
    }

    function create_response($question, $nameprefix, $questionsinuse) {
        /// This rather smart solution works for most cases:
        $rawresponse->question = $question->id;
        $rawresponse->answer = '';
        return $this->extract_response($rawresponse, $nameprefix);
    }

    function extract_response($rawresponse, $nameprefix) {
    /// This function is very much the inverse of convert_to_response_answer_field
    /// This function and convert_to_response_answer_field, should be
    /// obsolete as soon as we get a better response storage
    /// Right now they are a bridge between a consistent
    /// response model and the old field answer in quiz_responses

        /// Default behaviour that works for singlton response question types
        /// like SHORTANSWER, NUMERICAL and TRUEFALSE

        return array($nameprefix => $rawresponse->answer);
    }

    function print_question_number_and_grading_details
            ($number, $grade, $actualgrade=false, $recentlyadded=false, $questionid=0, $courseid=0) {

        /// Print question number and grade:

        global $CFG;

        static $streditquestions, $strmarks, $strrecentlyaddedquestion;

        if (!isset($streditquestions)) {
            $streditquestions         = get_string('editquestions', 'quiz');
            $strmarks                 = get_string('marks', 'quiz');
            $strrecentlyaddedquestion = get_string('recentlyaddedquestion', 'quiz');
        }

        echo '<center><b>' . $number . '</b>';
        if ($questionid and isteacher($courseid)) {
            echo '<br /><font size="1">( ';
            link_to_popup_window ($CFG->wwwroot.'//mod/quiz/question.php?id='.$questionid,
                                  'editquestion', '#'.$questionid, 450, 550, $streditquestions);
            echo ')</font>';
        }
        echo '</center>';

        if (false !== $grade) {
            //echo '<p align="center"><font size="1">';
            echo '<br /><center><font size="1">';
            if (false !== $actualgrade) {
                echo "$strmarks: $actualgrade/$grade</font></center>";
            } else {
                echo "$grade $strmarks</font></center>";
            }
        }
        print_spacer(1,100);

        /// Print possible recently-added information:

        if ($recentlyadded) {
            echo '</td><td valign="top" align="right">';
            // Notify the user of this recently added question
            echo '<font color="red">'.$strrecentlyaddedquestion.'</font>';
            echo '</td></tr><tr><td></td><td valign="top">';

        } else { // The normal case
            echo '</td><td valign="top">';
        }
    }

    function print_question($currentnumber, $quiz, $question,
                            $readonly, $resultdetails) {
        /// Note that this method must return the number of the next
        /// question, making it possible not to increase the number when
        /// overriding this method (as for qtype=DESCRIPTION).

        echo '<table width="100%" cellspacing="10">';
        echo '<tr><td nowrap="nowrap" width="100" valign="top">';

        $this->print_question_number_and_grading_details
                ($currentnumber,
                 $quiz->grade ? $question->maxgrade : false,
                 empty($resultdetails) ? false : $resultdetails->grade,
                 isset($question->recentlyadded) ? $question->recentlyadded : false,
                 $question->id, $quiz->course);

        $this->print_question_formulation_and_controls(
                $question, $quiz, $readonly,
                empty($resultdetails) ? false : $resultdetails->answers,
                empty($resultdetails) ? false : $resultdetails->correctanswers,
                quiz_qtype_nameprefix($question));

        echo "</td></tr></table>";
        return $currentnumber + 1;
    }

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {
        /// This default implementation must be overridden by all
        /// question type implemenations, unless the default
        /// implementation of print_question has been overridden...

        notify('Error: Question formulation and input controls has not'
               .'  been implemented for question type '.$this->name());
    }

    function actual_number_of_questions($question) {
        /// Used for the feature number-of-questions-per-page
        /// to determine the actual number of questions wrapped
        /// by this question. The default is ONE!
        return 1;
    }

    function grade_response($question, $nameprefix) {
    // Analyzes $question->response[] and determines the result
    // The result is to be returned in this structure:
    // ->grade          (The fraction of maxgrade awarded on the question)
    // ->answers        (result answer records)
    // ->correctanswers (potential answer records for best ->response[])

        error('grade_response has not been implemented for question type '
                .$this->name());
    }

    function get_config_options() {
    // Returns an array of objects describing the options for the question type
    // to be included on the quiz module admin page
    //
    // Configuration options can be included by setting the following fields in
    // the object:
    // ->name           (The name of the option within this question type
    //                   - the full option name will be constructed as
    //                   "quiz_{$this->name()}_$name", the human readable name
    //                   will be displayed with get_string($name, 'quiz'))
    // ->code           (The code to display the form element, help button, etc.
    //                   i.e. the content for the central table cell. Be sure
    //                   to name the element "quiz_{$this->name()}_$name" and
    //                   set the value to $CFG->{"quiz_{$this->name()}_$name"})
    // ->help           (Name of the string from the quiz module language file
    //                   to be used for the help message in the third column of
    //                   the table. An empty string (or the field not set)
    //                   means to leave the box empty)
    //
    // Links to custom settings pages can be included by setting the following
    // fields in the object:
    // ->name           (The name of the link text string -
    //                   get_string($name, 'quiz') will be called)
    // ->link           (The filename part of the URL for the link
    //                   - the full URL is contructed as
    //                   "$CFG->wwwroot/mod/quiz/questiontypes/{$this->name()}/$link?sesskey=$sesskey"
    //                   [but with the relavant calls to the s and rawurlencode
    //                   functions] where $sesskey is the sesskey for the user)

        // No options by default
        return false;
    }
}

quiz_load_questiontypes();
function quiz_load_questiontypes() {
    global $QUIZ_QTYPES;
    global $CFG;

    $qtypenames= get_list_of_plugins('mod/quiz/questiontypes');
    foreach($qtypenames as $qtypename) {
        // Instanciates all plug-in question types
        $qtypefilepath= "$CFG->dirroot/mod/quiz/questiontypes/$qtypename/questiontype.php";

        // echo "Loading $qtypename<br/>"; // Uncomment for debugging
        if (is_readable($qtypefilepath)) {
            require_once($qtypefilepath);
        }
    }
}



/// SQL FUNCTIONS ////////////////////////////////////////////////////////////////////

function quiz_move_questions($category1, $category2) {
    global $CFG;
    return execute_sql("UPDATE {$CFG->prefix}quiz_questions
                           SET category = '$category2'
                         WHERE category = '$category1'",
                       false);
}

function quiz_get_question_grades($quizid, $questionlist) {
    global $CFG;

    return get_records_sql("SELECT question,grade
                            FROM {$CFG->prefix}quiz_question_grades
                            WHERE quiz = '$quizid'
                            AND question IN ($questionlist)");
}

function quiz_questiongrades_update($grades, $quizid) {
    // this is called from edit.php to store changes to the question grades
    // in the quiz_question_grades table. It does not update 'sumgrades' in the quiz table.
    $existing = get_records("quiz_question_grades", "quiz", $quizid, "", "question,grade,id");
    foreach ($grades as $question => $grade) {
        unset($questiongrade);
        $questiongrade->quiz = $quizid;
        $questiongrade->question = $question;
        $questiongrade->grade = $grade;
        if (isset($existing[$question])) {
            if ($existing[$question]->grade != $grade) {
                $questiongrade->id = $existing[$question]->id;
                if (!update_record("quiz_question_grades", $questiongrade)) {
                    return false;
                }
            }
        } else {
            if (!insert_record("quiz_question_grades", $questiongrade)) {
                return false;
            }
        }
    }
}

function quiz_get_grade_records($quiz) {
/// Gets all info required to display the table of quiz results
/// for report.php
    global $CFG;

    return get_records_sql("SELECT qg.*, u.firstname, u.lastname, u.picture
                            FROM {$CFG->prefix}quiz_grades qg,
                                 {$CFG->prefix}user u
                            WHERE qg.quiz = '$quiz->id'
                              AND qg.userid = u.id");
}

function quiz_get_answers($question) {
// Given a question, returns the correct answers for a given question
    global $QUIZ_QTYPES;

    return $QUIZ_QTYPES[$question->qtype]->get_answers($question);
}

function quiz_get_attempt_questions($quiz, $attempt, $attempting = false) {
    /// Returns the questions of the quiz attempt in a format used for
    /// grading and printing them...
    ///
    /// $attempting should be set to true if this function is called in
    ///   order to create an attempt page and false if it is called to create
    ///   a review page.
    ///
    /// On top of the ordinary persistent question fields,
    /// this function also set these properties:
    //
    /// ->response   -   contains names (as keys) and values (as values)
    ///                            for all question html-form inputs
    /// ->recentlyadded - true only if the question has been added to the quiz
    ///                   after the responses for the attempt were saved;
    ///                   false otherwise
    /// ->maxgrade   - the max grade the question has on the quiz if grades
    ///                 are used on the quiz; false otherwise

    global $QUIZ_QTYPES;
    global $CFG;

    /////////////////////////
    /// Get the questions:
    /////////////////////////
    if (!($questions =
            get_records_list('quiz_questions', 'id', $quiz->questions))) {
        notify('Error when reading questions from the database!');
        return false;
    }

    ////////////////////////////////////////////
    /// Determine ->maxgrade for all questions
    ////////////////////////////////////////////
    If (!($grades = quiz_get_question_grades($quiz->id, $quiz->questions))) {
        $grades = array();
    }
    foreach ($questions as $qid => $question) {
        if (isset($grades[$qid])) {
            $questions[$qid]->maxgrade = $grades[$qid]->grade;
        } else {
            $questions[$qid]->maxgrade = 0.0;
        }
    }

    //////////////////////////////////////////////////////////////
    /// Determine attributes ->response and ->recentlyadded (hard)
    //////////////////////////////////////////////////////////////

    /// Get all existing responses on this attempt
    $rawresponses = get_records_sql("
            SELECT question, answer, attempt
            FROM {$CFG->prefix}quiz_responses
            WHERE attempt = '$attempt->id' ");

    /// The setting for ->recentlyadded depends on whether this is
    /// a test attempt or just a review
    if ($attempting) {
        /// This is a test attempt so there is a need to create responses
        /// in case there are none existing.
        /// Further - the attribute recentlyadded is determined from
        /// whether the question has a response in the previous attempt,
        /// which might be used in case the attemptonlast quiz option
        /// is true.

        $prevattempt = $attempt->attempt;
        $prevresponses= array();
        while (--$prevattempt) {
            $prevresponses = get_records_sql("
                    SELECT r.question, r.answer, r.attempt, r.grade
                    FROM {$CFG->prefix}quiz_responses r, {$CFG->prefix}quiz_attempts a
                    WHERE a.quiz='$quiz->id' AND a.userid='$attempt->userid'
                      AND a.attempt='$prevattempt' AND r.attempt=a.id ");
            if (!empty($prevresponses)) {
                break;
            }
        }

        $questionsinuse = $quiz->questions; // used if responses must be created
        foreach ($questions as $qid => $question) {
            if ($questions[$qid]->recentlyadded =
                    $prevattempt && empty($prevresponses[$qid])) {
                /* No action */

            } else if ($prevattempt && $quiz->attemptonlast
                    && empty($rawresponses[$qid])) {
                /// Store the previous response on this attempt!
                $rawresponses[$qid] = $prevresponses[$qid];
                $rawresponses[$qid]->attempt = $attempt->id;
                $rawresponses[$qid]->id =
                        insert_record("quiz_responses", $rawresponses[$qid])
                or error("Unable to create attemptonlast response for question $qid");
            }

            /* Extract possible response and its wrapped questions */
            if (!empty($rawresponses[$qid])) {
                $questions[$qid]->response = $QUIZ_QTYPES[$question->qtype]
                        ->extract_response($rawresponses[$qid],
                                           quiz_qtype_nameprefix($question));
                /// Catch any additional wrapped questions:
                if ($wrapped = $QUIZ_QTYPES[$question->qtype]
                        ->wrapped_questions($questions[$question->id],
                                            quiz_qtype_nameprefix($question))) {
                    $questionsinuse .= ",$wrapped";
                }
            }
        }

        /// Make sure all the questions will have responses:
        foreach ($questions as $question) {
            if (empty($question->response)) {
                /// No response on this question

                $nameprefix = quiz_qtype_nameprefix($question);
                $questions[$question->id]->response =
                        $QUIZ_QTYPES[$question->qtype]->create_response
                        ($question, $nameprefix, $questionsinuse);

                //////////////////////////////////////////////
                // Saving the newly created response before
                // continuing with the quiz...
                //////////////////////////////////////////////
                $responserecord->attempt = $attempt->id;
                $responserecord->question = $question->id;
                $responserecord->answer = $QUIZ_QTYPES[$question->qtype]
                        ->convert_to_response_answer_field
                        ($questions[$question->id]->response);

                insert_record("quiz_responses", $responserecord)
                or error("Unable to create initial response for question $question->id");

                /// Catch any additional wrapped questions:
                if ($wrapped = $QUIZ_QTYPES[$question->qtype]
                        ->wrapped_questions($questions[$question->id],
                                            quiz_qtype_nameprefix($question))) {
                    $questionsinuse .= ",$wrapped";
                }
            }
        }

    } else {
        /// In the case of review, the recentlyadded flag is set true
        /// when the question has been added after the attempt and new
        /// responses are never created

        foreach ($questions as $qid => $question) {
            if ($questions[$qid]->recentlyadded = empty($rawresponses[$qid])) {
                /* No action */
            } else {
                $questions[$qid]->response = $QUIZ_QTYPES[$question->qtype]
                        ->extract_response($rawresponses[$qid],
                                           quiz_qtype_nameprefix($question));
            }
        }
    }

    return $questions;
}


function get_list_of_questions($questionlist) {
/// Returns an ordered list of questions, including course for each

    global $CFG;

    return get_records_sql("SELECT q.*,c.course
                              FROM {$CFG->prefix}quiz_questions q,
                                   {$CFG->prefix}quiz_categories c
                             WHERE q.id in ($questionlist)
                               AND q.category = c.id");
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other quiz functions go here.  Each of them must have a name that
/// starts with quiz_

function quiz_qtype_nameprefix($question, $prefixstart='question') {
    global $QUIZ_QTYPES;
    return $prefixstart.$question->id.$QUIZ_QTYPES[$question->qtype]->name();
}
function quiz_extract_posted_id($name, $nameprefix='question') {
    if (ereg("^$nameprefix([0-9]+)", $name, $regs)) {
        return $regs[1];
    } else {
        return false;
    }
}

function quiz_print_comment($text) {
    echo "<span class=\"feedbacktext\">".format_text($text, true, false)."</span>";
}

function quiz_print_correctanswer($text) {
    echo "<p align=\"right\"><span class=\"highlight\">$text</span></p>";
}

function quiz_print_question_icon($question, $editlink=true) {
// Prints a question icon

    global $QUIZ_QUESTION_TYPE;
    global $QUIZ_QTYPES;

    if ($editlink) {
        echo "<a href=\"question.php?id=$question->id\" title=\""
                .$QUIZ_QTYPES[$question->qtype]->name()."\">";
    }
    echo '<img border="0" height="16" width="16" src="questiontypes/';
    echo $QUIZ_QTYPES[$question->qtype]->name().'/icon.gif" alt="';
    echo get_string($QUIZ_QTYPES[$question->qtype]->name(), 'quiz').'" />';
    if ($editlink) {
        echo "</a>\n";
    }
}

function quiz_print_possible_question_image($quizid, $question) {
// Includes the question image if there is one

    global $CFG;

    if ($quizid == '') {
        $quizid = '0';
    }

    if ($question->image) {
        echo '<img border="0" src="';

        if (substr(strtolower($question->image), 0, 7) == 'http://') {
            echo $question->image;

        } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/mod/quiz/quizfile.php/$quizid/$question->id/$question->image";

        } else {
            echo "$CFG->wwwroot/mod/quiz/quizfile.php?file=/$quizid/$question->id/$question->image";
        }
        echo '" alt="" />';

    }
}

function quiz_navigation_javascript($link) {
    return "javascript:navigate($link);";
}

function quiz_print_navigation_panel($questions, $questionsperpage, $navigation) {
    global $QUIZ_QTYPES;

    $numberinglayout = array();
    $nextqnumber = 1;
    foreach ($questions as $question) {
        if ($qnumberinc = $QUIZ_QTYPES[$question->qtype]
                ->actual_number_of_questions($question)) {
            $numberinglayout[] = $nextqnumber;
            $nextqnumber += $qnumberinc;
        }
    }

    if ($nextqnumber - $qnumberinc <= $questionsperpage) {
        /// The total number of questions does not exceed the maximum
        /// number of allowed questions per page so...
        return 0;
    }
    /// else - Navigation menu will be printed!

    ///////////////////////////////////////////////
    /// Determine the layout of the navigation menu
    ///////////////////////////////////////////////
    if (1 == $questionsperpage) {
        /// The simple case:
        $pagelinkagelayout = $pagenavigationlayout = $numberinglayout;

    } else {
        /// More complicated:
        $pagenavigationlayout = array();
        $pagelinkagelayout = array($currentpagestart = 1);
        foreach ($numberinglayout as $questionnumber) {
            if ($questionnumber - $currentpagestart >= $questionsperpage) {
                $pagenavigationlayout[] = $currentpagestart
                        .'-'. ($questionnumber - 1);
                if ($currentpagestart < $navigation
                        && $navigation < $questionnumber) {
                    // $navigation is out of sync so adjust for robustness
                    $navigation = $currentpagestart;
                }
                $pagelinkagelayout[] = $currentpagestart = $questionnumber;
            }
        }
        $pagenavigationlayout[] = $currentpagestart .'-'. ($nextqnumber - 1);
        if ($currentpagestart < $navigation) {
            // $firsquestion is out of sync so adjust it for robustness...
            $navigation = $currentpagestart;
        }
    }

    foreach ($pagelinkagelayout as $key => $link) {
        if ($link < $navigation) {
            $previouspagelink = $link;
        } else if ($link == $navigation) {
            $currentnavigationtitle = $pagenavigationlayout[$key];
        } else {
            $endpagelink = $link;
            if (false == isset($nextpagelink)) {
               $nextpagelink = $link;
            }
        }
    }

    ///////////////////////////////////////////////
    /// Print the navigation meny
    ///////////////////////////////////////////////
    print_simple_box_start('center', '*');
    echo '<table><tr><td colspan="5" align="center"><table><tr>';
    foreach ($pagelinkagelayout as $key => $link) {
        echo '<td align="center">&nbsp;';
        if ($link != $navigation) {
            echo '<a href="' . quiz_navigation_javascript($link) . '">';
        }
        echo $pagenavigationlayout[$key];
        if ($link != $navigation) {
            echo '</a>';
        }
        echo '&nbsp;</td>';
    }
    echo '</tr></table></td></tr><tr><td width="20%" align="left">';
    if (isset($previouspagelink)) {
        echo '<a href="' . quiz_navigation_javascript('1') . '">|&lt;&lt;&lt;</a></td><td width="20%" align="center" cellpadding="2">';
        echo '<a href="' . quiz_navigation_javascript($previouspagelink) . '">&lt;&lt;&lt;</a></td>';
    } else {
        echo '</td><td width="20%"></td>';
    }
    echo '<td width="20%" align="center"><b>';
    echo $currentnavigationtitle;
    echo '</b></td><td width="20%" align="center" cellpadding="2">';
    if (isset($nextpagelink)) {
        echo '<a href="';
        echo quiz_navigation_javascript($nextpagelink);
        echo '">&gt;&gt;&gt;</a></td><td width="20%" align="right"><a href="';
        echo quiz_navigation_javascript($endpagelink);
        echo '">&gt;&gt;&gt;|</a>';
    } else {
        echo '</td><td width="20%">';
    }
    echo '</td></tr></table>';
    print_simple_box_end();

    ////////////////////////////////////////////////
    /// Return the potentially adjusted $navigation
    ////////////////////////////////////////////////
    return $navigation;
}

function quiz_print_quiz_questions($quiz, $questions, $results=NULL,
                                   $shuffleorder=NULL, $navigation=0) {
// Prints a whole quiz on one page.

    if ($navigation < 0) {
        $navigation = 0; // For robustness
    }

    global $QUIZ_QTYPES;

    /// Check arguments

    if (empty($questions)) {
        notify("No questions have been defined!");
        return false;
    }

    if (!$shuffleorder) {
        if (!empty($quiz->shufflequestions)) {              // Mix everything up
            $questions = swapshuffle_assoc($questions);
        } else {
            $shuffleorder = explode(",", $quiz->questions);  // Use originally defined order
        }
    }

    if ($shuffleorder) { // Order has been defined, so reorder questions
        $oldquestions = $questions;
        $questions = array();
        foreach ($shuffleorder as $key) {
            $questions[] = $oldquestions[$key];      // This loses the index key, but doesn't matter
        }
    }

    $strconfirmattempt = addslashes(get_string("readytosend", "quiz"));

    if (empty($quiz->grade)) {
        $onsubmit = "";
    } else {
        $onsubmit = "onsubmit=\"return confirm('$strconfirmattempt');\"";
    }
    // BEGIN EDIT
    if($quiz->timelimit > 0) {
        ?>
        <script language="javascript" type="text/javascript">
        <!--
            document.write("<form name=\"responseform\" method=\"post\" action=\"attempt.php\" <?php print(addslashes($onsubmit));?>>\n");
        // -->
        </script>
        <noscript>
        <center><p><strong><?php print_string("noscript","quiz"); ?></strong></p></center>
        </noscript>
        <?php
    } else {
        echo "<form name=\"responseform\" method=\"post\" action=\"attempt.php\" $onsubmit>\n";
    }
    // END EDIT
    echo "<input type=\"hidden\" name=\"q\" value=\"$quiz->id\" />\n";

    if ($navigation && $quiz->questionsperpage) {
        echo '<input type="hidden" id="navigation" name="navigation" value="0" />';
        $navigation = quiz_print_navigation_panel($questions,
                $quiz->questionsperpage, $navigation);
    } else {
        $navigation = 0;
    }

    $nextquestionnumber = 1;
    $questionorder = array();

    // $readonly determines if it is an attempt or an review,
    // The condition used here is unfortunatelly somewhat confusing...
    $readonly = !empty($results) && !isset($results->attemptbuildsonthelast)
            ? ' disabled="disabled" ' : '';

    foreach ($questions as $question) {

        if (empty($question->qtype)) {    // Just for robustness
            continue;
        }

        $questionorder[] = $question->id;

        if (0 == $navigation
                || $navigation <= $nextquestionnumber
                && $nextquestionnumber - $navigation < $quiz->questionsperpage) {
            if ($results && isset($results->details[$question->id])) {
                $details = $results->details[$question->id];
            } else {
                $details = false;
            }

            echo "<br />";
            print_simple_box_start("center", "90%");
            $nextquestionnumber = $QUIZ_QTYPES[$question->qtype]->print_question
                    ($nextquestionnumber, $quiz, $question, $readonly, $details);
            print_simple_box_end();
        } else {
            $nextquestionnumber += $QUIZ_QTYPES[$question->qtype]
                    ->actual_number_of_questions($question);
        }
    }

    if ($navigation) {
        quiz_print_navigation_panel($questions, $quiz->questionsperpage,
                                    $navigation);
    }
    echo "<br />";

    if (empty($readonly)) {
        if (!empty($quiz->shufflequestions)) {  // Things have been mixed up, so pass the question order
            $shuffleorder = implode(',', $questionorder);
            echo "<input type=\"hidden\" name=\"shuffleorder\" value=\"$shuffleorder\" />\n";
        }
        if($quiz->timelimit > 0) {
            echo "<script language=\"javascript\" type=\"text/javascript\">\n";
            echo "<!--\n";
            echo "document.write('<center><input type=\"button\" value=\"".get_string("savemyanswers", "quiz")."\" onclick=\"return send_data();\" /></center>');\n";
            echo "// -->\n";
            echo "</script>\n";
            echo "<noscript>\n";
            echo "<center><strong>".get_string("noscript","quiz")."</strong></center>\n";
            echo "</noscript>\n";
        } else {
            echo "<center>\n<input type=\"submit\" value=\"".get_string("savemyanswers", "quiz")."\" />\n</center>";
        }
    }
    echo "</form>";

    if ($navigation && $quiz->questionsperpage) {
        echo '<script language="javascript" type="text/javascript">';
        echo "function navigate(link) {
                document.responseform.navigation.value=link;
                document.responseform.submit();
              }
              </script>";
    }

    return true;
}



function quiz_get_default_category($courseid) {
/// Returns the current category

    if ($categories = get_records_select("quiz_categories", "course = '$courseid' AND parent = '0'", "id")) {
        foreach ($categories as $category) {
            return $category;   // Return the first one (lowest id)
        }
    }

    // Otherwise, we need to make one
    $category->name = get_string("default", "quiz");
    $category->info = get_string("defaultinfo", "quiz");
    $category->course = $courseid;
    $category->parent = 0;
    $category->sortorder = QUIZ_CATEGORIES_SORTORDER;
    $category->publish = 0;
    $category->stamp = make_unique_id_code();

    if (!$category->id = insert_record("quiz_categories", $category)) {
        notify("Error creating a default category!");
        return false;
    }
    return $category;
}

function quiz_get_category_menu($courseid, $published=false) {
/// Returns the list of categories
    $publish = "";
    if ($published) {
        $publish = "OR publish = '1'";
    }

    if (!isadmin()) {
        $categories = get_records_select("quiz_categories", "course = '$courseid' $publish", 'parent, sortorder, name ASC');
    } else {
        $categories = get_records_select("quiz_categories", '', 'parent, sortorder, name ASC');
    }
    if (!$categories) {
        return false;
    }
    $categories = add_indented_names($categories);

    foreach ($categories as $category) {
       if ($catcourse = get_record("course", "id", $category->course)) {
           if ($category->publish && ($category->course != $courseid)) {
               $category->indentedname .= " ($catcourse->shortname)";
           }
           $catmenu[$category->id] = $category->indentedname;
       }
    }
    return $catmenu;
}

function quiz_print_category_form($course, $current, $recurse=1) {
/// Prints a form to choose categories

/// Make sure the default category exists for this course
    if (!$categories = get_records("quiz_categories", "course", $course->id, "id ASC")) {
        if (!$category = quiz_get_default_category($course->id)) {
            notify("Error creating a default category!");
        }
    }

/// Get all the existing categories now
    if (!$categories = get_records_select("quiz_categories", "course = '{$course->id}' OR publish = '1'", "parent, sortorder, name ASC")) {
        notify("Could not find any question categories!");
        return false;    // Something is really wrong
    }
    $categories = add_indented_names($categories);
    foreach ($categories as $key => $category) {
       if ($catcourse = get_record("course", "id", $category->course)) {
           if ($category->publish && $category->course != $course->id) {
               $category->indentedname .= " ($catcourse->shortname)";
           }
           $catmenu[$category->id] = $category->indentedname;
       }
    }
    $strcategory = get_string("category", "quiz");
    $strshow = get_string("show", "quiz");
    $streditcats = get_string("editcategories", "quiz");

    echo "<table width=\"100%\"><tr><td width=\"20\" nowrap=\"nowrap\">";
    echo "<b>$strcategory:</b>&nbsp;";
    echo "</td><td>";
    popup_form ("edit.php?cat=", $catmenu, "catmenu", $current, "", "", "", false, "self");
    echo "</td><td align=\"right\">";
    echo "<form method=\"get\" action=\"category.php\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
    echo "<input type=\"submit\" value=\"$streditcats\" />";
    echo "</form>";
    echo '</td></tr></table>';
    echo '<form method="get" action="edit.php" name="recurse">';
    print_string('recurse', 'quiz');
    echo '<input type="hidden" name="recurse" value="0">';
    echo '<input type="checkbox" name="recurse" value="1"';
    if ($recurse) {
        echo ' checked="checked"';
    }
    echo ' onclick="document.recurse.submit(); return true;">';
    echo '</form>';
}


function add_indented_names(&$categories, $id = 0, $indent = 0) {
// returns the categories with their names indented to show parent-child relationships
    $fillstr = '&nbsp;&nbsp;&nbsp;';
    $fill = str_repeat($fillstr, $indent);
    $children = array();
    $keys = array_keys($categories);

    foreach ($keys as $key) {
        if (!isset($categories[$key]->processed) && $categories[$key]->parent == $id) {
            $children[$key] = $categories[$key];
            $children[$key]->indentedname = $fill . $children[$key]->name;
            $categories[$key]->processed = true;
            $children = $children + add_indented_names($categories, $children[$key]->id, $indent + 1);
        }
    }
    return $children;
}


function quiz_category_select_menu($courseid,$published=false,$only_editable=false,$selected="") {
/// displays a select menu of categories with appended coursenames
/// optionaly non editable categories may be excluded
/// added Howard Miller June '04
    // get sql fragment for published
    $publishsql="";
    if ($published) {
        $publishsql = "or publish=1";
    }

    if (!isadmin()) {
        $categories = get_records_select("quiz_categories","course=$courseid $publishsql", 'parent, sortorder, name ASC');
    } else {
        $categories = get_records_select("quiz_categories", '', 'parent, sortorder, name ASC');
    }

    $categories = add_indented_names($categories);

    echo "<select name=\"category\">\n";
    foreach ($categories as $category) {
        $cid = $category->id;
        $cname = quiz_get_category_coursename($category, $courseid);
        $seltxt = "";
        if ($cid==$selected) {
            $seltxt = "selected=\"selected\"";
        }
        if ((!$only_editable) || isteacheredit($category->course)) {
            echo "    <option value=\"$cid\" $seltxt>$cname</option>\n";
        }
    }
    echo "</select>\n";
}

function quiz_get_category_coursename($category, $courseid = 0) {
/// if the category is not from this course and is published , adds on the course
/// name
    $cname = (isset($category->indentedname)) ? $category->indentedname : $category->name;
    if ($category->course != $courseid && $category->publish) {
        if ($catcourse=get_record("course","id",$category->course)) {
            $cname .= " ($catcourse->shortname) ";
        }
    }
    return $cname;
}

function quiz_get_all_question_grades($questionlist, $quizid) {
// Given a list of question IDs, finds grades or invents them to
// create an array of matching grades

    if (empty($questionlist)) {
        return array();
    }

    $questions = quiz_get_question_grades($quizid, $questionlist);

    $list = explode(",", $questionlist);
    $grades = array();

    foreach ($list as $qid) {
        if (isset($questions[$qid])) {
            $grades[$qid] = $questions[$qid]->grade;
        } else {
            $grades[$qid] = 1;
        }
    }
    return $grades;
}

function quiz_gradesmenu_options($defaultgrade) {
// Especially for multianswer questions it is often
// desirable to have the grade of the question in a quiz
// larger than the earlier maximum of 10 points.
// This function makes quiz question list grade selector drop-down
// have the maximum grade option set to the highest value between 10
// and the defaultgrade of the question.

    if ($defaultgrade && $defaultgrade>10) {
        $maxgrade = $defaultgrade;
    } else {
        $maxgrade = 10;
    }

    unset($gradesmenu);
    for ($i=$maxgrade ; $i>=0 ; --$i) {
        $gradesmenu[$i] = $i;
    }
    return $gradesmenu;
}

function quiz_print_question_list($questionlist, $grades, $allowdelete=true) {
// Prints a list of quiz questions in a small layout form with knobs
// returns sum of maximum grades
// $questionlist is comma-separated list
// $grades is an array of corresponding grades

    global $USER;

    if (!$questionlist) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    $order = explode(",", $questionlist);

    if (!$questions = get_list_of_questions($questionlist)) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;

    }

    $strorder = get_string("order");
    $strquestionname = get_string("questionname", "quiz");
    $strgrade = get_string("grade");
    $strremove = get_string('remove', 'quiz');
    $stredit = get_string("edit");
    $strmoveup = get_string("moveup");
    $strmovedown = get_string("movedown");
    $strsavegrades = get_string("savegrades", "quiz");
    $strtype = get_string("type", "quiz");
    $strpreview = get_string("preview", "quiz");

    $count = 0;
    $sumgrade = 0;
    $total = count($order);
    echo "<form method=\"post\" action=\"edit.php\">";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\">";
    echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"100%\">\n";
    echo "<tr><th width=\"*\" colspan=\"3\" nowrap=\"nowrap\">$strorder</th><th align=\"left\" width=\"100%\" nowrap=\"nowrap\">$strquestionname</th><th width=\"*\" nowrap=\"nowrap\">$strtype</th><th width=\"*\" nowrap=\"nowrap\">$strgrade</th><th align=\"center\" width=\"60\" nowrap=\"nowrap\">$stredit</th></tr>\n";
    foreach ($order as $qnum) {
        if (empty($questions[$qnum])) {
            continue;
        }
        $question = $questions[$qnum];
        $canedit = isteacheredit($question->course);
        $count++;
        echo "<tr>";
        echo "<td>$count</td>";
        echo "<td>";
        if ($count != 1) {
            echo "<a title=\"$strmoveup\" href=\"edit.php?up=$qnum&amp;sesskey=$USER->sesskey\"><img
                 src=\"../../pix/t/up.gif\" border=\"0\" alt=\"$strmoveup\" /></a>";
        }
        echo "</td>";
        echo "<td>";
        if ($count != $total) {
            echo "<a title=\"$strmovedown\" href=\"edit.php?down=$qnum&amp;sesskey=$USER->sesskey\"><img
                 src=\"../../pix/t/down.gif\" border=\"0\" alt=\"$strmovedown\" /></a>";
        }
        echo "</td>";
        echo "<td>$question->name</td>";
        echo "<td align=\"center\">";
        quiz_print_question_icon($question, $canedit);
        echo "</td>";
        echo "<td>";
        if ($question->qtype == DESCRIPTION) {
            echo "<input type=\"hidden\" name=\"q$qnum\" value=\"0\" /> \n";
        } else {
            choose_from_menu(quiz_gradesmenu_options($question->defaultgrade),
                             "q$qnum", (string)$grades[$qnum], "");
        }
        echo '<td align="center">';

        if ($canedit) {
            echo "<a title=\"$strpreview\" href=\"javascript:void();\" onClick=\"openpopup('/mod/quiz/preview.php?id=$qnum','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\">
                  <img src=\"../../pix/t/preview.gif\" border=\"0\" alt=\"$strpreview\" /></a>&nbsp;";
            echo "<a title=\"$stredit\" href=\"question.php?id=$qnum\">
                  <img src=\"../../pix/t/edit.gif\" border=\"0\" alt=\"$stredit\" /></a>&nbsp;";
            if ($allowdelete) {
                echo "<a title=\"$strremove\" href=\"edit.php?delete=$qnum&amp;sesskey=$USER->sesskey\">
                      <img src=\"../../pix/t/removeright.gif\" border=\"0\" alt=\"$strremove\" /></a>";
            }
        }
        echo "</td>";

        $sumgrade += $grades[$qnum];
    }
    echo "<tr><td colspan=\"5\" align=\"right\">\n";
    echo "<input type=\"submit\" value=\"$strsavegrades:\" />\n";
    echo "<input type=\"hidden\" name=\"setgrades\" value=\"save\" />\n";
    echo "<td align=\"left\">\n";
    echo "<b>$sumgrade</b>";
    echo "</td><td>\n</td></tr>\n";
    echo "</table>\n";
    echo "</form>\n";

    return $sumgrade;
}


function quiz_print_cat_question_list($categoryid, $quizselected=true, $recurse=1, $page, $perpage) {
// Prints the table of questions in a category with interactions

    global $QUIZ_QUESTION_TYPE, $USER;

    $strcategory = get_string("category", "quiz");
    $strquestion = get_string("question", "quiz");
    $straddquestions = get_string("addquestions", "quiz");
    $strimportquestions = get_string("importquestions", "quiz");
    $strexportquestions = get_string("exportquestions", "quiz");
    $strnoquestions = get_string("noquestions", "quiz");
    $strselect = get_string("select", "quiz");
    $strselectall = get_string("selectall", "quiz");
    $strcreatenewquestion = get_string("createnewquestion", "quiz");
    $strquestionname = get_string("questionname", "quiz");
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $strcopy = get_string("copy");
    $straddselectedtoquiz = get_string("addselectedtoquiz", "quiz");
    $strtype = get_string("type", "quiz");
    $strcreatemultiple = get_string("createmultiple", "quiz");
    $strpreview = get_string("preview","quiz");

    if (!$categoryid) {
        echo "<p align=\"center\"><b>";
        print_string("selectcategoryabove", "quiz");
        echo "</b></p>";
        if ($quizselected) {
            echo "<p>";
            print_string("addingquestions", "quiz");
            echo "</p>";
        }
        return;
    }

    if (!$category = get_record("quiz_categories", "id", "$categoryid")) {
        notify("Category not found!");
        return;
    }
    echo "<center>";
    echo format_text($category->info, FORMAT_MOODLE);

    echo '<table><tr>';

    // check if editing of this category is allowed
    if (isteacheredit($category->course)) {
        echo "<td valign=\"top\"><b>$strcreatenewquestion:</b></td>";
        echo '<td valign="top" align="right">';
        popup_form ("question.php?category=$category->id&amp;qtype=", $QUIZ_QUESTION_TYPE, "addquestion",
                    "", "choose", "", "", false, "self");
        echo '<td width="10" valign="top" align="right">';
        helpbutton("questiontypes", $strcreatenewquestion, "quiz");
        echo '</td></tr>';
    }
    else {
        echo '<tr><td>';
        print_string("publishedit","quiz");
        echo '</td></tr>';
    }

    echo '<tr><td colspan="3" align="right"><font size="2">';
    if (isteacheredit($category->course)) {
        echo '<a href="import.php?category='.$category->id.'">'.$strimportquestions.'</a>';
        helpbutton("import", $strimportquestions, "quiz");
        echo ' | ';
    }
    echo '<a href="export.php?category='.$category->id.'">'.$strexportquestions.'</a>';
    helpbutton("export", $strexportquestions, "quiz");
    echo '</font></td></tr>';

    echo '</table>';

    echo '</center>';

    $categorylist = ($recurse) ? quiz_categorylist($category->id) : $category->id;

    if (!$questions = get_records_select('quiz_questions', "category IN ($categorylist) AND qtype != '".RANDOM."'", 'qtype, name ASC', '*', $page*$perpage, $perpage)) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    $canedit = isteacheredit($category->course);

    echo "<form method=\"post\" action=\"edit.php\">";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\">";
    echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"100%\">";
    echo "<tr>";
    if ($quizselected) {
        echo "<th width=\"*\" nowrap=\"nowrap\">$strselect</th>";
    }
    echo "<th width=\"100%\" align=\"left\" nowrap=\"nowrap\">$strquestionname</th><th width=\"*\" nowrap=\"nowrap\">$strtype</th>";
    if ($canedit) {
        echo "<th width=\"70\" nowrap=\"nowrap\">$stredit</th>";
    }
    echo "</tr>\n";
    foreach ($questions as $question) {
        if ($question->qtype == RANDOM) {
            //continue;
        }
        echo "<tr>\n";
        if ($quizselected) {
            echo "<td align=\"center\">";
            echo "<input type=\"checkbox\" name=\"q$question->id\" value=\"1\" />\n";
            echo "</td>";
        }
        echo "<td>".$question->name."</td>\n";
        echo "<td align=\"center\">\n";
        quiz_print_question_icon($question, $canedit);
        echo "</td>\n";
        if ($canedit) {
            echo "<td>\n";
                echo "<a title=\"$strdelete\" href=\"question.php?id=$question->id&amp;delete=$question->id\">\n<img
                     src=\"../../pix/t/delete.gif\" border=\"0\" alt=\"$strdelete\" /></a>&nbsp;";
                echo "<a title=\"$strpreview\" href=\"javascript:void();\" onClick=\"openpopup('/mod/quiz/preview.php?id=$question->id','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\"><img
                      src=\"../../pix/t/preview.gif\" border=\"0\" alt=\"$strpreview\" /></a>&nbsp;";
                echo "<a title=\"$stredit\" href=\"question.php?id=$question->id\"><img
                     src=\"../../pix/t/edit.gif\" border=\"0\" alt=\"$stredit\" /></a>&nbsp;";
                echo "<a title=\"$strcopy\" href=\"question.php?id=$question->id&amp;copy=true\"><img
                     src=\"../../pix/t/copy.gif\" border=\"0\" alt=\"$strcopy\" /></a>";
            echo "</td>\n";
        }
        echo "</tr>\n";
    }
    $numquestions = count_records_select('quiz_questions', "category IN ($categorylist) AND qtype != '".RANDOM."'");
    echo '<tr><td colspan="3">';
    print_paging_bar($numquestions, $page, $perpage,
                "edit.php?perpage=$perpage&amp;");
    echo '</td></tr>';

    if ($quizselected) {
        echo "<tr>\n<td colspan=\"3\">";
        echo "<input type=\"submit\" name=\"add\" value=\"<< $straddselectedtoquiz\" />\n";
        // echo "<input type=\"submit\" name=\"delete\" value=\"XX Delete selected\">";
        echo "<input type=\"button\" onclick=\"checkall()\" value=\"$strselectall\" />\n";
        echo "</td></tr>";
    }
    echo "</table>\n";
    echo "</form>\n";
    if ($quizselected and isteacheredit($category->course)) {
        for ($i=1;$i<=10; $i++) {
            $randomcount[$i] = $i;
        }
        echo '<form method="post" action="multiple.php">';
        echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\">";
        print_string('addrandom1', 'quiz');
        choose_from_menu($randomcount, 'randomcreate', '10', '');
        print_string('addrandom2', 'quiz');
        // Don't offer the option to change the grade
        //choose_from_menu($randomcount, 'randomgrade', '1', '');
        echo '<input type="hidden" name="randomgrade" value="1" />';
        echo '<input type="hidden" name="recurse" value="'.$recurse.'" />';
        echo "<input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
        echo ' <input type="submit" name="save" value="'. get_string('add') .'" />';
        helpbutton('random', get_string('random', 'quiz'), 'quiz');
        echo '</form>';
    }
}


function quiz_start_attempt($quizid, $userid, $numattempt) {
    $attempt->quiz = $quizid;
    $attempt->userid = $userid;
    $attempt->attempt = $numattempt;
    $attempt->timestart = time();
    $attempt->timefinish = 0;
    $attempt->timemodified = time();
    $attempt->id = insert_record("quiz_attempts", $attempt);
    return $attempt;
}

function quiz_get_user_attempt_unfinished($quizid, $userid) {
// Returns an object containing an unfinished attempt (if there is one)
    return get_record("quiz_attempts", "quiz", $quizid, "userid", $userid, "timefinish", 0);
}

function quiz_get_user_attempts($quizid, $userid) {
// Returns a list of all attempts by a user
    return get_records_select("quiz_attempts", "quiz = '$quizid' AND userid = '$userid' AND timefinish > 0",
                              "attempt ASC");
}


function quiz_get_user_attempts_string($quiz, $attempts, $bestgrade) {
/// Returns a simple little comma-separated list of all attempts,
/// with each grade linked to the feedback report and with the best grade highlighted

    $bestgrade = format_float($bestgrade);
    foreach ($attempts as $attempt) {
        $attemptgrade = format_float(($attempt->sumgrades / $quiz->sumgrades) * $quiz->grade);
        if ($attemptgrade == $bestgrade) {
            $userattempts[] = "<span class=\"highlight\"><a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">$attemptgrade</a></span>";
        } else {
            $userattempts[] = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">$attemptgrade</a>";
        }
    }
    return implode(",", $userattempts);
}

function quiz_get_best_grade($quizid, $userid) {
/// Get the best current grade for a particular user in a quiz
    if (!$grade = get_record('quiz_grades', 'quiz', $quizid, 'userid', $userid)) {
        return NULL;
    }

    return (round($grade->grade));
}

function quiz_save_best_grade($quiz, $userid) {
/// Calculates the best grade out of all attempts at a quiz for a user,
/// and then saves that grade in the quiz_grades table.

    if (!$attempts = quiz_get_user_attempts($quiz->id, $userid)) {
        notify('Could not find any user attempts');
        return false;
    }

    $bestgrade = quiz_calculate_best_grade($quiz, $attempts);
    $bestgrade = (($bestgrade / $quiz->sumgrades) * $quiz->grade);

    if ($grade = get_record('quiz_grades', 'quiz', $quiz->id, 'userid', $userid)) {
        $grade->grade = round($bestgrade, 2);
        $grade->timemodified = time();
        if (!update_record('quiz_grades', $grade)) {
            notify('Could not update best grade');
            return false;
        }
    } else {
        $grade->quiz = $quiz->id;
        $grade->userid = $userid;
        $grade->grade = round($bestgrade, 2);
        $grade->timemodified = time();
        if (!insert_record('quiz_grades', $grade)) {
            notify('Could not insert new best grade');
            return false;
        }
    }
    return true;
}


function quiz_calculate_best_grade($quiz, $attempts) {
/// Calculate the best grade for a quiz given a number of attempts by a particular user.

    switch ($quiz->grademethod) {

        case ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt->sumgrades;
            }
            break;

        case ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt->sumgrades;
            }
            return $final;

        case GRADEAVERAGE:
            $sum = 0;
            $count = 0;
            foreach ($attempts as $attempt) {
                $sum += $attempt->sumgrades;
                $count++;
            }
            return (float)$sum/$count;

        default:
        case GRADEHIGHEST:
            $max = 0;
            foreach ($attempts as $attempt) {
                if ($attempt->sumgrades > $max) {
                    $max = $attempt->sumgrades;
                }
            }
            return $max;
    }
}


function quiz_calculate_best_attempt($quiz, $attempts) {
/// Return the attempt with the best grade for a quiz

    switch ($quiz->grademethod) {

        case ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt;
            }
            break;

        case GRADEAVERAGE: // need to do something with it :-)
        case ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt;
            }
            return $final;

        default:
        case GRADEHIGHEST:
            $max = -1;
            foreach ($attempts as $attempt) {
                if ($attempt->sumgrades > $max) {
                    $max = $attempt->sumgrades;
                    $maxattempt = $attempt;
                }
            }
            return $maxattempt;
    }
}


function quiz_save_attempt($quiz, $questions, $result,
                           $attemptnum, $finished = true) {
/// Given a quiz, a list of attempted questions and a total grade
/// this function saves EVERYTHING so it can be reconstructed later
/// if necessary.

    global $USER;
    global $QUIZ_QTYPES;

    // First find the attempt in the database (start of attempt)

    if (!$attempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        notify("Trying to save an attempt that was not started!");
        return false;
    }

    // Not usually necessary, but there's some sort of very rare glitch
    // I've seen where the number wasn't already the same.  In these cases
    // We upgrade the database to match the attemptnum we calculated
    $attempt->attempt = $attemptnum;

    // Now let's complete this record and save it

    $attempt->sumgrades = $result->sumgrades;
    if ($finished) {
        $attempt->timefinish = time();
    }
    $attempt->timemodified = time();

    if (!update_record("quiz_attempts", $attempt)) {
        notify("Error while saving attempt");
        return false;
    }

    // Now let's save all the questions for this attempt

    foreach ($questions as $question) {

        // Fetch the response record for this question...
        $response = get_record('quiz_responses',
                'attempt', $attempt->id, 'question', $question->id);

        $response->grade = $result->details[$question->id]->grade;

        if (!empty($question->response)) {
            $responseanswerfield = $QUIZ_QTYPES[$question->qtype]
                    ->convert_to_response_answer_field($question->response);

            $response->answer = $responseanswerfield;

        } else if (!isset($response->answer)) {
            $response->answer = '';
        }

        if (!update_record("quiz_responses", $response)) {
            notify("Error while saving response");
            return false;
        }
    }
    return $attempt;
}

function quiz_extract_correctanswers($answers, $nameprefix) {
/// Convinience function that is used by some single-response
/// question-types for determining correct answers.

    $bestanswerfraction = 0.0;
    $correctanswers = array();
    foreach ($answers as $answer) {
        if ($answer->fraction > $bestanswerfraction) {
            $correctanswers = array($nameprefix.$answer->id => $answer);
            $bestanswerfraction = $answer->fraction;
        } else if ($answer->fraction == $bestanswerfraction) {
            $correctanswers[$nameprefix.$answer->id] = $answer;
        }
    }
    return $correctanswers;
}

function quiz_grade_responses($quiz, $questions, $attemptid=0) {
/// Given a list of questions (including ->response[] and ->maxgrade
/// on each question) this function does all the hard work of calculating the
/// score for each question, as well as a total grade for
/// the whole quiz. It returns everything in a structure
/// that lookas like this
/// ->sumgrades     (sum of all grades for all questions)
/// ->grade         (final grade result for the whole quiz)
/// ->percentage    (Percentage of the max grade achieved)
/// ->details[]
/// The array ->details[] is indexed like the $questions argument
/// and contains scoring information per question. Each element has
/// this structure:
/// []->grade            (Grade awarded on the specific question)
/// []->answers[]        (result answer records for the question response(s))
/// []->correctanswers[] (answer records if question response(s) had been correct)
///  - HOWEVER, ->answers[] and ->correctanswers[] are supplied only
///             if there is a response on the question...
/// The array ->answers[] is indexed like ->response[] on its corresponding
/// element in $questions. It is the case for ->correctanswers[] when
/// there can be multiple responses per question but if there can be only one
/// response per question then all possible correctanswers will be
/// represented, indexed like the response index concatenated with the ->id
/// of its answer record.

    global $QUIZ_QTYPES;

    if (!$questions) {
        error("No questions!");
    }

    $result->sumgrades = 0.0;
    foreach ($questions as $qid => $question) {

        if (!isset($question->response) && $attemptid) {
            /// No response on the question
            /// This case is common if the quiz shows a limited
            /// number of questions per page.
            $response = get_record('quiz_responses', 'attempt',
                                   $attemptid, 'question', $qid);
            $resultdetails->grade = $response->grade;

        } else if (empty($question->qtype)) {
            continue;

        } else {

            $resultdetails = $QUIZ_QTYPES[$question->qtype]->grade_response
                                ($question, quiz_qtype_nameprefix($question));

            // Negative grades will not do:
            if (((float)($resultdetails->grade)) <= 0.0) {
                $resultdetails->grade = 0.0;

            // Neither will extra credit:
            } else if (((float)($resultdetails->grade)) >= 1.0) {
                $resultdetails->grade = $question->maxgrade;

            } else {
                $resultdetails->grade *= $question->maxgrade;
            }
        }

        // if time limit is enabled and exceeded, return zero grades
        if ($quiz->timelimit > 0) {
            if (($quiz->timelimit + 60) <= $quiz->timesincestart) {
                $resultdetails->grade = 0;
            }
        }

        $result->sumgrades += $resultdetails->grade;
        $resultdetails->grade = round($resultdetails->grade, 2);
        $result->details[$qid] = $resultdetails;
    }

    $fraction = (float)($result->sumgrades / $quiz->sumgrades);
    $result->percentage = format_float($fraction * 100.0);
    $result->grade      = format_float($fraction * $quiz->grade);
    $result->sumgrades = round($result->sumgrades, 2);

    return $result;
}


// this function creates default export filename
function default_export_filename($course,$category) {
    //Take off some characters in the filename !!
    $takeoff = array(" ", ":", "/", "\\", "|");
    $export_word = str_replace($takeoff,"_",strtolower(get_string("exportfilename","quiz")));
    //If non-translated, use "export"
    if (substr($export_word,0,1) == "[") {
        $export_word= "export";
    }

    //Calculate the date format string
    $export_date_format = str_replace(" ","_",get_string("exportnameformat","quiz"));
    //If non-translated, use "%Y%m%d-%H%M"
    if (substr($export_date_format,0,1) == "[") {
        $export_date_format = "%%Y%%m%%d-%%H%%M";
    }

    //Calculate the shortname
    $export_shortname = clean_filename($course->shortname);
    if (empty($export_shortname) or $export_shortname == '_' ) {
        $export_shortname = $course->id;
    }

    //Calculate the category name
    $export_categoryname = clean_filename($category->name);

    //Calculate the final export filename
    //The export word
    $export_name = $export_word."-";
    //The shortname
    $export_name .= strtolower($export_shortname)."-";
    //The category name
    $export_name .= strtolower($export_categoryname)."-";
    //The date format
    $export_name .= userdate(time(),$export_date_format,99,false);
    //The extension - no extension, supplied by format
    // $export_name .= ".txt";

    return $export_name;
}

// function to read all questions for category into big array
// added by Howard Miller June 2004
function get_questions_category( $category ) {

    // questions will be added to an array
    $qresults = array();

    // get the list of questions for the category
    if ($questions = get_records("quiz_questions","category",$category->id)) {

        // iterate through questions, getting stuff we need
        foreach($questions as $question) {
            $new_question = get_question_data( $question );
            $qresults[] = $new_question;
        }
    }

    return $qresults;
}

// function to read single question, parameter is object view of
// quiz_categories record, results is a combined object
// defined as follows...
// ->id     quiz_questions id
// ->category   category
// ->name   q name
// ->questiontext
// ->image
// ->qtype  see defines at the top of this file
// ->stamp  not too sure
// ->version    not sure
// ----SHORTANSWER
// ->usecase
// ->answers    array of answers
// ----TRUEFALSE
// ->trueanswer truefalse answer
// ->falseanswer truefalse answer
// ----MULTICHOICE
// ->layout
// ->single many or just one correct answer
// ->answers    array of answer objects
// ----NUMERIC
// ->min  minimum answer span
// ->max  maximum answer span
// ->answer single answer
// ----MATCH
// ->subquestions array of sub questions
// ---->questiontext
// ---->answertext
function get_question_data( $question ) {
    // what to do next depends of question type (qtype)
    switch ($question->qtype)  {
        case SHORTANSWER:
            $shortanswer = get_record("quiz_shortanswer","question",$question->id);
            $question->usecase = $shortanswer->usecase;
            $question->answers = get_exp_answers( $question->id );
            break;
        case TRUEFALSE:
            if (!$truefalse = get_record("quiz_truefalse","question",$question->id)) {
                error( "quiz_truefalse record $question->id not found" );
            }
            $question->trueanswer = get_exp_answer( $truefalse->trueanswer );
            $question->falseanswer = get_exp_answer( $truefalse->falseanswer );
            break;
        case MULTICHOICE:
            if (!$multichoice = get_record("quiz_multichoice","question",$question->id)) {
                error( "quiz_multichoice $question->id not found" );
            }
            $question->layout = $multichoice->layout;
            $question->single = $multichoice->single;
            $question->answers = get_exp_answers( $multichoice->question );
            break;
        case NUMERICAL:
            if (!$numeric = get_record("quiz_numerical","question",$question->id)) {
                error( "quiz_numerical $question->id not found" );
            }
            $question->min = $numeric->min;
            $question->max = $numeric->max;
            $question->answer = get_exp_answer( $numeric->answer );
            break;
        case MATCH:
            if (!$subquestions = get_records("quiz_match_sub","question",$question->id)) {
                error( "quiz_match_sub $question->id not found" );
            }
            $question->subquestions = $subquestions;
            break;
        case DESCRIPTION:
            // nothing to do
            break;
        case MULTIANSWER:
            // nothing to do
            break;
        default:
            notify("No handler for question type $question->qtype in get_question");
    }
    return $question;
}

// function to return single answer
// ->id     answer id
// ->question   question number
// ->answer
// ->fraction
// ->feedback
function get_exp_answer( $id ) {
    if (!$answer = get_record("quiz_answers","id",$id )) {
        error( "quiz_answers record $id not found" );
    }
    return $answer;
}

// function to return array of answers for export
function get_exp_answers( $question_num ) {
    if (!$answers = get_records("quiz_answers","question",$question_num)) {
        error( "quiz_answers question $question_num not found" );
    }
    return $answers;
}

function quiz_categorylist($categoryid) {
    // returns a comma separated list of ids of the category and all subcategories
    $categorylist = $categoryid;
    if ($subcategories = get_records('quiz_categories', 'parent', $categoryid, 'sortorder ASC', 'id, id')) {
        foreach ($subcategories as $subcategory) {
            $categorylist .= ','. quiz_categorylist($subcategory->id);
        }
    }
    return $categorylist;
}

// function to determine where question is in use
function quizzes_question_used( $id, $published=false, $courseid=0 ) {
  // $id = question id
  // $published = is category published
  // $courseid = course id, required only if $published=true
  // returns array of names of quizzes it appears in
  if ($published) {
    $quizzes = get_records("quiz");
  }
  else {
    $quizzes = get_records("quiz","course",$courseid);
  }
  $beingused = array();
  if ($quizzes) {
    foreach ($quizzes as $quiz) {
      $questions = explode(',', $quiz->questions);
      foreach ($questions as $question) {
        if ($question==$id) {
          $beingused[] = $quiz->name;
        }
      }
    }
  }
  return $beingused;
}
?>
