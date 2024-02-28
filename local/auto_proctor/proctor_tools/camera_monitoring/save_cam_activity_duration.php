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

if (isset($_POST['filename'])) {

    // Initialize all the necessary details about the record.
    $filename = $_POST['filename'];
    $duration = $_POST['duration'];
    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];

    // SQL parameter
    $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $quizattempt, 'filename' => $filename);

    // Create update instance
    $update_data = new stdClass();

    // Initialize that the duration will be the value of the fetch duration from the monitor_cam.js.
    $update_data->duration = $duration;

    // SQL query
    $sql = "UPDATE {auto_proctor_activity_report_tb}
            SET duration = :duration
            WHERE userid = :userid
            AND quizid = :quizid
            AND attempt = :attempt
            AND evidence = :filename";

    // Add the duration in parameter
    $params['duration'] = $update_data->duration;

    // Execute the SQL query
    $update_duration = $DB->execute($sql, $params);

    
    echo "filename: " . $filename;
    echo "</br>";
    echo "duration: " . $duration;
}
?>