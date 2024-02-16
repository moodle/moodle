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

require_once(__DIR__ . '/../../../config.php'); // Setup moodle global variable also
require_login();
// Get the global $DB object
global $DB, $PAGE, $USER, $CFG;

require_once($CFG->libdir . '/outputrenderers.php');

// Get required parameters
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url(url:'/local/auto_proctor/prompts.php')); // Set url

// Retrieve the data from the URL parameter
$data_param = optional_param('data', '', PARAM_RAW);

// Decode the JSON data
$jsdata = json_decode(urldecode($data_param), true);

// Access the values
$wwwroot = $jsdata['wwwroot'];
$userid = $jsdata['userid'];
$quizid = $jsdata['quizid'];
$quizattempt = $jsdata['quizattempt'];
$quizattempturl = $jsdata['quizattempturl'];
$monitor_camera_activated = $jsdata['monitor_camera_activated'];

// ====== DEBUUGING PURPOSE
// echo "<script>";
// echo
// "
//         console.log('wwwroot: ', ". json_encode($wwwroot) .");
//         console.log('userid', $userid);
//         console.log('quizid', $quizid);
//         console.log('quizattempt', $quizattempt);
//         console.log('quizattempturl: ', ". json_encode($quizattempturl) .");
// ";
// echo "</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROMPTS</title>
</head>
<body>
<script>
    if (<?php echo $monitor_camera_activated; ?> === 1){
        console.log('prompt cam');
    }
    if (window.screen.isExtended){
        console.log('prompt multiple monitor');
    }
    // Display a dialog box with "Yes" and "No" options
    var userResponse = confirm("Do you consent to share your screen for proctoring purposes?");

    // If aggreed sharing screen
    if (userResponse) {
        var screenshare_consent = 2;
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', <?php echo json_encode($wwwroot . '/local/auto_proctor/proctor_tools/tab_monitoring/save_screen_session.php'); ?>, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // ==== DEBUGGING =====
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    console.log('POST request successful');
                    window.location.href = <?php echo json_encode($quizattempturl); ?>;
                    // You can add further actions if needed
                } else {
                    console.error('POST request failed with status: ' + xhr.status);
                    // Handle the error or provide feedback to the user
                }
            }
        };
        xhr.send('userid=' + <?php echo $userid; ?> + '&quizid=' + <?php echo $quizid; ?> + '&quizattempt=' + <?php echo $quizattempt; ?> + '&screenshare_consent=' + screenshare_consent + '&quizattempturl=' + <?php echo json_encode($quizattempturl); ?>);
    }
    else{
        var screenshare_consent = 1;
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', <?php echo json_encode($wwwroot . '/local/auto_proctor/proctor_tools/tab_monitoring/save_screen_session.php'); ?>, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // ==== DEBUGGING =====
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    console.log('POST request successful');
                    window.location.href = <?php echo json_encode($quizattempturl); ?>;
                    // You can add further actions if needed
                } else {
                    console.error('POST request failed with status: ' + xhr.status);
                    // Handle the error or provide feedback to the user
                }
            }
        };
        xhr.send('userid=' + <?php echo $userid; ?> + '&quizid=' + <?php echo $quizid; ?> + '&quizattempt=' + <?php echo $quizattempt; ?> + '&screenshare_consent=' + screenshare_consent + '&quizattempturl=' + <?php echo json_encode($quizattempturl); ?>);
    }
</script>
</body>
</html>
