<?php // $Id$
/**
 * This file contains dtabase upgrade code that is called from lib/db/upgrade.php,
 * and also check methods that can be used for pre-install checks via
 * admin/environment.php and lib/environmentlib.php.
 *
 * @copyright &copy; 2007 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

/**
 * This test is becuase the RQP question type was included in core
 * up to and including Moodle 1.8, and was removed before Moodle 1.9.
 *
 * Therefore, we want to check whether any rqp questions exist in the database
 * before doing the upgrade. However, the check is not relevant if that
 * question type was never installed, or if the person has chosen to
 * manually reinstall the rqp question type from contrib.
 *
 * @param $result the result object that can be modified.
 * @return null if the test is irrelevant, or true or false depending on whether the test passes.
 */
function question_check_no_rqp_questions($result) {
    global $CFG;

    if (empty($CFG->qtype_rqp_version) || is_dir($CFG->dirroot . '/question/type/rqp')) {
        return null;
    } else {
        $result->setStatus(count_records('question', 'qtype', 'rqp') == 0);
    }
    return $result;
}

function question_remove_rqp_qtype() {
    global $CFG;

    $result = true;

    // Only remove the question type if the code is gone.
    if (!is_dir($CFG->dirroot . '/question/type/rqp')) {
        $table = new XMLDBTable('question_rqp_states');
        $result = $result && drop_table($table);

        $table = new XMLDBTable('question_rqp');
        $result = $result && drop_table($table);

        $table = new XMLDBTable('question_rqp_types');
        $result = $result && drop_table($table);

        $table = new XMLDBTable('question_rqp_servers');
        $result = $result && drop_table($table);

        $result = $result && unset_config('qtype_rqp_version');
    }

    return $result;
}

function question_remove_rqp_qtype_config_string() {
    global $CFG;

    $result = true;

    // An earlier, buggy version of the previous function missed out the unset_config call.
    if (!empty($CFG->qtype_rqp_version) && !is_dir($CFG->dirroot . '/question/type/rqp')) {
        $result = $result && unset_config('qtype_rqp_version');
    }

    return $result;
}

/**
 * @param $result the result object that can be modified.
 * @return null if the test is irrelevant, or true or false depending on whether the test passes.
 */
function question_random_check($result){
    global $CFG;
    if (!empty($CFG->running_installer) //no test on first installation, no questions to test yet
            || $CFG->version >= 2007081000){//no test after upgrade seperates question cats into contexts.
        return null;
    }
    if (!$toupdate = question_cwqpfs_to_update()){
        $result->setStatus(true);//pass test
    } else {
        //set the feedback string here and not in xml file since we need something
        //more complex than just a string picked from admin.php lang file
        $a = new object();
        $a->reporturl = "{$CFG->wwwroot}/{$CFG->admin}/report/question/";
        $lang = str_replace('_utf8', '', current_language());
        $a->docsurl = "{$CFG->docroot}/$lang/admin/report/question/index";
        $result->setFeedbackStr(array('questioncwqpfscheck', 'admin', $a));
        $result->setStatus(false);//fail test
    }
    return $result;
}
/*
 * Delete all 'random' questions that are not been used in a quiz.
 */
function question_delete_unused_random(){
    global $CFG;
    $tofix = array();
    $result = true;
    //delete all 'random' questions that are not been used in a quiz.
    if ($qqis = get_records_sql("SELECT q.* FROM {$CFG->prefix}question q LEFT JOIN ".
                                    "{$CFG->prefix}quiz_question_instances qqi ".
                                    "ON q.id = qqi.question WHERE q.qtype='random' AND qqi.question IS NULL")){
        $qqilist = join(array_keys($qqis), ',');
        $result = $result && delete_records_select('question', "id IN ($qqilist)");
    }
    return $result;
}
function question_cwqpfs_to_update($categories = null){
    global $CFG;

    $tofix = array();
    $result = true;

    //any cats with questions picking from subcats?
    if (!$cwqpfs = get_records_sql_menu("SELECT DISTINCT qc.id, 1 ".
                                    "FROM {$CFG->prefix}question q, {$CFG->prefix}question_categories qc ".
                                    "WHERE q.qtype='random' AND qc.id = q.category AND ".
                                     sql_compare_text('q.questiontext'). " = '1'")){
        return array();
    } else {
        if ($categories === null){
            $categories = get_records('question_categories');
        }
        $categorychildparents = array();
        foreach ($categories as $id => $category){
            $categorychildparents[$category->course][$id] = $category->parent;
        }
        foreach ($categories as $id => $category){
            if (FALSE !== array_key_exists($category->parent, $categorychildparents[$category->course])){
                //this is not a top level cat
                continue;//go to next category
            } else{
                $tofix += question_cwqpfs_check_children($id, $categories, $categorychildparents[$category->course], $cwqpfs);
            }
        }
    }

    return $tofix;
}

