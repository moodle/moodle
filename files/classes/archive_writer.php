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
 * Abstraction of general file archives.
 *
 * @package   core_files
 * @copyright 2020 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_files;

use core_files\local\archive_writer\file_writer_interface as file_writer_interface;
use core_files\local\archive_writer\stream_writer_interface as stream_writer_interface;

/**
 * Each file archive type must extend this class.
 *
 * @package   core_files
 * @copyright 2020 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class archive_writer {

    /**
     * The zip writer class.
     */
    public const ZIP_WRITER = 'zip_writer';

    /**
     * Returns the stream writer.
     *
     * @param string $filename
     * @param string $type
     * @return stream_writer_interface
     */
    public static function get_stream_writer(string $filename, string $type): stream_writer_interface {
        $classname = self::get_classname_for_type($type);

        if (!is_a($classname, stream_writer_interface::class, true)) {
            throw new \InvalidArgumentException("{$type} does not support streaming");
        }

        return $classname::stream_instance($filename);
    }

    /**
     * Returns the file writer.
     *
     * @param string $filepath
     * @param string $type
     * @return file_writer_interface
     */
    public static function get_file_writer(string $filepath, string $type): file_writer_interface {
        $classname = self::get_classname_for_type($type);

        if (!is_a($classname, file_writer_interface::class, true)) {
            throw new \InvalidArgumentException("{$type} does not support writing to files");
        }

        return $classname::file_instance($filepath);
    }

    /**
     * Sanitise the file path, removing any unsuitable characters.
     *
     * @param string $filepath
     * @return string
     */
    public function sanitise_filepath(string $filepath): string {
        return clean_param($filepath, PARAM_PATH);
    }

    /**
     * Returns the class name for the type that was provided in get_file_writer().
     *
     * @param string $type
     * @return string
     */
    protected static function get_classname_for_type(string $type): string {
        return "core_files\local\archive_writer\\" . $type;
    }

    /**
     * The archive_writer Constructor.
     */
    protected function __construct() {

    }

    /**
     * Adds a file from a file path.
     *
     * @param string $name The path of file in archive (including directory).
     * @param string $path The path to file on disk (note: paths should be encoded using
     *                     UNIX-style forward slashes -- e.g '/path/to/some/file').
     */
    abstract public function add_file_from_filepath(string $name, string $path): void;

    /**
     * Adds a file from a string.
     *
     * @param string $name The path of file in archive (including directory).
     * @param string $data The contents of file
     */
    abstract public function add_file_from_string(string $name, string $data): void;

    /**
     * Adds a file from a stream.
     *
     * @param string $name The path of file in archive (including directory).
     * @param resource $stream The contents of file as a stream resource
     */
    abstract public function add_file_from_stream(string $name, $stream): void;

    /**
     * Adds a stored_file to archive.
     *
     * @param string $name The path of file in archive (including directory).
     * @param \stored_file $file
     */
    abstract public function add_file_from_stored_file(string $name, \stored_file $file): void;

    /**
     * Finish writing the zip footer.
     */
    abstract public function finish(): void;
}
