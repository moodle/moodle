<?php  //$Id$

abstract class file_info {
    protected $context;
    protected $browser;

    public function __construct($browser, $context) {
        $this->browser = $browser;
        $this->context = $context;
    }

    public abstract function get_params();
    public abstract function get_visible_name();
    public abstract function is_directory();
    public abstract function get_children();
    public abstract function get_parent();


    public function get_params_rawencoded() {
        $params = $this->get_params();
        $encoded = array();
        $encoded[] = 'contextid='.$params['contextid'];
        $encoded[] = 'filearea='.$params['filearea'];
        $encoded[] = 'itemid='.(is_null($params['itemid']) ? -1 : $params['itemid']);
        $encoded[] = 'filepath='.(is_null($params['filepath']) ? '' : rawurlencode($params['filepath']));
        $encoded[] = 'filename='.((is_null($params['filename']) or $params['filename'] === '.') ? '' : rawurlencode($params['filename']));

        return $encoded;
    }



    public function get_url($forcedownload=false, $https=false) {
        return null;
    }

    public function is_readable() {
        return true;
    }

    public function is_writable() {
        return true;
    }

    public function get_filesize() {
        return null;
    }

    public function get_mimetype() {
        // TODO: add some custom mime icons for courses, categories??
        return null;
    }

    public function get_timecreated() {
        return null;
    }

    public function get_timemodified() {
        return null;
    }

    public function create_directory($newdirname, $userid=null) {
        return null;
    }

    public function create_file_from_string($newfilename, $content, $userid=null) {
        return null;
    }

    public function create_file_from_pathname($newfilename, $pathname, $userid=null) {
        return null;
    }

    public function create_file_from_storedfile($newfilename, $fid, $userid=null) {
        return null;
    }

    public function delete() {
        return false;
    }

//TODO: following methods are not implemented yet ;-)

    //public abstract function copy(location params);
    //public abstract function move(location params);
    //public abstract function rename(new name);
    //public abstract function unzip(location params);
    //public abstract function zip(zip file, file info);
}