function question_cwqpfs_check_children($checkid, $categories, $categorychildparents, $cwqpfs){
    $tofix = array();
    if (array_key_exists($checkid, $cwqpfs)){//cwqpfs in this cat
        $getchildren = array();
        $getchildren[] = $checkid;
        //search down tree and find all children
        while ($nextid = array_shift($getchildren)){//repeat until $getchildren
                                                    //empty;
            $childids = array_keys($categorychildparents, $nextid);
            foreach ($childids as $childid){
                if ($categories[$childid]->publish != $categories[$checkid]->publish){
                    $tofix[$childid] = $categories[$checkid]->publish;
                }
            }
            $getchildren = array_merge($getchildren, $childids);
        }
    } else { // check children for cwqpfs
        $childrentocheck = array_keys($categorychildparents, $checkid);
        foreach ($childrentocheck as $childtocheck){
            $tofix += question_cwqpfs_check_children($childtocheck, $categories, $categorychildparents, $cwqpfs);
        }
    }
    return $tofix;
}

function question_category_next_parent_in($contextid, $question_categories, $id, $already_seen = array()){
    // Recursively look for an ancestor category of the given category that
    // belongs to context $contextid. (In a lot of cases, the parent will be 
    // the one.) If there is none, return 0, meaning the top level.
    $already_seen[] = $id;

    $nextparent = $question_categories[$id]->parent;
    if ($nextparent == 0) {
        // Hit the top level, we are done.
        return 0;
    } else if (!array_key_exists($nextparent, $question_categories)) {
        // The category hierarchy must have been screwed up before, in that
        // we have run out of categories to search, but without reaching the
        // top level. Repair the situation by returning 0, meaning top level.
        notify(get_string('upgradeproblemunknowncategory', 'question', $question_categories[$id]));
        return 0;
    } else if (in_array($nextparent, $already_seen)) {
        // The category hierarchy must have been screwed up before, in that
        // we have just found a loop in the category 'tree'. That should,
        // of course, be impossible, but it did acutally happen in at least once.
        // Repair the situation by returning 0, meaning top level.
        notify(get_string('upgradeproblemcategoryloop', 'question', implode(', ', $already_seen)));
        return 0;
    } else if ($contextid == $question_categories[$nextparent]->contextid) {
        // Found a suitable category, we are done.
        return $nextparent;
    } else {
        // The immediate parent is not in the same context, so look further up.
        return question_category_next_parent_in($contextid, $question_categories, $nextparent, $already_seen);
    }
}

/**
 * Check that either category parent is 0 or a category shared in the same context.
 * Fix any categories to point to grand or grand grand parent etc in the same context or 0.
 */
function question_category_checking($question_categories){
    //make an array that is easier to search
    $newparents = array();
    foreach ($question_categories as $id => $category){
        $newparents[$id] = question_category_next_parent_in($category->contextid, $question_categories, $id);
    }
    foreach (array_keys($question_categories) as $id){
        $question_categories[$id]->parent = $newparents[$id];
    }
    return $question_categories;
}

