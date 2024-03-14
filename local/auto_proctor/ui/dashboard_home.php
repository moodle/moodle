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


echo "<script>console.log('courses enrolled: ', " . json_encode(count($managing_context)) . ");</script>";

// If a user does not have a course management role, there is no reason for them to access the Auto Proctor Dashboard.
// The user will be redirected to the normal dashboard.
if (!$managing_context && !is_siteadmin($user_id)) {
    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot . '/my/';  // Use a default redirect path if HTTP_REFERER is not set
    header("Location: $previous_page");
    exit();
}

// Now, we will retrieve all the context IDs for the instances or context that the user manages.
// Array for the course IDs we will retrieve.
    // $course_ids = array();

    // // Loop through the context that the user manages
    // foreach ($managing_context as $context) {

    //     // Get the context id of the context
    //     $context_id = $context->contextid;
    //     echo "<script>console.log('Managing Course ID: ', " . json_encode($context_id) . ");</script>";

    //     // Get instance id of the context from contex table
    //     $sql = "SELECT instanceid
    //         FROM {context}
    //         WHERE id= :id
    //     "; 
    //     $instance_ids = $DB->get_fieldset_sql($sql, ['id' => $context_id]);

    //     echo "<script>console.log('instance id: ', " . json_encode($instance_ids) . ");</script>";

    //     // Push the instance_ids into the $course_ids array
    //     $course_ids = array_merge($course_ids, $instance_ids);
    // }

    // echo "<script>console.log('All Course IDs: ', " . json_encode($course_ids) . ");</script>";
    
// ========= IF USER IS TEACHER
    if(!is_siteadmin($user_id)){
       // Array for the course IDs we will retrieve.
       $course_ids = array();

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

            // Select only the course id in BSIT

                $name = 'Bachelor of Science in Information Technology (Boni Campus)';
                $sql = "
                    SELECT id
                    FROM {course_categories}
                    WHERE name = :name;
                ";
                $params = array('name' => $name);

                $bsit_id = $DB->get_fieldset_sql($sql, $params);

                $sql = "SELECT id
                    FROM {course}
                    WHERE id= :id
                    AND category= :bsit_id;
                ";

                $params = array('id' => $instance_id[0], 'bsit_id' => $bsit_id[0]);
                $course_id = $DB->get_fieldset_sql($sql, $params);


            // Push the instance_ids into the $course_ids array
            $course_ids = array_merge($course_ids, $course_id);
            echo "</br>";
        }
       //print_r($course_ids);
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

        echo "<script>console.log('All Course IDs COUNT: ', " . json_encode($num_of_courses) . ");</script>";
        
    }

// Get the wwwroot of the site
$wwwroot = $CFG->wwwroot;


// ====== GET ALL STUDENTS

$course_id_placeholders = implode(', ', array_fill(0, count($course_ids), '?'));

$sql = "
    SELECT u.id
    FROM {user} u
    JOIN {user_enrolments} ue ON u.id = ue.userid
    JOIN {enrol} e ON ue.enrolid = e.id
    JOIN {role_assignments} ra ON u.id = ra.userid
    WHERE e.courseid IN ($course_id_placeholders)
    AND ra.roleid = (SELECT id FROM {role} WHERE shortname = 'student')
    AND u.id <> ?
";

$params = array_merge($course_ids, [$user_id]); // Append $user_id to the end of $course_ids array

$all_students = $DB->get_records_sql($sql, $params);


    // // Initialize an array to store student IDs
    // $student_ids = array();

    // // Iterate over the results and push IDs into the array
    // foreach ($all_students as $student) {
    //     $student_ids[] = $student->id;
    // }

    //echo implode(', ', $student_ids);

    // ======= NUMBER OF ALL HANDLED STUDENTS
        $num_of_all_students = count($all_students);

    // ======= NUMBER OF ALL CREATED QUIZ
        $sql = "SELECT *
            FROM {quiz}
            WHERE course IN ($course_id_placeholders)
        ";

        // Execute the query
        $all_created_quizzes = $DB->get_records_sql($sql, $course_ids);

        $num_of_all_created_quiz = count($all_created_quizzes);

        $num_of_courses = count($course_ids);

