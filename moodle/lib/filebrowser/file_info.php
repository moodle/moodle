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
 * Base for all file browsing classes.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for things in the tree navigated by {@link file_browser}.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class file_info {

    /** @var stdClass File context */
    protected $context;

    /** @var file_browser File browser instance */
    protected $browser;

    /**
     * Constructor
     *
     * @param file_browser $browser file_browser instance
     * @param stdClass $context
     */
    public function __construct($browser, $context) {
        $this->browser = $browser;
        $this->context = $context;
    }

    /**
     * Returns list of standard virtual file/directory identification.
     * The difference from stored_file parameters is that null values
     * are allowed in all fields
     *
     * @return array with keys contextid, component, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array('contextid' => $this->context->id,
                     'component' => null,
                     'filearea'  => null,
                     'itemid'    => null,
                     'filepath'  => null,
                     'filename'  => null);
    }

    /**
     * Returns localised visible name.
     *
     * @return string
     */
    public abstract function get_visible_name();

    /**
     * Whether or not this is a directory
     *
     * @return bool
     */
    public abstract function is_directory();

    /**
     * Returns list of children.
     *
     * @return array of file_info instances
     */
    public abstract function get_children();

    /**
     * Builds SQL sub query (WHERE clause) for selecting files with the specified extensions
     *
     * If $extensions == '*' (any file), the result is array('', array())
     * otherwise the result is something like array('AND filename ...', array(...))
     *
     * @param string|array $extensions - either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @param string $prefix prefix for DB table files in the query (empty by default)
     * @return array of two elements: $sql - sql where clause and $params - array of parameters
     */
    protected function build_search_files_sql($extensions, $prefix = null) {
        global $DB;
        if (strlen($prefix)) {
            $prefix = $prefix.'.';
        } else {
            $prefix = '';
        }
        $sql = '';
        $params = array();
        if (is_array($extensions) && !in_array('*', $extensions)) {
            $likes = array();
            $cnt = 0;
            foreach ($extensions as $ext) {
                $cnt++;
                $likes[] = $DB->sql_like($prefix.'filename', ':filename'.$cnt, false);
                $params['filename'.$cnt] = '%'.$ext;
            }
            $sql .= ' AND (' . join(' OR ', $likes) . ')';
        }
        return array($sql, $params);
     }

    /**
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * It is recommended to overwrite this function so it uses a proper SQL
     * query and does not create unnecessary file_info objects (might require a lot of time
     * and memory usage on big sites).
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        $list = $this->get_children();
        $nonemptylist = array();
        foreach ($list as $fileinfo) {
            if ($fileinfo->is_directory()) {
                if ($fileinfo->count_non_empty_children($extensions)) {
                    $nonemptylist[] = $fileinfo;
                }
            } else if ($extensions === '*') {
                $nonemptylist[] = $fileinfo;
            } else {
                $filename = $fileinfo->get_visible_name();
                $extension = core_text::strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!empty($extension) && in_array('.' . $extension, $extensions)) {
                    $nonemptylist[] = $fileinfo;
                }
            }
        }
        return $nonemptylist;
    }

    /**
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * We usually don't need the exact number of non empty children if it is >=2 (see param $limit)
     * This function is used by repository_local to evaluate if the folder is empty. But
     * it also can be used to check if folder has only one subfolder because in some cases
     * this subfolder can be skipped.
     *
     * It is strongly recommended to overwrite this function so it uses a proper SQL
     * query and does not create file_info objects (later might require a lot of time
     * and memory usage on big sites).
     *
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        $list = $this->get_children();
        $cnt = 0;
        // first loop through files
        foreach ($list as $fileinfo) {
            if (!$fileinfo->is_directory()) {
                if ($extensions !== '*') {
                    $filename = $fileinfo->get_visible_name();
                    $extension = core_text::strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if (empty($extension) || !in_array('.' . $extension, $extensions)) {
                        continue;
                    }
                }
                if ((++$cnt) >= $limit) {
                    return $cnt;
                }
            }
        }
        // now loop through directories
        foreach ($list as $fileinfo) {
            if ($fileinfo->is_directory() && $fileinfo->count_non_empty_children($extensions)) {
                if ((++$cnt) >= $limit) {
                    return $cnt;
                }
            }
        }
        return $cnt;
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info or null for root
     */
    public abstract function get_parent();

    /**
     * Returns array of url encoded params.
     *
     * @return array with numeric keys
     */
    public function get_params_rawencoded() {
        $params = $this->get_params();
        $encoded = array();
        $encoded[] = 'contextid=' . $params['contextid'];
        $encoded[] = 'component=' . $params['component'];
        $encoded[] = 'filearea=' . $params['filearea'];
        $encoded[] = 'itemid=' . (is_null($params['itemid']) ? -1 : $params['itemid']);
        $encoded[] = 'filepath=' . (is_null($params['filepath']) ? '' : rawurlencode($params['filepath']));
        $encoded[] = 'filename=' . ((is_null($params['filename']) or $params['filename'] === '.') ? '' : rawurlencode($params['filename']));

        return $encoded;
    }

    /**
     * Returns file download url
     *
     * @param bool $forcedownload whether or not force download
     * @param bool $https whether or not force https
     * @return string url
     */
    public function get_url($forcedownload=false, $https=false) {
        return null;
    }

    /**
     * Whether or not I can read content of this file or enter directory
     *
     * @return bool
     */
    public function is_readable() {
        return true;
    }

    /**
     * Whether or not new files or directories can be added
     *
     * @return bool
     */
    public function is_writable() {
        return true;
    }

    /**
     * Is this info area and is it "empty"? Are there any files in subfolders?
     *
     * This is used mostly in repositories to reduce the
     * number of empty folders. This method may be very slow,
     * use with care.
     *
     * @return bool
     */
    public function is_empty_area() {
        return false;
    }

    /**
     * Returns file size in bytes, null for directories
     *
     * @return int bytes or null if not known
     */
    public function get_filesize() {
        return null;
    }

    /**
     * Returns mimetype
     *
     * @return string mimetype or null if not known
     */
    public function get_mimetype() {
        return null;
    }

    /**
     * Returns time created unix timestamp if known
     *
     * @return int timestamp or null
     */
    public function get_timecreated() {
        return null;
    }

    /**
     * Returns time modified unix timestamp if known
     *
     * @return int timestamp or null
     */
    public function get_timemodified() {
        return null;
    }

    /**
     * Returns the license type of the file
     * @return string license short name or null
     */
    public function get_license() {
        return null;
    }

    /**
     * Returns the author name of the file
     *
     * @return string author name or null
     */
    public function get_author() {
        return null;
    }

    /**
     * Returns the source of the file
     *
     * @return string a source url or null
     */
    public function get_source() {
        return null;
    }

    /**
     * Returns the sort order of the file
     *
     * @return int
     */
    public function get_sortorder() {
        return 0;
    }

    /**
     * Whether or not this is a external resource
     *
     * @return bool
     */
    public function is_external_file() {
        return false;
    }

    /**
     * Returns file status flag.
     *
     * @return int 0 means file OK, anything else is a problem and file can not be used
     */
    public function get_status() {
        return 0;
    }

    /**
     * Returns the localised human-readable name of the file together with virtual path
     *
     * @see file_info_stored::get_readable_fullname()
     * @return string
     */
    public function get_readable_fullname() {
        return null;
    }

    /**
     * Create new directory, may throw exception - make sure
     * params are valid.
     *
     * @param string $newdirname name of new directory
     * @param int $userid id of author, default $USER->id
     * @return file_info new directory
     */
    public function create_directory($newdirname, $userid = NULL) {
        return null;
    }

    /**
     * Create new file from string - make sure
     * params are valid.
     *
     * @param string $newfilename name of new file
     * @param string $content of file
     * @param int $userid id of author, default $USER->id
     * @return file_info new file
     */
    public function create_file_from_string($newfilename, $content, $userid = NULL) {
        return null;
    }

    /**
     * Create new file from pathname - make sure
     * params are valid.
     *
     * @param string $newfilename name of new file
     * @param string $pathname location of file
     * @param int $userid id of author, default $USER->id
     * @return file_info new file
     */
    public function create_file_from_pathname($newfilename, $pathname, $userid = NULL) {
        return null;
    }

    /**
     * Create new file from stored file - make sure
     * params are valid.
     *
     * @param string $newfilename name of new file
     * @param int|stored_file $fid id or stored_file of file
     * @param int $userid id of author, default $USER->id
     * @return file_info new file
     */
    public function create_file_from_storedfile($newfilename, $fid, $userid = NULL) {
        return null;
    }

    /**
     * Delete file, make sure file is deletable first.
     *
     * @return bool success
     */
    public function delete() {
        return false;
    }

    /**
     * Copy content of this file to local storage, overriding current file if needed.
     *
     * @param array|stdClass $filerecord contains contextid, component, filearea,
     *    itemid, filepath, filename and optionally other attributes of the new file
     * @return bool success
     */
    public function copy_to_storage($filerecord) {
        return false;
    }

    /**
     * Copy content of this file to local storage, overriding current file if needed.
     *
     * @todo MDL-31068 implement move() rename() unzip() zip()
     * @param string $pathname real local full file name
     * @return boolean success
     */
    public function copy_to_pathname($pathname) {
        return false;
    }


//TODO: following methods are not implemented yet ;-)
    //public abstract function move(location params);
    //public abstract function rename(new name);
    //public abstract function unzip(location params);
    //public abstract function zip(zip file, file info);
}
