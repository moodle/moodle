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
     * The key of the $files array is always the path within the archive, e.g.
     * 'folder/subfolder/file.txt'. There are several options for the values of
     * the array:
     * - null = this entry represents a directory, so no file
     * - string = full path to file within operating system filesystem
     * - stored_file = file within Moodle filesystem
     * - array with one string element = use in-memory string for file content
     *
     * For the string (OS path) and stored_file (Moodle filesystem) cases, you
     * can specify a directory instead of a file to recursively include all files
     * within this directory.
     *
     * @param array $files Array of files to archive
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @param int $userid user ID
     * @param bool $ignoreinvalidfiles true means ignore missing or invalid files, false means abort on any error
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return stored_file|bool false if error stored_file instance if ok
     */
    abstract public function archive_to_storage(array $files, $contextid,
            $component, $filearea, $itemid, $filepath, $filename,
            $userid = NULL, $ignoreinvalidfiles=true, ?file_progress $progress = null);

    /**
     * Archive files and store the result in os file.
     *
     * The key of the $files array is always the path within the archive, e.g.
     * 'folder/subfolder/file.txt'. There are several options for the values of
     * the array:
     * - null = this entry represents a directory, so no file
     * - string = full path to file within operating system filesystem
     * - stored_file = file within Moodle filesystem
     * - array with one string element = use in-memory string for file content
     *
     * For the string (OS path) and stored_file (Moodle filesystem) cases, you
     * can specify a directory instead of a file to recursively include all files
     * within this directory.
     *
     * @param array $files array with zip paths as keys (archivepath=>ospathname or archivepath=>stored_file)
     * @param string $archivefile path to target zip file
     * @param bool $ignoreinvalidfiles true means ignore missing or invalid files, false means abort on any error
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return bool true if file created, false if not
     */
    abstract public function archive_to_pathname(array $files, $archivefile,
            $ignoreinvalidfiles=true, ?file_progress $progress = null);

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param stored_file|string $archivefile full pathname of zip file or stored_file instance
     * @param string $pathname target directory
     * @param array $onlyfiles only extract files present in the array
     * @param file_progress $progress Progress indicator callback or null if not required
     * @param bool $returnbool Whether to return a basic true/false indicating error state, or full per-file error
     * details.
     * @return array|bool list of processed files; false if error
     */
    abstract public function extract_to_pathname($archivefile, $pathname,
            ?array $onlyfiles = NULL, ?file_progress $progress = null, $returnbool = false);

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
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return array|bool list of processed files; false if error
     */
    abstract public function extract_to_storage($archivefile, $contextid,
            $component, $filearea, $itemid, $pathbase, $userid = NULL,
            ?file_progress $progress = null);

    /**
     * Returns array of info about all files in archive.
     *
     * @param string|file_archive $archivefile
     * @return array of file infos
     */
    abstract public function list_files($archivefile);
}
