<?php  // $Id$

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

    if ($result && $oldversion < 2007070200) {

    /// Changing precision of field timelimit on table quiz to (10)
        $table = new XMLDBTable('quiz');
        $field = new XMLDBField('timelimit');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'timemodified');

    /// Launch change of precision for field timelimit
        $result = $result && change_field_precision($table, $field);
    }

    if ($result && $oldversion < 2007072200) {
        require_once $CFG->dirroot.'/mod/quiz/lib.php';
        // too much debug output
        $db->debug = false;
        quiz_update_grades();
        $db->debug = true;
    }

    // Separate control for when overall feedback is displayed, independant of the question feedback settings.
    if ($result && $oldversion < 2007072600) {

        // Adjust the quiz review options so that overall feedback is displayed whenever feedback is.
        $result = $result && execute_sql('UPDATE ' . $CFG->prefix . 'quiz SET review = ' .
                sql_bitor(sql_bitand('review', sql_bitnot(QUIZ_REVIEW_OVERALLFEEDBACK)),
                sql_bitor(sql_bitand('review', QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY) . ' * 65536',
                sql_bitor(sql_bitand('review', QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_OPEN) . ' * 16384',
                          sql_bitand('review', QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_CLOSED) . ' * 4096'))));

        // Same adjustment to the defaults for new quizzes.
        $result = $result && set_config('quiz_review', ($CFG->quiz_review & ~QUIZ_REVIEW_OVERALLFEEDBACK) |
                (($CFG->quiz_review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY) << 16) |
                (($CFG->quiz_review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_OPEN) << 14) |
                (($CFG->quiz_review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_CLOSED) << 12));
    }

//===== 1.9.0 upgrade line ======//

    return $result;
}

?>
