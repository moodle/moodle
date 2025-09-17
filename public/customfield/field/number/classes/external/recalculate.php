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

declare(strict_types=1);

namespace customfield_number\external;

use core\exception\invalid_parameter_exception;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_api;
use core_external\external_value;
use customfield_number\provider_base;

/**
 * Implementation of web service customfield_number_recalculate_value
 *
 * @package    customfield_number
 * @author     2024 Marina Glancy
 * @copyright  2024 Moodle Pty Ltd <support@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recalculate extends external_api {

    /**
     * Describes the parameters for customfield_number_recalculate_value
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'fieldid' => new external_value(PARAM_INT, 'Field id', VALUE_REQUIRED),
            'instanceid' => new external_value(PARAM_INT, 'Instance id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Implementation of web service customfield_number_recalculate_value
     *
     * @param int $fieldid
     * @param int $instanceid
     * @return array
     */
    public static function execute(int $fieldid, int $instanceid): array {
        // Parameter validation.
        [
            'fieldid' => $fieldid,
            'instanceid' => $instanceid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'fieldid' => $fieldid,
            'instanceid' => $instanceid,
        ]);

        // Access validation.
        $context = \context_system::instance();
        self::validate_context($context);

        $field = \core_customfield\field_controller::create($fieldid);
        $provider = provider_base::instance($field);
        if (!$provider) {
            throw new invalid_parameter_exception('Invalid parameter');
        }

        $handler = $field->get_handler();
        if (!$handler->can_edit($field, $instanceid)) {
            throw new \moodle_exception('nopermissions', '', '', get_string('update'));
        }

        $provider->recalculate($instanceid);

        $data = $handler->get_instance_fields_data(
            [$fieldid => $field], $instanceid)[$fieldid];

        return ['value' => $data->export_value()];
    }

    /**
     * Describe the return structure for customfield_number_recalculate_value
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'value' => new external_value(PARAM_RAW, 'Recalculated value (prepared for display)'),
        ]);
    }
}
