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

    if ($oldversion < 2008000703) {
        // Rename the quiz_report table to quiz_reports.
        $table = new xmldb_table('quiz_report');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'quiz_reports');
        }

        upgrade_plugin_savepoint(true, 2008000703, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000704) {

        // Changing nullability of field sumgrades on table quiz_attempts to null
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'attempt');

        // Launch change of nullability for field sumgrades
        $dbman->change_field_notnull($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000704, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000799) {
        // Define field needsupgradetonewqe to be added to quiz_attempts
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('needsupgradetonewqe', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'preview');

        // Launch add field needsupgradetonewqe
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->set_field('quiz_attempts', 'needsupgradetonewqe', 1);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000800, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000800) {
        $table = new xmldb_table('question_states');
        if ($dbman->table_exists($table)) {
            // First delete all data from preview attempts.
            $DB->delete_records_select('question_states',
                    "attempt IN (SELECT uniqueid FROM {quiz_attempts} WHERE preview = 1)");
            $DB->delete_records_select('question_sessions',
                    "attemptid IN (SELECT uniqueid FROM {quiz_attempts} WHERE preview = 1)");
            $DB->delete_records('quiz_attempts', array('preview' => 1));

            // Now update all the old attempt data.
            $oldrcachesetting = $CFG->rcache;
            $CFG->rcache = false;

            require_once($CFG->dirroot . '/question/engine/upgradefromoldqe/upgrade.php');
            $upgrader = new question_engine_attempt_upgrader();
            $upgrader->convert_all_quiz_attempts();

            $CFG->rcache = $oldrcachesetting;
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000800, 'local', 'qedatabase');
    }
}
