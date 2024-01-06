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
require_once(__DIR__ . '/../../config.php');

global $DB, $PAGE, $USER, $CFG;

if (isset($_POST['screen_status'])) {
    $screen_status = intval($_POST['screen_status']); // Ensure it's an integer
    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];
    $activity_type = $_POST['activity_type'];

    switch ($activity_type) {
        case 'did_not_share_screen':
            $activity_type = 0;
            break;
        case 'shared_screen':
            $activity_type = 1;
            break;
        case 'stops_sharing_screen':
            $activity_type = 2;
            break;
        case 'tab_switch':
            $activity_type = 3;
            break;
        case 'tab_switch_screen_not_shared':
            $activity_type = 3;
            break;
        // default:
            
        //     break;
    }

    if($activity_type === 1 || $activity_type === 3){
        if (isset($_POST['filename'])){
            $filename = $_POST['filename'];

            $insertData = new stdClass();
            $insertData->userid = $userid;
            $insertData->quizid = $quizid;
            $insertData->attempt = $quizattempt; // Assuming 'attempt' is a field in 'auto_proctor_activity_report_tb'
            $insertData->evidence = $filename;
            $insertData->activity_type = $activity_type; // You need to set the appropriate value for 'activity_type'
            $DB->insert_record('auto_proctor_activity_report_tb', $insertData);

            //echo json_encode(['message' => 'Received screen status: ' . $status]);
            echo '<script>console.log("sdfds");</script>';
            echo '<script>console.log(' . json_encode(['screen_status' => $screen_status]) . ');</script>';
            exit(); // Terminate the script after sending the response
        }
    }
    if($activity_type === 0 || $activity_type === 3 || $activity_type === 2){
        $insertData = new stdClass();
        $insertData->userid = $userid;
        $insertData->quizid = $quizid;
        $insertData->attempt = $quizattempt; // Assuming 'attempt' is a field in 'auto_proctor_activity_report_tb'
        $insertData->activity_type = $activity_type; // You need to set the appropriate value for 'activity_type'
        $DB->insert_record('auto_proctor_activity_report_tb', $insertData);

        //echo json_encode(['message' => 'Received screen status: ' . $status]);
        echo '<script>console.log("sdfds");</script>';
        echo '<script>console.log(' . json_encode(['screen_status' => $screen_status]) . ');</script>';
        exit(); // Terminate the script after sending the response
    }
}
?>