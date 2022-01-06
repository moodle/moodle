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
 * dataformatlib.php - Contains core dataformat related functions.
 *
 * @package    core
 * @subpackage dataformat
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Sends a formated data file to the browser
 *
 * @package    core
 * @subpackage dataformat
 *
 * @param string $filename The base filename without an extension
 * @param string $dataformat A dataformat name
 * @param array $columns An ordered map of column keys and labels
 * @param Iterator $iterator An iterator over the records, usually a RecordSet
 * @param callable $callback An option function applied to each record before writing
 * @throws coding_exception
 *
 * @deprecated since Moodle 3.9 - MDL-68500 please use \core\dataformat::download_data
 */
function download_as_dataformat($filename, $dataformat, $columns, $iterator, $callback = null) {
    debugging('download_as_dataformat() is deprecated, please use \core\dataformat::download_data() instead', DEBUG_DEVELOPER);

    \core\dataformat::download_data($filename, $dataformat, $columns, $iterator, $callback);
}
