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

use block_lsuxe\models;

class router {

    /**
     * Retrieve basic info about the course and it's group information.
     * @param  object containing the course id and name
     * @return array
     */
    public function get_group_data($params) {

        $fuzzy = new \block_lsuxe\models\xemixed();
        $dbresult = $fuzzy->get_course_group_data($params);
        $results = array();

        if ($dbresult->success == true) {
            $results["success"] = true;
            $results["count"] = count($dbresult->data);
            $results["data"] = $dbresult->data;

        } else {
            $results["success"] = false;
            $results["msg"] = $dbresult->msg;
        }
        return $results;
    }

    /**
     * This returns the token to the calling server.
     * @param  array list of params being sent, but should only have url.
     * @return array
     */
    public function get_token($params) {

        $url = isset($params->url) ? $params->url : null;
        $fuzzy = new \block_lsuxe\models\xemixed();
        $results = array();
        $dbresult = $fuzzy->get_token_data($url);

        if ($dbresult->success == true) {
            $results["success"] = true;
            $results["data"] = $dbresult->data;

        } else {
            $results["success"] = false;
            $results["msg"] = $dbresult->msg;
        }
        return $results;
    }

    /**
     * Verify if the course and group exists
     * @param  array containing course name and group name
     * @return array
     */
    public function verify_course($params) {
        $results = array();

        $fuzzy = new \block_lsuxe\models\xemixed();
        $dbresult = $fuzzy->verify_course_group($params);
        $dbcount = count($dbresult);

        if ($dbcount == 0) {
            $returnobj->success = false;
            $returnobj->msg = "Either the course shortname and/or group name do not exist.";
        } else if ($dbcount > 1) {
            $returnobj->success = false;
            $returnobj->msg = "There are multiple records.";
        } else {
            $returnobj->success = true;
            $dbresult = array_values($dbresult);
            $returnobj->data = $dbresult[0];
        }

        return $returnobj;
    }

    public function test_service($params) {
        return array("success" => true);
    }
}
