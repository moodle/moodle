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
 * @since Moodle 2.0
 * @package    repository_local
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_local class is used to browse moodle files
 *
 * @since Moodle 2.0
 * @package    repository_local
 * @copyright  2012 Marina Glancy
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_local extends repository {
    /**
     * Get file listing
     *
     * @param string $encodedpath
     * @param string $page no paging is used in repository_local
     * @return mixed
     */
    public function get_listing($encodedpath = '', $page = '') {
        global $CFG, $USER, $OUTPUT;
        $ret = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = false;
        $ret['nologin'] = true;
        $ret['list'] = array();

        $itemid   = null;
        $filename = null;
        $filearea = null;
        $filepath = null;
        $component = null;

        if (!empty($encodedpath)) {
            $params = json_decode(base64_decode($encodedpath), true);
            if (is_array($params) && isset($params['contextid'])) {
                $component = is_null($params['component']) ? NULL : clean_param($params['component'], PARAM_COMPONENT);
                $filearea  = is_null($params['filearea']) ? NULL : clean_param($params['filearea'], PARAM_AREA);
                $itemid    = is_null($params['itemid']) ? NULL : clean_param($params['itemid'], PARAM_INT);
                $filepath  = is_null($params['filepath']) ? NULL : clean_param($params['filepath'], PARAM_PATH);
                $filename  = is_null($params['filename']) ? NULL : clean_param($params['filename'], PARAM_FILE);
                $context = context::instance_by_id(clean_param($params['contextid'], PARAM_INT));
            }
        }
        if (empty($context) && !empty($this->context)) {
            $context = $this->context->get_course_context(false);
        }
        if (empty($context)) {
            $context = context_system::instance();
        }

        // prepare list of allowed extensions: $extensions is either string '*'
        // or array of lowercase extensions, i.e. array('.gif','.jpg')
        $extensions = optional_param_array('accepted_types', '', PARAM_RAW);
        if (empty($extensions) || $extensions === '*' || (is_array($extensions) && in_array('*', $extensions))) {
            $extensions = '*';
        } else {
            if (!is_array($extensions)) {
                $extensions = array($extensions);
            }
            $extensions = array_map('core_text::strtolower', $extensions);
        }

        // build file tree
        $browser = get_file_browser();
        if (!($fileinfo = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename))) {
            // if file doesn't exist, build path nodes root of current context
            $fileinfo = $browser->get_file_info($context, null, null, null, null, null);
        }
        $ret['list'] = $this->get_non_empty_children($fileinfo, $extensions);

        // build path navigation
        $path = array();
        for ($level = $fileinfo; $level; $level = $level->get_parent()) {
            array_unshift($path, $level);
        }
        array_unshift($path, null);
        $ret['path'] = array();
        for ($i=1; $i<count($path); $i++) {
            if ($path[$i] == $fileinfo || !$this->can_skip($path[$i], $extensions, $path[$i-1])) {
                $ret['path'][] = $this->get_node_path($path[$i]);
            }
        }
        return $ret;
    }

    /**
     * Tells how the file can be picked from this repository
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
     * Returns all children elements that have one of the specified extensions
     *
     * This function may skip subfolders and recursively add their children
     * {@link repository_local::can_skip()}
     *
     * @param file_info $fileinfo
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @return array array of file_info elements
     */
    private function get_non_empty_children(file_info $fileinfo, $extensions) {
        $nonemptychildren = $fileinfo->get_non_empty_children($extensions);
        $list = array();
        foreach ($nonemptychildren as $child) {
            if ($this->can_skip($child, $extensions, $fileinfo)) {
                $list = array_merge($list, $this->get_non_empty_children($child, $extensions));
            } else {
                $list[] = $this->get_node($child);
            }
        }
        return $list;
    }

    /**
     * Whether this folder may be skipped in folder hierarchy
     *
     * 1. Skip the name of a single filearea in a module
     * 2. Skip course categories for non-admins who do not have navshowmycoursecategories setting
     *
     * @param file_info $fileinfo
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param file_info|int $parent specify parent here if we know it to avoid creating extra objects
     * @return bool
     */
    private function can_skip(file_info $fileinfo, $extensions, $parent = -1) {
        global $CFG;
        if (!$fileinfo->is_directory()) {
            // do not skip files
            return false;
        }
        if ($fileinfo instanceof file_info_context_coursecat) {
            // This is a course category. For non-admins we do not display categories
            return empty($CFG->navshowmycoursecategories) &&
                            !has_capability('moodle/course:update', context_system::instance());
        } else if ($fileinfo instanceof file_info_context_course ||
                $fileinfo instanceof file_info_context_user ||
                $fileinfo instanceof file_info_area_course_legacy ||
                $fileinfo instanceof file_info_context_module ||
                $fileinfo instanceof file_info_context_system) {
            // these instances can never be filearea inside an activity, they will never be skipped
            return false;
        } else {
            $params = $fileinfo->get_params();
            if (strlen($params['filearea']) &&
                    ($params['filepath'] === '/' || empty($params['filepath'])) &&
                    ($params['filename'] === '.' || empty($params['filename'])) &&
                    context::instance_by_id($params['contextid'])->contextlevel == CONTEXT_MODULE) {
                if ($parent === -1) {
                    $parent = $fileinfo->get_parent();
                }
                // This is a filearea inside an activity, it can be skipped if it has no non-empty siblings
                if ($parent && ($parent instanceof file_info_context_module)) {
                    if ($parent->count_non_empty_children($extensions, 2) <= 1) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Converts file_info object to element of repository return list
     *
     * @param file_info $fileinfo
     * @return array
     */
    private function get_node(file_info $fileinfo) {
        global $OUTPUT;
        $encodedpath = base64_encode(json_encode($fileinfo->get_params()));
        $node = array(
            'title' => $fileinfo->get_visible_name(),
            'datemodified' => $fileinfo->get_timemodified(),
            'datecreated' => $fileinfo->get_timecreated()
        );
        if ($fileinfo->is_directory()) {
            $node['path'] = $encodedpath;
            $node['thumbnail'] = $OUTPUT->image_url(file_folder_icon(90))->out(false);
            $node['children'] = array();
        } else {
            $node['size'] = $fileinfo->get_filesize();
            $node['author'] = $fileinfo->get_author();
            $node['license'] = $fileinfo->get_license();
            $node['isref'] = $fileinfo->is_external_file();
            if ($fileinfo->get_status() == 666) {
                $node['originalmissing'] = true;
            }
            $node['source'] = $encodedpath;
            $node['thumbnail'] = $OUTPUT->image_url(file_file_icon($fileinfo, 90))->out(false);
            $node['icon'] = $OUTPUT->image_url(file_file_icon($fileinfo, 24))->out(false);
            if ($imageinfo = $fileinfo->get_imageinfo()) {
                // what a beautiful picture, isn't it
                $fileurl = new moodle_url($fileinfo->get_url());
                $node['realthumbnail'] = $fileurl->out(false, array('preview' => 'thumb', 'oid' => $fileinfo->get_timemodified()));
                $node['realicon'] = $fileurl->out(false, array('preview' => 'tinyicon', 'oid' => $fileinfo->get_timemodified()));
                $node['image_width'] = $imageinfo['width'];
                $node['image_height'] = $imageinfo['height'];
            }
        }
        return $node;
    }

    /**
     * Converts file_info object to element of repository return path
     *
     * @param file_info $fileinfo
     * @return array
     */
    private function get_node_path(file_info $fileinfo) {
        $encodedpath = base64_encode(json_encode($fileinfo->get_params()));
        return array(
            'path' => $encodedpath,
            'name' => $fileinfo->get_visible_name()
        );
    }

    /**
     * Search through all the files.
     *
     * This method will do a raw search through the database, then will try
     * to match with files that a user can access. A maximum of 50 files will be
     * returned at a time, excluding possible duplicates found along the way.
     *
     * Queries are done in chunk of 100 files to prevent too many records to be fetched
     * at once. When too many files are not included, or a maximum of 10 queries are
     * performed we consider that this was the last page.
     *
     * @param  String  $q    The query string.
     * @param  integer $page The page number.
     * @return array of results.
     */
    public function search($q, $page = 1) {
        global $DB, $SESSION;

        // Because the repository API is weird, the first page is 0, but it should be 1.
        if (!$page) {
            $page = 1;
        }

        if (!isset($SESSION->repository_local_search)) {
            $SESSION->repository_local_search = array();
        }

        $fs = get_file_storage();
        $fb = get_file_browser();

        $max = 50;
        $limit = 100;
        if ($page <= 1) {
            $SESSION->repository_local_search['query'] = $q;
            $SESSION->repository_local_search['from'] = 0;
            $from = 0;
        } else {
            // Yes, the repository does not send the query again...
            $q = $SESSION->repository_local_search['query'];
            $from = (int) $SESSION->repository_local_search['from'];
        }

        $count = $fs->search_server_files('%' . $DB->sql_like_escape($q) . '%', null, null, true);
        $remaining = $count - $from;
        $maxloops = 3000;
        $loops = 0;

        $results = array();
        while (count($results) < $max && $maxloops > 0 && $remaining > 0) {
            if (empty($files)) {
                $files = $fs->search_server_files('%' . $DB->sql_like_escape($q) . '%', $from, $limit);
                $from += $limit;
            };

            $remaining--;
            $maxloops--;
            $loops++;

            $file = array_shift($files);
            if (!$file) {
                // This should not happen.
                throw new coding_exception('Unexpected end of files list.');
            }

            $key = $file->get_contenthash() . ':' . $file->get_filename();
            if (isset($results[$key])) {
                // We found the file with same content and same name, let's skip it.
                continue;
            }

            $ctx = context::instance_by_id($file->get_contextid());
            $fileinfo = $fb->get_file_info($ctx, $file->get_component(), $file->get_filearea(), $file->get_itemid(),
                $file->get_filepath(), $file->get_filename());
            if ($fileinfo) {
                $results[$key] = $this->get_node($fileinfo);
            }

        }

        // Save the position for the paging to work.
        if ($maxloops > 0 && $remaining > 0) {
            $SESSION->repository_local_search['from'] += $loops;
            $pages = -1;
        } else {
            $SESSION->repository_local_search['from'] = 0;
            $pages = 0;
        }

        $return = array(
            'list' => array_values($results),
            'dynload' => true,
            'pages' => $pages,
            'page' => $page
        );

        return $return;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }
}
