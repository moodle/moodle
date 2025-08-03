<?php
// Upgrade script for tool_bruteforce.

defined('MOODLE_INTERNAL') || die();

function xmldb_tool_bruteforce_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025031500) {
        // Add unique index to list table to avoid duplicates.
        $table = new xmldb_table('tool_bruteforce_list');
        $index = new xmldb_index('listuniq', XMLDB_INDEX_UNIQUE, ['listtype', 'type', 'value']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_plugin_savepoint(true, 2025031500, 'tool', 'bruteforce');
    }

    if ($oldversion < 2025031600) {
        // Ensure non-unique indexes exist on log table for userid and ip.
        $table = new xmldb_table('tool_bruteforce_log');

        $useridx = new xmldb_index('userix', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        if (!$dbman->index_exists($table, $useridx)) {
            $dbman->add_index($table, $useridx);
        }

        $ipidx = new xmldb_index('ipix', XMLDB_INDEX_NOTUNIQUE, ['ip']);
        if (!$dbman->index_exists($table, $ipidx)) {
            $dbman->add_index($table, $ipidx);
        }

        upgrade_plugin_savepoint(true, 2025031600, 'tool', 'bruteforce');
    }

    return true;
}