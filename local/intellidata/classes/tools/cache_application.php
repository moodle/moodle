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
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\tools;

use cache_application as moodle_cache_application;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class cache_application extends moodle_cache_application {

    /**
     * Purge.
     *
     * @return bool
     */
    public function purge() {
        return true;
    }

    /**
     * Delete many.
     *
     * @param array $keys
     * @param bool $recurse
     * @return bool
     */
    public function delete_many(array $keys, $recurse = true) {
        return true;
    }

    /**
     * Get all_keys.
     *
     * @return array
     */
    public function get_all_keys() {
        $rawkeys = $this->get_store()->find_all();
        $keys = [];
        foreach ($rawkeys as $rawkey) {
            $keys[] = explode('-', $rawkey)[0];
        }

        return $keys;
    }

    /**
     * Store supports get all keys.
     *
     * @return bool
     */
    public function store_supports_get_all_keys() {
        return method_exists($this->get_store(), 'find_all');
    }

}
