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
 * This plugin is used to access local files
 *
 * @since 2.0
 * @package    repository_local
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_local class is used to browse moodle files
 *
 * @since 2.0
 * @package    repository_local
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_local extends repository {
    /**
     * local plugin doesn't require login, so list all files
     * @return mixed
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     * Get file listing
     *
     * @param string $encodedpath
     * @return mixed
     */
    public function get_listing($encodedpath = '', $page = '') {
        global $CFG, $USER, $OUTPUT;
        $ret = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = true;
        $ret['nologin'] = true;
        $list = array();

        if (!empty($encodedpath)) {
            $params = unserialize(base64_decode($encodedpath));
            if (is_array($params)) {
                $component = is_null($params['component']) ? NULL : clean_param($params['component'], PARAM_COMPONENT);
                $filearea  = is_null($params['filearea']) ? NULL : clean_param($params['filearea'], PARAM_AREA);
                $itemid    = is_null($params['itemid']) ? NULL : clean_param($params['itemid'], PARAM_INT);
                $filepath  = is_null($params['filepath']) ? NULL : clean_param($params['filepath'], PARAM_PATH);;
                $filename  = is_null($params['filename']) ? NULL : clean_param($params['filename'], PARAM_FILE);
                $context = get_context_instance_by_id(clean_param($params['contextid'], PARAM_INT));
            }
        } else {
            $itemid   = null;
            $filename = null;
            $filearea = null;
            $filepath = null;
            $component = null;
            if (!empty($this->context)) {
                list($context, $course, $cm) = get_context_info_array($this->context->id);
                if (is_object($course)) {
                    $context = context_course::instance($course->id);
                } else {
                    $context = get_system_context();
                }
            } else {
                $context = get_system_context();
            }
        }

        $browser = get_file_browser();

        $list = array();
        if ($fileinfo = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename)) {
            // build file tree
            $element = repository_local_file::retrieve_file_info($fileinfo, $this);
            $nonemptychildren = $element->get_non_empty_children();
            foreach ($nonemptychildren as $child) {
                $list[] = (array)$child->get_node();
            }
        } else {
            // if file doesn't exist, build path nodes root of current context
            $fileinfo = $browser->get_file_info($context, null, null, null, null, null);
        }
        // build path navigation
        $ret['path'] = array();
        $element = repository_local_file::retrieve_file_info($fileinfo, $this);
        for ($level = $element; $level; $level = $level->get_parent()) {
            if ($level == $element || !$level->can_skip()) {
                array_unshift($ret['path'], $level->get_node_path());
            }
        }
        $ret['list'] = array_filter($list, array($this, 'filter'));
        return $ret;
    }

    /**
     * Local file don't support to link to external links
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL | FILE_REFERENCE;
    }

    /**
     * Does this repository used to browse moodle files?
     *
     * @return boolean
     */
    public function has_moodle_files() {
        return true;
    }

    /**
     * Return reference file life time
     *
     * @param string $ref
     * @return int
     */
    public function get_reference_file_lifetime($ref) {
        // this should be realtime
        return 0;
    }
}

