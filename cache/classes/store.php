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
 * Cache store - base class
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are required in order to use caching.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract cache store base class.
 *
 * This class implements the cache_store interface that all caches store plugins are required in implement.
 * It then implements basic methods that likely won't need to be overridden by plugins.
 * It will also be used to implement any API changes that come about in the future.
 *
 * While it is not required that you extend this class it is highly recommended.
 *
 * @since 2.4
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class cache_store_base implements cache_store {

    /**
     * Returns true if the user can add an instance of the store plugin.
     *
     * @return bool
     */
    public static function can_add_instance() {
        return true;
    }

    /**
     * Returns true if the store instance guarantees data.
     *
     * @return bool
     */
    public function supports_data_guarantee() {
        return $this::get_supported_features() & self::SUPPORTS_DATA_GUARANTEE;
    }

    /**
     * Returns true if the store instance supports multiple identifiers.
     *
     * @return bool
     */
    public function supports_multiple_identifiers() {
        return $this::get_supported_features() & self::SUPPORTS_MULTIPLE_IDENTIFIERS;
    }

    /**
     * Returns true if the store instance supports native ttl.
     *
     * @return bool
     */
    public function supports_native_ttl() {
        return $this::supports_data_guarantee() & self::SUPPORTS_NATIVE_TTL;
    }
}