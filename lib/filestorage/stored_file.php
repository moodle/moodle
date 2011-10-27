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
 * Definition of a class stored_file.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/stored_file.php");

/**
 * Class representing local files stored in a sha1 file pool.
 *
 * Since Moodle 2.0 file contents are stored in sha1 pool and
 * all other file information is stored in new "files" database table.
 *
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class stored_file {
    /** @var file_storage file storage pool instance */
    private $fs;
    /** @var object record from the files table */
    private $file_record;
    /** @var string location of content files */
    private $filedir;

    /**
     * Constructor, this constructor should be called ONLY from the file_storage class!
     *
     * @param file_storage $fs file  storage instance
     * @param object $file_record description of file
     * @param string $filepool location of file directory with sh1 named content files
     */
    public function __construct(file_storage $fs, stdClass $file_record, $filedir) {
        $this->fs          = $fs;
        $this->file_record = clone($file_record); // prevent modifications
        $this->filedir     = $filedir; // keep secret, do not expose!
    }

    /**
     * Is this a directory?
     *
     * Directories are only emulated, internally they are stored as empty
     * files with a "." instead of name - this means empty directory contains
     * exactly one empty file with name dot.
     *
     * @return bool true means directory, false means file
     */
    public function is_directory() {
        return ($this->file_record->filename === '.');
    }

    /**
     * Delete file from files table.
     *
     * The content of files stored in sha1 pool is reclaimed
     * later - the occupied disk space is reclaimed much later.
     *
     * @return bool always true or exception if error occurred
     */
    public function delete() {
        global $DB;
        $DB->delete_records('files', array('id'=>$this->file_record->id));
        // moves pool file to trash if content not needed any more
        $this->fs->deleted_file_cleanup($this->file_record->contenthash);
        return true; // BC only
    }

    /**
     * Protected - developers must not gain direct access to this function.
     *
     * NOTE: do not make this public, we must not modify or delete the pool files directly! ;-)
     *
     * @return string full path to pool file with file content
     **/
    protected function get_content_file_location() {
        $contenthash = $this->file_record->contenthash;
        $l1 = $contenthash[0].$contenthash[1];
        $l2 = $contenthash[2].$contenthash[3];
        return "$this->filedir/$l1/$l2/$contenthash";
    }

    /**
    * adds this file path to a curl request (POST only)
    *
    * @param curl $curlrequest the curl request object
    * @param string $key what key to use in the POST request
    * @return void
    */
    public function add_to_curl_request(&$curlrequest, $key) {
        $curlrequest->_tmp_file_post_params[$key] = '@' . $this->get_content_file_location();
    }

    /**
     * Returns file handle - read only mode, no writing allowed into pool files!
     *
     * When you want to modify a file, create a new file and delete the old one.
     *
     * @return resource file handle
     */
    public function get_content_file_handle() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        return fopen($path, 'rb'); //binary reading only!!
    }

    /**
     * Dumps file content to page.
     *
     * @return void
     */
    public function readfile() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        readfile($path);
    }

    /**
     * Returns file content as string.
     *
     * @return string content
     */
    public function get_content() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        return file_get_contents($this->get_content_file_location());
    }

    /**
     * Copy content of file to given pathname.
     *
     * @param string $pathname real path to the new file
     * @return bool success
     */
    public function copy_content_to($pathname) {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        return copy($path, $pathname);
    }

    /**
     * List contents of archive.
     *
     * @param file_packer $file_packer
     * @return array of file infos
     */
    public function list_files(file_packer $packer) {
        $archivefile = $this->get_content_file_location();
        return $packer->list_files($archivefile);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param file_packer $file_packer
     * @param string $pathname target directory
     * @return array|bool list of processed files; false if error
     */
    public function extract_to_pathname(file_packer $packer, $pathname) {
        $archivefile = $this->get_content_file_location();
        return $packer->extract_to_pathname($archivefile, $pathname);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param file_packer $file_packer
     * @param int $contextid
     * @param string $component
     * @param string $filearea
     * @param int $itemid
     * @param string $pathbase
     * @param int $userid
     * @return array|bool list of processed files; false if error
     */
    public function extract_to_storage(file_packer $packer, $contextid, $component, $filearea, $itemid, $pathbase, $userid = NULL) {
        $archivefile = $this->get_content_file_location();
        return $packer->extract_to_storage($archivefile, $contextid, $component, $filearea, $itemid, $pathbase);
    }

    /**
     * Add file/directory into archive.
     *
     * @param file_archive $filearch
     * @param string $archivepath pathname in archive
     * @return bool success
     */
    public function archive_file(file_archive $filearch, $archivepath) {
        if ($this->is_directory()) {
            return $filearch->add_directory($archivepath);
        } else {
            $path = $this->get_content_file_location();
            if (!is_readable($path)) {
                return false;
            }
            return $filearch->add_file_from_pathname($archivepath, $path);
        }
    }

    /**
     * Returns information about image,
     * information is determined from the file content
     * @return mixed array with width, height and mimetype; false if not an image
     */
    public function get_imageinfo() {
        if (!$imageinfo = getimagesize($this->get_content_file_location())) {
            return false;
        }
        $image = array('width'=>$imageinfo[0], 'height'=>$imageinfo[1], 'mimetype'=>image_type_to_mime_type($imageinfo[2]));
        if (empty($image['width']) or empty($image['height']) or empty($image['mimetype'])) {
            // gd can not parse it, sorry
            return false;
        }
        return $image;
    }

    /**
     * Verifies the file is a valid web image - gif, png and jpeg only.
     *
     * It should be ok to serve this image from server without any other security workarounds.
     *
     * @return bool true if file ok
     */
    public function is_valid_image() {
        $mimetype = $this->get_mimetype();
        if ($mimetype !== 'image/gif' and $mimetype !== 'image/jpeg' and $mimetype !== 'image/png') {
            return false;
        }
        if (!$info = $this->get_imageinfo()) {
            return false;
        }
        if ($info['mimetype'] !== $mimetype) {
            return false;
        }
        // ok, GD likes this image
        return true;
    }

    /**
     * Returns parent directory, creates missing parents if needed.
     *
     * @return stored_file
     */
    public function get_parent_directory() {
        if ($this->file_record->filepath === '/' and $this->file_record->filename === '.') {
            //root dir does not have parent
            return null;
        }

        if ($this->file_record->filename !== '.') {
            return $this->fs->create_directory($this->file_record->contextid, $this->file_record->component, $this->file_record->filearea, $this->file_record->itemid, $this->file_record->filepath);
        }

        $filepath = $this->file_record->filepath;
        $filepath = trim($filepath, '/');
        $dirs = explode('/', $filepath);
        array_pop($dirs);
        $filepath = implode('/', $dirs);
        $filepath = ($filepath === '') ? '/' : "/$filepath/";

        return $this->fs->create_directory($this->file_record->contextid, $this->file_record->component, $this->file_record->filearea, $this->file_record->itemid, $filepath);
    }

    /**
     * Returns context id of the file-
     *
     * @return int context id
     */
    public function get_contextid() {
        return $this->file_record->contextid;
    }

    /**
     * Returns component name - this is the owner of the areas,
     * nothing else is allowed to read or modify the files directly!!
     *
     * @return string
     */
    public function get_component() {
        return $this->file_record->component;
    }

    /**
     * Returns file area name, this divides files of one component into groups with different access control.
     * All files in one area have the same access control.
     *
     * @return string
     */
    public function get_filearea() {
        return $this->file_record->filearea;
    }

    /**
     * Returns returns item id of file.
     *
     * @return int
     */
    public function get_itemid() {
        return $this->file_record->itemid;
    }

    /**
     * Returns file path - starts and ends with /, \ are not allowed.
     *
     * @return string
     */
    public function get_filepath() {
        return $this->file_record->filepath;
    }

    /**
     * Returns file name or '.' in case of directories.
     *
     * @return string
     */
    public function get_filename() {
        return $this->file_record->filename;
    }

    /**
     * Returns id of user who created the file.
     *
     * @return int
     */
    public function get_userid() {
        return $this->file_record->userid;
    }

    /**
     * Returns the size of file in bytes.
     *
     * @return int bytes
     */
    public function get_filesize() {
        return $this->file_record->filesize;
    }

    /**
     * Returns mime type of file.
     *
     * @return string
     */
    public function get_mimetype() {
        return $this->file_record->mimetype;
    }

    /**
     * Returns unix timestamp of file creation date.
     *
     * @return int
     */
    public function get_timecreated() {
        return $this->file_record->timecreated;
    }

    /**
     * Returns unix timestamp of last file modification.
     *
     * @return int
     */
    public function get_timemodified() {
        return $this->file_record->timemodified;
    }

    /**
     * Returns file status flag.
     *
     * @return int 0 means file OK, anything else is a problem and file can not be used
     */
    public function get_status() {
        return $this->file_record->status;
    }

    /**
     * Returns file id.
     *
     * @return int
     */
    public function get_id() {
        return $this->file_record->id;
    }

    /**
     * Returns sha1 hash of file content.
     *
     * @return string
     */
    public function get_contenthash() {
        return $this->file_record->contenthash;
    }

    /**
     * Returns sha1 hash of all file path components sha1("contextid/component/filearea/itemid/dir/dir/filename.ext").
     *
     * @return string
     */
    public function get_pathnamehash() {
        return $this->file_record->pathnamehash;
    }

    /**
     * Returns the license type of the file, it is a short name referred from license table.
     *
     * @return string
     */
    public function get_license() {
        return $this->file_record->license;
    }

    /**
     * Returns the author name of the file.
     *
     * @return string
     */
    public function get_author() {
        return $this->file_record->author;
    }

    /**
     * Returns the source of the file, usually it is a url.
     *
     * @return string
     */
    public function get_source() {
        return $this->file_record->source;
    }

    /**
     * Returns the sort order of file
     *
     * @return int
     */
    public function get_sortorder() {
        return $this->file_record->sortorder;
    }
}
