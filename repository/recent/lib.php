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
 * @package    repository
 * @subpackage recent
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('DEFAULT_RECENT_FILES_NUM', 50);
class repository_recent extends repository {

    /**
     * Initialize recent plugin
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

    private function get_recent_files($limitfrom = 0, $limit = DEFAULT_RECENT_FILES_NUM) {
        // XXX: get current itemid
        global $USER, $DB, $itemid;
        $sql = 'SELECT * FROM {files} files1
                JOIN (SELECT contenthash, filename, MAX(id) AS id
                FROM {files}
                WHERE userid = ? AND filename != ? AND ((filearea = ? AND itemid = ?) OR filearea != ?)
                GROUP BY contenthash, filename) files2 ON files1.id = files2.id
                ORDER BY files1.timemodified DESC';
        $params = array('userid'=>$USER->id, 'filename'=>'.', 'filearea'=>'draft', 'itemid'=>$itemid, 'draft');
        $rs = $DB->get_recordset_sql($sql, $params, $limitfrom, $limit);
        $result = array();
        foreach ($rs as $file_record) {
            $info = array();
            $info['contextid'] = $file_record->contextid;
            $info['itemid'] = $file_record->itemid;
            $info['filearea'] = $file_record->filearea;
            $info['component'] = $file_record->component;
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
        $ret['nologin'] = true;
        $list = array();
        $files = $this->get_recent_files(0, $this->number);

        try {
            foreach ($files as $file) {
                $params = base64_encode(serialize($file));
                $node = array(
                    'title' => $file['filename'],
                    'size' => 0,
                    'date' => '',
                    'source'=> $params,
                    'thumbnail' => $OUTPUT->pix_url(file_extension_icon($file['filename'], 32))->out(false),
                );
                $list[] = $node;
            }
        } catch (Exception $e) {
            throw new repository_exception('emptyfilelist', 'repository_recent');
        }
        $ret['list'] = array_filter($list, array($this, 'filter'));
        return $ret;
    }

    public static function get_type_option_names() {
        return array('recentfilesnumber', 'pluginname');
    }

    public function type_config_form($mform) {
        parent::type_config_form($mform);
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
     * This function overwrite the default implement to copying file using file_storage
     *
     * @global object $USER
     * @global object $DB
     * @param string $encoded The information of file, it is base64 encoded php serialized data
     * @param string $draftitemid itemid
     * @param string $new_filename The intended name of file
     * @param string $new_filepath the new path in draft area
     * @return array The information of file
     */
    public function copy_to_area($encoded, $draftitemid, $new_filepath, $new_filename) {
        global $USER, $DB;

        $user_context = get_context_instance(CONTEXT_USER, $USER->id);

        $fs = get_file_storage();

        $params = unserialize(base64_decode($encoded));

        $contextid  = clean_param($params['contextid'], PARAM_INT);
        $fileitemid = clean_param($params['itemid'],    PARAM_INT);
        $filename   = clean_param($params['filename'],  PARAM_FILE);
        $filepath   = clean_param($params['filepath'],  PARAM_PATH);;
        $filearea   = clean_param($params['filearea'],  PARAM_ALPHAEXT);
        $component  = clean_param($params['component'], PARAM_ALPHAEXT);

        // XXX:
        // When user try to pick a file from other filearea, normally file api will use file browse to
        // operate the files with capability check, but in some areas, users don't have permission to
        // browse the files (for example, forum_attachment area).
        //
        // To get 'recent' plugin working, we need to use lower level file_stoarge class to bypass the
        // capability check, we will use a better workaround to improve it.
        if ($stored_file = $fs->get_file($contextid, $component, $filearea, $fileitemid, $filepath, $filename)) {
            // verify user id
            if ($USER->id != $stored_file->get_userid()) {
                throw new moodle_exception('errornotyourfile', 'repository');
            }
            $file_record = array('contextid'=>$user_context->id, 'component'=>'user', 'filearea'=>'draft',
                'itemid'=>$draftitemid, 'filepath'=>$new_filepath, 'filename'=>$new_filename, 'sortorder'=>0);

            // test if file already exists
            if (repository::draftfile_exists($draftitemid, $new_filepath, $new_filename)) {
                // create new file
                $unused_filename = repository::get_unused_filename($draftitemid, $new_filepath, $new_filename);
                $file_record['filename'] = $unused_filename;
                // create a tmp file
                $fs->create_file_from_storedfile($file_record, $stored_file);
                $event = array();
                $event['event'] = 'fileexists';
                $event['newfile'] = new stdClass;
                $event['newfile']->filepath = $new_filepath;
                $event['newfile']->filename = $unused_filename;
                $event['newfile']->url = moodle_url::make_draftfile_url($draftitemid, $new_filepath, $unused_filename)->out();
                $event['existingfile'] = new stdClass;
                $event['existingfile']->filepath = $new_filepath;
                $event['existingfile']->filename = $new_filename;
                $event['existingfile']->url      = moodle_url::make_draftfile_url($draftitemid, $new_filepath, $new_filename)->out();;
                return $event;
            } else {
                $fs->create_file_from_storedfile($file_record, $stored_file);
                $info = array();
                $info['title']  = $new_filename;
                $info['itemid'] = $draftitemid;
                $info['filesize']  = $stored_file->get_filesize();
                $info['url'] = moodle_url::make_draftfile_url($draftitemid, $new_filepath, $new_filename)->out();;
                $info['contextid'] = $user_context->id;
                return $info;
            }
        }
        return false;

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
