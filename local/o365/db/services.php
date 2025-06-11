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
 * Web service definition.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_o365_create_onenoteassignment' => [
        'classname' => '\local_o365\webservices\create_onenoteassignment',
        'methodname' => 'assignment_create',
        'classpath' => 'local/o365/classes/webservices/create_onenoteassignment.php',
        'description' => 'Create an assignment',
        'type' => 'write',
    ],
    'local_o365_get_onenoteassignment' => [
        'classname' => '\local_o365\webservices\read_onenoteassignment',
        'methodname' => 'assignment_read',
        'classpath' => 'local/o365/classes/webservices/read_onenoteassignment.php',
        'description' => 'Get an assignment',
        'type' => 'read',
    ],
    'local_o365_update_onenoteassignment' => [
        'classname' => '\local_o365\webservices\update_onenoteassignment',
        'methodname' => 'assignment_update',
        'classpath' => 'local/o365/classes/webservices/update_onenoteassignment.php',
        'description' => 'Update an assignment',
        'type' => 'write',
    ],
    'local_o365_delete_onenoteassignment' => [
        'classname' => '\local_o365\webservices\delete_onenoteassignment',
        'methodname' => 'assignment_delete',
        'classpath' => 'local/o365/classes/webservices/delete_onenoteassignment.php',
        'description' => 'Delete an assignment',
        'type' => 'write',
    ],
    'local_o365_get_teachercourses' => [
        'classname' => '\local_o365\webservices\read_teachercourses',
        'methodname' => 'teachercourses_read',
        'classpath' => 'local/o365/classes/webservices/read_teachercourses.php',
        'description' => 'Get a list of courses that the current user is a teacher in.',
        'type' => 'read',
    ],
    'local_o365_get_course_users' => [
        'classname' => '\local_o365\webservices\read_courseusers',
        'methodname' => 'courseusers_read',
        'classpath' => 'local/o365/classes/webservices/read_courseusers.php',
        'description' => 'Get a list of students in a course.',
        'type' => 'read',
    ],
    'local_o365_get_assignments' => [
        'classname' => '\local_o365\webservices\read_assignments',
        'methodname' => 'assignments_read',
        'classpath' => 'local/o365/classes/webservices/read_assignments.php',
        'description' => 'Get a list of courses and assignments for the user',
        'type' => 'read',
    ],
    'local_o365_update_grade' => [
        'classname' => '\local_o365\webservices\update_grade',
        'methodname' => 'grade_update',
        'classpath' => 'local/o365/classes/webservices/update_grade.php',
        'description' => 'Update a grade.',
        'type' => 'write',
    ],
];

// Pre-built service.
$services = [
    'Moodle Microsoft 365 Webservices' => [
        'functions' => [
            'local_o365_create_onenoteassignment',
            'local_o365_get_onenoteassignment',
            'local_o365_update_onenoteassignment',
            'local_o365_delete_onenoteassignment',
            'local_o365_get_teachercourses',
            'local_o365_get_course_users',
            'local_o365_get_assignments',
            'local_o365_update_grade',
            'mod_assign_get_assignments',
            'mod_assign_get_grades',
            'mod_assign_save_grade',
        ],
        'restrictedusers' => 0,
        'enabled' => 0,
        'shortname' => 'o365_webservices',
    ],
];
