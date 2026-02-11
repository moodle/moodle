<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_aiemotion_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025122708) {

        $table = new xmldb_table('local_aiemotion_assign');

        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('assignmentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('enableaifeedback', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
            $table->add_field('aifeedbackprompt', XMLDB_TYPE_TEXT, null, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);

            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('assignmentid_uk', XMLDB_INDEX_UNIQUE, ['assignmentid']);

            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025122708, 'local', 'aiemotion');
    }

    return true;
}
