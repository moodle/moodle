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
 * repository_recent class is used to browse recent used files
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage repository
 * @copyright 2010 Dongsheng Cai
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('DEFAULT_RECENT_FILES_NUM', 50);
class repository_recent extends repository {

    /**
     * initialize recent plugin
     * @param int $repositoryid
     * @param int $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $number = get_config('recent', 'recentfilesnumber');
        $number = (int)$number;
        if (empty($number)) {
            $this->number = DEFAULT_RECENT_FILES_NUM;
        } else {
            $this->number = $number;
        }
    }

    /**
     * recent plugin doesn't require login, so list all files
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

    private function get_recent_files($limitfrom = 0, $limit = DEFAULT_RECENT_FILES_NUM) {
        global $USER, $DB;
        // TODO: should exclude user_draft area files?
        $sql = 'SELECT * FROM {files} files1
                JOIN (SELECT contenthash, filename, MAX(id) AS id
                FROM {files} 
                WHERE userid = ? AND filename != ?
                GROUP BY contenthash, filename) files2 ON files1.id = files2.id
                ORDER BY files1.timemodified DESC';
        $params = array('userid'=>$USER->id, 'filename'=>'.');
        $rs = $DB->get_recordset_sql($sql, $params, $limitfrom, $limit);
        $result = array();
        foreach ($rs as $file_record) {
            $info = array();
            $info['contextid'] = $file_record->contextid;
            $info['itemid'] = $file_record->itemid;
            $info['filearea'] = $file_record->filearea;
            $info['filepath'] = $file_record->filepath;
            $info['filename'] = $file_record->filename;
            $result[$file_record->pathnamehash] = $info;
        }
        $rs->close();
        return $result;
    }

    /**
     * Get file listing
     *
     * @param string $encodedpath
     * @param string $path not used by this plugin
     * @return mixed
     */
    public function get_listing($encodedpath = '', $page = '') {
        global $CFG, $USER, $OUTPUT;
        $ret = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = true;
        $list = array();
        $files = $this->get_recent_files(0, $this->number);

        try {
            foreach ($files as $file) {
                $params = base64_encode(serialize($file));
                $icon = 'f/'.str_replace('.gif', '', mimeinfo('icon', $file['filename'])) . '-32';
                $node = array(
                    'title' => $file['filename'],
                    'size' => 0,
                    'date' => '',
                    'source'=> $params,
                    'thumbnail' => $OUTPUT->pix_url($icon) . '',
                );
                $list[] = $node;
            }
        } catch (Exception $e) {
            throw new repository_exception('emptyfilelist', 'repository_recent');
        }
        $ret['list'] = $list;
        return $ret;
    }


    /**
     * Set repository name
     *
     * @return string repository name
     */
    public function get_name(){
        return get_string('repositoryname', 'repository_recent');;
    }

    public static function get_type_option_names() {
        return array('recentfilesnumber');
    }

    public function type_config_form($mform) {
        $number = get_config('repository_recent', 'recentfilesnumber');
        if (empty($number)) {
            $number = DEFAULT_RECENT_FILES_NUM;
        }
        $mform->addElement('text', 'recentfilesnumber', get_string('recentfilesnumber', 'repository_recent'));
        $mform->setDefault('recentfilesnumber', $number);
    }

    /**
     * This plugin doesn't support to link to external links
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }
    /**
     * Copy a file to draft area
     *
     * @global object $USER
     * @global object $DB
     * @param string $encoded The information of file, it is base64 encoded php seriablized data
     * @param string $new_filename The intended name of file
     * @param string $new_itemid itemid
     * @param string $new_filepath the new path in draft area
     * @return array The information of file
     */
    public function copy_to_draft($encoded, $new_filename = '', $new_itemid = '', $new_filepath = '/') {
        global $USER, $DB;
        $info = array();
        $fs = get_file_storage();

        $params = unserialize(base64_decode($encoded));
        $user_context = get_context_instance(CONTEXT_USER, $USER->id);

        $contextid  = $params['contextid'];
        $filearea   = $params['filearea'];
        $filepath   = $params['filepath'];
        $filename   = $params['filename'];
        $fileitemid = $params['itemid'];

        // XXX:
        // When user try to pick a file from other filearea, normally file api will use file browse to
        // operate the files with capability check, but in some areas, users don't have permission to
        // browse the files (for example, forum_attachment area).
        //
        // To get 'recent' plugin working, we need to use lower level file_stoarge class to bypass the
        // capability check, we will use a better workaround to improve it.
        if ($stored_file = $fs->get_file($contextid, $filearea, $fileitemid, $filepath, $filename)) {
            $file_record = array('contextid'=>$user_context->id, 'filearea'=>'user_draft',
                'itemid'=>$new_itemid, 'filepath'=>$new_filepath, 'filename'=>$new_filename);
            if ($file = $fs->get_file($user_context->id, 'user_draft', $new_itemid, $new_filepath, $new_filename)) {
                $file->delete();
            }
            $fs->create_file_from_storedfile($file_record, $stored_file);
        }

        $info['title']  = $new_filename;
        $info['itemid'] = $new_itemid;
        $info['filesize']  = $stored_file->get_filesize();
        $info['contextid'] = $user_context->id;

        return $info;
    }
}
