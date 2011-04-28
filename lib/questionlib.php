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
 * Major Contributors
 *     - Alex Smith, Julian Sedding and Gustav Delius {@link http://maths.york.ac.uk/serving_maths}
 *
 * @package    core
 * @subpackage question
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

define('QUESTION_EVENTS_GRADED', QUESTION_EVENTGRADE.','.
                    QUESTION_EVENTCLOSEANDGRADE.','.
                    QUESTION_EVENTMANUALGRADE);


define('QUESTION_EVENTS_CLOSED', QUESTION_EVENTCLOSE.','.
                    QUESTION_EVENTCLOSEANDGRADE.','.
                    QUESTION_EVENTMANUALGRADE);

define('QUESTION_EVENTS_CLOSED_OR_GRADED', QUESTION_EVENTGRADE.','.
                    QUESTION_EVENTS_CLOSED);

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
define('QUESTION_PREVIEW_POPUP_OPTIONS', 'scrollbars=yes&resizable=yes&width=700&height=540');

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

/**#@+
 * Options for whether flags are shown/editable when rendering questions.
 */
define('QUESTION_FLAGSHIDDEN', 0);
define('QUESTION_FLAGSSHOWN', 1);
define('QUESTION_FLAGSEDITABLE', 2);
/**#@-*/

/**
 * GLOBAL VARAIBLES
 * @global array $QTYPES
 * @name $QTYPES
 */
global $QTYPES;
/**
 * Array holding question type objects. Initialised via calls to
 * question_register_questiontype as the question type classes are included.
 */
$QTYPES = array();

/**
 * Add a new question type to the various global arrays above.
 *
 * @global object
 * @param object $qtype An instance of the new question type class.
 */
function question_register_questiontype($qtype) {
    global $QTYPES;

    $name = $qtype->name();
    $QTYPES[$name] = $qtype;
}

require_once("$CFG->dirroot/question/type/questiontype.php");

// Load the questiontype.php file for each question type
// These files in turn call question_register_questiontype()
// with a new instance of each qtype class.
$qtypenames = get_plugin_list('qtype');
foreach($qtypenames as $qtypename => $qdir) {
    // Instanciates all plug-in question types
    $qtypefilepath= "$qdir/questiontype.php";

    // echo "Loading $qtypename<br/>"; // Uncomment for debugging
    if (is_readable($qtypefilepath)) {
        require_once($qtypefilepath);
    }
}

/**
 * An array of question type names translated to the user's language, suitable for use when
 * creating a drop-down menu of options.
 *
 * Long-time Moodle programmers will realise that this replaces the old $QTYPE_MENU array.
 * The array returned will only hold the names of all the question types that the user should
 * be able to create directly. Some internal question types like random questions are excluded.
 *
 * @global object
 * @return array an array of question type names translated to the user's language.
 */
function question_type_menu() {
    global $QTYPES;
    static $menuoptions = null;
    if (is_null($menuoptions)) {
        $config = get_config('question');
        $menuoptions = array();
        foreach ($QTYPES as $name => $qtype) {
            // Get the name if this qtype is enabled.
            $menuname = $qtype->menu_name();
            $enabledvar = $name . '_disabled';
            if ($menuname && !isset($config->$enabledvar)) {
                $menuoptions[$name] = $menuname;
            }
        }

        $menuoptions = question_sort_qtype_array($menuoptions, $config);
    }
    return $menuoptions;
}

/**
 * Sort an array of question type names according to the question type sort order stored in
 * config_plugins. Entries for which there is no xxx_sortorder defined will go
 * at the end, sorted according to textlib_get_instance()->asort($inarray).
 * @param $inarray an array $qtype => $QTYPES[$qtype]->local_name().
 * @param $config get_config('question'), if you happen to have it around, to save one DB query.
 * @return array the sorted version of $inarray.
 */
function question_sort_qtype_array($inarray, $config = null) {
    if (is_null($config)) {
        $config = get_config('question');
    }

    $sortorder = array();
    foreach ($inarray as $name => $notused) {
        $sortvar = $name . '_sortorder';
        if (isset($config->$sortvar)) {
            $sortorder[$config->$sortvar] = $name;
        }
    }

    ksort($sortorder);
    $outarray = array();
    foreach ($sortorder as $name) {
        $outarray[$name] = $inarray[$name];
        unset($inarray[$name]);
    }
    textlib_get_instance()->asort($inarray);
    return array_merge($outarray, $inarray);
}

/**
 * Move one question type in a list of question types. If you try to move one element
 * off of the end, nothing will change.
 *
 * @param array $sortedqtypes An array $qtype => anything.
 * @param string $tomove one of the keys from $sortedqtypes
 * @param integer $direction +1 or -1
 * @return array an array $index => $qtype, with $index from 0 to n in order, and
 *      the $qtypes in the same order as $sortedqtypes, except that $tomove will
 *      have been moved one place.
 */
function question_reorder_qtypes($sortedqtypes, $tomove, $direction) {
    $neworder = array_keys($sortedqtypes);
    // Find the element to move.
    $key = array_search($tomove, $neworder);
    if ($key === false) {
        return $neworder;
    }
    // Work out the other index.
    $otherkey = $key + $direction;
    if (!isset($neworder[$otherkey])) {
        return $neworder;
    }
    // Do the swap.
    $swap = $neworder[$otherkey];
    $neworder[$otherkey] = $neworder[$key];
    $neworder[$key] = $swap;
    return $neworder;
}

/**
 * Save a new question type order to the config_plugins table.
 * @global object
 * @param $neworder An arra $index => $qtype. Indices should start at 0 and be in order.
 * @param $config get_config('question'), if you happen to have it around, to save one DB query.
 */
function question_save_qtype_order($neworder, $config = null) {
    global $DB;

    if (is_null($config)) {
        $config = get_config('question');
    }

    foreach ($neworder as $index => $qtype) {
        $sortvar = $qtype . '_sortorder';
        if (!isset($config->$sortvar) || $config->$sortvar != $index + 1) {
            set_config($sortvar, $index + 1, 'question');
        }
    }
}

/// OTHER CLASSES /////////////////////////////////////////////////////////

/**
 * This holds the options that are set by the course module
 *
 * @package moodlecore
 * @subpackage question
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * @global object
 * @global object
 * @param object $questionid
 * @return array of strings
 */
function question_list_instances($questionid) {
    global $CFG, $DB;
    $instances = array();
    $modules = $DB->get_records('modules');
    foreach ($modules as $module) {
        $fullmod = $CFG->dirroot . '/mod/' . $module->name;
        if (file_exists($fullmod . '/lib.php')) {
            include_once($fullmod . '/lib.php');
            $fn = $module->name.'_question_list_instances';
            if (function_exists($fn)) {
                $instances = $instances + $fn($questionid);
            }
        }
    }
    return $instances;
}

/**
 * Determine whether there arey any questions belonging to this context, that is whether any of its
 * question categories contain any questions. This will return true even if all the questions are
 * hidden.
 *
 * @global object
 * @param mixed $context either a context object, or a context id.
 * @return boolean whether any of the question categories beloning to this context have
 *         any questions in them.
 */
function question_context_has_any_questions($context) {
    global $DB;
    if (is_object($context)) {
        $contextid = $context->id;
    } else if (is_numeric($context)) {
        $contextid = $context;
    } else {
        print_error('invalidcontextinhasanyquestions', 'question');
    }
    return $DB->record_exists_sql("SELECT *
                                     FROM {question} q
                                     JOIN {question_categories} qc ON qc.id = q.category
                                    WHERE qc.contextid = ? AND q.parent = 0", array($contextid));
}

/**
 * Returns list of 'allowed' grades for grade selection
 * formatted suitably for dropdown box function
 * @return object ->gradeoptionsfull full array ->gradeoptions +ve only
 */
function get_grade_options() {
    // define basic array of grades. This list comprises all fractions of the form:
    // a. p/q for q <= 6, 0 <= p <= q
    // b. p/10 for 0 <= p <= 10
    // c. 1/q for 1 <= q <= 10
    // d. 1/20
    $grades = array(
        1.0000000,
        0.9000000,
        0.8333333,
        0.8000000,
        0.7500000,
        0.7000000,
        0.6666667,
        0.6000000,
        0.5000000,
        0.4000000,
        0.3333333,
        0.3000000,
        0.2500000,
        0.2000000,
        0.1666667,
        0.1428571,
        0.1250000,
        0.1111111,
        0.1000000,
        0.0500000,
        0.0000000);

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
 * @global object
 * @return boolean
 * @param integer $categoryid
 * @param boolean $recursive Whether to examine category children recursively
 */
function question_category_isused($categoryid, $recursive = false) {
    global $DB;

    //Look at each question in the category
    if ($questions = $DB->get_records('question', array('category'=>$categoryid), '', 'id,qtype')) {
        foreach ($questions as $question) {
            if (count(question_list_instances($question->id))) {
                return true;
            }
        }
    }

    //Look under child categories recursively
    if ($recursive) {
        if ($children = $DB->get_records('question_categories', array('parent'=>$categoryid))) {
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
 * @global object
 * @global object
 * @param integer $attemptid The id of the attempt being deleted
 */
function delete_attempt($attemptid) {
    global $QTYPES, $DB;

    $states = $DB->get_records('question_states', array('attempt'=>$attemptid));
    if ($states) {
        $stateslist = implode(',', array_keys($states));

        // delete question-type specific data
        foreach ($QTYPES as $qtype) {
            $qtype->delete_states($stateslist);
        }
    }

    // delete entries from all other question tables
    // It is important that this is done only after calling the questiontype functions
    $DB->delete_records("question_states", array("attempt"=>$attemptid));
    $DB->delete_records("question_sessions", array("attemptid"=>$attemptid));
    $DB->delete_records("question_attempts", array("id"=>$attemptid));
}

/**
 * Deletes question and all associated data from the database
 *
 * It will not delete a question if it is used by an activity module
 *
 * @global object
 * @global object
 * @param object $question  The question being deleted
 */
function delete_question($questionid) {
    global $QTYPES, $DB;

    $question = $DB->get_record_sql('
            SELECT q.*, qc.contextid
            FROM {question} q
            JOIN {question_categories} qc ON qc.id = q.category
            WHERE q.id = ?', array($questionid));
    if (!$question) {
        // In some situations, for example if this was a child of a
        // Cloze question that was previously deleted, the question may already
        // have gone. In this case, just do nothing.
        return;
    }

    // Do not delete a question if it is used by an activity module
    if (count(question_list_instances($questionid))) {
        return;
    }

    // delete questiontype-specific data
    question_require_capability_on($question, 'edit');
    if (isset($QTYPES[$question->qtype])) {
        $QTYPES[$question->qtype]->delete_question($questionid, $question->contextid);
    }

    if ($states = $DB->get_records('question_states', array('question'=>$questionid))) {
        $stateslist = implode(',', array_keys($states));

        // delete questiontype-specific data
        foreach ($QTYPES as $qtype) {
            $qtype->delete_states($stateslist);
        }
    }

    // Delete entries from all other question tables
    // It is important that this is done only after calling the questiontype functions
    $DB->delete_records('question_answers', array('question' => $questionid));
    $DB->delete_records('question_states', array('question' => $questionid));
    $DB->delete_records('question_sessions', array('questionid' => $questionid));

    // Now recursively delete all child questions
    if ($children = $DB->get_records('question', array('parent' => $questionid), '', 'id,qtype')) {
        foreach ($children as $child) {
            if ($child->id != $questionid) {
                delete_question($child->id);
            }
        }
    }

    // Finally delete the question record itself
    $DB->delete_records('question', array('id'=>$questionid));
}

/**
 * All question categories and their questions are deleted for this course.
 *
 * @global object
 * @param object $mod an object representing the activity
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function question_delete_course($course, $feedback=true) {
    global $DB, $OUTPUT;

    //To store feedback to be showed at the end of the process
    $feedbackdata   = array();

    //Cache some strings
    $strcatdeleted = get_string('unusedcategorydeleted', 'quiz');
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
    $categoriescourse = $DB->get_records('question_categories', array('contextid'=>$coursecontext->id), 'parent', 'id, parent, name, contextid');

    if ($categoriescourse) {

        //Sort categories following their tree (parent-child) relationships
        //this will make the feedback more readable
        $categoriescourse = sort_categories_by_tree($categoriescourse);

        foreach ($categoriescourse as $category) {

            //Delete it completely (questions and category itself)
            //deleting questions
            if ($questions = $DB->get_records('question', array('category' => $category->id), '', 'id,qtype')) {
                foreach ($questions as $question) {
                    delete_question($question->id);
                }
                $DB->delete_records("question", array("category"=>$category->id));
            }
            //delete the category
            $DB->delete_records('question_categories', array('id'=>$category->id));

            //Fill feedback
            $feedbackdata[] = array($category->name, $strcatdeleted);
        }
        //Inform about changes performed if feedback is enabled
        if ($feedback) {
            $table = new html_table();
            $table->head = array(get_string('category','quiz'), get_string('action'));
            $table->data = $feedbackdata;
            echo html_writer::table($table);
        }
    }
    return true;
}

/**
 * Category is about to be deleted,
 * 1/ All question categories and their questions are deleted for this course category.
 * 2/ All questions are moved to new category
 *
 * @global object
 * @param object $category course category object
 * @param object $newcategory empty means everything deleted, otherwise id of category where content moved
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function question_delete_course_category($category, $newcategory, $feedback=true) {
    global $DB, $OUTPUT;

    $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
    if (empty($newcategory)) {
        $feedbackdata   = array(); // To store feedback to be showed at the end of the process
        $rescueqcategory = null; // See the code around the call to question_save_from_deletion.
        $strcatdeleted = get_string('unusedcategorydeleted', 'quiz');

        // Loop over question categories.
        if ($categories = $DB->get_records('question_categories', array('contextid'=>$context->id), 'parent', 'id, parent, name')) {
            foreach ($categories as $category) {

                // Deal with any questions in the category.
                if ($questions = $DB->get_records('question', array('category' => $category->id), '', 'id,qtype')) {

                    // Try to delete each question.
                    foreach ($questions as $question) {
                        delete_question($question->id);
                    }

                    // Check to see if there were any questions that were kept because they are
                    // still in use somehow, even though quizzes in courses in this category will
                    // already have been deteted. This could happen, for example, if questions are
                    // added to a course, and then that course is moved to another category (MDL-14802).
                    $questionids = $DB->get_records_menu('question', array('category'=>$category->id), '', 'id,1');
                    if (!empty($questionids)) {
                        if (!$rescueqcategory = question_save_from_deletion(array_keys($questionids),
                                get_parent_contextid($context), print_context_name($context), $rescueqcategory)) {
                            return false;
                       }
                       $feedbackdata[] = array($category->name, get_string('questionsmovedto', 'question', $rescueqcategory->name));
                    }
                }

                // Now delete the category.
                if (!$DB->delete_records('question_categories', array('id'=>$category->id))) {
                    return false;
                }
                $feedbackdata[] = array($category->name, $strcatdeleted);

            } // End loop over categories.
        }

        // Output feedback if requested.
        if ($feedback and $feedbackdata) {
            $table = new html_table();
            $table->head = array(get_string('questioncategory','question'), get_string('action'));
            $table->data = $feedbackdata;
            echo html_writer::table($table);
        }

    } else {
        // Move question categories ot the new context.
        if (!$newcontext = get_context_instance(CONTEXT_COURSECAT, $newcategory->id)) {
            return false;
        }
        $DB->set_field('question_categories', 'contextid', $newcontext->id, array('contextid'=>$context->id));
        if ($feedback) {
            $a = new stdClass;
            $a->oldplace = print_context_name($context);
            $a->newplace = print_context_name($newcontext);
            echo $OUTPUT->notification(get_string('movedquestionsandcategories', 'question', $a), 'notifysuccess');
        }
    }

    return true;
}

/**
 * Enter description here...
 *
 * @global object
 * @param string $questionids list of questionids
 * @param object $newcontext the context to create the saved category in.
 * @param string $oldplace a textual description of the think being deleted, e.g. from get_context_name
 * @param object $newcategory
 * @return mixed false on
 */
function question_save_from_deletion($questionids, $newcontextid, $oldplace, $newcategory = null) {
    global $DB;

    // Make a category in the parent context to move the questions to.
    if (is_null($newcategory)) {
        $newcategory = new stdClass();
        $newcategory->parent = 0;
        $newcategory->contextid = $newcontextid;
        $newcategory->name = get_string('questionsrescuedfrom', 'question', $oldplace);
        $newcategory->info = get_string('questionsrescuedfrominfo', 'question', $oldplace);
        $newcategory->sortorder = 999;
        $newcategory->stamp = make_unique_id_code();
        $newcategory->id = $DB->insert_record('question_categories', $newcategory);
    }

    // Move any remaining questions to the 'saved' category.
    if (!question_move_questions_to_category($questionids, $newcategory->id)) {
        return false;
    }
    return $newcategory;
}

/**
 * All question categories and their questions are deleted for this activity.
 *
 * @global object
 * @param object $cm the course module object representing the activity
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function question_delete_activity($cm, $feedback=true) {
    global $DB, $OUTPUT;

    //To store feedback to be showed at the end of the process
    $feedbackdata   = array();

    //Cache some strings
    $strcatdeleted = get_string('unusedcategorydeleted', 'quiz');
    $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
    if ($categoriesmods = $DB->get_records('question_categories', array('contextid'=>$modcontext->id), 'parent', 'id, parent, name, contextid')){
        //Sort categories following their tree (parent-child) relationships
        //this will make the feedback more readable
        $categoriesmods = sort_categories_by_tree($categoriesmods);

        foreach ($categoriesmods as $category) {

            //Delete it completely (questions and category itself)
            //deleting questions
            if ($questions = $DB->get_records('question', array('category' => $category->id), '', 'id,qtype')) {
                foreach ($questions as $question) {
                    delete_question($question->id);
                }
                $DB->delete_records("question", array("category"=>$category->id));
            }
            //delete the category
            $DB->delete_records('question_categories', array('id'=>$category->id));

            //Fill feedback
            $feedbackdata[] = array($category->name, $strcatdeleted);
        }
        //Inform about changes performed if feedback is enabled
        if ($feedback) {
            $table = new html_table();
            $table->head = array(get_string('category','quiz'), get_string('action'));
            $table->data = $feedbackdata;
            echo html_writer::table($table);
        }
    }
    return true;
}

/**
 * This function should be considered private to the question bank, it is called from
 * question/editlib.php question/contextmoveq.php and a few similar places to to the work of
 * acutally moving questions and associated data. However, callers of this function also have to
 * do other work, which is why you should not call this method directly from outside the questionbank.
 *
 * @global object
 * @param string $questionids a comma-separated list of question ids.
 * @param integer $newcategoryid the id of the category to move to.
 */
function question_move_questions_to_category($questionids, $newcategoryid) {
    global $DB, $QTYPES;

    $newcontextid = $DB->get_field('question_categories', 'contextid',
            array('id' => $newcategoryid));
    list($questionidcondition, $params) = $DB->get_in_or_equal($questionids);
    $questions = $DB->get_records_sql("
            SELECT q.id, q.qtype, qc.contextid
              FROM {question} q
              JOIN {question_categories} qc ON q.category = qc.id
             WHERE  q.id $questionidcondition", $params);
    foreach ($questions as $question) {
        if ($newcontextid != $question->contextid) {
            $QTYPES[$question->qtype]->move_files($question->id,
                    $question->contextid, $newcontextid);
        }
    }

    // Move the questions themselves.
    $DB->set_field_select('question', 'category', $newcategoryid, "id $questionidcondition", $params);

    // Move any subquestions belonging to them.
    $DB->set_field_select('question', 'category', $newcategoryid, "parent $questionidcondition", $params);

    // TODO Deal with datasets.

    return true;
}

/**
 * This function helps move a question cateogry to a new context by moving all
 * the files belonging to all the questions to the new context.
 * Also moves subcategories.
 * @param integer $categoryid the id of the category being moved.
 * @param integer $oldcontextid the old context id.
 * @param integer $newcontextid the new context id.
 */
function question_move_category_to_context($categoryid, $oldcontextid, $newcontextid) {
    global $DB, $QTYPES;

    $questionids = $DB->get_records_menu('question',
            array('category' => $categoryid), '', 'id,qtype');
    foreach ($questionids as $questionid => $qtype) {
        $QTYPES[$qtype]->move_files($questionid, $oldcontextid, $newcontextid);
    }

    $subcatids = $DB->get_records_menu('question_categories',
            array('parent' => $categoryid), '', 'id,1');
    foreach ($subcatids as $subcatid => $notused) {
        $DB->set_field('question_categories', 'contextid', $newcontextid, array('id' => $subcatid));
        question_move_category_to_context($subcatid, $oldcontextid, $newcontextid);
    }
}

/**
 * Given a list of ids, load the basic information about a set of questions from the questions table.
 * The $join and $extrafields arguments can be used together to pull in extra data.
 * See, for example, the usage in mod/quiz/attemptlib.php, and
 * read the code below to see how the SQL is assembled. Throws exceptions on error.
 *
 * @global object
 * @global object
 * @param array $questionids array of question ids.
 * @param string $extrafields extra SQL code to be added to the query.
 * @param string $join extra SQL code to be added to the query.
 * @param array $extraparams values for any placeholders in $join.
 * You are strongly recommended to use named placeholder.
 *
 * @return array partially complete question objects. You need to call get_question_options
 * on them before they can be properly used.
 */
function question_preload_questions($questionids, $extrafields = '', $join = '', $extraparams = array()) {
    global $CFG, $DB;
    if (empty($questionids)) {
        return array();
    }
    if ($join) {
        $join = ' JOIN '.$join;
    }
    if ($extrafields) {
        $extrafields = ', ' . $extrafields;
    }
    list($questionidcondition, $params) = $DB->get_in_or_equal(
            $questionids, SQL_PARAMS_NAMED, 'qid0000');
    $sql = 'SELECT q.*' . $extrafields . ' FROM {question} q' . $join .
            ' WHERE q.id ' . $questionidcondition;

    // Load the questions
    if (!$questions = $DB->get_records_sql($sql, $extraparams + $params)) {
        return 'Could not load questions.';
    }

    foreach ($questions as $question) {
        $question->_partiallyloaded = true;
    }

    // Note, a possible optimisation here would be to not load the TEXT fields
    // (that is, questiontext and generalfeedback) here, and instead load them in
    // question_load_questions. That would add one DB query, but reduce the amount
    // of data transferred most of the time. I am not going to do this optimisation
    // until it is shown to be worthwhile.

    return $questions;
}

/**
 * Load a set of questions, given a list of ids. The $join and $extrafields arguments can be used
 * together to pull in extra data. See, for example, the usage in mod/quiz/attempt.php, and
 * read the code below to see how the SQL is assembled. Throws exceptions on error.
 *
 * @param array $questionids array of question ids.
 * @param string $extrafields extra SQL code to be added to the query.
 * @param string $join extra SQL code to be added to the query.
 * @param array $extraparams values for any placeholders in $join.
 * You are strongly recommended to use named placeholder.
 *
 * @return array question objects.
 */
function question_load_questions($questionids, $extrafields = '', $join = '') {
    $questions = question_preload_questions($questionids, $extrafields, $join);

    // Load the question type specific information
    if (!get_question_options($questions)) {
        return 'Could not load the question options';
    }

    return $questions;
}

/**
 * Private function to factor common code out of get_question_options().
 *
 * @global object
 * @global object
 * @param object $question the question to tidy.
 * @param boolean $loadtags load the question tags from the tags table. Optional, default false.
 * @return boolean true if successful, else false.
 */
function _tidy_question(&$question, $loadtags = false) {
    global $CFG, $QTYPES;
    if (!array_key_exists($question->qtype, $QTYPES)) {
        $question->qtype = 'missingtype';
        $question->questiontext = '<p>' . get_string('warningmissingtype', 'quiz') . '</p>' . $question->questiontext;
    }
    $question->name_prefix = question_make_name_prefix($question->id);
    if ($success = $QTYPES[$question->qtype]->get_question_options($question)) {
        if (isset($question->_partiallyloaded)) {
            unset($question->_partiallyloaded);
        }
    }
    if ($loadtags && !empty($CFG->usetags)) {
        require_once($CFG->dirroot . '/tag/lib.php');
        $question->tags = tag_get_tags_array('question', $question->id);
    }
    return $success;
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
 * @param boolean $loadtags load the question tags from the tags table. Optional, default false.
 * @return bool Indicates success or failure.
 */
function get_question_options(&$questions, $loadtags = false) {
    if (is_array($questions)) { // deal with an array of questions
        foreach ($questions as $i => $notused) {
            if (!_tidy_question($questions[$i], $loadtags)) {
                return false;
            }
        }
        return true;
    } else { // deal with single question
        return _tidy_question($questions, $loadtags);
    }
}

/**
 * Load the basic state information for
 *
 * @global object
 * @param integer $attemptid the attempt id to load the states for.
 * @return array an array of state data from the database, you will subsequently
 *      need to call question_load_states to get fully loaded states that can be
 *      used by the question types. The states here should be sufficient for
 *      basic tasks like rendering navigation.
 */
function question_preload_states($attemptid) {
    global $DB;
    // Note, changes here probably also need to be reflected in
    // regrade_question_in_attempt and question_load_specific_state.

    // The questionid field must be listed first so that it is used as the
    // array index in the array returned by $DB->get_records_sql
    $statefields = 'n.questionid as question, s.id, s.attempt, ' .
            's.seq_number, s.answer, s.timestamp, s.event, s.grade, s.raw_grade, ' .
            's.penalty, n.sumpenalty, n.manualcomment, n.manualcommentformat, ' .
            'n.flagged, n.id as questionsessionid';

    // Load the newest states for the questions
    $sql = "SELECT $statefields
              FROM {question_states} s, {question_sessions} n
             WHERE s.id = n.newest AND n.attemptid = ?";
    $states = $DB->get_records_sql($sql, array($attemptid));
    if (!$states) {
        return false;
    }

    // Load the newest graded states for the questions
    $sql = "SELECT $statefields
              FROM {question_states} s, {question_sessions} n
             WHERE s.id = n.newgraded AND n.attemptid = ?";
    $gradedstates = $DB->get_records_sql($sql, array($attemptid));

    // Hook the two together.
    foreach ($states as $questionid => $state) {
        $states[$questionid]->_partiallyloaded = true;
        if ($gradedstates[$questionid]) {
            $states[$questionid]->last_graded = $gradedstates[$questionid];
            $states[$questionid]->last_graded->_partiallyloaded = true;
        }
    }

    return $states;
}

/**
 * Finish loading the question states that were extracted from the database with
 * question_preload_states, creating new states for any question where there
 * is not a state in the database.
 *
 * @global object
 * @global object
 * @param array $questions the questions to load state for.
 * @param array $states the partially loaded states this array is updated.
 * @param object $cmoptions options from the module we are loading the states for. E.g. $quiz.
 * @param object $attempt The attempt for which the question sessions are
 *      to be restored or created.
 * @param mixed either the id of a previous attempt, if this attmpt is
 *      building on a previous one, or false for a clean attempt.
 * @return true or false for success or failure.
 */
function question_load_states(&$questions, &$states, $cmoptions, $attempt, $lastattemptid = false) {
    global $QTYPES, $DB;

    // loop through all questions and set the last_graded states
    foreach (array_keys($questions) as $qid) {
        if (isset($states[$qid])) {
            restore_question_state($questions[$qid], $states[$qid]);
            if (isset($states[$qid]->_partiallyloaded)) {
                unset($states[$qid]->_partiallyloaded);
            }
            if (isset($states[$qid]->last_graded)) {
                restore_question_state($questions[$qid], $states[$qid]->last_graded);
                if (isset($states[$qid]->last_graded->_partiallyloaded)) {
                    unset($states[$qid]->last_graded->_partiallyloaded);
                }
            } else {
                $states[$qid]->last_graded = clone($states[$qid]);
            }
        } else {

            if ($lastattemptid) {
                // If the new attempt is to be based on this previous attempt.
                // Find the responses from the previous attempt and save them to the new session

                // Load the last graded state for the question. Note, $statefields is
                // the same as above, except that we don't want n.manualcomment.
                $statefields = 'n.questionid as question, s.id, s.attempt, ' .
                        's.seq_number, s.answer, s.timestamp, s.event, s.grade, s.raw_grade, ' .
                        's.penalty, n.sumpenalty';
                $sql = "SELECT $statefields
                          FROM {question_states} s, {question_sessions} n
                         WHERE s.id = n.newest
                               AND n.attemptid = ?
                               AND n.questionid = ?";
                if (!$laststate = $DB->get_record_sql($sql, array($lastattemptid, $qid))) {
                    // Only restore previous responses that have been graded
                    continue;
                }
                // Restore the state so that the responses will be restored
                restore_question_state($questions[$qid], $laststate);
                $states[$qid] = clone($laststate);
                unset($states[$qid]->id);
            } else {
                // create a new empty state
                $states[$qid] = new stdClass();
                $states[$qid]->question = $qid;
                $states[$qid]->responses = array('' => '');
                $states[$qid]->raw_grade = 0;
            }

            // now fill/overide initial values
            $states[$qid]->attempt = $attempt->uniqueid;
            $states[$qid]->seq_number = 0;
            $states[$qid]->timestamp = $attempt->timestart;
            $states[$qid]->event = ($attempt->timefinish) ? QUESTION_EVENTCLOSE : QUESTION_EVENTOPEN;
            $states[$qid]->grade = 0;
            $states[$qid]->penalty = 0;
            $states[$qid]->sumpenalty = 0;
            $states[$qid]->manualcomment = '';
            $states[$qid]->manualcommentformat = FORMAT_HTML;
            $states[$qid]->flagged = 0;

            // Prevent further changes to the session from incrementing the
            // sequence number
            $states[$qid]->changed = true;

            if ($lastattemptid) {
                // prepare the previous responses for new processing
                $action = new stdClass;
                $action->responses = $laststate->responses;
                $action->timestamp = $laststate->timestamp;
                $action->event = QUESTION_EVENTSAVE; //emulate save of questions from all pages MDL-7631

                // Process these responses ...
                question_process_responses($questions[$qid], $states[$qid], $action, $cmoptions, $attempt);

                // Fix for Bug #5506: When each attempt is built on the last one,
                // preserve the options from any previous attempt.
                if ( isset($laststate->options) ) {
                    $states[$qid]->options = $laststate->options;
                }
            } else {
                // Create the empty question type specific information
                if (!$QTYPES[$questions[$qid]->qtype]->create_session_and_responses(
                        $questions[$qid], $states[$qid], $cmoptions, $attempt)) {
                    return false;
                }
            }
            $states[$qid]->last_graded = clone($states[$qid]);
        }
    }
    return true;
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
    // Preload the states.
    $states = question_preload_states($attempt->uniqueid);
    if (!$states) {
        $states = array();
    }

    // Then finish the job.
    if (!question_load_states($questions, $states, $cmoptions, $attempt, $lastattemptid)) {
        return false;
    }

    return $states;
}

/**
 * Load a particular previous state of a question.
 *
 * @global object
 * @param array $question The question to load the state for.
 * @param object $cmoptions Options from the specifica activity module, e.g. $quiz.
 * @param integer $attemptid The question_attempts this is part of.
 * @param integer $stateid The id of a specific state of this question.
 * @return object the requested state. False on error.
 */
function question_load_specific_state($question, $cmoptions, $attemptid, $stateid) {
    global $DB;

    // Load specified states for the question.
    // sess.sumpenalty is probably wrong here shoul really be a sum of penalties from before the one we are asking for.
    $sql = 'SELECT st.*, sess.sumpenalty, sess.manualcomment, sess.manualcommentformat,
                        sess.flagged, sess.id as questionsessionid
              FROM {question_states} st, {question_sessions} sess
             WHERE st.id = ?
               AND st.attempt = ?
               AND sess.attemptid = st.attempt
               AND st.question = ?
               AND sess.questionid = st.question';
    $state = $DB->get_record_sql($sql, array($stateid, $attemptid, $question->id));
    if (!$state) {
        return false;
    }
    restore_question_state($question, $state);

    // Load the most recent graded states for the questions before the specified one.
    $sql = 'SELECT st.*, sess.sumpenalty, sess.manualcomment, sess.manualcommentformat,
                        sess.flagged, sess.id as questionsessionid
              FROM {question_states} st, {question_sessions} sess
             WHERE st.seq_number <= ?
               AND st.attempt = ?
               AND sess.attemptid = st.attempt
               AND st.question = ?
               AND sess.questionid = st.question
               AND st.event IN ('.QUESTION_EVENTS_GRADED.') '.
           'ORDER BY st.seq_number DESC';
    $gradedstates = $DB->get_records_sql($sql, array($state->seq_number, $attemptid, $question->id), 0, 1);
    if (empty($gradedstates)) {
        $state->last_graded = clone($state);
    } else {
        $gradedstate = reset($gradedstates);
        restore_question_state($question, $gradedstate);
        $state->last_graded = $gradedstate;
    }
    return $state;
}

/**
* Creates the run-time fields for the states
*
* Extends the state objects for a question by calling
* {@link restore_session_and_responses()}
 *
 * @global object
* @param object $question The question for which the state is needed
* @param object $state The state as loaded from the database
* @return boolean Represents success or failure
*/
function restore_question_state(&$question, &$state) {
    global $QTYPES;

    // initialise response to the value in the answer field
    $state->responses = array('' => $state->answer);

    // Set the changed field to false; any code which changes the
    // question session must set this to true and must increment
    // ->seq_number. The save_question_session
    // function will save the new state object to the database if the field is
    // set to true.
    $state->changed = false;

    // Load the question type specific data
    return $QTYPES[$question->qtype]->restore_session_and_responses($question, $state);

}

/**
* Saves the current state of the question session to the database
*
* The state object representing the current state of the session for the
* question is saved to the question_states table with ->responses[''] saved
* to the answer field of the database table. The information in the
* question_sessions table is updated.
* The question type specific data is then saved.
 *
 * @global array
 * @global object
* @return mixed           The id of the saved or updated state or false
* @param object $question The question for which session is to be saved.
* @param object $state    The state information to be saved. In particular the
*                         most recent responses are in ->responses. The object
*                         is updated to hold the new ->id.
*/
function save_question_session($question, $state) {
    global $QTYPES, $DB;

    // Check if the state has changed
    if (!$state->changed && isset($state->id)) {
        if (isset($state->newflaggedstate) &&  $state->flagged != $state->newflaggedstate) {
            // If this fails, don't worry too much, it is not critical data.
            question_update_flag($state->questionsessionid, $state->newflaggedstate);
        }
        return $state->id;
    }
    // Set the legacy answer field
    $state->answer = isset($state->responses['']) ? $state->responses[''] : '';

    // Save the state
    if (!empty($state->update)) { // this forces the old state record to be overwritten
        $DB->update_record('question_states', $state);
    } else {
        $state->id = $DB->insert_record('question_states', $state);
    }

    // create or update the session
    if (!$session = $DB->get_record('question_sessions', array('attemptid' => $state->attempt, 'questionid' => $question->id))) {
        $session = new stdClass;
        $session->attemptid = $state->attempt;
        $session->questionid = $question->id;
        $session->newest = $state->id;
        // The following may seem weird, but the newgraded field needs to be set
        // already even if there is no graded state yet.
        $session->newgraded = $state->id;
        $session->sumpenalty = $state->sumpenalty;
        $session->manualcomment = $state->manualcomment;
        $session->manualcommentformat = $state->manualcommentformat;
        $session->flagged = !empty($state->newflaggedstate);
        $DB->insert_record('question_sessions', $session);
    } else {
        $session->newest = $state->id;
        if (question_state_is_graded($state) or $state->event == QUESTION_EVENTOPEN) {
            // this state is graded or newly opened, so it goes into the lastgraded field as well
            $session->newgraded = $state->id;
            $session->sumpenalty = $state->sumpenalty;
            $session->manualcomment = $state->manualcomment;
            $session->manualcommentformat = $state->manualcommentformat;
        }
        $session->flagged = !empty($state->newflaggedstate);
        $DB->update_record('question_sessions', $session);
    }

    unset($state->answer);

    // Save the question type specific state information and responses
    if (!$QTYPES[$question->qtype]->save_session_and_responses($question, $state)) {
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
    static $question_events_graded = array();
    if (!$question_events_graded){
        $question_events_graded = explode(',', QUESTION_EVENTS_GRADED);
    }
    return (in_array($state->event, $question_events_graded));
}

/**
* Determines whether a state has been closed by looking at the event field
*
* @return boolean         true if the state has been closed
* @param object $state
*/
function question_state_is_closed($state) {
    static $question_events_closed = array();
    if (!$question_events_closed){
        $question_events_closed = explode(',', QUESTION_EVENTS_CLOSED);
    }
    return (in_array($state->event, $question_events_closed));
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
                print_error('formquestionnotinids', 'question');
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
 *
 * @global object
 * @param float   $fraction  value representing the correctness of the user's
 *                           response to a question.
 * @param boolean $selected  whether or not the answer is the one that the
 *                           user picked.
 * @return string
 */
function question_get_feedback_image($fraction, $selected=true) {
    global $CFG, $OUTPUT;
    static $icons = array('correct' => 'tick_green', 'partiallycorrect' => 'tick_amber',
            'incorrect' => 'cross_red');

    if ($selected) {
        $size = 'big';
    } else {
        $size = 'small';
    }
    $class = question_get_feedback_class($fraction);
    return '<img src="' . $OUTPUT->pix_url('i/' . $icons[$class] . '_' . $size) .
            '" alt="' . get_string($class, 'quiz') . '" class="icon" />';
}

/**
 * Returns the class name for question feedback.
 * @param float  $fraction  value representing the correctness of the user's
 *                          response to a question.
 * @return string
 */
function question_get_feedback_class($fraction) {
    if ($fraction >= 1/1.01) {
        return 'correct';
    } else if ($fraction > 0.0) {
        return 'partiallycorrect';
    } else {
        return 'incorrect';
    }
}


/**
* For a given question in an attempt we walk the complete history of states
* and recalculate the grades as we go along.
*
* This is used when a question is changed and old student
* responses need to be marked with the new version of a question.
*
* @todo Make sure this is not quiz-specific
*
 * @global object
* @return boolean            Indicates whether the grade has changed
* @param object  $question   A question object
* @param object  $attempt    The attempt, in which the question needs to be regraded.
* @param object  $cmoptions
* @param boolean $verbose    Optional. Whether to print progress information or not.
* @param boolean $dryrun     Optional. Whether to make changes to grades records
* or record that changes need to be made for a later regrade.
*/
function regrade_question_in_attempt($question, $attempt, $cmoptions, $verbose=false, $dryrun=false) {
    global $DB, $OUTPUT;

    // load all states for this question in this attempt, ordered in sequence
    if ($states = $DB->get_records('question_states',
            array('attempt'=>$attempt->uniqueid, 'question'=>$question->id),
            'seq_number ASC')) {
        $states = array_values($states);

        // Subtract the grade for the latest state from $attempt->sumgrades to get the
        // sumgrades for the attempt without this question.
        $attempt->sumgrades -= $states[count($states)-1]->grade;

        // Initialise the replaystate
        $replaystate = question_load_specific_state($question, $cmoptions, $attempt->uniqueid, $states[0]->id);
        $replaystate->sumpenalty = 0;
        $replaystate->last_graded->sumpenalty = 0;

        $changed = false;
        for($j = 1; $j < count($states); $j++) {
            restore_question_state($question, $states[$j]);
            $action = new stdClass;
            $action->responses = $states[$j]->responses;
            $action->timestamp = $states[$j]->timestamp;

            // Change event to submit so that it will be reprocessed
            if (in_array($states[$j]->event, array(QUESTION_EVENTCLOSE,
                    QUESTION_EVENTGRADE, QUESTION_EVENTCLOSEANDGRADE))) {
                $action->event = QUESTION_EVENTSUBMIT;

            // By default take the event that was saved in the database
            } else {
                $action->event = $states[$j]->event;
            }

            if ($action->event == QUESTION_EVENTMANUALGRADE) {
                // Ensure that the grade is in range - in the past this was not checked,
                // but now it is (MDL-14835) - so we need to ensure the data is valid before
                // proceeding.
                if ($states[$j]->grade < 0) {
                    $states[$j]->grade = 0;
                    $changed = true;
                } else if ($states[$j]->grade > $question->maxgrade) {
                    $states[$j]->grade = $question->maxgrade;
                    $changed = true;

                }
                if (!$dryrun){
                    $error = question_process_comment($question, $replaystate, $attempt,
                            $replaystate->manualcomment, $replaystate->manualcommentformat, $states[$j]->grade);
                    if (is_string($error)) {
                         echo $OUTPUT->notification($error);
                    }
                } else {
                    $replaystate->grade = $states[$j]->grade;
                }
            } else {
                // Reprocess (regrade) responses
                if (!question_process_responses($question, $replaystate,
                        $action, $cmoptions, $attempt) && $verbose) {
                    $a = new stdClass;
                    $a->qid = $question->id;
                    $a->stateid = $states[$j]->id;
                    echo $OUTPUT->notification(get_string('errorduringregrade', 'question', $a));
                }
                // We need rounding here because grades in the DB get truncated
                // e.g. 0.33333 != 0.3333333, but we want them to be equal here
                if ((round((float)$replaystate->raw_grade, 5) != round((float)$states[$j]->raw_grade, 5))
                        or (round((float)$replaystate->penalty, 5) != round((float)$states[$j]->penalty, 5))
                        or (round((float)$replaystate->grade, 5) != round((float)$states[$j]->grade, 5))) {
                    $changed = true;
                }
                // If this was previously a closed state, and it has been knoced back to
                // graded, then fix up the state again.
                if ($replaystate->event == QUESTION_EVENTGRADE &&
                        ($states[$j]->event == QUESTION_EVENTCLOSE ||
                        $states[$j]->event == QUESTION_EVENTCLOSEANDGRADE)) {
                    $replaystate->event = $states[$j]->event;
                }
            }

            $replaystate->id = $states[$j]->id;
            $replaystate->changed = true;
            $replaystate->update = true; // This will ensure that the existing database entry is updated rather than a new one created
            if (!$dryrun){
                save_question_session($question, $replaystate);
            }
        }
        if ($changed) {
            if (!$dryrun){
                // TODO, call a method in quiz to do this, where 'quiz' comes from
                // the question_attempts table.
                $DB->update_record('quiz_attempts', $attempt);
            }
        }
        if ($changed){
            $toinsert = new stdClass();
            $toinsert->oldgrade = round((float)$states[count($states)-1]->grade, 5);
            $toinsert->newgrade = round((float)$replaystate->grade, 5);
            $toinsert->attemptid = $attempt->uniqueid;
            $toinsert->questionid = $question->id;
            //the grade saved is the old grade if the new grade is saved
            //it is the new grade if this is a dry run.
            $toinsert->regraded = $dryrun?0:1;
            $toinsert->timemodified = time();
            $DB->insert_record('quiz_question_regrade', $toinsert);
            return true;
        } else {
            return false;
        }
    }
    return false;
}

/**
* Processes an array of student responses, grading and saving them as appropriate
*
 * @global array
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
* @return boolean         Indicates success/failure
*/
function question_process_responses($question, &$state, $action, $cmoptions, &$attempt) {
    global $QTYPES;

    // if no responses are set initialise to empty response
    if (!isset($action->responses)) {
        $action->responses = array('' => '');
    }

    $state->newflaggedstate = !empty($action->responses['_flagged']);

    // make sure these are gone!
    unset($action->responses['submit'], $action->responses['validate'], $action->responses['_flagged']);

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
    if (!empty($state->last_graded) && $state->last_graded->event == QUESTION_EVENTOPEN &&
            question_isgradingevent($action->event)) {
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
    $newstate->newflaggedstate = $state->newflaggedstate;
    $newstate->flagged = $state->flagged;
    $newstate->questionsessionid = $state->questionsessionid;
    $state = $newstate;

    // Set the event to the action we will perform. The question type specific
    // grading code may override this by setting it to QUESTION_EVENTCLOSE if the
    // attempt at the question causes the session to close
    $state->event = $action->event;

    if (!question_isgradingevent($action->event)) {
        // Grade the response but don't update the overall grade
        if (!$QTYPES[$question->qtype]->grade_responses($question, $state, $cmoptions)) {
            return false;
        }

        // Temporary hack because question types are not given enough control over what is going
        // on. Used by Opaque questions.
        // TODO fix this code properly.
        if (!empty($state->believeevent)) {
            // If the state was graded we need to ...
            if (question_state_is_graded($state)) {
                question_apply_penalty_and_timelimit($question, $state, $attempt, $cmoptions);

                // update the attempt grade
                $attempt->sumgrades -= (float)$state->last_graded->grade;
                $attempt->sumgrades += (float)$state->grade;

                // and update the last_graded field.
                unset($state->last_graded);
                $state->last_graded = clone($state);
                unset($state->last_graded->changed);
            }
        } else {
            // Don't allow the processing to change the event type
            $state->event = $action->event;
        }

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
            if (!$QTYPES[$question->qtype]->grade_responses($question, $state, $cmoptions)) {
                return false;
            }

            // Calculate overall grade using correct penalty method
            question_apply_penalty_and_timelimit($question, $state, $attempt, $cmoptions);
        }

        // If the state was graded we need to ...
        if (question_state_is_graded($state)) {
            // update the attempt grade
            $attempt->sumgrades -= (float)$state->last_graded->grade;
            $attempt->sumgrades += (float)$state->grade;

            // and update the last_graded field.
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
        if ($state->timestamp - $attempt->timestart > $cmoptions->timelimit * 1.05) {
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
 * @global array
 * @global object
* @param object $question The question object for which the icon is required
*       only $question->qtype is used.
* @param boolean $return If true the functions returns the link as a string
*/
function print_question_icon($question, $return = false) {
    global $QTYPES, $CFG, $OUTPUT;

    if (array_key_exists($question->qtype, $QTYPES)) {
        $namestr = $QTYPES[$question->qtype]->local_name();
    } else {
        $namestr = 'missingtype';
    }
    $html = '<img src="' . $OUTPUT->pix_url('icon', 'qtype_'.$question->qtype) . '" alt="' .
            $namestr . '" title="' . $namestr . '" />';
    if ($return) {
        return $html;
    } else {
        echo $html;
    }
}

/**
 * @param $question
 * @param $state
 * @param $prefix
 * @param $cmoptions
 * @param $caption
 */
function question_print_comment_fields($question, $state, $prefix, $cmoptions, $caption = '') {
    global $QTYPES;
    $idprefix = preg_replace('/[^-_a-zA-Z0-9]/', '', $prefix);
    $otherquestionsinuse = '';
    if (!empty($cmoptions->questions)) {
        $otherquestionsinuse = $cmoptions->questions;
    }
    if (!question_state_is_graded($state) && $QTYPES[$question->qtype]->is_question_manual_graded($question, $otherquestionsinuse)) {
        $grade = '';
    } else {
        $grade = question_format_grade($cmoptions, $state->last_graded->grade);
    }
    $maxgrade = question_format_grade($cmoptions, $question->maxgrade);
    $fieldsize = strlen($maxgrade) - 1;
    if (empty($caption)) {
        $caption = format_string($question->name);
    }
    ?>
<fieldset class="que comment clearfix">
    <legend class="ftoggler"><?php echo $caption; ?></legend>
    <div class="fcontainer clearfix">
        <div class="fitem">
            <div class="fitemtitle">
                <label for="<?php echo $idprefix; ?>_comment_box"><?php print_string('comment', 'quiz'); ?></label>
            </div>
            <div class="felement fhtmleditor">
                <?php print_textarea(can_use_html_editor(), 15, 60, 630, 300, $prefix . '[comment]',
                        $state->manualcomment, 0, false, $idprefix . '_comment_box'); ?>
            </div>
        </div>
        <div class="fitem">
            <div class="fitemtitle">
                <label for="<?php echo $idprefix; ?>_grade_field"><?php print_string('grade', 'quiz'); ?></label>
            </div>
            <div class="felement ftext">
                <input type="text" name="<?php echo $prefix; ?>[grade]" size="<?php echo $fieldsize; ?>" id="<?php echo $idprefix; ?>_grade_field" value="<?php echo $grade; ?>" /> / <?php echo $maxgrade; ?>
            </div>
        </div>
    </div>
</fieldset>
    <?php
}

/**
 * Process a manual grading action. That is, use $comment and $grade to update
 * $state and $attempt. The attempt and the comment text are stored in the
 * database. $state is only updated in memory, it is up to the call to store
 * that, if appropriate.
 *
 * @global object
 * @param object $question the question
 * @param object $state the state to be updated.
 * @param object $attempt the attempt the state belongs to, to be updated.
 * @param string $comment the new comment from the teacher.
 * @param mixed $grade the grade the teacher assigned, or '' to not change the grade.
 * @return mixed true on success, a string error message if a problem is detected
 *         (for example score out of range).
 */
function question_process_comment($question, &$state, &$attempt, $comment, $commentformat, $grade) {
    global $DB;

    $grade = trim($grade);
    if ($grade < 0 || $grade > $question->maxgrade) {
        $a = new stdClass;
        $a->grade = $grade;
        $a->maxgrade = $question->maxgrade;
        $a->name = $question->name;
        return get_string('errormanualgradeoutofrange', 'question', $a);
    }

    // Update the comment and save it in the database
    $comment = trim($comment);
    $state->manualcomment = $comment;
    $state->manualcommentformat = $commentformat;
    $state->newflaggedstate = $state->flagged;
    $DB->set_field('question_sessions', 'manualcomment', $comment, array('attemptid'=>$attempt->uniqueid, 'questionid'=>$question->id));

    // Update the attempt if the score has changed.
    if ($grade !== '' && (abs($state->last_graded->grade - $grade) > 0.002 || $state->last_graded->event != QUESTION_EVENTMANUALGRADE)) {
        $attempt->sumgrades = $attempt->sumgrades - $state->last_graded->grade + $grade;
        $attempt->timemodified = time();
        $DB->update_record('quiz_attempts', $attempt);

        // We want to update existing state (rather than creating new one) if it
        // was itself created by a manual grading event.
        $state->update = $state->event == QUESTION_EVENTMANUALGRADE;

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

    return true;
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
    if (!preg_match('/^resp([0-9]+)_/', $name, $matches)) {
        return false;
    }
    return (integer) $matches[1];
}

/**
 * Extract question id from the prefix of form element names
 *
 * @return integer      The question id
 * @param string $name  The name that contains a prefix that was
 *                      constructed with {@link question_make_name_prefix()}
 */
function question_id_and_key_from_post_name($name) {
    if (!preg_match('/^resp([0-9]+)_(.*)$/', $name, $matches)) {
        return array(false, false);
    }
    return array((integer) $matches[1], $matches[2]);
}

/**
 * Returns the unique id for a new attempt
 *
 * Every module can keep their own attempts table with their own sequential ids but
 * the question code needs to also have a unique id by which to identify all these
 * attempts. Hence a module, when creating a new attempt, calls this function and
 * stores the return value in the 'uniqueid' field of its attempts table.
 *
 * @global object
 */
function question_new_attempt_uniqueid($modulename='quiz') {
    global $DB;

    $attempt = new stdClass;
    $attempt->modulename = $modulename;
    $id = $DB->insert_record('question_attempts', $attempt);
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

/**
 * Round a grade to to the correct number of decimal places, and format it for display.
 * If $cmoptions->questiondecimalpoints is set, that is used, otherwise
 * else if $cmoptions->decimalpoints is used,
 * otherwise a default of 2 is used, but this should not be relied upon, and generated a developer debug warning.
 * However, if $cmoptions->questiondecimalpoints is -1, the means use $cmoptions->decimalpoints.
 *
 * @param object $cmoptions The modules settings.
 * @param float $grade The grade to round.
 */
function question_format_grade($cmoptions, $grade) {
    if (isset($cmoptions->questiondecimalpoints) && $cmoptions->questiondecimalpoints != -1) {
        $decimalplaces = $cmoptions->questiondecimalpoints;
    } else if (isset($cmoptions->decimalpoints)) {
        $decimalplaces = $cmoptions->decimalpoints;
    } else {
        $decimalplaces = 2;
        debugging('Code that leads to question_format_grade being called should set ' .
                '$cmoptions->questiondecimalpoints or $cmoptions->decimalpoints', DEBUG_DEVELOPER);
    }
    return format_float($grade, $decimalplaces);
}

/**
 * @return string An inline script that creates a JavaScript object storing
 * various strings and bits of configuration that the scripts in qengine.js need
 * to get from PHP.
 */
function question_init_qengine_js() {
    global $CFG, $PAGE, $OUTPUT;
    static $done = false;
    if ($done) {
        return;
    }
    $module = array(
        'name' => 'core_question_flags',
        'fullpath' => '/question/flags.js',
        'requires' => array('base', 'dom', 'event-delegate', 'io-base'),
    );
    $actionurl = $CFG->wwwroot . '/question/toggleflag.php';
    $flagattributes = array(
        0 => array(
            'src' => $OUTPUT->pix_url('i/unflagged') . '',
            'title' => get_string('clicktoflag', 'question'),
            'alt' => get_string('notflagged', 'question'),
        ),
        1 => array(
            'src' => $OUTPUT->pix_url('i/flagged') . '',
            'title' => get_string('clicktounflag', 'question'),
            'alt' => get_string('flagged', 'question'),
        ),
    );
    $PAGE->requires->js_init_call('M.core_question_flags.init',
            array($actionurl, $flagattributes), false, $module);
    $done = true;
}

/// FUNCTIONS THAT SIMPLY WRAP QUESTIONTYPE METHODS //////////////////////////////////
/**
 * Give the questions in $questionlist a chance to request the CSS or JavaScript
 * they need, before the header is printed.
 *
 * If your code is going to call the print_question function, it must call this
 * funciton before print_header.
 *
 * @param array $questionlist a list of questionids of the questions what will appear on this page.
 * @param array $questions an array of question objects, whose keys are question ids.
 *      Must contain all the questions in $questionlist
 * @param array $states an array of question state objects, whose keys are question ids.
 *      Must contain the state of all the questions in $questionlist
 */
function question_get_html_head_contributions($questionlist, &$questions, &$states) {
    global $CFG, $PAGE, $QTYPES;

    // The question engine's own JavaScript.
    question_init_qengine_js();

    // Anything that questions on this page need.
    foreach ($questionlist as $questionid) {
        $question = $questions[$questionid];
        $QTYPES[$question->qtype]->get_html_head_contributions($question, $states[$questionid]);
    }
}

/**
 * Like {@link get_html_head_contributions()} but for the editing page
 * question/question.php.
 *
 * @param $question A question object. Only $question->qtype is used.
 * @return string Deprecated. Some HTML code that can go inside the head tag.
 */
function question_get_editing_head_contributions($question) {
    global $QTYPES;
    $QTYPES[$question->qtype]->get_editing_head_contributions();
}

/**
 * Prints a question
 *
 * Simply calls the question type specific print_question() method.
 *
 * @global array
 * @param object $question The question to be rendered.
 * @param object $state    The state to render the question in.
 * @param integer $number  The number for this question.
 * @param object $cmoptions  The options specified by the course module
 * @param object $options  An object specifying the rendering options.
 */
function print_question(&$question, &$state, $number, $cmoptions, $options=null, $context=null) {
    global $QTYPES;
    $QTYPES[$question->qtype]->print_question($question, $state, $number, $cmoptions, $options, $context);
}
/**
 * Saves question options
 *
 * Simply calls the question type specific save_question_options() method.
 *
 * @global array
 */
function save_question_options($question) {
    global $QTYPES;

    $QTYPES[$question->qtype]->save_question_options($question);
}

/**
* Gets all teacher stored answers for a given question
*
* Simply calls the question type specific get_all_responses() method.
 *
 * @global array
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
 *
 * @global array
*/
// ULPGC ecastro
function get_question_actual_response($question, $state) {
    global $QTYPES;

    $r = $QTYPES[$question->qtype]->get_actual_response($question, $state);
    return $r;
}

/**
* TODO: document this
 *
 * @global array
*/
// ULPGc ecastro
function get_question_fraction_grade($question, $state) {
    global $QTYPES;

    $r = $QTYPES[$question->qtype]->get_fractional_grade($question, $state);
    return $r;
}
/**
 * @global array
* @return integer grade out of 1 that a random guess by a student might score.
*/
// ULPGc ecastro
function question_get_random_guess_score($question) {
    global $QTYPES;

    $r = $QTYPES[$question->qtype]->get_random_guess_score($question);
    return $r;
}
/// CATEGORY FUNCTIONS /////////////////////////////////////////////////////////////////

/**
 * returns the categories with their names ordered following parent-child relationships
 * finally it tries to return pending categories (those being orphaned, whose parent is
 * incorrect) to avoid missing any category from original array.
 *
 * @global object
 */
function sort_categories_by_tree(&$categories, $id = 0, $level = 1) {
    global $DB;

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
            // If not processed and it's a good candidate to start (because its parent doesn't exist in the course)
            if (!isset($categories[$key]->processed) && !$DB->record_exists(
                    'question_categories', array('contextid'=>$categories[$key]->contextid, 'id'=>$categories[$key]->parent))) {
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
function flatten_category_tree(&$categories, $id, $depth = 0, $nochildrenof = -1) {

    // Indent the name of this category.
    $newcategories = array();
    $newcategories[$id] = $categories[$id];
    $newcategories[$id]->indentedname = str_repeat('&nbsp;&nbsp;&nbsp;', $depth) . $categories[$id]->name;

    // Recursively indent the children.
    foreach ($categories[$id]->childids as $childid) {
        if ($childid != $nochildrenof){
            $newcategories = $newcategories + flatten_category_tree($categories, $childid, $depth + 1, $nochildrenof);
        }
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
function add_indented_names($categories, $nochildrenof = -1) {

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
        $newcategories = $newcategories + flatten_category_tree($categories, $id, 0, $nochildrenof);
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
function question_category_select_menu($contexts, $top = false, $currentcat = 0, $selected = "", $nochildrenof = -1) {
    global $OUTPUT;
    $categoriesarray = question_category_options($contexts, $top, $currentcat, false, $nochildrenof);
    if ($selected) {
        $choose = '';
    } else {
        $choose = 'choosedots';
    }
    $options = array();
    foreach($categoriesarray as $group=>$opts) {
        $options[] = array($group=>$opts);
    }

    echo html_writer::select($options, 'category', $selected, $choose);
}

/**
 * @global object
 * @param integer $contextid a context id.
 * @return object the default question category for that context, or false if none.
 */
function question_get_default_category($contextid) {
    global $DB;
    $category = $DB->get_records('question_categories', array('contextid' => $contextid),'id','*',0,1);
    if (!empty($category)) {
        return reset($category);
    } else {
        return false;
    }
}

/**
 * @global object
 * @global object
 * @param object $context a context
 * @return string A URL for editing questions in this context.
 */
function question_edit_url($context) {
    global $CFG, $SITE;
    if (!has_any_capability(question_get_question_capabilities(), $context)) {
        return false;
    }
    $baseurl = $CFG->wwwroot . '/question/edit.php?';
    $defaultcategory = question_get_default_category($context->id);
    if ($defaultcategory) {
        $baseurl .= 'cat=' . $defaultcategory->id . ',' . $context->id . '&amp;';
    }
    switch ($context->contextlevel) {
        case CONTEXT_SYSTEM:
            return $baseurl . 'courseid=' . $SITE->id;
        case CONTEXT_COURSECAT:
            // This is nasty, becuase we can only edit questions in a course
            // context at the moment, so for now we just return false.
            return false;
        case CONTEXT_COURSE:
            return $baseurl . 'courseid=' . $context->instanceid;
        case CONTEXT_MODULE:
            return $baseurl . 'cmid=' . $context->instanceid;
    }

}

/**
* Gets the default category in the most specific context.
* If no categories exist yet then default ones are created in all contexts.
*
 * @global object
* @param array $contexts  The context objects for this context and all parent contexts.
* @return object The default category - the category in the course context
*/
function question_make_default_categories($contexts) {
    global $DB;
    static $preferredlevels = array(
        CONTEXT_COURSE => 4,
        CONTEXT_MODULE => 3,
        CONTEXT_COURSECAT => 2,
        CONTEXT_SYSTEM => 1,
    );

    $toreturn = null;
    $preferredness = 0;
    // If it already exists, just return it.
    foreach ($contexts as $key => $context) {
        if (!$exists = $DB->record_exists("question_categories", array('contextid'=>$context->id))) {
            // Otherwise, we need to make one
            $category = new stdClass;
            $contextname = print_context_name($context, false, true);
            $category->name = get_string('defaultfor', 'question', $contextname);
            $category->info = get_string('defaultinfofor', 'question', $contextname);
            $category->contextid = $context->id;
            $category->parent = 0;
            $category->sortorder = 999; // By default, all categories get this number, and are sorted alphabetically.
            $category->stamp = make_unique_id_code();
            $category->id = $DB->insert_record('question_categories', $category);
        } else {
            $category = question_get_default_category($context->id);
        }
        if ($preferredlevels[$context->contextlevel] > $preferredness &&
                has_any_capability(array('moodle/question:usemine', 'moodle/question:useall'), $context)) {
            $toreturn = $category;
            $preferredness = $preferredlevels[$context->contextlevel];
        }
    }

    if (!is_null($toreturn)) {
        $toreturn = clone($toreturn);
    }
    return $toreturn;
}

/**
 * Get all the category objects, including a count of the number of questions in that category,
 * for all the categories in the lists $contexts.
 *
 * @global object
 * @param mixed $contexts either a single contextid, or a comma-separated list of context ids.
 * @param string $sortorder used as the ORDER BY clause in the select statement.
 * @return array of category objects.
 */
function get_categories_for_contexts($contexts, $sortorder = 'parent, sortorder, name ASC') {
    global $DB;
    return $DB->get_records_sql("
            SELECT c.*, (SELECT count(1) FROM {question} q
                        WHERE c.id = q.category AND q.hidden='0' AND q.parent='0') AS questioncount
              FROM {question_categories} c
             WHERE c.contextid IN ($contexts)
          ORDER BY $sortorder");
}

/**
 * Output an array of question categories.
 * @global object
 */
function question_category_options($contexts, $top = false, $currentcat = 0, $popupform = false, $nochildrenof = -1) {
    global $CFG;
    $pcontexts = array();
    foreach($contexts as $context){
        $pcontexts[] = $context->id;
    }
    $contextslist = join($pcontexts, ', ');

    $categories = get_categories_for_contexts($contextslist);

    $categories = question_add_context_in_key($categories);

    if ($top){
        $categories = question_add_tops($categories, $pcontexts);
    }
    $categories = add_indented_names($categories, $nochildrenof);

    //sort cats out into different contexts
    $categoriesarray = array();
    foreach ($pcontexts as $pcontext){
        $contextstring = print_context_name(get_context_instance_by_id($pcontext), true, true);
        foreach ($categories as $category) {
            if ($category->contextid == $pcontext){
                $cid = $category->id;
                if ($currentcat!= $cid || $currentcat==0) {
                    $countstring = (!empty($category->questioncount))?" ($category->questioncount)":'';
                    $categoriesarray[$contextstring][$cid] = $category->indentedname.$countstring;
                }
            }
        }
    }
    if ($popupform){
        $popupcats = array();
        foreach ($categoriesarray as $contextstring => $optgroup){
            $group = array();
            foreach ($optgroup as $key=>$value) {
                $key = str_replace($CFG->wwwroot, '', $key);
                $group[$key] = $value;
            }
            $popupcats[] = array($contextstring=>$group);
        }
        return $popupcats;
    } else {
        return $categoriesarray;
    }
}

function question_add_context_in_key($categories){
    $newcatarray = array();
    foreach ($categories as $id => $category) {
        $category->parent = "$category->parent,$category->contextid";
        $category->id = "$category->id,$category->contextid";
        $newcatarray["$id,$category->contextid"] = $category;
    }
    return $newcatarray;
}
function question_add_tops($categories, $pcontexts){
    $topcats = array();
    foreach ($pcontexts as $context){
        $newcat = new stdClass();
        $newcat->id = "0,$context";
        $newcat->name = get_string('top');
        $newcat->parent = -1;
        $newcat->contextid = $context;
        $topcats["0,$context"] = $newcat;
    }
    //put topcats in at beginning of array - they'll be sorted into different contexts later.
    return array_merge($topcats, $categories);
}

/**
 * Returns a comma separated list of ids of the category and all subcategories
 * @global object
 */
function question_categorylist($categoryid) {
    global $DB;

    // returns a comma separated list of ids of the category and all subcategories
    $categorylist = $categoryid;
    if ($subcategories = $DB->get_records('question_categories', array('parent'=>$categoryid), 'sortorder ASC', 'id, 1')) {
        foreach ($subcategories as $subcategory) {
            $categorylist .= ','. question_categorylist($subcategory->id);
        }
    }
    return $categorylist;
}




//===========================
// Import/Export Functions
//===========================

/**
 * Get list of available import or export formats
 *
 * @global object
 * @param string $type 'import' if import list, otherwise export list assumed
 * @return array sorted list of import/export formats available
 */
function get_import_export_formats( $type ) {

    global $CFG;
    $fileformats = get_plugin_list("qformat");

    $fileformatname=array();
    require_once( "{$CFG->dirroot}/question/format.php" );
    foreach ($fileformats as $fileformat=>$fdir) {
        $format_file = "$fdir/format.php";
        if (file_exists($format_file) ) {
            require_once($format_file);
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
                $formatname = get_string($fileformat, 'qformat_'.$fileformat);
                if ($formatname == "[[$fileformat]]") {
                    $formatname = $fileformat;  // Just use the raw folder name
                }
            }
            $fileformatnames[$fileformat] = $formatname;
        }
    }
    natcasesort($fileformatnames);

    return $fileformatnames;
}


/**
* Create a reasonable default file name for exporting questions from a particular
* category.
* @param object $course the course the questions are in.
* @param object $category the question category.
* @return string the filename.
*/
function question_default_export_filename($course, $category) {
    // We build a string that is an appropriate name (questions) from the lang pack,
    // then the corse shortname, then the question category name, then a timestamp. 

    $base = clean_filename(get_string('exportfilename', 'question'));

    $dateformat = str_replace(' ', '_', get_string('exportnameformat', 'question'));
    $timestamp = clean_filename(userdate(time(), $dateformat, 99, false));

    $shortname = clean_filename($course->shortname);
    if ($shortname == '' || $shortname == '_' ) {
        $shortname = $course->id;
    }

    $categoryname = clean_filename(format_string($category->name));

    return "{$base}-{$shortname}-{$categoryname}-{$timestamp}";

    return $export_name;
}

/**
 * @package moodlecore
 * @subpackage question
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_to_string_translator{
    /**
     * @var array used to translate between contextids and strings for this context.
     */
    var $contexttostringarray = array();

    function context_to_string_translator($contexts){
        $this->generate_context_to_string_array($contexts);
    }

    function context_to_string($contextid){
        return $this->contexttostringarray[$contextid];
    }

    function string_to_context($contextname){
        $contextid = array_search($contextname, $this->contexttostringarray);
        return $contextid;
    }

    function generate_context_to_string_array($contexts){
        if (!$this->contexttostringarray){
            $catno = 1;
            foreach ($contexts as $context){
                switch  ($context->contextlevel){
                    case CONTEXT_MODULE :
                        $contextstring = 'module';
                        break;
                    case CONTEXT_COURSE :
                        $contextstring = 'course';
                        break;
                    case CONTEXT_COURSECAT :
                        $contextstring = "cat$catno";
                        $catno++;
                        break;
                    case CONTEXT_SYSTEM :
                        $contextstring = 'system';
                        break;
                }
                $this->contexttostringarray[$context->id] = $contextstring;
            }
        }
    }

}

/**
 * @return array all the capabilities that relate to accessing particular questions.
 */
function question_get_question_capabilities() {
    return array(
        'moodle/question:add',
        'moodle/question:editmine',
        'moodle/question:editall',
        'moodle/question:viewmine',
        'moodle/question:viewall',
        'moodle/question:usemine',
        'moodle/question:useall',
        'moodle/question:movemine',
        'moodle/question:moveall',
    );
}

/**
 * @return array all the question bank capabilities.
 */
function question_get_all_capabilities() {
    $caps = question_get_question_capabilities();
    $caps[] = 'moodle/question:managecategory';
    $caps[] = 'moodle/question:flag';
    return $caps;
}

/**
 * Check capability on category
 *
 * @global object
 * @global object
 * @param mixed $question object or id
 * @param string $cap 'add', 'edit', 'view', 'use', 'move'
 * @param integer $cachecat useful to cache all question records in a category
 * @return boolean this user has the capability $cap for this question $question?
 */
function question_has_capability_on($question, $cap, $cachecat = -1){
    global $USER, $DB;

    // nicolasconnault@gmail.com In some cases I get $question === false. Since no such object exists, it can't be deleted, we can safely return true
    if ($question === false) {
        return true;
    }

    // these are capabilities on existing questions capabilties are
    //set per category. Each of these has a mine and all version. Append 'mine' and 'all'
    $question_questioncaps = array('edit', 'view', 'use', 'move');
    static $questions = array();
    static $categories = array();
    static $cachedcat = array();
    if ($cachecat != -1 && array_search($cachecat, $cachedcat) === false) {
        $questions += $DB->get_records('question', array('category' => $cachecat));
        $cachedcat[] = $cachecat;
    }
    if (!is_object($question)){
        if (!isset($questions[$question])){
            if (!$questions[$question] = $DB->get_record('question', array('id' => $question), 'id,category,createdby')) {
                print_error('questiondoesnotexist', 'question');
            }
        }
        $question = $questions[$question];
    }
    if (!isset($categories[$question->category])){
        if (!$categories[$question->category] = $DB->get_record('question_categories', array('id'=>$question->category))) {
            print_error('invalidcategory', 'quiz');
        }
    }
    $category = $categories[$question->category];
    $context = get_context_instance_by_id($category->contextid);

    if (array_search($cap, $question_questioncaps)!== FALSE){
        if (!has_capability('moodle/question:'.$cap.'all', $context)){
            if ($question->createdby == $USER->id){
                return has_capability('moodle/question:'.$cap.'mine', $context);
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return has_capability('moodle/question:'.$cap, $context);
    }

}

/**
 * Require capability on question.
 */
function question_require_capability_on($question, $cap){
    if (!question_has_capability_on($question, $cap)){
        print_error('nopermissions', '', '', $cap);
    }
    return true;
}

/**
 * Get the real state - the correct question id and answer - for a random
 * question.
 * @param object $state with property answer.
 * @return mixed return integer real question id or false if there was an
 * error..
 */
function question_get_real_state($state) {
    global $OUTPUT;
    $realstate = clone($state);
    $matches = array();
    if (!preg_match('|^random([0-9]+)-(.*)|', $state->answer, $matches)){
        echo $OUTPUT->notification(get_string('errorrandom', 'quiz_statistics'));
        return false;
    } else {
        $realstate->question = $matches[1];
        $realstate->answer = $matches[2];
        return $realstate;
    }
}

/**
 * Update the flagged state of a particular question session.
 *
 * @global object
 * @param integer $sessionid question_session id.
 * @param boolean $newstate the new state for the flag.
 * @return boolean success or failure.
 */
function question_update_flag($sessionid, $newstate) {
    global $DB;
    return $DB->set_field('question_sessions', 'flagged', $newstate, array('id' => $sessionid));
}

/**
 * Update the flagged state of all the questions in an attempt, where a new .
 *
 * @global object
 * @param integer $sessionid question_session id.
 * @param boolean $newstate the new state for the flag.
 * @return boolean success or failure.
 */
function question_save_flags($formdata, $attemptid, $questionids) {
    global $DB;
    $donequestionids = array();
    foreach ($formdata as $postvariable => $value) {
        list($qid, $key) = question_id_and_key_from_post_name($postvariable);
        if ($qid !== false && in_array($qid, $questionids)) {
            if ($key == '_flagged') {
                $DB->set_field('question_sessions', 'flagged', !empty($value),
                        array('attemptid' => $attemptid, 'questionid' => $qid));
                $donequestionids[$qid] = 1;
            }
        }
    }
    foreach ($questionids as $qid) {
        if (!isset($donequestionids[$qid])) {
            $DB->set_field('question_sessions', 'flagged', 0,
                    array('attemptid' => $attemptid, 'questionid' => $qid));
        }
    }
}

/**
 *
 * @global object
 * @param integer $attemptid the question_attempt id.
 * @param integer $questionid the question id.
 * @param integer $sessionid the question_session id.
 * @param object $user a user, or null to use $USER.
 * @return string that needs to be sent to question/toggleflag.php for it to work.
 */
function question_get_toggleflag_checksum($attemptid, $questionid, $sessionid, $user = null) {
    if (is_null($user)) {
        global $USER;
        $user = $USER;
    }
    return md5($attemptid . "_" . $user->secret . "_" . $questionid . "_" . $sessionid);
}

/**
 * Adds question bank setting links to the given navigation node if caps are met.
 *
 * @param navigation_node $navigationnode The navigation node to add the question branch to
 * @param stdClass $context
 * @return navigation_node Returns the question branch that was added
 */
function question_extend_settings_navigation(navigation_node $navigationnode, $context) {
    global $PAGE;

    if ($context->contextlevel == CONTEXT_COURSE) {
        $params = array('courseid'=>$context->instanceid);
    } else if ($context->contextlevel == CONTEXT_MODULE) {
        $params = array('cmid'=>$context->instanceid);
    } else {
        return;
    }

    $questionnode = $navigationnode->add(get_string('questionbank','question'), new moodle_url('/question/edit.php', $params), navigation_node::TYPE_CONTAINER);

    $contexts = new question_edit_contexts($context);
    if ($contexts->have_one_edit_tab_cap('questions')) {
        $questionnode->add(get_string('questions', 'quiz'), new moodle_url('/question/edit.php', $params), navigation_node::TYPE_SETTING);
    }
    if ($contexts->have_one_edit_tab_cap('categories')) {
        $questionnode->add(get_string('categories', 'quiz'), new moodle_url('/question/category.php', $params), navigation_node::TYPE_SETTING);
    }
    if ($contexts->have_one_edit_tab_cap('import')) {
        $questionnode->add(get_string('import', 'quiz'), new moodle_url('/question/import.php', $params), navigation_node::TYPE_SETTING);
    }
    if ($contexts->have_one_edit_tab_cap('export')) {
        $questionnode->add(get_string('export', 'quiz'), new moodle_url('/question/export.php', $params), navigation_node::TYPE_SETTING);
    }

    return $questionnode;
}

class question_edit_contexts {

    public static $CAPS = array(
        'editq' => array('moodle/question:add',
            'moodle/question:editmine',
            'moodle/question:editall',
            'moodle/question:viewmine',
            'moodle/question:viewall',
            'moodle/question:usemine',
            'moodle/question:useall',
            'moodle/question:movemine',
            'moodle/question:moveall'),
        'questions'=>array('moodle/question:add',
            'moodle/question:editmine',
            'moodle/question:editall',
            'moodle/question:viewmine',
            'moodle/question:viewall',
            'moodle/question:movemine',
            'moodle/question:moveall'),
        'categories'=>array('moodle/question:managecategory'),
        'import'=>array('moodle/question:add'),
        'export'=>array('moodle/question:viewall', 'moodle/question:viewmine'));

    protected $allcontexts;

    /**
     * @param current context
     */
    public function question_edit_contexts($thiscontext){
        $pcontextids = get_parent_contexts($thiscontext);
        $contexts = array($thiscontext);
        foreach ($pcontextids as $pcontextid){
            $contexts[] = get_context_instance_by_id($pcontextid);
        }
        $this->allcontexts = $contexts;
    }
    /**
     * @return array all parent contexts
     */
    public function all(){
        return $this->allcontexts;
    }
    /**
     * @return object lowest context which must be either the module or course context
     */
    public function lowest(){
        return $this->allcontexts[0];
    }
    /**
     * @param string $cap capability
     * @return array parent contexts having capability, zero based index
     */
    public function having_cap($cap){
        $contextswithcap = array();
        foreach ($this->allcontexts as $context){
            if (has_capability($cap, $context)){
                $contextswithcap[] = $context;
            }
        }
        return $contextswithcap;
    }
    /**
     * @param array $caps capabilities
     * @return array parent contexts having at least one of $caps, zero based index
     */
    public function having_one_cap($caps){
        $contextswithacap = array();
        foreach ($this->allcontexts as $context){
            foreach ($caps as $cap){
                if (has_capability($cap, $context)){
                    $contextswithacap[] = $context;
                    break; //done with caps loop
                }
            }
        }
        return $contextswithacap;
    }
    /**
     * @param string $tabname edit tab name
     * @return array parent contexts having at least one of $caps, zero based index
     */
    public function having_one_edit_tab_cap($tabname){
        return $this->having_one_cap(self::$CAPS[$tabname]);
    }
    /**
     * Has at least one parent context got the cap $cap?
     *
     * @param string $cap capability
     * @return boolean
     */
    public function have_cap($cap){
        return (count($this->having_cap($cap)));
    }

    /**
     * Has at least one parent context got one of the caps $caps?
     *
     * @param array $caps capability
     * @return boolean
     */
    public function have_one_cap($caps){
        foreach ($caps as $cap) {
            if ($this->have_cap($cap)) {
                return true;
            }
        }
        return false;
    }
    /**
     * Has at least one parent context got one of the caps for actions on $tabname
     *
     * @param string $tabname edit tab name
     * @return boolean
     */
    public function have_one_edit_tab_cap($tabname){
        return $this->have_one_cap(self::$CAPS[$tabname]);
    }
    /**
     * Throw error if at least one parent context hasn't got the cap $cap
     *
     * @param string $cap capability
     */
    public function require_cap($cap){
        if (!$this->have_cap($cap)){
            print_error('nopermissions', '', '', $cap);
        }
    }
    /**
     * Throw error if at least one parent context hasn't got one of the caps $caps
     *
     * @param array $cap capabilities
     */
     public function require_one_cap($caps) {
        if (!$this->have_one_cap($caps)) {
            $capsstring = join($caps, ', ');
            print_error('nopermissions', '', '', $capsstring);
        }
    }

    /**
     * Throw error if at least one parent context hasn't got one of the caps $caps
     *
     * @param string $tabname edit tab name
     */
    public function require_one_edit_tab_cap($tabname){
        if (!$this->have_one_edit_tab_cap($tabname)) {
            print_error('nopermissions', '', '', 'access question edit tab '.$tabname);
        }
    }
}

/**
 * Rewrite question url, file_rewrite_pluginfile_urls always build url by
 * $file/$contextid/$component/$filearea/$itemid/$pathname_in_text, so we cannot add
 * extra questionid and attempted in url by it, so we create quiz_rewrite_question_urls
 * to build url here
 *
 * @param string $text text being processed
 * @param string $file the php script used to serve files
 * @param int $contextid
 * @param string $component component
 * @param string $filearea filearea
 * @param array $ids other IDs will be used to check file permission
 * @param int $itemid
 * @param array $options
 * @return string
 */
function quiz_rewrite_question_urls($text, $file, $contextid, $component, $filearea, array $ids, $itemid, array $options=null) {
    global $CFG;

    $options = (array)$options;
    if (!isset($options['forcehttps'])) {
        $options['forcehttps'] = false;
    }

    if (!$CFG->slasharguments) {
        $file = $file . '?file=';
    }

    $baseurl = "$CFG->wwwroot/$file/$contextid/$component/$filearea/";

    if (!empty($ids)) {
        $baseurl .= (implode('/', $ids) . '/');
    }

    if ($itemid !== null) {
        $baseurl .= "$itemid/";
    }

    if ($options['forcehttps']) {
        $baseurl = str_replace('http://', 'https://', $baseurl);
    }

    return str_replace('@@PLUGINFILE@@/', $baseurl, $text);
}

/**
 * Called by pluginfile.php to serve files related to the 'question' core
 * component and for files belonging to qtypes.
 *
 * For files that relate to questions in a question_attempt, then we delegate to
 * a function in the component that owns the attempt (for example in the quiz,
 * or in core question preview) to get necessary inforation.
 *
 * (Note that, at the moment, all question file areas relate to questions in
 * attempts, so the If at the start of the last paragraph is always true.)
 *
 * Does not return, either calls send_file_not_found(); or serves the file.
 *
 * @param object $course course settings object
 * @param object $context context object
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 */
function question_pluginfile($course, $context, $component, $filearea, $args, $forcedownload) {
    global $DB, $CFG;

    list($context, $course, $cm) = get_context_info_array($context->id);
    require_login($course, false, $cm);

    if ($filearea === 'export') {
        require_once($CFG->dirroot . '/question/editlib.php');
        $contexts = new question_edit_contexts($context);
        // check export capability
        $contexts->require_one_edit_tab_cap('export');
        $category_id = (int)array_shift($args);
        $format      = array_shift($args);
        $cattofile   = array_shift($args);
        $contexttofile = array_shift($args);
        $filename    = array_shift($args);

        // load parent class for import/export
        require_once($CFG->dirroot . '/question/format.php');
        require_once($CFG->dirroot . '/question/editlib.php');
        require_once($CFG->dirroot . '/question/format/' . $format . '/format.php');

        $classname = 'qformat_' . $format;
        if (!class_exists($classname)) {
            send_file_not_found();
        }

        $qformat = new $classname();

        if (!$category = $DB->get_record('question_categories', array('id' => $category_id))) {
            send_file_not_found();
        }

        $qformat->setCategory($category);
        $qformat->setContexts($contexts->having_one_edit_tab_cap('export'));
        $qformat->setCourse($course);

        if ($cattofile == 'withcategories') {
            $qformat->setCattofile(true);
        } else {
            $qformat->setCattofile(false);
        }

        if ($contexttofile == 'withcontexts') {
            $qformat->setContexttofile(true);
        } else {
            $qformat->setContexttofile(false);
        }

        if (!$qformat->exportpreprocess()) {
            send_file_not_found();
            print_error('exporterror', 'question', $thispageurl->out());
        }

        // export data to moodle file pool
        if (!$content = $qformat->exportprocess(true)) {
            send_file_not_found();
        }

        //DEBUG
        //echo '<textarea cols=90 rows=20>';
        //echo $content;
        //echo '</textarea>';
        //die;
        send_file($content, $filename, 0, 0, true, true, $qformat->mime_type());
    }

    $attemptid = (int)array_shift($args);
    $questionid = (int)array_shift($args);


    if ($attemptid === 0) {
        // preview
        require_once($CFG->dirroot . '/question/previewlib.php');
        return question_preview_question_pluginfile($course, $context,
                $component, $filearea, $attemptid, $questionid, $args, $forcedownload);

    } else {
        $module = $DB->get_field('question_attempts', 'modulename',
                array('id' => $attemptid));

        $dir = get_component_directory($module);
        if (!file_exists("$dir/lib.php")) {
            send_file_not_found();
        }
        include_once("$dir/lib.php");

        $filefunction = $module . '_question_pluginfile';
        if (!function_exists($filefunction)) {
            send_file_not_found();
        }

        $filefunction($course, $context, $component, $filearea, $attemptid, $questionid,
                $args, $forcedownload);

        send_file_not_found();
    }
}

/**
 * Final test for whether a studnet should be allowed to see a particular file.
 * This delegates the decision to the question type plugin.
 *
 * @param object $question The question to be rendered.
 * @param object $state    The state to render the question in.
 * @param object $options  An object specifying the rendering options.
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 */
function question_check_file_access($question, $state, $options, $contextid, $component,
        $filearea, $args, $forcedownload) {
    global $QTYPES;
    return $QTYPES[$question->qtype]->check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args, $forcedownload);
}

/**
 * Create url for question export
 *
 * @param int $contextid, current context
 * @param int $categoryid, categoryid
 * @param string $format
 * @param string $withcategories
 * @param string $ithcontexts
 * @param moodle_url export file url
 */
function question_make_export_url($contextid, $categoryid, $format, $withcategories, $withcontexts, $filename) {
    global $CFG;
    $urlbase = "$CFG->httpswwwroot/pluginfile.php";
    return moodle_url::make_file_url($urlbase, "/$contextid/question/export/{$categoryid}/{$format}/{$withcategories}/{$withcontexts}/{$filename}", true);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function question_pagetypelist($pagetype, $parentcontext, $currentcontext) {
    global $CFG;
    $types = array(
        'question-*'=>get_string('page-question-x', 'question'),
        'question-edit'=>get_string('page-question-edit', 'question'),
        'question-category'=>get_string('page-question-category', 'question'),
        'question-export'=>get_string('page-question-export', 'question'),
        'question-import'=>get_string('page-question-import', 'question')
    );
    if ($currentcontext->contextlevel == CONTEXT_COURSE) {
        require_once($CFG->dirroot . '/course/lib.php');
        return array_merge(course_pagetypelist($pagetype, $parentcontext, $currentcontext), $types);
    } else {
        return $types;
    }
}
