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
        global $DB, $PAGE, $USER;

        // Check if the current page is a quiz attempt
        if ($PAGE->cm && $PAGE->cm->modname === 'quiz' && $PAGE->cm->instance) {
            $quizid = $PAGE->cm->instance;

            // Check if the user is starting or reattempting the quiz
            $action = optional_param('attempt', '', PARAM_TEXT);

            if (!empty($action)) {

                // Check if aut0-proctor is activated
                $sql = "SELECT *
                    FROM {auto_proctor_quiz_tb}
                    WHERE quizid = :quizid
                    AND (monitor_tab_switching = 1 OR monitor_camera = 1 OR monitor_microphone = 1)"
                ;

                $params = array('quizid' => $quizid);

                $auto_proctor_activated = $DB->get_records_sql($sql, $params);

                if ($auto_proctor_activated){
                    echo '<script type="text/javascript">';
                    echo 'console.log("AP ACTIVATED");';
                    echo '</script>';

                    // Retrieve the user ID
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
                        $consent_tab_switching = $DB->get_record('auto_proctor_session_consent_tb', array(
                            'userid' => $userid,
                            'quizid' => $quizid,
                            'attempt' => $attemptValue,
                            'consent_tab_switching' => 0, // Check for consent_tab_switching equal to 0
                        ));

                        if($consent_tab_switching){
                            echo 'console.log("Prompt");';
                        }
                        else{
                            echo 'console.log("Not prompt");';
                        }
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