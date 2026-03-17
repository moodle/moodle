<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade script for local_wellbeing
 */
function xmldb_local_wellbeing_upgrade($oldversion) {

    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026032201) {

        $table = new xmldb_table('local_wellbeing_courses');

        /* metrics_name_json column */

        $metricsname = new xmldb_field(
            'metrics_name_json',
            XMLDB_TYPE_TEXT,
            null,
            null,
            null,
            null,
            null,
            'courseid'
        );

        if (!$dbman->field_exists($table, $metricsname)) {
            $dbman->add_field($table, $metricsname);
        }

        /* metrics_prompt column */

        $metricsprompt = new xmldb_field(
            'metrics_prompt',
            XMLDB_TYPE_TEXT,
            null,
            null,
            null,
            null,
            null,
            'metrics_name_json'
        );

        if (!$dbman->field_exists($table, $metricsprompt)) {
            $dbman->add_field($table, $metricsprompt);
        }

        /* savepoint */

        upgrade_plugin_savepoint(true, 2026032201, 'local', 'wellbeing');
    }

     if ($oldversion < 2026032205) {

        // Define table
        $table = new xmldb_table('local_wb_assign_metrics');

        // Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        $table->add_field('assignid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);

        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);

        $table->add_field('metricname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL);

        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);

        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null);

        // Keys
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Indexes
        $table->add_index('assignid_idx', XMLDB_INDEX_NOTUNIQUE, ['assignid']);
        $table->add_index('courseid_idx', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Create table
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Upgrade savepoint
        upgrade_plugin_savepoint(true, 2026032205, 'local', 'wellbeing');
    }
    return true;
}