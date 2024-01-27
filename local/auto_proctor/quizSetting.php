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

// Initialize var quiz_is and course_name
if (isset($_GET['quiz_id']) && isset($_GET['course_name'])){
  $quiz_id = $_GET['quiz_id'];
  $course_name = $_GET['course_name'];
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

      // Unset the save btn
      unset($_GET['save_btn']);
    }
}
//echo $OUTPUT->header(); // Output header
?>

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
                <a href = "<?php echo $CFG->wwwroot . '/local/auto_proctor/auto_proctor_dashboard.php'?>">
                  <button type="button" class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700" aria-controls="dropdown-layouts" data-collapse-toggle="dropdown-layouts">
                    <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Home</span>
                  </button>
                </a>
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
        <div class="p-4 bg-white block sm:flex items-center justify-between  lg:mt-1.5 ">
          <div class="w-full mb-1">
            <div class="mb-4">
              <nav class="flex mb-5" aria-label="Breadcrumb">
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
              </nav>
              <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl ">Test Settings</h1>
              <span class="text-base font-normal text-gray-500 ">Change the settings for <?php echo $course_name; ?></span>
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
                    <form method = "GET" action="quizSettings.php">
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
                      <input type="hidden" name="course_name" value="<?php echo $course_name; ?>" />
                      <!-- Save and Cancel buttons -->
                      <div class="flex justify-start">
                        <button class="bg-[#0061A8] text-white px-4 py-2 rounded-md mr-2" name = "save_btn">Save</button>
                        <button class="bg-gray-300 text-black px-4 py-2 rounded-md">Cancel</button>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

        </form>


      </main>

    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://flowbite-admin-dashboard.vercel.app//app.bundle.js"></script>
</body>

</html>