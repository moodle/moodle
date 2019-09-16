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
 * @param function $callback An option function applied to each record before writing
 * @param mixed $extra An optional value which is passed into the callback function
 */
function download_as_dataformat($filename, $dataformat, $columns, $iterator, $callback = null) {

    if (ob_get_length()) {
        throw new coding_exception("Output can not be buffered before calling download_as_dataformat");
    }

    $classname = 'dataformat_' . $dataformat . '\writer';
    if (!class_exists($classname)) {
        throw new coding_exception("Unable to locate dataformat/$dataformat/classes/writer.php");
    }
    $format = new $classname;

    // The data format export could take a while to generate...
    set_time_limit(0);

    // Close the session so that the users other tabs in the same session are not blocked.
    \core\session\manager::write_close();

    // If this file was requested from a form, then mark download as complete (before sending headers).
    \core_form\util::form_download_complete();

    $format->set_filename($filename);
    $format->send_http_headers();
    // This exists to support all dataformats - see MDL-56046.
    if (method_exists($format, 'write_header')) {
        error_log('The function write_header() does not support multiple sheets. In order to support multiple sheets you ' .
            'must implement start_output() and start_sheet() and remove write_header() in your dataformat.');
        $format->write_header($columns);
    } else {
        $format->start_output();
        $format->start_sheet($columns);
    }
    $c = 0;
    foreach ($iterator as $row) {
        if ($callback) {
            $row = $callback($row);
        }
        if ($row === null) {
            continue;
        }
        $format->write_record($row, $c++);
    }
    // This exists to support all dataformats - see MDL-56046.
    if (method_exists($format, 'write_footer')) {
        error_log('The function write_footer() does not support multiple sheets. In order to support multiple sheets you ' .
            'must implement close_sheet() and close_output() and remove write_footer() in your dataformat.');
        $format->write_footer($columns);
    } else {
        $format->close_sheet($columns);
        $format->close_output();
    }
}

