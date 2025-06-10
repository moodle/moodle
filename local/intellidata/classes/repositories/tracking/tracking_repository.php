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

namespace local_intellidata\repositories\tracking;

use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\PageParamsHelper;
use local_intellidata\helpers\SettingsHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class tracking_repository {

    /**
     * Type live.
     */
    const TYPE_LIVE = 0;
    /**
     * Type cache,
     */
    const TYPE_CACHE = 1;
    /**
     * Type file.
     */
    const TYPE_FILE = 2;

    /**
     * Create tracking record.
     *
     * @param $pageparams
     * @param false $ajaxrequest
     * @throws \dml_exception
     */
    public static function create_record($pageparams, $ajaxrequest = false) {
        global $USER;
        $compresstracking = SettingsHelper::get_setting('compresstracking');

        $data = new \stdClass();
        $data->userid       = $USER->id;
        $data->courseid     = PageParamsHelper::get_courseid($pageparams);
        $data->page         = $pageparams['page'];
        $data->param        = $pageparams['param'];
        $data->timespend    = $pageparams['time'];
        $data->useragent    = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        $data->ip = ($ip) ? $ip : 0;

        try {
            $storage = self::get_storage($compresstracking, $ajaxrequest);
            $storage->save_data($data);
        } catch (\Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }
    }

    /**
     * Export record to storage.
     *
     * @throws \dml_exception
     */
    public static function export_records() {
        $compresstracking = SettingsHelper::get_setting('compresstracking');

        try {
            $storage = self::get_storage($compresstracking);
            $storage->export_data();
        } catch (\Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }
    }

    /**
     * Get storage for tracking.
     *
     * @param $compresstracking
     * @param false $ajaxrequest
     * @return cache_storage_repository|file_storage_repository|live_storage_repository|void
     */
    public static function get_storage($compresstracking, $ajaxrequest = false) {
        switch ($compresstracking){
            case self::TYPE_FILE:
                return new file_storage_repository($ajaxrequest);
            case self::TYPE_LIVE:
                return new live_storage_repository($ajaxrequest);
            case self::TYPE_CACHE:
            default:
                return new cache_storage_repository($ajaxrequest);
        }
    }
}
