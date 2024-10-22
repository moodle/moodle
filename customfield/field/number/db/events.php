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
 * Event observers for Number
 *
 * @package    customfield_number
 * @category   event
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => core_customfield\event\field_created::class,
        'callback' => 'customfield_number\observer::field_created',
    ],
    [
        'eventname' => core_customfield\event\field_updated::class,
        'callback' => 'customfield_number\observer::field_updated',
    ],
    [
        'eventname' => core\event\course_module_created::class,
        'callback' => 'customfield_number\observer::course_module_created',
    ],
    [
        'eventname' => core\event\course_module_deleted::class,
        'callback' => 'customfield_number\observer::course_module_deleted',
    ],
    [
        'eventname' => core\event\course_module_updated::class,
        'callback' => 'customfield_number\observer::course_module_updated',
    ],
];
