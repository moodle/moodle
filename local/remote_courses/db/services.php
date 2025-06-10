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
 * Custom web services for this plugin.
 *
 * @package    local_remote_courses
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'local_remote_courses_get_courses_by_username' => array(
        'classname'    => 'local_remote_courses_external',
        'methodname'   => 'get_courses_by_username',
        'classpath'    => 'local/remote_courses/externallib.php',
        'description'  => 'Get user\'s courses by username.',
        'type'         => 'read',
        'capabilities' => 'moodle/course:view, moodle/course:viewparticipants',
    ),
);
