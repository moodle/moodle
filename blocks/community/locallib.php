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

/*
 * @package    blocks
 * @subpackage community
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * Community library
*/

class community {

///////////////////////////
/// DB Facade functions  //
///////////////////////////

    /**
     * Add a community course
     * @param object $course
     * @param integer $userid
     */
    public function add_community_course($course, $userid) {
        global $DB;
        $community->userid = $userid;
        $community->coursename = $course->name;
        $community->coursedescription = $course->description;
        $community->courseurl = $course->url;
        $community->imageurl = $course->imageurl;
        return $DB->insert_record('block_community', $community);
    }

    /**
     Return all community courses of a user
     * @param integer $userid
     * @return array of course
     */
    public function get_community_courses($userid) {
        global $DB;
        return $DB->get_records('block_community', array('userid' => $userid), 'coursename');
    }

    /**
     *
     * @param <type> $courseid
     * @param <type> $huburl
     */
    public function get_community_course_backup($courseid, $huburl) {
        global $CFG;
        require_once($CFG->dirroot. "/lib/filelib.php");
        require_once($CFG->dirroot. "/lib/hublib.php");
        $curl = new curl();
        $params['courseid'] = $courseid;
        $params['filetype'] = BACKUP_FILE_TYPE;
        $filecontent = $curl->get($huburl.'/local/hub/webservice/download.php', $params);
    }

}