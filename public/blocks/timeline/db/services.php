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
 * Web service definitions for the timeline block.
 *
 * @package    block_timeline
 * @copyright  2026 Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_timeline_get_timeline_events' => [
        'classname'   => 'block_timeline\external\get_timeline_events',
        'description' => 'Fetch calendar action events for the timeline block dates view.',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
    ],
    'block_timeline_get_courses_with_events' => [
        'classname'   => 'block_timeline\external\get_courses_with_events',
        'description' => 'Fetch enrolled in-progress courses with their action events for the timeline block courses view.',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
    ],
];
