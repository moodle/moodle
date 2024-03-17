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

        echo $course_id . "</br>";
        echo $user_id . "</br>";
        echo $quiz_id . "</br>";
        echo $quiz_attempt . "</br>";

        // =========== Select user's fullname
            $sql = "SELECT *
                FROM {user}
                WHERE id = :user_id;
            ";
            $params = array('user_id' => $user_id);
            $user_info = $DB->get_records_sql($sql, $params);

        // =========== Select user's emails
            foreach($user_info as $info){
                $user_fullname = $info->firstname . ' ' . $info->lastname;
                $user_email = $info->email;
            }

        // =========== Select quiz attempt details
            $sql = "SELECT *
                    FROM {quiz_attempts}
                    WHERE quiz = :quiz_id
                    AND attempt = :attempt
                    AND userid = :userid
            ";
            $params = array('quiz_id' => $quiz_id, 'attempt' => $quiz_attempt, 'userid' => $user_id);
            $attempt_details = $DB->get_records_sql($sql, $params);

            foreach($attempt_details as $details){
                $attempt_start_date = $details->timestart;
                $attempt_finish_date = $details->timefinish;
            }

        // ========== Select session setup details
            $sql = "SELECT *
                FROM {auto_proctor_proctoring_session_tb}
                WHERE userid = :user_id
                AND quizid = :quiz_id
                AND attempt = :quiz_attempt;
            ";
            $params = array('user_id' => $user_id, 'quiz_id' => $quiz_id, 'quiz_attempt' => $quiz_attempt);
            $quiz_attempt_session_setup = $DB->get_records_sql($sql, $params);
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

                echo $device_type . '</br>';
                echo $multiple_monitor . '</br>';
            }
        // =========== Select activated features quiz
            $sql = "SELECT *
                FROM {auto_proctor_quiz_tb}
                WHERE quizid = :quiz_id
                AND course = :course_id;
            ";
            $params = array('quiz_id' => $quiz_id, 'course_id' => $course_id);
            $activated_features = $DB->get_records_sql($sql, $params);

            foreach ($activated_features as $feature){
                $monitor_camera = $feature->monitor_camera;
                $monitor_microphone = $feature->monitor_microphone;
                $monitor_tab_switching = $feature->monitor_tab_switching;
            }

            print_r($activated_features);

        // Retrieve all records from AP Table
            $AP_tb = 'auto_proctor_quiz_tb';
            $AP_records = $DB->get_records($AP_tb);
            $params = array('course_id' => $course_id);

        // Selecting user's activities
            $sql = "SELECT *
                FROM {auto_proctor_activity_report_tb}
                WHERE userid = :user_id
                AND quizid = :quiz_id
                AND attempt = :quiz_attempt;
            ";

            $params = array('user_id' => $user_id, 'quiz_id' => $quiz_id, 'quiz_attempt' => $quiz_attempt);
            $quiz_activities = $DB->get_records_sql($sql, $params);

            foreach ($quiz_activities as $activity){
                if ($activity->activity_type == 13 || $activity->activity_type == 14){
                    $num_of_noise_detected++;
                }
                if ($activity->activity_type == 4 || $activity->activity_type == 5){
                    $num_of_tab_switch_detected++;
                }
                if ($activity->activity_type == 8){
                    $num_of_no_face_detected++;
                }
                if ($activity->activity_type == 9){
                    $num_of_multiple_face_detected++;
                }
            }

            // SELECTING CAMERA RECORDINGS
                $sql = "SELECT *
                    FROM {auto_proctor_session_camera_recording}
                    WHERE userid = :user_id
                    AND quizid = :quiz_id
                    AND attempt = :quiz_attempt;
                ";
                $params = array('user_id' => $user_id, 'quiz_id' => $quiz_id, 'quiz_attempt' => $quiz_attempt);
                $quiz_session_recordings = $DB->get_records_sql($sql, $params);
                print_r($quiz_session_recordings);

    }


?>
<!-- <!DOCTYPE html>
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

    ?>
</table>
</body>
</html>

 -->

<style>
   /* Popup modal styles */
/* Popup modal styles */
/* Popup modal styles */
.modal {
  display: none;
  position: fixed;
  z-index: 9999; /* Set a high z-index value */
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.9);
}

.modal-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  max-width: 90%;
  max-height: 90%;
}

.close {
  color: white;
  position: absolute;
  top: 15px;
  right: 35px;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}




</style>

