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

defined('MOODLE_INTERNAL') || die();

// Include necessary files
require_once(__DIR__ . '/lib.php');


function xmldb_local_esupervision_install() {
    // Call the installation function from install.php
    xmldb_local_esupervision_install();
}

function create_project($projectName, $projectDescription, $assignedSupervisor, $projectStatus) {
    global $DB;

    $table = 'esupervision_projects';

    // Create a new project record
    $newProject = new stdClass();
    $newProject->name = $projectName;
    $newProject->description = $projectDescription;
    $newProject->supervisor = $assignedSupervisor;
    $newProject->status = $projectStatus;

    // Insert the new project record into the database
    return $DB->insert_record($table, $newProject);
}

function get_student_projects($userId) {
    global $DB;

    $table = 'esupervision_projects';

    // Retrieve projects based on the student's ID
    $sql = "SELECT * FROM { $table } WHERE student_id = :userId";
    $params = ['userId' => $userId];

    return $DB->get_records_sql($sql, $params);
}


// Function to get projects assigned to the supervisor
function get_supervisor_projects($supervisorId) {
    global $DB;

    $table = 'esupervision_projects';

    // Retrieve projects based on the supervisor's ID
    $sql = "SELECT p.id AS project_id, p.name AS project_name, p.description, p.status, u.id AS student_id, u.firstname AS student_firstname, u.lastname AS student_lastname
            FROM { $table } p
            INNER JOIN {user} u ON p.student_id = u.id
            WHERE p.supervisor_id = :supervisorId";
    
    $params = ['supervisorId' => $supervisorId];

    return $DB->get_records_sql($sql, $params);
}

function get_project_list() {
    global $DB;
    $table = 'esupervision_projects';

    // Retrieve the list of projects
    $sql = "SELECT id, name, description, supervisor, status FROM { $table }";

    return $DB->get_records_sql($sql);
}

function get_project($projectId) {
    global $DB;
    $table = 'esupervision_projects';

    // Retrieve the project based on the project ID
    $sql = "SELECT * FROM { $table } WHERE id = :projectId";
    $params = ['projectId' => $projectId];

    return $DB->get_record_sql($sql, $params);
}

function update_project($projectId, $projectName, $projectDescription, $assignedSupervisor, $projectStatus) {
    global $DB;
    $table = 'esupervision_projects';

    // Retrieve the project based on the project ID
    $project = $DB->get_record($table, ['id' => $projectId]);

    // Update the project record
    $project->name = $projectName;
    $project->description = $projectDescription;
    $project->supervisor = $assignedSupervisor;
    $project->status = $projectStatus;

    // Update the project record in the database
    return $DB->update_record($table, $project);
}

function delete_project($projectId) {
    global $DB;
    $table = 'esupervision_projects';

    // Delete the project record from the database
    return $DB->delete_records($table, ['id' => $projectId]);
}

function get_supervisor_list() {
    global $DB;
    $table = 'esupervision_supervisors';

    // Retrieve the list of supervisors
    $sql = "SELECT id, firstname, lastname FROM { $table }";

    return $DB->get_records_sql($sql);
}

function get_supervisor($supervisorId) {
    global $DB;
    $table = 'esupervision_supervisors';

    // Retrieve the supervisor based on the supervisor ID
    $sql = "SELECT * FROM { $table } WHERE id = :supervisorId";
    $params = ['supervisorId' => $supervisorId];

    return $DB->get_record_sql($sql, $params);
}

function local_esupervision_extend_navigation(global_navigation $nav) {
    if (has_capability('local/esupervision:supervisor', context_system::instance())) {
        $url = new moodle_url('/local/esupervision/supervisor_dashboard.php');
        $supervisorNode = navigation_node::create(
            'Supervisor',
            $url,
            navigation_node::TYPE_CUSTOM,
            null,
            null,
            new pix_icon('i/users', '')
        );
        $nav->add_node($supervisorNode);
    }  else if (has_capability('local/esupervision:student', context_system::instance())) {
        $url = new moodle_url('/local/esupervision/student_dashboard.php');
        $studentNode = navigation_node::create(
            'Student',
            $url,
            navigation_node::TYPE_CUSTOM,
            null,
            null,
            new pix_icon('i/users', '')
        );
        $nav->add_node($studentNode);
    }

    global $PAGE;
    $PAGE->requires->navigation_extend_for_plugin('local_esupervision', 'local_esupervision_extend_navigation');
}