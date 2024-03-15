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
    $recording_filename = $_POST['filename'];
    $activity_type = $_POST['evidence_name_type']; // Ensure it's an integer
    $activity_timestamp = $_POST['activity_timestamp'];
    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];

    // Converting the activity_type to an integer to save in the database.
    switch ($activity_type) {
        case 'camera_permission_denied':
            $activity_type = 6;
            break;
        case 'camera_permission_denied_during_quiz':
            $activity_type = 7;
            break;
        case 'no_face':
            $activity_type = 8;
            break;
        case 'multiple_face':
            $activity_type = 9;
            break;
        case 'suspicious_movement':
            $activity_type = 10;
            break;
    }

    // Create insert instance
    $insertData = new stdClass();

    // Initialize that the userid, quizid, attempt, evidence, and activity_type are the values that are fetch from the monitor_cam.js.
    $insertData->userid = $userid;
    $insertData->quizid = $quizid;
    $insertData->attempt = $quizattempt;
    $insertData->evidence = $filename;
    $insertData->activity_type = $activity_type;
    $insertData->timestamp = $activity_timestamp;

    // Insert the activity record
    $insert_activity = $DB->insert_record('auto_proctor_activity_report_tb', $insertData);
    
    echo "evdtype: " . $evidence_name_type;
    echo "</br>";
    echo "filename: " . $filename;
}
?>