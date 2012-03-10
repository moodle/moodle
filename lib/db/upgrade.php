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

    if ($oldversion < 2011120500) {
        // just in case somebody hacks upgrade scripts or env, we really can not continue
        echo("You need to upgrade to 2.2.x first!\n");
        exit(1);
        // Note this savepoint is 100% unreachable, but needed to pass the upgrade checks
        upgrade_main_savepoint(true, 2011120500);
    }

    ////////////////////////////////////////
    ///upgrade supported only from 2.2.x ///
    ////////////////////////////////////////

    if ($oldversion < 2011120500.02) {

        upgrade_set_timeout(60*20); // This may take a while
        // MDL-28180. Some missing restrictions in certain backup & restore operations
        // were causing incorrect duplicates in the course_completion_aggr_methd table.
        // This upgrade step takes rid of them.
        $sql = 'SELECT course, criteriatype, MIN(id) AS minid
                  FROM {course_completion_aggr_methd}
              GROUP BY course, criteriatype
                HAVING COUNT(*) > 1';
        $duprs = $DB->get_recordset_sql($sql);
        foreach ($duprs as $duprec) {
            // We need to handle NULLs in criteriatype diferently
            if (is_null($duprec->criteriatype)) {
                $where = 'course = ? AND criteriatype IS NULL AND id > ?';
                $params = array($duprec->course, $duprec->minid);
            } else {
                $where = 'course = ? AND criteriatype = ? AND id > ?';
                $params = array($duprec->course, $duprec->criteriatype, $duprec->minid);
            }
            $DB->delete_records_select('course_completion_aggr_methd', $where, $params);
        }
        $duprs->close();

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011120500.02);
    }

    if ($oldversion < 2011120500.03) {

        // Changing precision of field value on table user_preferences to (1333)
        $table = new xmldb_table('user_preferences');
        $field = new xmldb_field('value', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'name');

        // Launch change of precision for field value
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2011120500.03);
    }

    if ($oldversion < 2012020200.03) {

        // Define index rolecontext (not unique) to be added to role_assignments
        $table = new xmldb_table('role_assignments');
        $index = new xmldb_index('rolecontext', XMLDB_INDEX_NOTUNIQUE, array('roleid', 'contextid'));

        // Conditionally launch add index rolecontext
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index usercontextrole (not unique) to be added to role_assignments
        $index = new xmldb_index('usercontextrole', XMLDB_INDEX_NOTUNIQUE, array('userid', 'contextid', 'roleid'));

        // Conditionally launch add index usercontextrole
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012020200.03);
    }

    if ($oldversion < 2012020200.06) {
        // Previously we always allowed users to override their email address via the messaging system
        // We have now added a setting to allow admins to turn this this ability on and off
        // While this setting defaults to 0 (off) we're setting it to 1 (on) to maintain the behaviour for upgrading sites
        set_config('messagingallowemailoverride', 1);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012020200.06);
    }

    if ($oldversion < 2012021700.01) {
        // Changing precision of field uniquehash on table post to 255
        $table = new xmldb_table('post');
        $field = new xmldb_field('uniquehash', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'content');

        // Launch change of precision for field uniquehash
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012021700.01);
    }

    if ($oldversion < 2012021700.02) {
        // Somewhere before 1.9 summary and content column in post table were not null. In 1.9+
        // not null became false.
        $columns = $DB->get_columns('post');

        // Fix discrepancies in summary field after upgrade from 1.9
        if (array_key_exists('summary', $columns) && $columns['summary']->not_null != false) {
            $table = new xmldb_table('post');
            $summaryfield = new xmldb_field('summary', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'subject');

            if ($dbman->field_exists($table, $summaryfield)) {
                $dbman->change_field_notnull($table, $summaryfield);
            }

        }

        // Fix discrepancies in content field after upgrade from 1.9
        if (array_key_exists('content', $columns) && $columns['content']->not_null != false) {
            $table = new xmldb_table('post');
            $contentfield = new xmldb_field('content', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'summary');

            if ($dbman->field_exists($table, $contentfield)) {
                $dbman->change_field_notnull($table, $contentfield);
            }

        }

        upgrade_main_savepoint(true, 2012021700.02);
    }

    // The ability to backup user (private) files is out completely - MDL-29248
    if ($oldversion < 2012030100.01) {
        unset_config('backup_general_user_files', 'backup');
        unset_config('backup_general_user_files_locked', 'backup');
        unset_config('backup_auto_user_files', 'backup');

        upgrade_main_savepoint(true, 2012030100.01);
    }

    if ($oldversion < 2012030100.02) {
        // migrate all numbers to signed - it should be safe to interrupt this and continue later
        upgrade_mysql_fix_unsigned_columns();

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012030100.02);
    }


    return true;
}

