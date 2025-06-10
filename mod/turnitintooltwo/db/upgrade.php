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
 * @package   turnitintooltwo
 * @copyright 2010 iParadigms LLC
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_turnitintooltwo_upgrade($oldversion) {

    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Do necessary DB upgrades here.
    if ($oldversion < 2014012401) {
        $table = new xmldb_table('turnitintooltwo');
        $field = new xmldb_field('allownonor', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'rubric');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $table = new xmldb_table('turnitintooltwo_submissions');
        $field1 = new xmldb_field('submission_acceptnothing', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, null, 0, 'submission_transmatch');
        $field2 = new xmldb_field('submission_orcapable', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, null, 0, 'submission_acceptnothing');
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }
        upgrade_mod_savepoint(true, 2014012401, 'turnitintooltwo');
    }

    if ($oldversion < 2014012404) {
        $table = new xmldb_table('turnitintooltwo_users');
        $field = new xmldb_field('user_agreement_accepted', XMLDB_TYPE_INTEGER, '1', null,
            XMLDB_NOTNULL, null, 0, 'instructor_rubrics');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2014012404, 'turnitintooltwo');
    }

    if ($oldversion < 2014012405) {
        $table = new xmldb_table('turnitintooltwo');
        $field = new xmldb_field('submitted', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'anon');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add new indexes to tables.
        $table = new xmldb_table('turnitintooltwo_parts');
        $index = new xmldb_index('turnitintooltwoid', XMLDB_INDEX_NOTUNIQUE, array('turnitintooltwoid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('tiiassignid', XMLDB_INDEX_NOTUNIQUE, array('tiiassignid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('turnitintooltwo_courses');
        $index = new xmldb_index('courseid-course_type', XMLDB_INDEX_NOTUNIQUE, array('courseid', 'course_type'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('turnitintooltwo_peermarks');
        $index = new xmldb_index('parent_tii_assign_id', XMLDB_INDEX_NOTUNIQUE, array('parent_tii_assign_id'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('tiiassignid', XMLDB_INDEX_NOTUNIQUE, array('tiiassignid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2014012405, 'turnitintooltwo');
    }

    if ($oldversion < 2014012412) {
        $table = new xmldb_table('turnitintooltwo');
        $field = new xmldb_field('needs_updating', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'allownonor');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2014012412, 'turnitintooltwo');
    }

    if ($oldversion < 2015040101) {
        $table = new xmldb_table('turnitintooltwo_parts');
        $field = new xmldb_field('unanon', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'migrated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('submitted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'unanon');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('turnitintooltwo_submissions');
        $index = new xmldb_index('submission_objectid', XMLDB_INDEX_NOTUNIQUE, array('submission_objectid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2015040101, 'turnitintooltwo');
    }

    if ($oldversion < 2015040104) {
        $table = new xmldb_table('turnitintooltwo_users');
        // Alter datatype of user_agreement_accepted.
        $field = new xmldb_field('user_agreement_accepted', XMLDB_TYPE_INTEGER, '1', false,
            XMLDB_NOTNULL, null, 0, 'instructor_rubrics');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        } else {
            $dbman->change_field_unsigned($table, $field);
        }
        upgrade_mod_savepoint(true, 2015040104, 'turnitintooltwo');
    }

    if ($oldversion < 2015040107) {
        $table = new xmldb_table('turnitintooltwo');
        // Add field for institution check.
        $field = new xmldb_field('institution_check', XMLDB_TYPE_INTEGER, '1', false, false, null, null, 'journalcheck');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2015040107, 'turnitintooltwo');
    }

    if ($oldversion < 2015040109) {
        // Update URL for UK accounts.
        $apiurl = get_config('turnitintooltwo', 'apiurl');
        $newurl = str_replace('submit.ac.uk', 'api.turnitinuk.com', strtolower($apiurl));
        set_config('apiurl', $newurl, 'turnitintooltwo');
        upgrade_mod_savepoint(true, 2015040109, 'turnitintooltwo');
    }

    if ($oldversion < 2015040111) {
        // Update gradedisplay value to be consistent with V1 plugin.
        $DB->set_field("turnitintooltwo", "gradedisplay", 2);
        upgrade_mod_savepoint(true, 2015040111, 'turnitintooltwo');
    }

    if ($oldversion < 2016011101) {
        $table = new xmldb_table('turnitintooltwo');
        // Add field for whether or not the OR should be synced to the gradebook.
        $field = new xmldb_field('syncreport', XMLDB_TYPE_INTEGER, '1', false, true, false, '0', 'needs_updating');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add field to transfer grades to gradebook for anonymous assignments.
        $field = new xmldb_field('anongradebook', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'syncreport');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2016011101, 'turnitintooltwo');
    }

    if ($oldversion < 2016011107) {
        $table = new xmldb_table('turnitintooltwo_parts');
        // Add timestamp to store when grades were last updated.
        $field = new xmldb_field('gradesupdated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'submitted');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2016011107, 'turnitintooltwo');
    }

    if ($oldversion < 2017011301) {
        // Grab any duplicated submission rows.
        $query = "SELECT
            sb.id,
            sb.submission_objectid AS objectid,
            sb.userid AS userid,
            mu.firstname AS firstname,
            mu.lastname AS lastname,
            sb.submission_grade AS grade,
            cm.course AS courseid,
            tu.id AS activityid,
            cm.id AS cmid,
            tp.id AS partid
            FROM ".$CFG->prefix."turnitintooltwo_submissions sb
            LEFT JOIN ".$CFG->prefix."user mu ON mu.id = sb.userid
            LEFT JOIN ".$CFG->prefix."turnitintooltwo_parts tp ON tp.id = sb.submission_part
            LEFT JOIN ".$CFG->prefix."turnitintooltwo tu ON tu.id = tp.turnitintooltwoid
            LEFT JOIN ".$CFG->prefix."course_modules cm ON tp.turnitintooltwoid = cm.instance
            LEFT JOIN ".$CFG->prefix."modules mo ON mo.id = cm.module
            WHERE submission_objectid IS NOT NULL
            AND mo.name = ?
            AND sb.submission_objectid IN (
                SELECT submission_objectid FROM ".$CFG->prefix."turnitintooltwo_submissions
                GROUP BY userid, turnitintooltwoid, submission_objectid
                HAVING COUNT(1) > 1
            )
            ORDER BY sb.id ASC";
        $duplicates = $DB->get_records_sql($query, array('turnitintooltwo'));

        // Dump the results of query into a csv.
        $tempdir = make_temp_directory('turnitintooltwo');
        $filename = 'duplicate_submissions_'. time() . '.csv';
        try {
            $file = $tempdir . DIRECTORY_SEPARATOR . $filename;

            $fh = fopen($file, "w");
            $headers = 'Submission Record Id, Turnitin Paper Id, User Id, User Lastname, User Firstname, Grade, Course Id, Course Module Id, Part Id';
            fwrite($fh, $headers.PHP_EOL);
            foreach ($duplicates as $duplicate) {
                fwrite($fh, $duplicate->id.','.$duplicate->objectid.','.$duplicate->userid.','.$duplicate->lastname.','.$duplicate->firstname.','.$duplicate->grade.','.$duplicate->courseid.','.$duplicate->cmid.','.$duplicate->partid.PHP_EOL);
            }
            fclose($fh);
        } catch (Exception $e) {
            turnitintooltwo_activitylog("Could not create file to log duplicated submissions","UPGRADE");
        }

        // Add new column that has to be unique.
        $table = new xmldb_table('turnitintooltwo_submissions');
        $field = new xmldb_field('submission_hash', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'submission_orcapable');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Retrospectively update the new column to be id for previous submissions.
        $DB->execute("UPDATE ".$CFG->prefix."turnitintooltwo_submissions SET submission_hash = id WHERE submission_hash IS NULL");

        // Add hash as key after update.
        $key = new xmldb_key('submission_hash', XMLDB_KEY_UNIQUE, array('submission_hash'));
        $dbman->add_key($table, $key);

        upgrade_mod_savepoint(true, 2017011301, 'turnitintooltwo');
    }

    if ($oldversion < 2017103001) {
        $table = new xmldb_table('turnitintooltwo');
        // Add field for flagging whether a V2 assignment is a legacy V1 assignment.
        $field = new xmldb_field('legacy', XMLDB_TYPE_INTEGER, '1', false, null, false, '0', 'anongradebook');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('turnitintooltwo_submissions');
        $field = new xmldb_field('migrate_gradebook', XMLDB_TYPE_INTEGER, '1', false, true, false, '0', 'submission_hash');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2017103001, 'turnitintooltwo');
    }

    // This block is to solve a number of inconsistencies between the install and upgrade scripts
    if ($oldversion < 2020081401) {
        $table = new xmldb_table('turnitintooltwo');
        // Ensure default for institution check is 0.
        $field = new xmldb_field('institution_check', XMLDB_TYPE_INTEGER, '1', false, false, null, 0, 'journalcheck');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Ensure needs_updating allows null and int length is 1.
        $field = new xmldb_field('needs_updating', XMLDB_TYPE_INTEGER, '1', null, false, null, 0, 'allownonor');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
            $dbman->change_field_precision($table, $field);
        }

        $table = new xmldb_table('turnitintooltwo_parts');
        // Ensure unanon length is 1.
        $field = new xmldb_field('unanon', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'migrated');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }
        // Ensure submitted length is 1.
        $field = new xmldb_field('submitted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'unanon');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        $table = new xmldb_table('turnitintooltwo_users');
        // Ensure user_agreement_accepted allows null.
        $field = new xmldb_field('user_agreement_accepted', XMLDB_TYPE_INTEGER, '1', false, false, null, 0, 'instructor_rubrics');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }
        upgrade_mod_savepoint(true, 2020081401, 'turnitintooltwo');
    }

    if ($oldversion < 2021060801) {
        // Drop unused fields
        $table = new xmldb_table('turnitintooltwo_submissions');
        $field = new xmldb_field('submission_status');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('submission_queued');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Remove erater settings
        set_config('useerater', null, 'turnitintooltwo');
        set_config('default_erater', null, 'turnitintooltwo');
        set_config('default_erater_handbook', null, 'turnitintooltwo');
        set_config('default_erater_dictionary', null, 'turnitintooltwo');
        set_config('default_erater_spelling', null, 'turnitintooltwo');
        set_config('default_erater_grammar', null, 'turnitintooltwo');
        set_config('default_erater_usage', null, 'turnitintooltwo');
        set_config('default_erater_mechanics', null, 'turnitintooltwo');
        set_config('default_erater_style', null, 'turnitintooltwo');

        upgrade_mod_savepoint(true, 2021060801, 'turnitintooltwo');
    }

    // Need to drop these again in case they weren't in previous upgrade.
    if ($oldversion < 2021073001) {
        // Drop unused fields
        $table = new xmldb_table('turnitintooltwo_submissions');
        $field = new xmldb_field('submission_status');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('submission_queued');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2021073001, 'turnitintooltwo');
    }

    return true;
}
