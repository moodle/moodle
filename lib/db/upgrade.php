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
    // allowed version to upgrade from (v3.2.0 right now).
    if ($oldversion < 2016120500) {
        // Just in case somebody hacks upgrade scripts or env, we really can not continue.
        echo("You need to upgrade to 3.2.x or higher first!\n");
        exit(1);
        // Note this savepoint is 100% unreachable, but needed to pass the upgrade checks.
        upgrade_main_savepoint(true, 2016120500);
    }

    if ($oldversion < 2016122800.00) {
        // Find all roles with the coursecreator archetype.
        $coursecreatorroleids = $DB->get_records('role', array('archetype' => 'coursecreator'), '', 'id');

        $context = context_system::instance();
        $capability = 'moodle/site:configview';

        foreach ($coursecreatorroleids as $roleid => $notused) {

            // Check that the capability has not already been assigned. If it has then it's either already set
            // to allow or specifically set to prohibit or prevent.
            if (!$DB->record_exists('role_capabilities', array('roleid' => $roleid, 'capability' => $capability))) {
                // Assign the capability.
                $cap = new stdClass();
                $cap->contextid    = $context->id;
                $cap->roleid       = $roleid;
                $cap->capability   = $capability;
                $cap->permission   = CAP_ALLOW;
                $cap->timemodified = time();
                $cap->modifierid   = 0;

                $DB->insert_record('role_capabilities', $cap);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016122800.00);
    }

    if ($oldversion < 2017020200.01) {

        // Define index useridfrom_timeuserfromdeleted_notification (not unique) to be added to message.
        $table = new xmldb_table('message');
        $index = new xmldb_index('useridfrom_timeuserfromdeleted_notification', XMLDB_INDEX_NOTUNIQUE, array('useridfrom', 'timeuserfromdeleted', 'notification'));

        // Conditionally launch add index useridfrom_timeuserfromdeleted_notification.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index useridto_timeusertodeleted_notification (not unique) to be added to message.
        $index = new xmldb_index('useridto_timeusertodeleted_notification', XMLDB_INDEX_NOTUNIQUE, array('useridto', 'timeusertodeleted', 'notification'));

        // Conditionally launch add index useridto_timeusertodeleted_notification.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('useridto', XMLDB_INDEX_NOTUNIQUE, array('useridto'));

        // Conditionally launch drop index useridto.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017020200.01);
    }

    if ($oldversion < 2017020200.02) {

        // Define index useridfrom_timeuserfromdeleted_notification (not unique) to be added to message_read.
        $table = new xmldb_table('message_read');
        $index = new xmldb_index('useridfrom_timeuserfromdeleted_notification', XMLDB_INDEX_NOTUNIQUE, array('useridfrom', 'timeuserfromdeleted', 'notification'));

        // Conditionally launch add index useridfrom_timeuserfromdeleted_notification.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index useridto_timeusertodeleted_notification (not unique) to be added to message_read.
        $index = new xmldb_index('useridto_timeusertodeleted_notification', XMLDB_INDEX_NOTUNIQUE, array('useridto', 'timeusertodeleted', 'notification'));

        // Conditionally launch add index useridto_timeusertodeleted_notification.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('useridto', XMLDB_INDEX_NOTUNIQUE, array('useridto'));

        // Conditionally launch drop index useridto.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017020200.02);
    }

    if ($oldversion < 2017020901.00) {

        // Delete "orphaned" block positions. Note, the query does not use indexes (because there are none),
        // if it runs too long during upgrade you can comment this line - it will leave orphaned records
        // in the database but they won't bother you.
        upgrade_block_positions();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017020901.00);
    }

    if ($oldversion < 2017021300.00) {
        unset_config('loginpasswordautocomplete');
        upgrade_main_savepoint(true, 2017021300.00);
    }

    if ($oldversion < 2017021400.00) {
        // Define field visibleoncoursepage to be added to course_modules.
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('visibleoncoursepage', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'visible');

        // Conditionally launch add field visibleoncoursepage.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017021400.00);
    }

    if ($oldversion < 2017030700.00) {

        // Define field priority to be added to event.
        $table = new xmldb_table('event');
        $field = new xmldb_field('priority', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'subscriptionid');

        // Conditionally launch add field priority.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017030700.00);
    }

    if ($oldversion < 2017031400.00) {

        // Define table file_conversion to be created.
        $table = new xmldb_table('file_conversion');

        // Adding fields to table file_conversion.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sourcefileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('targetformat', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('statusmessage', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('converter', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('destfileid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table file_conversion.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('sourcefileid', XMLDB_KEY_FOREIGN, array('sourcefileid'), 'files', array('id'));
        $table->add_key('destfileid', XMLDB_KEY_FOREIGN, array('destfileid'), 'files', array('id'));

        // Conditionally launch create table for file_conversion.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017031400.00);
    }

    if ($oldversion < 2017040400.00) {

        // If block_course_overview is no longer present, replace with block_myoverview.
        if (!file_exists($CFG->dirroot . '/blocks/course_overview/block_course_overview.php')) {
            $DB->set_field('block_instances', 'blockname', 'myoverview', array('blockname' => 'course_overview'));
        }

        upgrade_main_savepoint(true, 2017040400.00);
    }

    if ($oldversion < 2017040401.00) {

        // If block_course_overview is no longer present, remove it.
        // Note - we do not need to completely remove the block context etc because we
        // have replaced all occurrences of block_course_overview with block_myoverview
        // in the upgrade step above.
        if (!file_exists($CFG->dirroot . '/blocks/course_overview/block_course_overview.php')) {
            // Delete the block from the block table.
            $DB->delete_records('block', array('name' => 'course_overview'));
            // Remove capabilities.
            capabilities_cleanup('block_course_overview');
            // Clean config.
            unset_all_config_for_plugin('block_course_overview');
        }

        upgrade_main_savepoint(true, 2017040401.00);
    }

    if ($oldversion < 2017040402.00) {

        // Define fields to be added to the 'event' table.
        $table = new xmldb_table('event');
        $fieldtype = new xmldb_field('type', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, 0, 'instance');
        $fieldtimesort = new xmldb_field('timesort', XMLDB_TYPE_INTEGER, '10', null, false, null, null, 'timeduration');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $fieldtype)) {
            $dbman->add_field($table, $fieldtype);
        }

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $fieldtimesort)) {
            $dbman->add_field($table, $fieldtimesort);
        }

        // Now, define the index we will be adding.
        $index = new xmldb_index('type-timesort', XMLDB_INDEX_NOTUNIQUE, array('type', 'timesort'));

        // Conditionally launch add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2017040402.00);
    }

    if ($oldversion < 2017040700.01) {

        // Define table oauth2_issuer to be created.
        $table = new xmldb_table('oauth2_issuer');

        // Adding fields to table oauth2_issuer.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('image', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('baseurl', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('clientid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('clientsecret', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('loginscopes', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('loginscopesoffline', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('loginparams', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('loginparamsoffline', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('alloweddomains', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('scopessupported', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('showonloginpage', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table oauth2_issuer.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for oauth2_issuer.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017040700.01);
    }

    if ($oldversion < 2017040700.02) {

        // Define table oauth2_endpoint to be created.
        $table = new xmldb_table('oauth2_endpoint');

        // Adding fields to table oauth2_endpoint.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table oauth2_endpoint.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('issuer_id_key', XMLDB_KEY_FOREIGN, array('issuerid'), 'oauth2_issuer', array('id'));

        // Conditionally launch create table for oauth2_endpoint.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017040700.02);
    }

    if ($oldversion < 2017040700.03) {

        // Define table oauth2_system_account to be created.
        $table = new xmldb_table('oauth2_system_account');

        // Adding fields to table oauth2_system_account.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('refreshtoken', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('grantedscopes', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('username', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('email', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table oauth2_system_account.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('issueridkey', XMLDB_KEY_FOREIGN_UNIQUE, array('issuerid'), 'oauth2_issuer', array('id'));

        // Conditionally launch create table for oauth2_system_account.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017040700.03);
    }

    if ($oldversion < 2017040700.04) {

        // Define table oauth2_user_field_mapping to be created.
        $table = new xmldb_table('oauth2_user_field_mapping');

        // Adding fields to table oauth2_user_field_mapping.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('externalfield', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('internalfield', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table oauth2_user_field_mapping.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('issuerkey', XMLDB_KEY_FOREIGN, array('issuerid'), 'oauth2_issuer', array('id'));
        $table->add_key('uniqinternal', XMLDB_KEY_UNIQUE, array('issuerid', 'internalfield'));

        // Conditionally launch create table for oauth2_user_field_mapping.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017040700.04);
    }

    if ($oldversion < 2017041801.00) {

        // Define table course_completion_defaults to be created.
        $table = new xmldb_table('course_completion_defaults');

        // Adding fields to table course_completion_defaults.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('module', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('completion', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('completionview', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('completionusegrade', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('completionexpected', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('customrules', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table course_completion_defaults.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('module', XMLDB_KEY_FOREIGN, array('module'), 'modules', array('id'));
        $table->add_key('course', XMLDB_KEY_FOREIGN, array('course'), 'course', array('id'));

        // Adding indexes to table course_completion_defaults.
        $table->add_index('coursemodule', XMLDB_INDEX_UNIQUE, array('course', 'module'));

        // Conditionally launch create table for course_completion_defaults.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_main_savepoint(true, 2017041801.00);
    }

    if ($oldversion < 2017050500.01) {
        // Get the list of parent event IDs.
        $sql = "SELECT DISTINCT repeatid
                           FROM {event}
                          WHERE repeatid <> 0";
        $parentids = array_keys($DB->get_records_sql($sql));
        // Check if there are repeating events we need to process.
        if (!empty($parentids)) {
            // The repeat IDs of parent events should match their own ID.
            // So we need to update parent events that have non-matching IDs and repeat IDs.
            list($insql, $params) = $DB->get_in_or_equal($parentids);
            $updatesql = "UPDATE {event}
                             SET repeatid = id
                           WHERE id <> repeatid
                                 AND id $insql";
            $DB->execute($updatesql, $params);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017050500.01);
    }

    if ($oldversion < 2017050500.02) {
        // MDL-58684:
        // Remove all portfolio_tempdata records as these may contain serialized \file_system type objects, which are now unable to
        // be unserialized because of changes to the file storage API made in MDL-46375. Portfolio now stores an id reference to
        // files instead of the object.
        // These records are normally removed after a successful export, however, can be left behind if the user abandons the
        // export attempt (a stale record). Additionally, each stale record cannot be reused and is normally cleaned up by the cron
        // task core\task\portfolio_cron_task. Since the cron task tries to unserialize them, and generates a warning, we'll remove
        // all records here.
        $DB->delete_records_select('portfolio_tempdata', 'id > ?', [0]);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017050500.02);
    }

    if ($oldversion < 2017050900.01) {
        // Create adhoc task for upgrading of existing calendar events.
        $record = new \stdClass();
        $record->classname = '\core\task\refresh_mod_calendar_events_task';
        $record->component = 'core';

        // Next run time based from nextruntime computation in \core\task\manager::queue_adhoc_task().
        $nextruntime = time() - 1;
        $record->nextruntime = $nextruntime;
        $DB->insert_record('task_adhoc', $record);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017050900.01);
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017061201.00) {
        $table = new xmldb_table('course_sections');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'availability');

        // Define a field 'timemodified' in the 'course_sections' table.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2017061201.00);
    }

    if ($oldversion < 2017061301.00) {
        // Check if the value of 'navcourselimit' is set to the old default value, if so, change it to the new default.
        if ($CFG->navcourselimit == 20) {
            set_config('navcourselimit', 10);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017061301.00);
    }

    if ($oldversion < 2017071000.00) {

        // Define field requireconfirmation to be added to oauth2_issuer.
        $table = new xmldb_table('oauth2_issuer');
        $field = new xmldb_field('requireconfirmation', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'sortorder');

        // Conditionally launch add field requireconfirmation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017071000.00);
    }

    if ($oldversion < 2017071001.00) {

        // Define field timemodified to be added to block_instances.
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null,
                null, null, 'configdata');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Set field to current time.
            $DB->set_field('block_instances', 'timemodified', time());

            // Changing nullability of field timemodified on table block_instances to not null.
            $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL,
                    null, null, 'configdata');

            // Launch change of nullability for field timemodified.
            $dbman->change_field_notnull($table, $field);

            // Define index timemodified (not unique) to be added to block_instances.
            $index = new xmldb_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

            // Conditionally launch add index timemodified.
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // Define field timecreated to be added to block_instances.
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null,
                null, null, 'configdata');

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Set field to current time.
            $DB->set_field('block_instances', 'timecreated', time());

            // Changing nullability of field timecreated on table block_instances to not null.
            $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL,
                    null, null, 'configdata');

            // Launch change of nullability for field timecreated.
            $dbman->change_field_notnull($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017071001.00);
    }

    if ($oldversion < 2017071100.00 ) {
        // Clean old upgrade setting not used anymore.
        unset_config('upgrade_minmaxgradestepignored');
        upgrade_main_savepoint(true, 2017071100.00);
    }

    if ($oldversion < 2017072000.02) {

        // Define table analytics_models to be created.
        $table = new xmldb_table('analytics_models');

        // Adding fields to table analytics_models.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('trained', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('target', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('indicators', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesplitting', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('version', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table analytics_models.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table analytics_models.
        $table->add_index('enabledandtrained', XMLDB_INDEX_NOTUNIQUE, array('enabled', 'trained'));

        // Conditionally launch create table for analytics_models.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table analytics_models_log to be created.
        $table = new xmldb_table('analytics_models_log');

        // Adding fields to table analytics_models_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('modelid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('version', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('target', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('indicators', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesplitting', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('score', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('info', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('dir', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table analytics_models_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table analytics_models_log.
        $table->add_index('modelid', XMLDB_INDEX_NOTUNIQUE, array('modelid'));

        // Conditionally launch create table for analytics_models_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table analytics_predictions to be created.
        $table = new xmldb_table('analytics_predictions');

        // Adding fields to table analytics_predictions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('modelid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sampleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('rangeindex', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('prediction', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('predictionscore', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('calculations', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table analytics_predictions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table analytics_predictions.
        $table->add_index('modelidandcontextid', XMLDB_INDEX_NOTUNIQUE, array('modelid', 'contextid'));

        // Conditionally launch create table for analytics_predictions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table analytics_train_samples to be created.
        $table = new xmldb_table('analytics_train_samples');

        // Adding fields to table analytics_train_samples.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('modelid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('analysableid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesplitting', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sampleids', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table analytics_train_samples.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table analytics_train_samples.
        $table->add_index('modelidandanalysableidandtimesplitting', XMLDB_INDEX_NOTUNIQUE,
            array('modelid', 'analysableid', 'timesplitting'));

        // Conditionally launch create table for analytics_train_samples.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table analytics_predict_ranges to be created.
        $table = new xmldb_table('analytics_predict_ranges');

        // Adding fields to table analytics_predict_ranges.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('modelid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('analysableid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesplitting', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('rangeindex', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table analytics_predict_ranges.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table analytics_predict_ranges.
        $table->add_index('modelidandanalysableidandtimesplitting', XMLDB_INDEX_NOTUNIQUE,
            array('modelid', 'analysableid', 'timesplitting'));

        // Conditionally launch create table for analytics_predict_ranges.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table analytics_used_files to be created.
        $table = new xmldb_table('analytics_used_files');

        // Adding fields to table analytics_used_files.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('modelid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('fileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('action', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table analytics_used_files.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table analytics_used_files.
        $table->add_index('modelidandfileidandaction', XMLDB_INDEX_NOTUNIQUE, array('modelid', 'fileid', 'action'));

        // Conditionally launch create table for analytics_used_files.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $now = time();
        $admin = get_admin();

        $targetname = '\core\analytics\target\course_dropout';
        if (!$DB->record_exists('analytics_models', array('target' => $targetname))) {
            // We can not use API calls to create the built-in models.
            $modelobj = new stdClass();
            $modelobj->target = $targetname;
            $modelobj->indicators = json_encode(array(
                '\mod_assign\analytics\indicator\cognitive_depth',
                '\mod_assign\analytics\indicator\social_breadth',
                '\mod_book\analytics\indicator\cognitive_depth',
                '\mod_book\analytics\indicator\social_breadth',
                '\mod_chat\analytics\indicator\cognitive_depth',
                '\mod_chat\analytics\indicator\social_breadth',
                '\mod_choice\analytics\indicator\cognitive_depth',
                '\mod_choice\analytics\indicator\social_breadth',
                '\mod_data\analytics\indicator\cognitive_depth',
                '\mod_data\analytics\indicator\social_breadth',
                '\mod_feedback\analytics\indicator\cognitive_depth',
                '\mod_feedback\analytics\indicator\social_breadth',
                '\mod_folder\analytics\indicator\cognitive_depth',
                '\mod_folder\analytics\indicator\social_breadth',
                '\mod_forum\analytics\indicator\cognitive_depth',
                '\mod_forum\analytics\indicator\social_breadth',
                '\mod_glossary\analytics\indicator\cognitive_depth',
                '\mod_glossary\analytics\indicator\social_breadth',
                '\mod_imscp\analytics\indicator\cognitive_depth',
                '\mod_imscp\analytics\indicator\social_breadth',
                '\mod_label\analytics\indicator\cognitive_depth',
                '\mod_label\analytics\indicator\social_breadth',
                '\mod_lesson\analytics\indicator\cognitive_depth',
                '\mod_lesson\analytics\indicator\social_breadth',
                '\mod_lti\analytics\indicator\cognitive_depth',
                '\mod_lti\analytics\indicator\social_breadth',
                '\mod_page\analytics\indicator\cognitive_depth',
                '\mod_page\analytics\indicator\social_breadth',
                '\mod_quiz\analytics\indicator\cognitive_depth',
                '\mod_quiz\analytics\indicator\social_breadth',
                '\mod_resource\analytics\indicator\cognitive_depth',
                '\mod_resource\analytics\indicator\social_breadth',
                '\mod_scorm\analytics\indicator\cognitive_depth',
                '\mod_scorm\analytics\indicator\social_breadth',
                '\mod_survey\analytics\indicator\cognitive_depth',
                '\mod_survey\analytics\indicator\social_breadth',
                '\mod_url\analytics\indicator\cognitive_depth',
                '\mod_url\analytics\indicator\social_breadth',
                '\mod_wiki\analytics\indicator\cognitive_depth',
                '\mod_wiki\analytics\indicator\social_breadth',
                '\mod_workshop\analytics\indicator\cognitive_depth',
                '\mod_workshop\analytics\indicator\social_breadth',
            ));
            $modelobj->version = $now;
            $modelobj->timecreated = $now;
            $modelobj->timemodified = $now;
            $modelobj->usermodified = $admin->id;
            $DB->insert_record('analytics_models', $modelobj);
        }

        $targetname = '\core\analytics\target\no_teaching';
        if (!$DB->record_exists('analytics_models', array('target' => $targetname))) {
            $modelobj = new stdClass();
            $modelobj->enabled = 1;
            $modelobj->trained = 1;
            $modelobj->target = $targetname;
            $modelobj->indicators = json_encode(array('\core_course\analytics\indicator\no_teacher'));
            $modelobj->timesplitting = '\core\analytics\time_splitting\single_range';
            $modelobj->version = $now;
            $modelobj->timecreated = $now;
            $modelobj->timemodified = $now;
            $modelobj->usermodified = $admin->id;
            $DB->insert_record('analytics_models', $modelobj);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017072000.02);
    }

    if ($oldversion < 2017072700.01) {
        // Changing nullability of field email on table oauth2_system_account to null.
        $table = new xmldb_table('oauth2_system_account');
        $field = new xmldb_field('email', XMLDB_TYPE_TEXT, null, null, null, null, null, 'grantedscopes');

        // Launch change of nullability for field email.
        $dbman->change_field_notnull($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017072700.01);
    }

    if ($oldversion < 2017072700.02) {

        // If the site was previously registered with http://hub.moodle.org change the registration to
        // point to https://moodle.net - this is the correct hub address using https protocol.
        $oldhuburl = "http://hub.moodle.org";
        $newhuburl = "https://moodle.net";
        $cleanoldhuburl = preg_replace('/[^A-Za-z0-9_-]/i', '', $oldhuburl);
        $cleannewhuburl = preg_replace('/[^A-Za-z0-9_-]/i', '', $newhuburl);

        // Update existing registration.
        $DB->execute("UPDATE {registration_hubs} SET hubname = ?, huburl = ? WHERE huburl = ?",
            ['Moodle.net', $newhuburl, $oldhuburl]);

        // Update settings of existing registration.
        $sqlnamelike = $DB->sql_like('name', '?');
        $entries = $DB->get_records_sql("SELECT * FROM {config_plugins} where plugin=? and " . $sqlnamelike,
            ['hub', '%' . $DB->sql_like_escape('_' . $cleanoldhuburl)]);
        foreach ($entries as $entry) {
            $newname = substr($entry->name, 0, -strlen($cleanoldhuburl)) . $cleannewhuburl;
            try {
                $DB->update_record('config_plugins', ['id' => $entry->id, 'name' => $newname]);
            } catch (dml_exception $e) {
                // Entry with new name already exists, remove the one with an old name.
                $DB->delete_records('config_plugins', ['id' => $entry->id]);
            }
        }

        // Update published courses.
        $DB->execute('UPDATE {course_published} SET huburl = ? WHERE huburl = ?', [$newhuburl, $oldhuburl]);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017072700.02);
    }

    if ($oldversion < 2017080700.01) {

        // Get the table by its previous name.
        $table = new xmldb_table('analytics_predict_ranges');
        if ($dbman->table_exists($table)) {

            // We can only accept this because we are in master.
            $DB->delete_records('analytics_predictions');
            $DB->delete_records('analytics_used_files', array('action' => 'predicted'));
            $DB->delete_records('analytics_predict_ranges');

            // Define field sampleids to be added to analytics_predict_ranges (renamed below to analytics_predict_samples).
            $field = new xmldb_field('sampleids', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'rangeindex');

            // Conditionally launch add field sampleids.
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            // Define field timemodified to be added to analytics_predict_ranges (renamed below to analytics_predict_samples).
            $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');

            // Conditionally launch add field timemodified.
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            // Rename the table to its new name.
            $dbman->rename_table($table, 'analytics_predict_samples');
        }

        $table = new xmldb_table('analytics_predict_samples');

        $index = new xmldb_index('modelidandanalysableidandtimesplitting', XMLDB_INDEX_NOTUNIQUE,
            array('modelid', 'analysableid', 'timesplitting'));

        // Conditionally launch drop index.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('modelidandanalysableidandtimesplittingandrangeindex', XMLDB_INDEX_NOTUNIQUE,
            array('modelid', 'analysableid', 'timesplitting', 'rangeindex'));

        // Conditionally launch add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017080700.01);
    }

    if ($oldversion < 2017082200.00) {
        $plugins = ['radius', 'fc', 'nntp', 'pam', 'pop3', 'imap'];

        foreach ($plugins as $plugin) {
            // Check to see if the plugin exists on disk.
            // If it does not, remove the config for it.
            if (!file_exists($CFG->dirroot . "/auth/{$plugin}/auth.php")) {
                // Clean config.
                unset_all_config_for_plugin("auth_{$plugin}");
            }
        }
        upgrade_main_savepoint(true, 2017082200.00);
    }

    if ($oldversion < 2017082200.01) {

        // Define table analytics_indicator_calc to be created.
        $table = new xmldb_table('analytics_indicator_calc');

        // Adding fields to table analytics_indicator_calc.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('starttime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('endtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sampleorigin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sampleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('indicator', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_NUMBER, '10, 2', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table analytics_indicator_calc.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table analytics_indicator_calc.
        $table->add_index('starttime-endtime-contextid', XMLDB_INDEX_NOTUNIQUE, array('starttime', 'endtime', 'contextid'));

        // Conditionally launch create table for analytics_indicator_calc.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017082200.01);
    }

    if ($oldversion < 2017082300.01) {

        // This script in included in each major version upgrade process so make sure we don't run it twice.
        if (empty($CFG->linkcoursesectionsupgradescriptwasrun)) {
            // Check if the site is using a boost-based theme.
            // If value of 'linkcoursesections' is set to the old default value, change it to the new default.
            if (upgrade_theme_is_from_family('boost', $CFG->theme)) {
                set_config('linkcoursesections', 1);
            }
            set_config('linkcoursesectionsupgradescriptwasrun', 1);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017082300.01);
    }

    if ($oldversion < 2017082500.00) {
        // Handle FKs for the table 'analytics_models_log'.
        $table = new xmldb_table('analytics_models_log');

        // Remove the existing index before adding FK (which creates an index).
        $index = new xmldb_index('modelid', XMLDB_INDEX_NOTUNIQUE, array('modelid'));

        // Conditionally launch drop index.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Now, add the FK.
        $key = new xmldb_key('modelid', XMLDB_KEY_FOREIGN, array('modelid'), 'analytics_models', array('id'));
        $dbman->add_key($table, $key);

        // Handle FKs for the table 'analytics_predictions'.
        $table = new xmldb_table('analytics_predictions');
        $key = new xmldb_key('modelid', XMLDB_KEY_FOREIGN, array('modelid'), 'analytics_models', array('id'));
        $dbman->add_key($table, $key);

        $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $dbman->add_key($table, $key);

        // Handle FKs for the table 'analytics_train_samples'.
        $table = new xmldb_table('analytics_train_samples');
        $key = new xmldb_key('modelid', XMLDB_KEY_FOREIGN, array('modelid'), 'analytics_models', array('id'));
        $dbman->add_key($table, $key);

        $key = new xmldb_key('fileid', XMLDB_KEY_FOREIGN, array('fileid'), 'files', array('id'));
        $dbman->add_key($table, $key);

        // Handle FKs for the table 'analytics_predict_samples'.
        $table = new xmldb_table('analytics_predict_samples');
        $key = new xmldb_key('modelid', XMLDB_KEY_FOREIGN, array('modelid'), 'analytics_models', array('id'));
        $dbman->add_key($table, $key);

        // Handle FKs for the table 'analytics_used_files'.
        $table = new xmldb_table('analytics_used_files');
        $key = new xmldb_key('modelid', XMLDB_KEY_FOREIGN, array('modelid'), 'analytics_models', array('id'));
        $dbman->add_key($table, $key);

        $key = new xmldb_key('fileid', XMLDB_KEY_FOREIGN, array('fileid'), 'files', array('id'));
        $dbman->add_key($table, $key);

        // Handle FKs for the table 'analytics_indicator_calc'.
        $table = new xmldb_table('analytics_indicator_calc');
        $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $dbman->add_key($table, $key);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017082500.00);
    }

    if ($oldversion < 2017082800.00) {

        // Changing type of field prediction on table analytics_predictions to number.
        $table = new xmldb_table('analytics_predictions');
        $field = new xmldb_field('prediction', XMLDB_TYPE_NUMBER, '10, 2', null, XMLDB_NOTNULL, null, null, 'rangeindex');

        // Launch change of type for field prediction.
        $dbman->change_field_type($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017082800.00);
    }

    if ($oldversion < 2017090700.01) {

        // Define table analytics_prediction_actions to be created.
        $table = new xmldb_table('analytics_prediction_actions');

        // Adding fields to table analytics_prediction_actions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('predictionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('actionname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table analytics_prediction_actions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('predictionid', XMLDB_KEY_FOREIGN, array('predictionid'), 'analytics_predictions', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Adding indexes to table analytics_prediction_actions.
        $table->add_index('predictionidanduseridandactionname', XMLDB_INDEX_NOTUNIQUE,
            array('predictionid', 'userid', 'actionname'));

        // Conditionally launch create table for analytics_prediction_actions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017090700.01);
    }

    if ($oldversion < 2017091200.00) {
        // Force all messages to be reindexed.
        set_config('core_message_message_sent_lastindexrun', '0', 'core_search');
        set_config('core_message_message_received_lastindexrun', '0', 'core_search');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017091200.00);
    }

    if ($oldversion < 2017091201.00) {
        // Define field userid to be added to task_adhoc.
        $table = new xmldb_table('task_adhoc');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'customdata');

        // Conditionally launch add field userid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $key = new xmldb_key('useriduser', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key userid_user.
        $dbman->add_key($table, $key);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017091201.00);
    }

    if ($oldversion < 2017092201.00) {

        // Remove duplicate registrations.
        $newhuburl = "https://moodle.net";
        $registrations = $DB->get_records('registration_hubs', ['huburl' => $newhuburl], 'confirmed DESC, id ASC');
        if (count($registrations) > 1) {
            $reg = array_shift($registrations);
            $DB->delete_records_select('registration_hubs', 'huburl = ? AND id <> ?', [$newhuburl, $reg->id]);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017092201.00);
    }

    if ($oldversion < 2017092202.00) {

        if (!file_exists($CFG->dirroot . '/blocks/messages/block_messages.php')) {

            // Delete instances.
            $instances = $DB->get_records_list('block_instances', 'blockname', ['messages']);
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
            $DB->delete_records('block', array('name' => 'messages'));

            // Remove capabilities.
            capabilities_cleanup('block_messages');

            // Clean config.
            unset_all_config_for_plugin('block_messages');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017092202.00);
    }

    if ($oldversion < 2017092700.00) {

        // Rename several fields in registration data to match the names of the properties that are sent to moodle.net.
        $renames = [
            'site_address_httpsmoodlenet' => 'site_street_httpsmoodlenet',
            'site_region_httpsmoodlenet' => 'site_regioncode_httpsmoodlenet',
            'site_country_httpsmoodlenet' => 'site_countrycode_httpsmoodlenet'];
        foreach ($renames as $oldparamname => $newparamname) {
            try {
                $DB->execute("UPDATE {config_plugins} SET name = ? WHERE name = ? AND plugin = ?",
                    [$newparamname, $oldparamname, 'hub']);
            } catch (dml_exception $e) {
                // Exception can happen if the config value with the new name already exists, ignore it and move on.
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017092700.00);
    }

    if ($oldversion < 2017092900.00) {
        // Define field categoryid to be added to event.
        $table = new xmldb_table('event');
        $field = new xmldb_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'format');

        // Conditionally launch add field categoryid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add the categoryid key.
        $key = new xmldb_key('categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'course_categories', array('id'));
        $dbman->add_key($table, $key);

        // Add a new index for groupid/courseid/categoryid/visible/userid.
        // Do this before we remove the old index.
        $index = new xmldb_index('groupid-courseid-categoryid-visible-userid', XMLDB_INDEX_NOTUNIQUE, array('groupid', 'courseid', 'categoryid', 'visible', 'userid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Drop the old index.
        $index = new xmldb_index('groupid-courseid-visible-userid', XMLDB_INDEX_NOTUNIQUE, array('groupid', 'courseid', 'visible', 'userid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017092900.00);
    }

    if ($oldversion < 2017100900.00) {
        // Add index on time modified to grade_outcomes_history, grade_categories_history,
        // grade_items_history, and scale_history.
        $table = new xmldb_table('grade_outcomes_history');
        $index = new xmldb_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('grade_items_history');
        $index = new xmldb_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('grade_categories_history');
        $index = new xmldb_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('scale_history');
        $index = new xmldb_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017100900.00);
    }

    if ($oldversion < 2017101000.00) {

        // Define table analytics_used_analysables to be created.
        $table = new xmldb_table('analytics_used_analysables');

        // Adding fields to table analytics_used_analysables.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('modelid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('analysableid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timeanalysed', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table analytics_used_analysables.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('modelid', XMLDB_KEY_FOREIGN, array('modelid'), 'analytics_models', array('id'));

        // Adding indexes to table analytics_used_analysables.
        $table->add_index('modelid-action', XMLDB_INDEX_NOTUNIQUE, array('modelid', 'action'));

        // Conditionally launch create table for analytics_used_analysables.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017101000.00);
    }

    if ($oldversion < 2017101000.01) {
        // Define field override to be added to course_modules_completion.
        $table = new xmldb_table('course_modules_completion');
        $field = new xmldb_field('overrideby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'viewed');

        // Conditionally launch add field override.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017101000.01);
    }

    if ($oldversion < 2017101000.02) {
        // Define field 'timestart' to be added to 'analytics_predictions'.
        $table = new xmldb_table('analytics_predictions');
        $field = new xmldb_field('timestart', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timecreated');

        // Conditionally launch add field 'timestart'.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field 'timeend' to be added to 'analytics_predictions'.
        $field = new xmldb_field('timeend', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timestart');

        // Conditionally launch add field 'timeend'.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017101000.02);
    }

    if ($oldversion < 2017101200.00) {
        // Define table search_index_requests to be created.
        $table = new xmldb_table('search_index_requests');

        // Adding fields to table search_index_requests.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('searcharea', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timerequested', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('partialarea', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('partialtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table search_index_requests.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

        // Conditionally launch create table for search_index_requests.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017101200.00);
    }

    // Index modification upgrade step.
    if ($oldversion < 2017101300.01) {

        $table = new xmldb_table('analytics_used_files');

        // Define index modelidandfileidandaction (not unique) to be dropped form analytics_used_files.
        $index = new xmldb_index('modelidandfileidandaction', XMLDB_INDEX_NOTUNIQUE, array('modelid', 'fileid', 'action'));

        // Conditionally launch drop index modelidandfileidandaction.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index modelidandactionandfileid (not unique) to be dropped form analytics_used_files.
        $index = new xmldb_index('modelidandactionandfileid', XMLDB_INDEX_NOTUNIQUE, array('modelid', 'action', 'fileid'));

        // Conditionally launch add index modelidandactionandfileid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017101300.01);
    }

    if ($oldversion < 2017101900.01) {

        $fs = get_file_storage();
        $models = $DB->get_records('analytics_models');
        foreach ($models as $model) {
            $files = $fs->get_directory_files(\context_system::instance()->id, 'analytics', 'unlabelled', $model->id,
                '/analysable/', true, true);
            foreach ($files as $file) {
                $file->delete();
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017101900.01);
    }

    if ($oldversion < 2017101900.02) {
        // Create adhoc task for upgrading of existing calendar events.
        $record = new \stdClass();
        $record->classname = '\core\task\refresh_mod_calendar_events_task';
        $record->component = 'core';

        // Next run time based from nextruntime computation in \core\task\manager::queue_adhoc_task().
        $nextruntime = time() - 1;
        $record->nextruntime = $nextruntime;
        $DB->insert_record('task_adhoc', $record);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017101900.02);
    }

    if ($oldversion < 2017102100.01) {
        // We will need to force them onto ssl if loginhttps is set.
        if (!empty($CFG->loginhttps)) {
            set_config('overridetossl', 1);
        }
        // Loginhttps should no longer be set.
        unset_config('loginhttps');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017102100.01);
    }

    if ($oldversion < 2017110300.01) {

        // Define field categoryid to be added to event_subscriptions.
        $table = new xmldb_table('event_subscriptions');
        $field = new xmldb_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'url');

        // Conditionally launch add field categoryid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017110300.01);
    }

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017111300.02) {

        // Define field basicauth to be added to oauth2_issuer.
        $table = new xmldb_table('oauth2_issuer');
        $field = new xmldb_field('basicauth', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'showonloginpage');

        // Conditionally launch add field basicauth.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017111300.02);
    }

    if ($oldversion < 2017121200.00) {

        // Define key subscriptionid (foreign) to be added to event.
        $table = new xmldb_table('event');
        $key = new xmldb_key('subscriptionid', XMLDB_KEY_FOREIGN, array('subscriptionid'), 'event_subscriptions', array('id'));

        // Launch add key subscriptionid.
        $dbman->add_key($table, $key);

        // Define index uuid (not unique) to be added to event.
        $table = new xmldb_table('event');
        $index = new xmldb_index('uuid', XMLDB_INDEX_NOTUNIQUE, array('uuid'));

        // Conditionally launch add index uuid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017121200.00);
    }

    if ($oldversion < 2017121900.00) {

        // Define table role_allow_view to be created.
        $table = new xmldb_table('role_allow_view');

        // Adding fields to table role_allow_view.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('allowview', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table role_allow_view.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));
        $table->add_key('allowview', XMLDB_KEY_FOREIGN, array('allowview'), 'role', array('id'));

        // Conditionally launch create table for role_allow_view.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $index = new xmldb_index('roleid-allowview', XMLDB_INDEX_UNIQUE, array('roleid', 'allowview'));

        // Conditionally launch add index roleid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $roles = $DB->get_records('role', array(), 'sortorder ASC');

        $DB->delete_records('role_allow_view');
        foreach ($roles as $role) {
            foreach ($roles as $allowedrole) {
                $record = new stdClass();
                $record->roleid      = $role->id;
                $record->allowview = $allowedrole->id;
                $DB->insert_record('role_allow_view', $record);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017121900.00);
    }

    if ($oldversion < 2017122200.01) {

        // Define field indexpriority to be added to search_index_requests. Allow null initially.
        $table = new xmldb_table('search_index_requests');
        $field = new xmldb_field('indexpriority', XMLDB_TYPE_INTEGER, '10',
                null, null, null, null, 'partialtime');

        // Conditionally add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Set existing values to 'normal' value (100).
            $DB->set_field('search_index_requests', 'indexpriority', 100);

            // Now make the field 'NOT NULL'.
            $field = new xmldb_field('indexpriority', XMLDB_TYPE_INTEGER, '10',
                    null, XMLDB_NOTNULL, null, null, 'partialtime');
            $dbman->change_field_notnull($table, $field);
        }

        // Define index indexprioritytimerequested (not unique) to be added to search_index_requests.
        $index = new xmldb_index('indexprioritytimerequested', XMLDB_INDEX_NOTUNIQUE,
                array('indexpriority', 'timerequested'));

        // Conditionally launch add index indexprioritytimerequested.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2017122200.01);
    }

    if ($oldversion < 2018020500.00) {

        $topcategory = new stdClass();
        $topcategory->name = 'top'; // A non-real name for the top category. It will be localised at the display time.
        $topcategory->info = '';
        $topcategory->parent = 0;
        $topcategory->sortorder = 0;

        // Get the total record count - used for the progress bar.
        $total = $DB->count_records_sql("SELECT COUNT(DISTINCT contextid) FROM {question_categories} WHERE parent = 0");

        // Get the records themselves - a list of contextids.
        $rs = $DB->get_recordset_sql("SELECT DISTINCT contextid FROM {question_categories} WHERE parent = 0");

        // For each context, create a single top-level category.
        $i = 0;
        $pbar = new progress_bar('createtopquestioncategories', 500, true);
        foreach ($rs as $contextid => $notused) {
            $topcategory->contextid = $contextid;
            $topcategory->stamp = make_unique_id_code();

            $topcategoryid = $DB->insert_record('question_categories', $topcategory);

            $DB->set_field_select('question_categories', 'parent', $topcategoryid,
                    'contextid = ? AND id <> ? AND parent = 0',
                    array($contextid, $topcategoryid));

            // Update progress.
            $i++;
            $pbar->update($i, $total, "Creating top-level question categories - $i/$total.");
        }

        $rs->close();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018020500.00);
    }

    if ($oldversion < 2018022800.01) {
        // Fix old block configurations that use the deprecated (and now removed) object class.
        upgrade_fix_block_instance_configuration();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018022800.01);
    }

    if ($oldversion < 2018022800.02) {
        // Define index taggeditem (unique) to be dropped form tag_instance.
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component',
            'itemtype', 'itemid', 'tiuserid', 'tagid'));

        // Conditionally launch drop index taggeditem.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component',
            'itemtype', 'itemid', 'contextid', 'tiuserid', 'tagid'));

        // Conditionally launch add index taggeditem.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018022800.02);
    }

    if ($oldversion < 2018022800.03) {

        // Define field multiplecontexts to be added to tag_area.
        $table = new xmldb_table('tag_area');
        $field = new xmldb_field('multiplecontexts', XMLDB_TYPE_INTEGER, '1', null,
            XMLDB_NOTNULL, null, '0', 'showstandard');

        // Conditionally launch add field multiplecontexts.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018022800.03);
    }

    if ($oldversion < 2018032200.01) {
        // Define table 'messages' to be created.
        $table = new xmldb_table('messages');

        // Adding fields to table 'messages'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('useridfrom', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('conversationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullmessage', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullmessageformat', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('fullmessagehtml', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('smallmessage', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table 'messages'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('useridfrom', XMLDB_KEY_FOREIGN, array('useridfrom'), 'user', array('id'));
        $table->add_key('conversationid', XMLDB_KEY_FOREIGN, array('conversationid'), 'message_conversations', array('id'));

        // Conditionally launch create table for 'messages'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'message_conversations' to be created.
        $table = new xmldb_table('message_conversations');

        // Adding fields to table 'message_conversations'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table 'message_conversations'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for 'message_conversations'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'message_conversation_members' to be created.
        $table = new xmldb_table('message_conversation_members');

        // Adding fields to table 'message_conversation_members'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('conversationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table 'message_conversation_members'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('conversationid', XMLDB_KEY_FOREIGN, array('conversationid'), 'message_conversations', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for 'message_conversation_members'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'message_user_actions' to be created.
        $table = new xmldb_table('message_user_actions');

        // Adding fields to table 'message_user_actions'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('messageid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table 'message_user_actions'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('messageid', XMLDB_KEY_FOREIGN, array('messageid'), 'messages', array('id'));

        // Conditionally launch create table for 'message_user_actions'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'notifications' to be created.
        $table = new xmldb_table('notifications');

        // Adding fields to table 'notifications'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('useridfrom', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('useridto', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullmessage', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullmessageformat', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('fullmessagehtml', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('smallmessage', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('eventtype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('contexturl', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('contexturlname', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timeread', XMLDB_TYPE_INTEGER, '10', null, false, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table 'notifications'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('useridto', XMLDB_KEY_FOREIGN, array('useridto'), 'user', array('id'));

        // Conditionally launch create table for 'notifications'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018032200.01);
    }

    if ($oldversion < 2018032200.04) {
        // Define table 'message_conversations' to be updated.
        $table = new xmldb_table('message_conversations');
        $field = new xmldb_field('convhash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field 'convhash'.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally launch add index.
        $index = new xmldb_index('convhash', XMLDB_INDEX_UNIQUE, array('convhash'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018032200.04);
    }

    if ($oldversion < 2018032200.05) {
        // Drop table that is no longer needed.
        $table = new xmldb_table('message_working');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018032200.05);
    }

    if ($oldversion < 2018032200.06) {
        // Define table 'message_user_actions' to add an index to.
        $table = new xmldb_table('message_user_actions');

        // Conditionally launch add index.
        $index = new xmldb_index('userid_messageid_action', XMLDB_INDEX_UNIQUE, array('userid', 'messageid', 'action'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018032200.06);
    }

    if ($oldversion < 2018032200.07) {
        // Define table 'messages' to add an index to.
        $table = new xmldb_table('messages');

        // Conditionally launch add index.
        $index = new xmldb_index('conversationid_timecreated', XMLDB_INDEX_NOTUNIQUE, array('conversationid', 'timecreated'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018032200.07);
    }

    if ($oldversion < 2018032700.00) {
        // Update default search engine to search_simpledb if global search is disabled and there is no solr index defined.
        if (empty($CFG->enableglobalsearch) && empty(get_config('search_solr', 'indexname'))) {
            set_config('searchengine', 'simpledb');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018032700.00);
    }

    if ($oldversion < 2018040500.01) {

        // Define field cohort to be added to theme. Allow null initially.
        $table = new xmldb_table('cohort');
        $field = new xmldb_field('theme', XMLDB_TYPE_CHAR, '50',
                null, null, null, null, 'timemodified');

        // Conditionally add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018040500.01);
    }

    if ($oldversion < 2018050900.01) {
        // Update default digital age consent map according to the current legislation on each country.
        $ageofdigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'AT, 14',
            'ES, 14',
            'US, 13'
        ]);
        set_config('agedigitalconsentmap', $ageofdigitalconsentmap);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018050900.01);
    }

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018062800.01) {
        // Add foreign key fk_user to the comments table.
        $table = new xmldb_table('comments');
        $key = new xmldb_key('fk_user', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $dbman->add_key($table, $key);

        upgrade_main_savepoint(true, 2018062800.01);
    }

    if ($oldversion < 2018062800.02) {
        // Add composite index ix_concomitem to the table comments.
        $table = new xmldb_table('comments');
        $index = new xmldb_index('ix_concomitem', XMLDB_INDEX_NOTUNIQUE, array('contextid', 'commentarea', 'itemid'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2018062800.02);
    }

    if ($oldversion < 2018062800.03) {
        // Define field location to be added to event.
        $table = new xmldb_table('event');
        $field = new xmldb_field('location', XMLDB_TYPE_TEXT, null, null, null, null, null, 'priority');

        // Conditionally launch add field location.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018062800.03);
    }

    if ($oldversion < 2018072500.00) {
        // Find all duplicate top level categories per context.
        $duplicates = $DB->get_records_sql("SELECT qc1.*
                                              FROM {question_categories} qc1
                                              JOIN {question_categories} qc2
                                                ON qc1.contextid = qc2.contextid AND qc1.id <> qc2.id
                                             WHERE qc1.parent = 0 AND qc2.parent = 0
                                          ORDER BY qc1.contextid, qc1.id");

        // For each context, let the first top category to remain as top category and make the rest its children.
        $currentcontextid = 0;
        $chosentopid = 0;
        foreach ($duplicates as $duplicate) {
            if ($currentcontextid != $duplicate->contextid) {
                $currentcontextid = $duplicate->contextid;
                $chosentopid = $duplicate->id;
            } else {
                $DB->set_field('question_categories', 'parent', $chosentopid, ['id' => $duplicate->id]);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018072500.00);
    }

    if ($oldversion < 2018073000.00) {
        // Main savepoint reached.
        if (!file_exists($CFG->dirroot . '/admin/tool/assignmentupgrade/version.php')) {
            unset_all_config_for_plugin('tool_assignmentupgrade');
        }
        upgrade_main_savepoint(true, 2018073000.00);
    }

    if ($oldversion < 2018083100.01) {
        // Remove module associated blog posts for non-existent (deleted) modules.
        $sql = "SELECT ba.contextid as modcontextid
                  FROM {blog_association} ba
                  JOIN {post} p
                       ON p.id = ba.blogid
             LEFT JOIN {context} c
                       ON c.id = ba.contextid
                 WHERE p.module = :module
                       AND c.contextlevel IS NULL
              GROUP BY ba.contextid";
        if ($deletedmodules = $DB->get_records_sql($sql, array('module' => 'blog'))) {
            foreach ($deletedmodules as $module) {
                $assocblogids = $DB->get_fieldset_select('blog_association', 'blogid',
                    'contextid = :contextid', ['contextid' => $module->modcontextid]);
                list($sql, $params) = $DB->get_in_or_equal($assocblogids, SQL_PARAMS_NAMED);

                $DB->delete_records_select('tag_instance', "itemid $sql", $params);
                $DB->delete_records_select('post', "id $sql AND module = :module",
                    array_merge($params, ['module' => 'blog']));
                $DB->delete_records('blog_association', ['contextid' => $module->modcontextid]);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018083100.01);
    }

    if ($oldversion < 2018091200.00) {
        if (!file_exists($CFG->dirroot . '/cache/stores/memcache/settings.php')) {
            unset_all_config_for_plugin('cachestore_memcache');
        }

        upgrade_main_savepoint(true, 2018091200.00);
    }

    if ($oldversion < 2018091700.01) {
        // Remove unused setting.
        unset_config('messaginghidereadnotifications');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018091700.01);
    }

    // Add idnumber fields to question and question_category tables.
    // This is done in four parts to aid error recovery during upgrade, should that occur.
    if ($oldversion < 2018092100.01) {
        $table = new xmldb_table('question');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'modifiedby');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_main_savepoint(true, 2018092100.01);
    }

    if ($oldversion < 2018092100.02) {
        $table = new xmldb_table('question');
        $index = new xmldb_index('categoryidnumber', XMLDB_INDEX_UNIQUE, array('category', 'idnumber'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_main_savepoint(true, 2018092100.02);
    }

    if ($oldversion < 2018092100.03) {
        $table = new xmldb_table('question_categories');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'sortorder');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_main_savepoint(true, 2018092100.03);
    }

    if ($oldversion < 2018092100.04) {
        $table = new xmldb_table('question_categories');
        $index = new xmldb_index('contextididnumber', XMLDB_INDEX_UNIQUE, array('contextid', 'idnumber'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018092100.04);
    }

    if ($oldversion < 2018092800.00) {
        // Alter the table 'message_contacts'.
        $table = new xmldb_table('message_contacts');

        // Remove index so we can alter the fields.
        $index = new xmldb_index('userid-contactid', XMLDB_INDEX_UNIQUE, ['userid', 'contactid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Remove defaults of '0' from the 'userid' and 'contactid' fields.
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_default($table, $field);

        $field = new xmldb_field('contactid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');
        $dbman->change_field_default($table, $field);

        // Add the missing FKs that will now be added to new installs.
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $dbman->add_key($table, $key);

        $key = new xmldb_key('contactid', XMLDB_KEY_FOREIGN, ['contactid'], 'user', ['id']);
        $dbman->add_key($table, $key);

        // Re-add the index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Add the field 'timecreated'. Allow null, since existing records won't have an accurate value we can use.
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'blocked');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Create new 'message_contact_requests' table.
        $table = new xmldb_table('message_contact_requests');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('requesteduserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'requesteduserid');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id'], null, null);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('requesteduserid', XMLDB_KEY_FOREIGN, ['requesteduserid'], 'user', ['id']);

        $table->add_index('userid-requesteduserid', XMLDB_INDEX_UNIQUE, ['userid', 'requesteduserid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Create new 'message_users_blocked' table.
        $table = new xmldb_table('message_users_blocked');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('blockeduserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');
        // Allow NULLs in the 'timecreated' field because we will be moving existing data here that has no timestamp.
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'blockeduserid');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id'], null, null);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('blockeduserid', XMLDB_KEY_FOREIGN, ['blockeduserid'], 'user', ['id']);

        $table->add_index('userid-blockeduserid', XMLDB_INDEX_UNIQUE, ['userid', 'blockeduserid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_main_savepoint(true, 2018092800.00);
    }

    if ($oldversion < 2018092800.01) {
        // Move all the 'blocked' contacts to the new table 'message_users_blocked'.
        $updatesql = "INSERT INTO {message_users_blocked} (userid, blockeduserid, timecreated)
                           SELECT userid, contactid, null as timecreated
                             FROM {message_contacts}
                            WHERE blocked = :blocked";
        $DB->execute($updatesql, ['blocked' => 1]);

        // Removed the 'blocked' column from 'message_contacts'.
        $table = new xmldb_table('message_contacts');
        $field = new xmldb_field('blocked');
        $dbman->drop_field($table, $field);

        upgrade_main_savepoint(true, 2018092800.01);
    }

    if ($oldversion < 2018092800.02) {
        // Delete any contacts that are not mutual (meaning they both haven't added each other).
        $sql = "SELECT c1.id
                  FROM {message_contacts} c1
             LEFT JOIN {message_contacts} c2
                    ON c1.userid = c2.contactid
                   AND c1.contactid = c2.userid
                 WHERE c2.id IS NULL";
        if ($contacts = $DB->get_records_sql($sql)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contacts));
            $DB->delete_records_select('message_contacts', "id $insql", $inparams);
        }

        upgrade_main_savepoint(true, 2018092800.02);
    }

    if ($oldversion < 2018092800.03) {
        // Remove any duplicate rows - from now on adding contacts just requires 1 row.
        // The person who made the contact request (userid) and the person who approved
        // it (contactid). Upgrade the table so that the first person to add the contact
        // was the one who made the request.
        $sql = "SELECT c1.id
                  FROM {message_contacts} c1
            INNER JOIN {message_contacts} c2
                    ON c1.userid = c2.contactid
                   AND c1.contactid = c2.userid
                 WHERE c1.id > c2.id";
        if ($contacts = $DB->get_records_sql($sql)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contacts));
            $DB->delete_records_select('message_contacts', "id $insql", $inparams);
        }

        upgrade_main_savepoint(true, 2018092800.03);
    }

    if ($oldversion < 2018101700.01) {
        if (empty($CFG->keepmessagingallusersenabled)) {
            // When it is not set, $CFG->messagingallusers should be disabled by default.
            // When $CFG->messagingallusers = false, the default user preference is MESSAGE_PRIVACY_COURSEMEMBER
            // (contacted by users sharing a course).
            set_config('messagingallusers', false);
        } else {
            // When $CFG->keepmessagingallusersenabled is set to true, $CFG->messagingallusers is set to true.
            set_config('messagingallusers', true);

            // When $CFG->messagingallusers = true, the default user preference is MESSAGE_PRIVACY_SITE
            // (contacted by all users site). So we need to set existing values from 0 (MESSAGE_PRIVACY_COURSEMEMBER)
            // to 2 (MESSAGE_PRIVACY_SITE).
            $DB->set_field(
                'user_preferences',
                'value',
                \core_message\api::MESSAGE_PRIVACY_SITE,
                array('name' => 'message_blocknoncontacts', 'value' => 0)
            );
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018101700.01);
    }

    if ($oldversion < 2018101800.00) {
        // Define table 'favourite' to be created.
        $table = new xmldb_table('favourite');

        // Adding fields to table favourite.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemtype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ordering', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table favourite.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Adding indexes to table favourite.
        $table->add_index('uniqueuserfavouriteitem', XMLDB_INDEX_UNIQUE, ['component', 'itemtype', 'itemid', 'contextid', 'userid']);

        // Conditionally launch create table for favourite.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018101800.00);
    }

    if ($oldversion < 2018102200.00) {
        // Add field 'type' to 'message_conversations'.
        $table = new xmldb_table('message_conversations');
        $field = new xmldb_field('type', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 1, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add field 'name' to 'message_conversations'.
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'type');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally launch add index 'type'.
        $index = new xmldb_index('type', XMLDB_INDEX_NOTUNIQUE, ['type']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define table 'message_conversations' to be updated.
        $table = new xmldb_table('message_conversations');

        // Remove the unique 'convhash' index, change to null and add a new non unique index.
        $index = new xmldb_index('convhash', XMLDB_INDEX_UNIQUE, ['convhash']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field = new xmldb_field('convhash', XMLDB_TYPE_CHAR, '40', null, null, null, null, 'name');
        $dbman->change_field_notnull($table, $field);

        $index = new xmldb_index('convhash', XMLDB_INDEX_NOTUNIQUE, ['convhash']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2018102200.00);
    }

    if ($oldversion < 2018102300.02) {
        // Alter 'message_conversations' table to support groups.
        $table = new xmldb_table('message_conversations');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'convhash');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('itemtype', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'component');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'itemtype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'itemid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'contextid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'enabled');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add key.
        $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);
        $dbman->add_key($table, $key);

        // Add index.
        $index = new xmldb_index('component-itemtype-itemid-contextid', XMLDB_INDEX_NOTUNIQUE, ['component', 'itemtype',
            'itemid', 'contextid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2018102300.02);
    }

    if ($oldversion < 2018102900.00) {
        // Define field predictionsprocessor to be added to analytics_models.
        $table = new xmldb_table('analytics_models');
        $field = new xmldb_field('predictionsprocessor', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'timesplitting');

        // Conditionally launch add field predictionsprocessor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018102900.00);
    }

    if ($oldversion < 2018110500.01) {
        // Define fields to be added to the 'badge' table.
        $tablebadge = new xmldb_table('badge');
        $fieldversion = new xmldb_field('version', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'nextcron');
        $fieldlanguage = new xmldb_field('language', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'version');
        $fieldimageauthorname = new xmldb_field('imageauthorname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'language');
        $fieldimageauthoremail = new xmldb_field('imageauthoremail', XMLDB_TYPE_CHAR, '255', null, null,
            null, null, 'imageauthorname');
        $fieldimageauthorurl = new xmldb_field('imageauthorurl', XMLDB_TYPE_CHAR, '255', null, null,
            null, null, 'imageauthoremail');
        $fieldimagecaption = new xmldb_field('imagecaption', XMLDB_TYPE_TEXT, null, null, null, null, null, 'imageauthorurl');

        if (!$dbman->field_exists($tablebadge, $fieldversion)) {
            $dbman->add_field($tablebadge, $fieldversion);
        }
        if (!$dbman->field_exists($tablebadge, $fieldlanguage)) {
            $dbman->add_field($tablebadge, $fieldlanguage);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimageauthorname)) {
            $dbman->add_field($tablebadge, $fieldimageauthorname);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimageauthoremail)) {
            $dbman->add_field($tablebadge, $fieldimageauthoremail);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimageauthorurl)) {
            $dbman->add_field($tablebadge, $fieldimageauthorurl);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimagecaption)) {
            $dbman->add_field($tablebadge, $fieldimagecaption);
        }

        // Define table badge_endorsement to be created.
        $table = new xmldb_table('badge_endorsement');

        // Adding fields to table badge_endorsement.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('issuername', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issueremail', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('claimid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('claimcomment', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('dateissued', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table badge_endorsement.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('endorsementbadge', XMLDB_KEY_FOREIGN, ['badgeid'], 'badge', ['id']);

        // Conditionally launch create table for badge_endorsement.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table badge_related to be created.
        $table = new xmldb_table('badge_related');

        // Adding fields to table badge_related.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('relatedbadgeid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table badge_related.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('badgeid', XMLDB_KEY_FOREIGN, ['badgeid'], 'badge', ['id']);
        $table->add_key('relatedbadgeid', XMLDB_KEY_FOREIGN, ['relatedbadgeid'], 'badge', ['id']);
        $table->add_key('badgeid-relatedbadgeid', XMLDB_KEY_UNIQUE, ['badgeid', 'relatedbadgeid']);

        // Conditionally launch create table for badge_related.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table badge_competencies to be created.
        $table = new xmldb_table('badge_competencies');

        // Adding fields to table badge_competencies.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('targetname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('targeturl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('targetdescription', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('targetframework', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('targetcode', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table badge_competencies.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('competenciesbadge', XMLDB_KEY_FOREIGN, ['badgeid'], 'badge', ['id']);

        // Conditionally launch create table for badge_competencies.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018110500.01);
    }

    if ($oldversion < 2018110700.01) {
        // This config setting added and then removed.
        unset_config('showcourseimages', 'moodlecourse');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018110700.01);
    }

    if ($oldversion < 2018111301.00) {
        // Define field locked to be added to context.
        $table = new xmldb_table('context');
        $field = new xmldb_field('locked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'depth');

        // Conditionally launch add field locked.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field locked to be added to context_temp.
        $table = new xmldb_table('context_temp');
        $field = new xmldb_field('locked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'depth');

        // Conditionally launch add field locked.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Note: This change also requires a bump in is_major_upgrade_required.
        upgrade_main_savepoint(true, 2018111301.00);
    }

    if ($oldversion < 2018111900.00) {
        // Update favourited courses, so they are saved in the particular course context instead of the system.
        $favouritedcourses = $DB->get_records('favourite', ['component' => 'core_course', 'itemtype' => 'courses']);

        foreach ($favouritedcourses as $fc) {
            $coursecontext = \context_course::instance($fc->itemid);
            $fc->contextid = $coursecontext->id;
            $DB->update_record('favourite', $fc);
        }

        upgrade_main_savepoint(true, 2018111900.00);
    }

    if ($oldversion < 2018111900.01) {
        // Define table oauth2_access_token to be created.
        $table = new xmldb_table('oauth2_access_token');

        // Adding fields to table oauth2_access_token.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('scope', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table oauth2_access_token.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('issueridkey', XMLDB_KEY_FOREIGN_UNIQUE, ['issuerid'], 'oauth2_issuer', ['id']);

        // Conditionally launch create table for oauth2_access_token.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2018111900.01);
    }

    if ($oldversion < 2018112000.00) {
        // Update favourited conversations, so they are saved in the proper context instead of the system.
        $sql = "SELECT f.*, mc.contextid as conversationctx
                  FROM {favourite} f
                  JOIN {message_conversations} mc
                    ON mc.id = f.itemid";
        $favouritedconversations = $DB->get_records_sql($sql);
        foreach ($favouritedconversations as $fc) {
            if (empty($fc->conversationctx)) {
                $conversationidctx = \context_user::instance($fc->userid)->id;
            } else {
                $conversationidctx = $fc->conversationctx;
            }

            $DB->set_field('favourite', 'contextid', $conversationidctx, ['id' => $fc->id]);
        }

        upgrade_main_savepoint(true, 2018112000.00);
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

    return true;
}
