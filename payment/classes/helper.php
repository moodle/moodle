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
 * Contains helper class for the payment subsystem.
 *
 * @package    core_payment
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for the payment subsystem.
 *
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Returns an accumulated list of supported currencies by all payment gateways.
     *
     * @return string[] An array of the currency codes in the three-character ISO-4217 format
     */
    public static function get_supported_currencies(): array {
        $currencies = [];

        $plugins = \core_plugin_manager::instance()->get_enabled_plugins('pg');
        foreach ($plugins as $plugin) {
            /** @var \pg_paypal\gateway $classname */
            $classname = '\pg_' . $plugin . '\gateway';

            $currencies += component_class_callback($classname, 'get_supported_currencies', [], []);
        }

        $currencies = array_unique($currencies);

        return $currencies;
    }

    /**
     * Returns the list of gateways that can process payments in the given currency.
     *
     * @param string $component
     * @param int $componentid
     * @return string[]
     */
    public static function get_gateways_for_currency(string $component, int $componentid): array {
        $gateways = [];

        [
            'amount' => $amount,
            'currency' => $currency,
            'accountid' => $accountid,
        ] = self::get_cost($component, $componentid);
        $account = new account($accountid);
        if (!$account->get('id') || !$account->get('enabled')) {
            return $gateways;
        }

        foreach ($account->get_gateways() as $plugin => $gateway) {
            if (!$gateway->get('enabled')) {
                continue;
            }
            /** @var gateway $classname */
            $classname = '\pg_' . $plugin . '\gateway';

            $currencies = component_class_callback($classname, 'get_supported_currencies', [], []);
            if (in_array($currency, $currencies)) {
                $gateways[] = $plugin;
            }
        }

        return $gateways;
    }

    /**
     * Calculates the cost with the surcharge
     *
     * @param float $amount amount in the currency units
     * @param float $surcharge surcharge in percents
     * @param string $currency currency, used for calculating the number of fractional digits
     * @return float
     */
    public static function get_cost_with_surcharge(float $amount, float $surcharge, string $currency): float {
        return round($amount + $amount * $surcharge / 100, 2); // TODO number of digits depends on currency.
    }

    /**
     * Returns human-readable amount with fixed number of fractional digits and currency indicator
     *
     * @param float $amount
     * @param string $currency
     * @return string
     * @throws \coding_exception
     */
    public static function get_cost_as_string(float $amount, string $currency): string {
        if (class_exists('NumberFormatter') && function_exists('numfmt_format_currency')) {
            $locale = get_string('localecldr', 'langconfig');
            $fmt = \NumberFormatter::create($locale, \NumberFormatter::CURRENCY);
            $localisedcost = numfmt_format_currency($fmt, $amount, $currency);
        } else {
            $localisedcost = sprintf("%.2f %s", $amount, $currency); // TODO number of digits depends on currency.
        }

        return $localisedcost;
    }

    /**
     * Returns the percentage of surcharge that is applied when using a gateway
     *
     * @param string $gateway Name of the gateway
     * @return float
     */
    public static function get_gateway_surcharge(string $gateway): float {
        return (float)get_config('pg_' . $gateway, 'surcharge');
    }

    /**
     * Returns the attributes to place on a pay button.
     *
     * @param string $component Name of the component that the componentid belongs to
     * @param int $componentid An internal identifier that is used by the component
     * @param string $description Description of the payment
     * @return array
     */
    public static function gateways_modal_link_params(string $component, int $componentid, string $description): array {
        return [
            'id' => 'gateways-modal-trigger',
            'role' => 'button',
            'data-component' => $component,
            'data-componentid' => $componentid,
            'data-description' => $description,
        ];
    }

    /**
     * Asks the cost from the related component.
     *
     * @param string $component Name of the component that the componentid belongs to
     * @param int $componentid An internal identifier that is used by the component
     * @return array['amount' => float, 'currency' => string, 'accountid' => int]
     * @throws \moodle_exception
     */
    public static function get_cost(string $component, int $componentid): array {
        $cost = component_class_callback("$component\\payment\\provider", 'get_cost', [$componentid]);

        if ($cost === null || !is_array($cost) || !array_key_exists('amount', $cost)
                || !array_key_exists('currency', $cost) || !array_key_exists('accountid', $cost) ) {
            throw new \moodle_exception('callbacknotimplemented', 'core_payment', '', $component);
        }

        return $cost;
    }

    /**
     * Returns the gateway configuration for given component and gateway
     *
     * @param string $component
     * @param int $componentid
     * @param string $gatewayname
     * @return array
     * @throws \moodle_exception
     */
    public static function get_gateway_configuration(string $component, int $componentid, string $gatewayname): array {
        $x = self::get_cost($component, $componentid);
        $gateway = null;
        $account = new account($x['accountid']);
        if ($account && $account->get('enabled')) {
            $gateway = $account->get_gateways()[$gatewayname] ?? null;
        }
        if (!$gateway) {
            throw new \moodle_exception('gatewaynotfound', 'payment');
        }
        return $gateway->get_configuration();
    }

    /**
     * Delivers what the user paid for.
     *
     * @uses \core_payment\local\callback\provider::deliver_order()
     *
     * @param string $component Name of the component that the componentid belongs to
     * @param int $componentid An internal identifier that is used by the component
     * @param int $paymentid payment id as inserted into the 'payments' table, if needed for reference
     * @return bool Whether successful or not
     */
    public static function deliver_order(string $component, int $componentid, int $paymentid): bool {
        $result = component_class_callback("$component\\payment\\provider", 'deliver_order', [$componentid, $paymentid]);

        if ($result === null) {
            throw new \moodle_exception('callbacknotimplemented', 'core_payment', '', $component);
        }

        return $result;
    }

    /**
     * Stores essential information about the payment and returns the "id" field of the payment record in DB.
     * Each payment gateway may then store the additional information their way.
     *
     * @param int $accountid Account id
     * @param string $component Name of the component that the componentid belongs to
     * @param int $componentid An internal identifier that is used by the component
     * @param int $userid Id of the user who is paying
     * @param float $amount Amount of payment
     * @param string $currency Currency of payment
     * @param string $gateway The gateway that is used for the payment
     * @return int
     */
    public static function save_payment(int $accountid, string $component, int $componentid, int $userid, float $amount, string $currency,
            string $gateway): int {
        global $DB;

        $record = new \stdClass();
        $record->component = $component;
        $record->componentid = $componentid;
        $record->userid = $userid;
        $record->amount = $amount;
        $record->currency = $currency;
        $record->gateway = $gateway;
        $record->accountid = $accountid;
        $record->timecreated = $record->timemodified = time();

        $id = $DB->insert_record('payments', $record);

        return $id;
    }

    /**
     * This functions adds the settings that are common for all payment gateways.
     *
     * @param \admin_settingpage $settings The settings object
     * @param string $gateway The gateway name prefic with pg_
     */
    public static function add_common_gateway_settings(\admin_settingpage $settings, string $gateway): void {
        $settings->add(new \admin_setting_configtext($gateway . '/surcharge', get_string('surcharge', 'core_payment'),
                get_string('surcharge_desc', 'core_payment'), 0, PARAM_INT));

    }

    /**
     * Save a new or edited payment account (used in management interface)
     *
     * @param \stdClass $data
     */
    public static function save_payment_account(\stdClass $data) {

        if (empty($data->id)) {
            $account = new account(0, $data);
        } else {
            $account = new account($data->id);
            $account->from_record($data);
        }

        $account->save();
        // TODO trigger event.
    }

    /**
     * Delete a payment account (used in management interface)
     *
     * @param account $account
     */
    public static function delete_payment_account(account $account) {
        foreach ($account->get_gateways(false) as $gateway) {
            if ($gateway->get('id')) {
                $gateway->delete();
            }
        }
        $account->delete();
        // TODO trigger event.
    }

    /**
     * Save a payment gateway linked to an existing account (used in management interface)
     *
     * @param \stdClass $data
     */
    public static function save_payment_gateway(\stdClass $data) {
        if (empty($data->id)) {
            $gateway = new account_gateway(0, $data);
        } else {
            $gateway = new account_gateway($data->id);
            unset($data->accountid, $data->gateway, $data->id);
            $gateway->from_record($data);
        }

        $gateway->save();
        // TODO trigger event.
    }

    /**
     * Returns the list of payment accounts in the given context (used in management interface)
     *
     * @param \context $context
     * @return account[]
     */
    public static function get_payment_accounts_to_manage(\context $context): array {
        return account::get_records(['contextid' => $context->id]);
    }

    /**
     * Get list of accounts available in the given context
     *
     * @param \context $context
     * @return array
     */
    public static function get_payment_accounts_menu(\context $context): array {
        global $DB;
        [$sql, $params] = $DB->get_in_or_equal($context->get_parent_context_ids(true));
        $accounts = array_filter(account::get_records_select('contextid '.$sql, $params), function($account) {
            return $account->is_available();
        });
        return array_map(function($account) {
            return $account->get_formatted_name();
        }, $accounts);
    }
}
