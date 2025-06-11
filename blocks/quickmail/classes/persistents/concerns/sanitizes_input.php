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

namespace block_quickmail\persistents\concerns;

defined('MOODLE_INTERNAL') || die();

use \dml_missing_record_exception;

trait sanitizes_input {

    /**
     * Returns an array that is fit for creating this persistent
     *
     * @param  array  $params
     * @param  array  $keepkeys   optional array of param keys to be kept
     * @return array
     */
    public static function sanitize_creation_params($params, $keepkeys = []) {
        // Throw exception if any required key is missing from input params.
        self::check_required_params(self::$requiredcreationkeys, $params);

        // Fill in any missing non-required params with default values.
        $params = self::fill_optional_params($params);

        $props = array_keys(self::define_properties());

        // Get rid of non-course-configurable fields.
        $params = \block_quickmail_plugin::array_filter_key($params, function ($key) use ($props, $keepkeys) {
            return in_array($key, $props) || in_array($key, $keepkeys);
        });

        return $params;
    }

    /**
     * Throws an exception if any of the given required keys are missing from the params
     *
     * @param  array  $requiredkeys
     * @param  array  $params
     * @return void
     * @throws \Exception
     */
    private static function check_required_params($requiredkeys, $params) {
        foreach ($requiredkeys as $key) {
            if (!array_key_exists($key, $params)) {
                throw new \Exception('Missing ' . $key);
            }
        }
    }

    /**
     * Returns an array of params with any missing default params filled with default values
     *
     * @param  array  $params
     * @return array
     */
    private static function fill_optional_params($params) {
        foreach (self::$defaultcreationparams as $key => $value) {
            if (!array_key_exists($key, $params)) {
                $params[$key] = $value;
            } else if (empty($params[$key])) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

}
