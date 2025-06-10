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

namespace local_intellidata\helpers;

use local_intellidata\repositories\config_repository;
use local_intellidata\services\config_service;
use local_intellidata\services\datatypes_service;
use local_intellidata\repositories\export_log_repository;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class DatatypesHelper {

    /**
     * Delete excluded tables.
     *
     * @return void
     * @throws \coding_exception
     */
    public static function delete_excluded_tables() {

        $configservice = new config_service();
        $config = $configservice->get_config();

        $exportlogrepository = new export_log_repository();

        $datatypestodelete = array_diff(
            array_keys($config),
            array_keys(datatypes_service::get_all_optional_datatypes())
        );

        if (count($datatypestodelete)) {
            foreach ($datatypestodelete as $datatype) {
                if (isset($config[$datatype]) &&
                    datatypes_service::is_optional($config[$datatype]->datatype, $config[$datatype]->tabletype)) {

                    // Delete table from config.
                    $configservice->delete_config($datatype);

                    // Delete table from export.
                    $exportlogrepository->remove_datatype($datatype);
                }
            }
        }

        $configservice->cache_config();
    }

    /**
     * Reset datatype.
     *
     * @param string $datatype
     *
     * @return void
     * @throws \coding_exception
     */
    public static function reset_datatype($datatype) {
        $configrepository = new config_repository();
        $record = $configrepository->get_record(['datatype' => $datatype]);
        if (!$record) {
            return;
        }

        (new config_service())->reset_config_datatype($record);
    }
}
