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

use core_cache\versionable_data_source_interface;

/**
 * A dummy datasource which supports versioning.
 *
 * @package core_cache
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_phpunit_dummy_datasource_versionable extends cache_phpunit_dummy_datasource implements
    versionable_data_source_interface
{
    /** @var array Data in cache */
    protected $data = [];

    /** @var cache_phpunit_dummy_datasource_versionable Last created instance */
    protected static $lastinstance;

    /**
     * Returns an instance of this object for use with the cache.
     *
     * @param cache_definition $definition
     * @return cache_phpunit_dummy_datasource New object
     */
    public static function get_instance_for_cache(cache_definition $definition): cache_phpunit_dummy_datasource_versionable {
        self::$lastinstance = new cache_phpunit_dummy_datasource_versionable();
        return self::$lastinstance;
    }

    /**
     * Gets the last instance that was created.
     *
     * @return cache_phpunit_dummy_datasource_versionable
     */
    public static function get_last_instance(): cache_phpunit_dummy_datasource_versionable {
        return self::$lastinstance;
    }

    /**
     * Sets up the datasource so that it has a value for a particular key.
     *
     * @param string $key Key
     * @param int $version Version for key
     * @param mixed $data
     */
    public function has_value(string $key, int $version, $data): void {
        $this->data[$key] = new \core_cache\version_wrapper($data, $version);
    }

    /**
     * Loads versioned data.
     *
     * @param int|string $key Key
     * @param int $requiredversion Minimum version number
     * @param mixed $actualversion Should be set to the actual version number retrieved
     * @return mixed Data retrieved from cache or false if none
     */
    public function load_for_cache_versioned($key, int $requiredversion, &$actualversion) {
        if (!array_key_exists($key, $this->data)) {
            return false;
        }
        $value = $this->data[$key];
        if ($value->version < $requiredversion) {
            return false;
        }
        $actualversion = $value->version;
        return $value->data;
    }
}
