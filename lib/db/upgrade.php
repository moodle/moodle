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
 * @package    core
 * @subpackage admin
 * @copyright  2006 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @global stdClass $CFG
 * @global stdClass $USER
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_main_upgrade($oldversion) {
    global $CFG, $USER, $DB, $OUTPUT;

    require_once($CFG->libdir.'/db/upgradelib.php'); // Core Upgrade-related functions

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    ////////////////////////////////////////
    ///upgrade supported only from 1.9.x ///
    ////////////////////////////////////////

    if ($oldversion < 2008030600) {
        //NOTE: this table was added much later later in dev cycle, but we need it here, upgrades from pre PR1 not supported

    /// Define table upgrade_log to be created
        $table = new xmldb_table('upgrade_log');

    /// Adding fields to table upgrade_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('version', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('targetversion', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('info', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('details', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('backtrace', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table upgrade_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table upgrade_log
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));
        $table->add_index('type-timemodified', XMLDB_INDEX_NOTUNIQUE, array('type', 'timemodified'));

    /// Create table for upgrade_log
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008030600);
    }

    if ($oldversion < 2008030601) {
        //NOTE: this table was added much later later in dev cycle, but we need it here, upgrades from pre PR1 not supported

    /// Define table log_queries to be created
        $table = new xmldb_table('log_queries');

    /// Adding fields to table log_queries
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('qtype', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sqltext', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sqlparams', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('error', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('info', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('backtrace', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('exectime', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timelogged', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table log_queries
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for log_queries
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008030601);
    }

    if ($oldversion < 2008030602) {
        @unlink($CFG->dataroot.'/cache/languages');

        if (file_exists("$CFG->dataroot/lang")) {
            // rename old lang directory so that the new and old langs do not mix
            if (rename("$CFG->dataroot/lang", "$CFG->dataroot/oldlang")) {
                $oldlang = "$CFG->dataroot/oldlang";
            } else {
                $oldlang = "$CFG->dataroot/lang";
            }
        } else {
            $oldlang = '';
        }
        // TODO: fetch previously installed languages ("*_utf8") found in $oldlang from moodle.org
        upgrade_set_timeout(60*20); // this may take a while


        // TODO: add some info file to $oldlang describing what to do with "$oldlang/*_utf8_local" dirs


        // Main savepoint reached
        upgrade_main_savepoint(true, 2008030602);
    }

    if ($oldversion < 2008030700) {
        upgrade_set_timeout(60*20); // this may take a while

    /// Define index contextid-lowerboundary (not unique) to be dropped form grade_letters
        $table = new xmldb_table('grade_letters');
        $index = new xmldb_index('contextid-lowerboundary', XMLDB_INDEX_NOTUNIQUE, array('contextid', 'lowerboundary'));

    /// Launch drop index contextid-lowerboundary
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

    /// Define index contextid-lowerboundary-letter (unique) to be added to grade_letters
        $table = new xmldb_table('grade_letters');
        $index = new xmldb_index('contextid-lowerboundary-letter', XMLDB_INDEX_UNIQUE, array('contextid', 'lowerboundary', 'letter'));

    /// Launch add index contextid-lowerboundary-letter
        $dbman->add_index($table, $index);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008030700);
    }

    if ($oldversion < 2008050100) {
        // Update courses that used weekscss to weeks
        $DB->set_field('course', 'format', 'weeks', array('format' => 'weekscss'));
        upgrade_main_savepoint(true, 2008050100);
    }

    if ($oldversion < 2008050200) {
        // remove unused config options
        unset_config('statsrolesupgraded');
        upgrade_main_savepoint(true, 2008050200);
    }

    if ($oldversion < 2008050700) {
        upgrade_set_timeout(60*20); // this may take a while

    /// Fix minor problem caused by MDL-5482.
        require_once($CFG->dirroot . '/question/upgrade.php');
        question_fix_random_question_parents();
        upgrade_main_savepoint(true, 2008050700);
    }

    if ($oldversion < 2008051201) {
        echo $OUTPUT->notification('Increasing size of user idnumber field, this may take a while...', 'notifysuccess');
        upgrade_set_timeout(60*20); // this may take a while

    /// Under MySQL and Postgres... detect old NULL contents and change them by correct empty string. MDL-14859
        $dbfamily = $DB->get_dbfamily();
        if ($dbfamily === 'mysql' || $dbfamily === 'postgres') {
            $DB->execute("UPDATE {user} SET idnumber = '' WHERE idnumber IS NULL");
        }

    /// Define index idnumber (not unique) to be dropped form user
        $table = new xmldb_table('user');
        $index = new xmldb_index('idnumber', XMLDB_INDEX_NOTUNIQUE, array('idnumber'));

    /// Launch drop index idnumber
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

    /// Changing precision of field idnumber on table user to (255)
        $table = new xmldb_table('user');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'password');

    /// Launch change of precision for field idnumber
        $dbman->change_field_precision($table, $field);

    /// Launch add index idnumber again
        $index = new xmldb_index('idnumber', XMLDB_INDEX_NOTUNIQUE, array('idnumber'));
        $dbman->add_index($table, $index);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008051201);
    }

    if ($oldversion < 2008051203) {
        $table = new xmldb_table('mnet_enrol_course');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $dbman->change_field_precision($table, $field);
        upgrade_main_savepoint(true, 2008051203);
    }

    if ($oldversion < 2008063001) {
        upgrade_set_timeout(60*20); // this may take a while

        // table to be modified
        $table = new xmldb_table('tag_instance');
        // add field
        $field = new xmldb_field('tiuserid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'itemid');
            $dbman->add_field($table, $field);
        }
        // modify index
        $index = new xmldb_index('itemtype-itemid-tagid');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        $index = new xmldb_index('itemtype-itemid-tagid-tiuserid');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid', 'tiuserid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        /// Main savepoint reached
        upgrade_main_savepoint(true, 2008063001);
    }

    if ($oldversion < 2008070300) {
        $DB->delete_records_select('role_names', $DB->sql_isempty('role_names', 'name', false, false));
        upgrade_main_savepoint(true, 2008070300);
    }

    if ($oldversion < 2008070701) {

    /// Define table portfolio_instance to be created
        $table = new xmldb_table('portfolio_instance');

    /// Adding fields to table portfolio_instance
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');

    /// Adding keys to table portfolio_instance
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for portfolio_instance
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
  /// Define table portfolio_instance_config to be created
        $table = new xmldb_table('portfolio_instance_config');

    /// Adding fields to table portfolio_instance_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

    /// Adding keys to table portfolio_instance_config
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('instance', XMLDB_KEY_FOREIGN, array('instance'), 'portfolio_instance', array('id'));

    /// Adding indexes to table portfolio_instance_config
        $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, array('name'));

    /// Conditionally launch create table for portfolio_instance_config
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

   /// Define table portfolio_instance_user to be created
        $table = new xmldb_table('portfolio_instance_user');

    /// Adding fields to table portfolio_instance_user
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

    /// Adding keys to table portfolio_instance_user
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('instancefk', XMLDB_KEY_FOREIGN, array('instance'), 'portfolio_instance', array('id'));
        $table->add_key('userfk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Conditionally launch create table for portfolio_instance_user
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008070701);
    }

    if ($oldversion < 2008072400) {
    /// Create the database tables for message_processors
        $table = new xmldb_table('message_processors');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

    /// delete old and create new fields
        $table = new xmldb_table('message');
        $field = new xmldb_field('messagetype');
        $dbman->drop_field($table, $field);

    /// fields to rename
        $field = new xmldb_field('message');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->rename_field($table, $field, 'fullmessage');
        $field = new xmldb_field('format');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', null);
        $dbman->rename_field($table, $field, 'fullmessageformat');

    /// new message fields
        $field = new xmldb_field('subject');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('fullmessagehtml');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('smallmessage');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);


        $table = new xmldb_table('message_read');
        $field = new xmldb_field('messagetype');
        $dbman->drop_field($table, $field);
        $field = new xmldb_field('mailed');
        $dbman->drop_field($table, $field);

    /// fields to rename
        $field = new xmldb_field('message');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->rename_field($table, $field, 'fullmessage');
        $field = new xmldb_field('format');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', null);
        $dbman->rename_field($table, $field, 'fullmessageformat');


    /// new message fields
        $field = new xmldb_field('subject');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('fullmessagehtml');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('smallmessage');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);

    /// new table
        $table = new xmldb_table('message_working');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unreadmessageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('processorid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);


        upgrade_main_savepoint(true, 2008072400);
    }

    if ($oldversion < 2008072800) {

    /// Define field enablecompletion to be added to course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enablecompletion');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'defaultrole');

    /// Launch add field enablecompletion
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completion to be added to course_modules
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('completion');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'groupmembersonly');

    /// Launch add field completion
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completiongradeitemnumber to be added to course_modules
        $field = new xmldb_field('completiongradeitemnumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'completion');

    /// Launch add field completiongradeitemnumber
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionview to be added to course_modules
        $field = new xmldb_field('completionview');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completiongradeitemnumber');

    /// Launch add field completionview
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionexpected to be added to course_modules
        $field = new xmldb_field('completionexpected');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completionview');

    /// Launch add field completionexpected
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

   /// Define table course_modules_completion to be created
        $table = new xmldb_table('course_modules_completion');
        if (!$dbman->table_exists($table)) {

        /// Adding fields to table course_modules_completion
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('completionstate', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('viewed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        /// Adding keys to table course_modules_completion
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// Adding indexes to table course_modules_completion
            $table->add_index('coursemoduleid', XMLDB_INDEX_NOTUNIQUE, array('coursemoduleid'));
            $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        /// Launch create table for course_modules_completion
            $dbman->create_table($table);
        }

        /// Main savepoint reached
        upgrade_main_savepoint(true, 2008072800);
    }

    if ($oldversion < 2008073000) {

    /// Define table portfolio_log to be created
        $table = new xmldb_table('portfolio_log');

    /// Adding fields to table portfolio_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('time', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('portfolio', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('caller_class', XMLDB_TYPE_CHAR, '150', null, XMLDB_NOTNULL, null, null);
        $table->add_field('caller_file', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('caller_sha1', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tempdataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('continueurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table portfolio_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userfk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('portfoliofk', XMLDB_KEY_FOREIGN, array('portfolio'), 'portfolio_instance', array('id'));

    /// Conditionally launch create table for portfolio_log
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008073000);
    }

    if ($oldversion < 2008073104) {
    /// Drop old table that might exist for some people
        $table = new xmldb_table('message_providers');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table message_providers to be created
        $table = new xmldb_table('message_providers');

    /// Adding fields to table message_providers
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('capability', XMLDB_TYPE_CHAR, '255', null, null, null, null);

    /// Adding keys to table message_providers
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table message_providers
        $table->add_index('componentname', XMLDB_INDEX_UNIQUE, array('component', 'name'));

    /// Create table for message_providers
        $dbman->create_table($table);

        upgrade_main_savepoint(true, 2008073104);
    }

    if ($oldversion < 2008073111) {
    /// Define table files to be created
        $table = new xmldb_table('files');

    /// Adding fields to table files
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('pathnamehash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filearea', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('filepath', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('filesize', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('mimetype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('source', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('author', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('license', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table files
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table files
        $table->add_index('component-filearea-contextid-itemid', XMLDB_INDEX_NOTUNIQUE, array('component', 'filearea', 'contextid', 'itemid'));
        $table->add_index('contenthash', XMLDB_INDEX_NOTUNIQUE, array('contenthash'));
        $table->add_index('pathnamehash', XMLDB_INDEX_UNIQUE, array('pathnamehash'));

    /// Conditionally launch create table for files
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008073111);
    }

    if ($oldversion < 2008073112) {
        // Define field legacyfiles to be added to course
        $table = new xmldb_table('course');
        $field = new xmldb_field('legacyfiles', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'maxbytes');

        // Launch add field legacyfiles
        $dbman->add_field($table, $field);
        // enable legacy files in all courses
        $DB->execute("UPDATE {course} SET legacyfiles = 2");

        // Main savepoint reached
        upgrade_main_savepoint(true, 2008073112);
    }

    if ($oldversion < 2008073113) {
    /// move all course, backup and other files to new filepool based storage
        upgrade_migrate_files_courses();
    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008073113);
    }

    if ($oldversion < 2008073114) {
    /// move all course, backup and other files to new filepool based storage
        upgrade_migrate_files_blog();
    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008073114);
    }

    if ($oldversion < 2008080400) {
        // Add field ssl_jump_url to mnet application, and populate existing default applications
        $table = new xmldb_table('mnet_application');
        $field = new xmldb_field('sso_jump_url');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $dbman->add_field($table, $field);
            $DB->set_field('mnet_application', 'sso_jump_url', '/auth/mnet/jump.php', array('name' => 'moodle'));
            $DB->set_field('mnet_application', 'sso_jump_url', '/auth/xmlrpc/jump.php', array('name' => 'mahara'));
        }

        /// Main savepoint reached
        upgrade_main_savepoint(true, 2008080400);
    }

    if ($oldversion < 2008080500) {

    /// Define table portfolio_tempdata to be created
        $table = new xmldb_table('portfolio_tempdata');

    /// Adding fields to table portfolio_tempdata
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('expirytime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

    /// Adding keys to table portfolio_tempdata
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userfk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('instance', XMLDB_KEY_FOREIGN, array('instance'), 'portfolio_instance', array('id'));

    /// Conditionally launch create table for portfolio_tempdata
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008080500);
    }

    if ($oldversion < 2008081500) {
    /// Changing the type of all the columns that the question bank uses to store grades to be NUMBER(12, 7).
        $table = new xmldb_table('question');
        $field = new xmldb_field('defaultgrade', XMLDB_TYPE_NUMBER, '12, 7', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1.0000000', 'generalfeedback');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint(true, 2008081500);
    }

    if ($oldversion < 2008081501) {
        $table = new xmldb_table('question');
        $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0.1000000', 'defaultgrade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint(true, 2008081501);
    }

    if ($oldversion < 2008081502) {
        $table = new xmldb_table('question_answers');
        $field = new xmldb_field('fraction', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'answer');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint(true, 2008081502);
    }

    if ($oldversion < 2008081503) {
        $table = new xmldb_table('question_sessions');
        $field = new xmldb_field('sumpenalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'newgraded');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint(true, 2008081503);
    }

    if ($oldversion < 2008081504) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'event');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint(true, 2008081504);
    }

    if ($oldversion < 2008081505) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('raw_grade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'grade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint(true, 2008081505);
    }

    if ($oldversion < 2008081506) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'raw_grade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint(true, 2008081506);
    }

    if ($oldversion < 2008081600) {

    /// all 1.9 sites and fresh installs must already be unicode, not needed anymore
        unset_config('unicodedb');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008081600);
    }

    if ($oldversion < 2008082602) {

    /// Define table repository to be dropped
        $table = new xmldb_table('repository');

    /// Conditionally launch drop table for repository
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table repository to be created
        $table = new xmldb_table('repository');

    /// Adding fields to table repository
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '1');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table repository
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Define table repository_instances to be created
        $table = new xmldb_table('repository_instances');

    /// Adding fields to table repository_instances
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('username', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('readonly', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table repository_instances
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository_instances
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Define table repository_instance_config to be created
        $table = new xmldb_table('repository_instance_config');

    /// Adding fields to table repository_instance_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

    /// Adding keys to table repository_instance_config
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository_instance_config
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008082602);
    }

    if ($oldversion < 2008082700) {
    /// Add a new column to the question sessions table to record whether a
    /// question has been flagged.

    /// Define field flagged to be added to question_sessions
        $table = new xmldb_table('question_sessions');
        $field = new xmldb_field('flagged', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'manualcomment');

    /// Conditionally launch add field flagged
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008082700);
    }

    if ($oldversion < 2008082900) {

    /// Changing precision of field parent_type on table mnet_rpc to (20)
        $table = new xmldb_table('mnet_rpc');
        $field = new xmldb_field('parent_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpc_path');

    /// Launch change of precision for field parent_type
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008082900);
    }

    // MDL-16411 Move all plugintype_pluginname_version values from config to config_plugins.
    if ($oldversion < 2008091000) {
        foreach (get_object_vars($CFG) as $name => $value) {
            if (substr($name, strlen($name) - 8) !== '_version') {
                continue;
            }
            $pluginname = substr($name, 0, strlen($name) - 8);
            if (!strpos($pluginname, '_')) {
                // Skip things like backup_version that don't contain an extra _
                continue;
            }
            if ($pluginname == 'enrol_ldap_version') {
                // Special case - this is something different from a plugin version number.
                continue;
            }
            if (!preg_match('/^\d{10}$/', $value)) {
                // Extra safety check, skip anything that does not look like a Moodle
                // version number (10 digits).
                continue;
            }
            set_config('version', $value, $pluginname);
            unset_config($name);
        }
        upgrade_main_savepoint(true, 2008091000);
    }

    if ($oldversion < 2008092300) {
        unset_config('editorspelling');
        unset_config('editordictionary');
    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008092300);
    }

    if ($oldversion < 2008101300) {

        if (!get_config(NULL, 'statsruntimedays')) {
            set_config('statsruntimedays', '31');
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008101300);
    }

    /// Drop the deprecated teacher, teachers, student and students columns from the course table.
    if ($oldversion < 2008111200) {
        $table = new xmldb_table('course');

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('teacher');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('teachers');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('student');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('students');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008111200);
    }

/// Add a unique index to the role.name column.
    if ($oldversion < 2008111800) {

    /// Define index name (unique) to be added to role
        $table = new xmldb_table('role');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, array('name'));

    /// Conditionally launch add index name
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008111800);
    }

