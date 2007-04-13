<?php  // $Id$
/**
 * Code for handling and processing questions
 *
 * This is code that is module independent, i.e., can be used by any module that
 * uses questions, like quiz, lesson, ..
 * This script also loads the questiontype classes
 * Code for handling the editing of questions is in {@link question/editlib.php}
 *
 * TODO: separate those functions which form part of the API
 *       from the helper functions.
 *
 * @version $Id$
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

/// CONSTANTS ///////////////////////////////////

/**#@+
 * The different types of events that can create question states
 */
define('QUESTION_EVENTOPEN', '0');      // The state was created by Moodle
define('QUESTION_EVENTNAVIGATE', '1');  // The responses were saved because the student navigated to another page (this is not currently used)
define('QUESTION_EVENTSAVE', '2');      // The student has requested that the responses should be saved but not submitted or validated
define('QUESTION_EVENTGRADE', '3');     // Moodle has graded the responses. A SUBMIT event can be changed to a GRADE event by Moodle.
define('QUESTION_EVENTDUPLICATE', '4'); // The responses submitted were the same as previously
define('QUESTION_EVENTVALIDATE', '5');  // The student has requested a validation. This causes the responses to be saved as well, but not graded.
define('QUESTION_EVENTCLOSEANDGRADE', '6'); // Moodle has graded the responses. A CLOSE event can be changed to a CLOSEANDGRADE event by Moodle.
define('QUESTION_EVENTSUBMIT', '7');    // The student response has been submitted but it has not yet been marked
define('QUESTION_EVENTCLOSE', '8');     // The response has been submitted and the session has been closed, either because the student requested it or because Moodle did it (e.g. because of a timelimit). The responses have not been graded.
define('QUESTION_EVENTMANUALGRADE', '9');   // Grade was entered by teacher
/**#@-*/

/**#@+
 * The core question types.
 */
define("SHORTANSWER",   "shortanswer");
define("TRUEFALSE",     "truefalse");
define("MULTICHOICE",   "multichoice");
define("RANDOM",        "random");
define("MATCH",         "match");
define("RANDOMSAMATCH", "randomsamatch");
define("DESCRIPTION",   "description");
define("NUMERICAL",     "numerical");
define("MULTIANSWER",   "multianswer");
define("CALCULATED",    "calculated");
define("RQP",           "rqp");
define("ESSAY",         "essay");
/**#@-*/

/**
 * Constant determines the number of answer boxes supplied in the editing
 * form for multiple choice and similar question types.
 */
define("QUESTION_NUMANS", "10");

/**
 * Constant determines the number of answer boxes supplied in the editing
 * form for multiple choice and similar question types to start with, with
 * the option of adding QUESTION_NUMANS_ADD more answers.
 */
define("QUESTION_NUMANS_START", 3);

/**
 * Constant determines the number of answer boxes to add in the editing
 * form for multiple choice and similar question types when the user presses
 * 'add form fields button'.
 */
define("QUESTION_NUMANS_ADD", 3);

/**
 * The options used when popping up a question preview window in Javascript.
 */
define('QUESTION_PREVIEW_POPUP_OPTIONS', "'scrollbars=yes,resizable=yes,width=700,height=540'");

/**#@+
 * Option flags for ->optionflags
 * The options are read out via bitwise operation using these constants
 */
/**
 * Whether the questions is to be run in adaptive mode. If this is not set then
 * a question closes immediately after the first submission of responses. This
 * is how question is Moodle always worked before version 1.5
 */
define('QUESTION_ADAPTIVE', 1);

/**#@-*/

/// QTYPES INITIATION //////////////////
// These variables get initialised via calls to question_register_questiontype
// as the question type classes are included.
global $QTYPES, $QTYPE_MENU, $QTYPE_MANUAL, $QTYPE_EXCLUDE_FROM_RANDOM;
/**
 * Array holding question type objects
 */
$QTYPES = array();
/**
 * Array of question types names translated to the user's language
 *
 * The $QTYPE_MENU array holds the names of all the question types that the user should
 * be able to create directly. Some internal question types like random questions are excluded.
 * The complete list of question types can be found in {@link $QTYPES}.
 */
$QTYPE_MENU = array();
/**
 * String in the format "'type1','type2'" that can be used in SQL clauses like
 * "WHERE q.type IN ($QTYPE_MANUAL)".
 */
$QTYPE_MANUAL = '';
/**
 * String in the format "'type1','type2'" that can be used in SQL clauses like
 * "WHERE q.type NOT IN ($QTYPE_EXCLUDE_FROM_RANDOM)".
 */
$QTYPE_EXCLUDE_FROM_RANDOM = '';

/**
 * Add a new question type to the various global arrays above.
 *
 * @param object $qtype An instance of the new question type class.
 */
function question_register_questiontype($qtype) {
    global $QTYPES, $QTYPE_MENU, $QTYPE_MANUAL, $QTYPE_EXCLUDE_FROM_RANDOM;

    $name = $qtype->name();
    $QTYPES[$name] = $qtype;
    $menuname = $qtype->menu_name();
    if ($menuname) {
        $QTYPE_MENU[$name] = $menuname;
    }
    if ($qtype->is_manual_graded()) {
        if ($QTYPE_MANUAL) {
            $QTYPE_MANUAL .= ',';
        }
        $QTYPE_MANUAL .= "'$name'";
    }
    if (!$qtype->is_usable_by_random()) {
        if ($QTYPE_EXCLUDE_FROM_RANDOM) {
            $QTYPE_EXCLUDE_FROM_RANDOM .= ',';
        }
        $QTYPE_EXCLUDE_FROM_RANDOM .= "'$name'";
    }
}

require_once("$CFG->dirroot/question/type/questiontype.php");

// Load the questiontype.php file for each question type
// These files in turn call question_register_questiontype()
// with a new instance of each qtype class.
$qtypenames= get_list_of_plugins('question/type');
foreach($qtypenames as $qtypename) {
    // Instanciates all plug-in question types
    $qtypefilepath= "$CFG->dirroot/question/type/$qtypename/questiontype.php";

    // echo "Loading $qtypename<br/>"; // Uncomment for debugging
    if (is_readable($qtypefilepath)) {
        require_once($qtypefilepath);
    }
}

/// OTHER CLASSES /////////////////////////////////////////////////////////

/**
 * This holds the options that are set by the course module
 */
class cmoptions {
    /**
    * Whether a new attempt should be based on the previous one. If true
    * then a new attempt will start in a state where all responses are set
    * to the last responses from the previous attempt.
    */
    var $attemptonlast = false;

    /**
    * Various option flags. The flags are accessed via bitwise operations
    * using the constants defined in the CONSTANTS section above.
    */
    var $optionflags = QUESTION_ADAPTIVE;

    /**
    * Determines whether in the calculation of the score for a question
    * penalties for earlier wrong responses within the same attempt will
    * be subtracted.
    */
    var $penaltyscheme = true;

    /**
    * The maximum time the user is allowed to answer the questions withing
    * an attempt. This is measured in minutes so needs to be multiplied by
    * 60 before compared to timestamps. If set to 0 no timelimit will be applied
    */
    var $timelimit = 0;

    /**
    * Timestamp for the closing time. Responses submitted after this time will
    * be saved but no credit will be given for them.
    */
    var $timeclose = 9999999999;

    /**
    * The id of the course from withing which the question is currently being used
    */
    var $course = SITEID;

    /**
    * Whether the answers in a multiple choice question should be randomly
    * shuffled when a new attempt is started.
    */
    var $shuffleanswers = true;

