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
 * @package moodlecore
 * @subpackage questionbank
 * @copyright 1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/questiontypebase.php');



/// CONSTANTS ///////////////////////////////////

/**
 * Constant determines the number of answer boxes supplied in the editing
 * form for multiple choice and similar question types.
 */
define("QUESTION_NUMANS", 10);

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

/// FUNCTIONS //////////////////////////////////////////////////////

/**
 * Returns an array of names of activity modules that use this question
 *
 * @deprecated since Moodle 2.1. Use {@link questions_in_use} instead.
 *
 * @param object $questionid
 * @return array of strings
 */
function question_list_instances($questionid) {
    throw new coding_exception('question_list_instances has been deprectated. ' .
            'Please use questions_in_use instead.');
}

/**
 * @param array $questionids of question ids.
 * @return boolean whether any of these questions are being used by any part of Moodle.
 */
function questions_in_use($questionids) {
    global $CFG;

    if (question_engine::questions_in_use($questionids)) {
        return true;
    }

    foreach (get_plugin_list('mod') as $module => $path) {
        $lib = $path . '/lib.php';
        if (is_readable($lib)) {
            include_once($lib);

            $fn = $module . '_questions_in_use';
            if (function_exists($fn)) {
                if ($fn($questionids)) {
                    return true;
                }
            } else {

                // Fallback for legacy modules.
                $fn = $module . '_question_list_instances';
                if (function_exists($fn)) {
                    foreach ($questionids as $questionid) {
                        $instances = $fn($questionid);
                        if (!empty($instances)) {
                            return true;
                        }
                    }
                }
            }
        }
    }

    return false;
}

/**
 * Determine whether there arey any questions belonging to this context, that is whether any of its
 * question categories contain any questions. This will return true even if all the questions are
 * hidden.
 *
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
 *
 * @deprecated since 2.1. Use {@link question_bank::fraction_options()} or
 * {@link question_bank::fraction_options_full()} instead.
 *
 * @return object ->gradeoptionsfull full array ->gradeoptions +ve only
 */
function get_grade_options() {
    $grades = new stdClass();
    $grades->gradeoptions = question_bank::fraction_options();
    $grades->gradeoptionsfull = question_bank::fraction_options_full();

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
    if ($matchgrades == 'error') {
        // if we just need an error...
        foreach ($gradeoptionsfull as $value => $option) {
            // slightly fuzzy test, never check floats for equality :-)
            if (abs($grade - $value) < 0.00001) {
                return $grade;
            }
        }
        // didn't find a match so that's an error
        return false;
    } else if ($matchgrades == 'nearest') {
        // work out nearest value
        $hownear = array();
        foreach ($gradeoptionsfull as $value => $option) {
            if ($grade==$value) {
                return $grade;
            }
            $hownear[ $value ] = abs( $grade - $value );
        }
        // reverse sort list of deltas and grab the last (smallest)
        asort( $hownear, SORT_NUMERIC );
        reset( $hownear );
        return key( $hownear );
    } else {
        return false;
    }
}

/**
 * @deprecated Since Moodle 2.1. Use {@link question_category_in_use} instead.
 * @param integer $categoryid a question category id.
 * @param boolean $recursive whether to check child categories too.
 * @return boolean whether any question in this category is in use.
 */
function question_category_isused($categoryid, $recursive = false) {
    throw new coding_exception('question_category_isused has been deprectated. ' .
            'Please use question_category_in_use instead.');
}

/**
 * Tests whether any question in a category is used by any part of Moodle.
 *
 * @param integer $categoryid a question category id.
 * @param boolean $recursive whether to check child categories too.
 * @return boolean whether any question in this category is in use.
 */
