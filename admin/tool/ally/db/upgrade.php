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
 * Upgrade path.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade path.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_tool_ally_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016121501) {
        // Migrate settings from report_allylti to tool_ally.
        $settings = ['key', 'secret', 'adminurl'];
        foreach ($settings as $setting) {
            $value = get_config('report_allylti', $setting);

            if ($value !== false) {
                set_config($setting, $value, 'tool_ally');
            }

            unset_config($setting, 'report_allylti');
        }

        upgrade_plugin_savepoint(true, 2016121501, 'tool', 'ally');
    }

    if ($oldversion < 2016121900) {

        // Define table tool_ally_deleted_files to be created.
        $table = new xmldb_table('tool_ally_deleted_files');

        // Adding fields to table tool_ally_deleted_files.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('pathnamehash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, null, null, null);
        $table->add_field('mimetype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('timedeleted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_ally_deleted_files.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for tool_ally_deleted_files.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2016121900, 'tool', 'ally');
    }

    if ($oldversion < 2016121910) {
        $user = $DB->get_record('user', ['username' => 'ally_webuser']);
        if ($user) {
            // We only do this if auto config has created a user, we are not doing auto config here.
            $user->policyagreed = 1;
            $DB->update_record('user', $user);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2016121910, 'tool', 'ally');
    }

    if ($oldversion < 2017120811) {

        // Define table tool_ally_deleted_content to be created.
        $table = new xmldb_table('tool_ally_deleted_content');

        // Adding fields to table tool_ally_deleted_content.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('comptable', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('field', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timedeleted', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_ally_deleted_content.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for tool_ally_deleted_content.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2017120811, 'tool', 'ally');
    }

    if ($oldversion < 2017120822) {

        // Define table tool_ally_content_queue to be created.
        $table = new xmldb_table('tool_ally_content_queue');

        // Adding fields to table tool_ally_content_queue.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('componentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('comptable', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('compfield', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('eventtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('eventname', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_ally_content_queue.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table tool_ally_content_queue.
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
        $table->add_index('component', XMLDB_INDEX_NOTUNIQUE, array('component'));

        // Conditionally launch create table for tool_ally_content_queue.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2017120822, 'tool', 'ally');
    }

    if ($oldversion < 2018080200) {

        // Define field attempts to be added to tool_ally_content_queue.
        $table = new xmldb_table('tool_ally_content_queue');

        $field = new xmldb_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'eventname');

        // Conditionally launch add field content.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('attempts', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'content');

        // Conditionally launch add field attempts.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Rename field field on table tool_ally_deleted_content to compfield.
        $table = new xmldb_table('tool_ally_deleted_content');
        $field = new xmldb_field('field', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, 'instanceid');

        // Launch rename field compfield.
        $dbman->rename_field($table, $field, 'compfield');

        // Rename field instanceid on table tool_ally_deleted_content to comprowid.
        $field = new xmldb_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'comptable');

        // Launch rename field comprowid.
        $dbman->rename_field($table, $field, 'comprowid');

        // Conditionally launch add field timeprocessed.
        $field = new xmldb_field('timeprocessed', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timedeleted');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Rename field componentid on table tool_ally_content_queue to comprowid.
        $table = new xmldb_table('tool_ally_content_queue');
        $field = new xmldb_field('componentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field comprowid.
        $dbman->rename_field($table, $field, 'comprowid');

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2018080200, 'tool', 'ally');
    }

    if ($oldversion < 2018080814) {

        // Define table tool_ally_log to be created.
        $table = new xmldb_table('tool_ally_log');

        // Adding fields to table tool_ally_log.
        $table->add_field('id',          XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('time',        XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('level',       XMLDB_TYPE_CHAR, '12', null, XMLDB_NOTNULL, null, null);
        $table->add_field('code',        XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('message',     XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('explanation', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('data',        XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('exception',   XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table tool_ally_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table tool_ally_log.
        $table->add_index('level', XMLDB_INDEX_NOTUNIQUE, array('level'));
        $table->add_index('code', XMLDB_INDEX_NOTUNIQUE, array('code'));

        // Conditionally launch create table for tool_ally_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2018080814, 'tool', 'ally');
    }

    if ($oldversion < 2018080815) {

        // Define table tool_ally_course_event to be created.
        $table = new xmldb_table('tool_ally_course_event');

        // Adding fields to table tool_ally_course_event.
        $table->add_field('id',        XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name',      XMLDB_TYPE_CHAR, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time',      XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_ally_course_event.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for tool_ally_course_event.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2018080815, 'tool', 'ally');
    }

    if ($oldversion < 2019061200) {

        $user = $DB->get_record('user', ['username' => 'ally_webuser']);
        // If the user exists we will update its capabilites.
        if ($user) {

            $contextid = \context_system::instance()->id;
            // The two new capabilites.
            $caps = [
                "moodle/category:viewhiddencategories",
                "tool/ally:viewlogs"
            ];
            // We assign those new capabilities.
            foreach ($caps as $cap) {
                // Only assign capabilities if they exist.
                // Most likely happens on upgrades only, not on fresh installations.
                if (get_capability_info($cap)) {
                    try {
                        assign_capability($cap, CAP_ALLOW, $user->id, $contextid);
                    } catch (moodle_exception $ex) {
                        // Upgrade or installation must successfully end.
                        // Just outputting the error.
                        mtrace('Could not assign capability to Ally user.');
                        mtrace($ex->getMessage());
                    }
                }
            }
        }
        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2019061200, 'tool', 'ally');
    }

    if ($oldversion < 2020061102) {
        // Table could be very large, so just clear it out.
        $DB->delete_records('tool_ally_log');

        // Define index time (not unique) to be added to tool_ally_log.
        $table = new xmldb_table('tool_ally_log');
        $index = new xmldb_index('time', XMLDB_INDEX_NOTUNIQUE, ['time']);

        // Conditionally launch add index time.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2020061102, 'tool', 'ally');
    }

    if ($oldversion < 2020061104) {

        // Define table tool_ally_file_in_use to be created.
        $table = new xmldb_table('tool_ally_file_in_use');

        // Adding fields to table tool_ally_file_in_use.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('fileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('inuse', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('needsupdate', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table tool_ally_file_in_use.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fileid', XMLDB_KEY_FOREIGN_UNIQUE, ['fileid'], 'files', ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);

        // Conditionally launch create table for tool_ally_file_in_use.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2020061104, 'tool', 'ally');
    }

    if ($oldversion < 2020061105) {

        // Define index needsupdate (not unique) to be added to tool_ally_file_in_use.
        $table = new xmldb_table('tool_ally_file_in_use');
        $index = new xmldb_index('needsupdate', XMLDB_INDEX_NOTUNIQUE, ['needsupdate']);

        // Conditionally launch add index needsupdate.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Ally savepoint reached.
        upgrade_plugin_savepoint(true, 2020061105, 'tool', 'ally');
    }

    return true;
}
