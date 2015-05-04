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
 * This is the external API for this tool.
 *
 * @package    tool_templatelibrary
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_templatelibrary;

require_once("$CFG->libdir/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;

/**
 * This is the external API for this tool.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns description of list_templates() parameters.
     *
     * @return external_function_parameters
     */
    public static function list_templates_parameters() {
        $component = new external_value(
            PARAM_COMPONENT,
            'The component to search',
            VALUE_DEFAULT,
            ''
        );
        $search = new external_value(
            PARAM_RAW,
            'The search string',
            VALUE_DEFAULT,
            ''
        );
        $params = array('component' => $component, 'search' => $search);
        return new external_function_parameters($params);
    }

    /**
     * Expose to AJAX
     * @return boolean
     */
    public static function list_templates_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Loads the list of templates.
     * @param string $component Limit the search to a component.
     * @param string $search The search string.
     * @return array[string]
     */
    public static function list_templates($component, $search) {
        $params = self::validate_parameters(self::list_templates_parameters(),
                                            array(
                                                'component' => $component,
                                                'search' => $search,
                                            ));

        return api::list_templates($component, $search);
    }

    /**
     * Returns description of list_templates() result value.
     *
     * @return external_description
     */
    public static function list_templates_returns() {
        return new external_multiple_structure(new external_value(PARAM_RAW, 'The template name (format is component/templatename)'));
    }

    /**
     * Returns description of load_canonical_template() parameters.
     *
     * @return external_function_parameters
     */
    public static function load_canonical_template_parameters() {
        return new external_function_parameters(
                array('component' => new external_value(PARAM_COMPONENT, 'component containing the template'),
                      'template' => new external_value(PARAM_ALPHANUMEXT, 'name of the template'))
            );
    }

    /**
     * Can this function be called directly from ajax?
     *
     * @return boolean
     * @since Moodle 2.9
     */
    public static function load_canonical_template_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Return a mustache template.
     * Note - this function differs from the function core_output_load_template
     * because it will never return a theme overridden version of a template.
     *
     * @param string $component The component that holds the template.
     * @param string $template The name of the template.
     * @return string the template
     */
    public static function load_canonical_template($component, $template) {
        $params = self::validate_parameters(self::load_canonical_template_parameters(),
                                            array('component' => $component,
                                                  'template' => $template));

        $component = $params['component'];
        $template = $params['template'];

        return api::load_canonical_template($component, $template);
    }

    /**
     * Returns description of load_canonical_template() result value.
     *
     * @return external_description
     */
    public static function load_canonical_template_returns() {
        return new external_value(PARAM_RAW, 'template');
    }
}
