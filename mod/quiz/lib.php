<?PHP  // $Id$

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

$QUIZ_QUESTION_TYPE = array ( MULTICHOICE   => get_string("multichoice", "quiz"),
                              TRUEFALSE     => get_string("truefalse", "quiz"),
                              SHORTANSWER   => get_string("shortanswer", "quiz"),
                              NUMERICAL     => get_string("numerical", "quiz"),
                              MATCH         => get_string("match", "quiz"),
                              DESCRIPTION   => get_string("description", "quiz"),
                              RANDOM        => get_string("random", "quiz"),
                              RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
                              MULTIANSWER   => get_string("multianswer", "quiz")
                              );


define("QUIZ_PICTURE_MAX_HEIGHT", "600");   // Not currently implemented
define("QUIZ_PICTURE_MAX_WIDTH",  "600");   // Not currently implemented

define("QUIZ_MAX_NUMBER_ANSWERS", "10");

define("QUIZ_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

/// FUNCTIONS ///////////////////////////////////////////////////////////////////

function quiz_add_instance($quiz) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    global $SESSION;

    unset($SESSION->modform);

    $quiz->created      = time();
    $quiz->timemodified = time();
    $quiz->timeopen = make_timestamp($quiz->openyear, $quiz->openmonth, $quiz->openday, 
                                     $quiz->openhour, $quiz->openminute, 0);
    $quiz->timeclose = make_timestamp($quiz->closeyear, $quiz->closemonth, $quiz->closeday, 
                                      $quiz->closehour, $quiz->closeminute, 0);

    if (!$quiz->id = insert_record("quiz", $quiz)) {
        return false;  // some error occurred
    }

    // The grades for every question in this quiz are stored in an array
    if ($quiz->grades) {
        foreach ($quiz->grades as $question => $grade) {
            if ($question) {
                unset($questiongrade);
                $questiongrade->quiz = $quiz->id;
                $questiongrade->question = $question;
                $questiongrade->grade = $grade;
                if (!insert_record("quiz_question_grades", $questiongrade)) {
                    return false;
                }
            }
        }
    }

    delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id);  // Just in case

    $event = NULL;
    $event->name        = $quiz->name;
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->eventtype   = 'open';
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz->id);
    $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

    if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events
        $event2 = $event;

        $event->name         .= ' ('.get_string('quizopens', 'quiz').')';
        $event->timeduration  = 0;

        $event2->timestart    = $quiz->timeclose;
        $event2->eventtype    = 'close';
        $event2->timeduration = 0;
        $event2->name        .= ' ('.get_string('quizcloses', 'quiz').')';

        add_event($event2);
    }

    add_event($event);

    return $quiz->id;
}


function quiz_update_instance($quiz) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    global $SESSION;

    unset($SESSION->modform);

    $quiz->timemodified = time();
    $quiz->timeopen = make_timestamp($quiz->openyear, $quiz->openmonth, $quiz->openday, 
                                     $quiz->openhour, $quiz->openminute, 0);
    $quiz->timeclose = make_timestamp($quiz->closeyear, $quiz->closemonth, $quiz->closeday, 
                                      $quiz->closehour, $quiz->closeminute, 0);
    $quiz->id = $quiz->instance;

    if (!update_record("quiz", $quiz)) {
        return false;  // some error occurred
    }


    // The grades for every question in this quiz are stored in an array
    // Insert or update records as appropriate

    $existing = get_records("quiz_question_grades", "quiz", $quiz->id, "", "question,grade,id");

    if ($quiz->grades) {
        foreach ($quiz->grades as $question => $grade) {
            if ($question) {
                unset($questiongrade);
                $questiongrade->quiz = $quiz->id;
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
    }

    delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id);  // Delete old and add new

    $event = NULL;
    $event->name        = $quiz->name;
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->eventtype   = 'open';
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz->id);
    $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

    if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events
        $event2 = $event;

        $event->name         .= ' ('.get_string('quizopens', 'quiz').')';
        $event->timeduration  = 0;

        $event2->timestart    = $quiz->timeclose;
        $event2->eventtype    = 'close';
        $event2->timeduration = 0;
        $event2->name        .= ' ('.get_string('quizcloses', 'quiz').')';

        add_event($event2);
    }

    add_event($event);

    return true;
}


function quiz_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    if (! $quiz = get_record("quiz", "id", "$id")) {
        return false;
    }

    $result = true;

    if ($attempts = get_records("quiz_attempts", "quiz", "$quiz->id")) {
        foreach ($attempts as $attempt) {
            if (! delete_records("quiz_responses", "attempt", "$attempt->id")) {
                $result = false;
            }
        }
    }

    if (! delete_records("quiz_attempts", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz_grades", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz_question_grades", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz", "id", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id)) {
        $result = false;
    }

    return $result;
}

function quiz_delete_course($course) {
/// Given a course object, this function will clean up anything that
/// would be leftover after all the instances were deleted
/// In this case, all non-publish quiz categories and questions

    if ($categories = get_records_select("quiz_categories", "course = '$course->id' AND publish = '0'")) {
        foreach ($categories as $category) {
            if ($questions = get_records("quiz_questions", "category", $category->id)) {
                foreach ($questions as $question) {
                    delete_records("quiz_answers", "question", $question->id);
                    delete_records("quiz_match", "question", $question->id);
                    delete_records("quiz_match_sub", "question", $question->id);
                    delete_records("quiz_multianswers", "question", $question->id);
                    delete_records("quiz_multichoice", "question", $question->id);
                    delete_records("quiz_numerical", "question", $question->id);
                    delete_records("quiz_randommatch", "question", $question->id);
                    delete_records("quiz_responses", "question", $question->id);
                    delete_records("quiz_shortanswer", "question", $question->id);
                    delete_records("quiz_truefalse", "question", $question->id);
                }
                delete_records("quiz_questions", "category", $category->id);
            }
        }
        return delete_records("quiz_categories", "course", $course->id);
    }
    return true;
}


function quiz_user_outline($course, $user, $mod, $quiz) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    if ($grade = get_record("quiz_grades", "userid", $user->id, "quiz", $quiz->id)) {
        
        if ($grade->grade) {
            $result->info = get_string("grade").": $grade->grade";
        }
        $result->time = $grade->timemodified;
        return $result;
    }
    return NULL;

    return $return;
}

function quiz_user_complete($course, $user, $mod, $quiz) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.

    return true;
}

function quiz_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

    global $CFG;

    return true;
}

function quiz_grades($quizid) {
/// Must return an array of grades, indexed by user, and a max grade.

    $quiz = get_record("quiz", "id", $quizid);
    if (empty($quiz) or empty($quiz->grade)) {
        return NULL;
    }

    $return->grades = get_records_menu("quiz_grades", "quiz", $quizid, "", "userid,grade");
    $return->maxgrade = get_field("quiz", "grade", "id", "$quizid");
    return $return;
}

function quiz_get_participants($quizid) {
/// Returns an array of users who have data in a given quiz
/// (users with records in quiz_attempts, students)

    global $CFG;

    return get_records_sql("SELECT DISTINCT u.*
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}quiz_attempts a
                            WHERE a.quiz = '$quizid' and  
                                  u.id = a.userid");
}

function quiz_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every quiz event in the site is checked, else
// only quiz events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid == 0) {
        if (! $quizzes = get_records("quiz")) {
            return true;
        }
    } else {
        if (! $quizzes = get_records("quiz", "course", $courseid)) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'quiz');
    
    foreach ($quizzes as $quiz) {
        $event = NULL;
        $event2 = NULL;
        $event2old = NULL;

        if ($events = get_records_select('event', "modulename = 'quiz' AND instance = '$quiz->id' ORDER BY timestart")) {
            $event = array_shift($events);
            if (!empty($events)) {
                $event2old = array_shift($events);
                if (!empty($events)) {
                    foreach ($events as $badevent) {
                        delete_records('event', 'id', $badevent->id);
                    }
                }
            }
        }

        $event->name        = addslashes($quiz->name);
        $event->description = addslashes($quiz->intro);
        $event->courseid    = $quiz->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'quiz';
        $event->instance    = $quiz->id;
        $event->visible     = instance_is_visible('quiz', $quiz->id);
        $event->timestart   = $quiz->timeopen;
        $event->eventtype   = 'open';
        $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

        if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Set up two events

            $event2 = $event;

            $event->name         = addslashes($quiz->name).' ('.get_string('quizopens', 'quiz').')';
            $event->timeduration = 0;

            $event2->name        = addslashes($quiz->name).' ('.get_string('quizcloses', 'quiz').')';
            $event2->timestart   = $quiz->timeclose;
            $event2->eventtype   = 'close';
            $event2->timeduration = 0;

            if (empty($event2old->id)) {
                unset($event2->id);
                add_event($event2);
            } else {
                $event2->id = $event2old->id;
                update_event($event2);
            }
        } else if (!empty($event2->id)) {
            delete_event($event2->id);
        }

        if (empty($event->id)) {
            add_event($event);
        } else {
            update_event($event);
        }

    }
    return true;
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

