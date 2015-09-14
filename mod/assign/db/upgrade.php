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
 * Upgrade code for install
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * upgrade this assignment instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the assign module
 * @return bool
 */
function xmldb_assign_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012051700) {

        // Define field to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('sendlatenotifications', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'sendnotifications');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2012051700, 'assign');
    }

    // Moodle v2.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2012071800) {

        // Define field requiresubmissionstatement to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('requiresubmissionstatement', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field requiresubmissionstatement.

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2012071800, 'assign');
    }

    if ($oldversion < 2012081600) {

        // Define field to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('completionsubmit', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2012081600, 'assign');
    }

    // Individual extension dates support.
    if ($oldversion < 2012082100) {

        // Define field cutoffdate to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('cutoffdate', XMLDB_TYPE_INTEGER, '10', null,
                                 XMLDB_NOTNULL, null, '0', 'completionsubmit');

        // Conditionally launch add field cutoffdate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // If prevent late is on - set cutoffdate to due date.

        // Now remove the preventlatesubmissions column.
        $field = new xmldb_field('preventlatesubmissions', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'nosubmissions');
        if ($dbman->field_exists($table, $field)) {
            // Set the cutoffdate to the duedate if preventlatesubmissions was enabled.
            $sql = 'UPDATE {assign} SET cutoffdate = duedate WHERE preventlatesubmissions = 1';
            $DB->execute($sql);

            $dbman->drop_field($table, $field);
        }

        // Define field extensionduedate to be added to assign_grades.
        $table = new xmldb_table('assign_grades');
        $field = new xmldb_field('extensionduedate', XMLDB_TYPE_INTEGER, '10', null,
                                 XMLDB_NOTNULL, null, '0', 'mailed');

        // Conditionally launch add field extensionduedate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2012082100, 'assign');
    }

    // Team assignment support.
    if ($oldversion < 2012082300) {

        // Define field to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('teamsubmission', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'cutoffdate');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('requireallteammemberssubmit', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'teamsubmission');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('teamsubmissiongroupingid', XMLDB_TYPE_INTEGER, '10', null,
                                 XMLDB_NOTNULL, null, '0', 'requireallteammemberssubmit');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $index = new xmldb_index('teamsubmissiongroupingid',
                                 XMLDB_INDEX_NOTUNIQUE,
                                 array('teamsubmissiongroupingid'));
        // Conditionally launch add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $table = new xmldb_table('assign_submission');
        $field = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null,
                                 XMLDB_NOTNULL, null, '0', 'status');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2012082300, 'assign');
    }
    if ($oldversion < 2012082400) {

        // Define table assign_user_mapping to be created.
        $table = new xmldb_table('assign_user_mapping');

        // Adding fields to table assign_user_mapping.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('assignment', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table assign_user_mapping.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('assignment', XMLDB_KEY_FOREIGN, array('assignment'), 'assign', array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for assign_user_mapping.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field blindmarking to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('blindmarking', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'teamsubmissiongroupingid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field revealidentities to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('revealidentities', XMLDB_TYPE_INTEGER, '2', null,
                                 XMLDB_NOTNULL, null, '0', 'blindmarking');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assignment savepoint reached.
        upgrade_mod_savepoint(true, 2012082400, 'assign');
    }

    // Moodle v2.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013030600) {
        upgrade_set_timeout(60*20);

        // Some assignments (upgraded from 2.2 assignment) have duplicate entries in the assign_submission
        // and assign_grades tables for a single user. This needs to be cleaned up before we can add the unique indexes
        // below.

        // Only do this cleanup if the attempt number field has not been added to the table yet.
        $table = new xmldb_table('assign_submission');
        $field = new xmldb_field('attemptnumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'groupid');
        if (!$dbman->field_exists($table, $field)) {
            // OK safe to cleanup duplicates here.

            $sql = 'SELECT assignment, userid, groupid from {assign_submission} ' .
                   'GROUP BY assignment, userid, groupid HAVING (count(id) > 1)';
            $badrecords = $DB->get_recordset_sql($sql);

            foreach ($badrecords as $badrecord) {
                $params = array('userid'=>$badrecord->userid,
                                'groupid'=>$badrecord->groupid,
                                'assignment'=>$badrecord->assignment);
                $duplicates = $DB->get_records('assign_submission', $params, 'timemodified DESC', 'id, timemodified');
                if ($duplicates) {
                    // Take the first (last updated) entry out of the list so it doesn't get deleted.
                    $valid = array_shift($duplicates);
                    $deleteids = array();
                    foreach ($duplicates as $duplicate) {
                        $deleteids[] = $duplicate->id;
                    }

                    list($sqlids, $sqlidparams) = $DB->get_in_or_equal($deleteids);
                    $DB->delete_records_select('assign_submission', 'id ' . $sqlids, $sqlidparams);
                }
            }

            $badrecords->close();
        }

        // Same cleanup required for assign_grades
        // Only do this cleanup if the attempt number field has not been added to the table yet.
        $table = new xmldb_table('assign_grades');
        $field = new xmldb_field('attemptnumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'grade');
        if (!$dbman->field_exists($table, $field)) {
            // OK safe to cleanup duplicates here.

            $sql = 'SELECT assignment, userid from {assign_grades} GROUP BY assignment, userid HAVING (count(id) > 1)';
            $badrecords = $DB->get_recordset_sql($sql);

            foreach ($badrecords as $badrecord) {
                $params = array('userid'=>$badrecord->userid,
                                'assignment'=>$badrecord->assignment);
                $duplicates = $DB->get_records('assign_grades', $params, 'timemodified DESC', 'id, timemodified');
                if ($duplicates) {
                    // Take the first (last updated) entry out of the list so it doesn't get deleted.
                    $valid = array_shift($duplicates);
                    $deleteids = array();
                    foreach ($duplicates as $duplicate) {
                        $deleteids[] = $duplicate->id;
                    }

                    list($sqlids, $sqlidparams) = $DB->get_in_or_equal($deleteids);
                    $DB->delete_records_select('assign_grades', 'id ' . $sqlids, $sqlidparams);
                }
            }

            $badrecords->close();
        }

        // Define table assign_user_flags to be created.
        $table = new xmldb_table('assign_user_flags');

        // Adding fields to table assign_user_flags.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('assignment', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('locked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('mailed', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('extensionduedate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table assign_user_flags.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('assignment', XMLDB_KEY_FOREIGN, array('assignment'), 'assign', array('id'));

        // Adding indexes to table assign_user_flags.
        $table->add_index('mailed', XMLDB_INDEX_NOTUNIQUE, array('mailed'));

        // Conditionally launch create table for assign_user_flags.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);

            // Copy the flags from the old table to the new one.
            $sql = 'INSERT INTO {assign_user_flags}
                        (assignment, userid, locked, mailed, extensionduedate)
                    SELECT assignment, userid, locked, mailed, extensionduedate
                    FROM {assign_grades}';
            $DB->execute($sql);
        }

        // And delete the old columns.
        // Define index mailed (not unique) to be dropped form assign_grades.
        $table = new xmldb_table('assign_grades');
        $index = new xmldb_index('mailed', XMLDB_INDEX_NOTUNIQUE, array('mailed'));

        // Conditionally launch drop index mailed.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define field locked to be dropped from assign_grades.
        $table = new xmldb_table('assign_grades');
        $field = new xmldb_field('locked');

        // Conditionally launch drop field locked.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field mailed to be dropped from assign_grades.
        $table = new xmldb_table('assign_grades');
        $field = new xmldb_field('mailed');

        // Conditionally launch drop field mailed.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field extensionduedate to be dropped from assign_grades.
        $table = new xmldb_table('assign_grades');
        $field = new xmldb_field('extensionduedate');

        // Conditionally launch drop field extensionduedate.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field attemptreopenmethod to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('attemptreopenmethod', XMLDB_TYPE_CHAR, '10', null,
                                 XMLDB_NOTNULL, null, 'none', 'revealidentities');

        // Conditionally launch add field attemptreopenmethod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field maxattempts to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('maxattempts', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL, null, '-1', 'attemptreopenmethod');

        // Conditionally launch add field maxattempts.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field attemptnumber to be added to assign_submission.
        $table = new xmldb_table('assign_submission');
        $field = new xmldb_field('attemptnumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'groupid');

        // Conditionally launch add field attemptnumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index attemptnumber (not unique) to be added to assign_submission.
        $table = new xmldb_table('assign_submission');
        $index = new xmldb_index('attemptnumber', XMLDB_INDEX_NOTUNIQUE, array('attemptnumber'));
        // Conditionally launch add index attemptnumber.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field attemptnumber to be added to assign_grades.
        $table = new xmldb_table('assign_grades');
        $field = new xmldb_field('attemptnumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'grade');

        // Conditionally launch add field attemptnumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index attemptnumber (not unique) to be added to assign_grades.
        $table = new xmldb_table('assign_grades');
        $index = new xmldb_index('attemptnumber', XMLDB_INDEX_NOTUNIQUE, array('attemptnumber'));

        // Conditionally launch add index attemptnumber.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index uniqueattemptsubmission (unique) to be added to assign_submission.
        $table = new xmldb_table('assign_submission');
        $index = new xmldb_index('uniqueattemptsubmission',
                                 XMLDB_INDEX_UNIQUE,
                                 array('assignment', 'userid', 'groupid', 'attemptnumber'));

        // Conditionally launch add index uniqueattempt.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index uniqueattemptgrade (unique) to be added to assign_grades.
        $table = new xmldb_table('assign_grades');
        $index = new xmldb_index('uniqueattemptgrade', XMLDB_INDEX_UNIQUE, array('assignment', 'userid', 'attemptnumber'));

        // Conditionally launch add index uniqueattempt.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Module assign savepoint reached.
        upgrade_mod_savepoint(true, 2013030600, 'assign');
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013061101) {
        // Define field markingworkflow to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('markingworkflow', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'maxattempts');

        // Conditionally launch add field markingworkflow.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field markingallocation to be added to assign.
        $field = new xmldb_field('markingallocation', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'markingworkflow');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field workflowstate to be added to assign_grades.
        $table = new xmldb_table('assign_user_flags');
        $field = new xmldb_field('workflowstate', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'extensionduedate');

        // Conditionally launch add field workflowstate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field allocatedmarker to be added to assign_grades.
        $field = new xmldb_field('allocatedmarker', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'workflowstate');
        // Conditionally launch add field workflowstate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2013061101, 'assign');
    }

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014010801) {

        // Define field sendstudentnotifications to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('sendstudentnotifications',
                                 XMLDB_TYPE_INTEGER,
                                 '2',
                                 null,
                                 XMLDB_NOTNULL,
                                 null,
                                 '1',
                                 'markingallocation');

        // Conditionally launch add field sendstudentnotifications.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014010801, 'assign');
    }

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014051201) {

        // Cleanup bad database records where assignid is missing.

        $DB->delete_records('assign_user_mapping', array('assignment'=>0));
        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014051201, 'assign');
    }
    if ($oldversion < 2014072400) {

        // Add "latest" column to submissions table to mark the latest attempt.
        $table = new xmldb_table('assign_submission');
        $field = new xmldb_field('latest', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'attemptnumber');

        // Conditionally launch add field latest.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014072400, 'assign');
    }
    if ($oldversion < 2014072401) {

         // Define index latestattempt (not unique) to be added to assign_submission.
        $table = new xmldb_table('assign_submission');
        $index = new xmldb_index('latestattempt', XMLDB_INDEX_NOTUNIQUE, array('assignment', 'userid', 'groupid', 'latest'));

        // Conditionally launch add index latestattempt.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014072401, 'assign');
    }
    if ($oldversion < 2014072405) {

        // Prevent running this multiple times.

        $countsql = 'SELECT COUNT(id) FROM {assign_submission} WHERE latest = ?';

        $count = $DB->count_records_sql($countsql, array(1));
        if ($count == 0) {

            // Mark the latest attempt for every submission in mod_assign.
            $maxattemptsql = 'SELECT assignment, userid, groupid, max(attemptnumber) AS maxattempt
                                FROM {assign_submission}
                            GROUP BY assignment, groupid, userid';

            $maxattemptidssql = 'SELECT souter.id
                                   FROM {assign_submission} souter
                                   JOIN (' . $maxattemptsql . ') sinner
                                     ON souter.assignment = sinner.assignment
                                    AND souter.userid = sinner.userid
                                    AND souter.groupid = sinner.groupid
                                    AND souter.attemptnumber = sinner.maxattempt';

            // We need to avoid using "WHERE ... IN(SELECT ...)" clause with MySQL for performance reason.
            // TODO MDL-29589 Remove this dbfamily exception when implemented.
            if ($DB->get_dbfamily() === 'mysql') {
                $params = array('latest' => 1);
                $sql = 'UPDATE {assign_submission}
                    INNER JOIN (' . $maxattemptidssql . ') souterouter ON souterouter.id = {assign_submission}.id
                           SET latest = :latest';
                $DB->execute($sql, $params);
            } else {
                $select = 'id IN(' . $maxattemptidssql . ')';
                $DB->set_field_select('assign_submission', 'latest', 1, $select);
            }

            // Look for grade records with no submission record.
            // This is when a teacher has marked a student before they submitted anything.
            $records = $DB->get_records_sql('SELECT g.id, g.assignment, g.userid
                                               FROM {assign_grades} g
                                          LEFT JOIN {assign_submission} s
                                                 ON s.assignment = g.assignment
                                                AND s.userid = g.userid
                                              WHERE s.id IS NULL');
            $submissions = array();
            foreach ($records as $record) {
                $submission = new stdClass();
                $submission->assignment = $record->assignment;
                $submission->userid = $record->userid;
                $submission->status = 'new';
                $submission->groupid = 0;
                $submission->latest = 1;
                $submission->timecreated = time();
                $submission->timemodified = time();
                array_push($submissions, $submission);
            }

            $DB->insert_records('assign_submission', $submissions);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014072405, 'assign');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014122600) {
        // Delete any entries from the assign_user_flags and assign_user_mapping that are no longer required.
        if ($DB->get_dbfamily() === 'mysql') {
            $sql1 = "DELETE {assign_user_flags}
                       FROM {assign_user_flags}
                  LEFT JOIN {assign}
                         ON {assign_user_flags}.assignment = {assign}.id
                      WHERE {assign}.id IS NULL";

            $sql2 = "DELETE {assign_user_mapping}
                       FROM {assign_user_mapping}
                  LEFT JOIN {assign}
                         ON {assign_user_mapping}.assignment = {assign}.id
                      WHERE {assign}.id IS NULL";
        } else {
            $sql1 = "DELETE FROM {assign_user_flags}
                WHERE NOT EXISTS (
                          SELECT 'x' FROM {assign}
                           WHERE {assign_user_flags}.assignment = {assign}.id)";

            $sql2 = "DELETE FROM {assign_user_mapping}
                WHERE NOT EXISTS (
                          SELECT 'x' FROM {assign}
                           WHERE {assign_user_mapping}.assignment = {assign}.id)";
        }

        $DB->execute($sql1);
        $DB->execute($sql2);

        upgrade_mod_savepoint(true, 2014122600, 'assign');
    }

    if ($oldversion < 2015022300) {

        // Define field preventsubmissionnotingroup to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('preventsubmissionnotingroup',
            XMLDB_TYPE_INTEGER,
            '2',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'sendstudentnotifications');

        // Conditionally launch add field preventsubmissionnotingroup.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2015022300, 'assign');
    }

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
