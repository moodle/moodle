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
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_ai_manager\ai_manager_utils;

/**
 * Web service to submit a query to an AI tool.
 *
 * @package    local_ai_manager
 * @copyright  ISB Bayern, 2024
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_ai_config extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
                'tenant' => new external_value(PARAM_TEXT,
                        'The tenant identifier, only useful to add if the user has access to multiple tenants',
                        VALUE_DEFAULT,
                        null),
        ]);
    }

    /**
     * Retrieve the purpose config.
     *
     * @param ?string $tenant the tenant to use, only useful for accounts which can access/manage more than their own tenant
     * @return array associative array containing the result of the request
     */
    public static function execute(?string $tenant = null): array {
        global $USER;
        ['tenant' => $tenant] = self::validate_parameters(self::execute_parameters(), ['tenant' => $tenant]);
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/ai_manager:use', $context);

        return ai_manager_utils::get_ai_config($USER, $tenant);
    }

    /**
     * Describes the return structure of the service.
     *
     * @return external_single_structure the return structure
     */
    public static function execute_returns(): external_single_structure {
        $singlestructuredefinition = [];
        $singlestructuredefinition['purposes'] = new external_multiple_structure(
                new external_single_structure([
                                'purpose' => new external_value(PARAM_ALPHANUM,
                                        'Name of the tool configured for the purpose',
                                        VALUE_REQUIRED),
                                'isconfigured' => new external_value(PARAM_BOOL,
                                        'If there is an AI tool configured for the purpose',
                                        VALUE_REQUIRED),
                                'limitreached' => new external_value(PARAM_BOOL,
                                        'If the user has reached the maximum amount of requests for the purpose',
                                        VALUE_REQUIRED),
                                'lockedforrole' => new external_value(PARAM_BOOL,
                                        'If the purpose is locked for the user\'s role',
                                        VALUE_REQUIRED),
                        ]
                ));
        $singlestructuredefinition['tools'] = new external_multiple_structure(
                new external_single_structure([
                        'name' => new external_value(PARAM_TEXT, 'Name of the AI tool', VALUE_REQUIRED),
                        'addurl' => new external_value(PARAM_RAW, 'URL to add an instance', VALUE_REQUIRED),
                ])
        );

        $singlestructuredefinition['tenantenabled'] =
                new external_value(PARAM_BOOL, 'If AI manager is being enabled for this tenant', VALUE_REQUIRED);
        $singlestructuredefinition['userlocked'] =
                new external_value(PARAM_BOOL, 'If user is being locked, thus must not use any AI tools', VALUE_REQUIRED);
        $singlestructuredefinition['userconfirmed'] =
                new external_value(PARAM_BOOL, 'If user has confirmed to use the AI tools', VALUE_REQUIRED);
        $singlestructuredefinition['role'] =
                new external_value(PARAM_TEXT, 'The user\'s role in the context of the AI manager', VALUE_REQUIRED);
        $singlestructuredefinition['aiwarningurl'] =
                new external_value(PARAM_URL, 'The URL which should be shown to the user to warn about AI results', VALUE_REQUIRED);
        return new external_single_structure(
                $singlestructuredefinition,
                'Object containing the tools configured for each purpose'
        );
    }
}
