<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_wellbeing_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026021007) {

        $table = new xmldb_table('local_wellbeing_metrics');

        $field = new xmldb_field(
            'timemodified',
            XMLDB_TYPE_INTEGER,
            '10',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'timecreated'
        );

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026021007, 'local', 'wellbeing');
    }

    return true;
}
