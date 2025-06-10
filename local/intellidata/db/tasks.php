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
 * Tasks page.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'local_intellidata\task\export_files_task',
        'blocking' => 0,
        'minute' => 0,
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 0,
    ],
    [
        'classname' => 'local_intellidata\task\export_data_task',
        'blocking' => 0,
        'minute' => 15,
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 0,
    ],
    [
        'classname' => 'local_intellidata\task\cleaner_task',
        'blocking' => 0,
        'minute' => 0,
        'hour' => 0,
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'local_intellidata\task\migration_task',
        'blocking' => 0,
        'minute' => '*/10',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 1,
    ],
    [
        'classname' => 'local_intellidata\task\copy_intelliboard_tracking',
        'blocking' => 0,
        'minute' => '*/2',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 1,
    ],
    [
        'classname' => 'local_intellidata\task\daily_snapshot_task',
        'blocking' => 0,
        'minute' => 0,
        'hour' => 0,
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 1,
    ],
];
