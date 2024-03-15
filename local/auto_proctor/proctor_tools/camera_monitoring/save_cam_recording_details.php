<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_auto_proctor
 * @author      Angelica
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
*/
require_once(__DIR__ . '/../../../../config.php');

global $DB, $PAGE, $USER, $CFG;

if (isset($_POST['recording_filename'])) {

    // Initialize all the necessary details about the record.
    $userid = $_POST['userid']; // Ensure it's an integer
    $quizid = $_POST['quizid'];
    $attempt = $_POST['quizattempt'];
    $recording_filename = $_POST['recording_filename'];

    echo "userid: " . $userid ;
    echo "quizid: " . $quizid ;
    echo "attempt: " . $attempt ;
    echo "recording_filename: " . $recording_filename ;

    $insertData = new stdClass();
    $insertData->userid = $userid;
    $insertData->quizid = $quizid;
    $insertData->attempt = $attempt;
    $insertData->camera_recording = $recording_filename . '.webm';

    $insert_new_cam_record_session = $DB->insert_record('auto_proctor_session_camera_recording', $insertData);

}
?>