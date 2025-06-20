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
 * @package    enrol_workdaystudent
 * @copyright  2025 onwards LSU Online & Continuing Education
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class workdaystudent_helper {
    /**
     * Converts a date string (e.g., from Workday) to a Unix timestamp.
     *
     * @param string $datestring The date string to convert.
     * @return int|null Unix timestamp or null on failure or empty input.
     */
    public static function dateconv($datestring) {
        if (empty($datestring)) {
            return null;
        }

        // Attempt to parse common ISO 8601 like formats.
        try {
            $dt = new \DateTime($datestring);
            return $dt->getTimestamp();
        } catch (\Exception $e) {

            // Fallback for dates like 'YYYY-MM-DD' if the above fails without time.
            try {
                $dt = \DateTime::createFromFormat('Y-m-d', $datestring);
                if ($dt) {

                    // Ensure it's treated as start of day in UTC for consistency if time is not present.
                    $dt->setTime(0, 0, 0); 
                    return $dt->getTimestamp();
                }
            } catch (\Exception $e2) {

                // Log error.
                mtrace("Workday Student Helper: Failed to parse date string: " . $datestring . " Error: " . $e2->getMessage());
                return null;
            }
            mtrace("Workday Student Helper: Failed to parse date string (unknown format): " . $datestring);
            return null;
        }
    }

    /**
     * Fetches student name details from the local database using universal_id.
     * Prioritizes preferred names if available.
     *
     * @param string $universalid The Universal ID of the student.
     * @return object|null An object with firstname, middlename, lastname properties, or a placeholder/null if not found.
     */
    public static function get_student_details_by_universal_id($universalid) {
        global $DB;

        if (empty($universalid)) {
            return null;
        }

        $table = 'enrol_wds_students'; 

        if (!$DB->table_exists($table)) {
            mtrace("Workday Student Helper: Table '{$table}' does not exist. Cannot fetch student details for UID: {$universalid}.");
            return (object)['firstname' => 'DB Setup Issue', 'lastname' => '(UID: ' . $universalid . ')', 'middlename' => ''];
        }

        $sql = "SELECT firstname,
                middlename,
                lastname,
                preferred_firstname,
                preferred_lastname
            FROM {{$table}}
            WHERE universal_id = :universalid";
        
        $params = ['universalid' => $universalid];

        try {
            $studentrecord = $DB->get_record_sql($sql, $params);
        } catch (\Exception $e) {
            mtrace("Workday Student Helper: Error fetching student details for UID {$universalid}: " . $e->getMessage());
            return (object)['firstname' => 'DB Query Error', 'lastname' => '(UID: ' . $universalid . ')', 'middlename' => ''];
        }

        if ($studentrecord) {
            $details = new \stdClass();
            $details->firstname = !empty($studentrecord->preferred_firstname) ?
                $studentrecord->preferred_firstname :
                $studentrecord->firstname;
            $details->middlename = isset($studentrecord->middlename) ?
                $studentrecord->middlename :
                 '';
            $details->lastname = !empty($studentrecord->preferred_lastname) ?
                $studentrecord->preferred_lastname :
                $studentrecord->lastname;

            return $details;
        } else {
            mtrace("Workday Student: Student with UID {$universalid} not found in {$table}.");
            return (object)['firstname' => 'Unknown', 'lastname' => 'User (UID: ' . $universalid . ')', 'middlename' => ''];
        }
    }

    /**
     * Fetches historical enrollment records for a given Moodle course ID from local DB tables.
     *
     * @param int $courseid The Moodle course ID.
     * @return array An array of enrollment records, or an empty array on failure/no records.
     */
    public static function get_historical_enrollments_for_course($courseid) {
        global $DB;

        if (empty($courseid)) {
            mtrace("Workday Student: Course ID is empty. Cannot fetch historical enrollments.");
            return [];
        }

        $sql = "SELECT senr.universal_id AS Universal_Id,
                       senr.registered_date AS Registered_Date,
                       senr.drop_date AS Drop_Date,
                       senr.status,
                       senr.prevstatus,
                       senr.registration_status
                  FROM {enrol_wds_student_enroll} senr
                  JOIN {enrol_wds_sections} sec ON sec.section_listing_id = senr.section_listing_id
                 WHERE sec.moodle_course_id = :courseid
              ORDER BY senr.registered_date DESC, senr.universal_id ASC";

        $params = ['courseid' => $courseid];

        try {
            $enrollments = $DB->get_records_sql($sql, $params);
            return array_values($enrollments);
        } catch (\Exception $e) {
            mtrace("Workday Student: Error fetching historical enrollments for course ID {$courseid}: " . $e->getMessage());
            return [];
        }
    }
}
