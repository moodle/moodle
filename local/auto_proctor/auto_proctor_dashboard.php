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

// Convert PHP array/object to JSON for JavaScript
$quiz_records_json = json_encode($quiz_records);

echo "<script>console.log($quiz_records_json);</script>";


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
<!-- <!DOCTYPE html>
<html lang="en"> -->

<!-- <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto-Proctor Dashboard</title>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<!-- <script src="https://cdn.tailwindcss.com"></script> -->

<!-- </head> -->

<!-- <body> -->

<!-- <?php
        // Display the results in an HTML table
        // echo '<form method="post">';
        // echo '<table border="1">';
        // echo '<thead>
        // <tr>
        // <th>Quiz ID</th>
        // <th>Course ID</th>
        // <th>Quiz Name</th>
        //     <th></th>
        //     <th></th>
        //     <th></th>
        // </tr>
        // </thead>';

        // echo '<tbody>';
        // foreach ($quiz_records as $quiz_record) {
        //     echo '<tr>';
        //     echo '<td>' . $quiz_record->id . '</td>';
        //     echo '<td>' . $quiz_record->course . '</td>';
        //     echo '<td>' . $quiz_record->name . '</td>';
        //     echo '<td><button type="submit" name="enable_tab_switching_' . $quiz_record->id . '">Monitor Tab Switching</button></td>';
        //     echo '<td><button type="submit" name="enable_camera_' . $quiz_record->id . '">Monitor Camera</button></td>';
        //     echo '<td><button type="submit" name="enable_microphone_' . $quiz_record->id . '">Monitor Microphone</button></td>';
        //     echo '</tr>';
        // }
        //         echo '</tbody>';
        //         echo '</table>';
        //         echo '</form>';
        //         
        // 

        ?>
// </body>
</html> -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <title>e-RTU</title>
</head>

