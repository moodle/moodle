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

defined('MOODLE_INTERNAL') || die();

/**
 * upgrade this assignment instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the assign module
 * @return bool
 */
function xmldb_assign_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

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
            // Look for grade records with no submission record.
            // This is when a teacher has marked a student before they submitted anything.
            $records = $DB->get_records_sql('SELECT g.id, g.assignment, g.userid, g.attemptnumber
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
                $submission->attemptnumber = $record->attemptnumber;
                $submission->status = 'new';
                $submission->groupid = 0;
                $submission->latest = 0;
                $submission->timecreated = time();
                $submission->timemodified = time();
                array_push($submissions, $submission);
            }

            $DB->insert_records('assign_submission', $submissions);

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

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016100301) {

        // Define table assign_overrides to be created.
        $table = new xmldb_table('assign_overrides');

        // Adding fields to table assign_overrides.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('assignid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('allowsubmissionsfromdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('duedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('cutoffdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table assign_overrides.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('assignid', XMLDB_KEY_FOREIGN, array('assignid'), 'assign', array('id'));
        $table->add_key('groupid', XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for assign_overrides.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2016100301, 'assign');
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017021500) {
        // Fix event types of assign events.
        $params = [
            'modulename' => 'assign',
            'eventtype' => 'close'
        ];
        $select = "modulename = :modulename AND eventtype = :eventtype";
        $DB->set_field_select('event', 'eventtype', 'due', $select, $params);

        // Delete 'open' events.
        $params = [
            'modulename' => 'assign',
            'eventtype' => 'open'
        ];
        $DB->delete_records('event', $params);

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2017021500, 'assign');
    }

    if ($oldversion < 2017031300) {
        // Add a 'gradingduedate' field to the 'assign' table.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('gradingduedate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'cutoffdate');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2017031300, 'assign');
    }

    if ($oldversion < 2017042800) {
        // Update query to set the grading due date one week after the due date.
        // Only assign instances with grading due date not set and with a due date of not older than 3 weeks will be updated.
        $sql = "UPDATE {assign}
                   SET gradingduedate = duedate + :weeksecs
                 WHERE gradingduedate = 0
                       AND duedate > :timelimit";

        // Calculate the time limit, which is 3 weeks before the current date.
        $interval = new DateInterval('P3W');
        $timelimit = new DateTime();
        $timelimit->sub($interval);

        // Update query params.
        $params = [
            'weeksecs' => WEEKSECS,
            'timelimit' => $timelimit->getTimestamp()
        ];

        // Execute DB update for assign instances.
        $DB->execute($sql, $params);

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2017042800, 'assign');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2017051501) {
        // Data fix any assign group override event priorities which may have been accidentally nulled due to a bug on the group
        // overrides edit form.

        // First, find all assign group override events having null priority (and join their corresponding assign_overrides entry).
        $sql = "SELECT e.id AS id, o.sortorder AS priority
                  FROM {assign_overrides} o
                  JOIN {event} e ON (e.modulename = 'assign' AND o.assignid = e.instance AND e.groupid = o.groupid)
                 WHERE o.groupid IS NOT NULL AND e.priority IS NULL
              ORDER BY o.id";
        $affectedrs = $DB->get_recordset_sql($sql);

        // Now update the event's priority based on the assign_overrides sortorder we found. This uses similar logic to
        // assign_refresh_events(), except we've restricted the set of assignments and overrides we're dealing with here.
        foreach ($affectedrs as $record) {
            $DB->set_field('event', 'priority', $record->priority, ['id' => $record->id]);
        }
        $affectedrs->close();

        // Main savepoint reached.
        upgrade_mod_savepoint(true, 2017051501, 'assign');
    }

    if ($oldversion < 2017051502) {
        require_once($CFG->dirroot.'/mod/assign/upgradelib.php');
        $brokenassigns = get_assignments_with_rescaled_null_grades();

        // Set config value.
        foreach ($brokenassigns as $assign) {
            set_config('has_rescaled_null_grades_' . $assign, 1, 'assign');
        }

        // Main savepoint reached.
        upgrade_mod_savepoint(true, 2017051502, 'assign');
    }

    return true;
}
