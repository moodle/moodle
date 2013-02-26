<?php

// This file keeps track of upgrades to
// the survey module
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

function xmldb_survey_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    //===== 1.9.0 upgrade line ======//

    if ($oldversion < 2009042002) {

        // Define field introformat to be added to survey.
        $table = new xmldb_table('survey');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        // Conditionally launch add field introformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally migrate to html format in intro.
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('survey', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $s) {
                $s->intro       = text_to_html($s->intro, false, false, true);
                $s->introformat = FORMAT_HTML;
                $DB->update_record('survey', $s);
                upgrade_set_timeout();
            }
            $rs->close();
        }

        // Survey savepoint reached.
        upgrade_mod_savepoint(true, 2009042002, 'survey');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2011112901) {
        // Tables to change.
        $arrtables = array();
        $arrtables['survey'] = new xmldb_table('survey');
        $arrtables['survey_answers'] = new xmldb_table('survey_answers');
        $arrtables['survey_questions'] = new xmldb_table('survey_questions');
        // Columns to change.
        $arrfields = array();
        $arrfields['survey']['intro'] = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, '', 'name');
        $arrfields['survey']['questions'] = new xmldb_field('questions', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '', 'introformat');
        $arrfields['survey_answers']['time'] = new xmldb_field('time', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'question');
        $arrfields['survey_answers']['answer1'] = new xmldb_field('answer1', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, '', 'time');
        $arrfields['survey_answers']['answer2'] = new xmldb_field('answer2', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, '', 'answer1');
        $arrfields['survey_questions']['intro'] = new xmldb_field('intro', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, '', 'multi');

        // Loop through the tables.
        foreach ($arrtables as $tablename => $table) {
            // Loop through columns and update the fields.
            foreach ($arrfields[$tablename] as $fieldname => $field) {
                // Check the field exists.
                if ($dbman->field_exists($tablename, $fieldname)) {
                    // If the field name is time, then we set to 0, not empty as SQL will fail.
                    if ($fieldname == 'time') {
                        $DB->execute("UPDATE " . $CFG->prefix . $tablename . " SET {$fieldname} = 0 WHERE {$fieldname} IS NULL", array($DB->sql_empty()));
                    } else {
                        $DB->execute("UPDATE " . $CFG->prefix . $tablename . " SET {$fieldname} = ? WHERE {$fieldname} IS NULL", array($DB->sql_empty()));
                    }
                    $dbman->change_field_precision($table, $field);
                }
            }
        }

        // Survey savepoint reached.
        upgrade_mod_savepoint(true, 2011112901, 'survey');
    }

    return true;
}


