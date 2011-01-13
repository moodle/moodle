<?php

// This file keeps track of upgrades to
// the match qtype plugin
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_qtype_match_upgrade($oldversion) {
    global $CFG, $DB, $QTYPES;

    $dbman = $DB->get_manager();

    if ($oldversion < 2009072100) {

        // Define field questiontextformat to be added to question_match_sub
        $table = new xmldb_table('question_match_sub');
        $field = new xmldb_field('questiontextformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'questiontext');

        // Conditionally launch add field questiontextformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // In the past, question_match_sub.questiontext assumed to contain
        // content of the same form as question.questiontextformat. If we are
        // using the HTML editor, then convert FORMAT_MOODLE content to FORMAT_HTML.
        $rs = $DB->get_recordset_sql('
                SELECT qms.*, q.oldquestiontextformat
                FROM {question_match_sub} qms
                JOIN {question} q ON qms.question = q.id');
        foreach ($rs as $record) {
            if ($CFG->texteditors !== 'textarea' && $record->oldquestiontextformat == FORMAT_MOODLE) {
                $record->questiontext = text_to_html($record->questiontext, false, false, true);
                $record->questiontextformat = FORMAT_HTML;
            } else {
                $record->questiontextformat = $record->oldquestiontextformat;
            }
            $DB->update_record('question_match_sub', $record);
        }
        $rs->close();

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2009072100, 'qtype', 'match');
    }

    if ($oldversion < 2011011300) {

        // Define field correctfeedback to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'shuffleanswers');

        // Conditionally launch add field correctfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011300, 'qtype', 'match');
    }

    if ($oldversion < 2011011301) {

        // Define field correctfeedbackformat to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('correctfeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'correctfeedback');

        // Conditionally launch add field correctfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011301, 'qtype', 'match');
    }

    if ($oldversion < 2011011302) {

        // Define field partiallycorrectfeedback to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'correctfeedbackformat');

        // Conditionally launch add field partiallycorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011302, 'qtype', 'match');
    }

    if ($oldversion < 2011011303) {

        // Define field partiallycorrectfeedbackformat to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'partiallycorrectfeedback');

        // Conditionally launch add field partiallycorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011303, 'qtype', 'match');
    }

    if ($oldversion < 2011011304) {

        // Define field incorrectfeedback to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'partiallycorrectfeedbackformat');

        // Conditionally launch add field incorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011304, 'qtype', 'match');
    }

    if ($oldversion < 2011011305) {

        // Define field incorrectfeedbackformat to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('incorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'incorrectfeedback');

        // Conditionally launch add field incorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011305, 'qtype', 'match');
    }

    if ($oldversion < 2011011306) {

        // Define field shownumcorrect to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('shownumcorrect', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'incorrectfeedbackformat');

        // Conditionally launch add field shownumcorrect
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011306, 'qtype', 'match');
    }

    return true;
}
