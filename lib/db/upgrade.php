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

defined('MOODLE_INTERNAL') || die();

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
 *     // help you here. See {@link http://docs.moodle.org/dev/XMLDB_editor}.
 *     upgrade_main_savepoint(true, XXXXXXXXXX.XX);
 * }
 *
 * All plugins within Moodle (modules, blocks, reports...) support the existence of
 * their own upgrade.php file, using the "Frankenstyle" component name as
 * defined at {@link http://docs.moodle.org/dev/Frankenstyle}, for example:
 *     - {@link xmldb_page_upgrade($oldversion)}. (modules don't require the plugintype ("mod_") to be used.
 *     - {@link xmldb_auth_manual_upgrade($oldversion)}.
 *     - {@link xmldb_workshopform_accumulative_upgrade($oldversion)}.
 *     - ....
 *
 * In order to keep the contents of this file reduced, it's allowed to create some helper
 * functions to be used here in the {@link upgradelib.php} file at the same directory. Note
 * that such a file must be manually included from upgrade.php, and there are some restrictions
 * about what can be used within it.
 *
 * For more information, take a look to the documentation available:
 *     - Data definition API: {@link http://docs.moodle.org/dev/Data_definition_API}
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_main_upgrade($oldversion) {
    global $CFG, $DB;

    require_once($CFG->libdir.'/db/upgradelib.php'); // Core Upgrade-related functions.

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Always keep this upgrade step with version being the minimum
    // allowed version to upgrade from (v3.6.0 right now).
    if ($oldversion < 2018120300) {
        // Just in case somebody hacks upgrade scripts or env, we really can not continue.
        echo("You need to upgrade to 3.6.x or higher first!\n");
        exit(1);
        // Note this savepoint is 100% unreachable, but needed to pass the upgrade checks.
        upgrade_main_savepoint(true, 2018120300);
    }

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018120300.01) {
        // Update the FB logo URL.
        $oldurl = 'https://facebookbrand.com/wp-content/themes/fb-branding/prj-fb-branding/assets/images/fb-art.png';
        $newurl = 'https://facebookbrand.com/wp-content/uploads/2016/05/flogo_rgb_hex-brc-site-250.png';

        $updatesql = "UPDATE {oauth2_issuer}
                         SET image = :newimage
                       WHERE " . $DB->sql_compare_text('image', 100). " = :oldimage";
        $params = [
            'newimage' => $newurl,
            'oldimage' => $oldurl
        ];
        $DB->execute($updatesql, $params);

        upgrade_main_savepoint(true, 2018120300.01);
    }

    if ($oldversion < 2018120300.02) {
        // Set all individual conversations to enabled.
        $updatesql = "UPDATE {message_conversations}
                         SET enabled = :enabled
                       WHERE type = :type";
        $DB->execute($updatesql, ['enabled' => 1, 'type' => 1]);

        upgrade_main_savepoint(true, 2018120300.02);
    }

    if ($oldversion < 2018120301.02) {
        upgrade_delete_orphaned_file_records();
        upgrade_main_savepoint(true, 2018120301.02);
    }

    if ($oldversion < 2019011500.00) {
        // Define table task_log to be created.
        $table = new xmldb_table('task_log');

        // Adding fields to table task_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('classname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timestart', XMLDB_TYPE_NUMBER, '20, 10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timeend', XMLDB_TYPE_NUMBER, '20, 10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dbreads', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dbwrites', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('result', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table task_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table task_log.
        $table->add_index('classname', XMLDB_INDEX_NOTUNIQUE, ['classname']);
        $table->add_index('timestart', XMLDB_INDEX_NOTUNIQUE, ['timestart']);

        // Conditionally launch create table for task_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019011500.00);
    }

    if ($oldversion < 2019011501.00) {
        // Define field output to be added to task_log.
        $table = new xmldb_table('task_log');
        $field = new xmldb_field('output', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'result');

        // Conditionally launch add field output.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019011501.00);
    }

    if ($oldversion < 2019011801.00) {

        // Define table customfield_category to be created.
        $table = new xmldb_table('customfield_category');

        // Adding fields to table customfield_category.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '400', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table customfield_category.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);

        // Adding indexes to table customfield_category.
        $table->add_index('component_area_itemid', XMLDB_INDEX_NOTUNIQUE, ['component', 'area', 'itemid', 'sortorder']);

        // Conditionally launch create table for customfield_category.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table customfield_field to be created.
        $table = new xmldb_table('customfield_field');

        // Adding fields to table customfield_field.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '400', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('configdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table customfield_field.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('categoryid', XMLDB_KEY_FOREIGN, ['categoryid'], 'customfield_category', ['id']);

        // Adding indexes to table customfield_field.
        $table->add_index('categoryid_sortorder', XMLDB_INDEX_NOTUNIQUE, ['categoryid', 'sortorder']);

        // Conditionally launch create table for customfield_field.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table customfield_data to be created.
        $table = new xmldb_table('customfield_data');

        // Adding fields to table customfield_data.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('fieldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intvalue', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('decvalue', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->add_field('shortcharvalue', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('charvalue', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('valueformat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table customfield_data.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fieldid', XMLDB_KEY_FOREIGN, ['fieldid'], 'customfield_field', ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);

        // Adding indexes to table customfield_data.
        $table->add_index('instanceid-fieldid', XMLDB_INDEX_UNIQUE, ['instanceid', 'fieldid']);
        $table->add_index('fieldid-intvalue', XMLDB_INDEX_NOTUNIQUE, ['fieldid', 'intvalue']);
        $table->add_index('fieldid-shortcharvalue', XMLDB_INDEX_NOTUNIQUE, ['fieldid', 'shortcharvalue']);
        $table->add_index('fieldid-decvalue', XMLDB_INDEX_NOTUNIQUE, ['fieldid', 'decvalue']);

        // Conditionally launch create table for customfield_data.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_main_savepoint(true, 2019011801.00);
    }

    if ($oldversion < 2019011801.01) {

        // Delete all files that have been used in sections, which are already deleted.
        $sql = "SELECT DISTINCT f.itemid as sectionid, f.contextid
                  FROM {files} f
             LEFT JOIN {course_sections} s ON f.itemid = s.id
                 WHERE f.component = :component AND f.filearea = :filearea AND s.id IS NULL ";

        $params = [
            'component' => 'course',
            'filearea' => 'section'
        ];

        $stalefiles = $DB->get_recordset_sql($sql, $params);

        $fs = get_file_storage();
        foreach ($stalefiles as $stalefile) {
            $fs->delete_area_files($stalefile->contextid, 'course', 'section', $stalefile->sectionid);
        }
        $stalefiles->close();

        upgrade_main_savepoint(true, 2019011801.01);
    }

    if ($oldversion < 2019011801.02) {
        // Add index 'useridfrom' to the table 'notifications'.
        $table = new xmldb_table('notifications');
        $index = new xmldb_index('useridfrom', XMLDB_INDEX_NOTUNIQUE, ['useridfrom']);

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2019011801.02);
    }

    if ($oldversion < 2019011801.03) {
        // Remove duplicate entries from group memberships.
        // Find records with multiple userid/groupid combinations and find the highest ID.
        // Later we will remove all those entries.
        $sql = "
            SELECT MIN(id) as minid, userid, groupid
            FROM {groups_members}
            GROUP BY userid, groupid
            HAVING COUNT(id) > 1";
        if ($duplicatedrows = $DB->get_recordset_sql($sql)) {
            foreach ($duplicatedrows as $row) {
                $DB->delete_records_select('groups_members',
                    'userid = :userid AND groupid = :groupid AND id <> :minid', (array)$row);
            }
        }
        $duplicatedrows->close();

        // Define key useridgroupid (unique) to be added to group_members.
        $table = new xmldb_table('groups_members');
        $key = new xmldb_key('useridgroupid', XMLDB_KEY_UNIQUE, array('userid', 'groupid'));
        // Launch add key useridgroupid.
        $dbman->add_key($table, $key);
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019011801.03);
    }

    if ($oldversion < 2019021500.01) {
        $insights = $DB->get_record('message_providers', ['component' => 'moodle', 'name' => 'insights']);
        if (!empty($insights)) {
            $insights->capability = null;
            $DB->update_record('message_providers', $insights);
        }
        upgrade_main_savepoint(true, 2019021500.01);
    }

    if ($oldversion < 2019021500.02) {
        // Default 'off' for existing sites as this is the behaviour they had earlier.
        set_config('messagingdefaultpressenter', false);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019021500.02);
    }

    if ($oldversion < 2019030100.01) {
        // Create adhoc task to delete renamed My Course search area (ID core_course-mycourse).
        $record = new \stdClass();
        $record->classname = '\core\task\clean_up_deleted_search_area_task';
        $record->component = 'core';

        // Next run time based from nextruntime computation in \core\task\manager::queue_adhoc_task().
        $nextruntime = time() - 1;
        $record->nextruntime = $nextruntime;
        $record->customdata = json_encode('core_course-mycourse');

        $DB->insert_record('task_adhoc', $record);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019030100.01);
    }

    if ($oldversion < 2019030700.01) {

        // Define field evaluationmode to be added to analytics_models_log.
        $table = new xmldb_table('analytics_models_log');
        $field = new xmldb_field('evaluationmode', XMLDB_TYPE_CHAR, '50', null, null, null,
            null, 'version');

        // Conditionally launch add field evaluationmode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            $updatesql = "UPDATE {analytics_models_log}
                             SET evaluationmode = 'configuration'";
            $DB->execute($updatesql, []);

            // Changing nullability of field evaluationmode on table block_instances to not null.
            $field = new xmldb_field('evaluationmode', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL,
                null, null, 'version');

            // Launch change of nullability for field evaluationmode.
            $dbman->change_field_notnull($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019030700.01);
    }

    if ($oldversion < 2019030800.00) {
        // Define table 'message_conversation_actions' to be created.
        // Note - I would have preferred 'message_conversation_user_actions' but due to Oracle we can't. Boo.
        $table = new xmldb_table('message_conversation_actions');

        // Adding fields to table 'message_conversation_actions'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('conversationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table 'message_conversation_actions'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('conversationid', XMLDB_KEY_FOREIGN, ['conversationid'], 'message_conversations', ['id']);

        // Conditionally launch create table for 'message_conversation_actions'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019030800.00);
    }

    if ($oldversion < 2019030800.02) {
        // Remove any conversations and their members associated with non-existent groups.
        $sql = "SELECT mc.id
                  FROM {message_conversations} mc
             LEFT JOIN {groups} g
                    ON mc.itemid = g.id
                 WHERE mc.component = :component
                   AND mc.itemtype = :itemtype
                   AND g.id is NULL";
        $conversations = $DB->get_records_sql($sql, ['component' => 'core_group', 'itemtype' => 'groups']);

        if ($conversations) {
            $conversationids = array_keys($conversations);

            $DB->delete_records_list('message_conversations', 'id', $conversationids);
            $DB->delete_records_list('message_conversation_members', 'conversationid', $conversationids);
            $DB->delete_records_list('message_conversation_actions', 'conversationid', $conversationids);

            // Now, go through each conversation and delete any messages and related message actions.
            foreach ($conversationids as $conversationid) {
                if ($messages = $DB->get_records('messages', ['conversationid' => $conversationid])) {
                    $messageids = array_keys($messages);

                    // Delete the actions.
                    list($insql, $inparams) = $DB->get_in_or_equal($messageids);
                    $DB->delete_records_select('message_user_actions', "messageid $insql", $inparams);

                    // Delete the messages.
                    $DB->delete_records('messages', ['conversationid' => $conversationid]);
                }
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019030800.02);
    }

    if ($oldversion < 2019030800.03) {

        // Add missing indicators to course_dropout.
        $params = [
            'target' => '\core\analytics\target\course_dropout',
            'trained' => 0,
            'enabled' => 0,
        ];
        $models = $DB->get_records('analytics_models', $params);
        foreach ($models as $model) {
            $indicators = json_decode($model->indicators);

            $potentiallymissingindicators = [
                '\core_course\analytics\indicator\completion_enabled',
                '\core_course\analytics\indicator\potential_cognitive_depth',
                '\core_course\analytics\indicator\potential_social_breadth',
                '\core\analytics\indicator\any_access_after_end',
                '\core\analytics\indicator\any_access_before_start',
                '\core\analytics\indicator\any_write_action_in_course',
                '\core\analytics\indicator\read_actions'
            ];

            $missing = false;
            foreach ($potentiallymissingindicators as $potentiallymissingindicator) {
                if (!in_array($potentiallymissingindicator, $indicators)) {
                    // Add the missing indicator to sites upgraded before 2017072000.02.
                    $indicators[] = $potentiallymissingindicator;
                    $missing = true;
                }
            }

            if ($missing) {
                $model->indicators = json_encode($indicators);
                $model->version = time();
                $model->timemodified = time();
                $DB->update_record('analytics_models', $model);
            }
        }

        // Add missing indicators to no_teaching.
        $params = [
            'target' => '\core\analytics\target\no_teaching',
        ];
        $models = $DB->get_records('analytics_models', $params);
        foreach ($models as $model) {
            $indicators = json_decode($model->indicators);
            if (!in_array('\core_course\analytics\indicator\no_student', $indicators)) {
                // Add the missing indicator to sites upgraded before 2017072000.02.

                $indicators[] = '\core_course\analytics\indicator\no_student';

                $model->indicators = json_encode($indicators);
                $model->version = time();
                $model->timemodified = time();
                $DB->update_record('analytics_models', $model);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019030800.03);
    }

    if ($oldversion < 2019031500.01) {

        $defaulttimesplittings = get_config('analytics', 'timesplittings');
        if ($defaulttimesplittings !== false) {
            set_config('defaulttimesplittingsevaluation', $defaulttimesplittings, 'analytics');
            unset_config('timesplittings', 'analytics');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019031500.01);
    }

    if ($oldversion < 2019032200.02) {
        // The no_teaching model might have been marked as not-trained by mistake (static models are always trained).
        $DB->set_field('analytics_models', 'trained', 1, ['target' => '\core\analytics\target\no_teaching']);
        upgrade_main_savepoint(true, 2019032200.02);
    }

    if ($oldversion < 2019032900.00) {

        // Define table badge_competencies to be renamed to badge_alignment.
        $table = new xmldb_table('badge_competencies');

        // Be careful if this step gets run twice.
        if ($dbman->table_exists($table)) {
            $key = new xmldb_key('competenciesbadge', XMLDB_KEY_FOREIGN, ['badgeid'], 'badge', ['id']);

            // Launch drop key competenciesbadge.
            $dbman->drop_key($table, $key);

            $key = new xmldb_key('alignmentsbadge', XMLDB_KEY_FOREIGN, ['badgeid'], 'badge', ['id']);

            // Launch add key alignmentsbadge.
            $dbman->add_key($table, $key);

            // Launch rename table for badge_alignment.
            $dbman->rename_table($table, 'badge_alignment');
        }

        upgrade_main_savepoint(true, 2019032900.00);
    }

    if ($oldversion < 2019032900.01) {
        $sql = "UPDATE {task_scheduled}
                   SET classname = ?
                 WHERE component = ?
                   AND classname = ?";
        $DB->execute($sql, [
            '\core\task\question_preview_cleanup_task',
            'moodle',
            '\core\task\question_cron_task'
        ]);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019032900.01);
     }

    if ($oldversion < 2019040200.01) {
        // Removing the themes BSB, Clean, More from core.
        // If these theme wish to be retained empty this array before upgrade.
        $themes = array('theme_bootstrapbase' => 'bootstrapbase',
                'theme_clean' => 'clean', 'theme_more' => 'more');
        foreach ($themes as $key => $theme) {
            if (check_dir_exists($CFG->dirroot . '/theme/' . $theme, false)) {
                // Ignore the themes that have been re-downloaded.
                unset($themes[$key]);
            }
        }
        // Check we actually have themes to remove.
        if (count($themes) > 0) {
            list($insql, $inparams) = $DB->get_in_or_equal($themes, SQL_PARAMS_NAMED);

            // Replace the theme usage.
            $DB->set_field_select('course', 'theme', 'classic', "theme $insql", $inparams);
            $DB->set_field_select('course_categories', 'theme', 'classic', "theme $insql", $inparams);
            $DB->set_field_select('user', 'theme', 'classic', "theme $insql", $inparams);
            $DB->set_field_select('mnet_host', 'theme', 'classic', "theme $insql", $inparams);
            $DB->set_field_select('cohort', 'theme', 'classic', "theme $insql", $inparams);

            // Replace the theme configs.
            if (in_array(get_config('core', 'theme'), $themes)) {
                set_config('theme', 'classic');
            }
            if (in_array(get_config('core', 'thememobile'), $themes)) {
                set_config('thememobile', 'classic');
            }
            if (in_array(get_config('core', 'themelegacy'), $themes)) {
                set_config('themelegacy', 'classic');
            }
            if (in_array(get_config('core', 'themetablet'), $themes)) {
                set_config('themetablet', 'classic');
            }

            // Hacky emulation of plugin uninstallation.
            foreach ($themes as $key => $theme) {
                unset_all_config_for_plugin($key);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019040200.01);
    }

    if ($oldversion < 2019040600.02) {

        // Define key fileid (foreign) to be dropped form analytics_train_samples.
        $table = new xmldb_table('analytics_train_samples');
        $key = new xmldb_key('fileid', XMLDB_KEY_FOREIGN, ['fileid'], 'files', ['id']);

        // Launch drop key fileid.
        $dbman->drop_key($table, $key);

        // Define field fileid to be dropped from analytics_train_samples.
        $table = new xmldb_table('analytics_train_samples');
        $field = new xmldb_field('fileid');

        // Conditionally launch drop field fileid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019040600.02);
    }

    if ($oldversion < 2019040600.04) {
        // Define field and index to be added to backup_controllers.
        $table = new xmldb_table('backup_controllers');
        $field = new xmldb_field('progress', XMLDB_TYPE_NUMBER, '15, 14', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        $index = new xmldb_index('useritem_ix', XMLDB_INDEX_NOTUNIQUE, ['userid', 'itemid']);
        // Conditionally launch add field progress.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Conditionally launch add index useritem_ix.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019040600.04);
    }

    if ($oldversion < 2019041000.02) {

        // Define field fullmessagetrust to be added to messages.
        $table = new xmldb_table('messages');
        $field = new xmldb_field('fullmessagetrust', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'timecreated');

        // Conditionally launch add field fullmessagetrust.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019041000.02);
    }

    if ($oldversion < 2019041300.01) {
        // Add the field 'name' to the 'analytics_models' table.
        $table = new xmldb_table('analytics_models');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'trained');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019041300.01);
    }

    if ($oldversion < 2019041800.01) {
        // STEP 1. For the existing and migrated self-conversations, set the type to the new MESSAGE_CONVERSATION_TYPE_SELF, update
        // the convhash and star them.
        $sql = "SELECT mcm.conversationid, mcm.userid, MAX(mcm.id) as maxid
                  FROM {message_conversation_members} mcm
            INNER JOIN {user} u ON mcm.userid = u.id
                 WHERE u.deleted = 0
              GROUP BY mcm.conversationid, mcm.userid
                HAVING COUNT(*) > 1";
        $selfconversationsrs = $DB->get_recordset_sql($sql);
        $maxids = [];
        foreach ($selfconversationsrs as $selfconversation) {
            $DB->update_record('message_conversations',
                ['id' => $selfconversation->conversationid,
                 'type' => \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF,
                 'convhash' => \core_message\helper::get_conversation_hash([$selfconversation->userid])
                ]
            );

            // Star the existing self-conversation.
            $favouriterecord = new \stdClass();
            $favouriterecord->component = 'core_message';
            $favouriterecord->itemtype = 'message_conversations';
            $favouriterecord->itemid = $selfconversation->conversationid;
            $userctx = \context_user::instance($selfconversation->userid);
            $favouriterecord->contextid = $userctx->id;
            $favouriterecord->userid = $selfconversation->userid;
            if (!$DB->record_exists('favourite', (array)$favouriterecord)) {
                $favouriterecord->timecreated = time();
                $favouriterecord->timemodified = $favouriterecord->timecreated;
                $DB->insert_record('favourite', $favouriterecord);
            }

            // Set the self-conversation member with maxid to remove it later.
            $maxids[] = $selfconversation->maxid;
        }
        $selfconversationsrs->close();

        // Remove the repeated member with the higher id for all the existing self-conversations.
        if (!empty($maxids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($maxids);
            $DB->delete_records_select('message_conversation_members', "id $insql", $inparams);
        }

        // STEP 2. Migrate existing self-conversation relying on old message tables, setting the type to the new
        // MESSAGE_CONVERSATION_TYPE_SELF and the convhash to the proper one. Star them also.

        // On the messaging legacy tables, self-conversations are only present in the 'message_read' table, so we don't need to
        // check the content in the 'message' table.
        $sql = "SELECT mr.*
                  FROM {message_read} mr
            INNER JOIN {user} u ON mr.useridfrom = u.id
                 WHERE mr.useridfrom = mr.useridto AND mr.notification = 0 AND u.deleted = 0";
        $legacyselfmessagesrs = $DB->get_recordset_sql($sql);
        foreach ($legacyselfmessagesrs as $message) {
            // Get the self-conversation or create and star it if doesn't exist.
            $conditions = [
                'type' => \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF,
                'convhash' => \core_message\helper::get_conversation_hash([$message->useridfrom])
            ];
            $selfconversation = $DB->get_record('message_conversations', $conditions);
            if (empty($selfconversation)) {
                // Create the self-conversation.
                $selfconversation = new \stdClass();
                $selfconversation->type = \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF;
                $selfconversation->convhash = \core_message\helper::get_conversation_hash([$message->useridfrom]);
                $selfconversation->enabled = 1;
                $selfconversation->timecreated = time();
                $selfconversation->timemodified = $selfconversation->timecreated;

                $selfconversation->id = $DB->insert_record('message_conversations', $selfconversation);

                // Add user to this self-conversation.
                $member = new \stdClass();
                $member->conversationid = $selfconversation->id;
                $member->userid = $message->useridfrom;
                $member->timecreated = time();

                $member->id = $DB->insert_record('message_conversation_members', $member);

                // Star the self-conversation.
                $favouriterecord = new \stdClass();
                $favouriterecord->component = 'core_message';
                $favouriterecord->itemtype = 'message_conversations';
                $favouriterecord->itemid = $selfconversation->id;
                $userctx = \context_user::instance($message->useridfrom);
                $favouriterecord->contextid = $userctx->id;
                $favouriterecord->userid = $message->useridfrom;
                if (!$DB->record_exists('favourite', (array)$favouriterecord)) {
                    $favouriterecord->timecreated = time();
                    $favouriterecord->timemodified = $favouriterecord->timecreated;
                    $DB->insert_record('favourite', $favouriterecord);
                }
            }

            // Create the object we will be inserting into the database.
            $tabledata = new \stdClass();
            $tabledata->useridfrom = $message->useridfrom;
            $tabledata->conversationid = $selfconversation->id;
            $tabledata->subject = $message->subject;
            $tabledata->fullmessage = $message->fullmessage;
            $tabledata->fullmessageformat = $message->fullmessageformat ?? FORMAT_MOODLE;
            $tabledata->fullmessagehtml = $message->fullmessagehtml;
            $tabledata->smallmessage = $message->smallmessage;
            $tabledata->timecreated = $message->timecreated;

            $messageid = $DB->insert_record('messages', $tabledata);

            // Check if we need to mark this message as deleted (self-conversations add this information on the
            // timeuserfromdeleted field.
            if ($message->timeuserfromdeleted) {
                $mua = new \stdClass();
                $mua->userid = $message->useridfrom;
                $mua->messageid = $messageid;
                $mua->action = \core_message\api::MESSAGE_ACTION_DELETED;
                $mua->timecreated = $message->timeuserfromdeleted;

                $DB->insert_record('message_user_actions', $mua);
            }

            // Mark this message as read.
            $mua = new \stdClass();
            $mua->userid = $message->useridto;
            $mua->messageid = $messageid;
            $mua->action = \core_message\api::MESSAGE_ACTION_READ;
            $mua->timecreated = $message->timeread;

            $DB->insert_record('message_user_actions', $mua);

            // The self-conversation message has been migrated. Delete the record from the legacy table as soon as possible
            // to avoid migrate it twice.
            $DB->delete_records('message_read', ['id' => $message->id]);
        }
        $legacyselfmessagesrs->close();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019041800.01);
    }

    if ($oldversion < 2019042200.01) {

        // Define table role_sortorder to be dropped.
        $table = new xmldb_table('role_sortorder');

        // Conditionally launch drop table for role_sortorder.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019042200.01);
    }

    if ($oldversion < 2019042200.02) {

        // Let's update all (old core) targets to their new (core_course) locations.
        $targets = [
            '\core\analytics\target\course_competencies' => '\core_course\analytics\target\course_competencies',
            '\core\analytics\target\course_completion' => '\core_course\analytics\target\course_completion',
            '\core\analytics\target\course_dropout' => '\core_course\analytics\target\course_dropout',
            '\core\analytics\target\course_gradetopass' => '\core_course\analytics\target\course_gradetopass',
            '\core\analytics\target\no_teaching' => '\core_course\analytics\target\no_teaching',
        ];

        foreach ($targets as $oldclass => $newclass) {
            $DB->set_field('analytics_models', 'target', $newclass, ['target' => $oldclass]);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019042200.02);
    }

    if ($oldversion < 2019042300.01) {
        $sql = "UPDATE {capabilities}
                   SET name = ?,
                       contextlevel = ?
                 WHERE name = ?";
        $DB->execute($sql, ['moodle/category:viewcourselist', CONTEXT_COURSECAT, 'moodle/course:browse']);

        $sql = "UPDATE {role_capabilities}
                   SET capability = ?
                 WHERE capability = ?";
        $DB->execute($sql, ['moodle/category:viewcourselist', 'moodle/course:browse']);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019042300.01);
    }

    if ($oldversion < 2019042300.03) {

        // Add new customdata field to message table.
        $table = new xmldb_table('message');
        $field = new xmldb_field('customdata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'eventtype');

        // Conditionally launch add field output.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add new customdata field to notifications and messages table.
        $table = new xmldb_table('notifications');
        $field = new xmldb_field('customdata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timecreated');

        // Conditionally launch add field output.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('messages');
        // Conditionally launch add field output.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019042300.03);
    }

    if ($oldversion < 2019042700.01) {

        // Define field firstanalysis to be added to analytics_used_analysables.
        $table = new xmldb_table('analytics_used_analysables');

        // Declaring it as null initially (although it is NOT NULL).
        $field = new xmldb_field('firstanalysis', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'analysableid');

        // Conditionally launch add field firstanalysis.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Set existing values to the current timeanalysed value.
            $recordset = $DB->get_recordset('analytics_used_analysables');
            foreach ($recordset as $record) {
                $record->firstanalysis = $record->timeanalysed;
                $DB->update_record('analytics_used_analysables', $record);
            }
            $recordset->close();

            // Now make the field 'NOT NULL'.
            $field = new xmldb_field('firstanalysis', XMLDB_TYPE_INTEGER, '10',
                null, XMLDB_NOTNULL, null, null, 'analysableid');
            $dbman->change_field_notnull($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019042700.01);
    }

    if ($oldversion < 2019050300.01) {
        // Delete all stale favourite records which were left behind when a course was deleted.
        $params = ['component' => 'core_message', 'itemtype' => 'message_conversations'];
        $sql = "SELECT fav.id as id
                  FROM {favourite} fav
             LEFT JOIN {context} ctx ON (ctx.id = fav.contextid)
                 WHERE fav.component = :component
                       AND fav.itemtype = :itemtype
                       AND ctx.id IS NULL";

        if ($records = $DB->get_fieldset_sql($sql, $params)) {
            // Just for safety, delete by chunks.
            $chunks = array_chunk($records, 1000);
            foreach ($chunks as $chunk) {
                list($insql, $inparams) = $DB->get_in_or_equal($chunk);
                $DB->delete_records_select('favourite', "id $insql", $inparams);
            }
        }

        upgrade_main_savepoint(true, 2019050300.01);
    }

    if ($oldversion < 2019050600.00) {

        // Define field apiversion to be added to badge_backpack.
        $table = new xmldb_table('badge_backpack');
        $field = new xmldb_field('apiversion', XMLDB_TYPE_CHAR, '12', null, XMLDB_NOTNULL, null, '1.0', 'password');

        // Conditionally launch add field apiversion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table badge_external_backpack to be created.
        $table = new xmldb_table('badge_external_backpack');

        // Adding fields to table badge_external_backpack.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('backpackapiurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('backpackweburl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('apiversion', XMLDB_TYPE_CHAR, '12', null, XMLDB_NOTNULL, null, '1.0');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table badge_external_backpack.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('backpackapiurlkey', XMLDB_KEY_UNIQUE, ['backpackapiurl']);
        $table->add_key('backpackweburlkey', XMLDB_KEY_UNIQUE, ['backpackweburl']);

        // Conditionally launch create table for badge_external_backpack.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field entityid to be added to badge_external.
        $table = new xmldb_table('badge_external');
        $field = new xmldb_field('entityid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'collectionid');

        // Conditionally launch add field entityid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table badge_external_identifier to be created.
        $table = new xmldb_table('badge_external_identifier');

        // Adding fields to table badge_external_identifier.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sitebackpackid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('internalid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('externalid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table badge_external_identifier.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_backpackid', XMLDB_KEY_FOREIGN, ['sitebackpackid'], 'badge_backpack', ['id']);
        $table->add_key('backpack-internal-external', XMLDB_KEY_UNIQUE, ['sitebackpackid', 'internalid', 'externalid', 'type']);

        // Conditionally launch create table for badge_external_identifier.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field externalbackpackid to be added to badge_backpack.
        $table = new xmldb_table('badge_backpack');
        $field = new xmldb_field('externalbackpackid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'password');

        // Conditionally launch add field externalbackpackid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define key externalbackpack (foreign) to be added to badge_backpack.
        $key = new xmldb_key('externalbackpack', XMLDB_KEY_FOREIGN, ['externalbackpackid'], 'badge_external_backpack', ['id']);

        // Launch add key externalbackpack.
        $dbman->add_key($table, $key);

        $field = new xmldb_field('apiversion');

        // Conditionally launch drop field apiversion.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('backpackurl');

        // Conditionally launch drop field backpackurl.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Add default backpacks.
        require_once($CFG->dirroot . '/badges/upgradelib.php'); // Core install and upgrade related functions only for badges.
        badges_install_default_backpacks();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019050600.00);
    }

    if ($oldversion < 2019051300.01) {
        $DB->set_field('analytics_models', 'enabled', '1', ['target' => '\core_user\analytics\target\upcoming_activities_due']);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019051300.01);
    }

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019060600.02) {
        // Renaming 'opentogoogle' config to 'opentowebcrawlers'.
        $opentogooglevalue = get_config('core', 'opentogoogle');

        // Move the value over if it was previously configured.
        if ($opentogooglevalue !== false) {
            set_config('opentowebcrawlers', $opentogooglevalue);
        }

        // Remove the now unused value.
        unset_config('opentogoogle');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019060600.02);
    }

    if ($oldversion < 2019062900.00) {
        // Debugsmtp is now only available via config.php.
        $DB->delete_records('config', array('name' => 'debugsmtp'));

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019062900.00);
    }

    if ($oldversion < 2019070400.01) {

        $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894',
            '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];

        $colornr = 1;
        foreach ($basecolors as $color) {
            set_config('coursecolor' .  $colornr, $color, 'core_admin');
            $colornr++;
        }

        upgrade_main_savepoint(true, 2019070400.01);
    }

    if ($oldversion < 2019072200.00) {

        // Define field relativedatesmode to be added to course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('relativedatesmode', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'enddate');

        // Conditionally launch add field relativedatesmode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019072200.00);
    }

    if ($oldversion < 2019072500.01) {
        // Remove the "popup" processor from the list of default processors for the messagecontactrequests notification.
        $oldloggedinconfig = get_config('message', 'message_provider_moodle_messagecontactrequests_loggedin');
        $oldloggedoffconfig = get_config('message', 'message_provider_moodle_messagecontactrequests_loggedoff');
        $newloggedinconfig = implode(',', array_filter(explode(',', $oldloggedinconfig), function($value) {
            return $value != 'popup';
        }));
        $newloggedoffconfig = implode(',', array_filter(explode(',', $oldloggedoffconfig), function($value) {
            return $value != 'popup';
        }));
        set_config('message_provider_moodle_messagecontactrequests_loggedin', $newloggedinconfig, 'message');
        set_config('message_provider_moodle_messagecontactrequests_loggedoff', $newloggedoffconfig, 'message');

        upgrade_main_savepoint(true, 2019072500.01);
    }

    if ($oldversion < 2019072500.03) {
        unset_config('httpswwwroot');

        upgrade_main_savepoint(true, 2019072500.03);
    }

    if ($oldversion < 2019073100.00) {
        // Update the empty tag instructions to null.
        $instructions = get_config('core', 'auth_instructions');

        if (trim(html_to_text($instructions)) === '') {
            set_config('auth_instructions', '');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019073100.00);
    }

    if ($oldversion < 2019083000.01) {

        // If block_community is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/blocks/community/communitycourse.php')) {
            // Drop table that is no longer needed.
            $table = new xmldb_table('block_community');
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }

            // Delete instances.
            $instances = $DB->get_records_list('block_instances', 'blockname', ['community']);
            $instanceids = array_keys($instances);

            if (!empty($instanceids)) {
                $DB->delete_records_list('block_positions', 'blockinstanceid', $instanceids);
                $DB->delete_records_list('block_instances', 'id', $instanceids);
                list($sql, $params) = $DB->get_in_or_equal($instanceids, SQL_PARAMS_NAMED);
                $params['contextlevel'] = CONTEXT_BLOCK;
                $DB->delete_records_select('context', "contextlevel=:contextlevel AND instanceid " . $sql, $params);

                $preferences = array();
                foreach ($instances as $instanceid => $instance) {
                    $preferences[] = 'block' . $instanceid . 'hidden';
                    $preferences[] = 'docked_block_instance_' . $instanceid;
                }
                $DB->delete_records_list('user_preferences', 'name', $preferences);
            }

            // Delete the block from the block table.
            $DB->delete_records('block', array('name' => 'community'));

            // Remove capabilities.
            capabilities_cleanup('block_community');
            // Clean config.
            unset_all_config_for_plugin('block_community');

            // Remove Moodle-level community based capabilities.
            $capabilitiestoberemoved = ['block/community:addinstance', 'block/community:myaddinstance'];
            // Delete any role_capabilities for the old roles.
            $DB->delete_records_list('role_capabilities', 'capability', $capabilitiestoberemoved);
            // Delete the capability itself.
            $DB->delete_records_list('capabilities', 'name', $capabilitiestoberemoved);
        }

        upgrade_main_savepoint(true, 2019083000.01);
    }

    if ($oldversion < 2019083000.02) {
        // Remove unused config.
        unset_config('enablecoursepublishing');
        upgrade_main_savepoint(true, 2019083000.02);
    }

    if ($oldversion < 2019083000.04) {
        // Delete "orphaned" subscriptions.
        $sql = "SELECT DISTINCT es.userid
                  FROM {event_subscriptions} es
             LEFT JOIN {user} u ON u.id = es.userid
                 WHERE u.deleted = 1 OR u.id IS NULL";
        $deletedusers = $DB->get_fieldset_sql($sql);
        if ($deletedusers) {
            list($sql, $params) = $DB->get_in_or_equal($deletedusers);

            // Delete orphaned subscriptions.
            $DB->execute("DELETE FROM {event_subscriptions} WHERE userid " . $sql, $params);
        }

        upgrade_main_savepoint(true, 2019083000.04);
    }

    if ($oldversion < 2019090500.01) {

        // Define index analysableid (not unique) to be added to analytics_used_analysables.
        $table = new xmldb_table('analytics_used_analysables');
        $index = new xmldb_index('analysableid', XMLDB_INDEX_NOTUNIQUE, ['analysableid']);

        // Conditionally launch add index analysableid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019090500.01);
    }

    if ($oldversion < 2019092700.01) {
        upgrade_rename_prediction_actions_useful_incorrectly_flagged();
        upgrade_main_savepoint(true, 2019092700.01);
    }

    if ($oldversion < 2019100800.02) {
        // Rename the official moodle sites directory the site is registered with.
        $DB->execute("UPDATE {registration_hubs}
                         SET hubname = ?, huburl = ?
                       WHERE huburl = ?", ['moodle', 'https://stats.moodle.org', 'https://moodle.net']);

        // Convert the hub site specific settings to the new naming format without the hub URL in the name.
        $hubconfig = get_config('hub');

        if (!empty($hubconfig)) {
            foreach (upgrade_convert_hub_config_site_param_names($hubconfig, 'https://moodle.net') as $name => $value) {
                set_config($name, $value, 'hub');
            }
        }

        upgrade_main_savepoint(true, 2019100800.02);
    }

    if ($oldversion < 2019100900.00) {
        // If block_participants is no longer present, remove it.
        if (!file_exists($CFG->dirroot . '/blocks/participants/block_participants.php')) {
            // Delete instances.
            $instances = $DB->get_records_list('block_instances', 'blockname', ['participants']);
            $instanceids = array_keys($instances);

            if (!empty($instanceids)) {
                $DB->delete_records_list('block_positions', 'blockinstanceid', $instanceids);
                $DB->delete_records_list('block_instances', 'id', $instanceids);
                list($sql, $params) = $DB->get_in_or_equal($instanceids, SQL_PARAMS_NAMED);
                $params['contextlevel'] = CONTEXT_BLOCK;
                $DB->delete_records_select('context', "contextlevel=:contextlevel AND instanceid " . $sql, $params);

                $preferences = array();
                foreach ($instances as $instanceid => $instance) {
                    $preferences[] = 'block' . $instanceid . 'hidden';
                    $preferences[] = 'docked_block_instance_' . $instanceid;
                }
                $DB->delete_records_list('user_preferences', 'name', $preferences);
            }

            // Delete the block from the block table.
            $DB->delete_records('block', array('name' => 'participants'));

            // Remove capabilities.
            capabilities_cleanup('block_participants');

            // Clean config.
            unset_all_config_for_plugin('block_participants');
        }

        upgrade_main_savepoint(true, 2019100900.00);
    }

    if ($oldversion < 2019101600.01) {

        // Change the setting $CFG->requestcategoryselection into $CFG->lockrequestcategory with opposite value.
        set_config('lockrequestcategory', empty($CFG->requestcategoryselection));

        upgrade_main_savepoint(true, 2019101600.01);
    }

    if ($oldversion < 2019101800.02) {

        // Get the table by its previous name.
        $table = new xmldb_table('analytics_models');
        if ($dbman->table_exists($table)) {

            // Define field contextids to be added to analytics_models.
            $field = new xmldb_field('contextids', XMLDB_TYPE_TEXT, null, null, null, null, null, 'version');

            // Conditionally launch add field contextids.
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019101800.02);
    }

    if ($oldversion < 2019102500.04) {
        // Define table h5p_libraries to be created.
        $table = new xmldb_table('h5p_libraries');

        // Adding fields to table h5p_libraries.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('machinename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('majorversion', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('minorversion', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('patchversion', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('runnable', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fullscreen', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('embedtypes', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('preloadedjs', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('preloadedcss', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('droplibrarycss', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('semantics', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('addto', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table h5p_libraries.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table h5p_libraries.
        $table->add_index('machinemajorminorpatch', XMLDB_INDEX_NOTUNIQUE,
            ['machinename', 'majorversion', 'minorversion', 'patchversion', 'runnable']);

        // Conditionally launch create table for h5p_libraries.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table h5p_library_dependencies to be created.
        $table = new xmldb_table('h5p_library_dependencies');

        // Adding fields to table h5p_library_dependencies.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('libraryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('requiredlibraryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dependencytype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table h5p_library_dependencies.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('libraryid', XMLDB_KEY_FOREIGN, ['libraryid'], 'h5p_libraries', ['id']);
        $table->add_key('requiredlibraryid', XMLDB_KEY_FOREIGN, ['requiredlibraryid'], 'h5p_libraries', ['id']);

        // Conditionally launch create table for h5p_library_dependencies.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table h5p to be created.
        $table = new xmldb_table('h5p');

        // Adding fields to table h5p.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('jsoncontent', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('mainlibraryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('displayoptions', XMLDB_TYPE_INTEGER, '4', null, null, null, null);
        $table->add_field('pathnamehash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filtered', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table h5p.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('mainlibraryid', XMLDB_KEY_FOREIGN, ['mainlibraryid'], 'h5p_libraries', ['id']);

        // Conditionally launch create table for h5p.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table h5p_contents_libraries to be created.
        $table = new xmldb_table('h5p_contents_libraries');

        // Adding fields to table h5p_contents_libraries.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('h5pid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('libraryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dependencytype', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dropcss', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('weight', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table h5p_contents_libraries.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('h5pid', XMLDB_KEY_FOREIGN, ['h5pid'], 'h5p', ['id']);
        $table->add_key('libraryid', XMLDB_KEY_FOREIGN, ['libraryid'], 'h5p_libraries', ['id']);

        // Conditionally launch create table for h5p_contents_libraries.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table h5p_libraries_cachedassets to be created.
        $table = new xmldb_table('h5p_libraries_cachedassets');

        // Adding fields to table h5p_libraries_cachedassets.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('libraryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hash', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table h5p_libraries_cachedassets.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('libraryid', XMLDB_KEY_FOREIGN, ['libraryid'], 'h5p_libraries_cachedassets', ['id']);

        // Conditionally launch create table for h5p_libraries_cachedassets.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019102500.04);
    }

    if ($oldversion < 2019103000.13) {

        upgrade_analytics_fix_contextids_defaults();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019103000.13);
    }

    if ($oldversion < 2019111300.00) {

        // Define field coremajor to be added to h5p_libraries.
        $table = new xmldb_table('h5p_libraries');
        $field = new xmldb_field('coremajor', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'addto');

        // Conditionally launch add field coremajor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('coreminor', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'coremajor');

        // Conditionally launch add field coreminor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019111300.00);
    }

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019120500.01) {
        // Delete any role assignments for roles which no longer exist.
        $DB->delete_records_select('role_assignments', "roleid NOT IN (SELECT id FROM {role})");

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2019120500.01);
    }

    if ($oldversion < 2019121800.00) {
        // Upgrade MIME types for existing streaming files.
        $filetypes = array(
            '%.fmp4' => 'video/mp4',
            '%.ts' => 'video/MP2T',
            '%.mpd' => 'application/dash+xml',
            '%.m3u8' => 'application/x-mpegURL',
        );

        $select = $DB->sql_like('filename', '?', false);
        foreach ($filetypes as $extension => $mimetype) {
            $DB->set_field_select(
                'files',
                'mimetype',
                $mimetype,
                $select,
                array($extension)
            );
        }

        upgrade_main_savepoint(true, 2019121800.00);
    }

    if ($oldversion < 2019122000.01) {
        // Clean old upgrade setting not used anymore.
        unset_config('linkcoursesectionsupgradescriptwasrun');
        upgrade_main_savepoint(true, 2019122000.01);
    }

    if ($oldversion < 2020010900.02) {
        $table = new xmldb_table('event');

        // This index will improve the performance when the Events API retrieves category and group events.
        $index = new xmldb_index('eventtype', XMLDB_INDEX_NOTUNIQUE, ['eventtype']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // This index improves the performance of backups, deletion and visibilty changes on activities.
        $index = new xmldb_index('modulename-instance', XMLDB_INDEX_NOTUNIQUE, ['modulename', 'instance']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2020010900.02);
    }

    if ($oldversion < 2020011700.02) {
        // Delete all orphaned subscription events.
        $select = "subscriptionid IS NOT NULL
                   AND subscriptionid NOT IN (SELECT id from {event_subscriptions})";
        $DB->delete_records_select('event', $select);

        upgrade_main_savepoint(true, 2020011700.02);
    }

    if ($oldversion < 2020013000.01) {
        global $DB;
        // Delete any associated files.
        $fs = get_file_storage();
        $sql = "SELECT cuc.id, cuc.userid
                  FROM {competency_usercomp} cuc
             LEFT JOIN {user} u ON cuc.userid = u.id
                 WHERE u.deleted = 1";
        $usercompetencies = $DB->get_records_sql($sql);
        foreach ($usercompetencies as $usercomp) {
            $DB->delete_records('competency_evidence', ['usercompetencyid' => $usercomp->id]);
            $DB->delete_records('competency_usercompcourse', ['userid' => $usercomp->userid]);
            $DB->delete_records('competency_usercompplan', ['userid' => $usercomp->userid]);
            $DB->delete_records('competency_usercomp', ['userid' => $usercomp->userid]);
        }

        $sql = "SELECT cue.id, cue.userid
                  FROM {competency_userevidence} cue
             LEFT JOIN {user} u ON cue.userid = u.id
                 WHERE u.deleted = 1";
        $userevidences = $DB->get_records_sql($sql);
        foreach ($userevidences as $userevidence) {
            $DB->delete_records('competency_userevidencecomp', ['userevidenceid' => $userevidence->id]);
            $DB->delete_records('competency_userevidence', ['id' => $userevidence->id]);

            if ($record = $DB->get_record('context', ['contextlevel' => CONTEXT_USER, 'instanceid' => $userevidence->userid],
                    '*', IGNORE_MISSING)) {
                // Delete all orphaned user evidences files.
                $fs->delete_area_files($record->id, 'core_competency', 'userevidence', $userevidence->userid);
            }
        }

        $sql = "SELECT cp.id
                  FROM {competency_plan} cp
             LEFT JOIN {user} u ON cp.userid = u.id
                 WHERE u.deleted = 1";
        $userplans = $DB->get_records_sql($sql);
        foreach ($userplans as $userplan) {
            $DB->delete_records('competency_plancomp', ['planid' => $userplan->id]);
            $DB->delete_records('competency_plan', ['id' => $userplan->id]);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020013000.01);
    }

    if ($oldversion < 2020040200.01) {
        // Clean up completion criteria records referring to courses that no longer exist.
        $select = 'criteriatype = :type AND courseinstance NOT IN (SELECT id FROM {course})';
        $params = ['type' => 8]; // COMPLETION_CRITERIA_TYPE_COURSE.

        $DB->delete_records_select('course_completion_criteria', $select, $params);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020040200.01);
    }

    if ($oldversion < 2020040700.00) {
        // Remove deprecated Mozilla OpenBadges backpack.
        $url = 'https://backpack.openbadges.org';
        $bp = $DB->get_record('badge_external_backpack', ['backpackapiurl' => $url]);
        if ($bp) {
            // Remove connections for users to this backpack.
            $sql = "SELECT DISTINCT bb.id
                      FROM {badge_backpack} bb
                 LEFT JOIN {badge_external} be ON be. backpackid = bb.externalbackpackid
                     WHERE bb.externalbackpackid = :backpackid";
            $params = ['backpackid' => $bp->id];
            $externalbackpacks = $DB->get_fieldset_sql($sql, $params);
            if ($externalbackpacks) {
                list($sql, $params) = $DB->get_in_or_equal($externalbackpacks);

                // Delete user external collections references to this backpack.
                $DB->execute("DELETE FROM {badge_external} WHERE backpackid " . $sql, $params);
            }
            $DB->delete_records('badge_backpack', ['externalbackpackid' => $bp->id]);

            // Delete deprecated backpack entry.
            $DB->delete_records('badge_external_backpack', ['backpackapiurl' => $url]);
        }

        // Set active external backpack to Badgr.io.
        $url = 'https://api.badgr.io/v2';
        if ($bp = $DB->get_record('badge_external_backpack', ['backpackapiurl' => $url])) {
            set_config('badges_site_backpack', $bp->id);
        } else {
            unset_config('badges_site_backpack');
        }

        upgrade_main_savepoint(true, 2020040700.00);
    }

    if ($oldversion < 2020041500.00) {
        // Define table to store contentbank contents.
        $table = new xmldb_table('contentbank_content');

        // Adding fields to table content_bank.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contenttype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('configdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table contentbank_content.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_key('usercreated', XMLDB_KEY_FOREIGN, ['usercreated'], 'user', ['id']);

        // Adding indexes to table contentbank_content.
        $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, ['name']);
        $table->add_index('instance', XMLDB_INDEX_NOTUNIQUE, ['contextid', 'contenttype', 'instanceid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020041500.00);
    }

    if ($oldversion < 2020041700.01) {
        // Upgrade h5p MIME type for existing h5p files.
        $select = $DB->sql_like('filename', '?', false);
        $DB->set_field_select(
            'files',
            'mimetype',
            'application/zip.h5p',
            $select,
            array('%.h5p')
        );

        upgrade_main_savepoint(true, 2020041700.01);
    }

    if ($oldversion < 2020042800.01) {
        // Delete obsolete config value.
        unset_config('enablesafebrowserintegration');
        // Clean up config of the old plugin.
        unset_all_config_for_plugin('quizaccess_safebrowser');

        upgrade_main_savepoint(true, 2020042800.01);
    }

    if ($oldversion < 2020051900.01) {
        // Define field component to be added to event.
        $table = new xmldb_table('event');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'repeatid');

        // Conditionally launch add field component.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index component (not unique) to be added to event.
        $table = new xmldb_table('event');
        $index = new xmldb_index('component', XMLDB_INDEX_NOTUNIQUE, ['component', 'eventtype', 'instance']);

        // Conditionally launch add index component.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020051900.01);
    }

    if ($oldversion < 2020052000.00) {
        // Define table badge_backpack_oauth2 to be created.
        $table = new xmldb_table('badge_backpack_oauth2');

        // Adding fields to table badge_backpack_oauth2.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('externalbackpackid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('refreshtoken', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('scope', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table badge_backpack_oauth2.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('issuerid', XMLDB_KEY_FOREIGN, ['issuerid'], 'oauth2_issuer', ['id']);
        $table->add_key('externalbackpackid', XMLDB_KEY_FOREIGN, ['externalbackpackid'], 'badge_external_backpack', ['id']);
        // Conditionally launch create table for badge_backpack_oauth2.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field oauth2_issuerid to be added to badge_external_backpack.
        $tablebadgeexternalbackpack = new xmldb_table('badge_external_backpack');
        $fieldoauth2issuerid = new xmldb_field('oauth2_issuerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'password');
        $keybackpackoauth2key = new xmldb_key('backpackoauth2key', XMLDB_KEY_FOREIGN, ['oauth2_issuerid'], 'oauth2_issuer', ['id']);

        // Conditionally launch add field oauth2_issuerid.
        if (!$dbman->field_exists($tablebadgeexternalbackpack, $fieldoauth2issuerid)) {
            $dbman->add_field($tablebadgeexternalbackpack, $fieldoauth2issuerid);

            // Launch add key backpackoauth2key.
            $dbman->add_key($tablebadgeexternalbackpack, $keybackpackoauth2key);
        }

        // Define field assertion to be added to badge_external.
        $tablebadgeexternal = new xmldb_table('badge_external');
        $fieldassertion = new xmldb_field('assertion', XMLDB_TYPE_TEXT, null, null, null, null, null, 'entityid');

        // Conditionally launch add field assertion.
        if (!$dbman->field_exists($tablebadgeexternal, $fieldassertion)) {
            $dbman->add_field($tablebadgeexternal, $fieldassertion);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020052000.00);
    }

    if ($oldversion < 2020052200.01) {

        // Define field custom to be added to license.
        $table = new xmldb_table('license');
        $field = new xmldb_field('custom', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Conditionally launch add field custom.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field sortorder to be added to license.
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0');

        // Conditionally launch add field sortorder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index license (not unique) to be added to files.
        $table = new xmldb_table('files');
        $index = new xmldb_index('license', XMLDB_INDEX_NOTUNIQUE, ['license']);

        // Conditionally launch add index license.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Upgrade the core license details.
        upgrade_core_licenses();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020052200.01);
    }

    if ($oldversion < 2020060500.01) {
        // Define field moodlenetprofile to be added to user.
        $table = new xmldb_table('user');
        $field = new xmldb_field('moodlenetprofile', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'alternatename');

        // Conditionally launch add field moodlenetprofile.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020060500.01);
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2020061500.02) {
        // Update default digital age consent map according to the current legislation on each country.

        // The default age of digital consent map for 38 and below.
        $oldageofdigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'AT, 14',
            'ES, 14',
            'US, 13'
        ]);

        // Check if the current age of digital consent map matches the old one.
        if (get_config('moodle', 'agedigitalconsentmap') === $oldageofdigitalconsentmap) {
            // If the site is still using the old defaults, upgrade to the new default.
            $ageofdigitalconsentmap = implode(PHP_EOL, [
                '*, 16',
                'AT, 14',
                'BE, 13',
                'BG, 14',
                'CY, 14',
                'CZ, 15',
                'DK, 13',
                'EE, 13',
                'ES, 14',
                'FI, 13',
                'FR, 15',
                'GB, 13',
                'GR, 15',
                'IT, 14',
                'LT, 14',
                'LV, 13',
                'MT, 13',
                'NO, 13',
                'PT, 13',
                'SE, 13',
                'US, 13'
            ]);
            set_config('agedigitalconsentmap', $ageofdigitalconsentmap);
        }

        upgrade_main_savepoint(true, 2020061500.02);
    }

    if ($oldversion < 2020062600.01) {
        // Add index to the token field in the external_tokens table.
        $table = new xmldb_table('external_tokens');
        $index = new xmldb_index('token', XMLDB_INDEX_NOTUNIQUE, ['token']);

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2020062600.01);
    }

    if ($oldversion < 2020071100.01) {
        // Clean up completion criteria records referring to NULL course prerequisites.
        $select = 'criteriatype = :type AND courseinstance IS NULL';
        $params = ['type' => 8]; // COMPLETION_CRITERIA_TYPE_COURSE.

        $DB->delete_records_select('course_completion_criteria', $select, $params);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020071100.01);
    }

    if ($oldversion < 2020072300.01) {
        // Restore and set the guest user if it has been previously removed via GDPR, or set to an nonexistent
        // user account.
        $currentguestuser = $DB->get_record('user', array('id' => $CFG->siteguest));

        if (!$currentguestuser) {
            if (!$guest = $DB->get_record('user', array('username' => 'guest', 'mnethostid' => $CFG->mnet_localhost_id))) {
                // Create a guest user account.
                $guest = new stdClass();
                $guest->auth        = 'manual';
                $guest->username    = 'guest';
                $guest->password    = hash_internal_user_password('guest');
                $guest->firstname   = get_string('guestuser');
                $guest->lastname    = ' ';
                $guest->email       = 'root@localhost';
                $guest->description = get_string('guestuserinfo');
                $guest->mnethostid  = $CFG->mnet_localhost_id;
                $guest->confirmed   = 1;
                $guest->lang        = $CFG->lang;
                $guest->timemodified= time();
                $guest->id = $DB->insert_record('user', $guest);
            }
            // Set the guest user.
            set_config('siteguest', $guest->id);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020072300.01);
    }

    if ($oldversion < 2020081400.01) {
        // Delete all user evidence files from users that have been deleted.
        $sql = "SELECT DISTINCT f.*
                  FROM {files} f
             LEFT JOIN {context} c ON f.contextid = c.id
                 WHERE f.component = :component
                   AND f.filearea = :filearea
                   AND c.id IS NULL";
        $stalefiles = $DB->get_records_sql($sql, ['component' => 'core_competency', 'filearea' => 'userevidence']);

        $fs = get_file_storage();
        foreach ($stalefiles as $stalefile) {
            $fs->get_file_instance($stalefile)->delete();
        }

        upgrade_main_savepoint(true, 2020081400.01);
    }

    if ($oldversion < 2020081400.02) {

        // Define field timecreated to be added to task_adhoc.
        $table = new xmldb_table('task_adhoc');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'blocking');

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020081400.02);
    }

    if ($oldversion < 2020082200.01) {
        // Define field metadatasettings to be added to h5p_libraries.
        $table = new xmldb_table('h5p_libraries');
        $field = new xmldb_field('metadatasettings', XMLDB_TYPE_TEXT, null, null, null, null, null, 'coreminor');

        // Conditionally launch add field metadatasettings.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Get installed library files that have no metadata settings value.
        $params = [
            'component' => 'core_h5p',
            'filearea' => 'libraries',
            'filename' => 'library.json',
        ];
        $sql = "SELECT l.id, f.id as fileid
                  FROM {files} f
             LEFT JOIN {h5p_libraries} l ON f.itemid = l.id
                 WHERE f.component = :component
                       AND f.filearea = :filearea
                       AND f.filename = :filename";
        $libraries = $DB->get_records_sql($sql, $params);

        // Update metadatasettings field when the attribute is present in the library.json file.
        $fs = get_file_storage();
        foreach ($libraries as $library) {
            $jsonfile = $fs->get_file_by_id($library->fileid);
            $jsoncontent = json_decode($jsonfile->get_content());
            if (isset($jsoncontent->metadataSettings)) {
                unset($library->fileid);
                $library->metadatasettings = json_encode($jsoncontent->metadataSettings);
                $DB->update_record('h5p_libraries', $library);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020082200.01);
    }

    if ($oldversion < 2020082200.02) {
        // Define fields to be added to task_scheduled.
        $table = new xmldb_table('task_scheduled');
        $field = new xmldb_field('timestarted', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'disabled');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('hostname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'timestarted');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('pid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'hostname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define fields to be added to task_adhoc.
        $table = new xmldb_table('task_adhoc');
        $field = new xmldb_field('timestarted', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'blocking');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('hostname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'timestarted');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('pid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'hostname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define fields to be added to task_log.
        $table = new xmldb_table('task_log');
        $field = new xmldb_field('hostname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'output');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('pid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'hostname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020082200.02);
    }

    if ($oldversion < 2020082200.03) {
        // Define table to store virus infected details.
        $table = new xmldb_table('infected_files');

        // Adding fields to table infected_files.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('filename', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('quarantinedfile', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('reason', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table infected_files.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Conditionally launch create table for infected_files.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_main_savepoint(true, 2020082200.03);
    }

    if ($oldversion < 2020091000.02) {
        // Remove all the files with component='core_h5p' and filearea='editor' because they won't be used anymore.
        $fs = get_file_storage();
        $syscontext = context_system::instance();
        $fs->delete_area_files($syscontext->id, 'core_h5p', 'editor');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020091000.02);
    }

    if ($oldversion < 2020091800.01) {
        // Copy From id captures the id of the source course when a new course originates from a restore
        // of another course on the same site.
        $table = new xmldb_table('course');
        $field = new xmldb_field('originalcourseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020091800.01);
    }

    if ($oldversion < 2020100200.01) {
        // Define table oauth2_refresh_token to be created.
        $table = new xmldb_table('oauth2_refresh_token');

        // Adding fields to table oauth2_refresh_token.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('scopehash', XMLDB_TYPE_CHAR, 40, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table oauth2_refresh_token.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('issueridkey', XMLDB_KEY_FOREIGN, ['issuerid'], 'oauth2_issuer', ['id']);
        $table->add_key('useridkey', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Adding indexes to table oauth2_refresh_token.
        $table->add_index('userid-issuerid-scopehash', XMLDB_INDEX_UNIQUE, array('userid', 'issuerid', 'scopehash'));

        // Conditionally launch create table for oauth2_refresh_token.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020100200.01);
    }

    if ($oldversion < 2020100700.00) {

        // Define index modulename-instance-eventtype (not unique) to be added to event.
        $table = new xmldb_table('event');
        $index = new xmldb_index('modulename-instance-eventtype', XMLDB_INDEX_NOTUNIQUE, ['modulename', 'instance', 'eventtype']);

        // Conditionally launch add index modulename-instance-eventtype.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index modulename-instance (not unique) to be dropped form event.
        $table = new xmldb_table('event');
        $index = new xmldb_index('modulename-instance', XMLDB_INDEX_NOTUNIQUE, ['modulename', 'instance']);

        // Conditionally launch drop index modulename-instance.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020100700.00);
    }

    if ($oldversion < 2020101300.01) {
        // Define fields tutorial and example to be added to h5p_libraries.
        $table = new xmldb_table('h5p_libraries');

        // Add tutorial field.
        $field = new xmldb_field('tutorial', XMLDB_TYPE_TEXT, null, null, null, null, null, 'metadatasettings');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add example field.
        $field = new xmldb_field('example', XMLDB_TYPE_TEXT, null, null, null, null, null, 'tutorial');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020101300.01);
    }

    if ($oldversion < 2020101600.01) {
        // Delete orphaned course_modules_completion rows; these were not deleted properly
        // by remove_course_contents function.
        $DB->delete_records_select('course_modules_completion', "
                NOT EXISTS (
                        SELECT 1
                          FROM {course_modules} cm
                         WHERE cm.id = {course_modules_completion}.coursemoduleid
                )");
        upgrade_main_savepoint(true, 2020101600.01);
    }

    if ($oldversion < 2020101600.02) {
        // Script to fix incorrect records of "hidden" field in existing grade items.
        $sql = "SELECT cm.instance, cm.course
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                 WHERE m.name = :module AND cm.visible = :visible";
        $hidequizlist = $DB->get_recordset_sql($sql, ['module' => 'quiz', 'visible' => 0]);

        foreach ($hidequizlist as $hidequiz) {
            $params = [
                'itemmodule'    => 'quiz',
                'courseid'      => $hidequiz->course,
                'iteminstance'  => $hidequiz->instance,
            ];

            $DB->set_field('grade_items', 'hidden', 1, $params);
        }
        $hidequizlist->close();

        upgrade_main_savepoint(true, 2020101600.02);
    }

    if ($oldversion < 2020102100.01) {
        // Get the current guest user which is also set as 'deleted'.
        $guestuser = $DB->get_record('user', ['id' => $CFG->siteguest, 'deleted' => 1]);
        // If there is a deleted guest user, reset the user to not be deleted and make sure the related
        // user context exists.
        if ($guestuser) {
            $guestuser->deleted = 0;
            $DB->update_record('user', $guestuser);

            // Get the guest user context.
            $guestusercontext = $DB->get_record('context',
                ['contextlevel' => CONTEXT_USER, 'instanceid' => $guestuser->id]);

            // If the guest user context does not exist, create it.
            if (!$guestusercontext) {
                $record = new stdClass();
                $record->contextlevel = CONTEXT_USER;
                $record->instanceid = $guestuser->id;
                $record->depth = 0;
                // The path is not known before insert.
                $record->path = null;
                $record->locked = 0;

                $record->id = $DB->insert_record('context', $record);

                // Update the path.
                $record->path = '/' . SYSCONTEXTID . '/' . $record->id;
                $record->depth = substr_count($record->path, '/');
                $DB->update_record('context', $record);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020102100.01);
    }

    if ($oldversion < 2020102100.02) {
        // Reset analytics model output dir if it's the default value.
        $modeloutputdir = get_config('analytics', 'modeloutputdir');
        if (strcasecmp($modeloutputdir, $CFG->dataroot . DIRECTORY_SEPARATOR . 'models') == 0) {
            set_config('modeloutputdir', '', 'analytics');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020102100.02);
    }

    if ($oldversion < 2020102300.01) {
        // Define field downloadcontent to be added to course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('downloadcontent', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'visibleold');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020102300.01);
    }

    if ($oldversion < 2020102300.02) {
        $table = new xmldb_table('badge_backpack');

        // There is no key_exists, so test the equivalent index.
        $oldindex = new xmldb_index('backpackcredentials', XMLDB_KEY_UNIQUE, ['userid', 'externalbackpackid']);
        if (!$dbman->index_exists($table, $oldindex)) {
            // All external backpack providers/hosts are now exclusively stored in badge_external_backpack.
            // All credentials are stored in badge_backpack and are unique per user, backpack.
            $uniquekey = new xmldb_key('backpackcredentials', XMLDB_KEY_UNIQUE, ['userid', 'externalbackpackid']);
            $dbman->add_key($table, $uniquekey);
        }

        // Drop the password field as this is moved to badge_backpack.
        $table = new xmldb_table('badge_external_backpack');
        $field = new xmldb_field('password', XMLDB_TYPE_CHAR, '50');
        if ($dbman->field_exists($table, $field)) {
            // If there is a current backpack set then copy it across to the new structure.
            if ($CFG->badges_defaultissuercontact) {
                // Get the currently used site backpacks.
                $records = $DB->get_records_select('badge_external_backpack', "password IS NOT NULL AND password != ''");
                $backpack = [
                    'userid' => '0',
                    'email' => $CFG->badges_defaultissuercontact,
                    'backpackuid' => -1
                ];

                // Create records corresponding to the site backpacks.
                foreach ($records as $record) {
                    $backpack['password'] = $record->password;
                    $backpack['externalbackpackid'] = $record->id;
                    $DB->insert_record('badge_backpack', (object) $backpack);
                }
            }

            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020102300.02);
    }

    if ($oldversion < 2020102700.04) {

        // Define table payment_accounts to be created.
        $table = new xmldb_table('payment_accounts');

        // Adding fields to table payment_accounts.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('archived', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table payment_accounts.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for payment_accounts.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table payment_gateways to be created.
        $table = new xmldb_table('payment_gateways');

        // Adding fields to table payment_gateways.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('accountid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('config', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table payment_gateways.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('accountid', XMLDB_KEY_FOREIGN, ['accountid'], 'payment_accounts', ['id']);

        // Conditionally launch create table for payment_gateways.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table payments to be created.
        $table = new xmldb_table('payments');

        // Adding fields to table payments.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('paymentarea', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('amount', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('currency', XMLDB_TYPE_CHAR, '3', null, XMLDB_NOTNULL, null, null);
        $table->add_field('accountid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table payments.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('accountid', XMLDB_KEY_FOREIGN, ['accountid'], 'payment_accounts', ['id']);

        // Adding indexes to table payments.
        $table->add_index('gateway', XMLDB_INDEX_NOTUNIQUE, ['gateway']);
        $table->add_index('component-paymentarea-itemid', XMLDB_INDEX_NOTUNIQUE, ['component', 'paymentarea', 'itemid']);

        // Conditionally launch create table for payments.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2020102700.04);
    }

    // Automatically generated Moodle v3.10.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020111500.01) {
        // Get all lessons that are set with a completion criteria of 'requires grade' but with no grade type set.
        $sql = "SELECT cm.id
                  FROM {course_modules} cm
                  JOIN {lesson} l ON l.id = cm.instance
                  JOIN {modules} m ON m.id = cm.module
                 WHERE m.name = :name AND cm.completiongradeitemnumber IS NOT NULL AND l.grade = :grade";

        do {
            if ($invalidconfigrations = $DB->get_records_sql($sql, ['name' => 'lesson', 'grade' => 0], 0, 1000)) {
                list($insql, $inparams) = $DB->get_in_or_equal(array_keys($invalidconfigrations), SQL_PARAMS_NAMED);
                $DB->set_field_select('course_modules', 'completiongradeitemnumber', null, "id $insql", $inparams);
            }
        } while ($invalidconfigrations);

        upgrade_main_savepoint(true, 2020111500.01);
    }

    if ($oldversion < 2021013100.00) {
        $DB->delete_records_select('event', "eventtype = 'category' AND categoryid = 0 AND userid <> 0");

        upgrade_main_savepoint(true, 2021013100.00);
    }

    if ($oldversion < 2021021100.01) {
        // Define field visibility to be added to contentbank_content.
        $table = new xmldb_table('contentbank_content');
        $field = new xmldb_field('visibility', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'contextid');

        // Conditionally launch add field visibility.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021021100.01);
    }

    if ($oldversion < 2021021600.00) {

        // We are going to remove the field 'hidepicture' from the groups
        // so we need to remove the pictures from those groups. But we prevent
        // the execution twice because this could be executed again when upgrading
        // to different versions.
        if ($dbman->field_exists('groups', 'hidepicture')) {

            $sql = "SELECT g.id, g.courseid, ctx.id AS contextid
                       FROM {groups} g
                       JOIN {context} ctx
                         ON ctx.instanceid = g.courseid
                        AND ctx.contextlevel = :contextlevel
                      WHERE g.hidepicture = 1";

            // Selecting all the groups that have hide picture enabled, and organising them by context.
            $groupctx = [];
            $records = $DB->get_recordset_sql($sql, ['contextlevel' => CONTEXT_COURSE]);
            foreach ($records as $record) {
                if (!isset($groupctx[$record->contextid])) {
                    $groupctx[$record->contextid] = [];
                }
                $groupctx[$record->contextid][] = $record->id;
            }
            $records->close();

            // Deleting the group files.
            $fs = get_file_storage();
            foreach ($groupctx as $contextid => $groupids) {
                list($in, $inparams) = $DB->get_in_or_equal($groupids, SQL_PARAMS_NAMED);
                $fs->delete_area_files_select($contextid, 'group', 'icon', $in, $inparams);
            }

            // Updating the database to remove picture from all those groups.
            $sql = "UPDATE {groups} SET picture = :pic WHERE hidepicture = :hide";
            $DB->execute($sql, ['pic' => 0, 'hide' => 1]);
        }

        // Define field hidepicture to be dropped from groups.
        $table = new xmldb_table('groups');
        $field = new xmldb_field('hidepicture');

        // Conditionally launch drop field hidepicture.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021021600.00);
    }

    if ($oldversion < 2021022600.01) {
        // Get all the external backpacks and update the sortorder column, to avoid repeated/wrong values. As sortorder was not
        // used since now, the id column will be the criteria to follow for re-ordering them with a valid value.
        $i = 1;
        $records = $DB->get_records('badge_external_backpack', null, 'id ASC');
        foreach ($records as $record) {
            $record->sortorder = $i++;
            $DB->update_record('badge_external_backpack', $record);
        }

        upgrade_main_savepoint(true, 2021022600.01);
    }

    if ($oldversion < 2021030500.01) {
        // The $CFG->badges_site_backpack setting has been removed because it's not required anymore. From now, the default backpack
        // will be the one with lower sortorder value.
        unset_config('badges_site_backpack');

        upgrade_main_savepoint(true, 2021030500.01);
    }

    if ($oldversion < 2021031200.01) {

        // Define field type to be added to oauth2_issuer.
        $table = new xmldb_table('oauth2_issuer');
        $field = new xmldb_field('servicetype', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'requireconfirmation');

        // Conditionally launch add field type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set existing values to the proper servicetype value.
        // It's not critical if the servicetype column doesn't contain the proper value for Google, Microsoft, Facebook or
        // Nextcloud services because, for now, this value is used for services using different discovery method.
        // However, let's try to upgrade it using the default value for the baseurl or image. If any of these default values
        // have been changed, the servicetype column will remain NULL.
        $recordset = $DB->get_recordset('oauth2_issuer');
        foreach ($recordset as $record) {
            if ($record->baseurl == 'https://accounts.google.com/') {
                $record->servicetype = 'google';
                $DB->update_record('oauth2_issuer', $record);
            } else if ($record->image == 'https://www.microsoft.com/favicon.ico') {
                $record->servicetype = 'microsoft';
                $DB->update_record('oauth2_issuer', $record);
            } else if ($record->image == 'https://facebookbrand.com/wp-content/uploads/2016/05/flogo_rgb_hex-brc-site-250.png') {
                $record->servicetype = 'facebook';
                $DB->update_record('oauth2_issuer', $record);
            } else if ($record->image == 'https://nextcloud.com/wp-content/themes/next/assets/img/common/favicon.png?x16328') {
                $record->servicetype = 'nextcloud';
                $DB->update_record('oauth2_issuer', $record);
            }
        }
        $recordset->close();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021031200.01);
    }

    if ($oldversion < 2021033100.00) {
        // Define field 'showactivitydates' to be added to course table.
        $table = new xmldb_table('course');
        $field = new xmldb_field('showactivitydates', XMLDB_TYPE_INTEGER, '1', null,
            XMLDB_NOTNULL, null, '0', 'originalcourseid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021033100.00);
    }

    if ($oldversion < 2021033100.01) {
        // Define field 'showcompletionconditions' to be added to course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('showcompletionconditions', XMLDB_TYPE_INTEGER, '1', null,
            XMLDB_NOTNULL, null, '1', 'completionnotify');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021033100.01);
    }

    if ($oldversion < 2021041300.01) {

        // Define field enabled to be added to h5p_libraries.
        $table = new xmldb_table('h5p_libraries');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, null, null, '1', 'example');

        // Conditionally launch add field enabled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021041300.01);
    }

    if ($oldversion < 2021042100.00) {

        // Define field loginpagename to be added to oauth2_issuer.
        $table = new xmldb_table('oauth2_issuer');
        $field = new xmldb_field('loginpagename', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'servicetype');

        // Conditionally launch add field loginpagename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021042100.00);
    }

    if ($oldversion < 2021042100.01) {
        require_once($CFG->dirroot . '/user/profile/field/social/upgradelib.php');
        $table = new xmldb_table('user');
        $tablecolumns = ['icq', 'skype', 'aim', 'yahoo', 'msn', 'url'];

        foreach ($tablecolumns as $column) {
            $field = new xmldb_field($column);
            if ($dbman->field_exists($table, $field)) {
                user_profile_social_moveto_profilefield($column);
                $dbman->drop_field($table, $field);
            }
        }

        // Update all module availability if it relies on the old user fields.
        user_profile_social_update_module_availability();

        // Remove field mapping for oauth2.
        $DB->delete_records('oauth2_user_field_mapping', array('internalfield' => 'url'));

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021042100.01);
    }

    if ($oldversion < 2021042100.02) {
        require_once($CFG->libdir . '/db/upgradelib.php');

        // Check if this site has executed the problematic upgrade steps.
        $needsfixing = upgrade_calendar_site_status(false);

        // Only queue the task if this site has been affected by the problematic upgrade step.
        if ($needsfixing) {

            // Create adhoc task to search and recover orphaned calendar events.
            $record = new \stdClass();
            $record->classname = '\core\task\calendar_fix_orphaned_events';

            // Next run time based from nextruntime computation in \core\task\manager::queue_adhoc_task().
            $nextruntime = time() - 1;
            $record->nextruntime = $nextruntime;
            $DB->insert_record('task_adhoc', $record);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021042100.02);
    }

    if ($oldversion < 2021042400.00) {
        // Changing the default of field showcompletionconditions on table course to 0.
        $table = new xmldb_table('course');
        $field = new xmldb_field('showcompletionconditions', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'showactivitydates');

        // Launch change of nullability for field showcompletionconditions.
        $dbman->change_field_notnull($table, $field);

        // Launch change of default for field showcompletionconditions.
        $dbman->change_field_default($table, $field);

        // Set showcompletionconditions to null for courses which don't track completion.
        $sql = "UPDATE {course}
                   SET showcompletionconditions = null
                 WHERE enablecompletion <> 1";
        $DB->execute($sql);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021042400.00);
    }

    if ($oldversion < 2021043000.01) {
        // Remove usemodchooser user preference for every user.
        $DB->delete_records('user_preferences', ['name' => 'usemodchooser']);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021043000.01);
    }

    // Automatically generated Moodle v3.11.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021051700.03) {

        // Define index name (not unique) to be added to user_preferences.
        $table = new xmldb_table('user_preferences');
        $index = new xmldb_index('name', XMLDB_INDEX_NOTUNIQUE, ['name']);

        // Conditionally launch add index name.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021051700.03);
    }

    if ($oldversion < 2021051700.05) {
        // Update the externalfield to be larger.
        $table = new xmldb_table('oauth2_user_field_mapping');
        $field = new xmldb_field('externalfield', XMLDB_TYPE_CHAR, '500', null, XMLDB_NOTNULL, false, null, 'issuerid');
        $dbman->change_field_type($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2021051700.05);
    }

    return true;
}
