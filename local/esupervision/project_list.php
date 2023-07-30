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
 * lib file.
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once(__DIR__ . '/lib.php');
// require_login();

// Set up the page context and layout
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/esupervision/project_list.php');
$PAGE->set_pagelayout('course');
$PAGE->set_title('Project List');
$PAGE->set_heading('Project List');

// echo '<link rel="stylesheet" type="text/css" href="styles.css">';
// Include the Moodle header
echo $OUTPUT->header();

// Retrieve the list of projects (replace with your own logic)
$projects = get_project_list();

// Display project list
echo '<h1>Project List</h1>';

if (!empty($projects)) {
    echo '<table class="project-list">';
    echo '<tr><th>Project Name</th><th>Description</th><th>Assigned Supervisor</th><th>Status</th></tr>';

    foreach ($projects as $project) {
        echo '<tr>';
        echo '<td>' . $project->name . '</td>';
        echo '<td>' . $project->description . '</td>';
        echo '<td>' . $project->supervisor . '</td>';
        echo '<td>' . $project->status . '</td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo '<p>No projects available.</p>';
}

// Include the Moodle footer
echo $OUTPUT->footer();
