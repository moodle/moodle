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

use local_intellidata\helpers\ParamsHelper;
use local_intellidata\persistent\datatypeconfig;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class cache_config_repository {

    /** @var \cache */
    private $cache = null;

    /** @var string */
    private $alldatatypes = 'alldatatypes';

    /**
     * cache_config_repository constructor.
     */
    public function __construct() {
        $this->cache = \cache::make(ParamsHelper::PLUGIN, 'config');
    }

    /**
     * Create or retrieve configuration.
     *
     * @param array $params
     * @return config[]
     */
    public function get_datatypes($key) {
        $datatypes = $this->cache->get($key);

        if (!$datatypes) {
            $datatypes = $this->cache_config($key);
        }

        return $datatypes;
    }

    /**
     * Get configuration.
     *
     * @param array $params
     * @return config[]
     */
    public function get_config($params = []) {
        return $this->get_datatypes($this->alldatatypes);
    }

    /**
     * Cache configuration.
     *
     * @param string $key
     * @return config[]
     */
    public function cache_config($key = 'alldatatypes') {

        $databaseconfigrepository = new database_config_repository();
        $config = $databaseconfigrepository->get_config();

        // All datatypes config.
        $configtocache = [
            $this->alldatatypes => $config,
        ];

        // Optional datatypes config.
        $optionaldatatypes = $config;
        array_map(function ($key) use (&$optionaldatatypes) {
            if ($optionaldatatypes[$key]->tabletype != datatypeconfig::TABLETYPE_OPTIONAL) {
                unset($optionaldatatypes[$key]);
            }
        }, array_keys($optionaldatatypes));
        $configtocache[datatypeconfig::TABLETYPE_OPTIONAL] = $optionaldatatypes;

        // Logs datatypes config.
        $logsdatatypes = $config;
        array_map(function ($key) use (&$logsdatatypes) {
            if ($logsdatatypes[$key]->tabletype != datatypeconfig::TABLETYPE_LOGS) {
                unset($logsdatatypes[$key]);
            }
        }, array_keys($logsdatatypes));
        $configtocache[datatypeconfig::TABLETYPE_LOGS] = $logsdatatypes;

        // Save cache.
        $this->cache->set_many($configtocache);

        return $configtocache[$key];
    }

    /**
     * Returns optional datatypes list.
     *
     * @param array $params
     * @return config[]
     */
    public function get_optional_datatypes($status = null) {

        $config = $this->get_datatypes(datatypeconfig::TABLETYPE_OPTIONAL);

        if ($status !== null) {
            array_map(function($key) use (&$config, $status) {
                if ($config[$key]->status != $status) {
                    unset($config[$key]);
                }
            }, array_keys($config));
        }

        return $config;
    }

    /**
     * Returns logs datatypes list.
     *
     * @param array $params
     * @return config[]
     */
    public function get_logs_datatypes($status = null) {

        $config = $this->get_datatypes(datatypeconfig::TABLETYPE_LOGS);

        if ($status !== null) {
            array_map(function($key) use (&$config, $status) {
                if ($config[$key]->status != $status) {
                    unset($config[$key]);
                }
            }, array_keys($config));
        }

        return $config;
    }
}
