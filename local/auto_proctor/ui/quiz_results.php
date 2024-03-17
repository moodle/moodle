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
    // Get user user id
    $user_id = $USER->id;

    // Check if the user has a managing role, such as an editing teacher or teacher.
    // Only users with those roles are allowed to create or modify a quiz.
    $managing_context = $DB->get_records_sql(
        'SELECT * FROM {role_assignments} WHERE userid = ? AND roleid IN (?, ?)',
        [
            $user_id,
            3, // Editing Teacehr
            4, // Teacher
        ]
    );


    echo "<script>console.log('courses enrolled: ', " . json_encode(count($managing_context)) . ");</script>";

    // If a user does not have a course management role, there is no reason for them to access the Auto Proctor Dashboard.
    // The user will be redirected to the normal dashboard.
    if (!$managing_context && !is_siteadmin($user_id)) {
        $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
        header("Location: $previous_page");
        exit();
    }

    // Check if user is techer in this course
        $isteacher = false;
        if(!is_siteadmin($user_id)){                
    
            // Loop through the context that the user manages
            foreach ($managing_context as $context) {
    
                // Get the context id of the context
                $context_id = $context->contextid;
                echo "<script>console.log('Managing Course IDhome: ', " . json_encode($context_id) . ");</script>";
    
                // Get instance id of the context from contex table
                $sql = "SELECT instanceid
                    FROM {context}
                    WHERE id= :id
                ";
                $instance_id = $DB->get_fieldset_sql($sql, ['id' => $context_id]);

                //echo $instance_id . "</br>";
                if ($_GET['course_id'] == $instance_id[0]){
                    //break;
                    // echo "is teacher";
                    // echo "</br>";
                    $isteacher = true;
                    break;
                }
            }
        }

        if (!$isteacher && !is_siteadmin($user_id)){
            $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
            header("Location: $previous_page");
            exit();
        }

    if(isset($_GET['course_id']) && isset($_GET['quiz_id'])){
        $course_id = $_GET['course_id'];
        $quiz_name = $_GET['quiz_name'];
        $quiz_id = $_GET['quiz_id'];
        $params = array('course_id' => $course_id);

        // Retrieve all records from AP Table
        $AP_tb = 'auto_proctor_quiz_tb';
        $AP_records = $DB->get_records($AP_tb);

        // SELECTING COURSE FULLNAME
        $sql = "SELECT fullname
            FROM {course}
            WHERE id = :course_id;
        ";
        $course_name = $DB->get_fieldset_sql($sql, $params);

        $sql = "
        SELECT u.id AS user_id,
        CASE WHEN COUNT(c.id) > 0 THEN 'Yes' ELSE 'No' END AS enrolled_in_bs_it
        FROM {user} u
        LEFT JOIN {user_enrolments} ue ON u.id = ue.userid
        LEFT JOIN {enrol} e ON ue.enrolid = e.id
        LEFT JOIN {course} c ON e.courseid = c.id
        LEFT JOIN {course_categories} cc ON c.category = cc.id
        WHERE cc.name = 'Bachelor of Science in Information Technology (Boni Campus)'
        AND u.id = :user_id
        GROUP BY u.id;

    ";
    
    $userid = $USER->id;
    $params = array('user_id' => $userid);
    $is_user_enrolled_in_BSIT = $DB->get_records_sql($sql, $params);

    if (!empty($is_user_enrolled_in_BSIT)) {
        foreach ($is_user_enrolled_in_BSIT as $record) {
            $user_id = $record->user_id;
            $enrolled_status = $record->enrolled_in_bs_it;
            
            if ($enrolled_status === "Yes"){
                //print_r($is_user_enrolled_in_BSIT);
            }
        }
    } 


        // COLLECTING ALL ID OF EXPECTED QUIZ TAKERS
            $sql = "SELECT u.*
                FROM {user} u
                JOIN {user_enrolments} ue ON u.id = ue.userid
                JOIN {enrol} e ON ue.enrolid = e.id
                LEFT JOIN {role_assignments} ra ON u.id = ra.userid
                WHERE e.courseid = :course_id
                AND ra.roleid <> (SELECT id FROM {role} WHERE shortname = 'editingteacher')
            ";

            $params = array('course_id' => $course_id);

            $enrolled_students = $DB->get_records_sql($sql, $params);

            // Initialize an array to store student IDs
            $student_ids = array();

            // Iterate over the results and push IDs into the array
            foreach ($enrolled_students as $student) {
                $student_ids[] = $student->id;
            }

            //echo implode(', ', $student_ids);

            $student_id_placeholders = implode(', ', array_map(function($id) { return ':student_id_' . $id; }, $student_ids));
        

        // SELECTING QUIZ COMPLETERS
            $sql = "SELECT *
                FROM {quiz_attempts}
                WHERE quiz = :quiz_id
                AND userid IN ($student_id_placeholders)
                AND state = 'finished';
            ";

            $params = array('quiz_id' => $quiz_id);

            // Merge student IDs into params array
            foreach ($student_ids as $student_id) {
                $params['student_id_' . $student_id] = $student_id;
            }

            $quiz_completers = $DB->get_records_sql($sql, $params);

            // print_r($quiz_completers);
            // echo "</br>";

        // SELECTING IN PROGRESS QUIZ TAKERS
                $sql = "SELECT *
                FROM {quiz_attempts}
                WHERE quiz = :quiz_id
                AND userid IN ($student_id_placeholders)
                AND state = 'inprogress';
            ";

            $params = array('quiz_id' => $quiz_id);

            // Merge student IDs into params array
            foreach ($student_ids as $student_id) {
                $params['student_id_' . $student_id] = $student_id;
            }

            $inprogress_quiz_takers = $DB->get_records_sql($sql, $params);

            $num_of_inprogress = count($inprogress_quiz_takers);

            // print_r($inprogress_quiz_takers);
            // echo "</br>";

        // NUMBER OF EXEPECTED QUIZ TAKERS
            $num_of_all_students = count($enrolled_students);

        // NUMBER OF QUIZ COMPLETERS
            $num_of_quiz_completers = count($quiz_completers);

        // NUMBER OF STUDENT THAT SUBMITTED THE QUIZ
            $sql = "SELECT *
                FROM {quiz_attempts}
                WHERE quiz = :quiz_id
                AND userid IN ($student_id_placeholders)
                AND attempt = 1
                AND state = 'finished';
            ";

            $params = array('quiz_id' => $quiz_id);

            // Merge student IDs into params array
            foreach ($student_ids as $student_id) {
                $params['student_id_' . $student_id] = $student_id;
            }

            $all_student_submitted = $DB->get_records_sql($sql, $params);

            $num_of_student_submitted = count($all_student_submitted);

        // NUMBER OF STUDENT THAT NOT YET SUBMITTED THE QUIZ
            $num_of_student_unsubmitted = $num_of_all_students - $num_of_student_submitted;

        
        // =========== QUIZ STATUS

            $sql = "SELECT timeclose
                FROM {quiz}
                WHERE id = :quiz_id;
            ";

            $params = array('quiz_id' => $quiz_id);
            $quiz_time_close = $DB->get_fieldset_sql($sql, $params);
            $date_quiz_created = date('j-M g:i A', $quiz_time_close[0]);
            $current_time = date('j-M g:i A');

            if ($date_quiz_created > $current_time){
                $quiz_status = "In progress";
            }

            else {
                $quiz_status = "Complete";
            }

            //echo $current_time;
            

        // ========= SELECT DATE QUIZ CREATED
        $sql = "SELECT timecreated
                FROM {quiz}
                WHERE id = :quiz_id;
            ";

        $params = array('quiz_id' => $quiz_id);
        $date_quiz_created = $DB->get_fieldset_sql($sql, $params);

        // ============ ALL STUDENTS QUIZ ATTEMPTS
        $sql = "SELECT *
                FROM {quiz_attempts}
                WHERE quiz = :quiz_id;
            ";

        $params = array('quiz_id' => $quiz_id);

        $all_quiz_attempts = $DB->get_records_sql($sql, $params);

        $count_quiz_attempts = count($all_quiz_attempts);

        // print_r($all_quiz_attempts);
        // echo "</br>";


    }

