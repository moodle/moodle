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
 * The personal block helper functions
 *
 * @package   block_personal
 * @copyright 2016 HsuanTang
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns assignment status
 *
 * @param int $assignmentid
 * @param int userid
 * @return string status
 */
function get_assignment_status($assignmentid) {
    global $DB, $USER;

    $record = $DB->get_record('assign_submission',
        array('assignment'=>$assignmentid, 'userid'=>$USER->id),
        'status'
    );

    $result = $record->status;

    return $result;
}