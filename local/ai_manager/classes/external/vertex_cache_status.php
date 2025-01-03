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
use local_ai_manager\local\aitool_option_vertexai_authhandler;
use local_ai_manager\local\userinfo;

/**
 * Web service to check and update the Google Vertex AI cache status.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vertex_cache_status extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
                'serviceaccountinfo' => new external_value(PARAM_RAW,
                        'The JSON string containing the service account information of the used Google Account',
                        VALUE_REQUIRED),
                'newstatus' => new external_value(PARAM_BOOL,
                        'The status to which the caching config should be set to',
                        VALUE_DEFAULT,
                        null),
        ]);
    }

    /**
     * Retrieve the purpose config.
     *
     * @param string $serviceaccountinfo The service account info stringified JSON
     * @return array associative array containing the result of the request
     */
    public static function execute(string $serviceaccountinfo, ?bool $newstatus = null): array {
        global $USER;
        [
                'serviceaccountinfo' => $serviceaccountinfo,
                'newstatus' => $newstatus,
        ] = self::validate_parameters(self::execute_parameters(),
                [
                        'serviceaccountinfo' => $serviceaccountinfo,
                        'newstatus' => $newstatus,
                ]);
        $tenant = userinfo::get_tenant_for_user($USER->id);
        $context = $tenant->get_context();
        self::validate_context($context);
        require_capability('local/ai_manager:managevertexcache', $context);

        $vertexaiauthhandler = new aitool_option_vertexai_authhandler(0, $serviceaccountinfo);
        if (!is_null($newstatus)) {
            try {
                $cachingchangeresult = $vertexaiauthhandler->set_google_cache_status($newstatus);
            } catch (\moodle_exception $exception) {
                return ['code' => 500, 'error' => $exception->getMessage()];
            }
            return $cachingchangeresult ? ['code' => 200, 'cachingstatus' => $newstatus] :
                    ['code' => 500, 'error' => 'COULD NOT SET THE CACHING STATUS'];
        } else {
            // Variable $newstatus is null, so we just want to query and return the result.
            try {
                $currentcachingstatus = $vertexaiauthhandler->get_google_cache_status();
            } catch (\moodle_exception $exception) {
                return ['code' => 500, 'error' => $exception->getMessage()];
            }
            return ['code' => 200, 'cachingEnabled' => $currentcachingstatus];
        }
    }

    /**
     * Describes the return structure of the service.
     *
     * @return external_single_structure the return structure
     */
    public static function execute_returns(): external_single_structure {
        $singlestructuredefinition = [];
        $singlestructuredefinition['code'] = new external_value(PARAM_INT,
                'Status code of the request',
                VALUE_REQUIRED);
        $singlestructuredefinition['cachingEnabled'] = new external_value(PARAM_BOOL,
                'If the Google Vertex AI cache is enabled', VALUE_OPTIONAL);
        $singlestructuredefinition['error'] = new external_value(PARAM_TEXT,
                'Error message if there is an error', VALUE_OPTIONAL);
        return new external_single_structure(
                $singlestructuredefinition,
                'Object containing the tools configured for each purpose'
        );
    }
}
