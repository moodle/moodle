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

global $DB, $PAGE, $USER, $CFG;

if (isset($_POST['screen_status'])) {
    //$userid = $USER->id;
    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];

    $screen_status = intval($_POST['screen_status']); // Ensure it's an integer

    $insertData = new stdClass();
    $insertData->userid = $userid;
    $insertData->quizid = $quizid;
    $insertData->attempt = $quizattempt; // Assuming 'attempt' is a field in 'auto_proctor_activity_report_tb'
    $insertData->activity_type = $screen_status; // You need to set the appropriate value for 'activity_type'
    $DB->insert_record('auto_proctor_activity_report_tb', $insertData);

    //echo json_encode(['message' => 'Received screen status: ' . $status]);
    echo '<script>console.log("sdfds");</script>';
    //echo '<script>console.log(' . json_encode(['screen_status' => $screen_status]) . ');</script>';
    exit(); // Terminate the script after sending the response
}
?>