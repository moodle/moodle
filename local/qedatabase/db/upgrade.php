<?php

function xmldb_local_qedatabase_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2008000700) {

        // Define field hintformat to be added to question_hints table.
        $table = new xmldb_table('question_hints');
        $field = new xmldb_field('hintformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Conditionally launch add field partiallycorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2008000700, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000701) {
       // Define table quiz_report to be renamed to quiz_reports
        $table = new xmldb_table('quiz_report');

        // Launch rename table for quiz_reports
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'quiz_reports');
        }

        upgrade_plugin_savepoint(true, 2008000701, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000702) {
        // Define index name (unique) to be added to quiz_reports
        $table = new xmldb_table('quiz_reports');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, array('name'));

        // Conditionally launch add index name
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2008000702, 'local', 'qedatabase');
    }

}
