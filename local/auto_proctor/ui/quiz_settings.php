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
global $DB, $USER, $CFG;

    if(isset($_GET['course_id']) && isset($_GET['quiz_id'])){
        $course_id = $_GET['course_id'];
        $quiz_name = $_GET['quiz_name'];
        $course_name = $_GET['course_name'];
        $quiz_id = $_GET['quiz_id'];
        $params = array('course_id' => $course_id);

        // Retrieve all records from AP Table
        $AP_tb = 'auto_proctor_quiz_tb';
        $AP_records = $DB->get_records($AP_tb);

        echo "<script>console.log('course name: ', ". json_encode($course_name) .");</script>";


        // SELECTING COURSE'S QUIZZES
            $sql = "SELECT *
                FROM {quiz}
                WHERE course = :course_id;
            ";
            $quiz_records = $DB->get_records_sql($sql, $params);

            echo "<script>console.log('quiz_records: ', " . json_encode($quiz_records) . ");</script>";
    }

    // Enabling auto-proctor features
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['save_btn'])) {
      $quiz_id = $_GET['quiz_id'];
    
        // If save button was clicked
        // If the box is checked, update the AP feature field to 1 in the AP table; if it's not checked, update it to 0.
        if(isset($_GET['save_btn'])){
          
          // Monitor tab switching ==========================================================================
            $new_field_value_tab_switching = isset($_GET['enable_monitor_tab_switching']) ? 1 : 0;
    
            // Update the auto_proctor_quiz_tb table with new value
            $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_tab_switching = :new_field_value WHERE quizid = :quiz_id";
            $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_tab_switching);
            $DB->execute($sql, $params);
    
          // Monitor camera =================================================================================
            $new_field_value_camera = isset($_GET['enable_monitor_camera']) ? 1 : 0;
    
            // Update the auto_proctor_quiz_tb table with new value
            $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_camera = :new_field_value WHERE quizid = :quiz_id";
            $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_camera);
            $DB->execute($sql, $params);
    
          // Monitor Microphone =============================================================================
            $new_field_value_microphone = isset($_GET['enable_monitor_microphone']) ? 1 : 0;
    
            // Update the auto_proctor_quiz_tb table with new value
            $sql = "UPDATE {auto_proctor_quiz_tb} SET monitor_microphone = :new_field_value WHERE quizid = :quiz_id";
            $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_microphone);
            $DB->execute($sql, $params);
    
          // Strict Mode ====================================================================================
            $new_field_value_strict = isset($_GET['enable_strict_mode']) ? 1 : 0;
    
            // Update the auto_proctor_quiz_tb table with new value
            $sql = "UPDATE {auto_proctor_quiz_tb} SET strict_mode = :new_field_value WHERE quizid = :quiz_id";
            $params = array('quiz_id' => $quiz_id, 'new_field_value' => $new_field_value_strict);
            $DB->execute($sql, $params);

          // Refresh URL
          $new_url = $CFG->wwwroot . "/local/auto_proctor/ui/auto_proctor_dashboard.php?course_id=" . $_GET['course_id'] . "&quiz_id=" . $_GET['quiz_id'] . "&quiz_name=" . $_GET['quiz_name'];
          echo "<script>window.location.href = '{$new_url}';</script>";
        }
    }
?>

