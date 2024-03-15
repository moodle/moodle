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

// If a user does not have a course management role, there is no reason for them to access the Auto Proctor Dashboard.
// The user will be redirected to the normal dashboard.
if (!$managing_context && !is_siteadmin($user_id)) {
    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
    header("Location: $previous_page");
    exit();
}
// Now, we will retrieve all the context IDs for the instances or context that the user manages.

    // ========= IF USER IS TEACHER
    if(!is_siteadmin($user_id)){
        // Array for the course IDs we will retrieve.
        $course_ids = array();

        // Loop through the context that the user manages
        foreach ($managing_context as $context) {

            // Get the context id of the context
            $context_id = $context->contextid;
            echo "<script>console.log('Managing Course ID: ', " . json_encode($context_id) . ");</script>";

            // Get instance id of the context from contex table
            $sql = "SELECT instanceid
            FROM {context}
            WHERE id= :id";
            $instance_ids = $DB->get_fieldset_sql($sql, ['id' => $context_id]);

            echo "<script>console.log('instance id: ', " . json_encode($instance_ids) . ");</script>";

            // Push the instance_ids into the $course_ids array
            $course_ids = array_merge($course_ids, $instance_ids);

        }

        $course_id_placeholders = implode(',', array_fill(0, count($instance_ids), '?'));
            // GET ALL QUIZZES OF COURSES IN AUTOPROCTOR TABLE
            $sql = "SELECT *
                FROM {auto_proctor_quiz_tb}
                WHERE course IN ($course_id_placeholders);
            ";
        $ap_quiz_records = $DB->get_records_sql($sql, $course_ids);
        echo "<script>console.log('All Course IDs: ', " . json_encode($course_ids) . ");</script>";
    }

    // ======== IF USER IS ADMIN
    if(is_siteadmin($user_id)){
        $course_ids = array();
        $sql = "
            SELECT c.id AS course_id, ctx.id AS context_id
            FROM {course} c
            JOIN {course_categories} cc ON c.category = cc.id
            JOIN {context} ctx ON ctx.instanceid = c.id
            WHERE cc.name = 'Bachelor of Science in Information Technology (Boni Campus)'
        ";

        $courses = $DB->get_records_sql($sql);

        foreach ($courses as $course) {
            $course_ids[] = $course->course_id;
        }

        $course_ids = array_merge($course_ids);
        $course_id_placeholders = implode(',', array_fill(0, count($course_ids), '?'));

        // GET ALL QUIZZES OF COURSES IN AUTOPROCTOR TABLE
        $sql = "SELECT *
            FROM {auto_proctor_quiz_tb}
            WHERE course IN ($course_id_placeholders);
        ";
        $ap_quiz_records = $DB->get_records_sql($sql, $course_ids);

        $quiz_ids = array();

        if ($ap_quiz_records) {
            foreach ($ap_quiz_records as $record) {
                // Push each quiz ID into the array
                $quiz_ids[] = $record->quizid;
            }
        }
        $quiz_id_placeholders = implode(',', array_fill(0, count($quiz_ids), '?'));

    // SELECTING COURSE'S QUIZZES
        $sql = "SELECT *
            FROM {quiz}
            WHERE id IN ($quiz_id_placeholders);
        ";
        $quiz_records = $DB->get_records_sql($sql, $quiz_ids);
    }
    

// Get the wwwroot of the site
$wwwroot = $CFG->wwwroot;

// $num_of_quiz_record = count($quiz_records);
// $num_of_ap_quiz_record = count($ap_quiz_records);

