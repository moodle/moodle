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


// Process enabling feature submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($quiz_records as $quiz_record) {
        $buttonName = 'enable_tab_switching_' . $quiz_record->id;

        // Check which button was clicked
        if (isset($_POST[$buttonName])) {
            // Handle the action for the clicked button
            $quizId = $quiz_record->id;

            // Update the auto_proctor_quiz_tb table using direct SQL query
            $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_tab_switching = 1 WHERE quizid = :quizid";
            $params = array('quizid' => $quizId);
            $DB->execute($sql, $params);

            echo "Tab switching enabled for Quiz ID: {$quizId}";
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
                            <th>Quiz Name</th>
                            <th>Course</th>
                            <th></th>
                        </tr>
                    </thead>';

                echo '<tbody>';
                    foreach ($quiz_records as $quiz_record) {
                        echo '<tr>';
                        echo '<td>' . $quiz_record->id . '</td>';
                        echo '<td>' . $quiz_record->course . '</td>';
                        echo '<td>' . $quiz_record->name. '</td>';
                        echo '<td><button type="submit" name="enable_tab_switching_' . $quiz_record->id . '">Enable Tab Switching</button></td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
            echo '</table>';
        echo '</form>';
    ?>

</body>
</html>