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
 * @param array $questionids of question ids.
 * @return boolean whether any of these questions are being used by any part of Moodle.
 */
function questions_in_use($questionids) {

    // Are they used by the core question system?
    if (question_engine::questions_in_use($questionids)) {
        return true;
    }

    // Check if any plugins are using these questions.
    $callbacksbytype = get_plugins_with_function('questions_in_use');
    foreach ($callbacksbytype as $callbacks) {
        foreach ($callbacks as $function) {
            if ($function($questionids)) {
                return true;
            }
        }
    }

    // Finally check legacy callback.
    $legacycallbacks = get_plugin_list_with_function('mod', 'question_list_instances');
    foreach ($legacycallbacks as $plugin => $function) {
        if (isset($callbacksbytype['mod'][substr($plugin, 4)])) {
            continue; // Already done.
        }

        foreach ($questionids as $questionid) {
            if (!empty($function($questionid))) {
                return true;
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
 * Check whether a given grade is one of a list of allowed options. If not,
 * depending on $matchgrades, either return the nearest match, or return false
 * to signal an error.
 * @param array $gradeoptionsfull list of valid options
 * @param int $grade grade to be tested
 * @param string $matchgrades 'error' or 'nearest'
 * @return mixed either 'fixed' value or false if error.
 */
function match_grade_options($gradeoptionsfull, $grade, $matchgrades = 'error') {

    if ($matchgrades == 'error') {
        // (Almost) exact match, or an error.
        foreach ($gradeoptionsfull as $value => $option) {
            // Slightly fuzzy test, never check floats for equality.
            if (abs($grade - $value) < 0.00001) {
                return $value; // Be sure the return the proper value.
            }
        }
        // Didn't find a match so that's an error.
        return false;

    } else if ($matchgrades == 'nearest') {
        // Work out nearest value
        $best = false;
        $bestmismatch = 2;
        foreach ($gradeoptionsfull as $value => $option) {
            $newmismatch = abs($grade - $value);
            if ($newmismatch < $bestmismatch) {
                $best = $value;
                $bestmismatch = $newmismatch;
            }
        }
        return $best;

    } else {
        // Unknow option passed.
        throw new coding_exception('Unknown $matchgrades ' . $matchgrades .
                ' passed to match_grade_options');
    }
}

/**
 * Remove stale questions from a category.
 *
 * While questions should not be left behind when they are not used any more,
 * it does happen, maybe via restore, or old logic, or uncovered scenarios. When
 * this happens, the users are unable to delete the question category unless
 * they move those stale questions to another one category, but to them the
 * category is empty as it does not contain anything. The purpose of this function
 * is to detect the questions that may have gone stale and remove them.
 *
 * You will typically use this prior to checking if the category contains questions.
 *
 * The stale questions (unused and hidden to the user) handled are:
 * - hidden questions
 * - random questions
 *
 * @param int $categoryid The category ID.
 */
function question_remove_stale_questions_from_category($categoryid) {
    global $DB;

    $select = 'category = :categoryid AND (qtype = :qtype OR hidden = :hidden)';
    $params = ['categoryid' => $categoryid, 'qtype' => 'random', 'hidden' => 1];
    $questions = $DB->get_recordset_select("question", $select, $params, '', 'id');
    foreach ($questions as $question) {
        // The function question_delete_question does not delete questions in use.
        question_delete_question($question->id);
    }
    $questions->close();
}

/**
 * Category is about to be deleted,
 * 1/ All questions are deleted for this question category.
 * 2/ Any questions that can't be deleted are moved to a new category
 * NOTE: this function is called from lib/db/upgrade.php
 *
 * @param object|core_course_category $category course category object
 */
function question_category_delete_safe($category) {
    global $DB;
    $criteria = array('category' => $category->id);
    $context = context::instance_by_id($category->contextid, IGNORE_MISSING);
    $rescue = null; // See the code around the call to question_save_from_deletion.

    // Deal with any questions in the category.
    if ($questions = $DB->get_records('question', $criteria, '', 'id,qtype')) {

        // Try to delete each question.
        foreach ($questions as $question) {
            question_delete_question($question->id);
        }

        // Check to see if there were any questions that were kept because
        // they are still in use somehow, even though quizzes in courses
        // in this category will already have been deleted. This could
        // happen, for example, if questions are added to a course,
        // and then that course is moved to another category (MDL-14802).
        $questionids = $DB->get_records_menu('question', $criteria, '', 'id, 1');
        if (!empty($questionids)) {
            $parentcontextid = SYSCONTEXTID;
            $name = get_string('unknown', 'question');
            if ($context !== false) {
                $name = $context->get_context_name();
                $parentcontext = $context->get_parent_context();
                if ($parentcontext) {
                    $parentcontextid = $parentcontext->id;
                }
            }
            question_save_from_deletion(array_keys($questionids), $parentcontextid, $name, $rescue);
        }
    }

    // Now delete the category.
    $DB->delete_records('question_categories', array('id' => $category->id));
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
 * It will not delete a question if it is used somewhere.
 *
 * @param object $question  The question being deleted
 */
function question_delete_question($questionid) {
    global $DB;

    $question = $DB->get_record_sql('
            SELECT q.*, ctx.id AS contextid
            FROM {question} q
            LEFT JOIN {question_categories} qc ON qc.id = q.category
            LEFT JOIN {context} ctx ON ctx.id = qc.contextid
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

    // This sometimes happens in old sites with bad data.
    if (!$question->contextid) {
        debugging('Deleting question ' . $question->id . ' which is no longer linked to a context. ' .
                'Assuming system context to avoid errors, but this may mean that some data like files, ' .
                'tags, are not cleaned up.');
        $question->contextid = context_system::instance()->id;
    }

    // Delete previews of the question.
    $dm = new question_engine_data_mapper();
    $dm->delete_previews($questionid);

    // delete questiontype-specific data
    question_bank::get_qtype($question->qtype, false)->delete_question(
            $questionid, $question->contextid);

    // Delete all tag instances.
    core_tag_tag::remove_all_item_tags('core_question', 'question', $question->id);

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
    question_bank::notify_question_edited($questionid);

    // Log the deletion of this question.
    $event = \core\event\question_deleted::create_from_question_instance($question);
    $event->add_record_snapshot('question', $question);
    $event->trigger();
}

/**
 * All question categories and their questions are deleted for this context id.
 *
 * @param object $contextid The contextid to delete question categories from
 * @return array Feedback from deletes (if any)
 */
function question_delete_context($contextid) {
    global $DB;

    //To store feedback to be showed at the end of the process
    $feedbackdata   = array();

    //Cache some strings
    $strcatdeleted = get_string('unusedcategorydeleted', 'question');
    $fields = 'id, parent, name, contextid';
    if ($categories = $DB->get_records('question_categories', array('contextid' => $contextid), 'parent', $fields)) {
        //Sort categories following their tree (parent-child) relationships
        //this will make the feedback more readable
        $categories = sort_categories_by_tree($categories);

        foreach ($categories as $category) {
            question_category_delete_safe($category);

            //Fill feedback
            $feedbackdata[] = array($category->name, $strcatdeleted);
        }
    }
    return $feedbackdata;
}

/**
 * All question categories and their questions are deleted for this course.
 *
 * @param stdClass $course an object representing the activity
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function question_delete_course($course, $feedback=true) {
    $coursecontext = context_course::instance($course->id);
    $feedbackdata = question_delete_context($coursecontext->id, $feedback);

    // Inform about changes performed if feedback is enabled.
    if ($feedback && $feedbackdata) {
        $table = new html_table();
        $table->head = array(get_string('category', 'question'), get_string('action'));
        $table->data = $feedbackdata;
        echo html_writer::table($table);
    }
    return true;
}

/**
 * Category is about to be deleted,
 * 1/ All question categories and their questions are deleted for this course category.
 * 2/ All questions are moved to new category
 *
 * @param object|core_course_category $category course category object
 * @param object|core_course_category $newcategory empty means everything deleted, otherwise id of
 *      category where content moved
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function question_delete_course_category($category, $newcategory, $feedback=true) {
    global $DB, $OUTPUT;

    $context = context_coursecat::instance($category->id);
    if (empty($newcategory)) {
        $feedbackdata = question_delete_context($context->id, $feedback);

        // Output feedback if requested.
        if ($feedback && $feedbackdata) {
            $table = new html_table();
            $table->head = array(get_string('questioncategory', 'question'), get_string('action'));
            $table->data = $feedbackdata;
            echo html_writer::table($table);
        }

    } else {
        // Move question categories to the new context.
        if (!$newcontext = context_coursecat::instance($newcategory->id)) {
            return false;
        }

        // Only move question categories if there is any question category at all!
        if ($topcategory = question_get_top_category($context->id)) {
            $newtopcategory = question_get_top_category($newcontext->id, true);

            question_move_category_to_context($topcategory->id, $context->id, $newcontext->id);
            $DB->set_field('question_categories', 'parent', $newtopcategory->id, array('parent' => $topcategory->id));
            // Now delete the top category.
            $DB->delete_records('question_categories', array('id' => $topcategory->id));
        }

        if ($feedback) {
            $a = new stdClass();
            $a->oldplace = $context->get_context_name();
            $a->newplace = $newcontext->get_context_name();
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
 * @param object $newcontextid the context to create the saved category in.
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
        $newcategory->parent = question_get_top_category($newcontextid, true)->id;
        $newcategory->contextid = $newcontextid;
        // Max length of column name in question_categories is 255.
        $newcategory->name = shorten_text(get_string('questionsrescuedfrom', 'question', $oldplace), 255);
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
    global $DB;

    $modcontext = context_module::instance($cm->id);
    $feedbackdata = question_delete_context($modcontext->id, $feedback);
    // Inform about changes performed if feedback is enabled.
    if ($feedback && $feedbackdata) {
        $table = new html_table();
        $table->head = array(get_string('category', 'question'), get_string('action'));
        $table->data = $feedbackdata;
        echo html_writer::table($table);
    }
    return true;
}

/**
 * This function will handle moving all tag instances to a new context for a
 * given list of questions.
 *
 * Questions can be tagged in up to two contexts:
 * 1.) The context the question exists in.
 * 2.) The course context (if the question context is a higher context.
 *     E.g. course category context or system context.
 *
 * This means a question that exists in a higher context (e.g. course cat or
 * system context) may have multiple groups of tags in any number of child
 * course contexts.
 *
 * Questions in the course category context can be move "down" a context level
 * into one of their child course contexts or activity contexts which affects the
 * availability of that question in other courses / activities.
 *
 * In this case it makes the questions no longer available in the other course or
 * activity contexts so we need to make sure that the tag instances in those other
 * contexts are removed.
 *
 * @param stdClass[] $questions The list of question being moved (must include
 *                              the id and contextid)
 * @param context $newcontext The Moodle context the questions are being moved to
 */
function question_move_question_tags_to_new_context(array $questions, context $newcontext) {
    // If the questions are moving to a new course/activity context then we need to
    // find any existing tag instances from any unavailable course contexts and
    // delete them because they will no longer be applicable (we don't support
    // tagging questions across courses).
    $instancestodelete = [];
    $instancesfornewcontext = [];
    $newcontextparentids = $newcontext->get_parent_context_ids();
    $questionids = array_map(function($question) {
        return $question->id;
    }, $questions);
    $questionstagobjects = core_tag_tag::get_items_tags('core_question', 'question', $questionids);

    foreach ($questions as $question) {
        $tagobjects = $questionstagobjects[$question->id] ?? [];

        foreach ($tagobjects as $tagobject) {
            $tagid = $tagobject->taginstanceid;
            $tagcontextid = $tagobject->taginstancecontextid;
            $istaginnewcontext = $tagcontextid == $newcontext->id;
            $istaginquestioncontext = $tagcontextid == $question->contextid;

            if ($istaginnewcontext) {
                // This tag instance is already in the correct context so we can
                // ignore it.
                continue;
            }

            if ($istaginquestioncontext) {
                // This tag instance is in the question context so it needs to be
                // updated.
                $instancesfornewcontext[] = $tagid;
                continue;
            }

            // These tag instances are in neither the new context nor the
            // question context so we need to determine what to do based on
            // the context they are in and the new question context.
            $tagcontext = context::instance_by_id($tagcontextid);
            $tagcoursecontext = $tagcontext->get_course_context(false);
            // The tag is in a course context if get_course_context() returns
            // itself.
            $istaginstancecontextcourse = !empty($tagcoursecontext)
                && $tagcontext->id == $tagcoursecontext->id;

            if ($istaginstancecontextcourse) {
                // If the tag instance is in a course context we need to add some
                // special handling.
                $tagcontextparentids = $tagcontext->get_parent_context_ids();
                $isnewcontextaparent = in_array($newcontext->id, $tagcontextparentids);
                $isnewcontextachild = in_array($tagcontext->id, $newcontextparentids);

                if ($isnewcontextaparent) {
                    // If the tag instance is a course context tag and the new
                    // context is still a parent context to the tag context then
                    // we can leave this tag where it is.
                    continue;
                } else if ($isnewcontextachild) {
                    // If the new context is a child context (e.g. activity) of this
                    // tag instance then we should move all of this tag instance
                    // down into the activity context along with the question.
                    $instancesfornewcontext[] = $tagid;
                } else {
                    // If the tag is in a course context that is no longer a parent
                    // or child of the new context then this tag instance should be
                    // removed.
                    $instancestodelete[] = $tagid;
                }
            } else {
                // This is a catch all for any tag instances not in the question
                // context or a course context. These tag instances should be
                // updated to the new context id. This will clean up old invalid
                // data.
                $instancesfornewcontext[] = $tagid;
            }
        }
    }

    if (!empty($instancestodelete)) {
        // Delete any course context tags that may no longer be valid.
        core_tag_tag::delete_instances_by_id($instancestodelete);
    }

    if (!empty($instancesfornewcontext)) {
        // Update the tag instances to the new context id.
        core_tag_tag::change_instances_context($instancesfornewcontext, $newcontext);
    }
}

/**
 * This function should be considered private to the question bank, it is called from
 * question/editlib.php question/contextmoveq.php and a few similar places to to the
 * work of actually moving questions and associated data. However, callers of this
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
            SELECT q.id, q.qtype, qc.contextid, q.idnumber, q.category
              FROM {question} q
              JOIN {question_categories} qc ON q.category = qc.id
             WHERE  q.id $questionidcondition", $params);
    foreach ($questions as $question) {
        if ($newcontextid != $question->contextid) {
            question_bank::get_qtype($question->qtype)->move_files(
                    $question->id, $question->contextid, $newcontextid);
        }
        // Check whether there could be a clash of idnumbers in the new category.
        if (((string) $question->idnumber !== '') &&
                $DB->record_exists('question', ['idnumber' => $question->idnumber, 'category' => $newcategoryid])) {
            $rec = $DB->get_records_select('question', "category = ? AND idnumber LIKE ?",
                    [$newcategoryid, $question->idnumber . '_%'], 'idnumber DESC', 'id, idnumber', 0, 1);
            $unique = 1;
            if (count($rec)) {
                $rec = reset($rec);
                $idnumber = $rec->idnumber;
                if (strpos($idnumber, '_') !== false) {
                    $unique = substr($idnumber, strpos($idnumber, '_') + 1) + 1;
                }
            }
            // For the move process, add a numerical increment to the idnumber. This means that if a question is
            // mistakenly moved then the idnumber will not be completely lost.
            $q = new stdClass();
            $q->id = $question->id;
            $q->category = $newcategoryid;
            $q->idnumber = $question->idnumber . '_' . $unique;
            $DB->update_record('question', $q);
        }

        // Log this question move.
        $event = \core\event\question_moved::create_from_question_instance($question, context::instance_by_id($question->contextid),
                ['oldcategoryid' => $question->category, 'newcategoryid' => $newcategoryid]);
        $event->trigger();
    }

    // Move the questions themselves.
    $DB->set_field_select('question', 'category', $newcategoryid,
            "id $questionidcondition", $params);

    // Move any subquestions belonging to them.
    $DB->set_field_select('question', 'category', $newcategoryid,
            "parent $questionidcondition", $params);

    $newcontext = context::instance_by_id($newcontextid);
    question_move_question_tags_to_new_context($questions, $newcontext);

    // TODO Deal with datasets.

    // Purge these questions from the cache.
    foreach ($questions as $question) {
        question_bank::notify_question_edited($question->id);
    }

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

    $questions = [];
    $questionids = $DB->get_records_menu('question',
            array('category' => $categoryid), '', 'id,qtype');
    foreach ($questionids as $questionid => $qtype) {
        question_bank::get_qtype($qtype)->move_files(
                $questionid, $oldcontextid, $newcontextid);
        // Purge this question from the cache.
        question_bank::notify_question_edited($questionid);

        $questions[] = (object) [
            'id' => $questionid,
            'contextid' => $oldcontextid
        ];
    }

    $newcontext = context::instance_by_id($newcontextid);
    question_move_question_tags_to_new_context($questions, $newcontext);

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
 * @return moodle_url the URL.
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
        $params['maxmark'] = format_float($maxmark, -1);
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
 * @param array $questionids array of question ids to load. If null, then all
 * questions matched by $join will be loaded.
 * @param string $extrafields extra SQL code to be added to the query.
 * @param string $join extra SQL code to be added to the query.
 * @param array $extraparams values for any placeholders in $join.
 * You must use named placeholders.
 * @param string $orderby what to order the results by. Optional, default is unspecified order.
 *
 * @return array partially complete question objects. You need to call get_question_options
 * on them before they can be properly used.
 */
function question_preload_questions($questionids = null, $extrafields = '', $join = '',
        $extraparams = array(), $orderby = '') {
    global $DB;

    if ($questionids === null) {
        $where = '';
        $params = array();
    } else {
        if (empty($questionids)) {
            return array();
        }

        list($questionidcondition, $params) = $DB->get_in_or_equal(
                $questionids, SQL_PARAMS_NAMED, 'qid0000');
        $where = 'WHERE q.id ' . $questionidcondition;
    }

    if ($join) {
        $join = 'JOIN ' . $join;
    }

    if ($extrafields) {
        $extrafields = ', ' . $extrafields;
    }

    if ($orderby) {
        $orderby = 'ORDER BY ' . $orderby;
    }

    $sql = "SELECT q.*, qc.contextid{$extrafields}
              FROM {question} q
              JOIN {question_categories} qc ON q.category = qc.id
              {$join}
             {$where}
          {$orderby}";

    // Load the questions.
    $questions = $DB->get_records_sql($sql, $extraparams + $params);
    foreach ($questions as $question) {
        $question->_partiallyloaded = true;
    }

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
 * @param stdClass $category The question_categories record for the given $question.
 * @param stdClass[]|null $tagobjects The tags for the given $question.
 * @param stdClass[]|null $filtercourses The courses to filter the course tags by.
 */
function _tidy_question($question, $category, array $tagobjects = null, array $filtercourses = null) {
    // Load question-type specific fields.
    if (!question_bank::is_qtype_installed($question->qtype)) {
        $question->questiontext = html_writer::tag('p', get_string('warningmissingtype',
                'qtype_missingtype')) . $question->questiontext;
    }
    question_bank::get_qtype($question->qtype)->get_question_options($question);

    // Convert numeric fields to float. (Prevents these being displayed as 1.0000000.)
    $question->defaultmark += 0;
    $question->penalty += 0;

    if (isset($question->_partiallyloaded)) {
        unset($question->_partiallyloaded);
    }

    $question->categoryobject = $category;

    if (!is_null($tagobjects)) {
        $categorycontext = context::instance_by_id($category->contextid);
        $sortedtagobjects = question_sort_tags($tagobjects, $categorycontext, $filtercourses);
        $question->coursetagobjects = $sortedtagobjects->coursetagobjects;
        $question->coursetags = $sortedtagobjects->coursetags;
        $question->tagobjects = $sortedtagobjects->tagobjects;
        $question->tags = $sortedtagobjects->tags;
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
 * @param bool $loadtags load the question tags from the tags table. Optional, default false.
 * @param stdClass[] $filtercourses The courses to filter the course tags by.
 * @return bool Indicates success or failure.
 */
function get_question_options(&$questions, $loadtags = false, $filtercourses = null) {
    global $DB;

    $questionlist = is_array($questions) ? $questions : [$questions];
    $categoryids = [];
    $questionids = [];

    if (empty($questionlist)) {
        return true;
    }

    foreach ($questionlist as $question) {
        $questionids[] = $question->id;

        if (!in_array($question->category, $categoryids)) {
            $categoryids[] = $question->category;
        }
    }

    $categories = $DB->get_records_list('question_categories', 'id', $categoryids);

    if ($loadtags && core_tag_tag::is_enabled('core_question', 'question')) {
        $tagobjectsbyquestion = core_tag_tag::get_items_tags('core_question', 'question', $questionids);
    } else {
        $tagobjectsbyquestion = null;
    }

    foreach ($questionlist as $question) {
        if (is_null($tagobjectsbyquestion)) {
            $tagobjects = null;
        } else {
            $tagobjects = $tagobjectsbyquestion[$question->id];
        }

        _tidy_question($question, $categories[$question->category], $tagobjects, $filtercourses);
    }

    return true;
}

/**
 * Sort question tags by course or normal tags.
 *
 * This function also search tag instances that may have a context id that don't match either a course or
 * question context and fix the data setting the correct context id.
 *
 * @param stdClass[] $tagobjects The tags for the given $question.
 * @param stdClass $categorycontext The question categories context.
 * @param stdClass[]|null $filtercourses The courses to filter the course tags by.
 * @return stdClass $sortedtagobjects Sorted tag objects.
 */
function question_sort_tags($tagobjects, $categorycontext, $filtercourses = null) {

    // Questions can have two sets of tag instances. One set at the
    // course context level and another at the context the question
    // belongs to (e.g. course category, system etc).
    $sortedtagobjects = new stdClass();
    $sortedtagobjects->coursetagobjects = [];
    $sortedtagobjects->coursetags = [];
    $sortedtagobjects->tagobjects = [];
    $sortedtagobjects->tags = [];
    $taginstanceidstonormalise = [];
    $filtercoursecontextids = [];
    $hasfiltercourses = !empty($filtercourses);

    if ($hasfiltercourses) {
        // If we're being asked to filter the course tags by a set of courses
        // then get the context ids to filter below.
        $filtercoursecontextids = array_map(function($course) {
            $coursecontext = context_course::instance($course->id);
            return $coursecontext->id;
        }, $filtercourses);
    }

    foreach ($tagobjects as $tagobject) {
        $tagcontextid = $tagobject->taginstancecontextid;
        $tagcontext = context::instance_by_id($tagcontextid);
        $tagcoursecontext = $tagcontext->get_course_context(false);
        // This is a course tag if the tag context is a course context which
        // doesn't match the question's context. Any tag in the question context
        // is not considered a course tag, it belongs to the question.
        $iscoursetag = $tagcoursecontext
            && $tagcontext->id == $tagcoursecontext->id
            && $tagcontext->id != $categorycontext->id;

        if ($iscoursetag) {
            // Any tag instance in a course context level is considered a course tag.
            if (!$hasfiltercourses || in_array($tagcontextid, $filtercoursecontextids)) {
                // Add the tag to the list of course tags if we aren't being
                // asked to filter or if this tag is in the list of courses
                // we're being asked to filter by.
                $sortedtagobjects->coursetagobjects[] = $tagobject;
                $sortedtagobjects->coursetags[$tagobject->id] = $tagobject->get_display_name();
            }
        } else {
            // All non course context level tag instances or tags in the question
            // context belong to the context that the question was created in.
            $sortedtagobjects->tagobjects[] = $tagobject;
            $sortedtagobjects->tags[$tagobject->id] = $tagobject->get_display_name();

            // Due to legacy tag implementations that don't force the recording
            // of a context id, some tag instances may have context ids that don't
            // match either a course context or the question context. In this case
            // we should take the opportunity to fix up the data and set the correct
            // context id.
            if ($tagcontext->id != $categorycontext->id) {
                $taginstanceidstonormalise[] = $tagobject->taginstanceid;
                // Update the object properties to reflect the DB update that will
                // happen below.
                $tagobject->taginstancecontextid = $categorycontext->id;
            }
        }
    }

    if (!empty($taginstanceidstonormalise)) {
        // If we found any tag instances with incorrect context id data then we can
        // correct those values now by setting them to the question context id.
        core_tag_tag::change_instances_context($taginstanceidstonormalise, $categorycontext);
    }

    return $sortedtagobjects;
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
    echo html_writer::label(get_string('questioncategory', 'core_question'), 'id_movetocategory', false, array('class' => 'accesshide'));
    $attrs = array(
        'id' => 'id_movetocategory',
        'class' => 'custom-select',
        'data-action' => 'toggle',
        'data-togglegroup' => 'qbank',
        'data-toggle' => 'action',
        'disabled' => true,
    );
    echo html_writer::select($options, 'category', $selected, $choose, $attrs);
}

/**
 * @param integer $contextid a context id.
 * @return object the default question category for that context, or false if none.
 */
function question_get_default_category($contextid) {
    global $DB;
    $category = $DB->get_records_select('question_categories', 'contextid = ? AND parent <> 0',
            array($contextid), 'id', '*', 0, 1);
    if (!empty($category)) {
        return reset($category);
    } else {
        return false;
    }
}

/**
 * Gets the top category in the given context.
 * This function can optionally create the top category if it doesn't exist.
 *
 * @param int $contextid A context id.
 * @param bool $create Whether create a top category if it doesn't exist.
 * @return bool|stdClass The top question category for that context, or false if none.
 */
function question_get_top_category($contextid, $create = false) {
    global $DB;
    $category = $DB->get_record('question_categories',
            array('contextid' => $contextid, 'parent' => 0));

    if (!$category && $create) {
        // We need to make one.
        $category = new stdClass();
        $category->name = 'top'; // A non-real name for the top category. It will be localised at the display time.
        $category->info = '';
        $category->contextid = $contextid;
        $category->parent = 0;
        $category->sortorder = 0;
        $category->stamp = make_unique_id_code();
        $category->id = $DB->insert_record('question_categories', $category);
    }

    return $category;
}

/**
 * Gets the list of top categories in the given contexts in the array("categoryid,categorycontextid") format.
 *
 * @param array $contextids List of context ids
 * @return array
 */
function question_get_top_categories_for_contexts($contextids) {
    global $DB;

    $concatsql = $DB->sql_concat_join("','", ['id', 'contextid']);
    list($insql, $params) = $DB->get_in_or_equal($contextids);
    $sql = "SELECT $concatsql FROM {question_categories} WHERE contextid $insql AND parent = 0";
    $topcategories = $DB->get_fieldset_sql($sql, $params);

    return $topcategories;
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
        $topcategory = question_get_top_category($context->id, true);
        if (!$exists = $DB->record_exists("question_categories",
                array('contextid' => $context->id, 'parent' => $topcategory->id))) {
            // Otherwise, we need to make one
            $category = new stdClass();
            $contextname = $context->get_context_name(false, true);
            // Max length of name field is 255.
            $category->name = shorten_text(get_string('defaultfor', 'question', $contextname), 255);
            $category->info = get_string('defaultinfofor', 'question', $contextname);
            $category->contextid = $context->id;
            $category->parent = $topcategory->id;
            // By default, all categories get this number, and are sorted alphabetically.
            $category->sortorder = 999;
            $category->stamp = make_unique_id_code();
            $category->id = $DB->insert_record('question_categories', $category);
        } else {
            $category = question_get_default_category($context->id);
        }
        $thispreferredness = $preferredlevels[$context->contextlevel];
        if (has_any_capability(array('moodle/question:usemine', 'moodle/question:useall'), $context)) {
            $thispreferredness += 10;
        }
        if ($thispreferredness > $preferredness) {
            $toreturn = $category;
            $preferredness = $thispreferredness;
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
 * @param bool $top Whether to return the top categories or not.
 * @return array of category objects.
 */
function get_categories_for_contexts($contexts, $sortorder = 'parent, sortorder, name ASC', $top = false) {
    global $DB;
    $topwhere = $top ? '' : 'AND c.parent <> 0';
    return $DB->get_records_sql("
            SELECT c.*, (SELECT count(1) FROM {question} q
                        WHERE c.id = q.category AND q.hidden='0' AND q.parent='0') AS questioncount
              FROM {question_categories} c
             WHERE c.contextid IN ($contexts) $topwhere
          ORDER BY $sortorder");
}

/**
 * Output an array of question categories.
 *
 * @param array $contexts The list of contexts.
 * @param bool $top Whether to return the top categories or not.
 * @param int $currentcat
 * @param bool $popupform
 * @param int $nochildrenof
 * @return array
 */
function question_category_options($contexts, $top = false, $currentcat = 0,
        $popupform = false, $nochildrenof = -1) {
    global $CFG;
    $pcontexts = array();
    foreach ($contexts as $context) {
        $pcontexts[] = $context->id;
    }
    $contextslist = join(', ', $pcontexts);

    $categories = get_categories_for_contexts($contextslist, 'parent, sortorder, name ASC', $top);

    if ($top) {
        $categories = question_fix_top_names($categories);
    }

    $categories = question_add_context_in_key($categories);
    $categories = add_indented_names($categories, $nochildrenof);

    // sort cats out into different contexts
    $categoriesarray = array();
    foreach ($pcontexts as $contextid) {
        $context = context::instance_by_id($contextid);
        $contextstring = $context->get_context_name(true, true);
        foreach ($categories as $category) {
            if ($category->contextid == $contextid) {
                $cid = $category->id;
                if ($currentcat != $cid || $currentcat == 0) {
                    $a = new stdClass;
                    $a->name = format_string($category->indentedname, true,
                            array('context' => $context));
                    if ($category->idnumber !== null && $category->idnumber !== '') {
                        $a->idnumber = s($category->idnumber);
                    }
                    if (!empty($category->questioncount)) {
                        $a->questioncount = $category->questioncount;
                    }
                    if (isset($a->idnumber) && isset($a->questioncount)) {
                        $formattedname = get_string('categorynamewithidnumberandcount', 'question', $a);
                    } else if (isset($a->idnumber)) {
                        $formattedname = get_string('categorynamewithidnumber', 'question', $a);
                    } else if (isset($a->questioncount)) {
                        $formattedname = get_string('categorynamewithcount', 'question', $a);
                    } else {
                        $formattedname = $a->name;
                    }
                    $categoriesarray[$contextstring][$cid] = $formattedname;
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

/**
 * Finds top categories in the given categories hierarchy and replace their name with a proper localised string.
 *
 * @param array $categories An array of question categories.
 * @return array The same question category list given to the function, with the top category names being translated.
 */
function question_fix_top_names($categories) {

    foreach ($categories as $id => $category) {
        if ($category->parent == 0) {
            $context = context::instance_by_id($category->contextid);
            $categories[$id]->name = get_string('topfor', 'question', $context->get_context_name(false));
        }
    }

    return $categories;
}

/**
 * @return array of question category ids of the category and all subcategories.
 */
function question_categorylist($categoryid) {
    global $DB;

    // final list of category IDs
    $categorylist = array();

    // a list of category IDs to check for any sub-categories
    $subcategories = array($categoryid);

    while ($subcategories) {
        foreach ($subcategories as $subcategory) {
            // if anything from the temporary list was added already, then we have a loop
            if (isset($categorylist[$subcategory])) {
                throw new coding_exception("Category id=$subcategory is already on the list - loop of categories detected.");
            }
            $categorylist[$subcategory] = $subcategory;
        }

        list ($in, $params) = $DB->get_in_or_equal($subcategories);

        $subcategories = $DB->get_records_select_menu('question_categories',
                "parent $in", $params, NULL, 'id,id AS id2');
    }

    return $categorylist;
}

/**
 * Get all parent categories of a given question category in decending order.
 * @param int $categoryid for which you want to find the parents.
 * @return array of question category ids of all parents categories.
 */
function question_categorylist_parents(int $categoryid) {
    global $DB;
    $parent = $DB->get_field('question_categories', 'parent', array('id' => $categoryid));
    if (!$parent) {
        return [];
    }
    $categorylist = [$parent];
    $currentid = $parent;
    while ($currentid) {
        $currentid = $DB->get_field('question_categories', 'parent', array('id' => $currentid));
        if ($currentid) {
            $categorylist[] = $currentid;
        }
    }
    // Present the list in decending order (the top category at the top).
    $categorylist = array_reverse($categorylist);
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
    require_once($CFG->dirroot . '/question/format.php');

    $formatclasses = core_component::get_plugin_list_with_class('qformat', '', 'format.php');

    $fileformatname = array();
    foreach ($formatclasses as $component => $formatclass) {

        $format = new $formatclass();
        if ($type == 'import') {
            $provided = $format->provide_import();
        } else {
            $provided = $format->provide_export();
        }

        if ($provided) {
            list($notused, $fileformat) = explode('_', $component, 2);
            $fileformatnames[$fileformat] = get_string('pluginname', $component);
        }
    }

    core_collator::asort($fileformatnames);
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
 * @param int|stdClass|question_definition $questionorid object or id.
 *      If an object is passed, it should include ->contextid and ->createdby.
 * @param string $cap 'add', 'edit', 'view', 'use', 'move' or 'tag'.
 * @param int $notused no longer used.
 * @return bool this user has the capability $cap for this question $question?
 * @throws coding_exception
 */
function question_has_capability_on($questionorid, $cap, $notused = -1) {
    global $USER, $DB;

    if (is_numeric($questionorid)) {
        $questionid = (int)$questionorid;
    } else if (is_object($questionorid)) {
        // All we really need in this function is the contextid and author of the question.
        // We won't bother fetching other details of the question if these 2 fields are provided.
        if (isset($questionorid->contextid) && isset($questionorid->createdby)) {
            $question = $questionorid;
        } else if (!empty($questionorid->id)) {
            $questionid = $questionorid->id;
        }
    }

    // At this point, either $question or $questionid is expected to be set.
    if (isset($questionid)) {
        try {
            $question = question_bank::load_question_data($questionid);
        } catch (Exception $e) {
            // Let's log the exception for future debugging,
            // but not during Behat, or we can't test these cases.
            if (!defined('BEHAT_SITE_RUNNING')) {
                debugging($e->getMessage(), DEBUG_NORMAL, $e->getTrace());
            }

            // Well, at least we tried. Seems that we really have to read from DB.
            $question = $DB->get_record_sql('SELECT q.id, q.createdby, qc.contextid
                                               FROM {question} q
                                               JOIN {question_categories} qc ON q.category = qc.id
                                              WHERE q.id = :id', ['id' => $questionid]);
        }
    }

    if (!isset($question)) {
        throw new coding_exception('$questionorid parameter needs to be an integer or an object.');
    }

    $context = context::instance_by_id($question->contextid);

    // These are existing questions capabilities that are set per category.
    // Each of these has a 'mine' and 'all' version that is appended to the capability name.
    $capabilitieswithallandmine = ['edit' => 1, 'view' => 1, 'use' => 1, 'move' => 1, 'tag' => 1];

    if (!isset($capabilitieswithallandmine[$cap])) {
        return has_capability('moodle/question:' . $cap, $context);
    } else {
        return has_capability('moodle/question:' . $cap . 'all', $context) ||
            ($question->createdby == $USER->id && has_capability('moodle/question:' . $cap . 'mine', $context));
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
            new moodle_url('/question/edit.php', $params), navigation_node::TYPE_CONTAINER, null, 'questionbank');

    $contexts = new question_edit_contexts($context);
    if ($contexts->have_one_edit_tab_cap('questions')) {
        $questionnode->add(get_string('questions', 'question'), new moodle_url(
                '/question/edit.php', $params), navigation_node::TYPE_SETTING, null, 'questions');
    }
    if ($contexts->have_one_edit_tab_cap('categories')) {
        $questionnode->add(get_string('categories', 'question'), new moodle_url(
                '/question/category.php', $params), navigation_node::TYPE_SETTING, null, 'categories');
    }
    if ($contexts->have_one_edit_tab_cap('import')) {
        $questionnode->add(get_string('import', 'question'), new moodle_url(
                '/question/import.php', $params), navigation_node::TYPE_SETTING, null, 'import');
    }
    if ($contexts->have_one_edit_tab_cap('export')) {
        $questionnode->add(get_string('export', 'question'), new moodle_url(
                '/question/export.php', $params), navigation_node::TYPE_SETTING, null, 'export');
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


/**
 * Tracks all the contexts related to the one where we are currently editing
 * questions, and provides helper methods to check permissions.
 *
 * @copyright 2007 Jamie Pratt me@jamiep.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
     * Constructor
     * @param context the current context.
     */
    public function __construct(context $thiscontext) {
        $this->allcontexts = array_values($thiscontext->get_parent_contexts(true));
    }

    /**
     * @return context[] all parent contexts
     */
    public function all() {
        return $this->allcontexts;
    }

    /**
     * @return context lowest context which must be either the module or course context
     */
    public function lowest() {
        return $this->allcontexts[0];
    }

    /**
     * @param string $cap capability
     * @return context[] parent contexts having capability, zero based index
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
     * @return context[] parent contexts having at least one of $caps, zero based index
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
     * @return context[] parent contexts having at least one of $caps, zero based index
     */
    public function having_one_edit_tab_cap($tabname) {
        return $this->having_one_cap(self::$caps[$tabname]);
    }

    /**
     * @return context[] those contexts where a user can add a question and then use it.
     */
    public function having_add_and_use() {
        $contextswithcap = array();
        foreach ($this->allcontexts as $context) {
            if (!has_capability('moodle/question:add', $context)) {
                continue;
            }
            if (!has_any_capability(array('moodle/question:useall', 'moodle/question:usemine'), $context)) {
                            continue;
            }
            $contextswithcap[] = $context;
        }
        return $contextswithcap;
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
     * @param array $caps capabilities
     */
    public function require_one_cap($caps) {
        if (!$this->have_one_cap($caps)) {
            $capsstring = join(', ', $caps);
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
 * @package  core_question
 * @category files
 * @param string $text text being processed
 * @param string $file the php script used to serve files
 * @param int $contextid context ID
 * @param string $component component
 * @param string $filearea filearea
 * @param array $ids other IDs will be used to check file permission
 * @param int $itemid item ID
 * @param array $options options
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
 * Rewrite the PLUGINFILE urls in part of the content of a question, for use when
 * viewing the question outside an attempt (for example, in the question bank
 * listing or in the quiz statistics report).
 *
 * @param string $text the question text.
 * @param int $questionid the question id.
 * @param int $filecontextid the context id of the question being displayed.
 * @param string $filecomponent the component that owns the file area.
 * @param string $filearea the file area name.
 * @param int|null $itemid the file's itemid
 * @param int $previewcontextid the context id where the preview is being displayed.
 * @param string $previewcomponent component responsible for displaying the preview.
 * @param array $options text and file options ('forcehttps'=>false)
 * @return string $questiontext with URLs rewritten.
 */
function question_rewrite_question_preview_urls($text, $questionid,
        $filecontextid, $filecomponent, $filearea, $itemid,
        $previewcontextid, $previewcomponent, $options = null) {

    $path = "preview/$previewcontextid/$previewcomponent/$questionid";
    if ($itemid) {
        $path .= '/' . $itemid;
    }

    return file_rewrite_pluginfile_urls($text, 'pluginfile.php', $filecontextid,
            $filecomponent, $filearea, $path, $options);
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
 * @package  core_question
 * @category files
 * @param stdClass $course course settings object
 * @param stdClass $context context object
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 * @param array $options additional options affecting the file serving
 */
function question_pluginfile($course, $context, $component, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    // Special case, sending a question bank export.
    if ($filearea === 'export') {
        list($context, $course, $cm) = get_context_info_array($context->id);
        require_login($course, false, $cm);

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
        if (!$content = $qformat->exportprocess()) {
            send_file_not_found();
        }

        send_file($content, $filename, 0, 0, true, true, $qformat->mime_type());
    }

    // Normal case, a file belonging to a question.
    $qubaidorpreview = array_shift($args);

    // Two sub-cases: 1. A question being previewed outside an attempt/usage.
    if ($qubaidorpreview === 'preview') {
        $previewcontextid = (int)array_shift($args);
        $previewcomponent = array_shift($args);
        $questionid = (int) array_shift($args);
        $previewcontext = context_helper::instance_by_id($previewcontextid);

        $result = component_callback($previewcomponent, 'question_preview_pluginfile', array(
                $previewcontext, $questionid,
                $context, $component, $filearea, $args,
                $forcedownload, $options), 'callbackmissing');

        if ($result === 'callbackmissing') {
            throw new coding_exception("Component {$previewcomponent} does not define the callback " .
                    "{$previewcomponent}_question_preview_pluginfile callback. " .
                    "Which is required if you are using question_rewrite_question_preview_urls.", DEBUG_DEVELOPER);
        }

        send_file_not_found();
    }

    // 2. A question being attempted in the normal way.
    $qubaid = (int)$qubaidorpreview;
    $slot = (int)array_shift($args);

    $module = $DB->get_field('question_usages', 'component',
            array('id' => $qubaid));
    if (!$module) {
        send_file_not_found();
    }

    if ($module === 'core_question_preview') {
        require_once($CFG->dirroot . '/question/previewlib.php');
        return question_preview_question_pluginfile($course, $context,
                $component, $filearea, $qubaid, $slot, $args, $forcedownload, $options);

    } else {
        $dir = core_component::get_component_directory($module);
        if (!file_exists("$dir/lib.php")) {
            send_file_not_found();
        }
        include_once("$dir/lib.php");

        $filefunction = $module . '_question_pluginfile';
        if (function_exists($filefunction)) {
            $filefunction($course, $context, $component, $filearea, $qubaid, $slot,
                $args, $forcedownload, $options);
        }

        // Okay, we're here so lets check for function without 'mod_'.
        if (strpos($module, 'mod_') === 0) {
            $filefunctionold  = substr($module, 4) . '_question_pluginfile';
            if (function_exists($filefunctionold)) {
                $filefunctionold($course, $context, $component, $filearea, $qubaid, $slot,
                    $args, $forcedownload, $options);
            }
        }

        send_file_not_found();
    }
}

/**
 * Serve questiontext files in the question text when they are displayed in this report.
 *
 * @package  core_files
 * @category files
 * @param context $previewcontext the context in which the preview is happening.
 * @param int $questionid the question id.
 * @param context $filecontext the file (question) context.
 * @param string $filecomponent the component the file belongs to.
 * @param string $filearea the file area.
 * @param array $args remaining file args.
 * @param bool $forcedownload.
 * @param array $options additional options affecting the file serving.
 */
function core_question_question_preview_pluginfile($previewcontext, $questionid,
        $filecontext, $filecomponent, $filearea, $args, $forcedownload, $options = array()) {
    global $DB;

    // Verify that contextid matches the question.
    $question = $DB->get_record_sql('
            SELECT q.*, qc.contextid
              FROM {question} q
              JOIN {question_categories} qc ON qc.id = q.category
             WHERE q.id = :id AND qc.contextid = :contextid',
            array('id' => $questionid, 'contextid' => $filecontext->id), MUST_EXIST);

    // Check the capability.
    list($context, $course, $cm) = get_context_info_array($previewcontext->id);
    require_login($course, false, $cm);

    question_require_capability_on($question, 'use');

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/{$filecontext->id}/{$filecomponent}/{$filearea}/{$relativepath}";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
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
    $urlbase = "$CFG->wwwroot/pluginfile.php";
    return moodle_url::make_file_url($urlbase,
            "/$contextid/question/export/{$categoryid}/{$format}/{$withcategories}" .
            "/{$withcontexts}/{$filename}", true);
}

/**
 * Get the URL to export a single question (exportone.php).
 *
 * @param stdClass|question_definition $question the question definition as obtained from
 *      question_bank::load_question_data() or question_bank::make_question().
 *      (Only ->id and ->contextid are used.)
 * @return moodle_url the requested URL.
 */
function question_get_export_single_question_url($question) {
    $params = ['id' => $question->id, 'sesskey' => sesskey()];
    $context = context::instance_by_id($question->contextid);
    switch ($context->contextlevel) {
        case CONTEXT_MODULE:
            $params['cmid'] = $context->instanceid;
            break;

        case CONTEXT_COURSE:
            $params['courseid'] = $context->instanceid;
            break;

        default:
            $params['courseid'] = SITEID;
    }

    return new moodle_url('/question/exportone.php', $params);
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

/**
 * Does an activity module use the question bank?
 *
 * @param string $modname The name of the module (without mod_ prefix).
 * @return bool true if the module uses questions.
 */
function question_module_uses_questions($modname) {
    if (plugin_supports('mod', $modname, FEATURE_USES_QUESTIONS)) {
        return true;
    }

    $component = 'mod_'.$modname;
    if (component_callback_exists($component, 'question_pluginfile')) {
        debugging("{$component} uses questions but doesn't declare FEATURE_USES_QUESTIONS", DEBUG_DEVELOPER);
        return true;
    }

    return false;
}
