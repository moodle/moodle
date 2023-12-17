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

require_once(__DIR__ . '/../../config.php'); // Setup moodle global variable also
require_login();
// Get the global $DB object
global $DB;

// Retrieve all records from AP Table
    $AP_tb = 'auto_proctor_quiz_tb';
    $AP_records = $DB->get_records($AP_tb);

// Retrieve all records from quiz Table
    $quiz_tb = 'quiz';
    $quiz_records = $DB->get_records($quiz_tb);


// Enabling auto-proctor features
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($quiz_records as $quiz_record) {
        $monitor_tab_switching = 'enable_tab_switching_' . $quiz_record->id;
        $monitor_camera = 'enable_camera_' . $quiz_record->id;
        $monitor_microphone = 'enable_microphone_' . $quiz_record->id;

        // Monitor tab switching
        if (isset($_POST[$monitor_tab_switching])) {
            $quizId = $quiz_record->id;

            // Get monitor_tab_switching activation value
            $field_monitor_tab_switching = 'monitor_tab_switching';
            $field_value_monitor_tab_switching = $DB->get_field($AP_tb, $field_monitor_tab_switching, array('quizid' => $quizId));

            // If activated, then activate, and vice versa
            $new_field_value = ($field_value_monitor_tab_switching == 0) ? 1 : 0;

            // Update the auto_proctor_quiz_tb table with new value
            $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_tab_switching = :new_field_value WHERE quizid = :quizid";
            $params = array('quizid' => $quizId, 'new_field_value' => $new_field_value);
            $DB->execute($sql, $params);

            // Redirect to the same page after processing the form to prevent the form being submitted every refresh
            header("Location: {$_SERVER['PHP_SELF']}");
            exit;
        }
        
        // Monitor camera
        if (isset($_POST[$monitor_camera])) {
            $quizId = $quiz_record->id;

            // Get monitor_tab_switching activation value
            $field_monitor_camera = 'monitor_camera';
            $field_value_monitor_camera = $DB->get_field($AP_tb, $field_monitor_camera, array('quizid' => $quizId));

            // If activated, then activate, and vice versa
            $new_field_value = ($field_value_monitor_camera == 0) ? 1 : 0;

            // Update the auto_proctor_quiz_tb table with new value
            $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_camera= :new_field_value WHERE quizid = :quizid";
            $params = array('quizid' => $quizId, 'new_field_value' => $new_field_value);
            $DB->execute($sql, $params);

            // Redirect to the same page after processing the form to prevent the form being submitted every refresh
            header("Location: {$_SERVER['PHP_SELF']}");
            exit;
        }

        // Monitor microphone
        if (isset($_POST[$monitor_microphone])) {
            $quizId = $quiz_record->id;

            // Get monitor_tab_switching activation value
            $field_monitor_microphone = 'monitor_microphone';
            $field_value_monitor_microphone = $DB->get_field($AP_tb, $field_monitor_microphone, array('quizid' => $quizId));

            // If activated, then activate, and vice versa
            $new_field_value = ($field_value_monitor_microphone == 0) ? 1 : 0;

            // Update the auto_proctor_quiz_tb table with new value
            $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_microphone= :new_field_value WHERE quizid = :quizid";
            $params = array('quizid' => $quizId, 'new_field_value' => $new_field_value);
            $DB->execute($sql, $params);

            // Redirect to the same page after processing the form to prevent the form being submitted every refresh
            header("Location: {$_SERVER['PHP_SELF']}");
            exit;
        }
    }
}
//echo $OUTPUT->header(); // Output header
?>

<!-- Design here, make sure any additional files are in the auto_proctor folder - Angel-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto-Proctor Dashboard</title>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>
<body>

    <?php
        // Display the results in an HTML table
        echo '<form method="post">';
            echo '<table border="1">';
                echo '<thead>
                        <tr>
                            <th>Quiz ID</th>
                            <th>Course ID</th>
                            <th>Quiz Name</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>';

                echo '<tbody>';
                    foreach ($quiz_records as $quiz_record) {
                        echo '<tr>';
                        echo '<td>' . $quiz_record->id . '</td>';
                        echo '<td>' . $quiz_record->course . '</td>';
                        echo '<td>' . $quiz_record->name. '</td>';
                        echo '<td><button type="submit" name="enable_tab_switching_' . $quiz_record->id . '">Monitor Tab Switching</button></td>';
                        echo '<td><button type="submit" name="enable_camera_' . $quiz_record->id . '">Monitor Camera</button></td>';
                        echo '<td><button type="submit" name="enable_microphone_' . $quiz_record->id . '">Monitor Microphone</button></td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
            echo '</table>';
        echo '</form>';
    ?>

</body>
</html>