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
 * Capabilities for the Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Install script to clone data from UES sports grades viewer.
 *
 * @return @bool
 */
function xmldb_block_wds_sportsgrades_install() {
    global $DB;

    // Check if required table exists before proceeding.
    if (!table_enrol_sports_mentors_exists()) {
        mtrace('Required table enrol_sports_mentors does not exist. Skipping record cloning.');
        return true;
    }

    try {

        // Execute the query to fetch records.
        $records = fetch_sport_mentor_records();

        if (!empty($records)) {

            // Insert records into target table.
            $success = insert_sport_mentor_records($records);

            if ($success) {
                mtrace('Successfully inserted ' . count($records) . ' records into target table.');
                return true;
            } else {
                mtrace('Failed to insert records into target table.');
                return false;
            }
        } else {
            mtrace('No records found to insert.');
            return true;
        }

    } catch (Exception $e) {
        mtrace('Error during installation: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check if the enrol_sports_mentors table exists.
 *
 * @return bool True if table exists, false otherwise
 */
function table_enrol_sports_mentors_exists() {
    global $DB;

    $dbman = $DB->get_manager();
    $table = new xmldb_table('enrol_sports_mentors');

    return $dbman->table_exists($table);
}

/**
 * Fetch sport mentor records using the provided SQL query.
 *
 * @return @array Array of records
 */
function fetch_sport_mentor_records() {
    global $DB;

    // Build the SQL. THIS IS HARDCODED TO ME!!!
    $sql = "SELECT CONCAT(u.id, '_', s.id) AS uniquer,
            u.id AS userid,
            s.id AS sportid,
            UNIX_TIMESTAMP() AS timecreated,
            UNIX_TIMESTAMP() AS timemodified,
            30567 AS createdby,
            30567 AS modifiedby,
            u.firstname,
            u.lastname,
            u.username,
            s.code,
            s.name,
            FROM_UNIXTIME(u.lastaccess) AS lastaccess
        FROM {enrol_wds_sport} s
        INNER JOIN {enrol_sports_mentors} sm1 ON sm1.path = s.code
        INNER JOIN {user} u ON u.id = sm1.userid
        ORDER BY s.id ASC, sm1.userid ASC";

    $data = $DB->get_records_sql($sql);

    return $data;
}

/**
 * Insert sport mentor records into target table.
 *
 * @param @array $records Array of records to insert
 * @return @bool Success status
 */
function insert_sport_mentor_records($records) {
    global $DB;

    // Set the table.
    $table = 'block_wds_sportsgrades_access';

    try {

        // Loop through the data.
        foreach ($records as $record) {

            // Prepare record for insertion.
            $insertrecord = prepare_record_for_insertion($record);

            // let's be super extra safe here.
            $transaction = $DB->start_delegated_transaction();

            // Insert the record.
            $DB->insert_record($table, $insertrecord);

            // Commit the transaction when we're done.
            $transaction->allow_commit();
        }

        return true;

    } catch (Exception $e) {

        $transaction->rollback($e);

        return false;
    }
}

/**
 * Prepare record object for database insertion.
 *
 * @param @object $record Original record from query
 * @return @object Prepared record for insertion
 */
function prepare_record_for_insertion($record) {

    // Build the new record.
    $insertrecord = new stdClass();

    // Map fields from query result to target table fields.
    $insertrecord->userid = $record->userid;
    $insertrecord->sportid = $record->sportid;
    $insertrecord->timecreated = $record->timecreated;
    $insertrecord->timemodified = $record->timemodified;
    $insertrecord->createdby = $record->createdby;
    $insertrecord->modifiedby = $record->modifiedby;

    return $insertrecord;
}
