<?php  //$Id$

class file_info_stored extends file_info {
    protected $lf;
    protected $urlbase;
    protected $areavisiblename;
    protected $itemidused;
    protected $readaccess;
    protected $writeaccess;

    public function __construct($browser, $context, $localfile, $urlbase, $areavisiblename, $itemidused, $readaccess, $writeaccess) {
        parent::__construct($browser, $context);

        $this->lf              = $localfile;
        $this->urlbase         = $urlbase;
        $this->areavisiblename = $areavisiblename;
        $this->itemidused      = $itemidused;
        $this->readaccess      = $readaccess;
        $this->writeaccess     = $writeaccess;
    }

    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>$this->lf->get_filearea(),
                     'itemid'   =>$this->lf->get_itemid(),
                     'filepath' =>$this->lf->get_filepath(),
                     'filename' =>$this->lf->get_filename());
    }

    public function get_visible_name() {
        $filename = $this->lf->get_filename();
        $filepath = $this->lf->get_filepath();

        if ($filename !== '.') {
            return $filename;

        } else {
            $dir = trim($filepath, '/');
            $dir = explode('/', $dir);
            $dir = array_pop($dir);
            if ($dir === '') {
                if ($this->itemidused) {
                    return $this->itemid;
                } else {
                    return $this->areavisiblename;
                }
            } else {
                return $dir;
            }
        }
    }

    public function get_url($forcedownload=false, $https=false) {
        global $CFG;

        if (!$this->is_readable()) {
            return null;
        }

        if ($this->is_directory()) {
            return null;
        }

        $this->urlbase;
        $contextid = $this->lf->get_contextid();
        $filearea  = $this->lf->get_filearea();
        $filepath  = $this->lf->get_filepath();
        $filename  = $this->lf->get_filename();
        $itemid    = $this->lf->get_itemid();

        if ($this->itemidused) {
            $path = '/'.$contextid.'/'.$filearea.'/'.$itemid.$filepath.$filename;
        } else {
            $path = '/'.$contextid.'/'.$filearea.$filepath.$filename;
        }
        return $this->browser->encodepath($this->urlbase, $path, $forcedownload, $https);
    }

    public function is_readable() {
        return $this->readaccess;
    }

    public function is_writable() {
        return $this->writeaccess;
    }

    public function get_filesize() {
        return $this->lf->get_filesize();
    }

    public function get_mimetype() {
        // TODO: add some custom mime icons for courses, categories??
        return $this->lf->get_mimetype();
    }

    public function get_timecreated() {
        return $this->lf->get_timecreated();
    }

    public function get_timemodified() {
        return $this->lf->get_timemodified();
    }

    public function is_directory() {
        if (!$this->lf) {
            return true;
        }

        return ($this->lf->get_filename() === '.');
    }

    public function get_children() {
        if ($this->lf->get_filename() !== '.') {
            return array(); //not a dir
        }
        return $this->browser->build_stored_file_children($this->context, $this->lf->get_filearea(), $this->lf->get_itemid(), $this->lf->get_filepath(),
                                                         $this->urlbase, $this->areavisiblename, $this->itemidused, $this->readaccess, $this->writeaccess);
    }

    public function get_parent() {
        if ($this->lf->get_filename() !== '.') {
            return $this->browser->get_file_info($this->context, $this->lf->get_filearea(), $this->lf->get_itemid(), $this->lf->get_filepath(), '.');
        }

        if ($this->lf->get_filepath() === '/') {
            if ($this->itemidused) {
                return $this->browser->get_file_info($this->context, $this->lf->get_filearea(), $this->lf->get_itemid());
            } else {
                return $this->browser->get_file_info($this->context, $this->lf->get_filearea());
            }
        }

        $filepath = $this->lf->get_filepath();
        $filepath = trim($filepath, '/');
        $dirs = explode('/', $filepath);
        array_pop($dirs);
        $filepath = implode('/', $dirs);
        $filepath = ($filepath === '') ? '/' : "/$filepath/";

        return $this->browser->get_file_info($this->context, $this->lf->get_filearea(), $this->lf->get_itemid(), $filepath, '.');
    }

    public function create_directory($newdirname, $userid=null) {
        if (!$this->is_writable() or $this->lf->get_filename() !== '.') {
            return null;
        }

        $newdirname = clean_param($newdirname, PARAM_FILE);
        if ($newdirname === '') {
            return null;
        }

        $filepath = $this->lf->get_filepath().'/'.$newdirname.'/';

        $fs = get_file_storage();

        if ($file = $fs->create_directory($this->lf->get_contextid(), $this->lf->get_filearea(), $this->lf->get_itemid(), $filepath, $userid)) {
            return $this->browser->get_file_info($this->context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }


    public function create_file_from_string($newfilename, $content, $userid=null) {
        if (!$this->is_writable() or $this->lf->get_filename() !== '.') {
            return null;
        }

        $newfilename = clean_param($newfilename, PARAM_FILE);
        if ($newfilename === '') {
            return null;
        }

        $now = time();

        $newrecord = new object();
        $newrecord->contextid = $this->lf->get_contextid();
        $newrecord->filearea  = $this->lf->get_filearea();
        $newrecord->itemid    = $this->lf->get_itemid();
        $newrecord->filepath  = $this->lf->get_filepath();
        $newrecord->filename  = $newfilename;

        $newrecord->timecreated  = $now;
        $newrecord->timemodified = $now;
        $newrecord->mimetype     = mimeinfo('type', $newfilename);
        $newrecord->userid       = $userid;

        $fs = get_file_storage();

        if ($file = $fs->create_file_from_string($newrecord, $content)) {
            return $this->browser->get_file_info($this->context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }

    public function create_file_from_pathname($newfilename, $pathname, $userid=null) {
        if (!$this->is_writable() or $this->lf->get_filename() !== '.') {
            return null;
        }

        $newfilename = clean_param($newfilename, PARAM_FILE);
        if ($newfilename === '') {
            return null;
        }

        $now = time();

        $newrecord = new object();
        $newrecord->contextid = $this->lf->get_contextid();
        $newrecord->filearea  = $this->lf->get_filearea();
        $newrecord->itemid    = $this->lf->get_itemid();
        $newrecord->filepath  = $this->lf->get_filepath();
        $newrecord->filename  = $newfilename;

        $newrecord->timecreated  = $now;
        $newrecord->timemodified = $now;
        $newrecord->mimetype     = mimeinfo('type', $newfilename);
        $newrecord->userid       = $userid;

        $fs = get_file_storage();

        if ($file = $fs->create_file_from_pathname($newrecord, $pathname)) {
            return $this->browser->get_file_info($this->context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }

    public function create_file_from_localfile($newfilename, $fid, $userid=null) {
        if (!$this->is_writable() or $this->lf->get_filename() !== '.') {
            return null;
        }

        $newfilename = clean_param($newfilename, PARAM_FILE);
        if ($newfilename === '') {
            return null;
        }

        $now = time();

        $newrecord = new object();
        $newrecord->contextid = $this->lf->get_contextid();
        $newrecord->filearea  = $this->lf->get_filearea();
        $newrecord->itemid    = $this->lf->get_itemid();
        $newrecord->filepath  = $this->lf->get_filepath();
        $newrecord->filename  = $newfilename;

        $newrecord->timecreated  = $now;
        $newrecord->timemodified = $now;
        $newrecord->mimetype     = mimeinfo('type', $newfilename);
        $newrecord->userid       = $userid;

        $fs = get_file_storage();

        if ($file = $fs->create_file_from_localfile($newrecord, $fid)) {
            return $this->browser->get_file_info($this->context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }

    public function delete() {
        if (!$this->lf or !$this->is_writable()) {
            return false;
        }

        if ($this->is_directory()) {
            $filepath = $this->lf->get_filepath();
            $fs = get_file_storage();
            $localfiles = $fs->get_area_files($this->context->id, $this->lf->get_filearea(), $this->lf->get_itemid(), "");
            foreach ($localfiles as $file) {
                if (strpos($file->get_filepath(), $filepath) === 0) {
                    $file->delete();
                }
            }
        }

        return $this->lf->delete();
    }
}