    /**
    * The number of decimals to be shown when scores are printed
    */
    var $decimalpoints = 2;
}


/// FUNCTIONS //////////////////////////////////////////////////////

/**
 * Returns an array of names of activity modules that use this question
 *
 * @param object $questionid
 * @return array of strings
 */
function question_list_instances($questionid) {
    $instances = array();
    $modules = get_records('modules');
    foreach ($modules as $module) {
        $fn = $module->name.'_question_list_instances';
        if (function_exists($fn)) {
            $instances = $instances + $fn($questionid);
        }
    }
    return $instances;
}


/**
 * Returns list of 'allowed' grades for grade selection
 * formatted suitably for dropdown box function
 * @return object ->gradeoptionsfull full array ->gradeoptions +ve only
 */
function get_grade_options() {
    // define basic array of grades
    $grades = array(
        1,
        0.9,
        0.8,
        0.75,
        0.70,
        0.66666,
        0.60,
        0.50,
        0.40,
        0.33333,
        0.30,
        0.25,
        0.20,
        0.16666,
        0.142857,
        0.125,
        0.11111,
        0.10,
        0.05,
        0);

    // iterate through grades generating full range of options
    $gradeoptionsfull = array();
    $gradeoptions = array();
    foreach ($grades as $grade) {
        $percentage = 100 * $grade;
        $neggrade = -$grade;
        $gradeoptions["$grade"] = "$percentage %";
        $gradeoptionsfull["$grade"] = "$percentage %";
        $gradeoptionsfull["$neggrade"] = -$percentage." %";
    }
    $gradeoptionsfull["0"] = $gradeoptions["0"] = get_string("none");

    // sort lists
    arsort($gradeoptions, SORT_NUMERIC);
    arsort($gradeoptionsfull, SORT_NUMERIC);

    // construct return object
    $grades = new stdClass;
    $grades->gradeoptions = $gradeoptions;
    $grades->gradeoptionsfull = $gradeoptionsfull;

    return $grades;
}

/**
 * match grade options
 * if no match return error or match nearest
 * @param array $gradeoptionsfull list of valid options
 * @param int $grade grade to be tested
 * @param string $matchgrades 'error' or 'nearest'
 * @return mixed either 'fixed' value or false if erro
 */
function match_grade_options($gradeoptionsfull, $grade, $matchgrades='error') {
    // if we just need an error...
    if ($matchgrades=='error') {
        foreach($gradeoptionsfull as $value => $option) {
            // slightly fuzzy test, never check floats for equality :-)
            if (abs($grade-$value)<0.00001) {
                return $grade;
            }
        }
        // didn't find a match so that's an error
        return false;
    }
    // work out nearest value
    else if ($matchgrades=='nearest') {
        $hownear = array();
        foreach($gradeoptionsfull as $value => $option) {
            if ($grade==$value) {
                return $grade;
            }
            $hownear[ $value ] = abs( $grade - $value );
        }
        // reverse sort list of deltas and grab the last (smallest)
        asort( $hownear, SORT_NUMERIC );
        reset( $hownear );
        return key( $hownear );
    }
    else {
        return false;
    }
}

/**
 * Tests whether a category is in use by any activity module
 *
 * @return boolean
 * @param integer $categoryid
 * @param boolean $recursive Whether to examine category children recursively
 */
function question_category_isused($categoryid, $recursive = false) {

    //Look at each question in the category
    if ($questions = get_records('question', 'category', $categoryid)) {
        foreach ($questions as $question) {
            if (count(question_list_instances($question->id))) {
                return true;
            }
        }
    }

    //Look under child categories recursively
    if ($recursive) {
        if ($children = get_records('question_categories', 'parent', $categoryid)) {
            foreach ($children as $child) {
                if (question_category_isused($child->id, $recursive)) {
                    return true;
                }
            }
        }
    }

    return false;
}

/**
 * Deletes all data associated to an attempt from the database
 *
 * @param integer $attemptid The id of the attempt being deleted
 */
function delete_attempt($attemptid) {
    global $QTYPES;

    $states = get_records('question_states', 'attempt', $attemptid);
    if ($states) {
        $stateslist = implode(',', array_keys($states));
    
        // delete question-type specific data
        foreach ($QTYPES as $qtype) {
            $qtype->delete_states($stateslist);
        }
    }

    // delete entries from all other question tables
    // It is important that this is done only after calling the questiontype functions
    delete_records("question_states", "attempt", $attemptid);
    delete_records("question_sessions", "attemptid", $attemptid);
    delete_records("question_attempts", "id", $attemptid);
}

/**
 * Deletes question and all associated data from the database
 *
 * It will not delete a question if it is used by an activity module
 * @param object $question  The question being deleted
 */
function delete_question($questionid) {
    global $QTYPES;

    // Do not delete a question if it is used by an activity module
    if (count(question_list_instances($questionid))) {
        return;
    }

    // delete questiontype-specific data
    if ($question = get_record('question', 'id', $questionid)) {
        if (isset($QTYPES[$question->qtype])) {
            $QTYPES[$question->qtype]->delete_question($questionid);
        }
    } else {
        echo "Question with id $questionid does not exist.<br />";
    }

    if ($states = get_records('question_states', 'question', $questionid)) {
        $stateslist = implode(',', array_keys($states));

        // delete questiontype-specific data
        foreach ($QTYPES as $qtype) {
            $qtype->delete_states($stateslist);
        }
    }

    // delete entries from all other question tables
    // It is important that this is done only after calling the questiontype functions
    delete_records("question_answers", "question", $questionid);
    delete_records("question_states", "question", $questionid);
    delete_records("question_sessions", "questionid", $questionid);

    // Now recursively delete all child questions
    if ($children = get_records('question', 'parent', $questionid)) {
        foreach ($children as $child) {
            if ($child->id != $questionid) {
                delete_question($child->id);
            }
        }
    }

    // Finally delete the question record itself
    delete_records('question', 'id', $questionid);

    return;
}

