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
 * Plugin external functions and services are defined here.
 *
 * @package     format_remuiformat
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$functions = [
    'format_remuiformat_move_activities' => [
        'classname' => 'format_remuiformat\external\api',
        'methodname' => 'move_activities',
        'classpath' => '',
        'description' => 'Adds/Update the sequence of activity in Section.',
        'type' => 'write',
        'ajax' => true,
    ],
    'format_remuiformat_show_activity_in_row' => [
        'classname' => 'format_remuiformat\external\api',
        'methodname' => 'show_activity_in_row',
        'classpath' => '',
        'description' => 'Show activity in row.',
        'type' => 'write',
        'ajax' => true,
    ],
    'format_remuiformat_move_activity_to_section' => [
        'classname' => 'format_remuiformat\external\api',
        'methodname' => 'move_activity_to_section',
        'classpath' => '',
        'description' => 'Show activity in row.',
        'type' => 'write',
        'ajax' => true,
    ],
    'format_remuiformat_course_progress_data' => [
        'classname' => 'format_remuiformat\external\api',
        'methodname' => 'course_progress_data',
        'classpath' => '',
        'description' => 'It will return course progress and module details',
        'type' => 'read',
        'ajax' => true,
    ]
];
