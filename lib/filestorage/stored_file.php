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
 * @package   core_files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing local files stored in a sha1 file pool.
 *
 * Since Moodle 2.0 file contents are stored in sha1 pool and
 * all other file information is stored in new "files" database table.
 *
 * @package   core_files
 * @category  files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class stored_file {
    /** @var file_storage file storage pool instance */
    private $fs;
    /** @var stdClass record from the files table left join files_reference table */
    private $file_record;
    /** @var string location of content files */
    private $filedir;
    /** @var repository repository plugin instance */
    private $repository;

    /**
     * Constructor, this constructor should be called ONLY from the file_storage class!
     *
     * @param file_storage $fs file  storage instance
     * @param stdClass $file_record description of file
     * @param string $filedir location of file directory with sh1 named content files
     */
    public function __construct(file_storage $fs, stdClass $file_record, $filedir) {
        global $DB, $CFG;
        $this->fs          = $fs;
        $this->file_record = clone($file_record); // prevent modifications
        $this->filedir     = $filedir; // keep secret, do not expose!

        if (!empty($file_record->repositoryid)) {
            require_once("$CFG->dirroot/repository/lib.php");
            $this->repository = repository::get_repository_by_id($file_record->repositoryid, SYSCONTEXTID);
            if ($this->repository->supported_returntypes() & FILE_REFERENCE != FILE_REFERENCE) {
                // Repository cannot do file reference.
                throw new moodle_exception('error');
            }
        } else {
            $this->repository = null;
        }
    }

    /**
     * Whether or not this is a external resource
     *
     * @return bool
     */
    public function is_external_file() {
        return !empty($this->repository);
    }

    /**
     * Update some file record fields
     * NOTE: Must remain protected
     *
     * @param stdClass $dataobject
     */
    protected function update($dataobject) {
        global $DB;
        $keys = array_keys((array)$this->file_record);
        foreach ($dataobject as $field => $value) {
            if (in_array($field, $keys)) {
                if ($field == 'contextid' and (!is_number($value) or $value < 1)) {
                    throw new file_exception('storedfileproblem', 'Invalid contextid');
                }

                if ($field == 'component') {
                    $value = clean_param($value, PARAM_COMPONENT);
                    if (empty($value)) {
                        throw new file_exception('storedfileproblem', 'Invalid component');
                    }
                }

                if ($field == 'filearea') {
                    $value = clean_param($value, PARAM_AREA);
                    if (empty($value)) {
                        throw new file_exception('storedfileproblem', 'Invalid filearea');
                    }
                }

                if ($field == 'itemid' and (!is_number($value) or $value < 0)) {
                    throw new file_exception('storedfileproblem', 'Invalid itemid');
                }


                if ($field == 'filepath') {
                    $value = clean_param($value, PARAM_PATH);
                    if (strpos($value, '/') !== 0 or strrpos($value, '/') !== strlen($value)-1) {
                        // path must start and end with '/'
                        throw new file_exception('storedfileproblem', 'Invalid file path');
                    }
                }

                if ($field == 'filename') {
                    // folder has filename == '.', so we pass this
                    if ($value != '.') {
                        $value = clean_param($value, PARAM_FILE);
                    }
                    if ($value === '') {
                        throw new file_exception('storedfileproblem', 'Invalid file name');
                    }
                }

                if ($field === 'timecreated' or $field === 'timemodified') {
                    if (!is_number($value)) {
                        throw new file_exception('storedfileproblem', 'Invalid timestamp');
                    }
                    if ($value < 0) {
                        $value = 0;
                    }
                }

                if ($field == 'referencefileid' or $field == 'referencelastsync' or $field == 'referencelifetime') {
                    $value = clean_param($value, PARAM_INT);
                }

                // adding the field
                $this->file_record->$field = $value;
            } else {
                throw new coding_exception("Invalid field name, $field doesn't exist in file record");
            }
        }
        // Validate mimetype field
        // we don't use {@link stored_file::get_content_file_location()} here becaues it will try to update file_record
        $pathname = $this->get_pathname_by_contenthash();
        // try to recover the content from trash
        if (!is_readable($pathname)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($pathname)) {
                throw new file_exception('storedfilecannotread', '', $pathname);
            }
        }
        $mimetype = $this->fs->mimetype($pathname);
        $this->file_record->mimetype = $mimetype;

        $DB->update_record('files', $this->file_record);
    }

    /**
     * Rename filename
     *
     * @param string $filepath file path
     * @param string $filename file name
     */
    public function rename($filepath, $filename) {
        if ($this->fs->file_exists($this->get_contextid(), $this->get_component(), $this->get_filearea(), $this->get_itemid(), $filepath, $filename)) {
            throw new file_exception('storedfilenotcreated', '', 'file exists, cannot rename');
        }
        $filerecord = new stdClass;
        $filerecord->filepath = $filepath;
        $filerecord->filename = $filename;
        // populate the pathname hash
        $filerecord->pathnamehash = $this->fs->get_pathname_hash($this->file_record->contextid, $this->file_record->component, $this->file_record->filearea, $this->file_record->itemid, $filepath, $filename);
        $this->update($filerecord);
    }

    /**
     * Replace the content by providing another stored_file instance
     *
     * @param stored_file $storedfile
     */
    public function replace_content_with(stored_file $storedfile) {
        $contenthash = $storedfile->get_contenthash();
        $this->set_contenthash($contenthash);
    }

    /**
     * Delete file reference
     *
     */
    public function delete_reference() {
        global $DB;

        // Remove repository info.
        $this->repository = null;

        $transaction = $DB->start_delegated_transaction();

        // Remove reference info from DB.
        $DB->delete_records('files_reference', array('id'=>$this->file_record->referencefileid));

        // Must refresh $this->file_record form DB
        $filerecord = $DB->get_record('files', array('id'=>$this->get_id()));
        // Update DB
        $filerecord->referencelastsync = null;
        $filerecord->referencelifetime = null;
        $filerecord->referencefileid = null;
        $this->update($filerecord);

        $transaction->allow_commit();

        // unset object variable
        unset($this->file_record->repositoryid);
        unset($this->file_record->reference);
        unset($this->file_record->referencelastsync);
        unset($this->file_record->referencelifetime);
        unset($this->file_record->referencefileid);
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

        $transaction = $DB->start_delegated_transaction();

        // If other files referring to this file, we need convert them
        if ($files = $this->fs->get_references_by_storedfile($this)) {
            foreach ($files as $file) {
                $this->fs->import_external_file($file);
            }
        }
        // Now delete file records in DB
        $DB->delete_records('files', array('id'=>$this->file_record->id));
        $DB->delete_records('files_reference', array('id'=>$this->file_record->referencefileid));

        $transaction->allow_commit();

        // moves pool file to trash if content not needed any more
        $this->fs->deleted_file_cleanup($this->file_record->contenthash);
        return true; // BC only
    }

    /**
     * Get file pathname by contenthash
     *
     * NOTE, this function is not calling sync_external_file, it assume the contenthash is current
     * Protected - developers must not gain direct access to this function.
     *
     * @return string full path to pool file with file content
     */
    protected function get_pathname_by_contenthash() {
        // Detect is local file or not.
        $contenthash = $this->file_record->contenthash;
        $l1 = $contenthash[0].$contenthash[1];
        $l2 = $contenthash[2].$contenthash[3];
        return "$this->filedir/$l1/$l2/$contenthash";
    }

    /**
     * Get file pathname by given contenthash, this method will try to sync files
     *
     * Protected - developers must not gain direct access to this function.
     *
     * NOTE: do not make this public, we must not modify or delete the pool files directly! ;-)
     *
     * @return string full path to pool file with file content
     **/
    protected function get_content_file_location() {
        $this->sync_external_file();
        return $this->get_pathname_by_contenthash();
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
        return fopen($path, 'rb'); // Binary reading only!!
    }

    /**
     * Dumps file content to page.
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
     * @param file_packer $packer file packer instance
     * @return array of file infos
     */
    public function list_files(file_packer $packer) {
        $archivefile = $this->get_content_file_location();
        return $packer->list_files($archivefile);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param file_packer $packer file packer instance
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
     * @param file_packer $packer file packer instance
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $pathbase path base
     * @param int $userid user ID
     * @return array|bool list of processed files; false if error
     */
    public function extract_to_storage(file_packer $packer, $contextid, $component, $filearea, $itemid, $pathbase, $userid = NULL) {
        $archivefile = $this->get_content_file_location();
        return $packer->extract_to_storage($archivefile, $contextid, $component, $filearea, $itemid, $pathbase);
    }

    /**
     * Add file/directory into archive.
     *
     * @param file_archive $filearch file archive instance
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
     *
     * @return mixed array with width, height and mimetype; false if not an image
     */
    public function get_imageinfo() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        $mimetype = $this->get_mimetype();
        if (!preg_match('|^image/|', $mimetype) || !filesize($path) || !($imageinfo = getimagesize($path))) {
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
        if (!file_mimetype_in_typegroup($mimetype, 'web_image')) {
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
     * Sync external files
     *
     * @return bool true if file content changed, false if not
     */
    public function sync_external_file() {
        global $CFG, $DB;
        if (empty($this->file_record->referencefileid)) {
            return false;
        }
        if (empty($this->file_record->referencelastsync) or ($this->file_record->referencelastsync + $this->file_record->referencelifetime < time())) {
            require_once($CFG->dirroot.'/repository/lib.php');
            if (repository::sync_external_file($this)) {
                $prevcontent = $this->file_record->contenthash;
                $sql = "SELECT f.*, r.repositoryid, r.reference
                          FROM {files} f
                     LEFT JOIN {files_reference} r
                               ON f.referencefileid = r.id
                         WHERE f.id = ?";
                $this->file_record = $DB->get_record_sql($sql, array($this->file_record->id), MUST_EXIST);
                return ($prevcontent !== $this->file_record->contenthash);
            }
        }
        return false;
    }

    /**
     * Returns context id of the file
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
        $this->sync_external_file();
        return $this->file_record->filesize;
    }

    /**
     * Returns the size of file in bytes.
     *
     * @param int $filesize bytes
     */
    public function set_filesize($filesize) {
        $filerecord = new stdClass;
        $filerecord->filesize = $filesize;
        $this->update($filerecord);
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
        $this->sync_external_file();
        return $this->file_record->timemodified;
    }

    /**
     * set timemodified
     *
     * @param int $timemodified
     */
    public function set_timemodified($timemodified) {
        $filerecord = new stdClass;
        $filerecord->timemodified = $timemodified;
        $this->update($filerecord);
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
        $this->sync_external_file();
        return $this->file_record->contenthash;
    }

    /**
     * Set contenthash
     *
     * @param string $contenthash
     */
    protected function set_contenthash($contenthash) {
        // make sure the content exists in moodle file pool
        if ($this->fs->content_exists($contenthash)) {
            $filerecord = new stdClass;
            $filerecord->contenthash = $contenthash;
            $this->update($filerecord);
        } else {
            throw new file_exception('storedfileproblem', 'Invalid contenthash, content must be already in filepool', $contenthash);
        }
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
     * Set license
     *
     * @param string $license license
     */
    public function set_license($license) {
        $filerecord = new stdClass;
        $filerecord->license = $license;
        $this->update($filerecord);
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
     * Set author
     *
     * @param string $author
     */
    public function set_author($author) {
        $filerecord = new stdClass;
        $filerecord->author = $author;
        $this->update($filerecord);
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
     * Set license
     *
     * @param string $license license
     */
    public function set_source($source) {
        $filerecord = new stdClass;
        $filerecord->source = $source;
        $this->update($filerecord);
    }


    /**
     * Returns the sort order of file
     *
     * @return int
     */
    public function get_sortorder() {
        return $this->file_record->sortorder;
    }

    /**
     * Set file sort order
     *
     * @param int $sortorder
     * @return int
     */
    public function set_sortorder($sortorder) {
        $filerecord = new stdClass;
        $filerecord->sortorder = $sortorder;
        $this->update($filerecord);
    }

    /**
     * Returns repository id
     *
     * @return int|null
     */
    public function get_repository_id() {
        if (!empty($this->repository)) {
            return $this->repository->id;
        } else {
            return null;
        }
    }

    /**
     * get reference file id
     * @return int
     */
    public function get_referencefileid() {
        return $this->file_record->referencefileid;
    }

    /**
     * Get reference last sync time
     * @return int
     */
    public function get_referencelastsync() {
        return $this->file_record->referencelastsync;
    }

    /**
     * Get reference last sync time
     * @return int
     */
    public function get_referencelifetime() {
        return $this->file_record->referencelifetime;
    }
    /**
     * Returns file reference
     *
     * @return string
     */
    public function get_reference() {
        return $this->file_record->reference;
    }

    /**
     * Get human readable file reference information
     *
     * @return string
     */
    public function get_reference_details() {
        return $this->repository->get_reference_details($this->get_reference());
    }

    /**
     * Send file references
     *
     * @param int $lifetime Number of seconds before the file should expire from caches (default 24 hours)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($lifetime, $filter, $forcedownload, $options) {
        $this->repository->send_file($this, $lifetime, $filter, $forcedownload, $options);
    }
}