/**
 * All non-used question categories and their questions are deleted and
 * categories still used by other courses are moved to the site course.
 *
 * @param object $course an object representing the course
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function question_delete_course($course, $feedback=true) {

    global $CFG, $QTYPES;

    //To detect if we have created the "container category"
    $concatid = 0;

    //The "container" category we'll create if we need if
    $contcat = new object;

    //To temporary store changes performed with parents
    $parentchanged = array();

    //To store feedback to be showed at the end of the process
    $feedbackdata   = array();

    //Cache some strings
    $strcatcontainer=get_string('containercategorycreated', 'quiz');
    $strcatmoved   = get_string('usedcategorymoved', 'quiz');
    $strcatdeleted = get_string('unusedcategorydeleted', 'quiz');

    if ($categories = get_records('question_categories', 'course', $course->id, 'parent', 'id, parent, name, course')) {

        //Sort categories following their tree (parent-child) relationships
        $categories = sort_categories_by_tree($categories);

        foreach ($categories as $cat) {

            //Get the full record
            $category = get_record('question_categories', 'id', $cat->id);

            //Check if the category is being used anywhere
            if(question_category_isused($category->id, true)) {
                //It's being used. Cannot delete it, so:
                //Create a container category in SITEID course if it doesn't exist
                if (!$concatid) {
                    $concat = new stdClass;
                    $concat->course = SITEID;
                    if (!isset($course->shortname)) {
                        $course->shortname = 'id=' . $course->id;
                    }
                    $concat->name = get_string('savedfromdeletedcourse', 'quiz', format_string($course->shortname));
                    $concat->info = $concat->name;
                    $concat->publish = 1;
                    $concat->stamp = make_unique_id_code();
                    $concatid = insert_record('question_categories', $concat);

                    //Fill feedback
                    $feedbackdata[] = array($concat->name, $strcatcontainer);
                }
                //Move the category to the container category in SITEID course
                $category->course = SITEID;
                //Assign to container if the category hasn't parent or if the parent is wrong (not belongs to the course)
                if (!$category->parent || !isset($categories[$category->parent])) {
                    $category->parent = $concatid;
                }
                //If it's being used, its publish field should be 1
                $category->publish = 1;
                //Let's update it
                update_record('question_categories', $category);

                //Save this parent change for future use
                $parentchanged[$category->id] = $category->parent;

                //Fill feedback
                $feedbackdata[] = array($category->name, $strcatmoved);

            } else {
                //Category isn't being used so:
                //Delete it completely (questions and category itself)
                //deleting questions
                if ($questions = get_records("question", "category", $category->id)) {
                    foreach ($questions as $question) {
                        delete_question($question->id);
                    }
                    delete_records("question", "category", $category->id);
                }
                //delete the category
                delete_records('question_categories', 'id', $category->id);

                //Save this parent change for future use
                if (!empty($category->parent)) {
                    $parentchanged[$category->id] = $category->parent;
                } else {
                    $parentchanged[$category->id] = $concatid;
                }

                //Update all its child categories to re-parent them to grandparent.
                set_field ('question_categories', 'parent', $parentchanged[$category->id], 'parent', $category->id);

                //Fill feedback
                $feedbackdata[] = array($category->name, $strcatdeleted);
            }
        }
        //Inform about changes performed if feedback is enabled
        if ($feedback) {
            $table = new stdClass;
            $table->head = array(get_string('category','quiz'), get_string('action'));
            $table->data = $feedbackdata;
            print_table($table);
        }
    }
    return true;
}

function questionbank_navigation_tabs(&$row, $context, $courseid) {
    global $CFG;
    if (has_capability('moodle/question:manage', $context)) {
        $row[] = new tabobject('questions', "$CFG->wwwroot/question/edit.php?courseid=$courseid", get_string('questions', 'quiz'), get_string('editquestions', "quiz"));
    }
    
    if (has_capability('moodle/question:managecategory', $context)) {
        $row[] = new tabobject('categories', "$CFG->wwwroot/question/category.php?id=$courseid", get_string('categories', 'quiz'), get_string('editqcats', 'quiz'));
    }
    
    if (has_capability('moodle/question:import', $context)) {
        $row[] = new tabobject('import', "$CFG->wwwroot/question/import.php?course=$courseid", get_string('import', 'quiz'), get_string('importquestions', 'quiz'));
    }
    
    if (has_capability('moodle/question:export', $context)) {
        $row[] = new tabobject('export', "$CFG->wwwroot/question/export.php?courseid=$courseid", get_string('export', 'quiz'), get_string('exportquestions', 'quiz'));
    }
}

/**
 * Private function to factor common code out of get_question_options().
 *
 * @param object $question the question to tidy.
 * @return boolean true if successful, else false.
 */
function _tidy_question(&$question) {
    global $QTYPES;
    if (!array_key_exists($question->qtype, $QTYPES)) {
        $question->qtype = 'missingtype';
        $question->questiontext = '<p>' . get_string('warningmissingtype', 'quiz') . '</p>' . $question->questiontext;
    }
    $question->name_prefix = question_make_name_prefix($question->id);
    return $QTYPES[$question->qtype]->get_question_options($question);
}

/**
 * Updates the question objects with question type specific
 * information by calling {@link get_question_options()}
 *
 * Can be called either with an array of question objects or with a single
 * question object.
 *
 * @param mixed $questions Either an array of question objects to be updated
 *         or just a single question object
 * @return bool Indicates success or failure.
 */
function get_question_options(&$questions) {
    if (is_array($questions)) { // deal with an array of questions
        foreach ($questions as $i => $notused) {
            if (!_tidy_question($questions[$i])) {
                return false;
            }
        }
        return true;
    } else { // deal with single question
        return _tidy_question($questions);
    }
}

/**
* Loads the most recent state of each question session from the database
* or create new one.
*
* For each question the most recent session state for the current attempt
* is loaded from the question_states table and the question type specific data and
* responses are added by calling {@link restore_question_state()} which in turn
* calls {@link restore_session_and_responses()} for each question.
* If no states exist for the question instance an empty state object is
* created representing the start of a session and empty question
* type specific information and responses are created by calling
* {@link create_session_and_responses()}.
*
* @return array           An array of state objects representing the most recent
*                         states of the question sessions.
* @param array $questions The questions for which sessions are to be restored or
*                         created.
* @param object $cmoptions
* @param object $attempt  The attempt for which the question sessions are
*                         to be restored or created.
* @param mixed either the id of a previous attempt, if this attmpt is
*                         building on a previous one, or false for a clean attempt.
*/
function get_question_states(&$questions, $cmoptions, $attempt, $lastattemptid = false) {
    global $CFG, $QTYPES;

    // get the question ids
    $ids = array_keys($questions);
    $questionlist = implode(',', $ids);

    // The question field must be listed first so that it is used as the
    // array index in the array returned by get_records_sql
    $statefields = 'n.questionid as question, s.*, n.sumpenalty, n.manualcomment';
    // Load the newest states for the questions
    $sql = "SELECT $statefields".
           "  FROM {$CFG->prefix}question_states s,".
           "       {$CFG->prefix}question_sessions n".
           " WHERE s.id = n.newest".
           "   AND n.attemptid = '$attempt->uniqueid'".
           "   AND n.questionid IN ($questionlist)";
    $states = get_records_sql($sql);

    // Load the newest graded states for the questions
    $sql = "SELECT $statefields".
           "  FROM {$CFG->prefix}question_states s,".
           "       {$CFG->prefix}question_sessions n".
           " WHERE s.id = n.newgraded".
           "   AND n.attemptid = '$attempt->uniqueid'".
           "   AND n.questionid IN ($questionlist)";
    $gradedstates = get_records_sql($sql);

    // loop through all questions and set the last_graded states
    foreach ($ids as $i) {
        if (isset($states[$i])) {
            restore_question_state($questions[$i], $states[$i]);
            if (isset($gradedstates[$i])) {
                restore_question_state($questions[$i], $gradedstates[$i]);
                $states[$i]->last_graded = $gradedstates[$i];
            } else {
                $states[$i]->last_graded = clone($states[$i]);
            }
        } else {
            // If the new attempt is to be based on a previous attempt get it and clean things
            // Having lastattemptid filled implies that (should we double check?):
            //    $attempt->attempt > 1 and $cmoptions->attemptonlast and !$attempt->preview
            if ($lastattemptid) {
                // find the responses from the previous attempt and save them to the new session

                // Load the last graded state for the question
                $statefields = 'n.questionid as question, s.*, n.sumpenalty';
                $sql = "SELECT $statefields".
                       "  FROM {$CFG->prefix}question_states s,".
                       "       {$CFG->prefix}question_sessions n".
                       " WHERE s.id = n.newgraded".
                       "   AND n.attemptid = '$lastattemptid'".
                       "   AND n.questionid = '$i'";
                if (!$laststate = get_record_sql($sql)) {
                    // Only restore previous responses that have been graded
                    continue;
                }
                // Restore the state so that the responses will be restored
                restore_question_state($questions[$i], $laststate);
                $states[$i] = clone ($laststate);
            } else {
               // create a new empty state
               $states[$i] = new object;
            }

            // now fill/overide initial values
            $states[$i]->attempt = $attempt->uniqueid;
            $states[$i]->question = (int) $i;
            $states[$i]->seq_number = 0;
            $states[$i]->timestamp = $attempt->timestart;
            $states[$i]->event = ($attempt->timefinish) ? QUESTION_EVENTCLOSE : QUESTION_EVENTOPEN;
            $states[$i]->grade = 0;
            $states[$i]->raw_grade = 0;
            $states[$i]->penalty = 0;
            $states[$i]->sumpenalty = 0;
            $states[$i]->manualcomment = '';

            // if building on last attempt we want to preserve responses  
            if (!$lastattemptid) {
              $states[$i]->responses = array('' => '');
            }
            // Prevent further changes to the session from incrementing the
            // sequence number
            $states[$i]->changed = true;

            if ($lastattemptid) {
                // prepare the previous responses for new processing
                $action = new stdClass;
                $action->responses = $laststate->responses;
                $action->timestamp = $laststate->timestamp;
                $action->event = QUESTION_EVENTSAVE; //emulate save of questions from all pages MDL-7631

                // Process these responses ...
                question_process_responses($questions[$i], $states[$i], $action, $cmoptions, $attempt);

                // Fix for Bug #5506: When each attempt is built on the last one,
                // preserve the options from any previous attempt. 
                if ( isset($laststate->options) ) {
                    $states[$i]->options = $laststate->options;
                }
            } else {
                // Create the empty question type specific information
                if (!$QTYPES[$questions[$i]->qtype]->create_session_and_responses(
                        $questions[$i], $states[$i], $cmoptions, $attempt)) {
                    return false;
                }
            }
            $states[$i]->last_graded = clone($states[$i]);
        }
    }
    return $states;
}


