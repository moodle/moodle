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

use local_intellidata\helpers\SettingsHelper;
use local_intellidata\persistent\datatypeconfig;
use local_intellidata\repositories\config\database_config_repository;
use local_intellidata\repositories\config\cache_config_repository;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class config_repository {

    /**
     * @var cache_config_repository|database_config_repository|null
     */
    public $repo = null;

    /**
     * Config repository construct.
     */
    public function __construct() {
        $this->repo = self::get_repository();
    }

    /**
     * Get repository.
     *
     * @return cache_config_repository|database_config_repository
     * @throws \dml_exception
     */
    public static function get_repository() {
        return (SettingsHelper::get_setting('cacheconfig'))
            ? new cache_config_repository()
            : new database_config_repository();
    }

    /**
     * Get configuration.
     *
     * @param array $params
     * @return config[]
     */
    public function get_config($params = []) {
        return $this->repo->get_config($params);
    }

    /**
     * Returns optional datatypes list.
     *
     * @param array $params
     * @return config[]
     */
    public static function get_optional_datatypes($status = null) {
        return (self::get_repository())->get_optional_datatypes($status);
    }

    /**
     * Returns logs datatypes list.
     *
     * @param array $params
     * @return config[]
     */
    public static function get_logs_datatypes($status = null) {
        return (self::get_repository())->get_logs_datatypes($status);
    }

    /**
     * Get config record from DB.
     *
     * @param $params
     * @return false|datatypeconfig
     */
    public function get_record($params = []) {
        return datatypeconfig::get_record($params);
    }

    /**
     * Saves config to DB.
     *
     * @param $datatype
     * @param $data
     * @return mixed
     */
    public function save($datatype, $data) {

        $recordid = 0;
        if ($record = $this->get_record(['datatype' => $datatype])) {
            $recordid = $record->get('id');
        }

        $config = new datatypeconfig($recordid, $data);
        $config->save();

        return $config->to_record();
    }

    /**
     * Delete config for specific datatype.
     *
     * @param $datatype
     * @return bool
     * @throws \coding_exception
     */
    public function delete($datatype) {

        if ($record = $this->get_record(['datatype' => $datatype])) {
            return (new datatypeconfig($record->get('id')))->delete();
        }

        return false;
    }

    /**
     * Enable config for specific datatype.
     *
     * @param $datatype
     * @return bool
     * @throws \coding_exception
     */
    public function enable(string $datatype) {
        if ($record = $this->get_record(['datatype' => $datatype])) {
            $record->set('status', datatypeconfig::STATUS_ENABLED);
            $record->save();

            $this->cache_config();
        }
    }

    /**
     * Cache config.
     *
     * @return void
     * @throws \coding_exception
     */
    public function cache_config() {
        return $this->repo->cache_config();
    }
}
