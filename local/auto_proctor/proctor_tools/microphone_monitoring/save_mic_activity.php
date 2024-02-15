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

if (isset($_POST['evidence_name_type'])) {
    $filename = $_POST['filename'];
    $activity_type = $_POST['evidence_name_type']; // Ensure it's an integer
    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];

    switch ($activity_type) {
        case 'speech_detected':
            $activity_type = 11;
            break;
        case 'loud_noise':
            $activity_type = 12;
            break;
        // default:
    }
    
    $insertData = new stdClass();
    $insertData->userid = $userid;
    $insertData->quizid = $quizid;
    $insertData->attempt = $quizattempt; // Assuming 'attempt' is a field in 'auto_proctor_activity_report_tb'
    $insertData->evidence = $filename;
    $insertData->activity_type = $activity_type; // You need to set the appropriate value for 'activity_type'
    $DB->insert_record('auto_proctor_activity_report_tb', $insertData);
}
?>