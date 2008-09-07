<?php  //$Id$

/**
 * Root directory in empty file area
 */
class virtual_root_file {
    protected $contextid;
    protected $filearea;
    protected $itemid;

    /**
     * Constructor
     */
    public function __construct($contextid, $filearea, $itemid) {
        $this->contextid = $contextid;
        $this->filearea  = $filearea;
        $this->itemid    = $itemid;
    }

    /**
     * Is this a directory?
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Delete file
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
     * @return file handle
     */
    public function get_content_file_handle() {
        return null;
    }

    /**
     * Dumps file content to page
     * @return file handle
     */
    public function readfile() {
        return;
    }

    /**
     * Returns file content as string
     * @return string content
     */
    public function get_content() {
        return '';
    }

    /**
     * Copy content of file to give npathname
     * @param string $pathnema rela path to new file
     * @return bool success
     */
    public function copy_content_to($pathname) {
        return false;
    }

    /**
     * List contents of archive
     * @param object $file_packer
     * @return array of file infos
     */
    public function list_files(file_packer $packer) {
        return null;
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwrited
     * @param object $file_packer
     * @param string $pathname target directory
     * @return mixed list of processed files; false if error
     */
    public function extract_to_pathname(file_packer $packer, $pathname) {
        return false;
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwrited
     * @param object $file_packer
     * @param int $contextid
     * @param string $filearea
     * @param int $itemid
     * @param string $pathbase
     * @param int $userid
     * @return mixed list of processed files; false if error
     */
    public function extract_to_storage(file_packer $packer, $contextid, $filearea, $itemid, $pathbase, $userid=null) {
        return false;
    }

    /**
     * Add file/directory into archive
     * @param object $filearch
     * @param string $archivepath pathname in archive
     * @return bool success
     */
    public function archive_file(file_archive $filearch, $archivepath) {
        return false;
    }

    public function get_contextid() {
        return $this->contextid;
    }

    public function get_filearea() {
        return $this->filearea;
    }

    public function get_itemid() {
        return $this->itemid;
    }

    public function get_filepath() {
        return '/';
    }

    public function get_filename() {
        return '.';
    }

    public function get_userid() {
        return null;
    }

    public function get_filesize() {
        return 0;
    }

    public function get_mimetype() {
        return null;
    }

    public function get_timecreated() {
        return 0;
    }

    public function get_timemodified() {
        return 0;
    }

    public function get_status() {
        return 0;
    }

    public function get_id() {
        return 0;
    }

    public function get_contenthash() {
        return sha1('');
    }
}
