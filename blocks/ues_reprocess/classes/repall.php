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
 * Reprocess All Tool
 * @package    block_ues_reprocess
 * @copyright  Louisiana State University
 * @copyright  The guy who did stuff: David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/enrol/ues/publiclib.php');

class repall {

    public function get_semesters() {
        // Get the list of semesters from config.
        $sems = get_config('ues_reprocess', "sems");

        // Return the data.
        return $sems;
    }

    public function get_departments() {
        // Get the list of categories from config.
        $depts = get_config('ues_reprocess', "cats");

        // Return the data.
        return $depts;
    }

    public function get_courses($semesters, $departments) {
        global $DB;

        // Build the SQL.
        $sql = "SELECT c.* FROM {course} c
                INNER JOIN {enrol_ues_sections} sec ON sec.idnumber = c.idnumber
                    AND sec.idnumber IS NOT NULL
                    AND sec.idnumber != ''
                WHERE c.category IN (" . $departments . ")
                    AND sec.semesterid IN (" . $semesters . ")
                GROUP BY c.id ORDER BY RAND()";
        
        // Fetch the data.
        $course_list = $DB->get_records_sql($sql);

        // Return the array of objects.
        return $course_list;
    }

    public static function unenroll_users($course) {
        global $DB;

        // Build the SQL to change the status of students for a course in the ues DB.
        $sql = "UPDATE {enrol_ues_students} stu
               INNER JOIN {enrol_ues_sections} sec ON stu.sectionid = sec.id
               INNER JOIN {course} c ON c.idnumber = sec.idnumber
                   AND sec.idnumber IS NOT NULL
                   AND sec.idnumber != ''
               SET stu.status = ''
               WHERE c.id = " . $course->id;
               // AND sec.status = 'manifested'

        // Execute the SQL.
        $unenrolled = $DB->execute($sql);

        return $unenrolled;
    }

    public static function get_sections($course) {
        global $DB;

        // Set the table.
        $table = 'enrol_ues_sections';

        // Get the sections matching the course idnumber.
        $sections = $DB->get_records($table, array('idnumber' => $course->idnumber));

        // Return them.
        return $sections;
    }

    public static function set_reprocessed($section, $reprocessed) {
        global $DB;

        // Set the table.
        $table = 'enrol_ues_sectionmeta';

        // Build the dataobject.
        $dataobj = new stdClass;

        // Populate it.
        $dataobj->sectionid = $section->id;
        $dataobj->name = "section_reprocessed";
        $dataobj->value = 1;

        // Check it 1st.
        $exists = $DB->record_exists($table,
            array(
                'sectionid' => $section->id,
                'name' => "section_reprocessed",
                'value' => 1
            )
        );

        // Set this up for future use.
        $innit = false;

        // Log some stuff for now and set innit to true if we find it.
        foreach($reprocessed as $data) {
            if ($data->id == $section->id) {
                mtrace("Reprocessed id: $data->id with value $data->value.");
                $innit = true;
                break;
            }
        }

        // If it's not there, add it.
        if (!$exists && $innit) {
            // Insert a record.
            if ($DB->insert_record($table, $dataobj, $returnid = true)) {
                $treprocessed = true;
            } else {
                $treprocessed = false;
            }
        } else { 
            $treprocessed = false;
        }

        // Return it.
        return $treprocessed;
    }

    public static function get_reprocessed($course) {
        global $DB;

        // Set the table.
        $table = 'enrol_ues_sectionmeta';

        // Get sections.
        $sections = self::get_sections($course);

        // Counte the sections.
        $count = count($sections);

        // Set up the challenge.
        $challenge = 0;

        // Loop through the sections to check if it exists.
        foreach ($sections as $section) {
            $exists = $DB->record_exists($table,
                array(
                    'sectionid' => $section->id,
                    'name' => 'section_reprocessed',
                    'value' => 1
                )
            );

            // If a section exists, increment the challenge.
            if ($exists) {
                $challenge++;
            }
        }

        // If we have the same number existing that we expected to exist, return true.
        if ($count == $challenge) {
            return true;
        } else {
            return false;
        }
    }

    public function run_it_all_task() {
        global $CFG;

        // Get the required libs.
        require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
        require_once(dirname(__DIR__).'/lib.php');

        // Get the data access objects from UES.
        ues::require_daos();

        // Make sure we are logged in.
        // require_login();

        // Get the list of semesters.
        $semesters = self::get_semesters();

        // Get the list of departments.
        $departments = self::get_departments();

        // Get the courses.
        $courses = self::get_courses($semesters, $departments);

        // Loop through them.
        foreach($courses as $course) {
            $coursestarttime = microtime(true);

            // Do not process non-teaching courses.
            if ($course->id < 2) {
                continue;
            }

            // See if this course has already been reprocessed.
            $alreadydone = self::get_reprocessed($course);

            // If so, skip.
            if ($alreadydone) {
                continue;
            }

            // Get sections for future use.
            $sections = self::get_sections($course);

            // Prestage students in enrol_ues_students for the course.
            self::unenroll_users($course);

            // Output what we did.
            mtrace("Prestaged interstitial enrollments in: $course->fullname with ID: $course->id.");

            // Reprocess enrollment.
            $reprocessed = ues::repall_course($course);

            if (is_array($reprocessed)) {
                // Add an entry is in the sectionmeta table for each section.
                foreach ($sections as $section) {
                    $success = self::set_reprocessed($section, $reprocessed);
                }

                $coursefinishtime = microtime(true);
                $courseelapsedtime = round($coursefinishtime - $coursestarttime, 1);
                if ($success) {
                    // Log what we did.
                    mtrace("Successfully reprocessed course: $course->fullname in $courseelapsedtime seconds.\n\n");
                } else {
                    // Log what we didn't do.
                    mtrace("Failed to reprocess course: $course->fullname in $courseelapsedtime seconds.\n\n");
                }
            } else {
                $coursefinishtime = microtime(true);
                $courseelapsedtime = round($coursefinishtime - $coursestarttime, 1);

                // Log what we didn't do.
                mtrace("Failed to reprocess course: $course->fullname in $courseelapsedtime seconds.\n\n");
            }
        }
    }
}
