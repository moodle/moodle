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
                      'template' => new external_value(PARAM_SAFEPATH, 'name of the template'),
                      'themename' => new external_value(PARAM_ALPHANUMEXT, 'The current theme.'),
                      'includecomments' => new external_value(PARAM_BOOL, 'Include comments or not', VALUE_DEFAULT, false)
                         )
            );
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

        $loader = new mustache_template_source_loader();
        // Will throw exceptions if the template does not exist.
        return $loader->load(
            $params['component'],
            $params['template'],
            $params['themename'],
            $params['includecomments']
        );
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
     * Returns description of load_template_with_dependencies() parameters.
     *
     * @return external_function_parameters
     */
    public static function load_template_with_dependencies_parameters() {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, 'component containing the template'),
            'template' => new external_value(PARAM_SAFEPATH, 'name of the template'),
            'themename' => new external_value(PARAM_ALPHANUMEXT, 'The current theme.'),
            'includecomments' => new external_value(PARAM_BOOL, 'Include comments or not', VALUE_DEFAULT, false),
            'lang' => new external_value(PARAM_LANG, 'lang', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Return a mustache template, and all the child templates and strings it requires.
     *
     * @param string $component The component that holds the template.
     * @param string $template The name of the template.
     * @param string $themename The name of the current theme.
     * @param bool $includecomments Whether to strip comments from the template source.
     * @param string $lang moodle translation language, null means use current.
     * @return string the template
     */
    public static function load_template_with_dependencies(
        string $component,
        string $template,
        string $themename,
        bool $includecomments = false,
        string $lang = null
    ) {
        global $DB, $CFG, $PAGE;

        $params = self::validate_parameters(
            self::load_template_with_dependencies_parameters(),
            [
                'component' => $component,
                'template' => $template,
                'themename' => $themename,
                'includecomments' => $includecomments,
                'lang' => $lang
            ]
        );

        $loader = new mustache_template_source_loader();
        // Will throw exceptions if the template does not exist.
        $dependencies = $loader->load_with_dependencies(
            $params['component'],
            $params['template'],
            $params['themename'],
            $params['includecomments'],
            [],
            [],
            $params['lang']
        );
        $formatdependencies = function($dependency) {
            $results = [];
            foreach ($dependency as $dependencycomponent => $dependencyvalues) {
                foreach ($dependencyvalues as $dependencyname => $dependencyvalue) {
                    array_push($results, [
                        'component' => $dependencycomponent,
                        'name' => $dependencyname,
                        'value' => $dependencyvalue
                    ]);
                }
            }
            return $results;
        };

        // Now we have to unpack the dependencies into a format that can be returned
        // by external functions (because they don't support dynamic keys).
        return [
            'templates' => $formatdependencies($dependencies['templates']),
            'strings' => $formatdependencies($dependencies['strings'])
        ];
    }

    /**
     * Returns description of load_template_with_dependencies() result value.
     *
     * @return external_description
     */
    public static function load_template_with_dependencies_returns() {
        $resourcestructure = new external_single_structure([
            'component' => new external_value(PARAM_COMPONENT, 'component containing the resource'),
            'name' => new external_value(PARAM_TEXT, 'name of the resource'),
            'value' => new external_value(PARAM_RAW, 'resource value')
        ]);

        return new external_single_structure([
            'templates' => new external_multiple_structure($resourcestructure),
            'strings' => new external_multiple_structure($resourcestructure)
        ]);
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

