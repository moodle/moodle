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
                $this->deleteProctoringSession($userid);
            }
        }
        else{
            $this->deleteProctoringSession($userid);
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
        $sql = "SELECT *
            FROM {auto_proctor_quiz_tb}
            WHERE quizid = :quizid
            AND (monitor_tab_switching = 1)"
        ;

        $monitor_tab_switching_activated = $this->DB->get_records_sql($sql, $params);

        // Select monitor_microphone state
        $sql = "SELECT *
            FROM {auto_proctor_quiz_tb}
            WHERE quizid = :quizid
            AND (monitor_microphone = 1)"
        ;

        $monitor_tab_microphone_activated = $this->DB->get_records_sql($sql, $params);

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
                    
                    // Pass necessarry value to js file in form of json
                    $jsdata = array(
                        'wwwroot' => $this->CFG->wwwroot,
                        'userid' => $userid,
                        'quizid' => $quizid,
                        'quizattempt' => $attemptValue,
                        'quizattempturl' => $quizattempturl,
                        'cmid' => $cmid,
                        'strict_mode_activated' => $strict_mode_activated,
                    );

                    // Send to prompts.php
                    // Convert the array to JSON
                    $jsdata_json = json_encode($jsdata);

                    // Send to monitor_tab.js
                    echo '<script>';
                    echo 'var jsdata = ' . json_encode($jsdata) . ';';
                    echo '</script>';

                    // Check if monitor tab switching is activated
                    if ($monitor_tab_switching_activated) {
                    
                        // Check if there existing is existing screen_share proctoring consent record
                        $sql = "SELECT screenshare_consent
                            FROM {auto_proctor_proctoring_session_tb}
                            WHERE userid = :userid
                            AND quizid = :quizid
                            AND attempt = :attempt"
                        ;
                        $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                        $screenshare_consent = $this->DB->get_records_sql($sql, $params);

                        // If there is no session record
                        // Insert new session
                        if (empty($screenshare_consent)) {
                            $insertData = new stdClass();
                            $insertData->userid = $userid;
                            $insertData->quizid = $quizid;
                            $insertData->attempt = $attemptValue;
                            $insert_new_session = $this->DB->insert_record('auto_proctor_proctoring_session_tb', $insertData);

                            echo "<script> console.log('no recorded session'); </script>";
                        }

                        // Select the screen_share proctoring consent value
                        $sql = "SELECT screenshare_consent
                            FROM {auto_proctor_proctoring_session_tb}
                            WHERE userid = :userid
                            AND quizid = :quizid
                            AND attempt = :attempt"
                        ;
                        $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                        $screenshare_consent = $this->DB->get_fieldset_sql($sql, $params);

                        // 0 not yet prompted the consent modal
                        // 1 = did not agree to consent
                        // 2 = agreed to consent

                        // If not yet prompted then redirect to prompts page
                        if ($screenshare_consent[0] == 0) {
                            //redirect($CFG->wwwroot . '/mod/quiz/view.php?id=' . $quizid);
                            $this->redirect($this->CFG->wwwroot . '/local/auto_proctor/ui/prompts.php?data=' . urlencode($jsdata_json));
                            echo "<script> console.log('consent is 0'); </script>";
                        }

                        // If did not agreed to consent
                        if ($screenshare_consent[0] == 1){
                            
                            // Checl if strict mode was activated
                            // If yes then redirect it to quiz attempt review page
                            if ($strict_mode_activated[0] == 1){

                                // Delete current session
                                $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue);
                                $this->DB->delete_records('auto_proctor_proctoring_session_tb', $params);

                                $this->redirect($this->CFG->wwwroot . '/mod/quiz/view.php?id=' . $cmid);
                                //echo "<script>console.log(". $CFG->wwwroot . '/mod/quiz/view.php?id=' . $quizid .");</script>";
                            }
                            // If not activated then redirect to quiz
                        }

                        // If agreed to consent
                        // Then prompt the screen sharing for proctoring
                        else if ($screenshare_consent[0] == 2){
                            echo '<script type="text/javascript"> console.log("MONITOR TAB ACTIVATED"); </script>';
                            echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
                            echo '<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
                            echo '<script src="' . $this->CFG->wwwroot . '/local/auto_proctor/proctor_tools/tab_monitoring/monitor_tab.js"></script>';
                        }
                    }
                }
            }
        }
    }

    public function deleteProctoringSession($userid) {
        // Delete current session
        $params = array('userid' => $userid);
        $this->DB->delete_records('auto_proctor_proctoring_session_tb', $params);
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
    //$quizProctor->deleteProctoringSession($USER->id);
        
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