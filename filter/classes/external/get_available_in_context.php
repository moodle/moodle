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
 * This is the external API for the filter component.
 *
 * @package    core_filters
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_filters\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use Exception;

/**
 * This is the external API for the filter component.
 *
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_available_in_context extends external_api {
    /**
     * Returns description of get_available_in_context parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.4
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'contexts' => new external_multiple_structure(
                new external_single_structure([
                    'contextlevel' => new external_value(
                        PARAM_ALPHA,
                        'The context level where the filters are: (coursecat, course, module)',
                    ),
                    'instanceid' => new external_value(PARAM_INT, 'The instance id of item associated with the context.'),
                ]),
                'The list of contexts to check.'
            ),
        ]);
    }

    /**
     * Returns the filters available in the given contexts.
     *
     * @param array $contexts the list of contexts to check
     * @return array with the filters information and warnings
     * @since Moodle 3.4
     */
    public static function execute($contexts) {
        global $CFG;

        require_once($CFG->libdir . '/filterlib.php');

        $params = self::validate_parameters(self::execute_parameters(), ['contexts' => $contexts]);
        $filters = $warnings = [];

        foreach ($params['contexts'] as $contextinfo) {
            try {
                $context = self::get_context_from_params($contextinfo);
                self::validate_context($context);
                $contextinfo['contextid'] = $context->id;
            } catch (Exception $e) {
                $warnings[] = [
                    'item' => 'context',
                    'itemid' => $contextinfo['instanceid'],
                    'warningcode' => $e->getCode(),
                    'message' => $e->getMessage(),
                ];
                continue;
            }
            $contextfilters = filter_get_available_in_context($context);

            foreach ($contextfilters as $filter) {
                $filters[] = array_merge($contextinfo, (array) $filter);
            }
        }

        return [
            'filters' => $filters,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of get_available_in_context result value.
     *
     * @return external_single_structure
     * @since  Moodle 3.4
     */
    public static function execute_returns() {
        return new external_single_structure([
            'filters' => new external_multiple_structure(
                new external_single_structure([
                    'contextlevel' => new external_value(
                        PARAM_ALPHA,
                        'The context level where the filters are: (coursecat, course, module).',
                    ),
                    'instanceid' => new external_value(PARAM_INT, 'The instance id of item associated with the context.'),
                    'contextid' => new external_value(PARAM_INT, 'The context id.'),
                    'filter'  => new external_value(PARAM_PLUGIN, 'Filter plugin name.'),
                    'localstate' => new external_value(PARAM_INT, 'Filter state: 1 for on, -1 for off, 0 if inherit.'),
                    'inheritedstate' => new external_value(PARAM_INT, '1 or 0 to use when localstate is set to inherit.'),
                ]),
                'Available filters'
            ),
            'warnings' => new external_warnings(),
        ]);
    }
}
