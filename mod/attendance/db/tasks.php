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
 * Attendance module tasks.
 *
 * @package    mod_attendance
 * @copyright  2017 Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'mod_attendance\task\auto_mark',
        'blocking' => 0,
        'minute' => '8',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'),
    array(
        'classname' => 'mod_attendance\task\notify',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '1',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'),
        array(
        'classname' => 'mod_attendance\task\clear_temporary_passwords',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '1',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*')
);