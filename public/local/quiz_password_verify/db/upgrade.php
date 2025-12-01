<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_quiz_password_verify_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024112504) {
        $table = new xmldb_table('local_quiz_pwd_verify');
        $field = new xmldb_field('attemptid', XMLDB_TYPE_INTEGER, '10', null, false, null, null, 'userid');
        $index = new xmldb_index('attemptid_idx', XMLDB_INDEX_NOTUNIQUE, ['attemptid']);
        $key = new xmldb_key('attemptid', XMLDB_KEY_FOREIGN, ['attemptid'], 'quiz_attempts', ['id']);

        // 1. Drop Foreign Key if exists
        if ($dbman->find_key_name($table, $key)) {
            $dbman->drop_key($table, $key);
        }

        // 2. Drop Index if exists
        if ($dbman->find_index_name($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // 3. Change field to nullable
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        // 4. Re-create Index
        if (!$dbman->find_index_name($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // 5. Re-create Foreign Key
        if (!$dbman->find_key_name($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2024112504, 'local', 'quiz_password_verify');
    }

    return true;
}
