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

        $rs = $DB->get_recordset('question_match_sub');
        foreach ($rs as $record) {
            if ($CFG->texteditors !== 'textarea') {
                if (!empty($record->questiontext)) {
                    $record->questiontext = text_to_html($record->questiontext);
                }
                $record->questiontextformat = FORMAT_HTML;
            } else {
                $record->questiontextformat = FORMAT_MOODLE;
            }
            $DB->update_record('question_match_sub', $record);
        }
        $rs->close();

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2009072100, 'qtype', 'match');
    }

    return true;
}
