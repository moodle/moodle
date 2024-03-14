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

    // If the user is on the quiz page.
    // The quiz attempt page, quiz taking page, and quiz summary page are all considered as quiz pages.
    public function captureQuizAttempt($userid, $course) {

        // This applies when the user is on a quiz attempt page.
        if ($this->isQuizAttempt()) {
            $quizid = $this->PAGE->cm->instance;
            $action = optional_param('attempt', '', PARAM_TEXT);

            // If the user attempts, reattempts, or continue attempt a quiz.
            // Process the proctoring session.
            if (!empty($action)) {
                $this->processQuizAttempt($quizid, $userid, $course);
            }

            // Reset the user's proctoring session prompted modal status,
            // ensuring that the modal will always appear whenever a user attempts, reattempts, or continues an attempt.
            else{
                $this->refreshProctoringSession($userid);
            }
        }

        // This is applies when the user is not on a quiz page.
        else {
            $this->refreshProctoringSession($userid);
        }
        
    }

    private function isQuizAttempt() {
        return ($this->PAGE->cm && $this->PAGE->cm->modname === 'quiz');
    }

    // For processing proctoring session
    private function processQuizAttempt($quizid, $userid, $course) {
    
        // SQL STATEMENTS
            // SQL parameter
            $params = array('quizid' => $quizid);

            // Check if any of the auto-proctor features are activated.
            // This selects records that have at least one feature activated.
                $sql = "SELECT *
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid
                    AND (monitor_tab_switching = 1 OR monitor_camera = 1 OR monitor_microphone = 1)"
                ;
                $auto_proctor_activated = $this->DB->get_records_sql($sql, $params);

            // Select monitor_tab_switching state
                $sql = "SELECT monitor_tab_switching
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid"
                ;
                $monitor_tab_switching_activated = $this->DB->get_fieldset_sql($sql, $params);

            // Select monitor_tab_switching state
                $sql = "SELECT monitor_camera
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid"
                ;
                $monitor_camera_activated = $this->DB->get_fieldset_sql($sql, $params);

            // Select monitor_microphone state
                $sql = "SELECT monitor_microphone
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid"
                ;
                $monitor_microphone_activated = $this->DB->get_fieldset_sql($sql, $params);

            // Select strict_mode state
                $sql = "SELECT strict_mode
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid
                    AND (strict_mode = 1)"
                ;
                $strict_mode_activated = $this->DB->get_fieldset_sql($sql, $params);


        // Get the course module ID for constructing the URL of the quiz attempt page.
        // This is for forcefully kicking the user out of the quiz.
        // A quiz is a module within a course.
        $cm = get_coursemodule_from_instance('quiz', $quizid, $course->id);
        $cmid = $cm->id;

        // Ensuring that this is not in quiz summary page.
        // So the AP will not be activated when the user is in a quiz summary page.
        if($this->PAGE->cm->instance && $this->PAGE->pagetype !== 'mod-quiz-summary'){

            // Check if autproctor is activated.
            // To prevent the AP process when AP is deactivated.
            if ($auto_proctor_activated){
                // Get the user's current attempt record
                // This is for getting the attempt value.
                $quizattempt = $this->DB->get_record('quiz_attempts', array('userid' => $userid, 'quiz' => $quizid, 'state' => 'inprogress'));

                // Ensuring $quizattempt is not empty before logging
                if (!empty($quizattempt)) {
        
                    // Get attempt value
                    // For data processing
                    $attemptValue = $quizattempt->attempt;

                    // Get quiz url
                    // For quiz redirection from AP setup modal page.
                    $quizattempturl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                    // Select existing proctoring session for this quiz and attempt
                        $sql = "SELECT *
                            FROM {auto_proctor_proctoring_session_tb}
                            WHERE userid = :userid
                            AND quizid = :quizid
                            AND attempt = :attempt"
                        ;
                        $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                        $proctoring_session = $this->DB->get_records_sql($sql, $params);

                    // If none then insert new session record
                    if (empty($proctoring_session)){

                        $insertData = new stdClass();
                        $insertData->userid = $userid;
                        $insertData->quizid = $quizid;
                        $insertData->attempt = $attemptValue;
                        $insert_new_session = $this->DB->insert_record('auto_proctor_proctoring_session_tb', $insertData);
                    }

                    // Select user's prompted_of_modal_setup status
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
                    // To be used in monitor camera session
                        $sql = "SELECT camera_device_id
                            FROM {auto_proctor_proctoring_session_tb}
                            WHERE userid = :userid
                            AND quizid = :quizid
                            AND attempt = :attempt"
                        ;
                        $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                        $chosen_camera_device = $this->DB->get_fieldset_sql($sql, $params);
                    
                    // Pass necessarry data value to js files in form of json
                    // For data processing
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
                        echo '<script> var jsdata = ' . json_encode($jsdata) . '; </script>';

                    // Pass necessarry data value to php files in form of json
                    // Convert the array to JSON
                        $jsdata_json = json_encode($jsdata);


                    // Check if user has been prompted of the AP setup modal.
                    // If not then redirected to prompts page.
                    // The quiz attempt URL will be sent along with the URL of the prompts page for redirecting back to the quiz.
                    if ($prompted_of_modal_setup[0] == 0){
                        $this->redirect($this->CFG->wwwroot . '/local/auto_proctor/ui/prompts.php?data=' . urlencode($jsdata_json));
                    }


                    // Check if monitor tab switching is activated
                    // If yes, then provide the link to the tab monitoring feature tool.
                    if ($monitor_tab_switching_activated[0] == 1) {                 
                        echo '<script type="text/javascript"> console.log("MONITOR TAB ACTIVATED"); </script>';
                        echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
                        echo '<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
                        echo '<script src="' . $this->CFG->wwwroot . '/local/auto_proctor/proctor_tools/tab_monitoring/monitor_tab.js"></script>';
                    }

                    // Check if monitor camera is activated
                    // If yes, then provide the link to the camera monitoring feature tool.
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

                    // Check if microphone camera is activated
                    // If yes, then provide the link to the microphone monitoring feature tool.
                    if ($monitor_microphone_activated[0] == 1){
                        echo '<script type="text/javascript"> console.log("MONITOR MIC ACTIVATED"); </script>';
                        echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
                        echo '<script src="' . $this->CFG->wwwroot . '/local/auto_proctor/proctor_tools/microphone_monitoring/monitor_mic.js"></script>';
                    }
                }
            }
        }
    }

    // Refreshing user's proctoring session prompted_of_modal_setup status
    public function refreshProctoringSession($userid) {
        $update_data = new stdClass();
        $update_data->prompted_of_modal_setup= 0;

        $params = array('userid' => $userid);

        $sql = "UPDATE {auto_proctor_proctoring_session_tb}
        SET prompted_of_modal_setup = :prompted_of_modal_setup
        WHERE userid = :userid";

        $params['prompted_of_modal_setup'] = $update_data->prompted_of_modal_setup;
        $this->DB->execute($sql, $params);
    }

    // URL redirection
    private function redirect($url) {
        redirect($url);
    }
}