<main>
                    <div class="p-4 bg-white pt-14 border border-gray-200 rounded-lg shadow-sm ">
                        <!--main-->
                        <section class="bg-white mb-12">
                            <dl class="grid max-w-screen-2xl gap-2 mx-auto text-gray-900 sm:grid-cols-5">
                                <div class=" pt-4 pb-10">
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <li class="py-3 sm:py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <img class="w-12 h-12 rounded-4"
                                                        src="https://flowbite-admin-dashboard.vercel.app/images/users/neil-sims.png"
                                                        alt="Neil image">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-gray-900 truncate ">
                                                        <?php echo $user_fullname; ?>
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate ">
                                                        <?php echo $user_email; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <!--TRUST SCORE-->
                                <div
                                    class="flex flex-col items-center justify-center rounded-lg p-4 mx-4 shadow-lg bg-gray-100">
                                    <dt class="mb-2 text-xl md:text-3xl font-bold">40%</dt>
                                    <span class="text-gray-500 "> TRUST SCORE
                                    </span>
                                </div>
                                <!--STARTED AT-->
                                <div class="flex flex-col items-center justify-center text-start">
                                    <div class="flex-1 min-w-0 pt-2">
                                        <p class=" text-sm text-gray-500 truncate  uppercase">
                                            started at
                                        </p>
                                        <h3 class="font-medium text-gray-900  ">
                                            <?php echo date('j-M g:i A', $attempt_start_date); ?>
                                        </h3>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-2">
                                        <p class=" text-sm text-gray-500 truncate  uppercase">
                                            tracking
                                        </p>
                                        <h3 class="font-medium text-gray-900  ">
                                            <span class="text-base font-md font-bold text-gray-700 ">
                                                <div class="flex space-x-4 sm:justify-center mt-2">
                                                    <?php
                                                        if($monitor_camera == 1){
                                                            echo '
                                                                <a href="">
                                                                    <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg"
                                                                        fill="gray-800" viewBox="0 0 24 24">
                                                                        <path stroke="currentColor" stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M14 6H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h10c.6 0 1-.4 1-1V7c0-.6-.4-1-1-1Zm7 11-6-2V9l6-2v10Z" />
                                                                    </svg>
                                                                </a>
                                                            ';
                                                        }
                                                        if ($monitor_microphone == 1){
                                                            echo '
                                                                <a href="">
                                                                    <svg fill="#000000" width="15" height="15" viewBox="0 0 1920 1920"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M425.818 709.983V943.41c0 293.551 238.946 532.497 532.497 532.497 293.55 0 532.496-238.946 532.496-532.497V709.983h96.818V943.41c0 330.707-256.438 602.668-580.9 627.471l-.006 252.301h242.044V1920H667.862v-96.818h242.043l-.004-252.3C585.438 1546.077 329 1274.116 329 943.41V709.983h96.818ZM958.315 0c240.204 0 435.679 195.475 435.679 435.68v484.087c0 240.205-195.475 435.68-435.68 435.68-240.204 0-435.679-195.475-435.679-435.68V435.68C522.635 195.475 718.11 0 958.315 0Z"
                                                                            fill-rule="evenodd" />
                                                                    </svg>

                                                                </a>
                                                            ';
                                                        }
                                                        if ($monitor_tab_switching == 1){
                                                            echo '
                                                                <a href="">
                                                                    <svg width="15" height="15" viewBox="0 0 32 32" version="1.1"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                                                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd" sketch:type="MSPage">
                                                                            <g id="Icon-Set" sketch:type="MSLayerGroup"
                                                                                transform="translate(-256.000000, -671.000000)"
                                                                                fill="#000000">
                                                                                <path
                                                                                    d="M265,675 C264.448,675 264,675.448 264,676 C264,676.553 264.448,677 265,677 C265.552,677 266,676.553 266,676 C266,675.448 265.552,675 265,675 L265,675 Z M269,675 C268.448,675 268,675.448 268,676 C268,676.553 268.448,677 269,677 C269.552,677 270,676.553 270,676 C270,675.448 269.552,675 269,675 L269,675 Z M286,679 L258,679 L258,675 C258,673.896 258.896,673 260,673 L284,673 C285.104,673 286,673.896 286,675 L286,679 L286,679 Z M286,699 C286,700.104 285.104,701 284,701 L260,701 C258.896,701 258,700.104 258,699 L258,681 L286,681 L286,699 L286,699 Z M284,671 L260,671 C257.791,671 256,672.791 256,675 L256,699 C256,701.209 257.791,703 260,703 L284,703 C286.209,703 288,701.209 288,699 L288,675 C288,672.791 286.209,671 284,671 L284,671 Z M261,675 C260.448,675 260,675.448 260,676 C260,676.553 260.448,677 261,677 C261.552,677 262,676.553 262,676 C262,675.448 261.552,675 261,675 L261,675 Z"
                                                                                    id="browser" sketch:type="MSShapeGroup">

                                                                                </path>
                                                                            </g>
                                                                        </g>
                                                                    </svg>
                                                                </a>
                                                            ';
                                                        }
                                                    ?>
                                                    <!-- ARROW -->
                                                    <a href="">
                                                        
                                                        <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7 17L17 7M17 7H8M17 7V16" stroke="#000000"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </span>
                                        </h3>
                                    </div>
                                </div>
                                <!--FINISHED AT-->
                                <div class="flex flex-col items-center justify-center text-start">
                                    <div class="flex-1 min-w-0 pt-2">
                                        <p class=" text-sm text-gray-500 truncate  uppercase">
                                            finished at
                                        </p>
                                        <h3 class="font-medium text-gray-900  ">
                                            <?php echo date('j-M g:i A', $attempt_finish_date); ?>
                                        </h3>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-2 pr-10">
                                        <p class=" text-sm text-gray-500 truncate  uppercase">
                                            Attempt
                                        </p>
                                        <h3 class="font-medium text-gray-900  ">
                                            1
                                        </h3>
                                    </div>
                                </div>
                                <!--Device type-->

                                <div class="flex flex-col items-center justify-center text-start">
                                    <div class="flex-1 min-w-0 pt-2">
                                        <p class=" text-sm text-gray-500 truncate  uppercase">
                                            device type
                                        </p>
                                        <h3 class="font-medium text-gray-900  ">
                                            <?php echo $device_type; ?>
                                        </h3>
                                    </div>

                                </div>
                            </dl>
                        </section>
                        <section class="bg-white">
                            <dl class="grid max-w-screen-2xl gap-8 mx-auto text-gray-900 sm:grid-cols-5">
                                <!--Noise Detected-->
                                <div class="flex flex-col items-center justify-center border p-4 mx-2 shadow-lg">
                                    <dd class="font-light text-gray-500 text-sm">
                                        <!-- Adjusted font size -->
                                        <div class="flex items-baseline my-2">
                                            <span class="mr-2 text-5xl font-extrabold">

                                                <svg fill="#5a5858" width="15" height="15" viewBox="0 0 1920 1920"
                                                    xmlns="http://www.w3.org/2000/svg" stroke="#5a5858">

                                                    <g id="SVGRepo_bgCarrier" stroke-width="0" />

                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                        stroke-linejoin="round" />

                                                    <g id="SVGRepo_iconCarrier">
                                                        <path
                                                            d="M960.315 96.818c-186.858 0-338.862 152.003-338.862 338.861v484.088c0 186.858 152.004 338.862 338.862 338.862 186.858 0 338.861-152.004 338.861-338.862V435.68c0-186.858-152.003-338.861-338.861-338.861M427.818 709.983V943.41c0 293.551 238.946 532.497 532.497 532.497 293.55 0 532.496-238.946 532.496-532.497V709.983h96.818V943.41c0 330.707-256.438 602.668-580.9 627.471l-.006 252.301h242.044V1920H669.862v-96.818h242.043l-.004-252.3C587.438 1546.077 331 1274.116 331 943.41V709.983h96.818ZM960.315 0c240.204 0 435.679 195.475 435.679 435.68v484.087c0 240.205-195.475 435.68-435.68 435.68-240.204 0-435.679-195.475-435.679-435.68V435.68C524.635 195.475 720.11 0 960.315 0Z"
                                                            fill-rule="evenodd" />
                                                    </g>

                                                </svg>
                                            </span>
                                            <span class="text-gray-500 ">Noise
                                                Detected</span>
                                        </div>
                                    </dd>
                                    <dt class="mb-2 text-xl md:text-2xl font-bold">
                                        <?php echo $num_of_noise_detected; ?>
                                    </dt>
                                </div>
                                <!--multiple monitor-->
                                <div class="flex flex-col items-center justify-center border p-4 mx-2 shadow-lg">
                                    <dd class="font-light text-gray-500 text-sm ">
                                        <!-- Adjusted font size -->
                                        <div class="flex items-baseline my-2">
                                            <span class="mr-2 text-5xl font-extrabold">
                                                <svg width="15" height="15" version="1.1" id="Icons"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32"
                                                    xml:space="preserve">
                                                    <style type="text/css">
                                                        .st0 {
                                                            fill: none;
                                                            stroke: #000000;
                                                            stroke-width: 2;
                                                            stroke-linecap: round;
                                                            stroke-linejoin: round;
                                                            stroke-miterlimit: 10;
                                                        }

                                                        .st1 {
                                                            fill: none;
                                                            stroke: #000000;
                                                            stroke-width: 2;
                                                            stroke-linejoin: round;
                                                            stroke-miterlimit: 10;
                                                        }
                                                    </style>
                                                    <rect x="4" y="4" class="st0" width="10" height="10" />
                                                    <rect x="4" y="18" class="st0" width="10" height="10" />
                                                    <rect x="18" y="4" class="st0" width="10" height="10" />
                                                    <line class="st0" x1="23" y1="19" x2="23" y2="27" />
                                                    <line class="st0" x1="19" y1="23" x2="27" y2="23" />
                                                </svg></span>
                                            <span class="text-gray-500 "> Multiple
                                                Monitors</span>
                                        </div>

                                    </dd>
                                    <dt class="mb-2 text-xl md:text-2xl font-bold"><?php echo $multiple_monitor; ?></dt>
                                </div>
                                <!--TAB SWITCH-->
                                <div class="flex flex-col items-center justify-center border p-4 mx-2 shadow-lg">
                                    <dd class="font-light text-gray-500 text-sm">
                                        <div class="flex items-baseline my-2">
                                            <span class="mr-2 text-5xl font-extrabold">

                                                <svg width="15" height="15" version="1.1" id="_x32_"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                                    xml:space="preserve">
                                                    <style type="text/css">
                                                        .st0 {
                                                            fill: #000000;
                                                        }
                                                    </style>
                                                    <g>
                                                        <path class="st0" d="M464,0H48C21.492,0,0,21.492,0,48v416c0,26.508,21.492,48,48,48h416c26.508,0,48-21.492,48-48V48
                                                    C512,21.492,490.508,0,464,0z M444.664,35c10.492,0,19,8.508,19,19s-8.508,19-19,19c-10.492,0-19-8.508-19-19
                                                    S434.172,35,444.664,35z M374.164,35c10.492,0,19,8.508,19,19s-8.508,19-19,19c-10.492,0-19-8.508-19-19S363.672,35,374.164,35z
                                                        M303.664,35c10.492,0,19,8.508,19,19s-8.508,19-19,19c-10.492,0-19-8.508-19-19S293.172,35,303.664,35z M472,464
                                                    c0,4.406-3.586,8-8,8H48c-4.414,0-8-3.594-8-8V104h432V464z" />
                                                        <rect x="96" y="192" class="st0" width="152" height="32" />
                                                        <rect x="96" y="352" class="st0" width="328" height="32" />
                                                        <rect x="304" y="192" class="st0" width="120" height="120" />
                                                        <polygon class="st0"
                                                            points="229.042,304 248,304 248,272 96,272 96,304 213.042,304 	" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <span class="text-gray-500 ">Tab
                                                Switched</span>
                                        </div>
                                    </dd>
                                    <dt class="mb-2 text-xl md:text-2xl font-bold">
                                        <?php echo $num_of_tab_switch_detected; ?>
                                    </dt>
                                </div>
                                <!--NO FACE DETECTED-->
                                <div class="flex flex-col items-center justify-center border p-4 mx-2 shadow-lg">
                                    <dd class="font-light text-gray-500 text-sm">
                                        <!-- Adjusted font size -->
                                        <div class="flex items-baseline my-2">
                                            <span class="mr-2 text-5xl font-extrabold">

                                                <svg width="15" height="15" viewBox="0 0 20 20" version="1.1"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <g id="Dribbble-Light-Preview"
                                                            transform="translate(-140.000000, -2159.000000)" fill="#000000">
                                                            <g id="icons" transform="translate(56.000000, 160.000000)">
                                                                <path
                                                                    d="M100.562548,2016.99998 L87.4381713,2016.99998 C86.7317804,2016.99998 86.2101535,2016.30298 86.4765813,2015.66198 C87.7127655,2012.69798 90.6169306,2010.99998 93.9998492,2010.99998 C97.3837885,2010.99998 100.287954,2012.69798 101.524138,2015.66198 C101.790566,2016.30298 101.268939,2016.99998 100.562548,2016.99998 M89.9166645,2004.99998 C89.9166645,2002.79398 91.7489936,2000.99998 93.9998492,2000.99998 C96.2517256,2000.99998 98.0830339,2002.79398 98.0830339,2004.99998 C98.0830339,2007.20598 96.2517256,2008.99998 93.9998492,2008.99998 C91.7489936,2008.99998 89.9166645,2007.20598 89.9166645,2004.99998 M103.955674,2016.63598 C103.213556,2013.27698 100.892265,2010.79798 97.837022,2009.67298 C99.4560048,2008.39598 100.400241,2006.33098 100.053171,2004.06998 C99.6509769,2001.44698 97.4235996,1999.34798 94.7348224,1999.04198 C91.0232075,1998.61898 87.8750721,2001.44898 87.8750721,2004.99998 C87.8750721,2006.88998 88.7692896,2008.57398 90.1636971,2009.67298 C87.1074334,2010.79798 84.7871636,2013.27698 84.044024,2016.63598 C83.7745338,2017.85698 84.7789973,2018.99998 86.0539717,2018.99998 L101.945727,2018.99998 C103.221722,2018.99998 104.226185,2017.85698 103.955674,2016.63598"
                                                                    id="profile_round-[#1342]">

                                                                </path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                            <span class="text-gray-500 ">No face
                                                Detected</span>
                                        </div>
                                    </dd>
                                    <dt class="mb-2 text-xl md:text-2xl font-bold">
                                        <?php echo $num_of_no_face_detected; ?>
                                    </dt>
                                </div>
                                <!--multiple faces-->
                                <div class="flex flex-col items-center justify-center border p-4 mx-2 shadow-lg">
                                    <dd class="font-light text-gray-500 text-sm">
                                        <!-- Adjusted font size -->
                                        <div class="flex items-baseline my-2">
                                            <span class="mr-2 text-5xl font-extrabold">

                                
                                                <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
                                                <svg width="15" height="15" viewBox="0 0 600 600" version="1.1" id="svg9724"
                                                    sodipodi:docname="people.svg"
                                                    inkscape:version="1.2.2 (1:1.2.2+202212051550+b0a8486541)" width="600"
                                                    height="600"
                                                    xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                                                    xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:svg="http://www.w3.org/2000/svg">
                                                    <defs id="defs9728" />
                                                    <sodipodi:namedview id="namedview9726" pagecolor="#ffffff"
                                                        bordercolor="#666666" borderopacity="1.0"
                                                        inkscape:showpageshadow="2" inkscape:pageopacity="0.0"
                                                        inkscape:pagecheckerboard="0" inkscape:deskcolor="#d1d1d1"
                                                        showgrid="true" inkscape:zoom="0.84118632" inkscape:cx="319.19207"
                                                        inkscape:cy="427.37262" inkscape:window-width="1920"
                                                        inkscape:window-height="1009" inkscape:window-x="0"
                                                        inkscape:window-y="1080" inkscape:window-maximized="1"
                                                        inkscape:current-layer="g10449" showguides="true">
                                                        <inkscape:grid type="xygrid" id="grid9972" originx="0"
                                                            originy="0" />
                                                        <sodipodi:guide position="-260,300" orientation="0,-1" id="guide383"
                                                            inkscape:locked="false" />
                                                        <sodipodi:guide position="300,520" orientation="1,0" id="guide385"
                                                            inkscape:locked="false" />
                                                        <sodipodi:guide position="240,520" orientation="0,-1" id="guide939"
                                                            inkscape:locked="false" />
                                                        <sodipodi:guide position="220,80" orientation="0,-1" id="guide941"
                                                            inkscape:locked="false" />
                                                    </sodipodi:namedview>

                                                    <g id="g10449"
                                                        transform="matrix(0.95173205,0,0,0.95115787,13.901174,12.168794)"
                                                        style="stroke-width:1.05103">
                                                        <g id="path10026" inkscape:transform-center-x="-0.59233046"
                                                            inkscape:transform-center-y="-20.347403"
                                                            transform="matrix(1.3807551,0,0,1.2700888,273.60014,263.99768)" />
                                                        <g id="g11314"
                                                            transform="matrix(1.5092301,0,0,1.3955555,36.774048,-9.4503933)"
                                                            style="stroke-width:50.6951" />
                                                        <path
                                                            style="color:#000000;fill:#000000;stroke-width:1.05103;stroke-linecap:round;stroke-linejoin:round;-inkscape-stroke:none;paint-order:stroke fill markers"
                                                            d="m 248.07279,-12.793664 c -72.13241,0 -131.33949,59.250935 -131.33949,131.392074 0,38.92115 17.25502,74.07152 44.45432,98.20884 C 58.500207,254.84854 -14.606185,358.21398 -14.606185,477.846 a 35.037921,35.037921 0 0 0 35.034809,35.03543 H 188.95771 c 6.88866,-25.46243 17.91968,-49.15043 32.45932,-70.0688 H 58.235927 C 73.730605,344.39181 153.38526,271.2598 248.07279,271.2598 c 13.12286,0 25.94065,1.45153 38.35524,4.13353 4.26325,-42.80875 34.59589,-78.30933 74.73011,-90.32371 11.57931,-19.5408 18.25414,-42.27592 18.25414,-66.47121 0,-72.141139 -59.20709,-131.392074 -131.33949,-131.392074 z m 0,70.068794 c 34.24293,0 61.26987,27.028459 61.26987,61.32328 0,34.29482 -27.02694,61.3274 -61.26987,61.3274 -34.24293,0 -61.27192,-27.03258 -61.27192,-61.3274 0,-34.294821 27.02899,-61.32328 61.27192,-61.32328 z"
                                                            id="path295" />
                                                        <path id="path295-3"
                                                            style="color:#000000;fill:#000000;stroke-width:1.05103;stroke-linecap:round;stroke-linejoin:round;-inkscape-stroke:none;paint-order:stroke fill markers"
                                                            d="m 405.68024,197.47637 c -57.70598,0 -105.07159,47.40151 -105.07159,105.11449 0,31.13694 13.80343,59.25664 35.56289,78.56652 -82.15001,30.43306 -140.63449,113.12556 -140.63449,208.83127 a 28.030337,28.030337 0 0 0 28.0273,28.0278 h 182.11589 182.11452 a 28.030337,28.030337 0 0 0 28.0286,-28.0278 c 0,-95.70539 -58.4835,-178.39795 -140.63307,-208.83127 21.75947,-19.30988 35.56153,-47.42958 35.56153,-78.56652 0,-57.71298 -47.3656,-105.11449 -105.07158,-105.11449 z m 0,56.05559 c 27.39437,0 49.01562,21.62301 49.01562,49.0589 0,27.43588 -21.62125,49.06164 -49.01562,49.06164 -27.39437,0 -49.017,-21.62576 -49.017,-49.06164 0,-27.43589 21.62263,-49.0589 49.017,-49.0589 z m 0,171.18664 c 75.7501,0 139.47372,58.50552 151.86952,137.24226 H 405.68024 253.81075 C 266.2065,483.22412 329.93014,424.7186 405.68024,424.7186 Z" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <span class="text-gray-500 ">Multiple
                                                Faces </span>
                                        </div>
                                    </dd>
                                    <dt class="mb-2 text-xl md:text-2xl font-bold">
                                        <?php echo $num_of_multiple_face_detected; ?>
                                    </dt>
                                </div>
                            </dl>

                        </section>
                        <section class>
                            <!-- Start coding here -->
                            <div class="relative sm:rounded-lg overflow-hidden">
                                <div class="flex items-end justify-end p-4">
                                    <button id="dropdownDefault" data-dropdown-toggle="dropdown"
                                        class="text-gray-700 border  shadow hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center"
                                        type="button">
                                        Filter
                                        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown menu -->
                                    <div id="dropdown" class="z-10 hidden w-58 p-3 bg-gray-200 rounded-lg shadow ">
                                        <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    All
                                                </a>
                                            </li>

                                            <li class="flex items-center">
                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Only With Evidence
                                                </a>
                                            </li>

                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Only Violations With No
                                                    Evidence
                                                </a>
                                            </li>
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Only Violations With
                                                    Evidence
                                                </a>
                                            </li>
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    No Face On Camera
                                                </a>
                                            </li>
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Multiple Face Detected
                                                </a>
                                            </li>
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Suspicious Movement Detected
                                                </a>
                                            </li>
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Loud Noise Detected
                                                </a>
                                            </li>
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Speech Detected
                                                </a>
                                            </li>
                                            <li class="flex items-center">

                                                <a href class="ml-2 text-sm font-medium text-gray-900 ">
                                                    Tab Switch
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-md text-left text-gray-600">
                                        <thead class="text-lg text-gray-700 uppercase bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">VIOLATION
                                                    TYPE</th>
                                                <th scope="col" class="px-4 py-3">OCCURRED
                                                    AT</th>
                                                <th scope="col" class="px-24 py-3">EVIDENCE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach ($quiz_activities as $activity){
                                                    echo '
                                                    <div class="modal">
                                                                <span class="close">&times;</span>
                                                                <img class="modal-content">
                                                            </div>  
                                                    ';
                                                    $activity_type = $activity->activity_type;
                                                    $activity_time = DateTime::createFromFormat('Y-m-d H:i:s', $activity->event_datetime);
                                                    $formatted_activity_time = $activity_time->format('d M g:i:s A');
                                                    $activity_timestamp = $activity->timestamp;

                                                    //echo $activity_time . '</br>';

                                                    switch ($activity_type) {
                                                        case 1:
                                                            $activity_name = 'Did not share screen';
                                                            break;
                                                        case 2:
                                                            $activity_name = 'Shared Screen';
                                                            break;
                                                        case 3:
                                                            $activity_name = 'Stops Sharing';
                                                            break;
                                                        case 4:
                                                            $activity_name = 'Tab Switch';
                                                            break;
                                                        case 5:
                                                            $activity_name = 'Tab switch but not shared';
                                                            break;
                                                        case 6:
                                                            $activity_name = 'Camera permission denied';
                                                            break;
                                                        case 7:
                                                            $activity_name = 'Camera permission denied during quiz';
                                                            break;
                                                        case 8:
                                                            $activity_name = 'No Face';
                                                            break;
                                                        case 9:
                                                            $activity_name = 'Multiple Face';
                                                            break;
                                                        case 10:
                                                            $activity_name = 'Suspicious Movement';
                                                            break;
                                                        case 11:
                                                            $activity_name = 'Microphone permission denied';
                                                            break;
                                                        case 12:
                                                            $activity_name = 'Microphone permission denied during quiz';
                                                            break;
                                                        case 13:
                                                            $activity_name = 'Speech detected';
                                                            break;
                                                        case 14:
                                                            $activity_name = 'Loud noise';
                                                            break;
                                                    }

                                                    // Assuming $activity->evidence contains the URL
                                                    $url = $activity->evidence;
                                                    $evidence = $activity->evidence;

                                                    // Get the file extension from the URL
                                                    $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

                                                    // Output the file extension
                                                    echo $extension;

                                                    if ($extension === "png" || $extension === "webm"){
                                                        if ($activity_type >= 1 && $activity_type <= 5){
                                                            $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/screen_capture_evidence/';
                                                        }
                                                        if ($activity_type >= 6 && $activity_type <= 10){
                                                            $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/camera_capture_evidence/';
                                                        }
                                                    }
                                                    else if ($extension === "wav"){
                                                        $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/microphone_capture_evidence/';
                                                    }
                                                    // else{
                                                    //     $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/microphone_capture_evidence/';
                                                    // }

                                                    
                                                    
                                                    // IMAGE TYPE
                                                    if ($extension === "png"){
                                                            echo '                  
                                                                <tr class="border-b">
                                                                    <th scope="row" class="px-4 py-10 font-medium whitespace-nowrap">
                                                                        '. $activity_name .'
                                                                    </th>
                                                                    <td class="px-4 py-10">
                                                                        '. $formatted_activity_time .'
                                                                        <br>
                                                                        '. $activity_timestamp .'
                                                                    </td>
                                                                    <td class="px-24 py-10">
                                                                        <svg width="50px" height="50px" viewBox="0 0 24 24">
                                                                        <img class="popup-image" width="160px" height="160px" src="'.$directory . urlencode($evidence).'" alt="Image">
                                                                        </svg>
                                                                        <span class>
                                                                            <h6 class="text-xs">The
                                                                                evidence has not
                                                                                been captured.</h6>
                                                                        </span>
                                                                        <span>
                                                                            <a href class="text-xs text-blue-700">
                                                                                Learn Why?
                                                                            </a>
                                                                        </span>

                                                                    </td>
                                                                </tr>
                                                            ';
                                                        
                                                    }
                                                    else if ($extension === "wav"){
                                                        echo '
                                                            <tr class="bg-gray-300">
                                                                <th scope="row" class="px-4 py-10 font-medium whitespace-nowrap">
                                                                    '. $activity_name .'
                                                                </th>
                                                                <td class="px-4 py-10">
                                                                    '. $formatted_activity_time .'
                                                                </td>
                                                                <td class="px-4 py-10">
                                                                    <!--AUDIO HERE-->
                                                                    <audio controls>
                                                                        <source src = "'. $directory . urlencode($evidence) .'" type="audio/wav">
                                                                        Your browser does not
                                                                        support the audio
                                                                        element.
                                                                    </audio>
                                                                </td>
                                                            </tr>
                                                        ';
                                                    }
                                                    else{
                                                        echo '                  
                                                                <tr class="border-b">
                                                                    <th scope="row" class="px-4 py-10 font-medium whitespace-nowrap">
                                                                        '. $activity_name .'
                                                                    </th>
                                                                    <td class="px-4 py-10">
                                                                        '. $formatted_activity_time .'
                                                                    </td>
                                                                    <td class="px-24 py-10">
                                                                    <svg width="50px" height="50px" viewBox="0 0 24 24"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M21 5h-8.8L11 3H4L2.8 5H1v16h20l2.902-12H21zM2 6h1.366l1.2-2h5.868l1.2 2H20v3h-5.6l-2 2H4.35L2 18.015zm20.632 4l-2.42 10H2.39l2.68-8h7.744l2-2z" />
                                                                        <path fill="none" d="M0 0h24v24H0z" />
                                                                    </svg>
                                                                    <span class>
                                                                        <h6 class="text-xs">The
                                                                            evidence has not
                                                                            been captured.</h6>
                                                                    </span>
                                                                    <span>
                                                                        <a href class="text-xs text-blue-700">
                                                                            Learn Why?
                                                                        </a>
                                                                    </span>

                                                                    </td>
                                                                </tr>
                                                            ';
                                                    }
                                                }

                                                foreach ($quiz_session_recordings as $recording){
                                                    // Assuming $activity->evidence contains the URL
                                                    $cam_video = $recording->camera_recording;
                                                    $directory = $CFG->wwwroot . '/local/auto_proctor/proctor_tools/evidences/camera_capture_evidence/';
                                                    $file_path = $directory . urlencode($cam_video);
                                                    $file_handle = @fopen($file_path, 'r');

                                                        echo '
                                                            <!--CAMERA RECORDING-->
                                                            <tr class="bg-gray-300">
                                                                <th scope="row" class="px-4 py-10 font-medium whitespace-nowrap">
                                                                    Camera Recording
                                                                </th>
                                                                <td class="px-4 py-10">
                                                                    '. $formatted_activity_time .'
                                                                </td>
                                                                <td class="px-24 py-10">
                                                            ';
                                                            if ($file_handle !== false) {
                                                                fclose($file_handle);
                                                                echo '
                                                                    <video width="220" height="140" controls autoplay muted>
                                                                        <source src="'. $directory . urlencode($cam_video)  .'" type="video/webm">
                                                                    </video>
                                                                ';
                                                            }
                                                            else{
                                                                echo '
                                                                    <svg width="50px" height="50px" viewBox="0 0 24 24"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M21 5h-8.8L11 3H4L2.8 5H1v16h20l2.902-12H21zM2 6h1.366l1.2-2h5.868l1.2 2H20v3h-5.6l-2 2H4.35L2 18.015zm20.632 4l-2.42 10H2.39l2.68-8h7.744l2-2z" />
                                                                        <path fill="none" d="M0 0h24v24H0z" />
                                                                    </svg>
                                                                    <span class>
                                                                        <h6 class="text-xs">The
                                                                            evidence has not
                                                                            been captured.</h6>
                                                                    </span>
                                                                    <span>
                                                                        <a href class="text-xs text-blue-700">
                                                                            Learn Why?
                                                                        </a>
                                                                    </span>
                                                                ';
                                                            }
                                                            echo'
                                                                </td>
                                                            </tr>
                                                            
                                                        ';
                                                    
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </section>

                    </div>
</main>
<!-- MAIN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<script src="https://flowbite-admin-dashboard.vercel.app//app.bundle.js"></script>
<script>
// Get all the modal elements and images
var modals = document.querySelectorAll(".modal");
var modalImages = document.querySelectorAll(".modal-content");
var popupImages = document.querySelectorAll(".popup-image");

// Loop through each popup image
popupImages.forEach(function(popupImage, index) {
    // Attach click event listener to each popup image
    popupImage.addEventListener("click", function() {
        // Show the corresponding modal for this image
        modals[index].style.display = "block";
        // Set the image source inside the modal
        modalImages[index].src = this.src;
    });

    // Get the <span> element that closes the modal
    var close = modals[index].querySelector(".close");

    // Attach click event listener to each close button
    close.addEventListener("click", function() {
        // Close the modal
        modals[index].style.display = "none";
    });

    // Close the modal when clicking outside the image
    modals[index].addEventListener("click", function(event) {
        if (event.target === this) {
            this.style.display = "none";
        }
    });
});



</script>
