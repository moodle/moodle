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
 * @since Moodle 2.0
 * @package    repository_recent
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_recent class is used to browse recent used files
 *
 * @since Moodle 2.0
 * @package    repository_recent
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('DEFAULT_RECENT_FILES_NUM', 50);

/**
 * DEFAULT_RECENT_FILES_TIME_LIMIT - default time limit.
 */
define('DEFAULT_RECENT_FILES_TIME_LIMIT', 6 * 4 * WEEKSECS);
class repository_recent extends repository {

    /** @var int only retrieve files within the time limit */
    protected $timelimit;

    /** @var int recent files number configuration. */
    protected $number;

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
        $timelimit = get_config('recent', 'recentfilestimelimit');
        $this->timelimit = (int)$timelimit;
    }

    /**
     * recent plugin doesn't require login, so list all files
     * @return mixed
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     * Only return files within the time limit
     *
     * @param int $limitfrom retrieve the files from
     * @param int $limit limit number of the files
     * @param int $timelimit only return files with the time limit
     * @return array list of recent files
     */
    private function get_recent_files($limitfrom = 0, $limit = DEFAULT_RECENT_FILES_NUM, $timelimit = 0) {
        // XXX: get current itemid
        global $USER, $DB, $itemid;
        $timelimitsql = '';
        if ($timelimit > 0) {
            $timelimitsql = "AND timemodified >= :timelimit";
            $timelimitparam = ['timelimit' => time() - $timelimit];
        }
        // This SQL will ignore draft files if not owned by current user.
        // Ignore all file references.
        $sql = "SELECT files1.id, files1.contextid, files1.component, files1.filearea,
                       files1.itemid, files1.filepath, files1.filename, files1.pathnamehash
                  FROM {files} files1
                  JOIN (
                      SELECT contenthash, filename, MAX(id) AS id
                        FROM {files}
                       WHERE userid = :userid
                         AND referencefileid is NULL
                         AND filename != :filename
                         AND ((filearea = :filearea1 AND itemid = :itemid) OR filearea != :filearea2)
                         $timelimitsql
                    GROUP BY contenthash, filename
                  ) files2 ON files1.id = files2.id
              ORDER BY files1.timemodified DESC";
        $params = array(
            'userid' => $USER->id,
            'filename' => '.',
            'filearea1' => 'draft',
            'itemid' => $itemid,
            'filearea2' => 'draft');
        if (isset($timelimitparam)) {
            $params = array_merge($params, $timelimitparam);
        }
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
        $files = $this->get_recent_files(0, $this->number, $this->timelimit);

        try {
            foreach ($files as $file) {
                // Check that file exists and accessible, retrieve size/date info
                $browser = get_file_browser();
                $context = context::instance_by_id($file['contextid']);
                $fileinfo = $browser->get_file_info($context, $file['component'],
                        $file['filearea'], $file['itemid'], $file['filepath'], $file['filename']);
                if ($fileinfo) {
                    $params = base64_encode(json_encode($file));
                    $node = array(
                        'title' => $fileinfo->get_visible_name(),
                        'size' => $fileinfo->get_filesize(),
                        'datemodified' => $fileinfo->get_timemodified(),
                        'datecreated' => $fileinfo->get_timecreated(),
                        'author' => $fileinfo->get_author(),
                        'license' => $fileinfo->get_license(),
                        'source'=> $params,
                        'icon' => $OUTPUT->image_url(file_file_icon($fileinfo))->out(false),
                        'thumbnail' => $OUTPUT->image_url(file_file_icon($fileinfo))->out(false),
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
        return array('recentfilesnumber', 'recentfilestimelimit', 'pluginname');
    }

    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form($mform, $classname);
        $number = get_config('repository_recent', 'recentfilesnumber');
        if (empty($number)) {
            $number = DEFAULT_RECENT_FILES_NUM;
        }
        $mform->addElement('text', 'recentfilesnumber', get_string('recentfilesnumber', 'repository_recent'));
        $mform->setType('recentfilesnumber', PARAM_INT);
        $mform->setDefault('recentfilesnumber', $number);

        $mform->addElement('duration', 'recentfilestimelimit',
            get_string('timelimit', 'repository_recent'), ['units' => [DAYSECS, WEEKSECS], 'optional' => true]);
        $mform->addHelpButton('recentfilestimelimit', 'timelimit', 'repository_recent');
        $mform->setDefault('recentfilestimelimit', DEFAULT_RECENT_FILES_TIME_LIMIT);
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
     * Repository method to make sure that user can access particular file.
     *
     * This is checked when user tries to pick the file from repository to deal with
     * potential parameter substitutions is request
     *
     * @todo MDL-33805 remove this function when recent files are managed correctly
     *
     * @param string $source
     * @return bool whether the file is accessible by current user
     */
    public function file_is_accessible($source) {
        global $USER;
        $reference = $this->get_file_reference($source);
        $file = self::get_moodle_file($reference);
        return (!empty($file) && $file->get_userid() == $USER->id);
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
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }
}
