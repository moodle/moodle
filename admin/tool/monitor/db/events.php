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
 * This file definies observers needed by the tool.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// List of observers.
$observers = array(
    array(
        'eventname'   => '\core\event\course_deleted',
        'priority'    => 1,
        'callback'    => '\tool_monitor\eventobservers::course_deleted',
    ),
    array(
        'eventname'   => '*',
        'callback'    => '\tool_monitor\eventobservers::process_event',
    ),
    array(
        'eventname'   => '\core\event\user_deleted',
        'callback'    => '\tool_monitor\eventobservers::user_deleted',
    ),
    array(
        'eventname'   => '\core\event\course_module_deleted',
        'callback'    => '\tool_monitor\eventobservers::course_module_deleted',
    )
);
