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

$quiz_id = 1;


        $new_field_value = 1;

        // Update the auto_proctor_quiz_tb table with new value
        $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_tab_switching = :new_field_value WHERE quizid = :quiz_id";
        $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value);
        $DB->execute($sql, $params);


// Enabling auto-proctor features
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  if(isset($_POST['quiz_id'])){
    $quiz_id = $_POST['quiz_id'];

    // Monitor tab switching
      if(isset($_POST['enable_tab_switching'])){

        $new_field_value = 1;

        // Update the auto_proctor_quiz_tb table with new value
        $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_tab_switching = :new_field_value WHERE quizid = :quiz_id";
        $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value);
        $DB->execute($sql, $params);

      }
      else{

        $new_field_value = 0;

        // Update the auto_proctor_quiz_tb table with new value
        $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_tab_switching = :new_field_value WHERE quizid = :quiz_id";
        $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value);
        $DB->execute($sql, $params);
      }
  }
}
?>