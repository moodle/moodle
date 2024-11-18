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
 * @package   local_report_attendance
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class attendancerep{

    // Check the user and the companyid are allowed.
    public function confirm_user_company( $user, $companyid ) {
        global $DB;

        // Companyid is defined?
        if ($companyid == 0) {
            return true;
        }

        // User must either be in the companymanager table for THIS company
        // or not at all.
        if ($companies = $DB->get_records('companymanager', array('userid' => $user->id))) {
            foreach ($companies as $company) {
                if ($company->companyid == $companyid) {
                    return true;
                }
            }

            // If we get this far then there's a problem.
            return false;
        }

        // Not in table, so that's fine.
        return true;
    }

    // Create the select list of courses.
    static public function courseselectlist($companyid=0) {
        global $DB;
        global $SITE;

        // Create "empty" array.
        $courseselect = array();

        // If the companyid=0 then there's no courses.
        if ($companyid == 0) {
            return $courseselect;
        }

        // Get courses for given company.
        if (!$companycourses = $DB->get_records('company_course', array('companyid' => $companyid),
                                                                        null, 'courseid')) {
            return $courseselect;
        } else {
            $companyselect = " course in (".implode(',', array_keys($companycourses)).")";
        }

        if (!$classmodinfo = $DB->get_record('modules', array('name' => 'trainingevent'))) {
            return $courseselect;
        }
        if (!$courses = $DB->get_records_sql("SELECT DISTINCT course FROM {course_modules}
                                              WHERE module=$classmodinfo->id AND $companyselect")) {
            return $courseselect;
        }
        // Get the course names and put them in the list.
        foreach ($courses as $course) {
            if ($course->course == $SITE->id) {
                continue;
            }
            $coursefull = $DB->get_record('course', array('id' => $course->course));
            $courseselect[$coursefull->id] = format_string($coursefull->fullname, true, 1);
        }
        return $courseselect;
    }

}
