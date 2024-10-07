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
 * tool_dataprivacy plugin upgrade code
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade tool_dataprivacy.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_tool_dataprivacy_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023062700) {
        // Define table tool_dataprivacy_contextlist to be created.
        $table = new xmldb_table('tool_dataprivacy_contextlist');

        // Adding fields to table tool_dataprivacy_contextlist.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table tool_dataprivacy_contextlist.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for tool_dataprivacy_contextlist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table tool_dataprivacy_ctxlst_ctx to be created.
        $table = new xmldb_table('tool_dataprivacy_ctxlst_ctx');

        // Adding fields to table tool_dataprivacy_ctxlst_ctx.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextlistid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table tool_dataprivacy_ctxlst_ctx.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('contextlistid', XMLDB_KEY_FOREIGN, ['contextlistid'], 'tool_dataprivacy_contextlist', ['id']);

        // Conditionally launch create table for tool_dataprivacy_ctxlst_ctx.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table tool_dataprivacy_rqst_ctxlst to be created.
        $table = new xmldb_table('tool_dataprivacy_rqst_ctxlst');

        // Adding fields to table tool_dataprivacy_rqst_ctxlst.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('requestid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextlistid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_dataprivacy_rqst_ctxlst.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('requestid', XMLDB_KEY_FOREIGN, ['requestid'], 'tool_dataprivacy_request', ['id']);
        $table->add_key('contextlistid', XMLDB_KEY_FOREIGN, ['contextlistid'], 'tool_dataprivacy_contextlist', ['id']);
        $table->add_key('requestidcontextlistid', XMLDB_KEY_UNIQUE, ['requestid', 'contextlistid']);

        // Conditionally launch create table for tool_dataprivacy_rqst_ctxlst.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2023062700, 'tool', 'dataprivacy');
    }

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