/**
* Creates the run-time fields for the states
*
* Extends the state objects for a question by calling
* {@link restore_session_and_responses()}
* @param object $question The question for which the state is needed
* @param object $state The state as loaded from the database
* @return boolean Represents success or failure
*/
function restore_question_state(&$question, &$state) {
    global $QTYPES;

    // initialise response to the value in the answer field
    $state->responses = array('' => addslashes($state->answer));
    unset($state->answer);
    $state->manualcomment = isset($state->manualcomment) ? addslashes($state->manualcomment) : '';

    // Set the changed field to false; any code which changes the
    // question session must set this to true and must increment
    // ->seq_number. The save_question_session
    // function will save the new state object to the database if the field is
    // set to true.
    $state->changed = false;

    // Load the question type specific data
    return $QTYPES[$question->qtype]
            ->restore_session_and_responses($question, $state);

}

/**
* Saves the current state of the question session to the database
*
* The state object representing the current state of the session for the
* question is saved to the question_states table with ->responses[''] saved
* to the answer field of the database table. The information in the
* question_sessions table is updated.
* The question type specific data is then saved.
* @return mixed           The id of the saved or updated state or false
* @param object $question The question for which session is to be saved.
* @param object $state    The state information to be saved. In particular the
*                         most recent responses are in ->responses. The object
*                         is updated to hold the new ->id.
*/
function save_question_session(&$question, &$state) {
    global $QTYPES;
    // Check if the state has changed
    if (!$state->changed && isset($state->id)) {
        return $state->id;
    }
    // Set the legacy answer field
    $state->answer = isset($state->responses['']) ? $state->responses[''] : '';

    // Save the state
    if (!empty($state->update)) { // this forces the old state record to be overwritten
        update_record('question_states', $state);
    } else {
        if (!$state->id = insert_record('question_states', $state)) {
            unset($state->id);
            unset($state->answer);
            return false;
        }
    }

    // create or update the session
    if (!$session = get_record('question_sessions', 'attemptid',
            $state->attempt, 'questionid', $question->id)) {
        $session->attemptid = $state->attempt;
        $session->questionid = $question->id;
        $session->newest = $state->id;
        // The following may seem weird, but the newgraded field needs to be set
        // already even if there is no graded state yet.
        $session->newgraded = $state->id;
        $session->sumpenalty = $state->sumpenalty;
        $session->manualcomment = $state->manualcomment;
        if (!insert_record('question_sessions', $session)) {
            error('Could not insert entry in question_sessions');
        }
    } else {
        $session->newest = $state->id;
        if (question_state_is_graded($state) or $state->event == QUESTION_EVENTOPEN) {
            // this state is graded or newly opened, so it goes into the lastgraded field as well
            $session->newgraded = $state->id;
            $session->sumpenalty = $state->sumpenalty;
            $session->manualcomment = $state->manualcomment;
        } else {
            $session->manualcomment = addslashes($session->manualcomment);
        }
        update_record('question_sessions', $session);
    }

    unset($state->answer);

    // Save the question type specific state information and responses
    if (!$QTYPES[$question->qtype]->save_session_and_responses(
     $question, $state)) {
        return false;
    }
    // Reset the changed flag
    $state->changed = false;
    return $state->id;
}

/**
* Determines whether a state has been graded by looking at the event field
*
* @return boolean         true if the state has been graded
* @param object $state
*/
function question_state_is_graded($state) {
    return ($state->event == QUESTION_EVENTGRADE
         or $state->event == QUESTION_EVENTCLOSEANDGRADE
         or $state->event == QUESTION_EVENTMANUALGRADE);
}

/**
* Determines whether a state has been closed by looking at the event field
*
* @return boolean         true if the state has been closed
* @param object $state
*/
function question_state_is_closed($state) {
    return ($state->event == QUESTION_EVENTCLOSE
        or $state->event == QUESTION_EVENTCLOSEANDGRADE
        or $state->event == QUESTION_EVENTMANUALGRADE);
}


/**
 * Extracts responses from submitted form
 *
 * This can extract the responses given to one or several questions present on a page
 * It returns an array with one entry for each question, indexed by question id
 * Each entry is an object with the properties
 *  ->event     The event that has triggered the submission. This is determined by which button
 *               the user has pressed.
 *  ->responses An array holding the responses to an individual question, indexed by the
 *               name of the corresponding form element.
 *  ->timestamp A unix timestamp
 * @return array            array of action objects, indexed by question ids.
 * @param array $questions  an array containing at least all questions that are used on the form
 * @param array $formdata   the data submitted by the form on the question page
 * @param integer $defaultevent  the event type used if no 'mark' or 'validate' is submitted
 */
function question_extract_responses($questions, $formdata, $defaultevent=QUESTION_EVENTSAVE) {

    $time = time();
    $actions = array();
    foreach ($formdata as $key => $response) {
        // Get the question id from the response name
        if (false !== ($quid = question_get_id_from_name_prefix($key))) {
            // check if this is a valid id
            if (!isset($questions[$quid])) {
                error('Form contained question that is not in questionids');
            }

            // Remove the name prefix from the name
            //decrypt trying
            $key = substr($key, strlen($questions[$quid]->name_prefix));
            if (false === $key) {
                $key = '';
            }
            // Check for question validate and mark buttons & set events
            if ($key === 'validate') {
                $actions[$quid]->event = QUESTION_EVENTVALIDATE;
            } else if ($key === 'submit') {
                $actions[$quid]->event = QUESTION_EVENTSUBMIT;
            } else {
                $actions[$quid]->event = $defaultevent;
            }

            // Update the state with the new response
            $actions[$quid]->responses[$key] = $response;

            // Set the timestamp
            $actions[$quid]->timestamp = $time;
        }
    }
    foreach ($actions as $quid => $notused) {
        ksort($actions[$quid]->responses);
    }
    return $actions;
}


