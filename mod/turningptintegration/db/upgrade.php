<?php

/**
 * Upgrade code for install
 *
 * @package   mod_turningptintegration
 * @copyright 2019 Turning Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * upgrade this turningptintegration instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the turningptintegration module
 * @return bool
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_turningptintegration_upgrade($oldversion = 0) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019090900) {
        //3.0.x+ release of plugin
        $device_types_table = new xmldb_table('turningptintegration_device_types');
        if ($dbman->table_exists($device_types_table)) {
            $dbman->drop_table($device_types_table, true, true);
        }

        $device_mapping_table = new xmldb_table('turningptintegration_device_mapping');
        if ($dbman->table_exists($device_mapping_table)) {
            $dbman->drop_table($device_mapping_table, true, true);
        }

        $device_escrow_table = new xmldb_table('turningptintegration_escrow');
        if ($dbman->table_exists($device_escrow_table)) {
            $dbman->drop_table($device_escrow_table, true, true);
        }

        upgrade_mod_savepoint(true, 2019090900, 'turningptintegration');
    }

    return true;
}