function question_category_in_use($categoryid, $recursive = false) {
    global $DB;

    //Look at each question in the category
    if ($questions = $DB->get_records_menu('question',
            array('category' => $categoryid), '', 'id, 1')) {
        if (questions_in_use(array_keys($questions))) {
            return true;
        }
    }
    if (!$recursive) {
        return false;
    }

    //Look under child categories recursively
    if ($children = $DB->get_records('question_categories',
            array('parent' => $categoryid), '', 'id, 1')) {
        foreach ($children as $child) {
            if (question_category_in_use($child->id, $recursive)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Deletes question and all associated data from the database
 *
 * It will not delete a question if it is used by an activity module
 * @param object $question  The question being deleted
 */
function question_delete_question($questionid) {
    global $DB;

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
    if (questions_in_use(array($questionid))) {
        return;
    }

    // Check permissions.
    question_require_capability_on($question, 'edit');

    $dm = new question_engine_data_mapper();
    $dm->delete_previews($questionid);

    // delete questiontype-specific data
    question_bank::get_qtype($question->qtype, false)->delete_question(
            $questionid, $question->contextid);

    // Now recursively delete all child questions
    if ($children = $DB->get_records('question',
            array('parent' => $questionid), '', 'id, qtype')) {
        foreach ($children as $child) {
            if ($child->id != $questionid) {
                question_delete_question($child->id);
            }
        }
    }

    // Finally delete the question record itself
    $DB->delete_records('question', array('id' => $questionid));
}

/**
 * All question categories and their questions are deleted for this course.
 *
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
    $categoriescourse = $DB->get_records('question_categories',
            array('contextid' => $coursecontext->id), 'parent', 'id, parent, name, contextid');

    if ($categoriescourse) {

        //Sort categories following their tree (parent-child) relationships
        //this will make the feedback more readable
        $categoriescourse = sort_categories_by_tree($categoriescourse);

        foreach ($categoriescourse as $category) {

            //Delete it completely (questions and category itself)
            //deleting questions
            if ($questions = $DB->get_records('question',
                    array('category' => $category->id), '', 'id,qtype')) {
                foreach ($questions as $question) {
                    question_delete_question($question->id);
                }
                $DB->delete_records("question", array("category" => $category->id));
            }
            //delete the category
            $DB->delete_records('question_categories', array('id' => $category->id));

            //Fill feedback
            $feedbackdata[] = array($category->name, $strcatdeleted);
        }
        //Inform about changes performed if feedback is enabled
        if ($feedback) {
            $table = new html_table();
            $table->head = array(get_string('category', 'quiz'), get_string('action'));
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
 * @param object $category course category object
 * @param object $newcategory empty means everything deleted, otherwise id of
 *      category where content moved
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
        if ($categories = $DB->get_records('question_categories',
                array('contextid'=>$context->id), 'parent', 'id, parent, name')) {
            foreach ($categories as $category) {

                // Deal with any questions in the category.
                if ($questions = $DB->get_records('question',
                        array('category' => $category->id), '', 'id,qtype')) {

                    // Try to delete each question.
                    foreach ($questions as $question) {
                        question_delete_question($question->id);
                    }

                    // Check to see if there were any questions that were kept because
                    // they are still in use somehow, even though quizzes in courses
                    // in this category will already have been deteted. This could
                    // happen, for example, if questions are added to a course,
                    // and then that course is moved to another category (MDL-14802).
                    $questionids = $DB->get_records_menu('question',
                            array('category'=>$category->id), '', 'id, 1');
                    if (!empty($questionids)) {
                        if (!$rescueqcategory = question_save_from_deletion(
                                array_keys($questionids), get_parent_contextid($context),
                                print_context_name($context), $rescueqcategory)) {
                            return false;
                        }
                        $feedbackdata[] = array($category->name,
                            get_string('questionsmovedto', 'question', $rescueqcategory->name));
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
            $table->head = array(get_string('questioncategory', 'question'), get_string('action'));
            $table->data = $feedbackdata;
            echo html_writer::table($table);
        }

    } else {
        // Move question categories ot the new context.
        if (!$newcontext = get_context_instance(CONTEXT_COURSECAT, $newcategory->id)) {
            return false;
        }
        $DB->set_field('question_categories', 'contextid', $newcontext->id,
                array('contextid'=>$context->id));
        if ($feedback) {
            $a = new stdClass();
            $a->oldplace = print_context_name($context);
            $a->newplace = print_context_name($newcontext);
            echo $OUTPUT->notification(
                    get_string('movedquestionsandcategories', 'question', $a), 'notifysuccess');
        }
    }

    return true;
}

/**
 * Enter description here...
 *
 * @param array $questionids of question ids
 * @param object $newcontext the context to create the saved category in.
 * @param string $oldplace a textual description of the think being deleted,
 *      e.g. from get_context_name
 * @param object $newcategory
 * @return mixed false on
 */
function question_save_from_deletion($questionids, $newcontextid, $oldplace,
        $newcategory = null) {
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
    if ($categoriesmods = $DB->get_records('question_categories',
            array('contextid' => $modcontext->id), 'parent', 'id, parent, name, contextid')) {
        //Sort categories following their tree (parent-child) relationships
        //this will make the feedback more readable
        $categoriesmods = sort_categories_by_tree($categoriesmods);

        foreach ($categoriesmods as $category) {

            //Delete it completely (questions and category itself)
            //deleting questions
            if ($questions = $DB->get_records('question',
                    array('category' => $category->id), '', 'id,qtype')) {
                foreach ($questions as $question) {
                    question_delete_question($question->id);
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
            $table->head = array(get_string('category', 'quiz'), get_string('action'));
            $table->data = $feedbackdata;
            echo html_writer::table($table);
        }
    }
    return true;
}

/**
 * This function should be considered private to the question bank, it is called from
 * question/editlib.php question/contextmoveq.php and a few similar places to to the
 * work of acutally moving questions and associated data. However, callers of this
 * function also have to do other work, which is why you should not call this method
 * directly from outside the questionbank.
 *
 * @param array $questionids of question ids.
 * @param integer $newcategoryid the id of the category to move to.
 */
function question_move_questions_to_category($questionids, $newcategoryid) {
    global $DB;

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
            question_bank::get_qtype($question->qtype)->move_files(
                    $question->id, $question->contextid, $newcontextid);
        }
    }

    // Move the questions themselves.
    $DB->set_field_select('question', 'category', $newcategoryid,
            "id $questionidcondition", $params);

    // Move any subquestions belonging to them.
    $DB->set_field_select('question', 'category', $newcategoryid,
            "parent $questionidcondition", $params);

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
    global $DB;

    $questionids = $DB->get_records_menu('question',
            array('category' => $categoryid), '', 'id,qtype');
    foreach ($questionids as $questionid => $qtype) {
        question_bank::get_qtype($qtype)->move_files(
                $questionid, $oldcontextid, $newcontextid);
    }

    $subcatids = $DB->get_records_menu('question_categories',
            array('parent' => $categoryid), '', 'id,1');
    foreach ($subcatids as $subcatid => $notused) {
        $DB->set_field('question_categories', 'contextid', $newcontextid,
                array('id' => $subcatid));
        question_move_category_to_context($subcatid, $oldcontextid, $newcontextid);
    }
}

/**
 * Generate the URL for starting a new preview of a given question with the given options.
 * @param integer $questionid the question to preview.
 * @param string $preferredbehaviour the behaviour to use for the preview.
 * @param float $maxmark the maximum to mark the question out of.
 * @param question_display_options $displayoptions the display options to use.
 * @param int $variant the variant of the question to preview. If null, one will
 *      be picked randomly.
 * @param object $context context to run the preview in (affects things like
 *      filter settings, theme, lang, etc.) Defaults to $PAGE->context.
 * @return string the URL.
 */
function question_preview_url($questionid, $preferredbehaviour = null,
        $maxmark = null, $displayoptions = null, $variant = null, $context = null) {

    $params = array('id' => $questionid);

    if (is_null($context)) {
        global $PAGE;
        $context = $PAGE->context;
    }
    if ($context->contextlevel == CONTEXT_MODULE) {
        $params['cmid'] = $context->instanceid;
    } else if ($context->contextlevel == CONTEXT_COURSE) {
        $params['courseid'] = $context->instanceid;
    }

    if (!is_null($preferredbehaviour)) {
        $params['behaviour'] = $preferredbehaviour;
    }

    if (!is_null($maxmark)) {
        $params['maxmark'] = $maxmark;
    }

    if (!is_null($displayoptions)) {
        $params['correctness']     = $displayoptions->correctness;
        $params['marks']           = $displayoptions->marks;
        $params['markdp']          = $displayoptions->markdp;
        $params['feedback']        = (bool) $displayoptions->feedback;
        $params['generalfeedback'] = (bool) $displayoptions->generalfeedback;
        $params['rightanswer']     = (bool) $displayoptions->rightanswer;
        $params['history']         = (bool) $displayoptions->history;
    }

    if ($variant) {
        $params['variant'] = $variant;
    }

    return new moodle_url('/question/preview.php', $params);
}

/**
 * @return array that can be passed as $params to the {@link popup_action} constructor.
 */
function question_preview_popup_params() {
    return array(
        'height' => 600,
        'width' => 800,
    );
}

/**
 * Given a list of ids, load the basic information about a set of questions from
 * the questions table. The $join and $extrafields arguments can be used together
 * to pull in extra data. See, for example, the usage in mod/quiz/attemptlib.php, and
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
function question_preload_questions($questionids, $extrafields = '', $join = '',
        $extraparams = array()) {
    global $DB;
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
    $sql = 'SELECT q.*, qc.contextid' . $extrafields . ' FROM {question} q
            JOIN {question_categories} qc ON q.category = qc.id' .
            $join .
          ' WHERE q.id ' . $questionidcondition;

    // Load the questions
    if (!$questions = $DB->get_records_sql($sql, $extraparams + $params)) {
        return array();
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
 * @param object $question the question to tidy.
 * @param boolean $loadtags load the question tags from the tags table. Optional, default false.
 */
function _tidy_question($question, $loadtags = false) {
    global $CFG;
    if (!question_bank::is_qtype_installed($question->qtype)) {
        $question->questiontext = html_writer::tag('p', get_string('warningmissingtype',
                'qtype_missingtype')) . $question->questiontext;
    }
    question_bank::get_qtype($question->qtype)->get_question_options($question);
    if (isset($question->_partiallyloaded)) {
        unset($question->_partiallyloaded);
    }
    if ($loadtags && !empty($CFG->usetags)) {
        require_once($CFG->dirroot . '/tag/lib.php');
        $question->tags = tag_get_tags_array('question', $question->id);
    }
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
            _tidy_question($questions[$i], $loadtags);
        }
    } else { // deal with single question
        _tidy_question($questions, $loadtags);
    }
    return true;
}

/**
 * Print the icon for the question type
 *
 * @param object $question The question object for which the icon is required.
 *       Only $question->qtype is used.
 * @return string the HTML for the img tag.
 */
function print_question_icon($question) {
    global $PAGE;
    return $PAGE->get_renderer('question', 'bank')->qtype_icon($question->qtype);
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
 * Get anything that needs to be included in the head of the question editing page
 * for a particular question type. This function is called by question/question.php.
 *
 * @param $question A question object. Only $question->qtype is used.
 * @return string Deprecated. Some HTML code that can go inside the head tag.
 */
function question_get_editing_head_contributions($question) {
    question_bank::get_qtype($question->qtype, false)->get_editing_head_contributions();
}

/**
 * Saves question options
 *
 * Simply calls the question type specific save_question_options() method.
 */
function save_question_options($question) {
    question_bank::get_qtype($question->qtype)->save_question_options($question);
}

/// CATEGORY FUNCTIONS /////////////////////////////////////////////////////////////////

/**
 * returns the categories with their names ordered following parent-child relationships
 * finally it tries to return pending categories (those being orphaned, whose parent is
 * incorrect) to avoid missing any category from original array.
 */
function sort_categories_by_tree(&$categories, $id = 0, $level = 1) {
    global $DB;

    $children = array();
    $keys = array_keys($categories);

    foreach ($keys as $key) {
        if (!isset($categories[$key]->processed) && $categories[$key]->parent == $id) {
            $children[$key] = $categories[$key];
            $categories[$key]->processed = true;
            $children = $children + sort_categories_by_tree(
                    $categories, $children[$key]->id, $level+1);
        }
    }
    //If level = 1, we have finished, try to look for non processed categories
    // (bad parent) and sort them too
    if ($level == 1) {
        foreach ($keys as $key) {
            // If not processed and it's a good candidate to start (because its
            // parent doesn't exist in the course)
            if (!isset($categories[$key]->processed) && !$DB->record_exists('question_categories',
                    array('contextid' => $categories[$key]->contextid,
                            'id' => $categories[$key]->parent))) {
                $children[$key] = $categories[$key];
                $categories[$key]->processed = true;
                $children = $children + sort_categories_by_tree(
                        $categories, $children[$key]->id, $level + 1);
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
    $newcategories[$id]->indentedname = str_repeat('&nbsp;&nbsp;&nbsp;', $depth) .
            $categories[$id]->name;

    // Recursively indent the children.
    foreach ($categories[$id]->childids as $childid) {
        if ($childid != $nochildrenof) {
            $newcategories = $newcategories + flatten_category_tree(
                    $categories, $childid, $depth + 1, $nochildrenof);
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

    // Add an array to each category to hold the child category ids. This array
    // will be removed again by flatten_category_tree(). It should not be used
    // outside these two functions.
    foreach (array_keys($categories) as $id) {
        $categories[$id]->childids = array();
    }

    // Build the tree structure, and record which categories are top-level.
    // We have to be careful, because the categories array may include published
    // categories from other courses, but not their parents.
    $toplevelcategoryids = array();
    foreach (array_keys($categories) as $id) {
        if (!empty($categories[$id]->parent) &&
                array_key_exists($categories[$id]->parent, $categories)) {
            $categories[$categories[$id]->parent]->childids[] = $id;
        } else {
            $toplevelcategoryids[] = $id;
        }
    }

    // Flatten the tree to and add the indents.
    $newcategories = array();
    foreach ($toplevelcategoryids as $id) {
        $newcategories = $newcategories + flatten_category_tree(
                $categories, $id, 0, $nochildrenof);
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
 * @param integer $selected optionally, the id of a category to be selected by
 *      default in the dropdown.
 */
function question_category_select_menu($contexts, $top = false, $currentcat = 0,
        $selected = "", $nochildrenof = -1) {
    global $OUTPUT;
    $categoriesarray = question_category_options($contexts, $top, $currentcat,
            false, $nochildrenof);
    if ($selected) {
        $choose = '';
    } else {
        $choose = 'choosedots';
    }
    $options = array();
    foreach ($categoriesarray as $group => $opts) {
        $options[] = array($group => $opts);
    }

    echo html_writer::select($options, 'category', $selected, $choose);
}

/**
 * @param integer $contextid a context id.
 * @return object the default question category for that context, or false if none.
 */
function question_get_default_category($contextid) {
    global $DB;
    $category = $DB->get_records('question_categories',
            array('contextid' => $contextid), 'id', '*', 0, 1);
    if (!empty($category)) {
        return reset($category);
    } else {
        return false;
    }
}

/**
 * Gets the default category in the most specific context.
 * If no categories exist yet then default ones are created in all contexts.
 *
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
        if (!$exists = $DB->record_exists("question_categories",
                array('contextid' => $context->id))) {
            // Otherwise, we need to make one
            $category = new stdClass();
            $contextname = print_context_name($context, false, true);
            $category->name = get_string('defaultfor', 'question', $contextname);
            $category->info = get_string('defaultinfofor', 'question', $contextname);
            $category->contextid = $context->id;
            $category->parent = 0;
            // By default, all categories get this number, and are sorted alphabetically.
            $category->sortorder = 999;
            $category->stamp = make_unique_id_code();
            $category->id = $DB->insert_record('question_categories', $category);
        } else {
            $category = question_get_default_category($context->id);
        }
        if ($preferredlevels[$context->contextlevel] > $preferredness && has_any_capability(
                array('moodle/question:usemine', 'moodle/question:useall'), $context)) {
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
 */
function question_category_options($contexts, $top = false, $currentcat = 0,
        $popupform = false, $nochildrenof = -1) {
    global $CFG;
    $pcontexts = array();
    foreach ($contexts as $context) {
        $pcontexts[] = $context->id;
    }
    $contextslist = join($pcontexts, ', ');

    $categories = get_categories_for_contexts($contextslist);

    $categories = question_add_context_in_key($categories);

    if ($top) {
        $categories = question_add_tops($categories, $pcontexts);
    }
    $categories = add_indented_names($categories, $nochildrenof);

    // sort cats out into different contexts
    $categoriesarray = array();
    foreach ($pcontexts as $pcontext) {
        $contextstring = print_context_name(
                get_context_instance_by_id($pcontext), true, true);
        foreach ($categories as $category) {
            if ($category->contextid == $pcontext) {
                $cid = $category->id;
                if ($currentcat != $cid || $currentcat == 0) {
                    $countstring = !empty($category->questioncount) ?
                            " ($category->questioncount)" : '';
                    $categoriesarray[$contextstring][$cid] = $category->indentedname.$countstring;
                }
            }
        }
    }
    if ($popupform) {
        $popupcats = array();
        foreach ($categoriesarray as $contextstring => $optgroup) {
            $group = array();
            foreach ($optgroup as $key => $value) {
                $key = str_replace($CFG->wwwroot, '', $key);
                $group[$key] = $value;
            }
            $popupcats[] = array($contextstring => $group);
        }
        return $popupcats;
    } else {
        return $categoriesarray;
    }
}

function question_add_context_in_key($categories) {
    $newcatarray = array();
    foreach ($categories as $id => $category) {
        $category->parent = "$category->parent,$category->contextid";
        $category->id = "$category->id,$category->contextid";
        $newcatarray["$id,$category->contextid"] = $category;
    }
    return $newcatarray;
}

function question_add_tops($categories, $pcontexts) {
    $topcats = array();
    foreach ($pcontexts as $context) {
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
 * @return array of question category ids of the category and all subcategories.
 */
function question_categorylist($categoryid) {
    global $DB;

    $subcategories = $DB->get_records('question_categories',
            array('parent' => $categoryid), 'sortorder ASC', 'id, 1');

    $categorylist = array($categoryid);
    foreach ($subcategories as $subcategory) {
        $categorylist = array_merge($categorylist, question_categorylist($subcategory->id));
    }

    return $categorylist;
}

//===========================
// Import/Export Functions
//===========================

/**
 * Get list of available import or export formats
 * @param string $type 'import' if import list, otherwise export list assumed
 * @return array sorted list of import/export formats available
 */
function get_import_export_formats($type) {
    global $CFG;

    $fileformats = get_plugin_list('qformat');

    $fileformatname = array();
    require_once($CFG->dirroot . '/question/format.php');
    foreach ($fileformats as $fileformat => $fdir) {
        $formatfile = $fdir . '/format.php';
        if (is_readable($formatfile)) {
            include_once($formatfile);
        } else {
            continue;
        }

        $classname = 'qformat_' . $fileformat;
        $formatclass = new $classname();
        if ($type == 'import') {
            $provided = $formatclass->provide_import();
        } else {
            $provided = $formatclass->provide_export();
        }

        if ($provided) {
            $fileformatnames[$fileformat] = get_string($fileformat, 'qformat_' . $fileformat);
        }
    }

    textlib_get_instance()->asort($fileformatnames);
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
 * Converts contextlevels to strings and back to help with reading/writing contexts
 * to/from import/export files.
 *
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_to_string_translator{
    /**
     * @var array used to translate between contextids and strings for this context.
     */
    protected $contexttostringarray = array();

    public function __construct($contexts) {
        $this->generate_context_to_string_array($contexts);
    }

    public function context_to_string($contextid) {
        return $this->contexttostringarray[$contextid];
    }

    public function string_to_context($contextname) {
        $contextid = array_search($contextname, $this->contexttostringarray);
        return $contextid;
    }

    protected function generate_context_to_string_array($contexts) {
        if (!$this->contexttostringarray) {
            $catno = 1;
            foreach ($contexts as $context) {
                switch ($context->contextlevel) {
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
 * Check capability on category
 *
 * @param mixed $question object or id
 * @param string $cap 'add', 'edit', 'view', 'use', 'move'
 * @param integer $cachecat useful to cache all question records in a category
 * @return boolean this user has the capability $cap for this question $question?
 */
function question_has_capability_on($question, $cap, $cachecat = -1) {
    global $USER, $DB;

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
    if (!is_object($question)) {
        if (!isset($questions[$question])) {
            if (!$questions[$question] = $DB->get_record('question',
                    array('id' => $question), 'id,category,createdby')) {
                print_error('questiondoesnotexist', 'question');
            }
        }
        $question = $questions[$question];
    }
    if (empty($question->category)) {
        // This can happen when we have created a fake 'missingtype' question to
        // take the place of a deleted question.
        return false;
    }
    if (!isset($categories[$question->category])) {
        if (!$categories[$question->category] = $DB->get_record('question_categories',
                array('id'=>$question->category))) {
            print_error('invalidcategory', 'quiz');
        }
    }
    $category = $categories[$question->category];
    $context = get_context_instance_by_id($category->contextid);

    if (array_search($cap, $question_questioncaps)!== false) {
        if (!has_capability('moodle/question:' . $cap . 'all', $context)) {
            if ($question->createdby == $USER->id) {
                return has_capability('moodle/question:' . $cap . 'mine', $context);
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return has_capability('moodle/question:' . $cap, $context);
    }

}

/**
 * Require capability on question.
 */
function question_require_capability_on($question, $cap) {
    if (!question_has_capability_on($question, $cap)) {
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
    if (!preg_match('|^random([0-9]+)-(.*)|', $state->answer, $matches)) {
        echo $OUTPUT->notification(get_string('errorrandom', 'quiz_statistics'));
        return false;
    } else {
        $realstate->question = $matches[1];
        $realstate->answer = $matches[2];
        return $realstate;
    }
}

/**
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
 * Adds question bank setting links to the given navigation node if caps are met.
 *
 * @param navigation_node $navigationnode The navigation node to add the question branch to
 * @param object $context
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

    if (($cat = $PAGE->url->param('cat')) && preg_match('~\d+,\d+~', $cat)) {
        $params['cat'] = $cat;
    }

    $questionnode = $navigationnode->add(get_string('questionbank', 'question'),
            new moodle_url('/question/edit.php', $params), navigation_node::TYPE_CONTAINER);

    $contexts = new question_edit_contexts($context);
    if ($contexts->have_one_edit_tab_cap('questions')) {
        $questionnode->add(get_string('questions', 'quiz'), new moodle_url(
                '/question/edit.php', $params), navigation_node::TYPE_SETTING);
    }
    if ($contexts->have_one_edit_tab_cap('categories')) {
        $questionnode->add(get_string('categories', 'quiz'), new moodle_url(
                '/question/category.php', $params), navigation_node::TYPE_SETTING);
    }
    if ($contexts->have_one_edit_tab_cap('import')) {
        $questionnode->add(get_string('import', 'quiz'), new moodle_url(
                '/question/import.php', $params), navigation_node::TYPE_SETTING);
    }
    if ($contexts->have_one_edit_tab_cap('export')) {
        $questionnode->add(get_string('export', 'quiz'), new moodle_url(
                '/question/export.php', $params), navigation_node::TYPE_SETTING);
    }

    return $questionnode;
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

class question_edit_contexts {

    public static $caps = array(
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
    public function __construct($thiscontext) {
        $pcontextids = get_parent_contexts($thiscontext);
        $contexts = array($thiscontext);
        foreach ($pcontextids as $pcontextid) {
            $contexts[] = get_context_instance_by_id($pcontextid);
        }
        $this->allcontexts = $contexts;
    }
    /**
     * @return array all parent contexts
     */
    public function all() {
        return $this->allcontexts;
    }
    /**
     * @return object lowest context which must be either the module or course context
     */
    public function lowest() {
        return $this->allcontexts[0];
    }
    /**
     * @param string $cap capability
     * @return array parent contexts having capability, zero based index
     */
    public function having_cap($cap) {
        $contextswithcap = array();
        foreach ($this->allcontexts as $context) {
            if (has_capability($cap, $context)) {
                $contextswithcap[] = $context;
            }
        }
        return $contextswithcap;
    }
    /**
     * @param array $caps capabilities
     * @return array parent contexts having at least one of $caps, zero based index
     */
    public function having_one_cap($caps) {
        $contextswithacap = array();
        foreach ($this->allcontexts as $context) {
            foreach ($caps as $cap) {
                if (has_capability($cap, $context)) {
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
    public function having_one_edit_tab_cap($tabname) {
        return $this->having_one_cap(self::$caps[$tabname]);
    }
    /**
     * Has at least one parent context got the cap $cap?
     *
     * @param string $cap capability
     * @return boolean
     */
    public function have_cap($cap) {
        return (count($this->having_cap($cap)));
    }

    /**
     * Has at least one parent context got one of the caps $caps?
     *
     * @param array $caps capability
     * @return boolean
     */
    public function have_one_cap($caps) {
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
    public function have_one_edit_tab_cap($tabname) {
        return $this->have_one_cap(self::$caps[$tabname]);
    }

    /**
     * Throw error if at least one parent context hasn't got the cap $cap
     *
     * @param string $cap capability
     */
    public function require_cap($cap) {
        if (!$this->have_cap($cap)) {
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
    public function require_one_edit_tab_cap($tabname) {
        if (!$this->have_one_edit_tab_cap($tabname)) {
            print_error('nopermissions', '', '', 'access question edit tab '.$tabname);
        }
    }
}

/**
 * Helps call file_rewrite_pluginfile_urls with the right parameters.
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
function question_rewrite_question_urls($text, $file, $contextid, $component,
        $filearea, array $ids, $itemid, array $options=null) {

    $idsstr = '';
    if (!empty($ids)) {
        $idsstr .= implode('/', $ids);
    }
    if ($itemid !== null) {
        $idsstr .= '/' . $itemid;
    }
    return file_rewrite_pluginfile_urls($text, $file, $contextid, $component,
            $filearea, $idsstr, $options);
}

/**
 * Rewrite the PLUGINFILE urls in the questiontext, when viewing the question
 * text outside and attempt (for example, in the question bank listing or in the
 * quiz statistics report).
 *
 * @param string $questiontext the question text.
 * @param int $contextid the context the text is being displayed in.
 * @param string $component component
 * @param array $ids other IDs will be used to check file permission
 * @param array $options
 * @return string $questiontext with URLs rewritten.
 */
function question_rewrite_questiontext_preview_urls($questiontext, $contextid,
        $component, $questionid, $options=null) {

    return file_rewrite_pluginfile_urls($questiontext, 'pluginfile.php', $contextid,
            'question', 'questiontext_preview', "$component/$questionid", $options);
}

/**
 * Send a file from the question text of a question.
 * @param int $questionid the question id
 * @param array $args the remaining file arguments (file path).
 * @param bool $forcedownload whether the user must be forced to download the file.
 */
function question_send_questiontext_file($questionid, $args, $forcedownload) {
    global $DB;

    $question = $DB->get_record_sql('
            SELECT q.id, qc.contextid
              FROM {question} q
              JOIN {question_categories} qc ON qc.id = q.category
             WHERE q.id = :id', array('id' => $questionid), MUST_EXIST);

    $fs = get_file_storage();
    $fullpath = "/$question->contextid/question/questiontext/$question->id/" . implode('/', $args);
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload);
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

    if ($filearea === 'questiontext_preview') {
        $component = array_shift($args);
        $questionid = array_shift($args);

        component_callback($component, 'questiontext_preview_pluginfile', array(
                $context, $questionid, $args, $forcedownload));

        send_file_not_found();
    }

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

        send_file($content, $filename, 0, 0, true, true, $qformat->mime_type());
    }

    $qubaid = (int)array_shift($args);
    $slot = (int)array_shift($args);

    $module = $DB->get_field('question_usages', 'component',
            array('id' => $qubaid));

    if ($module === 'core_question_preview') {
        require_once($CFG->dirroot . '/question/previewlib.php');
        return question_preview_question_pluginfile($course, $context,
                $component, $filearea, $qubaid, $slot, $args, $forcedownload);

    } else {
        $dir = get_component_directory($module);
        if (!file_exists("$dir/lib.php")) {
            send_file_not_found();
        }
        include_once("$dir/lib.php");

        $filefunction = $module . '_question_pluginfile';
        if (!function_exists($filefunction)) {
            send_file_not_found();
        }

        $filefunction($course, $context, $component, $filearea, $qubaid, $slot,
                $args, $forcedownload);

        send_file_not_found();
    }
}

/**
 * Serve questiontext files in the question text when they are displayed in this report.
 * @param context $context the context
 * @param int $questionid the question id
 * @param array $args remaining file args
 * @param bool $forcedownload
 */
function core_question_questiontext_preview_pluginfile($context, $questionid, $args, $forcedownload) {
    global $DB;

    // Verify that contextid matches the question.
    $question = $DB->get_record_sql('
            SELECT q.*, qc.contextid
              FROM {question} q
              JOIN {question_categories} qc ON qc.id = q.category
             WHERE q.id = :id AND qc.contextid = :contextid',
            array('id' => $questionid, 'contextid' => $context->id), MUST_EXIST);

    // Check the capability.
    list($context, $course, $cm) = get_context_info_array($context->id);
    require_login($course, false, $cm);

    question_require_capability_on($question, 'use');

    question_send_questiontext_file($questionid, $args, $forcedownload);
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
function question_make_export_url($contextid, $categoryid, $format, $withcategories,
        $withcontexts, $filename) {
    global $CFG;
    $urlbase = "$CFG->httpswwwroot/pluginfile.php";
    return moodle_url::make_file_url($urlbase,
            "/$contextid/question/export/{$categoryid}/{$format}/{$withcategories}" .
            "/{$withcontexts}/{$filename}", true);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function question_page_type_list($pagetype, $parentcontext, $currentcontext) {
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
        return array_merge(course_page_type_list($pagetype, $parentcontext, $currentcontext), $types);
    } else {
        return $types;
    }
}
