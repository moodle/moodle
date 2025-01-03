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

namespace local_ai_manager\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_ai_manager\local\connector_factory;

/**
 * Web service to retrieve available options of a given purpose.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_purpose_options extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
                'purpose' => new external_value(PARAM_ALPHANUM, 'The purpose name', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Retrieve the purpose options.
     *
     * @param string $purpose The purpose string to retrieve the options for
     * @return array associative array containing the result of the request
     */
    public static function execute(string $purpose): array {
        [
                'purpose' => $purpose,
        ] = self::validate_parameters(self::execute_parameters(), [
                'purpose' => $purpose,
        ]);
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/ai_manager:use', $context);

        if (empty($purpose)) {
            return ['options' => []];
        }
        $factory = \core\di::get(connector_factory::class);
        $purposeobject = $factory->get_purpose_by_purpose_string($purpose);
        return [
                'options' => json_encode($purposeobject->get_available_purpose_options()),
        ];
    }

    /**
     * Describes the return structure of the service.
     *
     * @return external_single_structure the return structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
                        'options' => new external_value(PARAM_TEXT, 'JSON encoded string of available options', VALUE_OPTIONAL),
                ]
        );
    }
}
