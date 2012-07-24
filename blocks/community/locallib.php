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
     * Download the community course backup and save it in file API
     * @param integer $courseid
     * @param string $huburl
     * @return array 'privatefile' the file name saved in private area
     *               'tmpfile' the file name saved in the moodledata temp dir (for restore)
     */
    public function block_community_download_course_backup($course) {
        global $CFG, $USER;
        require_once($CFG->libdir . "/filelib.php");
        require_once($CFG->dirroot. "/course/publish/lib.php");

        $params['courseid'] = $course->id;
        $params['filetype'] = HUB_BACKUP_FILE_TYPE;

        make_temp_directory('backup');

        $filename = md5(time() . '-' . $course->id . '-'. $USER->id . '-'. random_string(20));

        $url  = new moodle_url($course->huburl.'/local/hub/webservice/download.php', $params);
        $path = $CFG->tempdir.'/backup/'.$filename.".mbz";
        $fp = fopen($path, 'w');
        $curlurl = $course->huburl.'/local/hub/webservice/download.php?filetype='
                .HUB_BACKUP_FILE_TYPE.'&courseid='.$course->id;

        //send an identification token if the site is registered on the hub
        require_once($CFG->dirroot . '/' . $CFG->admin . '/registration/lib.php');
        $registrationmanager = new registration_manager();
        $registeredhub = $registrationmanager->get_registeredhub($course->huburl);
        if (!empty($registeredhub)) {
            $token = $registeredhub->token;
            $curlurl .= '&token='.$token;
        }

        $ch = curl_init($curlurl);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $fs = get_file_storage();
        $record = new stdClass();
        $record->contextid = context_user::instance($USER->id)->id;
        $record->component = 'user';
        $record->filearea = 'private';
        $record->itemid = 0;
        $record->filename = urlencode($course->fullname)."_".time().".mbz";
        $record->filepath = '/downloaded_backup/';
        if (!$fs->file_exists($record->contextid, $record->component,
                $record->filearea, 0, $record->filepath, $record->filename)) {
            $fs->create_file_from_pathname($record,
                    $CFG->tempdir.'/backup/'.$filename.".mbz");
        }

        $filenames = array();
        $filenames['privatefile'] = $record->filename;
        $filenames['tmpfile'] = $filename;
        return $filenames;
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
