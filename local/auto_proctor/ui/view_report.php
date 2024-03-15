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

require_once(__DIR__ . '/../../../config.php'); // Setup moodle global variable also
require_login();
// Get the global $DB object

global $DB, $USER, $CFG;
    // Get user user id
    $user_id = $USER->id;

    // Check if the user has a managing role, such as an editing teacher or teacher.
    // Only users with those roles are allowed to create or modify a quiz.
    $managing_context = $DB->get_records_sql(
        'SELECT * FROM {role_assignments} WHERE userid = ? AND roleid IN (?, ?)',
        [
            $user_id,
            3, // Editing Teacehr
            4, // Teacher
        ]
    );


    echo "<script>console.log('courses enrolled: ', " . json_encode(count($managing_context)) . ");</script>";

    // If a user does not have a course management role, there is no reason for them to access the Auto Proctor Dashboard.
    // The user will be redirected to the normal dashboard.
    if (!$managing_context && !is_siteadmin($user_id)) {
        $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
        header("Location: $previous_page");
        exit();
    }

    // Check if user is techer in this course
        $isteacher = false;
        if(!is_siteadmin($user_id)){                
    
            // Loop through the context that the user manages
            foreach ($managing_context as $context) {
    
                // Get the context id of the context
                $context_id = $context->contextid;
                echo "<script>console.log('Managing Course IDhome: ', " . json_encode($context_id) . ");</script>";
    
                // Get instance id of the context from contex table
                $sql = "SELECT instanceid
                    FROM {context}
                    WHERE id= :id
                ";
                $instance_id = $DB->get_fieldset_sql($sql, ['id' => $context_id]);

                //echo $instance_id . "</br>";
                if ($_GET['course_id'] == $instance_id[0]){
                    //break;
                    echo "is teacher";
                    echo "</br>";
                    $isteacher = true;
                    break;
                }
            }
        }

        if (!$isteacher && !is_siteadmin($user_id)){
            $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
            header("Location: $previous_page");
            exit();
        }

    if(isset($_GET['course_id']) && isset($_GET['quiz_id'])){
        $course_id = $_GET['course_id'];
        $user_id = $_GET['user_id'];
        $quiz_id = $_GET['quiz_id'];
        $quiz_attempt = $_GET['quiz_attempt'];
        $params = array('course_id' => $course_id);

        // Retrieve all records from AP Table
        $AP_tb = 'auto_proctor_quiz_tb';
        $AP_records = $DB->get_records($AP_tb);

        // SELECTING USERS ACTIVITIES
        $sql = "SELECT *
            FROM {auto_proctor_activity_report_tb}
            WHERE userid = :user_id
            AND quizid = :quiz_id
            AND attempt = :quiz_attempt;
        ";

        $params = array('user_id' => $user_id, 'quiz_id' => $quiz_id, 'quiz_attempt' => $quiz_attempt);
        $quiz_activities = $DB->get_records_sql($sql, $params);

        // SELECTING CAMERA RECORDINGS
        $sql = "SELECT *
            FROM {auto_proctor_session_camera_recording}
            WHERE userid = :user_id
            AND quizid = :quiz_id
            AND attempt = :quiz_attempt;
        ";
        $params = array('user_id' => $user_id, 'quiz_id' => $quiz_id, 'quiz_attempt' => $quiz_attempt);
        $quiz_attempt_recordings = $DB->get_records_sql($sql, $params);

        // SELECTING CAMERA RECORDINGS
        $sql = "SELECT *
            FROM {auto_proctor_proctoring_session_tb}
            WHERE userid = :user_id
            AND quizid = :quiz_id
            AND attempt = :quiz_attempt;
        ";
        $params = array('user_id' => $user_id, 'quiz_id' => $quiz_id, 'quiz_attempt' => $quiz_attempt);
        $quiz_attempt_session_setup = $DB->get_records_sql($sql, $params);

    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HTML Table</title>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }
</style>
</head>
<body>

<table>
    <tr>
        <th>Datetime</th>
        <th>attempt</th>
        <th>activity_type</th>
        <th>evidence</th>
        <th>timestamp</th>
    </tr>
    <?php
        foreach($quiz_activities as $activity){
            switch ($activity->activity_type) {
                case 1:
                    $activity_type = 'did not share screen';
                    break;
                case 2:
                    $activity_type = 'shared screen';
                    break;
                case 3:
                    $activity_type = 'stops sharing';
                    break;
                case 4:
                    $activity_type = 'tab switch';
                    break;
                case 5:
                    $activity_type = 'tab switch not shared';
                    break;
                case 6:
                    $activity_type = 'camera_permission_denied';
                    break;
                case 7:
                    $activity_type = 'camera permission denied during quiz';
                    break;
                case 8:
                    $activity_type = 'no face';
                    break;
                case 9:
                    $activity_type = 'multiple face';
                    break;
                case 10:
                    $activity_type = 'suspicious movement';
                    break;
                case 11:
                    $activity_type = 'microphone permission denied';
                    break;
                case 12:
                    $activity_type = 'microphone permission denied during quiz';
                    break;
                case 13:
                    $activity_type = 'speech detected';
                    break;
                case 14:
                    $activity_type = 'loud noise';
                    break;
                default:
                    $activity_type = '';
                    break;
            }

            // Assuming $activity->evidence contains the URL
            $url = $activity->evidence;

            // Get the file extension from the URL
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

            // Output the file extension
            echo $extension;

            if ($extension === "png" || $extension === "webm"){
                $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/camera_capture_evidence/';
            }
            else if ($extension === "wav"){
                $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/microphone_capture_evidence/';
            }
            else{
                $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/microphone_capture_evidence/';
            }

            echo '
                <tr>
                    <td>'. $activity->event_datetime .'</td>
                    <td>'. $activity->attempt .'</td>
                    <td>'. $activity_type .'</td>
                    <td><a href ="';

            echo $directory .urlencode($activity->evidence) .'" target="_blank">'. $activity->evidence.'</a></td>
                    <td>'. $activity->timestamp.'</td>
                </tr>
            ';
        }
    ?>
</table>

<table>
    <tr>
        <th>Datetime</th>
        <th>Recording FIle</th>
    </tr>
    <?php
        foreach($quiz_attempt_recordings as $recording){
            echo '
                <tr>
                    <td>'. $recording->event_datetime .'</td>
                    <td><a href ="';
            echo $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/camera_capture_evidence/' .urlencode($recording->camera_recording) .'" target="_blank">'. $recording->camera_recording.'</a></td>
                </tr>
            ';
        }
    ?>
</table>

<table>
    <tr>
        <th>Device type</th>
        <th>Multiple monitor</th>
    </tr>
    <?php
        foreach($quiz_attempt_session_setup as $setup){

            switch ($setup->device_type) {
                case 1:
                    $device_type = 'Mobile';
                    break;
                case 2:
                    $device_type = 'Tablet';
                    break;
                case 3:
                    $device_type = 'Desktop';
                    break;
            }

            switch ($setup->monitor_setup) {
                case 1:
                    $multiple_monitor = 'No';
                    break;
                case 2:
                    $multiple_monitor = 'Yes';
                    break;
            }
            echo '
                <tr>
                    <td>'. $device_type .'</td>
                    <td>'. $multiple_monitor .'</td>
                </tr>
            ';
        }
    ?>
</table>
</body>
</html>


