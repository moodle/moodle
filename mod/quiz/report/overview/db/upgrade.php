<?php

function xmldb_quiz_overview_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2009091400) {

    /// Define table quiz_question_regrade to be created
        $table = new xmldb_table('quiz_question_regrade');

    /// Adding fields to table quiz_question_regrade
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('attemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('newgrade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null);
        $table->add_field('oldgrade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null);
        $table->add_field('regraded', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table quiz_question_regrade
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for quiz_question_regrade
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// overview savepoint reached
        upgrade_plugin_savepoint(true, 2009091400, 'quizreport', 'overview');
    }

    return true;
}


