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
 * Filter for component 'filter_oembed'
 *
 * @package   filter_oembed
 * @copyright Erich M. Wappis / Guy Thomas 2016
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use filter_oembed\service\oembed;

/**
 * Class testable_oembed.
 *
 * @package   filter_oembed
 * @copyright Erich M. Wappis / Guy Thomas 2016
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_oembed extends oembed {

    /**
     * Singleton
     *
     * @param string $providerstate Either 'enabled', 'disabled', or 'all'.
     * @return oembed
     */
    public static function get_instance($providerstate = 'enabled') {
        /** @var $instance oembed */
        static $instance = [];
        if (!isset($instance[$providerstate])) {
            $instance[$providerstate] = new testable_oembed($providerstate);
        }
        return $instance[$providerstate];
    }

    /**
     * Calls the protected set_providers function.
     */
    public function protected_set_providers($state = 'enabled') {
        return self::set_providers($state);
    }

    /**
     * Calls the protected download_providers function.
     */
    public static function protected_download_providers() {
        return self::download_providers();
    }

    /**
     * Calls the protected get_local_providers function.
     */
    public static function protected_get_local_providers() {
        return self::get_local_providers();
    }

    /**
     * Calls the protected get_plugin_providers function.
     */
    public static function protected_get_plugin_providers() {
        return self::get_plugin_providers();
    }

    /**
     * Calls the protected match_provider_names function.
     */
    public static function protected_match_provider_names($providerarray, $provider) {
        return self::match_provider_names($providerarray, $provider);
    }

    /**
     * Empty the properties variable.
     */
    public function empty_providers() {
        $this->providers = [];
    }

    /**
     * Calls the protected get_all_provider_data function.
     */
    public static function protected_get_all_provider_data($fields = '*') {
        return self::get_all_provider_data($fields);
    }

}