/// Add a unique index to the role.shortname column.
    if ($oldversion < 2008111801) {

    /// Define index shortname (unique) to be added to role
        $table = new xmldb_table('role');
        $index = new xmldb_index('shortname', XMLDB_INDEX_UNIQUE, array('shortname'));

    /// Conditionally launch add index shortname
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008111801);
    }

    if ($oldversion < 2008120700) {

    /// Changing precision of field shortname on table course_request to (100)
        $table = new xmldb_table('course_request');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'fullname');

    /// Before changing the field, drop dependent indexes
    /// Define index shortname (not unique) to be dropped form course_request
        $index = new xmldb_index('shortname', XMLDB_INDEX_NOTUNIQUE, array('shortname'));
    /// Conditionally launch drop index shortname
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

    /// Launch change of precision for field shortname
        $dbman->change_field_precision($table, $field);

    /// After changing the field, recreate dependent indexes
    /// Define index shortname (not unique) to be added to course_request
        $index = new xmldb_index('shortname', XMLDB_INDEX_NOTUNIQUE, array('shortname'));
    /// Conditionally launch add index shortname
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008120700);
    }

    if ($oldversion < 2008120801) {

    /// Changing precision of field shortname on table mnet_enrol_course to (100)
        $table = new xmldb_table('mnet_enrol_course');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'fullname');

    /// Launch change of precision for field shortname
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008120801);
    }

    if ($oldversion < 2008121701) {

    /// Define field availablefrom to be added to course_modules
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('availablefrom', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completionexpected');

    /// Conditionally launch add field availablefrom
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field availableuntil to be added to course_modules
        $field = new xmldb_field('availableuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'availablefrom');

    /// Conditionally launch add field availableuntil
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field showavailability to be added to course_modules
        $field = new xmldb_field('showavailability', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'availableuntil');

    /// Conditionally launch add field showavailability
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define table course_modules_availability to be created
        $table = new xmldb_table('course_modules_availability');

    /// Adding fields to table course_modules_availability
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sourcecmid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('requiredcompletion', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradeitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->add_field('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);

    /// Adding keys to table course_modules_availability
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('coursemoduleid', XMLDB_KEY_FOREIGN, array('coursemoduleid'), 'course_modules', array('id'));
        $table->add_key('sourcecmid', XMLDB_KEY_FOREIGN, array('sourcecmid'), 'course_modules', array('id'));
        $table->add_key('gradeitemid', XMLDB_KEY_FOREIGN, array('gradeitemid'), 'grade_items', array('id'));

    /// Conditionally launch create table for course_modules_availability
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Changes to modinfo mean we need to rebuild course cache
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2008121701);
    }

    if ($oldversion < 2009010500) {
    /// clean up config table a bit
        unset_config('session_error_counter');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009010500);
    }

    if ($oldversion < 2009010600) {

    /// Define field originalquestion to be dropped from question_states
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('originalquestion');

    /// Conditionally launch drop field originalquestion
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009010600);
    }

    if ($oldversion < 2009010601) {

    /// Changing precision of field ip on table log to (45)
        $table = new xmldb_table('log');
        $field = new xmldb_field('ip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'userid');

    /// Launch change of precision for field ip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009010601);
    }

    if ($oldversion < 2009010602) {

    /// Changing precision of field lastip on table user to (45)
        $table = new xmldb_table('user');
        $field = new xmldb_field('lastip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'currentlogin');

    /// Launch change of precision for field lastip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009010602);
    }

    if ($oldversion < 2009010603) {

    /// Changing precision of field ip_address on table mnet_host to (45)
        $table = new xmldb_table('mnet_host');
        $field = new xmldb_field('ip_address', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'wwwroot');

    /// Launch change of precision for field ip_address
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009010603);
    }

    if ($oldversion < 2009010604) {

    /// Changing precision of field ip on table mnet_log to (45)
        $table = new xmldb_table('mnet_log');
        $field = new xmldb_field('ip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'userid');

    /// Launch change of precision for field ip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009010604);
    }

    if ($oldversion < 2009011000) {

    /// Changing nullability of field configdata on table block_instance to null
        $table = new xmldb_table('block_instance');
        $field = new xmldb_field('configdata');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'visible');

    /// Launch change of nullability for field configdata
        $dbman->change_field_notnull($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009011000);
    }

    if ($oldversion < 2009011100) {
    /// Remove unused settings
        unset_config('zip');
        unset_config('unzip');
        unset_config('adminblocks_initialised');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009011100);
    }

    if ($oldversion < 2009011101) {
    /// Migrate backup settings to core plugin config table
        $configs = $DB->get_records('backup_config');
        foreach ($configs as $config) {
            set_config($config->name, $config->value, 'backup');
        }

    /// Define table to be dropped
        $table = new xmldb_table('backup_config');

    /// Launch drop table for old backup config
        $dbman->drop_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009011101);
    }

    if ($oldversion < 2009011303) {

    /// Define table config_log to be created
        $table = new xmldb_table('config_log');

    /// Adding fields to table config_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('oldvalue', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

    /// Adding keys to table config_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table config_log
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

    /// Launch create table for config_log
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009011303);
    }

    if ($oldversion < 2009011900) {

    /// Define table sessions2 to be dropped
        $table = new xmldb_table('sessions2');

    /// Conditionally launch drop table for sessions
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table sessions to be dropped
        $table = new xmldb_table('sessions');

    /// Conditionally launch drop table for sessions
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table sessions to be created
        $table = new xmldb_table('sessions');

    /// Adding fields to table sessions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('sid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sessdata', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('firstip', XMLDB_TYPE_CHAR, '45', null, null, null, null);
        $table->add_field('lastip', XMLDB_TYPE_CHAR, '45', null, null, null, null);

    /// Adding keys to table sessions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table sessions
        $table->add_index('state', XMLDB_INDEX_NOTUNIQUE, array('state'));
        $table->add_index('sid', XMLDB_INDEX_UNIQUE, array('sid'));
        $table->add_index('timecreated', XMLDB_INDEX_NOTUNIQUE, array('timecreated'));
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

    /// Launch create table for sessions
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009011900);
    }

    if ($oldversion < 2009021800) {
        // Converting format of grade conditions, if any exist, to percentages.
        $DB->execute("
UPDATE {course_modules_availability} SET grademin=(
    SELECT 100.0*({course_modules_availability}.grademin-gi.grademin)
        /(gi.grademax-gi.grademin)
    FROM {grade_items} gi
    WHERE gi.id={course_modules_availability}.gradeitemid)
WHERE gradeitemid IS NOT NULL AND grademin IS NOT NULL");
        $DB->execute("
UPDATE {course_modules_availability} SET grademax=(
    SELECT 100.0*({course_modules_availability}.grademax-gi.grademin)
        /(gi.grademax-gi.grademin)
    FROM {grade_items} gi
    WHERE gi.id={course_modules_availability}.gradeitemid)
WHERE gradeitemid IS NOT NULL AND grademax IS NOT NULL");

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009021800);
    }

    if ($oldversion < 2009021801) {
    /// Define field backuptype to be added to backup_log
        $table = new xmldb_table('backup_log');
        $field = new xmldb_field('backuptype', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'info');
    /// Conditionally Launch add field backuptype and set all old records as 'scheduledbackup' records.
        if (!$dbman->field_exists($table, $field)) {
            // Set the default we want applied to any existing records
            $field->setDefault('scheduledbackup');
            // Add the field to the database
            $dbman->add_field($table, $field);
            // Remove the default
            $field->setDefault(null);
            // Update the database to remove the default
            $dbman->change_field_default($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009021801);
    }

    /// Add default sort order for question types.
    if ($oldversion < 2009030300) {
        set_config('multichoice_sortorder', 1, 'question');
        set_config('truefalse_sortorder', 2, 'question');
        set_config('shortanswer_sortorder', 3, 'question');
        set_config('numerical_sortorder', 4, 'question');
        set_config('calculated_sortorder', 5, 'question');
        set_config('essay_sortorder', 6, 'question');
        set_config('match_sortorder', 7, 'question');
        set_config('randomsamatch_sortorder', 8, 'question');
        set_config('multianswer_sortorder', 9, 'question');
        set_config('description_sortorder', 10, 'question');
        set_config('random_sortorder', 11, 'question');
        set_config('missingtype_sortorder', 12, 'question');

        upgrade_main_savepoint(true, 2009030300);
    }

    /// MDL-18132 replace the use a new Role allow switch settings page, instead of
    /// $CFG->allowuserswitchrolestheycantassign
    if ($oldversion < 2009032000) {
    /// First create the new table.
            $table = new xmldb_table('role_allow_switch');

    /// Adding fields to table role_allow_switch
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('allowswitch', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table role_allow_switch
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));
        $table->add_key('allowswitch', XMLDB_KEY_FOREIGN, array('allowswitch'), 'role', array('id'));

    /// Adding indexes to table role_allow_switch
        $table->add_index('roleid-allowoverride', XMLDB_INDEX_UNIQUE, array('roleid', 'allowswitch'));

    /// Conditionally launch create table for role_allow_switch
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009032000);
    }

    if ($oldversion < 2009032001) {
    /// Copy from role_allow_assign into the new table.
        $DB->execute('INSERT INTO {role_allow_switch} (roleid, allowswitch)
                SELECT roleid, allowassign FROM {role_allow_assign}');

    /// Unset the config variable used in 1.9.
        unset_config('allowuserswitchrolestheycantassign');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009032001);
    }

    if ($oldversion < 2009040300) {

    /// Define table filter_active to be created
        $table = new xmldb_table('filter_active');

    /// Adding fields to table filter_active
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('filter', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table filter_active
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Adding indexes to table filter_active
        $table->add_index('contextid-filter', XMLDB_INDEX_UNIQUE, array('contextid', 'filter'));

    /// Conditionally launch create table for filter_active
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009040300);
    }

    if ($oldversion < 2009040301) {

    /// Define table filter_config to be created
        $table = new xmldb_table('filter_config');

    /// Adding fields to table filter_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('filter', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

    /// Adding keys to table filter_config
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Adding indexes to table filter_config
        $table->add_index('contextid-filter-name', XMLDB_INDEX_UNIQUE, array('contextid', 'filter', 'name'));

    /// Conditionally launch create table for filter_config
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009040301);
    }

    if ($oldversion < 2009040302) {
    /// Transfer current settings from $CFG->textfilters
        $disabledfilters = filter_get_all_installed();
        if (empty($CFG->textfilters)) {
            $activefilters = array();
        } else {
            $activefilters = explode(',', $CFG->textfilters);
        }
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $sortorder = 1;
        foreach ($activefilters as $filter) {
            filter_set_global_state($filter, TEXTFILTER_ON, $sortorder);
            $sortorder += 1;
            unset($disabledfilters[$filter]);
        }
        foreach ($disabledfilters as $filter => $notused) {
            filter_set_global_state($filter, TEXTFILTER_DISABLED, $sortorder);
            $sortorder += 1;
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009040302);
    }

    if ($oldversion < 2009040600) {
    /// Ensure that $CFG->stringfilters is set.
        if (empty($CFG->stringfilters)) {
            if (!empty($CFG->filterall)) {
                set_config('stringfilters', $CFG->textfilters);
            } else {
                set_config('stringfilters', '');
            }
        }

        set_config('filterall', !empty($CFG->stringfilters));
        unset_config('textfilters');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009040600);
    }

    if ($oldversion < 2009041700) {
    /// To ensure the UI remains consistent with no behaviour change, any
    /// 'until' date in an activity condition should have 1 second subtracted
    /// (to go from 0:00 on the following day to 23:59 on the previous one).
        $DB->execute('UPDATE {course_modules} SET availableuntil = availableuntil - 1 WHERE availableuntil <> 0');
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009041700);
    }

    if ($oldversion < 2009042600) {
    /// Deleting orphaned messages from deleted users.
        require_once($CFG->dirroot.'/message/lib.php');
    /// Detect deleted users with messages sent(useridfrom) and not read
        if ($deletedusers = $DB->get_records_sql('SELECT DISTINCT u.id
                                                    FROM {user} u
                                                    JOIN {message} m ON m.useridfrom = u.id
                                                   WHERE u.deleted = ?', array(1))) {
            foreach ($deletedusers as $deleteduser) {
                message_move_userfrom_unread2read($deleteduser->id); // move messages
            }
        }
    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009042600);
    }

    /// Dropping all enums/check contraints from core. MDL-18577
    if ($oldversion < 2009042700) {

    /// Changing list of values (enum) of field stattype on table stats_daily to none
        $table = new xmldb_table('stats_daily');
        $field = new xmldb_field('stattype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'activity', 'roleid');

    /// Launch change of list of values for field stattype
        $dbman->drop_enum_from_field($table, $field);

    /// Changing list of values (enum) of field stattype on table stats_weekly to none
        $table = new xmldb_table('stats_weekly');
        $field = new xmldb_field('stattype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'activity', 'roleid');

    /// Launch change of list of values for field stattype
        $dbman->drop_enum_from_field($table, $field);

    /// Changing list of values (enum) of field stattype on table stats_monthly to none
        $table = new xmldb_table('stats_monthly');
        $field = new xmldb_field('stattype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'activity', 'roleid');

    /// Launch change of list of values for field stattype
        $dbman->drop_enum_from_field($table, $field);

    /// Changing list of values (enum) of field publishstate on table post to none
        $table = new xmldb_table('post');
        $field = new xmldb_field('publishstate', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'draft', 'attachment');

    /// Launch change of list of values for field publishstate
        $dbman->drop_enum_from_field($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009042700);
    }

    if ($oldversion < 2009043000) {
        unset_config('grade_report_showgroups');
        upgrade_main_savepoint(true, 2009043000);
    }

    if ($oldversion < 2009050600) {
    /// Site front page blocks need to be moved due to page name change.
        $DB->set_field('block_instance', 'pagetype', 'site-index', array('pagetype' => 'course-view', 'pageid' => SITEID));

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050600);
    }

    if ($oldversion < 2009050601) {

    /// Define table block_instance to be renamed to block_instances
        $table = new xmldb_table('block_instance');

    /// Launch rename table for block_instance
        $dbman->rename_table($table, 'block_instances');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050601);
    }

    if ($oldversion < 2009050602) {

    /// Define table block_instance to be renamed to block_instance_old
        $table = new xmldb_table('block_pinned');

    /// Launch rename table for block_instance
        $dbman->rename_table($table, 'block_pinned_old');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050602);
    }

    if ($oldversion < 2009050603) {

    /// Define table block_instance_old to be created
        $table = new xmldb_table('block_instance_old');

    /// Adding fields to table block_instance_old
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('blockid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('pageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('pagetype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('position', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('weight', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('configdata', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

    /// Adding keys to table block_instance_old
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('blockid', XMLDB_KEY_FOREIGN, array('blockid'), 'block', array('id'));

    /// Adding indexes to table block_instance_old
        $table->add_index('pageid', XMLDB_INDEX_NOTUNIQUE, array('pageid'));
        $table->add_index('pagetype', XMLDB_INDEX_NOTUNIQUE, array('pagetype'));

    /// Conditionally launch create table for block_instance_old
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050603);
    }

    if ($oldversion < 2009050604) {
    /// Copy current blocks data from block_instances to block_instance_old
        $DB->execute('INSERT INTO {block_instance_old} (oldid, blockid, pageid, pagetype, position, weight, visible, configdata)
            SELECT id, blockid, pageid, pagetype, position, weight, visible, configdata FROM {block_instances} ORDER BY id');

        upgrade_main_savepoint(true, 2009050604);
    }

    if ($oldversion < 2009050605) {

    /// Define field multiple to be dropped from block
        $table = new xmldb_table('block');
        $field = new xmldb_field('multiple');

    /// Conditionally launch drop field multiple
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050605);
    }

    if ($oldversion < 2009050606) {
        $table = new xmldb_table('block_instances');

    /// Rename field weight on table block_instances to defaultweight
        $field = new xmldb_field('weight', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, 'position');
        $dbman->rename_field($table, $field, 'defaultweight');

    /// Rename field position on table block_instances to defaultregion
        $field = new xmldb_field('position', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, 'pagetype');
        $dbman->rename_field($table, $field, 'defaultregion');

        /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050606);
    }

    if ($oldversion < 2009050607) {
    /// Changing precision of field defaultregion on table block_instances to (16)
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('defaultregion', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null, 'pagetype');

    /// Launch change of precision for field defaultregion
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050607);
    }

    if ($oldversion < 2009050608) {
    /// Change regions to the new notation
        $DB->set_field('block_instances', 'defaultregion', 'side-pre', array('defaultregion' => 'l'));
        $DB->set_field('block_instances', 'defaultregion', 'side-post', array('defaultregion' => 'r'));
        $DB->set_field('block_instances', 'defaultregion', 'course-view-top', array('defaultregion' => 'c'));
        // This third one is a custom value from contrib/patches/center_blocks_position_patch and the
        // flex page course format. Hopefully this new value is an adequate alternative.

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050608);
    }

    if ($oldversion < 2009050609) {

    /// Define key blockname (unique) to be added to block
        $table = new xmldb_table('block');
        $key = new xmldb_key('blockname', XMLDB_KEY_UNIQUE, array('name'));

    /// Launch add key blockname
        $dbman->add_key($table, $key);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050609);
    }

    if ($oldversion < 2009050610) {
        $table = new xmldb_table('block_instances');

    /// Define field blockname to be added to block_instances
        $field = new xmldb_field('blockname', XMLDB_TYPE_CHAR, '40', null, null, null, null, 'blockid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field contextid to be added to block_instances
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'blockname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field showinsubcontexts to be added to block_instances
        $field = new xmldb_field('showinsubcontexts', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'contextid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field subpagepattern to be added to block_instances
        $field = new xmldb_field('subpagepattern', XMLDB_TYPE_CHAR, '16', null, null, null, null, 'pagetype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050610);
    }

    if ($oldversion < 2009050611) {
        $table = new xmldb_table('block_instances');

    /// Fill in blockname from blockid
        $DB->execute("UPDATE {block_instances} SET blockname = (SELECT name FROM {block} WHERE id = blockid)");

    /// Set showinsubcontexts = 0 for all rows.
        $DB->execute("UPDATE {block_instances} SET showinsubcontexts = 0");

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050611);
    }

    if ($oldversion < 2009050612) {

    /// Rename field pagetype on table block_instances to pagetypepattern
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('pagetype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'pageid');

    /// Launch rename field pagetype
        $dbman->rename_field($table, $field, 'pagetypepattern');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050612);
    }

    if ($oldversion < 2009050613) {
    /// fill in contextid and subpage, and update pagetypepattern from pagetype and pageid

    /// site-index
        $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
        $DB->execute("UPDATE {block_instances} SET contextid = " . $frontpagecontext->id . ",
                                                   pagetypepattern = 'site-index',
                                                   subpagepattern = NULL
                      WHERE pagetypepattern = 'site-index'");

    /// course-view
        $DB->execute("UPDATE {block_instances} SET
                        contextid = (
                            SELECT {context}.id
                            FROM {context}
                            JOIN {course} ON instanceid = {course}.id AND contextlevel = " . CONTEXT_COURSE . "
                            WHERE {course}.id = pageid
                        ),
                       pagetypepattern = 'course-view-*',
                       subpagepattern = NULL
                      WHERE pagetypepattern = 'course-view'");

    /// admin
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $DB->execute("UPDATE {block_instances} SET
                        contextid = " . $syscontext->id . ",
                        pagetypepattern = 'admin-*',
                        subpagepattern = NULL
                      WHERE pagetypepattern = 'admin'");

    /// my-index
        $DB->execute("UPDATE {block_instances} SET
                        contextid = (
                            SELECT {context}.id
                            FROM {context}
                            JOIN {user} ON instanceid = {user}.id AND contextlevel = " . CONTEXT_USER . "
                            WHERE {user}.id = pageid
                        ),
                        pagetypepattern = 'my-index',
                        subpagepattern = NULL
                      WHERE pagetypepattern = 'my-index'");

    /// tag-index
        $DB->execute("UPDATE {block_instances} SET
                        contextid = " . $syscontext->id . ",
                        pagetypepattern = 'tag-index',
                        subpagepattern = pageid
                      WHERE pagetypepattern = 'tag-index'");

    /// blog-view
        $DB->execute("UPDATE {block_instances} SET
                        contextid = (
                            SELECT {context}.id
                            FROM {context}
                            JOIN {user} ON instanceid = {user}.id AND contextlevel = " . CONTEXT_USER . "
                            WHERE {user}.id = pageid
                        ),
                        pagetypepattern = 'blog-index',
                        subpagepattern = NULL
                      WHERE pagetypepattern = 'blog-view'");

    /// mod-xxx-view
        $moduleswithblocks = array('chat', 'data', 'lesson', 'quiz', 'dimdim', 'game', 'wiki', 'oublog');
        foreach ($moduleswithblocks as $modname) {
            if (!$dbman->table_exists($modname)) {
                continue;
            }
            $DB->execute("UPDATE {block_instances} SET
                            contextid = (
                                SELECT {context}.id
                                FROM {context}
                                JOIN {course_modules} ON instanceid = {course_modules}.id AND contextlevel = " . CONTEXT_MODULE . "
                                JOIN {modules} ON {modules}.id = {course_modules}.module AND {modules}.name = '$modname'
                                JOIN {{$modname}} ON {course_modules}.instance = {{$modname}}.id
                                WHERE {{$modname}}.id = pageid
                            ),
                            pagetypepattern = 'blog-index',
                            subpagepattern = NULL
                          WHERE pagetypepattern = 'blog-view'");
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050613);
    }

    if ($oldversion < 2009050614) {
    /// fill in any missing contextids with a dummy value, so we can add the not-null constraint.
        $DB->execute("UPDATE {block_instances} SET contextid = 0 WHERE contextid IS NULL");

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050614);
    }

    if ($oldversion < 2009050615) {
        $table = new xmldb_table('block_instances');

    /// Arrived here, any block_instances record without blockname is one
    /// orphan block coming from 1.9. Just delete them. MDL-22503
        $DB->delete_records_select('block_instances', 'blockname IS NULL');

    /// Changing nullability of field blockname on table block_instances to not null
        $field = new xmldb_field('blockname', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_notnull($table, $field);

    /// Changing nullability of field contextid on table block_instances to not null
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'blockname');
        $dbman->change_field_notnull($table, $field);

    /// Changing nullability of field showinsubcontexts on table block_instances to not null
        $field = new xmldb_field('showinsubcontexts', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, 'contextid');
        $dbman->change_field_notnull($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050615);
    }

    if ($oldversion < 2009050616) {
    /// Add exiting sticky blocks.
        $blocks = $DB->get_records('block');
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $newregions = array(
            'l' => 'side-pre',
            'r' => 'side-post',
            'c' => 'course-view-top',
        );
        $stickyblocks = $DB->get_recordset('block_pinned_old');
        foreach ($stickyblocks as $stickyblock) {
            // Only if the block exists (avoid orphaned sticky blocks)
            if (!isset($blocks[$stickyblock->blockid]) || empty($blocks[$stickyblock->blockid]->name)) {
                continue;
            }
            $newblock = new stdClass();
            $newblock->blockname = $blocks[$stickyblock->blockid]->name;
            $newblock->contextid = $syscontext->id;
            $newblock->showinsubcontexts = 1;
            switch ($stickyblock->pagetype) {
                case 'course-view':
                    $newblock->pagetypepattern = 'course-view-*';
                    break;
                default:
                    $newblock->pagetypepattern = $stickyblock->pagetype;
            }
            $newblock->defaultregion = $newregions[$stickyblock->position];
            $newblock->defaultweight = $stickyblock->weight;
            $newblock->configdata = $stickyblock->configdata;
            $newblock->visible = 1;
            $DB->insert_record('block_instances', $newblock);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050616);
    }

    if ($oldversion < 2009050617) {

    /// Define table block_positions to be created
        $table = new xmldb_table('block_positions');

    /// Adding fields to table block_positions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('blockinstanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('pagetype', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subpage', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('region', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null);
        $table->add_field('weight', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table block_positions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('blockinstanceid', XMLDB_KEY_FOREIGN, array('blockinstanceid'), 'block_instances', array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Adding indexes to table block_positions
        $table->add_index('blockinstanceid-contextid-pagetype-subpage', XMLDB_INDEX_UNIQUE, array('blockinstanceid', 'contextid', 'pagetype', 'subpage'));

    /// Conditionally launch create table for block_positions
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050617);
    }

    if ($oldversion < 2009050618) {
    /// And block instances with visible = 0, copy that information to block_positions
        $DB->execute("INSERT INTO {block_positions} (blockinstanceid, contextid, pagetype, subpage, visible, region, weight)
                SELECT bi.id, bi.contextid,
                       CASE WHEN bi.pagetypepattern = 'course-view-*'
                           THEN (SELECT " . $DB->sql_concat("'course-view-'", 'c.format') . "
                                   FROM {course} c
                                   JOIN {context} ctx ON c.id = ctx.instanceid
                                  WHERE ctx.id = bi.contextid)
                           ELSE bi.pagetypepattern END,
                       CASE WHEN bi.subpagepattern IS NULL
                           THEN '" . $DB->sql_empty() . "'
                           ELSE bi.subpagepattern END,
                       0, bi.defaultregion, bi.defaultweight
                  FROM {block_instances} bi
                 WHERE bi.visible = 0 AND bi.pagetypepattern <> 'admin-*' AND bi.pagetypepattern IS NOT NULL");
        // note: MDL-25031 all block instances should have a pagetype pattern, NULL is not allowed,
        //       if we manage to find out how NULLs get there we should fix them before this step

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050618);
    }

    if ($oldversion < 2009050619) {
        $table = new xmldb_table('block_instances');

    /// Define field blockid to be dropped from block_instances
        $field = new xmldb_field('blockid');
        if ($dbman->field_exists($table, $field)) {
        /// Before dropping the field, drop dependent indexes
            $index = new xmldb_index('blockid', XMLDB_INDEX_NOTUNIQUE, array('blockid'));
            if ($dbman->index_exists($table, $index)) {
            /// Launch drop index blockid
                $dbman->drop_index($table, $index);
            }
            $dbman->drop_field($table, $field);
        }

    /// Define field pageid to be dropped from block_instances
        $field = new xmldb_field('pageid');
        if ($dbman->field_exists($table, $field)) {
        /// Before dropping the field, drop dependent indexes
            $index = new xmldb_index('pageid', XMLDB_INDEX_NOTUNIQUE, array('pageid'));
            if ($dbman->index_exists($table, $index)) {
            /// Launch drop index pageid
                $dbman->drop_index($table, $index);
            }
            $dbman->drop_field($table, $field);
        }

    /// Define field visible to be dropped from block_instances
        $field = new xmldb_field('visible');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009050619);
    }

    if ($oldversion < 2009051200) {
    /// Let's check the status of mandatory mnet_host records, fixing them
    /// and moving "orphan" users to default localhost record. MDL-16879
        echo $OUTPUT->notification('Fixing mnet records, this may take a while...', 'notifysuccess');
        upgrade_fix_incorrect_mnethostids();

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009051200);
    }


    if ($oldversion < 2009051700) {
    /// migrate editor settings
        if (empty($CFG->htmleditor)) {
            set_config('texteditors', 'textarea');
        } else {
            set_config('texteditors', 'tinymce,textarea');
        }

        unset_config('htmleditor');
        unset_config('defaulthtmleditor');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009051700);
    }

    /// Repeat 2009050607 upgrade step, which Petr commented out because of XMLDB
    /// stupidity, so lots of people will have missed.
    if ($oldversion < 2009061600) {
    /// Changing precision of field defaultregion on table block_instances to (16)
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('defaultregion', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null, 'configdata');

    /// Launch change of precision for field defaultregion
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009061600);
    }

    if ($oldversion < 2009061702) {
        // standardizing plugin names
        if ($configs = $DB->get_records_select('config_plugins', "plugin LIKE 'quizreport_%'")) {
            foreach ($configs as $config) {
                unset_config($config->name, $config->plugin); /// unset old config
                $config->plugin = str_replace('quizreport_', 'quiz_', $config->plugin);
                set_config($config->name, $config->value, $config->plugin); /// set new config
            }
        }
        unset($configs);
        upgrade_main_savepoint(true, 2009061702);
    }

    if ($oldversion < 2009061703) {
        // standardizing plugin names
        if ($configs = $DB->get_records_select('config_plugins', "plugin LIKE 'assignment_type_%'")) {
            foreach ($configs as $config) {
                unset_config($config->name, $config->plugin); /// unset old config
                $config->plugin = str_replace('assignment_type_', 'assignment_', $config->plugin);
                set_config($config->name, $config->value, $config->plugin); /// set new config
            }
        }
        unset($configs);
        upgrade_main_savepoint(true, 2009061703);
    }

    if ($oldversion < 2009061704) {
        // change component string in capability records to new "_" format
        if ($caps = $DB->get_records('capabilities')) {
            foreach ($caps as $cap) {
                $cap->component = str_replace('/', '_', $cap->component);
                $DB->update_record('capabilities', $cap);
            }
        }
        unset($caps);
        upgrade_main_savepoint(true, 2009061704);
    }

    if ($oldversion < 2009063000) {
        // upgrade format of _with_advanced settings - quiz only
        // note: this can be removed later, not needed for upgrades from 1.9.x
        if ($quiz = get_config('quiz')) {
            foreach ($quiz as $name=>$value) {
                if (strpos($name, 'fix_') !== 0) {
                    continue;
                }
                $newname = substr($name,4).'_adv';
                set_config($newname, $value, 'quiz');
                unset_config($name, 'quiz');
            }
        }
        upgrade_main_savepoint(true, 2009063000);
    }

    if ($oldversion < 2009071000) {

    /// Rename field contextid on table block_instances to parentcontextid
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'blockname');

    /// Launch rename field parentcontextid
        $dbman->rename_field($table, $field, 'parentcontextid');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009071000);
    }

    if ($oldversion < 2009071600) {

    /// Define field summaryformat to be added to post
        $table = new xmldb_table('post');
        $field = new xmldb_field('summaryformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'format');

    /// Conditionally launch add field summaryformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009071600);
    }

    if ($oldversion < 2009072400) {

    /// Define table comments to be created
        $table = new xmldb_table('comments');

    /// Adding fields to table comments
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('commentarea', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('format', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table comments
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for comments
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009072400);
    }

    /**
     * This upgrade is to set up the new navigation blocks that have been developed
     * as part of Moodle 2.0
     * Now I [Sam Hemelryk] hit a conundrum while exploring how to go about this
     * as not only do we want to install the new blocks but we also want to set up
     * default instances of them, and at the same time remove instances of the blocks
     * that were/will-be outmoded by the two new navigation blocks.
     * After talking it through with Tim Hunt {@link http://moodle.org/mod/cvsadmin/view.php?conversationid=3112}
     * we decided that the best way to go about this was to put the bulk of the
     * upgrade operation into core upgrade `here` but to let the plugins block
     * still install the blocks.
     * This leaves one hairy end in that we will create block_instances within the
     * DB before the blocks themselves are created within the DB
     */
    if ($oldversion < 2009082800) {

        echo $OUTPUT->notification(get_string('navigationupgrade', 'admin'));

        // Get the system context so we can set the block instances to it
        $syscontext = get_context_instance(CONTEXT_SYSTEM);

        // An array to contain the new block instances we will create
        $newblockinstances = array('globalnavigation'=>new stdClass,'settingsnavigation'=>new stdClass);
        // The new global navigation block instance as a stdClass
        $newblockinstances['globalnavigation']->blockname = 'global_navigation_tree';
        $newblockinstances['globalnavigation']->parentcontextid = $syscontext->id; // System context
        $newblockinstances['globalnavigation']->showinsubcontexts = true; // Show absolutely everywhere
        $newblockinstances['globalnavigation']->pagetypepattern = '*'; // Thats right everywhere
        $newblockinstances['globalnavigation']->subpagetypepattern = null;
        $newblockinstances['globalnavigation']->defaultregion = BLOCK_POS_LEFT;
        $newblockinstances['globalnavigation']->defaultweight = -10; // Try make this first
        $newblockinstances['globalnavigation']->configdata = '';
        // The new settings navigation block instance as a stdClass
        $newblockinstances['settingsnavigation']->blockname = 'settings_navigation_tree';
        $newblockinstances['settingsnavigation']->parentcontextid = $syscontext->id;
        $newblockinstances['settingsnavigation']->showinsubcontexts = true;
        $newblockinstances['settingsnavigation']->pagetypepattern = '*';
        $newblockinstances['settingsnavigation']->subpagetypepattern = null;
        $newblockinstances['settingsnavigation']->defaultregion = BLOCK_POS_LEFT;
        $newblockinstances['settingsnavigation']->defaultweight = -9; // Try make this second
        $newblockinstances['settingsnavigation']->configdata = '';

        // Blocks that are outmoded and for whom the bells will toll... by which I
        // mean we will delete all instances of
        $outmodedblocks = array('participants','admin_tree','activity_modules','admin','course_list');
        $outmodedblocksstring = '\''.join('\',\'',$outmodedblocks).'\'';
        unset($outmodedblocks);
        // Retrieve the block instance id's and parent contexts, so we can join them an GREATLY
        // cut down the number of delete queries we will need to run
        $allblockinstances = $DB->get_recordset_select('block_instances', 'blockname IN ('.$outmodedblocksstring.')', array(), '', 'id, parentcontextid');

        $contextids = array();
        $instanceids = array();
        // Iterate through all block instances
        foreach ($allblockinstances as $blockinstance) {
            if (!in_array($blockinstance->parentcontextid, $contextids)) {
                $contextids[] = $blockinstance->parentcontextid;

                // If we have over 1000 contexts clean them up and reset the array
                // this ensures we don't hit any nasty memory limits or such
                if (count($contextids) > 1000) {
                    upgrade_cleanup_unwanted_block_contexts($contextids);
                    $contextids = array();
                }
            }
            if (!in_array($blockinstance->id, $instanceids)) {
                $instanceids[] = $blockinstance->id;
                // If we have more than 1000 block instances now remove all block positions
                // and empty the array
                if (count($instanceids) > 1000) {
                    $instanceidstring = join(',',$instanceids);
                    $DB->delete_records_select('block_positions', 'blockinstanceid IN ('.$instanceidstring.')');
                    $instanceids = array();
                }
            }
        }

        upgrade_cleanup_unwanted_block_contexts($contextids);

        if ($instanceids) {
            $instanceidstring = join(',',$instanceids);
            $DB->delete_records_select('block_positions', 'blockinstanceid IN ('.$instanceidstring.')');
        }

        unset($allblockinstances);
        unset($contextids);
        unset($instanceids);
        unset($instanceidstring);

        // Now remove the actual block instance
        $DB->delete_records_select('block_instances', 'blockname IN ('.$outmodedblocksstring.')');
        unset($outmodedblocksstring);

        // Insert the new block instances. Remember they have not been installed yet
        // however this should not be a problem
        foreach ($newblockinstances as $blockinstance) {
            $blockinstance->id= $DB->insert_record('block_instances', $blockinstance);
            // Ensure the block context is created.
            get_context_instance(CONTEXT_BLOCK, $blockinstance->id);
        }
        unset($newblockinstances);

        upgrade_main_savepoint(true, 2009082800);
        // The end of the navigation upgrade
    }

    if ($oldversion < 2009100602) {
    /// Define table external_functions to be created
        $table = new xmldb_table('external_functions');

    /// Adding fields to table external_functions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('classname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('methodname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('classpath', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table external_functions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table external_functions
        $table->add_index('name', XMLDB_INDEX_UNIQUE, array('name'));

    /// Launch create table for external_functions
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009100602);
    }

    if ($oldversion < 2009100603) {
        /// Define table external_services to be created
        $table = new xmldb_table('external_services');

    /// Adding fields to table external_services
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('requiredcapability', XMLDB_TYPE_CHAR, '150', null, null, null, null);
        $table->add_field('restrictedusers', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table external_services
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table external_services
        $table->add_index('name', XMLDB_INDEX_UNIQUE, array('name'));

    /// Launch create table for external_services
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009100603);
    }

    if ($oldversion < 2009100604) {
    /// Define table external_services_functions to be created
        $table = new xmldb_table('external_services_functions');

    /// Adding fields to table external_services_functions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('externalserviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('functionname', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table external_services_functions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('externalserviceid', XMLDB_KEY_FOREIGN, array('externalserviceid'), 'external_services', array('id'));

    /// Launch create table for external_services_functions
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009100604);
    }

    if ($oldversion < 2009100605) {
    /// Define table external_services_users to be created
        $table = new xmldb_table('external_services_users');

    /// Adding fields to table external_services_users
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('externalserviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('iprestriction', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('validuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table external_services_users
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('externalserviceid', XMLDB_KEY_FOREIGN, array('externalserviceid'), 'external_services', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Launch create table for external_services_users
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009100605);
    }

    if ($oldversion < 2009102600) {

    /// Define table external_tokens to be created
        $table = new xmldb_table('external_tokens');

    /// Adding fields to table external_tokens
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tokentype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('externalserviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sid', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('creatorid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('iprestriction', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('validuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('lastaccess', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table external_tokens
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('externalserviceid', XMLDB_KEY_FOREIGN, array('externalserviceid'), 'external_services', array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $table->add_key('creatorid', XMLDB_KEY_FOREIGN, array('creatorid'), 'user', array('id'));

    /// Launch create table for external_tokens
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009102600);
    }

   if ($oldversion < 2009103000) {

    /// Define table blog_association to be created
        $table = new xmldb_table('blog_association');

    /// Adding fields to table blog_association
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('blogid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table blog_association
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $table->add_key('blogid', XMLDB_KEY_FOREIGN, array('blogid'), 'post', array('id'));

    /// Conditionally launch create table for blog_association
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

/// Define table blog_external to be created
        $table = new xmldb_table('blog_external');

    /// Adding fields to table blog_external
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filtertags', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('failedlastsync', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timefetched', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table blog_external
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Conditionally launch create table for blog_external
        if ($dbman->table_exists($table)) {
            // Delete the existing one first (comes from early dev version)
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);

        // now inform admins that some settings require attention after upgrade
        if (($CFG->bloglevel == BLOG_COURSE_LEVEL || $CFG->bloglevel == BLOG_GROUP_LEVEL) && empty($CFG->bloglevel_upgrade_complete)) {
            echo $OUTPUT->notification(get_string('bloglevelupgradenotice', 'admin'));

            $site = get_site();

            $a = new StdClass;
            $a->sitename = $site->fullname;
            $a->fixurl   = "$CFG->wwwroot/$CFG->admin/bloglevelupgrade.php";

            $subject = get_string('bloglevelupgrade', 'admin');
            $description = get_string('bloglevelupgradedescription', 'admin', $a);

            // can not use messaging here because it is not configured yet!
            upgrade_log(UPGRADE_LOG_NOTICE, null, $subject, $description);
        }
    /// Main savepoint reached
        upgrade_main_savepoint(true, 2009103000);
    }

    if ($oldversion < 2009110400) {
        // list of tables where we need to add new format field and convert texts
        $extendtables = array('course'              => 'summary',
                              'course_categories'   => 'description',
                              'course_categories'   => 'description',
                              'course_request'      => 'summary',
                              'grade_outcomes'      => 'description',
                              'groups'              => 'description',
                              'groupings'           => 'description',
                              'scale'               => 'description',
                              'user_info_field'     => 'description',
                              'user_info_field'     => 'defaultdata',
                              'user_info_data'      => 'data');

        foreach ($extendtables as $tablestr => $fieldstr) {
            $formatfieldstr = $fieldstr.'format';

            $table = new xmldb_table($tablestr);
            $field = new xmldb_field($formatfieldstr, XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', $fieldstr);
            // Check that the field doesn't already exists
            if (!$dbman->field_exists($table, $field)) {
                // Add the new field
                $dbman->add_field($table, $field);
            }
            if ($CFG->texteditors !== 'textarea') {
                $rs = $DB->get_recordset($tablestr, array($formatfieldstr => FORMAT_MOODLE), '', "id,$fieldstr,$formatfieldstr");
                foreach ($rs as $rec) {
                    $rec->$fieldstr       = text_to_html($rec->$fieldstr, false, false, true);
                    $rec->$formatfieldstr = FORMAT_HTML;
                    $DB->update_record($tablestr, $rec);
                    upgrade_set_timeout();
                }
                $rs->close();
                unset($rs);
            }
        }

        unset($rec);
        unset($extendtables);

        upgrade_main_savepoint(true, 2009110400);
    }

    if ($oldversion < 2009110401) {
        $table = new xmldb_table('user');

        // Change the precision of the description field first up.
        // This may grow!
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'url');
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('descriptionformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'description');
        // Check that the field doesn't already exists
        if (!$dbman->field_exists($table, $field)) {
            // Add the new field
            $dbman->add_field($table, $field);
        }
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('user', array('descriptionformat'=>FORMAT_MOODLE, 'deleted'=>0, 'htmleditor'=>1), '', "id,description,descriptionformat");
            foreach ($rs as $rec) {
                $rec->description       = text_to_html($rec->description, false, false, true);
                $rec->descriptionformat = FORMAT_HTML;
                $DB->update_record('user', $rec);
                upgrade_set_timeout();
            }
            $rs->close();
        }

        upgrade_main_savepoint(true, 2009110401);
    }

    if ($oldversion < 2009112400) {
        if (empty($CFG->passwordsaltmain)) {
            $subject = get_string('check_passwordsaltmain_name', 'report_security');
            $description = get_string('check_passwordsaltmain_warning', 'report_security');;
            upgrade_log(UPGRADE_LOG_NOTICE, null, $subject, $description);
        }
        upgrade_main_savepoint(true, 2009112400);
    }

    if ($oldversion < 2010011200) {
        $table = new xmldb_table('grade_categories');
        $field = new xmldb_field('hidden', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'timemodified');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2010011200);
    }

    if ($oldversion < 2010012500) {
        upgrade_fix_incorrect_mnethostids();
        upgrade_main_savepoint(true, 2010012500);
    }

    if ($oldversion < 2010012600) {
        // do stuff to the mnet table
        $table = new xmldb_table('mnet_rpc');

        $field = new xmldb_field('parent_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpc_path');
        $dbman->rename_field($table, $field, 'plugintype');

        $field = new xmldb_field('parent', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpc_path');
        $dbman->rename_field($table, $field, 'pluginname');

        $field = new xmldb_field('filename', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'profile');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('classname', XMLDB_TYPE_CHAR, '150', null, null, null, null, 'filename');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('static', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'classname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010012600);
    }

    if ($oldversion < 2010012900) {

    /// Define table mnet_remote_rpc to be created
        $table = new xmldb_table('mnet_remote_rpc');

    /// Adding fields to table mnet_remote_rpc
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('functionname', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('xmlrpcpath', XMLDB_TYPE_CHAR, '80', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table mnet_remote_rpc
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for mnet_remote_rpc
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Define table mnet_remote_service2rpc to be created
        $table = new xmldb_table('mnet_remote_service2rpc');

    /// Adding fields to table mnet_remote_service2rpc
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('serviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('rpcid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table mnet_remote_service2rpc
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table mnet_remote_service2rpc
        $table->add_index('rpcid_serviceid', XMLDB_INDEX_UNIQUE, array('rpcid', 'serviceid'));

    /// Conditionally launch create table for mnet_remote_service2rpc
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Rename field function_name on table mnet_rpc to functionname
        $table = new xmldb_table('mnet_rpc');
        $field = new xmldb_field('function_name', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'id');

    /// Launch rename field function_name
        $dbman->rename_field($table, $field, 'functionname');


    /// Rename field xmlrpc_path on table mnet_rpc to xmlrpcpath
        $table = new xmldb_table('mnet_rpc');
        $field = new xmldb_field('xmlrpc_path', XMLDB_TYPE_CHAR, '80', null, XMLDB_NOTNULL, null, null, 'function_name');

    /// Launch rename field xmlrpc_path
        $dbman->rename_field($table, $field, 'xmlrpcpath');


    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010012900);
    }

    if ($oldversion < 2010012901) {

        /// Define field plugintype to be added to mnet_remote_rpc
        $table = new xmldb_table('mnet_remote_rpc');
        $field = new xmldb_field('plugintype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpcpath');

        /// Conditionally launch add field plugintype
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field pluginname to be added to mnet_remote_rpc
        $field = new xmldb_field('pluginname', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'plugintype');

    /// Conditionally launch add field pluginname
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        /// Main savepoint reached
        upgrade_main_savepoint(true, 2010012901);
    }

    if ($oldversion < 2010012902) {

    /// Define field enabled to be added to mnet_remote_rpc
        $table = new xmldb_table('mnet_remote_rpc');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, 'pluginname');

    /// Conditionally launch add field enabled
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        /// Main savepoint reached
        upgrade_main_savepoint(true, 2010012902);
    }

    /// MDL-17863. Increase the portno column length on mnet_host to handle any port number
    if ($oldversion < 2010020100) {
    /// Changing precision of field portno on table mnet_host to (5)
        $table = new xmldb_table('mnet_host');
        $field = new xmldb_field('portno', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'transport');

    /// Launch change of precision for field portno
        $dbman->change_field_precision($table, $field);

        upgrade_main_savepoint(true, 2010020100);
    }

    if ($oldversion < 2010020300) {

    /// Define field timecreated to be added to user
        $table = new xmldb_table('user');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'trackforums');

        if (!$dbman->field_exists($table, $field)) {
        /// Launch add field timecreated
            $dbman->add_field($table, $field);

            $DB->execute("UPDATE {user} SET timecreated = firstaccess");

            $sql = "UPDATE {user} SET timecreated = " . time() ." where timecreated = 0";
            $DB->execute($sql);
        }
        upgrade_main_savepoint(true, 2010020300);
    }

    // MDL-21407. Trim leading spaces from default tex latexpreamble causing problems under some confs
    if ($oldversion < 2010020301) {
        if ($preamble = $CFG->filter_tex_latexpreamble) {
            $preamble = preg_replace('/^ +/m', '', $preamble);
            set_config('filter_tex_latexpreamble', $preamble);
        }
        upgrade_main_savepoint(true, 2010020301);
    }

    if ($oldversion < 2010021400) {
    /// Changes to modinfo mean we need to rebuild course cache
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);
        upgrade_main_savepoint(true, 2010021400);
    }

    if ($oldversion < 2010021800) {
        $DB->set_field('mnet_application', 'sso_jump_url', '/auth/mnet/jump.php', array('name' => 'moodle'));
        upgrade_main_savepoint(true, 2010021800);
    }

    if ($oldversion < 2010031900) {
        // regeneration of sessions is always enabled, no need for this setting any more
        unset_config('regenloginsession');
        upgrade_main_savepoint(true, 2010031900);
    }

    if ($oldversion < 2010033101.02) {

    /// Define table license to be created
        $table = new xmldb_table('license');

    /// Adding fields to table license
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('fullname', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('source', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('version', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table license
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for license
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        $active_licenses = array();

        $license = new stdClass();

        // add unknown license
        $license->shortname = 'unknown';
        $license->fullname = 'Unknown license';
        $license->source = '';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add all rights reserved license
        $license->shortname = 'allrightsreserved';
        $license->fullname = 'All rights reserved';
        $license->source = 'http://en.wikipedia.org/wiki/All_rights_reserved';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->id = $record->id;
                $license->enabled = $record->enabled;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add public domain license
        $license->shortname = 'public';
        $license->fullname = 'Public Domain';
        $license->source = 'http://creativecommons.org/licenses/publicdomain/';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add creative commons license
        $license->shortname = 'cc';
        $license->fullname = 'Creative Commons';
        $license->source = 'http://creativecommons.org/licenses/by/3.0/';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add creative commons no derivs license
        $license->shortname = 'cc-nd';
        $license->fullname = 'Creative Commons - NoDerivs';
        $license->source = 'http://creativecommons.org/licenses/by-nd/3.0/';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add creative commons no commercial no derivs license
        $license->shortname = 'cc-nc-nd';
        $license->fullname = 'Creative Commons - No Commercial NoDerivs';
        $license->source = 'http://creativecommons.org/licenses/by-nc-nd/3.0/';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add creative commons no commercial
        $license->shortname = 'cc-nc-nd';
        $license->shortname = 'cc-nc';
        $license->fullname = 'Creative Commons - No Commercial';
        $license->source = 'http://creativecommons.org/licenses/by-nd/3.0/';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add creative commons no commercial sharealike
        $license->shortname = 'cc-nc-sa';
        $license->fullname = 'Creative Commons - No Commercial ShareAlike';
        $license->source = 'http://creativecommons.org/licenses/by-nc-sa/3.0/';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        // add creative commons sharealike
        $license->shortname = 'cc-sa';
        $license->fullname = 'Creative Commons - ShareAlike';
        $license->source = 'http://creativecommons.org/licenses/by-sa/3.0/';
        $license->enabled = 1;
        $license->version = '2010033100';
        $active_licenses[] = $license->shortname;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            if ($record->version < $license->version) {
                // update license record
                $license->enabled = $record->enabled;
                $license->id = $record->id;
                $DB->update_record('license', $license);
            }
        } else {
            $DB->insert_record('license', $license);
        }

        set_config('licenses', implode(',', $active_licenses));
    /// set site default license
        set_config('sitedefaultlicense', 'allrightsreserved');

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010033101.02);
    }

    if ($oldversion < 2010033102.00) {
        // rename course view capability to participate
        $params = array('viewcap'=>'moodle/course:view', 'participatecap'=>'moodle/course:participate');
        $sql = "UPDATE {role_capabilities} SET capability = :participatecap WHERE capability = :viewcap";
        $DB->execute($sql, $params);
        $sql = "UPDATE {capabilities} SET name = :participatecap WHERE name = :viewcap";
        $DB->execute($sql, $params);
        // note: the view capability is readded again at the end of upgrade, but with different meaning
        upgrade_main_savepoint(true, 2010033102.00);
    }

    if ($oldversion < 2010033102.01) {
        // Define field archetype to be added to role table
        $table = new xmldb_table('role');
        $field = new xmldb_field('archetype', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, 'sortorder');
        $dbman->add_field($table, $field);
        upgrade_main_savepoint(true, 2010033102.01);
    }

    if ($oldversion < 2010033102.02) {
        // Set archetype for existing roles and change admin role to manager role
        $sql = "SELECT r.*, rc.capability
                  FROM {role} r
                  JOIN {role_capabilities} rc ON rc.roleid = r.id
                 WHERE rc.contextid = :syscontextid AND rc.capability LIKE :legacycaps
              ORDER BY r.id";
        $params = array('syscontextid'=>SYSCONTEXTID, 'legacycaps'=>'moodle/legacy:%');
        $substart = strlen('moodle/legacy:');
        $roles = $DB->get_recordset_sql($sql, $params); // in theory could be multiple legacy flags in one role
        foreach ($roles as $role) {
            $role->archetype = substr($role->capability, $substart);
            unset($role->capability);
            if ($role->archetype === 'admin') {
                $i = '';
                if ($DB->record_exists('role', array('shortname'=>'manager')) or $DB->record_exists('role', array('name'=>get_string('manager', 'role')))) {
                    $i = 2;
                    while($DB->record_exists('role', array('shortname'=>'manager'.$i)) or $DB->record_exists('role', array('name'=>get_string('manager', 'role').$i))) {
                        $i++;
                    }
                }
                $role->archetype = 'manager';
                if ($role->shortname === 'admin') {
                    $role->shortname   = 'manager'.$i;
                    $role->name        = get_string('manager', 'role').$i;
                    $role->description = get_string('managerdescription', 'role');
                }
            }
            $DB->update_record('role', $role);
        }
        $roles->close();

        upgrade_main_savepoint(true, 2010033102.02);
    }

    if ($oldversion < 2010033102.03) {
        // Now pick site admins (===have manager role assigned at the system context)
        // and store them in the new $CFG->siteadmins setting as comma separated list
        $sql = "SELECT ra.id, ra.userid
                  FROM {role_assignments} ra
                  JOIN {role} r ON r.id = ra.roleid
                  JOIN {user} u ON u.id = ra.userid
                 WHERE ra.contextid = :syscontext AND r.archetype = 'manager' AND u.deleted = 0
              ORDER BY ra.id";
        $ras = $DB->get_records_sql($sql, array('syscontext'=>SYSCONTEXTID));
        $admins = array();
        foreach ($ras as $ra) {
            $admins[$ra->userid] = $ra->userid;
            set_config('siteadmins', implode(',', $admins)); // better to save it repeatedly, we do need at least one admin
            $DB->delete_records('role_assignments', array('id'=>$ra->id));
        }

        upgrade_main_savepoint(true, 2010033102.03);
    }

    if ($oldversion < 2010033102.04) {
        // clean up the manager roles
        $managers = $DB->get_records('role', array('archetype'=>'manager'));
        foreach ($managers as $manager) {
            // now sanitize the capabilities and overrides
            $DB->delete_records('role_capabilities', array('capability'=>'moodle/site:config', 'roleid'=>$manager->id)); // only site admins may configure servers
            // note: doanything and legacy caps are deleted automatically, they get moodle/course:view later at the end of the upgrade

            // remove manager role assignments bellow the course context level - admin role was never intended for activities and blocks,
            // the problem is that those assignments would not be visible after upgrade and old style admins in activities make no sense anyway
            $DB->delete_records_select('role_assignments', "roleid = :manager AND contextid IN (SELECT id FROM {context} WHERE contextlevel > 50)", array('manager'=>$manager->id));

            // allow them to assign all roles except default user, guest and frontpage - users get these roles automatically on the fly when needed
            $DB->delete_records('role_allow_assign', array('roleid'=>$manager->id));
            $roles = $DB->get_records_sql("SELECT * FROM {role} WHERE archetype <> 'user' AND archetype <> 'guest' AND archetype <> 'frontpage'");
            foreach ($roles as $role) {
                $record = (object)array('roleid'=>$manager->id, 'allowassign'=>$role->id);
                $DB->insert_record('role_allow_assign', $record);
            }

            // allow them to override all roles
            $DB->delete_records('role_allow_override', array('roleid'=>$manager->id));
            $roles = $DB->get_records_sql("SELECT * FROM {role}");
            foreach ($roles as $role) {
                $record = (object)array('roleid'=>$manager->id, 'allowoverride'=>$role->id);
                $DB->insert_record('role_allow_override', $record);
            }

            // allow them to switch to all following roles
            $DB->delete_records('role_allow_switch', array('roleid'=>$manager->id));
            $roles = $DB->get_records_sql("SELECT * FROM {role} WHERE archetype IN ('student', 'teacher', 'editingteacher')");
            foreach ($roles as $role) {
                $record = (object)array('roleid'=>$manager->id, 'allowswitch'=>$role->id);
                $DB->insert_record('role_allow_switch', $record);
            }
        }

        upgrade_main_savepoint(true, 2010033102.04);
    }

    if ($oldversion < 2010033102.05) {
        // remove course:view from all roles that are not used for enrolment, it does NOT belong there because it really means user is enrolled!
        $noenrolroles = $DB->get_records_select('role', "archetype IN ('guest', 'user', 'manager', 'coursecreator', 'frontpage')");
        foreach ($noenrolroles as $role) {
            $DB->delete_records('role_capabilities', array('roleid'=>$role->id, 'capability'=>'moodle/course:participate'));
        }
        upgrade_main_savepoint(true, 2010033102.05);
    }

    if ($oldversion < 2010033102.06) {
        // make sure there is nothing weird in default user role
        if (!empty($CFG->defaultuserroleid)) {
            if ($role = $DB->get_record('role', array('id'=>$CFG->defaultuserroleid))) {
                if ($role->archetype !== '' and $role->archetype !== 'user') {
                    upgrade_log(UPGRADE_LOG_NOTICE, null, 'Default authenticated user role (defaultuserroleid) value is invalid, setting cleared.');
                    unset_config('defaultuserroleid');
                }
            } else {
                unset_config('defaultuserroleid');
            }
        }
        upgrade_main_savepoint(true, 2010033102.06);
    }

    if ($oldversion < 2010033102.07) {
        if (!empty($CFG->displayloginfailures) and $CFG->displayloginfailures === 'teacher') {
            upgrade_log(UPGRADE_LOG_NOTICE, null, 'Displaying of login failuters to teachers is not supported any more.');
            unset_config('displayloginfailures');
        }
        upgrade_main_savepoint(true, 2010033102.07);
    }

    if ($oldversion < 2010033102.08) {
        // make sure there are no problems in default guest role settings
        if (!empty($CFG->guestroleid)) {
            if ($role = $DB->get_record('role', array('id'=>$CFG->guestroleid))) {
                if ($role->archetype !== '' and $role->archetype !== 'guest') {
                    upgrade_log(UPGRADE_LOG_NOTICE, null, 'Default guest role (guestroleid) value is invalid, setting cleared.');
                    unset_config('guestroleid');
                }
            } else {
                upgrade_log(UPGRADE_LOG_NOTICE, null, 'Role specified in Default guest role (guestroleid) does not exist, setting cleared.');
                unset_config('guestroleid');
            }
        }
        // remove all roles of the guest account - the only way to change it is to override the guest role, sorry
        // the guest account gets all the role assignments on the fly which works fine in has_capability(),
        $DB->delete_records_select('role_assignments', "userid IN (SELECT id FROM {user} WHERE username = 'guest')");

        upgrade_main_savepoint(true, 2010033102.08);
    }

    /// New table for storing which roles can be assigned in which contexts.
    if ($oldversion < 2010033102.09) {

    /// Define table role_context_levels to be created
        $table = new xmldb_table('role_context_levels');

    /// Adding fields to table role_context_levels
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('contextlevel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table role_context_levels
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextlevel-roleid', XMLDB_KEY_UNIQUE, array('contextlevel', 'roleid'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));

    /// Conditionally launch create table for role_context_levels
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010033102.09);
    }

    if ($oldversion < 2010033102.10) {
        // Now populate the role_context_levels table with the default values
        // NOTE: do not use accesslib methods here

        $rolecontextlevels = array();
        $defaults = array('manager'        => array(CONTEXT_SYSTEM, CONTEXT_COURSECAT, CONTEXT_COURSE),
                          'coursecreator'  => array(CONTEXT_SYSTEM, CONTEXT_COURSECAT),
                          'editingteacher' => array(CONTEXT_COURSE, CONTEXT_MODULE),
                          'teacher'        => array(CONTEXT_COURSE, CONTEXT_MODULE),
                          'student'        => array(CONTEXT_COURSE, CONTEXT_MODULE),
                          'guest'          => array(),
                          'user'           => array(),
                          'frontpage'      => array());

        $roles = $DB->get_records('role', array(), '', 'id, archetype');
        foreach ($roles as $role) {
            if (isset($defaults[$role->archetype])) {
                $rolecontextlevels[$role->id] = $defaults[$role->archetype];
            }
        }

        // add roles without archetypes, it may contain weird things, but we can not fix them
        list($narsql, $params) = $DB->get_in_or_equal(array_keys($defaults), SQL_PARAMS_NAMED, 'ar', false);
        $sql = "SELECT DISTINCT ra.roleid, con.contextlevel
                  FROM {role_assignments} ra
                  JOIN {context} con ON ra.contextid = con.id
                  JOIN {role} r ON r.id = ra.roleid
                 WHERE r.archetype $narsql";
        $existingrolecontextlevels = $DB->get_recordset_sql($sql, $params);
        foreach ($existingrolecontextlevels as $rcl) {
            if (!isset($rolecontextlevels[$rcl->roleid])) {
                $rolecontextlevels[$rcl->roleid] = array();
            }
            $rolecontextlevels[$rcl->roleid][] = $rcl->contextlevel;
        }
        $existingrolecontextlevels->close();

        // Put the data into the database.
        $rcl = new stdClass();
        foreach ($rolecontextlevels as $roleid => $contextlevels) {
            $rcl->roleid = $roleid;
            foreach ($contextlevels as $level) {
                $rcl->contextlevel = $level;
                $DB->insert_record('role_context_levels', $rcl, false);
            }
        }

        // release memory!!
        unset($roles);
        unset($defaults);
        unset($rcl);
        unset($existingrolecontextlevels);
        unset($rolecontextlevels);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010033102.10);
    }

    if ($oldversion < 2010040700) {
        // migrate old groupings --> groupmembersonly setting
        if (isset($CFG->enablegroupings)) {
            set_config('enablegroupmembersonly', $CFG->enablegroupings);
            unset_config('enablegroupings');
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010040700);
    }

    if ($oldversion < 2010040900) {

        // Changing the default of field lang on table user to good old "en"
        $table = new xmldb_table('user');
        $field = new xmldb_field('lang', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'en', 'country');

        // Launch change of default for field lang
        $dbman->change_field_default($table, $field);

        // update main site lang
        if (strpos($CFG->lang, '_utf8') !== false) {
            $lang = str_replace('_utf8', '', $CFG->lang);
            set_config('lang', $lang);
        }

        // tweak langlist
        if (!empty($CFG->langlist)) {
            $langs = explode(',', $CFG->langlist);
            foreach ($langs as $key=>$lang) {
                $lang = str_replace('_utf8', '', $lang);
                $langs[$key] = $lang;
            }
            set_config('langlist', implode(',', $langs));
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010040900);
    }

    if ($oldversion < 2010040901) {

        // Remove "_utf8" suffix from all langs in user table
        $langs = $DB->get_records_sql("SELECT DISTINCT lang FROM {user} WHERE lang LIKE ?", array('%_utf8'));

        foreach ($langs as $lang=>$unused) {
            $newlang = str_replace('_utf8', '', $lang);
            $sql = "UPDATE {user} SET lang = :newlang WHERE lang = :lang";
            $DB->execute($sql, array('newlang'=>$newlang, 'lang'=>$lang));
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010040901);
    }

    if ($oldversion < 2010041301) {
        $sql = "UPDATE {block} SET name=? WHERE name=?";
        $DB->execute($sql, array('navigation', 'global_navigation_tree'));
        $DB->execute($sql, array('settings', 'settings_navigation_tree'));

        $sql = "UPDATE {block_instances} SET blockname=? WHERE blockname=?";
        $DB->execute($sql, array('navigation', 'global_navigation_tree'));
        $DB->execute($sql, array('settings', 'settings_navigation_tree'));
        upgrade_main_savepoint(true, 2010041301);
    }

    if ($oldversion < 2010042100) {

    /// Define table backup_controllers to be created
        $table = new xmldb_table('backup_controllers');

    /// Adding fields to table backup_controllers
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('backupid', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '6', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('format', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('interactive', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('purpose', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('execution', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('executiontime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('checksum', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('controller', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table backup_controllers
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('backupid_uk', XMLDB_KEY_UNIQUE, array('backupid'));
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table backup_controllers
        $table->add_index('typeitem_ix', XMLDB_INDEX_NOTUNIQUE, array('type', 'itemid'));

    /// Conditionally launch create table for backup_controllers
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Define table backup_ids_template to be created
        $table = new xmldb_table('backup_ids_template');

    /// Adding fields to table backup_ids_template
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('backupid', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemname', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('parentitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table backup_ids_template
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('backupid_itemname_itemid_uk', XMLDB_KEY_UNIQUE, array('backupid', 'itemname', 'itemid'));

    /// Adding indexes to table backup_ids_template
        $table->add_index('backupid_parentitemid_ix', XMLDB_INDEX_NOTUNIQUE, array('backupid', 'itemname', 'parentitemid'));

    /// Conditionally launch create table for backup_controllers
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010042100);
    }

    if ($oldversion < 2010042301) {

        $table = new xmldb_table('course_sections');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'section');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2010042301);
    }

    if ($oldversion < 2010042302) {
        // Define table cohort to be created
        $table = new xmldb_table('cohort');

        // Adding fields to table cohort
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '254', null, XMLDB_NOTNULL, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table cohort
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('context', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

        // Conditionally launch create table for cohort
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_main_savepoint(true, 2010042302);
    }

    if ($oldversion < 2010042303) {
        // Define table cohort_members to be created
        $table = new xmldb_table('cohort_members');

        // Adding fields to table cohort_members
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cohortid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timeadded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table cohort_members
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('cohortid', XMLDB_KEY_FOREIGN, array('cohortid'), 'cohort', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Adding indexes to table cohort_members
        $table->add_index('cohortid-userid', XMLDB_INDEX_UNIQUE, array('cohortid', 'userid'));

        // Conditionally launch create table for cohort_members
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010042303);
    }

    if ($oldversion < 2010042800) {
        //drop the previously created ratings table
        $table = new xmldb_table('ratings');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        //create the rating table (replaces module specific rating implementations)
        $table = new xmldb_table('rating');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Adding fields to table rating
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('rating', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table rating
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table rating
        $table->add_index('itemid', XMLDB_INDEX_NOTUNIQUE, array('itemid'));

    /// Create table for ratings
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_main_savepoint(true, 2010042800);
    }

    if ($oldversion < 2010042801) {
        // migrating old comments block content
        $DB->execute("UPDATE {comments}
                         SET contextid = (SELECT parentcontextid
                                            FROM {block_instances}
                                           WHERE id = {comments}.itemid AND blockname = 'comments'),
                             commentarea = 'page_comments',
                             itemid = 0
                       WHERE commentarea = 'block_comments'
                             AND itemid != 0
                             AND EXISTS (SELECT 'x'
                                           FROM {block_instances}
                                          WHERE id = {comments}.itemid
                                                AND blockname = 'comments')");

        // remove all orphaned record
        $DB->delete_records('comments', array('commentarea'=>'block_comments'));
        upgrade_main_savepoint(true, 2010042801);
    }

    if ($oldversion < 2010042802) { // Change backup_controllers->type to varchar10 (recreate dep. index)

    /// Define index typeitem_ix (not unique) to be dropped form backup_controllers
        $table = new xmldb_table('backup_controllers');
        $index = new xmldb_index('typeitem_ix', XMLDB_INDEX_NOTUNIQUE, array('type', 'itemid'));

    /// Conditionally launch drop index typeitem_ix
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

    /// Changing precision of field type on table backup_controllers to (10)
        $table = new xmldb_table('backup_controllers');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, 'backupid');

    /// Launch change of precision for field type
        $dbman->change_field_precision($table, $field);

    /// Define index typeitem_ix (not unique) to be added to backup_controllers
        $table = new xmldb_table('backup_controllers');
        $index = new xmldb_index('typeitem_ix', XMLDB_INDEX_NOTUNIQUE, array('type', 'itemid'));

    /// Conditionally launch add index typeitem_ix
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010042802);
    }

    if ($oldversion < 2010043000) {  // Adding new course completion feature

    /// Add course completion tables
    /// Define table course_completion_aggr_methd to be created
        $table = new xmldb_table('course_completion_aggr_methd');

    /// Adding fields to table course_completion_aggr_methd
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('criteriatype', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('method', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('value', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);

    /// Adding keys to table course_completion_aggr_methd
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_aggr_methd
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('criteriatype', XMLDB_INDEX_NOTUNIQUE, array('criteriatype'));

    /// Conditionally launch create table for course_completion_aggr_methd
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Define table course_completion_criteria to be created
        $table = new xmldb_table('course_completion_criteria');

    /// Adding fields to table course_completion_criteria
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('criteriatype', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('module', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('moduleinstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('courseinstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('enrolperiod', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timeend', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradepass', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->add_field('role', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table course_completion_criteria
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_criteria
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

    /// Conditionally launch create table for course_completion_criteria
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Define table course_completion_crit_compl to be created
        $table = new xmldb_table('course_completion_crit_compl');

    /// Adding fields to table course_completion_crit_compl
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('criteriaid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('gradefinal', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->add_field('unenroled', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table course_completion_crit_compl
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_crit_compl
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('criteriaid', XMLDB_INDEX_NOTUNIQUE, array('criteriaid'));
        $table->add_index('timecompleted', XMLDB_INDEX_NOTUNIQUE, array('timecompleted'));

    /// Conditionally launch create table for course_completion_crit_compl
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Define table course_completion_notify to be created
        $table = new xmldb_table('course_completion_notify');

    /// Adding fields to table course_completion_notify
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('role', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('message', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table course_completion_notify
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_notify
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

    /// Conditionally launch create table for course_completion_notify
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Define table course_completions to be created
        $table = new xmldb_table('course_completions');

    /// Adding fields to table course_completions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timenotified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timeenrolled', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timestarted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('reaggregate', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table course_completions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completions
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('timecompleted', XMLDB_INDEX_NOTUNIQUE, array('timecompleted'));

    /// Conditionally launch create table for course_completions
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Add cols to course table
    /// Define field enablecompletion to be added to course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enablecompletion', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'defaultrole');

    /// Conditionally launch add field enablecompletion
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionstartonenrol to be added to course
        $field = new xmldb_field('completionstartonenrol', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'enablecompletion');

    /// Conditionally launch add field completionstartonenrol
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionnotify to be added to course
        $field = new xmldb_field('completionnotify', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'enablecompletion');

    /// Conditionally launch add field completionnotify
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2010043000);
    }

    if ($oldversion < 2010043001) {

    /// Define table registration_hubs to be created
        $table = new xmldb_table('registration_hubs');

    /// Adding fields to table registration_hubs
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hubname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('huburl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('confirmed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table registration_hubs
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for registration_hubs
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010043001);
    }

    if ($oldversion < 2010050200) {

    /// Define table backup_logs to be created
        $table = new xmldb_table('backup_logs');

    /// Adding fields to table backup_logs
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('backupid', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('loglevel', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('message', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table backup_logs
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('backupid', XMLDB_KEY_FOREIGN, array('backupid'), 'backup_controllers', array('backupid'));

    /// Adding indexes to table backup_logs
        $table->add_index('backupid-id', XMLDB_INDEX_UNIQUE, array('backupid', 'id'));

    /// Conditionally launch create table for backup_logs
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Drop some old backup tables, not used anymore

    /// Define table backup_files to be dropped
        $table = new xmldb_table('backup_files');

    /// Conditionally launch drop table for backup_files
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table backup_ids to be dropped
        $table = new xmldb_table('backup_ids');

    /// Conditionally launch drop table for backup_ids
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010050200);
    }

    if ($oldversion < 2010050403) {  // my_pages for My Moodle and Public Profile pages

    /// Define table my_pages to be created
        $table = new xmldb_table('my_pages');

    /// Adding fields to table my_pages
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 0);
        $table->add_field('name', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('private', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL, null, '0');


    /// Adding keys to table my_pages
        $table->add_key('id', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table my_pages
        $table->add_index('useridprivate', XMLDB_INDEX_NOTUNIQUE, array('userid', 'private'));

    /// Conditionally launch create table for my_pages
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Add two lines of data into this new table.  These are the default pages.
        $mypage = new stdClass();
        $mypage->userid = NULL;
        $mypage->name = '__default';
        $mypage->private = 0;
        $mypage->sortorder  = 0;
        if (!$DB->record_exists('my_pages', array('userid'=>NULL, 'private'=>0))) {
            $DB->insert_record('my_pages', $mypage);
        }
        $mypage->private = 1;
        if (!$DB->record_exists('my_pages', array('userid'=>NULL, 'private'=>1))) {
            $DB->insert_record('my_pages', $mypage);
        }

    /// This bit is a "illegal" hack, unfortunately, but there is not a better way to install default
    /// blocks right now, since the upgrade function need to be called after core AND plugins upgrade,
    /// and there is no such hook yet.  Sigh.

        if ($mypage = $DB->get_record('my_pages', array('userid'=>NULL, 'private'=>1))) {
            if (!$DB->record_exists('block_instances', array('pagetypepattern'=>'my-index', 'parentcontextid'=>SITEID, 'subpagepattern'=>$mypage->id))) {

                // No default exist there yet, let's put a few into My Moodle so it's useful.

                $blockinstance = new stdClass;
                $blockinstance->parentcontextid = SYSCONTEXTID;
                $blockinstance->showinsubcontexts = 0;
                $blockinstance->pagetypepattern = 'my-index';
                $blockinstance->subpagepattern = $mypage->id;
                $blockinstance->configdata = '';

                $blockinstance->blockname = 'private_files';
                $blockinstance->defaultregion = 'side-post';
                $blockinstance->defaultweight = 0;
                $blockinstanceid = $DB->insert_record('block_instances', $blockinstance);
                get_context_instance(CONTEXT_BLOCK, $blockinstanceid);

                $blockinstance->blockname = 'online_users';
                $blockinstance->defaultregion = 'side-post';
                $blockinstance->defaultweight = 1;
                $blockinstanceid = $DB->insert_record('block_instances', $blockinstance);
                get_context_instance(CONTEXT_BLOCK, $blockinstanceid);

                $blockinstance->blockname = 'course_overview';
                $blockinstance->defaultregion = 'content';
                $blockinstance->defaultweight = 0;
                $blockinstanceid = $DB->insert_record('block_instances', $blockinstance);
                get_context_instance(CONTEXT_BLOCK, $blockinstanceid);
            }
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010050403);
    }

    if ($oldversion < 2010051500) {

    /// Fix a bad table name that existed for a few days in HEAD
        $table = new xmldb_table('published_courses');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table course_published to be created
        $table = new xmldb_table('course_published');

    /// Adding fields to table course_published
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('hubid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timepublished', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('enrollable', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('hubcourseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table course_published
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for course_published
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010051500);
    }

    if ($oldversion < 2010051600) {

    /// Delete the blocks completely.  All the contexts, instances etc were cleaned up above in 2009082800
        $DB->delete_records('block', array('name'=>'admin'));
        $DB->delete_records('block', array('name'=>'admin_tree'));

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010051600);
    }

    if ($oldversion < 2010051800) {
        // switching to userid in config settings because user names are not unique and reliable enough
        if (!empty($CFG->courserequestnotify) and $CFG->courserequestnotify !== '$@NONE@$' and $CFG->courserequestnotify !== '$@ALL@$') {
            list($where, $params) = $DB->get_in_or_equal(explode(',', $CFG->courserequestnotify));
            $params[] = $CFG->mnet_localhost_id;
            $users = $DB->get_fieldset_select('user', 'id', "username $where AND mnethostid = ?", $params);
            if ($users) {
                set_config('courserequestnotify', implode(',', $users));
            } else {
                set_config('courserequestnotify', '$@NONE@$');
            }
        }
        upgrade_main_savepoint(true, 2010051800);
    }

    if ($oldversion < 2010051801) {
        // Update the notifyloginfailures setting.
        if ($CFG->notifyloginfailures == 'mainadmin') {
            if ($admins = explode(',', $CFG->siteadmins)) {
                $adminid = reset($admins);
                set_config('notifyloginfailures', $adminid);
            } else {
                unset_config('notifyloginfailures'); // let them choose
            }
            unset($admins);

        } else if ($CFG->notifyloginfailures == 'alladmins') {
            set_config('notifyloginfailures', '$@ALL@$');

        } else {
            set_config('notifyloginfailures', '$@NONE@$');
        }

        upgrade_main_savepoint(true, 2010051801);
    }

    if ($oldversion < 2010052100) {
        // Switch to html purifier as default cleaning engine - KSES is really very bad
        if (empty($CFG->enablehtmlpurifier)) {
            unset_config('enablehtmlpurifier');
        }
        upgrade_main_savepoint(true, 2010052100);
    }

    if ($oldversion < 2010052200) {
        // Define field legacyfiles to be added to course - just in case we are upgrading from PR1
        $table = new xmldb_table('course');
        $field = new xmldb_field('legacyfiles', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'maxbytes');

        // Conditionally launch add field legacyfiles
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            // enable legacy files in all courses
            $DB->execute("UPDATE {course} SET legacyfiles = 2");
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010052200);
    }

    if ($oldversion < 2010052401) {

    /// Define field status to be added to course_published
        $table = new xmldb_table('course_published');
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0', 'hubcourseid');

    /// Conditionally launch add field status
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field timechecked to be added to course_published
        $table = new xmldb_table('course_published');
        $field = new xmldb_field('timechecked', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'status');

    /// Conditionally launch add field timechecked
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010052401);
    }

    if ($oldversion < 2010052700) {

    /// Define field summaryformat to be added to course sections table
        $table = new xmldb_table('course_sections');
        $field = new xmldb_field('summaryformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'summary');

    /// Conditionally launch add field summaryformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->set_field('course_sections', 'summaryformat', 1, array()); // originally treated as HTML

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010052700);
    }

    if ($oldversion < 2010052800) {
    /// Changes to modinfo mean we need to rebuild course cache
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);
        upgrade_main_savepoint(true, 2010052800);
    }

    if ($oldversion < 2010052801) {

    /// Define field sortorder to be added to files
        $table = new xmldb_table('files');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timemodified');

    /// Conditionally launch add field sortorder
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010052801);
    }

    if ($oldversion < 2010061900.01) {
        // Define table enrol to be created
        $table = new xmldb_table('enrol');

        // Adding fields to table enrol
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('enrol', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('enrolperiod', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('enrolstartdate', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('enrolenddate', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('expirynotify', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('expirythreshold', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('notifyall', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('password', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('cost', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('currency', XMLDB_TYPE_CHAR, '3', null, null, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('customint1', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('customint2', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('customint3', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('customint4', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('customchar1', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('customchar2', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('customdec1', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null);
        $table->add_field('customdec2', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null);
        $table->add_field('customtext1', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('customtext2', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table enrol
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

        // Adding indexes to table enrol
        $table->add_index('enrol', XMLDB_INDEX_NOTUNIQUE, array('enrol'));

        // launch create table for enrol
        $dbman->create_table($table);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.01);
    }

    if ($oldversion < 2010061900.02) {
        // Define table course_participant to be created
        $table = new xmldb_table('user_enrolments');

        // Adding fields to table course_participant
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('enrolid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timestart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timeend', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2147483647');
        $table->add_field('modifierid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table course_participant
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('enrolid', XMLDB_KEY_FOREIGN, array('enrolid'), 'enrol', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('modifierid', XMLDB_KEY_FOREIGN, array('modifierid'), 'user', array('id'));


        // Adding indexes to table user_enrolments
        $table->add_index('enrolid-userid', XMLDB_INDEX_UNIQUE, array('enrolid', 'userid'));

        // Launch create table for course_participant
        $dbman->create_table($table);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.02);
    }

    if ($oldversion < 2010061900.03) {
        // Define field itemid to be added to role_assignments
        $table = new xmldb_table('role_assignments');
        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'enrol');

        // Launch add field itemid
        $dbman->add_field($table, $field);

        // The new enrol plugins may assign one role several times in one context,
        // if we did not allow it we would have big problems with roles when unenrolling
        $table = new xmldb_table('role_assignments');
        $index = new xmldb_index('contextid-roleid-userid', XMLDB_INDEX_UNIQUE, array('contextid', 'roleid', 'userid'));

        // Conditionally launch drop index contextid-roleid-userid
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.03);
    }

    if ($oldversion < 2010061900.04) {
        // there is no default course role any more, each enrol plugin has to handle it separately
        if (!empty($CFG->defaultcourseroleid)) {
            $sql = "UPDATE {course} SET defaultrole = :defaultrole WHERE defaultrole = 0";
            $params = array('defaultrole' => $CFG->defaultcourseroleid);
            $DB->execute($sql, $params);
        }
        unset_config('defaultcourseroleid');

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.04);
    }

    if ($oldversion < 2010061900.05) {
        // make sure enrol settings make actually sense and tweak defaults a bit

        $sqlempty = $DB->sql_empty();

        // set course->enrol to default value so that other upgrade code is simpler
        $defaultenrol = empty($CFG->enrol) ? 'manual' : $CFG->enrol;
        $sql = "UPDATE {course} SET enrol = ? WHERE enrol = '$sqlempty'";
        $DB->execute($sql, array($defaultenrol));
        unset_config('enrol');

        if (!isset($CFG->enrol_plugins_enabled) or empty($CFG->enrol_plugins_enabled)) {
            set_config('enrol_plugins_enabled', 'manual');
        } else {
            $enabledplugins = explode(',', $CFG->enrol_plugins_enabled);
            $enabledplugins = array_unique($enabledplugins);
            set_config('enrol_plugins_enabled', implode(',', $enabledplugins));
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.05);
    }

    if ($oldversion < 2010061900.06) {
        $sqlempty = $DB->sql_empty();
        $params = array('siteid'=>SITEID);

        // enable manual in all courses
        $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, enrolperiod, expirynotify, expirythreshold, notifyall, roleid, timecreated, timemodified)
                SELECT 'manual', 0, id, 0, enrolperiod, expirynotify, expirythreshold, notifystudents, defaultrole, timecreated, timemodified
                  FROM {course}
                 WHERE id <> :siteid";
        $DB->execute($sql, $params);

        // enable self enrol only when course enrollable
        $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, enrolperiod, enrolstartdate, enrolenddate, expirynotify, expirythreshold,
                                     notifyall, password, roleid, timecreated, timemodified)
                SELECT 'self', 0, id, 1, enrolperiod, enrolstartdate, enrolenddate, expirynotify, expirythreshold,
                       notifystudents, password, defaultrole, timecreated, timemodified
                  FROM {course}
                 WHERE enrollable = 1 AND id <> :siteid";
        $DB->execute($sql, $params);

        // enable guest access if previously allowed - separately with or without password
        $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, timecreated, timemodified)
                SELECT 'guest', 0, id, 2, timecreated, timemodified
                  FROM {course}
                 WHERE guest = 1 AND id <> :siteid";
        $DB->execute($sql, $params);
        $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, password, timecreated, timemodified)
                SELECT 'guest', 0, id, 2, password, timecreated, timemodified
                  FROM {course}
                 WHERE guest = 2 and password <> '$sqlempty' AND id <> :siteid";
        $DB->execute($sql, $params);

        upgrade_main_savepoint(true, 2010061900.06);
    }

    if ($oldversion < 2010061900.07) {
        // now migrate old style "interactive" enrol plugins - we know them by looking into course.enrol
        $params = array('siteid'=>SITEID);
        $enabledplugins = explode(',', $CFG->enrol_plugins_enabled);
        $usedplugins = $DB->get_fieldset_sql("SELECT DISTINCT enrol FROM {course}");
        foreach ($usedplugins as $plugin) {
            if ($plugin === 'manual') {
                continue;
            }
            $enabled = in_array($plugin, $enabledplugins) ? 0 : 1; // 0 means active, 1 disabled
            $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, enrolperiod, enrolstartdate, enrolenddate, expirynotify, expirythreshold,
                                         notifyall, password, cost, currency, roleid, timecreated, timemodified)
                    SELECT enrol, $enabled, id, 4, enrolperiod, enrolstartdate, enrolenddate, expirynotify, expirythreshold,
                           notifystudents, password, cost, currency, defaultrole, timecreated, timemodified
                      FROM {course}
                     WHERE enrol = :plugin AND id <> :siteid";
            $params['plugin'] = $plugin;
            $DB->execute($sql, $params);
        }
        upgrade_main_savepoint(true, 2010061900.07);
    }

    if ($oldversion < 2010061900.08) {
        // now migrate the rest - these plugins are not in course.enrol, instead we just look for suspicious role assignments,
        // unfortunately old enrol plugins were doing sometimes weird role assignments :-(

        // enabled
            $enabledplugins = explode(',', $CFG->enrol_plugins_enabled);
        list($sqlenabled, $params) = $DB->get_in_or_equal($enabledplugins, SQL_PARAMS_NAMED, 'ena');
        $params['siteid'] = SITEID;
        $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, enrolperiod, enrolstartdate, enrolenddate, expirynotify, expirythreshold,
                                     notifyall, password, cost, currency, roleid, timecreated, timemodified)
                SELECT DISTINCT ra.enrol, 0, c.id, 5, c.enrolperiod, c.enrolstartdate, c.enrolenddate, c.expirynotify, c.expirythreshold,
                       c.notifystudents, c.password, c.cost, c.currency, c.defaultrole, c.timecreated, c.timemodified
                  FROM {course} c
                  JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = 50)
                  JOIN {role_assignments} ra ON (ra.contextid = ctx.id)
                 WHERE c.id <> :siteid AND ra.enrol $sqlenabled";
        $processed = $DB->get_fieldset_sql("SELECT DISTINCT enrol FROM {enrol}");
        if ($processed) {
            list($sqlnotprocessed, $params2) = $DB->get_in_or_equal($processed, SQL_PARAMS_NAMED, 'np', false);
            $params = array_merge($params, $params2);
            $sql = "$sql AND ra.enrol $sqlnotprocessed";
        }
        $DB->execute($sql, $params);

        // disabled
        $params = array('siteid' => SITEID);
        $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, enrolperiod, enrolstartdate, enrolenddate, expirynotify, expirythreshold,
                                     notifyall, password, cost, currency, roleid, timecreated, timemodified)
                SELECT DISTINCT ra.enrol, 1, c.id, 5, c.enrolperiod, c.enrolstartdate, c.enrolenddate, c.expirynotify, c.expirythreshold,
                       c.notifystudents, c.password, c.cost, c.currency, c.defaultrole, c.timecreated, c.timemodified
                  FROM {course} c
                  JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = 50)
                  JOIN {role_assignments} ra ON (ra.contextid = ctx.id)
                 WHERE c.id <> :siteid";
        $processed = $DB->get_fieldset_sql("SELECT DISTINCT enrol FROM {enrol}");
        if ($processed) {
            list($sqlnotprocessed, $params2) = $DB->get_in_or_equal($processed, SQL_PARAMS_NAMED, 'np', false);
            $params = array_merge($params, $params2);
            $sql = "$sql AND ra.enrol $sqlnotprocessed";
        }
        $DB->execute($sql, $params);

        upgrade_main_savepoint(true, 2010061900.08);
    }

    if ($oldversion < 2010061900.09) {
        // unfortunately there may be still some leftovers
        // after reconfigured, uninstalled or borked enrol plugins,
        // unfortunately this may be a bit slow - but there should not be many of these
        upgrade_set_timeout();
        $sqlempty = $DB->sql_empty();
        $sql = "SELECT DISTINCT c.id AS courseid, ra.enrol, c.timecreated, c.timemodified
                  FROM {course} c
                  JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = 50)
                  JOIN {role_assignments} ra ON (ra.contextid = ctx.id AND ra.enrol <> '$sqlempty')
             LEFT JOIN {enrol} e ON (e.courseid = c.id AND e.enrol = ra.enrol)
                 WHERE c.id <> :siteid AND e.id IS NULL";
        $params = array('siteid'=>SITEID);
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $enrol) {
            upgrade_set_timeout();
            $enrol->status = 1; // better disable them
            $DB->insert_record('enrol', $enrol);
        }
        $rs->close();
        upgrade_main_savepoint(true, 2010061900.09);
    }

    if ($oldversion < 2010061900.10) {
        // migrate existing setup of meta courses, ignore records referencing invalid courses
        $sql = "INSERT INTO {enrol} (enrol, status, courseid, sortorder, customint1)
                SELECT 'meta', 0, cm.parent_course, 5, cm.child_course
                  FROM {course_meta} cm
                  JOIN {course} p ON p.id = cm.parent_course
                  JOIN {course} c ON c.id = cm.child_course";
        $DB->execute($sql);

        upgrade_main_savepoint(true, 2010061900.10);
    }

    if ($oldversion < 2010061900.11) {
        // nuke any old role assignments+enrolments in previous meta courses, we have to start from scratch
        $select = "SELECT ctx.id
                     FROM {context} ctx
                     JOIN {course} c ON (c.id = ctx.instanceid AND ctx.contextlevel = 50 AND c.metacourse = 1)";
        $DB->delete_records_select('role_assignments', "contextid IN ($select) AND enrol = 'manual'");

        // course_meta to be dropped - we use enrol_meta plugin instead now
        $table = new xmldb_table('course_meta');

        // Launch drop table for course_meta
        $dbman->drop_table($table);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.11);
    }

    if ($oldversion < 2010061900.12) {
        // finally remove all obsolete fields from the course table - yay!
        // all the information was migrated to the enrol table

        // Define field guest to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('guest');

        // Conditionally launch drop field guest
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field password to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('password');

        // Conditionally launch drop field password
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field enrolperiod to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enrolperiod');

        // Conditionally launch drop field enrolperiod
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field cost to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('cost');

        // Conditionally launch drop field cost
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field currency to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('currency');

        // Conditionally launch drop field currency
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field metacourse to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('metacourse');

        // Conditionally launch drop field metacourse
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field expirynotify to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('expirynotify');

        // Conditionally launch drop field expirynotify
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field expirythreshold to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('expirythreshold');

        // Conditionally launch drop field expirythreshold
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field notifystudents to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('notifystudents');

        // Conditionally launch drop field notifystudents
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field enrollable to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enrollable');

        // Conditionally launch drop field enrollable
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field enrolstartdate to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enrolstartdate');

        // Conditionally launch drop field enrolstartdate
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field enrolenddate to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enrolenddate');

        // Conditionally launch drop field enrolenddate
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field enrol to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enrol');

        // Conditionally launch drop field enrol
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field defaultrole to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('defaultrole');

        // Conditionally launch drop field defaultrole
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_main_savepoint(true, 2010061900.12);
    }

    if ($oldversion < 2010061900.13) {
        // Define field visibleold to be added to course_categories
        $table = new xmldb_table('course_categories');
        $field = new xmldb_field('visibleold', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'visible');

        // Launch add field visibleold
        $dbman->add_field($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.13);
    }

    if ($oldversion < 2010061900.14) {
        // keep previous visible state
        $DB->execute("UPDATE {course_categories} SET visibleold = visible");

        // make sure all subcategories of hidden categories are hidden too, do not rely on category path yet
        $sql = "SELECT c.id
                  FROM {course_categories} c
                  JOIN {course_categories} pc ON (pc.id = c.parent AND pc.visible = 0)
                 WHERE c.visible = 1";
        while ($categories = $DB->get_records_sql($sql)) {
            foreach ($categories as $cat) {
                upgrade_set_timeout();
                $DB->set_field('course_categories', 'visible', 0, array('id'=>$cat->id));
            }
        }
        upgrade_main_savepoint(true, 2010061900.14);
    }

    if ($oldversion < 2010061900.15) {
        // Define field visibleold to be added to course
        $table = new xmldb_table('course');
        $field = new xmldb_field('visibleold', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'visible');

        // Launch add field visibleold
        $dbman->add_field($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.15);
    }

    if ($oldversion < 2010061900.16) {
        // keep previous visible state
        $DB->execute("UPDATE {course} SET visibleold = visible");

        // make sure all courses in hidden categories are hidden
        $DB->execute("UPDATE {course} SET visible = 0 WHERE category IN (SELECT id FROM {course_categories} WHERE visible = 0)");

        upgrade_main_savepoint(true, 2010061900.16);
    }

    if ($oldversion < 2010061900.20) {
        // now set up the enrolments - look for roles with course:participate only at course context - the category enrolments are synchronised later by archetype and new capability

        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $params = array('syscontext'=>$syscontext->id, 'participate'=>'moodle/course:participate');
        $roles = $DB->get_fieldset_sql("SELECT DISTINCT roleid FROM {role_capabilities} WHERE contextid = :syscontext AND capability = :participate AND permission = 1", $params);
        if ($roles) {
            list($sqlroles, $params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r');

            $sql = "INSERT INTO {user_enrolments} (status, enrolid, userid, timestart, timeend, modifierid, timecreated, timemodified)

                    SELECT 0, e.id, ra.userid, MIN(ra.timestart), MIN(ra.timeend), 0, MIN(ra.timemodified), MAX(ra.timemodified)
                      FROM {role_assignments} ra
                      JOIN {context} c ON (c.id = ra.contextid AND c.contextlevel = 50)
                      JOIN {enrol} e ON (e.enrol = ra.enrol AND e.courseid = c.instanceid)
                      JOIN {user} u ON u.id = ra.userid
                     WHERE u.deleted = 0 AND ra.roleid $sqlroles
                  GROUP BY e.id, ra.userid";
            $DB->execute($sql, $params);
        }

        upgrade_main_savepoint(true, 2010061900.20);
    }

    if ($oldversion < 2010061900.21) {
        // hidden is completely removed, timestart+timeend are now in the user_enrolments table

        // Define field hidden to be dropped from role_assignments
        $table = new xmldb_table('role_assignments');
        $field = new xmldb_field('hidden');

        // Conditionally launch drop field hidden
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field timestart to be dropped from role_assignments
        $table = new xmldb_table('role_assignments');
        $field = new xmldb_field('timestart');

        // Conditionally launch drop field timestart
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field timeend to be dropped from role_assignments
        $table = new xmldb_table('role_assignments');
        $field = new xmldb_field('timeend');

        // Conditionally launch drop field timeend
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.21);
    }

    if ($oldversion < 2010061900.22) {
        // Rename field enrol on table role_assignments to component and update content

        $table = new xmldb_table('role_assignments');
        $field = new xmldb_field('enrol', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'modifierid');

        // Launch rename field enrol
        $dbman->rename_field($table, $field, 'component');

        // Changing precision of field component on table role_assignments to (100)
        $table = new xmldb_table('role_assignments');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'modifierid');

        // Launch change of precision for field component
        $dbman->change_field_precision($table, $field);

        // Manual is a special case - we use empty string instead now
        $params = array('empty'=>$DB->sql_empty(), 'manual'=>'manual');
        $sql = "UPDATE {role_assignments}
                   SET component = :empty
                 WHERE component = :manual";
        $DB->execute($sql, $params);

        // Now migrate to real enrol component names
        $params = array('empty'=>$DB->sql_empty());
        $concat = $DB->sql_concat("'enrol_'", 'component');
        $sql = "UPDATE {role_assignments}
                   SET component = $concat
                 WHERE component <> :empty
                       AND contextid IN (
                           SELECT id
                             FROM {context}
                            WHERE contextlevel >= 50)";
        $DB->execute($sql, $params);

        // Now migrate to real auth component names
        $params = array('empty'=>$DB->sql_empty());
        $concat = $DB->sql_concat("'auth_'", 'component');
        $sql = "UPDATE {role_assignments}
                   SET component = $concat
                 WHERE component <> :empty
                       AND contextid IN (
                           SELECT id
                             FROM {context}
                            WHERE contextlevel < 50)";
        $DB->execute($sql, $params);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.22);
    }

    if ($oldversion < 2010061900.23) {
        // add proper itemid to role assignments that were added by enrolment plugins
        $sql = "UPDATE {role_assignments}
                   SET itemid = (SELECT MIN({enrol}.id)
                                    FROM {enrol}
                                    JOIN {context} ON ({context}.contextlevel = 50 AND {context}.instanceid = {enrol}.courseid)
                                   WHERE {role_assignments}.component = ".$DB->sql_concat("'enrol_'", "{enrol}.enrol")." AND {context}.id = {role_assignments}.contextid)
                 WHERE component <> 'enrol_manual' AND component LIKE 'enrol_%'";
        $DB->execute($sql);
        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.23);
    }

    if ($oldversion < 2010061900.30) {
        // make new list of active enrol plugins - order is important, meta should be always last, manual first
        $enabledplugins = explode(',', $CFG->enrol_plugins_enabled);
        $enabledplugins = array_merge(array('manual', 'guest', 'self', 'cohort'), $enabledplugins);
        if ($DB->record_exists('enrol', array('enrol'=>'meta'))) {
            $enabledplugins[] = 'meta';
        }
        $enabledplugins = array_unique($enabledplugins);
        set_config('enrol_plugins_enabled', implode(',', $enabledplugins));

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010061900.30);
    }

    if ($oldversion < 2010061900.31) {
        // finalise all new enrol settings and cleanup old settings

        // legacy allowunenrol was deprecated in 1.9 already
        unset_config('allwunenroll');

        // obsolete course presets
        unset_config('metacourse', 'moodlecourse');
        unset_config('enrol', 'moodlecourse');
        unset_config('enrollable', 'moodlecourse');
        unset_config('enrolperiod', 'moodlecourse');
        unset_config('expirynotify', 'moodlecourse');
        unset_config('notifystudents', 'moodlecourse');
        unset_config('expirythreshold', 'moodlecourse');
        unset_config('enrolpassword', 'moodlecourse');
        unset_config('guest', 'moodlecourse');

        unset_config('backup_sche_metacourse', 'backup');

        unset_config('lastexpirynotify');

        // hidden course categories now prevent only browsing, courses are accessible if you know the URL and course is visible
        unset_config('allowvisiblecoursesinhiddencategories');

        if (isset($CFG->coursemanager)) {
            set_config('coursecontact', $CFG->coursemanager);
            unset_config('coursemanager');
        }

        // migrate plugin settings - the problem here is we are splitting manual into three different plugins
        if (isset($CFG->enrol_manual_usepasswordpolicy)) {
            set_config('usepasswordpolicy', $CFG->enrol_manual_usepasswordpolicy, 'enrol_guest');
            set_config('usepasswordpolicy', $CFG->enrol_manual_usepasswordpolicy, 'enrol_self');
            set_config('groupenrolmentkeypolicy', $CFG->enrol_manual_usepasswordpolicy);
            unset_config('enrol_manual_usepasswordpolicy');
        }
        if (isset($CFG->enrol_manual_requirekey)) {
            set_config('requirepassword', $CFG->enrol_manual_requirekey, 'enrol_guest');
            set_config('requirepassword', $CFG->enrol_manual_requirekey, 'enrol_self');
            unset_config('enrol_manual_requirekey');
        }
        if (isset($CFG->enrol_manual_showhint)) {
            set_config('showhint', $CFG->enrol_manual_showhint, 'enrol_guest');
            set_config('showhint', $CFG->enrol_manual_showhint, 'enrol_self');
            unset_config('enrol_manual_showhint');
        }

        upgrade_main_savepoint(true, 2010061900.31);
    }

    if ($oldversion < 2010061900.32) {
        // MDL-22797 course completion has to be updated to use new enrol framework, it will not be enabled in final 2.0
        set_config('enableavailability', 0);
        set_config('enablecompletion', 0);
        upgrade_main_savepoint(true, 2010061900.32);
    }

    if ($oldversion < 2010062101) {

    /// Define field huburl to be dropped from course_published
        $table = new xmldb_table('course_published');
        $field = new xmldb_field('hubid');

    /// Conditionally launch drop field huburl
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Define field huburl to be added to course_published
        $table = new xmldb_table('course_published');
        $field = new xmldb_field('huburl', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'id');

    /// Conditionally launch add field huburl
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010062101);
    }

    if ($oldversion < 2010070300) {
        //TODO: this is a temporary hack for upgrade from PR3, to be removed later

        // Define field component to be added to files
        $table = new xmldb_table('files');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'contextid');

        // Conditionally upgrade from PR3
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $index = new xmldb_index('filearea-contextid-itemid', XMLDB_INDEX_NOTUNIQUE, array('filearea', 'contextid', 'itemid'));
            $dbman->drop_index($table, $index);
            $index = new xmldb_index('component-filearea-contextid-itemid', XMLDB_INDEX_NOTUNIQUE, array('component', 'filearea', 'contextid', 'itemid'));
            $dbman->add_index($table, $index);

            // Rename areas as add proper component
            $areas = $DB->get_fieldset_sql("SELECT DISTINCT filearea FROM {files}");
            if ($areas) {
                // fix incorrect itemids
                $DB->execute("UPDATE {files} SET itemid = 0 WHERE filearea = 'category_description'"); // context identifies instances
                $DB->execute("UPDATE {files} SET itemid = 0 WHERE filearea = 'user_profile'"); // context identifies instances
                $DB->execute("UPDATE {files} SET itemid = 0 WHERE filearea = 'block_html'"); // context identifies instances
                foreach ($areas as $area) {
                    // rename areas
                    if ($area === 'course_backup') {
                        $area = 'backup_course';
                    } else if ($area === 'section_backup') {
                        $area = 'backup_section';
                    } else if ($area === 'activity_backup') {
                        $area = 'backup_activity';
                    } else if ($area === 'category_description') {
                        $area = 'coursecat_description';
                    }
                    if ($area === 'block_html') {
                        $component = 'block_html';
                        $filearea = 'content';
                    } else {
                        list($component, $filearea) = explode('_', $area, 2);
                        // note this is just a hack which guesses plugin from old PRE3 files code, the whole point of adding component is to get rid of this guessing
                        if (file_exists("$CFG->dirroot/mod/$component/lib.php")) {
                            $component = 'mod_'.$component;
                        }
                    }
                    $DB->execute("UPDATE {files} SET component = :component, filearea = :filearea WHERE filearea = :area", array('component'=>$component, 'filearea'=>$filearea, 'area'=>$area));
                }
                // Update all hashes
                $rs = $DB->get_recordset('files', array());
                foreach ($rs as $file) {
                    upgrade_set_timeout();
                    $pathnamehash = sha1("/$file->contextid/$file->component/$file->filearea/$file->itemid".$file->filepath.$file->filename);
                    $DB->set_field('files', 'pathnamehash', $pathnamehash, array('id'=>$file->id));
                }
                $rs->close();
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010070300);
    }

    if ($oldversion < 2010070500) {

    /// Define field operation to be added to backup_controllers
        $table = new xmldb_table('backup_controllers');
        $field = new xmldb_field('operation', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'backup', 'backupid');

    /// Conditionally launch add field operation
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010070500);
    }

    if ($oldversion < 2010070501) {

    /// Define field suspended to be added to user
        $table = new xmldb_table('user');
        $field = new xmldb_field('suspended', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'deleted');

    /// Conditionally launch add field suspended
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010070501);
    }

    if ($oldversion < 2010070502) {

    /// Define field newitemid to be added to backup_ids_template
        $table = new xmldb_table('backup_ids_template');
        $field = new xmldb_field('newitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'itemid');
    /// Conditionally launch add field newitemid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field info to be added to backup_ids_template
        $table = new xmldb_table('backup_ids_template');
        $field = new xmldb_field('info', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'parentitemid');
    /// Conditionally launch add field info
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010070502);
    }

    if ($oldversion < 2010070601) {
        // delete loan calc if not used - it was moved to contrib
        if (!file_exists("$CFG->dirroot/blocks/loancalc/version.php")) {
            if (!$DB->record_exists('block_instances', array('blockname'=>'loancalc'))) {
                $DB->delete_records('block', array('name'=>'loancalc'));
            }
        }
        upgrade_main_savepoint(true, 2010070601);
    }

    if ($oldversion < 2010070602) {
        // delete exercise if not used and not installed - now in contrib (do not use adminlib uninstall functions, they may change)
        if (!file_exists("$CFG->dirroot/mod/exercise/version.php")) {
            if ($module = $DB->get_record('modules', array('name'=>'exercise'))) {
                if (!$DB->record_exists('course_modules', array('module'=>$module->id))) {
                    //purge capabilities
                    $DB->delete_records_select('capabilities', "name LIKE ?", array('mod/exercise:%'));
                    $DB->delete_records_select('role_capabilities', "capability LIKE ?", array('mod/exercise:%'));
                    $tables = array('exercise', 'exercise_submissions', 'exercise_assessments', 'exercise_elements', 'exercise_rubrics', 'exercise_grades');
                    foreach ($tables as $tname) {
                        $table = new xmldb_table($tname);
                        if ($dbman->table_exists($table)) {
                            $dbman->drop_table($table);
                        }
                    }
                    $DB->delete_records('event', array('modulename' => 'exercise'));
                    $DB->delete_records('log', array('module' => 'exercise'));
                    $DB->delete_records('modules', array('name'=>'exercise'));
                }
            }
        }
        upgrade_main_savepoint(true, 2010070602);
    }

    if ($oldversion < 2010070603) {
        // delete journal if not used and not installed - now in contrib (do not use adminlib uninstall functions, they may change)
        if (!file_exists("$CFG->dirroot/mod/journal/version.php")) {
            if ($module = $DB->get_record('modules', array('name'=>'journal'))) {
                if (!$DB->record_exists('course_modules', array('module'=>$module->id))) {
                    //purge capabilities
                    $DB->delete_records_select('capabilities', "name LIKE ?", array('mod/journal:%'));
                    $DB->delete_records_select('role_capabilities', "capability LIKE ?", array('mod/journal:%'));
                    $tables = array('journal', 'journal_entries');
                    foreach ($tables as $tname) {
                        $table = new xmldb_table($tname);
                        if ($dbman->table_exists($table)) {
                            $dbman->drop_table($table);
                        }
                    }
                    $DB->delete_records('event', array('modulename' => 'journal'));
                    $DB->delete_records('log', array('module' => 'journal'));
                    $DB->delete_records('modules', array('name'=>'journal'));
                    unset_config('journal_initialdisable');
                }
            }
        }
        upgrade_main_savepoint(true, 2010070603);
    }

    if ($oldversion < 2010070604) {
        // delete lams if not used and not installed - now in contrib (do not use adminlib uninstall functions, they may change)
        if (!file_exists("$CFG->dirroot/mod/lams/version.php")) {
            if ($module = $DB->get_record('modules', array('name'=>'lams'))) {
                if (!$DB->record_exists('course_modules', array('module'=>$module->id))) {
                    //purge capabilities
                    $DB->delete_records_select('capabilities', "name LIKE ?", array('mod/lams:%'));
                    $DB->delete_records_select('role_capabilities', "capability LIKE ?", array('mod/lams:%'));
                    $tables = array('lams', '');
                    foreach ($tables as $tname) {
                        $table = new xmldb_table($tname);
                        if ($dbman->table_exists($table)) {
                            $dbman->drop_table($table);
                        }
                    }
                    $DB->delete_records('event', array('modulename' => 'lams'));
                    $DB->delete_records('log', array('module' => 'lams'));
                    $DB->delete_records('modules', array('name'=>'lams'));
                    unset_config('lams_initialdisable');
                }
            }
        }
        upgrade_main_savepoint(true, 2010070604);
    }

    if ($oldversion < 2010070801) {
    /// Before changing the field, drop dependent indexes
    /// Define index shortname (not unique) to be dropped form course_request
        $table = new xmldb_table('user');
        $index = new xmldb_index('city', XMLDB_INDEX_NOTUNIQUE, array('city'));
    /// Conditionally launch drop index shortname
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
    /// Changing precision of field city on table user to (120)
        $field = new xmldb_field('city', XMLDB_TYPE_CHAR, '120', null, XMLDB_NOTNULL, null, null, 'address');

    /// Launch change of precision for field city
        $dbman->change_field_precision($table, $field);

    /// Conditionally launch add index typeitem_ix
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010070801);
    }

    if ($oldversion < 2010071000) {
        //purge unused editor settings
        unset_config('editorbackgroundcolor');
        unset_config('editorfontfamily');
        unset_config('editorfontsize');
        unset_config('editorkillword');
        unset_config('editorhidebuttons');
        unset_config('editorfontlist');

        upgrade_main_savepoint(true, 2010071000);
    }

    if ($oldversion < 2010071001) {
        // purge obsolete stats settings
        unset_config('statscatdepth');
        upgrade_main_savepoint(true, 2010071001);
    }

    if ($oldversion < 2010071100) {
        // move user icons to file storage pool
        upgrade_migrate_user_icons();
        upgrade_main_savepoint(true, 2010071100);
    }

    if ($oldversion < 2010071101) {
        // move user icons to file storage pool
        upgrade_migrate_group_icons();
        upgrade_main_savepoint(true, 2010071101);
    }

    if ($oldversion < 2010071300) {
        // Define field timecreated to be added to user_enrolments
        $table = new xmldb_table('user_enrolments');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'modifierid');

        // Launch add field timecreated
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // now try to guess the time created
        $sql = "UPDATE {user_enrolments} SET timecreated = timemodified WHERE timecreated = 0";
        $DB->execute($sql);
        $sql = "UPDATE {user_enrolments} SET timecreated = timestart WHERE timestart <> 0 AND timestart < timemodified";
        $DB->execute($sql);

        upgrade_main_savepoint(true, 2010071300);
    }

    if ($oldversion < 2010071700) { // Make itemname bigger (160cc) to store component+filearea

        $table = new xmldb_table('backup_ids_template');
        // Define key backupid_itemname_itemid_uk (unique) to be dropped form backup_ids_template
        $key = new xmldb_key('backupid_itemname_itemid_uk', XMLDB_KEY_UNIQUE, array('backupid', 'itemname', 'itemid'));
        // Define index backupid_parentitemid_ix (not unique) to be dropped form backup_ids_template
        $index = new xmldb_index('backupid_parentitemid_ix', XMLDB_INDEX_NOTUNIQUE, array('backupid', 'itemname', 'parentitemid'));
        // Define field itemname to be 160cc
        $field = new xmldb_field('itemname', XMLDB_TYPE_CHAR, '160', null, XMLDB_NOTNULL, null, null, 'backupid');

        // Launch drop key backupid_itemname_itemid_uk
        $dbman->drop_key($table, $key);
        // Conditionally launch drop index backupid_parentitemid_ix
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Changing precision of field itemname on table backup_ids_template to (160)
        $dbman->change_field_precision($table, $field);

        // Launch add key backupid_itemname_itemid_uk
        $dbman->add_key($table, $key);
        // Conditionally launch add index backupid_parentitemid_ix
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010071700);
    }

    if ($oldversion < 2010071701) {
        // Drop legacy core tables that now belongs to mnetservice_enrol plugin
        // Upgrade procedure not needed as the tables are used for caching purposes only
        $tables = array('mnet_enrol_course', 'mnet_enrol_assignments');
        foreach ($tables as $tname) {
            $table = new xmldb_table($tname);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }

        upgrade_main_savepoint(true, 2010071701);
    }

    if ($oldversion < 2010071800) {

        // Define table backup_files_template to be created
        $table = new xmldb_table('backup_files_template');

        // Adding fields to table backup_files_template
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('backupid', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filearea', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('info', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table backup_files_template
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table backup_files_template
        $table->add_index('backupid_contextid_component_filearea_itemid_ix', XMLDB_INDEX_NOTUNIQUE, array('backupid', 'contextid', 'component', 'filearea', 'itemid'));

        // Conditionally launch create table for backup_files_template
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010071800);
    }

    if ($oldversion < 2010072300) {

        // Define field capabilities to be added to external_functions
        $table = new xmldb_table('external_functions');
        $field = new xmldb_field('capabilities', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'component');

        // Conditionally launch add field capabilities
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010072300);
    }

    if ($oldversion < 2010072700) {

        // Define index backupid_itemname_newitemid_ix (not unique) to be added to backup_ids_template
        $table = new xmldb_table('backup_ids_template');
        $index = new xmldb_index('backupid_itemname_newitemid_ix', XMLDB_INDEX_NOTUNIQUE, array('backupid', 'itemname', 'newitemid'));

        // Conditionally launch add index backupid_itemname_newitemid_ix
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010072700);
    }

    if ($oldversion < 2010080303) {
        $rs = $DB->get_recordset_sql('SELECT i.id, i.name, r.type FROM {repository_instances} i, {repository} r WHERE i.typeid = r.id');
        foreach ($rs as $record) {
            upgrade_set_timeout();
            if ($record->name == $record->type) {
                // repository_instances was saving type name as in name field
                // which should be empty, the repository api will try to find
                // instance name from language files
                $DB->set_field('repository_instances', 'name', '');
            }
        }
        $rs->close();
        upgrade_main_savepoint(true, 2010080303);
    }

    if ($oldversion < 2010080305) {
        // first drop all log display actions, we will recreate them automatically later
        $DB->delete_records('log_display', array());

        // Define field component to be added to log_display
        $table = new xmldb_table('log_display');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'field');

        // Launch add field component
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010080305);
    }

    if ($oldversion < 2010080900) {

    /// Define field generalfeedbackformat to be added to question
        $table = new xmldb_table('question');
        $field = new xmldb_field('generalfeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'generalfeedback');

    /// Conditionally launch add field generalfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Upgrading the text formats in some question types depends on the
    /// questiontextformat field, but the question type upgrade only runs
    /// after the code below has messed around with the questiontextformat
    /// value. Therefore, we need to create a new column to store the old value.
    /// The column should be dropped in Moodle 2.1.
    /// Define field oldquestiontextformat to be added to question
        $field = new xmldb_field('oldquestiontextformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'generalfeedback');

    /// Conditionally launch add field oldquestiontextformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field infoformat to be added to question_categories
        $table = new xmldb_table('question_categories');
        $field = new xmldb_field('infoformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'info');

    /// Conditionally launch add field infoformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field answerformat to be added to question_answers
        $table = new xmldb_table('question_answers');
        $field = new xmldb_field('answerformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'answer');

    /// Conditionally launch add field answerformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field feedbackformat to be added to question_answers
        $field = new xmldb_field('feedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'feedback');

    /// Conditionally launch add field feedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field manualcommentformat to be added to question_sessions
        $table = new xmldb_table('question_sessions');
        $field = new xmldb_field('manualcommentformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'manualcomment');

    /// Conditionally launch add field manualcommentformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint(true, 2010080900);
    }

    /// updating question image
    if ($oldversion < 2010080901) {
        $fs = get_file_storage();

        // Define field image to be dropped from question
        $table = new xmldb_table('question');
        $field = new xmldb_field('image');

        // Conditionally launch drop field image
        if ($dbman->field_exists($table, $field)) {

            $rs = $DB->get_recordset('question');
            $textlib = textlib_get_instance();

            foreach ($rs as $question) {
                // may take awhile
                upgrade_set_timeout();
                if (empty($question->image)) {
                    continue;
                }
                if (!$category = $DB->get_record('question_categories', array('id'=>$question->category))) {
                    continue;
                }
                $categorycontext = get_context_instance_by_id($category->contextid);
                // question files are stored in course level
                // so we have to find course context
                switch ($categorycontext->contextlevel){
                    case CONTEXT_COURSE :
                        $context = $categorycontext;
                        break;
                    case CONTEXT_MODULE :
                        $courseid = $DB->get_field('course_modules', 'course', array('id'=>$categorycontext->instanceid));
                        $context = get_context_instance(CONTEXT_COURSE, $courseid);
                        break;
                    case CONTEXT_COURSECAT :
                    case CONTEXT_SYSTEM :
                        $context = get_system_context();
                        break;
                    default :
                        continue;
                }
                if ($textlib->substr($textlib->strtolower($question->image), 0, 7) == 'http://') {
                    // it is a link, appending to existing question text
                    $question->questiontext .= ' <img src="' . $question->image . '" />';
                    $question->image = '';
                    // update question record
                    $DB->update_record('question', $question);
                } else {
                    $filename = basename($question->image);
                    $filepath = dirname($question->image);
                    if (empty($filepath) or $filepath == '.' or $filepath == '/') {
                        $filepath = '/';
                    } else {
                        // append /
                        $filepath = '/'.trim($filepath, './@#$ ').'/';
                    }

                    // course files already moved to file pool by previous upgrade block
                    // so we just create copy from course_legacy area
                    if ($image = $fs->get_file($context->id, 'course', 'legacy', 0, $filepath, $filename)) {
                        // move files to file pool
                        $file_record = array(
                            'contextid'=>$category->contextid,
                            'component'=>'question',
                            'filearea'=>'questiontext',
                            'itemid'=>$question->id
                        );
                        $fs->create_file_from_storedfile($file_record, $image);
                        $question->questiontext .= ' <img src="@@PLUGINFILE@@' . $filepath . $filename . '" />';
                        $question->image = '';
                        // update question record
                        $DB->update_record('question', $question);
                    }
                }
            }
            $rs->close();

            $dbman->drop_field($table, $field);
        }

        // Update question_answers.
        // In question_answers.feedback was previously always treated as
        // FORMAT_HTML in calculated, multianswer, multichoice, numerical,
        // shortanswer and truefalse; and
        // FORMAT_MOODLE in essay (despite being edited using the HTML editor)
        // So essay feedback needs to be converted to HTML unless $CFG->texteditors == 'textarea'.
        // For all question types except multichoice,
        // question_answers.answer is FORMAT_PLAIN and does not need to be changed.
        // For multichoice, question_answers.answer is FORMAT_MOODLE, and should
        // stay that way, at least for now.
        $rs = $DB->get_recordset_sql('
                SELECT qa.*, q.qtype
                FROM {question_answers} qa
                JOIN {question} q ON qa.question = q.id');
        foreach ($rs as $record) {
            // may take awhile
            upgrade_set_timeout();
            // Convert question_answers.answer
            if ($record->qtype !== 'multichoice') {
                $record->answerformat = FORMAT_PLAIN;
            } else {
                $record->answerformat = FORMAT_MOODLE;
            }

            // Convert question_answers.feedback
            if ($CFG->texteditors !== 'textarea') {
                if ($record->qtype == 'essay') {
                    $record->feedback = text_to_html($record->feedback, false, false, true);
                }
                $record->feedbackformat = FORMAT_HTML;
            } else {
                $record->feedbackformat = FORMAT_MOODLE;
            }

            $DB->update_record('question_answers', $record);
        }
        $rs->close();

        // In the question table, the code previously used questiontextformat
        // for both question text and general feedback. We need to copy the
        // values into the new column.
        // Then we need to convert FORMAT_MOODLE to FORMAT_HTML (depending on
        // $CFG->texteditors).
        $DB->execute('
                UPDATE {question}
                SET generalfeedbackformat = questiontextformat');
        // Also save the old questiontextformat, so that plugins that need it
        // can access it.
        $DB->execute('
                UPDATE {question}
                SET oldquestiontextformat = questiontextformat');
        // Now covert FORMAT_MOODLE content, if necssary.
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('question', array('questiontextformat'=>FORMAT_MOODLE));
            foreach ($rs as $record) {
                // may take awhile
                upgrade_set_timeout();
                $record->questiontext = text_to_html($record->questiontext, false, false, true);
                $record->questiontextformat = FORMAT_HTML;
                $record->generalfeedback = text_to_html($record->generalfeedback, false, false, true);
                $record->generalfeedbackformat = FORMAT_HTML;
                $DB->update_record('question', $record);
            }
            $rs->close();
        }

        // In the past, question_sessions.manualcommentformat was always treated
        // as FORMAT_HTML.
        $DB->set_field('question_sessions', 'manualcommentformat', FORMAT_HTML);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010080901);
    }

    if ($oldversion < 2010082502) {
        // migrate file pool xx/xx/xx directory structure to xx/xx in older 2.0dev installs
        upgrade_simplify_overkill_pool_structure();
        upgrade_main_savepoint(true, 2010082502);
    }

    if ($oldversion < 2010091303) {
        // drop all test tables from old xmldb test suite
        $table = new xmldb_table('testtable');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $table = new xmldb_table('anothertest');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $table = new xmldb_table('newnameforthetable');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        upgrade_main_savepoint(true, 2010091303);
    }

    if ($oldversion < 2010091500) {

        // Changing precision of field token on table registration_hubs to (255)
        $table = new xmldb_table('registration_hubs');
        $field = new xmldb_field('token', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of precision for field token
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091500);
    }

    if ($oldversion < 2010091501) {
        // This index used to exist in Moodle 1.9 and was never dropped in the upgrade above.
        // Drop it now, or it breaks the following alter column.

        // Define index pagetypepattern (not unique) to be dropped form block_instances
        $table = new xmldb_table('block_instances');
        $index = new xmldb_index('pagetypepattern', XMLDB_INDEX_NOTUNIQUE, array('pagetypepattern'));

        // Conditionally launch drop index pagetypepattern
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091501);
    }

    if ($oldversion < 2010091502) {
        // Need to drop the index before we can alter the column precision in the next step.

        // Define index parentcontextid-showinsubcontexts-pagetypepattern-subpagepattern (not unique) to be dropped form block_instances
        $table = new xmldb_table('block_instances');
        $index = new xmldb_index('parentcontextid-showinsubcontexts-pagetypepattern-subpagepattern', XMLDB_INDEX_NOTUNIQUE, array('parentcontextid', 'showinsubcontexts', 'pagetypepattern', 'subpagepattern'));

        // Conditionally launch drop index parentcontextid-showinsubcontexts-pagetypepattern-subpagepattern
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091502);
    }

    if ($oldversion < 2010091503) {

        // Changing precision of field pagetypepattern on table block_instances to (64)
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('pagetypepattern', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'showinsubcontexts');

        // Launch change of precision for field pagetypepattern
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091503);
    }

    if ($oldversion < 2010091504) {
        // Now add the index back.

        // Define index parentcontextid-showinsubcontexts-pagetypepattern-subpagepattern (not unique) to be added to block_instances
        $table = new xmldb_table('block_instances');
        $index = new xmldb_index('parentcontextid-showinsubcontexts-pagetypepattern-subpagepattern', XMLDB_INDEX_NOTUNIQUE, array('parentcontextid', 'showinsubcontexts', 'pagetypepattern', 'subpagepattern'));

        // Conditionally launch add index parentcontextid-showinsubcontexts-pagetypepattern-subpagepattern
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091504);
    }

    if ($oldversion < 2010091505) {
        // drop all events queued from 1.9, unfortunately we can not process them because the serialisation of data changed
        // also the events format was changed....
        $DB->delete_records('events_queue_handlers', array());
        $DB->delete_records('events_queue', array());

        //reset all status fields too
        $DB->set_field('events_handlers', 'status', 0, array());

        upgrade_main_savepoint(true, 2010091505);
    }

    if ($oldversion < 2010091506) {
        // change component string in events_handlers records to new "_" format
        if ($handlers = $DB->get_records('events_handlers')) {
            foreach ($handlers as $handler) {
                $handler->handlermodule = str_replace('/', '_', $handler->handlermodule);
                $DB->update_record('events_handlers', $handler);
            }
        }
        unset($handlers);
        upgrade_main_savepoint(true, 2010091506);
    }

    if ($oldversion < 2010091507) {

        // Define index eventname-handlermodule (unique) to be dropped form events_handlers
        $table = new xmldb_table('events_handlers');
        $index = new xmldb_index('eventname-handlermodule', XMLDB_INDEX_UNIQUE, array('eventname', 'handlermodule'));

        // Conditionally launch drop index eventname-handlermodule
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091507);
    }

    if ($oldversion < 2010091508) {

        // Rename field handlermodule on table events_handlers to component
        $table = new xmldb_table('events_handlers');
        $field = new xmldb_field('handlermodule', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null, 'eventname');

        // Launch rename field handlermodule
        $dbman->rename_field($table, $field, 'component');

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091508);
    }

    if ($oldversion < 2010091509) {

        // Define index eventname-component (unique) to be added to events_handlers
        $table = new xmldb_table('events_handlers');
        $index = new xmldb_index('eventname-component', XMLDB_INDEX_UNIQUE, array('eventname', 'component'));

        // Conditionally launch add index eventname-component
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091509);
    }

    if ($oldversion < 2010091510) {

        // Define field internal to be added to events_handlers
        $table = new xmldb_table('events_handlers');
        $field = new xmldb_field('internal', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'status');

        // Conditionally launch add field internal
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010091510);
    }

    if ($oldversion < 2010091700) {
        // Fix MNet sso_jump_url for Moodle application
        $DB->set_field('mnet_application', 'sso_jump_url', '/auth/mnet/jump.php',
                       array('name' => 'moodle', 'sso_jump_url' => '/auth/mnet/land.php'));
        upgrade_main_savepoint(true, 2010091700);
    }

    if ($oldversion < 2010092000) {
        // drop multiple field again because it was still in install.xml in 2.0dev

        // Define field multiple to be dropped from block
        $table = new xmldb_table('block');
        $field = new xmldb_field('multiple');

        // Conditionally launch drop field multiple
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010092000);
    }

    if ($oldversion < 2010101300) {
        // Fix MDL-24641 : the registered language should not be empty otherwise cron will fail
        $registeredhubs = $DB->get_records('registration_hubs', array('confirmed' => 1));
        if (!empty($registeredhubs)) {
            foreach ($registeredhubs as $hub) {
                $cleanhuburl = clean_param($hub->huburl, PARAM_ALPHANUMEXT);
                $sitelanguage = get_config('hub', 'site_language_' . $cleanhuburl);
                if (empty($sitelanguage)) {
                    set_config('site_language_' . $cleanhuburl, current_language(), 'hub');
                }
            }
        }
        upgrade_main_savepoint(true, 2010101300);
    }

    //MDL-24721 -add hidden column to grade_categories. This was done previously but it wasn't included in
    //install.xml so there are 2.0 sites that are missing it.
    if ($oldversion < 2010101900) {
        $table = new xmldb_table('grade_categories');
        $field = new xmldb_field('hidden', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'timemodified');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2010101900);
    }

    // new format of the emoticons setting
    if ($oldversion < 2010102300) {
        unset($CFG->emoticons);
        $DB->delete_records('config', array('name' => 'emoticons'));
        $DB->delete_records('cache_text'); // changed md5 hash calculation
        upgrade_main_savepoint(true, 2010102300);
    }

    //MDL-24771
    if ($oldversion < 2010102601) {

        $fieldnotification = new xmldb_field('notification', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, 0, 'smallmessage');
        $fieldcontexturl = new xmldb_field('contexturl', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'notification');
        $fieldcontexturlname = new xmldb_field('contexturlname', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'contexturl');
        $fieldstoadd = array($fieldnotification, $fieldcontexturl, $fieldcontexturlname);

        $tablestomodify = array(new xmldb_table('message'), new xmldb_table('message_read'));

        foreach($tablestomodify as $table) {
            foreach($fieldstoadd as $field) {
                if (!$dbman->field_exists($table, $field)) {
                    $dbman->add_field($table, $field);
                }
            }
        }

        upgrade_main_savepoint(true, 2010102601);
    }

    // MDL-24694 needs increasing size of user_preferences.name(varchar[50]) field due to
    // long preferences names for messaging which need components parts within the name
    // eg: 'message_provider_mod_assignment_assignments_loggedin'
    if ($oldversion < 2010102602) {

        // Define index userid-name (unique) to be dropped form user_preferences
        $table = new xmldb_table('user_preferences');
        $index = new xmldb_index('userid-name', XMLDB_INDEX_UNIQUE, array('userid', 'name'));

        // Conditionally launch drop index userid-name
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Changing precision of field name on table user_preferences to (255)
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'userid');

        // Launch change of precision for field name
        $dbman->change_field_precision($table, $field);

        // Conditionally launch add index userid-name
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010102602);
    }

    if ($oldversion < 2010102700) {

        $table = new xmldb_table('post');
        $field = new xmldb_field('uniquehash', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, 'content');
        // Launch change of precision for field name
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010102700);
    }

    if ($oldversion < 2010110200) {

        // fix tags itemtype for wiki
        $sql = "UPDATE {tag_instance}
                SET itemtype = 'wiki_pages'
                WHERE itemtype = 'wiki_page'";
        $DB->execute($sql);

        echo $OUTPUT->notification('Updating tags itemtype', 'notifysuccess');

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010110200);
    }

    //remove forum_logblocked from config. No longer required after user->emailstop was removed
    if ($oldversion < 2010110500) {
        unset_config('forum_logblocked');
        upgrade_main_savepoint(true, 2010110500);
    }

    if ($oldversion < 2010110800) {
        // convert $CFG->disablecourseajax to $CFG->enablecourseajax
        $disabledcourseajax = get_config('disablecourseajax', 0);
        if ($disabledcourseajax) {
            set_config('enablecourseajax', 0);
        } else {
            set_config('enablecourseajax', 1);
        }
        unset_config('disablecourseajax');

        upgrade_main_savepoint(true, 2010110800);
    }

    if ($oldversion < 2010111000) {

        // Clean up the old scheduled backup settings that are no longer relevant
        update_fix_automated_backup_config();
        upgrade_main_savepoint(true, 2010111000);
    }

    if ($oldversion < 2010111702) {

        // Clean up the old experimental split restore no loger used
        unset_config('experimentalsplitrestore');

        upgrade_main_savepoint(true, 2010111702);
    }

    if ($oldversion < 2010121401) {

        // Define table profiling to be created
        $table = new xmldb_table('profiling');

        // Adding fields to table profiling
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('runid', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $table->add_field('totalexecutiontime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('totalcputime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('totalcalls', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('totalmemory', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('runreference', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('runcomment', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table profiling
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('runid_uk', XMLDB_KEY_UNIQUE, array('runid'));

        // Adding indexes to table profiling
        $table->add_index('url_runreference_ix', XMLDB_INDEX_NOTUNIQUE, array('url', 'runreference'));
        $table->add_index('timecreated_runreference_ix', XMLDB_INDEX_NOTUNIQUE, array('timecreated', 'runreference'));

        // Conditionally launch create table for profiling
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2010121401);
    }

    if ($oldversion < 2011011401) {
        $columns = $DB->get_columns('block_instances');

        // Check if we need to fix the default weight column
        if (array_key_exists('defaultweight', $columns) && $columns['defaultweight']->max_length != 10) {
            // Fix discrepancies in the block_instances table after upgrade from 1.9
            $table = new xmldb_table('block_instances');

            // defaultweight is smallint(3) after upgrade should be bigint 10
            // Also fixed in earlier upgrade code
            $field = new xmldb_field('defaultweight', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null, 'defaultregion');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            }

            // add missing key `blocinst_par_ix` (`parentcontextid`)
            $index = new xmldb_index('parentcontextid', XMLDB_INDEX_NOTUNIQUE, array('parentcontextid'));
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011011401);
    }

    if ($oldversion < 2011011402) {
        // Fix discrepancies in the block_positions table after upgrade from 1.9
        $table = new xmldb_table('block_positions');
        $columns = $DB->get_columns('block_positions');

        // Check if we need to fix the blockinstanceid field
        if (array_key_exists('blockinstanceid', $columns) && empty($columns['blockinstanceid']->unsigned)) {
            // Fix blockinstanceid
            // First remove the indexs on the field
            $indexone = new xmldb_index('blockinstanceid', XMLDB_INDEX_NOTUNIQUE, array('blockinstanceid'));
            $indexall = new xmldb_index('blockinstanceid-contextid-pagetype-subpage', XMLDB_INDEX_UNIQUE, array('blockinstanceid','contextid','pagetype','subpage'));
            if ($dbman->index_exists($table, $indexone)) {
                $dbman->drop_index($table, $indexone);
            }
            if ($dbman->index_exists($table, $indexall)) {
                $dbman->drop_index($table, $indexall);
            }
            // blockinstanceid should be unsigned
            // Also fixed in earlier upgrade code
            $field = new xmldb_field('blockinstanceid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'id');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_unsigned($table, $field);
            }

            // Add the indexs back in
            $dbman->add_index($table, $indexone);
            $dbman->add_index($table, $indexall);
        }

        // Check if the visible field needs fixing.
        if (array_key_exists('visible', $columns) && !empty($columns['visible']->has_default)) {
            // visible shouldn't have a default
            // Also fixed in earlier upgrade code
            $field = new xmldb_field('visible', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, null, 'subpage');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011011402);
    }

    if ($oldversion < 2011011403) {
        $columns = $DB->get_columns('grade_categories');
        // Check if we need to fix the hidden field
        if (array_key_exists('hidden', $columns) && $columns['hidden']->max_length != 1) {
            // Fix discrepancies in the grade_categories table after upgrade from 1.9
            $table = new xmldb_table('grade_categories');

            // hidden should be tinyint(1)
            // Also fixed in earlier upgrade code
            $field = new xmldb_field('hidden', XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'timemodified');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_precision($table, $field);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011011403);
    }

    if ($oldversion < 2011011404) {
        // Fix discrepancies in the message table after upgrade from 1.9
        $columns = $DB->get_columns('message');
        $table = new xmldb_table('message');

        // Check if we need to fix the useridfrom field
        if (array_key_exists('useridfrom', $columns) && empty($columns['useridfrom']->unsigned)) {
            // useridfrom should be unsigned
            $field = new xmldb_field('useridfrom', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'id');
            $index = new xmldb_index('useridfrom', XMLDB_INDEX_NOTUNIQUE, array('useridfrom'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_unsigned($table, $field);
            }
            $dbman->add_index($table, $index);
        }

        // Check if we need to fix the useridto field
        if (array_key_exists('useridto', $columns) && empty($columns['useridto']->unsigned)) {
            // useridto should be unsigned
            $field = new xmldb_field('useridto', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'useridfrom');
            $index = new xmldb_index('useridto', XMLDB_INDEX_NOTUNIQUE, array('useridto'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_unsigned($table, $field);
            }
            $dbman->add_index($table, $index);
        }

        // Check if we need to fix the notification field
        if (array_key_exists('notification', $columns) && !empty($columns['notification']->not_null)) {
            // notification should allow null
            // Fixed in earlier upgrade code
            $field = new xmldb_field('notification', XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, null, null, 0, 'smallmessage');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_notnull($table, $field);
            }
        }

        // Check if we need to fix the contexturl field
        if (array_key_exists('contexturl', $columns) && strpos($columns['contexturl']->type, 'text') === false) {
            // contexturl should be text
            // Fixed in earlier upgrade code
            $field = new xmldb_field('contexturl', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'notification');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            }
        }

        // Check if we need to fix the contexturl field
        if (array_key_exists('contexturlname', $columns) && strpos($columns['contexturlname']->type, 'text') === false) {
            // contexturlname should be text
            // Fixed in earlier upgrade code
            $field = new xmldb_field('contexturlname', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'contexturl');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011011404);
    }

    if ($oldversion < 2011011405) {
        // Fix discrepancies in the message_read table after upgrade from 1.9
        $columns = $DB->get_columns('message_read');
        $table = new xmldb_table('message_read');

        // Check if we need to fix the useridfrom field
        if (array_key_exists('useridfrom', $columns) && empty($columns['useridfrom']->unsigned)) {
            // useridfrom should be unsigned
            $field = new xmldb_field('useridfrom', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'id');
            $index = new xmldb_index('useridfrom', XMLDB_INDEX_NOTUNIQUE, array('useridfrom'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_unsigned($table, $field);
            }
            $dbman->add_index($table, $index);
        }

        // Check if we need to fix the useridto field
        if (array_key_exists('useridto', $columns) && empty($columns['useridto']->unsigned)) {
            // useridto should be unsigned
            $field = new xmldb_field('useridto', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'useridfrom');
            $index = new xmldb_index('useridto', XMLDB_INDEX_NOTUNIQUE, array('useridto'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_unsigned($table, $field);
            }
            $dbman->add_index($table, $index);
        }

        // Check if we need to fix the notification field
        if (array_key_exists('notification', $columns) && !empty($columns['notification']->not_null)) {
            // notification should allow null
            // Fixed in earlier upgrade code
            $field = new xmldb_field('notification', XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, null, null, 0, 'smallmessage');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_notnull($table, $field);
            }
        }

        // Check if we need to fix the contexturl field
        if (array_key_exists('contexturl', $columns) && strpos($columns['contexturl']->type, 'text') === false) {
            // contexturl should be text
            // Fixed in earlier upgrade code
            $field = new xmldb_field('contexturl', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'notification');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            }
        }

        // Check if we need to fix the contexturl field
        if (array_key_exists('contexturlname', $columns) && strpos($columns['contexturlname']->type, 'text') === false) {
            // contexturlname should be text
            // Fixed in earlier upgrade code
            $field = new xmldb_field('contexturlname', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'contexturl');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011011405);
    }

    if ($oldversion < 2011011406) {
        // Fix discrepancies in the my_pages table after upgrade from 1.9
        $columns = $DB->get_columns('my_pages');
        $table = new xmldb_table('my_pages');

        // Check if we need to fix the private column
        if (array_key_exists('private', $columns) && $columns['private']->default_value != '1') {
            // private should be default 1
            // Fixed in earlier upgrade code
            $field = new xmldb_field('private', XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 1, 'name');
            $index = new xmldb_index('user_idx', XMLDB_INDEX_NOTUNIQUE, array('userid','private'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
            $dbman->add_index($table, $index);
        }

        // Check if we need to fix the sortorder field
        if (array_key_exists('sortorder', $columns) && !empty($columns['sortorder']->unsigned)) {
            // Sortorder should not be unsigned
            // Fixed in earlier upgrade code
            $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, 6, null, XMLDB_NOTNULL, null, 0, 'private');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_notnull($table, $field);
            }
        }

        upgrade_main_savepoint(true, 2011011406);
    }

    if ($oldversion < 2011011407) {
        // Check if we need to fix post.uniquehash
        $columns = $DB->get_columns('my_pages');
        if (array_key_exists('uniquehash', $columns) && $columns['uniquehash']->max_length != 128) {
            // Fix discrepancies in the post table after upgrade from 1.9
            $table = new xmldb_table('post');

            // Uniquehash should be 128 chars
            // Fixed in earlier upgrade code
            $field = new xmldb_field('uniquehash', XMLDB_TYPE_CHAR, 128, null, XMLDB_NOTNULL, null, null, 'content');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_precision($table, $field);
            }
        }

        upgrade_main_savepoint(true, 2011011407);
    }

    if ($oldversion < 2011011408) {
        // Fix question in the post table after upgrade from 1.9
        $columns = $DB->get_columns('question');
        $table = new xmldb_table('question');

        // Check if we need to fix default grade
        if (array_key_exists('defaultgrade', $columns) && (
                empty($columns['defaultgrade']->unsigned) ||
                empty($columns['defaultgrade']->not_null) ||
                $columns['defaultgrade']->default_value !== '1.0000000')) {
            // defaultgrade should be unsigned NOT NULL DEFAULT '1.0000000'
            // Fixed in earlier upgrade code
            $field = new xmldb_field('defaultgrade', XMLDB_TYPE_NUMBER, '12, 7', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1.0000000', 'generalfeedbackformat');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        // Check if we need to fix penalty
        if (array_key_exists('penalty', $columns) && (empty($columns['penalty']->not_null) || $columns['penalty']->default_value !== '0.1000000')) {
            // penalty should be NOT NULL DEFAULT '0.1000000'
            // Fixed in earlier upgrade code
            $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0.1000000', 'defaultgrade');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_main_savepoint(true, 2011011408);
    }

    if ($oldversion < 2011011409) {
        // Fix question_answers in the post table after upgrade from 1.9
        $columns = $DB->get_columns('question_answers');
        $table = new xmldb_table('question_answers');

        if (array_key_exists('fraction', $columns) && empty($columns['fraction']->not_null)) {
            // fraction should be NOT NULL DEFAULT '0.0000000',
            // Fixed in earlier upgrade code
            $field = new xmldb_field('fraction', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'feedback');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_main_savepoint(true, 2011011409);
    }

    if ($oldversion < 2011011410) {
        // Fix question_sessions in the post table after upgrade from 1.9
        $columns = $DB->get_columns('question_sessions');
        $table = new xmldb_table('question_sessions');

        // Check if we need to fix sumpenalty
        if (array_key_exists('sumpenalty', $columns) && empty($columns['sumpenalty']->not_null)) {
            // sumpenalty should be NOT NULL DEFAULT '0.0000000',
            // Fixed in earlier upgrade code
            $field = new xmldb_field('sumpenalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'newgraded');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_main_savepoint(true, 2011011410);
    }

    if ($oldversion < 2011011411) {
        // Fix question_states in the post table after upgrade from 1.9
        $columns = $DB->get_columns('question_states');
        $table = new xmldb_table('question_states');

        // Check if we need to fix grade
        if (array_key_exists('grade', $columns) && empty($columns['grade']->not_null)) {
            // grade should be NOT NULL DEFAULT '0.0000000',
            // Fixed in earlier upgrade code
            $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'event');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        // Check if we need to fix raw_grade
        if (array_key_exists('raw_grade', $columns) && empty($columns['raw_grade']->not_null)) {
            // raw_grade should be NOT NULL DEFAULT '0.0000000',
            // Fixed in earlier upgrade code
            $field = new xmldb_field('raw_grade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'grade');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        // Check if we need to fix raw_grade
        if (array_key_exists('penalty', $columns) && empty($columns['penalty']->not_null)) {
            // penalty should be NOT NULL DEFAULT '0.0000000',
            // Fixed in earlier upgrade code
            $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'raw_grade');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_main_savepoint(true, 2011011411);
    }

    if ($oldversion < 2011011412) {
        // Fix tag_instance in the post table after upgrade from 1.9
        $columns = $DB->get_columns('tag_instance');
        $table = new xmldb_table('tag_instance');

        // Check if we need to fix tiuserid
        if (array_key_exists('tiuserid', $columns) && !empty($columns['tiuserid']->has_default)) {
            // tiuserid should have no default
            // Fixed in earlier upgrade code
            $field = new xmldb_field('tiuserid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'itemid');
            $index = new xmldb_index('itemtype-itemid-tagid-tiuserid', XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid', 'tiuserid'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2011011412);
    }

    if ($oldversion < 2011011413) {
        // Fix user_info_field in the post table after upgrade from 1.9
        $table = new xmldb_table('user_info_field');

        // Missing field descriptionformat
        // Fixed in earlier upgrade code
        $field = new xmldb_field('descriptionformat', XMLDB_TYPE_INTEGER, 2, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2011011413);
    }

    if ($oldversion < 2011011414) {
        // Drop the adodb_logsql table if it exists... it was never actually used anyway.
        $table = new xmldb_table('adodb_logsql');

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_main_savepoint(true, 2011011414);
    }

    if ($oldversion < 2011011415) {
        //create the rating table indexes if required
        $table = new xmldb_table('rating');

        $index = new xmldb_index('itemid', XMLDB_INDEX_NOTUNIQUE, array('itemid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);

            $key = new xmldb_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
            $dbman->add_key($table, $key);

            $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $dbman->add_key($table, $key);
        }

        upgrade_main_savepoint(true, 2011011415);
    }

    if ($oldversion < 2011012400) {
        // Clean up the old progress tracked roles setting, no longer used (replaced by enrolment)
        unset_config('progresstrackedroles');
        upgrade_main_savepoint(true, 2011012400);
    }

    if ($oldversion < 2011012500) {
        $columns = $DB->get_columns('tag_instance');
        $table = new xmldb_table('tag_instance');

        // Drop and recreate index if tiuserid doesn't have default value
        if (array_key_exists('tiuserid', $columns) && empty($columns['tiuserid']->has_default)) {
            // Define index itemtype-itemid-tagid-tiuserid (unique) to be dropped form tag_instance
            $index = new xmldb_index('itemtype-itemid-tagid-tiuserid', XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid', 'tiuserid'));
            // Conditionally launch drop index itemtype-itemid-tagid-tiuserid
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            // Changing the default of field tiuserid on table tag_instance to 0
            $field = new xmldb_field('tiuserid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'itemid');

            // Launch change of default for field tiuserid
            $dbman->change_field_default($table, $field);

            $index = new xmldb_index('itemtype-itemid-tagid-tiuserid', XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid', 'tiuserid'));

            // Conditionally launch add index itemtype-itemid-tagid-tiuserid
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011012500);
    }

    if ($oldversion < 2011012501) {
        //add the index userfieldidx (not unique) to user_info_data
        $table = new xmldb_table('user_info_data');
        $index = new xmldb_index('userfieldidx', XMLDB_INDEX_NOTUNIQUE, array('userid', 'fieldid'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2011012501);
    }

    if ($oldversion < 2011020200.01) {

        // Define field targetversion to be added to upgrade_log
        $table = new xmldb_table('upgrade_log');
        $field = new xmldb_field('targetversion', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'version');

        // Conditionally launch add field targetversion
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011020200.01);
    }

    if ($oldversion < 2011020900.07) {
        $DB->delete_records('course_display', array('display' => 0));
        upgrade_main_savepoint(true, 2011020900.07);
    }

    if ($oldversion < 2011020900.08) {
         // Define field secret to be added to registration_hubs
        $table = new xmldb_table('registration_hubs');
        $field = new xmldb_field('secret', XMLDB_TYPE_CHAR, '255', null, null, null,
                null, 'confirmed');

        // Conditionally launch add field secret  and set its value
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $DB->set_field('registration_hubs', 'secret', $CFG->siteidentifier);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011020900.08);
    }

    if ($oldversion < 2011022100.01) {
        // hack alert: inject missing version of manual auth_plugin,
        //             we need to do it so that we may use upgrade.php there

        set_config('version', 2011022100, 'auth_manual');
        upgrade_main_savepoint(true, 2011022100.01);
    }

    if ($oldversion < 2011052300.00) {
        $table = new xmldb_table('rating');

        // Add the component field to the ratings table
        upgrade_set_timeout(60 * 20);
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, 'unknown', 'contextid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add the ratingarea field to the ratings table
        upgrade_set_timeout(60 * 20);
        $field = new xmldb_field('ratingarea', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'unknown', 'component');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2011052300.00);
    }

    if ($oldversion < 2011052300.01) {

        // Define index uniqueuserrating (unique) to be added to rating
        $table = new xmldb_table('rating');
        $index = new xmldb_index('uniqueuserrating', XMLDB_INDEX_NOTUNIQUE, array('component', 'ratingarea', 'contextid', 'itemid'));

        // Conditionally launch add index uniqueuserrating
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011052300.01);
    }

    if ($oldversion < 2011052300.02) {

        // Define index itemid (not unique) to be dropped form rating
        $table = new xmldb_table('rating');
        $index = new xmldb_index('itemid', XMLDB_INDEX_NOTUNIQUE, array('itemid'));

        // Conditionally launch drop index itemid
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011052300.02);
    }

    // Question engine 2 changes (14) start here
    if ($oldversion < 2011060300) {
        // Changing the default of field penalty on table question to 0.3333333
        $table = new xmldb_table('question');
        $field = new xmldb_field('penalty');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '12, 7', null,
                XMLDB_NOTNULL, null, '0.3333333');

        // Launch change of default for field penalty
        $dbman->change_field_default($table, $field);

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060300);
    }

    if ($oldversion < 2011060301) {

        // Rename field defaultgrade on table question to defaultmark
        $table = new xmldb_table('question');
        $field = new xmldb_field('defaultgrade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '12, 7', null,
                XMLDB_NOTNULL, null, '1');

        // Launch rename field defaultmark
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'defaultmark');
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060301);
    }

    if ($oldversion < 2011060302) {

        // Rename the question_attempts table to question_usages.
        $table = new xmldb_table('question_attempts');
        if (!$dbman->table_exists('question_usages')) {
            $dbman->rename_table($table, 'question_usages');
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060302);
    }

    if ($oldversion < 2011060303) {

        // Rename the modulename field to component ...
        $table = new xmldb_table('question_usages');
        $field = new xmldb_field('modulename');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null,
                XMLDB_NOTNULL, null, null, 'contextid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'component');
        }

        // ... and update its contents.
        $DB->set_field('question_usages', 'component', 'mod_quiz', array('component' => 'quiz'));

        // Add the contextid field.
        $field = new xmldb_field('contextid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // And populate it.
            $quizmoduleid = $DB->get_field('modules', 'id', array('name' => 'quiz'));
            $DB->execute("
                UPDATE {question_usages} SET contextid = (
                    SELECT ctx.id
                    FROM {context} ctx
                    JOIN {course_modules} cm ON cm.id = ctx.instanceid AND cm.module = $quizmoduleid
                    JOIN {quiz_attempts} quiza ON quiza.quiz = cm.instance
                    WHERE ctx.contextlevel = " . CONTEXT_MODULE . "
                    AND quiza.uniqueid = {question_usages}.id
                )
            ");

            // It seems that it is possible, in old versions of Moodle, for a
            // quiz_attempt to be deleted while the question_attempt remains.
            // In that situation we still get NULLs left in the table, which
            // causes the upgrade to break at the next step. To avoid breakage,
            // without risking dataloss, we just replace all NULLs with 0 here.
            $DB->set_field_select('question_usages', 'contextid', 0, 'contextid IS NULL');

            // Then make it NOT NULL.
            $field = new xmldb_field('contextid');
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null, 'id');
            $dbman->change_field_notnull($table, $field);
        }

        // Add the preferredbehaviour column. Populate it with a dummy value
        // for now. We will fill in the appropriate behaviour name when
        // updating all the rest of the attempt data.
        $field = new xmldb_field('preferredbehaviour');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '32', null,
                    XMLDB_NOTNULL, null, 'to_be_set_later', 'component');
            $dbman->add_field($table, $field);

            // Then remove the default value, now the column is populated.
            $field = new xmldb_field('preferredbehaviour');
            $field->set_attributes(XMLDB_TYPE_CHAR, '32', null,
                    XMLDB_NOTNULL, null, null, 'component');
            $dbman->change_field_default($table, $field);
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060303);
    }

    if ($oldversion < 2011060304) {

        // Define key contextid (foreign) to be added to question_usages
        $table = new xmldb_table('question_usages');
        $key = new XMLDBKey('contextid');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

        // Launch add key contextid
        $dbman->add_key($table, $key);

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060304);
    }

    if ($oldversion < 2011060305) {

        // Changing precision of field component on table question_usages to (255)
        // This was missed during the upgrade from old versions.
        $table = new xmldb_table('question_usages');
        $field = new xmldb_field('component');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null,
                XMLDB_NOTNULL, null, null, 'contextid');

        // Launch change of precision for field component
        $dbman->change_field_precision($table, $field);

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060305);
    }

    if ($oldversion < 2011060306) {

        // Define table question_attempts to be created
        $table = new xmldb_table('question_attempts');
        if (!$dbman->table_exists($table)) {

            // Adding fields to table question_attempts
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('questionusageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('slot', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('behaviour', XMLDB_TYPE_CHAR, '32', null,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('maxmark', XMLDB_TYPE_NUMBER, '12, 7', null,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('minfraction', XMLDB_TYPE_NUMBER, '12, 7', null,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('flagged', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, '0');
            $table->add_field('questionsummary', XMLDB_TYPE_TEXT, 'small', null,
                    null, null, null);
            $table->add_field('rightanswer', XMLDB_TYPE_TEXT, 'small', null,
                    null, null, null);
            $table->add_field('responsesummary', XMLDB_TYPE_TEXT, 'small', null,
                    null, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);

            // Adding keys to table question_attempts
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'),
                    'question', array('id'));
            $table->add_key('questionusageid', XMLDB_KEY_FOREIGN, array('questionusageid'),
                    'question_usages', array('id'));

            // Adding indexes to table question_attempts
            $table->add_index('questionusageid-slot', XMLDB_INDEX_UNIQUE,
                    array('questionusageid', 'slot'));

            // Launch create table for question_attempts
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060306);
    }

    if ($oldversion < 2011060307) {

        // Define table question_attempt_steps to be created
        $table = new xmldb_table('question_attempt_steps');
        if (!$dbman->table_exists($table)) {

            // Adding fields to table question_attempt_steps
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('questionattemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('sequencenumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('state', XMLDB_TYPE_CHAR, '13', null,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('fraction', XMLDB_TYPE_NUMBER, '12, 7', null,
                    null, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    null, null, null);

            // Adding keys to table question_attempt_steps
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('questionattemptid', XMLDB_KEY_FOREIGN,
                    array('questionattemptid'), 'question_attempts_new', array('id'));
            $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'),
                    'user', array('id'));

            // Adding indexes to table question_attempt_steps
            $table->add_index('questionattemptid-sequencenumber', XMLDB_INDEX_UNIQUE,
                    array('questionattemptid', 'sequencenumber'));

            // Launch create table for question_attempt_steps
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060307);
    }

    if ($oldversion < 2011060308) {

        // Define table question_attempt_step_data to be created
        $table = new xmldb_table('question_attempt_step_data');
        if (!$dbman->table_exists($table)) {

            // Adding fields to table question_attempt_step_data
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('attemptstepid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('name', XMLDB_TYPE_CHAR, '32', null,
                    XMLDB_NOTNULL, null, null);
            $table->add_field('value', XMLDB_TYPE_TEXT, 'small', null,
                    null, null, null);

            // Adding keys to table question_attempt_step_data
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('attemptstepid', XMLDB_KEY_FOREIGN, array('attemptstepid'),
                    'question_attempt_steps', array('id'));

            // Adding indexes to table question_attempt_step_data
            $table->add_index('attemptstepid-name', XMLDB_INDEX_UNIQUE,
                    array('attemptstepid', 'name'));

            // Launch create table for question_attempt_step_data
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060308);
    }

    if ($oldversion < 2011060309) {

        // Define table question_hints to be created
        $table = new xmldb_table('question_hints');

        // Adding fields to table question_hints
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('hint', XMLDB_TYPE_TEXT, 'small', null,
                XMLDB_NOTNULL, null, null);
        $table->add_field('hintformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('shownumcorrect', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('clearwrong', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('options', XMLDB_TYPE_CHAR, '255', null,
                null, null, null);

        // Adding keys to table question_hints
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'),
                'question', array('id'));

        // Conditionally launch create table for question_hints
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060309);
    }

    if ($oldversion < 2011060310) {

        // In the past, question_answer fractions were stored with rather
        // sloppy rounding. Now update them to the new standard of 7 d.p.
        $changes = array(
            '-0.66666'  => '-0.6666667',
            '-0.33333'  => '-0.3333333',
            '-0.16666'  => '-0.1666667',
            '-0.142857' => '-0.1428571',
             '0.11111'  =>  '0.1111111',
             '0.142857' =>  '0.1428571',
             '0.16666'  =>  '0.1666667',
             '0.33333'  =>  '0.3333333',
             '0.333333' =>  '0.3333333',
             '0.66666'  =>  '0.6666667',
        );
        foreach ($changes as $from => $to) {
            $DB->set_field('question_answers',
                    'fraction', $to, array('fraction' => $from));
        }

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060310);
    }

    if ($oldversion < 2011060311) {

        // In the past, question penalties were stored with rather
        // sloppy rounding. Now update them to the new standard of 7 d.p.
        $DB->set_field('question',
                'penalty', 0.3333333, array('penalty' => 33.3));
        $DB->set_field_select('question',
                'penalty', 0.3333333, 'penalty >= 0.33 AND penalty <= 0.34');
        $DB->set_field_select('question',
                'penalty', 0.6666667, 'penalty >= 0.66 AND penalty <= 0.67');
        $DB->set_field_select('question',
                'penalty', 1, 'penalty > 1');

        // quiz savepoint reached
        upgrade_main_savepoint(true, 2011060311);
    }

    if ($oldversion < 2011060312) {

        // Define field hintformat to be added to question_hints table.
        $table = new xmldb_table('question_hints');
        $field = new xmldb_field('hintformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');

        // Conditionally launch add field partiallycorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2011060312);
    }

    if ($oldversion < 2011060313) {
        // Define field variant to be added to question_attempts
        $table = new xmldb_table('question_attempts');
        $field = new xmldb_field('variant', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, 1, 'questionid');

        // Launch add field component
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011060313);
    }
    // Question engine 2 changes (14) end here

    if ($oldversion < 2011060500) {

         // Define index uniqueuserrating (not unique) to be dropped from rating
        $table = new xmldb_table('rating');
        $index = new xmldb_index('uniqueuserrating', XMLDB_INDEX_NOTUNIQUE,
                         array('component', 'ratingarea', 'contextid', 'itemid'));

        // Drop dependent index before changing fields specs
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Changing the default of field component on table rating to drop it
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'contextid');

        // Launch change of default for field component
        $dbman->change_field_default($table, $field);

        // Changing the default of field ratingarea on table rating to drop it
        $field = new xmldb_field('ratingarea', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'component');

        // Launch change of default for field ratingarea
        $dbman->change_field_default($table, $field);

        // Add dependent index back
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011060500);
    }

    if ($oldversion < 2011060800) {
        // Add enabled field to message_processors
        $table = new xmldb_table('message_processors');
        $field = new xmldb_field('enabled');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'name');

        // Launch add field addition
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        // Populate default messaging settings
        upgrade_populate_default_messaging_prefs();

        upgrade_main_savepoint(true, 2011060800);
    }

    if ($oldversion < 2011060800.01) { //TODO: put the right latest version
        // Define field shortname to be added to external_services
        $table = new xmldb_table('external_services');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'timemodified');

        // Conditionally launch add field shortname
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011060800.01);
    }

    if ($oldversion < 2011062000.01) {
        // Changing sign of field minfraction on table question_attempts to signed
        $table = new xmldb_table('question_attempts');
        $field = new xmldb_field('minfraction', XMLDB_TYPE_NUMBER, '12, 7', null,
                XMLDB_NOTNULL, null, null, 'maxmark');

        // Launch change of sign for field minfraction
        $dbman->change_field_unsigned($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011062000.01);
    }

    // Signed fixes - MDL-28032
    if ($oldversion < 2011062400.02) {

        // Changing sign of field defaultmark on table question to unsigned
        $table = new xmldb_table('question');
        $field = new xmldb_field('defaultmark', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '1', 'generalfeedbackformat');

        // Launch change of sign for field defaultmark
        $dbman->change_field_unsigned($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011062400.02);
    }

    if ($oldversion < 2011062400.03) {
        // Completion system has issue in which possible duplicate rows are
        // added to the course_modules_completion table. This change deletes
        // the older version of duplicate rows and replaces an index with a
        // unique one so it won't happen again.

        // This would have been a single query but because MySQL is a PoS
        // and can't do subqueries in DELETE, I have made it into two. The
        // system is unlikely to run out of memory as only IDs are stored in
        // the array.

        // Find all rows cmc1 where there is another row cmc2 with the
        // same user id and the same coursemoduleid, but a higher id (=> newer,
        // meaning that cmc1 is an older row).
        $rs = $DB->get_recordset_sql("
SELECT DISTINCT
    cmc1.id
FROM
    {course_modules_completion} cmc1
    JOIN {course_modules_completion} cmc2
        ON cmc2.userid = cmc1.userid
        AND cmc2.coursemoduleid = cmc1.coursemoduleid
        AND cmc2.id > cmc1.id");
        $deleteids = array();
        foreach ($rs as $row) {
            $deleteids[] = $row->id;
        }
        $rs->close();
        // Note: SELECT part performance tested on table with ~7m
        // rows of which ~15k match, only took 30 seconds so probably okay.

        // Delete all those rows
        $DB->delete_records_list('course_modules_completion', 'id', $deleteids);

        // Define index userid (not unique) to be dropped form course_modules_completion
        $table = new xmldb_table('course_modules_completion');
        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch drop index userid
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index userid-coursemoduleid (unique) to be added to course_modules_completion
        $index = new xmldb_index('userid-coursemoduleid', XMLDB_INDEX_UNIQUE,
                array('userid', 'coursemoduleid'));

        // Conditionally launch add index userid-coursemoduleid
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2011062400.03);
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2011070101.04) {
        // Remove category_sortorder index that was supposed to be removed long time ago
        $table = new xmldb_table('course');
        $index = new xmldb_index('category_sortorder', XMLDB_INDEX_UNIQUE, array('category', 'sortorder'));

        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        upgrade_main_savepoint(true, 2011070101.04);
    }

    if ($oldversion < 2011070101.09) {
        // Changing the default of field secret on table registration_hubs to NULL
        $table = new xmldb_table('registration_hubs');
        $field = new xmldb_field('secret', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'confirmed');

        // Launch change of default for field secret
        $dbman->change_field_default($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011070101.09);
    }

    if ($oldversion < 2011070101.10) {
        //preference not required since 2.0
        $DB->delete_records('user_preferences', array('name'=>'message_showmessagewindow'));

        //re-introducing emailstop. check that its turned off so people dont suddenly stop getting notifications
        $DB->set_field('user', 'emailstop', 0, array('emailstop' => 1));

        upgrade_main_savepoint(true, 2011070101.10);
    }

    return true;
}

