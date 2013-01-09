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
 * Abstraction of general file packer.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract class for archiving of files.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class file_packer {

    /**
     * Archive files and store the result in file storage.
     *
     * @param array $files array with zip paths as keys (archivepath=>ospathname or archivepath=>stored_file)
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @param int $userid user ID
     * @param bool $ignoreinvalidfiles true means ignore missing or invalid files, false means abort on any error
     * @return stored_file|bool false if error stored_file instance if ok
     */
    public abstract function archive_to_storage(array $files, $contextid, $component, $filearea, $itemid, $filepath, $filename, $userid = NULL, $ignoreinvalidfiles=true);

    /**
     * Archive files and store the result in os file.
     *
     * @param array $files array with zip paths as keys (archivepath=>ospathname or archivepath=>stored_file)
     * @param string $archivefile path to target zip file
     * @param bool $ignoreinvalidfiles true means ignore missing or invalid files, false means abort on any error
     * @return bool true if file created, false if not
     */
    public abstract function archive_to_pathname(array $files, $archivefile, $ignoreinvalidfiles=true);

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param stored_file|string $archivefile full pathname of zip file or stored_file instance
     * @param string $pathname target directory
     * @param array $onlyfiles only extract files present in the array
     * @return array|bool list of processed files; false if error
     */
    public abstract function extract_to_pathname($archivefile, $pathname, array $onlyfiles = NULL);

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param string|stored_file $archivefile full pathname of zip file or stored_file instance
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $pathbase file path
     * @param int $userid user ID
     * @return array|bool list of processed files; false if error
     */
    public abstract function extract_to_storage($archivefile, $contextid, $component, $filearea, $itemid, $pathbase, $userid = NULL);

    /**
     * Returns array of info about all files in archive.
     *
     * @param string|file_archive $archivefile
     * @return array of file infos
     */
    public abstract function list_files($archivefile);
}
