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
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_cas.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_tool_dataprivacy_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017051500) {
        // Nothing to do here. Moodle Plugins site's just complaining about missing upgrade.php.
        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2017051500, 'error', 'dataprivacy');
    }

    if ($oldversion < 2017051503) {

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

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2017051503, 'tool', 'dataprivacy');
    }

    if ($oldversion < 2017051504) {

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

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2017051504, 'tool', 'dataprivacy');
    }

    if ($oldversion < 2017051506) {
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
        upgrade_plugin_savepoint(true, 2017051506, 'tool', 'dataprivacy');
    }

    return true;
}
