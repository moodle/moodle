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
 * This plugin is used to access files on server file system
 *
 * @since 2.0
 * @package    repository_filesystem
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * repository_filesystem class
 *
 * Create a repository from your local filesystem
 * *NOTE* for security issue, we use a fixed repository path
 * which is %moodledata%/repository
 *
 * @package    repository
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_filesystem extends repository {

    /**
     * Constructor
     *
     * @param int $repositoryid repository ID
     * @param int $context context ID
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        global $CFG;
        parent::__construct($repositoryid, $context, $options);
        $root = $CFG->dataroot . '/repository/';
        $subdir = $this->get_option('fs_path');

        $this->root_path = $root;
        if (!empty($subdir)) {
            $this->root_path .= $subdir . '/';
        }

        if (!empty($options['ajax'])) {
            if (!is_dir($this->root_path)) {
                $created = mkdir($this->root_path, $CFG->directorypermissions, true);
                $ret = array();
                $ret['msg'] = get_string('invalidpath', 'repository_filesystem');
                $ret['nosearch'] = true;
                if ($options['ajax'] && !$created) {
                    echo json_encode($ret);
                    exit;
                }
            }
        }
    }
    public function get_listing($path = '', $page = '') {
        global $CFG, $OUTPUT;
        $list = array();
        $list['list'] = array();
        // process breacrumb trail
        $list['path'] = array(
            array('name'=>get_string('root', 'repository_filesystem'), 'path'=>'')
        );
        $trail = '';
        if (!empty($path)) {
            $parts = explode('/', $path);
            if (count($parts) > 1) {
                foreach ($parts as $part) {
                    if (!empty($part)) {
                        $trail .= ('/'.$part);
                        $list['path'][] = array('name'=>$part, 'path'=>$trail);
                    }
                }
            } else {
                $list['path'][] = array('name'=>$path, 'path'=>$path);
            }
            $this->root_path .= ($path.'/');
        }
        $list['manage'] = false;
        $list['dynload'] = true;
        $list['nologin'] = true;
        $list['nosearch'] = true;
        // retrieve list of files and directories and sort them
        $fileslist = array();
        $dirslist = array();
        if ($dh = opendir($this->root_path)) {
            while (($file = readdir($dh)) != false) {
                if ( $file != '.' and $file !='..') {
                    if (is_file($this->root_path.$file)) {
                        $fileslist[] = $file;
                    } else {
                        $dirslist[] = $file;
                    }
                }
            }
        }
        core_collator::asort($fileslist, core_collator::SORT_STRING);
        core_collator::asort($dirslist, core_collator::SORT_STRING);
        // fill the $list['list']
        foreach ($dirslist as $file) {
            if (!empty($path)) {
                $current_path = $path . '/'. $file;
            } else {
                $current_path = $file;
            }
            $list['list'][] = array(
                'title' => $file,
                'children' => array(),
                'datecreated' => filectime($this->root_path.$file),
                'datemodified' => filemtime($this->root_path.$file),
                'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                'path' => $current_path
                );
        }
        foreach ($fileslist as $file) {
            $node = array(
                'title' => $file,
                'source' => $path.'/'.$file,
                'size' => filesize($this->root_path.$file),
                'datecreated' => filectime($this->root_path.$file),
                'datemodified' => filemtime($this->root_path.$file),
                'thumbnail' => $OUTPUT->pix_url(file_extension_icon($file, 90))->out(false),
                'icon' => $OUTPUT->pix_url(file_extension_icon($file, 24))->out(false)
            );
            if (file_extension_in_typegroup($file, 'image') && ($imageinfo = @getimagesize($this->root_path. $file))) {
                // This means it is an image and we can return dimensions and try to generate thumbnail/icon.
                $token = $node['datemodified']. $node['size']; // To prevent caching by browser.
                $node['realthumbnail'] = $this->get_thumbnail_url($path. '/'. $file, 'thumb', $token)->out(false);
                $node['realicon'] = $this->get_thumbnail_url($path. '/'. $file, 'icon', $token)->out(false);
                $node['image_width'] = $imageinfo[0];
                $node['image_height'] = $imageinfo[1];
            }
            $list['list'][] = $node;
        }
        $list['list'] = array_filter($list['list'], array($this, 'filter'));
        return $list;
    }

    public function check_login() {
        return true;
    }
    public function print_login() {
        return true;
    }
    public function global_search() {
        return false;
    }

    /**
     * Return file path
     * @return array
     */
    public function get_file($file, $title = '') {
        global $CFG;
        if ($file{0} == '/') {
            $file = $this->root_path.substr($file, 1, strlen($file)-1);
        } else {
            $file = $this->root_path.$file;
        }
        // this is a hack to prevent move_to_file deleteing files
        // in local repository
        $CFG->repository_no_delete = true;
        return array('path'=>$file, 'url'=>'');
    }

    /**
     * Return the source information
     *
     * @param stdClass $filepath
     * @return string|null
     */
    public function get_file_source_info($filepath) {
        return $filepath;
    }

    public function logout() {
        return true;
    }

    public static function get_instance_option_names() {
        return array('fs_path');
    }

    public function set_option($options = array()) {
        $options['fs_path'] = clean_param($options['fs_path'], PARAM_PATH);
        $ret = parent::set_option($options);
        return $ret;
    }

    public static function instance_config_form($mform) {
        global $CFG, $PAGE;
        if (has_capability('moodle/site:config', context_system::instance())) {
            $path = $CFG->dataroot . '/repository/';
            if (!is_dir($path)) {
                mkdir($path, $CFG->directorypermissions, true);
            }
            if ($handle = opendir($path)) {
                $fieldname = get_string('path', 'repository_filesystem');
                $choices = array();
                while (false !== ($file = readdir($handle))) {
                    if (is_dir($path.$file) && $file != '.' && $file!= '..') {
                        $choices[$file] = $file;
                        $fieldname = '';
                    }
                }
                if (empty($choices)) {
                    $mform->addElement('static', '', '', get_string('nosubdir', 'repository_filesystem', $path));
                    $mform->addElement('hidden', 'fs_path', '');
                    $mform->setType('fs_path', PARAM_PATH);
                } else {
                    $mform->addElement('select', 'fs_path', $fieldname, $choices);
                    $mform->addElement('static', null, '',  get_string('information','repository_filesystem', $path));
                }
                closedir($handle);
            }
        } else {
            $mform->addElement('static', null, '',  get_string('nopermissions', 'error', get_string('configplugin', 'repository_filesystem')));
            return false;
        }
    }

    public static function create($type, $userid, $context, $params, $readonly=0) {
        global $PAGE;
        if (has_capability('moodle/site:config', context_system::instance())) {
            return parent::create($type, $userid, $context, $params, $readonly);
        } else {
            require_capability('moodle/site:config', context_system::instance());
            return false;
        }
    }
    public static function instance_form_validation($mform, $data, $errors) {
        if (empty($data['fs_path'])) {
            $errors['fs_path'] = get_string('invalidadminsettingname', 'error', 'fs_path');
        }
        return $errors;
    }

    /**
     * User cannot use the external link to dropbox
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL | FILE_REFERENCE;
    }

    /**
     * Return reference file life time
     *
     * @param string $ref
     * @return int
     */
    public function get_reference_file_lifetime($ref) {
        // Does not cost us much to synchronise within our own filesystem, set to 1 minute
        return 60;
    }

    /**
     * Return human readable reference information
     *
     * @param string $reference value of DB field files_reference.reference
     * @param int $filestatus status of the file, 0 - ok, 666 - source missing
     * @return string
     */
    public function get_reference_details($reference, $filestatus = 0) {
        $details = $this->get_name().': '.$reference;
        if ($filestatus) {
            return get_string('lostsource', 'repository', $details);
        } else {
            return $details;
        }
    }

    /**
     * Returns information about file in this repository by reference
     *
     * Returns null if file not found or is not readable
     *
     * @param stdClass $reference file reference db record
     * @return stdClass|null contains one of the following:
     *   - 'filesize' if file should not be copied to moodle filepool
     *   - 'filepath' if file should be copied to moodle filepool
     */
    public function get_file_by_reference($reference) {
        $ref = $reference->reference;
        if ($ref{0} == '/') {
            $filepath = $this->root_path.substr($ref, 1, strlen($ref)-1);
        } else {
            $filepath = $this->root_path.$ref;
        }
        if (file_exists($filepath) && is_readable($filepath)) {
            if (file_extension_in_typegroup($filepath, 'web_image')) {
                // return path to image files so it will be copied into moodle filepool
                // we need the file in filepool to generate an image thumbnail
                return (object)array('filepath' => $filepath);
            } else {
                // return just the file size so file will NOT be copied into moodle filepool
                return (object)array(
                    'filesize' => filesize($filepath)
                );
            }
        } else {
            return null;
        }
    }

    /**
     * Repository method to serve the referenced file
     *
     * @see send_stored_file
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param int $lifetime Number of seconds before the file should expire from caches (default 24 hours)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime=86400 , $filter=0, $forcedownload=false, array $options = null) {
        $reference = $storedfile->get_reference();
        if ($reference{0} == '/') {
            $file = $this->root_path.substr($reference, 1, strlen($reference)-1);
        } else {
            $file = $this->root_path.$reference;
        }
        if (is_readable($file)) {
            $filename = $storedfile->get_filename();
            if ($options && isset($options['filename'])) {
                $filename = $options['filename'];
            }
            $dontdie = ($options && isset($options['dontdie']));
            send_file($file, $filename, $lifetime , $filter, false, $forcedownload, '', $dontdie);
        } else {
            send_file_not_found();
        }
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }

    /**
     * Returns url of thumbnail file.
     *
     * @param string $filepath current path in repository (dir and filename)
     * @param string $thumbsize 'thumb' or 'icon'
     * @param string $token identifier of the file contents - to prevent browser from caching changed file
     * @return moodle_url
     */
    protected function get_thumbnail_url($filepath, $thumbsize, $token) {
        return moodle_url::make_pluginfile_url($this->context->id, 'repository_filesystem', $thumbsize, $this->id,
                '/'. trim($filepath, '/'). '/', $token);
    }

    /**
     * Returns the stored thumbnail file, generates it if not present.
     *
     * @param string $filepath current path in repository (dir and filename)
     * @param string $thumbsize 'thumb' or 'icon'
     * @return null|stored_file
     */
    public function get_thumbnail($filepath, $thumbsize) {
        global $CFG;

        $filepath = trim($filepath, '/');
        $origfile = $this->root_path. $filepath;
        // As thumbnail filename we use original file content hash.
        if (!($filecontents = @file_get_contents($origfile))) {
            // File is not found or is not readable.
            return null;
        }
        $filename = sha1($filecontents);
        unset($filecontents);

        // Try to get generated thumbnail for this file.
        $fs = get_file_storage();
        if (!($file = $fs->get_file(SYSCONTEXTID, 'repository_filesystem', $thumbsize, $this->id, '/'. $filepath. '/', $filename))) {
            // Thumbnail not found. Generate and store thumbnail.
            require_once($CFG->libdir. '/gdlib.php');
            if ($thumbsize === 'thumb') {
                $size = 90;
            } else {
                $size = 24;
            }
            if (!$data = @generate_image_thumbnail($origfile, $size, $size)) {
                // Generation failed.
                return null;
            }
            $record = array(
                'contextid' => SYSCONTEXTID,
                'component' => 'repository_filesystem',
                'filearea' => $thumbsize,
                'itemid' => $this->id,
                'filepath' => '/'. $filepath. '/',
                'filename' => $filename,
            );
            $file = $fs->create_file_from_string($record, $data);
        }
        return $file;
    }

    /**
     * Cron for particular repository instance. Removes thumbnails for deleted/modified files.
     */
    public function cron() {
        global $DB;
        // Get all records for generated thumbnails (we don't use files api because we don't
        // need creating instances of stored_file unless file is to be deleted).
        $sql = "SELECT id, contextid, component, filearea, itemid, filepath, filename, contenthash
                FROM {files} f WHERE f.contextid = :contextid
                           AND f.component = :component
                           AND (f.filearea = :filearea1 OR f.filearea = :filearea2)
                           AND itemid = :itemid";
        $filesraw = $DB->get_records_sql($sql, array(
            'contextid' => SYSCONTEXTID,
            'component' => 'repository_filesystem',
            'filearea1' => 'thumb',
            'filearea2' => 'icon',
            'itemid' => $this->id,
        ));
        // Group found files by filepath ('filepath' in Moodle file storage is dir+name in filesystem repository).
        $files = array();
        foreach ($filesraw as $filerecord) {
            if (!isset($files[$filerecord->filepath])) {
                $files[$filerecord->filepath] = array();
            }
            $files[$filerecord->filepath][] = $filerecord;
        }
        $filesraw = null;

        // Loop through all files and make sure the original exists and has the same contenthash.
        $deletedcount = 0;
        foreach ($files as $filepath => $filerecords) {
            if ($filecontents = @file_get_contents($this->root_path. trim($filepath, '/'))) {
                // 'filename' in Moodle file storage is contenthash of the file in filesystem repository.
                $filename = sha1($filecontents);
                foreach ($filerecords as $filerecord) {
                    if ($filerecord->filename !== $filename && $filerecord->filename !== '.') {
                        // Contenthash does not match, this is an old thumbnail.
                        $deletedcount++;
                        get_file_storage()->get_file_instance($filerecord)->delete();
                    }
                }
            } else {
                // Thumbnail exist but file not.
                foreach ($filerecords as $filerecord) {
                    if ($filerecord->filename !== '.') {
                        $deletedcount++;
                    }
                    get_file_storage()->get_file_instance($filerecord)->delete();
                }
            }
        }
        if ($deletedcount) {
            mtrace(" instance {$this->id}: deleted $deletedcount thumbnails");
        }
    }
}

