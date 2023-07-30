<?php
// This file is part of Moodle - http://moodle.org/
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
 * create project page
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once(__DIR__ . '/lib.php');
// require_login(); 

// Check if the current user is an admin
if (!is_siteadmin()) {
    // If not an admin, redirect to the home page or show an error message
    redirect(new moodle_url('/'));
}

// Set up the page context and layout
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Create Project List');
$PAGE->set_heading('Create Project List');

echo '<link rel="stylesheet" type="text/css" href="styles.css">';
// Include the Moodle header
echo $OUTPUT->header();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data (replace with your form field names)
    $projectName = $_POST['project_name'];
    $projectDescription = $_POST['project_description'];
    $projectSupervisor = $_POST['project_supervisor'];
    $projectStatus = $_POST['project_status'];

    // Perform data validation if required

    // Save the project to the database (replace with your own logic)
    // For example, insert the project details into the 'mdl_projects' table
    $newProjectId = create_project($projectName, $projectDescription, $projectSupervisor, $projectStatus);

    if ($newProjectId) {
        echo '<p>Project created successfully. Project ID: ' . $newProjectId . '</p>';
    } else {
        echo '<p>Failed to create the project.</p>';
    }
}

// Display the project list creation form inside a form-container class
echo '<div class="form-container">';
echo '<h1>Create Project List</h1>';
echo '<form method="POST">';
echo '<label for="project_name">Project Name:</label>';
echo '<input type="text" id="project_name" name="project_name" required><br>';
echo '<label for="project_description">Project Description:</label>';
echo '<textarea id="project_description" name="project_description" required></textarea><br>';
echo '<label for="project_supervisor">Assigned Supervisor:</label>';
echo '<input type="text" id="project_supervisor" name="project_supervisor" required><br>';
echo '<label for="project_status">Status:</label>';
echo '<input type="text" id="project_status" name="project_status" required><br>';
echo '<input type="submit" value="Create Project">';
echo '</form>';
echo '</div>';

// Include the Moodle footer
echo $OUTPUT->footer();
