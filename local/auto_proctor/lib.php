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

function local_auto_proctor_extend_navigation(global_navigation $navigation){
    
    // Acces control for admin only
    // if(!has_capability('moodle/site:config', context_system::instance())){
    //     return;
    // }

    // Adding the auto-proctor in navigation bar ==================================
        $main_node = $navigation->add('Auto-Proctor', '/local/auto_proctor/auto_proctor_dashboard.php');
        $main_node->nodetype = 1;
        $main_node->collapse = false;
        $main_node->forceopen = true;
        $main_node->isexpandable = false;
        $main_node->showinflatnavigation = true;


    // Capture student quiz attempt ========================================
        global $DB, $PAGE, $USER, $CFG;

        // Check if the current page is a quiz attempt
        if ($PAGE->cm && $PAGE->cm->modname === 'quiz' && $PAGE->cm->instance) {
            $quizid = $PAGE->cm->instance;

            // Check if the user is starting or reattempting the quiz
            $action = optional_param('attempt', '', PARAM_TEXT);

            // When attempt, continue attempt, reattempt button was clicked
            if (!empty($action)) {

                // Check if auto-proctor is activated
                $sql = "SELECT *
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid
                    AND (monitor_tab_switching = 1 OR monitor_camera = 1 OR monitor_microphone = 1)"
                ;

                $params = array('quizid' => $quizid);
                $auto_proctor_activated = $DB->get_records_sql($sql, $params);

                if ($auto_proctor_activated){
                    echo '<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
                    echo '<script type="text/javascript">';
                    echo 'console.log("AP ACTIVATED");';
                    echo '</script>';

                    // Get the user ID
                    $userid = $USER->id;

                    // Check if the user has an ongoing quiz attempt
                    $quizattempt = $DB->get_record('quiz_attempts', array('userid' => $userid, 'quiz' => $quizid, 'state' => 'inprogress'));

                    echo '<script type="text/javascript">';
                    echo 'console.log(' . json_encode(['userid' => $userid, 'quizid' => $quizid]) . ');';

                    // Check if $quizattempt is not empty before logging
                    if (!empty($quizattempt)) {

                        // Get attempt number
                        $attemptValue = $quizattempt->attempt;

                        // Check if there is existing record in auto_proctor_session_consent_tb table
                        $sql = "SELECT *
                                FROM {auto_proctor_session_consent_tb}
                                WHERE userid = :userid
                                AND quizid = :quizid
                                AND attempt = :attempt";

                                $params = [
                                    'userid' => $userid,
                                    'quizid' => $quizid,
                                    'attempt' => $attemptValue,
                                ]
                        ;

                        $existing_session = $DB->get_records_sql($sql, $params);

                        // If no record found the insert new record
                        if(!$existing_session){  
                            // Output the results to the browser console
                            echo 'console.log("Insert new session");';

                            // Insert data into auto_proctor_session_consent_tb table
                            $insertData = new stdClass();
                            $insertData->userid = $userid;
                            $insertData->quizid = $quizid;
                            $insertData->attempt = $attemptValue;

                            $DB->insert_record('auto_proctor_session_consent_tb', $insertData);
                        }

                        // TAB_SWITCHING ====================================================================================
                        // Check if there's a matching record in auto_proctor_session_consent_tb
                        // Check if given consent
                        $consent_tab_switching = $DB->get_record('auto_proctor_session_consent_tb', array(
                            'userid' => $userid,
                            'quizid' => $quizid,
                            'attempt' => $attemptValue,
                            'consent_tab_switching' => 0, // Check for consent_tab_switching equal to 0
                        ));

                        // $given_consent_tab_switching = $DB->get_record('auto_proctor_session_consent_tb', array(
                        //     'userid' => $userid,
                        //     'quizid' => $quizid,
                        //     'attempt' => $attemptValue,
                        //     'consent_tab_switching' => 1, // Check for consent_tab_switching equal to 1
                        // ));

                        if($consent_tab_switching){
                            // // Redirect to prompt page
                            echo 'console.log("Prompt");';
                            // echo 'var consent = confirm("Do you want to share your screen?");';
                            // echo 'if (consent) {';
                            // echo '  window.location.href = "' . $CFG->wwwroot . '/local/auto_proctor/prompts.php?attempt=' . $attemptValue . '";';
                            // echo '}';

                            // Prompt to consent screen sharing
                            echo "let screenShared = false;";
                            echo "let screenStream = null;";
                            echo "let videoElement;";
                            echo "let stopsSharing = false;";

                            // Function to handle screen sharing
                            echo "function startScreenSharing() {";
                                echo "navigator.mediaDevices.getDisplayMedia({ video: true })";
                                echo".then(stream => {";
                                    // Display the stream in a video element
                                    echo "videoElement = document.createElement('video');";
                                    echo "videoElement.srcObject = stream;";
                                    echo "videoElement.autoplay = true;";
                                    //document.getElementById('sharedScreenContainer').appendChild(videoElement);

                                    // Set the shared screen as the focused tab
                                    echo "screenStream = stream;";
                                    echo "screenShared = true;";

                                    // Attach an event listener to detect when the screen sharing is stopped
                                    echo "screenStream.getVideoTracks()[0].onended = () => {
                                        console.log('Screen sharing stopped by the student.');
                                        stopsSharing = true;
                                    };";
                                    

                                    // Attach event listeners for different scenarios
                                    echo "document.addEventListener('visibilitychange', handleVisibilityChange);";
                                    echo "window.addEventListener('focus', handleTabSwitch);";
                                    echo "window.addEventListener('blur', handleTabSwitch);";
                                    $consent = 1;
                                    $consent_tab_switching->consent_tab_switching = 1;
                                    $DB->update_record('auto_proctor_session_consent_tb', $consent_tab_switching);
                                    echo 'console.log(' . json_encode($consent) . ');';
                                    echo "})";
                                    echo ".catch(error => {";
                                    echo "console.error('Error starting screen sharing:', error);";
                                    $consent = 0;
                                    $consent_tab_switching->consent_tab_switching = 0;
                                    $DB->update_record('auto_proctor_session_consent_tb', $consent_tab_switching);
                                    echo 'console.log(' . json_encode($consent) . ');';
                                    echo "});";
                            echo "}";

                            // Function to capture and save the screen
                            echo "function captureAndSaveScreen() {";
                            // Introduce a delay of 500 milliseconds (half second)
                            echo "setTimeout(() => {";
                                // Create a canvas element and draw the video frame onto it
                                echo "const canvas = document.createElement('canvas');";
                                echo "canvas.width = videoElement.videoWidth;";
                                echo "canvas.height = videoElement.videoHeight;";
                                echo "const ctx = canvas.getContext('2d');";
                                echo "ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);";

                                // Convert the canvas content to a data URL
                                echo "const dataUrl = canvas.toDataURL('image/png');";

                                // Send the data to the server using a POST request
                                //echo "fetch('save_capture.php', {";
                                echo "fetch('$CFG->wwwroot/local/auto_proctor/save_capture.php', {";

                                echo "method: 'POST',";
                                echo "headers: {";
                                    echo "'Content-Type': 'application/x-www-form-urlencoded',";
                                echo "},";
                                echo "body: 'dataUri=' + encodeURIComponent(dataUrl),";
                                echo "})";
                                echo ".then(response => response.json())";
                                echo ".then(data => {";
                                echo "console.log('Screen captured and saved as: ' + data.filename);";
                                echo "})";
                                echo ".catch(error => {";
                                echo "console.error('Error saving screen capture:', error);";
                                echo "});";
                            echo "}, 500);"; // 500 milliseconds delay
                            echo "}";

                            // Function to handle tab switch events
                            echo "function handleTabSwitch() {";
                                echo "if (document.hasFocus()) {";
                                    echo "console.log('Tab switched back to focus');";
                                echo "} else {";
                                    echo "console.log('Tab switched');";
                                    // If the screen is shared, capture the shared screen
                                    echo "if (screenShared && !stopsSharing) {";
                                        echo "captureAndSaveScreen();";
                                    echo "}";
                                echo "}";
                            echo "}";

                            // Function to handle document visibility change
                            echo "function handleVisibilityChange() {";
                            // If the document is not visible, capture the shared screen
                            echo "if (document.visibilityState === 'hidden' && screenShared && !stopsSharing) {";
                                echo "captureAndSaveScreen();";
                            echo "}";
                            echo "}";

                            // Attach event listener to the share screen button
                            //document.getElementById('shareScreenButton').addEventListener('click', startScreenSharing);
                            echo "startScreenSharing();";

                        }
                        else{
                            echo 'console.log("Not prompt");';
                        }

                        // if($given_consent_tab_switching){
                        //     echo "
                            
                        //     let tabActive = true;

                        //     // Function to handle tab/window focus
                        //     function handleFocus() {
                        //         if (!tabActive) {
                        //         // Tab/Window is switching back
                        //         console.log('Tab switched back');
                        //         tabActive = true;
                        //         }
                        //     }

                        //     // Function to handle tab/window blur
                        //     function handleBlur() {
                        //         // Tab/Window is switching away
                        //         console.log('Tab switched away');
                        //         tabActive = false;
                        //     }

                        //     // Attach event listeners
                        //     window.addEventListener('focus', handleFocus);
                        //     window.addEventListener('blur', handleBlur);
                        //     ";
                            
                        // }
                    }

                    echo '</script>';
                }

            }
        }
        
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