/**
 * Returns the html for question feedback image.
 * @param float   $fraction  value representing the correctness of the user's
 *                           response to a question.
 * @param boolean $selected  whether or not the answer is the one that the
 *                           user picked.
 * @return string
 */
function question_get_feedback_image($fraction, $selected=true) {

    global $CFG;

    if ($fraction >= 1.0) {
        if ($selected) {
            $feedbackimg = '<img src="'.$CFG->pixpath.'/i/tick_green_big.gif" '.
                            'alt="'.get_string('correct', 'quiz').'" class="icon" />';
        } else {
            $feedbackimg = '<img src="'.$CFG->pixpath.'/i/tick_green_small.gif" '.
                            'alt="'.get_string('correct', 'quiz').'" class="icon" />';
        }
    } else if ($fraction > 0.0 && $fraction < 1.0) {
        if ($selected) {
            $feedbackimg = '<img src="'.$CFG->pixpath.'/i/tick_amber_big.gif" '.
                            'alt="'.get_string('partiallycorrect', 'quiz').'" class="icon" />';
        } else {
            $feedbackimg = '<img src="'.$CFG->pixpath.'/i/tick_amber_small.gif" '.
                            'alt="'.get_string('partiallycorrect', 'quiz').'" class="icon" />';
        }
    } else {
        if ($selected) {
            $feedbackimg = '<img src="'.$CFG->pixpath.'/i/cross_red_big.gif" '.
                            'alt="'.get_string('incorrect', 'quiz').'" class="icon" />';
        } else {
            $feedbackimg = '<img src="'.$CFG->pixpath.'/i/cross_red_small.gif" '.
                            'alt="'.get_string('incorrect', 'quiz').'" class="icon" />';
        }
    }
    return $feedbackimg;
}


/**
 * Returns the class name for question feedback.
 * @param float  $fraction  value representing the correctness of the user's
 *                          response to a question.
 * @return string
 */
function question_get_feedback_class($fraction) {

    global $CFG;

    if ($fraction >= 1.0) {
        $class = 'correct';
    } else if ($fraction > 0.0 && $fraction < 1.0) {
        $class = 'partiallycorrect';
    } else {
        $class = 'incorrect';
    }
    return $class;
}


/**
* For a given question in an attempt we walk the complete history of states
* and recalculate the grades as we go along.
*
* This is used when a question is changed and old student
* responses need to be marked with the new version of a question.
*
* TODO: Make sure this is not quiz-specific
*
* @return boolean            Indicates whether the grade has changed
* @param object  $question   A question object
* @param object  $attempt    The attempt, in which the question needs to be regraded.
* @param object  $cmoptions
* @param boolean $verbose    Optional. Whether to print progress information or not.
*/
function regrade_question_in_attempt($question, $attempt, $cmoptions, $verbose=false) {

    // load all states for this question in this attempt, ordered in sequence
    if ($states = get_records_select('question_states',
            "attempt = '{$attempt->uniqueid}' AND question = '{$question->id}'",
            'seq_number ASC')) {
        $states = array_values($states);

        // Subtract the grade for the latest state from $attempt->sumgrades to get the
        // sumgrades for the attempt without this question.
        $attempt->sumgrades -= $states[count($states)-1]->grade;

        // Initialise the replaystate
        $state = clone($states[0]);
        $state->manualcomment = get_field('question_sessions', 'manualcomment', 'attemptid',
                $attempt->uniqueid, 'questionid', $question->id);
        restore_question_state($question, $state);
        $state->sumpenalty = 0.0;
        $replaystate = clone($state);
        $replaystate->last_graded = $state;

        $changed = false;
        for($j = 1; $j < count($states); $j++) {
            restore_question_state($question, $states[$j]);
            $action = new stdClass;
            $action->responses = $states[$j]->responses;
            $action->timestamp = $states[$j]->timestamp;

            // Change event to submit so that it will be reprocessed
            if (QUESTION_EVENTCLOSE == $states[$j]->event
                    or QUESTION_EVENTGRADE == $states[$j]->event
                    or QUESTION_EVENTCLOSEANDGRADE == $states[$j]->event) {
                $action->event = QUESTION_EVENTSUBMIT;

            // By default take the event that was saved in the database
            } else {
                $action->event = $states[$j]->event;
            }

            if ($action->event == QUESTION_EVENTMANUALGRADE) {
                question_process_comment($question, $replaystate, $attempt,
                        $replaystate->manualcomment, $states[$j]->grade);
            } else {

                // Reprocess (regrade) responses
                if (!question_process_responses($question, $replaystate,
                        $action, $cmoptions, $attempt)) {
                    $verbose && notify("Couldn't regrade state #{$state->id}!");
                }
            }

            // We need rounding here because grades in the DB get truncated
            // e.g. 0.33333 != 0.3333333, but we want them to be equal here
            if ((round((float)$replaystate->raw_grade, 5) != round((float)$states[$j]->raw_grade, 5))
                    or (round((float)$replaystate->penalty, 5) != round((float)$states[$j]->penalty, 5))
                    or (round((float)$replaystate->grade, 5) != round((float)$states[$j]->grade, 5))) {
                $changed = true;
            }

            $replaystate->id = $states[$j]->id;
            $replaystate->changed = true;
            $replaystate->update = true; // This will ensure that the existing database entry is updated rather than a new one created
            save_question_session($question, $replaystate);
        }
        if ($changed) {
            // TODO, call a method in quiz to do this, where 'quiz' comes from
            // the question_attempts table.
            update_record('quiz_attempts', $attempt);
        }

        return $changed;
    }
    return false;
}

