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
 * Class simulating empty directories.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents the root directory of an empty file area in the tree navigated by {@link file_browser}.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class virtual_root_file {
    /** @var int context id */
    protected $contextid;
    /** @var string file component */
    protected $component;
    /** @var string file area */
    protected $filearea;
    /** @var int file itemid */
    protected $itemid;

    /**
     * Constructor
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     */
    public function __construct($contextid, $component, $filearea, $itemid) {
        $this->contextid = $contextid;
        $this->component = $component;
        $this->filearea  = $filearea;
        $this->itemid    = $itemid;
    }

    /**
     * Whether or not this is a directory
     *
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Delete file
     *
     * @return success
     */
    public function delete() {
        return true;
    }

    /**
    * adds this file path to a curl request (POST only)
    *
    * @param curl $curlrequest the curl request object
    * @param string $key what key to use in the POST request
    */
    public function add_to_curl_request(&$curlrequest, $key) {
        return;
    }

    /**
     * Returns file handle - read only mode, no writing allowed into pool files!
     *
     * @return resource file handle
     */
    public function get_content_file_handle() {
        return null;
    }

    /**
     * Dumps file content to page
     *
     * @return resource file handle
     */
    public function readfile() {
        return;
    }

    /**
     * Returns file content as string
     *
     * @return string content
     */
    public function get_content() {
        return '';
    }

    /**
     * Copy content of file to given pathname
     *
     * @param string $pathname real path to new file
     * @return bool success
     */
    public function copy_content_to($pathname) {
        return false;
    }

    /**
     * List contents of archive
     *
     * @param file_packer $packer file packer instance
     * @return array of file infos
     */
    public function list_files(file_packer $packer) {
        return null;
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwrited
     *
     * @param file_packer $packer file packer instance
     * @param string $pathname target directory
     * @return mixed list of processed files; false if error
     */
    public function extract_to_pathname(file_packer $packer, $pathname) {
        return false;
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwrited
     *
     * @param file_packer $packer file packer instance
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $pathbase path base
     * @param int $userid user ID
     * @return mixed list of processed files; false if error
     */
    public function extract_to_storage(file_packer $packer, $contextid, $component, $filearea, $itemid, $pathbase, $userid = NULL) {
        return false;
    }

    /**
     * Add file/directory into archive
     *
     * @param file_archive $filearch file archive instance
     * @param string $archivepath pathname in archive
     * @return bool success
     */
    public function archive_file(file_archive $filearch, $archivepath) {
        return false;
    }

    /**
     * Returns parent directory
     *
     * @return stored_file
     */
    public function get_parent_directory() {
        return null;
    }

    /**
     * Returns context ID
     *
     * @return int context ID
     */
    public function get_contextid() {
        return $this->contextid;
    }

    /**
     * Returns file component
     *
     * @return string component
     */
    public function get_component() {
        return $this->component;
    }

    /**
     * Returns file area
     *
     * @return string filearea
     */
    public function get_filearea() {
        return $this->filearea;
    }

    /**
     * Returns file itemid
     *
     * @return int itemid
     */
    public function get_itemid() {
        return $this->itemid;
    }

    /**
     * Returns file path
     *
     * @return string filepath
     */
    public function get_filepath() {
        return '/';
    }

    /**
     * Returns file name
     *
     * @return string filename
     */
    public function get_filename() {
        return '.';
    }

    /**
     * Returns user ID
     *
     * @return int userid
     */
    public function get_userid() {
        return null;
    }

    /**
     * Returns file size
     *
     * @return int filesize
     */
    public function get_filesize() {
        return 0;
    }

    /**
     * Returns mimetype
     *
     * @return string mimetype
     */
    public function get_mimetype() {
        return null;
    }

    /**
     * Returns time created
     *
     * @return int
     */
    public function get_timecreated() {
        return 0;
    }

    /**
     * Returns time modified
     *
     * @return int
     */
    public function get_timemodified() {
        return 0;
    }

    /**
     * Returns status
     *
     * @return int
     */
    public function get_status() {
        return 0;
    }

    /**
     * Returns ID
     *
     * @return int
     */
    public function get_id() {
        return 0;
    }

    /**
     * Returns sha1 hash code
     *
     * @return string
     */
    public function get_contenthash() {
        return sha1('');
    }

    /**
     * Returns path name hash
     *
     * @return string
     */
    public function get_pathnamehash() {
        return sha1('/'.$this->get_contextid().'/'.$this->get_component().'/'.$this->get_filearea().'/'.$this->get_itemid().$this->get_filepath().$this->get_filename());
    }

    /**
     * Returns license
     *
     * @return string
     */
    public function get_license() {
        return null;
    }

    /**
     * Returns file's author
     *
     * @return string
     */
    public function get_author() {
        return null;
    }

    /**
     * Returns file source
     *
     * @return string
     */
    public function get_source() {
        return null;
    }

    /**
     * Returns file sort order
     *
     * @return int
     */
    public function get_sortorder() {
        return null;
    }
}
