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
 * @package moodlecore
 * @subpackage repository
 * @copyright 2009 Dongsheng Cai
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_local extends repository {

    /**
     * initialize local plugin
     * @param int $repositoryid
     * @param int $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
    }

    /**
     * local plugin doesn't require login, so list all files
     * @return mixed
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     * Not supported by File API yet
     * @param string $search_text
     * @return mixed
     */
    public function search($search_text) {
        return array();
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
                $itemid   = $params['itemid'];
                $filename = $params['filename'];
                $filearea = $params['filearea'];
                $filepath = $params['filepath'];
                $context  = get_context_instance_by_id($params['contextid']);
            }
        } else {
            $itemid   = null;
            $filename = null;
            $filearea = null;
            $filepath = null;
            $context  = get_system_context();
        }

        try {
            $browser = get_file_browser();

            if ($fileinfo = $browser->get_file_info($context, $filearea, $itemid, $filepath, $filename)) {
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
                        $params = $child->get_params();
                        $subdir_children = $child->get_children();
                        if (empty($subdir_children)) {
                            continue;
                        }
                        $encodedpath = base64_encode(serialize($params));
                        // hide user_private area from local plugin, user should
                        // use private file plugin to access private files
                        if ($params['filearea'] == 'user_private') {
                            continue;
                        }
                        $node = array(
                            'title' => $child->get_visible_name(),
                            'size' => 0,
                            'date' => '',
                            'path' => $encodedpath,
                            'children'=>array(),
                            'thumbnail' => $OUTPUT->pix_url('f/folder-32') . ''
                        );
                        $list[] = $node;
                    } else {
                        $encodedpath = base64_encode(serialize($child->get_params()));
                        $icon = 'f/'.str_replace('.gif', '', mimeinfo('icon', $child->get_visible_name())).'-32';
                        $node = array(
                            'title' => $child->get_visible_name(),
                            'size' => 0,
                            'date' => '',
                            'source'=> $encodedpath,
                            'thumbnail' => $OUTPUT->pix_url($icon) . '',
                        );
                        $list[] = $node;
                    }
                }
            }
        } catch (Exception $e) {
            throw new repository_exception('emptyfilelist', 'repository_local');
        }
        $ret['list'] = $list;
        $ret['list'] = array_filter($list, array($this, 'filter'));
        return $ret;
    }

    /**
     * Set repository name
     *
     * @return string repository name
     */
    public function get_name(){
        return get_string('repositoryname', 'repository_local');;
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
     * Copy a file to file area
     *
     * @global object $USER
     * @global object $DB
     * @param string $encoded The metainfo of file, it is base64 encoded php seriablized data
     * @param string $new_filename The intended name of file
     * @param string $new_itemid itemid
     * @param string $new_filepath the new path in draft area
     * @return array The information of file
     */
    public function copy_to_area($encoded, $new_filearea='user_draft', $new_itemid = '', $new_filepath = '/', $new_filename = '') {
        global $USER, $DB;
        $info = array();

        $browser = get_file_browser();
        $params = unserialize(base64_decode($encoded));
        $user_context = get_context_instance(CONTEXT_USER, $USER->id);
        // the final file
        $contextid  = $params['contextid'];
        $filearea   = $params['filearea'];
        $filepath   = $params['filepath'];
        $filename   = $params['filename'];
        $fileitemid = $params['itemid'];
        $context    = get_context_instance_by_id($contextid);
        try {
            $file_info = $browser->get_file_info($context, $filearea, $fileitemid, $filepath, $filename);
            $file_info->copy_to_storage($user_context->id, $new_filearea, $new_itemid, $new_filepath, $new_filename);
        } catch (Exception $e) {
            throw $e;
        }

        $info['itemid'] = $new_itemid;
        $info['title']  = $new_filename;
        $info['contextid'] = $user_context->id;
        $info['filesize'] = $file_info->get_filesize();

        return $info;
    }
}