function question_upgrade_context_etc(){
    global $CFG;
    $result = true;
    $result = $result && question_delete_unused_random();

    $question_categories = get_records('question_categories');
    if ($question_categories) {
        //prepare content for new db structure
        $tofix = question_cwqpfs_to_update($question_categories);
        foreach ($tofix as $catid => $publish){
            $question_categories[$catid]->publish = $publish;
        }

        foreach ($question_categories as $id => $question_category){
            $course = $question_categories[$id]->course;
            unset($question_categories[$id]->course);
            if ($question_categories[$id]->publish){
                $context = get_context_instance(CONTEXT_SYSTEM);
                //new name with old course name in brackets
                $coursename = get_field('course', 'shortname', 'id', $course);
                $question_categories[$id]->name .= " ($coursename)";
            } else {
                $context = get_context_instance(CONTEXT_COURSE, $course);
            }
            $question_categories[$id]->contextid = $context->id;
            unset($question_categories[$id]->publish);
        }

        $question_categories = question_category_checking($question_categories);
    }

/// Define index course (not unique) to be dropped form question_categories
    $table = new XMLDBTable('question_categories');
    $index = new XMLDBIndex('course');
    $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course'));

/// Launch drop index course
    $result = $result && drop_index($table, $index);

/// Define field course to be dropped from question_categories
    $field = new XMLDBField('course');

/// Launch drop field course
    $result = $result && drop_field($table, $field);

/// Define field context to be added to question_categories
    $field = new XMLDBField('contextid');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'name');
    $field->comment = 'context that this category is shared in';

/// Launch add field context
    $result = $result && add_field($table, $field);

/// Define index context (not unique) to be added to question_categories
    $index = new XMLDBIndex('contextid');
    $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('contextid'));
    $index->comment = 'links to context table';

/// Launch add index context
    $result = $result && add_index($table, $index);

    $field = new XMLDBField('publish');

/// Launch drop field publish
    $result = $result && drop_field($table, $field);


    /// update table contents with previously calculated new contents.
    if ($question_categories) {
        foreach ($question_categories as $question_category) {
            $question_category->name = addslashes($question_category->name);
            $question_category->info = addslashes($question_category->info);
            if (!$result = $result && update_record('question_categories', $question_category)){
                notify(get_string('upgradeproblemcouldnotupdatecategory', 'question', $question_category));
            }
        }
    }

/// Define field timecreated to be added to question
    $table = new XMLDBTable('question');
    $field = new XMLDBField('timecreated');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'hidden');

/// Launch add field timecreated
    $result = $result && add_field($table, $field);

/// Define field timemodified to be added to question
    $table = new XMLDBTable('question');
    $field = new XMLDBField('timemodified');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'timecreated');

/// Launch add field timemodified
    $result = $result && add_field($table, $field);

/// Define field createdby to be added to question
    $table = new XMLDBTable('question');
    $field = new XMLDBField('createdby');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'timemodified');

/// Launch add field createdby
    $result = $result && add_field($table, $field);

/// Define field modifiedby to be added to question
    $table = new XMLDBTable('question');
    $field = new XMLDBField('modifiedby');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'createdby');

/// Launch add field modifiedby
    $result = $result && add_field($table, $field);

/// Define key createdby (foreign) to be added to question
    $table = new XMLDBTable('question');
    $key = new XMLDBKey('createdby');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('createdby'), 'user', array('id'));

/// Launch add key createdby
    $result = $result && add_key($table, $key);

/// Define key modifiedby (foreign) to be added to question
    $table = new XMLDBTable('question');
    $key = new XMLDBKey('modifiedby');
    $key->setAttributes(XMLDB_KEY_FOREIGN, array('modifiedby'), 'user', array('id'));

/// Launch add key modifiedby
    $result = $result && add_key($table, $key);

    return $result;
}

/**
 * In Moodle, all random questions should have question.parent set to be the same
 * as question.id. One effect of MDL-5482 is that this will not be true for questions that
 * were backed up then restored. The probably does not cause many problems, except occasionally,
 * if the bogus question.parent happens to point to a multianswer question type, or when you
 * try to do a subsequent backup. Anyway, these question.parent values should be fixed, and
 * that is what this update does.
 */
function question_fix_random_question_parents() {
    global $CFG;
    return execute_sql('UPDATE ' . $CFG->prefix . 'question SET parent = id ' .
            "WHERE qtype = 'random' AND parent <> id");
}

?>
