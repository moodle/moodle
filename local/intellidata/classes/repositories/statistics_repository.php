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

namespace local_intellidata\repositories;

use local_intellidata\helpers\ParamsHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class statistics_repository {

    /**
     * Get general statistics for current site.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_site_info() {
        global $USER, $CFG, $DB;

        $params = [];

        $params['email'] = (isset($USER->email)) ? $USER->email : '';
        $params['url'] = $CFG->wwwroot;
        $params['lang'] = current_language();
        $params['dbtype'] = $CFG->dbtype;
        $params['moodle'] = $CFG->version;
        $params['version'] = ParamsHelper::get_plugin_version();
        $params['courses_count'] = $DB->count_records("course", ["visible" => 1]);
        $params['users_count'] = $DB->count_records_sql("SELECT COUNT(id) FROM {user} WHERE deleted = 0 AND id > 1");

        return $params;
    }
}
