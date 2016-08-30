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
        $table->add_field('consumer_key256', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consumer_key', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('secret', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lti_version', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('consumer_name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('consumer_version', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('consumer_guid', XMLDB_TYPE_CHAR, '1024', null, null, null, null);
        $table->add_field('profile', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('tool_proxy', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('settings', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('protected', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enable_from', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('enable_until', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('last_access', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_consumer.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table enrol_lti_lti2_consumer.
        $table->add_index('consumer_key256_uniq', XMLDB_INDEX_UNIQUE, array('consumer_key256'));

        // Conditionally launch create table for enrol_lti_lti2_consumer.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_tool_proxy to be created.
        $table = new xmldb_table('enrol_lti_lti2_tool_proxy');

        // Adding fields to table enrol_lti_lti2_tool_proxy.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('tool_proxy_id', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consumer_pk', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tool_proxy', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_tool_proxy.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('tool_proxy_id_uniq', XMLDB_KEY_UNIQUE, array('tool_proxy_id'));
        $table->add_key('consumer_pk', XMLDB_KEY_FOREIGN, array('consumer_pk'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_tool_proxy.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_context to be created.
        $table = new xmldb_table('enrol_lti_lti2_context');

        // Adding fields to table enrol_lti_lti2_context.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('consumer_pk', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lti_context_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('settings', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_context.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('consumer_pk', XMLDB_KEY_FOREIGN, array('consumer_pk'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_context.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_nonce to be created.
        $table = new xmldb_table('enrol_lti_lti2_nonce');

        // Adding fields to table enrol_lti_lti2_nonce.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('consumer_pk', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_nonce.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('consumer_pk', XMLDB_KEY_FOREIGN_UNIQUE, array('consumer_pk'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_nonce.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_resource_link to be created.
        $table = new xmldb_table('enrol_lti_lti2_resource_link');

        // Adding fields to table enrol_lti_lti2_resource_link.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('context_pk', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('consumer_pk', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('lti_resource_link_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('settings', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('primary_resource_link_pk', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('share_approved', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_resource_link.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('context_pk', XMLDB_KEY_FOREIGN, array('context_pk'), 'enrol_lti_lti2_context', array('id'));
        $table->add_key('primary_resource_link_pk', XMLDB_KEY_FOREIGN, array('primary_resource_link_pk'),
            'enrol_lti_lti2_resource_link', array('id'));
        $table->add_key('consumer_pk', XMLDB_KEY_FOREIGN, array('consumer_pk'), 'enrol_lti_lti2_consumer', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_resource_link.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_share_key to be created.
        $table = new xmldb_table('enrol_lti_lti2_share_key');

        // Adding fields to table enrol_lti_lti2_share_key.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('share_key_id', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resource_link_pk', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('auto_approve', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_share_key.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('share_key_id', XMLDB_KEY_UNIQUE, array('share_key_id'));
        $table->add_key('resource_link_pk', XMLDB_KEY_FOREIGN_UNIQUE, array('resource_link_pk'), 'enrol_lti_lti2_resource_link', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_share_key.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table enrol_lti_lti2_user_result to be created.
        $table = new xmldb_table('enrol_lti_lti2_user_result');

        // Adding fields to table enrol_lti_lti2_user_result.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('resource_link_pk', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lti_user_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lti_result_sourcedid', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_lti2_user_result.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('resource_link_pk', XMLDB_KEY_FOREIGN, array('resource_link_pk'),
            'enrol_lti_lti2_resource_link', array('id'));

        // Conditionally launch create table for enrol_lti_lti2_user_result.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2016052303, 'enrol', 'lti');
    }

    return true;
}
