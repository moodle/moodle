<?php  // $Id$

// This file keeps track of upgrades to
// the ddwtos qtype plugin
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

function xmldb_qtype_ddwtos_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    // Rename question_ddwtos table to question_ddwtos
    // as it is too long for Oracle
    if ($result && $oldversion < 2008121900) {

    /// Define table course_extended_meta to be created
        $table = new XMLDBTable('question_ddwordsintosentences');

    /// Do nothing if table already exists (probably created from xml file)
        if (table_exists($table)) {

        /// Define table question_ddwtos to be renamed to NEWNAMEGOESHERE
            $table = new XMLDBTable('question_ddwordsintosentences');

        /// Launch rename table for question_ddwtos
            $result = $result && rename_table($table, 'question_ddwtos');
        }
    }

    if ($oldversion < 2009052000) {

        $table = new XMLDBTable('question_ddanswers');
        if (table_exists($table) && ($ddanswers = get_records('question_ddanswers'))) {
            foreach($ddanswers as $ddanswer){
                $answer = new stdClass;

                $answer->question = $ddanswer->questionid;
                $answer->answer = addslashes($ddanswer->answer);
                $answer->fraction = 1;

                $feedback = new stdClass;
                $feedback->draggroup = $ddanswer->draggroup;
                $feedback->infinite = $ddanswer->infinite;
                $answer->feedback = serialize($feedback);

                if(!insert_record('question_answers', $answer)){
                    notify('move_question_ddanswers_to_question_answers(): cannot insert row into question_answer table.');
                    return false;
                }
            }
        }

        // Drop table
        $result = $result && drop_table($table);
    }

    if ($result && $oldversion < 2010042800) {

    /// Rename field correctresponsesfeedback on table question_ddwtos to shownumcorrect
        $table = new XMLDBTable('question_ddwtos');
        $field = new XMLDBField('correctresponsesfeedback');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'incorrectfeedback');

    /// Launch rename field correctresponsesfeedback
        $result = $result && rename_field($table, $field, 'shownumcorrect');
    }

    return $result;
}
