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

use core_payment\helper;
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class get_available_gateways extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, 'Component'),
            'paymentarea' => new external_value(PARAM_AREA, 'Payment area in the component'),
            'itemid' => new external_value(PARAM_INT, 'An identifier for payment area in the component')
        ]);
    }

    /**
     * Returns the list of gateways that can process payments in the given currency.
     *
     * @param string $component
     * @param string $paymentarea
     * @param int $itemid
     * @return \stdClass[]
     */
    public static function execute(string $component, string $paymentarea, int $itemid): array {

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
        ]);

        $list = [];
        $gateways = helper::get_available_gateways($params['component'], $params['paymentarea'], $params['itemid']);
        $payable = helper::get_payable($params['component'], $params['paymentarea'], $params['itemid']);
        $amount = $payable->get_amount();
        $currency = $payable->get_currency();

        foreach ($gateways as $gateway) {
            $surcharge = helper::get_gateway_surcharge($gateway);
            $list[] = (object)[
                'shortname' => $gateway,
                'name' => get_string('gatewayname', 'paygw_' . $gateway),
                'description' => get_string('gatewaydescription', 'paygw_' . $gateway),
                'surcharge' => $surcharge,
                'cost' => helper::get_cost_as_string($amount, $currency, $surcharge),
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
                    'description' => new external_value(PARAM_RAW, 'description of the gateway'),
                    'surcharge' => new external_value(PARAM_INT, 'percentage of surcharge when using the gateway'),
                    'cost' => new external_value(PARAM_TEXT,
                        'Cost in human-readable form (amount plus surcharge with currency sign)'),
                ])
        );
    }
}
