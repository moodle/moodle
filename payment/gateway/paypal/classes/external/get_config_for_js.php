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
 * This class contains a list of webservice functions related to the PayPal payment gateway.
 *
 * @package    pg_paypal
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace pg_paypal\external;

use core_payment\helper;
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class get_config_for_js extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, ''),
            'componentid' => new external_value(PARAM_INT, ''),
        ]);
    }

    /**
     * Returns the full URL of the PayPal JavaScript SDK.
     *
     * @return string[]
     */
    public static function execute($component, $componentid): array {
        self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'componentid' => $componentid,
        ]);

        $config = helper::get_gateway_configuration($component, $componentid, 'paypal');
        $cost = helper::get_cost($component, $componentid);
        $surcharge = helper::get_gateway_surcharge('paypal');

        return [
            'clientid' => $config['clientid'],
            'brandname' => $config['brandname'],
            'cost' => helper::get_cost_with_surcharge($cost['amount'], $surcharge, $cost['currency']),
            'currency' => $cost['currency'],
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'clientid' => new external_value(PARAM_TEXT, 'PayPal client ID'),
            'brandname' => new external_value(PARAM_TEXT, 'Brand name'),
            'cost' => new external_value(PARAM_FLOAT, 'Cost with gateway surcharge'),
            'currency' => new external_value(PARAM_TEXT, 'Currency'),
        ]);
    }
}
