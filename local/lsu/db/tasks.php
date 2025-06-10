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
 * Definition of LSU student reprocess scheduled tasks.
 *
 * @package   local_lsu
 * @category  task
 * @copyright 2015 Louisiana State University
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'local_lsu\task\reprocess_student_data',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '22',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    )
);
