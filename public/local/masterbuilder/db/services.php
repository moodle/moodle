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
 * Service definitions for local_masterbuilder.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_masterbuilder_create_question' => [
        'classname'   => 'local_masterbuilder\external',
        'methodname'  => 'create_question',
        'description' => 'Creates a True/False question and adds it to a quiz',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'local_masterbuilder_reset_course_progress' => [
        'classname'   => 'local_masterbuilder\external\state',
        'methodname'  => 'reset_course_progress',
        'description' => 'Resets grades, completion, and quiz attempts for a course',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'local_masterbuilder_get_build_state' => [
        'classname' => 'local_masterbuilder\external\state',
        'methodname' => 'get_build_state',
        'description' => 'Gets the build version for a course.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_masterbuilder_update_build_state' => [
        'classname' => 'local_masterbuilder\external\state',
        'methodname' => 'update_build_state',
        'description' => 'Updates the build version for a course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_masterbuilder_reset_build_state' => [
        'classname' => 'local_masterbuilder\external\state',
        'methodname' => 'reset_build_state',
        'description' => 'Resets the entire build state table.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_masterbuilder_configure_course_completion' => [
        'classname'   => 'local_masterbuilder\external',
        'methodname'  => 'configure_course_completion',
        'description' => 'Configures course completion with activity and grade requirements',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'local_masterbuilder_configure_quiz_settings' => [
        'classname'   => 'local_masterbuilder\external',
        'methodname'  => 'configure_quiz_settings',
        'description' => 'Configures quiz with 10-point max grade, passing grade, and auto-completion',
        'type'        => 'write',
        'ajax'        => true,
    ],
];

$services = [
    'MasterBuilder Service' => [
        'functions' => [
            'local_masterbuilder_create_question',
            'local_masterbuilder_reset_course_progress',
            'local_masterbuilder_get_build_state',
            'local_masterbuilder_update_build_state',
            'local_masterbuilder_reset_build_state',
            'local_masterbuilder_configure_course_completion',
            'local_masterbuilder_configure_quiz_settings',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'masterbuilder',
    ],
];

