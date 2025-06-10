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
 * Setting up the scheduled task.
 *
 * @package    local_kalpanmaps
 * @copyright  2021 onwards LSUOnline & Continuing Education
 * @copyright  2021 onwards Robert Russo
 */

defined('MOODLE_INTERNAL') || die();

// Define the task defaults.
$tasks = array(
    array(
        'classname' => 'local_kalpanmaps\task\convert_kalvidres',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),

    array(
        'classname' => 'local_kalpanmaps\task\import_kalvidmap',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '0',
        'dayofweek' => '0',
        'month' => '0'
    ),

    array(
        'classname' => 'local_kalpanmaps\task\convert_kalembeds',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);
