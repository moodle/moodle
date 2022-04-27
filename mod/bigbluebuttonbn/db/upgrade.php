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
 * Upgrade logic.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 */

use mod_bigbluebuttonbn\plugin;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\task\upgrade_recordings_task;

defined('MOODLE_INTERNAL') || die();

/**
 * Performs data migrations and updates on upgrade.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_bigbluebuttonbn_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2015080605) {
        // Drop field description.
        $table5 = new xmldb_table('bigbluebuttonbn');
        $field4 = new xmldb_field('description');
        if ($dbman->field_exists($table5, $field4)) {
            $dbman->drop_field($table5, $field4, true, true);
        }
        // Change welcome, allow null.
        $fielddefinition = ['type' => XMLDB_TYPE_TEXT, 'precision' => null, 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => null, 'previous' => 'type'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'welcome',
            $fielddefinition);
        // Change userid definition in bigbluebuttonbn_log.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '10', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => null,
            'previous' => 'bigbluebuttonbnid'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn_log', 'userid',
            $fielddefinition);
        // No settings to migrate.
        // Update db version tag.
        upgrade_mod_savepoint(true, 2015080605, 'bigbluebuttonbn');
    }
    if ($oldversion < 2016011305) {
        // Define field type to be droped from bigbluebuttonbn.
        $table4 = new xmldb_table('bigbluebuttonbn');
        $field3 = new xmldb_field('type');
        if ($dbman->field_exists($table4, $field3)) {
            $dbman->drop_field($table4, $field3, true, true);
        }
        // Rename table bigbluebuttonbn_log to bigbluebuttonbn_logs.
        $table = new xmldb_table('bigbluebuttonbn_log');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'bigbluebuttonbn_logs', true, true);
        }
        // Rename field event to log in table bigbluebuttonbn_logs.
        $table1 = new xmldb_table('bigbluebuttonbn_logs');
        $field = new xmldb_field('event');
        if ($dbman->field_exists($table1, $field)) {
            $dbman->rename_field($table1, $field, 'log', true, true);
        }
        // No settings to migrate.
        // Update db version tag.
        upgrade_mod_savepoint(true, 2016011305, 'bigbluebuttonbn');
    }
    if ($oldversion < 2017101000) {
        // Drop field newwindow.
        $table3 = new xmldb_table('bigbluebuttonbn');
        $field2 = new xmldb_field('newwindow');
        if ($dbman->field_exists($table3, $field2)) {
            $dbman->drop_field($table3, $field2, true, true);
        }
        // Add field type.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '2', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'id'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'type',
            $fielddefinition);
        // Add field recordings_html.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordings_html',
            $fielddefinition);
        // Add field recordings_deleted.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 1, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordings_deleted',
            $fielddefinition);
        // Add field recordings_imported.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordings_imported',
            $fielddefinition);
        // Drop field newwindow.
        $table2 = new xmldb_table('bigbluebuttonbn');
        $field1 = new xmldb_field('tagging');
        if ($dbman->field_exists($table2, $field1)) {
            $dbman->drop_field($table2, $field1, true, true);
        }
        // Migrate settings.
        unset_config('bigbluebuttonbn_recordingtagging_default', '');
        unset_config('bigbluebuttonbn_recordingtagging_editable', '');
        $cfgvalue = get_config('', 'bigbluebuttonbn_importrecordings_from_deleted_activities_enabled');
        set_config('bigbluebuttonbn_importrecordings_from_deleted_enabled', $cfgvalue, '');
        unset_config('bigbluebuttonbn_importrecordings_from_deleted_activities_enabled', '');
        $cfgvalue = get_config('', 'bigbluebuttonbn_moderator_default');
        set_config('bigbluebuttonbn_participant_moderator_default', $cfgvalue, '');
        unset_config('bigbluebuttonbn_moderator_default', '');
        // Update db version tag.
        upgrade_mod_savepoint(true, 2017101000, 'bigbluebuttonbn');
    }
    if ($oldversion < 2017101009) {
        // Add field recordings_preview.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordings_preview',
            $fielddefinition);
        // Update db version tag.
        upgrade_mod_savepoint(true, 2017101009, 'bigbluebuttonbn');
    }
    if ($oldversion < 2017101010) {
        // Fix for CONTRIB-7221.
        if ($oldversion == 2017101003) {
            // A bug intorduced in 2017101003 causes new instances to be created without BBB passwords.
            // A workaround was put in place in version 2017101004 that was relabeled to 2017101005.
            // However, as the code was relocated to upgrade.php in version 2017101010, a new issue came up.
            // There is now a timeout error when the plugin is upgraded in large Moodle sites.
            // The script should only be considered when migrating from this version.
            $sql = "SELECT * FROM {bigbluebuttonbn} ";
            $sql .= "WHERE moderatorpass = ? OR viewerpass = ?";
            $instances = $DB->get_records_sql($sql, ['', '']);
            foreach ($instances as $instance) {
                $instance->moderatorpass = plugin::random_password(12);
                $instance->viewerpass = plugin::random_password(12, $instance->moderatorpass);
                // Store passwords in the database.
                $DB->update_record('bigbluebuttonbn', $instance);
            }
        }
        // Update db version tag.
        upgrade_mod_savepoint(true, 2017101010, 'bigbluebuttonbn');
    }
    if ($oldversion < 2017101012) {
        // Update field type (Fix for CONTRIB-7302).
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '2', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'id'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'type',
            $fielddefinition);
        // Update field meetingid (Fix for CONTRIB-7302).
        $fielddefinition = ['type' => XMLDB_TYPE_CHAR, 'precision' => '255', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => null, 'previous' => 'introformat'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'meetingid',
            $fielddefinition);
        // Update field recordings_imported (Fix for CONTRIB-7302).
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordings_imported',
            $fielddefinition);
        // Add field recordings_preview.(Fix for CONTRIB-7302).
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordings_preview',
            $fielddefinition);
        // Update db version tag.
        upgrade_mod_savepoint(true, 2017101012, 'bigbluebuttonbn');
    }
    if ($oldversion < 2017101015) {
        // Add field for client technology choice.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'clienttype',
            $fielddefinition);
        // Update db version tag.
        upgrade_mod_savepoint(true, 2017101015, 'bigbluebuttonbn');
    }
    if ($oldversion < 2019042000) {
        // Add field for Mute on start feature.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'muteonstart',
            $fielddefinition);
        // Add field for record all from start.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordallfromstart',
            $fielddefinition);
        // Add field for record hide button.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'recordhidebutton',
            $fielddefinition);
        // Update db version tag.
        upgrade_mod_savepoint(true, 2019042000, 'bigbluebuttonbn');
    }
    if ($oldversion < 2019101001) {
        // Add field for Completion with attendance.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '9', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'completionattendance',
            $fielddefinition);
        // Add field for Completion with engagement through chats.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '9', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'completionengagementchats',
            $fielddefinition);
        // Add field for Completion with engagement through talks.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '9', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'completionengagementtalks',
            $fielddefinition);
        // Add field for Completion with engagement through raisehand.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '9', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'completionengagementraisehand',
            $fielddefinition);
        // Add field for Completion with engagement through pollvotes.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '9', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'completionengagementpollvotes',
            $fielddefinition);
        // Add field for Completion with engagement through emojis.
        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '9', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => null];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'completionengagementemojis',
            $fielddefinition);
        // Add index to bigbluebuttonbn_logs (Fix for CONTRIB-8157).
        xmldb_bigbluebuttonbn_index_table($dbman, 'bigbluebuttonbn_logs', 'courseid',
            ['courseid']);
        xmldb_bigbluebuttonbn_index_table($dbman, 'bigbluebuttonbn_logs', 'log',
            ['log']);
        xmldb_bigbluebuttonbn_index_table($dbman, 'bigbluebuttonbn_logs', 'logrow',
            ['courseid', 'bigbluebuttonbnid', 'userid', 'log']);
        // Update db version tag.
        upgrade_mod_savepoint(true, 2019101001, 'bigbluebuttonbn');
    }

    if ($oldversion < 2019101002) {

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'muteonstart'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'disablecam',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'disablecam'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'disablemic',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'disablemic'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'disableprivatechat',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'disableprivatechat'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'disablepublicchat',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'disablepublicchat'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'disablenote',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'disablenote'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'hideuserlist',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'hideuserlist'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'lockedlayout',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'lockedlayout'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'lockonjoin',
            $fielddefinition);

        $fielddefinition = ['type' => XMLDB_TYPE_INTEGER, 'precision' => '1', 'unsigned' => null,
            'notnull' => XMLDB_NOTNULL, 'sequence' => null, 'default' => 0, 'previous' => 'lockonjoin'];
        xmldb_bigbluebuttonbn_add_change_field($dbman, 'bigbluebuttonbn', 'lockonjoinconfigurable',
            $fielddefinition);

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2019101002, 'bigbluebuttonbn');
    }

    if ($oldversion < 2019101004) {
        // Add index to bigbluebuttonbn_logs (Leftover for CONTRIB-8157).
        xmldb_bigbluebuttonbn_index_table($dbman, 'bigbluebuttonbn_logs', 'userlog',
            ['userid', 'log']);
        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2019101004, 'bigbluebuttonbn');
    }

    if ($oldversion < 2021072905) {
        // Add table bigbluebuttonbn_recordings (CONTRIB-7994).
        // Define table bigbluebuttonbn_recordings to be created.
        $table = new xmldb_table('bigbluebuttonbn_recordings');

        // Adding fields to table bigbluebuttonbn_recordings.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('bigbluebuttonbnid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('recordingid', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('headless', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('imported', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('state', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('recording', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table bigbluebuttonbn_recordings.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_bigbluebuttonbnid', XMLDB_KEY_FOREIGN, ['bigbluebuttonbnid'], 'bigbluebuttonbn', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Adding indexes to table bigbluebuttonbn_recordings.
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        $table->add_index('recordingid', XMLDB_INDEX_NOTUNIQUE, ['recordingid']);

        // Conditionally launch create table for bigbluebuttonbn_recordings.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2021072905, 'bigbluebuttonbn');
    }
    if ($oldversion < 2021072906) {

        // Rename field recording on table bigbluebuttonbn_recordings to remotedata, add new remotedatatstamp and status.
        $table = new xmldb_table('bigbluebuttonbn_recordings');

        $field = new xmldb_field('recording', XMLDB_TYPE_TEXT, null, null, null, null, null, 'state');
        // Launch rename field recording to remotedata.
        $dbman->rename_field($table, $field, 'remotedata');

        $field = new xmldb_field('remotedatatstamp', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'remotedata');
        // Conditionally launch add field remotedatatstamp.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // State is already used on remote bigbluebutton entity and has not the same semantic.
        $field = new xmldb_field('state', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'imported');
        // Launch rename field state to status.
        $dbman->rename_field($table, $field, 'status');

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2021072906, 'bigbluebuttonbn');
    }

    if ($oldversion < 2021072907) {
        // Define field id to be dropped from bigbluebuttonbn_recordings.
        $table = new xmldb_table('bigbluebuttonbn_recordings');
        $remotedatatstamp = new xmldb_field('remotedatatstamp');
        $remotedata = new xmldb_field('remotedata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'status');
        // Conditionally launch drop field remotedatatstamp.
        if ($dbman->field_exists($table, $remotedatatstamp)) {
            $dbman->drop_field($table, $remotedatatstamp);
        }
        // Launch rename field importeddata.
        $dbman->rename_field($table, $remotedata, 'importeddata');

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2021072907, 'bigbluebuttonbn');
    }

    if ($oldversion < 2021083100) {
        // Update the legacy notifications to use the legacy class which will be removed as per the deprecation policy.
        $DB->set_field('task_adhoc', 'classname', '\mod_bigbluebuttonbn\task\send_legacy_notification', [
            'component' => 'mod_bigbluebuttonbn',
            'classname' => '\mod_bigbluebuttonbn\task\send_notification',
        ]);

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2021083100, 'bigbluebuttonbn');
    }

    if ($oldversion < 2021091408) {
        // Change BigBliueButton Server credentials to new defaults if test-install is being used.
        if (config::get('server_url') == 'http://test-install.blindsidenetworks.com/bigbluebutton/') {
            set_config('bigbluebuttonbn_server_url', config::DEFAULT_SERVER_URL);
            set_config('bigbluebuttonbn_shared_secret', config::DEFAULT_SHARED_SECRET);
        }
        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2021091408, 'bigbluebuttonbn');
    }

    if ($oldversion < 2022021601) {
        // Create adhoc task for upgrading of existing bigbluebuttonbn_logs related to recordings.
        upgrade_recordings_task::schedule_upgrade_per_meeting();
        upgrade_recordings_task::schedule_upgrade_per_meeting(true);
        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2022021601, 'bigbluebuttonbn');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

/**
 * Generic helper function for adding or changing a field in a table.
 *
 * @param database_manager $dbman
 * @param string $tablename
 * @param string $fieldname
 * @param array $fielddefinition
 * @deprecated  please do not use this anymore (historical migrations)
 */
