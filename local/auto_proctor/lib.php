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
        if ($PAGE->cm && $PAGE->cm->modname === 'quiz' && $PAGE->cm->instance && $PAGE->pagetype !== 'mod-quiz-summary') {
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

                // Select monitor_tab_switching state
                $sql = "SELECT *
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid
                    AND (monitor_tab_switching = 1)"
                ;

                $monitor_tab_switching_activated = $DB->get_records_sql($sql, $params);

                // Select monitor_microphone state
                $sql = "SELECT *
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid
                    AND (monitor_microphone = 1)"
                ;

                $monitor_tab_microphone_activated = $DB->get_records_sql($sql, $params);

                if ($auto_proctor_activated){
                    echo '<script type="text/javascript"> console.log("AP ACTIVATED"); </script>';

                    // Get the user ID
                    $userid = $USER->id;

                    // Check if the user has an ongoing quiz attempt
                    $quizattempt = $DB->get_record('quiz_attempts', array('userid' => $userid, 'quiz' => $quizid, 'state' => 'inprogress'));

                    // Check if $quizattempt is not empty before logging
                    if (!empty($quizattempt)) {
                        
                        // Get attempt number
                        $attemptValue = $quizattempt->attempt;
                        echo '<script>console.log(' . json_encode(['userid' => $userid, 'quizid' => $quizid, 'attempt' => $attemptValue]) . ');</script>';

                        // In line js code for backup
                            // echo '<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
                            // echo '<script type="text/javascript">';
                            //     // TAB_SWITCHING ====================================================================================

                            //     echo 'console.log("Prompt");';

                            //     // Prompt to consent screen sharing
                            //     echo"
                            //     let screenShared = false;
                            //     let screenStream = null;
                            //     let videoElement;
                            //     let stopsSharing = false;

                            //     //document.addEventListener('visibilitychange', handleVisibilityChange);

                            //     // function handleVisibilityChange() {
                            //     //     if (document.visibilityState === 'hidden' && !document.hasFocus()) {
                            //     //         if (screenShared && !stopsSharing) {
                            //     //             // Send an AJAX request to your server to indicate screen sharing                                     
                            //     //             captureAndSaveScreen();
                            //     //         }
                            //     //     }
                            //     // }
                                
                            //     function startScreenSharing() {
                            //         navigator.mediaDevices.getDisplayMedia({ video: true })
                            //             .then(stream => {
                            //                 videoElement = document.createElement('video');
                            //                 videoElement.srcObject = stream;
                            //                 videoElement.autoplay = true;

                            //                 screenStream = stream;
                            //                 screenShared = true;

                            //                 screenStream.getVideoTracks()[0].onended = () => {
                            //                     stopsSharing = true;
                            //                     console.log('Screen sharing stopped by the student.');
                            //                     // Send an AJAX request to your server to indicate screen sharing stopped
                            //                     //sendScreenSharingStatus(2); // stops sharing
                            //                     captureAndSaveScreen('stops_sharing_screen');
                            //                 };

                            //                 captureAndSaveScreen('shared_screen'); // Capture the shared screen
                            //                 //sendScreenSharingStatus(1); // shared screen
                            //                 console.log('Consent:', 1);
                            //             })
                            //             .catch(error => {
                            //                 console.error('Error starting screen sharing:', error);
                            //                 // Send an AJAX request to your server to indicate screen sharing error
                            //                 //sendScreenSharingStatus(0); // 0 indicates screen sharing stopped
                            //                 captureAndSaveScreen('did_not_share_screen');
                            //             });
                                        
                            //             //document.addEventListener('visibilitychange', handleVisibilityChange);
                            //             window.addEventListener('focus', handleTabSwitch);
                            //             window.addEventListener('blur', handleTabSwitch);
                            //     }
                                
                            //     function handleTabSwitch() {
                            //         if (document.hasFocus()) {
                            //             console.log('Tab switched back to focus');
                            //         } else {
                            //             console.log('Tab switched');
                            //             if (screenShared && !stopsSharing) {
                            //                 // Capture and save the shared screen when the tab is switched
                            //                 captureAndSaveScreen('tab_switch');
                            //             }
                            //             else{
                            //                 //sendScreenSharingStatus(3);
                            //                 captureAndSaveScreen('tab_switch_screen_not_shared');
                            //             }
                                            
                            //         }
                            //     }

                            //     function sendScreenSharingStatus(screen_status, filename, activity_type) {
                            //         // Send an AJAX request to your server to record screen sharing status
                            //         console.log('Sending screen_status:', screen_status);
                            //         var xhr = new XMLHttpRequest();
                            //         xhr.open('POST', '" . $CFG->wwwroot . "/local/auto_proctor/save_activity.php', true); // Replace with the actual path
                            //         xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            //         xhr.send('screen_status=' + screen_status + '&userid=' + ". $userid ." + '&quizid=' + ". $quizid ." + '&quizattempt=' + ". $attemptValue ." + '&filename=' + encodeURIComponent(filename) + '&activity_type=' + activity_type);
                            //     }

                            //     function generateTimestamp() {
                            //         const now = new Date();
                            //         const options = {
                            //             year: 'numeric',
                            //             month: '2-digit',
                            //             day: '2-digit',
                            //             hour: '2-digit',
                            //             minute: '2-digit',
                            //             second: '2-digit',
                            //             hour12: true,
                            //             timeZoneName: 'short',
                            //         };

                            //         const formatter = new Intl.DateTimeFormat('en-US', options);
                            //         const timestamp = formatter.format(now);

                            //         return { timestamp, milliseconds: now.getMilliseconds() };
                            //     }
                                

                            //     function captureAndSaveScreen(evidence_name_type) {
                            //         if (evidence_name_type !== 'tab_switch_screen_not_shared' && evidence_name_type !== 'did_not_share_screen' && evidence_name_type !== 'stops_sharing_screen'){
                            //             setTimeout(() => {
                            //                 const canvas = document.createElement('canvas');
                            //                 canvas.width = videoElement.videoWidth;
                            //                 canvas.height = videoElement.videoHeight;
                            //                 const ctx = canvas.getContext('2d');
                            //                 ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
                                    
                            //                 const { timestamp, milliseconds } = generateTimestamp();
                            //                 const filename = 'EVD_' + timestamp.replace(/[/:, ]/g, '') + '_' + milliseconds + '_' + evidence_name_type + '.png'; // Custom filename with evidenceType
                                    
                            //                 const dataUrl = canvas.toDataURL('image/png');
                                    
                            //                 fetch('" . $CFG->wwwroot . "/local/auto_proctor/save_capture.php', {
                            //                     method: 'POST',
                            //                     headers: {
                            //                         'Content-Type': 'application/x-www-form-urlencoded',
                            //                     },
                            //                     body: 'dataUri=' + encodeURIComponent(dataUrl) + '&filename=' + encodeURIComponent(filename),
                            //                 })
                            //                 .then(response => response.json())
                            //                 .then(data => {
                            //                     console.log('Screen captured and saved as: ' + data.filename);
                            //                     sendScreenSharingStatus(4, filename, evidence_name_type);
                            //                 })
                            //                 .catch(error => {
                            //                     console.error('Error saving screen capture:', error);
                            //                 });
                            //             }, 500);
                            //         }
                            //         else{
                            //             sendScreenSharingStatus(4, 0,evidence_name_type);
                            //         }
                            //     }
                                                                        

                            //     // Start screen sharing when the script is loaded
                            //     startScreenSharing();";
                            // echo '</script>';

                        
                        // Pass necessarry value to js file in form of json
                        $jsdata = array(
                            'wwwroot' => $CFG->wwwroot,
                            'userid' => $userid,
                            'quizid' => $quizid,
                            'quizattempt' => $attemptValue,
                        );

                        echo '<script>';
                        echo 'var jsdata = ' . json_encode($jsdata) . ';';
                        echo '</script>';

                        if ($monitor_tab_switching_activated){
                            echo '<script type="text/javascript"> console.log("MONITOR TAB ACTIVATED"); </script>';
                            echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
                            echo '<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
                            echo '<script src="' . $CFG->wwwroot . '/local/auto_proctor/monitor_tab.js"></script>';
                        }

                        if ($monitor_tab_microphone_activated){
                            echo '<script type="text/javascript"> console.log("MONITOR MICROPHONE ACTIVATED"); </script>';
                        }
                    }
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