function quiz_get_random_categories($questionlist) {
/// Given an array of questions, this function looks for random
/// questions among them and returns a list of categories with 
/// an associated count of random questions for each.

    global $CFG;

    return get_records_sql_menu("SELECT category,count(*) 
                            FROM {$CFG->prefix}quiz_questions 
                            WHERE id IN ($questionlist) 
                              AND qtype = '".RANDOM."' 
                              GROUP BY category ");
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

function quiz_get_answers($question, $answerids=NULL) {
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


function quiz_get_attempt_responses($attempt) {
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

            $realresponse = $responses[$response->answer];
            $realanswer = $realresponse->answer;

            if (is_array($realanswer)) {
                $responses[$key]->answer = $realanswer;
            } else if ($realresponse->qtype == NUMERICAL or $realresponse->qtype == SHORTANSWER) {
                $responses[$key]->answer = array("$realanswer");
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

function quiz_print_comment($text) {
    global $THEME;

    echo "<span class=\"feedbacktext\">".format_text($text, true, false)."</span>";
}

function quiz_print_correctanswer($text) {
    global $THEME;

    echo "<p align=\"right\"><span class=\"highlight\">$text</span></p>";
}

function quiz_print_question_icon($question, $editlink=true) {
// Prints a question icon

    global $QUIZ_QUESTION_TYPE;

    if ($editlink) {
        echo "<a href=\"question.php?id=$question->id\" title=\"".$QUIZ_QUESTION_TYPE[$question->qtype]."\">";
    }
    switch ($question->qtype) {
        case SHORTANSWER:
            echo '<img border="0" height="16" width="16" src="pix/sa.gif">';
            break;
        case TRUEFALSE:
            echo '<img border="0" height="16" width="16" src="pix/tf.gif">';
            break;
        case MULTICHOICE:
            echo '<img border="0" height="16" width="16" src="pix/mc.gif">';
            break;
        case RANDOM:
            echo '<img border="0" height="16" width="16" src="pix/rs.gif">';
            break;
        case MATCH:
            echo '<img border="0" height="16" width="16" src="pix/ma.gif">';
            break;
        case RANDOMSAMATCH:
            echo '<img border="0" height="16" width="16" src="pix/rm.gif">';
            break;
        case DESCRIPTION:
            echo '<img border="0" height="16" width="16" src="pix/de.gif">';
            break;
        case NUMERICAL:
            echo '<img border="0" height="16" width="16" src="pix/nu.gif">';
            break;
        case MULTIANSWER:
            echo '<img border="0" height="16" width="16" src="pix/mu.gif">';
            break;
    }
    if ($editlink) {
        echo "</a>\n";
    }
}

function quiz_print_possible_question_image($quizid, $question) {
// Includes the question image is there is one

    global $CFG;

    if ($question->image) {
        echo '<img border="0" src="';

        if (substr(strtolower($question->image), 0, 7) == 'http://') {
            echo $question->image;

        } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/mod/quiz/quizfile.php/$quizid/$question->id/$question->image";

        } else {
            echo "$CFG->wwwroot/mod/quiz/quizfile.php?file=/$quizid/$question->id/$question->image";
        }
        echo '" />';

    }
}

function quiz_print_question($number, $question, $grade, $quizid, 
                             $feedback=NULL, $response=NULL, $actualgrade=NULL, $correct=NULL,
                             $realquestion=NULL, $shuffleanswers=false, $showgrades=true, $courseid=0) {

/// Prints a quiz question, any format
/// $question is provided as an object

    global $CFG, $THEME;

    $question->questiontextformat = isset($question->questiontextformat) ? $question->questiontextformat : NULL;

    if ($question->qtype == DESCRIPTION) {  // Special case question - has no answers etc
        echo '<p align="center">';
        echo format_text($question->questiontext, $question->questiontextformat, NULL, $courseid);
        quiz_print_possible_question_image($quizid, $question);
        echo '</p>';
        return true;
    }

    if (empty($actualgrade)) {
        $actualgrade = 0;
    }

    $stranswer = get_string("answer", "quiz");
    $strmarks  = get_string("marks", "quiz");

    echo '<table width="100%" cellspacing="10">';
    echo '<tr><td nowrap="nowrap" width="100" valign="top">';
    echo '<p align="center"><b>' . $number . '</b></p>';
    if ($showgrades) {
        if ($feedback or $response) {
            echo "<p align=\"center\"><font size=\"1\">$strmarks: $actualgrade/$grade</font></p>";
        } else {
            echo "<p align=\"center\"><font size=\"1\">$grade $strmarks</font></p>";
        }
    }
    print_spacer(1,100);
    
    if (isset($question->recentlyadded) and $question->recentlyadded) {
        echo '</td><td valign="top" align="right">';
        // Notify the user of this recently added question
        echo '<font color="red">';
        echo get_string('recentlyaddedquestion', 'quiz');
        echo '</font>';
        echo '</td></tr><tr><td></td><td valign="top">';

    } else { // The normal case
        echo '</td><td valign="top">';
    }


    if (empty($realquestion)) { 
        $realquestion->id = $question->id;
    } else {    // Add a marker to connect this question to the actual random parent
        echo "<input type=\"hidden\" name=\"q{$realquestion->id}rq$question->id\" value=\"x\" />\n";
    }

    switch ($question->qtype) {

       case SHORTANSWER: 
       case NUMERICAL:
           echo format_text($question->questiontext, $question->questiontextformat, NULL, $courseid);
           quiz_print_possible_question_image($quizid, $question);
           if ($response) {
               $value = 'value="'.s($response[0]).'"';
           } else {
               $value = '';
           }
           echo "<p align=\"right\">$stranswer: <input type=\"text\" name=\"q$realquestion->id\" size=\"80\" $value /></p>";
           if ($feedback) {
               quiz_print_comment("<p align=\"right\">$feedback[0]</p>");
           }
           if ($correct) {
               $correctanswers = implode(", ", $correct);
               quiz_print_correctanswer($correctanswers);
           }
           break;

       case TRUEFALSE:
           if (!$options = get_record("quiz_truefalse", "question", $question->id)) {
               notify("Error: Missing question options!");
           }
           if (!$true = get_record("quiz_answers", "id", $options->trueanswer)) {
               notify("Error: Missing question answers!");
           }
           if (!$false = get_record("quiz_answers", "id", $options->falseanswer)) {
               notify("Error: Missing question answers!");
           }
           if (!$true->answer) {
               $true->answer = get_string("true", "quiz");
           }
           if (!$false->answer) {
               $false->answer = get_string("false", "quiz");
           }
           echo format_text($question->questiontext, $question->questiontextformat, NULL, $courseid);
           quiz_print_possible_question_image($quizid, $question);

           $truechecked = "";
           $falsechecked = "";

           if (!empty($response[$true->id])) {
               $truechecked = 'checked="checked"';
               $feedbackid = $true->id;
           } else if (!empty($response[$false->id])) {
               $falsechecked = 'checked="checked"';
               $feedbackid = $false->id;
           }

           $truecorrect = "";
           $falsecorrect = "";
           if ($correct) {
               if (!empty($correct[$true->id])) {
                   $truecorrect = 'class="highlight"';
               }
               if (!empty($correct[$false->id])) {
                   $falsecorrect = 'class="highlight"';
               }
           }
           echo "<table align=\"right\" cellpadding=\"5\"><tr><td align=\"right\">$stranswer:&nbsp;&nbsp;";
           echo "<td $truecorrect>";
           echo "<input $truechecked type=\"radio\" name=\"q$realquestion->id\" value=\"$true->id\" />$true->answer";
           echo "</td><td $falsecorrect>";
           echo "<input $falsechecked type=\"radio\" name=\"q$realquestion->id\" value=\"$false->id\" />$false->answer";
           echo "</td></tr></table><br clear=\"all\">";// changed from CLEAR=ALL jm
           if ($feedback) {
               quiz_print_comment("<p align=\"right\">$feedback[$feedbackid]</p>");
           }

           break;

       case MULTICHOICE:
           if (!$options = get_record("quiz_multichoice", "question", $question->id)) {
               notify("Error: Missing question options!");
           }
           if (!$answers = get_records_list("quiz_answers", "id", $options->answers)) {
               notify("Error: Missing question answers!");
           }
           echo format_text($question->questiontext, $question->questiontextformat, NULL, $courseid);
           quiz_print_possible_question_image($quizid, $question);
           echo "<table align=\"right\">";
           echo "<tr><td valign=\"top\">$stranswer:&nbsp;&nbsp;</td><td>";
           echo "<table>";
           $answerids = explode(",", $options->answers);

           if ($shuffleanswers) {
               $answerids = swapshuffle($answerids);
           }

           foreach ($answerids as $key => $answerid) {
               $answer = $answers[$answerid];
               $qnumchar = chr(ord('a') + $key);

               if (empty($response[$answerid])) {
                   $checked = "";
               } else {
                   $checked = 'checked="checked"';
               }
               echo '<tr><td valign="top">';
               if ($options->single) {
                   echo "<input $checked type=\"radio\" name=\"q$realquestion->id\" value=\"$answer->id\" />";
               } else {
                   echo "<input $checked type=\"checkbox\" name=\"q$realquestion->id"."a$answer->id\" value=\"$answer->id\" />";
               }
               echo "</td>";
               if (empty($feedback) or empty($correct[$answer->id])) {
                   echo '<td valign="top">'.format_text("$qnumchar. $answer->answer").'</td>';
               } else {
                   echo '<td valign="top" class="highlight">'.format_text("$qnumchar. $answer->answer").'</td>';
               }
               if (!empty($feedback)) {
                   echo "<td valign=\"top\">&nbsp;";
                   if (!empty($response[$answerid])) {
                       quiz_print_comment($feedback[$answerid]);
                   }
                   echo "</td>";
               }
               echo "</tr>";
           }
           echo "</table>";
           echo "</td></tr></table>";
           break;

       case MATCH: 
           if (!$options = get_record("quiz_match", "question", $question->id)) {
               notify("Error: Missing question options!");
           }
           if (!$subquestions = get_records_list("quiz_match_sub", "id", $options->subquestions)) {
               notify("Error: Missing subquestions for this question!");
           }
           if (!empty($question->questiontext)) {
               echo format_text($question->questiontext, $question->questiontextformat, NULL, $courseid);
           }
           quiz_print_possible_question_image($quizid, $question);

           if ($shuffleanswers) {
               $subquestions = draw_rand_array($subquestions, count($subquestions));
           }
           foreach ($subquestions as $subquestion) {
               $answers[$subquestion->id] = $subquestion->answertext;
           }

           $answers = draw_rand_array($answers, count($answers));

           echo '<table border="0" cellpadding="10" align="right">';
           foreach ($subquestions as $key => $subquestion) {
               echo '<tr><td align="left" valign="top">';
               echo $subquestion->questiontext;
               echo '</td>';
               if (empty($response)) {
                   echo '<td align="right" valign="top">';
                   choose_from_menu($answers, "q$realquestion->id"."r$subquestion->id");
               } else {
                   if (empty($response[$key])) {
                       echo '<td align="right" valign="top">';
                       choose_from_menu($answers, "q$realquestion->id"."r$subquestion->id");
                   } else {
                       if ($response[$key] == $correct[$key]) {
                           echo '<td align="right" valign="top" class="highlight">';
                           choose_from_menu($answers, "q$realquestion->id"."r$subquestion->id", $response[$key]);
                       } else {
                           echo '<td align="right" valign="top">';
                           choose_from_menu($answers, "q$realquestion->id"."r$subquestion->id", $response[$key]);
                       }
                   }

                   if (!empty($feedback[$key])) {
                       quiz_print_comment($feedback[$key]);
                   }
               }
               echo '</td></tr>';
           }
           echo '</table>';

           break;

       case RANDOMSAMATCH: 
           if (!$options = get_record("quiz_randomsamatch", "question", $question->id)) {
               notify("Error: Missing question options!");
           }
           echo format_text($question->questiontext, $question->questiontextformat, NULL, $courseid);
           quiz_print_possible_question_image($quizid, $question);

           /// First, get all the questions available

           $allquestions = get_records_select("quiz_questions", 
                                              "category = $question->category AND qtype = ".SHORTANSWER);
           if (count($allquestions) < $options->choose) {
               notify("Error: could not find enough Short Answer questions in the database!");
               notify("Found ".count($allquestions).", need $options->choose.");
               break;
           }

           if (empty($response)) {  // Randomly pick the questions
               if (!$randomquestions = draw_rand_array($allquestions, $options->choose)) {
                   notify("Error choosing $options->choose random questions");
                   break;
               }
           } else {                 // Use existing questions
               $randomquestions = array();
               foreach ($response as $key => $rrr) {
                   $rrr = explode("-", $rrr);
                   $randomquestions[$key] = $allquestions[$key];
                   $responseanswer[$key] = $rrr[1];
               }
           }
    
           /// For each selected, find the best matching answers

           foreach ($randomquestions as $randomquestion) {
               $shortanswerquestion = get_record("quiz_shortanswer", "question", $randomquestion->id);
               $questionanswers = get_records_list("quiz_answers", "id", $shortanswerquestion->answers);
               $bestfraction = 0;
               $bestanswer = NULL;
               foreach ($questionanswers as $questionanswer) {
                   if ($questionanswer->fraction > $bestfraction) {
                       $bestanswer = $questionanswer;
                       $bestfraction = $questionanswer->fraction;
                   }
               }
               if (empty($bestanswer)) {
                   notify("Error: Could not find the best answer for question: ".$randomquestions->name);
                   break;
               }
               $randomanswers[$bestanswer->id] = trim($bestanswer->answer);
           }

           if (!$randomanswers = draw_rand_array($randomanswers, $options->choose)) {  // Mix them up
               notify("Error randomising answers!");
               break;
           }

           echo '<table border="0" cellpadding="10">';
           foreach ($randomquestions as $key => $randomquestion) {
               echo '<tr><td align="left" valign="top">';
               echo $randomquestion->questiontext;
               echo '</td>';
               echo '<td align="right" valign="top">';
               if (empty($response)) {
                   choose_from_menu($randomanswers, "q$realquestion->id"."r$randomquestion->id");
               } else {
                   if (!empty($correct[$key])) {
                       if ($randomanswers[$responseanswer[$key]] == $correct[$key]) {
                           echo '<span="highlight">';
                           choose_from_menu($randomanswers, "q$realquestion->id"."r$randomquestion->id", $responseanswer[$key]);
                           echo '</span><br />';
                       } else {
                           choose_from_menu($randomanswers, "q$realquestion->id"."r$randomquestion->id", $responseanswer[$key]);
                           quiz_print_correctanswer($correct[$key]);
                       }
                   } else {
                       choose_from_menu($randomanswers, "q$realquestion->id"."r$randomquestion->id", $responseanswer[$key]);
                   }
                   if (!empty($feedback[$key])) {
                       quiz_print_comment($feedback[$key]);
                   }
               }
               echo '</td></tr>';
           }
           echo '</table>';
           break;

       case MULTIANSWER:
           // For this question type, we better print the image on top:
           quiz_print_possible_question_image($quizid, $question);

           $qtextremaining = format_text($question->questiontext, $question->questiontextformat, NULL, $courseid);

           // The regex will recognize text snippets of type {#X} 
           // where the X can be any text not containg } or white-space characters.

           $strfeedback = get_string('feedback', 'quiz');

           while (ereg('\{#([^[:space:]}]*)}', $qtextremaining, $regs)) {
               $qtextsplits = explode($regs[0], $qtextremaining, 2);
               echo $qtextsplits[0];
               $qtextremaining = $qtextsplits[1];

               $multianswer = get_record('quiz_multianswers', 'question', $question->id, 'positionkey', $regs[1]);

               $inputname= " name=\"q{$realquestion->id}ma$multianswer->id\" ";

               if (!empty($response) && ereg('(.[^-]*)-(.+)', array_shift($response), $responseitems)) {
                   $responsefractiongrade = (float)$responseitems[1];
                   $actualresponse = $responseitems[2];

                   if (1.0 == $responsefractiongrade) {
                       $style = 'style="background-color:lime"';
                   } else if (0.0 < $responsefractiongrade) {
                       $style = 'style="background-color:yellow"';
                   } else if ('' != $actualresponse) {
                       // The response must have been totally wrong:
                       $style = 'style="background-color:red"';
                   } else { 
                       // There was no response given
                       $style = '';
                   }
               } else {
                   $responsefractiongrade = 0.0;
                   $actualresponse = '';
                   $style = '';
               }

               $feedbackitem = '';
               switch ($multianswer->answertype) {
                   case SHORTANSWER:
                   case NUMERICAL:
                       if (isset($feedback[$regs[1]-1])) {
                           $title = str_replace("'", "\\'", $feedback[$regs[1]-1] );
                           $popup = " onmouseover=\"return overlib('$title', CAPTION, '$strfeedback', FGCOLOR, '$THEME->cellcontent');\" ".
                                    " onmouseout=\"return nd();\" ";
                       } else {
                           $popup = '';
                       }
                       echo " <input $style $popup $inputname value=\"$actualresponse\" type=\"text\" size=\"12\" /> ";
                       break;
                   case MULTICHOICE:
                       $outputoptions = '';
                       $answers = get_records_list("quiz_answers", "id", $multianswer->answers);
                       $outputoptions .= '<option></option>'; // Default empty option 
                       foreach ($answers as $answer) {
                           if ($answer->id == $actualresponse) {
                               $selected = 'selected';
                               $feedbackitem = $answer->feedback;
                           } else {
                               $selected = '';
                           }
                           $outputoptions .= "<option value=\"$answer->id\" $selected>$answer->answer</option>";
                       }
                       if ($feedbackitem) {
                           $title = str_replace("'", "\\'", $feedbackitem);
                           $popup = " onmouseover=\"return overlib('$title', CAPTION, '$strfeedback', FGCOLOR, '$THEME->cellcontent');\" ".
                                    " onmouseout=\"return nd();\" ";
                       } else {
                           $popup = '';
                       }
                       echo "<select $popup $style $inputname>";
                       echo $outputoptions;
                       echo '</select>';
                       break;
                   default:
                       error("Unable to recognized answertype $answer->answertype");
                       break;
               }
           }

           // Print the final piece of question text:
           echo $qtextremaining;
           break;

       case RANDOM:
           // This can only happen if it is a recently added question

           echo '<p>' . get_string('random', 'quiz') . '</p>';
           break;

       default: 
           notify("Error: Unknown question type!");
    }

    echo "</td></tr></table>";
}



function quiz_print_quiz_questions($quiz, $results=NULL, $questions=NULL, $shuffleorder=NULL) {
// Prints a whole quiz on one page.

    /// Get the questions

    if (!$questions) {
        if (empty($quiz->questions)) {
            notify("No questions have been defined!");
            return false;
        }

        if (!$questions = get_records_list("quiz_questions", "id", $quiz->questions, "")) {
            notify("Error when reading questions from the database!");
            return false;
        }
    }

    if (!$shuffleorder) {
        if (!empty($quiz->shufflequestions)) {              // Mix everything up
            $questions = swapshuffle_assoc($questions);
        } else {
            $shuffleorder = explode(",", $quiz->questions);  // Use originally defined order
        }
    }

    if ($shuffleorder) {                             // Order has been defined, so reorder questions
        $oldquestions = $questions;
        $questions = array();
        foreach ($shuffleorder as $key) {
            if (empty($oldquestions[$key])) { // Check for recently added questions
                if ($recentlyaddedquestion =
                        get_record("quiz_questions", "id", $key)) {
                    $recentlyaddedquestion->recentlyadded = true;
                    $questions[] = $recentlyaddedquestion;
                }
            } else {
                $questions[] = $oldquestions[$key];      // This loses the index key, but doesn't matter
            }
        }
    }

    if (!$grades = get_records_list("quiz_question_grades", "question", $quiz->questions, "", "question,grade")) {
        notify("No grades were found for these questions!");
        return false;
    }


    /// Examine the set of questions for random questions, and retrieve them 

    if (empty($results)) {   // Choose some new random questions
        if ($randomcats = quiz_get_random_categories($quiz->questions)) {
            foreach ($randomcats as $randomcat => $randomdraw) {
                /// Get the appropriate amount of random questions from this category
                if (!$catquestions[$randomcat] = quiz_choose_random_questions($randomcat, $randomdraw, $quiz->questions)) {
                    notify(get_string("toomanyrandom", "quiz", $randomcat));
                    return false;
                }
            }
        }
    } else {                 // Get the previously chosen questions
        $chosen = array();
        foreach ($questions as $question) {
            if (isset($question->random)) {
                $chosen[] = $question->random;
            }
        }
        if ($chosen) {
            $chosenlist = implode(",", $chosen);
            if (!$chosen = get_records_list("quiz_questions", "id", $chosenlist, "")) {
                notify("Error when reading questions from the database!");
                return false;
            }
        }
    }

    $strconfirmattempt = addslashes(get_string("readytosend", "quiz"));

    if (empty($quiz->grade)) {
        $onsubmit = "";
    } else {
        $onsubmit = "onsubmit=\"return confirm('$strconfirmattempt');\"";
    }

    echo "<form method=\"post\" action=\"attempt.php\" $onsubmit>\n";
    echo "<input type=\"hidden\" name=\"q\" value=\"$quiz->id\" />\n";

    $count = 0;
    $questionorder = array();

    foreach ($questions as $question) {

        if ($question->qtype != DESCRIPTION) {    // Description questions are not counted
            $count++;
        }

        $questionorder[] = $question->id;

        $feedback       = NULL;
        $response       = NULL;
        $actualgrades   = NULL;
        $correct        = NULL;
        $randomquestion = NULL;

        if (empty($results)) {
            if ($question->qtype == RANDOM ) {   // Set up random questions
                $randomquestion = $question;
                $question = array_pop($catquestions[$randomquestion->category]);
                $grades[$question->id]->grade = $grades[$randomquestion->id]->grade;
            }
        } else {
            if (!empty($results->feedback[$question->id])) {
                $feedback      = $results->feedback[$question->id];
            }
            if (!empty($results->response[$question->id])) {
                $response      = $results->response[$question->id];
            }
            if (!empty($results->grades[$question->id])) {
                $actualgrades  = $results->grades[$question->id];
            }
            if ($quiz->correctanswers) {
                if (!empty($results->correct[$question->id])) {
                    $correct   = $results->correct[$question->id];
                }
            }
            if (!empty($question->random)) {
                $randomquestion = $question;
                $question = $chosen[$question->random];
                $grades[$question->id]->grade = $grades[$randomquestion->id]->grade;
            }
        }

        print_simple_box_start("center", "90%");
        quiz_print_question($count, $question, $grades[$question->id]->grade, $quiz->id, 
                            $feedback, $response, $actualgrades, $correct, 
                            $randomquestion, $quiz->shuffleanswers, $quiz->grade, $quiz->course);
        print_simple_box_end();
        echo "<br />";
    }

    $attemptbuildsonthelast = isset($results->attemptbuildsonthelast) ? $results->attemptbuildsonthelast : NULL;

    if (empty($results) || $attemptbuildsonthelast) {
        if (!empty($quiz->shufflequestions)) {  // Things have been mixed up, so pass the question order
            $shuffleorder = implode(',', $questionorder);
            echo "<input type=\"hidden\" name=\"shuffleorder\" value=\"$shuffleorder\" />\n";
        }
        echo "<center>\n<input type=\"submit\" value=\"".get_string("savemyanswers", "quiz")."\" />\n</center>";
    }
    echo "</form>";

    return true;
}


 
function quiz_get_default_category($courseid) {
/// Returns the current category

    if ($categories = get_records("quiz_categories", "course", $courseid, "id")) {
        foreach ($categories as $category) {
            return $category;   // Return the first one (lowest id)
        }
    }

    // Otherwise, we need to make one
    $category->name = get_string("default", "quiz");
    $category->info = get_string("defaultinfo", "quiz");
    $category->course = $courseid;
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
    return get_records_select_menu("quiz_categories", "course='$courseid' $publish", "name ASC", "id,name");
}

function quiz_print_category_form($course, $current) {
// Prints a form to choose categories

    if (!$categories = get_records_select("quiz_categories", "course = '$course->id' OR publish = '1'", "name ASC")) {
        if (!$category = quiz_get_default_category($course->id)) {
            notify("Error creating a default category!");
            return false;
        }
        $categories[$category->id] = $category;
    }
    foreach ($categories as $key => $category) {
       if ($catcourse = get_record("course", "id", $category->course)) {
           if ($category->publish) {
               $category->name .= " ($catcourse->shortname)";
           }
           $catmenu[$category->id] = $category->name;
       }
    }
    $strcategory = get_string("category", "quiz");
    $strshow = get_string("show", "quiz");
    $streditcats = get_string("editcategories", "quiz");

    echo "<table width=\"100%\"><tr><td width=\"20\" nowrap=\"nowrap\">";
    echo "<b>$strcategory:</b>&nbsp;";
    echo "</td><td>";
    popup_form ("edit.php?cat=", $catmenu, "catmenu", $current, "choose", "", "", false, "self");
    echo "</td><td align=\"right\">";
    echo "<form method=\"get\" action=\"category.php\">"; 
    echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
    echo "<input type=\"submit\" value=\"$streditcats\" />";
    echo "</form>";
    echo "</td></tr></table>";
}



function quiz_choose_random_questions($category, $draws, $excluded=0) {
/// Given a question category and a number of draws, this function
/// creates a random subset of that size - returned as an array of questions

    if (!$pool = get_records_select_menu("quiz_questions", 
                "category = '$category' AND id NOT IN ($excluded) 
                                        AND qtype <> ".RANDOM." 
                                        AND qtype <> ".DESCRIPTION, 
                                        "", "id,qtype")) {
        return false;
    }

    $countpool = count($pool);

    if ($countpool == $draws) {
        $chosen = $pool;
    } else if ($countpool < $draws) {
        return false;
    } else {
        $chosen = draw_rand_array($pool, $draws);
    }

    $chosenlist = implode(",", array_keys($chosen));
    return get_records_list("quiz_questions", "id", $chosenlist);
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

function quiz_print_question_list($questionlist, $grades) {
// Prints a list of quiz questions in a small layout form with knobs
// $questionlist is comma-separated list
// $grades is an array of corresponding grades

    global $THEME;

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
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $strmoveup = get_string("moveup");
    $strmovedown = get_string("movedown");
    $strsavegrades = get_string("savegrades", "quiz");
    $strtype = get_string("type", "quiz");

    $count = 0;
    $sumgrade = 0;
    $total = count($order);
    echo "<form method=\"post\" action=\"edit.php\">";
    echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"100%\">\n";
    echo "<tr><th width=\"*\" colspan=\"3\" nowrap=\"nowrap\">$strorder</th><th align=\"left\" width=\"100%\" nowrap=\"nowrap\">$strquestionname</th><th width=\"*\" nowrap=\"nowrap\">$strtype</th><th width=\"*\" nowrap=\"nowrap\">$strgrade</th><th width=\"*\" nowrap=\"nowrap\">$stredit</th></tr>\n";
    foreach ($order as $qnum) {
        if (empty($questions[$qnum])) {
            continue;
        }
        $question = $questions[$qnum];
        $canedit = isteacheredit($question->course);
        $count++;
        echo "<tr bgcolor=\"$THEME->cellcontent\">";
        echo "<td>$count</td>";
        echo "<td>";
        if ($count != 1) {
            echo "<a title=\"$strmoveup\" href=\"edit.php?up=$qnum\"><img 
                 src=\"../../pix/t/up.gif\" border=\"0\"></a>";
        }
        echo "</td>";
        echo "<td>";
        if ($count != $total) {
            echo "<a title=\"$strmovedown\" href=\"edit.php?down=$qnum\"><img 
                 src=\"../../pix/t/down.gif\" border=\"0\"></a>";
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
        echo "<td>";
            echo "<a title=\"$strdelete\" href=\"edit.php?delete=$qnum\"><img 
                 src=\"../../pix/t/delete.gif\" border=\"0\"></a>&nbsp;";
            if ($canedit) {
                echo "<a title=\"$stredit\" href=\"question.php?id=$qnum\"><img 
                     src=\"../../pix/t/edit.gif\" border=\"0\"></a>\n";
            }
        echo "</td>";

        $sumgrade += $grades[$qnum];
    }
    echo "<tr><td colspan=5 align=\"right\">\n";
    echo "<input type=\"submit\" value=\"$strsavegrades:\" />\n";
    echo "<input type=\"hidden\" name=\"setgrades\" value=\"save\" />\n";
    echo "<td align=\"left\" bgcolor=\"$THEME->cellcontent\">\n";
    echo "<b>$sumgrade</b>";
    echo "</td><td>\n</td></tr>\n";
    echo "</table>\n";
    echo "</form>\n";

    return $sumgrade;
}


function quiz_print_cat_question_list($categoryid, $quizselected=true) {
// Prints a form to choose categories

    global $THEME, $QUIZ_QUESTION_TYPE;

    $strcategory = get_string("category", "quiz");
    $strquestion = get_string("question", "quiz");
    $straddquestions = get_string("addquestions", "quiz");
    $strimportquestions = get_string("importquestions", "quiz");
    $strnoquestions = get_string("noquestions", "quiz");
    $strselect = get_string("select", "quiz");
    $strselectall = get_string("selectall", "quiz");
    $strcreatenewquestion = get_string("createnewquestion", "quiz");
    $strquestionname = get_string("questionname", "quiz");
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $straddselectedtoquiz = get_string("addselectedtoquiz", "quiz");
    $strtype = get_string("type", "quiz");
    $strcreatemultiple = get_string("createmultiple", "quiz");

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
    echo "<td valign=\"top\"><b>$strcreatenewquestion:</b></td>";
    echo '<td valign="top" align="right">';
    popup_form ("question.php?category=$category->id&qtype=", $QUIZ_QUESTION_TYPE, "addquestion", 
                "", "choose", "", "", false, "self");
    echo '<td width="10" valign="top" align="right">';
    helpbutton("questiontypes", $strcreatenewquestion, "quiz");
    echo '</td></tr>';

    echo '<tr><td colspan="3" align="right">';
    echo '<form method="get" action="import.php">'; 
    echo "<input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
    echo "<input type=\"submit\" value=\"$strimportquestions\" />";
    helpbutton("import", $strimportquestions, "quiz");
    echo '</form>';
    echo '</td></tr>';

    echo '<tr><td colspan="3" align="right">';
    echo '<form method="get" action="multiple.php">'; 
    echo "<input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
    echo "<input type=\"submit\" value=\"$strcreatemultiple\" />";
    helpbutton("createmultiple", $strcreatemultiple, "quiz");
    echo '</form>';
    echo '</td></tr>';

    echo '</table>';

    echo '</center>';

    if (!$questions = get_records("quiz_questions", "category", $category->id, "qtype ASC")) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    $canedit = isteacheredit($category->course);

    echo "<form method=\"post\" action=\"edit.php\">";
    echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"100%\">";
    echo "<tr>";
    if ($quizselected) {
        echo "<th width=\"*\" nowrap=\"nowrap\">$strselect</th>";
    }
    echo "<th width=\"100%\" align=\"left\" nowrap=\"nowrap\">$strquestionname</th><th width=\"*\" nowrap=\"nowrap\">$strtype</th>";
    if ($canedit) {
        echo "<th width=\"*\" nowrap=\"nowrap\">$stredit</th>";
    }
    echo "</tr>\n";
    foreach ($questions as $question) {
        echo "<tr bgcolor=\"$THEME->cellcontent\">\n";
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
                echo "<a title=\"$strdelete\" href=\"question.php?id=$question->id&delete=$question->id\">\n<img 
                     src=\"../../pix/t/delete.gif\" border=0></a>&nbsp;";
                echo "<a title=\"$stredit\" href=\"question.php?id=$question->id\"><img 
                     src=\"../../pix/t/edit.gif\" border=0></a>";
            echo "</td>\n";// deleted </tr> jm
        }
        echo "</tr>\n";
    }
    if ($quizselected) {
        echo "<tr>\n<td colspan=\"3\">";
        echo "<input type=\"submit\" name=\"add\" value=\"<< $straddselectedtoquiz\" />\n";
        //echo "<input type=submit name=\"delete\" value=\"XX Delete selected\">";
        echo "<input type=\"button\" onclick=\"checkall()\" value=\"$strselectall\" />\n";
        echo "</td></tr>";
    }
    echo "</table>\n";
    echo "</form>\n";
}


function quiz_start_attempt($quizid, $userid, $numattempt) {
    $attempt->quiz = $quizid;
    $attempt->userid = $userid;
    $attempt->attempt = $numattempt;
    $attempt->timestart = time();
    $attempt->timefinish = 0; 
    $attempt->timemodified = time();

    return insert_record("quiz_attempts", $attempt);
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
            $userattempts[] = "<span class=\"highlight\"><a href=\"review.php?q=$quiz->id&attempt=$attempt->id\">$attemptgrade</a></span>";
        } else {
            $userattempts[] = "<a href=\"review.php?q=$quiz->id&attempt=$attempt->id\">$attemptgrade</a>";
        }
    }
    return implode(",", $userattempts);
}

function quiz_get_best_grade($quizid, $userid) {
/// Get the best current grade for a particular user in a quiz
    if (!$grade = get_record("quiz_grades", "quiz", $quizid, "userid", $userid)) {
        return "";
    }

    return (round($grade->grade,0));
}

function quiz_save_best_grade($quiz, $userid) {
/// Calculates the best grade out of all attempts at a quiz for a user,
/// and then saves that grade in the quiz_grades table.

    if (!$attempts = quiz_get_user_attempts($quiz->id, $userid)) {
        notify("Could not find any user attempts");
        return false;
    }

    $bestgrade = quiz_calculate_best_grade($quiz, $attempts);
    $bestgrade = (($bestgrade / $quiz->sumgrades) * $quiz->grade);

    if ($grade = get_record("quiz_grades", "quiz", $quiz->id, "userid", $userid)) {
        $grade->grade = round($bestgrade, 2);
        $grade->timemodified = time();
        if (!update_record("quiz_grades", $grade)) {
            notify("Could not update best grade");
            return false;
        }
    } else {
        $grade->quiz = $quiz->id;
        $grade->userid = $userid;
        $grade->grade = round($bestgrade, 2);
        $grade->timemodified = time();
        if (!insert_record("quiz_grades", $grade)) {
            notify("Could not insert new best grade");
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


function quiz_save_attempt($quiz, $questions, $result, $attemptnum) {
/// Given a quiz, a list of attempted questions and a total grade 
/// this function saves EVERYTHING so it can be reconstructed later
/// if necessary.

    global $USER;

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
    $attempt->timefinish = time();
    $attempt->timemodified = time();

    if (! update_record("quiz_attempts", $attempt)) {
        notify("Error while saving attempt");
        return false;
    }

    // Now let's save all the questions for this attempt

    foreach ($questions as $question) {
        $response->attempt = $attempt->id;
        $response->question = $question->id;
        $response->grade = $result->grades[$question->id];

        if (!empty($question->random)) {
            // First save the response of the random question
            // the answer is the id of the REAL response
            $response->answer = $question->random;
            if (!insert_record("quiz_responses", $response)) {
                notify("Error while saving response");
                return false;
            }
            $response->question = $question->random;
        }

        if (!empty($question->answer)) {
            $response->answer = implode(",",$question->answer);
        } else {
            $response->answer = "";
        }
        if (!insert_record("quiz_responses", $response)) {
            notify("Error while saving response");
            return false;
        }
    }
    return $attempt;
}

function quiz_grade_attempt_question_result($question,
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
                    $answer->answer = moodle_strtolower($answer->answer);
                    $question->answer = moodle_strtolower($question->answer);
                }

                $potentialgrade = (float)$answer->fraction * $question->grade;

                if ($potentialgrade >= $grade) {
                    if ((strpos(' '.$answer->answer, '*'))) {
                        $search = array('\\', '+', '(', ')', '[', ']', '-');
                        $replace = array('\\\\', '\+', '\(', '\)', '\[', '\]', '\-');
    
                        $answer->answer = str_replace('\*','@@@@@@',$answer->answer);
                        $answer->answer = str_replace('*','.*',$answer->answer);
                        
                        $answer->answer = str_replace($search, $replace, $answer->answer);

                        $answer->answer = str_replace('@@@@@@', '\*',$answer->answer);
                        
                        if (eregi('^'.$answer->answer.'$', $question->answer)) {
                            $feedback[0] = $answer->feedback;
                            $grade = $potentialgrade;
                        }

                    } else if ($answer->answer == $question->answer) {
                        $feedback[0] = $answer->feedback;
                        $grade = $potentialgrade;
                    }
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
                        $subresult = quiz_grade_attempt_question_result($subquestion, $multianswer->subanswers, true);
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
            return quiz_grade_attempt_question_result($realquestion, $answers);
    }

    $result->grade =
            $gradecanbenegative ? $grade            // Grade can be negative
                                : max(0.0, $grade); // Grade must not be negative
    $result->correct = $correct;
    $result->feedback = $feedback;
    $result->response = $response;
    return $result;
}

function quiz_grade_attempt_results($quiz, $questions) {
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
        
        if (!$answers = quiz_get_answers($question)) {
            notify("No answers defined for question id $question->id!");
            continue;
        }

        $questionresult = quiz_grade_attempt_question_result($question,
                                                             $answers);

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


function quiz_save_question_options($question) {
/// Given some question info and some data about the the answers
/// this function parses, organises and saves the question
/// It is used by question.php when saving new data from a 
/// form, and also by import.php when importing questions
/// 
/// If this is an update, and old answers already exist, then
/// these are overwritten using an update().  To do this, it 
/// it is assumed that the IDs in quiz_answers are in the same
/// sort order as the new answers being saved.  This should always
/// be true, but it's something to keep in mind if fiddling with
/// question.php
///
/// Returns $result->error or $result->noticeyesno or $result->notice
    
    switch ($question->qtype) {
        case SHORTANSWER:

            if (!$oldanswers = get_records("quiz_answers", "question", $question->id, "id ASC")) {
                $oldanswers = array();
            }

            $answers = array();
            $maxfraction = -1;

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                        $answer = $oldanswer;
                        $answer->answer   = trim($dataanswer);
                        $answer->fraction = $question->fraction[$key];
                        $answer->feedback = $question->feedback[$key];
                        if (!update_record("quiz_answers", $answer)) {
                            $result->error = "Could not update quiz answer! (id=$answer->id)";
                            return $result;
                        }
                    } else {    // This is a completely new answer
                        unset($answer);
                        $answer->answer   = trim($dataanswer);
                        $answer->question = $question->id;
                        $answer->fraction = $question->fraction[$key];
                        $answer->feedback = $question->feedback[$key];
                        if (!$answer->id = insert_record("quiz_answers", $answer)) {
                            $result->error = "Could not insert quiz answer!";
                            return $result;
                        }
                    }
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            if ($options = get_record("quiz_shortanswer", "question", $question->id)) {
                $options->answers = implode(",",$answers);
                $options->usecase = $question->usecase;
                if (!update_record("quiz_shortanswer", $options)) {
                    $result->error = "Could not update quiz shortanswer options! (id=$options->id)";
                    return $result;
                }
            } else {
                unset($options);
                $options->question = $question->id;
                $options->answers = implode(",",$answers);
                $options->usecase = $question->usecase;
                if (!insert_record("quiz_shortanswer", $options)) {
                    $result->error = "Could not insert quiz shortanswer options!";
                    return $result;
                }
            }

            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->noticeyesno = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
        break;

        case NUMERICAL:   // Note similarities to SHORTANSWER

            if (!$oldanswers = get_records("quiz_answers", "question", $question->id, "id ASC")) {
                $oldanswers = array();
            }

            $answers = array();
            $maxfraction = -1;

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                        $answer = $oldanswer;
                        $answer->answer   = $dataanswer;
                        $answer->fraction = $question->fraction[$key];
                        $answer->feedback = $question->feedback[$key];
                        if (!update_record("quiz_answers", $answer)) {
                            $result->error = "Could not update quiz answer! (id=$answer->id)";
                            return $result;
                        }
                    } else {    // This is a completely new answer
                        unset($answer);
                        $answer->answer   = $dataanswer;
                        $answer->question = $question->id;
                        $answer->fraction = $question->fraction[$key];
                        $answer->feedback = $question->feedback[$key];
                        if (!$answer->id = insert_record("quiz_answers", $answer)) {
                            $result->error = "Could not insert quiz answer!";
                            return $result;
                        }
                    }
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }

                    if ($options = get_record("quiz_numerical", "answer", $answer->id)) {
                        $options->min= $question->min[$key];
                        $options->max= $question->max[$key];
                        if (!update_record("quiz_numerical", $options)) {
                            $result->error = "Could not update quiz numerical options! (id=$options->id)";
                            return $result;
                        }
                    } else { // completely new answer
                        unset($options);
                        $options->question = $question->id;
                        $options->answer = $answer->id;
                        $options->min = $question->min[$key];
                        $options->max = $question->max[$key];
                        if (!insert_record("quiz_numerical", $options)) {
                            $result->error = "Could not insert quiz numerical options!";
                            return $result;
                        }
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->noticeyesno = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
        break;


        case TRUEFALSE:

            if (!$oldanswers = get_records("quiz_answers", "question", $question->id, "id ASC")) {
                $oldanswers = array();
            }

            if ($true = array_shift($oldanswers)) {  // Existing answer, so reuse it
                $true->answer   = get_string("true", "quiz");
                $true->fraction = $question->answer;
                $true->feedback = $question->feedbacktrue;
                if (!update_record("quiz_answers", $true)) {
                    $result->error = "Could not update quiz answer \"true\")!";
                    return $result;
                }
            } else {
                unset($true);
                $true->answer   = get_string("true", "quiz");
                $true->question = $question->id;
                $true->fraction = $question->answer;
                $true->feedback = $question->feedbacktrue;
                if (!$true->id = insert_record("quiz_answers", $true)) {
                    $result->error = "Could not insert quiz answer \"true\")!";
                    return $result;
                }
            }

            if ($false = array_shift($oldanswers)) {  // Existing answer, so reuse it
                $false->answer   = get_string("false", "quiz");
                $false->fraction = 1 - (int)$question->answer;
                $false->feedback = $question->feedbackfalse;
                if (!update_record("quiz_answers", $false)) {
                    $result->error = "Could not insert quiz answer \"false\")!";
                    return $result;
                }
            } else {
                unset($false);
                $false->answer   = get_string("false", "quiz");
                $false->question = $question->id;
                $false->fraction = 1 - (int)$question->answer;
                $false->feedback = $question->feedbackfalse;
                if (!$false->id = insert_record("quiz_answers", $false)) {
                    $result->error = "Could not insert quiz answer \"false\")!";
                    return $result;
                }
            }

            if ($options = get_record("quiz_truefalse", "question", $question->id)) {
                // No need to do anything, since the answer IDs won't have changed
                // But we'll do it anyway, just for robustness
                $options->trueanswer  = $true->id;
                $options->falseanswer = $false->id;
                if (!update_record("quiz_truefalse", $options)) {
                    $result->error = "Could not update quiz truefalse options! (id=$options->id)";
                    return $result;
                }
            } else {
                unset($options);
                $options->question    = $question->id;
                $options->trueanswer  = $true->id;
                $options->falseanswer = $false->id;
                if (!insert_record("quiz_truefalse", $options)) {
                    $result->error = "Could not insert quiz truefalse options!";
                    return $result;
                }
            }
        break;


        case MULTICHOICE:

            if (!$oldanswers = get_records("quiz_answers", "question", $question->id, "id ASC")) {
                $oldanswers = array();
            }
            

            // following hack to check at least two answers exist
            $answercount = 0;
            foreach ($question->answer as $key=>$dataanswer) {
                if ($dataanswer != "") {
                    $answercount++;
                }
            }
            $answercount += count($oldanswers);
            if ($answercount < 2) { // check there are at lest 2 answers for multiple choice
                $result->notice = get_string("notenoughanswers", "quiz", "2");
                return $result;
            }
            
            

            // Insert all the new answers

            $totalfraction = 0;
            $maxfraction = -1;

            $answers = array();

            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    if ($answer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                        $answer->answer   = $dataanswer;
                        $answer->fraction = $question->fraction[$key];
                        $answer->feedback = $question->feedback[$key];
                        if (!update_record("quiz_answers", $answer)) {
                            $result->error = "Could not update quiz answer! (id=$answer->id)";
                            return $result;
                        }
                    } else {
                        unset($answer);
                        $answer->answer   = $dataanswer;
                        $answer->question = $question->id;
                        $answer->fraction = $question->fraction[$key];
                        $answer->feedback = $question->feedback[$key];
                        if (!$answer->id = insert_record("quiz_answers", $answer)) {
                            $result->error = "Could not insert quiz answer! ";
                            return $result;
                        }
                    }
                    $answers[] = $answer->id;

                    if ($question->fraction[$key] > 0) {                 // Sanity checks
                        $totalfraction += $question->fraction[$key];
                    }
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            if ($options = get_record("quiz_multichoice", "question", $question->id)) {
                $options->answers = implode(",",$answers);
                $options->single = $question->single;
                if (!update_record("quiz_multichoice", $options)) {
                    $result->error = "Could not update quiz multichoice options! (id=$options->id)";
                    return $result;
                }
            } else {
                unset($options);
                $options->question = $question->id;
                $options->answers = implode(",",$answers);
                $options->single = $question->single;
                if (!insert_record("quiz_multichoice", $options)) {
                    $result->error = "Could not insert quiz multichoice options!";
                    return $result;
                }
            }

            /// Perform sanity checks on fractional grades
            if ($options->single) {
                if ($maxfraction != 1) {
                    $maxfraction = $maxfraction * 100;
                    $result->noticeyesno = get_string("fractionsnomax", "quiz", $maxfraction);
                    return $result;
                }
            } else {
                $totalfraction = round($totalfraction,2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $result->noticeyesno = get_string("fractionsaddwrong", "quiz", $totalfraction);
                    return $result;
                }
            }
        break;

        case MATCH:

            if (!$oldsubquestions = get_records("quiz_match_sub", "question", $question->id, "id ASC")) {
                $oldsubquestions = array();
            }


            // following hack to check at least three answers exist
            $answercount = 0;
            foreach ($question->subquestions as $key=>$questiontext) {
                $answertext = $question->subanswers[$key];
                if (!empty($questiontext) and !empty($answertext)) {
                    $answercount++;
                }
            }
            $answercount += count($oldsubquestions);
            if ($answercount < 3) { // check there are at lest 3 answers for matching type questions
                $result->notice = get_string("notenoughanswers", "quiz", "3");
                return $result;
            }


            
            $subquestions = array();

            // Insert all the new question+answer pairs
            foreach ($question->subquestions as $key => $questiontext) {
                $answertext = $question->subanswers[$key];
                if (!empty($questiontext) and !empty($answertext)) {
                    if ($subquestion = array_shift($oldsubquestions)) {  // Existing answer, so reuse it
                        $subquestion->questiontext = $questiontext;
                        $subquestion->answertext   = $answertext;
                        if (!update_record("quiz_match_sub", $subquestion)) {
                            $result->error = "Could not insert quiz match subquestion! (id=$subquestion->id)";
                            return $result;
                        }
                    } else {
                        unset($subquestion);
                        $subquestion->question = $question->id;
                        $subquestion->questiontext = $questiontext;
                        $subquestion->answertext   = $answertext;
                        if (!$subquestion->id = insert_record("quiz_match_sub", $subquestion)) {
                            $result->error = "Could not insert quiz match subquestion!";
                            return $result;
                        }
                    }
                    $subquestions[] = $subquestion->id;
                }
            }

            if (count($subquestions) < 3) {
                $result->noticeyesno = get_string("notenoughsubquestions", "quiz");
                return $result;
            }

            if ($options = get_record("quiz_match", "question", $question->id)) {
                $options->subquestions = implode(",",$subquestions);
                if (!update_record("quiz_match", $options)) {
                    $result->error = "Could not update quiz match options! (id=$options->id)";
                    return $result;
                }
            } else {
                unset($options);
                $options->question = $question->id;
                $options->subquestions = implode(",",$subquestions);
                if (!insert_record("quiz_match", $options)) {
                    $result->error = "Could not insert quiz match options!";
                    return $result;
                }
            }

            break;


        case RANDOMSAMATCH:
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

        case MULTIANSWER:
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

        case RANDOM:
        break;

        case DESCRIPTION:
        break;

        default:
            $result->error = "Unsupported question type ($question->qtype)!";
            return $result;
        break;
    }
    return true;
}


function quiz_remove_unwanted_questions(&$questions, $quiz) {
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

function quiz_save_multianswer_alternatives
        ($questionid, $answertype, $alternatives, $oldalternativeids= NULL)
{
// Returns false if something goes wrong,
// otherwise the ids of the answers.

    if (empty($oldalternativeids)
        or !($oldalternatives =
                get_records_list('quiz_answers', 'id', $oldalternativeids)))
    {
        $oldalternatives = array();
    }

    $alternativeids = array();

    foreach ($alternatives as $altdata) {

        if ($altold = array_shift($oldalternatives)) { // Use existing one...
            $alt = $altold;
            $alt->answer = $altdata->answer;
            $alt->fraction = $altdata->fraction;
            $alt->feedback = $altdata->feedback;
            if (!update_record("quiz_answers", $alt)) {
                return false;
            }

        } else { // Completely new one
            unset($alt);
            $alt->question= $questionid;
            $alt->answer = $altdata->answer;
            $alt->fraction = $altdata->fraction;
            $alt->feedback = $altdata->feedback;
            if (!($alt->id = insert_record("quiz_answers", $alt))) {
                return false;
            }
        }

        // For the answer type numerical, each alternative has individual options:
        if ($answertype == NUMERICAL) {
            if ($numericaloptions =
                    get_record('quiz_numerical', 'answer', $alt->id))
            {
                // Reuse existing numerical options
                $numericaloptions->min = $altdata->min;
                $numericaloptions->max = $altdata->max;
                if (!update_record('quiz_numerical', $numericaloptions)) {
                    return false;
                }
            } else {
                // New numerical options
                $numericaloptions->answer = $alt->id;
                $numericaloptions->question = $questionid;
                $numericaloptions->min = $altdata->min;
                $numericaloptions->max = $altdata->max;
                if (!insert_record("quiz_numerical", $numericaloptions)) {
                    return false;
                }
            }
        } else { // Delete obsolete numerical options
            delete_records('quiz_numerical', 'answer', $alt->id);
        } // end if NUMERICAL

        $alternativeids[] = $alt->id;
    } // end foreach $alternatives
    $answers = implode(',', $alternativeids);

    // Removal of obsolete alternatives from answers and quiz_numerical:
    while ($altobsolete = array_shift($oldalternatives)) {
        delete_records("quiz_answers", "id", $altobsolete->id);
        
        // Possibly obsolute numerical options are also to be deleted:
        delete_records("quiz_numerical", 'answer', $altobsolete->id);
    }

    // Common alternative options and removal of obsolete options
    switch ($answertype) {
        case NUMERICAL:
            if (!empty($oldalternativeids)) {
                delete_records('quiz_shortanswer', 'answers', 
$oldalternativeids);
                delete_records('quiz_multichoice', 'answers', 
$oldalternativeids);
            }
            break;
        case SHORTANSWER:
            if (!empty($oldalternativeids)) {
                delete_records('quiz_multichoice', 'answers', 
$oldalternativeids);
                $options = get_record('quiz_shortanswer',
                                      'answers', $oldalternativeids);
            } else {
                unset($options);
            }
            if (empty($options)) {
                // Create new shortanswer options
                $options->question = $questionid;
                $options->usecase = 0;
                $options->answers = $answers;
                if (!insert_record('quiz_shortanswer', $options)) {
                    return false;
                }
            } else if ($answers != $oldalternativeids) {
                // Shortanswer options needs update:
                $options->answers = $answers;
                if (!update_record('quiz_shortanswer', $options)) {
                    return false;
                }
            }
            break;
        case MULTICHOICE:
            if (!empty($oldalternativeids)) {
                delete_records('quiz_shortanswer', 'answers', 
$oldalternativeids);
                $options = get_record('quiz_multichoice',
                                      'answers', $oldalternativeids);
            } else {
                unset($options);
            }
            if (empty($options)) {
                // Create new multichoice options
                $options->question = $questionid;
                $options->layout = 0;
                $options->single = 1;
                $options->answers = $answers;
                if (!insert_record('quiz_multichoice', $options)) {
                    return false;
                }
            } else if ($answers != $oldalternativeids) {
                // Multichoice options needs update:
                $options->answers = $answers;
                if (!update_record('quiz_multichoice', $options)) {
                    return false;
                }
            }
            break;
        default:
            return false;
    }
    return $answers;       
}

function quiz_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid, $quiz="0", $user="", $groupid="") {
// Returns all quizzes since a given time.  If quiz is specified then
// this restricts the results

    global $CFG;

    if ($quiz) {
        $quizselect = " AND cm.id = '$quiz'";
    } else {
        $quizselect = "";
    }
    if ($user) {
        $userselect = " AND u.id = '$user'";
    } else {
        $userselect = "";
    }

    $quizzes = get_records_sql("SELECT qa.*, q.name, u.firstname, u.lastname, u.picture,
                                       q.course, q.sumgrades as maxgrade, cm.instance, cm.section
                                  FROM {$CFG->prefix}quiz_attempts qa,
                                       {$CFG->prefix}quiz q,
                                       {$CFG->prefix}user u,
                                       {$CFG->prefix}course_modules cm
                                 WHERE qa.timefinish > '$sincetime'
                                   AND qa.userid = u.id $userselect
                                   AND qa.quiz = q.id $quizselect
                                   AND cm.instance = q.id
                                   AND cm.course = '$courseid'
                                   AND q.course = cm.course
                                 ORDER BY qa.timefinish ASC");

    if (empty($quizzes))
      return;

    foreach ($quizzes as $quiz) {
        if (empty($groupid) || ismember($groupid, $quiz->userid)) {

          $tmpactivity->type = "quiz";
          $tmpactivity->defaultindex = $index;
          $tmpactivity->instance = $quiz->quiz;

          $tmpactivity->name = $quiz->name;
          $tmpactivity->section = $quiz->section;

          $tmpactivity->content->attemptid = $quiz->id;
          $tmpactivity->content->sumgrades = $quiz->sumgrades;
          $tmpactivity->content->maxgrade = $quiz->maxgrade;
          $tmpactivity->content->attempt = $quiz->attempt;

          $tmpactivity->user->userid = $quiz->userid;
          $tmpactivity->user->fullname = fullname($quiz);
          $tmpactivity->user->picture = $quiz->picture;

          $tmpactivity->timestamp = $quiz->timefinish;

          $activities[] = $tmpactivity;

          $index++;
        }
    }

  return;
}


function quiz_print_recent_mod_activity($activity, $course, $detail=false) {
    global $CFG, $THEME;

    echo '<table border="0" cellpadding="3" cellspacing="0">';

    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td width=\"100%\"><font size=2>";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "height=16 width=16 alt=\"$activity->type\">  ";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/view.php?id=" . $activity->instance . "\">"
             . $activity->name . "</a> - ";

    }

    if (isteacher($USER)) {
        $grades = "(" .  $activity->content->sumgrades . " / " . $activity->content->maxgrade . ") ";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/review.php?q="
             . $activity->instance . "&attempt="
             . $activity->content->attemptid . "\">" . $grades . "</a> ";

        echo  get_string("attempt", "quiz") . " - " . $activity->content->attempt . "<br>";
    }
    echo "<a href=\"$CFG->wwwroot/user/view.php?id="
         . $activity->user->userid . "&course=$course\">"
         . $activity->user->fullname . "</a> ";

    echo " - " . userdate($activity->timestamp);

    echo "</font></td></tr>";
    echo "</table>";

    return;
}

?>
