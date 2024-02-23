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

class QuizProctor {
    
    private $PAGE;
    private $DB;
    private $CFG;
    private $COURSE;
    private $USER;

    public function __construct($PAGE, $DB, $CFG) {
        $this->PAGE = $PAGE;
        $this->DB = $DB;
        $this->CFG = $CFG;
    }

    public function captureQuizAttempt($userid, $course) {
        if ($this->isQuizAttempt()) {
            $quizid = $this->PAGE->cm->instance;
            $action = optional_param('attempt', '', PARAM_TEXT);

            if (!empty($action)) {
                $this->processQuizAttempt($quizid, $userid, $course);
                echo "<script>console.log('quiz taking', $quizid);</script>";
                echo "<script>console.log('quizid: ', $quizid);</script>";
                echo "<script>console.log('userid: ', $userid);</script>";
                echo "<script>console.log('course: ', " . json_encode($course) .");</script>";
            }
            else{
                $this->refreshProctoringSession($userid);
            }
        }

        else {
            $this->refreshProctoringSession($userid);
        }
        
    }

    private function isQuizAttempt() {
        return ($this->PAGE->cm && $this->PAGE->cm->modname === 'quiz');
    }

    private function processQuizAttempt($quizid, $userid, $course) {
        // Your existing logic for processing quiz attempt
        // ...

        // Example: Log information
        $this->logInformation(['userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue]);
        
        // // // Example: Redirect if needed
        // $this->redirect($redirectUrl);

        // Check if auto-proctor is activated
        $sql = "SELECT *
            FROM {auto_proctor_quiz_tb}
            WHERE quizid = :quizid
            AND (monitor_tab_switching = 1 OR monitor_camera = 1 OR monitor_microphone = 1)"
        ;
        $params = array('quizid' => $quizid);
        $auto_proctor_activated = $this->DB->get_records_sql($sql, $params);

        // Select monitor_tab_switching state
        $sql = "SELECT monitor_tab_switching
            FROM {auto_proctor_quiz_tb}
            WHERE quizid = :quizid"
        ;

        //$monitor_tab_switching_activated = $this->DB->get_records_sql($sql, $params);
        $monitor_tab_switching_activated = $this->DB->get_fieldset_sql($sql, $params);

        // Select monitor_tab_switching state
        $sql = "SELECT monitor_camera
            FROM {auto_proctor_quiz_tb}
            WHERE quizid = :quizid"
        ;

        //$monitor_camera_activated = $this->DB->get_records_sql($sql, $params);
        $monitor_camera_activated = $this->DB->get_fieldset_sql($sql, $params);

        // Select monitor_microphone state
        $sql = "SELECT monitor_microphone
            FROM {auto_proctor_quiz_tb}
            WHERE quizid = :quizid"
        ;

        //$monitor_tab_microphone_activated = $this->DB->get_records_sql($sql, $params);
        $monitor_microphone_activated = $this->DB->get_fieldset_sql($sql, $params);

        // Select strict_mode state
        $sql = "SELECT strict_mode
            FROM {auto_proctor_quiz_tb}
            WHERE quizid = :quizid
            AND (strict_mode = 1)"
        ;

        $strict_mode_activated = $this->DB->get_fieldset_sql($sql, $params);


        // Get the course module ID
        $cm = get_coursemodule_from_instance('quiz', $quizid, $course->id);
        $cmid = $cm->id;

        // Ensure that the AP will not be activated when in quiz summary page
        if($this->PAGE->cm->instance && $this->PAGE->pagetype !== 'mod-quiz-summary'){
            if ($auto_proctor_activated){
                echo '<script type="text/javascript"> console.log("AP ACTIVATED"); </script>';

                // Check if the user has an ongoing quiz attempt
                $quizattempt = $this->DB->get_record('quiz_attempts', array('userid' => $userid, 'quiz' => $quizid, 'state' => 'inprogress'));

                // Check if $quizattempt is not empty before logging
                if (!empty($quizattempt)) {
                    
                    // Get attempt number
                    $attemptValue = $quizattempt->attempt;
                    echo '<script>console.log(' . json_encode(['userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue]) . ');</script>';

                    // Get current url
                    $quizattempturl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                    // Check if there existing is existing proctoring consent record
                    $sql = "SELECT *
                        FROM {auto_proctor_proctoring_session_tb}
                        WHERE userid = :userid
                        AND quizid = :quizid
                        AND attempt = :attempt"
                    ;
                    $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                    $proctoring_session = $this->DB->get_records_sql($sql, $params);

                    // If none then insert new record
                    if (empty($proctoring_session)){
                        echo "<script>console.log('insert new record')</script>";

                        $insertData = new stdClass();
                        $insertData->userid = $userid;
                        $insertData->quizid = $quizid;
                        $insertData->attempt = $attemptValue;
                        $insert_new_session = $this->DB->insert_record('auto_proctor_proctoring_session_tb', $insertData);
                    }
                    else {
                        echo "<script>console.log('selected record: ', " . json_encode($proctoring_session) . ")</script>";
                    }

                    // Select user's setup status
                    // To check if the user's finished setting up
                    $sql = "SELECT prompted_of_modal_setup
                        FROM {auto_proctor_proctoring_session_tb}
                        WHERE userid = :userid
                        AND quizid = :quizid
                        AND attempt = :attempt"
                    ;
                    $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                    $prompted_of_modal_setup = $this->DB->get_fieldset_sql($sql, $params);

                    // Select user's chosen camera
                    $sql = "SELECT camera_device_id
                        FROM {auto_proctor_proctoring_session_tb}
                        WHERE userid = :userid
                        AND quizid = :quizid
                        AND attempt = :attempt"
                    ;
                    $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                    $chosen_camera_device = $this->DB->get_fieldset_sql($sql, $params);

                    echo "<script>console.log('prompted_of_modal_setup: ', " . json_encode($prompted_of_modal_setup[0]) . ");</script>";
                    
                    // Pass necessarry value to js file in form of json
                    $jsdata = array(
                        'wwwroot' => $this->CFG->wwwroot,
                        'userid' => $userid,
                        'quizid' => $quizid,
                        'quizattempt' => $attemptValue,
                        'quizattempturl' => $quizattempturl,
                        'cmid' => $cmid,
                        'strict_mode_activated' => $strict_mode_activated,
                        'monitor_camera_activated' => $monitor_camera_activated[0],
                        'monitor_microphone_activated' => $monitor_microphone_activated[0],
                        'chosen_camera_device' => $chosen_camera_device[0],
                        'monitor_tab_switching_activated' => $monitor_tab_switching_activated[0],
                    );

                    // Send to prompts.php
                    // Convert the array to JSON
                    $jsdata_json = json_encode($jsdata);

                    // Send to js files
                    echo '<script>';
                    echo 'var jsdata = ' . json_encode($jsdata) . ';';
                    echo '</script>';

                    // User has not yet setup the prompt modal setup
                    if ($prompted_of_modal_setup[0] == 0){
                        echo "<script>console.log('promptinggggggg');";
                        $this->redirect($this->CFG->wwwroot . '/local/auto_proctor/ui/prompts.php?data=' . urlencode($jsdata_json));
                    }


                    // Check if monitor tab switching is activated
                    if ($monitor_tab_switching_activated[0] == 1) {                 
                        echo '<script type="text/javascript"> console.log("MONITOR TAB ACTIVATED"); </script>';
                        echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
                        echo '<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
                        echo '<script src="' . $this->CFG->wwwroot . '/local/auto_proctor/proctor_tools/tab_monitoring/monitor_tab.js"></script>';
                    }

                    // Check if monitor camera is activated
                    if ($monitor_camera_activated[0] == 1){
                        echo '<script type="text/javascript"> console.log("MONITOR CAMERA ACTIVATED"); </script>';
                        echo '<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>';
                        echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
                        echo '<script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>';
                        echo '<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>';
                        echo '<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>';
                        echo '<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
                        echo '<script src="' . $this->CFG->wwwroot . '/local/auto_proctor/proctor_tools/camera_monitoring/monitor_cam.js"></script>';
                    }

                    if ($monitor_microphone_activated[0] == 1){
                        echo '<script type="text/javascript"> console.log("MONITOR MIC ACTIVATED"); </script>';
                        echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
                        echo '<script src="' . $this->CFG->wwwroot . '/local/auto_proctor/proctor_tools/microphone_monitoring/monitor_mic.js"></script>';
                    }
                }
            }
        }
    }

    public function refreshProctoringSession($userid) {
        $update_data = new stdClass();
        $update_data->prompted_of_modal_setup= 0;

        // Delete current session
        $params = array('userid' => $userid);

        $sql = "UPDATE {auto_proctor_proctoring_session_tb}
        SET prompted_of_modal_setup = :prompted_of_modal_setup
        WHERE userid = :userid";

        $params['prompted_of_modal_setup'] = $update_data->prompted_of_modal_setup;
        $this->DB->execute($sql, $params);
        //$this->DB->delete_records('auto_proctor_proctoring_session_tb', $params);
    }

    private function logInformation($data) {
        // Log information using echo or any other logging mechanism
        echo '<script>console.log(' . json_encode($data) . ');</script>';
    }

    private function redirect($url) {
        // Redirect to the given URL
        redirect($url);
    }
}


function local_auto_proctor_extend_navigation(global_navigation $navigation){
    
    global $DB, $PAGE, $COURSE, $USER, $CFG;

    // Get all course id
    $all_course_id = $DB->get_records_sql('SELECT id FROM {course}');

    // Get the user ID
    $userid = $USER->id;

    // Loop through course IDs and check if the user manages any courses.
    // If the user manages a course, add the 'Auto Proctor Dashboard' button to the navigation bar.
    foreach ($all_course_id as $course_id) {
        if (has_capability('moodle/course:manageactivities', context_course::instance($course_id->id), $USER->id)) {
            // Adding the auto-proctor in navigation bar ==================================
            $main_node = $navigation->add('Auto-Proctor', '/local/auto_proctor/ui/auto_proctor_dashboard.php');
            $main_node->nodetype = 1;
            $main_node->collapse = false;
            $main_node->forceopen = true;
            $main_node->isexpandable = false;
            $main_node->showinflatnavigation = true;
            break;
        }
    }

    $quizProctor = new QuizProctor($PAGE, $DB, $CFG, $USER, $COURSE);
    $quizProctor->captureQuizAttempt($USER->id, $COURSE);
    //$quizProctor->refreshProctoringSession($USER->id);
        
}


// Event observer, this check event happened or created
class local_auto_proctor_observer {
    
    public static function quiz_created($eventdata) {
        //error_log("Event Data: " . print_r($eventdata, true), 0);

        // Check if the created module is a quiz
        if ($eventdata->other['modulename'] === 'quiz') {
            // Log check
            error_log("quiz created", 0);
    
            // Insert data into mdl_auto_proctor_quiz_tb table
            global $DB;
            $quizId = $eventdata->other['instanceid'];
            $courseId = $eventdata->courseid;

            error_log("Quiz ID: $quizId, Course ID: $courseId", 0);

            $DB->insert_record('auto_proctor_quiz_tb', ['quizid' => $quizId, 'course' => $courseId]);
        }
        else {
            error_log("not quiz", 0);
        }
        // Rebuild caches so that event will be triggered correctly
        purge_all_caches();
    }
}