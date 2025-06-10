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
 * The only natsane function.
 * For every item that is weighted NATURAL extra credit in a non-excluded semester.
 * Sets the aggregationcoef to 1
 * Sets the aggregationcoef2 to 0
 * Sets the weightoverride to 1
 * Sets the needsupdate to 1 for all items in the course
 *
 * @package    local_natsane
 * @copyright  2017 Robert Russo, Louisiana State University
 */

defined('MOODLE_INTERNAL') || die();

// Define the task defaults.
$tasks = array(
    array(
        'classname' => 'local_natsane\task\unenroll_dupes',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'local_natsane\task\fix_courses',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'local_natsane\task\fix_kaltura',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '5',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);
