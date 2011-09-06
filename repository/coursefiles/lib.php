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
 * repository_coursefiles class is used to browse course files
 *
 * @since 2.0
 * @package    repository
 * @subpackage coursefiles
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_coursefiles extends repository {

    /**
     * coursefiles plugin doesn't require login, so list all files
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
        $component = 'course';
        $filearea  = 'legacy';
        $itemid = 0;

        $browser = get_file_browser();

        if (!empty($encodedpath)) {
            $params = unserialize(base64_decode($encodedpath));
            if (is_array($params)) {
                $filepath  = is_null($params['filepath']) ? NULL : clean_param($params['filepath'], PARAM_PATH);;
                $filename  = is_null($params['filename']) ? NULL : clean_param($params['filename'], PARAM_FILE);
                $context = get_context_instance_by_id(clean_param($params['contextid'], PARAM_INT));
            }
        } else {
            $filename = null;
            $filepath = null;
            list($context, $course, $cm) = get_context_info_array($this->context->id);
            $courseid = is_object($course) ? $course->id : SITEID;
            $context = get_context_instance(CONTEXT_COURSE, $courseid);
        }

        if ($fileinfo = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename)) {
            // build path navigation
            $pathnodes = array();
            $encodedpath = base64_encode(serialize($fileinfo->get_params()));
            $pathnodes[] = array('name'=>$fileinfo->get_visible_name(), 'path'=>$encodedpath);
            $level = $fileinfo->get_parent();
            while ($level) {
                $params = $level->get_params();
                $encodedpath = base64_encode(serialize($params));
                if ($params['contextid'] != $context->id) {
                    break;
                }
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
                    $encodedpath = base64_encode(serialize($params));
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
            $list = array();
        }
        $ret['list'] = array_filter($list, array($this, 'filter'));
        return $ret;
    }

    public function get_link($encoded) {
        $info = array();

        $browser = get_file_browser();

        // the final file
        $params = unserialize(base64_decode($encoded));
        $contextid  = clean_param($params['contextid'], PARAM_INT);
        $fileitemid = clean_param($params['itemid'], PARAM_INT);
        $filename = clean_param($params['filename'], PARAM_FILE);
        $filepath = clean_param($params['filepath'], PARAM_PATH);;
        $filearea = clean_param($params['filearea'], PARAM_ALPHAEXT);
        $component = clean_param($params['component'], PARAM_ALPHAEXT);
        $context = get_context_instance_by_id($contextid);

        $file_info = $browser->get_file_info($context, $component, $filearea, $fileitemid, $filepath, $filename);
        return $file_info->get_url();
    }

    /**
     * Return is the instance is visible
     * (is the type visible ? is the context enable ?)
     * @return boolean
     */
    public function is_visible() {
        global $COURSE; //TODO: this is deprecated (skodak)
        if ($COURSE->legacyfiles != 2) {
            // do not show repo if legacy files disabled in this course...
            return false;
        }

        return parent::is_visible();
    }

    public function get_name() {
        list($context, $course, $cm) = get_context_info_array($this->context->id);
        if (!empty($course)) {
            return get_string('courselegacyfiles') . format_string($course->shortname, true, array('context' => get_course_context($context)));
        } else {
            return get_string('courselegacyfiles');
        }
    }

    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
    public static function get_type_option_names() {
        return array();
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
