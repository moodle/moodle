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
 * An APCu cache for AWS.
 *
 * @package   local_aws_sdk
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_aws_sdk;

use Aws\CacheInterface;

/**
 * An APCu cache for AWS.
 *
 * @package   local_aws_sdk
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aws_apcu_cache implements CacheInterface {
    /**
     * Determine if this cache is usable.
     *
     * @return bool
     */
    public static function are_requirements_met() {
        // APCu on the CLI does not persist cached values between CLI runs.
        // So, rather pointless to use on the CLI.
        if (CLI_SCRIPT && !PHPUNIT_TEST) {
            return false;
        }
        if (!extension_loaded('apcu') || !ini_get('apc.enabled')) {
            return false;
        }

        return true;
    }

    /**
     * Get a cache item by key.
     *
     * @param string $key Key to retrieve.
     *
     * @return mixed|null Returns the value or null if not found.
     */
    public function get($key) {
        $success = false;
        $value   = apcu_fetch($key, $success);

        return $success ? $value : null;
    }

    /**
     * Set a cache key value.
     *
     * @param string $key Key to set
     * @param mixed $value Value to set.
     * @param int $ttl Number of seconds the item is allowed to live. Set
     *                      to 0 to allow an unlimited lifetime.
     */
    public function set($key, $value, $ttl = 0) {
        $success = apcu_store($key, $value, $ttl);

        if (!$success) {
            debugging('Failed to store cached value in APCu');
        }
    }

    /**
     * Remove a cache key.
     *
     * @param string $key Key to remove.
     */
    public function remove($key) {
        apcu_delete($key);
    }
}
