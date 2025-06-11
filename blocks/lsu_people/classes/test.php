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
 * @package    block_lsu_people
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsu_people\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use moodle_url;
use flexible_table;
use html_writer;

class lsu_people implements renderable {

    protected $courseid;
    protected $download;
    protected $group;

    public function __construct($courseid, $download = '', $group = '') {
        $this->courseid = $courseid;
        $this->download = $download;
        $this->group = $group;
    }

    public function export_for_template(renderer_base $output) {
        global $CFG, $DB;

        // We need the table libraries.
        require_once($CFG->libdir . '/tablelib.php');

        // Get the course object.
        $course = get_course($this->courseid);

        // Set the parms for courseid to use in SQL.
        $parms = ['courseid' => $course->id];

        // Set the groupsql to nothing.
        $groupsql = '';

        // Set groupappend to nothing.
        $groupappend = '';

        // Set the base groupid to 0.
        $groupid = 0;

        // If we have a group that is not 0.
        if (!empty($this->group) && $this->group > 0) {

            // Set the groupid to the specified value/
            $groupid = $this->group;

            // Build this out for naming.
            $groupappend = '_' . $this->group;

            // Set the groupid for the SQL.
            $parms['groupid'] = $this->group;

            // Build out the SQL for later.
            $groupsql = "AND g.id = :groupid";
        }

        // Instantiate the new table.
        $table = new \flexible_table('lsu_people_roster');

        // Set the base url.
        $baseurl = new \moodle_url('/blocks/lsu_people/view.php', ['id' => $course->id, 'group' => $groupid]);

        // Define the base url for the table.
        $table->define_baseurl($baseurl);

        // Define the columns.
        $table->define_columns([
            'firstname',
            'middlename',
            'lastname',
            'universal_id',
            'email',
            'section',
            'credit_hrs',
            'major',
            'college',
            'expected_completion',
            'ferpa_status',
            'degree_candidacy'
        ]);

        // Define the column headers.
        $table->define_headers([
            'First Name',
            'Middle Name',
            'Last Name',
            'Universal ID',
            'Email',
            'Section',
            'Credit Hours',
            'Major',
            'College',
            'Expected Completion Date',
            'Ferpa Hold',
            'Degree Candidacy'
        ]);

        // Do this BEFORE setup to prevent output buffering issues.
        if ($this->download) {

            // Get rid of str_replace deprecated warnings.
            set_error_handler(function () {}, E_DEPRECATED);

            // Generate a url safe course identifier.
            $coursename = rawurlencode($course->idnumber);

            // Set the table to be the downloader.
            $table->is_downloading($this->download, $coursename . $groupappend , 'LSU Course Roster');
        }

        // Set the table as sortable with lastname being the default.
        $table->sortable(true, 'lastname');

        // Set the class.
        $table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');

        // Set the table as downloadable.
        $table->is_downloadable(true);

        // Build out the download buttons and options.
        $table->show_download_buttons_at([TABLE_P_BOTTOM]);

        // Set up the table.
        $table->setup();

        // Define the sort.
        $sortcolumn = $table->get_sql_sort();

        // Define the sort SQL for inclusion in the SQL with a default in case.
        $sortsql = $sortcolumn ? "ORDER BY $sortcolumn" : "ORDER BY lastname ASC";

        // Build out the SQL.
        $sql = "SELECT
            stu.id,
            CONCAT(
                cou.course_subject_abbreviation, ' ',
                course_number, ' ',
                sec.section_number
            ) AS section,
            COALESCE(NULLIF(stu.preferred_firstname, ''), stu.firstname) AS firstname,
            COALESCE(stu.middlename, '') AS middlename,
            COALESCE(NULLIF(stu.preferred_lastname, ''), stu.lastname) AS lastname,
            stu.universal_id,
            stu.email,
            CONCAT(
                cou.course_subject_abbreviation, ' ',
                cou.course_number, ' ',
                sec.section_number
            ) AS section,
            stuenr.credit_hrs,
            MAX(CASE
                WHEN sm.datatype = 'Program_of_Study_Code' THEN
                (SELECT program_of_study FROM {enrol_wds_programs}
                    WHERE program_of_study_code = sm.data)
            END) AS major,
            MAX(CASE
                WHEN sm.datatype = 'Academic_Unit_Code' THEN
                (SELECT academic_unit FROM {enrol_wds_units}
                    WHERE academic_unit_code = sm.data)
            END) AS college,
            MAX(CASE
                WHEN sm.datatype = 'Expected_Completion_Date'
                THEN sm.data
            END) AS expected_completion,
            COALESCE(
                MAX(CASE
                    WHEN sm.datatype = 'Buckley_Hold'
                    THEN IF(sm.data = '1', 'FERPA Hold', '-')
                END),
            '-') AS ferpa_status,
            MAX(CASE
                WHEN sm.datatype = 'Degree_Candidacy'
                THEN IF(sm.data = '1', 'Graduating', '-')
            END) AS degree_candidacy
        FROM {enrol_wds_students} stu
        INNER JOIN {enrol_wds_student_enroll} stuenr
            ON stuenr.universal_id = stu.universal_id
        INNER JOIN {enrol_wds_sections} sec
            ON sec.section_listing_id = stuenr.section_listing_id
        INNER JOIN {enrol_wds_courses} cou
            ON cou.course_definition_id = sec.course_definition_id
        INNER JOIN {course} c
            ON c.idnumber = sec.idnumber
        INNER JOIN {groups} g
            ON g.courseid = c.id
            AND g.name = CONCAT(
                cou.course_subject_abbreviation, ' ',
                cou.course_number, ' ',
                sec.section_number)
        LEFT JOIN {enrol_wds_students_meta} sm
            ON sm.studentid = stu.id
            AND sm.academic_period_id = sec.academic_period_id
        WHERE c.id = :courseid
            $groupsql
        GROUP BY stu.id
        $sortsql";

        // Get the data.
        $students = $DB->get_records_sql($sql, $parms);

        // Loop through the students.
        foreach ($students as $student) {

            // Build out the email link if we're viewing on screen.
            $email = $table->is_downloading()
                ? $student->email
                : html_writer::link("mailto:$student->email", $student->email);

            // Build out the row.
            $row = [
                $student->firstname,
                $student->middlename,
                $student->lastname,
                $student->universal_id,
                $email,
                $student->section,
                $student->credit_hrs,
                $student->major,
                $student->college,
                $student->expected_completion,
                $student->ferpa_status,
                $student->degree_candidacy
            ];

            // FERPA highlighting only for web display.
            if (!$table->is_downloading() && $student->ferpa_status == 'FERPA Hold') {

                // Inject the span and class for ferpa protected folks
                $row = array_map(fn($val) => '<span class="ferpa">' . $val . '</span>', $row);
            }

            // Add the row to the table for this student.
            $table->add_data($row);
        }

        // Finish export and exit before sending any output.
        if ($table->is_downloading()) {
            $table->finish_output();
            exit;
        }

        // Start the output buffering.
        ob_start();

        // This is a nasty way to avoid the html being littered with str_replace deprecation warnings.
        @$table->print_html();

        // Return the data.
        return (object)[
            'tablehtml' => ob_get_clean()
        ];
    }
}
