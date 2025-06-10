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
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories\config;

use local_intellidata\persistent\datatypeconfig;


/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class database_config_repository {

    /**
     * Get config from DB.
     *
     * @param $params
     * @return array
     * @throws \coding_exception
     */
    public function get_config($params = []) {
        $dbconfig = datatypeconfig::get_records($params);

        $config = [];

        if (count($dbconfig)) {
            foreach ($dbconfig as $conf) {
                $confdata = $conf->to_record();
                $confdata->params = $conf->get('params');

                $config[$conf->get('datatype')] = $confdata;
            }
        }

        return $config;
    }

    /**
     * Returns optional datatypes list.
     *
     * @param array $params
     * @return config[]
     */
    public function get_optional_datatypes($status = null) {
        $config = [];
        $params = ['tabletype' => datatypeconfig::TABLETYPE_OPTIONAL];

        if ($status !== null) {
            $params['status'] = $status;
        }

        $dbconfig = datatypeconfig::get_records($params);

        if (count($dbconfig)) {
            foreach ($dbconfig as $conf) {
                $config[$conf->get('datatype')] = $conf->to_record();
            }
        }

        return $config;
    }

    /**
     * Returns logs datatypes list.
     *
     * @param array $params
     * @return config[]
     */
    public static function get_logs_datatypes($status = null) {
        $config = [];
        $params = ['tabletype' => datatypeconfig::TABLETYPE_LOGS];

        if ($status !== null) {
            $params['status'] = $status;
        }

        $dbconfig = datatypeconfig::get_records($params);

        if (count($dbconfig)) {
            foreach ($dbconfig as $conf) {
                $configdata = $conf->to_record();
                $configdata->params = $conf->get('params');

                $config[$conf->get('datatype')] = $configdata;
            }
        }

        return $config;
    }

    /**
     * Cache configuration.
     *
     * @param string $key
     * @return config[]
     */
    public function cache_config($key = 'alldatatypes') {
        return [];
    }

}
