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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\messenger\message\data_mapper;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_string;

trait maps_course_data {

    public function get_data_coursefullname() {
        return $this->get_course_prop('fullname');
    }

    public function get_data_courseshortname() {
        return $this->get_course_prop('shortname');
    }

    public function get_data_courseidnumber() {
        return $this->get_course_prop('idnumber');
    }

    public function get_data_coursesummary() {
        return $this->get_course_prop('summary');
    }

    public function get_data_coursestartdate() {
        return $this->format_mapped_date($this->get_course_prop('startdate'));
    }

    public function get_data_courseenddate() {
        return $this->format_mapped_date($this->get_course_prop('enddate'));
    }

    public function get_data_courselink() {
        return new \moodle_url('/course/view.php', ['id' => $this->course->id]);
    }

    public function get_data_courselastaccess() {
        global $DB;

        if (!$lastaccesstime = $DB->get_field('user_lastaccess',
                                              'timeaccess',
                                              ['userid' => $this->user->id, 'courseid' => $this->course->id],
                                              IGNORE_MISSING)) {
                                                  return block_quickmail_string::get('courseneveraccessed');
        }

        return $this->format_mapped_date($lastaccesstime);
    }

    /*
     * Returns student-specific start date for this course
     *
     * Thanks to Ben H.
     */
    public function get_data_studentstartdate() {
        global $DB;

        $sql = "SELECT ue.timestart
                FROM {course} AS c
                JOIN {enrol} AS en ON en.courseid = c.id
                JOIN {user_enrolments} AS ue ON ue.enrolid = en.id
                JOIN {user} AS u ON ue.userid = u.id
                JOIN {context} AS ct ON c.id = ct.instanceid
                JOIN {role_assignments} AS ra ON ra.contextid = ct.id
                WHERE u.id = ?
                AND c.id = ?
                AND ra.roleid = 5
                AND ra.userid = u.id
                AND ra.userid = ue.userid";

        if (!$studentstartdate = $DB->get_field_sql($sql, [$this->user->id, $this->course->id])) {
            return '--';
        }

        return $this->format_mapped_date($studentstartdate);
    }

    /*
     * Returns student-specific end date for this course
     *
     * Thanks to Ben H.
     */
    public function get_data_studentenddate() {
        global $DB;

        $sql = "SELECT ue.timeend
                FROM {course} AS c
                JOIN {enrol} AS en ON en.courseid = c.id
                JOIN {user_enrolments} AS ue ON ue.enrolid = en.id
                JOIN {user} AS u ON ue.userid = u.id
                JOIN {context} AS ct ON c.id = ct.instanceid
                JOIN {role_assignments} AS ra ON ra.contextid = ct.id
                WHERE u.id = ?
                AND c.id = ?
                AND ra.roleid = 5
                AND ra.userid = u.id
                AND ra.userid = ue.userid";

        if (!$studentenddate = $DB->get_field_sql($sql, [$this->user->id, $this->course->id])) {
            return '--';
        }

        return $this->format_mapped_date($studentenddate);
    }

    private function get_course_prop($prop) {
        return $this->course->$prop;
    }

}
