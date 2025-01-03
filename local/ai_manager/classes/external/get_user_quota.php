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
use local_ai_manager\base_purpose;
use local_ai_manager\local\config_manager;
use local_ai_manager\local\connector_factory;
use local_ai_manager\local\userinfo;
use local_ai_manager\local\userusage;

/**
 * Web service to retrieve available options of a given purpose.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_user_quota extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Retrieve the purpose options.
     *
     * @return array associative array containing the result of the request
     */
    public static function execute(): array {
        global $USER;
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/ai_manager:use', $context);

        $configmanager = \core\di::get(config_manager::class);
        $purposes = base_purpose::get_all_purposes();
        $userinfo = new userinfo($USER->id);

        $response = [];
        if ($userinfo->get_role() === userinfo::ROLE_UNLIMITED) {
            $response['period'] = format_time($configmanager->get_max_requests_period());
        } else {
            $response['period'] = preg_replace('/^1\s/', '', format_time($configmanager->get_max_requests_period()));
        }
        $response['role'] = userinfo::get_role_as_string($userinfo->get_role());
        foreach ($purposes as $purpose) {
            $purposeobject = \core\di::get(connector_factory::class)->get_purpose_by_purpose_string($purpose);
            $userusage = new userusage($purposeobject, $USER->id);
            $response['usage'][$purpose] = [
                    'currentusage' => $userusage->get_currentusage(),
                    'maxusage' => $configmanager->get_max_requests($purposeobject, $userinfo->get_role()),
            ];
        }
        return $response;
    }

    /**
     * Describes the return structure of the service.
     *
     * @return external_single_structure the return structure
     */
    public static function execute_returns(): external_single_structure {
        $purposes = base_purpose::get_all_purposes();
        $purposesstructure = [];
        foreach ($purposes as $purpose) {
            $purposesstructure[$purpose] =
                    new external_single_structure([
                            'currentusage' => new external_value(PARAM_INT, 'Currently used request count', VALUE_REQUIRED),
                            'maxusage' => new external_value(PARAM_INT, 'Currently used request count', VALUE_REQUIRED),
                    ]);
        }
        $singlestructuredefinition['usage'] = new external_single_structure($purposesstructure);
        $singlestructuredefinition['role'] =
                new external_value(PARAM_TEXT, 'String of the current role the user has', VALUE_REQUIRED);
        $singlestructuredefinition['period'] = new external_value(PARAM_TEXT, 'User formatted quota period', VALUE_REQUIRED);
        return new external_single_structure(
                $singlestructuredefinition,
                'Object containing information about the currently used quota of the user',
        );
    }
}
