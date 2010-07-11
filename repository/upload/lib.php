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
 * A repository plugin to allow user uploading files
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage repository
 * @copyright 2009 Dongsheng Cai
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_upload extends repository {
    private $mimetypes = array();

    /**
     * Print a upload form
     * @return array
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     * Process uploaded file
     * @return array|bool
     */
    public function upload() {
        global $USER, $CFG;

        $types = optional_param('accepted_types', '*', PARAM_RAW);
        if ((is_array($types) and in_array('*', $types)) or $types == '*') {
            $this->mimetypes = '*';
        } else {
            foreach ($types as $type) {
                $this->mimetypes[] = mimeinfo('type', $type);
            }
        }

        $record = new stdclass;
        $record->filearea = 'draft';
        $record->component = 'user';
        $record->filepath = urldecode(optional_param('savepath', '/', PARAM_PATH));
        $record->itemid   = optional_param('itemid', 0, PARAM_INT);
        $record->license  = optional_param('license', $CFG->sitedefaultlicense, PARAM_TEXT);
        $record->author   = optional_param('author', '', PARAM_TEXT);

        $context = get_context_instance(CONTEXT_USER, $USER->id);
        $elname = 'repo_upload_file';

        $fs = get_file_storage();
        $browser = get_file_browser();

        if ($record->filepath !== '/') {
            $record->filepath = file_correct_filepath($record->filepath);
        }

        if (!isset($_FILES[$elname])) {
            throw new moodle_exception('nofile');
        }

        if (!empty($_FILES[$elname]['error'])) {
            throw new moodle_exception('maxbytes');
        }

        if (empty($record->filename)) {
            $record->filename = $_FILES[$elname]['name'];
        }

        if ($this->mimetypes != '*') {
            // check filetype
            if (!in_array(mimeinfo('type', $_FILES[$elname]['name']), $this->mimetypes)) {
                throw new moodle_exception('invalidfiletype', 'repository', '', get_string(mimeinfo('type', $_FILES[$elname]['name']), 'mimetypes'));
            }
        }

        $userquota = file_get_user_used_space();
        if (filesize($_FILES[$elname]['tmp_name'])+$userquota>=(int)$CFG->userquota) {
            throw new file_exception('userquotalimit');
        }

        if (empty($record->itemid)) {
            $record->itemid = 0;
        }

        if ($file = $browser->get_file_info($context, $record->filearea, $record->itemid, $record->filepath, $record->filename)) {
            $file->delete();
            //throw new moodle_exception('fileexist');
        }

        $record->contextid = $context->id;
        $record->userid    = $USER->id;
        $record->source    = '';

        $stored_file = $fs->create_file_from_pathname($record, $_FILES[$elname]['tmp_name']);

        return array(
            'url'=>moodle_url::make_draftfile_url($record->itemid, $record->filepath, $record->filename)->out(),
            'id'=>$record->itemid,
            'file'=>$record->filename);
    }

    /**
     * Return a upload form
     * @return array
     */
    public function get_listing() {
        global $CFG;
        $ret = array();
        $ret['nologin']  = true;
        $ret['nosearch'] = true;
        $ret['norefresh'] = true;
        $ret['list'] = array();
        $ret['dynload'] = false;
        $ret['upload'] = array('label'=>get_string('attachment', 'repository'), 'id'=>'repo-form');
        return $ret;
    }

    /**
     * Define the readable name of this repository
     * @return string
     */
    public function get_name(){
        return get_string('pluginname', 'repository_upload');
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
    public function upload_to_filepool($elname, $record, $override = true) {
    }
}
