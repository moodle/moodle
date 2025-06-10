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
 * MUC support.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

/**
 * Class cache
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache {
    /**
     * @var \cache_application|null
     *
     */
    protected $muc = null;

    /**
     * @var null|cache
     */
    private static $instance = null;

    /**
     * cache constructor.
     */
    private function __construct() {
        if (local::duringtesting()) {
            return;
        }
        $this->muc = \cache::make('tool_ally', 'request');
    }

    /**
     * cache clone method.
     */
    private function __clone() {
        // Prevent cloning.
    }

    /**
     * Singleton instance getter.
     * @return cache|null
     */
    public static function instance() {
        if (self::$instance === null) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    /**
     * Retrieves elements from cache.
     * @param string $param
     * @return bool|string
     */
    public function get($param) {
        $result = false;
        if (local::duringtesting()) {
            return $result;
        }
        $result = $this->muc->get($param);
        return $result;
    }

    /**
     * Stores a value in the cache.
     * @param mixed $param
     * @param mixed $val
     * @return void
     */
    public function set($param, $val) {
        if (local::duringtesting()) {
            return;
        }
        $this->muc->set($param, $val);
    }

    /**
     * Deletes element from the cache.
     * @param mixed $param
     * @return int
     */
    public function delete($param) {
        if (local::duringtesting()) {
            return 0;
        }
        return $this->muc->delete($param);
    }

    /**
     * Deletes file keys associated with file.
     * @param \stored_file $file
     * @return bool true if succesful, false otherwise
     */
    public function invalidate_file_keys($file) {
        $contextid = $file->get_contextid();
        $component = $file->get_component();
        $filearea = $file->get_filearea();
        $itemid = $file->get_itemid();

        $itempath = "/$contextid/$component/$filearea/$itemid";
        $areakey = sha1($itempath);

        return $this->delete($areakey);
    }
}