/**
* Processes an array of student responses, grading and saving them as appropriate
*
* @return boolean         Indicates success/failure
* @param object $question Full question object, passed by reference
* @param object $state    Full state object, passed by reference
* @param object $action   object with the fields ->responses which
*                         is an array holding the student responses,
*                         ->action which specifies the action, e.g., QUESTION_EVENTGRADE,
*                         and ->timestamp which is a timestamp from when the responses
*                         were submitted by the student.
* @param object $cmoptions
* @param object $attempt  The attempt is passed by reference so that
*                         during grading its ->sumgrades field can be updated
*/
function question_process_responses(&$question, &$state, $action, $cmoptions, &$attempt) {
    global $QTYPES;

    // if no responses are set initialise to empty response
    if (!isset($action->responses)) {
        $action->responses = array('' => '');
    }

    // make sure these are gone!
    unset($action->responses['submit'], $action->responses['validate']);

    // Check the question session is still open
    if (question_state_is_closed($state)) {
        return true;
    }

    // If $action->event is not set that implies saving
    if (! isset($action->event)) {
        debugging('Ambiguous action in question_process_responses.' , DEBUG_DEVELOPER);
        $action->event = QUESTION_EVENTSAVE;
    }
    // If submitted then compare against last graded
    // responses, not last given responses in this case
    if (question_isgradingevent($action->event)) {
        $state->responses = $state->last_graded->responses;
    }

    // Check for unchanged responses (exactly unchanged, not equivalent).
    // We also have to catch questions that the student has not yet attempted
    $sameresponses = $QTYPES[$question->qtype]->compare_responses($question, $action, $state);
    if ($state->last_graded->event == QUESTION_EVENTOPEN && question_isgradingevent($action->event)) {
        $sameresponses = false;
    }

    // If the response has not been changed then we do not have to process it again
    // unless the attempt is closing or validation is requested
    if ($sameresponses and QUESTION_EVENTCLOSE != $action->event
            and QUESTION_EVENTVALIDATE != $action->event) {
        return true;
    }

    // Roll back grading information to last graded state and set the new
    // responses
    $newstate = clone($state->last_graded);
    $newstate->responses = $action->responses;
    $newstate->seq_number = $state->seq_number + 1;
    $newstate->changed = true; // will assure that it gets saved to the database
    $newstate->last_graded = clone($state->last_graded);
    $newstate->timestamp = $action->timestamp;
    $state = $newstate;

    // Set the event to the action we will perform. The question type specific
    // grading code may override this by setting it to QUESTION_EVENTCLOSE if the
    // attempt at the question causes the session to close
    $state->event = $action->event;

    if (!question_isgradingevent($action->event)) {
        // Grade the response but don't update the overall grade
        $QTYPES[$question->qtype]->grade_responses(
         $question, $state, $cmoptions);
        // Don't allow the processing to change the event type
        $state->event = $action->event;

    } else { // grading event

        // Unless the attempt is closing, we want to work out if the current responses
        // (or equivalent responses) were already given in the last graded attempt.
        if(QUESTION_EVENTCLOSE != $action->event && QUESTION_EVENTOPEN != $state->last_graded->event &&
                $QTYPES[$question->qtype]->compare_responses($question, $state, $state->last_graded)) {
            $state->event = QUESTION_EVENTDUPLICATE;
        }

        // If we did not find a duplicate or if the attempt is closing, perform grading
        if ((!$sameresponses and QUESTION_EVENTDUPLICATE != $state->event) or
                QUESTION_EVENTCLOSE == $action->event) {
            // Decrease sumgrades by previous grade and then later add new grade
            $attempt->sumgrades -= (float)$state->last_graded->grade;

            $QTYPES[$question->qtype]->grade_responses(
             $question, $state, $cmoptions);
            // Calculate overall grade using correct penalty method
            question_apply_penalty_and_timelimit($question, $state, $attempt, $cmoptions);

            $attempt->sumgrades += (float)$state->grade;
        }

        // If the state was graded we need to update the last_graded field.
        if (question_state_is_graded($state)) {
            unset($state->last_graded);
            $state->last_graded = clone($state);
            unset($state->last_graded->changed);
        }
    }
    $attempt->timemodified = $action->timestamp;

    return true;
}

/**
* Determine if event requires grading
*/
function question_isgradingevent($event) {
    return (QUESTION_EVENTSUBMIT == $event || QUESTION_EVENTCLOSE == $event);
}

/**
* Applies the penalty from the previous graded responses to the raw grade
* for the current responses
*
* The grade for the question in the current state is computed by subtracting the
* penalty accumulated over the previous graded responses at the question from the
* raw grade. If the timestamp is more than 1 minute beyond the end of the attempt
* the grade is set to zero. The ->grade field of the state object is modified to
* reflect the new grade but is never allowed to decrease.
* @param object $question The question for which the penalty is to be applied.
* @param object $state    The state for which the grade is to be set from the
*                         raw grade and the cumulative penalty from the last
*                         graded state. The ->grade field is updated by applying
*                         the penalty scheme determined in $cmoptions to the ->raw_grade and
*                         ->last_graded->penalty fields.
* @param object $cmoptions  The options set by the course module.
*                           The ->penaltyscheme field determines whether penalties
*                           for incorrect earlier responses are subtracted.
*/
function question_apply_penalty_and_timelimit(&$question, &$state, $attempt, $cmoptions) {
    // TODO. Quiz dependancy. The fact that the attempt that is passed in here
    // is from quiz_attempts, and we use things like $cmoptions->timelimit.
    
    // deal with penalty
    if ($cmoptions->penaltyscheme) {
            $state->grade = $state->raw_grade - $state->sumpenalty;
            $state->sumpenalty += (float) $state->penalty;
    } else {
        $state->grade = $state->raw_grade;
    }

    // deal with timelimit
    if ($cmoptions->timelimit) {
        // We allow for 5% uncertainty in the following test
        if ($state->timestamp - $attempt->timestart > $cmoptions->timelimit * 63) {
            $cm = get_coursemodule_from_instance('quiz', $cmoptions->id);
            if (!has_capability('mod/quiz:ignoretimelimits', get_context_instance(CONTEXT_MODULE, $cm->id),
                    $attempt->userid, false)) {
                $state->grade = 0;
            }
        }
    }

    // deal with closing time
    if ($cmoptions->timeclose and $state->timestamp > ($cmoptions->timeclose + 60) // allowing 1 minute lateness
             and !$attempt->preview) { // ignore closing time for previews
        $state->grade = 0;
    }

    // Ensure that the grade does not go down
    $state->grade = max($state->grade, $state->last_graded->grade);
}

/**
* Print the icon for the question type
*
* @param object $question  The question object for which the icon is required
* @param boolean $return   If true the functions returns the link as a string
*/
function print_question_icon($question, $return = false) {
    global $QTYPES, $CFG;

    $namestr = $QTYPES[$question->qtype]->menu_name();
    $html = '<img src="' . $CFG->wwwroot . '/question/type/' .
            $question->qtype . '/icon.gif" alt="' .
            $namestr . '" title="' . $namestr . '" />';
    if ($return) {
        return $html;
    } else {
        echo $html;
    }
}

/**
* Returns a html link to the question image if there is one
*
* @return string The html image tag or the empy string if there is no image.
* @param object $question The question object
*/
function get_question_image($question, $courseid) {

    global $CFG;
    $img = '';

    if ($question->image) {

        if (substr(strtolower($question->image), 0, 7) == 'http://') {
            $img .= $question->image;

        } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $img .= "$CFG->wwwroot/file.php/$courseid/$question->image";

        } else {
            $img .= "$CFG->wwwroot/file.php?file=/$courseid/$question->image";
        }
    }
    return $img;
}

function question_print_comment_box($question, $state, $attempt, $url) {
   global $CFG;

   $prefix = 'response';
   $usehtmleditor = can_use_richtext_editor();
   $grade = round($state->last_graded->grade, 3);
   echo '<form method="post" action="'.$url.'">';
   include($CFG->dirroot.'/question/comment.html');
   echo '<input type="hidden" name="attempt" value="'.$attempt->uniqueid.'" />';
   echo '<input type="hidden" name="question" value="'.$question->id.'" />';
   echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
   echo '<input type="submit" name="submit" value="'.get_string('save', 'quiz').'" />';
   echo '</form>';

   if ($usehtmleditor) {
       use_html_editor();
   }
}

function question_process_comment($question, &$state, &$attempt, $comment, $grade) {

    // Update the comment and save it in the database
    $state->manualcomment = $comment;
    if (!set_field('question_sessions', 'manualcomment', $comment, 'attemptid', $attempt->uniqueid, 'questionid', $question->id)) {
        error("Cannot save comment");
    }

    // Update the attempt if the score has changed.
    if (abs($state->last_graded->grade - $grade) > 0.002) {
        $attempt->sumgrades = $attempt->sumgrades - $state->last_graded->grade + $grade;
        $attempt->timemodified = time();
        if (!update_record('quiz_attempts', $attempt)) {
            error('Failed to save the current quiz attempt!');
        }
    }

    // Update the state if either the score has changed, or this is the first
    // manual grade event.
    // We don't need to store the modified state in the database, we just need
    // to set the $state->changed flag.
    if (abs($state->last_graded->grade - $grade) > 0.002 ||
            $state->last_graded->event != QUESTION_EVENTMANUALGRADE) {

        // We want to update existing state (rather than creating new one) if it
        // was itself created by a manual grading event.
        $state->update = ($state->event == QUESTION_EVENTMANUALGRADE) ? 1 : 0;

        // Update the other parts of the state object.
        $state->raw_grade = $grade;
        $state->grade = $grade;
        $state->penalty = 0;
        $state->timestamp = time();
        $state->seq_number++;
        $state->event = QUESTION_EVENTMANUALGRADE;

        // Update the last graded state (don't simplify!)
        unset($state->last_graded);
        $state->last_graded = clone($state);

        // We need to indicate that the state has changed in order for it to be saved.
        $state->changed = 1;
    }

}

