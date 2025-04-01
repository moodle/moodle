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
 * Describes hooks used for testing.
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_backup\hook\before_copy_course_execute;
use core_backup\hook\fixtures\copy_course_hook_callbacks;

require_once(__DIR__ . '/copy_course_hook_callbacks.php');

$callbacks = [
    [
        'hook' => before_copy_course_execute::class,
        'callback' => [
            copy_course_hook_callbacks::class,
            'before_copy_course_execute',
        ],
    ],
];
