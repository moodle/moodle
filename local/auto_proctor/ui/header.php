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

// Get user's name
$firstname = $USER->firstname;
$lastname = $USER->lastname;

// Check if any of the user's roles are administrator
// Get the user's roles
$roles = get_user_roles(context_system::instance(), $user_id);

// Check if any of the user's roles are administrator
$is_admin = false;

if (is_siteadmin($user_id)) { // 'admin' is the shortname for the administrator role
    $is_admin = true;

}

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

// Get the wwwroot of the site
$wwwroot = $CFG->wwwroot

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
<body>
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
                    <a href="<?php echo $CFG->wwwroot . '/my/';?>" class="flex ml-2 md:mr-24">
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-[#FFD66E]">e-RTU</span>
                    </a>
                </div>
                <div class="flex items-center">

                    <button id="toggleSidebarMobileSearch" type="button" class="p-2 text-white rounded-lg lg:hidden hover:text-gray-900 hover:bg-gray-100 ">
                    </button>

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
                                    <?php echo $firstname . ' ' . $lastname; ?>
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href = "<?php echo $wwwroot . '/user/profile.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Profile</a>
                                </li>
                                <li>
                                    <a href = "<?php echo $wwwroot . '/grade/report/overview/index.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Grades</a>
                                </li>
                                <li>
                                    <a href = "<?php echo $wwwroot . '/calendar/view.php?view=month'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Calendar</a>
                                </li>
                                <li>
                                    <a href = "<?php echo $wwwroot . '/user/files.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Private files</a>
                                </li>
                                <li>
                                    <a href = "<?php echo $wwwroot . '/user/preferences.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Reports</a>
                                </li>
                                <li>
                                    <a href = "<?php echo $wwwroot . '/course/switchrole.php?id=1&switchrole=-1&returnurl=%2Fmy%2Findex.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Preferenes</a>
                                </li>

                                <?php 
                                    if ($is_admin === true){
                                        echo '
                                        <li>
                                            <a href = "'. $wwwroot .'/course/switchrole.php?id=1&switchrole=-1&returnurl=%2Fmy%2Findex.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Switch role to...</a>
                                        </li>
                                        ';
                                    }
                                ?>
                                <li>
                                    <a href = "<?php echo $wwwroot . '/login/logout.php?sesskey=r8dXthDjHG'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  " role="menuitem">Log out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-t-8 border-[#FFD66E]">
    </nav>
</body>
</html>