/**
* Construct name prefixes for question form element names
*
* Construct the name prefix that should be used for example in the
* names of form elements created by questions.
* This is called by {@link get_question_options()}
* to set $question->name_prefix.
* This name prefix includes the question id which can be
* extracted from it with {@link question_get_id_from_name_prefix()}.
*
* @return string
* @param integer $id  The question id
*/
function question_make_name_prefix($id) {
    return 'resp' . $id . '_';
}

/**
* Extract question id from the prefix of form element names
*
* @return integer      The question id
* @param string $name  The name that contains a prefix that was
*                      constructed with {@link question_make_name_prefix()}
*/
function question_get_id_from_name_prefix($name) {
    if (!preg_match('/^resp([0-9]+)_/', $name, $matches))
        return false;
    return (integer) $matches[1];
}

/**
 * Returns the unique id for a new attempt
 *
 * Every module can keep their own attempts table with their own sequential ids but
 * the question code needs to also have a unique id by which to identify all these
 * attempts. Hence a module, when creating a new attempt, calls this function and
 * stores the return value in the 'uniqueid' field of its attempts table.
 */
function question_new_attempt_uniqueid($modulename='quiz') {
    global $CFG;
    $attempt = new stdClass;
    $attempt->modulename = $modulename;
    if (!$id = insert_record('question_attempts', $attempt)) {
        error('Could not create new entry in question_attempts table');
    }
    return $id;
}

/**
 * Creates a stamp that uniquely identifies this version of the question
 *
 * In future we want this to use a hash of the question data to guarantee that
 * identical versions have the same version stamp.
 *
 * @param object $question
 * @return string A unique version stamp
 */
function question_hash($question) {
    return make_unique_id_code();
}


/// FUNCTIONS THAT SIMPLY WRAP QUESTIONTYPE METHODS //////////////////////////////////
/**
 * Get the HTML that needs to be included in the head tag when the
 * questions in $questionlist are printed in the gives states.
 *
 * @param array $questionlist a list of questionids of the questions what will appear on this page.
 * @param array $questions an array of question objects, whose keys are question ids.
 *      Must contain all the questions in $questionlist
 * @param array $states an array of question state objects, whose keys are question ids.
 *      Must contain the state of all the questions in $questionlist
 *
 * @return string some HTML code that can go inside the head tag.
 */
function get_html_head_contributions(&$questionlist, &$questions, &$states) {
    global $QTYPES;

    $contributions = array();
    foreach ($questionlist as $questionid) {
        $question = $questions[$questionid];
        $contributions = array_merge($contributions,
                $QTYPES[$question->qtype]->get_html_head_contributions(
                $question, $states[$questionid]));
    }
    return implode("\n", array_unique($contributions));
}

/**
 * Prints a question
 *
 * Simply calls the question type specific print_question() method.
 * @param object $question The question to be rendered.
 * @param object $state    The state to render the question in.
 * @param integer $number  The number for this question.
 * @param object $cmoptions  The options specified by the course module
 * @param object $options  An object specifying the rendering options.
 */
function print_question(&$question, &$state, $number, $cmoptions, $options=null) {
    global $QTYPES;

    $QTYPES[$question->qtype]->print_question($question, $state, $number,
     $cmoptions, $options);
}
/**
 * Saves question options
 *
 * Simply calls the question type specific save_question_options() method.
 */
function save_question_options($question) {
    global $QTYPES;

    $QTYPES[$question->qtype]->save_question_options($question);
}

/**
* Gets all teacher stored answers for a given question
*
* Simply calls the question type specific get_all_responses() method.
*/
// ULPGC ecastro
function get_question_responses($question, $state) {
    global $QTYPES;
    $r = $QTYPES[$question->qtype]->get_all_responses($question, $state);
    return $r;
}


/**
* Gets the response given by the user in a particular state
*
* Simply calls the question type specific get_actual_response() method.
*/
// ULPGC ecastro
function get_question_actual_response($question, $state) {
    global $QTYPES;

    $r = $QTYPES[$question->qtype]->get_actual_response($question, $state);
    return $r;
}

/**
* TODO: document this
*/
// ULPGc ecastro
function get_question_fraction_grade($question, $state) {
    global $QTYPES;

    $r = $QTYPES[$question->qtype]->get_fractional_grade($question, $state);
    return $r;
}


/// CATEGORY FUNCTIONS /////////////////////////////////////////////////////////////////

/**
 * returns the categories with their names ordered following parent-child relationships
 * finally it tries to return pending categories (those being orphaned, whose parent is
 * incorrect) to avoid missing any category from original array.
 */
function sort_categories_by_tree(&$categories, $id = 0, $level = 1) {
    $children = array();
    $keys = array_keys($categories);

    foreach ($keys as $key) {
        if (!isset($categories[$key]->processed) && $categories[$key]->parent == $id) {
            $children[$key] = $categories[$key];
            $categories[$key]->processed = true;
            $children = $children + sort_categories_by_tree($categories, $children[$key]->id, $level+1);
        }
    }
    //If level = 1, we have finished, try to look for non processed categories (bad parent) and sort them too
    if ($level == 1) {
        foreach ($keys as $key) {
            //If not processed and it's a good candidate to start (because its parent doesn't exist in the course)
            if (!isset($categories[$key]->processed) && !record_exists('question_categories', 'course', $categories[$key]->course, 'id', $categories[$key]->parent)) {
                $children[$key] = $categories[$key];
                $categories[$key]->processed = true;
                $children = $children + sort_categories_by_tree($categories, $children[$key]->id, $level+1);
            }
        }
    }
    return $children;
}

/**
 * Private method, only for the use of add_indented_names().
 *
 * Recursively adds an indentedname field to each category, starting with the category
 * with id $id, and dealing with that category and all its children, and
 * return a new array, with those categories in the right order.
 *
 * @param array $categories an array of categories which has had childids
 *          fields added by flatten_category_tree(). Passed by reference for
 *          performance only. It is not modfied.
 * @param int $id the category to start the indenting process from.
 * @param int $depth the indent depth. Used in recursive calls.
 * @return array a new array of categories, in the right order for the tree.
 */
function flatten_category_tree(&$categories, $id, $depth = 0) {

    // Indent the name of this category.
    $newcategories = array();
    $newcategories[$id] = $categories[$id];
    $newcategories[$id]->indentedname = str_repeat('&nbsp;&nbsp;&nbsp;', $depth) . $categories[$id]->name;

    // Recursively indent the children.
    foreach ($categories[$id]->childids as $childid) {
        $newcategories = $newcategories + flatten_category_tree($categories, $childid, $depth + 1);
    }

    // Remove the childids array that were temporarily added.
    unset($newcategories[$id]->childids);

    return $newcategories;
}

/**
 * Format categories into an indented list reflecting the tree structure.
 *
 * @param array $categories An array of category objects, for example from the.
 * @return array The formatted list of categories.
 */