/**
 * Class to cache some information about file
 *
 * This class is a wrapper to instances of file_info. It caches such information as
 * parent and list of children. It also stores an array of already retrieved elements.
 *
 * It also implements more comprehensive algorithm for checking if folder is empty
 * (taking into account the filtering of the files). To decrease number of levels
 * we check if some subfolders can be skipped from the tree.
 *
 * As a result we display in Server files repository only non-empty folders and skip
 * filearea folders if this is the only filearea in the module.
 * For non-admin the course categories are not shown as well (courses are shown as a list)
 *
 * @package    repository_local
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_local_file {
    /** @var array stores already retrieved files */
    private static $cachedfiles = array();
    /** @var file_info Stores the original file */
    public $fileinfo;
    /** @var bool whether this file is directory */
    private $isdir;
    /** @var array caches retrieved children */
    private $children = null;
    /** @var array caches retrieved information whether this file is an empty directory */
    protected $isempty = null;
    /** @var repository link to the container repository (for filtering the results) */
    private $repository;
    /** @var repository_local_file link to parent directory */
    protected $parent;
    /** @var bool caches calculated information on whether this directory must be skipped in the tree */
    private $skip = null;

    /**
     * Creates (or retrieves from cache) the repository_local_file object for $file_info
     *
     * @param file_info $fileinfo
     * @param repository $repository
     * @param repository_local_file $parent
     * @return repository_local_file
     */
    public static function retrieve_file_info(file_info $fileinfo, repository $repository, repository_local_file $parent = null) {
        $encodedpath = base64_encode(serialize($fileinfo->get_params()));
        if (!isset(self::$cachedfiles[$encodedpath])) {
            self::$cachedfiles[$encodedpath] = new repository_local_file($fileinfo, $repository, $parent);
        }
        return self::$cachedfiles[$encodedpath];
    }

    /**
     * Creates an object
     *
     * @param file_info $fileinfo
     * @param repository $repository
     * @param repository_local_file $parent
     */
    private function __construct(file_info $fileinfo, repository $repository, repository_local_file $parent = null) {
        $this->repository = $repository;
        $this->fileinfo = $fileinfo;
        $this->isdir = $fileinfo->is_directory();
        if (!$this->isdir) {
            $node = array('title' => $this->fileinfo->get_visible_name());
            $this->isempty = !$repository->filter($node);
            $this->skip = false;
        }
    }

    /**
     * Returns node for $ret['list']
     *
     * @return array
     */
    public function get_node() {
        global $OUTPUT;
        $encodedpath = base64_encode(serialize($this->fileinfo->get_params()));
        $node = array(
            'title' => $this->fileinfo->get_visible_name(),
            'datemodified' => $this->fileinfo->get_timemodified(),
            'datecreated' => $this->fileinfo->get_timecreated()
        );
        if ($this->isdir) {
            $node['path'] = $encodedpath;
            $node['thumbnail'] = $OUTPUT->pix_url(file_folder_icon(90))->out(false);
            $node['children'] = array();
        } else {
            $node['size'] = $this->fileinfo->get_filesize();
            $node['author'] = $this->fileinfo->get_author();
            $node['license'] = $this->fileinfo->get_license();
            $node['isref'] = $this->fileinfo->is_external_file();
            if ($this->fileinfo->get_status() == 666) {
                $node['originalmissing'] = true;
            }
            $node['source'] = $encodedpath;
            $node['thumbnail'] = $OUTPUT->pix_url(file_file_icon($this->fileinfo, 90))->out(false);
            $node['icon'] = $OUTPUT->pix_url(file_file_icon($this->fileinfo, 24))->out(false);
            if ($imageinfo = $this->fileinfo->get_imageinfo()) {
                // what a beautiful picture, isn't it
                $fileurl = new moodle_url($this->fileinfo->get_url());
                $node['realthumbnail'] = $fileurl->out(false, array('preview' => 'thumb', 'oid' => $this->fileinfo->get_timemodified()));
                $node['realicon'] = $fileurl->out(false, array('preview' => 'tinyicon', 'oid' => $this->fileinfo->get_timemodified()));
                $node['image_width'] = $imageinfo['width'];
                $node['image_height'] = $imageinfo['height'];
            }
        }
        return $node;
    }

    /**
     * Returns node for $ret['path']
     *
     * @return array
     */
    public function get_node_path() {
        $encodedpath = base64_encode(serialize($this->fileinfo->get_params()));
        return array(
            'path' => $encodedpath,
            'name' => $this->fileinfo->get_visible_name()
        );
    }

    /**
     * Checks if this is a directory
     *
     * @return bool
     */
    public function is_dir() {
        return $this->isdir;
    }

    /**
     * Returns children of this element
     *
     * @return array
     */
    public function get_children() {
        if (!$this->isdir) {
            return array();
        }
        if ($this->children === null) {
            $this->children = array();
            $children = $this->fileinfo->get_children();
            for ($i=0; $i<count($children); $i++) {
                $this->children[] = self::retrieve_file_info($children[$i], $this->repository, $this);
            }
        }
        return $this->children;
    }

    /**
     * Checks if this folder is empty (contains no non-empty children)
     *
     * @return bool
     */
    public function is_empty() {
        if ($this->isempty === null) {
            $this->isempty = true;
            if (!$this->fileinfo->is_empty_area()) {
                // even if is_empty_area() returns false, element still may be empty
                $children = $this->get_children();
                if (!empty($children)) {
                    // 1. Let's look at already retrieved children
                    foreach ($children as $childnode) {
                        if ($childnode->isempty === false) {
                            // we already calculated isempty for a child, and it is not empty
                            $this->isempty = false;
                            break;
                        }
                    }
                    if ($this->isempty) {
                        // 2. now we know that this directory contains children that are either empty or we don't know
                        foreach ($children as $childnode) {
                            if (!$childnode->is_empty()) {
                                $this->isempty = false;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $this->isempty;
    }

    /**
     * Returns the parent element
     *
     * @return repository_local_file
     */
    public function get_parent() {
        if ($this->parent === null) {
            if ($parent = $this->fileinfo->get_parent()) {
                $this->parent = self::retrieve_file_info($parent, $this->repository);
            } else {
                $this->parent = false;
            }
        }
        return $this->parent;
    }

    /**
     * Wether this folder may be skipped in tree view
     *
     * @return bool
     */
    public function can_skip() {
        global $CFG;
        if ($this->skip === null) {
            $this->skip = false;
            if ($this->fileinfo instanceof file_info_stored) {
                $params = $this->fileinfo->get_params();
                if (strlen($params['filearea']) && $params['filepath'] == '/' && $params['filename'] == '.') {
                    // This is a filearea inside an activity, it can be skipped if it has no non-empty siblings
                    if ($parent = $this->get_parent()) {
                        $siblings = $parent->get_children();
                        $countnonempty = 0;
                        foreach ($siblings as $sibling) {
                            if (!$sibling->is_empty()) {
                                $countnonempty++;
                                if ($countnonempty > 1) {
                                    break;
                                }
                            }
                        }
                        if ($countnonempty <= 1) {
                            $this->skip = true;
                        }
                    }
                }
            } else if ($this->fileinfo instanceof file_info_context_coursecat) {
                // This is a course category. For non-admins we do not display categories
                $this->skip = empty($CFG->navshowmycoursecategories) &&
                        !has_capability('moodle/course:update', context_system::instance());
            }
        }
        return $this->skip;
    }

    /**
     * Returns array of children who have any elmenets
     *
     * If a subfolder can be skipped - list children of subfolder instead
     * (recursive function)
     *
     * @return array
     */
    public function get_non_empty_children() {
        $children = $this->get_children();
        $nonemptychildren = array();
        foreach ($children as $child) {
            if (!$child->is_empty()) {
                if ($child->can_skip()) {
                    $nonemptychildren = array_merge($nonemptychildren, $child->get_non_empty_children());
                } else {
                    $nonemptychildren[] = $child;
                }
            }
        }
        return $nonemptychildren;
    }
}
