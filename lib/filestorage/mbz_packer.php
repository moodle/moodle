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
 * Implementation of .mbz packer.
 *
 * This packer supports .mbz files which can be either .zip or .tar.gz format
 * internally. A suitable format is chosen depending on system option when
 * creating new files.
 *
 * Internally this packer works by wrapping the existing .zip/.tar.gz packers.
 *
 * Backup filenames do not contain non-ASCII characters so packers that do not
 * support UTF-8 (like the current .tar.gz packer, and possibly external zip
 * software in some cases if used) can be used by this packer.
 *
 * @package core_files
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/file_packer.php");

/**
 * Utility class - handles all packing/unpacking of .mbz files.
 *
 * @package core_files
 * @category files
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mbz_packer extends file_packer {
    /**
     * Archive files and store the result in file storage.
     *
     * Any existing file at that location will be overwritten.
     *
     * @param array $files array from archive path => pathname or stored_file
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
     * @throws file_exception If file operations fail
     * @throws coding_exception If any archive paths do not meet the restrictions
     */
    public function archive_to_storage(array $files, $contextid,
            $component, $filearea, $itemid, $filepath, $filename,
            $userid = null, $ignoreinvalidfiles = true, file_progress $progress = null) {
        return $this->get_packer_for_archive_operation()->archive_to_storage($files,
                $contextid, $component, $filearea, $itemid, $filepath, $filename,
                $userid, $ignoreinvalidfiles, $progress);
    }

    /**
     * Archive files and store the result in an OS file.
     *
     * @param array $files array from archive path => pathname or stored_file
     * @param string $archivefile path to target zip file
     * @param bool $ignoreinvalidfiles true means ignore missing or invalid files, false means abort on any error
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return bool true if file created, false if not
     * @throws coding_exception If any archive paths do not meet the restrictions
     */
    public function archive_to_pathname(array $files, $archivefile,
            $ignoreinvalidfiles=true, file_progress $progress = null) {
        return $this->get_packer_for_archive_operation()->archive_to_pathname($files,
                $archivefile, $ignoreinvalidfiles, $progress);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param stored_file|string $archivefile full pathname of zip file or stored_file instance
     * @param string $pathname target directory
     * @param array $onlyfiles only extract files present in the array
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return array list of processed files (name=>true)
     * @throws moodle_exception If error
     */
    public function extract_to_pathname($archivefile, $pathname,
            array $onlyfiles = null, file_progress $progress = null) {
        return $this->get_packer_for_read_operation($archivefile)->extract_to_pathname(
                $archivefile, $pathname, $onlyfiles, $progress);
    }

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
     * @return array list of processed files (name=>true)
     * @throws moodle_exception If error
     */
    public function extract_to_storage($archivefile, $contextid,
            $component, $filearea, $itemid, $pathbase, $userid = null,
            file_progress $progress = null) {
        return $this->get_packer_for_read_operation($archivefile)->extract_to_storage(
                $archivefile, $contextid, $component, $filearea, $itemid, $pathbase,
                $userid, $progress);
    }

    /**
     * Returns array of info about all files in archive.
     *
     * @param string|stored_file $archivefile
     * @return array of file infos
     */
    public function list_files($archivefile) {
        return $this->get_packer_for_read_operation($archivefile)->list_files($archivefile);
    }

    /**
     * Selects appropriate packer for new archive depending on system option
     * and whether required extension is available.
     *
     * @return file_packer Suitable packer
     */
    protected function get_packer_for_archive_operation() {
        global $CFG;
        require_once($CFG->dirroot . '/lib/filestorage/tgz_packer.php');

        if ($CFG->enabletgzbackups) {
            return get_file_packer('application/x-gzip');
        } else {
            return get_file_packer('application/zip');
        }
    }

    /**
     * Selects appropriate packer for existing archive depending on file contents.
     *
     * @param string|stored_file $archivefile full pathname of zip file or stored_file instance
     * @return file_packer Suitable packer
     */
    protected function get_packer_for_read_operation($archivefile) {
        global $CFG;
        require_once($CFG->dirroot . '/lib/filestorage/tgz_packer.php');

        if (tgz_packer::is_tgz_file($archivefile)) {
            return get_file_packer('application/x-gzip');
        } else {
            return get_file_packer('application/zip');
        }
    }
}
