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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\models;

/**
 * Mixed functions to retrieve info from the DB.
 */
class xemixed {

    /**
     * Retrieve basic info about the course and it's group information.
     * @param  object containing the course id and name
     * @return array
     */
    public function get_course_group_data($params = false) {
        global $DB;

        $courseid = isset($params->courseid) ? $params->courseid : null;
        $coursename = isset($params->coursename) ? $params->coursename : null;
        $returnobj = new \stdClass();

        $coursedata = $DB->get_records_sql(
            'SELECT g.id as groupid, c.id, c.idnumber, c.shortname, g.name as groupname
            FROM mdl_course c, mdl_groups g
            WHERE c.id = g.courseid AND c.id = ?',
            array($courseid)
        );
        if (count($coursedata) == 0) {
            $returnobj->success = false;
            $returnobj->msg = "There are no groups for this course.";
            return $returnobj;
        } else {
            $returnobj->success = true;
            $returnobj->data = $coursedata;
            return $returnobj;
        }
    }

    /**
     * Retrieve basic info about the course and it's group information.
     * @param  object containing the course id and name
     * @return array
     */
    public function get_token_data($url = false) {
        global $DB;
        $returnobj = new \stdClass();

        if ($url == false ) {
            $returnobj->success = false;
            $returnobj->msg = "The token was not passed to the destination";
            return $returnobj;
        }

        $tokenresult = $DB->get_record_sql(
            'SELECT token from mdl_block_lsuxe_moodles where url=?',
            array($url)
        );

        if (strlen($tokenresult->token) < 32) {
            $returnobj->success = false;
            $returnobj->msg = "The token stored on the destination did not meet the token requirements.";

        } else {
            $returnobj->success = true;
            $returnobj->data = $tokenresult->token;
        }

        return $returnobj;
    }

    /**
     * Does the course and group exist?
     * @param  object containing the course shortname and group name
     * @return array
     */
    public function verify_course_group($params = false) {
        global $DB;
        $coursename = isset($params->coursename) ? $params->coursename : null;
        $groupname = isset($params->groupname) ? $params->groupname : null;
        $returnobj = new \stdClass();

        $coursedata = $DB->get_records_sql(
            'SELECT g.id as groupid, c.id, c.idnumber, c.shortname, g.name as groupname
            FROM mdl_course c, mdl_groups g
            WHERE c.id = g.courseid AND c.shortname = ? AND g.name = ?',
            array($coursename, $groupname)
        );

        return $coursedata;
    }

    /**
     * Fetch the course and group
     * @param  array containing course name and group name
     * @return array
     */
    public function check_course_exists($coursename = false, $useid = false) {
        global $DB;
        if ($useid) {
            $coursecount = $DB->count_records("course", array("id" => $coursename));
        } else {
            $coursecount = $DB->count_records("course", array("shortname" => $coursename));
        }
        return $coursecount;

    }

    public function check_group_exists($groupname = false, $courseid = 0) {
        global $DB;
        $groupcount = $DB->count_records("groups", array("name" => $groupname));
        return $groupcount;
    }

    /**
     * Fetch the course and group
     * @param  array containing course name and group name
     * @return array
     */
    public function get_course_group_info($coursename = false, $groupname = false) {
        global $DB;

        $coursedata = $DB->get_record_sql(
            'SELECT g.id as groupid, c.id, c.idnumber, c.shortname, g.name as groupname
            FROM mdl_course c, mdl_groups g
            WHERE c.id = g.courseid AND c.shortname = ? AND g.name = ?',
            array($coursename, $groupname)
        );

        return $coursedata;
    }

    /**
     * Fetch the number of mappings for each URL
     * @return array of counts
     */
    public function get_mappings_count() {
        global $DB;

        $mapcount = $DB->get_records_sql(
            'SELECT destmoodleid, COUNT(*) as count
            FROM mdl_block_lsuxe_mappings
            GROUP BY destmoodleid'
        );

        return $mapcount;
    }
}
