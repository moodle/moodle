<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade code containing changes to the plugin data table.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */
function xmldb_local_kaltura_upgrade($oldversion) {
    global $CFG, $DB;

    $savePointDone = false;
    require_once($CFG->dirroot.'/local/kaltura/locallib.php');

    $dbman = $DB->get_manager();

    // plugin in any version below this is 3.x and requires migration
    if ($oldversion < 2014023000) {
        // Because the plug-in is being upgraded we need to set the migration flag to true.
        set_config('migration_yes', 1, KALTURA_PLUGIN_NAME);

        // Define table local_kaltura_log to be created.
        $table = new xmldb_table('local_kaltura_log');

        // Adding fields to table local_kaltura_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('module', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '3', null, XMLDB_NOTNULL, null, null);
        $table->add_field('endpoint', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_kaltura_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table local_kaltura_log.
        $table->add_index('module_idx', XMLDB_INDEX_NOTUNIQUE, array('module'));
        $table->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, array('timecreated'));

        // Conditionally launch create table for local_kaltura_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Kaltura savepoint reached.
        upgrade_plugin_savepoint(true, 2016070730, 'local', 'kaltura');
        $savePointDone = true;
    }

    if (!$savePointDone && $oldversion < 2016070730) {
        if($dbman->table_exists('local_kaltura_log') && $dbman->field_exists('local_kaltura_log', 'endpoint')) {
            $table = new xmldb_table('local_kaltura_log');
            $updatedFieldSchema = new xmldb_field('endpoint', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, null);
            $dbman->change_field_type($table, $updatedFieldSchema);
        }

        // Kaltura savepoint reached.
        upgrade_plugin_savepoint(true, 2016070730, 'local', 'kaltura');
    }
    return true;
}
