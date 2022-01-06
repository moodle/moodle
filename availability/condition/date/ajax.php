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
 * Handles AJAX processing (convert date to timestamp using current calendar).
 *
 * @package availability_date
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require(__DIR__ . '/../../../config.php');

// Action verb.
$action = required_param('action', PARAM_ALPHA);

switch ($action) {
    case 'totime':
        // Converts from time fields to timestamp using current user's calendar and time zone.
        echo \availability_date\frontend::get_time_from_fields(
                required_param('year', PARAM_INT),
                required_param('month', PARAM_INT),
                required_param('day', PARAM_INT),
                required_param('hour', PARAM_INT),
                required_param('minute', PARAM_INT));
        exit;

    case 'fromtime' :
        // Converts from timestamp to time fields.
        echo json_encode(\availability_date\frontend::get_fields_from_time(
                required_param('time', PARAM_INT)));
        exit;
}

// Unexpected actions throw coding_exception (this error should not occur
// unless there is a code bug).
throw new coding_exception('Unexpected action parameter');