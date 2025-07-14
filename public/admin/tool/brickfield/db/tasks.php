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
 * Tasks page
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$tasks = [
    [
        'classname' => 'tool_brickfield\task\bulk_process_courses',
        'blocking' => 0,
        'minute' => '*/5',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false
    ],
    [
        'classname' => 'tool_brickfield\task\bulk_process_caches',
        'blocking' => 0,
        'minute' => '*/5',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false
    ],
    [
        'classname' => 'tool_brickfield\task\checkid_validation',
        'blocking' => 0,
        'minute' => '05',
        'hour' => '9',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false
    ],
    [
        'classname' => 'tool_brickfield\task\update_summarydata',
        'blocking' => 0,
        'minute' => '50',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false
    ],
    [
        'classname' => 'tool_brickfield\task\process_analysis_requests',
        'blocking' => 0,
        'minute' => '*/5',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false
    ],
];
