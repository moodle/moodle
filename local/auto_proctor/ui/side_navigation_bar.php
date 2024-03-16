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

        echo "<script>console.log('All Course IDs: ', " . json_encode($course_ids) . ");</script>";
    }

// Get the wwwroot of the site
$wwwroot = $CFG->wwwroot;

$num_of_courses = count($course_ids);

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
<aside id="sidebar" class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 hidden  w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width" aria-label="Sidebar">
            <div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-gray-800 border-r border-gray-200">
                <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-1 px-3 space-y-1 bg-gray-800 divide-y divide-gray-200 ">
                        <ul class="pb-2 space-y-2">
                            <li>
                                <!-- When there is a current selected course then url must be cleared, if not then only toggleHome. -->
                                <a href="<?php echo $CFG->wwwroot . '/local/auto_proctor/ui/auto_proctor_dashboard.php';?>" onclick="toggleHome()" ?>
                                    <button type="button" class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700" aria-controls="dropdown-layouts" data-collapse-toggle="dropdown-layouts">
                                        <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                        </svg>
                                        <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Home</span>
                                    </button>
                                </a>
                            </li>
                            <li>
                                <button id="coursesDropdown" type="button" class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700" aria-controls="dropdown-crud" data-collapse-toggle="dropdown-crud">
                                    <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd" d="M.99 5.24A2.25 2.25 0 013.25 3h13.5A2.25 2.25 0 0119 5.25l.01 9.5A2.25 2.25 0 0116.76 17H3.26A2.267 2.267 0 011 14.74l-.01-9.5zm8.26 9.52v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75v.615c0 .414.336.75.75.75h5.373a.75.75 0 00.627-.74zm1.5 0a.75.75 0 00.627.74h5.373a.75.75 0 00.75-.75v-.615a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75v.625zm6.75-3.63v-.625a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75v.625c0 .414.336.75.75.75h5.25a.75.75 0 00.75-.75zm-8.25 0v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75v.625c0 .414.336.75.75.75H8.5a.75.75 0 00.75-.75zM17.5 7.5v-.625a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75V7.5c0 .414.336.75.75.75h5.25a.75.75 0 00.75-.75zm-8.25 0v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75V7.5c0 .414.336.75.75.75H8.5a.75.75 0 00.75-.75z"></path>
                                    </svg>
                                    <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Courses</span>
                                    <svg sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <ul id="dropdown-crud" class="space-y-2 py-2 <?php echo isset($_GET['course_id']) ? '' : 'hidden'; ?>">
                                    <?php
                                        // Get the course_category id for BSIT courses
                                        $name = 'Bachelor of Science in Information Technology (Boni Campus)';
                                        $params = array('name' => $name);
                                        $sql = "
                                            SELECT id
                                            FROM {course_categories}
                                            WHERE name = :name
                                        ";

                                        $course_category_id = $DB->get_fieldset_sql($sql, $params);

                                        for ($course = 0; $course < $num_of_courses; $course++) {

                                            $sql = "SELECT shortname
                                                FROM {course}
                                                WHERE id = :course_id
                                                AND category = :course_category_id;
                                            ";

                                            $params = array('course_id' => $course_ids[$course], 'course_category_id' => $course_category_id[0]);
                                            $course_name = $DB->get_field_sql($sql, $params);
                                            
                                            if ($course_name){

                                                // When course is selected then highlight the button
                                                if (isset($_GET['course_id']) && $_GET['course_id'] == $course_ids[$course]) {
                                                    echo '
                                                        <li>
                                                            <a id = "' . $course_ids[$course]. '_eproctor" href = "' . $CFG->wwwroot  . '/local/auto_proctor/ui/auto_proctor_dashboard.php?course_id=' . $course_ids[$course] . '&course_name=' . $course_name.'" class="text-base text-black rounded-lg flex items-center p-2 group bg-gray-100 hover:bg-gray-100 transition duration-75 pl-11 "" >' . $course_name . '</a>
                                                        </li>
                                                    ';
                                                }

                                                // If not then highlight when hover
                                                else{
                                                    echo '
                                                        <li>
                                                            <a id = "' . $course_ids[$course] . '_eproctor" href = "' . $CFG->wwwroot  . '/local/auto_proctor/ui/auto_proctor_dashboard.php?course_id=' . $course_ids[$course] . '&course_name=' . $course_name.'" class="text-base text-white rounded-lg flex items-center p-2 group hover:text-black hover:bg-gray-100 transition duration-75 pl-11 ">' . $course_name . '</a>
                                                        </li>
                                                    ';
                                                }
                                            }
                                        }
                                    ?>
                                </ul>
                            </li>
                        <li>
                            <a href="<?php echo $CFG->wwwroot  . '/local/auto_proctor/ui/auto_proctor_dashboard.php?archives=1' ?>">
                                <button type="button" class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700" aria-controls="dropdown-layouts" data-collapse-toggle="dropdown-layouts">
                                    <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M10 12v1h4v-1m4 7H6a1 1 0 0 1-1-1V9h14v9a1 1 0 0 1-1 1ZM4 5h16a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                                    </svg>

                                    <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Archives</span>
                                </button>
                            </a>
                        </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>
</body>
</html>
