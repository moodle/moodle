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
 * Class containing utility methods for dataformats
 *
 * @package     core
 * @copyright   2020 Moodle Pty Ltd <support@moodle.com>
 * @author      2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @license     Moodle Workplace License, distribution is restricted, contact support@moodle.com
 */

namespace core;

use coding_exception;
use core_php_time_limit;

/**
 * Dataformat utility class
 *
 * @package     core
 * @copyright   2020 Moodle Pty Ltd <support@moodle.com>
 * @author      2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @license     Moodle Workplace License, distribution is restricted, contact support@moodle.com
 */
class dataformat {

    /**
     * Return an instead of a dataformat writer from given dataformat type
     *
     * @param string $dataformat
     * @return dataformat\base
     * @throws coding_exception
     */
    protected static function get_format_instance(string $dataformat): \core\dataformat\base {
        $classname = 'dataformat_' . $dataformat . '\writer';
        if (!class_exists($classname)) {
            throw new coding_exception('Invalid dataformat', $dataformat);
        }

        return new $classname();
    }

    /**
     * Sends a formatted data file to the browser
     *
     * @param string $filename
     * @param string $dataformat
     * @param array $columns
     * @param Iterable $iterator
     * @param callable|null $callback
     * @throws coding_exception
     */
    public static function download_data(string $filename, string $dataformat, array $columns, Iterable $iterator,
            callable $callback = null): void {

        if (ob_get_length()) {
            throw new coding_exception('Output can not be buffered before calling download_data()');
        }

        $format = self::get_format_instance($dataformat);

        // The data format export could take a while to generate.
        core_php_time_limit::raise();

        // Close the session so that the users other tabs in the same session are not blocked.
        \core\session\manager::write_close();

        // If this file was requested from a form, then mark download as complete (before sending headers).
        \core_form\util::form_download_complete();

        $format->set_filename($filename);
        $format->send_http_headers();

        $format->start_output();
        $format->start_sheet($columns);

        $rownum = 0;
        foreach ($iterator as $row) {
            if (is_callable($callback)) {
                $row = $callback($row);
            }
            if ($row === null) {
                continue;
            }
            $format->write_record($row, $rownum++);
        }

        $format->close_sheet($columns);
        $format->close_output();
    }

    /**
     * Writes a formatted data file with specified filename
     *
     * @param string $filename
     * @param string $dataformat
     * @param array $columns
     * @param Iterable $iterator
     * @param callable|null $callback
     * @return string Complete path to the file on disk
     */
    public static function write_data(string $filename, string $dataformat, array $columns, Iterable $iterator,
            callable $callback = null): string {

        $format = self::get_format_instance($dataformat);

        // The data format export could take a while to generate.
        core_php_time_limit::raise();

        // Close the session so that the users other tabs in the same session are not blocked.
        \core\session\manager::write_close();

        $filepath = make_request_directory() . '/' . $filename . $format->get_extension();
        $format->set_filepath($filepath);

        $format->start_output_to_file();
        $format->start_sheet($columns);

        $rownum = 0;
        foreach ($iterator as $row) {
            if (is_callable($callback)) {
                $row = $callback($row);
            }
            if ($row === null) {
                continue;
            }
            $format->write_record($row, $rownum++);
        }

        $format->close_sheet($columns);
        $format->close_output_to_file();

        return $filepath;
    }
}