/**
 * Generates and sends the thumbnail for an image in filesystem.
 *
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 */
function repository_filesystem_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $OUTPUT;
    // Allowed filearea is either thumb or icon - size of the thumbnail.
    if ($filearea !== 'thumb' && $filearea !== 'icon') {
        return false;
    }

    // As itemid we pass repository instance id.
    $itemid = array_shift($args);
    // Filename is some token that we can ignore (used only to make sure browser does not serve cached copy when file is changed).
    array_pop($args);
    // As filepath we use full filepath (dir+name) of the file in this instance of filesystem repository.
    $filepath = implode('/', $args);

    // Make sure file exists in the repository and is accessible.
    $repo = repository::get_repository_by_id($itemid, $context);
    $repo->check_capability();
    // Find stored or generated thumbnail.
    if (!($file = $repo->get_thumbnail($filepath, $filearea))) {
        // Generation failed, redirect to default icon for file extension.
        redirect($OUTPUT->pix_url(file_extension_icon($file, 90)));
    }
    send_stored_file($file, 360, 0, $forcedownload, $options);
}

/**
 * Cron callback for repository_filesystem. Deletes the thumbnails for deleted or changed files.
 */
function repository_filesystem_cron() {
    global $DB;
    // Find all repository instances ids that have generated thumbnails.
    $sql = "SELECT DISTINCT itemid FROM {files} f WHERE f.contextid = :contextid
                       AND f.component = :component
                       AND (f.filearea = :filearea1 OR f.filearea = :filearea2)";
    $itemids = $DB->get_fieldset_sql($sql, array(
        'contextid' => SYSCONTEXTID,
        'component' => 'repository_filesystem',
        'filearea1' => 'thumb',
        'filearea2' => 'icon'
    ));
    $instances = repository::get_instances(array('type' => 'filesystem'));
    foreach ($itemids as $itemid) {
        if (!isset($instances[$itemid])) {
            // Instance was deleted.
            $fs = get_file_storage();
            $fs->delete_area_files(SYSCONTEXTID, 'repository_filesystem', 'thumb', $itemid);
            $fs->delete_area_files(SYSCONTEXTID, 'repository_filesystem', 'icon', $itemid);
            mtrace(" instance $itemid does not exist: deleted all thumbnails");
        } else {
            // Instance has some generated thumbnails, check that they are not outdated.
            $instances[$itemid]->cron();
        }
    }
}