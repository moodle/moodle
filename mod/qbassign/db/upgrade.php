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
 * @package   mod_qbassign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * upgrade this qbassignment instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the qbassign module
 * @return bool
 */
function xmldb_qbassign_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021110901) {
        // Define field activity to be added to qbassign.
        $table = new xmldb_table('qbassign');
        $field = new xmldb_field('activity', XMLDB_TYPE_TEXT, null, null, null, null, null, 'alwaysshowdescription');

        // Conditionally launch add field activity.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('activityformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'activity');

        // Conditionally launch add field activityformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timelimit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'cutoffdate');

        // Conditionally launch add field timelimit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('submissionattachments', XMLDB_TYPE_INTEGER, '2',
            null, XMLDB_NOTNULL, null, '0', 'activityformat');

        // Conditionally launch add field submissionattachments.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('qbassign_submission');
        $field = new xmldb_field('timestarted', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');

        // Conditionally launch add field timestarted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field timelimit to be added to qbassign_overrides.
        $table = new xmldb_table('qbassign_overrides');
        $field = new xmldb_field('timelimit', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'cutoffdate');

        // Conditionally launch add field timelimit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // qbassign savepoint reached.
        upgrade_mod_savepoint(true, 2021110901, 'qbassign');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022071300) {
        // The most recent qbassign submission should always have latest = 1, we want to find all records where this is not the case.
        // Find the records with the maximum timecreated for each qbassign and user combination where latest is also 0.
        $sqluser = "SELECT s.id
                      FROM {qbassign_submission} s
                     WHERE s.timecreated = (
                          SELECT  MAX(timecreated) timecreated
                            FROM {qbassign_submission} sm
                           WHERE s.qbassignment = sm.qbassignment
                                 AND s.userid = sm.userid
                                 AND sm.groupid = 0)
                           AND s.groupid = 0
                           AND s.latest = 0";
        $idstofixuser = $DB->get_records_sql($sqluser, null);

        $sqlgroup = "SELECT s.id
                       FROM {qbassign_submission} s
                      WHERE s.timecreated = (
                          SELECT  MAX(timecreated) timecreated
                            FROM {qbassign_submission} sm
                           WHERE s.qbassignment = sm.qbassignment
                                 AND s.groupid = sm.groupid
                                 AND sm.groupid <> 0)
                            AND s.groupid <> 0
                            AND s.latest = 0";
        $idstofixgroup = $DB->get_records_sql($sqlgroup, null);

        $idstofix = array_merge(array_keys($idstofixuser), array_keys($idstofixgroup));

        if (count($idstofix)) {
            [$insql, $inparams] = $DB->get_in_or_equal($idstofix);
            $DB->set_field_select('qbassign_submission', 'latest', 1, "id $insql", $inparams);
        }

        // qbassignment savepoint reached.
        upgrade_mod_savepoint(true, 2022071300, 'qbassign');
    }
    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
