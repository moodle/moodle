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
 * Community library
 *
 * @package    block_community
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 *
 */

class block_community_manager {

    /**
     * Add a community course
     * @param object $course
     * @param integer $userid
     * @return id of course or false if already added
     */
    public function block_community_add_course($course, $userid) {
        global $DB;

        $community = $this->block_community_get_course($course->url, $userid);

        if (empty($community)) {
            $community = new stdClass();
            $community->userid = $userid;
            $community->coursename = $course->name;
            $community->coursedescription = $course->description;
            $community->courseurl = $course->url;
            $community->imageurl = $course->imageurl;
            return $DB->insert_record('block_community', $community);
        } else {
            return false;
        }
    }

    /**
     * Return all community courses of a user
     * @param integer $userid
     * @return array of course
     */
    public function block_community_get_courses($userid) {
        global $DB;
        return $DB->get_records('block_community', array('userid' => $userid), 'coursename');
    }

    /**
     * Return a community courses of a user
     * @param integer $userid
     * @param integer $userid
     * @return array of course
     */
    public function block_community_get_course($courseurl, $userid) {
        global $DB;
        return $DB->get_record('block_community',
                array('courseurl' => $courseurl, 'userid' => $userid));
    }

    /**
     * Delete a community course
     * @param integer $communityid
     * @param integer $userid
     * @return bool true
     */
    public function block_community_remove_course($communityid, $userid) {
        global $DB, $USER;
        return $DB->delete_records('block_community',
                array('userid' => $userid, 'id' => $communityid));
    }

}
