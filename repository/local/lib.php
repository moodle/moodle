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
 * repository_local class is used to browse moodle files
 *
 * @since 2.0
 * @package    repository
 * @subpackage local
 * @copyright  2009 Dongsheng Cai <dongsheng@moodle.com>
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
    public function get_listing($encodedpath = '') {
        global $CFG, $USER, $OUTPUT;
        $ret = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = true;
        $ret['nologin'] = true;
        $list = array();

        if (!empty($encodedpath)) {
            $params = unserialize(base64_decode($encodedpath));
            if (is_array($params)) {
                $component = is_null($params['component']) ? NULL : clean_param($params['component'], PARAM_ALPHAEXT);
                $filearea  = is_null($params['filearea']) ? NULL : clean_param($params['filearea'], PARAM_ALPHAEXT);
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
                $courseid = is_object($course) ? $course->id : SITEID;
                $context = get_context_instance(CONTEXT_COURSE, $courseid);
            } else {
                $context = get_system_context();
            }
        }

        $browser = get_file_browser();

        if ($fileinfo = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename)) {
            // build path navigation
            $pathnodes = array();
            $encodedpath = base64_encode(serialize($fileinfo->get_params()));
            $pathnodes[] = array('name'=>$fileinfo->get_visible_name(), 'path'=>$encodedpath);
            $level = $fileinfo->get_parent();
            while ($level) {
                $encodedpath = base64_encode(serialize($level->get_params()));
                $pathnodes[] = array('name'=>$level->get_visible_name(), 'path'=>$encodedpath);
                $level = $level->get_parent();
            }
            if (!empty($pathnodes) && is_array($pathnodes)) {
                $pathnodes = array_reverse($pathnodes);
                $ret['path'] = $pathnodes;
            }
            // build file tree
            $children = $fileinfo->get_children();
            foreach ($children as $child) {
                if ($child->is_directory()) {
                    if ($child->is_empty_area()) {
                        continue;
                    }
                    $params = $child->get_params();
                    $subdir_children = $child->get_children();
                    //if (empty($subdir_children)) {
                        //continue;
                    //}
                    $encodedpath = base64_encode(serialize($params));
                    // hide user_private area from local plugin, user should
                    // use private file plugin to access private files
                    //if ($params['filearea'] == 'user_private') {
                        //continue;
                    //}
                    $node = array(
                        'title' => $child->get_visible_name(),
                        'size' => 0,
                        'date' => '',
                        'path' => $encodedpath,
                        'children'=>array(),
                        'thumbnail' => $OUTPUT->pix_url('f/folder-32')->out(false)
                    );
                    $list[] = $node;
                } else {
                    $encodedpath = base64_encode(serialize($child->get_params()));
                    $node = array(
                        'title' => $child->get_visible_name(),
                        'size' => 0,
                        'date' => '',
                        'source'=> $encodedpath,
                        'thumbnail' => $OUTPUT->pix_url(file_extension_icon($child->get_visible_name(), 32))->out(false)
                    );
                    $list[] = $node;
                }
            }
        } else {
            // if file doesn't exist, build path nodes root of current context
            $pathnodes = array();
            $fileinfo = $browser->get_file_info($context, null, null, null, null, null);
            $encodedpath = base64_encode(serialize($fileinfo->get_params()));
            $pathnodes[] = array('name'=>$fileinfo->get_visible_name(), 'path'=>$encodedpath);
            $level = $fileinfo->get_parent();
            while ($level) {
                $encodedpath = base64_encode(serialize($level->get_params()));
                $pathnodes[] = array('name'=>$level->get_visible_name(), 'path'=>$encodedpath);
                $level = $level->get_parent();
            }
            if (!empty($pathnodes) && is_array($pathnodes)) {
                $pathnodes = array_reverse($pathnodes);
                $ret['path'] = $pathnodes;
            }
            $list = array();
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
        return FILE_INTERNAL;
    }

    /**
     * Does this repository used to browse moodle files?
     *
     * @return boolean
     */
    public function has_moodle_files() {
        return true;
    }
}
