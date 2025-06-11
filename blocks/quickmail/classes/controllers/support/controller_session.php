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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\controllers\support;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_cache;

class controller_session {

    public $controller_key;
    public $store;

    public static $cachestore = 'qm_controller_store';

    public function __construct($controllerkey) {
        $this->controller_key = $controllerkey;
        $this->store = block_quickmail_cache::store(self::$cachestore);
    }

    /**
     * Merges the given array of data into this controller session's currently set data
     *
     * @param array $data
     * @return void
     */
    public function add_data($data = []) {
        $current = $this->get_data();

        $this->store->put($this->controller_key, array_merge($current, $data));
    }

    /**
     * Returns this controller session's currently set data
     *
     * @param  string  $key  optional, a specific key within the store to return
     * @return mixed
     */
    public function get_data($key = null) {
        $data = $this->store->get($this->controller_key, []);

        if (empty($key)) {
            return $data;
        } else {
            return array_key_exists($key, $data) ? $data[$key] : null;
        }
    }

    /**
     * Reports whether or not the given key exists in the current session input data
     *
     * @param  string  $key
     * @return bool
     */
    public function has_data($key) {
        return in_array($key, array_keys($this->store->get($this->controller_key, [])));
    }

    /**
     * Removes the given key's value from current session input data if it exists
     *
     * @param  string  $key
     * @return void
     */
    public function forget_data($key) {
        $current = $this->get_data();

        if (array_key_exists($key, $current)) {
            unset($current[$key]);
        }

        $this->store->put($this->controller_key, $current);
    }

    /**
     * Deletes this controller session's currently set data
     *
     * @return void
     */
    public function clear() {
        $this->store->forget($this->controller_key);
    }

    /**
     * Deletes this controller session's currently set data, and then resets it
     *
     * @return void
     */
    public function reflash() {
        // Get the current session data for this controller session.
        $current = $this->get_data();

        // Clear the session data for this controller session.
        $this->clear();

        // If there was any data, add it back to the session.
        if ( ! empty($current)) {
            $this->add_data($current);
        }
    }

}
