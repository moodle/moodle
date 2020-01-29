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
 * This is the external API for this component.
 *
 * @package    core_payment
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class get_gateways_for_currency extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
                ['currency' => new external_value(PARAM_ALPHA, 'Currency code')]
        );
    }

    /**
     * Returns the list of gateways that can process payments in the given currency.
     *
     * @param string $currency The currency in the three-character ISO-4217 format.
     * @return \stdClass[]
     */
    public static function execute(string $currency): array {

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'currency' => $currency,
        ]);

        $list = [];
        $gateways = \core_payment\helper::get_gateways_for_currency($params['currency']);

        foreach ($gateways as $gateway) {
            $list[] = (object)[
                'shortname' => $gateway,
                'name' => get_string('gatewayname', 'pg_' . $gateway),
                'description' => get_string('gatewaydescription', 'pg_' . $gateway),
            ];
        }

        return $list;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
                new external_single_structure([
                    'shortname' => new external_value(PARAM_PLUGIN, 'Name of the plugin'),
                    'name' => new external_value(PARAM_TEXT, 'Human readable name of the gateway'),
                    'description' => new external_value(PARAM_TEXT, 'description of the gateway'),
                ])
        );
    }
}
