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
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

use local_intellidata\helpers\DBManagerHelper;
use local_intellidata\services\datatypes_service;
use local_intellidata\helpers\DBHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\export_id_repository;
use local_intellidata\repositories\config_repository;
use local_intellidata\helpers\DebugHelper;

/**
 * Local intellidata plugin uninstallation.
 *
 * @return void
 */
function xmldb_local_intellidata_uninstall() {

    // Remove custom indexes.
    $configrepository = new config_repository();
    $configs = $configrepository->get_config();

    if (count($configs)) {
        $datatypes = datatypes_service::get_all_datatypes();

        foreach ($configs as $config) {
            if (!empty($config->tableindex) && !empty($datatypes[$config->datatype]['table'])) {
                DBManagerHelper::delete_index(
                    $datatypes[$config->datatype]['table'],
                    $config->tableindex
                );
            }
        }
    }

}
