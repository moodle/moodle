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

use external_api;
use external_function_parameters;
use external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class transaction_complete extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, 'The component name'),
            'componentid' => new external_value(PARAM_INT, 'The item id in the context of the component'),
            'orderid' => new external_value(PARAM_TEXT, 'The order id coming back from PayPal'),
            'authorizationid' => new external_value(PARAM_TEXT, 'The authorization id coming back from PayPal'),
        ]);
    }

    /**
     * Perform what needs to be done when a transaction is reported to be complete.
     * This function does not take cost as a parameter as we cannot rely on any provided value.
     *
     * @param string $component Name of the component that the componentid belongs to
     * @param int $componentid An internal identifier that is used by the component
     * @param string $orderid PayPal order ID
     * @param string $authorizationid The PayPal-generated ID for the authorized payment
     * @return array
     */
    public static function execute(string $component, int $componentid, string $orderid,
            string $authorizationid): array {
        global $USER, $DB;

        self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'componentid' => $componentid,
            'orderid' => $orderid,
            'authorizationid' => $authorizationid,
        ]);

        $config = get_config('pg_paypal');

        [
            'amount' => $amount,
            'currency' => $currency
        ] = \core_payment\helper::get_cost($component, $componentid);

        $paypalhelper = new paypal_helper($config->clientid, $config->secret, false);
        $authorization = $paypalhelper->capture_authorization($authorizationid, $amount, $currency);

        $success = false;
        $message = '';

        if ($authorization) {
            switch ($authorization['status']) {
                case 'COMPLETED':
                    $success = true;
                    // Everything is correct. Let's give them what they paid for.
                    try {
                        \core_payment\helper::deliver_order($component, $componentid);

                        $paymentid = \core_payment\helper::save_payment($component, $componentid, (int)$USER->id, $amount, $currency,
                                'paypal');

                        // Store PayPal extra information.
                        $record = new \stdClass();
                        $record->paymentid = $paymentid;
                        $record->pp_orderid = $orderid;
                        $record->pp_authorizationid = $authorizationid;
                        $record->pp_paymentid = $authorization->id; // The PayPal-generated ID for the captured payment.
                        $record->pp_status = 'COMPLETED';

                        $DB->insert_record('pg_paypal', $record);
                    } catch (\Exception $e) {
                        debugging('Exception while trying to process payment: ' . $e->getMessage(), DEBUG_DEVELOPER);
                        $success = false;
                        $message = get_string('internalerror', 'pg_paypal');
                    }
                    break;
                case 'PENDING':
                    $success = false;
                    $message = get_string('echecknotsupported', 'pg_paypal');
                    break;
                default:
                    $success = false;
                    $message = get_string('paymentnotcleared', 'pg_paypal');
            }
        } else {
            // Could not capture authorization!
            $success = false;
            $message = get_string('captureauthorizationfailed', 'pg_paypal');
        }

        return [
            'success' => $success,
            'message' => $message,
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_function_parameters
     */
    public static function execute_returns() {
        return new external_function_parameters([
            'success' => new external_value(PARAM_BOOL, 'Whether everything was successful or not.'),
            'message' => new external_value(PARAM_TEXT, 'Message (usually the error message).', VALUE_OPTIONAL),
        ]);
    }
}