function xmldb_bigbluebuttonbn_add_change_field(database_manager $dbman, string $tablename, string $fieldname,
    array $fielddefinition) {
    $table = new xmldb_table($tablename);
    $field = new xmldb_field($fieldname);
    $field->set_attributes($fielddefinition['type'], $fielddefinition['precision'], $fielddefinition['unsigned'],
        $fielddefinition['notnull'], $fielddefinition['sequence'], $fielddefinition['default'],
        $fielddefinition['previous']);
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field, true, true);
        return;
    }
    // Drop key before if needed.
    $fieldkey = new xmldb_key($fieldname, XMLDB_KEY_FOREIGN, [$fieldname], 'user', ['id']);
    if ($dbman->find_key_name($table, $fieldkey)) {
        $dbman->drop_key($table, $fieldkey);
    }
    $dbman->change_field_type($table, $field, true, true);
    $dbman->change_field_precision($table, $field, true, true);
    $dbman->change_field_notnull($table, $field, true, true);
    $dbman->change_field_default($table, $field, true, true);
}

/**
 * Generic helper function for adding index to a table.
 *
 * @param database_manager $dbman
 * @param string $tablename
 * @param string $indexname
 * @param array $indexfields
 * @param string|false|null $indextype
 * @deprecated please do not use this anymore (historical migrations)
 */
function xmldb_bigbluebuttonbn_index_table(database_manager $dbman, string $tablename, string $indexname, array $indexfields,
    $indextype = XMLDB_INDEX_NOTUNIQUE) {
    $table = new xmldb_table($tablename);
    if (!$dbman->table_exists($table)) {
        return;
    }
    $index = new xmldb_index($indexname, $indextype, $indexfields);
    if ($dbman->index_exists($table, $index)) {
        $dbman->drop_index($table, $index);
    }
    $dbman->add_index($table, $index, true, true);
}
