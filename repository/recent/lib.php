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
 * This plugin is used to access recent used files
 *
 * @since 2.0
 * @package    repository_recent
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_recent class is used to browse recent used files
 *
 * @since 2.0
 * @package    repository_recent
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
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
        // This SQL will ignore draft files if not owned by current user.
        // Ignore all file references.
        $sql = 'SELECT files1.*
                  FROM {files} files1
             LEFT JOIN {files_reference} r
                       ON files1.referencefileid = r.id
                  JOIN (
                      SELECT contenthash, filename, MAX(id) AS id
                        FROM {files}
                       WHERE userid = :userid
                         AND filename != :filename
                         AND ((filearea = :filearea1 AND itemid = :itemid) OR filearea != :filearea2)
                    GROUP BY contenthash, filename
                  ) files2 ON files1.id = files2.id
                 WHERE r.repositoryid is NULL
              ORDER BY files1.timemodified DESC';
        $params = array(
            'userid' => $USER->id,
            'filename' => '.',
            'filearea1' => 'draft',
            'itemid' => $itemid,
            'filearea2' => 'draft');
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
        global $OUTPUT;
        $ret = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = true;
        $ret['nologin'] = true;
        $list = array();
        $files = $this->get_recent_files(0, $this->number);

        try {
            foreach ($files as $file) {
                // Check that file exists and accessible, retrieve size/date info
                $browser = get_file_browser();
                $context = get_context_instance_by_id($file['contextid']);
                $fileinfo = $browser->get_file_info($context, $file['component'],
                        $file['filearea'], $file['itemid'], $file['filepath'], $file['filename']);
                if ($fileinfo) {
                    $params = base64_encode(serialize($file));
                    $node = array(
                        'title' => $fileinfo->get_visible_name(),
                        'size' => $fileinfo->get_filesize(),
                        'datemodified' => $fileinfo->get_timemodified(),
                        'datecreated' => $fileinfo->get_timecreated(),
                        'author' => $fileinfo->get_author(),
                        'license' => $fileinfo->get_license(),
                        'source'=> $params,
                        'icon' => $OUTPUT->pix_url(file_file_icon($fileinfo, 24))->out(false),
                        'thumbnail' => $OUTPUT->pix_url(file_file_icon($fileinfo, 90))->out(false),
                    );
                    if ($imageinfo = $fileinfo->get_imageinfo()) {
                        $fileurl = new moodle_url($fileinfo->get_url());
                        $node['realthumbnail'] = $fileurl->out(false, array('preview' => 'thumb', 'oid' => $fileinfo->get_timemodified()));
                        $node['realicon'] = $fileurl->out(false, array('preview' => 'tinyicon', 'oid' => $fileinfo->get_timemodified()));
                        $node['image_width'] = $imageinfo['width'];
                        $node['image_height'] = $imageinfo['height'];
                    }
                    $list[] = $node;
                }
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

    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form($mform, $classname);
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
     * @param string $encoded The information of file, it is base64 encoded php serialized data
     * @param stdClass|array $filerecord contains itemid, filepath, filename and optionally other
     *      attributes of the new file
     * @param int $maxbytes maximum allowed size of file, -1 if unlimited. If size of file exceeds
     *      the limit, the file_exception is thrown.
     * @return array The information of file
     */
    public function copy_to_area($encoded, $filerecord, $maxbytes = -1) {
        global $USER;

        $user_context = get_context_instance(CONTEXT_USER, $USER->id);

        $filerecord = (array)$filerecord;
        // make sure the new file will be created in user draft area
        $filerecord['component'] = 'user'; // make sure
        $filerecord['filearea'] = 'draft'; // make sure
        $filerecord['contextid'] = $user_context->id;
        $filerecord['sortorder'] = 0;
        $draftitemid = $filerecord['itemid'];
        $new_filepath = $filerecord['filepath'];
        $new_filename = $filerecord['filename'];

        $fs = get_file_storage();

        $params = unserialize(base64_decode($encoded));

        $contextid  = clean_param($params['contextid'], PARAM_INT);
        $fileitemid = clean_param($params['itemid'],    PARAM_INT);
        $filename   = clean_param($params['filename'],  PARAM_FILE);
        $filepath   = clean_param($params['filepath'],  PARAM_PATH);;
        $filearea   = clean_param($params['filearea'],  PARAM_AREA);
        $component  = clean_param($params['component'], PARAM_COMPONENT);

        // XXX:
        // When user try to pick a file from other filearea, normally file api will use file browse to
        // operate the files with capability check, but in some areas, users don't have permission to
        // browse the files (for example, forum_attachment area).
        //
        // To get 'recent' plugin working, we need to use lower level file_stoarge class to bypass the
        // capability check, we will use a better workaround to improve it.
        // TODO MDL-33297 apply here
        if ($stored_file = $fs->get_file($contextid, $component, $filearea, $fileitemid, $filepath, $filename)) {
            // verify user id
            if ($USER->id != $stored_file->get_userid()) {
                throw new moodle_exception('errornotyourfile', 'repository');
            }
            if ($maxbytes !== -1 && $stored_file->get_filesize() > $maxbytes) {
                throw new file_exception('maxbytes');
            }

            // test if file already exists
            if (repository::draftfile_exists($draftitemid, $new_filepath, $new_filename)) {
                // create new file
                $unused_filename = repository::get_unused_filename($draftitemid, $new_filepath, $new_filename);
                $filerecord['filename'] = $unused_filename;
                // create a tmp file
                $fs->create_file_from_storedfile($filerecord, $stored_file);
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
                $fs->create_file_from_storedfile($filerecord, $stored_file);
                $info = array();
                $info['title']  = $new_filename;
                $info['file']  = $new_filename;
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
