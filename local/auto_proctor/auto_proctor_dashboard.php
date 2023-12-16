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

// Get the global $DB object
global $DB;

// AP Table
$AP_tb = 'auto_proctor_quiz_tb';

// Retrieve records from auto_proctor_quiz_tb
$records = $DB->get_records($AP_tb);

//echo $OUTPUT->header(); // Output header

?>

<!-- Design ka na dito, make sure mo na yung mga madagdag mo na files nasa loob ng auto_proctor folder - Angel-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto-Proctor Dashboard</title>
</head>
<body>
    
    <?php
        // Display the results in an HTML table
            echo '<table border="1">';
            echo '<thead><tr><th>Quiz ID</th><th>Course</th><!-- Add more columns as needed --></tr></thead>';
            echo '<tbody>';
            foreach ($records as $record) {
                echo '<tr>';
                echo '<td>' . $record->quizid . '</td>';
                echo '<td>' . $record->course. '</td>';
                // Add more columns as needed
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
    ?>
</body>
</html>
