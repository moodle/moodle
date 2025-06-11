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
 * Core file storage class definition.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_files\hook\before_file_created;

require_once("$CFG->libdir/filestorage/stored_file.php");

/**
 * File storage class used for low level access to stored files.
 *
 * Only owner of file area may use this class to access own files,
 * for example only code in mod/assignment/* may access assignment
 * attachments. When some other part of moodle needs to access
 * files of modules it has to use file_browser class instead or there
 * has to be some callback API.
 *
 * @package   core_files
 * @category  files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class file_storage {

    /** @var string tempdir */
    private $tempdir;

    /** @var file_system filesystem */
    private $filesystem;

    /**
     * Constructor - do not use directly use {@link get_file_storage()} call instead.
     */
    public function __construct() {
        // The tempdir must always remain on disk, but shared between all ndoes in a cluster. Its content is not subject
        // to the file_system abstraction.
        $this->tempdir = make_temp_directory('filestorage');

        $this->setup_file_system();
    }

    /**
     * Complete setup procedure for the file_system component.
     *
     * @return file_system
     */
    public function setup_file_system() {
        global $CFG;
        if ($this->filesystem === null) {
            require_once($CFG->libdir . '/filestorage/file_system.php');
            if (!empty($CFG->alternative_file_system_class)) {
                $class = $CFG->alternative_file_system_class;
            } else {
                // The default file_system is the filedir.
                require_once($CFG->libdir . '/filestorage/file_system_filedir.php');
                $class = file_system_filedir::class;
            }
            $this->filesystem = new $class();
        }

        return $this->filesystem;
    }

    /**
     * Return the file system instance.
     *
     * @return file_system
     */
    public function get_file_system() {
        return $this->filesystem;
    }

    /**
     * Calculates sha1 hash of unique full path name information.
     *
     * This hash is a unique file identifier - it is used to improve
     * performance and overcome db index size limits.
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return string sha1 hash
     */
    public static function get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        if (substr($filepath, 0, 1) != '/') {
            $filepath = '/' . $filepath;
        }
        if (substr($filepath, - 1) != '/') {
            $filepath .= '/';
        }
        return sha1("/$contextid/$component/$filearea/$itemid".$filepath.$filename);
    }

    /**
     * Does this file exist?
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return bool
     */
    public function file_exists($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        $filepath = clean_param($filepath, PARAM_PATH);
        $filename = clean_param($filename, PARAM_FILE);

        if ($filename === '') {
            $filename = '.';
        }

        $pathnamehash = $this->get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, $filename);
        return $this->file_exists_by_hash($pathnamehash);
    }

    /**
     * Whether or not the file exist
     *
     * @param string $pathnamehash path name hash
     * @return bool
     */
    public function file_exists_by_hash($pathnamehash) {
        global $DB;

        return $DB->record_exists('files', array('pathnamehash'=>$pathnamehash));
    }

    /**
     * Create instance of file class from database record.
     *
     * @param stdClass $filerecord record from the files table left join files_reference table
     * @return stored_file instance of file abstraction class
     */
    public function get_file_instance(stdClass $filerecord) {
        $storedfile = new stored_file($this, $filerecord);
        return $storedfile;
    }

    /**
     * Get converted document.
     *
     * Get an alternate version of the specified document, if it is possible to convert.
     *
     * @param stored_file $file the file we want to preview
     * @param string $format The desired format - e.g. 'pdf'. Formats are specified by file extension.
     * @param boolean $forcerefresh If true, the file will be converted every time (not cached).
     * @return stored_file|bool false if unable to create the conversion, stored file otherwise
     */
    public function get_converted_document(stored_file $file, $format, $forcerefresh = false) {
        debugging('The get_converted_document function has been deprecated and the unoconv functions been removed. '
                . 'The file has not been converted. '
                . 'Please update your code to use the file conversion API instead.', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * Verify the format is supported.
     *
     * @param string $format The desired format - e.g. 'pdf'. Formats are specified by file extension.
     * @return bool - True if the format is supported for input.
     */
    protected function is_format_supported_by_unoconv($format) {
        debugging('The is_format_supported_by_unoconv function has been deprecated and the unoconv functions been removed. '
                . 'Please update your code to use the file conversion API instead.', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * Check if the installed version of unoconv is supported.
     *
     * @return bool true if the present version is supported, false otherwise.
     */
    public static function can_convert_documents() {
        debugging('The can_convert_documents function has been deprecated and the unoconv functions been removed. '
                . 'Please update your code to use the file conversion API instead.', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * Regenerate the test pdf and send it direct to the browser.
     */
    public static function send_test_pdf() {
        debugging('The send_test_pdf function has been deprecated and the unoconv functions been removed. '
                . 'Please update your code to use the file conversion API instead.', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * Check if unoconv configured path is correct and working.
     *
     * @return \stdClass an object with the test status and the UNOCONVPATH_ constant message.
     */
    public static function test_unoconv_path() {
        debugging('The test_unoconv_path function has been deprecated and the unoconv functions been removed. '
                . 'Please update your code to use the file conversion API instead.', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * Returns an image file that represent the given stored file as a preview
     *
     * At the moment, only GIF, JPEG, PNG and SVG files are supported to have previews. In the
     * future, the support for other mimetypes can be added, too (eg. generate an image
     * preview of PDF, text documents etc).
     *
     * @param stored_file $file the file we want to preview
     * @param string $mode preview mode, eg. 'thumb'
     * @return stored_file|bool false if unable to create the preview, stored file otherwise
     */
    public function get_file_preview(stored_file $file, $mode) {

        $context = context_system::instance();
        $path = '/' . trim($mode, '/') . '/';
        $preview = $this->get_file($context->id, 'core', 'preview', 0, $path, $file->get_contenthash());

        if (!$preview) {
            $preview = $this->create_file_preview($file, $mode);
            if (!$preview) {
                return false;
            }
        }

        return $preview;
    }

    /**
     * Return an available file name.
     *
     * This will return the next available file name in the area, adding/incrementing a suffix
     * of the file, ie: file.txt > file (1).txt > file (2).txt > etc...
     *
     * If the file name passed is available without modification, it is returned as is.
     *
     * @param int $contextid context ID.
     * @param string $component component.
     * @param string $filearea file area.
     * @param int $itemid area item ID.
     * @param string $filepath the file path.
     * @param string $filename the file name.
     * @return string available file name.
     * @throws coding_exception if the file name is invalid.
     * @since Moodle 2.5
     */
    public function get_unused_filename($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        global $DB;

        // Do not accept '.' or an empty file name (zero is acceptable).
        if ($filename == '.' || (empty($filename) && !is_numeric($filename))) {
            throw new coding_exception('Invalid file name passed', $filename);
        }

        // The file does not exist, we return the same file name.
        if (!$this->file_exists($contextid, $component, $filearea, $itemid, $filepath, $filename)) {
            return $filename;
        }

        // Trying to locate a file name using the used pattern. We remove the used pattern from the file name first.
        $pathinfo = pathinfo($filename);
        $basename = $pathinfo['filename'];
        $matches = array();
        if (preg_match('~^(.+) \(([0-9]+)\)$~', $basename, $matches)) {
            $basename = $matches[1];
        }

        $filenamelike = $DB->sql_like_escape($basename) . ' (%)';
        if (isset($pathinfo['extension'])) {
            $filenamelike .= '.' . $DB->sql_like_escape($pathinfo['extension']);
        }

        $filenamelikesql = $DB->sql_like('f.filename', ':filenamelike');
        $filenamelen = $DB->sql_length('f.filename');
        $sql = "SELECT filename
                FROM {files} f
                WHERE
                    f.contextid = :contextid AND
                    f.component = :component AND
                    f.filearea = :filearea AND
                    f.itemid = :itemid AND
                    f.filepath = :filepath AND
                    $filenamelikesql
                ORDER BY
                    $filenamelen DESC,
                    f.filename DESC";
        $params = array('contextid' => $contextid, 'component' => $component, 'filearea' => $filearea, 'itemid' => $itemid,
                'filepath' => $filepath, 'filenamelike' => $filenamelike);
        $results = $DB->get_fieldset_sql($sql, $params, IGNORE_MULTIPLE);

        // Loop over the results to make sure we are working on a valid file name. Because 'file (1).txt' and 'file (copy).txt'
        // would both be returned, but only the one only containing digits should be used.
        $number = 1;
        foreach ($results as $result) {
            $resultbasename = pathinfo($result, PATHINFO_FILENAME);
            $matches = array();
            if (preg_match('~^(.+) \(([0-9]+)\)$~', $resultbasename, $matches)) {
                $number = $matches[2] + 1;
                break;
            }
        }

        // Constructing the new filename.
        $newfilename = $basename . ' (' . $number . ')';
        if (isset($pathinfo['extension'])) {
            $newfilename .= '.' . $pathinfo['extension'];
        }

        return $newfilename;
    }

    /**
     * Return an available directory name.
     *
     * This will return the next available directory name in the area, adding/incrementing a suffix
     * of the last portion of path, ie: /path/ > /path (1)/ > /path (2)/ > etc...
     *
     * If the file path passed is available without modification, it is returned as is.
     *
     * @param int $contextid context ID.
     * @param string $component component.
     * @param string $filearea file area.
     * @param int $itemid area item ID.
     * @param string $suggestedpath the suggested file path.
     * @return string available file path
     * @since Moodle 2.5
     */
    public function get_unused_dirname($contextid, $component, $filearea, $itemid, $suggestedpath) {
        global $DB;

        // Ensure suggestedpath has trailing '/'
        $suggestedpath = rtrim($suggestedpath, '/'). '/';

        // The directory does not exist, we return the same file path.
        if (!$this->file_exists($contextid, $component, $filearea, $itemid, $suggestedpath, '.')) {
            return $suggestedpath;
        }

        // Trying to locate a file path using the used pattern. We remove the used pattern from the path first.
        if (preg_match('~^(/.+) \(([0-9]+)\)/$~', $suggestedpath, $matches)) {
            $suggestedpath = $matches[1]. '/';
        }

        $filepathlike = $DB->sql_like_escape(rtrim($suggestedpath, '/')) . ' (%)/';

        $filepathlikesql = $DB->sql_like('f.filepath', ':filepathlike');
        $filepathlen = $DB->sql_length('f.filepath');
        $sql = "SELECT filepath
                FROM {files} f
                WHERE
                    f.contextid = :contextid AND
                    f.component = :component AND
                    f.filearea = :filearea AND
                    f.itemid = :itemid AND
                    f.filename = :filename AND
                    $filepathlikesql
                ORDER BY
                    $filepathlen DESC,
                    f.filepath DESC";
        $params = array('contextid' => $contextid, 'component' => $component, 'filearea' => $filearea, 'itemid' => $itemid,
                'filename' => '.', 'filepathlike' => $filepathlike);
        $results = $DB->get_fieldset_sql($sql, $params, IGNORE_MULTIPLE);

        // Loop over the results to make sure we are working on a valid file path. Because '/path (1)/' and '/path (copy)/'
        // would both be returned, but only the one only containing digits should be used.
        $number = 1;
        foreach ($results as $result) {
            if (preg_match('~ \(([0-9]+)\)/$~', $result, $matches)) {
                $number = (int)($matches[1]) + 1;
                break;
            }
        }

        return rtrim($suggestedpath, '/'). ' (' . $number . ')/';
    }

    /**
     * Generates a preview image for the stored file
     *
     * @param stored_file $file the file we want to preview
     * @param string $mode preview mode, eg. 'thumb'
     * @return stored_file|bool the newly created preview file or false
     */
    protected function create_file_preview(stored_file $file, $mode) {

        $mimetype = $file->get_mimetype();

        if ($mimetype === 'image/gif' or $mimetype === 'image/jpeg' or $mimetype === 'image/png') {
            // make a preview of the image
            $data = $this->create_imagefile_preview($file, $mode);
        } else if ($mimetype === 'image/svg+xml') {
            // If we have an SVG image, then return the original (scalable) file.
            return $file;
        } else {
            // unable to create the preview of this mimetype yet
            return false;
        }

        if (empty($data)) {
            return false;
        }

        $context = context_system::instance();
        $record = array(
            'contextid' => $context->id,
            'component' => 'core',
            'filearea'  => 'preview',
            'itemid'    => 0,
            'filepath'  => '/' . trim($mode, '/') . '/',
            'filename'  => $file->get_contenthash(),
        );

        $imageinfo = getimagesizefromstring($data);
        if ($imageinfo) {
            $record['mimetype'] = $imageinfo['mime'];
        }

        return $this->create_file_from_string($record, $data);
    }

    /**
     * Generates a preview for the stored image file
     *
     * @param stored_file $file the image we want to preview
     * @param string $mode preview mode, eg. 'thumb'
     * @return string|bool false if a problem occurs, the thumbnail image data otherwise
     */
    protected function create_imagefile_preview(stored_file $file, $mode) {
        global $CFG;
        require_once($CFG->libdir.'/gdlib.php');

        if ($mode === 'tinyicon') {
            $data = $file->generate_image_thumbnail(24, 24);

        } else if ($mode === 'thumb') {
            $data = $file->generate_image_thumbnail(90, 90);

        } else if ($mode === 'bigthumb') {
            $data = $file->generate_image_thumbnail(250, 250);

        } else {
            throw new file_exception('storedfileproblem', 'Invalid preview mode requested');
        }

        return $data;
    }

    /**
     * Fetch file using local file id.
     *
     * Please do not rely on file ids, it is usually easier to use
     * pathname hashes instead.
     *
     * @param int $fileid file ID
     * @return stored_file|bool stored_file instance if exists, false if not
     */
    public function get_file_by_id($fileid) {
        global $DB;

        $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                  FROM {files} f
             LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE f.id = ?";
        if ($filerecord = $DB->get_record_sql($sql, array($fileid))) {
            return $this->get_file_instance($filerecord);
        } else {
            return false;
        }
    }

    /**
     * Fetch file using local file full pathname hash
     *
     * @param string $pathnamehash path name hash
     * @return stored_file|bool stored_file instance if exists, false if not
     */
    public function get_file_by_hash($pathnamehash) {
        global $DB;

        $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                  FROM {files} f
             LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE f.pathnamehash = ?";
        if ($filerecord = $DB->get_record_sql($sql, array($pathnamehash))) {
            return $this->get_file_instance($filerecord);
        } else {
            return false;
        }
    }

    /**
     * Fetch locally stored file.
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return stored_file|bool stored_file instance if exists, false if not
     */
    public function get_file($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        $filepath = clean_param($filepath, PARAM_PATH);
        $filename = clean_param($filename, PARAM_FILE);

        if ($filename === '') {
            $filename = '.';
        }

        $pathnamehash = $this->get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, $filename);
        return $this->get_file_by_hash($pathnamehash);
    }

    /**
     * Are there any files (or directories)
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param bool|int $itemid item id or false if all items
     * @param bool $ignoredirs whether or not ignore directories
     * @return bool empty
     */
    public function is_area_empty($contextid, $component, $filearea, $itemid = false, $ignoredirs = true) {
        global $DB;

        $params = array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea);
        $where = "contextid = :contextid AND component = :component AND filearea = :filearea";

        if ($itemid !== false) {
            $params['itemid'] = $itemid;
            $where .= " AND itemid = :itemid";
        }

        if ($ignoredirs) {
            $sql = "SELECT 'x'
                      FROM {files}
                     WHERE $where AND filename <> '.'";
        } else {
            $sql = "SELECT 'x'
                      FROM {files}
                     WHERE $where AND (filename <> '.' OR filepath <> '/')";
        }

        return !$DB->record_exists_sql($sql, $params);
    }

    /**
     * Returns all files belonging to given repository
     *
     * @param int $repositoryid
     * @param string $sort A fragment of SQL to use for sorting
     */
    public function get_external_files($repositoryid, $sort = '') {
        global $DB;
        $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                  FROM {files} f
             LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE r.repositoryid = ?";
        if (!empty($sort)) {
            $sql .= " ORDER BY {$sort}";
        }

        $result = array();
        $filerecords = $DB->get_records_sql($sql, array($repositoryid));
        foreach ($filerecords as $filerecord) {
            $result[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
        }
        return $result;
    }

    /**
     * Returns all area files (optionally limited by itemid)
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param mixed $filearea file area/s, you cannot specify multiple fileareas as well as an itemid
     * @param int|int[]|false $itemid item ID(s) or all files if not specified
     * @param string $sort A fragment of SQL to use for sorting
     * @param bool $includedirs whether or not include directories
     * @param int $updatedsince return files updated since this time
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @return stored_file[] array of stored_files indexed by pathanmehash
     */
    public function get_area_files($contextid, $component, $filearea, $itemid = false, $sort = "itemid, filepath, filename",
            $includedirs = true, $updatedsince = 0, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        list($areasql, $conditions) = $DB->get_in_or_equal($filearea, SQL_PARAMS_NAMED);
        $conditions['contextid'] = $contextid;
        $conditions['component'] = $component;

        if ($itemid !== false && is_array($filearea)) {
            throw new coding_exception('You cannot specify multiple fileareas as well as an itemid.');
        } else if ($itemid !== false) {
            $itemids = is_array($itemid) ? $itemid : [$itemid];
            list($itemidinorequalsql, $itemidconditions) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
            $itemidsql = " AND f.itemid {$itemidinorequalsql}";
            $conditions = array_merge($conditions, $itemidconditions);
        } else {
            $itemidsql = '';
        }

        $updatedsincesql = '';
        if (!empty($updatedsince)) {
            $conditions['time'] = $updatedsince;
            $updatedsincesql = 'AND f.timemodified > :time';
        }

        $includedirssql = '';
        if (!$includedirs) {
            $includedirssql = 'AND f.filename != :dot';
            $conditions['dot'] = '.';
        }

        if ($limitfrom && !$limitnum) {
            throw new coding_exception('If specifying $limitfrom you must also specify $limitnum');
        }

        $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                  FROM {files} f
             LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE f.contextid = :contextid
                       AND f.component = :component
                       AND f.filearea $areasql
                       $includedirssql
                       $updatedsincesql
                       $itemidsql";
        if (!empty($sort)) {
            $sql .= " ORDER BY {$sort}";
        }

        $result = array();
        $filerecords = $DB->get_records_sql($sql, $conditions, $limitfrom, $limitnum);
        foreach ($filerecords as $filerecord) {
            $result[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
        }
        return $result;
    }

    /**
     * Returns the file area item ids and their updatetime for a user's draft uploads, sorted by updatetime DESC.
     *
     * @param int $userid user id
     * @param int $updatedsince only return draft areas updated since this time
     * @param int $lastnum only return the last specified numbers
     * @return array
     */
    public function get_user_draft_items(int $userid, int $updatedsince = 0, int $lastnum = 0): array {
        global $DB;

        $params = [
            'component' => 'user',
            'filearea' => 'draft',
            'contextid' => context_user::instance($userid)->id,
        ];

        $updatedsincesql = '';
        if ($updatedsince) {
            $updatedsincesql = 'AND f.timemodified > :time';
            $params['time'] = $updatedsince;
        }
        $sql = "SELECT itemid,
                       MAX(f.timemodified) AS timemodified
                  FROM {files} f
                 WHERE component = :component
                       AND filearea = :filearea
                       AND contextid = :contextid
                       $updatedsincesql
              GROUP BY itemid
              ORDER BY MAX(f.timemodified) DESC";

        return $DB->get_records_sql($sql, $params, 0, $lastnum);
    }

    /**
     * Returns array based tree structure of area files
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @return array each dir represented by dirname, subdirs, files and dirfile array elements
     */
    public function get_area_tree($contextid, $component, $filearea, $itemid) {
        $result = array('dirname'=>'', 'dirfile'=>null, 'subdirs'=>array(), 'files'=>array());
        $files = $this->get_area_files($contextid, $component, $filearea, $itemid, '', true);
        // first create directory structure
        foreach ($files as $hash=>$dir) {
            if (!$dir->is_directory()) {
                continue;
            }
            unset($files[$hash]);
            if ($dir->get_filepath() === '/') {
                $result['dirfile'] = $dir;
                continue;
            }
            $parts = explode('/', trim($dir->get_filepath(),'/'));
            $pointer =& $result;
            foreach ($parts as $part) {
                if ($part === '') {
                    continue;
                }
                if (!isset($pointer['subdirs'][$part])) {
                    $pointer['subdirs'][$part] = array('dirname'=>$part, 'dirfile'=>null, 'subdirs'=>array(), 'files'=>array());
                }
                $pointer =& $pointer['subdirs'][$part];
            }
            $pointer['dirfile'] = $dir;
            unset($pointer);
        }
        foreach ($files as $hash=>$file) {
            $parts = explode('/', trim($file->get_filepath(),'/'));
            $pointer =& $result;
            foreach ($parts as $part) {
                if ($part === '') {
                    continue;
                }
                $pointer =& $pointer['subdirs'][$part];
            }
            $pointer['files'][$file->get_filename()] = $file;
            unset($pointer);
        }
        $result = $this->sort_area_tree($result);
        return $result;
    }

    /**
     * Sorts the result of {@link file_storage::get_area_tree()}.
     *
     * @param array $tree Array of results provided by {@link file_storage::get_area_tree()}
     * @return array of sorted results
     */
    protected function sort_area_tree($tree) {
        foreach ($tree as $key => &$value) {
            if ($key == 'subdirs') {
                core_collator::ksort($value, core_collator::SORT_NATURAL);
                foreach ($value as $subdirname => &$subtree) {
                    $subtree = $this->sort_area_tree($subtree);
                }
            } else if ($key == 'files') {
                core_collator::ksort($value, core_collator::SORT_NATURAL);
            }
        }
        return $tree;
    }

    /**
     * Returns all files and optionally directories
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param int $filepath directory path
     * @param bool $recursive include all subdirectories
     * @param bool $includedirs include files and directories
     * @param string $sort A fragment of SQL to use for sorting
     * @return array of stored_files indexed by pathanmehash
     */
    public function get_directory_files($contextid, $component, $filearea, $itemid, $filepath, $recursive = false, $includedirs = true, $sort = "filepath, filename") {
        global $DB;

        if (!$directory = $this->get_file($contextid, $component, $filearea, $itemid, $filepath, '.')) {
            return array();
        }

        $orderby = (!empty($sort)) ? " ORDER BY {$sort}" : '';

        if ($recursive) {

            $dirs = $includedirs ? "" : "AND filename <> '.'";
            $length = core_text::strlen($filepath);

            $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                      FROM {files} f
                 LEFT JOIN {files_reference} r
                           ON f.referencefileid = r.id
                     WHERE f.contextid = :contextid AND f.component = :component AND f.filearea = :filearea AND f.itemid = :itemid
                           AND ".$DB->sql_substr("f.filepath", 1, $length)." = :filepath
                           AND f.id <> :dirid
                           $dirs
                           $orderby";
            $params = array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>$filepath, 'dirid'=>$directory->get_id());

            $files = array();
            $dirs  = array();
            $filerecords = $DB->get_records_sql($sql, $params);
            foreach ($filerecords as $filerecord) {
                if ($filerecord->filename == '.') {
                    $dirs[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
                } else {
                    $files[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
                }
            }
            $result = array_merge($dirs, $files);

        } else {
            $result = array();
            $params = array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>$filepath, 'dirid'=>$directory->get_id());

            $length = core_text::strlen($filepath);

            if ($includedirs) {
                $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                          FROM {files} f
                     LEFT JOIN {files_reference} r
                               ON f.referencefileid = r.id
                         WHERE f.contextid = :contextid AND f.component = :component AND f.filearea = :filearea
                               AND f.itemid = :itemid AND f.filename = '.'
                               AND ".$DB->sql_substr("f.filepath", 1, $length)." = :filepath
                               AND f.id <> :dirid
                               $orderby";
                $reqlevel = substr_count($filepath, '/') + 1;
                $filerecords = $DB->get_records_sql($sql, $params);
                foreach ($filerecords as $filerecord) {
                    if (substr_count($filerecord->filepath, '/') !== $reqlevel) {
                        continue;
                    }
                    $result[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
                }
            }

            $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                      FROM {files} f
                 LEFT JOIN {files_reference} r
                           ON f.referencefileid = r.id
                     WHERE f.contextid = :contextid AND f.component = :component AND f.filearea = :filearea AND f.itemid = :itemid
                           AND f.filepath = :filepath AND f.filename <> '.'
                           $orderby";

            $filerecords = $DB->get_records_sql($sql, $params);
            foreach ($filerecords as $filerecord) {
                $result[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
            }
        }

        return $result;
    }

    /**
     * Delete all area files (optionally limited by itemid).
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area or all areas in context if not specified
     * @param int $itemid item ID or all files if not specified
     * @return bool success
     */
    public function delete_area_files($contextid, $component = false, $filearea = false, $itemid = false) {
        global $DB;

        $conditions = array('contextid'=>$contextid);
        if ($component !== false) {
            $conditions['component'] = $component;
        }
        if ($filearea !== false) {
            $conditions['filearea'] = $filearea;
        }
        if ($itemid !== false) {
            $conditions['itemid'] = $itemid;
        }

        $filerecords = $DB->get_records('files', $conditions);
        foreach ($filerecords as $filerecord) {
            $this->get_file_instance($filerecord)->delete();
        }

        return true; // BC only
    }

    /**
     * Delete all the files from certain areas where itemid is limited by an
     * arbitrary bit of SQL.
     *
     * @param int $contextid the id of the context the files belong to. Must be given.
     * @param string $component the owning component. Must be given.
     * @param string $filearea the file area name. Must be given.
     * @param string $itemidstest an SQL fragment that the itemid must match. Used
     *      in the query like WHERE itemid $itemidstest. Must used named parameters,
     *      and may not used named parameters called contextid, component or filearea.
     * @param array $params any query params used by $itemidstest.
     */
    public function delete_area_files_select($contextid, $component,
            $filearea, $itemidstest, ?array $params = null) {
        global $DB;

        $where = "contextid = :contextid
                AND component = :component
                AND filearea = :filearea
                AND itemid $itemidstest";
        $params['contextid'] = $contextid;
        $params['component'] = $component;
        $params['filearea'] = $filearea;

        $filerecords = $DB->get_recordset_select('files', $where, $params);
        foreach ($filerecords as $filerecord) {
            $this->get_file_instance($filerecord)->delete();
        }
        $filerecords->close();
    }

    /**
     * Delete all files associated with the given component.
     *
     * @param string $component the component owning the file
     */
    public function delete_component_files($component) {
        global $DB;

        $filerecords = $DB->get_recordset('files', array('component' => $component));
        foreach ($filerecords as $filerecord) {
            $this->get_file_instance($filerecord)->delete();
        }
        $filerecords->close();
    }

    /**
     * Move all the files in a file area from one context to another.
     *
     * @param int $oldcontextid the context the files are being moved from.
     * @param int $newcontextid the context the files are being moved to.
     * @param string $component the plugin that these files belong to.
     * @param string $filearea the name of the file area.
     * @param int $itemid file item ID
     * @return int the number of files moved, for information.
     */
    public function move_area_files_to_new_context($oldcontextid, $newcontextid, $component, $filearea, $itemid = false) {
        // Note, this code is based on some code that Petr wrote in
        // forum_move_attachments in mod/forum/lib.php. I moved it here because
        // I needed it in the question code too.
        $count = 0;

        $oldfiles = $this->get_area_files($oldcontextid, $component, $filearea, $itemid, 'id', false);
        foreach ($oldfiles as $oldfile) {
            $filerecord = new stdClass();
            $filerecord->contextid = $newcontextid;
            $this->create_file_from_storedfile($filerecord, $oldfile);
            $count += 1;
        }

        if ($count) {
            $this->delete_area_files($oldcontextid, $component, $filearea, $itemid);
        }

        return $count;
    }

    /**
     * Recursively creates directory.
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param int $userid the user ID
     * @return stored_file|false success
     */
    public function create_directory($contextid, $component, $filearea, $itemid, $filepath, $userid = null) {
        global $DB;

        // validate all parameters, we do not want any rubbish stored in database, right?
        if (!is_number($contextid) or $contextid < 1) {
            throw new file_exception('storedfileproblem', 'Invalid contextid');
        }

        $component = clean_param($component, PARAM_COMPONENT);
        if (empty($component)) {
            throw new file_exception('storedfileproblem', 'Invalid component');
        }

        $filearea = clean_param($filearea, PARAM_AREA);
        if (empty($filearea)) {
            throw new file_exception('storedfileproblem', 'Invalid filearea');
        }

        if (!is_number($itemid) or $itemid < 0) {
            throw new file_exception('storedfileproblem', 'Invalid itemid');
        }

        $filepath = clean_param($filepath, PARAM_PATH);
        if (strpos($filepath, '/') !== 0 or strrpos($filepath, '/') !== strlen($filepath)-1) {
            // path must start and end with '/'
            throw new file_exception('storedfileproblem', 'Invalid file path');
        }

        $pathnamehash = $this->get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, '.');

        if ($dir_info = $this->get_file_by_hash($pathnamehash)) {
            return $dir_info;
        }

        static $contenthash = null;
        if (!$contenthash) {
            $this->add_string_to_pool('');
            $contenthash = self::hash_from_string('');
        }

        $now = time();

        $dir_record = new stdClass();
        $dir_record->contextid = $contextid;
        $dir_record->component = $component;
        $dir_record->filearea  = $filearea;
        $dir_record->itemid    = $itemid;
        $dir_record->filepath  = $filepath;
        $dir_record->filename  = '.';
        $dir_record->contenthash  = $contenthash;
        $dir_record->filesize  = 0;

        $dir_record->timecreated  = $now;
        $dir_record->timemodified = $now;
        $dir_record->mimetype     = null;
        $dir_record->userid       = $userid;

        $dir_record->pathnamehash = $pathnamehash;

        $DB->insert_record('files', $dir_record);
        $dir_info = $this->get_file_by_hash($pathnamehash);

        if ($filepath !== '/') {
            //recurse to parent dirs
            $filepath = trim($filepath, '/');
            $filepath = explode('/', $filepath);
            array_pop($filepath);
            $filepath = implode('/', $filepath);
            $filepath = ($filepath === '') ? '/' : "/$filepath/";
            $this->create_directory($contextid, $component, $filearea, $itemid, $filepath, $userid);
        }

        return $dir_info;
    }

    /**
     * Add new file record to database and handle callbacks.
     *
     * @param stdClass $newrecord
     */
    protected function create_file($newrecord) {
        global $DB;
        $newrecord->id = $DB->insert_record('files', $newrecord);

        if ($newrecord->filename !== '.') {
            if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
                return;
            }

            // The $fileinstance is needed for the legacy callback.
            $fileinstance = $this->get_file_instance($newrecord);
            // Dispatch the new Hook implementation immediately after the legacy callback.
            $hook = new \core_files\hook\after_file_created($fileinstance, $newrecord);
            $hook->process_legacy_callbacks();
            \core\di::get(\core\hook\manager::class)->dispatch($hook);
        }
    }

    /**
     * Add new local file based on existing local file.
     *
     * @param stdClass|array $filerecord object or array describing changes
     * @param stored_file|int $fileorid id or stored_file instance of the existing local file
     * @return stored_file instance of newly created file
     */
    public function create_file_from_storedfile($filerecord, $fileorid) {
        global $DB;

        if ($fileorid instanceof stored_file) {
            $fid = $fileorid->get_id();
        } else {
            $fid = $fileorid;
        }

        $filerecord = (array)$filerecord; // We support arrays too, do not modify the submitted record!

        unset($filerecord['id']);
        unset($filerecord['filesize']);
        unset($filerecord['contenthash']);
        unset($filerecord['pathnamehash']);

        $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                  FROM {files} f
             LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE f.id = ?";

        if (!$newrecord = $DB->get_record_sql($sql, array($fid))) {
            throw new file_exception('storedfileproblem', 'File does not exist');
        }

        unset($newrecord->id);

        foreach ($filerecord as $key => $value) {
            // validate all parameters, we do not want any rubbish stored in database, right?
            if ($key == 'contextid' and (!is_number($value) or $value < 1)) {
                throw new file_exception('storedfileproblem', 'Invalid contextid');
            }

            if ($key == 'component') {
                $value = clean_param($value, PARAM_COMPONENT);
                if (empty($value)) {
                    throw new file_exception('storedfileproblem', 'Invalid component');
                }
            }

            if ($key == 'filearea') {
                $value = clean_param($value, PARAM_AREA);
                if (empty($value)) {
                    throw new file_exception('storedfileproblem', 'Invalid filearea');
                }
            }

            if ($key == 'itemid' and (!is_number($value) or $value < 0)) {
                throw new file_exception('storedfileproblem', 'Invalid itemid');
            }


            if ($key == 'filepath') {
                $value = clean_param($value, PARAM_PATH);
                if (strpos($value, '/') !== 0 or strrpos($value, '/') !== strlen($value)-1) {
                    // path must start and end with '/'
                    throw new file_exception('storedfileproblem', 'Invalid file path');
                }
            }

            if ($key == 'filename') {
                $value = clean_param($value, PARAM_FILE);
                if ($value === '') {
                    // path must start and end with '/'
                    throw new file_exception('storedfileproblem', 'Invalid file name');
                }
            }

            if ($key === 'timecreated' or $key === 'timemodified') {
                if (!is_number($value)) {
                    throw new file_exception('storedfileproblem', 'Invalid file '.$key);
                }
                if ($value < 0) {
                    //NOTE: unfortunately I make a mistake when creating the "files" table, we can not have negative numbers there, on the other hand no file should be older than 1970, right? (skodak)
                    $value = 0;
                }
            }

            if ($key == 'referencefileid' or $key == 'referencelastsync') {
                $value = clean_param($value, PARAM_INT);
            }

            $newrecord->$key = $value;
        }

        $newrecord->pathnamehash = $this->get_pathname_hash($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->filename);

        if ($newrecord->filename === '.') {
            // special case - only this function supports directories ;-)
            $directory = $this->create_directory($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->userid);
            // update the existing directory with the new data
            $newrecord->id = $directory->get_id();
            $DB->update_record('files', $newrecord);
            return $this->get_file_instance($newrecord);
        }

        // note: referencefileid is copied from the original file so that
        // creating a new file from an existing alias creates new alias implicitly.
        // here we just check the database consistency.
        if (!empty($newrecord->repositoryid)) {
            // It is OK if the current reference does not exist. It may have been altered by a repository plugin when the files
            // where saved from a draft area.
            $newrecord->referencefileid = $this->get_or_create_referencefileid($newrecord->repositoryid, $newrecord->reference);
        }

        try {
            $this->create_file($newrecord);
        } catch (dml_exception $e) {
            throw new stored_file_creation_exception($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid,
                                                     $newrecord->filepath, $newrecord->filename, $e->debuginfo);
        }


        $this->create_directory($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->userid);

        return $this->get_file_instance($newrecord);
    }

    /**
     * Add new local file.
     *
     * @param stdClass|array $filerecord object or array describing file
     * @param string $url the URL to the file
     * @param array $options {@link download_file_content()} options
     * @param bool $usetempfile use temporary file for download, may prevent out of memory problems
     * @return stored_file
     */
    public function create_file_from_url($filerecord, $url, ?array $options = null, $usetempfile = false) {

        $filerecord = (array)$filerecord;  // Do not modify the submitted record, this cast unlinks objects.
        $filerecord = (object)$filerecord; // We support arrays too.

        $headers        = isset($options['headers'])        ? $options['headers'] : null;
        $postdata       = isset($options['postdata'])       ? $options['postdata'] : null;
        $fullresponse   = isset($options['fullresponse'])   ? $options['fullresponse'] : false;
        $timeout        = isset($options['timeout'])        ? $options['timeout'] : 300;
        $connecttimeout = isset($options['connecttimeout']) ? $options['connecttimeout'] : 20;
        $skipcertverify = isset($options['skipcertverify']) ? $options['skipcertverify'] : false;
        $calctimeout    = isset($options['calctimeout'])    ? $options['calctimeout'] : false;

        if (!isset($filerecord->filename)) {
            $parts = explode('/', $url);
            $filename = array_pop($parts);
            $filerecord->filename = clean_param($filename, PARAM_FILE);
        }
        $source = !empty($filerecord->source) ? $filerecord->source : $url;
        $filerecord->source = clean_param($source, PARAM_URL);

        if ($usetempfile) {
            check_dir_exists($this->tempdir);
            $tmpfile = tempnam($this->tempdir, 'newfromurl');
            $content = download_file_content($url, $headers, $postdata, $fullresponse, $timeout, $connecttimeout, $skipcertverify, $tmpfile, $calctimeout);
            if ($content === false) {
                throw new file_exception('storedfileproblem', 'Cannot fetch file from URL');
            }
            try {
                $newfile = $this->create_file_from_pathname($filerecord, $tmpfile);
                @unlink($tmpfile);
                return $newfile;
            } catch (Exception $e) {
                @unlink($tmpfile);
                throw $e;
            }

        } else {
            $content = download_file_content($url, $headers, $postdata, $fullresponse, $timeout, $connecttimeout, $skipcertverify, NULL, $calctimeout);
            if ($content === false) {
                throw new file_exception('storedfileproblem', 'Cannot fetch file from URL');
            }
            return $this->create_file_from_string($filerecord, $content);
        }
    }

    /**
     * Add new local file.
     *
     * @param stdClass|array $filerecord object or array describing file
     * @param string $pathname path to file or content of file
     * @return stored_file
     */
    public function create_file_from_pathname($filerecord, $pathname) {
        global $DB;

        $filerecord = (array)$filerecord;  // Do not modify the submitted record, this cast unlinks objects.
        $filerecord = (object)$filerecord; // We support arrays too.

        // validate all parameters, we do not want any rubbish stored in database, right?
        if (!is_number($filerecord->contextid) or $filerecord->contextid < 1) {
            throw new file_exception('storedfileproblem', 'Invalid contextid');
        }

        $filerecord->component = clean_param($filerecord->component, PARAM_COMPONENT);
        if (empty($filerecord->component)) {
            throw new file_exception('storedfileproblem', 'Invalid component');
        }

        $filerecord->filearea = clean_param($filerecord->filearea, PARAM_AREA);
        if (empty($filerecord->filearea)) {
            throw new file_exception('storedfileproblem', 'Invalid filearea');
        }

        if (!is_number($filerecord->itemid) or $filerecord->itemid < 0) {
            throw new file_exception('storedfileproblem', 'Invalid itemid');
        }

        if (!empty($filerecord->sortorder)) {
            if (!is_number($filerecord->sortorder) or $filerecord->sortorder < 0) {
                $filerecord->sortorder = 0;
            }
        } else {
            $filerecord->sortorder = 0;
        }

        $filerecord->filepath = clean_param($filerecord->filepath, PARAM_PATH);
        if (strpos($filerecord->filepath, '/') !== 0 or strrpos($filerecord->filepath, '/') !== strlen($filerecord->filepath)-1) {
            // path must start and end with '/'
            throw new file_exception('storedfileproblem', 'Invalid file path');
        }

        $filerecord->filename = clean_param($filerecord->filename, PARAM_FILE);
        if ($filerecord->filename === '') {
            // filename must not be empty
            throw new file_exception('storedfileproblem', 'Invalid file name');
        }

        $now = time();
        if (isset($filerecord->timecreated)) {
            if (!is_number($filerecord->timecreated)) {
                throw new file_exception('storedfileproblem', 'Invalid file timecreated');
            }
            if ($filerecord->timecreated < 0) {
                //NOTE: unfortunately I make a mistake when creating the "files" table, we can not have negative numbers there, on the other hand no file should be older than 1970, right? (skodak)
                $filerecord->timecreated = 0;
            }
        } else {
            $filerecord->timecreated = $now;
        }

        if (isset($filerecord->timemodified)) {
            if (!is_number($filerecord->timemodified)) {
                throw new file_exception('storedfileproblem', 'Invalid file timemodified');
            }
            if ($filerecord->timemodified < 0) {
                //NOTE: unfortunately I make a mistake when creating the "files" table, we can not have negative numbers there, on the other hand no file should be older than 1970, right? (skodak)
                $filerecord->timemodified = 0;
            }
        } else {
            $filerecord->timemodified = $now;
        }

        $newrecord = new stdClass();

        $newrecord->contextid = $filerecord->contextid;
        $newrecord->component = $filerecord->component;
        $newrecord->filearea  = $filerecord->filearea;
        $newrecord->itemid    = $filerecord->itemid;
        $newrecord->filepath  = $filerecord->filepath;
        $newrecord->filename  = $filerecord->filename;

        $newrecord->timecreated  = $filerecord->timecreated;
        $newrecord->timemodified = $filerecord->timemodified;
        $newrecord->mimetype     = empty($filerecord->mimetype) ? $this->mimetype($pathname, $filerecord->filename) : $filerecord->mimetype;
        $newrecord->userid       = empty($filerecord->userid) ? null : $filerecord->userid;
        $newrecord->source       = empty($filerecord->source) ? null : $filerecord->source;
        $newrecord->author       = empty($filerecord->author) ? null : $filerecord->author;
        $newrecord->license      = empty($filerecord->license) ? null : $filerecord->license;
        $newrecord->status       = empty($filerecord->status) ? 0 : $filerecord->status;
        $newrecord->sortorder    = $filerecord->sortorder;

        list($newrecord->contenthash, $newrecord->filesize, $newfile) = $this->add_file_to_pool($pathname, null, $newrecord);

        $newrecord->pathnamehash = $this->get_pathname_hash($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->filename);

        try {
            $this->create_file($newrecord);
        } catch (dml_exception $e) {
            if ($newfile) {
                $this->filesystem->remove_file($newrecord->contenthash);
            }
            throw new stored_file_creation_exception($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid,
                                                    $newrecord->filepath, $newrecord->filename, $e->debuginfo);
        }

        $this->create_directory($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->userid);

        return $this->get_file_instance($newrecord);
    }

    /**
     * Add new local file.
     *
     * @param stdClass|array $filerecord object or array describing file
     * @param string $content content of file
     * @return stored_file
     */
    public function create_file_from_string($filerecord, $content) {
        global $DB;

        $filerecord = (array)$filerecord;  // Do not modify the submitted record, this cast unlinks objects.
        $filerecord = (object)$filerecord; // We support arrays too.

        // validate all parameters, we do not want any rubbish stored in database, right?
        if (!is_number($filerecord->contextid) or $filerecord->contextid < 1) {
            throw new file_exception('storedfileproblem', 'Invalid contextid');
        }

        $filerecord->component = clean_param($filerecord->component, PARAM_COMPONENT);
        if (empty($filerecord->component)) {
            throw new file_exception('storedfileproblem', 'Invalid component');
        }

        $filerecord->filearea = clean_param($filerecord->filearea, PARAM_AREA);
        if (empty($filerecord->filearea)) {
            throw new file_exception('storedfileproblem', 'Invalid filearea');
        }

        if (!is_number($filerecord->itemid) or $filerecord->itemid < 0) {
            throw new file_exception('storedfileproblem', 'Invalid itemid');
        }

        if (!empty($filerecord->sortorder)) {
            if (!is_number($filerecord->sortorder) or $filerecord->sortorder < 0) {
                $filerecord->sortorder = 0;
            }
        } else {
            $filerecord->sortorder = 0;
        }

        $filerecord->filepath = clean_param($filerecord->filepath, PARAM_PATH);
        if (strpos($filerecord->filepath, '/') !== 0 or strrpos($filerecord->filepath, '/') !== strlen($filerecord->filepath)-1) {
            // path must start and end with '/'
            throw new file_exception('storedfileproblem', 'Invalid file path');
        }

        $filerecord->filename = clean_param($filerecord->filename, PARAM_FILE);
        if ($filerecord->filename === '') {
            // path must start and end with '/'
            throw new file_exception('storedfileproblem', 'Invalid file name');
        }

        $now = time();
        if (isset($filerecord->timecreated)) {
            if (!is_number($filerecord->timecreated)) {
                throw new file_exception('storedfileproblem', 'Invalid file timecreated');
            }
            if ($filerecord->timecreated < 0) {
                //NOTE: unfortunately I make a mistake when creating the "files" table, we can not have negative numbers there, on the other hand no file should be older than 1970, right? (skodak)
                $filerecord->timecreated = 0;
            }
        } else {
            $filerecord->timecreated = $now;
        }

        if (isset($filerecord->timemodified)) {
            if (!is_number($filerecord->timemodified)) {
                throw new file_exception('storedfileproblem', 'Invalid file timemodified');
            }
            if ($filerecord->timemodified < 0) {
                //NOTE: unfortunately I make a mistake when creating the "files" table, we can not have negative numbers there, on the other hand no file should be older than 1970, right? (skodak)
                $filerecord->timemodified = 0;
            }
        } else {
            $filerecord->timemodified = $now;
        }

        $newrecord = new stdClass();

        $newrecord->contextid = $filerecord->contextid;
        $newrecord->component = $filerecord->component;
        $newrecord->filearea  = $filerecord->filearea;
        $newrecord->itemid    = $filerecord->itemid;
        $newrecord->filepath  = $filerecord->filepath;
        $newrecord->filename  = $filerecord->filename;

        $newrecord->timecreated  = $filerecord->timecreated;
        $newrecord->timemodified = $filerecord->timemodified;
        $newrecord->userid       = empty($filerecord->userid) ? null : $filerecord->userid;
        $newrecord->source       = empty($filerecord->source) ? null : $filerecord->source;
        $newrecord->author       = empty($filerecord->author) ? null : $filerecord->author;
        $newrecord->license      = empty($filerecord->license) ? null : $filerecord->license;
        $newrecord->status       = empty($filerecord->status) ? 0 : $filerecord->status;
        $newrecord->sortorder    = $filerecord->sortorder;

        list($newrecord->contenthash, $newrecord->filesize, $newfile) = $this->add_string_to_pool($content, $newrecord);
        if (empty($filerecord->mimetype)) {
            $newrecord->mimetype = $this->filesystem->mimetype_from_hash($newrecord->contenthash, $newrecord->filename);
        } else {
            $newrecord->mimetype = $filerecord->mimetype;
        }

        $newrecord->pathnamehash = $this->get_pathname_hash($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->filename);

        if (!empty($filerecord->repositoryid)) {
            $newrecord->referencefileid = $this->get_or_create_referencefileid($filerecord->repositoryid, $filerecord->reference);
        }

        try {
            $this->create_file($newrecord);
        } catch (dml_exception $e) {
            if ($newfile) {
                $this->filesystem->remove_file($newrecord->contenthash);
            }
            throw new stored_file_creation_exception($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid,
                                                    $newrecord->filepath, $newrecord->filename, $e->debuginfo);
        }

        $this->create_directory($newrecord->contextid, $newrecord->component, $newrecord->filearea, $newrecord->itemid, $newrecord->filepath, $newrecord->userid);

        return $this->get_file_instance($newrecord);
    }

    /**
     * Synchronise stored file from file.
     *
     * @param stored_file $file Stored file to synchronise.
     * @param string $path Path to the file to synchronise from.
     * @param stdClass $filerecord The file record from the database.
     */
    public function synchronise_stored_file_from_file(stored_file $file, $path, $filerecord) {
        list($contenthash, $filesize) = $this->add_file_to_pool($path, null, $filerecord);
        $file->set_synchronized($contenthash, $filesize);
    }

    /**
     * Synchronise stored file from string.
     *
     * @param stored_file $file Stored file to synchronise.
     * @param string $content File content.
     * @param stdClass $filerecord The file record from the database.
     */
    public function synchronise_stored_file_from_string(stored_file $file, $content, $filerecord) {
        list($contenthash, $filesize) = $this->add_string_to_pool($content, $filerecord);
        $file->set_synchronized($contenthash, $filesize);
    }

    /**
     * Create a new alias/shortcut file from file reference information
     *
     * @param stdClass|array $filerecord object or array describing the new file
     * @param int $repositoryid the id of the repository that provides the original file
     * @param string $reference the information required by the repository to locate the original file
     * @param array $options options for creating the new file
     * @return stored_file
     */
    public function create_file_from_reference($filerecord, $repositoryid, $reference, $options = array()) {
        global $DB;

        $filerecord = (array)$filerecord;  // Do not modify the submitted record, this cast unlinks objects.
        $filerecord = (object)$filerecord; // We support arrays too.

        // validate all parameters, we do not want any rubbish stored in database, right?
        if (!is_number($filerecord->contextid) or $filerecord->contextid < 1) {
            throw new file_exception('storedfileproblem', 'Invalid contextid');
        }

        $filerecord->component = clean_param($filerecord->component, PARAM_COMPONENT);
        if (empty($filerecord->component)) {
            throw new file_exception('storedfileproblem', 'Invalid component');
        }

        $filerecord->filearea = clean_param($filerecord->filearea, PARAM_AREA);
        if (empty($filerecord->filearea)) {
            throw new file_exception('storedfileproblem', 'Invalid filearea');
        }

        if (!is_number($filerecord->itemid) or $filerecord->itemid < 0) {
            throw new file_exception('storedfileproblem', 'Invalid itemid');
        }

        if (!empty($filerecord->sortorder)) {
            if (!is_number($filerecord->sortorder) or $filerecord->sortorder < 0) {
                $filerecord->sortorder = 0;
            }
        } else {
            $filerecord->sortorder = 0;
        }

        $filerecord->mimetype          = empty($filerecord->mimetype) ? $this->mimetype($filerecord->filename) : $filerecord->mimetype;
        $filerecord->userid            = empty($filerecord->userid) ? null : $filerecord->userid;
        $filerecord->source            = empty($filerecord->source) ? null : $filerecord->source;
        $filerecord->author            = empty($filerecord->author) ? null : $filerecord->author;
        $filerecord->license           = empty($filerecord->license) ? null : $filerecord->license;
        $filerecord->status            = empty($filerecord->status) ? 0 : $filerecord->status;
        $filerecord->filepath          = clean_param($filerecord->filepath, PARAM_PATH);
        if (strpos($filerecord->filepath, '/') !== 0 or strrpos($filerecord->filepath, '/') !== strlen($filerecord->filepath)-1) {
            // Path must start and end with '/'.
            throw new file_exception('storedfileproblem', 'Invalid file path');
        }

        $filerecord->filename = clean_param($filerecord->filename, PARAM_FILE);
        if ($filerecord->filename === '') {
            // Path must start and end with '/'.
            throw new file_exception('storedfileproblem', 'Invalid file name');
        }

        $now = time();
        if (isset($filerecord->timecreated)) {
            if (!is_number($filerecord->timecreated)) {
                throw new file_exception('storedfileproblem', 'Invalid file timecreated');
            }
            if ($filerecord->timecreated < 0) {
                // NOTE: unfortunately I make a mistake when creating the "files" table, we can not have negative numbers there, on the other hand no file should be older than 1970, right? (skodak)
                $filerecord->timecreated = 0;
            }
        } else {
            $filerecord->timecreated = $now;
        }

        if (isset($filerecord->timemodified)) {
            if (!is_number($filerecord->timemodified)) {
                throw new file_exception('storedfileproblem', 'Invalid file timemodified');
            }
            if ($filerecord->timemodified < 0) {
                // NOTE: unfortunately I make a mistake when creating the "files" table, we can not have negative numbers there, on the other hand no file should be older than 1970, right? (skodak)
                $filerecord->timemodified = 0;
            }
        } else {
            $filerecord->timemodified = $now;
        }

        $transaction = $DB->start_delegated_transaction();

        try {
            $filerecord->referencefileid = $this->get_or_create_referencefileid($repositoryid, $reference);
        } catch (Exception $e) {
            throw new file_reference_exception($repositoryid, $reference, null, null, $e->getMessage());
        }

        $existingfile = null;
        if (isset($filerecord->contenthash)) {
            $existingfile = $DB->get_record('files', array('contenthash' => $filerecord->contenthash), '*', IGNORE_MULTIPLE);
        }
        if (!empty($existingfile)) {
            // There is an existing file already available.
            if (empty($filerecord->filesize)) {
                $filerecord->filesize = $existingfile->filesize;
            } else {
                $filerecord->filesize = clean_param($filerecord->filesize, PARAM_INT);
            }
        } else {
            // Attempt to get the result of last synchronisation for this reference.
            $lastcontent = $DB->get_record('files', array('referencefileid' => $filerecord->referencefileid),
                    'id, contenthash, filesize', IGNORE_MULTIPLE);
            if ($lastcontent) {
                $filerecord->contenthash = $lastcontent->contenthash;
                $filerecord->filesize = $lastcontent->filesize;
            } else {
                // External file doesn't have content in moodle.
                // So we create an empty file for it.
                list($filerecord->contenthash, $filerecord->filesize, $newfile) = $this->add_string_to_pool(null, $filerecord);
            }
        }

        $filerecord->pathnamehash = $this->get_pathname_hash($filerecord->contextid, $filerecord->component, $filerecord->filearea, $filerecord->itemid, $filerecord->filepath, $filerecord->filename);

        try {
            $filerecord->id = $DB->insert_record('files', $filerecord);
        } catch (dml_exception $e) {
            if (!empty($newfile)) {
                $this->filesystem->remove_file($filerecord->contenthash);
            }
            throw new stored_file_creation_exception($filerecord->contextid, $filerecord->component, $filerecord->filearea, $filerecord->itemid,
                                                    $filerecord->filepath, $filerecord->filename, $e->debuginfo);
        }

        $this->create_directory($filerecord->contextid, $filerecord->component, $filerecord->filearea, $filerecord->itemid, $filerecord->filepath, $filerecord->userid);

        $transaction->allow_commit();

        // this will retrieve all reference information from DB as well
        return $this->get_file_by_id($filerecord->id);
    }

    /**
     * Creates new image file from existing.
     *
     * @param stdClass|array $filerecord object or array describing new file
     * @param int|stored_file $fid file id or stored file object
     * @param int $newwidth in pixels
     * @param int $newheight in pixels
     * @param bool $keepaspectratio whether or not keep aspect ratio
     * @param int $quality depending on image type 0-100 for jpeg, 0-9 (0 means no compression) for png
     * @return stored_file
     */
    public function convert_image($filerecord, $fid, $newwidth = null, $newheight = null, $keepaspectratio = true, $quality = null) {
        if (!function_exists('imagecreatefromstring')) {
            //Most likely the GD php extension isn't installed
            //image conversion cannot succeed
            throw new file_exception('storedfileproblem', 'imagecreatefromstring() doesnt exist. The PHP extension "GD" must be installed for image conversion.');
        }

        if ($fid instanceof stored_file) {
            $fid = $fid->get_id();
        }

        $filerecord = (array)$filerecord; // We support arrays too, do not modify the submitted record!

        if (!$file = $this->get_file_by_id($fid)) { // Make sure file really exists and we we correct data.
            throw new file_exception('storedfileproblem', 'File does not exist');
        }

        if (!$imageinfo = $file->get_imageinfo()) {
            throw new file_exception('storedfileproblem', 'File is not an image');
        }

        if (!isset($filerecord['filename'])) {
            $filerecord['filename'] = $file->get_filename();
        }

        if (!isset($filerecord['mimetype'])) {
            $filerecord['mimetype'] = $imageinfo['mimetype'];
        }

        $width    = $imageinfo['width'];
        $height   = $imageinfo['height'];

        if ($keepaspectratio) {
            if (0 >= $newwidth and 0 >= $newheight) {
                // no sizes specified
                $newwidth  = $width;
                $newheight = $height;

            } else if (0 < $newwidth and 0 < $newheight) {
                $xheight = ($newwidth*($height/$width));
                if ($xheight < $newheight) {
                    $newheight = (int)$xheight;
                } else {
                    $newwidth = (int)($newheight*($width/$height));
                }

            } else if (0 < $newwidth) {
                $newheight = (int)($newwidth*($height/$width));

            } else { //0 < $newheight
                $newwidth = (int)($newheight*($width/$height));
            }

        } else {
            if (0 >= $newwidth) {
                $newwidth = $width;
            }
            if (0 >= $newheight) {
                $newheight = $height;
            }
        }

        // The original image.
        $img = imagecreatefromstring($file->get_content());

        // A new true color image where we will copy our original image.
        $newimg = imagecreatetruecolor($newwidth, $newheight);

        // Determine if the file supports transparency.
        $hasalpha = $filerecord['mimetype'] == 'image/png' || $filerecord['mimetype'] == 'image/gif';

        // Maintain transparency.
        if ($hasalpha) {
            imagealphablending($newimg, true);

            // Get the current transparent index for the original image.
            $colour = imagecolortransparent($img);
            if ($colour == -1) {
                // Set a transparent colour index if there's none.
                $colour = imagecolorallocatealpha($newimg, 255, 255, 255, 127);
                // Save full alpha channel.
                imagesavealpha($newimg, true);
            }
            imagecolortransparent($newimg, $colour);
            imagefill($newimg, 0, 0, $colour);
        }

        // Process the image to be output.
        if ($height != $newheight or $width != $newwidth) {
            // Resample if the dimensions differ from the original.
            if (!imagecopyresampled($newimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height)) {
                // weird
                throw new file_exception('storedfileproblem', 'Can not resize image');
            }
            imagedestroy($img);
            $img = $newimg;

        } else if ($hasalpha) {
            // Just copy to the new image with the alpha channel.
            if (!imagecopy($newimg, $img, 0, 0, 0, 0, $width, $height)) {
                // Weird.
                throw new file_exception('storedfileproblem', 'Can not copy image');
            }
            imagedestroy($img);
            $img = $newimg;

        } else {
            // No particular processing needed for the original image.
            imagedestroy($newimg);
        }

        ob_start();
        switch ($filerecord['mimetype']) {
            case 'image/gif':
                imagegif($img);
                break;

            case 'image/jpeg':
                if (is_null($quality)) {
                    imagejpeg($img);
                } else {
                    imagejpeg($img, NULL, $quality);
                }
                break;

            case 'image/png':
                $quality = (int)$quality;

                // Woah nelly! Because PNG quality is in the range 0 - 9 compared to JPEG quality,
                // the latter of which can go to 100, we need to make sure that quality here is
                // in a safe range or PHP WILL CRASH AND DIE. You have been warned.
                $quality = $quality > 9 ? (int)(max(1.0, (float)$quality / 100.0) * 9.0) : $quality;
                imagepng($img, null, $quality, PNG_NO_FILTER);
                break;

            default:
                throw new file_exception('storedfileproblem', 'Unsupported mime type');
        }

        $content = ob_get_contents();
        ob_end_clean();
        imagedestroy($img);

        if (!$content) {
            throw new file_exception('storedfileproblem', 'Can not convert image');
        }

        return $this->create_file_from_string($filerecord, $content);
    }

    /**
     * Add file content to sha1 pool.
     *
     * @param string $pathname path to file
     * @param string|null $contenthash sha1 hash of content if known (performance only)
     * @param stdClass|null $newrecord New file record
     * @return array (contenthash, filesize, newfile)
     */
    public function add_file_to_pool($pathname, $contenthash = null, $newrecord = null) {
        $hook = new before_file_created(
            filerecord: $newrecord,
            filepath: $pathname,
        );

        $hook->process_legacy_callbacks();
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

        if ($hook->has_changed()) {
            $contenthash = null;
            $pathname = $hook->get_filepath();
        }

        return $this->filesystem->add_file_from_path($pathname, $contenthash);
    }

    /**
     * Add string content to sha1 pool.
     *
     * @param string $content file content - binary string
     * @return array (contenthash, filesize, newfile)
     */
    public function add_string_to_pool($content, $newrecord = null) {
        if ($content !== null) {
            // This is a directory and there is no record information.
            $hook = new before_file_created(
                filerecord: $newrecord,
                filecontent: $content,
            );

            $hook->process_legacy_callbacks();
            \core\di::get(\core\hook\manager::class)->dispatch($hook);

            if ($hook->has_changed()) {
                $content = $hook->get_filecontent();
            }
        }

        return $this->filesystem->add_file_from_string($content);
    }

    /**
     * Serve file content using X-Sendfile header.
     * Please make sure that all headers are already sent and the all
     * access control checks passed.
     *
     * This alternate method to xsendfile() allows an alternate file system
     * to use the full file metadata and avoid extra lookups.
     *
     * @param stored_file $file The file to send
     * @return bool success
     */
    public function xsendfile_file(stored_file $file): bool {
        return $this->filesystem->xsendfile_file($file);
    }

    /**
     * Serve file content using X-Sendfile header.
     * Please make sure that all headers are already sent
     * and the all access control checks passed.
     *
     * @param string $contenthash sah1 hash of the file content to be served
     * @return bool success
     */
    public function xsendfile($contenthash) {
        return $this->filesystem->xsendfile($contenthash);
    }

    /**
     * Returns true if filesystem is configured to support xsendfile.
     *
     * @return bool
     */
    public function supports_xsendfile() {
        return $this->filesystem->supports_xsendfile();
    }

    /**
     * Content exists
     *
     * @param string $contenthash
     * @return bool
     * @deprecated since 3.3
     */
    public function content_exists($contenthash) {
        debugging('The content_exists function has been deprecated and should no longer be used.', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * Tries to recover missing content of file from trash.
     *
     * @param stored_file $file stored_file instance
     * @return bool success
     * @deprecated since 3.3
     */
    public function try_content_recovery($file) {
        debugging('The try_content_recovery function has been deprecated and should no longer be used.', DEBUG_DEVELOPER);

        return false;
    }

    /**
     * When user referring to a moodle file, we build the reference field
     *
     * @param array|stdClass $params
     * @return string
     */
    public static function pack_reference($params) {
        $params = (array)$params;
        $reference = array();
        $reference['contextid'] = is_null($params['contextid']) ? null : clean_param($params['contextid'], PARAM_INT);
        $reference['component'] = is_null($params['component']) ? null : clean_param($params['component'], PARAM_COMPONENT);
        $reference['itemid']    = is_null($params['itemid'])    ? null : clean_param($params['itemid'],    PARAM_INT);
        $reference['filearea']  = is_null($params['filearea'])  ? null : clean_param($params['filearea'],  PARAM_AREA);
        $reference['filepath']  = is_null($params['filepath'])  ? null : clean_param($params['filepath'],  PARAM_PATH);
        $reference['filename']  = is_null($params['filename'])  ? null : clean_param($params['filename'],  PARAM_FILE);
        return base64_encode(serialize($reference));
    }

    /**
     * Unpack reference field
     *
     * @param string $str
     * @param bool $cleanparams if set to true, array elements will be passed through {@link clean_param()}
     * @throws file_reference_exception if the $str does not have the expected format
     * @return array
     */
    public static function unpack_reference($str, $cleanparams = false) {
        $decoded = base64_decode($str, true);
        if ($decoded === false) {
            throw new file_reference_exception(null, $str, null, null, 'Invalid base64 format');
        }
        $params = unserialize_array($decoded);
        if ($params === false) {
            throw new file_reference_exception(null, $decoded, null, null, 'Not an unserializeable value');
        }
        if (is_array($params) && $cleanparams) {
            $params = array(
                'component' => is_null($params['component']) ? ''   : clean_param($params['component'], PARAM_COMPONENT),
                'filearea'  => is_null($params['filearea'])  ? ''   : clean_param($params['filearea'], PARAM_AREA),
                'itemid'    => is_null($params['itemid'])    ? 0    : clean_param($params['itemid'], PARAM_INT),
                'filename'  => is_null($params['filename'])  ? null : clean_param($params['filename'], PARAM_FILE),
                'filepath'  => is_null($params['filepath'])  ? null : clean_param($params['filepath'], PARAM_PATH),
                'contextid' => is_null($params['contextid']) ? null : clean_param($params['contextid'], PARAM_INT)
            );
        }
        return $params;
    }

    /**
     * Search through the server files.
     *
     * The query parameter will be used in conjuction with the SQL directive
     * LIKE, so include '%' in it if you need to. This search will always ignore
     * user files and directories. Note that the search is case insensitive.
     *
     * This query can quickly become inefficient so use it sparignly.
     *
     * @param  string  $query The string used with SQL LIKE.
     * @param  integer $from  The offset to start the search at.
     * @param  integer $limit The maximum number of results.
     * @param  boolean $count When true this methods returns the number of results availabe,
     *                        disregarding the parameters $from and $limit.
     * @return int|array      Integer when count, otherwise array of stored_file objects.
     */
    public function search_server_files($query, $from = 0, $limit = 20, $count = false) {
        global $DB;
        $params = array(
            'contextlevel' => CONTEXT_USER,
            'directory' => '.',
            'query' => $query
        );

        if ($count) {
            $select = 'COUNT(1)';
        } else {
            $select = self::instance_sql_fields('f', 'r');
        }
        $like = $DB->sql_like('f.filename', ':query', false);

        $sql = "SELECT $select
                  FROM {files} f
             LEFT JOIN {files_reference} r
                    ON f.referencefileid = r.id
                  JOIN {context} c
                    ON f.contextid = c.id
                 WHERE c.contextlevel <> :contextlevel
                   AND f.filename <> :directory
                   AND " . $like . "";

        if ($count) {
            return $DB->count_records_sql($sql, $params);
        }

        $sql .= " ORDER BY f.filename";

        $result = array();
        $filerecords = $DB->get_recordset_sql($sql, $params, $from, $limit);
        foreach ($filerecords as $filerecord) {
            $result[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
        }
        $filerecords->close();

        return $result;
    }

    /**
     * Returns all aliases that refer to some stored_file via the given reference
     *
     * All repositories that provide access to a stored_file are expected to use
     * {@link self::pack_reference()}. This method can't be used if the given reference
     * does not use this format or if you are looking for references to an external file
     * (for example it can't be used to search for all aliases that refer to a given
     * Dropbox or Box.net file).
     *
     * Aliases in user draft areas are excluded from the returned list.
     *
     * @param string $reference identification of the referenced file
     * @return array of stored_file indexed by its pathnamehash
     */
    public function search_references($reference) {
        global $DB;

        if (is_null($reference)) {
            throw new coding_exception('NULL is not a valid reference to an external file');
        }

        // Give {@link self::unpack_reference()} a chance to throw exception if the
        // reference is not in a valid format.
        self::unpack_reference($reference);

        $referencehash = sha1($reference);

        $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                  FROM {files} f
                  JOIN {files_reference} r ON f.referencefileid = r.id
                  JOIN {repository_instances} ri ON r.repositoryid = ri.id
                 WHERE r.referencehash = ?
                       AND (f.component <> ? OR f.filearea <> ?)";

        $rs = $DB->get_recordset_sql($sql, array($referencehash, 'user', 'draft'));
        $files = array();
        foreach ($rs as $filerecord) {
            $files[$filerecord->pathnamehash] = $this->get_file_instance($filerecord);
        }
        $rs->close();

        return $files;
    }

    /**
     * Returns the number of aliases that refer to some stored_file via the given reference
     *
     * All repositories that provide access to a stored_file are expected to use
     * {@link self::pack_reference()}. This method can't be used if the given reference
     * does not use this format or if you are looking for references to an external file
     * (for example it can't be used to count aliases that refer to a given Dropbox or
     * Box.net file).
     *
     * Aliases in user draft areas are not counted.
     *
     * @param string $reference identification of the referenced file
     * @return int
     */
    public function search_references_count($reference) {
        global $DB;

        if (is_null($reference)) {
            throw new coding_exception('NULL is not a valid reference to an external file');
        }

        // Give {@link self::unpack_reference()} a chance to throw exception if the
        // reference is not in a valid format.
        self::unpack_reference($reference);

        $referencehash = sha1($reference);

        $sql = "SELECT COUNT(f.id)
                  FROM {files} f
                  JOIN {files_reference} r ON f.referencefileid = r.id
                  JOIN {repository_instances} ri ON r.repositoryid = ri.id
                 WHERE r.referencehash = ?
                       AND (f.component <> ? OR f.filearea <> ?)";

        return (int)$DB->count_records_sql($sql, array($referencehash, 'user', 'draft'));
    }

    /**
     * Returns all aliases that link to the given stored_file
     *
     * Aliases in user draft areas are excluded from the returned list.
     *
     * @param stored_file $storedfile
     * @return array of stored_file
     */
    public function get_references_by_storedfile(stored_file $storedfile) {
        global $DB;

        $params = array();
        $params['contextid'] = $storedfile->get_contextid();
        $params['component'] = $storedfile->get_component();
        $params['filearea']  = $storedfile->get_filearea();
        $params['itemid']    = $storedfile->get_itemid();
        $params['filename']  = $storedfile->get_filename();
        $params['filepath']  = $storedfile->get_filepath();

        return $this->search_references(self::pack_reference($params));
    }

    /**
     * Returns the number of aliases that link to the given stored_file
     *
     * Aliases in user draft areas are not counted.
     *
     * @param stored_file $storedfile
     * @return int
     */
    public function get_references_count_by_storedfile(stored_file $storedfile) {
        global $DB;

        $params = array();
        $params['contextid'] = $storedfile->get_contextid();
        $params['component'] = $storedfile->get_component();
        $params['filearea']  = $storedfile->get_filearea();
        $params['itemid']    = $storedfile->get_itemid();
        $params['filename']  = $storedfile->get_filename();
        $params['filepath']  = $storedfile->get_filepath();

        return $this->search_references_count(self::pack_reference($params));
    }

    /**
     * Updates all files that are referencing this file with the new contenthash
     * and filesize
     *
     * @param stored_file $storedfile
     */
    public function update_references_to_storedfile(stored_file $storedfile) {
        global $CFG, $DB;
        $params = array();
        $params['contextid'] = $storedfile->get_contextid();
        $params['component'] = $storedfile->get_component();
        $params['filearea']  = $storedfile->get_filearea();
        $params['itemid']    = $storedfile->get_itemid();
        $params['filename']  = $storedfile->get_filename();
        $params['filepath']  = $storedfile->get_filepath();
        $reference = self::pack_reference($params);
        $referencehash = sha1($reference);

        $sql = "SELECT repositoryid, id FROM {files_reference}
                 WHERE referencehash = ?";
        $rs = $DB->get_recordset_sql($sql, array($referencehash));

        $now = time();
        foreach ($rs as $record) {
            $this->update_references($record->id, $now, null,
                    $storedfile->get_contenthash(), $storedfile->get_filesize(), 0, $storedfile->get_timemodified());
        }
        $rs->close();
    }

    /**
     * Convert file alias to local file
     *
     * @throws moodle_exception if file could not be downloaded
     *
     * @param stored_file $storedfile a stored_file instances
     * @param int $maxbytes throw an exception if file size is bigger than $maxbytes (0 means no limit)
     * @return stored_file stored_file
     */
    public function import_external_file(stored_file $storedfile, $maxbytes = 0) {
        global $CFG;
        $storedfile->import_external_file_contents($maxbytes);
        $storedfile->delete_reference();
        return $storedfile;
    }

    /**
     * Return mimetype by given file pathname.
     *
     * If file has a known extension, we return the mimetype based on extension.
     * Otherwise (when possible) we try to get the mimetype from file contents.
     *
     * @param string $fullpath Full path to the file on disk
     * @param string $filename Correct file name with extension, if omitted will be taken from $path
     * @return string
     */
    public static function mimetype($fullpath, $filename = null) {
        if (empty($filename)) {
            $filename = $fullpath;
        }

        // The mimeinfo function determines the mimetype purely based on the file extension.
        $type = mimeinfo('type', $filename);

        if ($type === 'document/unknown') {
            // The type is unknown. Inspect the file now.
            $type = self::mimetype_from_file($fullpath);
        }
        return $type;
    }

    /**
     * Inspect a file on disk for it's mimetype.
     *
     * @param string $fullpath Path to file on disk
     * @return string The mimetype
     */
    public static function mimetype_from_file($fullpath) {
        if (file_exists($fullpath)) {
            // The type is unknown. Attempt to look up the file type now.
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            // See https://bugs.php.net/bug.php?id=79045 - finfo isn't consistent with returned type, normalize into value
            // that is used internally by the {@see core_filetypes} class and the {@see mimeinfo_from_type} call below.
            $mimetype = $finfo->file($fullpath);
            if ($mimetype === 'image/svg') {
                $mimetype = 'image/svg+xml';
            }

            return mimeinfo_from_type('type', $mimetype);
        }

        return 'document/unknown';
    }

    /**
     * Cron cleanup job.
     */
    public function cron() {
        global $CFG, $DB;

        // find out all stale draft areas (older than 4 days) and purge them
        // those are identified by time stamp of the /. root dir
        mtrace('Deleting old draft files... ', '');
        \core\cron::trace_time_and_memory();
        $old = time() - 60*60*24*4;
        $sql = "SELECT *
                  FROM {files}
                 WHERE component = 'user' AND filearea = 'draft' AND filepath = '/' AND filename = '.'
                       AND timecreated < :old";
        $rs = $DB->get_recordset_sql($sql, array('old'=>$old));
        foreach ($rs as $dir) {
            $this->delete_area_files($dir->contextid, $dir->component, $dir->filearea, $dir->itemid);
        }
        $rs->close();
        mtrace('done.');

        // Remove orphaned files:
        // * preview files in the core preview filearea without the existing original file.
        // * document converted files in core documentconversion filearea without the existing original file.
        mtrace('Deleting orphaned preview, and document conversion files... ', '');
        \core\cron::trace_time_and_memory();
        $sql = "SELECT p.*
                  FROM {files} p
             LEFT JOIN {files} o ON (p.filename = o.contenthash)
                 WHERE p.contextid = ?
                   AND p.component = 'core'
                   AND (p.filearea = 'preview' OR p.filearea = 'documentconversion')
                   AND p.itemid = 0
                   AND o.id IS NULL";
        $syscontext = context_system::instance();
        $rs = $DB->get_recordset_sql($sql, array($syscontext->id));
        foreach ($rs as $orphan) {
            $file = $this->get_file_instance($orphan);
            if (!$file->is_directory()) {
                $file->delete();
            }
        }
        $rs->close();
        mtrace('done.');

        // remove trash pool files once a day
        // if you want to disable purging of trash put $CFG->fileslastcleanup=time(); into config.php
        $filescleanupperiod = empty($CFG->filescleanupperiod) ? 86400 : $CFG->filescleanupperiod;
        if (empty($CFG->fileslastcleanup) || ($CFG->fileslastcleanup < time() - $filescleanupperiod)) {
            require_once($CFG->libdir.'/filelib.php');
            // Delete files that are associated with a context that no longer exists.
            mtrace('Cleaning up files from deleted contexts... ', '');
            \core\cron::trace_time_and_memory();
            $sql = "SELECT DISTINCT f.contextid
                    FROM {files} f
                    LEFT OUTER JOIN {context} c ON f.contextid = c.id
                    WHERE c.id IS NULL";
            $rs = $DB->get_recordset_sql($sql);
            if ($rs->valid()) {
                $fs = get_file_storage();
                foreach ($rs as $ctx) {
                    $fs->delete_area_files($ctx->contextid);
                }
            }
            $rs->close();
            mtrace('done.');

            mtrace('Call filesystem cron tasks.', '');
            \core\cron::trace_time_and_memory();
            $this->filesystem->cron();
            mtrace('done.');
        }
    }

    /**
     * Get the sql formated fields for a file instance to be created from a
     * {files} and {files_refernece} join.
     *
     * @param string $filesprefix the table prefix for the {files} table
     * @param string $filesreferenceprefix the table prefix for the {files_reference} table
     * @return string the sql to go after a SELECT
     */
    private static function instance_sql_fields($filesprefix, $filesreferenceprefix) {
        // Note, these fieldnames MUST NOT overlap between the two tables,
        // else problems like MDL-33172 occur.
        $filefields = array('contenthash', 'pathnamehash', 'contextid', 'component', 'filearea',
            'itemid', 'filepath', 'filename', 'userid', 'filesize', 'mimetype', 'status', 'source',
            'author', 'license', 'timecreated', 'timemodified', 'sortorder', 'referencefileid');

        $referencefields = array('repositoryid' => 'repositoryid',
            'reference' => 'reference',
            'lastsync' => 'referencelastsync');

        // id is specifically named to prevent overlaping between the two tables.
        $fields = array();
        $fields[] = $filesprefix.'.id AS id';
        foreach ($filefields as $field) {
            $fields[] = "{$filesprefix}.{$field}";
        }

        foreach ($referencefields as $field => $alias) {
            $fields[] = "{$filesreferenceprefix}.{$field} AS {$alias}";
        }

        return implode(', ', $fields);
    }

    /**
     * Returns the id of the record in {files_reference} that matches the passed repositoryid and reference
     *
     * If the record already exists, its id is returned. If there is no such record yet,
     * new one is created (using the lastsync provided, too) and its id is returned.
     *
     * @param int $repositoryid
     * @param string $reference
     * @param int $lastsync
     * @param int $lifetime argument not used any more
     * @return int
     */
    private function get_or_create_referencefileid($repositoryid, $reference, $lastsync = null, $lifetime = null) {
        global $DB;

        $id = $this->get_referencefileid($repositoryid, $reference, IGNORE_MISSING);

        if ($id !== false) {
            // bah, that was easy
            return $id;
        }

        // no such record yet, create one
        try {
            $id = $DB->insert_record('files_reference', array(
                'repositoryid'  => $repositoryid,
                'reference'     => $reference,
                'referencehash' => sha1($reference),
                'lastsync'      => $lastsync));
        } catch (dml_exception $e) {
            // if inserting the new record failed, chances are that the race condition has just
            // occured and the unique index did not allow to create the second record with the same
            // repositoryid + reference combo
            $id = $this->get_referencefileid($repositoryid, $reference, MUST_EXIST);
        }

        return $id;
    }

    /**
     * Returns the id of the record in {files_reference} that matches the passed parameters
     *
     * Depending on the required strictness, false can be returned. The behaviour is consistent
     * with standard DML methods.
     *
     * @param int $repositoryid
     * @param string $reference
     * @param int $strictness either {@link IGNORE_MISSING}, {@link IGNORE_MULTIPLE} or {@link MUST_EXIST}
     * @return int|bool
     */
    private function get_referencefileid($repositoryid, $reference, $strictness) {
        global $DB;

        return $DB->get_field('files_reference', 'id',
            array('repositoryid' => $repositoryid, 'referencehash' => sha1($reference)), $strictness);
    }

    /**
     * Updates a reference to the external resource and all files that use it
     *
     * This function is called after synchronisation of an external file and updates the
     * contenthash, filesize and status of all files that reference this external file
     * as well as time last synchronised.
     *
     * @param int $referencefileid
     * @param int $lastsync
     * @param int $lifetime argument not used any more, liefetime is returned by repository
     * @param string $contenthash
     * @param int $filesize
     * @param int $status 0 if ok or 666 if source is missing
     * @param int $timemodified last time modified of the source, if known
     */
    public function update_references($referencefileid, $lastsync, $lifetime, $contenthash, $filesize, $status, $timemodified = null) {
        global $DB;
        $referencefileid = clean_param($referencefileid, PARAM_INT);
        $lastsync = clean_param($lastsync, PARAM_INT);
        validate_param($contenthash, PARAM_TEXT, NULL_NOT_ALLOWED);
        $filesize = clean_param($filesize, PARAM_INT);
        $status = clean_param($status, PARAM_INT);
        $params = array('contenthash' => $contenthash,
                    'filesize' => $filesize,
                    'status' => $status,
                    'referencefileid' => $referencefileid,
                    'timemodified' => $timemodified);
        $DB->execute('UPDATE {files} SET contenthash = :contenthash, filesize = :filesize,
            status = :status ' . ($timemodified ? ', timemodified = :timemodified' : '') . '
            WHERE referencefileid = :referencefileid', $params);
        $data = array('id' => $referencefileid, 'lastsync' => $lastsync);
        $DB->update_record('files_reference', (object)$data);
    }

    /**
     * Calculate and return the contenthash of the supplied file.
     *
     * @param   string $filepath The path to the file on disk
     * @return  string The file's content hash
     */
    public static function hash_from_path($filepath) {
        return sha1_file($filepath);
    }

    /**
     * Calculate and return the contenthash of the supplied content.
     *
     * @param   string $content The file content
     * @return  string The file's content hash
     */
    public static function hash_from_string($content) {
        return sha1($content ?? '');
    }
}
