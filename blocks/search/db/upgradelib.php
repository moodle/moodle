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
 * Global search block upgrade related helper functions
 *
 * @package    blocks
 * @subpackage search
 * @copyright  2010 Aparup Banerjee
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/*
* Function to turn a mysql(datetime) or postgres(timestamp without timezone data) or any generic date string (YYYY-MM-DD HH:MM:SS)
* read in from a database's date/time field (ie:valid) into a unix timestamp
* @param str The string to be converted to timestamp
* @return timestamp or 0
*/

function convert_datetime_upgrade($str) {

    $timestamp = strtotime($str);
    //process different failure returns due to different php versions
    if ($timestamp === false || $timestamp < 1) {
        return 0;
    } else {
        return $timestamp;
    }
}

