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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories;

use local_intellidata\helpers\StorageHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class file_storage_repository {

    /**
     * Storage folder name.
     */
    const STORAGE_FOLDER_NAME = 'ibdata';

    /**
     * The name of the folder for storing files by user.
     */
    const STORAGE_FOLDER_BY_USER_NAME = 'byusers';

    /**
     * Storage file type.
     */
    const STORAGE_FILE_TYPE = 'csv';
    /**
     * Storage files component.
     */
    const STORAGE_FILES_COMPONENT = 'local_intellidata';

    /** @var string|null */
    public $storagefolder = null;
    /** @var string|null */
    public $storagefile = null;
    /** @var string|null */
    public $datatype = null;

    /**
     * File storage repository construct.
     *
     * @param $datatype
     */
    public function __construct($datatype = null) {
        $this->datatype = $datatype;
        $this->storagefolder = self::get_storage_folder();
        $this->storagefile = $datatype ? self::get_storage_file() : null;
    }

    /**
     * Get storage folder path.
     *
     * @return false|string
     */
    protected function get_storage_folder() {
        return make_temp_directory(self::STORAGE_FOLDER_NAME);
    }

    /**
     * Get storing files by user folder path.
     *
     * @return void
     */
    protected function make_storage_datatype_by_user_folder() {
        make_temp_directory(self::STORAGE_FOLDER_NAME . '/' . $this->datatype['name'] . '_' . self::STORAGE_FOLDER_BY_USER_NAME);
    }

    /**
     * Get storage file path.
     *
     * @return string
     */
    public function get_storage_file($withuser = true) {
        return $this->storagefolder . '/' . $this->get_file_name($withuser);
    }

    /**
     * Get storage file name path.
     *
     * @return string
     */
    public function get_file_name($withuser = true) {
        global $USER;

        if ($withuser) {
            $foldername = $this->datatype['name'] . '_' . self::STORAGE_FOLDER_BY_USER_NAME;
            $userid = !empty($USER->id) ? $USER->id : '';
            return $foldername . '/user_' . $userid . '.' . self::STORAGE_FILE_TYPE;
        }

        return $this->datatype['name'] . '.' . self::STORAGE_FILE_TYPE;
    }

    /**
     * Get temp file path.
     *
     * @return string
     */
    public function get_temp_file() {
        return $this->storagefolder . '/' . $this->datatype['name'] . '_temp.' . self::STORAGE_FILE_TYPE;
    }

    /**
     * Save data to the file.
     *
     * @param $data
     * @throws \moodle_exception
     */
    public function save_data($data) {
        $this->make_storage_datatype_by_user_folder();

        StorageHelper::save_in_file($this->storagefile, $data);
    }

    /**
     * Save file to the storage.
     *
     * @return \stored_file|null
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \moodle_exception
     * @throws \stored_file_creation_exception
     */
    public function save_file() {

        $tempfile = $this->get_temp_file();

        $filesbyuser = $this->storagefolder . '/' . $this->datatype['name'] . '_' . self::STORAGE_FOLDER_BY_USER_NAME . '/user_*.csv';
        $files = glob($filesbyuser);
        $storagefile = $this->get_storage_file(false);
        if (empty($files)) {
            return null;
        }

        exec('for f in ' . $filesbyuser . '; do echo; cat $f; rm $f; done | tail -n +2 >> ' . $storagefile . '; ');

        // Rename temp file to process.
        StorageHelper::rename_file($storagefile, $tempfile);

        // Save file to filedir and database.
        $params = [
            'datatype' => $this->datatype['name'],
            'filename' => StorageHelper::generate_filename(),
            'tempdir' => $this->storagefolder,
            'tempfile' => $tempfile,
        ];

        if ($this->datatype['rewritable']) {
            $this->delete_files();
        }

        return StorageHelper::save_file($params);
    }

    /**
     * Retrieve files list.
     *
     * @param array $params
     * @return array
     * @throws \dml_exception
     */
    public function get_files($params = []) {
        global $CFG;
        require_once("$CFG->libdir/externallib.php");

        $context = \context_system::instance();
        $component = self::STORAGE_FILES_COMPONENT;

        $timestart = (!empty($params['timestart'])) ? $params['timestart'] : 0;
        $timeend = (!empty($params['timeend'])) ? $params['timeend'] : 0;

        return self::get_area_files(
            $context->id, $component, $this->datatype['name'], false, "timecreated", true, $timestart, $timeend
        );
    }

    /**
     * Retrieve files list from storage.
     *
     * @param $contextid
     * @param $component
     * @param $filearea
     * @param false $itemid
     * @param string $sort
     * @param bool $includedirs
     * @param int $timestart
     * @param int $timeend
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $order
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_area_files($contextid, $component, $filearea, $itemid = false, $sort = "timecreated",
                                   $includedirs = true, $timestart = 0, $timeend = 0, $limitfrom = 0,
                                   $limitnum = 0, $order = 'ASC') {
        global $DB;

        $fs = get_file_storage();

        list($areasql, $conditions) = $DB->get_in_or_equal($filearea, SQL_PARAMS_NAMED);
        $conditions['contextid'] = $contextid;
        $conditions['component'] = $component;

        if ($itemid !== false && is_array($filearea)) {
            throw new coding_exception('You cannot specify multiple fileareas as well as an itemid.');
        } else if ($itemid !== false) {
            $itemidsql = ' AND f.itemid = :itemid ';
            $conditions['itemid'] = $itemid;
        } else {
            $itemidsql = '';
        }

        $timefiltersql = '';
        if (!empty($timestart)) {
            $conditions['timestart'] = $timestart;
            $timefiltersql .= 'AND f.timemodified >= :timestart';
        }
        if (!empty($timeend)) {
            $conditions['timeend'] = $timeend;
            $timefiltersql .= ' AND f.timemodified <= :timeend';
        }

        $includedirssql = '';
        if (!$includedirs) {
            $includedirssql = 'AND f.filename != :dot';
            $conditions['dot'] = '.';
        }

        if ($limitfrom && !$limitnum) {
            throw new coding_exception('If specifying $limitfrom you must also specify $limitnum');
        }

        $sql = "SELECT ".self::instance_sql_fields('f', 'r')."
                  FROM {files} f
             LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE f.contextid = :contextid
                       AND f.component = :component
                       AND mimetype IS NOT NULL
                       AND f.filearea $areasql
                       $includedirssql
                       $timefiltersql
                       $itemidsql";
        if (!empty($sort)) {
            $sql .= " ORDER BY {$sort} {$order}";
        }

        $files = [];
        $filerecords = $DB->get_records_sql($sql, $conditions, $limitfrom, $limitnum);
        foreach ($filerecords as $filerecord) {
            $areafile = $fs->get_file_instance($filerecord);

            $file = [];
            $file['filename'] = $areafile->get_filename();
            $file['filearea'] = $areafile->get_filearea();
            $file['itemid'] = $areafile->get_itemid();
            $file['filepath'] = $areafile->get_filepath();
            $file['mimetype'] = $areafile->get_mimetype();
            $file['filesize'] = $areafile->get_filesize();
            $file['timemodified'] = $areafile->get_timemodified();
            $file['isexternalfile'] = $areafile->is_external_file();
            if ($file['isexternalfile']) {
                $file['repositorytype'] = $areafile->get_repository_type();
            }
            $fileitemid = $areafile->get_itemid();
            $file['fileurl'] = \moodle_url::make_webservice_pluginfile_url($contextid, $component, $filearea,
                $fileitemid, $areafile->get_filepath(), $areafile->get_filename())->out(false);
            $files[] = $file;
        }

        return $files;
    }

    /**
     * Get sql to retrieve fiels from storage.
     *
     * Get the sql formated fields for a file instance to be created from a
     * {files} and {files_refernece} join.
     *
     * @param string $filesprefix the table prefix for the {files} table
     * @param string $filesreferenceprefix the table prefix for the {files_reference} table
     * @return string the sql to go after a SELECT
     */
    private static function instance_sql_fields($filesprefix, $filesreferenceprefix) {
        $filefields = [
            'contenthash', 'pathnamehash', 'contextid', 'component', 'filearea',
            'itemid', 'filepath', 'filename', 'userid', 'filesize', 'mimetype', 'status', 'source',
            'author', 'license', 'timecreated', 'timemodified', 'sortorder', 'referencefileid',
        ];

        $referencefields = [
            'repositoryid' => 'repositoryid',
            'reference' => 'reference',
            'lastsync' => 'referencelastsync',
        ];
        $fields = [];
        $fields[] = $filesprefix.'.id AS id';
        foreach ($filefields as $field) {
            $fields[] = "{$filesprefix}.{$field}";
        }

        foreach ($referencefields as $field => $alias) {
            $fields[] = "{$filesreferenceprefix}.{$field} AS {$alias}";
        }

        return implode(', ', $fields);
    }


    /**
     * Update time modified files.
     *
     * @param int $timemodified
     *
     * @return void
     * @throws \dml_exception
     */
    public function update_timemodified_files($timemodified) {
        global $DB;

        $context = \context_system::instance();

        $conditions = [
            'contextid = :contextid',
            'component = :component',
            'filearea = :filearea',
        ];
        $sqlparams = [
            'contextid' => $context->id,
            'component' => self::STORAGE_FILES_COMPONENT,
            'filearea' => $this->datatype['name'],
        ];

        $wheresql = implode(' AND ', $conditions);

        $filerecords = $DB->get_records_sql(
            "SELECT *
                   FROM {files}
                  WHERE $wheresql
               ORDER BY itemid",
            $sqlparams
        );

        if ($filerecords) {
            foreach ($filerecords as $filerecord) {
                $filerecord->timemodified = $timemodified;
                $DB->update_record('files', $filerecord);
            }
        }
    }

    /**
     * Delete files from storage.
     *
     * @param null $params
     * @return int
     * @throws \dml_exception
     */
    public function delete_files($params = null) {
        global $DB;

        $fs = get_file_storage();
        $context = \context_system::instance();

        $conditions = [
            'contextid = :contextid',
            'component = :component',
        ];

        $sqlparams = [
            'contextid' => $context->id,
            'component' => self::STORAGE_FILES_COMPONENT,
        ];

        if (!empty($this->datatype['name'])) {
            $conditions[] = 'filearea = :filearea';
            $sqlparams['filearea'] = $this->datatype['name'];
        }

        if (!empty($params['timemodified'])) {
            $conditions[] = 'timemodified < :timemodified';
            $sqlparams['timemodified'] = $params['timemodified'];
        }

        if (!empty($params['itemid'])) {
            $conditions[] = 'itemid = :itemid';
            $sqlparams['itemid'] = $params['itemid'];
        }

        $wheresql = implode(' AND ', $conditions);

        $filerecords = $DB->get_records_sql(
            "SELECT *
                   FROM {files}
                  WHERE $wheresql
               ORDER BY itemid",
            $sqlparams
        );

        foreach ($filerecords as $filerecord) {
            $fs->get_file_instance($filerecord)->delete();
        }

        return count($filerecords);
    }

    /**
     * Delete files from temp folder.
     *
     * @return bool
     */
    public function delete_temp_files() {

        // Delete temp files.
        return StorageHelper::delete_all_files($this->storagefolder);
    }
}
