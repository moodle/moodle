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
 * Utility class for browsing of stored files.
 *
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents an actual file or folder - a row in the file table -
 * in the tree navigated by @see{file_browser}.
 *
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_stored extends file_info {
    protected $lf;
    protected $urlbase;
    protected $topvisiblename;
    protected $itemidused;
    protected $readaccess;
    protected $writeaccess;
    protected $areaonly;

    /**
     * Constructor
     *
     * @param file_browser $browser
     * @param stdClass $context
     * @param stored_file|virtual_root_file $storedfile
     * @param string $urlbase the serving script - usually the $CFG->wwwroot/.'pluginfile.php'
     * @param string $topvisiblename the human readable name of this area
     * @param int|bool $itemidused false if itemid  always 0 and not included in URL
     * @param bool $readaccess allow file reading
     * @param bool $writeaccess allow file write, delete
     * @param string $areaonly do not show links to parent context/area
     */
    public function __construct(file_browser $browser, $context, $storedfile, $urlbase, $topvisiblename, $itemidused, $readaccess, $writeaccess, $areaonly) {
        parent::__construct($browser, $context);

        $this->lf             = $storedfile;
        $this->urlbase        = $urlbase;
        $this->topvisiblename = $topvisiblename;
        $this->itemidused     = $itemidused;
        $this->readaccess     = $readaccess;
        $this->writeaccess    = $writeaccess;
        $this->areaonly       = $areaonly;
    }

    /**
     * Returns list of standard virtual file/directory identification.
     * The difference from stored_file parameters is that null values
     * are allowed in all fields
     * @return array with keys contextid, component, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'component'=>$this->lf->get_component(),
                     'filearea' =>$this->lf->get_filearea(),
                     'itemid'   =>$this->lf->get_itemid(),
                     'filepath' =>$this->lf->get_filepath(),
                     'filename' =>$this->lf->get_filename());
    }

    /**
     * Returns localised visible name.
     * @return string
     */
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
                return $this->topvisiblename;
            } else {
                return $dir;
            }
        }
    }

    /**
     * Returns file download url
     * @param bool $forcedownload
     * @param bool $htts force https
     * @return string url
     */
    public function get_url($forcedownload=false, $https=false) {
        if (!$this->is_readable()) {
            return null;
        }

        if ($this->is_directory()) {
            return null;
        }

        $this->urlbase;
        $contextid = $this->lf->get_contextid();
        $component = $this->lf->get_component();
        $filearea  = $this->lf->get_filearea();
        $itemid    = $this->lf->get_itemid();
        $filepath  = $this->lf->get_filepath();
        $filename  = $this->lf->get_filename();

        if ($this->itemidused) {
            $path = '/'.$contextid.'/'.$component.'/'.$filearea.'/'.$itemid.$filepath.$filename;
        } else {
            $path = '/'.$contextid.'/'.$component.'/'.$filearea.$filepath.$filename;
        }
        return file_encode_url($this->urlbase, $path, $forcedownload, $https);
    }

    /**
     * Can I read content of this file or enter directory?
     * @return bool
     */
    public function is_readable() {
        return $this->readaccess;
    }

    /**
     * Can I add new files or directories?
     * @return bool
     */
    public function is_writable() {
        return $this->writeaccess;
    }

    /**
     * Is this top of empty area?
     *
     * @return bool
     */
    public function is_empty_area() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            // test the emptiness only in the top most level, it does not make sense at lower levels
            $fs = get_file_storage();
            return $fs->is_area_empty($this->lf->get_contextid(), $this->lf->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid());
        } else {
            return false;
        }
    }

    /**
     * Returns file size in bytes, null for directories
     * @return int bytes or null if not known
     */
    public function get_filesize() {
        return $this->lf->get_filesize();
    }

    /**
     * Returns mimetype
     * @return string mimetype or null if not known
     */
    public function get_mimetype() {
        return $this->lf->get_mimetype();
    }

    /**
     * Returns time created unix timestamp if known
     * @return int timestamp or null
     */
    public function get_timecreated() {
        return $this->lf->get_timecreated();
    }

    /**
     * Returns time modified unix timestamp if known
     * @return int timestamp or null
     */
    public function get_timemodified() {
        return $this->lf->get_timemodified();
    }

    /**
     * Is directory?
     * @return bool
     */
    public function is_directory() {
        return $this->lf->is_directory();
    }

    /**
     * Returns the license type of the file
     * @return string license short name or null
     */
    public function get_license() {
        return $this->lf->get_license();
    }

    /**
     * Returns the author name of the file
     * @return string author name or null
     */
    public function get_author() {
        return $this->lf->get_author();
    }

    /**
     * Returns the source of the file
     * @return string a source url or null
     */
    public function get_source() {
        return $this->lf->get_source();
    }

    /**
     * Returns the sort order of the file
     * @return int
     */
    public function get_sortorder() {
        return $this->lf->get_sortorder();
    }

    /**
     * Returns list of children.
     * @return array of file_info instances
     */
    public function get_children() {
        if (!$this->lf->is_directory()) {
            return array();
        }

        $result = array();
        $fs = get_file_storage();

        $storedfiles = $fs->get_directory_files($this->context->id, $this->lf->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid(),
                                                $this->lf->get_filepath(), false, true, "filepath, filename");
        foreach ($storedfiles as $file) {
            $result[] = new file_info_stored($this->browser, $this->context, $file, $this->urlbase, $this->topvisiblename,
                                             $this->itemidused, $this->readaccess, $this->writeaccess, false);
        }

        return $result;
    }

    /**
     * Returns parent file_info instance
     * @return file_info or null for root
     */
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->is_directory()) {
            if ($this->areaonly) {
                return null;
            } else if ($this->itemidused) {
                return $this->browser->get_file_info($this->context, $this->lf->get_component(), $this->lf->get_filearea());
            } else {
                return $this->browser->get_file_info($this->context);
            }
        }

        if (!$this->lf->is_directory()) {
            return $this->browser->get_file_info($this->context, $this->lf->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid(), $this->lf->get_filepath(), '.');
        }

        $filepath = $this->lf->get_filepath();
        $filepath = trim($filepath, '/');
        $dirs = explode('/', $filepath);
        array_pop($dirs);
        $filepath = implode('/', $dirs);
        $filepath = ($filepath === '') ? '/' : "/$filepath/";

        return $this->browser->get_file_info($this->context, $this->lf->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid(), $filepath, '.');
    }

    /**
     * Create new directory, may throw exception - make sure
     * params are valid.
     * @param string $newdirname name of new directory
     * @param int id of author, default $USER->id
     * @return file_info new directory
     */
    public function create_directory($newdirname, $userid = NULL) {
        if (!$this->is_writable() or !$this->lf->is_directory()) {
            return null;
        }

        $newdirname = clean_param($newdirname, PARAM_FILE);
        if ($newdirname === '') {
            return null;
        }

        $filepath = $this->lf->get_filepath().'/'.$newdirname.'/';

        $fs = get_file_storage();

        if ($file = $fs->create_directory($this->lf->get_contextid(), $this->lf->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid(), $filepath, $userid)) {
            return $this->browser->get_file_info($this->context, $this->lf->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }


    /**
     * Create new file from string - make sure
     * params are valid.
     * @param string $newfilename name of new file
     * @param string $content of file
     * @param int id of author, default $USER->id
     * @return file_info new file
     */
    public function create_file_from_string($newfilename, $content, $userid = NULL) {
        if (!$this->is_writable() or !$this->lf->is_directory()) {
            return null;
        }

        $newfilename = clean_param($newfilename, PARAM_FILE);
        if ($newfilename === '') {
            return null;
        }

        $fs = get_file_storage();

        $now = time();

        $newrecord = new stdClass();
        $newrecord->contextid = $this->lf->get_contextid();
        $newrecord->component = $this->lf->get_component();
        $newrecord->filearea  = $this->lf->get_filearea();
        $newrecord->itemid    = $this->lf->get_itemid();
        $newrecord->filepath  = $this->lf->get_filepath();
        $newrecord->filename  = $newfilename;

        if ($fs->file_exists($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->filename)) {
            // file already exists, sorry
            return null;
        }

        $newrecord->timecreated  = $now;
        $newrecord->timemodified = $now;
        $newrecord->mimetype     = mimeinfo('type', $newfilename);
        $newrecord->userid       = $userid;

        if ($file = $fs->create_file_from_string($newrecord, $content)) {
            return $this->browser->get_file_info($this->context, $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }

    /**
     * Create new file from pathname - make sure
     * params are valid.
     * @param string $newfilename name of new file
     * @param string $pathname location of file
     * @param int id of author, default $USER->id
     * @return file_info new file
     */
    public function create_file_from_pathname($newfilename, $pathname, $userid = NULL) {
        if (!$this->is_writable() or !$this->lf->is_directory()) {
            return null;
        }

        $newfilename = clean_param($newfilename, PARAM_FILE);
        if ($newfilename === '') {
            return null;
        }

        $fs = get_file_storage();

        $now = time();

        $newrecord = new stdClass();
        $newrecord->contextid = $this->lf->get_contextid();
        $newrecord->component = $this->lf->get_component();
        $newrecord->filearea  = $this->lf->get_filearea();
        $newrecord->itemid    = $this->lf->get_itemid();
        $newrecord->filepath  = $this->lf->get_filepath();
        $newrecord->filename  = $newfilename;

        if ($fs->file_exists($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->filename)) {
            // file already exists, sorry
            return null;
        }

        $newrecord->timecreated  = $now;
        $newrecord->timemodified = $now;
        $newrecord->mimetype     = mimeinfo('type', $newfilename);
        $newrecord->userid       = $userid;

        if ($file = $fs->create_file_from_pathname($newrecord, $pathname)) {
            return $this->browser->get_file_info($this->context, $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }

    /**
     * Create new file from stored file - make sure
     * params are valid.
     * @param string $newfilename name of new file
     * @param mixed file id or stored_file of file
     * @param int id of author, default $USER->id
     * @return file_info new file
     */
    public function create_file_from_storedfile($newfilename, $fid, $userid = NULL) {
        if (!$this->is_writable() or $this->lf->get_filename() !== '.') {
            return null;
        }

        $newfilename = clean_param($newfilename, PARAM_FILE);
        if ($newfilename === '') {
            return null;
        }

        $fs = get_file_storage();

        $now = time();

        $newrecord = new stdClass();
        $newrecord->contextid = $this->lf->get_contextid();
        $newrecord->component = $this->lf->get_component();
        $newrecord->filearea  = $this->lf->get_filearea();
        $newrecord->itemid    = $this->lf->get_itemid();
        $newrecord->filepath  = $this->lf->get_filepath();
        $newrecord->filename  = $newfilename;

        if ($fs->file_exists($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->filename)) {
            // file already exists, sorry
            return null;
        }

        $newrecord->timecreated  = $now;
        $newrecord->timemodified = $now;
        $newrecord->mimetype     = mimeinfo('type', $newfilename);
        $newrecord->userid       = $userid;

        if ($file = $fs->create_file_from_storedfile($newrecord, $fid)) {
            return $this->browser->get_file_info($this->context, $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        return null;
    }

    /**
     * Delete file, make sure file is deletable first.
     * @return bool success
     */
    public function delete() {
        if (!$this->is_writable()) {
            return false;
        }

        if ($this->is_directory()) {
            $filepath = $this->lf->get_filepath();
            $fs = get_file_storage();
            $storedfiles = $fs->get_area_files($this->context->id, $this->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid(), "");
            foreach ($storedfiles as $file) {
                if (strpos($file->get_filepath(), $filepath) === 0) {
                    $file->delete();
                }
            }
        }

        return $this->lf->delete();
    }

    /**
     * Copy content of this file to local storage, overriding current file if needed.
     * @param int $contextid
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @return boolean success
     */
    public function copy_to_storage($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        if (!$this->is_readable() or $this->is_directory()) {
            return false;
        }

        $fs = get_file_storage();
        if ($existing = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename)) {
            $existing->delete();
        }
        $file_record = array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>$filepath, 'filename'=>$filename);
        $fs->create_file_from_storedfile($file_record, $this->lf);

        return true;
    }

    /**
     * Copy content of this file to local storage, overriding current file if needed.
     * @param string $pathname real local full file name
     * @return boolean success
     */
    public function copy_to_pathname($pathname) {
        if (!$this->is_readable() or $this->is_directory()) {
            return false;
        }

        if (file_exists($pathname)) {
            if (!unlink($pathname)) {
                return false;
            }
        }

        $this->lf->copy_content_to($pathname);

        return true;
    }
}
