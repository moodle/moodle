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

    echo "userid: " . $userid;
    echo "quizid: " . $quizid;
    echo "quizattempt: " . $quizattempt;
    echo "quizattempturl: " . $quizattempturl;
    echo "chosen_camera_device: " . $chosen_camera_device;

    $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $quizattempt);

    $update_data = new stdClass();
    $update_data->camera_device_id = $chosen_camera_device;

    // Build the raw SQL update query
    $sql = "UPDATE {auto_proctor_proctoring_session_tb}
            SET camera_device_id = :camera_device_id
            WHERE userid = :userid
            AND quizid = :quizid
            AND attempt = :attempt";

    // Add the screenshare_consent value to the parameters array
    $params['camera_device_id'] = $update_data->camera_device_id;

    // Execute the raw SQL query
    $update_camera_device_id = $DB->execute($sql, $params);
}
?>