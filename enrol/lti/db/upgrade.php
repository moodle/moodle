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
//

/**
 * This file keeps track of upgrades to the lti enrolment plugin
 *
 * @package enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

/**
 * xmldb_lti_upgrade is the function that upgrades
 * the lti module database when is needed
 *
 * This function is automaticly called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 */
function xmldb_enrol_lti_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016052303) {

        // Define table enrol_lti_lti2_consumer to be created.
        $table = new xmldb_table('enrol_lti_lti2_consumer');

        // Adding fields to table enrol_lti_lti2_consumer.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consumerkey256', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consumerkey', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('secret', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ltiversion', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('consumername', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('consumerversion', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('consumerguid', XMLDB_TYPE_CHAR, '1024', null, null, null, null);
        $table->add_field('profile', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('toolproxy', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('settings', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('protected', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enablefrom', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('enableuntil', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('lastaccess', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_consumer.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table enrol_lti_lti2_consumer.
        $table->add_index('consumerkey256_uniq', XMLDB_INDEX_UNIQUE, array('consumerkey256'));

        // Conditionally launch create table for enrol_lti_lti2_consumer.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_tool_proxy to be created.
        $table = new xmldb_table('enrol_lti_lti2_tool_proxy');

        // Adding fields to table enrol_lti_lti2_tool_proxy.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('toolproxykey', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consumerid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('toolproxy', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_tool_proxy.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('toolproxykey_uniq', XMLDB_KEY_UNIQUE, array('toolproxykey'));
        $table->add_key('consumerid', XMLDB_KEY_FOREIGN, array('consumerid'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_tool_proxy.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_context to be created.
        $table = new xmldb_table('enrol_lti_lti2_context');

        // Adding fields to table enrol_lti_lti2_context.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('consumerid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lticontextkey', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('settings', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_context.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('consumerid', XMLDB_KEY_FOREIGN, array('consumerid'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_context.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_nonce to be created.
        $table = new xmldb_table('enrol_lti_lti2_nonce');

        // Adding fields to table enrol_lti_lti2_nonce.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('consumerid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_nonce.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('consumerid', XMLDB_KEY_FOREIGN, array('consumerid'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_nonce.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_resource_link to be created.
        $table = new xmldb_table('enrol_lti_lti2_resource_link');

        // Adding fields to table enrol_lti_lti2_resource_link.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('consumerid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('ltiresourcelinkkey', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('settings', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('primaryresourcelinkid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('shareapproved', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_resource_link.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'enrol_lti_lti2_context', array('id'));
        $table->add_key('primaryresourcelinkid', XMLDB_KEY_FOREIGN, array('primaryresourcelinkid'),
            'enrol_lti_lti2_resource_link', array('id'));
        $table->add_key('consumerid', XMLDB_KEY_FOREIGN, array('consumerid'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_resource_link.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_share_key to be created.
        $table = new xmldb_table('enrol_lti_lti2_share_key');

        // Adding fields to table enrol_lti_lti2_share_key.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sharekey', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resourcelinkid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('autoapprove', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_share_key.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('sharekey', XMLDB_KEY_UNIQUE, array('sharekey'));
        $table->add_key('resourcelinkid', XMLDB_KEY_FOREIGN_UNIQUE, array('resourcelinkid'),
            'enrol_lti_lti2_resource_link', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_share_key.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_user_result to be created.
        $table = new xmldb_table('enrol_lti_lti2_user_result');

        // Adding fields to table enrol_lti_lti2_user_result.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('resourcelinkid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ltiuserkey', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ltiresultsourcedid', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_user_result.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('resourcelinkid', XMLDB_KEY_FOREIGN, array('resourcelinkid'),
            'enrol_lti_lti2_resource_link', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_user_result.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_tool_consumer_map to be created.
        $table = new xmldb_table('enrol_lti_tool_consumer_map');

        // Adding fields to table enrol_lti_tool_consumer_map.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('toolid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consumerid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_tool_consumer_map.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('toolid', XMLDB_KEY_FOREIGN, array('toolid'), 'enrol_lti_tools', array('id'));
        $table->add_key('consumerid', XMLDB_KEY_FOREIGN, array('consumerid'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_tool_consumer_map.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2016052303, 'enrol', 'lti');
    }

    if ($oldversion < 2016052304) {

        // Define field type to be added to enrol_lti_lti2_context.
        $table = new xmldb_table('enrol_lti_lti2_context');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'lticontextkey');

        // Conditionally launch add field type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2016052304, 'enrol', 'lti');
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017011300) {

        // Changing precision of field value on table enrol_lti_lti2_nonce to (64).
        $table = new xmldb_table('enrol_lti_lti2_nonce');
        $field = new xmldb_field('value', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'consumerid');

        // Launch change of precision for field value.
        $dbman->change_field_precision($table, $field);

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2017011300, 'enrol', 'lti');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
