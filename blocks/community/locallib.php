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
     * @return id of course or false if already added
     */
    public function add_community_course($course, $userid) {
        global $DB;

        $community = $this->get_community_course($course->url, $userid);

        if (empty($community)) {
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
    public function get_community_courses($userid) {
        global $DB;
        return $DB->get_records('block_community', array('userid' => $userid), 'coursename');
    }

    /**
     * Return a community courses of a user
     * @param integer $userid
     * @param integer $userid
     * @return array of course
     */
    public function get_community_course($courseurl, $userid) {
        global $DB;
        return $DB->get_record('block_community', array('courseurl' => $courseurl, 'userid' => $userid));
    }

    /**
     * Download the community course backup and save it in file API
     * @param integer $courseid
     * @param string $huburl
     */
    public function download_community_course_backup($courseid, $huburl) {
        global $CFG, $USER;
        require_once($CFG->dirroot. "/lib/filelib.php");
        require_once($CFG->dirroot. "/lib/hublib.php");
        //$curl = new curl();
        $params['courseid'] = $courseid;
        $params['filetype'] = BACKUP_FILE_TYPE;

        $url  = new moodle_url($huburl.'/local/hub/webservice/download.php', $params);
        $path = $CFG->dataroot.'/temp/download/'.'backup_'.$courseid.".zip";
        $fp = fopen($path, 'w');
        $ch = curl_init($huburl.'/local/hub/webservice/download.php?filetype='.BACKUP_FILE_TYPE.'&courseid='.$courseid);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $record->contextid = get_context_instance(CONTEXT_USER, $USER->id)->id;
        $record->filearea = 'user_backup';
        $record->itemid = 0;
        $record->filename = 'backup_'.$courseid.".zip";
        $record->filepath = '/';
        $fs = get_file_storage();
        $fs->create_file_from_pathname($record, $CFG->dataroot.'/temp/download/'.'backup_'.$courseid.".zip");
    }

    /**
     * Delete a community course
     * @param integer $communityid
     * @param integer $userid
     * @return bool true
     */
    public function remove_community_course($communityid, $userid) {
        global $DB, $USER;
        return $DB->delete_records('block_community', array('userid' => $userid, 'id' => $communityid));
    }

    /**
     * Decide where to save the file, can be
     * reused by sub class
     * @param string filename
     */
    public function prepare_file($filename) {
        global $CFG;
        if (!file_exists($CFG->dataroot.'/temp/download')) {
            mkdir($CFG->dataroot.'/temp/download/', 0777, true);
        }
        if (is_dir($CFG->dataroot.'/temp/download')) {
            $dir = $CFG->dataroot.'/temp/download/';
        }
        if (empty($filename)) {
            $filename = uniqid('repo').'_'.time().'.tmp';
        }
        if (file_exists($dir.$filename)) {
            $filename = uniqid('m').$filename;
        }
        return $dir.$filename;
    }

}