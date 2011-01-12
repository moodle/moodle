<?php

function xmldb_local_qedatabase_upgrade($oldversion) {
    global $CFG, $DB, $QTYPES;

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
}