?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<link rel="icon" type="image/x-icon" href="/images/favicon.ico">




<main>
                <div class="p-4 bg-white block sm:flex items-center justify-between  lg:mt-1.5 ">
                    <div class="w-full mb-1">
                        <div class="pt-10">
                            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl "><?php echo $quiz_name; ?></h1>
                            <span class="text-base font-normal text-gray-500 ">Report of all test takers and their
                                attempts</span>
                        </div>
                    </div>
                </div>
                <!-- for proctoreing setting -->
                <div class="p-4 bg-white">
                    <div
                        class=" border-t border-gray-400 border-b grid w-full grid-cols-1  mt-4 xl:grid-cols-3 2xl:grid-cols-3">
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Proctoring Settings :
                        
                                    <span id="editSettingsLink"><a href = "<?php echo $CFG->wwwroot . '/local/auto_proctor/ui/auto_proctor_dashboard.php?course_id=' .$course_id . '&quiz_id='. $quiz_id .'&quiz_name='.$quiz_name .'&course_name='.$course_name[0].'&quiz_settings=1';?>" class="text-blue-700 text-base">Edit Settings</a></span></h3>
                                <span
                                    class="text-base font-md font-bold text-gray-700 ">
                                    <div class="flex space-x-6 sm:justify-center mt-2">
                                        <a class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[26px] h-[26px] text-gray-800 " viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="24" height="24" fill="white"/>
                                                <circle cx="12" cy="12" r="9" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 5.5V12H18" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                        </a>
                                        <a class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[28px] h-[28px] text-gray-800 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="gray-800" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h10c.6 0 1-.4 1-1V7c0-.6-.4-1-1-1Zm7 11-6-2V9l6-2v10Z"/>
                                              </svg>
                                              
                                        </a>
                                        <a class="text-gray-500 hover:text-gray-900  ">
                                            <svg fill="#000000" class="w-[26px] h-[26px] text-gray-800 " viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M425.818 709.983V943.41c0 293.551 238.946 532.497 532.497 532.497 293.55 0 532.496-238.946 532.496-532.497V709.983h96.818V943.41c0 330.707-256.438 602.668-580.9 627.471l-.006 252.301h242.044V1920H667.862v-96.818h242.043l-.004-252.3C585.438 1546.077 329 1274.116 329 943.41V709.983h96.818ZM958.315 0c240.204 0 435.679 195.475 435.679 435.68v484.087c0 240.205-195.475 435.68-435.68 435.68-240.204 0-435.679-195.475-435.679-435.68V435.68C522.635 195.475 718.11 0 958.315 0Z" fill-rule="evenodd"/>
                                            </svg>

                                        </a>
                                        <a class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[28px] h-[28px] text-gray-800 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M13 10c0-.6.4-1 1-1a1 1 0 1 1 0 2 1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                <path fill-rule="evenodd" d="M2 6c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v12c0 .6-.2 1-.6 1.4a1 1 0 0 1-.9.6H4a2 2 0 0 1-2-2V6Zm6.9 12 3.8-5.4-4-4.3a1 1 0 0 0-1.5.1L4 13V6h16v10l-3.3-3.7a1 1 0 0 0-1.5.1l-4 5.6H8.9Z" clip-rule="evenodd"/>
                                              </svg>
                                            
                                        </a>
                                        <a class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[18px]  h-[18px] text-gray-800 " viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                                                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                                                    <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-256.000000, -671.000000)" fill="#000000">
                                                        <path d="M265,675 C264.448,675 264,675.448 264,676 C264,676.553 264.448,677 265,677 C265.552,677 266,676.553 266,676 C266,675.448 265.552,675 265,675 L265,675 Z M269,675 C268.448,675 268,675.448 268,676 C268,676.553 268.448,677 269,677 C269.552,677 270,676.553 270,676 C270,675.448 269.552,675 269,675 L269,675 Z M286,679 L258,679 L258,675 C258,673.896 258.896,673 260,673 L284,673 C285.104,673 286,673.896 286,675 L286,679 L286,679 Z M286,699 C286,700.104 285.104,701 284,701 L260,701 C258.896,701 258,700.104 258,699 L258,681 L286,681 L286,699 L286,699 Z M284,671 L260,671 C257.791,671 256,672.791 256,675 L256,699 C256,701.209 257.791,703 260,703 L284,703 C286.209,703 288,701.209 288,699 L288,675 C288,672.791 286.209,671 284,671 L284,671 Z M261,675 C260.448,675 260,675.448 260,676 C260,676.553 260.448,677 261,677 C261.552,677 262,676.553 262,676 C262,675.448 261.552,675 261,675 L261,675 Z" id="browser" sketch:type="MSShapeGroup">
                                            
                                            </path>
                                                    </g>
                                                </g>
                                            </svg>
                                        </a>
                                        <!-- ARROW -->
                                        <a class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[30px] h-[30px] text-gray-800 " viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.3891 8.11096L8.61091 15.8891" stroke="#333333" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16.3891 8.11096L16.7426 12" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16.3891 8.11096L12.5 7.75741" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            
                                        </a>
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center text-base">
                                <h3 class="text-base font-normal text-gray-500 ">Status
                                </h3>
                                <span
                                    class=" text-base font-md font-bold text-gray-700 "><?php echo $quiz_status; ?></span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  ">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Created On</h3>
                                <span
                                    class="text-base font-md font-bold text-gray-700 "><?php echo date('j-M g:i A', $date_quiz_created[0]); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- insert here -->
               
                <div class="p-4 bg-white">
                    <h1 class="px-4 text-xl font-semibold text-gray-900 sm:text-2xl py-0 ">Submission Summary</h1>
                    <div
                        class=" border-t border-gray-400 border-b grid w-full grid-cols-1  mt-4 xl:grid-cols-3 2xl:grid-cols-3">
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Num Started</h3>
                                <span
                                    class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl "><?php echo $num_of_inprogress; ?></span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Num Submitted</h3>
                                <span
                                    class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl "><?php echo $num_of_student_submitted; ?></span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  ">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Num Unsubmitted</h3>
                                <span
                                    class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl "><?php echo $num_of_student_unsubmitted; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-4 items-center justify-between block sm:flex md:divide-x md:divide-gray-100 ">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <input type="text" name="email" id="products-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 "
                                    placeholder="Search">
                            </div>
                        </form>
                    </div>
                    <div class="items-center sm:flex">
                        <div class="flex items-center">
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 uppercase focus:ring-blue-300 font-medium rounded-lg text-xs px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm ">
                    <!-- Table -->
                    <div class="flex flex-col mt-6">
                      <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                          <div class="overflow-hidden shadow sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 ">
                              <thead class="bg-gray-50 ">
                                <tr>
                                    <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                        <div class="flex items-center">
                                          Name
                                          <span class="ml-2">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                              <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                            </svg>
                                          </span>
                                        </div>
                                      </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Email
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Stated at
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Submitted at
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Duration
                                      <span class="ml-2">
                                        
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                    </button>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Trust Score
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  
                                </tr>
                              </thead>
                              <tbody class="bg-white ">
                                <?php
                                    // SORTING FUNCTION SAMPLE
                                    // function compareAttempts($attempt1, $attempt2) {
                                    //     // Calculate duration of attempts
                                    //     $duration1 = $attempt1->timefinish - $attempt1->timestart;
                                    //     $duration2 = $attempt2->timefinish - $attempt2->timestart;
                                        
                                    //     // Compare durations
                                    //     if ($duration1 == $duration2) {
                                    //         return 0;
                                    //     }
                                    //     return ($duration1 < $duration2) ? -1 : 1;
                                    // }
                                    
                                    // // Sort the array of attempts using the custom sorting function
                                    // usort($all_quiz_attempts, 'compareAttempts');

                                    foreach($all_quiz_attempts as $attempt){
                                        $userid = $attempt->userid;

                                        // ======= SELECT USER INFO
                                            $sql = "SELECT *
                                                    FROM {user}
                                                    WHERE id = :userid";

                                            // Parameters for the query
                                            $params = array('userid' => $userid);
                                            $user_info = $DB->get_record_sql($sql, $params);

                                            $user_full_name = $user_info->firstname . ' ' . $user_info->lastname;

                                            $user_email = $user_info->email;

                                        // ======= SELECT USER'S ATTEMPT INFO
                                            $date_start_attempt = date('j-M g:i A',$attempt->timestart);
                                            
                                            if ($attempt->timefinish == 0) {
                                                $date_submitted = "-----------------";
                                            } else {
                                                $date_submitted = date('j-M g:i A', $attempt->timefinish);
                                            }

                                        // ======= COMPUTE DUATION
                                            // Convert the timestamps to Unix timestamp for calculation
                                                $start_timestamp = $attempt->timestart;
                                                $end_timestamp = $attempt->timefinish;

                                                // Calculate the duration in seconds
                                                $duration_seconds = $end_timestamp - $start_timestamp;

                                                // Calculate the duration in days, hours, minutes, and seconds
                                                $duration_days = floor($duration_seconds / (60 * 60 * 24));
                                                $duration_hours = floor(($duration_seconds % (60 * 60 * 24)) / 3600);
                                                $duration_minutes = floor(($duration_seconds % 3600) / 60);
                                                $duration_seconds = $duration_seconds % 60;

                                                // Format the duration
                                                $duration = sprintf("%d days, %02d:%02d:%02d", $duration_days, $duration_hours, $duration_minutes, $duration_seconds);

                                                if ($duration < 0){
                                                    $duration = "-----------------";
                                                }
                                                //echo "Duration: $duration";

                                        echo '
                                            <tr>
                                            <td class="p-4 text-sm font-normal text-gray-900 whitespace-nowrap ">
                                                <span class="font-semibold">'. $user_full_name .'</span>
                                            </td>
                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                '. $user_email .'
                                            </td>
                                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                '. $date_start_attempt .'
                                            </td>
                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                '. $date_submitted .'
                                            </td>
                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                '. $duration .'
                                            </td>
                                            <td class="p-4 whitespace-nowrap">
                                            <a href="' . $CFG->wwwroot . '/local/auto_proctor/ui/auto_proctor_dashboard.php?course_id='. $course_id .'&user_id=' . $userid . '&quiz_id=' . $attempt->quiz . '&quiz_attempt=' . $attempt->attempt . '" class="text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5">View Report</a>
                                            </td>
                                            </tr>
                                        ';
                                    }
                                ?>
                                <!-- <tr class="bg-gray-100 ">
                                    <td class="p-4 text-sm font-normal text-gray-900 whitespace-nowrap ">
                                      <span class="font-semibold">Alvince Arandia</span>
                                    </td>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                      renzidelposo@gmail.com
                                    </td>
                                    <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                      8-Dec 08:59 AM
                                    </td>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                      8-Dec 11:59 PM
                                    </td>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                      30 minutes
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                      <a
                                        class=" text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5">View Report</a>
                                    </td>
                                  </tr> -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                        <!-- card footer -->
                        <div class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between d">
                            <!-- note: do not delete this haha -->
                            <div class="flex items-center mb-4 sm:mb-0">
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center mb-4 sm:mb-0">
                                    <!-- previous 1 -->
                                    <a href="#" class="inline-flex border justify-center p-1 mr-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7 transform -scale-x-1" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.8536 11.1464C13.0488 11.3417 13.0488 11.6583 12.8536 11.8536C12.6583 12.0488 12.3417 12.0488 12.1464 11.8536L8.14645 7.85355C7.95118 7.65829 7.95118 7.34171 8.14645 7.14645L12.1464 3.14645C12.3417 2.95118 12.6583 2.95118 12.8536 3.14645C13.0488 3.34171 13.0488 3.65829 12.8536 3.85355L9.20711 7.5L12.8536 11.1464ZM6.85355 11.1464C7.04882 11.3417 7.04882 11.6583 6.85355 11.8536C6.65829 12.0488 6.34171 12.0488 6.14645 11.8536L2.14645 7.85355C1.95118 7.65829 1.95118 7.34171 2.14645 7.14645L6.14645 3.14645C6.34171 2.95118 6.65829 2.95118 6.85355 3.14645C7.04882 3.34171 7.04882 3.65829 6.85355 3.85355L3.20711 7.5L6.85355 11.1464Z" fill="#6b7280" />
                                        </svg>

                                    </a>
                                    <!--  -->
                                    <!-- previous 2 -->
                                    <a href="#" class="inline-flex border justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                    <!--  -->
                                    <!-- next 1 -->
                                    <a href="#" class="inline-flex justify-center border  p-1 mr-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                    <!--  -->
                                    <!-- next 2 -->
                                    <a href="#" class="inline-flex justify-center border  p-1 mr-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.14645 11.1464C1.95118 11.3417 1.95118 11.6583 2.14645 11.8536C2.34171 12.0488 2.65829 12.0488 2.85355 11.8536L6.85355 7.85355C7.04882 7.65829 7.04882 7.34171 6.85355 7.14645L2.85355 3.14645C2.65829 2.95118 2.34171 2.95118 2.14645 3.14645C1.95118 3.34171 1.95118 3.65829 2.14645 3.85355L5.79289 7.5L2.14645 11.1464ZM8.14645 11.1464C7.95118 11.3417 7.95118 11.6583 8.14645 11.8536C8.34171 12.0488 8.65829 12.0488 8.85355 11.8536L12.8536 7.85355C13.0488 7.65829 13.0488 7.34171 12.8536 7.14645L8.85355 3.14645C8.65829 2.95118 8.34171 2.95118 8.14645 3.14645C7.95118 3.34171 7.95118 3.65829 8.14645 3.85355L11.7929 7.5L8.14645 11.1464Z" fill="#6b7280" />
                                        </svg>
                                    </a>
                                    <span class="text-sm font-normal text-gray-500 ">Page<span class="font-semibold text-gray-900 ">1 of 1 </span>| <span class="font-semibold text-gray-900 pr-1 ">Go to Page</span></span>
                                    <input type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-gray-500 focus:border-gray-500 block w-12  p-2.5  " placeholder="1">
                                </div>
                            </div>
                        </div>
                  </div>
</main>
<script>

</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
        <script src="https://flowbite-admin-dashboard.vercel.app//app.bundle.js"></script>
