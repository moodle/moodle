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
 * Scheduled tasks.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => '\theme_snap\task\refresh_deadline_caches_task',
        'blocking'  => 0,
        'minute'    => 'R',
        'hour'      => '1',
        'day'       => '*',
        'dayofweek' => '1-5',
        'month'     => '*',
    ], [
        'classname' => '\theme_snap\task\reset_deadlines_query_count_task',
        'blocking'  => 0,
        'minute'    => '0',
        'hour'      => '*',
        'day'       => '*',
        'dayofweek' => '*',
        'month'     => '*',
    ],
];
