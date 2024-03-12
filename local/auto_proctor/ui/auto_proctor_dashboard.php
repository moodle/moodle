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
 * @author      Angelica, Renzi
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
if (!$managing_context) {
    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
    header("Location: $previous_page");
    exit();
}

// Now, we will retrieve all the context IDs for the instances or context that the user manages.

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

// Array for all the quizzes of the course that the user manages.
$managing_quizzes = array();

// Ensuring that the user is managing a course.
if (!empty($course_ids)) {

// Creating a placeholder for each element in $course_ids for the query
$placeholders = implode(', ', array_fill(0, count($course_ids), '?'));

// Constructing the SQL query
$sql = "SELECT * FROM {quiz} WHERE course IN ($placeholders)";

// Retrieve all the quizzes where the course is equal to the course IDs obtained earlier or the course IDs of the courses managed by the user.
$managing_quizzes = $DB->get_records_sql($sql, $course_ids);
};

// Convert PHP array/object to JSON for JavaScript
echo "<script>console.log('Auto Proctor Records: ', " . json_encode($managing_quizzes) . ");</script>";

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

<body class="bg-white">

    <!-- INCLUDE HEADER  -->
        <?php 
            include "header.php";
        ?>

    <div class="flex pt-16 overflow-hidden bg-gray-50 ">
        <!-- INCLUDE SIDE NAVIGATION BAR  -->
            <?php 
                include "side_navigation_bar.php";
            ?>

        <div class="fixed inset-0 z-10 hidden bg-gray-900/50 /90" id="sidebarBackdrop"></div>

        <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 ">

            <!-- INCLUDE HOME DISPLAY  -->
            <section id="home" style="display: block;">
                <?php
                    // Ensuring dashboard_home will display when no course is selected
                    if (!isset($_GET['course_id'])) {
                        include "dashboard_home.php";

                    }

                ?>

                <script>
                    function toggleHome() {
                        var home = document.getElementById("home");
                        if (home.style.display === "none") {
                            home.style.display = "block";

                            // Hide subject and quiz display
                            courses.style.display = "none";
                            archives.style.display = "none";
                            quiz_settings.style.display = "none";
                            quiz_results.style.display = "none";
                        }
                        
                    }
                </script>
            </section>

            <!-- INCLUDE COURSE DISPLAY  -->
            <section id="courses" style="display: none;">
                <?php
                    // Javascript logic for display is in side_navigation_bar.php
                    if (isset($_GET['course_id']) && !isset($_GET['quiz_id'])){
                        include "courses.php";
                        echo '
                            <script>
                                var courses = document.getElementById("courses");
                                if (courses.style.display === "none") {
                                    courses.style.display = "block";

                                    // Hide subject and quiz display
                                    home.style.display = "none";
                                    archives.style.display = "none";
                                    quiz_settings.display = "none";
                                    quiz_results.style.display = "none";
                                }
                            </script>
                        ';
                    }
                ?>

                <!-- <script>
                    function toggleCourses() {
                        var courses = document.getElementById("courses");
                        if (courses.style.display === "none") {
                            courses.style.display = "block";

                            // Hide subject and quiz display
                            home.style.display = "none";
                            archives.style.display = "none";
                        }
                    }
                </script> -->
            </section>

            <!-- INCLUDE ARCHOVES DISPLAY  -->
            <section id="archives" style="display: none;">
                <?php
                    //include "dashboard_main.php";
                    echo "ARCHIVES";
                ?>

                <script>
                    function toggleArchives() {

                        var archives = document.getElementById("archives");
                        var coursesDropdown = document.getElementById("coursesDropdown");
                        var courseId = new URLSearchParams(window.location.search).get('course_id');
                        var settings = new URLSearchParams(window.location.search).get('quiz_settings');
                        var results = new URLSearchParams(window.location.search).get('quiz_results');

                        if (courseId) {
                            coursesDropdown.click();
                             // Remove the parameters in URL
                            // Get the current URL
                            var url = new URL(window.location.href);

                            // Remove parameters individually
                            url.searchParams.delete('course_id');
                            url.searchParams.delete('quiz_id');
                            url.searchParams.delete('quiz_name');

                            // Replace the current URL without redirecting
                            history.replaceState(null, '', url.href);
                        }

                        if (archives.style.display === "none") {
                            archives.style.display = "block";

                            // Hide subject and quiz display
                            home.style.display = "none";
                            courses.style.display = "none";

                            if (settings){
                                quiz_settings.style.display = "none";
                            }

                            if (results){
                                quiz_results.style.display = "none";
                            }
                            dropdown.style.display = "none";
                        }
                    }
                </script>
            </section>

            <!-- INCLUDE QUIZ SETTINGS DISPLAY  -->
            <!-- <section id="quiz_settings" style="display: none;"> -->
                <?php
                if(isset($_GET['quiz_name']) && !isset($_GET['quiz_results'])){
                        if (isset($_GET['course_id']) && isset($_GET['quiz_id']) ){
                            echo '<section id="quiz_settings" style="display: none;">';
                            include "quiz_settings.php";

                            echo '
                                <script>
                                        quiz_settings.style.display = "block";

                                        // Hide subject and quiz display
                                        home.style.display = "none";
                                        archives.style.display = "none";
                                        courses.style.display = "none";
                                        
                                </script>
                            ';
                            echo "</section>";
                        }
                    }
                ?>
            <!-- </section> -->

            <!-- INCLUDE QUIZ RESULTS DISPLAY  -->
            <?php
                    if (isset($_GET['quiz_results'])){
                        echo '<section id="quiz_results" style="display: none;">';
                        include "quiz_results.php";

                        echo '
                            <script>
                                    quiz_results.style.display = "block";

                                    // Hide subject and quiz display
                                    home.style.display = "none";
                                    archives.style.display = "none";
                                    courses.style.display = "none";
                            </script>
                        ';
                        echo "</section>";
                    }
                ?>

            <p class="my-10 text-sm text-center text-gray-500">
                &copy; 2023-2024 <a href="https://flowbite.com/" class="hover:underline" target="_blank">e-RTU</a>. All rights reserved.
            </p>

        </div>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://flowbite-admin-dashboard.vercel.app//app.bundle.js"></script>
</body>

</html>