function add_indented_names($categories) {

    // Add an array to each category to hold the child category ids. This array will be removed
    // again by flatten_category_tree(). It should not be used outside these two functions.
    foreach (array_keys($categories) as $id) {
        $categories[$id]->childids = array();
    }

    // Build the tree structure, and record which categories are top-level.
    // We have to be careful, because the categories array may include published
    // categories from other courses, but not their parents.
    $toplevelcategoryids = array();
    foreach (array_keys($categories) as $id) {
        if (!empty($categories[$id]->parent) && array_key_exists($categories[$id]->parent, $categories)) {
            $categories[$categories[$id]->parent]->childids[] = $id;
        } else {
            $toplevelcategoryids[] = $id;
        }
    }

    // Flatten the tree to and add the indents.
    $newcategories = array();
    foreach ($toplevelcategoryids as $id) {
        $newcategories = $newcategories + flatten_category_tree($categories, $id);
    }

    return $newcategories;
}

/**
 * Output a select menu of question categories.
 *
 * Categories from this course and (optionally) published categories from other courses
 * are included. Optionally, only categories the current user may edit can be included.
 *
 * @param integer $courseid the id of the course to get the categories for.
 * @param integer $published if true, include publised categories from other courses.
 * @param integer $only_editable if true, exclude categories this user is not allowed to edit.
 * @param integer $selected optionally, the id of a category to be selected by default in the dropdown.
 */
function question_category_select_menu($courseid, $published = false, $only_editable = false, $selected = "") {
    $categoriesarray = question_category_options($courseid, $published, $only_editable);
    if ($selected) {
        $nothing = '';
    } else {
        $nothing = 'choose';
    }
    choose_from_menu($categoriesarray, 'category', $selected, $nothing);
}

/**
 * Output an array of question categories.
 *
 * Categories from this course and (optionally) published categories from other courses
 * are included. Optionally, only categories the current user may edit can be included.
 *
 * @param integer $courseid the id of the course to get the categories for.
 * @param integer $published if true, include publised categories from other courses.
 * @param integer $only_editable if true, exclude categories this user is not allowed to edit.
 * @return array The list of categories.
 */
function question_category_options($courseid, $published = false, $only_editable = false) {
    global $CFG;

    // get sql fragment for published
    $publishsql="";
    if ($published) {
        $publishsql = " OR publish = 1";
    }

    $categories = get_records_sql("
            SELECT cat.*, c.shortname AS coursename
            FROM {$CFG->prefix}question_categories cat, {$CFG->prefix}course c
            WHERE c.id = cat.course AND (cat.course = $courseid $publishsql)
            ORDER BY (CASE WHEN cat.course = $courseid THEN 0 ELSE 1 END), cat.parent, cat.sortorder, cat.name ASC");
    $categories = add_indented_names($categories);

    $categoriesarray = array();
    foreach ($categories as $category) {
        $cid = $category->id;
        $cname = question_category_coursename($category, $courseid);
        if ((!$only_editable) || has_capability('moodle/question:managecategory', get_context_instance(CONTEXT_COURSE, $category->course))) {
            $categoriesarray[$cid] = $cname;
        }
    }
    return $categoriesarray;
}

/**
 * If the category is not from this course, and it is a published category,
 * then return the course category name with the course shortname appended in
 * brackets. Otherwise, just return the category name.
 */
function question_category_coursename($category, $courseid = 0) {
    $cname = (isset($category->indentedname)) ? $category->indentedname : $category->name;
    if ($category->course != $courseid && $category->publish) {
        if (!empty($category->coursename)) {
            $coursename = $category->coursename;
        } else {
            $coursename = get_field('course', 'shortname', 'id', $category->course);
        }
        $cname .= " ($coursename)";
    }
    return $cname;
}

/**
 * Returns a comma separated list of ids of the category and all subcategories
 */
function question_categorylist($categoryid) {
    // returns a comma separated list of ids of the category and all subcategories
    $categorylist = $categoryid;
    if ($subcategories = get_records('question_categories', 'parent', $categoryid, 'sortorder ASC', 'id, id')) {
        foreach ($subcategories as $subcategory) {
            $categorylist .= ','. question_categorylist($subcategory->id);
        }
    }
    return $categorylist;
}

/**
 * find and/or create the category described by a delimited list
 * e.g. tom/dick/harry
 * @param string catpath delimited category path
 * @param string delimiter path delimiting character
 * @param int courseid course to search for categories
 * @return mixed category object or null if fails
 */
function create_category_path( $catpath, $delimiter='/', $courseid=0 ) {
	$catpath = clean_param( $catpath,PARAM_PATH );
    $catnames = explode( $delimiter, $catpath );
    $parent = 0;
    $category = null;
    foreach ($catnames as $catname) {
        if ($category = get_record( 'question_categories', 'name', $catname, 'course', $courseid, 'parent', $parent )) {
            $parent = $category->id;
        }
        else {
            // create the new category
            $category = new object;
            $category->course = $courseid;
            $category->name = $catname;
            $category->info = '';
            $category->publish = false;
            $category->parent = $parent;
            $category->sortorder = 999;
            $category->stamp = make_unique_id_code();
            if (!($id = insert_record( 'question_categories', $category ))) {
                error( "cannot create new category - $catname" );
            }
            $category->id = $id;
            $parent = $id;
        }
    }
    return $category;
}

/**
 * get the category as a path (e.g., tom/dick/harry)
 * @param int id the id of the most nested catgory
 * @param string delimiter the delimiter you want
 * @return string the path
 */
function get_category_path( $id, $delimiter='/' ) {
    $path = '';    
    do {
        if (!$category = get_record( 'question_categories','id',$id )) {
            print_error( "Error reading category record - $id" );    
        }
        $name = $category->name;
        $id = $category->parent;
        if (!empty($path)) {
            $path = "{$name}{$delimiter}{$path}";
        }
        else {
            $path = $name;
        }    
    } while ($id != 0);        
         
    return $path;
}

//===========================
// Import/Export Functions
//===========================

/**
 * Get list of available import or export formats
 * @param string $type 'import' if import list, otherwise export list assumed
 * @return array sorted list of import/export formats available
**/
function get_import_export_formats( $type ) {

    global $CFG;
    $fileformats = get_list_of_plugins("question/format");

    $fileformatname=array();
    require_once( "{$CFG->dirroot}/question/format.php" );
    foreach ($fileformats as $key => $fileformat) {
        $format_file = $CFG->dirroot . "/question/format/$fileformat/format.php";
        if (file_exists( $format_file ) ) {
            require_once( $format_file );
        }
        else {
            continue;
        }
        $classname = "qformat_$fileformat";
        $format_class = new $classname();
        if ($type=='import') {
            $provided = $format_class->provide_import();
        }
        else {
            $provided = $format_class->provide_export();
        }
        if ($provided) {
            $formatname = get_string($fileformat, 'quiz');
            if ($formatname == "[[$fileformat]]") {
                $formatname = $fileformat;  // Just use the raw folder name
            }
            $fileformatnames[$fileformat] = $formatname;
        }
    }
    natcasesort($fileformatnames);

    return $fileformatnames;
}


/**
* Create default export filename
*
* @return string   default export filename
* @param object $course
* @param object $category
*/
function default_export_filename($course,$category) {
    //Take off some characters in the filename !!
    $takeoff = array(" ", ":", "/", "\\", "|");
    $export_word = str_replace($takeoff,"_",moodle_strtolower(get_string("exportfilename","quiz")));
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
    $export_name .= moodle_strtolower($export_shortname)."-";
    //The category name
    $export_name .= moodle_strtolower($export_categoryname)."-";
    //The date format
    $export_name .= userdate(time(),$export_date_format,99,false);
    //The extension - no extension, supplied by format
    // $export_name .= ".txt";

    return $export_name;
}

?>
