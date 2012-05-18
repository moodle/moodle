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
 * This plugin is used to access user's private files
 *
 * @since 2.0
 * @package    repository_user
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_user class is used to browse user private files
 *
 * @since     2.0
 * @package   repository_user
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_user extends repository {

    /**
     * user plugin doesn't require login
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
                            'datemodified' => $child->get_timemodified(),
                            'datecreated' => $child->get_timecreated(),
                            'path' => $encodedpath,
                            'children'=>array(),
                            'thumbnail' => $OUTPUT->pix_url('f/folder-32')->out(false)
                        );
                        $list[] = $node;
                    } else {
                        $encodedpath = base64_encode(serialize($child->get_params()));
                        $node = array(
                            'title' => $child->get_visible_name(),
                            'size' => $child->get_filesize(),
                            'datemodified' => $child->get_timemodified(),
                            'datecreated' => $child->get_timecreated(),
                            'author' => $child->get_author(),
                            'license' => $child->get_license(),
                            'source'=> $encodedpath,
                            'thumbnail' => $OUTPUT->pix_url(file_extension_icon($child->get_visible_name(), 32))->out(false)
                        );
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
            }
        } catch (Exception $e) {
            throw new repository_exception('emptyfilelist', 'repository_user');
        }
        $ret['list'] = $list;
        $ret['list'] = array_filter($list, array($this, 'filter'));
        return $ret;
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
     * User cannot use the external link to dropbox
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL | FILE_REFERENCE;
    }


    /**
     * Prepare file reference information
     *
     * @param string $source
     * @return string file referece
     */
    public function get_file_reference($source) {
        global $USER;
        $params = unserialize(base64_decode($source));
        if (is_array($params)) {
            $filepath = clean_param($params['filepath'], PARAM_PATH);;
            $filename = clean_param($params['filename'], PARAM_FILE);
            $contextid = clean_param($params['contextid'], PARAM_INT);
        }
        // We store all file parameters, so file api could
        // find the refernces later.
        $reference = array();
        $reference['contextid'] = $contextid;
        $reference['component'] = 'user';
        $reference['filearea']  = 'private';
        $reference['itemid']    = 0;
        $reference['filepath']  = $filepath;
        $reference['filename']  = $filename;

        return file_storage::pack_reference($reference);
    }

    /**
     * Get file from external repository by reference
     * {@link repository::get_file_reference()}
     * {@link repository::get_file()}
     *
     * @param stdClass $reference file reference db record
     * @return stdClass|null|false
     */
    public function get_file_by_reference($reference) {
        $fs = get_file_storage();
        $ref = $reference->reference;
        $params = unserialize(base64_decode($ref));
        if (!is_array($params)) {
            throw new repository_exception('invalidparams', 'repository');
        }
        $filename  = is_null($params['filename'])  ? null : clean_param($params['filename'], PARAM_FILE);
        $filepath  = is_null($params['filepath'])  ? null : clean_param($params['filepath'], PARAM_PATH);;
        $contextid = is_null($params['contextid']) ? null : clean_param($params['contextid'], PARAM_INT);

        // hard coded component, filearea and item for security
        $component = 'user';
        $filearea  = 'private';
        $itemid    = 0;

        $storedfile = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);

        $fileinfo = new stdClass;
        $fileinfo->contenthash = $storedfile->get_contenthash();
        $fileinfo->filesize    = $storedfile->get_filesize();
        return $fileinfo;
    }

    /**
     * Return human readable reference information
     * {@link stored_file::get_reference()}
     *
     * @param string $reference
     * @return string|null
     */
    public function get_reference_details($reference) {
        $params = file_storage::unpack_reference($reference);
        // Indicate this is from user private area
        return $this->get_name() . ': ' . $params['filepath'] . $params['filename'];
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
     * Repository method to serve file
     *
     * @param stored_file $storedfile
     * @param int $lifetime Number of seconds before the file should expire from caches (default 24 hours)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime=86400 , $filter=0, $forcedownload=false, array $options = null) {
        $reference = $storedfile->get_reference();
        $params = file_storage::unpack_reference($reference);
        $filepath = clean_param($params['filepath'], PARAM_PATH);;
        $filename = clean_param($params['filename'], PARAM_FILE);
        $contextid = clean_param($params['contextid'], PARAM_INT);
        $filearea  = 'private';
        $component = 'user';
        $itemid    = 0;

        $fs = get_file_storage();
        $storedfile = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);

        send_stored_file($storedfile, $lifetime, $filter, $forcedownload, $options);
    }
}