?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<link rel="icon" type="image/x-icon" href="/images/favicon.ico">

            <main>
                <div class=" p-4 items-center  justify-between block sm:flex  mt-16">
                    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl ">ARCHIVES</h1>
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <input type="text" name="email" id="products-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 "
                                    placeholder="Search">
                            </div>
                        </form>
                        <div class="flex items-center justify-center p-4">
                            <button id="dropdownDefault" data-dropdown-toggle="dropdown"
                              class="text-gray-700 border  hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center"
                              type="button">
                              Filter
                              <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                              </svg>
                            </button>
                          
                            <!-- Dropdown menu -->
                            <div id="dropdown" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow ">
                              <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                                <li class="flex items-center">
                                  <input id="apple" type="checkbox" value=""
                                    class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 " />
                          
                                  <label for="apple" class="ml-2 text-sm font-medium text-gray-900 ">
                                    Course name
                                  </label>
                                </li>
                          
                                <li class="flex items-center">
                                  <input id="fitbit" type="checkbox" value=""
                                    class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 " />
                          
                                  <label for="fitbit" class="ml-2 text-sm font-medium text-gray-900 ">
                                    Course name
                                  </label>
                                </li>
                          
                                <li class="flex items-center">
                                  <input id="dell" type="checkbox" value=""
                                    class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 " />
                          
                                  <label for="dell" class="ml-2 text-sm font-medium text-gray-900 ">
                                    Course name
                                  </label>
                                </li>
                                <li class="flex items-center">
                                    <input id="dell" type="checkbox" value=""
                                      class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 " />
                            
                                    <label for="dell" class="ml-2 text-sm font-medium text-gray-900 ">
                                        Course name
                                    </label>
                                  </li>
                        
                              </ul>
                            </div>
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
                                                <th scope="col"
                                                    class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                                    <div class="flex items-center">
                                                        NAME
                                                        <span class="ml-2">
                                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                                id="arrow-circle-down" viewBox="0 0 24 24">
                                                                <path
                                                                    d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </th>
                                                <th scope="col"
                                                    class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                                    <div class="flex items-center">
                                                        COURSE
                                                        <span class="ml-2">
                                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                                id="arrow-circle-down" viewBox="0 0 24 24">
                                                                <path
                                                                    d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </th>
                                                <th scope="col"
                                                class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                                <div class="flex items-center">
                                                    PROCTOR
                                                    <span class="ml-2">
                                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                            id="arrow-circle-down" viewBox="0 0 24 24">
                                                            <path
                                                                d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </th>
                                                <th scope="col"
                                                    class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                                    <div class="flex items-center">
                                                        DATE CREATED
                                                        <span class="ml-2">
                                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                                id="arrow-circle-down" viewBox="0 0 24 24">
                                                                <path
                                                                    d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </th>
                                                <th scope="col"
                                                    class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                                    <div class="flex items-center">
                                                        RESTORE
                                                        <span class="ml-2">
                                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                                id="arrow-circle-down" viewBox="0 0 24 24">
                                                                <path
                                                                    d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody class="bg-white ">
                                            <?php
                                                foreach($ap_quiz_records as $ap_quiz){
                                                    $sql = "SELECT name
                                                        FROM {quiz}
                                                        WHERE id = :quiz_id;
                                                    ";
                                                    $param = array('quiz_id' => $ap_quiz->quizid);
                                                    $quiz_name = $DB->get_fieldset_sql($sql, $param);

                                                    $sql = "SELECT shortname
                                                        FROM {course}
                                                        WHERE id = :course_id;
                                                    ";
                                                    $param = array('course_id' => $ap_quiz->course);
                                                    $course_name = $DB->get_fieldset_sql($sql, $param);

                                                    // SELECT TEACHERS OR QUIZ
                                                    $teacher_role_id = 3;
                                                    $editing_teacher_role_id = 4; 

                                                    $sql = "SELECT DISTINCT u.*
                                                            FROM {user} u
                                                            INNER JOIN {role_assignments} ra ON ra.userid = u.id
                                                            INNER JOIN {context} ctx ON ctx.id = ra.contextid
                                                            INNER JOIN {course} c ON c.id = ctx.instanceid
                                                            WHERE c.id = :course_id
                                                            AND (ra.roleid = :teacher_role_id OR ra.roleid = :editing_teacher_role_id)";

                                                    // Parameters for the SQL query
                                                    $params = array(
                                                        'course_id' => $ap_quiz->course,
                                                        'teacher_role_id' => $teacher_role_id,
                                                        'editing_teacher_role_id' => $editing_teacher_role_id
                                                    );

                                                    $course_teacher = $DB->get_records_sql($sql, $params);

                                                    // SELECT THE DATE CREATED OF QUIZ
                                                    $sql = "SELECT timecreated
                                                        FROM {quiz}
                                                        WHERE id = :quiz_id;
                                                    ";
                                                    $param = array('quiz_id' => $ap_quiz->quizid);
                                                    $date_created = $DB->get_fieldset_sql($sql, $param);

                                                    //print_r($course_teacher);

                                                    echo '
                                                        <tr>
                                                            <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                                <h1>'. $quiz_name[0].'</h1>
                                                                <span class="font-normal text-[10px] text-center">
                                                                    <a href="" class="pl-2">PREVIEW</a>
                                                                </span>
                                                            </td>
                                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                                '. $course_name[0] .'
                                                            </td>
                                                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                            '
                                                        ;
                                                            foreach($course_teacher as $teacher){
                                                                $teacher_fullname = $teacher->firstname . ' ' . $teacher->lastname;

                                                                echo $teacher_fullname;
                                                            }
                                                    echo '
                                                            </td>
                                                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                                '. date("d M Y", $date_created[0]) .'
                                                            </td>
                                                            <td class="p-4 text-sm font-normal text-blue-700 whitespace-nowrap ">
                                                                <a href="">Restore</a>
                                                            </td>
                                                        </tr>
                                                    ';
                                                }
                                            ?>
                                            <!-- <tr>
                                                <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                    <h1>QUIZ NO .2</h1>
                                                    <span class="font-normal text-[10px] text-center">
                                                        <a href="" class="pl-2">PREVIEW</a>
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                    Data Science
                                                </td>
                                                <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                    Al Vince R. Arandia
                                                </td>
                                                <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                    08 Dec 2023
                                                </td>
                                                <td class="p-4 text-sm font-normal text-blue-700 whitespace-nowrap ">
                                                        <a href="">Restore</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                    <h1>QUIZ NO .3</h1>
                                                    <span class="font-normal text-[10px] text-center">
                                                        <a href="" class="pl-2">PREVIEW</a>
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                    Data Science
                                                </td>
                                                <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                    Al Vince R. Arandia
                                                </td>
                                                <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                    08 Dec 2023
                                                </td>
                                                <td class="p-4 text-sm font-normal text-blue-700 whitespace-nowrap ">
                                                    <a href="">Restore</a>
                                                </td>
                                            </tr> -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- card footer -->
                    <div
                        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between d">
                        <!-- note: do not delete this haha -->
                        <div class="flex items-center mb-4 sm:mb-0">
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center mb-4 sm:mb-0">
                                <!-- previous 1 -->
                                <!--  -->
                                <!-- previous 2 -->
                                <a href="#"
                                    class="inline-flex border justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                <!--  -->
                                <!-- next 1 -->
                                <a href="#"
                                    class="inline-flex justify-center border  p-1 mr-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                <span class="text-sm font-normal text-gray-500 ">Page<span
                                        class="font-semibold text-gray-900 ">1 of 1 </span>| <span
                                        class="font-semibold text-gray-900 pr-1 ">Go to Page</span></span>
                                <input type="text" id="first_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-gray-500 focus:border-gray-500 block w-12  p-2.5  "
                                    placeholder="1">
                            </div>
                        </div>
                    </div>
                </div>
            </main>
       
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<script src="https://flowbite-admin-dashboard.vercel.app//app.bundle.js"></script>
