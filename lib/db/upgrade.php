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
    global $CFG, $USER, $DB, $OUTPUT, $SITE;

    require_once($CFG->libdir.'/db/upgradelib.php'); // Core Upgrade-related functions

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    if ($oldversion < 2011120500) {
        // just in case somebody hacks upgrade scripts or env, we really can not continue
        echo("You need to upgrade to 2.2.x first!\n");
        exit(1);
        // Note this savepoint is 100% unreachable, but needed to pass the upgrade checks
        upgrade_main_savepoint(true, 2011120500);
    }

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

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

    if ($oldversion < 2012030900.01) {
        // Migrate all numbers to signed & all texts and binaries to big size.
        // It should be safe to interrupt this and continue later.
        upgrade_mysql_fix_unsigned_and_lob_columns();

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012030900.01);
    }

    if ($oldversion < 2012031500.01) {
        // Upgrade old course_allowed_modules data to be permission overrides.
        if ($CFG->restrictmodulesfor === 'all') {
            $courses = $DB->get_records_menu('course', array(), 'id', 'id, 1');
        } else if ($CFG->restrictmodulesfor === 'requested') {
            $courses = $DB->get_records_menu('course', array('restrictmodules' => 1), 'id', 'id, 1');
        } else {
            $courses = array();
        }

        if (!$dbman->table_exists('course_allowed_modules')) {
            // Upgrade must already have been run on this server. This might happen,
            // for example, during development of these changes.
            $courses = array();
        }

        $modidtoname = $DB->get_records_menu('modules', array(), 'id', 'id, name');

        $coursecount = count($courses);
        if ($coursecount) {
            $pbar = new progress_bar('allowedmods', 500, true);
            $transaction = $DB->start_delegated_transaction();
        }

        $i = 0;
        foreach ($courses as $courseid => $notused) {
            $i += 1;
            upgrade_set_timeout(60); // 1 minute per course should be fine.

            $allowedmoduleids = $DB->get_records_menu('course_allowed_modules',
            array('course' => $courseid), 'module', 'module, 1');
            if (empty($allowedmoduleids)) {
                // This seems to be the best match for backwards compatibility,
                // not necessarily with the old code in course_allowed_module function,
                // but with the code that used to be in the coures settings form.
                $allowedmoduleids = explode(',', $CFG->defaultallowedmodules);
                $allowedmoduleids = array_combine($allowedmoduleids, $allowedmoduleids);
            }

            $context = context_course::instance($courseid);

            list($roleids) = get_roles_with_cap_in_context($context, 'moodle/course:manageactivities');
            list($managerroleids) = get_roles_with_cap_in_context($context, 'moodle/site:config');
            foreach ($managerroleids as $roleid) {
                unset($roleids[$roleid]);
            }

            foreach ($modidtoname as $modid => $modname) {
                if (isset($allowedmoduleids[$modid])) {
                    // Module is allowed, no worries.
                    continue;
                }

                $capability = 'mod/' . $modname . ':addinstance';
                foreach ($roleids as $roleid) {
                    assign_capability($capability, CAP_PREVENT, $roleid, $context);
                }
            }

            $pbar->update($i, $coursecount, "Upgrading legacy course_allowed_modules data - $i/$coursecount.");
        }

        if ($coursecount) {
            $transaction->allow_commit();
        }

        upgrade_main_savepoint(true, 2012031500.01);
    }

    if ($oldversion < 2012031500.02) {

        // Define field restrictmodules to be dropped from course
        $table = new xmldb_table('course');
        $field = new xmldb_field('restrictmodules');

        // Conditionally launch drop field requested
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_main_savepoint(true, 2012031500.02);
    }

    if ($oldversion < 2012031500.03) {

        // Define table course_allowed_modules to be dropped
        $table = new xmldb_table('course_allowed_modules');

        // Conditionally launch drop table for course_allowed_modules
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_main_savepoint(true, 2012031500.03);
    }

    if ($oldversion < 2012031500.04) {
        // Clean up the old admin settings.
        unset_config('restrictmodulesfor');
        unset_config('restrictbydefault');
        unset_config('defaultallowedmodules');

        upgrade_main_savepoint(true, 2012031500.04);
    }

    if ($oldversion < 2012032300.02) {
        // Migrate the old admin debug setting.
        if ($CFG->debug == 38911) {
            set_config('debug', DEBUG_DEVELOPER);
        } else if ($CFG->debug == 6143) {
            set_config('debug', DEBUG_ALL);
        }
        upgrade_main_savepoint(true, 2012032300.02);
    }

    if ($oldversion < 2012042300.00) {
        // This change makes the course_section index unique.

        // xmldb does not allow changing index uniqueness - instead we must drop
        // index then add it again
        $table = new xmldb_table('course_sections');
        $index = new xmldb_index('course_section', XMLDB_INDEX_NOTUNIQUE, array('course', 'section'));

        // Conditionally launch drop index course_section
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Look for any duplicate course_sections entries. There should not be
        // any but on some busy systems we found a few, maybe due to previous
        // bugs.
        $transaction = $DB->start_delegated_transaction();
        $rs = $DB->get_recordset_sql('
                SELECT DISTINCT
                    cs.id, cs.course
                FROM
                    {course_sections} cs
                    INNER JOIN {course_sections} older
                        ON cs.course = older.course AND cs.section = older.section
                        AND older.id < cs.id');
        foreach ($rs as $rec) {
            $DB->delete_records('course_sections', array('id' => $rec->id));
            // We can't use rebuild_course_cache() here because introducing sectioncache later
            // so reset modinfo manually.
            $DB->set_field('course', 'modinfo', null, array('id' => $rec->course));
        }
        $rs->close();
        $transaction->allow_commit();

        // Define index course_section (unique) to be added to course_sections
        $index = new xmldb_index('course_section', XMLDB_INDEX_UNIQUE, array('course', 'section'));

        // Conditionally launch add index course_section
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012042300.00);
    }

    if ($oldversion < 2012042300.02) {
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria.php');
        // Delete orphaned criteria which were left when modules were removed
        if ($DB->get_dbfamily() === 'mysql') {
            $sql = "DELETE cc FROM {course_completion_criteria} cc
                    LEFT JOIN {course_modules} cm ON cm.id = cc.moduleinstance
                    WHERE cm.id IS NULL AND cc.criteriatype = ".COMPLETION_CRITERIA_TYPE_ACTIVITY;
        } else {
            $sql = "DELETE FROM {course_completion_criteria}
                    WHERE NOT EXISTS (
                        SELECT 'x' FROM {course_modules}
                        WHERE {course_modules}.id = {course_completion_criteria}.moduleinstance)
                    AND {course_completion_criteria}.criteriatype = ".COMPLETION_CRITERIA_TYPE_ACTIVITY;
        }
        $DB->execute($sql);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012042300.02);
    }

    if ($oldversion < 2012050300.01) {
        // Make sure deleted users do not have picture flag.
        $DB->set_field('user', 'picture', 0, array('deleted'=>1, 'picture'=>1));
        upgrade_main_savepoint(true, 2012050300.01);
    }

    if ($oldversion < 2012050300.02) {

        // Changing precision of field picture on table user to (10)
        $table = new xmldb_table('user');
        $field = new xmldb_field('picture', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'secret');

        // Launch change of precision for field picture
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012050300.02);
    }

    if ($oldversion < 2012050300.03) {

        // Define field coursedisplay to be added to course
        $table = new xmldb_table('course');
        $field = new xmldb_field('coursedisplay', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'completionnotify');

        // Conditionally launch add field coursedisplay
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012050300.03);
    }

    if ($oldversion < 2012050300.04) {

        // Define table course_display to be dropped
        $table = new xmldb_table('course_display');

        // Conditionally launch drop table for course_display
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012050300.04);
    }

    if ($oldversion < 2012050300.05) {

        // Clean up removed admin setting.
        unset_config('navlinkcoursesections');

        upgrade_main_savepoint(true, 2012050300.05);
    }

    if ($oldversion < 2012050400.01) {

        // Define index sortorder (not unique) to be added to course
        $table = new xmldb_table('course');
        $index = new xmldb_index('sortorder', XMLDB_INDEX_NOTUNIQUE, array('sortorder'));

        // Conditionally launch add index sortorder
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012050400.01);
    }

    if ($oldversion < 2012050400.02) {

        // Clean up removed admin setting.
        unset_config('enablecourseajax');

        upgrade_main_savepoint(true, 2012050400.02);
    }

    if ($oldversion < 2012051100.01) {

        // Define field idnumber to be added to groups
        $table = new xmldb_table('groups');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'courseid');
        $index = new xmldb_index('idnumber', XMLDB_INDEX_NOTUNIQUE, array('idnumber'));

        // Conditionally launch add field idnumber
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally launch add index idnumber
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field idnumber to be added to groupings
        $table = new xmldb_table('groupings');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'name');
        $index = new xmldb_index('idnumber', XMLDB_INDEX_NOTUNIQUE, array('idnumber'));

        // Conditionally launch add field idnumber
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally launch add index idnumber
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012051100.01);
    }

    if ($oldversion < 2012051100.03) {

        // Amend course table to add sectioncache cache
        $table = new xmldb_table('course');
        $field = new xmldb_field('sectioncache', XMLDB_TYPE_TEXT, null, null, null, null, null, 'showgrades');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Amend course_sections to add date, time and groupingid availability
        // conditions and a setting about whether to show them
        $table = new xmldb_table('course_sections');
        $field = new xmldb_field('availablefrom', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'visible');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('availableuntil', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'availablefrom');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('showavailability', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'availableuntil');
        // Conditionally launch add field showavailability
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('groupingid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'showavailability');
        // Conditionally launch add field groupingid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add course_sections_availability to add completion & grade availability conditions
        $table = new xmldb_table('course_sections_availability');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('coursesectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sourcecmid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('requiredcompletion', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('gradeitemid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->add_field('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('coursesectionid', XMLDB_KEY_FOREIGN, array('coursesectionid'), 'course_sections', array('id'));
        $table->add_key('sourcecmid', XMLDB_KEY_FOREIGN, array('sourcecmid'), 'course_modules', array('id'));
        $table->add_key('gradeitemid', XMLDB_KEY_FOREIGN, array('gradeitemid'), 'grade_items', array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012051100.03);
    }

    if ($oldversion < 2012052100.00) {

        // Define field referencefileid to be added to files.
        $table = new xmldb_table('files');

        // Define field referencefileid to be added to files.
        $field = new xmldb_field('referencefileid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'sortorder');

        // Conditionally launch add field referencefileid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field referencelastsync to be added to files.
        $field = new xmldb_field('referencelastsync', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'referencefileid');

        // Conditionally launch add field referencelastsync.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field referencelifetime to be added to files.
        $field = new xmldb_field('referencelifetime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'referencelastsync');

        // Conditionally launch add field referencelifetime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $key = new xmldb_key('referencefileid', XMLDB_KEY_FOREIGN, array('referencefileid'), 'files_reference', array('id'));
        // Launch add key referencefileid
        $dbman->add_key($table, $key);

        // Define table files_reference to be created.
        $table = new xmldb_table('files_reference');

        // Adding fields to table files_reference.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('repositoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastsync', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('lifetime', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('reference', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table files_reference.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('repositoryid', XMLDB_KEY_FOREIGN, array('repositoryid'), 'repository_instances', array('id'));

        // Conditionally launch create table for files_reference
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052100.00);
    }

    if ($oldversion < 2012052500.03) { // fix invalid course_completion_records MDL-27368
        //first get all instances of duplicate records
        $sql = 'SELECT userid, course FROM {course_completions} WHERE (deleted IS NULL OR deleted <> 1) GROUP BY userid, course HAVING (count(id) > 1)';
        $duplicates = $DB->get_recordset_sql($sql, array());

        foreach ($duplicates as $duplicate) {
            $pointer = 0;
            //now get all the records for this user/course
            $sql = 'userid = ? AND course = ? AND (deleted IS NULL OR deleted <> 1)';
            $completions = $DB->get_records_select('course_completions', $sql,
                array($duplicate->userid, $duplicate->course), 'timecompleted DESC, timestarted DESC');
            $needsupdate = false;
            $origcompletion = null;
            foreach ($completions as $completion) {
                $pointer++;
                if ($pointer === 1) { //keep 1st record but delete all others.
                    $origcompletion = $completion;
                } else {
                    //we need to keep the "oldest" of all these fields as the valid completion record.
                    $fieldstocheck = array('timecompleted', 'timestarted', 'timeenrolled');
                    foreach ($fieldstocheck as $f) {
                        if ($origcompletion->$f > $completion->$f) {
                            $origcompletion->$f = $completion->$f;
                            $needsupdate = true;
                        }
                    }
                    $DB->delete_records('course_completions', array('id'=>$completion->id));
                }
            }
            if ($needsupdate) {
                $DB->update_record('course_completions', $origcompletion);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052500.03);
    }

    if ($oldversion < 2012052900.00) {
        // Clean up all duplicate records in the course_completions table in preparation
        // for adding a new index there.
        upgrade_course_completion_remove_duplicates(
            'course_completions',
            array('userid', 'course'),
            array('timecompleted', 'timestarted', 'timeenrolled')
        );

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052900.00);
    }

    if ($oldversion < 2012052900.01) {
        // Add indexes to prevent new duplicates in the course_completions table.
        // Define index useridcourse (unique) to be added to course_completions
        $table = new xmldb_table('course_completions');
        $index = new xmldb_index('useridcourse', XMLDB_INDEX_UNIQUE, array('userid', 'course'));

        // Conditionally launch add index useridcourse
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052900.01);
    }

    if ($oldversion < 2012052900.02) {
        // Clean up all duplicate records in the course_completion_crit_compl table in preparation
        // for adding a new index there.
        upgrade_course_completion_remove_duplicates(
            'course_completion_crit_compl',
            array('userid', 'course', 'criteriaid'),
            array('timecompleted')
        );

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052900.02);
    }

    if ($oldversion < 2012052900.03) {
        // Add indexes to prevent new duplicates in the course_completion_crit_compl table.
        // Define index useridcoursecriteraid (unique) to be added to course_completion_crit_compl
        $table = new xmldb_table('course_completion_crit_compl');
        $index = new xmldb_index('useridcoursecriteraid', XMLDB_INDEX_UNIQUE, array('userid', 'course', 'criteriaid'));

        // Conditionally launch add index useridcoursecriteraid
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052900.03);
    }

    if ($oldversion < 2012052900.04) {
        // Clean up all duplicate records in the course_completion_aggr_methd table in preparation
        // for adding a new index there.
        upgrade_course_completion_remove_duplicates(
            'course_completion_aggr_methd',
            array('course', 'criteriatype')
        );

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052900.04);
    }

    if ($oldversion < 2012052900.05) {
        // Add indexes to prevent new duplicates in the course_completion_aggr_methd table.
        // Define index coursecriteratype (unique) to be added to course_completion_aggr_methd
        $table = new xmldb_table('course_completion_aggr_methd');
        $index = new xmldb_index('coursecriteriatype', XMLDB_INDEX_UNIQUE, array('course', 'criteriatype'));

        // Conditionally launch add index coursecriteratype
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012052900.05);
    }

    if ($oldversion < 2012060600.01) {
        // Add field referencehash to files_reference
        $table = new xmldb_table('files_reference');
        $field = new xmldb_field('referencehash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'reference');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_main_savepoint(true, 2012060600.01);
    }

    if ($oldversion < 2012060600.02) {
        // Populate referencehash field with SHA1 hash of the reference - this shoudl affect only 2.3dev sites
        // that were using the feature for testing. Production sites have the table empty.
        $rs = $DB->get_recordset('files_reference', null, '', 'id, reference');
        foreach ($rs as $record) {
            $hash = sha1($record->reference);
            $DB->set_field('files_reference', 'referencehash', $hash, array('id' => $record->id));
        }
        $rs->close();

        upgrade_main_savepoint(true, 2012060600.02);
    }

    if ($oldversion < 2012060600.03) {
        // Merge duplicate records in files_reference that were created during the development
        // phase at 2.3dev sites. This is needed so we can create the unique index over
        // (repositoryid, referencehash) fields.
        $sql = "SELECT repositoryid, referencehash, MIN(id) AS minid
                  FROM {files_reference}
              GROUP BY repositoryid, referencehash
                HAVING COUNT(*) > 1";
        $duprs = $DB->get_recordset_sql($sql);
        foreach ($duprs as $duprec) {
            // get the list of all ids in {files_reference} that need to be remapped
            $dupids = $DB->get_records_select('files_reference', "repositoryid = ? AND referencehash = ? AND id > ?",
                array($duprec->repositoryid, $duprec->referencehash, $duprec->minid), '', 'id');
            $dupids = array_keys($dupids);
            // relink records in {files} that are now referring to a duplicate record
            // in {files_reference} to refer to the first one
            list($subsql, $subparams) = $DB->get_in_or_equal($dupids);
            $DB->set_field_select('files', 'referencefileid', $duprec->minid, "referencefileid $subsql", $subparams);
            // and finally remove all orphaned records from {files_reference}
            $DB->delete_records_list('files_reference', 'id', $dupids);
        }
        $duprs->close();

        upgrade_main_savepoint(true, 2012060600.03);
    }

    if ($oldversion < 2012060600.04) {
        // Add a unique index over repositoryid and referencehash fields in files_reference table
        $table = new xmldb_table('files_reference');
        $index = new xmldb_index('uq_external_file', XMLDB_INDEX_UNIQUE, array('repositoryid', 'referencehash'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_main_savepoint(true, 2012060600.04);
    }

    if ($oldversion < 2012061800.01) {

        // Define field screenreader to be dropped from user
        $table = new xmldb_table('user');
        $field = new xmldb_field('ajax');

        // Conditionally launch drop field screenreader
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012061800.01);
    }

    if ($oldversion < 2012062000.00) {
        // Add field newcontextid to backup_files_template
        $table = new xmldb_table('backup_files_template');
        $field = new xmldb_field('newcontextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'info');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2012062000.00);
    }

    if ($oldversion < 2012062000.01) {
        // Add field newitemid to backup_files_template
        $table = new xmldb_table('backup_files_template');
        $field = new xmldb_field('newitemid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'newcontextid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint(true, 2012062000.01);
    }


    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    if ($oldversion < 2012062500.02) {
        // Drop some old backup tables, not used anymore

        // Define table backup_files to be dropped
        $table = new xmldb_table('backup_files');

        // Conditionally launch drop table for backup_files
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table backup_ids to be dropped
        $table = new xmldb_table('backup_ids');

        // Conditionally launch drop table for backup_ids
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012062500.02);
    }

    if ($oldversion < 2012070600.04) {
        // Define table course_modules_avail_fields to be created
        $table = new xmldb_table('course_modules_avail_fields');

        // Adding fields to table course_modules_avail_fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userfield', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('customfieldid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('operator', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table course_modules_avail_fields
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('coursemoduleid', XMLDB_KEY_FOREIGN, array('coursemoduleid'), 'course_modules', array('id'));

        // Conditionally launch create table for course_modules_avail_fields
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.04);
    }

    if ($oldversion < 2012070600.05) {
        // Define table course_sections_avail_fields to be created
        $table = new xmldb_table('course_sections_avail_fields');

        // Adding fields to table course_sections_avail_fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('coursesectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userfield', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('customfieldid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('operator', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table course_sections_avail_fields
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('coursesectionid', XMLDB_KEY_FOREIGN, array('coursesectionid'), 'course_sections', array('id'));

        // Conditionally launch create table for course_sections_avail_fields
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.05);
    }

    if ($oldversion < 2012070600.06) {

        // Drop "deleted" fields
        $table = new xmldb_table('course_completions');
        $field = new xmldb_field('timenotified');
        $field = new xmldb_field('deleted');

        // Conditionally launch drop field deleted from course_completions
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('timenotified');
        // Conditionally launch drop field timenotified from course_completions
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.06);
    }

    if ($oldversion < 2012070600.07) {
        $table = new xmldb_table('course_completion_crit_compl');
        $field = new xmldb_field('deleted');

        // Conditionally launch drop field deleted from course_completion_crit_compl
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.07);
    }

    if ($oldversion < 2012070600.08) {

        // Drop unused table "course_completion_notify"
        $table = new xmldb_table('course_completion_notify');

        // Conditionally launch drop table course_completion_notify
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.08);
     }

    if ($oldversion < 2012070600.09) {

        // Define index path (not unique) to be added to context
        $table = new xmldb_table('context');
        $index = new xmldb_index('path', XMLDB_INDEX_NOTUNIQUE, array('path'), array('varchar_pattern_ops'));

        // Recreate index with new pattern hint
        if ($DB->get_dbfamily() === 'postgres') {
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.09);
    }

    if ($oldversion < 2012070600.10) {

        // Define index name (unique) to be dropped form role
        $table = new xmldb_table('role');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, array('name'));

        // Conditionally launch drop index name
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.10);
    }

    if ($oldversion < 2012070600.11) {

        // Define index component-itemid-userid (not unique) to be added to role_assignments
        $table = new xmldb_table('role_assignments');
        $index = new xmldb_index('component-itemid-userid', XMLDB_INDEX_NOTUNIQUE, array('component', 'itemid', 'userid'));

        // Conditionally launch add index component-itemid-userid
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012070600.11);
    }

    if ($oldversion < 2012071900.01) {
        // Cleanup after simpeltests tool
        capabilities_cleanup('tool_unittest');
        unset_all_config_for_plugin('tool_unittest');

        upgrade_main_savepoint(true, 2012071900.01);
    }

    if ($oldversion < 2012072400.00) {
        // Remove obsolete xhtml strict setting - use THEME->doctype in theme config if necessary,
        // see theme_config->doctype in lib/outputlib.php for more details.
        unset_config('xmlstrictheaders');
        upgrade_main_savepoint(true, 2012072400.00);
    }

    if ($oldversion < 2012072401.00) {

        // Saves orphaned questions from the Dark Side
        upgrade_save_orphaned_questions();

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012072401.00);
    }

    if ($oldversion < 2012072600.01) {
        // Handle events with empty eventtype //MDL-32827

        $DB->set_field('event', 'eventtype', 'site', array('eventtype' => '', 'courseid' => $SITE->id));
        $DB->set_field_select('event', 'eventtype', 'due', "eventtype = '' AND courseid != 0 AND groupid = 0 AND (modulename = 'assignment' OR modulename = 'assign')");
        $DB->set_field_select('event', 'eventtype', 'course', "eventtype = '' AND courseid != 0 AND groupid = 0");
        $DB->set_field_select('event', 'eventtype', 'group', "eventtype = '' AND groupid != 0");
        $DB->set_field_select('event', 'eventtype', 'user', "eventtype = '' AND userid != 0");

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012072600.01);
    }

    if ($oldversion < 2012080200.02) {
        // Drop obsolete question upgrade field that should have been added to the install.xml.
        $table = new xmldb_table('question');
        $field = new xmldb_field('oldquestiontextformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_main_savepoint(true, 2012080200.02);
    }

    if ($oldversion < 2012081400.01) {
        // Move the ability to disable blogs to its own setting MDL-25012.

        if (isset($CFG->bloglevel)) {
            // Only change settings if existing setting was set.
            if (empty($CFG->bloglevel)) {
                set_config('enableblogs', 0);
                // Now set the bloglevel to a valid setting as the disabled setting has been removed.
                // This prevents confusing results when users enable the blog system in future.
                set_config('bloglevel', BLOG_USER_LEVEL);
            } else {
                set_config('enableblogs', 1);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012081400.01);
    }

    if ($oldversion < 2012081600.01) {
        // Delete removed setting - Google Maps API V2 will not work in 2013.
        unset_config('googlemapkey');
        upgrade_main_savepoint(true, 2012081600.01);
    }

    if ($oldversion < 2012082300.01) {
        // Add more custom enrol fields.
        $table = new xmldb_table('enrol');
        $field = new xmldb_field('customint5', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'customint4');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customint6', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'customint5');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customint7', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'customint6');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customint8', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'customint7');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customchar3', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'customchar2');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customtext3', XMLDB_TYPE_TEXT, null, null, null, null, null, 'customtext2');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customtext4', XMLDB_TYPE_TEXT, null, null, null, null, null, 'customtext3');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012082300.01);
    }

    if ($oldversion < 2012082300.02) {
        // Define field component to be added to groups_members
        $table = new xmldb_table('groups_members');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'timeadded');

        // Conditionally launch add field component
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field itemid to be added to groups_members
        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'component');

        // Conditionally launch add field itemid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012082300.02);
    }

    if ($oldversion < 2012090500.00) {
        $subquery = 'SELECT b.id FROM {blog_external} b where b.id = ' . $DB->sql_cast_char2int('{post}.content', true);
        $sql = 'DELETE FROM {post}
                      WHERE {post}.module = \'blog_external\'
                            AND NOT EXISTS (' . $subquery . ')
                            AND ' . $DB->sql_isnotempty('post', 'uniquehash', false, false);
        $DB->execute($sql);
        upgrade_main_savepoint(true, 2012090500.00);
    }

    if ($oldversion < 2012090700.01) {
        // Add a category field in the course_request table
        $table = new xmldb_table('course_request');
        $field = new xmldb_field('category', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'summaryformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012090700.01);
    }

    if ($oldversion < 2012091700.00) {

        // Dropping screenreader field from user.
        $table = new xmldb_table('user');
        $field = new xmldb_field('screenreader');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012091700.00);
    }

    if ($oldversion < 2012092100.01) {
        // Some folders still have a sortorder set, which is used for main files but is not
        // supported by the folder resource. We reset the value here.
        $sql = 'UPDATE {files} SET sortorder = ? WHERE component = ? AND filearea = ? AND sortorder <> ?';
        $DB->execute($sql, array(0, 'mod_folder', 'content', 0));

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012092100.01);
    }

    if ($oldversion < 2012092600.00) {
        // Define index idname (unique) to be added to tag
        $table = new xmldb_table('tag');
        $index = new xmldb_index('idname', XMLDB_INDEX_UNIQUE, array('id', 'name'));

        // Conditionally launch add index idname
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012092600.00);
    }

    if ($oldversion < 2012101500.01) {
        // Find all orphaned blog associations that might exist.
        $sql = "SELECT ba.id
                  FROM {blog_association} ba
             LEFT JOIN {post} p
                    ON p.id = ba.blogid
                 WHERE p.id IS NULL";
        $orphanedrecordids = $DB->get_records_sql($sql);
        // Now delete these associations.
        foreach ($orphanedrecordids as $orphanedrecord) {
            $DB->delete_records('blog_association', array('id' => $orphanedrecord->id));
        }

        upgrade_main_savepoint(true, 2012101500.01);
    }

    if ($oldversion < 2012101800.02) {
        // Renaming backups using previous file naming convention.
        upgrade_rename_old_backup_files_using_shortname();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012101800.02);
    }

    if ($oldversion < 2012103001.00) {
        // create new event_subscriptions table
        $table = new xmldb_table('event_subscriptions');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('pollinterval', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('lastupdated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Main savepoint reached
        upgrade_main_savepoint(true, 2012103001.00);
    }

    if ($oldversion < 2012103002.00) {
        // Add subscription field to the event table
        $table = new xmldb_table('event');
        $field = new xmldb_field('subscriptionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');

        // Conditionally launch add field subscriptionid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_main_savepoint(true, 2012103002.00);
    }

    if ($oldversion < 2012103003.00) {
        // Fix uuid field in event table to match RFC-2445 UID property
        $table = new xmldb_table('event');
        $field = new xmldb_field('uuid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'visible');
        if ($dbman->field_exists($table, $field)) {
            // Changing precision of field uuid on table event to (255)
            $dbman->change_field_precision($table, $field);
        }
        // Main savepoint reached
        upgrade_main_savepoint(true, 2012103003.00);
    }

    if ($oldversion < 2012110200.00) {

        // Define table course_format_options to be created
        $table = new xmldb_table('course_format_options');

        // Adding fields to table course_format_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('format', XMLDB_TYPE_CHAR, '21', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'format');
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table course_format_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

        // Adding indexes to table course_format_options
        $table->add_index('formatoption', XMLDB_INDEX_UNIQUE, array('courseid', 'format', 'sectionid', 'name'));

        // Conditionally launch create table for course_format_options
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Changing type of field format on table course to char with length 21
        $table = new xmldb_table('course');
        $field = new xmldb_field('format', XMLDB_TYPE_CHAR, '21', null, XMLDB_NOTNULL, null, 'topics', 'summaryformat');

        // Launch change of type for field format
        $dbman->change_field_type($table, $field);

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012110200.00);
    }

    if ($oldversion < 2012110201.00) {

        // Copy fields 'coursedisplay', 'numsections', 'hiddensections' from table {course}
        // to table {course_format_options} as the additional format options
        $fields = array();
        $table = new xmldb_table('course');
        foreach (array('coursedisplay', 'numsections', 'hiddensections') as $fieldname) {
            // first check that fields still exist
            $field = new xmldb_field($fieldname);
            if ($dbman->field_exists($table, $field)) {
                $fields[] = $fieldname;
            }
        }

        if (!empty($fields)) {
            $transaction = $DB->start_delegated_transaction();
            $rs = $DB->get_recordset_sql('SELECT id, format, '. join(',', $fields).'
                FROM {course}
                WHERE format <> ? AND format <> ?',
                array('scorm', 'social'));
            // (do not copy fields from scrom and social formats, we already know that they are not used)
            foreach ($rs as $rec) {
                foreach ($fields as $field) {
                    try {
                        $DB->insert_record('course_format_options',
                                array(
                                    'courseid'  => $rec->id,
                                    'format'    => $rec->format,
                                    'sectionid' => 0,
                                    'name'      => $field,
                                    'value'     => $rec->$field
                                ));
                    } catch (dml_exception $e) {
                        // index 'courseid,format,sectionid,name' violation
                        // continue; the entry in course_format_options already exists, use it
                    }
                }
            }
            $rs->close();
            $transaction->allow_commit();

            // Drop fields from table course
            foreach ($fields as $fieldname) {
                $field = new xmldb_field($fieldname);
                $dbman->drop_field($table, $field);
            }
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012110201.00);
    }

    if ($oldversion < 2012110700.01) {

        // Define field caller_component to be added to portfolio_log.
        $table = new xmldb_table('portfolio_log');
        $field = new xmldb_field('caller_component', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'caller_file');

        // Conditionally launch add field caller_component.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012110700.01);
    }

    if ($oldversion < 2012111200.00) {

        // Define table temp_enroled_template to be created
        $table = new xmldb_table('temp_enroled_template');

        // Adding fields to table temp_enroled_template
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table temp_enroled_template
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table temp_enroled_template
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
        $table->add_index('roleid', XMLDB_INDEX_NOTUNIQUE, array('roleid'));

        // Conditionally launch create table for temp_enroled_template
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table temp_log_template to be created
        $table = new xmldb_table('temp_log_template');

        // Adding fields to table temp_log_template
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('action', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table temp_log_template
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table temp_log_template
        $table->add_index('action', XMLDB_INDEX_NOTUNIQUE, array('action'));
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('user', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->add_index('usercourseaction', XMLDB_INDEX_NOTUNIQUE, array('userid', 'course', 'action'));

        // Conditionally launch create table for temp_log_template
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012111200.00);
    }

    if ($oldversion < 2012111200.01) {
        // Force the rebuild of the cache of every courses, some cached information could contain wrong icon references.
        $DB->execute('UPDATE {course} set modinfo = ?, sectioncache = ?', array(null, null));

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012111200.01);
    }

    if ($oldversion < 2012111601.01) {
        // Clea up after old shared memory caching support.
        unset_config('cachetype');
        unset_config('rcache');
        unset_config('rcachettl');
        unset_config('intcachemax');
        unset_config('memcachedhosts');
        unset_config('memcachedpconn');

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012111601.01);
    }

    if ($oldversion < 2012112100.00) {

        // Define field eventtype to be added to event_subscriptions.
        $table = new xmldb_table('event_subscriptions');
        $field = new xmldb_field('eventtype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'userid');

        // Conditionally launch add field eventtype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012112100.00);
    }

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    if ($oldversion < 2012120300.01) {
        // Make sure site-course has format='site' //MDL-36840

        if ($SITE->format !== 'site') {
            $DB->set_field('course', 'format', 'site', array('id' => $SITE->id));
            $SITE->format = 'site';
        }

        // Main savepoint reached
        upgrade_main_savepoint(true, 2012120300.01);
    }

    if ($oldversion < 2012120300.04) {
        // Remove "_utf8" suffix from all langs in course table.
        $langs = $DB->get_records_sql("SELECT DISTINCT lang FROM {course} WHERE lang LIKE ?", array('%_utf8'));

        foreach ($langs as $lang=>$unused) {
            $newlang = str_replace('_utf8', '', $lang);
            $sql = "UPDATE {course} SET lang = :newlang WHERE lang = :lang";
            $DB->execute($sql, array('newlang'=>$newlang, 'lang'=>$lang));
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012120300.04);
    }

    if ($oldversion < 2012123000.00) {
        // Purge removed module filters and all their settings.

        $tables = array('filter_active', 'filter_config');
        foreach ($tables as $table) {
            $DB->delete_records_select($table, "filter LIKE 'mod/%'");
            $filters = $DB->get_records_sql("SELECT DISTINCT filter FROM {{$table}} WHERE filter LIKE 'filter/%'");
            foreach ($filters as $filter) {
                $DB->set_field($table, 'filter', substr($filter->filter, 7), array('filter'=>$filter->filter));
            }
        }

        $configs = array('stringfilters', 'filterall');
        foreach ($configs as $config) {
            if ($filters = get_config(null, $config)) {
                $filters = explode(',', $filters);
                $newfilters = array();
                foreach($filters as $filter) {
                    if (strpos($filter, '/') === false) {
                        $newfilters[] = $filter;
                    } else if (strpos($filter, 'filter/') === 0) {
                        $newfilters[] = substr($filter, 7);
                    }
                }
                $filters = implode(',', $newfilters);
                set_config($config, $filters);
            }
        }

        unset($tables);
        unset($table);
        unset($configs);
        unset($newfilters);
        unset($filters);
        unset($filter);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2012123000.00);
    }

    if ($oldversion < 2013021100.01) {

        // Changing precision of field password on table user to (255).
        $table = new xmldb_table('user');
        $field = new xmldb_field('password', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'username');

        // Launch change of precision for field password.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013021100.01);
    }

    if ($oldversion < 2013021800.00) {
        // Add the site identifier to the cache config's file.
        $siteidentifier = $DB->get_field('config', 'value', array('name' => 'siteidentifier'));
        cache_helper::update_site_identifier($siteidentifier);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013021800.00);
    }

    if ($oldversion < 2013021801.00) {
        // Fixing possible wrong MIME types for SMART Notebook files.
        $extensions = array('%.gallery', '%.galleryitem', '%.gallerycollection', '%.nbk', '%.notebook', '%.xbk');
        $select = $DB->sql_like('filename', '?', false);
        foreach ($extensions as $extension) {
            $DB->set_field_select(
                'files',
                'mimetype',
                'application/x-smarttech-notebook',
                $select,
                array($extension)
            );
        }
        upgrade_main_savepoint(true, 2013021801.00);
    }

    if ($oldversion < 2013021801.01) {
        // Retrieve the list of course_sections as a recordset to save memory
        $coursesections = $DB->get_recordset('course_sections', null, 'course, id', 'id, course, sequence');
        foreach ($coursesections as $coursesection) {
            // Retrieve all of the actual modules in this course and section combination to reduce DB calls
            $actualsectionmodules = $DB->get_records('course_modules',
                    array('course' => $coursesection->course, 'section' => $coursesection->id), '', 'id, section');

            // Break out the current sequence so that we can compare it
            $currentsequence = explode(',', $coursesection->sequence);
            $newsequence = array();

            // Check each of the modules in the current sequence
            foreach ($currentsequence as $module) {
                if (isset($actualsectionmodules[$module])) {
                    $newsequence[] = $module;
                    // We unset the actualsectionmodules so that we don't get duplicates and that we can add orphaned
                    // modules later
                    unset($actualsectionmodules[$module]);
                }
            }

            // Append any modules which have somehow been orphaned
            foreach ($actualsectionmodules as $module) {
                $newsequence[] = $module->id;
            }

            // Piece it all back together
            $sequence = implode(',', $newsequence);

            // Only update if there have been changes
            if ($sequence !== $coursesection->sequence) {
                $coursesection->sequence = $sequence;
                $DB->update_record('course_sections', $coursesection);

                // And clear the sectioncache and modinfo cache - they'll be regenerated on next use
                $course = new stdClass();
                $course->id = $coursesection->course;
                $course->sectioncache = null;
                $course->modinfo = null;
                $DB->update_record('course', $course);
            }
        }
        $coursesections->close();

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013021801.01);
    }

    if ($oldversion < 2013021902.00) {
        // ISO country change: Netherlands Antilles is split into BQ, CW & SX
        // http://www.iso.org/iso/iso_3166-1_newsletter_vi-8_split_of_the_dutch_antilles_final-en.pdf
        $sql = "UPDATE {user} SET country = '' WHERE country = ?";
        $DB->execute($sql, array('AN'));

        upgrade_main_savepoint(true, 2013021902.00);
    }

    if ($oldversion < 2013022600.00) {
        // Delete entries regarding invalid 'interests' option which breaks course.
        $DB->delete_records('course_sections_avail_fields', array('userfield' => 'interests'));
        $DB->delete_records('course_modules_avail_fields', array('userfield' => 'interests'));
        // Clear course cache (will be rebuilt on first visit) in case of changes to these.
        $DB->execute('UPDATE {course} set modinfo = ?, sectioncache = ?', array(null, null));

        upgrade_main_savepoint(true, 2013022600.00);
    }

    // Add index to field "timemodified" for grade_grades_history table.
    if ($oldversion < 2013030400.00) {
        $table = new xmldb_table('grade_grades_history');
        $field = new xmldb_field('timemodified');

        if ($dbman->field_exists($table, $field)) {
            $index = new xmldb_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013030400.00);
    }

    if ($oldversion < 2013030400.02) {
        // Cleanup qformat blackboard settings.
        unset_all_config_for_plugin('qformat_blackboard');

        upgrade_main_savepoint(true, 2013030400.02);
    }

    // This is checking to see if the site has been running a specific version with a bug in it
    // because this upgrade step is slow and is only needed if the site has been running with the affected versions.
    if ($oldversion >= 2012062504.08 && $oldversion < 2012062504.13) {
        // Retrieve the list of course_sections as a recordset to save memory.
        // This is to fix a regression caused by MDL-37939.
        // In this case the upgrade step is fixing records where:
        // The data in course_sections.sequence contains the correct module id
        // The section field for on the course modules table may have been updated to point to the incorrect id.

        // This query is looking for sections where the sequence is not in sync with the course_modules table.
        // The syntax for the like query is looking for a value in a comma separated list.
        // It adds a comma to either site of the list and then searches for LIKE '%,id,%'.
        $sequenceconcat = $DB->sql_concat("','", 's.sequence', "','");
        $moduleconcat = $DB->sql_concat("'%,'", 'cm.id', "',%'");
        $sql = 'SELECT s2.id, s2.course, s2.sequence
                FROM {course_sections} s2
                JOIN(
                    SELECT DISTINCT s.id
                    FROM
                    {course_modules} cm
                    JOIN {course_sections} s
                    ON
                        cm.course = s.course
                    WHERE cm.section != s.id AND ' . $sequenceconcat . ' LIKE ' . $moduleconcat . '
                ) d
                ON s2.id = d.id';
        $coursesections = $DB->get_recordset_sql($sql);

        foreach ($coursesections as $coursesection) {
            // Retrieve all of the actual modules in this course and section combination to reduce DB calls.
            $actualsectionmodules = $DB->get_records('course_modules',
                    array('course' => $coursesection->course, 'section' => $coursesection->id), '', 'id, section');

            // Break out the current sequence so that we can compare it.
            $currentsequence = explode(',', $coursesection->sequence);
            $orphanlist = array();

            // Check each of the modules in the current sequence.
            foreach ($currentsequence as $cmid) {
                if (!empty($cmid) && !isset($actualsectionmodules[$cmid])) {
                    $orphanlist[] = $cmid;
                }
            }

            if (!empty($orphanlist)) {
                list($sql, $params) = $DB->get_in_or_equal($orphanlist, SQL_PARAMS_NAMED);
                $sql = "id $sql";

                $DB->set_field_select('course_modules', 'section', $coursesection->id, $sql, $params);

                // And clear the sectioncache and modinfo cache - they'll be regenerated on next use.
                $course = new stdClass();
                $course->id = $coursesection->course;
                $course->sectioncache = null;
                $course->modinfo = null;
                $DB->update_record('course', $course);
            }
        }
        $coursesections->close();

        // No savepoint needed for this change.
    }

    if ($oldversion < 2013032200.01) {
        // GD is now always available
        set_config('gdversion', 2);

        upgrade_main_savepoint(true, 2013032200.01);
    }

    if ($oldversion < 2013032600.03) {
        // Fixing possible wrong MIME type for MIME HTML (MHTML) files.
        $extensions = array('%.mht', '%.mhtml');
        $select = $DB->sql_like('filename', '?', false);
        foreach ($extensions as $extension) {
            $DB->set_field_select(
                'files',
                'mimetype',
                'message/rfc822',
                $select,
                array($extension)
            );
        }
        upgrade_main_savepoint(true, 2013032600.03);
    }

    if ($oldversion < 2013032600.04) {
        // MDL-31983 broke the quiz version number. Fix it.
        $DB->set_field('modules', 'version', '2013021500',
                array('name' => 'quiz', 'version' => '2013310100'));
        upgrade_main_savepoint(true, 2013032600.04);
    }

    if ($oldversion < 2013040200.00) {
        // Add openbadges tables.

        // Define table 'badge' to be created.
        $table = new xmldb_table('badge');

        // Adding fields to table 'badge'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');
        $table->add_field('image', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'description');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'image');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        $table->add_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'timemodified');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'usercreated');
        $table->add_field('issuername', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'usermodified');
        $table->add_field('issuerurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'issuername');
        $table->add_field('issuercontact', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'issuerurl');
        $table->add_field('expiredate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'issuercontact');
        $table->add_field('expireperiod', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'expiredate');
        $table->add_field('type', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'expireperiod');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'type');
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'courseid');
        $table->add_field('messagesubject', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'message');
        $table->add_field('attachment', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'messagesubject');
        $table->add_field('notification', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'attachment');
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'notification');
        $table->add_field('nextcron', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'status');

        // Adding keys to table 'badge'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->add_key('fk_usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));
        $table->add_key('fk_usercreated', XMLDB_KEY_FOREIGN, array('usercreated'), 'user', array('id'));

        // Adding indexes to table 'badge'.
        $table->add_index('type', XMLDB_INDEX_NOTUNIQUE, array('type'));

        // Conditionally launch create table for 'badge'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'badge_criteria' to be created.
        $table = new xmldb_table('badge_criteria');

        // Adding fields to table 'badge_criteria'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('criteriatype', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'badgeid');
        $table->add_field('method', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'criteriatype');

        // Adding keys to table 'badge_criteria'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_badgeid', XMLDB_KEY_FOREIGN, array('badgeid'), 'badge', array('id'));

        // Adding indexes to table 'badge_criteria'.
        $table->add_index('criteriatype', XMLDB_INDEX_NOTUNIQUE, array('criteriatype'));
        $table->add_index('badgecriteriatype', XMLDB_INDEX_UNIQUE, array('badgeid', 'criteriatype'));

        // Conditionally launch create table for 'badge_criteria'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'badge_criteria_param' to be created.
        $table = new xmldb_table('badge_criteria_param');

        // Adding fields to table 'badge_criteria_param'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('critid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'critid');
        $table->add_field('value', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'name');

        // Adding keys to table 'badge_criteria_param'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_critid', XMLDB_KEY_FOREIGN, array('critid'), 'badge_criteria', array('id'));

        // Conditionally launch create table for 'badge_criteria_param'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'badge_issued' to be created.
        $table = new xmldb_table('badge_issued');

        // Adding fields to table 'badge_issued'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'badgeid');
        $table->add_field('uniquehash', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'userid');
        $table->add_field('dateissued', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'uniquehash');
        $table->add_field('dateexpire', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'dateissued');
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'dateexpire');
        $table->add_field('issuernotified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'visible');

        // Adding keys to table 'badge_issued'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_badgeid', XMLDB_KEY_FOREIGN, array('badgeid'), 'badge', array('id'));
        $table->add_key('fk_userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        $table->add_index('badgeuser', XMLDB_INDEX_UNIQUE, array('badgeid', 'userid'));

        // Conditionally launch create table for 'badge_issued'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'badge_criteria_met' to be created.
        $table = new xmldb_table('badge_criteria_met');

        // Adding fields to table 'badge_criteria_met'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('issuedid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id');
        $table->add_field('critid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'issuedid');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'critid');
        $table->add_field('datemet', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Adding keys to table 'badge_criteria_met'
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_critid', XMLDB_KEY_FOREIGN, array('critid'), 'badge_criteria', array('id'));
        $table->add_key('fk_userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('fk_issuedid', XMLDB_KEY_FOREIGN, array('issuedid'), 'badge_issued', array('id'));

        // Conditionally launch create table for 'badge_criteria_met'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'badge_manual_award' to be created.
        $table = new xmldb_table('badge_manual_award');

        // Adding fields to table 'badge_manual_award'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('recipientid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'badgeid');
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'recipientid');
        $table->add_field('issuerrole', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'issuerid');
        $table->add_field('datemet', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'issuerrole');

        // Adding keys to table 'badge_manual_award'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_badgeid', XMLDB_KEY_FOREIGN, array('badgeid'), 'badge', array('id'));
        $table->add_key('fk_recipientid', XMLDB_KEY_FOREIGN, array('recipientid'), 'user', array('id'));
        $table->add_key('fk_issuerid', XMLDB_KEY_FOREIGN, array('issuerid'), 'user', array('id'));
        $table->add_key('fk_issuerrole', XMLDB_KEY_FOREIGN, array('issuerrole'), 'role', array('id'));

        // Conditionally launch create table for 'badge_manual_award'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'badge_backpack' to be created.
        $table = new xmldb_table('badge_backpack');

        // Adding fields to table 'badge_backpack'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('email', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'userid');
        $table->add_field('backpackurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'email');
        $table->add_field('backpackuid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'backpackurl');
        $table->add_field('backpackgid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'backpackuid');
        $table->add_field('autosync', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'backpackgid');
        $table->add_field('password', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'autosync');

        // Adding keys to table 'badge_backpack'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for 'badge_backpack'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013040200.00);
    }

    if ($oldversion < 2013040201.00) {
        // Convert name field in event table to text type as RFC-2445 doesn't have any limitation on it.
        $table = new xmldb_table('event');
        $field = new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013040201.00);
    }

    if ($oldversion < 2013040300.01) {

        // Define field completionstartonenrol to be dropped from course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('completionstartonenrol');

        // Conditionally launch drop field completionstartonenrol.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013040300.01);
    }

    if ($oldversion < 2013041200.00) {
        // MDL-29877 Some bad restores created grade items with no category information.
        $sql = "UPDATE {grade_items}
                   SET categoryid = courseid
                 WHERE itemtype <> 'course' and itemtype <> 'category'
                       AND categoryid IS NULL";
        $DB->execute($sql);
        upgrade_main_savepoint(true, 2013041200.00);
    }

    if ($oldversion < 2013041600.00) {
        // Copy constants from /course/lib.php instead of including the whole library:
        $c = array( 'FRONTPAGENEWS'                 => 0,
                    'FRONTPAGECOURSELIST'           => 1,
                    'FRONTPAGECATEGORYNAMES'        => 2,
                    'FRONTPAGETOPICONLY'            => 3,
                    'FRONTPAGECATEGORYCOMBO'        => 4,
                    'FRONTPAGEENROLLEDCOURSELIST'   => 5,
                    'FRONTPAGEALLCOURSELIST'        => 6,
                    'FRONTPAGECOURSESEARCH'         => 7);
        // Update frontpage settings $CFG->frontpage and $CFG->frontpageloggedin. In 2.4 there was too much of hidden logic about them.
        // This script tries to make sure that with the new (more user-friendly) frontpage settings the frontpage looks as similar as possible to what it was before upgrade.
        $ncourses = $DB->count_records('course');
        foreach (array('frontpage', 'frontpageloggedin') as $configkey) {
            if ($frontpage = explode(',', $CFG->{$configkey})) {
                $newfrontpage = array();
                foreach ($frontpage as $v) {
                    switch ($v) {
                        case $c['FRONTPAGENEWS']:
                            // Not related to course listings, leave as it is.
                            $newfrontpage[] = $c['FRONTPAGENEWS'];
                            break;
                        case $c['FRONTPAGECOURSELIST']:
                            if ($configkey === 'frontpageloggedin' && empty($CFG->disablemycourses)) {
                                // In 2.4 unless prohibited in config, the "list of courses" was considered "list of enrolled courses" plus course search box.
                                $newfrontpage[] = $c['FRONTPAGEENROLLEDCOURSELIST'];
                            } else if ($ncourses <= 200) {
                                // Still list of courses was only displayed in there were less than 200 courses in system. Otherwise - search box only.
                                $newfrontpage[] = $c['FRONTPAGEALLCOURSELIST'];
                                break; // skip adding search box
                            }
                            if (!in_array($c['FRONTPAGECOURSESEARCH'], $newfrontpage)) {
                                $newfrontpage[] = $c['FRONTPAGECOURSESEARCH'];
                            }
                            break;
                        case $c['FRONTPAGECATEGORYNAMES']:
                            // In 2.4 search box was displayed automatically after categories list. In 2.5 it is displayed as a separate setting.
                            $newfrontpage[] = $c['FRONTPAGECATEGORYNAMES'];
                            if (!in_array($c['FRONTPAGECOURSESEARCH'], $newfrontpage)) {
                                $newfrontpage[] = $c['FRONTPAGECOURSESEARCH'];
                            }
                            break;
                        case $c['FRONTPAGECATEGORYCOMBO']:
                            $maxcourses = empty($CFG->numcoursesincombo) ? 500 : $CFG->numcoursesincombo;
                            // In 2.4 combo list was not displayed if there are more than $CFG->numcoursesincombo courses in the system.
                            if ($ncourses < $maxcourses) {
                                $newfrontpage[] = $c['FRONTPAGECATEGORYCOMBO'];
                            }
                            if (!in_array($c['FRONTPAGECOURSESEARCH'], $newfrontpage)) {
                                $newfrontpage[] = $c['FRONTPAGECOURSESEARCH'];
                            }
                            break;
                    }
                }
                set_config($configkey, join(',', $newfrontpage));
            }
        }
        // $CFG->numcoursesincombo no longer affects whether the combo list is displayed. Setting is deprecated.
        unset_config('numcoursesincombo');

        upgrade_main_savepoint(true, 2013041600.00);
    }

    if ($oldversion < 2013041601.00) {
        // Create a new 'badge_external' table first.
        // Define table 'badge_external' to be created.
        $table = new xmldb_table('badge_external');

        // Adding fields to table 'badge_external'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('backpackid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('collectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'backpackid');

        // Adding keys to table 'badge_external'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_backpackid', XMLDB_KEY_FOREIGN, array('backpackid'), 'badge_backpack', array('id'));

        // Conditionally launch create table for 'badge_external'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Perform user data migration.
        $usercollections = $DB->get_records('badge_backpack');
        foreach ($usercollections as $usercollection) {
            $collection = new stdClass();
            $collection->backpackid = $usercollection->id;
            $collection->collectionid = $usercollection->backpackgid;
            $DB->insert_record('badge_external', $collection);
        }

        // Finally, drop the column.
        // Define field backpackgid to be dropped from 'badge_backpack'.
        $table = new xmldb_table('badge_backpack');
        $field = new xmldb_field('backpackgid');

        // Conditionally launch drop field backpackgid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013041601.00);
    }

    if ($oldversion < 2013041601.01) {
        // Changing the default of field descriptionformat on table user to 1.
        $table = new xmldb_table('user');
        $field = new xmldb_field('descriptionformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'description');

        // Launch change of default for field descriptionformat.
        $dbman->change_field_default($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013041601.01);
    }

    if ($oldversion < 2013041900.00) {
        require_once($CFG->dirroot . '/cache/locallib.php');
        // The features bin needs updating.
        cache_config_writer::update_default_config_stores();
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013041900.00);
    }

    if ($oldversion < 2013042300.00) {
        // Adding index to unreadmessageid field of message_working table (MDL-34933)
        $table = new xmldb_table('message_working');
        $index = new xmldb_index('unreadmessageid_idx', XMLDB_INDEX_NOTUNIQUE, array('unreadmessageid'));

        // Conditionally launch add index unreadmessageid
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013042300.00);
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013051400.01) {
        // Fix incorrect cc-nc url. Unfortunately the license 'plugins' do
        // not give a mechanism to do this.

        $sql = "UPDATE {license}
                   SET source = :url, version = :newversion
                 WHERE shortname = :shortname AND version = :oldversion";

        $params = array(
            'url' => 'http://creativecommons.org/licenses/by-nc/3.0/',
            'shortname' => 'cc-nc',
            'newversion' => '2013051500',
            'oldversion' => '2010033100'
        );

        $DB->execute($sql, $params);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013051400.01);
    }

    if ($oldversion < 2013051400.06) {
        // Clean up old tokens which haven't been deleted.
        $DB->execute("DELETE FROM {user_private_key} WHERE NOT EXISTS
                         (SELECT 'x' FROM {user} WHERE deleted = 0 AND id = userid)");

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013051400.06);
    }

    if ($oldversion < 2013051401.01) {

        // Remove orphan repository instances.
        if ($DB->get_dbfamily() === 'mysql') {
            $sql = "DELETE {repository_instances} FROM {repository_instances}
                    LEFT JOIN {context} ON {context}.id = {repository_instances}.contextid
                    WHERE {context}.id IS NULL";
        } else {
            $sql = "DELETE FROM {repository_instances}
                    WHERE NOT EXISTS (
                        SELECT 'x' FROM {context}
                        WHERE {context}.id = {repository_instances}.contextid)";
        }
        $DB->execute($sql);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2013051401.01);
    }

    return true;
}