function local_auto_proctor_extend_navigation(global_navigation $navigation){
    
    global $DB, $PAGE, $COURSE, $USER, $CFG;

    // Get all course id
    $all_course_id = $DB->get_records_sql('SELECT id FROM {course}');

    // Get the user ID
    $userid = $USER->id;

    // Check if user is taecher of courses with BSIT category
    // If yes then make the AP dashboard accessible
        $sql = "
            SELECT u.id AS user_id,
            CASE WHEN COUNT(c.id) > 0 THEN 'Yes' ELSE 'No' END AS enrolled_in_bs_it
            FROM {user} u
            LEFT JOIN {user_enrolments} ue ON u.id = ue.userid
            LEFT JOIN {enrol} e ON ue.enrolid = e.id
            LEFT JOIN {course} c ON e.courseid = c.id
            LEFT JOIN {course_categories} cc ON c.category = cc.id
            WHERE cc.name = 'Bachelor of Science in Information Technology (Boni Campus)'
            AND u.id = :user_id
            GROUP BY u.id;

        ";

        $params = array('user_id' => $userid);
        $is_user_enrolled_in_BSIT = $DB->get_records_sql($sql, $params);


        if (!empty($is_user_enrolled_in_BSIT)) {
            foreach ($is_user_enrolled_in_BSIT as $record) {
                $user_id = $record->user_id;
                $enrolled_status = $record->enrolled_in_bs_it;
                if ($enrolled_status === "Yes"){
                    break;
                }
            }
        } 

        $access_ap_dashboard = false;
        $capability = 'moodle/course:manageactivities';
        if ($enrolled_status === "Yes"){
            foreach ($all_course_id as $course_id) {
                if (has_capability($capability, context_course::instance($course_id->id), $userid)) {
                    echo "User has the capability '$capability'";
                    $main_node = $navigation->add('Auto-Proctor', '/local/auto_proctor/ui/auto_proctor_dashboard.php');
                    $main_node->nodetype = 1;
                    $main_node->collapse = false;
                    $main_node->forceopen = true;
                    $main_node->isexpandable = false;
                    $main_node->showinflatnavigation = true;
                    $access_ap_dashboard = true;
                    break;
                }
            }
        }

    // Check if user is admin
    // AP dashboard must be accessible
        if (!$access_ap_dashboard){
            if (is_siteadmin($user_id)) {
                $main_node = $navigation->add('Auto-Proctor', '/local/auto_proctor/ui/auto_proctor_dashboard.php');
                $main_node->nodetype = 1;
                $main_node->collapse = false;
                $main_node->forceopen = true;
                $main_node->isexpandable = false;
                $main_node->showinflatnavigation = true;
            }
        }



    // Loop through course IDs and check if the user manages any courses.
    // If the user manages a course, add the 'Auto Proctor Dashboard' button to the navigation bar.

    // foreach ($all_course_id as $course_id) {
    //     foreach ($course_category_ids as $category_id) {
    //         if (has_capability('moodle/course:manageactivities', context_course::instance($course_id->id), $USER->id) && has_capability('moodle/course:manageactivities', context_course::instance($course_category_ids[0]), $USER->id)) {

    //         //if (has_capability('moodle/course:manageactivities', $course_context, $USER->id) && has_capability('moodle/course:manageactivities', $category_context, $USER->id)) {

    //             // Adding the auto-proctor in navigation bar ==================================
    //             $main_node = $navigation->add('Auto-Proctor', '/local/auto_proctor/ui/auto_proctor_dashboard.php');
    //             $main_node->nodetype = 1;
    //             $main_node->collapse = false;
    //             $main_node->forceopen = true;
    //             $main_node->isexpandable = false;
    //             $main_node->showinflatnavigation = true;
    //             break 2;
    //         }
    //     }
    // }

    $quizProctor = new QuizProctor($PAGE, $DB, $CFG, $USER, $COURSE);
    $quizProctor->captureQuizAttempt($USER->id, $COURSE);
}


// Event observer, this check event happened or created
class local_auto_proctor_observer {
    
    // If user created q quiz
    public static function quiz_created($eventdata) {

        // Check if the created module is a quiz
        if ($eventdata->other['modulename'] === 'quiz') {
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