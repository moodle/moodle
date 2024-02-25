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

// If the setup data was sent from the setup modal,
// then process the sent data.
if(isset($_POST['userid'])){

    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];
    $quizattempturl = $_POST['quizattempturl'];
    $chosen_camera_device = $_POST['chosen_camera_device'];
    $chosen_monitor_set_up = $_POST['chosen_monitor_set_up'];
    $device_type = $_POST['device_type'];
    $prompted_of_modal_setup = 1;

    echo "userid: " . $userid . "</br>";
    echo "quizid: " . $quizid . "</br>";
    echo "quizattempt: " . $quizattempt . "</br>";
    echo "quizattempturl: " . $quizattempturl . "</br>";
    echo "chosen_camera_device: " . $chosen_camera_device . "</br>";
    echo "chosen_monitor_set_up: " . $chosen_monitor_set_up . "</br>";
    echo "device_type: " . $device_type . "</br>";

    /* MONITOR SETUP GUIDE
        1 = single monitor detected
        2 = continue with multiple monitor
    */
    
    switch ($chosen_monitor_set_up) {
        case "single_monitor_detected":
            $chosen_monitor_set_up = 1;
            break;
        case "continue_with_multiple_monitor":
            $chosen_monitor_set_up = 2;
            break;
    }

    /* DEVICE TYPE GUIDE
        1 = mobile
        2 = tablet
        3 = desktop
    */

    switch ($device_type) {
        case "mobile":
            $device_type = 1;
            break;
        case "tablet":
            $device_type = 2;
            break;
        case "desktop":
            $device_type = 3;
            break;
    }

    // Update the quiz proctoring session in session table

        // SQL paramater
        $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $quizattempt);

        // The data that will be updated.
        $update_data = new stdClass();
        $update_data->camera_device_id = $chosen_camera_device;
        $update_data->monitor_setup = $chosen_monitor_set_up;
        $update_data->prompted_of_modal_setup = $prompted_of_modal_setup;
        $update_data->device_type = $device_type;

        $sql = "UPDATE {auto_proctor_proctoring_session_tb}
                SET camera_device_id = :camera_device_id,
                monitor_setup = :monitor_setup,
                prompted_of_modal_setup = :prompted_of_modal_setup,
                device_type = :device_type
                WHERE userid = :userid
                AND quizid = :quizid
                AND attempt = :attempt";

        // Add the data that will be updated in parameter.
        $params['camera_device_id'] = $update_data->camera_device_id;
        $params['monitor_setup'] = $update_data->monitor_setup;
        $params['prompted_of_modal_setup'] = $update_data->prompted_of_modal_setup;
        $params['device_type'] = $update_data->device_type;

        // SQL execution
        $update_session_setup = $DB->execute($sql, $params);
}
?>