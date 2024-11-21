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
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\tools;

use cache_application as moodle_cache_application;

class cache_application extends moodle_cache_application {

    public function purge() {
        return true;
    }

    public function delete_many(array $keys, $recurse = true) {
        return true;
    }

    public function get_all_keys() {
        $rawkeys = $this->get_store()->find_all();
        $keys = [];
        foreach ($rawkeys as $rawkey) {
            $keys[] = explode('-', $rawkey)[0];
        }

        return $keys;
    }

}