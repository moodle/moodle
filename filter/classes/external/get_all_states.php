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

namespace core_filters\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use core_external\external_warnings;
use context;

/**
 * External function for getting all filter states.
 *
 * @package    core_filters
 * @copyright  2024 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.4
 */
class get_all_states extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }


    /**
     * Main method of the external function.
     *
     * @return array containing all filter states.
     */
    public static function execute(): array {
        global $CFG;
        require_once($CFG->libdir . '/filterlib.php');

        $system = \context_system::instance();
        external_api::validate_context($system);

        $filterstates = $warnings = [];
        $states = filter_get_all_states();

        foreach ($states as $state) {
            $context = context::instance_by_id($state->contextid);
            $classname = \core\context_helper::parse_external_level($context->contextlevel);

            $filterstates[] = [
                'contextlevel' => $classname::get_short_name(),
                'instanceid' => $context->instanceid,
                'contextid' => $state->contextid,
                'filter' => $state->filter,
                'state' => $state->active,
                'sortorder' => $state->sortorder,
            ];
        }

        return [
            'filters' => $filterstates,
            'warnings' => $warnings,
        ];
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'filters' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'contextlevel' => new external_value(PARAM_ALPHA, 'The context level where the filters are:
                                (coursecat, course, module).'),
                            'instanceid' => new external_value(PARAM_INT, 'The instance id of item associated with the context.'),
                            'contextid' => new external_value(PARAM_INT, 'The context id.'),
                            'filter'  => new external_value(PARAM_PLUGIN, 'Filter plugin name.'),
                            'state' => new external_value(PARAM_INT, 'Filter state: 1 for on, -1 for off, -9999 if disabled.'),
                            'sortorder' => new external_value(PARAM_INT, 'Execution order.'),
                        ]
                    ),
                    'All filters states'
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
