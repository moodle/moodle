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
 * @package   core_files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\url;

/**
 * Represents an actual file or folder - a row in the file table in the tree navigated by {@link file_browser}.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_stored extends file_info {
    /** @var stored_file|virtual_root_file stored_file or virtual_root_file instance */
    protected $lf;
    /** @var string the serving script */
    protected $urlbase;
    /** @var string the human readable name of this area */
    protected $topvisiblename;
    /** @var int|bool it's false if itemid is 0 and not included in URL */
    protected $itemidused;
    /** @var bool allow file reading */
    protected $readaccess;
    /** @var bool allow file write, delee */
    protected $writeaccess;
    /** @var string do not show links to parent context/area */
    protected $areaonly;

    /**
     * Constructor
     *
     * @param file_browser $browser file browser instance
     * @param stdClass $context context object
     * @param stored_file|virtual_root_file $storedfile stored_file instance
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
     *
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
     *
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
     * Returns the localised human-readable name of the file together with virtual path
     *
     * @return string
     */
    public function get_readable_fullname() {
        global $CFG;
        // retrieve the readable path with all parents (excluding the top most 'System')
        $fpath = array();
        for ($parent = $this; $parent && $parent->get_parent(); $parent = $parent->get_parent()) {
            array_unshift($fpath, $parent->get_visible_name());
        }

        if ($this->lf->get_component() == 'user' && $this->lf->get_filearea() == 'private') {
            // use the special syntax for user private files - 'USERNAME Private files: PATH'
            $username = array_shift($fpath);
            array_shift($fpath); // get rid of "Private Files/" in the beginning of the path
            return get_string('privatefilesof', 'repository', $username). ': '. join('/', $fpath);
        } else {
            // for all other files (except user private files) return 'Server files: PATH'

            // first, get and cache the name of the repository_local (will be used as prefix for file names):
            static $replocalname = null;
            if ($replocalname === null) {
                require_once($CFG->dirroot . "/repository/lib.php");
                $instances = repository::get_instances(array('type' => 'local'));
                if (count($instances)) {
                    $firstinstance = reset($instances);
                    $replocalname = $firstinstance->get_name();
                } else if (get_string_manager()->string_exists('pluginname', 'repository_local')) {
                    $replocalname = get_string('pluginname', 'repository_local');
                } else {
                    $replocalname = get_string('arearoot', 'repository');
                }
            }

            return $replocalname. ': '. join('/', $fpath);
        }
    }

    /**
     * Returns file download url
     *
     * @param bool $forcedownload Whether or not force download
     * @param bool $https whether or not force https
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
        $url = url::make_file_url($this->urlbase, $path, $forcedownload);
        if ($https) {
            $url->set_scheme('https');
        }
        return $url->out();
    }

    /**
     * Whether or not I can read content of this file or enter directory
     *
     * @return bool
     */
    public function is_readable() {
        return $this->readaccess;
    }

    /**
     * Whether or not new files or directories can be added
     *
     * @return bool
     */
    public function is_writable() {
        return $this->writeaccess;
    }

    /**
     * Whether or not this is an empty area
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
     *
     * @return int bytes or null if not known
     */
    public function get_filesize() {
        return $this->lf->get_filesize();
    }

    /**
     * Returns width, height and mimetype of the stored image, or false
     *
     * @see stored_file::get_imageinfo()
     * @return array|false
     */
    public function get_imageinfo() {
        return $this->lf->get_imageinfo();
    }

    /**
     * Returns mimetype
     *
     * @return string mimetype or null if not known
     */
    public function get_mimetype() {
        return $this->lf->get_mimetype();
    }

    /**
     * Returns time created unix timestamp if known
     *
     * @return int timestamp or null
     */
    public function get_timecreated() {
        return $this->lf->get_timecreated();
    }

    /**
     * Returns time modified unix timestamp if known
     *
     * @return int timestamp or null
     */
    public function get_timemodified() {
        return $this->lf->get_timemodified();
    }

    /**
     * Whether or not this is a directory
     *
     * @return bool
     */
    public function is_directory() {
        return $this->lf->is_directory();
    }

    /**
     * Returns the license type of the file
     *
     * @return string license short name or null
     */
    public function get_license() {
        return $this->lf->get_license();
    }

    /**
     * Returns the author name of the file
     *
     * @return string author name or null
     */
    public function get_author() {
        return $this->lf->get_author();
    }

    /**
     * Returns the source of the file
     *
     * @return string a source url or null
     */
    public function get_source() {
        return $this->lf->get_source();
    }

    /**
     * Returns the sort order of the file
     *
     * @return int
     */
    public function get_sortorder() {
        return $this->lf->get_sortorder();
    }

    /**
     * Whether or not this is a external resource
     *
     * @return bool
     */
    public function is_external_file() {
        return $this->lf->is_external_file();
    }

    /**
     * Returns file status flag.
     *
     * @return int 0 means file OK, anything else is a problem and file can not be used
     */
    public function get_status() {
        return $this->lf->get_status();
    }

    /**
     * Returns list of children.
     *
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
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        $result = array();
        if (!$this->lf->is_directory()) {
            return $result;
        }

        $fs = get_file_storage();

        $storedfiles = $fs->get_directory_files($this->context->id, $this->lf->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid(),
                                                $this->lf->get_filepath(), false, true, "filepath, filename");
        foreach ($storedfiles as $file) {
            $extension = core_text::strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION));
            if ($file->is_directory() || $extensions === '*' || (!empty($extension) && in_array('.'.$extension, $extensions))) {
                $fileinfo = new file_info_stored($this->browser, $this->context, $file, $this->urlbase, $this->topvisiblename,
                                                 $this->itemidused, $this->readaccess, $this->writeaccess, false);
                if (!$file->is_directory() || $fileinfo->count_non_empty_children($extensions)) {
                    $result[] = $fileinfo;
                }
            }
        }

        return $result;
    }

    /**
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        global $DB;
        if (!$this->lf->is_directory()) {
            return 0;
        }

        $filepath = $this->lf->get_filepath();
        $length = core_text::strlen($filepath);
        $sql = "SELECT filepath, filename
                  FROM {files} f
                 WHERE f.contextid = :contextid AND f.component = :component AND f.filearea = :filearea AND f.itemid = :itemid
                       AND ".$DB->sql_substr("f.filepath", 1, $length)." = :filepath
                       AND filename <> '.' ";
        $params = array('contextid' => $this->context->id,
            'component' => $this->lf->get_component(),
            'filearea' => $this->lf->get_filearea(),
            'itemid' => $this->lf->get_itemid(),
            'filepath' => $filepath);
        list($sql2, $params2) = $this->build_search_files_sql($extensions);
        $rs = $DB->get_recordset_sql($sql.' '.$sql2, array_merge($params, $params2));
        $children = array();
        foreach ($rs as $record) {
            // we don't need to check access to individual files here, since the user can access parent
            if ($record->filepath === $filepath) {
                $children[] = $record->filename;
            } else {
                $path = explode('/', core_text::substr($record->filepath, $length));
                if (!in_array($path[0], $children)) {
                    $children[] = $path[0];
                }
            }
            if (count($children) >= $limit) {
                break;
            }
        }
        $rs->close();
        return count($children);
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info|null file_info instance or null for root
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
     *
     * @param string $newdirname name of new directory
     * @param int $userid id of author, default $USER->id
     * @return file_info|null new directory's file_info instance or null if failed
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
     *
     * @param string $newfilename name of new file
     * @param string $content of file
     * @param int $userid id of author, default $USER->id
     * @return file_info|null new file's file_info instance or null if failed
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
     *
     * @param string $newfilename name of new file
     * @param string $pathname location of file
     * @param int $userid id of author, default $USER->id
     * @return file_info|null new file's file_info instance or null if failed
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
     *
     * @param string $newfilename name of new file
     * @param int|stored_file $fid file id or stored_file of file
     * @param int $userid id of author, default $USER->id
     * @return file_info|null new file's file_info instance or null if failed
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
     *
     * @return bool success
     */
    public function delete() {
        if (!$this->is_writable()) {
            return false;
        }

        if ($this->is_directory()) {
            $filepath = $this->lf->get_filepath();
            $fs = get_file_storage();
            $storedfiles = $fs->get_area_files($this->context->id, $this->get_component(), $this->lf->get_filearea(), $this->lf->get_itemid());
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
     *
     * @param array|stdClass $filerecord contains contextid, component, filearea,
     *    itemid, filepath, filename and optionally other attributes of the new file
     * @return bool success
     */
    public function copy_to_storage($filerecord) {
        if (!$this->is_readable() or $this->is_directory()) {
            return false;
        }
        $filerecord = (array)$filerecord;

        $fs = get_file_storage();
        if ($existing = $fs->get_file($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'], $filerecord['itemid'], $filerecord['filepath'], $filerecord['filename'])) {
            $existing->delete();
        }
        $fs->create_file_from_storedfile($filerecord, $this->lf);

        return true;
    }

    /**
     * Copy content of this file to local storage, overriding current file if needed.
     *
     * @param string $pathname real local full file name
     * @return bool success
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
