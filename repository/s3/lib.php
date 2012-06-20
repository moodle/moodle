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
 * This plugin is used to access s3 files
 *
 * @since 2.0
 * @package    repository_s3
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once('S3.php');

/**
 * This is a repository class used to browse Amazon S3 content.
 *
 * @since 2.0
 * @package    repository_s3
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_s3 extends repository {

    /**
     * Constructor
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $this->access_key = get_config('s3', 'access_key');
        $this->secret_key = get_config('s3', 'secret_key');
        $this->s = new S3($this->access_key, $this->secret_key);
    }

    /**
     * Get S3 file list
     *
     * @param string $path
     * @return array The file list and options
     */
    public function get_listing($path = '', $page = '') {
        global $CFG, $OUTPUT;
        if (empty($this->access_key)) {
            die(json_encode(array('e'=>get_string('needaccesskey', 'repository_s3'))));
        }
        $list = array();
        $list['list'] = array();
        // the management interface url
        $list['manage'] = false;
        // dynamically loading
        $list['dynload'] = true;
        // the current path of this list.
        // set to true, the login link will be removed
        $list['nologin'] = true;
        // set to true, the search button will be removed
        $list['nosearch'] = true;
        $tree = array();
        if (empty($path)) {
            $buckets = $this->s->listBuckets();
            foreach ($buckets as $bucket) {
                $folder = array(
                    'title' => $bucket,
                    'children' => array(),
                    'thumbnail'=>$OUTPUT->pix_url(file_folder_icon(90))->out(false),
                    'path'=>$bucket
                    );
                $tree[] = $folder;
            }
        } else {
            $contents = $this->s->getBucket($path);
            foreach ($contents as $file) {
                $info = $this->s->getObjectInfo($path, baseName($file['name']));
                $tree[] = array(
                    'title'=>$file['name'],
                    'size'=>$file['size'],
                    'date'=>userdate($file['time']),
                    'source'=>$path.'/'.$file['name'],
                    'thumbnail' => $OUTPUT->pix_url(file_extension_icon($file['name'], 90))->out(false)
                    );
            }
        }

        $list['list'] = $tree;

        return $list;
    }

    /**
     * Download S3 files to moodle
     *
     * @param string $filepath
     * @param string $file The file path in moodle
     * @return array The local stored path
     */
    public function get_file($filepath, $file = '') {
        global $CFG;
        $arr = explode('/', $filepath);
        $bucket   = $arr[0];
        $filename = $arr[1];
        $path = $this->prepare_file($file);
        $this->s->getObject($bucket, $filename, $path);
        return array('path'=>$path);
    }

    /**
     * Return the source information
     *
     * @param stdClass $filepath
     * @return string
     */
    public function get_file_source_info($filepath) {
        return 'Amazon S3: ' . $filepath;
    }

    /**
     * S3 doesn't require login
     *
     * @return bool
     */
    public function check_login() {
        return true;
    }

    /**
     * S3 doesn't provide search
     *
     * @return bool
     */
    public function global_search() {
        return false;
    }

    public static function get_type_option_names() {
        return array('access_key', 'secret_key', 'pluginname');
    }

    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form($mform);
        $strrequired = get_string('required');
        $mform->addElement('text', 'access_key', get_string('access_key', 'repository_s3'));
        $mform->addElement('text', 'secret_key', get_string('secret_key', 'repository_s3'));
        $mform->addRule('access_key', $strrequired, 'required', null, 'client');
        $mform->addRule('secret_key', $strrequired, 'required', null, 'client');
    }

    /**
     * S3 plugins doesn't support return links of files
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }
}
