<?php  //$Id$

// This file keeps track of upgrades to 
// the quiz module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function question_category_next_parent_in($contextid, $question_categories, $id){
    $nextparent = $question_categories[$id]->parent;
    if ($nextparent == 0){
        return 0;
    } elseif (!array_key_exists($nextparent, $question_categories)){
        //finished searching up the category hierarchy. For some reason 
        //the top level items is not 0. We'll return 0 though.
        return 0;
    } elseif ($contextid == $question_categories[$nextparent]->contextid){
        return $nextparent;
    } else {
        //parent is not in the same context look further up. 
        return question_category_next_parent_in($contextid, $question_categories, $nextparent);
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
    foreach (array_Keys($question_categories) as $id){
        $question_categories[$id]->parent = $newparents[$id];
    }
    return $question_categories;
}

function xmldb_quiz_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

    if ($result && $oldversion < 2007022800) {
    /// Ensure that there are not existing duplicate entries in the database.
        $duplicateunits = get_records_select('question_numerical_units', "id > (SELECT MIN(iqnu.id)
                FROM {$CFG->prefix}question_numerical_units iqnu
                WHERE iqnu.question = {$CFG->prefix}question_numerical_units.question AND
                        iqnu.unit = {$CFG->prefix}question_numerical_units.unit)", '', 'id');
        if ($duplicateunits) {
            delete_records_select('question_numerical_units', 'id IN (' . implode(',', array_keys($duplicateunits)) . ')');
        }

    /// Define index question-unit (unique) to be added to question_numerical_units
        $table = new XMLDBTable('question_numerical_units');
        $index = new XMLDBIndex('question-unit');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('question', 'unit'));

    /// Launch add index question-unit
        $result = $result && add_index($table, $index);
    }

    if ($result && $oldversion < 2007050805) {
        
        $question_categories = get_records('question_categories');
        
        foreach ($question_categories as $id => $question_category){
            $course = $question_categories[$id]->course;
            unset($question_categories[$id]->course);
            if ($question_categories[$id]->publish){
                $context = get_context_instance(CONTEXT_SYSTEM);
            } else {
                $context = get_context_instance(CONTEXT_COURSE, $course);
            }
            $question_categories[$id]->contextid = $context->id;
            unset($question_categories[$id]->publish);
        }
        
        $question_categories = question_category_checking($question_categories);
        
           

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
        
        foreach ($question_categories as $question_category){
            if (!$result = update_record('question_categories', $question_category)){
                notify('Couldn\'t update question_categories "'. $question_category->name .'"!');
            }
        }
    }

    return $result;
}

?>
