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

    if ($oldversion < 2025041400.08) {
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
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025041400.08);
    }

    if ($oldversion < 2025041400.09) {

        // Define field systememail to be added to oauth2_issuer.
        $table = new xmldb_table('oauth2_issuer');
        $field = new xmldb_field('systememail', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'loginpagename');

        // Conditionally launch add field systememail.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2025041400.09);
    }

    return true;
}
