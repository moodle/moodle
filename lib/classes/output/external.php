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
 * Mustache helper to load strings from string_manager.
 *
 * @package    core
 * @category   output
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_component;
use moodle_exception;
use context_system;
use theme_config;

/**
 * This class contains a list of webservice functions related to output.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
class external extends external_api {
    /**
     * Returns description of load_template() parameters.
     *
     * @return external_function_parameters
     */
    public static function load_template_parameters() {
        return new external_function_parameters(
                array('component' => new external_value(PARAM_COMPONENT, 'component containing the template'),
                      'template' => new external_value(PARAM_ALPHANUMEXT, 'name of the template'),
                      'themename' => new external_value(PARAM_ALPHANUMEXT, 'The current theme.'),
                      'includecomments' => new external_value(PARAM_BOOL, 'Include comments or not', VALUE_DEFAULT, false)
                         )
            );
    }

    /**
     * Remove comments from mustache template.
     * @param string $templatestr
     * @return mixed
     */
    protected static function strip_template_comments($templatestr) {
        return preg_replace('/(?={{!)(.*)(}})/sU', '', $templatestr);
    }

    /**
     * Return a mustache template, and all the strings it requires.
     *
     * @param string $component The component that holds the template.
     * @param string $templatename The name of the template.
     * @param string $themename The name of the current theme.
     * @return string the template
     */
    public static function load_template($component, $template, $themename, $includecomments = false) {
        global $DB, $CFG, $PAGE;

        $params = self::validate_parameters(self::load_template_parameters(),
                                            array('component' => $component,
                                                  'template' => $template,
                                                  'themename' => $themename,
                                                  'includecomments' => $includecomments));

        $component = $params['component'];
        $template = $params['template'];
        $themename = $params['themename'];
        $includecomments = $params['includecomments'];

        $templatename = $component . '/' . $template;

        // Will throw exceptions if the template does not exist.
        $filename = mustache_template_finder::get_template_filepath($templatename, $themename);
        $templatestr = file_get_contents($filename);

        // Remove comments from template.
        if (!$includecomments) {
            $templatestr = self::strip_template_comments($templatestr);
        }

        return $templatestr;
    }

    /**
     * Returns description of load_template() result value.
     *
     * @return external_description
     */
    public static function load_template_returns() {
        return new external_value(PARAM_RAW, 'template');
    }

    /**
     * Returns description of load_icon_map() parameters.
     *
     * @return external_function_parameters
     */
    public static function load_fontawesome_icon_map_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Return a mapping of icon names to icons.
     *
     * @return array the mapping
     */
    public static function load_fontawesome_icon_map() {
        $instance = icon_system::instance(icon_system::FONTAWESOME);

        $map = $instance->get_icon_name_map();

        $result = [];

        foreach ($map as $from => $to) {
            list($component, $pix) = explode(':', $from);
            $one = [];
            $one['component'] = $component;
            $one['pix'] = $pix;
            $one['to'] = $to;
            $result[] = $one;
        }
        return $result;
    }

    /**
     * Returns description of load_icon_map() result value.
     *
     * @return external_description
     */
    public static function load_fontawesome_icon_map_returns() {
        return new external_multiple_structure(new external_single_structure(
            array(
                'component' => new external_value(PARAM_COMPONENT, 'The component for the icon.'),
                'pix' => new external_value(PARAM_RAW, 'Value to map the icon from.'),
                'to' => new external_value(PARAM_RAW, 'Value to map the icon to.')
            )
        ));
    }
}

