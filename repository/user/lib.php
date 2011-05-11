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
 * repository_user class is used to browse user private files
 *
 * @since 2.0
 * @package    repository
 * @subpackage user
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_user extends repository {

    /**
     * user plugin doesn't require login
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
                $filepath = clean_param($params['filepath'], PARAM_PATH);;
                $filename = clean_param($params['filename'], PARAM_FILE);
            }
        } else {
            $itemid   = 0;
            $filepath = '/';
            $filename = null;
        }
        $filearea = 'private';
        $component = 'user';
        $itemid  = 0;
        $context = get_context_instance(CONTEXT_USER, $USER->id);

        try {
            $browser = get_file_browser();

            if ($fileinfo = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename)) {
                $pathnodes = array();
                $level = $fileinfo;
                $params = $fileinfo->get_params();
                while ($level && $params['component'] == 'user' && $params['filearea'] == 'private') {
                    $encodedpath = base64_encode(serialize($level->get_params()));
                    $pathnodes[] = array('name'=>$level->get_visible_name(), 'path'=>$encodedpath);
                    $level = $level->get_parent();
                    $params = $level->get_params();
                }
                $ret['path'] = array_reverse($pathnodes);

                // build file tree
                $children = $fileinfo->get_children();
                foreach ($children as $child) {
                    if ($child->is_directory()) {
                        $encodedpath = base64_encode(serialize($child->get_params()));
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
            }
        } catch (Exception $e) {
            throw new repository_exception('emptyfilelist', 'repository_user');
        }
        $ret['list'] = $list;
        $ret['list'] = array_filter($list, array($this, 'filter'));
        return $ret;
    }

    /**
     * User file don't support to link to external links
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
