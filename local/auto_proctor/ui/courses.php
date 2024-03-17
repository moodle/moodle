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
if (!is_siteadmin($user_id)) {

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
        if ($_GET['course_id'] == $instance_id[0]) {
            //break;
            echo "is teacher";
            echo "</br>";
            $isteacher = true;
            break;
        }
    }
}


if (!$isteacher && !is_siteadmin($user_id)) {
    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
    header("Location: $previous_page");
    exit();
}

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $params = array('course_id' => $course_id);

    // SELECTING COURSE FULLNAME
    $sql = "SELECT fullname
                FROM {course}
                WHERE id = :course_id;
            ";
    $course_name = $DB->get_fieldset_sql($sql, $params);

    // SELECTING COURSE'S QUIZZES
    $sql = "SELECT *
                FROM {auto_proctor_quiz_tb}
                WHERE course = :course_id
                AND archived = 0;
            ";
    $ap_quiz_records = $DB->get_records_sql($sql, $params);

    // Initialize an array to store student IDs
    $course_ids = array();

    // Iterate over the results and push IDs into the array
    foreach ($ap_quiz_records as $record) {
        $course_ids[] = $record->quizid;
    }

    $course_id_placeholders = implode(', ', array_map(function ($id) {
        return ':course_id_' . $id;
    }, $course_ids));

    echo "<script>console.log('quiz_records: ', " . json_encode($quiz_records) . ");</script>";
}
?>
<main>
    <div class="px-4 pt-6 p-2">
        <!-- Card header -->
        <div class="items-center justify-between mt-10 lg:flex">
            <div class="mb-4 lg:mb-0">
                <h3 class="mb-1 text-xl font-bold text-gray-900 text-gray-800">
                    <?php
                    echo  $course_name[0];
                    ?>
                </h3>
                <span class="text-sm font-xs text-gray-500 ">You can see all your tests below</span>
            </div>
            <div class="items-center sm:flex">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <form action="#" method="GET" class=" lg:pl-3">
                            <label for="topbar-search" class="sr-only">Search</label>
                            <div class="relative mt-1 lg:w-72">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 px-2 py-2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input type="text" name="text" id="topbar-search" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 px-4 py-2  text-white " placeholder="Search">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="flex items-center pl-2 mb-2">
                    <button id="dropdownDefault" data-dropdown-toggle="dropdown" class=" sm:mb-0 mr-4 inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2 " type="button">
                        Filter
                        <svg class="w-5 h-5 ml-1 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                        </svg>

                    </button>

                    <!-- Dropdown menu -->
                    <form action="">
                        <div id="dropdown" class="z-10 hidden w-70 p-3 bg-white rounded-lg shadow ">
                            <h6 class="mb-2 text-sm font-medium text-gray-900">
                                Status
                            </h6>
                            <ul class="space-y-2 text-sm " aria-labelledby="dropdownDefault">
                                <li class="inline-block">
                                    <input id="apple" type="checkbox" value="" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500" />
                                    <label for="apple" class="ml-2 text-sm font-medium text-gray-900">
                                        Completed
                                    </label>
                                </li>
                                <li class="inline-block pl-2">
                                    <input id="fitbit" type="checkbox" value="" checked class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500" />
                                    <label for="fitbit" class="ml-2 text-sm font-medium text-gray-900">
                                        In progress
                                    </label>
                                </li>
                                <div>
                                    <h6 class="mb-2 text-sm font-medium text-gray-900">
                                        Date Created
                                    </h6>
                                    <div id="accordion-flush" data-accordion="collapse" data-active-classes="text-black " data-inactive-classes="text-gray-500">
                                        <div id="price-body" class="" aria-labelledby="price-heading">
                                            <div class="flex items-center py-2 space-x-3 font-light border-gray-200 dark:border-gray-600">
                                                <div class="relative">
                                                    <input type="date" id="datepicker-from" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="">
                                                </div>
                                                <div class="relative">
                                                    <input type="date" id="datepicker-to" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="hell">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="flex justify-end items-end py-1 px-4 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-gray-200 rounded-full   hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Filter</button>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="flex flex-col mt-6">
            <div class="overflow-x-auto rounded-lg">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="p-4 text-sm font-bold tracking-wider text-left text-gray-700">
                                        <button onclick="window.location.href='https:#';" class="hover:text-[#FFD66E]">
                                            <div class="flex items-center uppercase text-xs font-medium tracking-wider ">
                                                Name
                                                <span class="ml-2">

                                                    <svg width=" 25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6 9.65685L7.41421 11.0711L11.6569 6.82843L15.8995 11.0711L17.3137 9.65685L11.6569 4L6 9.65685Z" fill="#6b7280" />
                                                        <path d="M6 14.4433L7.41421 13.0291L11.6569 17.2717L15.8995 13.0291L17.3137 14.4433L11.6569 20.1001L6 14.4433Z" fill="#6b7280" />
                                                    </svg>

                                                </span>
                                            </div>
                                        </button>
                                    </th>
                                    <th scope="col" class="p-4 text-sm font-bold tracking-wider text-left text-gray-700">
                                        <button onclick="window.location.href='https:#';" class="hover:text-[#FFD66E]">
                                            <div class="flex items-center uppercase text-xs font-medium tracking-wider ">
                                                Status
                                                <span class="ml-2">

                                                    <svg width=" 25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6 9.65685L7.41421 11.0711L11.6569 6.82843L15.8995 11.0711L17.3137 9.65685L11.6569 4L6 9.65685Z" fill="#6b7280" />
                                                        <path d="M6 14.4433L7.41421 13.0291L11.6569 17.2717L15.8995 13.0291L17.3137 14.4433L11.6569 20.1001L6 14.4433Z" fill="#6b7280" />
                                                    </svg>

                                                </span>
                                            </div>
                                        </button>
                                    </th>
                                    <th scope="col" class="p-4 text-sm font-bold tracking-wider text-left text-gray-700">
                                        <button onclick="window.location.href='https:#';" class="hover:text-[#FFD66E]">
                                            <div class="flex items-center uppercase text-xs font-medium tracking-wider ">
                                                Date Created
                                                <span class="ml-2">

                                                    <svg width=" 25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6 9.65685L7.41421 11.0711L11.6569 6.82843L15.8995 11.0711L17.3137 9.65685L11.6569 4L6 9.65685Z" fill="#6b7280" />
                                                        <path d="M6 14.4433L7.41421 13.0291L11.6569 17.2717L15.8995 13.0291L17.3137 14.4433L11.6569 20.1001L6 14.4433Z" fill="#6b7280" />
                                                    </svg>

                                                </span>
                                            </div>
                                        </button>
                                    </th>
                                    <th scope="col" class="p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500"></th>
                                    <th scope="col" class="p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500"></th>
                                    <th scope="col" class="p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white ">
                                <?php
                                foreach ($ap_quiz_records as $record) {
                                    $sql = "SELECT *
                                                FROM {quiz}
                                                WHERE id = :quizid;";

                                    $quizid = $record->quizid;
                                    $params = array('quizid' => $quizid);
                                    $quiz_record = $DB->get_records_sql($sql, $params);

                                    foreach ($quiz_record as $quiz) {
                                        $timestamp = $quiz->timecreated;
                                        $formatted_date = date("d M Y", $timestamp);

                                        echo "<script>console.log('date: ');</script>";
                                        echo
                                        '<tr class="shadow">
                                                        <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                            <h1>' . $quiz->name . '</h1>
                                                            <span class="font-normal text-[10px] text-center">
                                                                
                                                            </span>
                                                        </td>
                                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                            Completed
                                                        </td>
                                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                            ' . $formatted_date . '
                                                        </td>
                                                        <td class=" whitespace-nowrap">
                                                            <span class="bg-gray-100 text-gray-500 text-xs font-medium mr-2 px-3 py-1 rounded-md border hover:bg-gray-300">
                                                                <a href="' . $CFG->wwwroot  . '/local/auto_proctor/ui/auto_proctor_dashboard.php?course_id=' . $course_id . '&quiz_id=' . $quiz->id . '&quiz_name=' . $quiz->name . '&course_name=' . $course_name[0] . '&quiz_settings=1">SETTINGS</a>
                                                            </span>
                                                        </td>
                                                        <td class=" whitespace-nowrap">
                                                            <span class="bg-blue-600 hover:bg-blue-400 text-gray-100 text-xs font-medium mr-2 px-3 py-1 rounded-md   ">
                                                                <a href="' . $CFG->wwwroot  . '/local/auto_proctor/ui/auto_proctor_dashboard.php?course_id=' . $course_id . '&quiz_id=' . $quiz->id . '&quiz_name=' . $quiz->name . '&quiz_results=1">RESULTS</a>
                                                            </span>
                                                        </td>
                                                        <td class=" whitespace-nowrap">
                                                        <button href="" class="archiveThis" data-quizid="' . $quiz->id . '">
                                                            <span class="text-blue-700 hover:text-blue-900 text-xs font-medium mr-2 px-3 py-1 rounded-md">Archive
                                                            </span>
                                                        </button>
                                                        </td>
                                                    </tr>
                                                ';
                                    }
                                }
                                ?>
                                <!-- 2 -->
                                <!-- <tr class="bg-gray-100">
                                                    <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                        <h1>TEST NAME #2</h1>
                                                        <span class="font-normal text-[10px] text-center">
                                                            <a href="" class="">SHARE</a>
                                                            <a href="" class="pl-10">PREVIEW</a>
                                                        </span>
                                                    </td>
                                                    <td class="p-4 text-sm font-normal text-gray-800 whitespace-nowrap ">

                                                    </td>
                                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                        08 Dec 2023
                                                    </td>
                                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                        course 2
                                                    </td>
                                                    <td class=" whitespace-nowrap">
                                                        <span class="bg-white text-gray-500 text-xs font-medium mr-2 px-3 py-1 rounded-md border">
                                                            <a href="quizSetting.php">SETTINGS</a>
                                                        </span>
                                                        <span class="bg-[#0061A8] text-gray-100 text-xs font-medium mr-2 px-3 py-1 rounded-md   ">
                                                            <a href="">RESULTS</a>
                                                        </span>
                                                    </td>
                                                </tr> -->

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- card footer -->
        <div class="sticky bottom-0 right-0 items-center w-full p-4 pb-2 bg-white border-t border-gray-200 sm:flex sm:justify-between ">
            <!-- note: do not delete this haha -->
            <div class="flex items-center mb-4 sm:mb-0">
            </div>
            <div class="flex items-center space-x-3">
                <div class="flex items-center mb-4 sm:mb-0 gap-1">
                    <!-- previous 2 -->
                    <a href="#" class="inline-flex border justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <!-- next 1 -->
                    <a href="#" class="inline-flex justify-center border  p-1 mr-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <span class="text-sm font-normal text-gray-500 ">Page<span class="font-semibold text-gray-900 ">1 of 1 </span>| <span class="text-sm font-normal text-gray-500 pr-1 ">Go to Page</span></span>
                    <input type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-gray-500 focus:border-gray-500 block w-7 h-7 px-1" placeholder="1">

                </div>
            </div>
        </div>
    </div>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select all elements with class 'archiveThis'
        var archiveLinks = document.querySelectorAll('.archiveThis');

        // Iterate over each 'archiveThis' link
        archiveLinks.forEach(function(link) {
            // Add click event listener
            link.addEventListener('click', function(event) {
                // Prevent the default action of the link (i.e., navigating to href)
                event.preventDefault();
                //createOverlay();
                // Retrieve the quizid from the data attribute
                var quizId = link.getAttribute('data-quizid');

                // Send the quizid to a PHP script via AJAX
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'functions/courses_functions.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        console.log('Quiz archived successfully');
                        //removeOverlay();
                        location.reload();
                    }
                };
                xhr.send('quizid=' + quizId);

                // When page is loading prevent clicking archive button
                // when still loading it will not function
                archiveLinks.removeAttribute('href');
                archiveLinks.disabled = true;

                // Here you can perform further actions like sending the quizId via AJAX
            });
        });
    });

    // Function to create an loading overlay
    function createOverlay() {
        // Check if overlay already exists
        if (!document.getElementById('overlay')) {
            // Create a div element for the overlay
            var overlay = document.createElement('div');

            // Set attributes for the overlay
            overlay.id = 'overlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
            overlay.style.zIndex = '9999';

            // Append the loading animation HTML to the overlay
            overlay.innerHTML = `
                <style>
                    body {
                        font-family: 'Titillium Web', sans-serif;
                        font-size: 18px;
                        font-weight: bold;
                    }
                    .loading {
                        position: absolute;
                        left: 0;
                        right: 0;
                        top: 50%;
                        width: 100px;
                        color: #000;
                        margin: auto;
                        -webkit-transform: translateY(-50%);
                        -moz-transform: translateY(-50%);
                        -o-transform: translateY(-50%);
                        transform: translateY(-50%);
                    }
                    .loading span {
                        position: absolute;
                        height: 10px;
                        width: 84px;
                        top: 50px;
                        overflow: hidden;
                    }
                    .loading span > i {
                        position: absolute;
                        height: 10px;
                        width: 10px;
                        border-radius: 50%;
                        -webkit-animation: wait 4s infinite;
                        -moz-animation: wait 4s infinite;
                        -o-animation: wait 4s infinite;
                        animation: wait 4s infinite;
                    }
                    .loading span > i:nth-of-type(1) {
                        left: -28px;
                        background: black;
                    }
                    .loading span > i:nth-of-type(2) {
                        left: -21px;
                        -webkit-animation-delay: 0.8s;
                        animation-delay: 0.8s;
                        background: black;
                    }
                    @keyframes wait {
                        0%   { left: -7px  }
                        30%  { left: 52px  }
                        60%  { left: 22px  }
                        100% { left: 100px }
                    }
                </style>
                <div class="loading">
                    <p>Please wait</p>
                    <span><i></i><i></i></span>
                </div>`;

            // Append the overlay to the body
            document.body.appendChild(overlay);
        }
    }

    // Function to remove overlay
    function removeOverlay() {
        var overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.parentNode.removeChild(overlay);
        }
    }
</script>