<body class="bg-white">
    <!-- NAVAGATION BAR -->
    <nav class="fixed z-30 w-full bg-gray-800 border-b border-gray-200">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar" class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100  focus:ring-2 focus:ring-gray-100  ">
                        <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <svg id="toggleSidebarMobileClose" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <a href="#" class="flex ml-2 md:mr-24">
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-[#FFD66E]">e-RTU</span>
                    </a>
                </div>
                <div class="flex items-center">

                    <button id="toggleSidebarMobileSearch" type="button" class="p-2 text-white rounded-lg lg:hidden hover:text-gray-900 hover:bg-gray-100 ">
                    </button>

                    <button type="button" data-dropdown-toggle="notification-dropdown" class="p-2 text-white rounded-lg hover:text-gray-900 hover:bg-gray-100 ">
                        <span class="sr-only">View notifications</span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                    </button>

                    <div class="z-20 z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-white divide-y divide-gray-100 rounded shadow-lg" id="notification-dropdown">
                        <div class="block px-4 py-2 text-base font-medium text-center text-gray-700 bg-gray-50 ">
                            Notifications
                        </div>
                        <div>
                            <a href="#" class="flex px-4 py-3 border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11" src="https://flowbite-admin-dashboard.vercel.app/images/users/bonnie-green.png" alt="Jese image">
                                    <div class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 border border-white rounded-full bg-primary-700 ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z"></path>
                                            <path d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 ">New message from <span class="font-semibold text-gray-900 text-white">Bonnie Green</span>: "Hey, what's up? All set for the presentation?"</div>
                                    <div class="text-xs font-medium text-primary-700 ">a few moments ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3  border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11" src="https://flowbite-admin-dashboard.vercel.app/images/users/jese-leos.png" alt="Jese image">
                                    <div class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-gray-900 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span class="font-semibold text-gray-900 text-white">Jese leos</span> and <span class="font-medium text-gray-900 text-white">5 others</span> started following you.</div>
                                    <div class="text-xs font-medium text-primary-700 ">10 minutes ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3 border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11" src="https://flowbite-admin-dashboard.vercel.app/images/users/joseph-mcfall.png" alt="Joseph image">
                                    <div class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-red-600 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span class="font-semibold text-gray-900 text-white">Joseph Mcfall</span> and <span class="font-medium text-gray-900 text-white">141 others</span> love your story. See it and view more stories.</div>
                                    <div class="text-xs font-medium text-primary-700 ">44 minutes ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3 border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11" src="https://flowbite-admin-dashboard.vercel.app/images/users/leslie-livingston.png" alt="Leslie image">
                                    <div class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-green-400 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span class="font-semibold text-gray-900 text-white">Leslie Livingston</span> mentioned you in a comment: <span class="font-medium text-primary-700 ">@bonnie.green</span> what do you say?</div>
                                    <div class="text-xs font-medium text-primary-700 ">1 hour ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3 hover:bg-gray-100 ">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11" src="https://flowbite-admin-dashboard.vercel.app/images/users/robert-brown.png" alt="Robert image">
                                    <div class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-purple-500 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span class="font-semibold text-gray-900 text-white">Robert Brown</span> posted a new video: Glassmorphism - learn how to implement the new design trend.</div>
                                    <div class="text-xs font-medium text-primary-700 ">3 hours ago</div>
                                </div>
                            </a>
                        </div>
                        <a href="#" class="block py-2 text-base font-normal text-center text-gray-900 bg-gray-50 hover:bg-gray-100 text-white ">
                            <div class="inline-flex items-center ">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                View all
                            </div>
                        </a>
                    </div>
                    <button type="button" data-dropdown-toggle="apps-dropdown" class="hidden p-2 text-white rounded-lg sm:flex hover:text-gray-900 hover:bg-gray-100 ">
                        <span class="sr-only">View notifications</span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>

                    <div class="z-20 z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-white divide-y divide-gray-100 rounded shadow-lg" id="apps-dropdown">
                        <div class="block px-4 py-2 text-base font-medium text-center text-gray-700 bg-gray-50 ">
                            Apps
                        </div>
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Sales</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Users</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Inbox</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Profile</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Settings</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Products</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Pricing</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Billing</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Logout</div>
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center ml-3">
                        <div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 " id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                            </button>
                        </div>

                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-2">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 text-white" role="none">
                                    Neil Sims
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate " role="none">
                                    neil.sims@flowbite.com
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Dashboard</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Settings</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Earnings</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Sign out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-t-8 border-[#FFD66E]">
    </nav>
    <!-- MAIN -->

    <div class="flex pt-16 overflow-hidden bg-gray-50 ">

        <aside id="sidebar" class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 hidden  w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width" aria-label="Sidebar">
            <div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-gray-800 border-r border-gray-200">
                <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-1 px-3 space-y-1 bg-gray-800 divide-y divide-gray-200 ">
                        <ul class="pb-2 space-y-2">
                            <li>
                                <button type="button" class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700" aria-controls="dropdown-layouts" data-collapse-toggle="dropdown-layouts">
                                    <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                    </svg>
                                    <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Home</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700" aria-controls="dropdown-crud" data-collapse-toggle="dropdown-crud">
                                    <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd" d="M.99 5.24A2.25 2.25 0 013.25 3h13.5A2.25 2.25 0 0119 5.25l.01 9.5A2.25 2.25 0 0116.76 17H3.26A2.267 2.267 0 011 14.74l-.01-9.5zm8.26 9.52v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75v.615c0 .414.336.75.75.75h5.373a.75.75 0 00.627-.74zm1.5 0a.75.75 0 00.627.74h5.373a.75.75 0 00.75-.75v-.615a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75v.625zm6.75-3.63v-.625a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75v.625c0 .414.336.75.75.75h5.25a.75.75 0 00.75-.75zm-8.25 0v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75v.625c0 .414.336.75.75.75H8.5a.75.75 0 00.75-.75zM17.5 7.5v-.625a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75V7.5c0 .414.336.75.75.75h5.25a.75.75 0 00.75-.75zm-8.25 0v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75V7.5c0 .414.336.75.75.75H8.5a.75.75 0 00.75-.75z"></path>
                                    </svg>
                                    <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Recent Tests</span>
                                    <svg sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <ul id="dropdown-crud" class="space-y-2 py-2 hidden ">
                                    <li>
                                        <a href="https://flowbite-admin-dashboard.vercel.app/crud/products/" class="text-base text-gray-900 rounded-lg flex items-center p-2 group hover:bg-gray-100 transition duration-75 pl-11 ">Products</a>
                                    </li>
                                    <li>
                                        <a href="https://flowbite-admin-dashboard.vercel.app/crud/users/" class="text-base text-gray-900 rounded-lg flex items-center p-2 group hover:bg-gray-100 transition duration-75 pl-11 ">Users</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>

        <div class="fixed inset-0 z-10 hidden bg-gray-900/50 /90" id="sidebarBackdrop"></div>

        <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 ">
            <main>
                <div class="px-4 pt-6">
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm  sm:p-6 ">
                        <!-- Card header -->
                        <div class="items-center justify-between lg:flex">
                            <div class="mb-4 lg:mb-0">
                                <h3 class="mb-2 text-xl font-bold text-gray-900 text-gray-800">Hi, Proctor</h3>
                                <span class="text-base font-normal text-gray-500 ">You can see all your tests below</span>
                            </div>
                            <div class="items-center sm:flex">
                                <div class="flex items-center space-x-4">
                                    <div class="relative">
                                        <form action="#" method="GET" class=" lg:pl-3">
                                            <label for="topbar-search" class="sr-only">Search</label>
                                            <div class="relative mt-1 lg:w-96">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-500 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <input type="text" name="text" id="topbar-search" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5  text-white " placeholder="Search">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="flex items-center pl-2">
                                    <button id="dropdownDefault" data-dropdown-toggle="dropdown" class="mb-4 sm:mb-0 mr-4 inline-flex items-center text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                                        Filter by status
                                        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div id="dropdown" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                        <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                            Category
                                        </h6>
                                        <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                                            <li class="flex items-center">
                                                <input id="apple" type="checkbox" value="" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="apple" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    Completed (56)
                                                </label>
                                            </li>

                                            <li class="flex items-center">
                                                <input id="fitbit" type="checkbox" value="" checked class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="fitbit" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    Cancelled (56)
                                                </label>
                                            </li>

                                            <li class="flex items-center">
                                                <input id="dell" type="checkbox" value="" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="dell" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    In progress (56)
                                                </label>
                                            </li>

                                            <li class="flex items-center">
                                                <input id="asus" type="checkbox" value="" checked class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="asus" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    In review (97)
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <button id="dropdownDefault" data-dropdown-toggle="dropdown" class="mb-4 sm:mb-0 mr-4 inline-flex items-center text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                                        Sort by
                                        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div id="dropdown" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                        <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                            Category
                                        </h6>
                                        <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                                            <li class="flex items-center">
                                                <input id="apple" type="checkbox" value="" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="apple" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    Completed (56)
                                                </label>
                                            </li>

                                            <li class="flex items-center">
                                                <input id="fitbit" type="checkbox" value="" checked class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="fitbit" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    Cancelled (56)
                                                </label>
                                            </li>

                                            <li class="flex items-center">
                                                <input id="dell" type="checkbox" value="" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="dell" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    In progress (56)
                                                </label>
                                            </li>

                                            <li class="flex items-center">
                                                <input id="asus" type="checkbox" value="" checked class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                                <label for="asus" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    In review (97)
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Table -->
                        <div class="flex flex-col mt-6">
                            <div class="overflow-x-auto rounded-lg">
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden shadow sm:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500 flex items-center">
                                                        NAME
                                                        <span class="ml-2">
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6 9.65685L7.41421 11.0711L11.6569 6.82843L15.8995 11.0711L17.3137 9.65685L11.6569 4L6 9.65685Z" fill="#6b7280" />
                                                                <path d="M6 14.4433L7.41421 13.0291L11.6569 17.2717L15.8995 13.0291L17.3137 14.4433L11.6569 20.1001L6 14.4433Z" fill="#6b7280" />
                                                            </svg>
                                                        </span>
                                                    </th>


                                                    <th scope="col" class="p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500">
                                                        TYPE
                                                    </th>
                                                    <th scope="col" class="p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500">
                                                        DATE CREATED
                                                    </th>
                                                    <th scope="col" class="p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500">
                                                        COURSE
                                                    </th>
                                                    <th scope="col" class=" p-4 text-xs font-medium tracking-wider text-left  uppercase text-gray-500 flex items-center">
                                                        Sort by
                                                        <span class="ml-2">
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24" id="align-left-2" data-name="Flat Line" xmlns="http://www.w3.org/2000/svg" class="icon flat-line">
                                                                <path id="primary" d="M21,12H3M21,6H3M21,18H11" style="fill: none; stroke: #6b7280; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path>
                                                            </svg>
                                                        </span>
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody class="bg-white ">
                                                <?php
                                                foreach ($quiz_records as $quiz_record) {
                                                    $timestamp = $quiz_record->timecreated;
                                                    $formatted_date = date("d M Y", $timestamp);

                                                    // Select monitor_microphone state
                                                    $course_id = $quiz_record->course;
                                                    $sql = "SELECT shortname
                                                        FROM {course}
                                                        WHERE id = :course_id;
                                                        "
                                                    ;

                                                    $params = array('course_id' => $course_id);
                                                    $course_name = $DB->get_field_sql($sql, $params);

                                                    $quiz_id = $quiz_record->id;
                                                    echo
                                                        '<tr>
                                                            <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                                <h1>'. $quiz_record->name .'</h1>
                                                                <span class="font-normal text-[10px] text-center">
                                                                    <a href="" class="">SHARE</a>
                                                                    <a href="" class="pl-10">PREVIEW</a>
                                                                </span>
                                                            </td>
                                                            <td class="p-4 text-sm font-normal text-gray-800 whitespace-nowrap ">

                                                            </td>
                                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                                '. $formatted_date .'
                                                            </td>
                                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                                '. $course_name .'
                                                            </td>
                                                            <td class=" whitespace-nowrap">
                                                                <span class="bg-white text-gray-500 text-xs font-medium mr-2 px-3 py-1 rounded-md border">
                                                                    <a href="quizSetting.php?quiz_id='. $quiz_id .'&course_name='. $course_name .'">SETTINGS</a>
                                                                </span>
                                                                <span class="bg-[#0061A8] text-gray-100 text-xs font-medium mr-2 px-3 py-1 rounded-md   ">
                                                                    <a href="">RESULTS</a>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    ';
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
                                                <!-- 3 -->
                                                <!-- <tr>
                                                    <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                        <h1>TEST NAME #3</h1>
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
                                                        course 3
                                                    </td>
                                                    <td class=" whitespace-nowrap">
                                                        <span class="bg-white text-gray-500 text-xs font-medium mr-2 px-3 py-1 rounded-md border">
                                                            <a href="quizSetting.php">SETTINGS</a>
                                                        </span>
                                                        <span class="bg-[#0061A8] text-gray-100 text-xs font-medium mr-2 px-3 py-1 rounded-md   ">
                                                            <a href="quizSetting.php">RESULTS</a>
                                                        </span>
                                                    </td>
                                                </tr> -->
                                                <!-- 4 -->
                                                <!-- <tr class="bg-gray-100">
                                                    <td class="p-4 text-sm font-semibold  whitespace-nowrap text-gray-800">
                                                        <h1>TEST NAME #4A</h1>
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
                                                        course 4
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
                </div>

            </main>
            <p class="my-10 text-sm text-center text-gray-500">
                &copy; 2023-2024 <a href="https://flowbite.com/" class="hover:underline" target="_blank">e-RTU</a>. All rights reserved.
            </p>

        </div>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://flowbite-admin-dashboard.vercel.app//app.bundle.js"></script>
</body>

</html>