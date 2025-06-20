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

/**
 * Class enrollment_tracker_data
 *
 * Handles data fetching and processing for the Enrollment Tracker page.
 */
class enrollment_tracker_data {

    /**
     * Fetches student name details from the local database using universal_id.
     * Prioritizes preferred names if available.
     *
     * @param @string $universalid The Universal ID of the student.
     * @return @object or @null An @object with firstname, middlename, lastname properties, or null if not found/error.
     */
    public static function get_student_name_details(string $universalid) {
        global $DB;
        if (empty($universalid)) {
            return null;
        }
        $table = 'enrol_wds_students';

        $sql = "SELECT firstname,
                middlename,
                lastname,
                preferred_firstname,
                preferred_lastname
            FROM {{$table}}
            WHERE universal_id = :universalid";
        try {
            $record = $DB->get_record_sql($sql, ['universalid' => $universalid]);
            if ($record) {
                $details = new \stdClass();
                $details->firstname = !empty($record->preferred_firstname) ?
                    $record->preferred_firstname :
                    $record->firstname;
                $details->middlename = isset($record->middlename) ?
                    $record->middlename :
                    '';
                $details->lastname = !empty($record->preferred_lastname) ?
                    $record->preferred_lastname :
                    $record->lastname;

                return $details;
            }

            return null;
        } catch (\Exception $e) {
            mtrace("Error in get_student_name_details for UID {$universalid}: " . $e->getMessage());
            return (object)['firstname' => 'Error', 'lastname' => "(UID: {$universalid})", 'middlename' => ''];
        }
    }

    /**
     * Fetches all historical enrollment records for a given Moodle course ID.
     *
     * @param @int $courseid The Moodle course ID.
     * @return @array An array of enrollment objects, or an empty array if none found/error.
     * Each @object should contain universal_id, lastname (for sorting), registered_date (timestamp), drop_date (timestamp).
     */
    public static function get_historical_enrollments(int $courseid, $sort) {
        global $DB;

        if (empty($courseid)) {
            mtrace("enrollment_tracker_data: Course ID is empty, cannot fetch historical enrollments.");
            return [];
        }

        if (isset($sort['field'])) {
            $sortorder = $sort['dir'] == 3 ? 'ASC' : 'DESC';
            $studentfield = $sort['field'] == 'studentname' ?
                $sortfield = 'lastname' :
                $sortfield = $sort['field'];

            $order = "ORDER BY $sortfield $sortorder, lastname ASC, firstname ASC";
        } else {
            $order = '';
        }

        $sql = "SELECT senr.universal_id,
                       coalesce(stu.preferred_lastname, stu.lastname) AS lastname,
                       sec.section_number,
                       senr.registered_date,
                       senr.drop_date,
                       senr.status, 
                       senr.prevstatus,
                       senr.registration_status
                  FROM {enrol_wds_student_enroll} senr
                  INNER JOIN {enrol_wds_sections} sec ON sec.section_listing_id = senr.section_listing_id
                  INNER JOIN {enrol_wds_students} stu ON stu.universal_id = senr.universal_id
                 WHERE sec.moodle_status = :courseid" .
                 $order;
        
        try {
            $records = $DB->get_records_sql($sql, ['courseid' => $courseid]);

            return $records;
        } catch (\Exception $e) {
            mtrace("Error in get_historical_enrollments for course ID {$courseid}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Optional: Formats a UNIX timestamp for display according to user's timezone and Moodle settings.
     *
     * @param @int $timestamp The UNIX timestamp.
     * @return @string The formatted date string, or a placeholder if timestamp is empty.
     */
    public static function format_date_for_display(int $timestamp) {
        if (empty($timestamp)) {
            return '-';
        }

        $format = "m/d/Y h:i A";

        // Set timezone to Central Time (US).
        date_default_timezone_set('America/Chicago');

        $dtime = date($format, $timestamp);

        return $dtime;
    }
}
