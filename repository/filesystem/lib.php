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
 * @since Moodle 2.0
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
     * The subdirectory of the instance.
     *
     * @var string
     */
    protected $subdir;

    /**
     * Constructor
     *
     * @param int $repositoryid repository ID
     * @param int $context context ID
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $this->subdir = $this->get_option('fs_path');
    }

    /**
     * Get the list of files and directories in that repository.
     *
     * @param string $fullpath Path to explore. This is assembled by {@link self::build_node_path()}.
     * @param string $page Page number.
     * @return array List of files and folders.
     */
    public function get_listing($fullpath = '', $page = '') {
        global $OUTPUT;

        $list = array(
            'list' => array(),
            'manage' => false,
            'dynload' => true,
            'nologin' => true,
            'path' => array()
        );

        // We analyse the path to extract what to browse.
        $fullpath = empty($fullpath) ? $this->build_node_path('root') : $fullpath;
        $trail = explode('|', $fullpath);
        $trail = array_pop($trail);
        list($mode, $path, $unused) = $this->explode_node_path($trail);

        // Is that a search?
        if ($mode === 'search') {
            return $this->search($path, $page);
        }

        // Cleaning up the requested path.
        $path = trim($path, '/');
        if (!$this->is_in_repository($path)) {
            // In case of doubt on the path, reset to default.
            $path = '';
        }
        $rootpath = $this->get_rootpath();
        $abspath = rtrim($rootpath . $path, '/') . '/';

        // Retrieve list of files and directories and sort them.
        $fileslist = array();
        $dirslist = array();
        if ($dh = opendir($abspath)) {
            while (($file = readdir($dh)) != false) {
                if ($file != '.' and $file != '..') {
                    if (is_file($abspath . $file)) {
                        $fileslist[] = $file;
                    } else {
                        $dirslist[] = $file;
                    }
                }
            }
        }
        core_collator::asort($fileslist, core_collator::SORT_NATURAL);
        core_collator::asort($dirslist, core_collator::SORT_NATURAL);

        // Fill the results.
        foreach ($dirslist as $file) {
            $list['list'][] = $this->build_node($rootpath, $path, $file, true, $fullpath);
        }
        foreach ($fileslist as $file) {
            $list['list'][] = $this->build_node($rootpath, $path, $file, false, $fullpath);
        }

        $list['path'] = $this->build_breadcrumb($fullpath);
        $list['list'] = array_filter($list['list'], array($this, 'filter'));

        return $list;
    }

    /**
     * Search files in repository.
     *
     * This search works by walking through the directories returning the files that match. Once
     * the limit of files is reached the walk stops. Whenever more files are requested, the walk
     * starts from the beginning until it reaches an additional set of files to return.
     *
     * @param string $query The query string.
     * @param int $page The page number.
     * @return mixed
     */
    public function search($query, $page = 1) {
        global $OUTPUT, $SESSION;

        $query = core_text::strtolower($query);
        $remainingdirs = 1000;
        $remainingobjects = 5000;
        $perpage = 50;

        // Because the repository API is weird, the first page is 0, but it should be 1.
        if (!$page) {
            $page = 1;
        }

        // Initialise the session variable in which we store the search related things.
        if (!isset($SESSION->repository_filesystem_search)) {
            $SESSION->repository_filesystem_search = array();
        }

        // Restore, or initialise the session search variables.
        if ($page <= 1) {
            $SESSION->repository_filesystem_search['query'] = $query;
            $SESSION->repository_filesystem_search['from'] = 0;
            $from = 0;
        } else {
            // Yes, the repository does not send the query again...
            $query = $SESSION->repository_filesystem_search['query'];
            $from = (int) $SESSION->repository_filesystem_search['from'];
        }
        $limit = $from + $perpage;
        $searchpath = $this->build_node_path('search', $query);

        // Pre-search initialisation.
        $rootpath = $this->get_rootpath();
        $found = 0;
        $toexplore = array('');

        // Retrieve list of matching files and directories.
        $matches = array();
        while (($path = array_shift($toexplore)) !== null) {
            $remainingdirs--;

            if ($objects = scandir($rootpath . $path)) {
                foreach ($objects as $object) {
                    $objectabspath = $rootpath . $path . $object;
                    if ($object == '.' || $object == '..') {
                        continue;
                    }

                    $remainingobjects--;
                    $isdir = is_dir($objectabspath);

                    // It is a match!
                    if (strpos(core_text::strtolower($object), $query) !== false) {
                        $found++;
                        $matches[] = array($path, $object, $isdir);

                        // That's enough, no need to find more.
                        if ($found >= $limit) {
                            break 2;
                        }
                    }

                    // I've seen enough files, I give up!
                    if ($remainingobjects <= 0) {
                        break 2;
                    }

                    // Add the directory to things to explore later.
                    if ($isdir) {
                        $toexplore[] = $path . trim($object, '/') . '/';
                    }
                }
            }

            if ($remainingdirs <= 0) {
                break;
            }
        }

        // Extract the results from all the matches.
        $matches = array_slice($matches, $from, $perpage);

        // If we didn't reach our limits of browsing, and we appear to still have files to find.
        if ($remainingdirs > 0 && $remainingobjects > 0 && count($matches) >= $perpage) {
            $SESSION->repository_filesystem_search['from'] = $limit;
            $pages = -1;

        // We reached the end of the repository, or our limits.
        } else {
            $SESSION->repository_filesystem_search['from'] = 0;
            $pages = 0;
        }

        // Organise the nodes.
        $results = array();
        foreach ($matches as $match) {
            list($path, $name, $isdir) = $match;
            $results[] = $this->build_node($rootpath, $path, $name, $isdir, $searchpath);
        }

        $list = array();
        $list['list'] = array_filter($results, array($this, 'filter'));
        $list['dynload'] = true;
        $list['nologin'] = true;
        $list['page'] = $page;
        $list['pages'] = $pages;
        $list['path'] = $this->build_breadcrumb($searchpath);

        return $list;
    }

    /**
     * Build the breadcrumb from a full path.
     *
     * @param string $path A path generated by {@link self::build_node_path()}.
     * @return array
     */
    protected function build_breadcrumb($path) {
        $breadcrumb = array(array(
            'name' => get_string('root', 'repository_filesystem'),
            'path' => $this->build_node_path('root')
        ));

        $crumbs = explode('|', $path);
        $trail = '';

        foreach ($crumbs as $crumb) {
            list($mode, $nodepath, $display) = $this->explode_node_path($crumb);
            switch ($mode) {
                case 'search':
                    $breadcrumb[] = array(
                        'name' => get_string('searchresults', 'repository_filesystem'),
                        'path' => $this->build_node_path($mode, $nodepath, $display, $trail),
                    );
                    break;

                case 'browse':
                    $breadcrumb[] = array(
                        'name' => $display,
                        'path' => $this->build_node_path($mode, $nodepath, $display, $trail),
                    );
                    break;
            }

            $lastcrumb = end($breadcrumb);
            $trail = $lastcrumb['path'];
        }

        return $breadcrumb;
    }

    /**
     * Build a file or directory node.
     *
     * @param string $rootpath The absolute path to the repository.
     * @param string $path The relative path of the object
     * @param string $name The name of the object
     * @param string $isdir Is the object a directory?
     * @param string $rootnodepath The node leading to this node (for breadcrumb).
     * @return array
     */
    protected function build_node($rootpath, $path, $name, $isdir, $rootnodepath) {
        global $OUTPUT;

        $relpath = trim($path, '/') . '/' . $name;
        $abspath = $rootpath . $relpath;
        $node = array(
            'title' => $name,
            'datecreated' => filectime($abspath),
            'datemodified' => filemtime($abspath),
        );

        if ($isdir) {
            $node['children'] = array();
            $node['thumbnail'] = $OUTPUT->pix_url(file_folder_icon(90))->out(false);
            $node['path'] = $this->build_node_path('browse', $relpath, $name, $rootnodepath);

        } else {
            $node['source'] = $relpath;
            $node['size'] = filesize($abspath);
            $node['thumbnail'] = $OUTPUT->pix_url(file_extension_icon($name, 90))->out(false);
            $node['icon'] = $OUTPUT->pix_url(file_extension_icon($name, 24))->out(false);
            $node['path'] = $relpath;

            if (file_extension_in_typegroup($name, 'image') && ($imageinfo = @getimagesize($abspath))) {
                // This means it is an image and we can return dimensions and try to generate thumbnail/icon.
                $token = $node['datemodified'] . $node['size']; // To prevent caching by browser.
                $node['realthumbnail'] = $this->get_thumbnail_url($relpath, 'thumb', $token)->out(false);
                $node['realicon'] = $this->get_thumbnail_url($relpath, 'icon', $token)->out(false);
                $node['image_width'] = $imageinfo[0];
                $node['image_height'] = $imageinfo[1];
            }
        }

        return $node;
    }

    /**
     * Build the path to a browsable node.
     *
     * @param string $mode The type of browse mode.
     * @param string $realpath The path, or similar.
     * @param string $display The way to display the node.
     * @param string $root The path preceding this node.
     * @return string
     */
    protected function build_node_path($mode, $realpath = '', $display = '', $root = '') {
        $path = $mode . ':' . base64_encode($realpath) . ':' . base64_encode($display);
        if (!empty($root)) {
            $path = $root . '|' . $path;
        }
        return $path;
    }

    /**
     * Extract information from a node path.
     *
     * Note, this should not include preceding paths.
     *
     * @param string $path The path of the node.
     * @return array Contains the mode, the relative path, and the display text.
     */
    protected function explode_node_path($path) {
        list($mode, $realpath, $display) = explode(':', $path);
        return array(
            $mode,
            base64_decode($realpath),
            base64_decode($display)
        );
    }

    /**
     * To check whether the user is logged in.
     *
     * @return bool
     */
    public function check_login() {
        return true;
    }

    /**
     * Show the login screen, if required.
     *
     * @return string
     */
    public function print_login() {
        return true;
    }

    /**
     * Is it possible to do a global search?
     *
     * @return bool
     */
    public function global_search() {
        return false;
    }

    /**
     * Return file path.
     * @return array
     */
    public function get_file($file, $title = '') {
        global $CFG;
        $file = ltrim($file, '/');
        if (!$this->is_in_repository($file)) {
            throw new repository_exception('Invalid file requested.');
        }
        $file = $this->get_rootpath() . $file;

        // This is a hack to prevent move_to_file deleting files in local repository.
        $CFG->repository_no_delete = true;
        return array('path' => $file, 'url' => '');
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

    /**
     * Logout from repository instance
     *
     * @return string
     */
    public function logout() {
        return true;
    }

    /**
     * Return names of the instance options.
     *
     * @return array
     */
    public static function get_instance_option_names() {
        return array('fs_path', 'relativefiles');
    }

    /**
     * Save settings for repository instance
     *
     * @param array $options settings
     * @return bool
     */
    public function set_option($options = array()) {
        $options['fs_path'] = clean_param($options['fs_path'], PARAM_PATH);
        $options['relativefiles'] = clean_param($options['relativefiles'], PARAM_INT);
        $ret = parent::set_option($options);
        return $ret;
    }

    /**
     * Edit/Create Instance Settings Moodle form
     *
     * @param moodleform $mform Moodle form (passed by reference)
     */
    public static function instance_config_form($mform) {
        global $CFG;
        if (has_capability('moodle/site:config', context_system::instance())) {
            $path = $CFG->dataroot . '/repository/';
            if (!is_dir($path)) {
                mkdir($path, $CFG->directorypermissions, true);
            }
            if ($handle = opendir($path)) {
                $fieldname = get_string('path', 'repository_filesystem');
                $choices = array();
                while (false !== ($file = readdir($handle))) {
                    if (is_dir($path . $file) && $file != '.' && $file != '..') {
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
                    $mform->addElement('static', null, '',  get_string('information', 'repository_filesystem', $path));
                }
                closedir($handle);
            }
            $mform->addElement('checkbox', 'relativefiles', get_string('relativefiles', 'repository_filesystem'),
                get_string('relativefiles_desc', 'repository_filesystem'));
            $mform->setType('relativefiles', PARAM_INT);

        } else {
            $mform->addElement('static', null, '',  get_string('nopermissions', 'error', get_string('configplugin',
                'repository_filesystem')));
            return false;
        }
    }

    /**
     * Create an instance for this plug-in
     *
     * @static
     * @param string $type the type of the repository
     * @param int $userid the user id
     * @param stdClass $context the context
     * @param array $params the options for this instance
     * @param int $readonly whether to create it readonly or not (defaults to not)
     * @return mixed
     */
    public static function create($type, $userid, $context, $params, $readonly=0) {
        if (has_capability('moodle/site:config', context_system::instance())) {
            return parent::create($type, $userid, $context, $params, $readonly);
        } else {
            require_capability('moodle/site:config', context_system::instance());
            return false;
        }
    }

    /**
     * Validate repository plugin instance form
     *
     * @param moodleform $mform moodle form
     * @param array $data form data
     * @param array $errors errors
     * @return array errors
     */
    public static function instance_form_validation($mform, $data, $errors) {
        $fspath = clean_param(trim($data['fs_path'], '/'), PARAM_PATH);
        if (empty($fspath) && !is_numeric($fspath)) {
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

    public function sync_reference(stored_file $file) {
        if ($file->get_referencelastsync() + 60 > time()) {
            // Does not cost us much to synchronise within our own filesystem, check every 1 minute.
            return false;
        }
        static $issyncing = false;
        if ($issyncing) {
            // Avoid infinite recursion when calling $file->get_filesize() and get_contenthash().
            return false;
        }
        $filepath = $this->get_rootpath() . ltrim($file->get_reference(), '/');
        if ($this->is_in_repository($file->get_reference()) && file_exists($filepath) && is_readable($filepath)) {
            $fs = get_file_storage();
            $issyncing = true;
            if (file_extension_in_typegroup($filepath, 'web_image')) {
                $contenthash = sha1_file($filepath);
                if ($file->get_contenthash() == $contenthash) {
                    // File did not change since the last synchronisation.
                    $filesize = filesize($filepath);
                } else {
                    // Copy file into moodle filepool (used to generate an image thumbnail).
                    list($contenthash, $filesize, $newfile) = $fs->add_file_to_pool($filepath);
                }
            } else {
                // Update only file size so file will NOT be copied into moodle filepool.
                $emptyfile = $contenthash = sha1('');
                $currentcontenthash = $file->get_contenthash();
                if ($currentcontenthash !== $emptyfile && $currentcontenthash === sha1_file($filepath)) {
                    // File content was synchronised and has not changed since then, leave it.
                    $contenthash = null;
                }
                $filesize = filesize($filepath);
            }
            $issyncing = false;
            $modified = filemtime($filepath);
            $file->set_synchronized($contenthash, $filesize, 0, $modified);
        } else {
            $file->set_missingsource();
        }
        return true;
    }

    /**
     * Repository method to serve the referenced file
     *
     * @see send_stored_file
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param int $lifetime Number of seconds before the file should expire from caches (null means $CFG->filelifetime)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, array $options = null) {
        $reference = $storedfile->get_reference();
        $file = $this->get_rootpath() . ltrim($reference, '/');
        if ($this->is_in_repository($reference) && is_readable($file)) {
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
     * Return the rootpath of this repository instance.
     *
     * Trim() is a necessary step to ensure that the subdirectory is not '/'.
     *
     * @return string path
     * @throws repository_exception If the subdir is unsafe, or invalid.
     */
    public function get_rootpath() {
        global $CFG;
        $subdir = clean_param(trim($this->subdir, '/'), PARAM_PATH);
        $path = $CFG->dataroot . '/repository/' . $this->subdir . '/';
        if ((empty($this->subdir) && !is_numeric($this->subdir)) || $subdir != $this->subdir || !is_dir($path)) {
            throw new repository_exception('The instance is not properly configured, invalid path.');
        }
        return $path;
    }

    /**
     * Checks if $path is part of this repository.
     *
     * Try to prevent $path hacks such as ../ .
     *
     * We do not use clean_param(, PARAM_PATH) here because it also trims down some
     * characters that are allowed, like < > ' . But we do ensure that the directory
     * is safe by checking that it starts with $rootpath.
     *
     * @param string $path relative path to a file or directory in the repo.
     * @return boolean false when not.
     */
    protected function is_in_repository($path) {
        $rootpath = $this->get_rootpath();
        if (strpos(realpath($rootpath . $path), realpath($rootpath)) !== 0) {
            return false;
        }
        return true;
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
                '/' . trim($filepath, '/') . '/', $token);
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
        $origfile = $this->get_rootpath() . $filepath;
        // As thumbnail filename we use original file content hash.
        if (!$this->is_in_repository($filepath) || !($filecontents = @file_get_contents($origfile))) {
            // File is not found or is not readable.
            return null;
        }
        $filename = sha1($filecontents);

        // Try to get generated thumbnail for this file.
        $fs = get_file_storage();
        if (!($file = $fs->get_file(SYSCONTEXTID, 'repository_filesystem', $thumbsize, $this->id, '/' . $filepath . '/',
                $filename))) {
            // Thumbnail not found . Generate and store thumbnail.
            require_once($CFG->libdir . '/gdlib.php');
            if ($thumbsize === 'thumb') {
                $size = 90;
            } else {
                $size = 24;
            }
            if (!$data = generate_image_thumbnail_from_string($filecontents, $size, $size)) {
                // Generation failed.
                return null;
            }
            $record = array(
                'contextid' => SYSCONTEXTID,
                'component' => 'repository_filesystem',
                'filearea' => $thumbsize,
                'itemid' => $this->id,
                'filepath' => '/' . $filepath . '/',
                'filename' => $filename,
            );
            $file = $fs->create_file_from_string($record, $data);
        }
        return $file;
    }

    /**
     * Run in cron for particular repository instance. Removes thumbnails for deleted/modified files.
     *
     * @param stored_file[] $storedfiles
     */
    public function remove_obsolete_thumbnails($storedfiles) {
        // Group found files by filepath ('filepath' in Moodle file storage is dir+name in filesystem repository).
        $files = array();
        foreach ($storedfiles as $file) {
            if (!isset($files[$file->get_filepath()])) {
                $files[$file->get_filepath()] = array();
            }
            $files[$file->get_filepath()][] = $file;
        }

        // Loop through all files and make sure the original exists and has the same contenthash.
        $deletedcount = 0;
        foreach ($files as $filepath => $filesinpath) {
            if ($filecontents = @file_get_contents($this->get_rootpath() . trim($filepath, '/'))) {
                // The 'filename' in Moodle file storage is contenthash of the file in filesystem repository.
                $filename = sha1($filecontents);
                foreach ($filesinpath as $file) {
                    if ($file->get_filename() !== $filename && $file->get_filename() !== '.') {
                        // Contenthash does not match, this is an old thumbnail.
                        $deletedcount++;
                        $file->delete();
                    }
                }
            } else {
                // Thumbnail exist but file not.
                foreach ($filesinpath as $file) {
                    if ($file->get_filename() !== '.') {
                        $deletedcount++;
                    }
                    $file->delete();
                }
            }
        }
        if ($deletedcount) {
            mtrace(" instance {$this->id}: deleted $deletedcount thumbnails");
        }
    }

    /**
     *  Gets a file relative to this file in the repository and sends it to the browser.
     *
     * @param stored_file $mainfile The main file we are trying to access relative files for.
     * @param string $relativepath the relative path to the file we are trying to access.
     */
    public function send_relative_file(stored_file $mainfile, $relativepath) {
        global $CFG;
        // Check if this repository is allowed to use relative linking.
        $allowlinks = $this->supports_relative_file();
        if (!empty($allowlinks)) {
            // Get path to the mainfile.
            $mainfilepath = $mainfile->get_source();

            // Strip out filename from the path.
            $filename = $mainfile->get_filename();
            $basepath = strstr($mainfilepath, $filename, true);

            $fullrelativefilepath = realpath($this->get_rootpath().$basepath.$relativepath);

            // Sanity check to make sure this path is inside this repository and the file exists.
            if (strpos($fullrelativefilepath, realpath($this->get_rootpath())) === 0 && file_exists($fullrelativefilepath)) {
                send_file($fullrelativefilepath, basename($relativepath), null, 0);
            }
        }
        send_file_not_found();
    }

    /**
     * helper function to check if the repository supports send_relative_file.
     *
     * @return true|false
     */
    public function supports_relative_file() {
        return $this->get_option('relativefiles');
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
    global $OUTPUT, $CFG;
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
    // The thumbnails should not be changing much, but maybe the default lifetime is too long.
    $lifetime = $CFG->filelifetime;
    if ($lifetime > 60*10) {
        $lifetime = 60*10;
    }
    send_stored_file($file, $lifetime, 0, $forcedownload, $options);
}

/**
 * Cron callback for repository_filesystem. Deletes the thumbnails for deleted or changed files.
 */
function repository_filesystem_cron() {
    $fs = get_file_storage();
    // Find all generated thumbnails and group them in array by itemid (itemid == repository instance id).
    $allfiles = array_merge(
            $fs->get_area_files(SYSCONTEXTID, 'repository_filesystem', 'thumb'),
            $fs->get_area_files(SYSCONTEXTID, 'repository_filesystem', 'icon')
    );
    $filesbyitem = array();
    foreach ($allfiles as $file) {
        if (!isset($filesbyitem[$file->get_itemid()])) {
            $filesbyitem[$file->get_itemid()] = array();
        }
        $filesbyitem[$file->get_itemid()][] = $file;
    }
    // Find all instances of repository_filesystem.
    $instances = repository::get_instances(array('type' => 'filesystem'));
    // Loop through all itemids of generated thumbnails.
    foreach ($filesbyitem as $itemid => $files) {
        if (!isset($instances[$itemid]) || !($instances[$itemid] instanceof repository_filesystem)) {
            // Instance was deleted.
            $fs->delete_area_files(SYSCONTEXTID, 'repository_filesystem', 'thumb', $itemid);
            $fs->delete_area_files(SYSCONTEXTID, 'repository_filesystem', 'icon', $itemid);
            mtrace(" instance $itemid does not exist: deleted all thumbnails");
        } else {
            // Instance has some generated thumbnails, check that they are not outdated.
            $instances[$itemid]->remove_obsolete_thumbnails($files);
        }
    }
}
