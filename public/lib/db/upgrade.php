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
 * This file keeps track of upgrades to Moodle.
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package   core_install
 * @category  upgrade
 * @copyright 2006 onwards Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Main upgrade tasks to be executed on Moodle version bump
 *
 * This function is automatically executed after one bump in the Moodle core
 * version is detected. It's in charge of performing the required tasks
 * to raise core from the previous version to the next one.
 *
 * It's a collection of ordered blocks of code, named "upgrade steps",
 * each one performing one isolated (from the rest of steps) task. Usually
 * tasks involve creating new DB objects or performing manipulation of the
 * information for cleanup/fixup purposes.
 *
 * Each upgrade step has a fixed structure, that can be summarised as follows:
 *
 * if ($oldversion < XXXXXXXXXX.XX) {
 *     // Explanation of the update step, linking to issue in the Tracker if necessary
 *     upgrade_set_timeout(XX); // Optional for big tasks
 *     // Code to execute goes here, usually the XMLDB Editor will
 *     // help you here. See {@link https://moodledev.io/general/development/tools/xmldb}.
 *     upgrade_main_savepoint(true, XXXXXXXXXX.XX);
 * }
 *
 * All plugins within Moodle (modules, blocks, reports...) support the existence of
 * their own upgrade.php file, using the "Frankenstyle" component name as
 * defined at {@link https://moodledev.io/general/development/policies/codingstyle/frankenstyle}, for example:
 *     - {@see xmldb_page_upgrade($oldversion)}. (modules don't require the plugintype ("mod_") to be used.
 *     - {@see xmldb_auth_manual_upgrade($oldversion)}.
 *     - {@see xmldb_workshopform_accumulative_upgrade($oldversion)}.
 *     - ....
 *
 * In order to keep the contents of this file reduced, it's allowed to create some helper
 * functions to be used here in the {@see upgradelib.php} file at the same directory. Note
 * that such a file must be manually included from upgrade.php, and there are some restrictions
 * about what can be used within it.
 *
 * For more information, take a look to the documentation available:
 *     - Data definition API: {@link https://moodledev.io/docs/apis/core/dml/ddl}
 *     - Upgrade API: {@link https://moodledev.io/docs/guides/upgrade}
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_main_upgrade($oldversion) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/db/upgradelib.php'); // Core Upgrade-related functions.

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Always keep this upgrade step with version being the minimum
    // allowed version to upgrade from (v4.1.2 right now).
    if ($oldversion < 2022112802) {
        // Just in case somebody hacks upgrade scripts or env, we really can not continue.
        echo("You need to upgrade to 4.1.2 or higher first!\n");
        exit(1);
        // Note this savepoint is 100% unreachable, but needed to pass the upgrade checks.
        upgrade_main_savepoint(true, 2022112802);
    }

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2024070500.01) {
        // Remove the site_contactable config of the hub plugin from config plugin table.
        unset_config('site_contactable', 'hub');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024070500.01);
    }

    if ($oldversion < 2024071900.01) {
        // Define table stored_progress to be created.
        $table = new xmldb_table('stored_progress');

        // Adding fields to table stored_progress.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timestart', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('lastupdate', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('percentcompleted', XMLDB_TYPE_NUMBER, '5, 2', null, null, null, '0');
        $table->add_field('message', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('haserrored', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table stored_progress.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table stored_progress.
        $table->add_index('uid_index', XMLDB_INDEX_NOTUNIQUE, ['idnumber']);

        // Conditionally launch create table for stored_progress.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024071900.01);
    }

    if ($oldversion < 2024072600.01) {
        // If tool_innodb is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/admin/tool/innodb/version.php')) {
            // Delete tool_innodb.
            uninstall_plugin('tool', 'innodb');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024072600.01);
    }

    if ($oldversion < 2024080500.00) {

        // Fix missing default admin presets "sensible settings" (those that should be treated as sensitive).
        $newsensiblesettings = [
            'bigbluebuttonbn_shared_secret@@none',
            'apikey@@tiny_premium',
            'matrixaccesstoken@@communication_matrix',
            'api_secret@@factor_sms',
        ];

        $sensiblesettings = get_config('adminpresets', 'sensiblesettings');
        foreach ($newsensiblesettings as $newsensiblesetting) {
            if (strpos($sensiblesettings, $newsensiblesetting) === false) {
                $sensiblesettings .= ", {$newsensiblesetting}";
            }
        }

        set_config('sensiblesettings', $sensiblesettings, 'adminpresets');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024080500.00);
    }

    if ($oldversion < 2024082900.01) {
        // If filter_tidy is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/filter/tidy/version.php')) {
            // Clean config.
            uninstall_plugin('filter', 'tidy');
        }

        upgrade_main_savepoint(true, 2024082900.01);
    }

    if ($oldversion < 2024091000.01) {
        // Define table ai_policy_register to be created.
        $table = new xmldb_table('ai_policy_register');

        // Adding fields to table ai_policy_register.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timeaccepted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table ai_policy_register.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN_UNIQUE, ['userid'], 'user', ['id']);

        // Conditionally launch create table for ai_policy_register.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table ai_action_generate_image to be created.
        $table = new xmldb_table('ai_action_generate_image');

        // Adding fields to table ai_action_generate_image.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('prompt', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('numberimages', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('quality', XMLDB_TYPE_CHAR, '21', null, XMLDB_NOTNULL, null, null);
        $table->add_field('aspectratio', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('style', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('sourceurl', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('revisedprompt', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table ai_action_generate_image.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for ai_action_generate_image.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table ai_action_register to be created.
        $table = new xmldb_table('ai_action_register');

        // Adding fields to table ai_action_register.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('actionname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('actionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('success', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('provider', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('errorcode', XMLDB_TYPE_INTEGER, '4', null, null, null, null);
        $table->add_field('errormessage', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table ai_action_register.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Adding indexes to table ai_action_register.
        $table->add_index('action', XMLDB_INDEX_UNIQUE, ['actionname', 'actionid']);
        $table->add_index('provider', XMLDB_INDEX_NOTUNIQUE, ['actionname', 'provider']);

        // Conditionally launch create table for ai_action_register.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table ai_action_generate_text to be created.
        $table = new xmldb_table('ai_action_generate_text');

        // Adding fields to table ai_action_generate_text.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('prompt', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('responseid', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('fingerprint', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('generatedcontent', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('finishreason', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('prompttokens', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('completiontoken', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table ai_action_generate_text.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for ai_action_generate_text.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table ai_action_summarise_text to be created.
        $table = new xmldb_table('ai_action_summarise_text');

        // Adding fields to table ai_action_summarise_text.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('prompt', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('responseid', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('fingerprint', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('generatedcontent', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('finishreason', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('prompttokens', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('completiontoken', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table ai_action_summarise_text.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for ai_action_summarise_text.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024091000.01);
    }

    if ($oldversion < 2024091700.01) {
        // Convert the ai_action_register.success column to an integer, if necessary.
        upgrade_change_binary_column_to_int('ai_action_register', 'success', XMLDB_NOTNULL, 'actionid');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024091700.01);
    }

    if ($oldversion < 2024092000.01) {

        // Define table sms_messages to be created.
        $table = new xmldb_table('sms_messages');

        // Adding fields to table sms_messages.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('recipientnumber', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('messagetype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('recipientuserid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('issensitive', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('gatewayid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table sms_messages.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('gateway', XMLDB_KEY_FOREIGN, ['gatewayid'], 'sms_gateways', ['id']);

        // Conditionally launch create table for sms_messages.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table sms_gateways to be created.
        $table = new xmldb_table('sms_gateways');

        // Adding fields to table sms_gateways.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('config', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table sms_gateways.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for sms_gateways.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024092000.01);
    }

    if ($oldversion < 2024092600.00) {
        // If h5plib_v126 is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/h5p/h5plib/v126/version.php')) {
            // Clean config.
            uninstall_plugin('h5plib', 'v126');
        }

        // If h5plib_v127 is present, set it as the default one.
        if (file_exists($CFG->dirroot . '/h5p/h5plib/v127/version.php')) {
            set_config('h5plibraryhandler', 'h5plib_v127');
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024092600.00);
    }

    if ($oldversion < 2024100100.02) {
        upgrade_store_relative_url_sitehomepage();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024100100.02);
    }

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2024110400.00) {

        // Define field model to be added to ai_action_register.
        $table = new xmldb_table('ai_action_register');
        $field = new xmldb_field('model', XMLDB_TYPE_CHAR, '50', null, null, null, null, null);

        // Conditionally launch add field model.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024110400.00);
    }
    if ($oldversion < 2024110800.00) {
        // Delete settings that were removed from code.
        $settings = ['backup_general_questionbank', 'backup_import_questionbank', 'backup_auto_questionbank'];
        array_walk($settings, static fn($setting) => unset_config($setting, 'backup'));

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024110800.00);
    }

    if ($oldversion < 2024110800.02) {
        // Changing type of field value on table user_preferences to text.
        $table = new xmldb_table('user_preferences');
        $field = new xmldb_field('value', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'name');

        // Launch change of type for field value.
        $dbman->change_field_type($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024110800.02);
    }

    if ($oldversion < 2024111500.00) {

        // Changing precision of field fullname on table course to (1333).
        $table = new xmldb_table('course');
        $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'sortorder');

        // Launch change of precision for field fullname.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024111500.00);
    }

    if ($oldversion < 2024111500.01) {

        // Changing precision of field fullname on table course_request to (1333).
        $table = new xmldb_table('course_request');
        $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of precision for field fullname.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024111500.01);
    }

    // Now we want to change the precision of course_request.shortname.
    // To do this, we need to first drop the index, then re-create it.
    if ($oldversion < 2024111500.02) {

        // Define index shortname (not unique) to be dropped form course_request.
        $table = new xmldb_table('course_request');
        $index = new xmldb_index('shortname', XMLDB_INDEX_NOTUNIQUE, ['shortname']);

        // Conditionally launch drop index shortname.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024111500.02);
    }

    if ($oldversion < 2024111500.03) {

        // Changing precision of field shortname on table course_request to (255).
        $table = new xmldb_table('course_request');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'fullname');

        // Launch change of precision for field shortname.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024111500.03);
    }

    if ($oldversion < 2024111500.04) {

        // Define index shortname (not unique) to be added to course_request.
        $table = new xmldb_table('course_request');
        $index = new xmldb_index('shortname', XMLDB_INDEX_NOTUNIQUE, ['shortname']);

        // Conditionally launch add index shortname.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024111500.04);
    }

    if ($oldversion < 2024112900.01) {

        // Define table reportbuilder_user_filter to be created.
        $table = new xmldb_table('reportbuilder_user_filter');

        // Adding fields to table reportbuilder_user_filter.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('reportid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('filterdata', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table reportbuilder_user_filter.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('reportid', XMLDB_KEY_FOREIGN, ['reportid'], 'reportbuilder_report', ['id']);
        $table->add_key('usercreated', XMLDB_KEY_FOREIGN, ['usercreated'], 'user', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Adding indexes to table reportbuilder_user_filter.
        $table->add_index('report-user', XMLDB_INDEX_UNIQUE, ['reportid', 'usercreated']);

        // Conditionally launch create table for reportbuilder_user_filter.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024112900.01);
    }

    if ($oldversion < 2024112900.02) {

        // Structure to collect current user filter preferences.
        $userfilterdata = [];

        $select = $DB->sql_like('name', '?');
        $params = [$DB->sql_like_escape('reportbuilder-report-') . '%'];

        $preferences = $DB->get_records_select('user_preferences', $select, $params, 'userid, name');
        foreach ($preferences as $preference) {
            preg_match('/^reportbuilder-report-(?<reportid>\d+)-/', $preference->name, $matches);
            $userfilterdata[$preference->userid][$matches['reportid']][] = $preference->value;
        }

        // Migrate user filter preferences to new schema (combining previously chunked values due to size limitation).
        foreach ($userfilterdata as $userid => $reportfilterdata) {
            foreach ($reportfilterdata as $reportid => $filterdata) {
                $DB->insert_record('reportbuilder_user_filter', (object) [
                    'reportid' => $reportid,
                    'filterdata' => implode('', $filterdata),
                    'usercreated' => $userid,
                    'usermodified' => $userid,
                    'timecreated' => time(),
                    'timemodified' => time(),
                ]);
            }
        }

        // Clean up old user filter preferences.
        $DB->delete_records_select('user_preferences', $select, $params);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024112900.02);
    }

    // Moodle 5.0 Upgrade.
    if ($oldversion < 2024121800.00) {
        $smsgateways = $DB->get_records('sms_gateways');
        foreach ($smsgateways as $gateway) {
            $newconfig = json_decode($gateway->config);
            // Continue only if either the `returnurl` OR the `saveandreturn` property exists.
            if (property_exists($newconfig, "returnurl") || property_exists($newconfig, "saveandreturn")) {
                // Remove unnecessary data in the config.
                unset($newconfig->returnurl, $newconfig->saveandreturn);

                // Update the record with the new config.
                $gateway->config = json_encode($newconfig);
                $DB->update_record('sms_gateways', $gateway);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024121800.00);
    }

    if ($oldversion < 2024121900.01) {
        // Enable mod_subsection unless 'keepsubsectiondisabled' is set.
        if ((empty($CFG->keepsubsectiondisabled) || !$CFG->keepsubsectiondisabled)
                && $DB->get_record('modules', ['name' => 'subsection'])) {
            $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
            $manager::enable_plugin('subsection', 1);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024121900.01);
    }

    if ($oldversion < 2025011700.02) {
        // Define table ai_providers to be created.
        $table = new xmldb_table('ai_providers');

        // Adding fields to table ai_providers.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('provider', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('config', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('actionconfig', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table ai_provider.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table ai_provider.
        $table->add_index('provider', XMLDB_INDEX_NOTUNIQUE, ['provider']);

        // Conditionally launch create table for ai_provider.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Now the instance table exists, migrate the existing providers.
        upgrade_convert_ai_providers_to_instances();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025011700.02);
    }

    if ($oldversion < 2025012400.01) {
        // Remove the default value for the apiversion field.
        $table = new xmldb_table('badge_external_backpack');
        $apiversionfield = new xmldb_field('apiversion', XMLDB_TYPE_CHAR, '12', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_default($table, $apiversionfield);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025012400.01);
    }

    if ($oldversion < 2025013100.01) {
        // Remove imageauthorname, imageauthoremail and imageauthorurl fields for badges.
        $table = new xmldb_table('badge');
        $fields = [
            'imageauthorname',
            'imageauthoremail',
            'imageauthorurl',
        ];

        foreach ($fields as $field) {
            $field = new xmldb_field($field);
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025013100.01);
    }

    if ($oldversion < 2025022100.01) {
        // Define table ai_action_explain_text to be created.
        $table = new xmldb_table('ai_action_explain_text');

        // Adding fields to table ai_action_explain_text.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('prompt', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('responseid', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('fingerprint', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('generatedcontent', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('finishreason', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('prompttokens', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('completiontoken', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table ai_action_explain_text.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for ai_action_explain_text.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add explain action config to the AI providers.
        upgrade_add_explain_action_to_ai_providers();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025022100.01);
    }

    if ($oldversion < 2025022100.02) {
        // Due to a code restriction on the upgrade, invoking any core functions is not permitted.
        // Thus, to acquire the list of provider plugins,
        // we should extract them from the `config_plugins` database table.
        $condition = $DB->sql_like('plugin', ':pattern');
        $params = ['pattern' => 'aiprovider_%', 'name' => 'version'];
        $sql = "SELECT plugin FROM {config_plugins} WHERE {$condition} AND name = :name";
        $providers = $DB->get_fieldset_sql($sql, $params);
        foreach ($providers as $provider) {
            // Replace the provider's language string with the provider component's name.
            if (get_string_manager()->string_exists('pluginname', $provider)) {
                $providername = get_string('pluginname', $provider);
                $sql = 'UPDATE {ai_action_register}
                        SET provider = :provider
                        WHERE LOWER(provider) = :providername';
                $DB->execute($sql, ['provider' => $provider, 'providername' => strtolower($providername)]);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025022100.02);
    }

    // Uninstall auth_cas and remove dependencies.
    if ($oldversion < 2025030500.00) {
        if (!file_exists($CFG->dirroot . "/auth/cas/version.php")) {
            uninstall_plugin('auth', 'cas');

            // Remove the sensiblesettings config for auth_cas.
            $sensiblesettingsraw = explode(',', get_config('adminpresets', 'sensiblesettings'));
            $sensiblesettings = array_map('trim', $sensiblesettingsraw);

            if (($key = array_search('bind_pw@@auth_cas', $sensiblesettings)) !== false) {
                unset($sensiblesettings[$key]);
            }
            $sensiblesettings = implode(', ', $sensiblesettings);
            set_config('sensiblesettings', $sensiblesettings, 'adminpresets');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030500.00);
    }

    if ($oldversion < 2025030500.01) {
        // If atto is no longer present, remove it.
        if (!file_exists("{$CFG->dirroot}/lib/editor/atto/version.php")) {
            // Remove each of the subplugins first. These are no longer on disk so the standard `uninstall_plugin` approach
            // on atto itself will not remove them.
            $plugins = array_keys(core_plugin_manager::instance()->get_plugins_of_type('atto'));

            // Now remove each.
            foreach ($plugins as $pluginname) {
                uninstall_plugin('atto', $pluginname);
            }

            // Finally uninstall the actual plugin.
            uninstall_plugin('editor', 'atto');
        }

        upgrade_main_savepoint(true, 2025030500.01);
    }

    // Remove portfolio_mahara.
    if ($oldversion < 2025030600.02) {
        if (!file_exists($CFG->dirroot . "/portfolio/mahara/version.php")) {
            uninstall_plugin('portfolio', 'mahara');
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030600.02);
    }

    // Remove enrol_mnet.
    if ($oldversion < 2025030600.03) {
        if (!file_exists($CFG->dirroot . "/enrol/mnet/version.php")) {
            uninstall_plugin('enrol', 'mnet');
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030600.03);
    }

    // Remove block_mnet_hosts.
    if ($oldversion < 2025030600.04) {
        if (!file_exists($CFG->dirroot . "/blocks/mnet_hosts/version.php")) {
            uninstall_plugin('block', 'mnet_hosts');

            // Delete all the admin preset plugin states concerning mnet_hosts in adminpresets_plug table.
            $DB->delete_records('adminpresets_plug', ['name' => 'mnet_hosts']);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030600.04);
    }

    // Remove auth_mnet.
    if ($oldversion < 2025030600.05) {
        if (!file_exists($CFG->dirroot . "/auth/mnet/version.php")) {
            uninstall_plugin('auth', 'mnet');
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030600.05);
    }

    if ($oldversion < 2025030600.06) {
        // If mlbackend_php is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/lib/mlbackend/php/version.php')) {
            // Clean config.
            uninstall_plugin('mlbackend', 'php');

            // Change the processor if mlbackend_php is set.
            if (get_config('analytics', 'predictionsprocessor') === '\mlbackend_php\processor') {
                set_config('predictionsprocessor', '\mlbackend_python\processor', 'analytics');
                // We can't be sure mlbackend_python is set up correctly, so we disable analytics.
                set_config('enableanalytics', false);
            }

            // Cleanup any references to mlbackend_php.
            $select = $DB->sql_like('predictionsprocessor', ':predictionsprocessor', false);
            $params = ['predictionsprocessor' => '%' . $DB->sql_like_escape('mlbackend_php') . '%'];
            $DB->set_field_select(
                table: 'analytics_models',
                newfield: 'predictionsprocessor',
                newvalue: null,
                select: $select,
                params: $params,
            );
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030600.06);
    }

    if ($oldversion < 2025030600.07) {
        $providers = $DB->get_records('ai_providers', ['enabled' => 1]);
        // Formatting the value.
        $value = ','. implode(',', array_column($providers, 'id'));
        // Create the order config setting.
        set_config('provider_order', $value, 'core_ai');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030600.07);
    }

    // Remove mnetservice_enrol.
    if ($oldversion < 2025030600.08) {
        if (!file_exists($CFG->dirroot . "/mnet/service/enrol/version.php")) {
            uninstall_plugin('mnetservice', 'enrol');
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025030600.08);
    }

    if ($oldversion < 2025031800.00) {
        // Add index for querying delegated sections.
        $table = new xmldb_table('course_sections');
        $index = new xmldb_index('component_itemid', XMLDB_INDEX_NOTUNIQUE, ['component', 'itemid']);

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025031800.00);
    }

    if ($oldversion < 2025031800.03) {

        // Define field penalty to be added to grade_grades.
        $table = new xmldb_table('grade_grades');
        $field = new xmldb_field('deductedmark', XMLDB_TYPE_NUMBER, '10, 5', null,
            XMLDB_NOTNULL, null, '0', 'aggregationweight');

        // Conditionally launch add field penalty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025031800.03);
    }

    if ($oldversion < 2025031800.04) {

        // Define field overriddenmark to be added to grade_grades.
        $table = new xmldb_table('grade_grades');
        $field = new xmldb_field('overriddenmark', XMLDB_TYPE_NUMBER, '10, 5', null,
            XMLDB_NOTNULL, null, '0', 'deductedmark');

        // Conditionally launch add field penalty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025031800.04);
    }

    if ($oldversion < 2025032800.01) {
        // Upgrade webp mime type for existing webp files.
        upgrade_create_async_mimetype_upgrade_task('image/webp', ['webp']);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025032800.01);
    }

    // Remove chat and survey and respective analytics indicators.
    if ($oldversion < 2025040100.01) {
        $indicatorstoremove = [];
        $sqllikes = [];
        $sqlparams = [];

        if (!file_exists($CFG->dirroot . "/mod/survey/version.php")) {
            uninstall_plugin('mod', 'survey');
            $DB->delete_records('adminpresets_plug', ['plugin' => 'mod', 'name' => 'survey']);
            $indicatorstoremove['survey'] = [
                '\mod_survey\analytics\indicator\cognitive_depth',
                '\mod_survey\analytics\indicator\social_breadth',
            ];
            $sqlparams['surveypluginname'] = '%' . $DB->sql_like_escape('mod_survey') . '%';
            $sqllikes['survey'] = $DB->sql_like('indicators', ':surveypluginname');
        }
        if (!file_exists($CFG->dirroot . "/mod/chat/version.php")) {
            uninstall_plugin('mod', 'chat');
            $DB->delete_records('adminpresets_plug', ['plugin' => 'mod', 'name' => 'chat']);
            $indicatorstoremove['chat'] = [
                '\mod_chat\analytics\indicator\cognitive_depth',
                '\mod_chat\analytics\indicator\social_breadth',
            ];
            $sqlparams['chatpluginname'] = '%' . $DB->sql_like_escape('mod_chat') . '%';
            $sqllikes['chat'] = $DB->sql_like('indicators', ':chatpluginname');
        }

        foreach ($indicatorstoremove as $module => $indicators) {
            $models = $DB->get_recordset_select('analytics_models', $sqllikes[$module], $sqlparams);
            foreach ($models as $model) {
                $currentindicators = json_decode($model->indicators, true);
                if (!empty($indicators) && !empty($currentindicators)) {
                    $newindicators = array_values(array_diff($currentindicators, $indicators));
                    $model->indicators = json_encode($newindicators);
                    $DB->update_record('analytics_models', $model);
                }
            }
            $models->close();
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025040100.01);
    }

    // Remove overriddenmark field from grade_grades.
    if ($oldversion < 2025040700.00) {
        $table = new xmldb_table('grade_grades');
        $field = new xmldb_field('overriddenmark');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025040700.00);
    }

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025051600.02) {

        // Changing precision of field name on table badge to (1333).

        $table = new xmldb_table('badge');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field issuername on table badge to (1333).

        $table = new xmldb_table('badge');
        $field = new xmldb_field('issuername', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'usermodified');

        // Launch change of precision for field issuername.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field name on table course_sections to (1333).

        $table = new xmldb_table('course_sections');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'section');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field itemname on table grade_items to (1333).

        $table = new xmldb_table('grade_items');
        $field = new xmldb_field('itemname', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'categoryid');

        // Launch change of precision for field itemname.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field itemname on table grade_items_history to (1333).
        $table = new xmldb_table('grade_items_history');
        $field = new xmldb_field('itemname', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'categoryid');

        // Launch change of precision for field itemname.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025051600.02);
    }

    if ($oldversion < 2025053000.02) {

        // Define field systememail to be added to oauth2_issuer.
        $table = new xmldb_table('oauth2_issuer');
        $field = new xmldb_field('systememail', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'loginpagename');

        // Conditionally launch add field systememail.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025053000.02);
    }

    if ($oldversion < 2025062900.01) {
        // The activity chooser tab mode setting is no longer used.
        unset_config('activitychoosertabmode');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025062900.01);
    }

    if ($oldversion < 2025070600.01) {

        // Get all AI providers that potentially have model settings.
        $sql = "SELECT * FROM {ai_providers} WHERE provider IN (?, ?)";

        $params = [
            'aiprovider_openai\provider',
            'aiprovider_ollama\provider',
        ];
        $records = $DB->get_records_sql($sql, $params);

        $affectedkeys = [
            'gpt-4o' => [
                'top_p',
                'max_tokens',
                'frequency_penalty',
                'presence_penalty',
            ],
            'o1' => [
                'top_p',
                'max_tokens',
                'frequency_penalty',
                'presence_penalty',
            ],
            'llama3.3' => [
                'mirostat',
                'temperature',
                'seed',
                'top_k',
                'top_p',
            ],
        ];

        // Get covert model settings to new format.
        foreach ($records as $record) {
            $actionconfig = json_decode($record->actionconfig, true, 512);
            foreach($actionconfig as $actionkey => $action) {
                $model = $action['settings']['model'];
                $modelsettings = [];
                // Handle custom params.
                if (isset($action['settings']['modelextraparams'])) {
                    $modelsettings['custom']['modelextraparams'] = $action['settings']['modelextraparams'] ?? '';
                // Handle known models and their keys.
                } else if (isset($affectedkeys[$model])) {
                    foreach ($affectedkeys[$model] as $key) {
                        $modelsettings[$model][$key] = $action['settings'][$key] ?? '';
                    }
                }
                if (!empty($modelsettings)) {
                    $actionconfig[$actionkey]['modelsettings'] = $modelsettings;
                }
            };
            // Update the record with modified actionconfig.
            if (isset($actionconfig[$actionkey]['modelsettings'])) {
                $updatedrecord = new stdClass();
                $updatedrecord->id = $record->id;
                $updatedrecord->actionconfig = json_encode($actionconfig);
                $DB->update_record('ai_providers', $updatedrecord);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025070600.01);
    }

    if ($oldversion < 2025072500.01) {
        // Get all OpenAI providers.
        $records = $DB->get_records('ai_providers', ['provider' => 'aiprovider_openai\provider']);

        foreach ($records as $record) {
            $actionconfig = json_decode($record->actionconfig, true, 512);
            $originalactionconfig = $actionconfig;

            foreach ($actionconfig as $actionkey => $action) {
                $model = $action['settings']['model'];
                if ($model === 'gpt-4o' || $model === 'o1') {
                    // Rename setting max_tokens to max_completion_tokens.
                    if (isset($action['settings']['max_tokens'])) {
                        $actionconfig[$actionkey]['settings']['max_completion_tokens'] = intval($action['settings']['max_tokens']);
                        unset($actionconfig[$actionkey]['settings']['max_tokens']);
                    }
                    // Rename max_tokens in model settings too (casting not necessary).
                    if (isset($action['modelsettings'][$model]['max_tokens'])) {
                        $actionconfig[$actionkey]['modelsettings'][$model]['max_completion_tokens'] =
                            $action['modelsettings'][$model]['max_tokens'];
                        unset($actionconfig[$actionkey]['modelsettings'][$model]['max_tokens']);
                    }
                }
                // Cast settings for 'gpt-4o' model.
                if ($model === 'gpt-4o') {
                    if (isset($action['settings']['top_p'])) {
                        $actionconfig[$actionkey]['settings']['top_p'] = floatval($action['settings']['top_p']);
                    }
                    if (isset($action['settings']['presence_penalty'])) {
                        $actionconfig[$actionkey]['settings']['presence_penalty'] =
                            floatval($action['settings']['presence_penalty']);
                    }
                    if (isset($action['settings']['frequency_penalty'])) {
                        $actionconfig[$actionkey]['settings']['frequency_penalty'] =
                            floatval($action['settings']['frequency_penalty']);
                    }
                }
                // Remove settings from 'o1' model.
                if ($model === 'o1') {
                    if (isset($action['settings']['top_p'])) {
                        unset($actionconfig[$actionkey]['settings']['top_p']);
                    }
                    if (isset($action['settings']['presence_penalty'])) {
                        unset($actionconfig[$actionkey]['settings']['presence_penalty']);
                    }
                    if (isset($action['settings']['frequency_penalty'])) {
                        unset($actionconfig[$actionkey]['settings']['frequency_penalty']);
                    }
                    // Remove from model settings too.
                    if (isset($action['modelsettings'][$model]['top_p'])) {
                        unset($actionconfig[$actionkey]['modelsettings'][$model]['top_p']);
                    }
                    if (isset($action['modelsettings'][$model]['presence_penalty'])) {
                        unset($actionconfig[$actionkey]['modelsettings'][$model]['presence_penalty']);
                    }
                    if (isset($action['modelsettings'][$model]['frequency_penalty'])) {
                        unset($actionconfig[$actionkey]['modelsettings'][$model]['frequency_penalty']);
                    }
                }
            }

            if ($originalactionconfig !== $actionconfig) {
                $updatedrecord = new stdClass();
                $updatedrecord->id = $record->id;
                $updatedrecord->actionconfig = json_encode($actionconfig);
                $DB->update_record('ai_providers', $updatedrecord);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025072500.01);
    }

    if ($oldversion < 2025073100.01) {
        // A [name => url] map of new OIDC endpoints to be updated/created.
        $endpointuris = [
            'discovery_endpoint' => 'https://login.microsoftonline.com/common/v2.0/.well-known/openid-configuration',
            'token_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'userinfo_endpoint' => 'https://graph.microsoft.com/oidc/userinfo',
            'authorization_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'device_authorization_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/devicecode',
            'end_session_endpoint' => 'https://login.microsoftonline.com/common/oauth2/v2.0/logout',
            'kerberos_endpoint' => 'https://login.microsoftonline.com/common/kerberos',
        ];
        // A [name] map of endpoints to be deleted.
        $deletedendpointuris = [
            'userpicture_endpoint',
        ];
        // A [internalfield => externalfield] map of new OIDC-based user field mappings to be updated/created.
        $userfieldmappings = [
            'idnumber' => 'sub',
            'firstname' => 'givenname',
            'lastname' => 'familyname',
            'email' => 'email',
            'lang' => 'locale',
        ];
        $admin = get_admin();
        $adminid = $admin ? $admin->id : '0';
        $microsoftservices = $DB->get_records('oauth2_issuer', ['servicetype' => 'microsoft']);
        foreach ($microsoftservices as $microsoftservice) {
            $time = time();
            if (strpos($microsoftservice->baseurl, 'common') !== false) {
                // Multi-tenant endpoint, proceed with upgrade.
                // Insert/update the new endpoints.
                foreach ($endpointuris as $endpointname => $endpointuri) {
                    $endpoint = ['issuerid' => $microsoftservice->id, 'name' => $endpointname];
                    $endpointid = $DB->get_field('oauth2_endpoint', 'id', $endpoint);
                    if ($endpointid) {
                        $endpoint = array_merge($endpoint, [
                            'id' => $endpointid,
                            'url' => $endpointuri,
                            'timemodified' => $time,
                            'usermodified' => $adminid,
                        ]);
                        $DB->update_record('oauth2_endpoint', $endpoint);
                    } else {
                        $endpoint = array_merge($endpoint, [
                            'url' => $endpointuri,
                            'timecreated' => $time,
                            'timemodified' => $time,
                            'usermodified' => $adminid,
                        ]);
                        $DB->insert_record('oauth2_endpoint', $endpoint);
                    }
                }
                // Delete the old endpoints.
                foreach ($deletedendpointuris as $endpointname) {
                    $endpoint = ['issuerid' => $microsoftservice->id, 'name' => $endpointname];
                    $DB->delete_records('oauth2_endpoint', $endpoint);
                }
                // Insert/update new user field mappings.
                foreach ($userfieldmappings as $internalfieldname => $externalfieldname) {
                    $fieldmap = ['issuerid' => $microsoftservice->id, 'internalfield' => $internalfieldname];
                    $fieldmapid = $DB->get_field('oauth2_user_field_mapping', 'id', $fieldmap);
                    if ($fieldmapid) {
                        $fieldmap = array_merge($fieldmap, [
                            'id' => $fieldmapid,
                            'externalfield' => $externalfieldname,
                            'timemodified' => $time,
                            'usermodified' => $adminid,
                        ]);
                        $DB->update_record('oauth2_user_field_mapping', $fieldmap);
                    } else {
                        $fieldmap = array_merge($fieldmap, [
                            'externalfield' => $externalfieldname,
                            'timecreated' => $time,
                            'timemodified' => $time,
                            'usermodified' => $adminid,
                        ]);
                        $DB->insert_record('oauth2_user_field_mapping', $fieldmap);
                    }
                }
                // Update the baseurl for the issuer.
                $microsoftservice->baseurl = 'https://login.microsoftonline.com/common/v2.0';
                $microsoftservice->timemodified = $time;
                $microsoftservice->usermodified = $adminid;
                $DB->update_record('oauth2_issuer', $microsoftservice);
            } else {
                // Single-tenant endpoint, add discovery_endpoint if it doesn't exist.
                $url = $microsoftservice->baseurl;
                $url .= (substr($url, -1) === '/') ? '' : '/';
                $url .= '.well-known/openid-configuration';
                $endpoint = ['issuerid' => $microsoftservice->id, 'name' => 'discovery_endpoint'];
                $endpointid = $DB->get_field('oauth2_endpoint', 'id', $endpoint);
                if (!$endpointid) {
                    $endpoint = array_merge($endpoint, [
                        'url' => $url,
                        'timecreated' => $time,
                        'timemodified' => $time,
                        'usermodified' => $adminid,
                    ]);
                    $DB->insert_record('oauth2_endpoint', $endpoint);
                }
            }
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025073100.01);
    }

    if ($oldversion < 2025081900.03) {
        // Remove section_links block.

        if (!file_exists($CFG->dirroot . "/blocks/section_links/version.php")) {
            uninstall_plugin('block', 'section_links');
            // Delete all the admin preset plugin references to section_links.
            $DB->delete_records('adminpresets_plug', ['plugin' => 'block', 'name' => 'section_links']);
            // Remove the section_links block from the unaddableblocks setting.
            $settings = $DB->get_records('config_plugins', ['name' => 'unaddableblocks'], '', 'plugin, value');
            foreach ($settings as $setting) {
                // Split the value into an array of items and remove 'section_links'.
                // Using PREG_SPLIT_NO_EMPTY will remove any empty strings resulting from multiple commas.
                $items = preg_split('/,/', $setting->value, -1, PREG_SPLIT_NO_EMPTY);
                $newvalue = array_filter($items, function($item) {
                    return trim($item) !== 'section_links';
                });
                set_config(
                    'unaddableblocks',
                    implode(',', $newvalue),
                    $setting->plugin,
                );
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025081900.03);
    }

    if ($oldversion < 2025081900.04) {

        // Define table shortlink to be created.
        $table = new xmldb_table('shortlink');

        // Adding fields to table shortlink.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('shortcode', XMLDB_TYPE_CHAR, '12', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'shortcode');
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'userid');
        $table->add_field('linktype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'component');
        $table->add_field('identifier', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'linktype');

        // Adding keys to table shortlink.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Adding indexes to table shortlink.
        $table->add_index('shortcode_userid', XMLDB_INDEX_UNIQUE, ['userid', 'shortcode']);
        $table->add_index('shortcode', XMLDB_INDEX_NOTUNIQUE, ['shortcode']);

        // Conditionally launch create table for shortlink.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025081900.04);
    }

    if ($oldversion < 2025082600.01) {
        if (get_config('moodlecourse', 'format') === 'social') {
            // If the social course format is set as default, change it to topics.
            set_config('format', 'topics', 'moodlecourse');
        }
        $DB->delete_records('adminpresets_plug', ['plugin' => 'format', 'name' => 'social']);

        // Disable Social course format.
        $manager = \core_plugin_manager::resolve_plugininfo_class('format');
        $manager::enable_plugin('social', 0);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025082600.01);
    }

    if ($oldversion < 2025082900.01) {
        // Changing precision of field name on table question_categories to (1333).
        $table = new xmldb_table('question_categories');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025082900.01);
    }

    if ($oldversion < 2025090200.01) {
        // Define field enableaitools to be added to course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('enableaitools', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'pdfexportfont');

        // Conditionally launch add field enableaitools.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field enableaitools to be added to course_modules.
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('enableaitools', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'lang');

        // Conditionally launch add field enableaitools.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field enabledaiactions to be added to course_modules.
        $field = new xmldb_field('enabledaiactions', XMLDB_TYPE_TEXT, null, null, null, null, null, 'enableaitools');

        // Conditionally launch add field enabledaiactions.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025090200.01);
    }

    if ($oldversion < 2025090200.02) {
        $table = new xmldb_table('reportbuilder_schedule');

        // Conditionally launch add field classname.
        $field = new xmldb_field('classname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'enabled');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally launch add field configdata.
        $field = new xmldb_field('configdata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'classname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Migrate existing data to new structure.
        $schedules = $DB->get_records('reportbuilder_schedule');
        foreach ($schedules as $schedule) {
            $DB->update_record('reportbuilder_schedule', [
                'id' => $schedule->id,
                'classname' => core_reportbuilder\reportbuilder\schedule\message::class,
                'configdata' => json_encode([
                    'subject' => $schedule->subject,
                    'message' => ['text' => $schedule->message, 'format' => $schedule->messageformat],
                    'reportempty' => $schedule->reportempty,
                ]),
            ]);
        }

        // Launch change of nullability for field configdata (after migrating data).
        $field = new xmldb_field('configdata', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'classname');
        $dbman->change_field_notnull($table, $field);

        // Launch change of nullability for field audiences.
        $field = new xmldb_field('audiences', XMLDB_TYPE_TEXT, null, null, null, null, null, 'enabled');
        $dbman->change_field_notnull($table, $field);

        // Conditionally launch drop field subject.
        $field = new xmldb_field('subject');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Conditionally launch drop field message.
        $field = new xmldb_field('message');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Conditionally launch drop field messageformat.
        $field = new xmldb_field('messageformat');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Conditionally launch drop field reportempty.
        $field = new xmldb_field('reportempty');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025090200.02);
    }

    if ($oldversion < 2025091600.01) {
        // Define field shared to be added to customfield_category.
        $table = new xmldb_table('customfield_category');
        $field = new xmldb_field('shared', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'contextid');

        // Conditionally launch add field shared.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field component to be added to customfield_data.
        $table = new xmldb_table('customfield_data');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'contextid');

        // Conditionally launch add field component.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

         // Define field area to be added to customfield_data.
        $table = new xmldb_table('customfield_data');
        $field = new xmldb_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'component');

        // Conditionally launch add field area.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field itemid to be added to customfield_data.
        $table = new xmldb_table('customfield_data');
        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'area');

        // Conditionally launch add field itemid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index instanceid-fieldid (unique) to be dropped form customfield_data.
        $table = new xmldb_table('customfield_data');
        $index = new xmldb_index('instanceid-fieldid', XMLDB_INDEX_UNIQUE, ['instanceid', 'fieldid']);

        // Conditionally launch drop index instanceid-fieldid.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index instanceid-fieldid-component-area-itemid (unique) to be added to customfield_data.
        $table = new xmldb_table('customfield_data');
        $index = new xmldb_index(
            'instanceid-fieldid-component-area-itemid',
            XMLDB_INDEX_UNIQUE,
            ['instanceid', 'fieldid', 'component', 'area', 'itemid']
        );

        // Conditionally launch add index instanceid-fieldid-component-area-itemid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Populate component, area and itemid for each record in customfield_data table.
        $sql = "SELECT d.id, c.component, c.area, c.itemid
                  FROM {customfield_data} d
                  JOIN {customfield_field} f ON d.fieldid = f.id
                  JOIN {customfield_category} c ON f.categoryid = c.id";
        $records = $DB->get_recordset_sql($sql);

        foreach ($records as $r) {
            $DB->update_record('customfield_data', (object)[
                'id'        => $r->id,
                'component' => $r->component,
                'area'      => $r->area,
                'itemid'    => $r->itemid,
            ]);
        }

        $records->close();

        // Define table customfield_shared to be created.
        $table = new xmldb_table('customfield_shared');

        // Adding fields to table customfield_shared.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table customfield_shared.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('categoryid', XMLDB_KEY_FOREIGN, ['categoryid'], 'customfield_category', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Adding indexes to table customfield_shared.
        $table->add_index('categoryid-component-area-itemid', XMLDB_INDEX_UNIQUE, ['categoryid', 'component', 'area', 'itemid']);

        // Conditionally launch create table for customfield_shared.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025091600.01);
    }

    if ($oldversion < 2025092200.00) {
        // Changing precision of field name on table customfield_category to (1333).
        $table = new xmldb_table('customfield_category');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field name on table customfield_field to (1333).
        $table = new xmldb_table('customfield_field');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'shortname');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025092200.00);
    }

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025103000.01) {
        // Remove any orphaned competency evidence records (pointing to non-existing contexts).
        $DB->delete_records_select('competency_evidence', 'NOT EXISTS (
            SELECT ctx.id FROM {context} ctx WHERE ctx.id = {competency_evidence}.contextid
        )');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025103000.01);
    }

    if ($oldversion < 2025112700.02) {
        // Define index hashcode (not unique) to be added to question_response_analysis.
        $table = new xmldb_table('question_response_analysis');
        $index = new xmldb_index('hashcode', XMLDB_INDEX_NOTUNIQUE, ['hashcode']);

        // Conditionally launch add index hashcode.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index hashcode (not unique) to be added to question_statistics.
        $table = new xmldb_table('question_statistics');
        $index = new xmldb_index('hashcode', XMLDB_INDEX_NOTUNIQUE, ['hashcode']);

        // Conditionally launch add index hashcode.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025112700.02);
    }

    if ($oldversion < 2025121200.01) {
        // Fix Microsoft OAuth2 user field mappings to use OpenID Connect standard field names.
        // This corrects the mappings introduced in MDL-84432 which used non-standard field names
        // that only work with personal Microsoft accounts but not work/school (Entra ID) accounts.
        $userfieldmappings = [
            'firstname' => 'given_name',
            'lastname' => 'family_name',
        ];
        $admin = get_admin();
        $adminid = $admin ? $admin->id : '0';
        $microsoftservices = $DB->get_records('oauth2_issuer', ['servicetype' => 'microsoft']);
        foreach ($microsoftservices as $microsoftservice) {
            $time = time();
            // Update user field mappings to use OpenID Connect standard field names.
            foreach ($userfieldmappings as $internalfieldname => $externalfieldname) {
                $fieldmap = ['issuerid' => $microsoftservice->id, 'internalfield' => $internalfieldname];
                $fieldmapid = $DB->get_field('oauth2_user_field_mapping', 'id', $fieldmap);
                if ($fieldmapid) {
                    $fieldmap = array_merge($fieldmap, [
                        'id' => $fieldmapid,
                        'externalfield' => $externalfieldname,
                        'timemodified' => $time,
                        'usermodified' => $adminid,
                    ]);
                    $DB->update_record('oauth2_user_field_mapping', $fieldmap);
                }
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025121200.01);
    }

    if ($oldversion < 2025121900.01) {
            // Define field nextversion to be added to question_bank_entries.
        $table = new xmldb_table('question_bank_entries');
        $field = new xmldb_field('nextversion', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'ownerid');

        // Conditionally launch add field nextversion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_main_savepoint(true, 2025121900.01);
    }

    if ($oldversion < 2026010900.01) {
        // Changing the default of field showactivitydates on table course to 1.
        $table = new xmldb_table('course');
        $field = new xmldb_field('showactivitydates', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'originalcourseid');

        // Launch change of default for field showactivitydates.
        $dbman->change_field_default($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2026010900.01);
    }

    if ($oldversion < 2026010900.02) {
        // Delete any remaining instances of qtype_random questions.
        // At this point, such questions were created during a restore, but never used by anything (otherwise they would have
        // been converted to question set references and deleted already), so they are all safe to delete.
        $questions = $DB->get_records('question', ['qtype' => 'random']);
        foreach ($questions as $question) {
            question_delete_question($question->id);
        }
        // Finally, uninstall qtype_random as it's been removed.
        uninstall_plugin('qtype', 'random');
        upgrade_main_savepoint(true, 2026010900.02);
    }

    if ($oldversion < 2026011600.01) {
        // Remove activity_modules block.

        if (!file_exists($CFG->dirroot . "/blocks/activity_modules/version.php")) {
            uninstall_plugin('block', 'activity_modules');
            // Delete all the admin preset plugin references to activity_modules.
            $DB->delete_records('adminpresets_plug', ['plugin' => 'block', 'name' => 'activity_modules']);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2026011600.01);
    }

    if ($oldversion < 2026013000.01) {
        // Define index nextruntime_classname (not unique) to be added to task_adhoc.
        $table = new xmldb_table('task_adhoc');
        $index = new xmldb_index('nextruntime_classname', XMLDB_INDEX_NOTUNIQUE, ['nextruntime', 'classname']);

        // Conditionally launch add index nextruntime_classname.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2026013000.01);
    }

    if ($oldversion < 2026013000.02) {
        // Define index lastruntime_nextruntime (not unique) to be added to task_scheduled.
        $table = new xmldb_table('task_scheduled');
        $index = new xmldb_index('lastruntime_nextruntime', XMLDB_INDEX_NOTUNIQUE, ['lastruntime', 'nextruntime']);

        // Conditionally launch add index lastruntime_nextruntime.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2026013000.02);
    }

    if ($oldversion < 2026013000.03) {
        core_question\versions::resolve_unique_version_violations();

        // Define index questionbankentryid-version (unique) to be added to question_versions.
        $table = new xmldb_table('question_versions');
        $index = new xmldb_index('questionbankentryid-version', XMLDB_INDEX_UNIQUE, ['questionbankentryid', 'version']);

        // Conditionally launch add index questionbankentryid-version.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_main_savepoint(true, 2026013000.03);
    }

    if ($oldversion < 2026013000.04) {
        \core_question\category_manager::fix_restored_category_parents();
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2026013000.04);
    }

    return true;
}
