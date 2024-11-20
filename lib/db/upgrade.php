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

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022120900.01) {
        // Remove any orphaned role assignment records (pointing to non-existing roles).
        $DB->delete_records_select('role_assignments', 'NOT EXISTS (
            SELECT r.id FROM {role} r WHERE r.id = {role_assignments}.roleid
        )');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2022120900.01);
    }

    if ($oldversion < 2022121600.01) {
        // Define index blocknameindex (not unique) to be added to block_instances.
        $table = new xmldb_table('block_instances');
        $index = new xmldb_index('blocknameindex', XMLDB_INDEX_NOTUNIQUE, ['blockname']);

        // Conditionally launch add index blocknameindex.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2022121600.01);
    }

    if ($oldversion < 2023010300.00) {
        // The useexternalyui setting has been removed.
        unset_config('useexternalyui');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023010300.00);
    }

    if ($oldversion < 2023020800.00) {
        // If cachestore_memcached is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/cache/stores/memcached/version.php')) {
            // Clean config.
            unset_all_config_for_plugin('cachestore_memcached');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023020800.00);
    }

    if ($oldversion < 2023021700.01) {
        // Define field pdfexportfont to be added to course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('pdfexportfont', XMLDB_TYPE_CHAR, '50', null, false, false, null, 'showcompletionconditions');

        // Conditionally launch add field pdfexportfont.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023021700.01);
    }

    if ($oldversion < 2023022000.00) {
        // Remove grade_report_showquickfeedback, grade_report_enableajax, grade_report_showeyecons,
        // grade_report_showlocks, grade_report_showanalysisicon preferences for every user.
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showquickfeedback']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_enableajax']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showeyecons']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showlocks']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showanalysisicon']);

        // The grade_report_showquickfeedback, grade_report_enableajax, grade_report_showeyecons,
        // grade_report_showlocks, grade_report_showanalysisicon settings have been removed.
        unset_config('grade_report_showquickfeedback');
        unset_config('grade_report_enableajax');
        unset_config('grade_report_showeyecons');
        unset_config('grade_report_showlocks');
        unset_config('grade_report_showanalysisicon');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023022000.00);
    }

    if ($oldversion < 2023030300.01) {
        $sql = "SELECT preset.*
                  FROM {adminpresets} preset
            INNER JOIN {adminpresets_it} it ON preset.id = it.adminpresetid
                 WHERE it.name = :name AND it.value = :value AND preset.iscore > 0";
        // Some settings and plugins have been added/removed to the Starter and Full preset. Add them to the core presets if
        // they haven't been included yet.
        $params = ['name' => get_string('starterpreset', 'core_adminpresets'), 'iscore' => 1];
        $starterpreset = $DB->get_record('adminpresets', $params);
        if (!$starterpreset) {
            // Starter admin preset might have been created using the English name.
            $name = get_string_manager()->get_string('starterpreset', 'core_adminpresets', null, 'en');
            $params['name'] = $name;
            $starterpreset = $DB->get_record('adminpresets', $params);
        }
        if (!$starterpreset) {
            // We tried, but we didn't find starter by name. Let's find a core preset that sets 'usecomments' setting to 0.
            $params = ['name' => 'usecomments', 'value' => '0'];
            $starterpreset = $DB->get_record_sql($sql, $params);
        }

        $params = ['name' => get_string('fullpreset', 'core_adminpresets')];
        $fullpreset = $DB->get_record_select('adminpresets', 'name = :name and iscore > 0', $params);
        if (!$fullpreset) {
            // Full admin preset might have been created using the English name.
            $name = get_string_manager()->get_string('fullpreset', 'core_adminpresets', null, 'en');
            $params['name'] = $name;
            $fullpreset = $DB->get_record_select('adminpresets', 'name = :name and iscore > 0', $params);
        }
        if (!$fullpreset) {
            // We tried, but we didn't find full by name. Let's find a core preset that sets 'usecomments' setting to 1.
            $params = ['name' => 'usecomments', 'value' => '1'];
            $fullpreset = $DB->get_record_sql($sql, $params);
        }

        $settings = [
            // Settings. Set Activity chooser tabs to "Starred, Recommended, All"(5) for Starter and back it to default(3) for Full.
            [
                'presetid' => $starterpreset->id,
                'plugin' => 'none',
                'name' => 'activitychoosertabmode',
                'value' => '4',
            ],
            [
                'presetid' => $fullpreset->id,
                'plugin' => 'none',
                'name' => 'activitychoosertabmode',
                'value' => '3',
            ],
        ];
        foreach ($settings as $notused => $setting) {
            $params = ['adminpresetid' => $setting['presetid'], 'plugin' => $setting['plugin'], 'name' => $setting['name']];
            if (!$record = $DB->get_record('adminpresets_it', $params)) {
                $record = new \stdClass();
                $record->adminpresetid = $setting['presetid'];
                $record->plugin = $setting['plugin'];
                $record->name = $setting['name'];
                $record->value = $setting['value'];
                $DB->insert_record('adminpresets_it', $record);
            } else {
                $record->value = $setting['value'];
                $DB->update_record('adminpresets_it', $record);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023030300.01);
    }

    if ($oldversion < 2023030300.02) {
        // If cachestore_mongodb is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/cache/stores/mongodb/version.php')) {
            // Clean config.
            unset_all_config_for_plugin('cachestore_mongodb');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023030300.02);
    }

    if ($oldversion < 2023030300.03) {
        // If editor_tinymce is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/lib/editor/tinymce/version.php')) {
            // Clean config.
            uninstall_plugin('editor', 'tinymce');
            $DB->delete_records('user_preferences', [
                'name' => 'htmleditor',
                'value' => 'tinymce',
            ]);

            if ($editors = get_config('core', 'texteditors')) {
                $editors = array_flip(explode(',', $editors));
                unset($editors['tinymce']);
                set_config('texteditors', implode(',', array_flip($editors)));
            }
        }
        upgrade_main_savepoint(true, 2023030300.03);
    }

    if ($oldversion < 2023031000.02) {
        // If editor_tinymce is no longer present, remove it's sub-plugins too.
        if (!file_exists($CFG->dirroot . '/lib/editor/tinymce/version.php')) {
            $DB->delete_records_select(
                'config_plugins',
                $DB->sql_like('plugin', ':plugin'),
                ['plugin' => $DB->sql_like_escape('tinymce_') . '%']
            );
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023031000.02);
    }

    if ($oldversion < 2023031400.01) {
        // Define field id to be added to groups.
        $table = new xmldb_table('groups');
        $field = new xmldb_field('visibility', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'picture');

        // Conditionally launch add field visibility.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field participation to be added to groups.
        $field = new xmldb_field('participation', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'visibility');

        // Conditionally launch add field participation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023031400.01);
    }

    if ($oldversion < 2023031400.02) {
        // Define table xapi_states to be created.
        $table = new xmldb_table('xapi_states');

        // Adding fields to table xapi_states.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('stateid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('statedata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('registration', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table xapi_states.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table xapi_states.
        $table->add_index('component-itemid', XMLDB_INDEX_NOTUNIQUE, ['component', 'itemid']);
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, ['timemodified']);

        // Conditionally launch create table for xapi_states.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        if (!isset($CFG->xapicleanupperiod)) {
            set_config('xapicleanupperiod', WEEKSECS * 8);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023031400.02);
    }

    if ($oldversion < 2023040600.01) {
        // If logstore_legacy is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/admin/tool/log/store/legacy/version.php')) {
            uninstall_plugin('logstore', 'legacy');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023040600.01);
    }

    if ($oldversion < 2023041100.00) {
        // Add public key field to user_devices table.
        $table = new xmldb_table('user_devices');
        $field = new xmldb_field('publickey', XMLDB_TYPE_TEXT, null, null, null, null, null, 'uuid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023041100.00);
    }

    if ($oldversion < 2023042000.00) {
        // If mod_assignment is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/mod/assignment/version.php')) {
            // Delete all mod_assignment grade_grades orphaned data.
            $DB->delete_records_select(
                'grade_grades',
                "itemid IN (SELECT id FROM {grade_items} WHERE itemtype = 'mod' AND itemmodule = 'assignment')"
            );

            // Delete all mod_assignment grade_grades_history orphaned data.
            $DB->delete_records('grade_grades_history', ['source' => 'mod/assignment']);

            // Delete all mod_assignment grade_items orphaned data.
            $DB->delete_records('grade_items', ['itemtype' => 'mod', 'itemmodule' => 'assignment']);

            // Delete all mod_assignment grade_items_history orphaned data.
            $DB->delete_records('grade_items_history', ['itemtype' => 'mod', 'itemmodule' => 'assignment']);

            // Delete core mod_assignment subplugins.
            uninstall_plugin('assignment', 'offline');
            uninstall_plugin('assignment', 'online');
            uninstall_plugin('assignment', 'upload');
            uninstall_plugin('assignment', 'uploadsingle');

            // Delete other mod_assignment subplugins.
            $pluginnamelike = $DB->sql_like('plugin', ':pluginname');
            $subplugins = $DB->get_fieldset_select('config_plugins', 'plugin', "$pluginnamelike AND name = :name", [
                'pluginname' => $DB->sql_like_escape('assignment_') . '%',
                'name' => 'version',
            ]);
            foreach ($subplugins as $subplugin) {
                [$plugin, $subpluginname] = explode('_', $subplugin, 2);
                uninstall_plugin($plugin, $subpluginname);
            }

            // Delete mod_assignment.
            uninstall_plugin('mod', 'assignment');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023042000.00);
    }

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023051500.00) {
        // Define communication table.
        $table = new xmldb_table('communication');

        // Adding fields to table communication.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'instanceid');
        $table->add_field('instancetype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'component');
        $table->add_field('provider', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'instancerype');
        $table->add_field('roomname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'provider');
        $table->add_field('avatarfilename', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'roomname');
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1, 'avatarfilename');

        // Add key.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for communication.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define communication user table.
        $table = new xmldb_table('communication_user');

        // Adding fields to table communication.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('commid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'commid');
        $table->add_field('synced', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'userid');
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'synced');

        // Add keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('commid', XMLDB_KEY_FOREIGN, ['commid'], 'communication', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Conditionally launch create table for communication.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023051500.00);
    }

    if ($oldversion < 2023062200.00) {
        // Remove device specific fields for themes from config table.
        unset_config('thememobile');
        unset_config('themelegacy');
        unset_config('themetablet');

        upgrade_main_savepoint(true, 2023062200.00);
    }

    if ($oldversion < 2023062700.01) {
        // Define field name to be added to external_tokens.
        $table = new xmldb_table('external_tokens');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'lastaccess');
        // Conditionally launch add field name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Update the old external tokens.
        $sql = 'UPDATE {external_tokens}
                   SET name = ' . $DB->sql_concat(
                       // We only need the prefix, so leave the third param with an empty string.
                        "'" . get_string('tokennameprefix', 'webservice', '') . "'",
                        "id"
                    );
        $DB->execute($sql);
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023062700.01);
    }

    if ($oldversion < 2023062900.01) {
        // Define field avatarsynced to be added to communication.
        $table = new xmldb_table('communication');
        $field = new xmldb_field('avatarsynced', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'active');

        // Conditionally launch add field avatarsynced.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023062900.01);
    }

    if ($oldversion < 2023080100.00) {
        // Upgrade yaml mime type for existing yaml and yml files.
        $filetypes = [
            '%.yaml' => 'application/yaml',
            '%.yml' => 'application/yaml,',
        ];

        $select = $DB->sql_like('filename', '?', false);
        foreach ($filetypes as $extension => $mimetype) {
            $DB->set_field_select(
                'files',
                'mimetype',
                $mimetype,
                $select,
                [$extension]
            );
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023080100.00);
    }

    if ($oldversion < 2023081500.00) {
        upgrade_core_licenses();
        upgrade_main_savepoint(true, 2023081500.00);
    }

    if ($oldversion < 2023081800.01) {
        // Remove enabledevicedetection and devicedetectregex from config table.
        unset_config('enabledevicedetection');
        unset_config('devicedetectregex');
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023081800.01);
    }

    if ($oldversion < 2023082200.01) {
        // Some MIME icons have been removed and replaced with existing icons. They need to be upgraded for custom MIME types.
        $replacedicons = [
            'avi' => 'video',
            'base' => 'database',
            'bmp' => 'image',
            'html' => 'markup',
            'jpeg' => 'image',
            'mov' => 'video',
            'mp3' => 'audio',
            'mpeg' => 'video',
            'png' => 'image',
            'quicktime' => 'video',
            'tiff' => 'image',
            'wav' => 'audio',
            'wmv' => 'video',
        ];

        $custom = [];
        if (!empty($CFG->customfiletypes)) {
            if (array_key_exists('customfiletypes', $CFG->config_php_settings)) {
                // It's set in config.php, so the MIME icons can't be upgraded automatically.
                echo("\nYou need to manually check customfiletypes in config.php because some MIME icons have been removed!\n");
            } else {
                // It's a JSON string in the config table.
                $custom = json_decode($CFG->customfiletypes);
            }
        }

        $changed = false;
        foreach ($custom as $customentry) {
            if (!empty($customentry->icon) && array_key_exists($customentry->icon, $replacedicons)) {
                $customentry->icon = $replacedicons[$customentry->icon];
                $changed = true;
            }
        }

        if ($changed) {
            // Save the new customfiletypes.
            set_config('customfiletypes', json_encode($custom));
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023082200.01);
    }

    if ($oldversion < 2023082200.02) {
        // Some MIME icons have been removed. They need to be replaced to 'unknown' for custom MIME types.
        $removedicons = array_flip([
            'clip-353',
            'edit',
            'env',
            'explore',
            'folder-open',
            'help',
            'move',
            'parent',
        ]);

        $custom = [];
        if (!empty($CFG->customfiletypes)) {
            if (array_key_exists('customfiletypes', $CFG->config_php_settings)) {
                // It's set in config.php, so the MIME icons can't be upgraded automatically.
                echo("\nYou need to manually check customfiletypes in config.php because some MIME icons have been removed!\n");
            } else {
                // It's a JSON string in the config table.
                $custom = json_decode($CFG->customfiletypes);
            }
        }

        $changed = false;
        foreach ($custom as $customentry) {
            if (!empty($customentry->icon) && array_key_exists($customentry->icon, $removedicons)) {
                // The icon has been removed, so set it to unknown.
                $customentry->icon = 'unknown';
                $changed = true;
            }
        }

        if ($changed) {
            // Save the new customfiletypes.
            set_config('customfiletypes', json_encode($custom));
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023082200.02);
    }

    if ($oldversion < 2023082200.04) {
        // Remove any non-unique filters/conditions.
        $duplicates = $DB->get_records_sql("
            SELECT MIN(id) AS id, reportid, uniqueidentifier, iscondition
              FROM {reportbuilder_filter}
          GROUP BY reportid, uniqueidentifier, iscondition
            HAVING COUNT(*) > 1");

        foreach ($duplicates as $duplicate) {
            $DB->delete_records_select(
                'reportbuilder_filter',
                'id <> :id AND reportid = :reportid AND uniqueidentifier = :uniqueidentifier AND iscondition = :iscondition',
                (array) $duplicate
            );
        }

        // Define index report-filter (unique) to be added to reportbuilder_filter.
        $table = new xmldb_table('reportbuilder_filter');
        $index = new xmldb_index('report-filter', XMLDB_INDEX_UNIQUE, ['reportid', 'uniqueidentifier', 'iscondition']);

        // Conditionally launch add index report-filter.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023082200.04);
    }

    if ($oldversion < 2023082600.02) {
        // Get all the ids of users who still have md5 hashed passwords.
        if ($DB->sql_regex_supported()) {
            // If the database supports regex, we can add an exact check for md5.
            $condition = 'password ' . $DB->sql_regex() . ' :pattern';
            $params = ['pattern' => "^[a-fA-F0-9]{32}$"];
        } else {
            // Otherwise, we need to use a NOT LIKE condition and rule out bcrypt.
            $condition = $DB->sql_like('password', ':pattern', true, false, true);
            $params = ['pattern' => '$2y$%'];
        }

        // Regardless of database regex support we check the hash length which should be enough.
        // But extra regex or like matching makes sure.
        $sql = "SELECT id FROM {user} WHERE " . $DB->sql_length('password') . " = 32 AND $condition";
        $userids = $DB->get_fieldset_sql($sql, $params);

        // Update the password for each user with a new SHA-512 hash.
        // Users won't know this password, but they can reset it. This is a security measure,
        // in case the database is compromised or the hash has been leaked elsewhere.
        foreach ($userids as $userid) {
            $password = base64_encode(random_bytes(24)); // Generate a new password for the user.

            $user = new \stdClass();
            $user->id = $userid;
            $user->password = hash_internal_user_password($password);
            $DB->update_record('user', $user, true);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023082600.02);
    }

    if ($oldversion < 2023082600.03) {
        // The previous default configuration had a typo, check for its presence and correct if necessary.
        $sensiblesettings = get_config('adminpresets', 'sensiblesettings');
        if (strpos($sensiblesettings, 'smtppass@none') !== false) {
            $newsensiblesettings = str_replace('smtppass@none', 'smtppass@@none', $sensiblesettings);
            set_config('sensiblesettings', $newsensiblesettings, 'adminpresets');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023082600.03);
    }

    if ($oldversion < 2023082600.05) {
        unset_config('completiondefault');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023082600.05);
    }

    if ($oldversion < 2023090100.00) {
        // Upgrade MIME type for existing PSD files.
        $DB->set_field_select(
            'files',
            'mimetype',
            'image/vnd.adobe.photoshop',
            $DB->sql_like('filename', '?', false),
            ['%.psd']
        );

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023090100.00);
    }

    if ($oldversion < 2023090200.01) {
        // Define table moodlenet_share_progress to be created.
        $table = new xmldb_table('moodlenet_share_progress');

        // Adding fields to table moodlenet_share_progress.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resourceurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, null, null, null);

        // Adding keys to table moodlenet_share_progress.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for moodlenet_share_progress.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023090200.01);
    }

    if ($oldversion < 2023091300.03) {
        // Delete all the searchanywhere prefs in user_preferences table.
        $DB->delete_records('user_preferences', ['name' => 'userselector_searchanywhere']);
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023091300.03);
    }

    if ($oldversion < 2023100400.01) {
        // Delete datakey with datavalue -1.
        $DB->delete_records('messageinbound_datakeys', ['datavalue' => '-1']);
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023100400.01);
    }

    if ($oldversion < 2023100400.03) {
        // Define field id to be added to communication.
        $table = new xmldb_table('communication');

        // Add the field and allow it to be nullable.
        // We need to backfill data before setting it to NOT NULL.
        $field = new xmldb_field(
            name: 'contextid',
            type: XMLDB_TYPE_INTEGER,
            precision: '10',
            notnull: null,
            previous: 'id',
        );

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Fill the existing data.
        $sql = <<<EOF
                    SELECT comm.id, c.id AS contextid
                      FROM {communication} comm
                INNER JOIN {context} c ON c.instanceid = comm.instanceid AND c.contextlevel = :contextcourse
                     WHERE comm.contextid IS NULL
                       AND comm.instancetype = :instancetype
        EOF;
        $rs = $DB->get_recordset_sql(
            sql: $sql,
            params: [
                'contextcourse' => CONTEXT_COURSE,
                'instancetype' => 'coursecommunication',
            ],
        );
        foreach ($rs as $comm) {
            $DB->set_field(
                table: 'communication',
                newfield: 'contextid',
                newvalue: $comm->contextid,
                conditions: [
                    'id' => $comm->id,
                ],
            );
        }
        $rs->close();

        $systemcontext = \core\context\system::instance();
        $DB->set_field_select(
            table: 'communication',
            newfield: 'contextid',
            newvalue: $systemcontext->id,
            select: 'contextid IS NULL',
        );

        // Now make it NOTNULL.
        $field = new xmldb_field(
            name: 'contextid',
            type: XMLDB_TYPE_INTEGER,
            precision: '10',
            notnull:  XMLDB_NOTNULL,
        );
        $dbman->change_field_notnull($table, $field);

        // Add the contextid constraint.
        $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
        $dbman->add_key($table, $key);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023100400.03);
    }

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023110900.00) {
        // Reorder the editors to make Tiny the default for all upgrades.
        $editors = [];
        array_push($editors, 'tiny');
        $list = explode(',', $CFG->texteditors);
        foreach ($list as $editor) {
            if ($editor != 'tiny') {
                array_push($editors, $editor);
            }
        }
        set_config('texteditors', implode(',', $editors));

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023110900.00);
    }

    if ($oldversion < 2023120100.01) {
        // The $CFG->linkcoursesections setting has been removed because it's not required anymore.
        // From now, sections will be always linked because a new page, section.php, has been created to display a single section.
        unset_config('linkcoursesections');

        upgrade_main_savepoint(true, 2023120100.01);
    }

    if ($oldversion < 2023121800.02) {
        // Define field attemptsavailable to be added to task_adhoc.
        $table = new xmldb_table('task_adhoc');
        $field = new xmldb_field(
            name: 'attemptsavailable',
            type: XMLDB_TYPE_INTEGER,
            precision: '2',
            unsigned: null,
            notnull: null,
            sequence: null,
            default: null,
            previous: 'pid',
        );

        // Conditionally launch add field attemptsavailable.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set attemptsavailable to 0 for the tasks that have not been run before.
        // Set attemptsavailable to 1 for the tasks that have been run and failed before.
        $DB->execute('
            UPDATE {task_adhoc}
               SET attemptsavailable = CASE
                                            WHEN faildelay = 0 THEN 1
                                            WHEN faildelay > 0 THEN 0
                                       END
        ');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023121800.02);
    }

    if ($oldversion < 2023122100.01) {

        // Define field component to be added to course_sections.
        $table = new xmldb_table('course_sections');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'availability');

        // Conditionally launch add field component.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field itemid to be added to course_sections.
        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'component');

        // Conditionally launch add field itemid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2023122100.01);
    }

    if ($oldversion < 2023122100.02) {
        $sqllike = $DB->sql_like('filtercondition', '?');
        $params[] = '%includesubcategories%';

        $sql = "SELECT qsr.* FROM {question_set_references} qsr WHERE $sqllike";
        $results = $DB->get_recordset_sql($sql, $params);
        foreach ($results as $result) {
            $filtercondition = json_decode($result->filtercondition);
            if (isset($filtercondition->filter->category->includesubcategories)) {
                $filtercondition->filter->category->filteroptions =
                    ['includesubcategories' => $filtercondition->filter->category->includesubcategories];
                unset($filtercondition->filter->category->includesubcategories);
                $result->filtercondition = json_encode($filtercondition);
                $DB->update_record('question_set_references', $result);
            }
        }
        $results->close();

        upgrade_main_savepoint(true, 2023122100.02);
    }

    if ($oldversion < 2024010400.01) {

        // Define index timecreated (not unique) to be added to notifications.
        $table = new xmldb_table('notifications');
        $createdindex = new xmldb_index('timecreated', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);

        // Conditionally launch add index timecreated.
        if (!$dbman->index_exists($table, $createdindex)) {
            $dbman->add_index($table, $createdindex);
        }

        // Define index timeread (not unique) to be added to notifications.
        $readindex = new xmldb_index('timeread', XMLDB_INDEX_NOTUNIQUE, ['timeread']);

        // Conditionally launch add index timeread.
        if (!$dbman->index_exists($table, $readindex)) {
            $dbman->add_index($table, $readindex);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024010400.01);
    }

    if ($oldversion < 2024012300.00) {

        // Define field valuetrust to be added to customfield_data.
        $table = new xmldb_table('customfield_data');
        $field = new xmldb_field('valuetrust', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'valueformat');

        // Conditionally launch add field valuetrust.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024012300.00);
    }

    if ($oldversion < 2024020200.01) {
        // If h5plib_v124 is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/h5p/h5plib/v124/version.php')) {
            // Clean config.
            uninstall_plugin('h5plib', 'v124');
        }

        // If h5plib_v126 is present, set it as the default one.
        if (file_exists($CFG->dirroot . '/h5p/h5plib/v126/version.php')) {
            set_config('h5plibraryhandler', 'h5plib_v126');
        }

        upgrade_main_savepoint(true, 2024020200.01);
    }

    if ($oldversion < 2024021500.01) {
        // Change default course formats order for sites never changed the default order.
        if (!get_config('core', 'format_plugins_sortorder')) {
            set_config('format_plugins_sortorder', 'topics,weeks,singleactivity,social');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024021500.01);
    }

    if ($oldversion < 2024021500.02) {
        // A [name => url] map of new OIDC endpoints to be updated/created.
        $endpointuris = [
            'authorization_endpoint' => 'https://clever.com/oauth/authorize',
            'token_endpoint' => 'https://clever.com/oauth/tokens',
            'userinfo_endpoint' => 'https://api.clever.com/userinfo',
            'jwks_uri' => 'https://clever.com/oauth/certs',
        ];

        // A [internalfield => externalfield] map of new OIDC-based user field mappings to be updated/created.
        $userfieldmappings = [
            'idnumber' => 'sub',
            'firstname' => 'given_name',
            'lastname' => 'family_name',
            'email' => 'email',
        ];

        $admin = get_admin();
        $adminid = $admin ? $admin->id : '0';

        $cleverservices = $DB->get_records('oauth2_issuer', ['servicetype' => 'clever']);
        foreach ($cleverservices as $cleverservice) {
            $time = time();

            // Insert/update the new endpoints.
            foreach ($endpointuris as $endpointname => $endpointuri) {
                $endpoint = ['issuerid' => $cleverservice->id, 'name' => $endpointname];
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

            // Insert/update new user field mappings.
            foreach ($userfieldmappings as $internalfieldname => $externalfieldname) {
                $fieldmap = ['issuerid' => $cleverservice->id, 'internalfield' => $internalfieldname];
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
            $cleverservice->baseurl = 'https://clever.com';
            $cleverservice->timemodified = $time;
            $cleverservice->usermodified = $adminid;
            $DB->update_record('oauth2_issuer', $cleverservice);
        }

        upgrade_main_savepoint(true, 2024021500.02);
    }

    if ($oldversion < 2024022300.02) {
        // Removed advanced grade item settings.
        unset_config('grade_item_advanced');

        upgrade_main_savepoint(true, 2024022300.02);
    }

    if ($oldversion < 2024030500.01) {

        // Define field firststartingtime to be added to task_adhoc.
        $table = new xmldb_table('task_adhoc');
        $field = new xmldb_field('firststartingtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'attemptsavailable');

        // Conditionally launch add field firststartingtime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            // Main savepoint reached.
            upgrade_main_savepoint(true, 2024030500.01);
        }

    }

    if ($oldversion < 2024030500.02) {

        // Get all "select" custom field shortnames.
        $fieldshortnames = $DB->get_fieldset('customfield_field', 'shortname', ['type' => 'select']);

        // Ensure any used in custom reports columns are not using integer type aggregation.
        foreach ($fieldshortnames as $fieldshortname) {
            $DB->execute("
                UPDATE {reportbuilder_column}
                   SET aggregation = NULL
                 WHERE " . $DB->sql_like('uniqueidentifier', ':uniqueidentifier', false) . "
                   AND aggregation IN ('avg', 'max', 'min', 'sum')
            ", [
                'uniqueidentifier' => '%' . $DB->sql_like_escape(":customfield_{$fieldshortname}"),
            ]);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024030500.02);
    }

    if ($oldversion < 2024032600.01) {

        // Changing precision of field attemptsavailable on table task_adhoc to (2).
        $table = new xmldb_table('task_adhoc');
        $field = new xmldb_field('attemptsavailable', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'pid');

        // Launch change of precision for field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024032600.01);
    }

    if ($oldversion < 2024041200.00) {
        // Define field blocking to be dropped from task_adhoc.
        $table = new xmldb_table('task_adhoc');
        $field = new xmldb_field('blocking');

        // Conditionally launch drop field blocking.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field blocking to be dropped from task_scheduled.
        $table = new xmldb_table('task_scheduled');
        $field = new xmldb_field('blocking');

        // Conditionally launch drop field blocking.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2024041200.00);
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

    return true;
}
