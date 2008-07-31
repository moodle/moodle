<?php  //$Id$

/**
 * Class representing local files stored in sha1 file pool
 */
class stored_file {
    private $fs;
    private $file_record;

    /**
     * Constructor
     * @param object $fs file  storage instance
     * @param object $file_record description of file
     */
    public function __construct($fs, $file_record) {
        $this->fs = $fs;
        $this->file_record = clone($file_record);
    }

    /**
     * Is this a directory?
     * @return bool
     */
    public function is_directory() {
        return $this->file_record->filename === '.';
    }

    /**
     * Delete file
     * @return success
     */
    public function delete() {
        global $DB;
        $this->fs->mark_delete_candidate($this->file_record->contenthash);
        return $DB->delete_records('files', array('id'=>$this->file_record->id));
    }

    /**
     * Protected - devs must not gain direct access to this function
     **/
    protected function get_content_file_location() {
        // NOTE: do not make this public, we must not modify or delete the pool files directly! ;-)
        $hashpath = $this->fs->path_from_hash($this->file_record->contenthash);
        return $hashpath.'/'.$this->file_record->contenthash;
    }

    /**
     * Returns file handle - read only mode, no writing allowed into pool files!
     * @return file handle
     */
    public function get_content_file_handle() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            throw new file_exception('localfilecannotread');
        }
        return fopen($path, 'rb'); //binary reading only!!
    }

    /**
     * Dumps file content to page
     * @return file handle
     */
    public function readfile() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            throw new file_exception('localfilecannotread');
        }
        readfile($path);
    }

    /**
     * Returns file content as string
     * @return string content
     */
    public function get_content() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            throw new file_exception('localfilecannotread');
        }
        return file_get_contents($this->get_content_file_location());
    }

    public function get_contextid() {
        return $this->file_record->contextid;
    }

    public function get_filearea() {
        return $this->file_record->filearea;
    }

    public function get_itemid() {
        return $this->file_record->itemid;
    }

    public function get_filepath() {
        return $this->file_record->filepath;
    }

    public function get_filename() {
        return $this->file_record->filename;
    }

    public function get_userid() {
        return $this->file_record->userid;
    }

    public function get_filesize() {
        return $this->file_record->filesize;
    }

    public function get_mimetype() {
        return $this->file_record->mimetype;
    }

    public function get_timecreated() {
        return $this->file_record->timecreated;
    }

    public function get_timemodified() {
        return $this->file_record->timemodified;
    }
}
