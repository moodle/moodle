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
 
if(isset($_POST['userid'])){
    echo "<script>console.log('sent');</script>";
    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];
    $quizattempturl = $_POST['quizattempturl'];
    $chosen_camera_device = $_POST['chosen_camera_device'];
    $chosen_monitor_set_up = $_POST['chosen_monitor_set_up'];

    echo "userid: " . $userid . "</br>";
    echo "quizid: " . $quizid . "</br>";
    echo "quizattempt: " . $quizattempt . "</br>";
    echo "quizattempturl: " . $quizattempturl . "</br>";
    echo "chosen_camera_device: " . $chosen_camera_device . "</br>";
    echo "chosen_monitor_set_up: " . $chosen_monitor_set_up . "</br>";

    switch ($chosen_monitor_set_up) {
        case "have_not_conn_multiple_monitor":
            $chosen_monitor_set_up = 1;
            break;
        case "have_remove_external_monitor":
            $chosen_monitor_set_up = 2;
            break;
        case "continue_with_multiple_monitor":
            $chosen_monitor_set_up = 3;
            break;
        // default:
            
        //     break;
    }

    echo "monitor_set_up: " . $chosen_monitor_set_up;

    $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $quizattempt);

    $update_data = new stdClass();
    $update_data->camera_device_id = $chosen_camera_device;
    $update_data->monitor_setup = $chosen_monitor_set_up;

    // Build the raw SQL update query
    $sql = "UPDATE {auto_proctor_proctoring_session_tb}
            SET camera_device_id = :camera_device_id,
            monitor_setup = :monitor_setup
            WHERE userid = :userid
            AND quizid = :quizid
            AND attempt = :attempt";

    // Add the screenshare_consent value to the parameters array
    $params['camera_device_id'] = $update_data->camera_device_id;
    $params['monitor_setup'] = $update_data->monitor_setup;

    // Execute the raw SQL query
    $update_session_setup = $DB->execute($sql, $params);
}
?>