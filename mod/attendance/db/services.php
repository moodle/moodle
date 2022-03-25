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
 * Web service local plugin attendance external functions and service definitions.
 *
 * @package    mod_attendance
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'mod_attendance_add_attendance' => array(
        'classname'    => 'mod_attendance_external',
        'methodname'   => 'add_attendance',
        'classpath'    => 'mod/attendance/externallib.php',
        'description'  => 'Add attendance instance to course.',
        'type'         => 'write',
    ),
    'mod_attendance_remove_attendance' => array(
        'classname'    => 'mod_attendance_external',
        'methodname'   => 'remove_attendance',
        'classpath'    => 'mod/attendance/externallib.php',
        'description'  => 'Delete attendance instance.',
        'type'         => 'write',
    ),
    'mod_attendance_add_session' => array(
        'classname'    => 'mod_attendance_external',
        'methodname'   => 'add_session',
        'classpath'    => 'mod/attendance/externallib.php',
        'description'  => 'Add a new session.',
        'type'         => 'write',
    ),
    'mod_attendance_remove_session' => array(
        'classname'    => 'mod_attendance_external',
        'methodname'   => 'remove_session',
        'classpath'    => 'mod/attendance/externallib.php',
        'description'  => 'Delete a session.',
        'type'         => 'write',
    ),
    'mod_attendance_get_courses_with_today_sessions' => array(
        'classname'   => 'mod_attendance_external',
        'methodname'  => 'get_courses_with_today_sessions',
        'classpath'   => 'mod/attendance/externallib.php',
        'description' => 'Method that retrieves courses with today sessions of a teacher.',
        'type'        => 'read',
    ),
    'mod_attendance_get_session' => array(
        'classname'   => 'mod_attendance_external',
        'methodname'  => 'get_session',
        'classpath'   => 'mod/attendance/externallib.php',
        'description' => 'Method that retrieves the session data',
        'type'        => 'read',
    ),

    'mod_attendance_update_user_status' => array(
        'classname'   => 'mod_attendance_external',
        'methodname'  => 'update_user_status',
        'classpath'   => 'mod/attendance/externallib.php',
        'description' => 'Method that updates the user status in a session.',
        'type'        => 'write',
    )
);


// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Attendance' => array(
        'functions' => array(
            'mod_attendance_add_attendance',
            'mod_attendance_remove_attendance',
            'mod_attendance_add_session',
            'mod_attendance_remove_session',
            'mod_attendance_get_courses_with_today_sessions',
            'mod_attendance_get_session',
            'mod_attendance_update_user_status'
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'mod_attendance'
    )
);
