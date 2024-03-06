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
global $DB;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_btn'])) {
    $quiz_id = $_POST['quiz_id'];
  
      // If save button was clicked
      // If the box is checked, update the AP feature field to 1 in the AP table; if it's not checked, update it to 0.
      if(isset($_POST['save_btn'])){
        
        // Monitor tab switching ==========================================================================
          $new_field_value_tab_switching = isset($_POST['enable_monitor_tab_switching']) ? 1 : 0;
  
          // Update the auto_proctor_quiz_tb table with new value
          $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_tab_switching = :new_field_value WHERE quizid = :quiz_id";
          $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_tab_switching);
          $DB->execute($sql, $params);
  
        // Monitor camera =================================================================================
          $new_field_value_camera = isset($_POST['enable_monitor_camera']) ? 1 : 0;
  
          // Update the auto_proctor_quiz_tb table with new value
          $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_camera = :new_field_value WHERE quizid = :quiz_id";
          $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_camera);
          $DB->execute($sql, $params);
  
        // Monitor Microphone =============================================================================
          $new_field_value_microphone = isset($_POST['enable_monitor_microphone']) ? 1 : 0;
  
          // Update the auto_proctor_quiz_tb table with new value
          $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_microphone = :new_field_value WHERE quizid = :quiz_id";
          $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_microphone);
          $DB->execute($sql, $params);
  
        // Strict Mode ====================================================================================
          $new_field_value_strict = isset($_POST['enable_strict_mode']) ? 1 : 0;
  
          // Update the auto_proctor_quiz_tb table with new value
          $sql = "UPDATE {auto_proctor_quiz_tb} SET strict_mode = :new_field_value WHERE quizid = :quiz_id";
          $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_strict);
          $DB->execute($sql, $params);
  
        // Unset the save btn
        unset($_POST['save_btn']);
      }
  }
?>