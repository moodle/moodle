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
 
if(isset($_POST['quizattempt'])){
    echo "<script>console.log('sent');</script>";
    $userid = $_POST['userid'];
    $quizid = $_POST['quizid'];
    $quizattempt = $_POST['quizattempt'];
    $screenshare_consent = $_POST['screenshare_consent'];
    $quizattempturl = $_POST['quizattempturl'];

    $params = array('userid' => $userid, 'quizid' => $quizid, 'attempt' => $quizattempt);

    $update_data = new stdClass();
    $update_data->screenshare_consent = $screenshare_consent;

    // Build the raw SQL update query
    $sql = "UPDATE {auto_proctor_proctoring_session_tb}
            SET screenshare_consent = :screenshare_consent
            WHERE userid = :userid
            AND quizid = :quizid
            AND attempt = :attempt";

    // Add the screenshare_consent value to the parameters array
    $params['screenshare_consent'] = $update_data->screenshare_consent;

    // Execute the raw SQL query
    $update_screenshare_consent = $DB->execute($sql, $params);
}
?>