?>

<main>
                <div class="p-4 bg-white block sm:flex items-center justify-between  lg:mt-1.5 ">
                    <div class="w-full mb-1">
                        <div class="mb-4">
                            <!-- <nav class="flex mb-5" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                                    <li class="inline-flex items-center">
                                        <a href="#"
                                            class="inline-flex items-center text-gray-700 hover:text-primary-600 ">
                                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                                </path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <a href="#"
                                                class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 ">Home</a>
                                        </div>
                                    </li>
                                </ol>
                            </nav> -->
                            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl ">Dashboard</h1>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-white">

                    <div class=" grid w-full grid-cols-3 gap-4 xl:grid-cols-3 2xl:grid-cols-3">
                        <div
                            class="items-center justify-between p-8 bg-[#FFD66E] border border-gray-200 rounded-lg shadow-sm sm:flex ">
                            <span class="flex justify-center items-center">
                                <svg width="40px" height="40px" class="text-center" viewBox="0 0 8.4666669 8.4666669"
                                    id="svg8" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:cc="http://creativecommons.org/ns#"
                                    xmlns:dc="http://purl.org/dc/elements/1.1/"
                                    xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                                    xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                                    xmlns:svg="http://www.w3.org/2000/svg">

                                    <defs id="defs2" />

                                    <g id="layer1" transform="translate(0,-288.53332)">

                                        <path
                                            d="M 10.462891 16.070312 C 4.9244824 18.277775 0.99608795 23.682844 0.99609375 30 A 1.0001 1.0001 0 0 0 2 31.003906 L 30 31.003906 A 1.0001 1.0001 0 0 0 30.996094 30 C 30.996099 23.68349 27.06876 18.278206 21.53125 16.070312 C 19.99994 17.27111 18.07881 17.996094 15.996094 17.996094 C 13.912967 17.996094 11.992832 17.271538 10.462891 16.070312 z "
                                            id="path935"
                                            style="color:#000000;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:medium;line-height:normal;font-family:sans-serif;font-variant-ligatures:normal;font-variant-position:normal;font-variant-caps:normal;font-variant-numeric:normal;font-variant-alternates:normal;font-feature-settings:normal;text-indent:0;text-align:start;text-decoration:none;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#000000;letter-spacing:normal;word-spacing:normal;text-transform:none;writing-mode:lr-tb;direction:ltr;text-orientation:mixed;dominant-baseline:auto;baseline-shift:baseline;text-anchor:start;white-space:normal;shape-padding:0;clip-rule:nonzero;display:inline;overflow:visible;visibility:visible;opacity:1;isolation:auto;mix-blend-mode:normal;color-interpolation:sRGB;color-interpolation-filters:linearRGB;solid-color:#000000;solid-opacity:1;vector-effect:none;fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:1.99999988;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;paint-order:stroke fill markers;color-rendering:auto;image-rendering:auto;shape-rendering:auto;text-rendering:auto;enable-background:accumulate"
                                            transform="matrix(0.26458333,0,0,0.26458333,0,288.53332)" />

                                        <path
                                            d="M 15.996094 1.0039062 C 11.589664 1.0039062 8.0019573 4.5916469 8.0019531 8.9980469 C 8.0019573 13.404485 11.589664 17 15.996094 17 C 20.402524 17 23.998043 13.404485 23.998047 8.9980469 C 23.998043 4.5916469 20.402524 1.0039062 15.996094 1.0039062 z "
                                            id="path940"
                                            style="color:#000000;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:medium;line-height:normal;font-family:sans-serif;font-variant-ligatures:normal;font-variant-position:normal;font-variant-caps:normal;font-variant-numeric:normal;font-variant-alternates:normal;font-feature-settings:normal;text-indent:0;text-align:start;text-decoration:none;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#000000;letter-spacing:normal;word-spacing:normal;text-transform:none;writing-mode:lr-tb;direction:ltr;text-orientation:mixed;dominant-baseline:auto;baseline-shift:baseline;text-anchor:start;white-space:normal;shape-padding:0;clip-rule:nonzero;display:inline;overflow:visible;visibility:visible;opacity:1;isolation:auto;mix-blend-mode:normal;color-interpolation:sRGB;color-interpolation-filters:linearRGB;solid-color:#000000;solid-opacity:1;vector-effect:none;fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:1.99999988;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;paint-order:stroke fill markers;color-rendering:auto;image-rendering:auto;shape-rendering:auto;text-rendering:auto;enable-background:accumulate"
                                            transform="matrix(0.26458333,0,0,0.26458333,0,288.53332)" />

                                    </g>

                                </svg>
                            </span>
                            <div class="w-full text-center ">
                                <h3 class="text-base font-normal text-gray-500 ">Number of students</h3>
                                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl "><?php echo $num_of_all_students; ?></span>
                            </div>
                        </div>
                        <div
                            class="items-center justify-between p-8 bg-[#FFD66E] border border-gray-200 rounded-lg shadow-sm sm:flex ">
                            <span class="flex justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50px" height="50px"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:serif="http://www.serif.com/"
                                    viewBox="0 0 100 125" version="1.1" xml:space="preserve" style="" x="0px" y="0px"
                                    fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round"
                                    stroke-miterlimit="2">
                                    <g>
                                        <path
                                            d="M43.953,76.471c0.031,0.172 0.047,0.349 0.047,0.529c0,1.656 -1.344,3 -3,3l-15,-0c-1.656,-0 -3,-1.344 -3,-3c0,-1.656 1.344,-3 3,-3l14.292,-0c-1.426,-1.593 -2.292,-3.695 -2.292,-6l0,-8.485c0,-2.387 0.948,-4.676 2.636,-6.364l26.636,-26.636c0.235,-0.236 0.478,-0.459 0.728,-0.671l0,-8.844c0,-4.967 -4.033,-9 -9,-9l-36,-0c-4.967,-0 -9,4.033 -9,9c0,-0 0,66 0,66c0,4.967 4.033,9 9,9c0,-0 36,-0 36,-0c4.967,-0 9,-4.033 9,-9l0,-14.787l-6.151,6.151c-1.688,1.688 -3.977,2.636 -6.364,2.636c0,-0 -8.485,-0 -8.485,-0c-1.069,-0 -2.095,-0.186 -3.047,-0.529Zm-17.953,-8.471l6,-0c1.656,-0 3,-1.344 3,-3c0,-1.656 -1.344,-3 -3,-3l-6,-0c-1.656,-0 -3,1.344 -3,3c0,1.656 1.344,3 3,3Zm0,-18l12,-0c1.656,-0 3,-1.344 3,-3c0,-1.656 -1.344,-3 -3,-3l-12,-0c-1.656,-0 -3,1.344 -3,3c0,1.656 1.344,3 3,3Zm0,-12l24,-0c1.656,-0 3,-1.344 3,-3c0,-1.656 -1.344,-3 -3,-3l-24,-0c-1.656,-0 -3,1.344 -3,3c0,1.656 1.344,3 3,3Zm0,-12l24,-0c1.656,-0 3,-1.344 3,-3c0,-1.656 -1.344,-3 -3,-3l-24,-0c-1.656,-0 -3,1.344 -3,3c0,1.656 1.344,3 3,3Z" />
                                        <path
                                            d="M62.515,39.757l-17.636,17.636c-0.563,0.563 -0.879,1.326 -0.879,2.122l0,8.485c0,1.657 1.343,3 3,3l8.485,-0c0.796,-0 1.559,-0.316 2.122,-0.879l17.636,-17.636l-12.728,-12.728Zm4.242,-4.242l12.728,12.728l4.758,-4.758c2.343,-2.343 2.343,-6.142 -0,-8.485l-4.243,-4.243c-2.343,-2.343 -6.142,-2.343 -8.485,0l-4.758,4.758Z" />
                                    </g>
                                </svg>
                            </span>
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Number of published Quizzes</h3>
                                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl "><?php echo $num_of_all_created_quiz; ?></span>
                            </div>
                        </div>
                        <div
                            class="items-center justify-between p-10  bg-[#FFD66E] border border-gray-200 rounded-lg shadow-sm sm:flex ">
                            <span class="flex justify-center items-center">
                                <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="35px" height="35px"
                                    +++++++++++++++++++ viewBox="0 0 512 512" xml:space="preserve">
                                    <g>
                                        <path class="st0" d="M141.18,56.938l30.484,33.531v157.594c0,2.563,1.422,4.938,3.688,6.141c2.281,1.203,5.031,1.063,7.156-0.391
		l36.406-24.656l36.391,24.656c2.141,1.453,4.891,1.594,7.172,0.391c2.25-1.203,3.688-3.578,3.688-6.141V90.469l-30.5-33.531H141.18
		z" />
                                        <path class="st0" d="M436.008,93.344l-25.875-62.563c9.188-0.563,14.719-8.156,14.719-14.078C424.852,7.469,417.383,0,408.164,0
		H109.477C92.086,0,76.195,7.094,64.836,18.5C53.43,29.859,46.32,45.75,46.336,63.125V470.75c0,22.781,18.469,41.25,41.25,41.25
		h343.359c19.188,0,34.719-15.547,34.719-34.734V127.578C465.664,110.125,452.789,95.844,436.008,93.344z M290.664,92.844v155.219
		c0,11.672-6.406,22.328-16.719,27.797c-4.531,2.391-9.625,3.672-14.75,3.672c-6.313,0-12.422-1.875-17.641-5.438l-22.641-15.344
		l-22.656,15.344c-5.219,3.563-11.313,5.438-17.641,5.438c-5.109,0-10.219-1.281-14.75-3.688
		c-10.297-5.453-16.703-16.109-16.703-27.781V99.938l-6.469-7.094h-31.219c-8.266,0-15.594-3.313-21.016-8.703
		c-5.406-5.453-8.719-12.766-8.719-21.016s3.313-15.578,8.719-21c5.422-5.406,12.75-8.719,21.016-8.719H383.57l26.688,59.438
		H290.664z" />
                                    </g>
                                </svg>
                            </span>
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Number of Courses</h3>
                                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl "><?php echo $num_of_courses; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 items-center mb-8 justify-between block sm:flex md:divide-x ">
                    <h1 class="px-4 text-xl justify-start font-semibold text-gray-900 sm:text-2xl py-0">Students</h1>
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
                                                        ID
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
                                                        Name
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
                                                        Email
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
                                                        Course
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

                                                foreach ($all_students as $student){

                                                    // ====== SELECT USER INFO
                                                        $sql = "SELECT *
                                                                FROM {user}
                                                                WHERE id = :userid
                                                        ";

                                                        // Parameters for the query
                                                        $params = array('userid' => $student->id);
                                                        $user_info = $DB->get_record_sql($sql, $params);

                                                        $user_full_name = $user_info->firstname . ' ' . $user_info->lastname;

                                                        $user_email = $user_info->email;

                                                        $user_idnumber = $user_info->idnumber;

                                                    // ====== SELECT COURSE USER ENROLLED IN
                                                        $sql = "SELECT e.courseid
                                                            FROM {user_enrolments} ue
                                                            JOIN {enrol} e ON ue.enrolid = e.id
                                                            WHERE ue.userid = ?
                                                            AND e.courseid IN ($course_id_placeholders)
                                                            ORDER BY e.courseid
                                                        ";

                                                        $params = array_merge(array('userid' => $student->id), $course_ids);

                                                        // Execute the query
                                                        $course_ids_result = $DB->get_records_sql($sql, $params);

                                                        
                                                    echo'
                                                        <tr>
                                                            <td class="p-4 text-sm font-normal text-gray-900 whitespace-nowrap ">
                                                                <span class="font-semibold">'. $user_idnumber .'</span>
                                                            </td>
                                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                                '. $user_full_name.'
                                                            </td>
                                                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                                '. $user_email .'
                                                            </td>
                                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                    ';

                                                        foreach ($course_ids_result as $row) {
                                                            $course_id = $row->courseid;

                                                            $sql = "SELECT fullname
                                                                    FROM {course}
                                                                    WHERE id = :course_id
                                                                ";

                                                            $params = array('course_id' => $course_id);
                                                            $enrolled_courses = $DB->get_records_sql($sql, $params);

                                                            $fullnames_string = '';

                                                            // Check if the course exists and print its fullname
                                                            if (!empty($enrolled_courses)) {
                                                                // Since get_records_sql() returns an array, we access the first record directly
                                                                $course = reset($enrolled_courses);
                                                                $fullnames_string .= $course->fullname . '</br>';
                                                            }
                                                        
                                                            echo $fullnames_string;
                                                        }
                                                    echo'   </td>
                                                        </tr>
                                                    ';
                                                }
                                            ?>
                                            <!-- <tr>
                                                <td class="p-4 text-sm font-normal text-gray-900 whitespace-nowrap ">
                                                    <span class="font-semibold">002</span>
                                                </td>
                                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                    Renzi Delposo
                                                </td>
                                                <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                    renzidelposo@gmail.com
                                                </td>
                                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                    Art App
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p-4 text-sm font-normal text-gray-900 whitespace-nowrap ">
                                                    <span class="font-semibold">002</span>
                                                </td>
                                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                    Renzi Delposo
                                                </td>
                                                <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                                    renzidelposo@gmail.com
                                                </td>
                                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                                    Art App
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
                                <a href="#"
                                    class="inline-flex border justify-center p-1 mr-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-4 h-4 transform -scale-x-1" viewBox="0 0 15 15" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12.8536 11.1464C13.0488 11.3417 13.0488 11.6583 12.8536 11.8536C12.6583 12.0488 12.3417 12.0488 12.1464 11.8536L8.14645 7.85355C7.95118 7.65829 7.95118 7.34171 8.14645 7.14645L12.1464 3.14645C12.3417 2.95118 12.6583 2.95118 12.8536 3.14645C13.0488 3.34171 13.0488 3.65829 12.8536 3.85355L9.20711 7.5L12.8536 11.1464ZM6.85355 11.1464C7.04882 11.3417 7.04882 11.6583 6.85355 11.8536C6.65829 12.0488 6.34171 12.0488 6.14645 11.8536L2.14645 7.85355C1.95118 7.65829 1.95118 7.34171 2.14645 7.14645L6.14645 3.14645C6.34171 2.95118 6.65829 2.95118 6.85355 3.14645C7.04882 3.34171 7.04882 3.65829 6.85355 3.85355L3.20711 7.5L6.85355 11.1464Z"
                                            fill="#6b7280" />
                                    </svg>

                                </a>
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
                                <!--  -->
                                <!-- next 2 -->
                                <a href="#"
                                    class="inline-flex justify-center border  p-1 mr-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-7 h-7" viewBox="0 0 15 15" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M2.14645 11.1464C1.95118 11.3417 1.95118 11.6583 2.14645 11.8536C2.34171 12.0488 2.65829 12.0488 2.85355 11.8536L6.85355 7.85355C7.04882 7.65829 7.04882 7.34171 6.85355 7.14645L2.85355 3.14645C2.65829 2.95118 2.34171 2.95118 2.14645 3.14645C1.95118 3.34171 1.95118 3.65829 2.14645 3.85355L5.79289 7.5L2.14645 11.1464ZM8.14645 11.1464C7.95118 11.3417 7.95118 11.6583 8.14645 11.8536C8.34171 12.0488 8.65829 12.0488 8.85355 11.8536L12.8536 7.85355C13.0488 7.65829 13.0488 7.34171 12.8536 7.14645L8.85355 3.14645C8.65829 2.95118 8.34171 2.95118 8.14645 3.14645C7.95118 3.34171 7.95118 3.65829 8.14645 3.85355L11.7929 7.5L8.14645 11.1464Z"
                                            fill="#6b7280" />
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
