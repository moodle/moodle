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
 * repository_upload class
 * A subclass of repository, which is used to upload file
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage repository
 * @copyright 2009 Dongsheng Cai
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_upload extends repository {

    /**
     *
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()){
        parent::__construct($repositoryid, $context, $options);
        $this->itemid = optional_param('itemid', '', PARAM_INT);
        $this->filepath = urldecode(optional_param('savepath', '/', PARAM_PATH));
    }

    /**
     * Print a upload form
     *
     * @return array
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     * Process uploaded file
     *
     * @return array
     */
    public function upload() {
        try {
            $this->info = $this->upload_to_filepool('repo_upload_file', 'user_draft', $this->filepath, $this->itemid);
        } catch(Exception $e) {
            throw $e;
        }
        return $this->info;
    }

    public function get_listing() {
        global $CFG;
        $ret = array();
        $ret['nologin']  = true;
        $ret['nosearch'] = true;
        // define upload form in file picker
        $ret['upload'] = array('label'=>get_string('attachment', 'repository'), 'id'=>'repo-form');
        $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary
        $ret['list'] = array();
        $ret['dynload'] = false;
        return $ret;
    }

    /**
     * Define the name of this repository
     * @return string
     */
    public function get_name(){
        return get_string('repositoryname', 'repository_upload');
    }

    /**
     * supported return types
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }

    /**
     * Upload file to local filesystem pool
     * @param string $elname name of element
     * @param string $filearea
     * @param string $filepath
     * @param string $filename - use specified filename, if not specified name of uploaded file used
     * @param bool $override override file if exists
     * @return mixed stored_file object or false if error; may throw exception if duplicate found
     */
    public function upload_to_filepool($elname, $filearea='user_draft', $filepath='/', $itemid='', $filename = '', $override = true) {
        global $USER;

        if ($filepath !== '/') {
            $filepath = trim($filepath, '/');
            $filepath = '/'.$filepath.'/';
        }

        if (!isset($_FILES[$elname])) {
            throw new moodle_exception('nofile');
        }

        if (!empty($_FILES[$elname]['error'])) {
            throw new moodle_exception('maxbytes');
        }

        if (!$filename) {
            $filename = $_FILES[$elname]['name'];
        }

        $context = get_context_instance(CONTEXT_USER, $USER->id);
        if (empty($itemid)) {
            $itemid = (int)substr(hexdec(uniqid()), 0, 9)+rand(1,100);
        }
        $fs = get_file_storage();
        $browser = get_file_browser();

        if ($file = $fs->get_file($context->id, $filearea, $itemid, $filepath, $filename)) {
            if ($override) {
                $file->delete();
            } else {
                throw new moodle_exception('fileexist');
            }
        }

        $file_record = new object();
        $file_record->contextid = $context->id;
        $file_record->filearea  = $filearea;
        $file_record->itemid    = $itemid;
        $file_record->filepath  = $filepath;
        $file_record->filename  = $filename;
        $file_record->userid    = $USER->id;

        try {
            $file = $fs->create_file_from_pathname($file_record, $_FILES[$elname]['tmp_name']);
        } catch (Exception $e) {
            $e->obj = $_FILES[$elname];
            throw $e;
        }
        $info = $browser->get_file_info($context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        return array(
            'url'=>$info->get_url(),
            'id'=>$itemid,
            'file'=>$file->get_filename()
        );
    }
}

