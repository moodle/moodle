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

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade tool_dataprivacy.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_tool_dataprivacy_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2018051405) {
        // Define table tool_dataprivacy_ctxexpired to be created.
        $table = new xmldb_table('tool_dataprivacy_ctxexpired');

        // Adding fields to table tool_dataprivacy_ctxexpired.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_dataprivacy_ctxexpired.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN_UNIQUE, array('contextid'), 'context', array('id'));

        // Conditionally launch create table for tool_dataprivacy_ctxexpired.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table tool_dataprivacy_contextlist to be created.
        $table = new xmldb_table('tool_dataprivacy_contextlist');

        // Adding fields to table tool_dataprivacy_contextlist.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table tool_dataprivacy_contextlist.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

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
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextlistid', XMLDB_KEY_FOREIGN, array('contextlistid'), 'tool_dataprivacy_contextlist', array('id'));

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
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('requestid', XMLDB_KEY_FOREIGN, array('requestid'), 'tool_dataprivacy_request', array('id'));
        $table->add_key('contextlistid', XMLDB_KEY_FOREIGN, array('contextlistid'), 'tool_dataprivacy_contextlist', array('id'));
        $table->add_key('request_contextlist', XMLDB_KEY_UNIQUE, array('requestid', 'contextlistid'));

        // Conditionally launch create table for tool_dataprivacy_rqst_ctxlst.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field lawfulbases to be added to tool_dataprivacy_purpose.
        $table = new xmldb_table('tool_dataprivacy_purpose');

        // It is a required field. We initially define and add it as null and later update it to XMLDB_NOTNULL.
        $field = new xmldb_field('lawfulbases', XMLDB_TYPE_TEXT, null, null, null, null, null, 'descriptionformat');

        // Conditionally launch add field lawfulbases.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Set a kind-of-random value to lawfulbasis field.
            $DB->set_field('tool_dataprivacy_purpose', 'lawfulbases', 'gdpr_art_6_1_a');

            // We redefine it now as not null.
            $field = new xmldb_field('lawfulbases', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'descriptionformat');

            // Launch change of nullability for field lawfulbases.
            $dbman->change_field_notnull($table, $field);
        }

        // Define field sensitivedatareasons to be added to tool_dataprivacy_purpose.
        $table = new xmldb_table('tool_dataprivacy_purpose');
        $field = new xmldb_field('sensitivedatareasons', XMLDB_TYPE_TEXT, null, null, null, null, null, 'lawfulbases');

        // Conditionally launch add field sensitivedatareasons.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2018051405, 'tool', 'dataprivacy');
    }

    if ($oldversion < 2018051406) {
        // Update completed delete requests to new delete status.
        $query = "UPDATE {tool_dataprivacy_request}
                     SET status = :setstatus
                   WHERE type = :type
                         AND status = :wherestatus";
        $params = array(
            'setstatus' => 10, // Request deleted.
            'type' => 2, // Delete type.
            'wherestatus' => 5, // Request completed.
        );

        $DB->execute($query, $params);

        // Update completed data export requests to new download ready status.
        $params = array(
            'setstatus' => 8, // Request download ready.
            'type' => 1, // export type.
            'wherestatus' => 5, // Request completed.
        );

        $DB->execute($query, $params);

        upgrade_plugin_savepoint(true, 2018051406, 'tool', 'dataprivacy');
    }

    if ($oldversion < 2018082100) {

        // Changing precision of field status on table tool_dataprivacy_request to (2).
        $table = new xmldb_table('tool_dataprivacy_request');
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'requestedby');

        // Launch change of precision for field status.
        $dbman->change_field_precision($table, $field);

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2018082100, 'tool', 'dataprivacy');
    }

    if ($oldversion < 2018100401) {
        // Define table tool_dataprivacy_purposerole to be created.
        $table = new xmldb_table('tool_dataprivacy_purposerole');

        // Adding fields to table tool_dataprivacy_purposerole.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('purposeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lawfulbases', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sensitivedatareasons', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('retentionperiod', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('protected', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_dataprivacy_purposerole.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('purposepurposeid', XMLDB_KEY_FOREIGN, ['purposeid'], 'tool_dataprivacy_purpose', ['id']);
        $table->add_key('puproseroleid', XMLDB_KEY_FOREIGN, ['roleid'], 'role', ['id']);

        // Adding indexes to table tool_dataprivacy_purposerole.
        $table->add_index('purposerole', XMLDB_INDEX_UNIQUE, ['purposeid', 'roleid']);

        // Conditionally launch create table for tool_dataprivacy_purposerole.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Update the ctxexpired table.
        $table = new xmldb_table('tool_dataprivacy_ctxexpired');

        // Add the unexpiredroles field.
        $field = new xmldb_field('unexpiredroles', XMLDB_TYPE_TEXT, null, null, null, null, null, 'contextid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $DB->set_field('tool_dataprivacy_ctxexpired', 'unexpiredroles', '');

        // Add the expiredroles field.
        $field = new xmldb_field('expiredroles', XMLDB_TYPE_TEXT, null, null, null, null, null, 'unexpiredroles');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $DB->set_field('tool_dataprivacy_ctxexpired', 'expiredroles', '');

        // Add the defaultexpired field.
        $field = new xmldb_field('defaultexpired', XMLDB_TYPE_INTEGER, '1', null, null, null, '1', 'expiredroles');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Change the default for the expired field to be empty.
        $field = new xmldb_field('defaultexpired', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'expiredroles');
        $dbman->change_field_default($table, $field);

        // Prevent hte field from being nullable.
        $field = new xmldb_field('defaultexpired', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, 'expiredroles');
        $dbman->change_field_notnull($table, $field);

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2018100401, 'tool', 'dataprivacy');
    }

    if ($oldversion < 2018100406) {
        // Define field sensitivedatareasons to be added to tool_dataprivacy_purpose.
        $table = new xmldb_table('tool_dataprivacy_request');
        $field = new xmldb_field('creationmethod', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0, 'timemodified');

        // Conditionally launch add field sensitivedatareasons.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2018100406, 'tool', 'dataprivacy');
    }


    if ($oldversion < 2018110700) {
        // Define table tool_dataprivacy_ctxlst_ctx to be dropped.
        $table = new xmldb_table('tool_dataprivacy_ctxlst_ctx');

        // Conditionally launch drop table for tool_dataprivacy_ctxlst_ctx.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table tool_dataprivacy_rqst_ctxlst to be dropped.
        $table = new xmldb_table('tool_dataprivacy_rqst_ctxlst');

        // Conditionally launch drop table for tool_dataprivacy_rqst_ctxlst.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table tool_dataprivacy_contextlist to be dropped.
        $table = new xmldb_table('tool_dataprivacy_contextlist');

        // Conditionally launch drop table for tool_dataprivacy_contextlist.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Update all requests which were in states Pending, or Pre-Processing, to Awaiting approval.
        $DB->set_field('tool_dataprivacy_request', 'status', 2, ['status' => 0]);
        $DB->set_field('tool_dataprivacy_request', 'status', 2, ['status' => 1]);

        // Remove the old initiate_data_request_task adhoc entries.
        $DB->delete_records('task_adhoc', ['classname' => '\tool_dataprivacy\task\initiate_data_request_task']);

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2018110700, 'tool', 'dataprivacy');
    }

    if ($oldversion < 2018112500) {
        // Delete orphaned data privacy requests.
        $sql = "SELECT r.id
                  FROM {tool_dataprivacy_request} r LEFT JOIN {user} u ON r.userid = u.id
                 WHERE u.id IS NULL";
        $orphaned = $DB->get_fieldset_sql($sql);

        if ($orphaned) {
            $DB->delete_records_list('tool_dataprivacy_request', 'id', $orphaned);
        }

        upgrade_plugin_savepoint(true, 2018112500, 'tool', 'dataprivacy');
    }

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
