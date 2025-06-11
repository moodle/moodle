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
 * This class implements WIRIS StorageAndCache interface
 * to store WIRIS data on MUC and Moodle database.
 *
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlefilecache {
    /**
     * @var mixed $cache The cache instance.
     */
    private $cache;

    /**
     * @var mixed $area The area instance.
     */
    public $area;

    /**
     * @var mixed $module The module instance.
     */
    public $module;

    /**
     * Constructores for WIRIS file cache.
     * @param String $area   cache area.
     * @param String $module cache definition.
     */
    public function __construct($area, $module) {
        $this->area = $area;
        $this->module = $module;
        $this->cache = cache::make($area, $module);
    }

    /**
     * Delete the given key from the cache
     * @param key The key to delete.
     * @throws Error On unexpected exception.
     */
    public function delete($key) {
    }

    /**
     * Deletes all the data in the cache.
     * @throws moodle_exception failing purgue the cache.
     */
    // @codingStandardsIgnoreStart
    public function deleteAll()
    {
        // @codingStandardsIgnoreEnd
        if (!$this->cache->purgue()) {
            throw new moodle_exception(get_string('errordeletingcache', 'filter_wiris', $this->area), $this->module);
        }
    }

    /**
     * Retrieves the value for the given key for the cache.
     * @param key The key for for the data being requested.
     * @return Bytes The data retrieved from the cache. Returns null on cache miss or error.
     */
    public function get($key) {
        if ($data = $this->cache->get($key)) {
            return $data;
        } else {
            return null;
        }
    }

    /**
     * Stores a (key, value) pair to the cache.
     * @param key The key for the data being requested.
     * @param value The data to set against the key.
     * @throws moodle_exception when the data can't be written to the cache.
     */
    public function set($key, $value) {
        if (!$this->cache->set($key, $value)) {
            throw new moodle_exception(get_string('errorsavingcache', 'filter_wiris', $this->area), $this->module);
        }
    }
}
