<?php

// This file keeps track of upgrades to
// the numerical qtype plugin
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

function xmldb_qtype_numerical_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//
    if ($oldversion < 2009100100 ) { //New version in version.php

    /// Define table question_numerical_options to be created
        $table = new xmldb_table('question_numerical_options');

    /// Adding fields to table question_numerical_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('instructions', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('showunits', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('unitsleft', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('unitgradingtype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('unitpenalty', XMLDB_TYPE_NUMBER, '12, 7', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0.1');

    /// Adding keys to table question_numerical_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));
    /// Conditionally launch create table for question_calculated_options
        if (!$dbman->table_exists($table)) {
            // $dbman->create_table doesnt return a result, we just have to trust it
            $dbman->create_table($table);
        }//else
        upgrade_plugin_savepoint(true, 2009100100, 'qtype', 'numerical');
    }

    if ($oldversion < 2009100101) {

        // Define field instructionsformat to be added to question_numerical_options
        $table = new xmldb_table('question_numerical_options');
        $field = new xmldb_field('instructionsformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'instructions');

        // Conditionally launch add field instructionsformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // In the past, question_match_sub.questiontext assumed to contain
        // content of the same form as question.questiontextformat. If we are
        // using the HTML editor, then convert FORMAT_MOODLE content to FORMAT_HTML.
        $rs = $DB->get_recordset_sql('
                SELECT qno.*, q.oldquestiontextformat
                FROM {question_numerical_options} qno
                JOIN {question} q ON qno.question = q.id');
        foreach ($rs as $record) {
            if ($CFG->texteditors !== 'textarea' && $record->oldquestiontextformat == FORMAT_MOODLE) {
                $record->instructions = text_to_html($record->instructions, false, false, true);
                $record->instructionsformat = FORMAT_HTML;
            } else {
                $record->instructionsformat = $record->oldquestiontextformat;
            }
            $DB->update_record('question_numerical_options', $record);
        }
        $rs->close();

        // numerical savepoint reached
        upgrade_plugin_savepoint(true, 2009100101, 'qtype', 'numerical');
    }

    return true;
}


