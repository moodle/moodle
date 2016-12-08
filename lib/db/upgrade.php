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
    // allowed version to upgrade from (v2.7.0 right now).
    if ($oldversion < 2014051200) {
        // Just in case somebody hacks upgrade scripts or env, we really can not continue.
        echo("You need to upgrade to 2.7.x or higher first!\n");
        exit(1);
        // Note this savepoint is 100% unreachable, but needed to pass the upgrade checks.
        upgrade_main_savepoint(true, 2014051200);
    }

    // MDL-32543 Make sure that the log table has correct length for action and url fields.
    if ($oldversion < 2014051200.02) {

        $table = new xmldb_table('log');

        $columns = $DB->get_columns('log');
        if ($columns['action']->max_length < 40) {
            $index1 = new xmldb_index('course-module-action', XMLDB_INDEX_NOTUNIQUE, array('course', 'module', 'action'));
            if ($dbman->index_exists($table, $index1)) {
                $dbman->drop_index($table, $index1);
            }
            $index2 = new xmldb_index('action', XMLDB_INDEX_NOTUNIQUE, array('action'));
            if ($dbman->index_exists($table, $index2)) {
                $dbman->drop_index($table, $index2);
            }
            $field = new xmldb_field('action', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'cmid');
            $dbman->change_field_precision($table, $field);
            $dbman->add_index($table, $index1);
            $dbman->add_index($table, $index2);
        }

        if ($columns['url']->max_length < 100) {
            $field = new xmldb_field('url', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'action');
            $dbman->change_field_precision($table, $field);
        }

        upgrade_main_savepoint(true, 2014051200.02);
    }

    if ($oldversion < 2014060300.00) {
        $gspath = get_config('assignfeedback_editpdf', 'gspath');
        if ($gspath !== false) {
            set_config('pathtogs', $gspath);
            unset_config('gspath', 'assignfeedback_editpdf');
        }
        upgrade_main_savepoint(true, 2014060300.00);
    }

    if ($oldversion < 2014061000.00) {
        // Fixing possible wrong MIME type for Publisher files.
        $filetypes = array('%.pub'=>'application/x-mspublisher');
        upgrade_mimetypes($filetypes);
        upgrade_main_savepoint(true, 2014061000.00);
    }

    if ($oldversion < 2014062600.01) {
        // We only want to delete DragMath if the directory no longer exists. If the directory
        // is present then it means it has been restored, so do not perform the uninstall.
        if (!check_dir_exists($CFG->libdir . '/editor/tinymce/plugins/dragmath', false)) {
            // Purge DragMath plugin which is incompatible with GNU GPL license.
            unset_all_config_for_plugin('tinymce_dragmath');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014062600.01);
    }

    // Switch the order of the fields in the files_reference index, to improve the performance of search_references.
    if ($oldversion < 2014070100.00) {
        $table = new xmldb_table('files_reference');
        $index = new xmldb_index('uq_external_file', XMLDB_INDEX_UNIQUE, array('repositoryid', 'referencehash'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        upgrade_main_savepoint(true, 2014070100.00);
    }

    if ($oldversion < 2014070101.00) {
        $table = new xmldb_table('files_reference');
        $index = new xmldb_index('uq_external_file', XMLDB_INDEX_UNIQUE, array('referencehash', 'repositoryid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_main_savepoint(true, 2014070101.00);
    }

    if ($oldversion < 2014072400.01) {
        $table = new xmldb_table('user_devices');
        $oldindex = new xmldb_index('pushid-platform', XMLDB_KEY_UNIQUE, array('pushid', 'platform'));
        if ($dbman->index_exists($table, $oldindex)) {
            $key = new xmldb_key('pushid-platform', XMLDB_KEY_UNIQUE, array('pushid', 'platform'));
            $dbman->drop_key($table, $key);
        }
        upgrade_main_savepoint(true, 2014072400.01);
    }

    if ($oldversion < 2014080801.00) {

        // Define index behaviour (not unique) to be added to question_attempts.
        $table = new xmldb_table('question_attempts');
        $index = new xmldb_index('behaviour', XMLDB_INDEX_NOTUNIQUE, array('behaviour'));

        // Conditionally launch add index behaviour.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014080801.00);
    }

    if ($oldversion < 2014082900.01) {
        // Fixing possible wrong MIME type for 7-zip and Rar files.
        $filetypes = array(
                '%.7z' => 'application/x-7z-compressed',
                '%.rar' => 'application/x-rar-compressed');
        upgrade_mimetypes($filetypes);
        upgrade_main_savepoint(true, 2014082900.01);
    }

    if ($oldversion < 2014082900.02) {
        // Replace groupmembersonly usage with new availability system.
        $transaction = $DB->start_delegated_transaction();
        if ($CFG->enablegroupmembersonly) {
            // If it isn't already enabled, we need to enable availability.
            if (!$CFG->enableavailability) {
                set_config('enableavailability', 1);
            }

            // Count all course-modules with groupmembersonly set (for progress
            // bar).
            $total = $DB->count_records('course_modules', array('groupmembersonly' => 1));
            $pbar = new progress_bar('upgradegroupmembersonly', 500, true);

            // Get all these course-modules, one at a time.
            $rs = $DB->get_recordset('course_modules', array('groupmembersonly' => 1),
                    'course, id');
            $i = 0;
            foreach ($rs as $cm) {
                // Calculate and set new availability value.
                $availability = upgrade_group_members_only($cm->groupingid, $cm->availability);
                $DB->set_field('course_modules', 'availability', $availability,
                        array('id' => $cm->id));

                // Update progress.
                $i++;
                $pbar->update($i, $total, "Upgrading groupmembersonly settings - $i/$total.");
            }
            $rs->close();
        }

        // Define field groupmembersonly to be dropped from course_modules.
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('groupmembersonly');

        // Conditionally launch drop field groupmembersonly.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Unset old config variable.
        unset_config('enablegroupmembersonly');
        $transaction->allow_commit();

        upgrade_main_savepoint(true, 2014082900.02);
    }

    if ($oldversion < 2014100100.00) {

        // Define table messageinbound_handlers to be created.
        $table = new xmldb_table('messageinbound_handlers');

        // Adding fields to table messageinbound_handlers.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('classname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('defaultexpiration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '86400');
        $table->add_field('validateaddress', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table messageinbound_handlers.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('classname', XMLDB_KEY_UNIQUE, array('classname'));

        // Conditionally launch create table for messageinbound_handlers.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table messageinbound_datakeys to be created.
        $table = new xmldb_table('messageinbound_datakeys');

        // Adding fields to table messageinbound_datakeys.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('handler', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('datavalue', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('datakey', XMLDB_TYPE_CHAR, '64', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table messageinbound_datakeys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('handler_datavalue', XMLDB_KEY_UNIQUE, array('handler', 'datavalue'));
        $table->add_key('handler', XMLDB_KEY_FOREIGN, array('handler'), 'messageinbound_handlers', array('id'));

        // Conditionally launch create table for messageinbound_datakeys.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100100.00);
    }

    if ($oldversion < 2014100600.01) {
        // Define field aggregationstatus to be added to grade_grades.
        $table = new xmldb_table('grade_grades');
        $field = new xmldb_field('aggregationstatus', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'unknown', 'timemodified');

        // Conditionally launch add field aggregationstatus.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('aggregationweight', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'aggregationstatus');

        // Conditionally launch add field aggregationweight.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field aggregationcoef2 to be added to grade_items.
        $table = new xmldb_table('grade_items');
        $field = new xmldb_field('aggregationcoef2', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'aggregationcoef');

        // Conditionally launch add field aggregationcoef2.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('weightoverride', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'needsupdate');

        // Conditionally launch add field weightoverride.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100600.01);
    }

    if ($oldversion < 2014100600.02) {

        // Define field aggregationcoef2 to be added to grade_items_history.
        $table = new xmldb_table('grade_items_history');
        $field = new xmldb_field('aggregationcoef2', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'aggregationcoef');

        // Conditionally launch add field aggregationcoef2.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100600.02);
    }

    if ($oldversion < 2014100600.03) {

        // Define field weightoverride to be added to grade_items_history.
        $table = new xmldb_table('grade_items_history');
        $field = new xmldb_field('weightoverride', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'decimals');

        // Conditionally launch add field weightoverride.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100600.03);
    }
    if ($oldversion < 2014100600.04) {
        // Set flags so we can display a notice on all courses that might
        // be affected by the uprade to natural aggregation.
        if (!get_config('grades_sumofgrades_upgrade_flagged', 'core')) {
            // 13 == SUM_OF_GRADES.
            $sql = 'SELECT DISTINCT courseid
                      FROM {grade_categories}
                     WHERE aggregation = ?';
            $courses = $DB->get_records_sql($sql, array(13));

            foreach ($courses as $course) {
                set_config('show_sumofgrades_upgrade_' . $course->courseid, 1);
                // Set each of the grade items to needing an update so that when the user visits the grade reports the
                // figures will be updated.
                $DB->set_field('grade_items', 'needsupdate', 1, array('courseid' => $course->courseid));
            }

            set_config('grades_sumofgrades_upgrade_flagged', 1);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100600.04);
    }

    if ($oldversion < 2014100700.00) {

        // Define table messageinbound_messagelist to be created.
        $table = new xmldb_table('messageinbound_messagelist');

        // Adding fields to table messageinbound_messagelist.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('messageid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('address', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table messageinbound_messagelist.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for messageinbound_messagelist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100700.00);
    }

    if ($oldversion < 2014100700.01) {

        // Define field visible to be added to cohort.
        $table = new xmldb_table('cohort');
        $field = new xmldb_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'descriptionformat');

        // Conditionally launch add field visible.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100700.01);
    }

    if ($oldversion < 2014100800.00) {
        // Remove qformat_learnwise (unless it has manually been added back).
        if (!file_exists($CFG->dirroot . '/question/format/learnwise/format.php')) {
            unset_all_config_for_plugin('qformat_learnwise');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014100800.00);
    }

    if ($oldversion < 2014101001.00) {
        // Some blocks added themselves to the my/ home page, but they did not declare the
        // subpage of the default my home page. While the upgrade script has been fixed, this
        // upgrade script will fix the data that was wrongly added.

        // We only proceed if we can find the right entry from my_pages. Private => 1 refers to
        // the constant value MY_PAGE_PRIVATE.
        if ($systempage = $DB->get_record('my_pages', array('userid' => null, 'private' => 1))) {

            // Select the blocks there could have been automatically added. showinsubcontexts is hardcoded to 0
            // because it is possible for administrators to have forced it on the my/ page by adding it to the
            // system directly rather than updating the default my/ page.
            $blocks = array('course_overview', 'private_files', 'online_users', 'badges', 'calendar_month', 'calendar_upcoming');
            list($blocksql, $blockparams) = $DB->get_in_or_equal($blocks, SQL_PARAMS_NAMED);
            $select = "parentcontextid = :contextid
                    AND pagetypepattern = :page
                    AND showinsubcontexts = 0
                    AND subpagepattern IS NULL
                    AND blockname $blocksql";
            $params = array(
                'contextid' => context_system::instance()->id,
                'page' => 'my-index'
            );
            $params = array_merge($params, $blockparams);

            $DB->set_field_select(
                'block_instances',
                'subpagepattern',
                $systempage->id,
                $select,
                $params
            );
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014101001.00);
    }

    if ($oldversion < 2014102000.00) {

        // Define field aggregatesubcats to be dropped from grade_categories.
        $table = new xmldb_table('grade_categories');
        $field = new xmldb_field('aggregatesubcats');

        // Conditionally launch drop field aggregatesubcats.
        if ($dbman->field_exists($table, $field)) {

            $sql = 'SELECT DISTINCT courseid
                      FROM {grade_categories}
                     WHERE aggregatesubcats = ?';
            $courses = $DB->get_records_sql($sql, array(1));

            foreach ($courses as $course) {
                set_config('show_aggregatesubcats_upgrade_' . $course->courseid, 1);
                // Set each of the grade items to needing an update so that when the user visits the grade reports the
                // figures will be updated.
                $DB->set_field('grade_items', 'needsupdate', 1, array('courseid' => $course->courseid));
            }


            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014102000.00);
    }

    if ($oldversion < 2014110300.00) {
        // Run script restoring missing folder records for draft file areas.
        upgrade_fix_missing_root_folders_draft();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014110300.00);
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014111000.00) {
        // Coming from 2.7 or older, we need to flag the step minmaxgrade to be ignored.
        set_config('upgrade_minmaxgradestepignored', 1);
        // Coming from 2.7 or older, we need to flag the step for changing calculated grades to be regraded.
        set_config('upgrade_calculatedgradeitemsonlyregrade', 1);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014111000.00);
    }

    if ($oldversion < 2014120100.00) {

        // Define field sslverification to be added to mnet_host.
        $table = new xmldb_table('mnet_host');
        $field = new xmldb_field('sslverification', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'applicationid');

        // Conditionally launch add field sslverification.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014120100.00);
    }

    if ($oldversion < 2014120101.00) {

        // Define field component to be added to comments.
        $table = new xmldb_table('comments');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'contextid');

        // Conditionally launch add field component.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014120101.00);
    }

    if ($oldversion < 2014120102.00) {

        // Define table user_password_history to be created.
        $table = new xmldb_table('user_password_history');

        // Adding fields to table user_password_history.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hash', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table user_password_history.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for user_password_history.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014120102.00);
    }

    if ($oldversion < 2015010800.01) {

        // Make sure the private files handler is not set to expire.
        $DB->set_field('messageinbound_handlers', 'defaultexpiration', 0,
                array('classname' => '\core\message\inbound\private_files_handler'));

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015010800.01);

    }

    if ($oldversion < 2015012600.00) {

        // If the site is using internal and external storage, or just external
        // storage, and the external path specified is empty we change the setting
        // to internal only. That is how the backup code is handling this
        // misconfiguration.
        $storage = (int) get_config('backup', 'backup_auto_storage');
        $folder = get_config('backup', 'backup_auto_destination');
        if ($storage !== 0 && empty($folder)) {
            set_config('backup_auto_storage', 0, 'backup');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015012600.00);
    }

    if ($oldversion < 2015012600.01) {

        // Convert calendar_lookahead to nearest new value.
        $value = $DB->get_field('config', 'value', array('name' => 'calendar_lookahead'));
        if ($value > 90) {
            set_config('calendar_lookahead', '120');
        } else if ($value > 60 and $value < 90) {
            set_config('calendar_lookahead', '90');
        } else if ($value > 30 and $value < 60) {
            set_config('calendar_lookahead', '60');
        } else if ($value > 21 and $value < 30) {
            set_config('calendar_lookahead', '30');
        } else if ($value > 14 and $value < 21) {
            set_config('calendar_lookahead', '21');
        } else if ($value > 7 and $value < 14) {
            set_config('calendar_lookahead', '14');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015012600.01);
    }

    if ($oldversion < 2015021100.00) {

        // Define field timemodified to be added to registration_hubs.
        $table = new xmldb_table('registration_hubs');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'secret');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015021100.00);
    }

    if ($oldversion < 2015022401.00) {

        // Define index useridfromto (not unique) to be added to message.
        $table = new xmldb_table('message');
        $index = new xmldb_index('useridfromto', XMLDB_INDEX_NOTUNIQUE, array('useridfrom', 'useridto'));

        // Conditionally launch add index useridfromto.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index useridfromto (not unique) to be added to message_read.
        $table = new xmldb_table('message_read');
        $index = new xmldb_index('useridfromto', XMLDB_INDEX_NOTUNIQUE, array('useridfrom', 'useridto'));

        // Conditionally launch add index useridfromto.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015022401.00);
    }

    if ($oldversion < 2015022500.00) {
        $table = new xmldb_table('user_devices');
        $index = new xmldb_index('uuid-userid', XMLDB_INDEX_NOTUNIQUE, array('uuid', 'userid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_main_savepoint(true, 2015022500.00);
    }

    if ($oldversion < 2015030400.00) {
        // We have long since switched to storing timemodified per hub rather than a single 'registered' timestamp.
        unset_config('registered');
        upgrade_main_savepoint(true, 2015030400.00);
    }

    if ($oldversion < 2015031100.00) {
        // Unset old config variable.
        unset_config('enabletgzbackups');

        upgrade_main_savepoint(true, 2015031100.00);
    }

    if ($oldversion < 2015031400.00) {

        // Define index useridfrom (not unique) to be dropped form message.
        $table = new xmldb_table('message');
        $index = new xmldb_index('useridfrom', XMLDB_INDEX_NOTUNIQUE, array('useridfrom'));

        // Conditionally launch drop index useridfrom.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index useridfrom (not unique) to be dropped form message_read.
        $table = new xmldb_table('message_read');
        $index = new xmldb_index('useridfrom', XMLDB_INDEX_NOTUNIQUE, array('useridfrom'));

        // Conditionally launch drop index useridfrom.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015031400.00);
    }

    if ($oldversion < 2015031900.01) {
        unset_config('crontime', 'registration');
        upgrade_main_savepoint(true, 2015031900.01);
    }

    if ($oldversion < 2015032000.00) {
        $table = new xmldb_table('badge_criteria');

        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // Conditionally add description field to the badge_criteria table.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('descriptionformat', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0);
        // Conditionally add description format field to the badge_criteria table.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2015032000.00);
    }

    if ($oldversion < 2015040200.01) {
        // Force uninstall of deleted tool.
        if (!file_exists("$CFG->dirroot/$CFG->admin/tool/timezoneimport")) {
            // Remove capabilities.
            capabilities_cleanup('tool_timezoneimport');
            // Remove all other associated config.
            unset_all_config_for_plugin('tool_timezoneimport');
        }
        upgrade_main_savepoint(true, 2015040200.01);
    }

    if ($oldversion < 2015040200.02) {
        // Define table timezone to be dropped.
        $table = new xmldb_table('timezone');
        // Conditionally launch drop table for timezone.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        upgrade_main_savepoint(true, 2015040200.02);
    }

    if ($oldversion < 2015040200.03) {
        if (isset($CFG->timezone) and $CFG->timezone == 99) {
            // Migrate to real server timezone.
            unset_config('timezone');
        }
        upgrade_main_savepoint(true, 2015040200.03);
    }

    if ($oldversion < 2015040700.01) {
        $DB->delete_records('config_plugins', array('name' => 'requiremodintro'));
        upgrade_main_savepoint(true, 2015040700.01);
    }

    if ($oldversion < 2015040900.01) {
        // Add "My grades" to the user menu.
        $oldconfig = get_config('core', 'customusermenuitems');
        if (strpos("mygrades,grades|/grade/report/mygrades.php|grades", $oldconfig) === false) {
            $newconfig = "mygrades,grades|/grade/report/mygrades.php|grades\n" . $oldconfig;
            set_config('customusermenuitems', $newconfig);
        }

        upgrade_main_savepoint(true, 2015040900.01);
    }

    if ($oldversion < 2015040900.02) {
        // Update the default user menu (add preferences, remove my files and my badges).
        $oldconfig = get_config('core', 'customusermenuitems');

        // Add "My preferences" at the end.
        if (strpos($oldconfig, "mypreferences,moodle|/user/preference.php|preferences") === false) {
            $newconfig = $oldconfig . "\nmypreferences,moodle|/user/preferences.php|preferences";
        } else {
            $newconfig = $oldconfig;
        }
        // Remove my files.
        $newconfig = str_replace("myfiles,moodle|/user/files.php|download", "", $newconfig);
        // Remove my badges.
        $newconfig = str_replace("mybadges,badges|/badges/mybadges.php|award", "", $newconfig);
        // Remove holes.
        $newconfig = preg_replace('/\n+/', "\n", $newconfig);
        $newconfig = preg_replace('/(\r\n)+/', "\n", $newconfig);
        set_config('customusermenuitems', $newconfig);

        upgrade_main_savepoint(true, 2015040900.02);
    }

    if ($oldversion < 2015050400.00) {
        $config = get_config('core', 'customusermenuitems');

        // Change "My preferences" in the user menu to "Preferences".
        $config = str_replace("mypreferences,moodle|/user/preferences.php|preferences",
            "preferences,moodle|/user/preferences.php|preferences", $config);

        // Change "My grades" in the user menu to "Grades".
        $config = str_replace("mygrades,grades|/grade/report/mygrades.php|grades",
            "grades,grades|/grade/report/mygrades.php|grades", $config);

        set_config('customusermenuitems', $config);

        upgrade_main_savepoint(true, 2015050400.00);
    }

    if ($oldversion < 2015050401.00) {
        // Make sure we have messages in the user menu because it's no longer in the nav tree.
        $oldconfig = get_config('core', 'customusermenuitems');
        $messagesconfig = "messages,message|/message/index.php|message";
        $preferencesconfig = "preferences,moodle|/user/preferences.php|preferences";

        // See if it exists.
        if (strpos($oldconfig, $messagesconfig) === false) {
            // See if preferences exists.
            if (strpos($oldconfig, "preferences,moodle|/user/preferences.php|preferences") !== false) {
                // Insert it before preferences.
                $newconfig = str_replace($preferencesconfig, $messagesconfig . "\n" . $preferencesconfig, $oldconfig);
            } else {
                // Custom config - we can only insert it at the end.
                $newconfig = $oldconfig . "\n" . $messagesconfig;
            }
            set_config('customusermenuitems', $newconfig);
        }

        upgrade_main_savepoint(true, 2015050401.00);
    }

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015060400.02) {

        // Sites that were upgrading from 2.7 and older will ignore this step.
        if (empty($CFG->upgrade_minmaxgradestepignored)) {

            upgrade_minmaxgrade();

            // Flags this upgrade step as already run to prevent it from running multiple times.
            set_config('upgrade_minmaxgradestepignored', 1);
        }

        upgrade_main_savepoint(true, 2015060400.02);
    }

    if ($oldversion < 2015061900.00) {
        // MDL-49257. Changed the algorithm of calculating automatic weights of extra credit items.

        // Before the change, in case when grade category (in "Natural" agg. method) had items with
        // overridden weights, the automatic weight of extra credit items was illogical.
        // In order to prevent grades changes after the upgrade we need to freeze gradebook calculation
        // for the affected courses.

        // This script in included in each major version upgrade process so make sure we don't run it twice.
        if (empty($CFG->upgrade_extracreditweightsstepignored)) {
            upgrade_extra_credit_weightoverride();

            // To skip running the same script on the upgrade to the next major release.
            set_config('upgrade_extracreditweightsstepignored', 1);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015061900.00);
    }

    if ($oldversion < 2015062500.01) {
        // MDL-48239. Changed calculated grade items so that the maximum and minimum grade can be set.

        // If the changes are accepted and a regrade is done on the gradebook then some grades may change significantly.
        // This is here to freeze the gradebook in affected courses.

        // This script is included in each major version upgrade process so make sure we don't run it twice.
        if (empty($CFG->upgrade_calculatedgradeitemsignored)) {
            upgrade_calculated_grade_items();

            // To skip running the same script on the upgrade to the next major release.
            set_config('upgrade_calculatedgradeitemsignored', 1);
            // This config value is never used again.
            unset_config('upgrade_calculatedgradeitemsonlyregrade');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015062500.01);
    }

    if ($oldversion < 2015081300.01) {

        // Define field importtype to be added to grade_import_values.
        $table = new xmldb_table('grade_import_values');
        $field = new xmldb_field('importonlyfeedback', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'importer');

        // Conditionally launch add field importtype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015081300.01);
    }

    if ($oldversion < 2015082400.00) {

        // Define table webdav_locks to be dropped.
        $table = new xmldb_table('webdav_locks');

        // Conditionally launch drop table for webdav_locks.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015082400.00);
    }

    if ($oldversion < 2015090200.00) {
        $table = new xmldb_table('message');

        // Define the deleted fields to be added to the message tables.
        $field1 = new xmldb_field('timeuserfromdeleted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0',
            'timecreated');
        $field2 = new xmldb_field('timeusertodeleted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0',
            'timecreated');
        $oldindex = new xmldb_index('useridfromto', XMLDB_INDEX_NOTUNIQUE,
            array('useridfrom', 'useridto'));
        $newindex = new xmldb_index('useridfromtodeleted', XMLDB_INDEX_NOTUNIQUE,
            array('useridfrom', 'useridto', 'timeuserfromdeleted', 'timeusertodeleted'));

        // Conditionally launch add field timeuserfromdeleted.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Conditionally launch add field timeusertodeleted.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Conditionally launch drop index useridfromto.
        if ($dbman->index_exists($table, $oldindex)) {
            $dbman->drop_index($table, $oldindex);
        }

        // Conditionally launch add index useridfromtodeleted.
        if (!$dbman->index_exists($table, $newindex)) {
            $dbman->add_index($table, $newindex);
        }

        // Now add them to the message_read table.
        $table = new xmldb_table('message_read');

        // Conditionally launch add field timeuserfromdeleted.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Conditionally launch add field timeusertodeleted.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Conditionally launch drop index useridfromto.
        if ($dbman->index_exists($table, $oldindex)) {
            $dbman->drop_index($table, $oldindex);
        }

        // Conditionally launch add index useridfromtodeleted.
        if (!$dbman->index_exists($table, $newindex)) {
            $dbman->add_index($table, $newindex);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015090200.00);
    }

    if ($oldversion < 2015090801.00) {
        // This upgrade script merges all tag instances pointing to the same course tag.
        // User id is no longer used for those tag instances.
        upgrade_course_tags();

        // If configuration variable "Show course tags" is set, disable the block
        // 'tags' because it can not be used for tagging courses any more.
        if (!empty($CFG->block_tags_showcoursetags)) {
            if ($record = $DB->get_record('block', array('name' => 'tags'), 'id, visible')) {
                if ($record->visible) {
                    $DB->update_record('block', array('id' => $record->id, 'visible' => 0));
                }
            }
        }

        // Define index idname (unique) to be dropped form tag (it's really weird).
        $table = new xmldb_table('tag');
        $index = new xmldb_index('idname', XMLDB_INDEX_UNIQUE, array('id', 'name'));

        // Conditionally launch drop index idname.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015090801.00);
    }

    if ($oldversion < 2015092200.00) {
        // Define index qtype (not unique) to be added to question.
        $table = new xmldb_table('question');
        $index = new xmldb_index('qtype', XMLDB_INDEX_NOTUNIQUE, array('qtype'));

        // Conditionally launch add index qtype.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015092200.00);
    }

    if ($oldversion < 2015092900.00) {
        // Rename backup_auto_keep setting to backup_auto_max_kept.
        $keep = get_config('backup', 'backup_auto_keep');
        if ($keep !== false) {
            set_config('backup_auto_max_kept', $keep, 'backup');
            unset_config('backup_auto_keep', 'backup');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015092900.00);
    }

    if ($oldversion < 2015100600.00) {

        // Define index notification (not unique) to be added to message_read.
        $table = new xmldb_table('message_read');
        $index = new xmldb_index('notificationtimeread', XMLDB_INDEX_NOTUNIQUE, array('notification', 'timeread'));

        // Conditionally launch add index notification.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015100600.00);
    }

    if ($oldversion < 2015100800.01) {
        // The only flag for preventing all plugins installation features is
        // now $CFG->disableupdateautodeploy in config.php.
        unset_config('updateautodeploy');
        upgrade_main_savepoint(true, 2015100800.01);
    }

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016011300.01) {

        // This is a big upgrade script. We create new table tag_coll and the field
        // tag.tagcollid pointing to it.

        // Define table tag_coll to be created.
        $table = new xmldb_table('tag_coll');

        // Adding fields to table tagcloud.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('isdefault', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('searchable', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('customurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table tagcloud.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for tagcloud.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Table {tag}.
        // Define index name (unique) to be dropped form tag - we will replace it with index on (tagcollid,name) later.
        $table = new xmldb_table('tag');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, array('name'));

        // Conditionally launch drop index name.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define field tagcollid to be added to tag, we create it as null first and will change to notnull later.
        $table = new xmldb_table('tag');
        $field = new xmldb_field('tagcollid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'userid');

        // Conditionally launch add field tagcloudid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.01);
    }

    if ($oldversion < 2016011300.02) {
        // Create a default tag collection if not exists and update the field tag.tagcollid to point to it.
        if (!$tcid = $DB->get_field_sql('SELECT id FROM {tag_coll} ORDER BY isdefault DESC, sortorder, id', null,
                IGNORE_MULTIPLE)) {
            $tcid = $DB->insert_record('tag_coll', array('isdefault' => 1, 'sortorder' => 0));
        }
        $DB->execute('UPDATE {tag} SET tagcollid = ? WHERE tagcollid IS NULL', array($tcid));

        // Define index tagcollname (unique) to be added to tag.
        $table = new xmldb_table('tag');
        $index = new xmldb_index('tagcollname', XMLDB_INDEX_UNIQUE, array('tagcollid', 'name'));
        $field = new xmldb_field('tagcollid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Conditionally launch add index tagcollname.
        if (!$dbman->index_exists($table, $index)) {
            // Launch change of nullability for field tagcollid.
            $dbman->change_field_notnull($table, $field);
            $dbman->add_index($table, $index);
        }

        // Define key tagcollid (foreign) to be added to tag.
        $table = new xmldb_table('tag');
        $key = new xmldb_key('tagcollid', XMLDB_KEY_FOREIGN, array('tagcollid'), 'tag_coll', array('id'));

        // Launch add key tagcloudid.
        $dbman->add_key($table, $key);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.02);
    }

    if ($oldversion < 2016011300.03) {

        // Define table tag_area to be created.
        $table = new xmldb_table('tag_area');

        // Adding fields to table tag_area.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemtype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('tagcollid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('callback', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('callbackfile', XMLDB_TYPE_CHAR, '100', null, null, null, null);

        // Adding keys to table tag_area.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('tagcollid', XMLDB_KEY_FOREIGN, array('tagcollid'), 'tag_coll', array('id'));

        // Adding indexes to table tag_area.
        $table->add_index('compitemtype', XMLDB_INDEX_UNIQUE, array('component', 'itemtype'));

        // Conditionally launch create table for tag_area.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.03);
    }

    if ($oldversion < 2016011300.04) {

        // Define index itemtype-itemid-tagid-tiuserid (unique) to be dropped form tag_instance.
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('itemtype-itemid-tagid-tiuserid', XMLDB_INDEX_UNIQUE,
                array('itemtype', 'itemid', 'tagid', 'tiuserid'));

        // Conditionally launch drop index itemtype-itemid-tagid-tiuserid.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.04);
    }

    if ($oldversion < 2016011300.05) {

        $DB->execute("UPDATE {tag_instance} SET component = ? WHERE component IS NULL", array(''));

        // Changing nullability of field component on table tag_instance to not null.
        $table = new xmldb_table('tag_instance');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'tagid');

        // Launch change of nullability for field component.
        $dbman->change_field_notnull($table, $field);

        // Changing type of field itemtype on table tag_instance to char.
        $table = new xmldb_table('tag_instance');
        $field = new xmldb_field('itemtype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'component');

        // Launch change of type for field itemtype.
        $dbman->change_field_type($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.05);
    }

    if ($oldversion < 2016011300.06) {

        // Define index taggeditem (unique) to be added to tag_instance.
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component', 'itemtype', 'itemid', 'tiuserid', 'tagid'));

        // Conditionally launch add index taggeditem.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.06);
    }

    if ($oldversion < 2016011300.07) {

        // Define index taglookup (not unique) to be added to tag_instance.
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taglookup', XMLDB_INDEX_NOTUNIQUE, array('itemtype', 'component', 'tagid', 'contextid'));

        // Conditionally launch add index taglookup.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.07);
    }

    if ($oldversion < 2016011301.00) {

        // Force uninstall of deleted tool.
        if (!file_exists("$CFG->dirroot/webservice/amf")) {
            // Remove capabilities.
            capabilities_cleanup('webservice_amf');
            // Remove all other associated config.
            unset_all_config_for_plugin('webservice_amf');
        }
        upgrade_main_savepoint(true, 2016011301.00);
    }

    if ($oldversion < 2016011901.00) {

        // Convert calendar_lookahead to nearest new value.
        $transaction = $DB->start_delegated_transaction();

        // Count all users who curretly have that preference set (for progress bar).
        $total = $DB->count_records_select('user_preferences', "name = 'calendar_lookahead' AND value != '0'");
        $pbar = new progress_bar('upgradecalendarlookahead', 500, true);

        // Get all these users, one at a time.
        $rs = $DB->get_recordset_select('user_preferences', "name = 'calendar_lookahead' AND value != '0'");
        $i = 0;
        foreach ($rs as $userpref) {

            // Calculate and set new lookahead value.
            if ($userpref->value > 90) {
                $newvalue = 120;
            } else if ($userpref->value > 60 and $userpref->value < 90) {
                $newvalue = 90;
            } else if ($userpref->value > 30 and $userpref->value < 60) {
                $newvalue = 60;
            } else if ($userpref->value > 21 and $userpref->value < 30) {
                $newvalue = 30;
            } else if ($userpref->value > 14 and $userpref->value < 21) {
                $newvalue = 21;
            } else if ($userpref->value > 7 and $userpref->value < 14) {
                $newvalue = 14;
            } else {
                $newvalue = $userpref->value;
            }

            $DB->set_field('user_preferences', 'value', $newvalue, array('id' => $userpref->id));

            // Update progress.
            $i++;
            $pbar->update($i, $total, "Upgrading user preference settings - $i/$total.");
        }
        $rs->close();
        $transaction->allow_commit();

        upgrade_main_savepoint(true, 2016011901.00);
    }

    if ($oldversion < 2016020200.00) {

        // Define field isstandard to be added to tag.
        $table = new xmldb_table('tag');
        $field = new xmldb_field('isstandard', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'rawname');

        // Conditionally launch add field isstandard.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index tagcolltype (not unique) to be dropped form tag.
        // This index is no longer created however it was present at some point and it's better to be safe and try to drop it.
        $index = new xmldb_index('tagcolltype', XMLDB_INDEX_NOTUNIQUE, array('tagcollid', 'tagtype'));

        // Conditionally launch drop index tagcolltype.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index tagcolltype (not unique) to be added to tag.
        $index = new xmldb_index('tagcolltype', XMLDB_INDEX_NOTUNIQUE, array('tagcollid', 'isstandard'));

        // Conditionally launch add index tagcolltype.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field tagtype to be dropped from tag.
        $field = new xmldb_field('tagtype');

        // Conditionally launch drop field tagtype and update isstandard.
        if ($dbman->field_exists($table, $field)) {
            $DB->execute("UPDATE {tag} SET isstandard=(CASE WHEN (tagtype = ?) THEN 1 ELSE 0 END)", array('official'));
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016020200.00);
    }

    if ($oldversion < 2016020201.00) {

        // Define field showstandard to be added to tag_area.
        $table = new xmldb_table('tag_area');
        $field = new xmldb_field('showstandard', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'callbackfile');

        // Conditionally launch add field showstandard.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // By default set user area to hide standard tags. 2 = core_tag_tag::HIDE_STANDARD (can not use constant here).
        $DB->execute("UPDATE {tag_area} SET showstandard = ? WHERE itemtype = ? AND component = ?",
            array(2, 'user', 'core'));

        // Changing precision of field enabled on table tag_area to (1).
        $table = new xmldb_table('tag_area');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'itemtype');

        // Launch change of precision for field enabled.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016020201.00);
    }

    if ($oldversion < 2016021500.00) {
        $root = $CFG->tempdir . '/download';
        if (is_dir($root)) {
            // Fetch each repository type - include all repos, not just enabled.
            $repositories = $DB->get_records('repository', array(), '', 'type');

            foreach ($repositories as $id => $repository) {
                $directory = $root . '/repository_' . $repository->type;
                if (is_dir($directory)) {
                    fulldelete($directory);
                }
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016021500.00);
    }

    if ($oldversion < 2016021501.00) {
        // This could take a long time. Unfortunately, no way to know how long, and no way to do progress, so setting for 1 hour.
        upgrade_set_timeout(3600);

        // Define index userid-itemid (not unique) to be added to grade_grades_history.
        $table = new xmldb_table('grade_grades_history');
        $index = new xmldb_index('userid-itemid-timemodified', XMLDB_INDEX_NOTUNIQUE, array('userid', 'itemid', 'timemodified'));

        // Conditionally launch add index userid-itemid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016021501.00);
    }

    if ($oldversion < 2016030103.00) {

        // MDL-50887. Implement plugins infrastructure for antivirus and create ClamAV plugin.
        // This routine moves core ClamAV configuration to plugin level.

        // If clamav was configured and enabled, enable the plugin.
        if (!empty($CFG->runclamonupload) && !empty($CFG->pathtoclam)) {
            set_config('antiviruses', 'clamav');
        } else {
            set_config('antiviruses', '');
        }

        if (isset($CFG->runclamonupload)) {
            // Just unset global configuration, we have already enabled the plugin
            // which implies that ClamAV will be used for scanning uploaded files.
            unset_config('runclamonupload');
        }
        // Move core ClamAV configuration settings to plugin.
        if (isset($CFG->pathtoclam)) {
            set_config('pathtoclam', $CFG->pathtoclam, 'antivirus_clamav');
            unset_config('pathtoclam');
        }
        if (isset($CFG->quarantinedir)) {
            set_config('quarantinedir', $CFG->quarantinedir, 'antivirus_clamav');
            unset_config('quarantinedir');
        }
        if (isset($CFG->clamfailureonupload)) {
            set_config('clamfailureonupload', $CFG->clamfailureonupload, 'antivirus_clamav');
            unset_config('clamfailureonupload');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016030103.00);
    }

    if ($oldversion < 2016030400.01) {
        // Add the new services field.
        $table = new xmldb_table('external_functions');
        $field = new xmldb_field('services', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'capabilities');

        // Conditionally launch add field services.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016030400.01);
    }

    if ($oldversion < 2016041500.50) {

        // Define table competency to be created.
        $table = new xmldb_table('competency');

        // Adding fields to table competency.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('competencyframeworkid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('parentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruletype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('ruleoutcome', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('ruleconfig', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('scaleconfiguration', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency.
        $table->add_index('idnumberframework', XMLDB_INDEX_UNIQUE, array('competencyframeworkid', 'idnumber'));
        $table->add_index('ruleoutcome', XMLDB_INDEX_NOTUNIQUE, array('ruleoutcome'));

        // Conditionally launch create table for competency.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.50);
    }

    if ($oldversion < 2016041500.51) {

        // Define table competency_coursecompsetting to be created.
        $table = new xmldb_table('competency_coursecompsetting');

        // Adding fields to table competency_coursecompsetting.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('pushratingstouserplans', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_coursecompsetting.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseidlink', XMLDB_KEY_FOREIGN_UNIQUE, array('courseid'), 'course', array('id'));

        // Conditionally launch create table for competency_coursecompsetting.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.51);
    }

    if ($oldversion < 2016041500.52) {

        // Define table competency_framework to be created.
        $table = new xmldb_table('competency_framework');

        // Adding fields to table competency_framework.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('scaleconfiguration', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('taxonomies', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_framework.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_framework.
        $table->add_index('idnumber', XMLDB_INDEX_UNIQUE, array('idnumber'));

        // Conditionally launch create table for competency_framework.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.52);
    }

    if ($oldversion < 2016041500.53) {

        // Define table competency_coursecomp to be created.
        $table = new xmldb_table('competency_coursecomp');

        // Adding fields to table competency_coursecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruleoutcome', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_coursecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseidlink', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->add_key('competencyid', XMLDB_KEY_FOREIGN, array('competencyid'), 'competency_competency', array('id'));

        // Adding indexes to table competency_coursecomp.
        $table->add_index('courseidruleoutcome', XMLDB_INDEX_NOTUNIQUE, array('courseid', 'ruleoutcome'));
        $table->add_index('courseidcompetencyid', XMLDB_INDEX_UNIQUE, array('courseid', 'competencyid'));

        // Conditionally launch create table for competency_coursecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.53);
    }

    if ($oldversion < 2016041500.54) {

        // Define table competency_plan to be created.
        $table = new xmldb_table('competency_plan');

        // Adding fields to table competency_plan.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('origtemplateid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('duedate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('reviewerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_plan.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_plan.
        $table->add_index('useridstatus', XMLDB_INDEX_NOTUNIQUE, array('userid', 'status'));
        $table->add_index('templateid', XMLDB_INDEX_NOTUNIQUE, array('templateid'));
        $table->add_index('statusduedate', XMLDB_INDEX_NOTUNIQUE, array('status', 'duedate'));

        // Conditionally launch create table for competency_plan.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.54);
    }

    if ($oldversion < 2016041500.55) {

        // Define table competency_template to be created.
        $table = new xmldb_table('competency_template');

        // Adding fields to table competency_template.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('duedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_template.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for competency_template.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.55);
    }

    if ($oldversion < 2016041500.56) {

        // Define table competency_templatecomp to be created.
        $table = new xmldb_table('competency_templatecomp');

        // Adding fields to table competency_templatecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_templatecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('templateidlink', XMLDB_KEY_FOREIGN, array('templateid'), 'competency_template', array('id'));
        $table->add_key('competencyid', XMLDB_KEY_FOREIGN, array('competencyid'), 'competency_competency', array('id'));

        // Conditionally launch create table for competency_templatecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.56);
    }

    if ($oldversion < 2016041500.57) {

        // Define table competency_templatecohort to be created.
        $table = new xmldb_table('competency_templatecohort');

        // Adding fields to table competency_templatecohort.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cohortid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_templatecohort.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_templatecohort.
        $table->add_index('templateid', XMLDB_INDEX_NOTUNIQUE, array('templateid'));
        $table->add_index('templatecohortids', XMLDB_INDEX_UNIQUE, array('templateid', 'cohortid'));

        // Conditionally launch create table for competency_templatecohort.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.57);
    }

    if ($oldversion < 2016041500.58) {

        // Define table competency_relatedcomp to be created.
        $table = new xmldb_table('competency_relatedcomp');

        // Adding fields to table competency_relatedcomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('relatedcompetencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_relatedcomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for competency_relatedcomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.58);
    }

    if ($oldversion < 2016041500.59) {

        // Define table competency_usercomp to be created.
        $table = new xmldb_table('competency_usercomp');

        // Adding fields to table competency_usercomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('reviewerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_usercomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_usercomp.
        $table->add_index('useridcompetency', XMLDB_INDEX_UNIQUE, array('userid', 'competencyid'));

        // Conditionally launch create table for competency_usercomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.59);
    }

    if ($oldversion < 2016041500.60) {

        // Define table competency_usercompcourse to be created.
        $table = new xmldb_table('competency_usercompcourse');

        // Adding fields to table competency_usercompcourse.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_usercompcourse.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_usercompcourse.
        $table->add_index('useridcoursecomp', XMLDB_INDEX_UNIQUE, array('userid', 'courseid', 'competencyid'));

        // Conditionally launch create table for competency_usercompcourse.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.60);
    }

    if ($oldversion < 2016041500.61) {

        // Define table competency_usercompplan to be created.
        $table = new xmldb_table('competency_usercompplan');

        // Adding fields to table competency_usercompplan.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_usercompplan.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_usercompplan.
        $table->add_index('usercompetencyplan', XMLDB_INDEX_UNIQUE, array('userid', 'competencyid', 'planid'));

        // Conditionally launch create table for competency_usercompplan.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.61);
    }

    if ($oldversion < 2016041500.62) {

        // Define table competency_plancomp to be created.
        $table = new xmldb_table('competency_plancomp');

        // Adding fields to table competency_plancomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_plancomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_plancomp.
        $table->add_index('planidcompetencyid', XMLDB_INDEX_UNIQUE, array('planid', 'competencyid'));

        // Conditionally launch create table for competency_plancomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.62);
    }

    if ($oldversion < 2016041500.63) {

        // Define table competency_evidence to be created.
        $table = new xmldb_table('competency_evidence');

        // Adding fields to table competency_evidence.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('usercompetencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('actionuserid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('descidentifier', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('desccomponent', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('desca', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('note', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_evidence.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_evidence.
        $table->add_index('usercompetencyid', XMLDB_INDEX_NOTUNIQUE, array('usercompetencyid'));

        // Conditionally launch create table for competency_evidence.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.63);
    }

    if ($oldversion < 2016041500.64) {

        // Define table competency_userevidence to be created.
        $table = new xmldb_table('competency_userevidence');

        // Adding fields to table competency_userevidence.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_userevidence.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_userevidence.
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch create table for competency_userevidence.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.64);
    }

    if ($oldversion < 2016041500.65) {

        // Define table competency_userevidencecomp to be created.
        $table = new xmldb_table('competency_userevidencecomp');

        // Adding fields to table competency_userevidencecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userevidenceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_userevidencecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_userevidencecomp.
        $table->add_index('userevidenceid', XMLDB_INDEX_NOTUNIQUE, array('userevidenceid'));
        $table->add_index('userevidencecompids', XMLDB_INDEX_UNIQUE, array('userevidenceid', 'competencyid'));

        // Conditionally launch create table for competency_userevidencecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.65);
    }

    if ($oldversion < 2016041500.66) {

        // Define table competency_modulecomp to be created.
        $table = new xmldb_table('competency_modulecomp');

        // Adding fields to table competency_modulecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruleoutcome', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_modulecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('cmidkey', XMLDB_KEY_FOREIGN, array('cmid'), 'course_modules', array('id'));
        $table->add_key('competencyidkey', XMLDB_KEY_FOREIGN, array('competencyid'), 'competency_competency', array('id'));

        // Adding indexes to table competency_modulecomp.
        $table->add_index('cmidruleoutcome', XMLDB_INDEX_NOTUNIQUE, array('cmid', 'ruleoutcome'));
        $table->add_index('cmidcompetencyid', XMLDB_INDEX_UNIQUE, array('cmid', 'competencyid'));

        // Conditionally launch create table for competency_modulecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.66);
    }

    if ($oldversion < 2016042100.00) {
        // Update all countries to upper case.
        $DB->execute("UPDATE {user} SET country = UPPER(country)");
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016042100.00);
    }

    if ($oldversion < 2016042600.01) {
        $deprecatedwebservices = [
            'moodle_course_create_courses',
            'moodle_course_get_courses',
            'moodle_enrol_get_enrolled_users',
            'moodle_enrol_get_users_courses',
            'moodle_enrol_manual_enrol_users',
            'moodle_file_get_files',
            'moodle_file_upload',
            'moodle_group_add_groupmembers',
            'moodle_group_create_groups',
            'moodle_group_delete_groupmembers',
            'moodle_group_delete_groups',
            'moodle_group_get_course_groups',
            'moodle_group_get_groupmembers',
            'moodle_group_get_groups',
            'moodle_message_send_instantmessages',
            'moodle_notes_create_notes',
            'moodle_role_assign',
            'moodle_role_unassign',
            'moodle_user_create_users',
            'moodle_user_delete_users',
            'moodle_user_get_course_participants_by_id',
            'moodle_user_get_users_by_courseid',
            'moodle_user_get_users_by_id',
            'moodle_user_update_users',
            'core_grade_get_definitions',
            'core_user_get_users_by_id',
            'moodle_webservice_get_siteinfo',
            'mod_forum_get_forum_discussions'
        ];

        list($insql, $params) = $DB->get_in_or_equal($deprecatedwebservices);
        $DB->delete_records_select('external_functions', "name $insql", $params);
        $DB->delete_records_select('external_services_functions', "functionname $insql", $params);
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016042600.01);
    }

    if ($oldversion < 2016051300.00) {
        // Add a default competency rating scale.
        make_competence_scale();

        // Savepoint reached.
        upgrade_main_savepoint(true, 2016051300.00);
    }

    if ($oldversion < 2016051700.01) {
        // This script is included in each major version upgrade process (3.0, 3.1) so make sure we don't run it twice.
        if (empty($CFG->upgrade_letterboundarycourses)) {
            // MDL-45390. If a grade is being displayed with letters and the grade boundaries are not being adhered to properly
            // then this course will also be frozen.
            // If the changes are accepted then the display of some grades may change.
            // This is here to freeze the gradebook in affected courses.
            upgrade_course_letter_boundary();

            // To skip running the same script on the upgrade to the next major version release.
            set_config('upgrade_letterboundarycourses', 1);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016051700.01);
    }

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016081700.00) {

        // If someone is emotionally attached to it let's leave the config (basically the version) there.
        if (!file_exists($CFG->dirroot . '/report/search/classes/output/form.php')) {
            unset_all_config_for_plugin('report_search');
        }

        // Savepoint reached.
        upgrade_main_savepoint(true, 2016081700.00);
    }

    if ($oldversion < 2016081700.02) {
        // Default schedule values.
        $hour = 0;
        $minute = 0;

        // Get the old settings.
        if (isset($CFG->statsruntimestarthour)) {
            $hour = $CFG->statsruntimestarthour;
        }
        if (isset($CFG->statsruntimestartminute)) {
            $minute = $CFG->statsruntimestartminute;
        }

        // Retrieve the scheduled task record first.
        $stattask = $DB->get_record('task_scheduled', array('component' => 'moodle', 'classname' => '\core\task\stats_cron_task'));

        // Don't touch customised scheduling.
        if ($stattask && !$stattask->customised) {

            $nextruntime = mktime($hour, $minute, 0, date('m'), date('d'), date('Y'));
            if ($nextruntime < $stattask->lastruntime) {
                // Add 24 hours to the next run time.
                $newtime = new DateTime();
                $newtime->setTimestamp($nextruntime);
                $newtime->add(new DateInterval('P1D'));
                $nextruntime = $newtime->getTimestamp();
            }
            $stattask->nextruntime = $nextruntime;
            $stattask->minute = $minute;
            $stattask->hour = $hour;
            $stattask->customised = 1;
            $DB->update_record('task_scheduled', $stattask);
        }
        // These settings are no longer used.
        unset_config('statsruntimestarthour');
        unset_config('statsruntimestartminute');
        unset_config('statslastexecution');

        upgrade_main_savepoint(true, 2016081700.02);
    }

    if ($oldversion < 2016082200.00) {
        // An upgrade step to remove any duplicate stamps, within the same context, in the question_categories table, and to
        // add a unique index to (contextid, stamp) to avoid future stamp duplication. See MDL-54864.

        // Extend the execution time limit of the script to 2 hours.
        upgrade_set_timeout(7200);

        // This SQL fetches the id of those records which have duplicate stamps within the same context.
        // This doesn't return the original record within the context, from which the duplicate stamps were likely created.
        $fromclause = "FROM (
                        SELECT min(id) AS minid, contextid, stamp
                            FROM {question_categories}
                            GROUP BY contextid, stamp
                        ) minid
                        JOIN {question_categories} qc
                            ON qc.contextid = minid.contextid AND qc.stamp = minid.stamp AND qc.id > minid.minid";

        // Get the total record count - used for the progress bar.
        $countduplicatessql = "SELECT count(qc.id) $fromclause";
        $total = $DB->count_records_sql($countduplicatessql);

        // Get the records themselves.
        $getduplicatessql = "SELECT qc.id $fromclause ORDER BY minid";
        $rs = $DB->get_recordset_sql($getduplicatessql);

        // For each duplicate, update the stamp to a new random value.
        $i = 0;
        $pbar = new progress_bar('updatequestioncategorystamp', 500, true);
        foreach ($rs as $record) {
            // Generate a new, unique stamp and update the record.
            do {
                $newstamp = make_unique_id_code();
            } while (isset($usedstamps[$newstamp]));
            $usedstamps[$newstamp] = 1;
            $DB->set_field('question_categories', 'stamp', $newstamp, array('id' => $record->id));

            // Update progress.
            $i++;
            $pbar->update($i, $total, "Updating duplicate question category stamp - $i/$total.");
        }
        unset($usedstamps);

        // The uniqueness of each (contextid, stamp) pair is now guaranteed, so add the unique index to stop future duplicates.
        $table = new xmldb_table('question_categories');
        $index = new xmldb_index('contextidstamp', XMLDB_INDEX_UNIQUE, array('contextid', 'stamp'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Savepoint reached.
        upgrade_main_savepoint(true, 2016082200.00);
    }

    if ($oldversion < 2016091900.00) {

        // Removing the themes from core.
        $themes = array('base', 'canvas');

        foreach ($themes as $key => $theme) {
            if (check_dir_exists($CFG->dirroot . '/theme/' . $theme, false)) {
                // Ignore the themes that have been re-downloaded.
                unset($themes[$key]);
            }
        }

        if (!empty($themes)) {
            // Hacky emulation of plugin uninstallation.
            foreach ($themes as $theme) {
                unset_all_config_for_plugin('theme_' . $theme);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016091900.00);
    }

    if ($oldversion < 2016091900.02) {

        // Define index attemptstepid-name (unique) to be dropped from question_attempt_step_data.
        $table = new xmldb_table('question_attempt_step_data');
        $index = new xmldb_index('attemptstepid-name', XMLDB_INDEX_UNIQUE, array('attemptstepid', 'name'));

        // Conditionally launch drop index attemptstepid-name.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016091900.02);
    }

    if ($oldversion < 2016100300.00) {
        unset_config('enablecssoptimiser');

        upgrade_main_savepoint(true, 2016100300.00);
    }

    if ($oldversion < 2016100501.00) {

        // Define field enddate to be added to course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('enddate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'startdate');

        // Conditionally launch add field enddate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016100501.00);
    }

    if ($oldversion < 2016101100.00) {
        // Define field component to be added to message.
        $table = new xmldb_table('message');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'timeusertodeleted');

        // Conditionally launch add field component.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field eventtype to be added to message.
        $field = new xmldb_field('eventtype', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'component');

        // Conditionally launch add field eventtype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016101100.00);
    }


    if ($oldversion < 2016101101.00) {
        // Define field component to be added to message_read.
        $table = new xmldb_table('message_read');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'timeusertodeleted');

        // Conditionally launch add field component.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field eventtype to be added to message_read.
        $field = new xmldb_field('eventtype', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'component');

        // Conditionally launch add field eventtype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016101101.00);
    }

    if ($oldversion < 2016101401.00) {
        // Clean up repository_alfresco config unless plugin has been manually installed.
        if (!file_exists($CFG->dirroot . '/repository/alfresco/lib.php')) {
            // Remove capabilities.
            capabilities_cleanup('repository_alfresco');
            // Clean config.
            unset_all_config_for_plugin('repository_alfresco');
        }

        // Savepoint reached.
        upgrade_main_savepoint(true, 2016101401.00);
    }

    if ($oldversion < 2016101401.02) {
        $table = new xmldb_table('external_tokens');
        $field = new xmldb_field('privatetoken', XMLDB_TYPE_CHAR, '64', null, null, null, null);

        // Conditionally add privatetoken field to the external_tokens table.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016101401.02);
    }

    if ($oldversion < 2016110202.00) {

        // Force uninstall of deleted authentication plugin.
        if (!file_exists("$CFG->dirroot/auth/radius")) {
            // Leave settings inplace if there are radius users.
            if (!$DB->record_exists('user', array('auth' => 'radius', 'deleted' => 0))) {
                // Remove all other associated config.
                unset_all_config_for_plugin('auth/radius');
                // The version number for radius is in this format.
                unset_all_config_for_plugin('auth_radius');
            }
        }
        upgrade_main_savepoint(true, 2016110202.00);
    }

    if ($oldversion < 2016110300.00) {
        // Remove unused admin email setting.
        unset_config('emailonlyfromreplyaddress');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016110300.00);
    }

    if ($oldversion < 2016110500.00) {

        $oldplayers = [
            'vimeo' => null,
            'mp3' => ['.mp3'],
            'html5video' => ['.mov', '.mp4', '.m4v', '.mpeg', '.mpe', '.mpg', '.ogv', '.webm'],
            'flv' => ['.flv', '.f4v'],
            'html5audio' => ['.aac', '.flac', '.mp3', '.m4a', '.oga', '.ogg', '.wav'],
            'youtube' => null,
            'swf' => null,
        ];

        // Convert hardcoded media players to the settings of the new media player plugin type.
        if (get_config('core', 'media_plugins_sortorder') === false) {
            $enabledplugins = [];
            $videoextensions = [];
            $audioextensions = [];
            foreach ($oldplayers as $oldplayer => $extensions) {
                $settingname = 'core_media_enable_'.$oldplayer;
                if (!empty($CFG->$settingname)) {
                    if ($extensions) {
                        // VideoJS will be used for all media files players that were used previously.
                        $enabledplugins['videojs'] = 'videojs';
                        if ($oldplayer === 'mp3' || $oldplayer === 'html5audio') {
                            $audioextensions += array_combine($extensions, $extensions);
                        } else {
                            $videoextensions += array_combine($extensions, $extensions);
                        }
                    } else {
                        // Enable youtube, vimeo and swf.
                        $enabledplugins[$oldplayer] = $oldplayer;
                    }
                }
            }

            set_config('media_plugins_sortorder', join(',', $enabledplugins));

            // Configure VideoJS to match the existing players set up.
            if ($enabledplugins['videojs']) {
                $enabledplugins[] = 'videojs';
                set_config('audioextensions', join(',', $audioextensions), 'media_videojs');
                set_config('videoextensions', join(',', $videoextensions), 'media_videojs');
                $useflash = !empty($CFG->core_media_enable_flv) || !empty($CFG->core_media_enable_mp3);
                set_config('useflash', $useflash, 'media_videojs');
                if (empty($CFG->core_media_enable_youtube)) {
                    // Normally YouTube is enabled in videojs, but if youtube converter was disabled before upgrade
                    // disable it in videojs as well.
                    set_config('youtube', false, 'media_videojs');
                }
            }
        }

        // Unset old settings.
        foreach ($oldplayers as $oldplayer => $extensions) {
            unset_config('core_media_enable_' . $oldplayer);
        }

        // Preset defaults if CORE_MEDIA_VIDEO_WIDTH and CORE_MEDIA_VIDEO_HEIGHT are specified in config.php .
        // After this upgrade step these constants will not be used any more.
        if (defined('CORE_MEDIA_VIDEO_WIDTH')) {
            set_config('media_default_width', CORE_MEDIA_VIDEO_WIDTH);
        }
        if (defined('CORE_MEDIA_VIDEO_HEIGHT')) {
            set_config('media_default_height', CORE_MEDIA_VIDEO_HEIGHT);
        }

        // Savepoint reached.
        upgrade_main_savepoint(true, 2016110500.00);
    }

    if ($oldversion < 2016110600.00) {
        // Define a field 'deletioninprogress' in the 'course_modules' table, to background deletion tasks.
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('deletioninprogress', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'availability');

        // Conditionally launch add field 'deletioninprogress'.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016110600.00);
    }

    if ($oldversion < 2016112200.01) {

        // Define field requiredbytheme to be added to block_instances.
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('requiredbytheme', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'showinsubcontexts');

        // Conditionally launch add field requiredbytheme.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016112200.01);
    }
    if ($oldversion < 2016112200.02) {

        // Change the existing site level admin and settings blocks to be requiredbytheme which means they won't show in boost.
        $context = context_system::instance();
        $params = array('blockname' => 'settings', 'parentcontextid' => $context->id);
        $DB->set_field('block_instances', 'requiredbytheme', 1, $params);

        $params = array('blockname' => 'navigation', 'parentcontextid' => $context->id);
        $DB->set_field('block_instances', 'requiredbytheme', 1, $params);
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016112200.02);
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
