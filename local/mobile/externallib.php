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
 * External functions backported.
 *
 * @package    local_mobile
 * @copyright  2014 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

class local_mobile_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_plugin_settings_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Get all the plugin settings.
     * PLEASE DO NOT DELETE THIS FUNCTION.
     * The Mobile app relies in this function to detect if the site is using the local_mobile plugin.
     *
     * @return array of settings
     */
    public static function get_plugin_settings() {

        // Warnings array, it can be empty at the end but is mandatory.
        $warnings = array();
        $settings = array();

        $pluginsettings = get_config('local_mobile');
        foreach ($pluginsettings as $key => $val) {
            $settings[] = array(
                'name' => $key,
                'value' => $val,
            );
        }

        $results = array(
            'settings' => $settings,
            'warnings' => $warnings
        );
        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_plugin_settings_returns() {
        return new external_single_structure(
            array(
                'settings' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_NOTAGS, 'setting name'),
                            'value' => new external_value(PARAM_RAW, 'setting value'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}