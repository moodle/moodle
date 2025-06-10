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
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\services;

use local_intellidata\helpers\StorageHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class storage_service {

    /**
     * @var \local_intellidata\repositories\database_storage_repository|
     * \local_intellidata\repositories\file_storage_repository|null
     */
    protected $repo = null;

    /**
     * Storage service construct.
     *
     * @param $datatype
     * @throws \dml_exception
     */
    public function __construct($datatype) {
        $this->repo = StorageHelper::get_storage_service($datatype);
    }

    /**
     * Save data.
     *
     * @param $data
     */
    public function save_data($data) {
        $this->repo->save_data($data);
    }

    /**
     * Save file.
     *
     * @return \stored_file|null
     */
    public function save_file() {
        return $this->repo->save_file();
    }

    /**
     * Update timemodified files.
     *
     * @param int $timemodified
     * @return void
     */
    public function update_timemodified_files($timemodified) {
        $this->repo->update_timemodified_files($timemodified);
    }

    /**
     * Get files.
     *
     * @param array $params
     * @return array
     */
    public function get_files($params = []) {
        return $this->repo->get_files($params);
    }

    /**
     * Delete files.
     *
     * @param array $params
     * @return int|void
     */
    public function delete_files($params = []) {
        return $this->repo->delete_files($params);
    }

    /**
     * Delete temp files.
     *
     * @return bool
     */
    public function delete_temp_files() {
        return $this->repo->delete_temp_files();
    }
}
