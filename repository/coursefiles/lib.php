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
 * This plugin is used to access coursefiles repository
 *
 * @since 2.0
 * @package    repository_coursefiles
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_coursefiles class is used to browse course files
 *
 * @since 2.0
 * @package    repository_coursefiles
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_coursefiles extends repository {

    /**
     * coursefiles plugin doesn't require login, so list all files
     *
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
        $component = 'course';
        $filearea  = 'legacy';
        $itemid = 0;

        $browser = get_file_browser();

        if (!empty($encodedpath)) {
            $params = unserialize(base64_decode($encodedpath));
            if (is_array($params)) {
                $filepath  = is_null($params['filepath']) ? NULL : clean_param($params['filepath'], PARAM_PATH);
                $filename  = is_null($params['filename']) ? NULL : clean_param($params['filename'], PARAM_FILE);
                $context = context::instance_by_id(clean_param($params['contextid'], PARAM_INT));
            }
        } else {
            $filename = null;
            $filepath = null;
            list($context, $course, $cm) = get_context_info_array($this->context->id);
            $courseid = is_object($course) ? $course->id : SITEID;
            $context = context_course::instance($courseid);
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
                        'datemodified' => $child->get_timemodified(),
                        'datecreated' => $child->get_timecreated(),
                        'path' => $encodedpath,
                        'children'=>array(),
                        'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false)
                    );
                    $list[] = $node;
                } else {
                    $encodedpath = base64_encode(serialize($child->get_params()));
                    $node = array(
                        'title' => $child->get_visible_name(),
                        'size' => $child->get_filesize(),
                        'author' => $child->get_author(),
                        'license' => $child->get_license(),
                        'datemodified' => $child->get_timemodified(),
                        'datecreated' => $child->get_timecreated(),
                        'source'=> $encodedpath,
                        'isref' => $child->is_external_file(),
                        'thumbnail' => $OUTPUT->pix_url(file_file_icon($child, 90))->out(false)
                    );
                    if ($child->get_status() == 666) {
                        $node['originalmissing'] = true;
                    }
                    if ($imageinfo = $child->get_imageinfo()) {
                        $fileurl = new moodle_url($child->get_url());
                        $node['realthumbnail'] = $fileurl->out(false, array('preview' => 'thumb', 'oid' => $child->get_timemodified()));
                        $node['realicon'] = $fileurl->out(false, array('preview' => 'tinyicon', 'oid' => $child->get_timemodified()));
                        $node['image_width'] = $imageinfo['width'];
                        $node['image_height'] = $imageinfo['height'];
                    }
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
        $filepath = clean_param($params['filepath'], PARAM_PATH);
        $filearea = clean_param($params['filearea'], PARAM_AREA);
        $component = clean_param($params['component'], PARAM_COMPONENT);
        $context = context::instance_by_id($contextid);

        $file_info = $browser->get_file_info($context, $component, $filearea, $fileitemid, $filepath, $filename);
        return $file_info->get_url();
    }

    /**
     * Return is the instance is visible
     * (is the type visible ? is the context enable ?)
     *
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
        return (FILE_INTERNAL | FILE_REFERENCE);
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

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }
}