<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
  <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
  <title>e-RTU</title>

      <main>
        <div class="p-4 bg-white block sm:flex items-center justify-between  lg:mt-1.5 ">
          <div class="w-full mb-1">
            <div class="mb-4">
              <!-- <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                  <li class="inline-flex items-center">
                    <a href="#" class="inline-flex items-center text-gray-700 hover:text-primary-600 ">
                      <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                      </svg>
                    </a>
                  </li>
                  <li>
                    <div class="flex items-center">
                      <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                      </svg>
                      <a href="#" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 ">Test Result</a>
                    </div>
                  </li>
                </ol>
              </nav> -->
              <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl ">Test Settings</h1>
              <span class="text-base font-normal text-gray-500 ">Change the settings for <?php echo $quiz_name; ?></span>
            </div>
            <hr class="border-b border-gray-400  ">
          </div>
        </div>
        <form>
        <div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="p-4 bg-white block sm:flex items-center">
            <div class="w-full mb-1">
                <div class="p-6 rounded-md">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">Proctoring Settings</h2>
                    <form method = "GET" action="quiz_settings.php">
                      <div class="mb-4">
                          <h3 class="text-sm mb-4 text-gray-900">What gets tracked</h3>
                          <!-- Checkbox for Enable Tab Switching -->
                          <div class="flex items-center mb-2">
                              <input type="checkbox" id="tabSwitching" name  = "enable_monitor_tab_switching" value = "1" class="mr-2"
                              <?php
                                // Get monitor_tab_switching activation value
                                // If the activation value is 1, then check the box.
                                $field_monitor_tab_switching = 'monitor_tab_switching';
                                $field_value_monitor_tab_switching = $DB->get_field($AP_tb, $field_monitor_tab_switching, array('quizid' => $quiz_id));

                                  if($field_value_monitor_tab_switching == 1){
                                    echo "checked";
                                  }    
                                echo '>';
                              ?>
                              <label for="tabSwitching" class="text-gray-900">Enable Tab Switching</label>
                          </div>
                          <p class="text-sm text-gray-600 mb-4">Take screenshot when the test taker switches tabs or applications</p>

                          <!-- Checkbox for Enable Camera -->
                          <div class="flex items-center mb-2">
                              <input type="checkbox" id="camera" name  = "enable_monitor_camera" value = "1" class="mr-2"
                              <?php
                                // Get monitor_camera activation value
                                // If the activation value is 1, then check the box.
                                $field_monitor_camera = 'monitor_camera';
                                $field_value_monitor_camera = $DB->get_field($AP_tb, $field_monitor_camera, array('quizid' => $quiz_id));   

                                if($field_value_monitor_camera == 1){
                                  echo "checked";
                                }
                                echo '>';
                              ?>
                              <label for="camera" class="text-gray-900">Enable Camera</label>
                          </div>
                          <p class="text-sm text-gray-600 mb-4">Take photo when no face is visible or multiple faces are visible</p>

                          <!-- Checkbox for Enable Microphone -->
                          <div class="flex items-center mb-2">
                            <input type="checkbox" id="microphone" class="mr-2" name  = "enable_monitor_microphone" value = "1" 
                            <?php
                                // Get monitor_microphone activation value
                                // If the activation value is 1, then check the box.
                                $field_monitor_microphone = 'monitor_microphone';
                                $field_value_monitor_microphone = $DB->get_field($AP_tb, $field_monitor_microphone, array('quizid' => $quiz_id));   

                                if($field_value_monitor_microphone == 1){
                                  echo "checked";
                                }
                              echo '>';
                            ?>
                            <label for="microphone" class="text-gray-900">Enable Microphone</label>
                          </div>
                          <p class="text-sm text-gray-600 mb-5">Detect when the background noise is loud</p>

                          <!-- Checkbox for Enable Strict Mode -->
                          <div class="flex items-center mb-2">
                              <input type="checkbox" id="strictMode" class="mr-2" name="enable_strict_mode" value="1"
                              <?php
                                // Get monitor_microphone activation value
                                // If the activation value is 1, then check the box.
                                $field_strict_mode = 'strict_mode';
                                $field_value_strict_mode = $DB->get_field($AP_tb, $field_strict_mode, array('quizid' => $quiz_id));   

                                if($field_value_strict_mode == 1){
                                  echo "checked";
                                }
                              echo '>';
                              ?>
                              <label for="strictMode" class="text-gray-900">Enable Strict Mode</label>
                          </div>
                          <p class="text-sm text-gray-600 mb-5">Enabling this option enforces proctoring. If a student
                              disagrees with tracking, the quiz will automatically end.</p>
                        </div>

                      <!-- Pass the quiz id in form for processing in the database -->
                      <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>" />
                      <input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />
                      <input type="hidden" name="quiz_settings" value="1" />
                      <input type="hidden" name="quiz_name" value="<?php echo $quiz_name; ?>" />
                      <!-- Save and Cancel buttons -->
                      <div class="flex justify-start">
                        <button class="bg-[#0061A8] text-white px-4 py-2 rounded-md mr-2" name = "save_btn">Save</button>
                        <button id = "cancelQuizSettings" class="bg-gray-300 text-black px-4 py-2 rounded-md">Cancel</button>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
  document.getElementById("cancelQuizSettings").addEventListener("click", function(event) {
    event.preventDefault();
    //Simulate a click event on the currently selected course link
    var courseLink = document.getElementById("<?php echo $course_id . '_eproctor'; ?>");
    
    // Check if the course link exists
    if (courseLink) {
        // Trigger a click event on the course link
        courseLink.click();
        console.error("FOUND COURSE LINK.");
    } else {
        console.error("Course link not found.");
    }

});
</script>